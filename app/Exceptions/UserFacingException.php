<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A business-rule failure whose message is written for the user ("This order cannot be shipped: …").
 * Its message is shown as-is; every other exception is replaced by a generic message (App\Support\SafeErrors)
 * and logged. Never put technical details (SQL, paths, ids from other users) in this message.
 *
 * If one is not caught, the app answers 422 with the message (JSON) or goes back with it as an error flash.
 */
class UserFacingException extends RuntimeException
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }

        return back()->withInput($request->except(['password', 'password_confirmation', 'current_password']))->with('error', $this->getMessage());
    }

    /** Expected outcome, not a bug: keep it out of the error log. */
    public function report(): bool
    {
        return true;
    }
}
