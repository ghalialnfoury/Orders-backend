<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsForImages
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*');
    }
}
