<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartnerCodeIsProvided
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $partnerCode = $request->header('X-PARTNER-CODE');
        if (!$partnerCode) {
            return response()->json([
                'message' => 'Unauthorized. Your secure partner code is required.'
            ], 401);
        }
        return $next($request);
    }
}
