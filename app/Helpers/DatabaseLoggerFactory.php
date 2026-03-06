<?php

namespace App\Helpers;

use App\Helpers\DatabaseLogger;
use Monolog\Logger;

class DatabaseLoggerFactory
{
    public function __invoke(array $config)
    {
        $logger = new Logger('database');
        $logger->pushHandler(new DatabaseLogger($config['level']));
        return $logger;
    }
}
