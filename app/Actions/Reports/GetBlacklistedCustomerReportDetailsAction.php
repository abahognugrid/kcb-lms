<?php

namespace App\Actions\Reports;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetBlacklistedCustomerReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected ?int $partnerId;

    public function execute()
    {
        $query = DB::table('blacklisted_customers as bc')
            ->selectRaw('
                bc.created_at as date_blacklisted,
                loans.credit_amount as amount_disbursed,
                loan_repayments.amount as amount_repaid,
                bc.reason as reason_for_blacklisting,
                bc.blacklisted_by,
                users.name as blacklisted_by_name,
                CONCAT(customers.first_name, " ", customers.last_name) as customer_name,
                customers.telephone_number,
                bc.customer_id
            ')
            ->join('customers', 'customers.id', '=', 'bc.customer_id')
            ->join('users', 'users.id', '=', 'bc.blacklisted_by')
            ->leftJoin('loans', function ($join) {
                $join->on('loans.Customer_ID', '=', 'bc.customer_id')
                    ->where('loans.partner_id', '=', DB::raw('bc.partner_id'));
            })
            ->leftJoin('loan_repayments', function ($join) {
                $join->on('loan_repayments.Customer_ID', '=', 'bc.customer_id')
                    ->where('loan_repayments.partner_id', '=', DB::raw('bc.partner_id'));
            })
            ->where('bc.partner_id', $this->partnerId);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('bc.created_at', [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
            ]);
        }

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());
        $this->partnerId = Arr::get($details, 'partnerId', 0);

        return $this;
    }
}
