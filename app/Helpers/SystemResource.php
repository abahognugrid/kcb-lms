<?php

namespace App\Helpers;

class SystemResource
{

    public static function getCrudSystemResources(): array
    {
        return [
            "customers",
            "partners",
            "loans",
            "loan-applications",
            "loan-accounts",
            "loan-products",
            "loan-product-terms",
            "sms-templates",
            "sms-campaigns",
            "float-management",
            "sms-float-topups",
            "users",
            "roles",
            "loan-loss-provisions",
            "tickets",
            "agents",
            "chart-of-accounts",
            "loan-repayments",
            'exclusion-parameters',
            'business-rules'
        ];
    }

    public static function getReadOnlySystemResources(): array
    {
        return [
            "dashboard",
            "sms",
            "sms-logs",
            "sms-notifications",
            "audit-trail",
            "ticket-dashboard",
            "due-loans-report",
            "missed-repayments-report",
            "no-repayments",
            "past-maturity-date",
            "principal-outstanding",
            "1-month-late-loans",
            "3-months-late-loans",
            "loan-report",
            "disbursement-report",
            "pending-disbursement-report",
            "collections-report",
            "loans-in-arrears-report",
            "paidoff-report",
            "outstanding-report",
            "overdue-report",
            "blacklisted-report",
            "repayment-report",
            "written-report",
            "written-off-recovered-report",
            "portfolio-at-risk-report",
            "arrears-aging-report",
            "monthly-report",
            "rejected-loans-report",
            "loan-product-report",
            "financial-reports",
            "trial-balance-report",
            "balance-sheet-report",
            "income-statement-report",
            "general-ledger-summary",
            "cash-flow-statement",
            "income-report",
            "transactions",
            "daily-transactions-report",
            "gl-statement-report",
            "other-reports",
            "monitoring-reports",
            "borrowers-report",
            "fees-report",
            "daily-report",
            "at-a-glance-report",
            "deferred-income-report",
            "deferred-monthly-income-report",
            "payment-history-velocity-report",
            "loan-applications-report",
            "ticket-reports",
            'partner-settings',
            'credit-limits',
            'logs',
        ];
    }
}
