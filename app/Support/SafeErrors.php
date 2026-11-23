<?php

namespace App\Support;

use App\Exceptions\UserFacingException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Turns exceptions into messages that are safe to show, and logs the full details privately.
 *
 *   } catch (\Exception $e) {
 *       return back()->with('error', SafeErrors::message($e, 'Failed to update material', ['material_id' => $id]));
 *   }
 *
 * Users see "Failed to update material. Please try again or contact support (ref. 7F3K2Q9X)." — never the exception
 * text, SQL, file paths or stack trace. The log entry (storage/logs) has the exception with its trace, the given
 * context and the same reference, so support can find it. Messages written for users (UserFacingException,
 * validation, 403/404 …) are passed through unchanged.
 */
final class SafeErrors
{
    public const GENERIC = 'Something went wrong. Please try again or contact support.';

    /** Exceptions already logged in this request (so log() + message() on the same exception logs once). */
    private static ?\WeakMap $logged = null;

    private static ?string $reference = null;

    /** A short per-request id shown to the user and written to the log. */
    public static function reference(): string
    {
        return self::$reference ??= strtoupper(Str::random(8));
    }

    /** True when the exception's own message was written for users and is safe to show. */
    public static function isSafe(Throwable $e): bool
    {
        return $e instanceof UserFacingException
            || $e instanceof ValidationException
            || $e instanceof AuthorizationException
            || ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500 && $e->getMessage() !== '');
    }

    /** The message to show for $e. Unexpected errors are logged (once) and replaced by a generic message. */
    public static function message(Throwable $e, ?string $action = null, array $context = []): string
    {
        if (self::isSafe($e)) {
            return $action ? rtrim($action, '.: ') . ': ' . $e->getMessage() : $e->getMessage();
        }

        self::log($e, $action ?? 'Unexpected error', $context);

        return ($action ? rtrim($action, '.: ') . '. ' : 'Something went wrong. ')
            . 'Please try again or contact support (ref. ' . self::reference() . ').';
    }

    /** Log the full exception (message, class, file, trace) with context — for the server log only. */
    public static function log(Throwable $e, string $what, array $context = []): void
    {
        self::$logged ??= new \WeakMap();
        if (isset(self::$logged[$e])) {
            return;
        }
        self::$logged[$e] = true;

        Log::error(rtrim($what, '.: '), $context + ['ref' => self::reference(), 'exception' => $e]);
    }

    /** For tests: start a fresh request. */
    public static function reset(): void
    {
        self::$logged = null;
        self::$reference = null;
    }
}
