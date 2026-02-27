<?php

namespace App\Models;

class CustomerInteraction
{
    public function __construct(
        public string $type,
        public mixed $id,
        public string $date,
        public ?array $details = null
    ) {}
}
