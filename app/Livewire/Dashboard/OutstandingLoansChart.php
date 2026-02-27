<?php

namespace App\Livewire\Dashboard;

use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Traits\ExportsData;
use App\Traits\HasChart;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OutstandingLoansChart extends Component
{
    use HasChart, ExportsData;

    protected string $cacheKey = 'outstanding_chart_';

    public function mount(): void
    {
        $this->cacheKey .= auth()->user()->partner_id;
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.dashboard.outstanding-loans-chart', [
            'lineChartModel' => $this->getLineChartModel(),
            'summary' => $this->getSummary(),
        ]);
    }

    protected function getRecords()
    {
        return cache()
            ->flexible($this->cacheKey, [20, 5], function () {
                $query = DB::table('loans as l')
                    ->selectRaw('
                        date_format(l.credit_account_date, "%Y-%m") as year_month_name,
                        date_format(l.credit_account_date, "%b %Y") as month_display,
                        sum(ls.principal_remaining) as principal_remaining,
                        sum(ls.interest_remaining) as interest_remaining,
                        sum(ls.principal_remaining) + sum(ls.interest_remaining) as total_outstanding
                    ')
                    ->join('loan_schedules as ls', 'ls.loan_id', '=', 'l.id')
                    ->where('l.credit_account_date', '<=', $this->endDate)
                    ->where('l.partner_id', auth()->user()->partner_id)
                    ->whereNotIn('l.credit_account_status', [
                        Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                        Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
                    ])
                    ->groupByRaw('year_month_name, month_display');

                return $query->orderBy('year_month_name')->get();
            });
    }

    private function getLineChartModel(): LineChartModel
    {

        // Create an array of all months from 1 year ago to now
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = [
                'month_display' => $date->format('M Y'),
                'principal_remaining' => 0,
                'interest_remaining' => 0
            ];
        }

        $records = $this->getRecords();
        //        dd($records);
        // Fill in the actual data
        foreach ($records as $record) {
            if (isset($months[$record->year_month_name])) {
                $months[$record->year_month_name] = [
                    'month_display' => $record->month_display,
                    'principal_remaining' => $record->principal_remaining,
                    'interest_remaining' => $record->interest_remaining
                ];
            }
        }

        $lineChartModel = $this->makeLineChartModel('Outstanding Loans (Principal & Interest)');

        // Add data points for each series
        foreach ($months as $monthData) {
            $monthDisplay = $monthData['month_display'];

            $lineChartModel->addSeriesPoint(
                'Principal Outstanding',
                $monthDisplay,
                $monthData['principal_remaining']
            );

            $lineChartModel->addSeriesPoint(
                'Interest Outstanding',
                $monthDisplay,
                $monthData['interest_remaining']
            );
        }

        return $lineChartModel
            ->setColors(['#42a242', '#36a2eb'])
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);
    }

    private function getSummary(): \Illuminate\Support\Collection
    {
        $result = DB::table('loans as l')
            ->join('loan_schedules as ls', 'ls.loan_id', '=', 'l.id')
            ->where('l.partner_id', auth()->user()->partner_id)
            ->whereNotIn('l.credit_account_status', [
                Loan::ACCOUNT_STATUS_WRITTEN_OFF,
                Loan::ACCOUNT_STATUS_FULLY_PAID_OFF,
            ])
            ->selectRaw('
                SUM(CASE WHEN ls.payment_due_date = ? THEN principal_remaining + interest_remaining ELSE 0 END) as due_in_period,
                SUM(CASE WHEN ls.payment_due_date < ? THEN principal_remaining + interest_remaining ELSE 0 END) as past_due,
                SUM(CASE WHEN ls.payment_due_date > ? THEN principal_remaining + interest_remaining ELSE 0 END) as not_yet_due,
                SUM(CASE WHEN l.credit_account_date <= ? THEN principal_remaining + interest_remaining ELSE 0 END) as total_outstanding
            ', [$this->endDate, $this->endDate, $this->endDate, $this->endDate])
            ->first();

        return collect([
            'due_in_period' => $result->due_in_period,
            'past_due' => $result->past_due,
            'not_yet_due' => $result->not_yet_due,
            'total_outstanding' => $result->total_outstanding,
        ]);
    }
}
