<?php

namespace App\Actions\Reports;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetDelinkedCustomerReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected int $partnerId;

    public function execute()
    {
        $query = DB::table('customers as c')
            ->selectRaw('
                c.created_at as date_delinked,
                loans."Credit_Amount" as amount_disbursed,
                loan_repayments.amount as amount_repaid,
                c."First_Name",
                c."Last_Name",
                c."Delinked_Phone_Number" as telephone_number,
                c.id
            ')
            ->leftJoin('loans', function ($join) {
                $join->on('loans.Customer_ID', '=', 'c.id');
            })
            ->leftJoin('loan_repayments', function ($join) {
                $join->on('loan_repayments.Customer_ID', '=', 'c.id');
            });

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('c.Delinked_At', [
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
