<?php

return [
    'loans' => [
        'enable_ageing' => env('BACK_DATE_LOAN', false),
        'back_date_days' => env('BACK_DATE_DAYS', 7),
        'write_off_days' => env('ARREARS_WRITE_OFF_DAYS', 180),
    ],
    'sms' => [
        'cost' => env('SMS_COST', 13),
    ],
    'payments' => [
        'bank_name' => env('BANK_NAME'),
        'bank_username' => env('BANK_USERNAME'),
        'bank_password' => env('BANK_PASSWORD'),
        'bank_api_url' => env('BANK_API_URL'),
        'bank_account_no' => env('BANK_ACCOUNT_NO'),
    ],
    'ussd_code' => env('USSD_CODE'),
    'crb' => [
        'url' => env('CRB_URL'),
        'client-id' => env('CRB_CLIENT_ID'),
        'client-secret' => env('CRB_CLIENT_SECRET'),
    ],
    'others' => [
        'payments' => [
            'lowOnTime' => 20,
            'lowOnTimeNotifyDay' => \Illuminate\Support\Carbon::WEDNESDAY,
        ],
        'loans' => [
            'firstTimeLoanPercentage' => 30,
            'firstTimeLoanNotifyDay' => \Illuminate\Support\Carbon::SUNDAY,
        ],
    ],
    'notifications' => [
        'admins' => env('ADMIN_NOTIFICATIONS', []),
    ]
];
