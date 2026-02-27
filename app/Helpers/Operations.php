<?php

namespace App\Helpers;

class Operations {
    public static function getSupportedOperations(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
        ];
    }
}