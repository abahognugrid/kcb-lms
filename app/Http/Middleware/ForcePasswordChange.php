<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('users.show') || $request->routeIs('users.update-password')) {
            return $next($request);
        }
        if (auth()->check() && is_null(auth()->user()->password_changed_at)) {
            return redirect()->route('users.show', ['user' => auth()->id()])->with('error', 'Please update your password.');
        }
        return $next($request);
    }
}
