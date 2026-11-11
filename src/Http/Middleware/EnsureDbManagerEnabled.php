<?php

namespace Jb\DbManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDbManagerEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('db-manager.enabled')) {
            return $next($request);
        }

        if (app()->environment('local')) {
            return $next($request);
        }

        abort(404);
    }
}
