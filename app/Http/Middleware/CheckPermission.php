<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Rawilk\Settings\Facades\Settings;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::user() || !Auth::user()->can($permission)) {
            abort(403, 'You are not authorized to ' . $permission . '.');
        }

        Settings::setTeamId(session('partner_id'));

        return $next($request);
    }
}
