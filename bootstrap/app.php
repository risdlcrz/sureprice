<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Signed-in users who open a guest page (login, register) go to their own home page.
        $middleware->redirectUsersTo(fn (\Illuminate\Http\Request $request) => route($request->user()->homeRoute()));
        // Locked accounts (User::lockBy) are signed out on their next request.
        $middleware->web(append: [\App\Http\Middleware\EnsureAccountIsNotLocked::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Users never see exception messages, SQL, file paths or stack traces (see App\Support\SafeErrors):
        // full details go to storage/logs with a reference id, the user gets a short message with the same id.
        $exceptions->context(fn () => ['ref' => \App\Support\SafeErrors::reference()]);

        // JSON clients get only the HTTP error's public message (e.g. 403 "This account has been locked..."),
        // never the exception class, file, line or stack trace, whatever APP_DEBUG says.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, \Illuminate\Http\Request $request) {
            if (!$request->expectsJson() || $e->getStatusCode() >= 500) {
                return null;   // HTML: Laravel's error page; 5xx: replaced below
            }
            $status = $e->getStatusCode();

            return response()->json(
                ['message' => $e->getMessage() ?: (\Symfony\Component\HttpFoundation\Response::$statusTexts[$status] ?? 'Error')],
                $status,
                $e->getHeaders()
            );
        });

        // Any server error (500+) answered as JSON: a generic message, even with APP_DEBUG on.
        // (HTML server errors use resources/views/errors/500.blade.php when debug is off.)
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() >= 500 && $request->expectsJson()) {
                return response()->json(
                    ['message' => \App\Support\SafeErrors::GENERIC . ' (ref. ' . \App\Support\SafeErrors::reference() . ')'],
                    $response->getStatusCode()
                );
            }

            return $response;
        });
    })->create();
