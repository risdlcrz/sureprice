<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** In production, every plain-HTTP request is permanently redirected to the same URL over HTTPS. */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction() && !$request->secure()) {
            return redirect()->to('https://' . $request->getHttpHost() . $request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
