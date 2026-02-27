<?php

namespace App\Models\Accounts\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Accountable {
    public function fixedParentSlug(): string;
    public function accountDisplayName(): string;
    public function general_ledger_account(): MorphOne;
    public function getIndentifier(): string;
    public function getTypeLetter(): string;
}