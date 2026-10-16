<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateFinanceApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('finance.import_token');
        $provided = $request->bearerToken();

        if (! is_string($expected) || strlen($expected) < 32 ||
            ! is_string($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
