<?php

namespace Jbmm\TableRecordManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTableRecordManagerEnabled
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
