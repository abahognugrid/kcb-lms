<?php

namespace App\Services;

class DecisionEngineService
{
    const MAX_LIMIT = 999999999999999;
    const MIN_LIMIT = -999999999999999;
    const PREV_LOAN_DAYS_LATE_MULTIPLIER = 1;
    const RISK_MULTIPLIER = 1;
    const REPAYMENT_MULTIPLIER = 1.1;
    const SPEND_MULTIPLIER = 0;
    const SLOW_PROGRESS_MULTIPLIER = 1;
    const AGE_MULTIPLIER = 0;
    const LOAN_LIMIT_CAPPING = 0;
    const RISK_CLASS_TYPES = ["CRB", "MNO"];
}
