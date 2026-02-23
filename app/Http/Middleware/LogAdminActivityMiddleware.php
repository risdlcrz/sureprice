<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAdminActivityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->user_type === 'admin') {
            try {
                date_default_timezone_set('Asia/Manila');
                $route = $request->route();
            $routeName = $route ? $route->getName() : null;
            $userName = $user->name ?? 'An admin';

            // determine action based on HTTP method
            $method = $request->method();
            switch ($method) {
                case 'POST':
                    $action = 'created';
                    break;
                case 'PUT':
                case 'PATCH':
                    $action = 'updated';
                    break;
                case 'DELETE':
                    $action = 'deleted';
                    break;
                case 'GET':
                default:
                    $action = 'viewed';
                    break;
            }

            // build a human-friendly description
            if ($routeName) {
                $descBase = str_replace(['.', '-'], [' ', ' '], $routeName);
                $description = "$userName $action the page: $descBase";
            } else {
                $description = "$userName $action URL: " . $request->path();
            }

            // attempt to capture bound model info if any
            $modelType = null;
            $modelId = null;
            if ($route) {
                $parameters = $route->parameters();
                if (!empty($parameters)) {
                    // take first model parameter as reference
                    $first = reset($parameters);
                    if (is_object($first) && isset($first->id)) {
                        $modelType = get_class($first);
                        $modelId = $first->id;
                    }
                }
            }

            \App\Models\Activity::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'model_type' => $modelType,
                'model_id' => $modelId,
            ]);
            } catch (\Throwable $e) {
                // Don't break the request if logging fails
            }
        }
        return $next($request);
    }
} 