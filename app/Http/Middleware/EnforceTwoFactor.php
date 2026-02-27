<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceTwoFactor
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is logged in and has enabled 2FA
        if ($user && $user->has_2fa_enabled) {
            // If 2FA is enabled but not yet verified in the session
            if (!$request->session()->get('2fa_verified', false)) {
                // Redirect to the 2FA verification screen
                return redirect()->route('verify-2fa');
            }
        }

        return $next($request);
    }
}
