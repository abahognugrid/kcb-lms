<?php

namespace App\Actions\OtherReports;

use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanProductFee;
use App\Models\LoanSchedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetBorrowersReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected int $perPage = 0;
    public function execute()
    {
        $query = Customer::query()->has('loans')
            ->withCount('loans')
            ->withSum(['loans' => function ($query) {
                $query->select(DB::raw('"Facility_Amount_Granted"::DECIMAL'));
            }], 'Facility_Amount_Granted')
            ->orderBy('First_Name');
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

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }
}
