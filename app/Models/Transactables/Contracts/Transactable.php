<?php

namespace App\Models\Transactables\Contracts;

interface Transactable
{
    public function amount();
    public function saveJournalEntries(): void;
}
