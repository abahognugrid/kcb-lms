<?php

namespace App\Services\Contracts;

use App\Models\Partner;
use App\Models\Transaction;

interface PartnerNotificationInterface
{
  public function notify(Transaction $transaction);
}
