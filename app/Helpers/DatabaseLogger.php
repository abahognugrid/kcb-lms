<?php

namespace App\Helpers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Illuminate\Support\Facades\DB;

class DatabaseLogger extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        DB::table('logs')->insert([
            'level' => $record->level->getName(),       // e.g., INFO, ERROR
            'message' => $record->message,
            'context' => json_encode($record->context),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
