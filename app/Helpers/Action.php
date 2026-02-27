<?php

namespace App\Helpers;

use App\Models\Audit;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Action
{
    public static function track(Model $trackable, string $action)
    {
        if (!auth()->check()) {
            throw new Exception('User needs to be authenticated first before tracking all their actions.');
        }

        return Audit::create([
            'auditable_type' => get_class($trackable),
            'auditable_id' => $trackable->id,
            'event' => 'updated',
            'old_values' => $trackable->getOriginal(),
            'new_values' => $trackable->getChanges(),
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'partner_id' => auth()->user()->partner_id ?? null,
            'user_id' => auth()->user()->id,
            'user_type' => get_class(auth()->user()),
        ]);
    }
}
