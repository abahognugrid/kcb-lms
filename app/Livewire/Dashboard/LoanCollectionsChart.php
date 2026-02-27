<?php

namespace App\Livewire\Dashboard;

use App\Models\JournalEntry;
use App\Models\Loan;
use App\Traits\ExportsData;
use App\Traits\HasChart;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LoanCollectionsChart extends Component
{
    use HasChart, ExportsData;

    public string $selectedPeriod = 'daily';

    protected string $cacheKey = 'collections_chart_';

    public function mount(): void
    {
        $this->cacheKey .= auth()->user()->partner_id;
        $this->startDate = now()->subDays(29)->startOfDay()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.dashboard.loan-collections-chart', [
            'lineChartModel' => $this->getLineChartModel(),
            'summary' => $this->getSummary(),
        ]);
    }

    protected function getRecords()
    {
        if ($this->selectedPeriod === 'monthly') {
            return $this->getMonthlyRecords();
        }

        if ($this->selectedPeriod === 'weekly') {
            return $this->getWeeklyRecords();
        }

        return $this->getDailyRecords();
    }

    protected function getDailyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . '_daily', [60, 5], function () {
                return JournalEntry::selectRaw('
                    DATE_FORMAT(created_at, "%b %d") as value_label,
                    DAY(created_at) as day,
                    SUM(debit_amount) as total_collections
                ')
                    ->where('transactable', 'App\Models\LoanRepayment')
                    ->where('partner_id', auth()->user()->partner_id)
                    ->whereBetween('created_at', $this->getDateRange())
                    ->groupBy('day', 'value_label')
                    ->orderBy('day')
                    ->get();
            });
    }

    protected function getWeeklyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . '_weekly', [60, 5], function () {
                return JournalEntry::selectRaw("
                        WEEKOFYEAR(created_at) as week_number,
                        CONCAT('As at: ', DATE_FORMAT(created_at, '%b %d')) as value_label,
                        SUM(debit_amount) as total_collections
                    ")
                    ->where('partner_id', auth()->user()->partner_id)
                    ->where('transactable', 'App\Models\LoanRepayment')
                    ->whereBetween('created_at', $this->getDateRange())
                    ->groupByRaw("week_number, value_label")
                    ->orderBy('week_number')
                    ->get();
            });
    }

    protected function getMonthlyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . "_monthly", [60, 5], function () {
                return JournalEntry::selectRaw("
                        DATE_FORMAT(created_at, '%Y %b') as value_label,
                        DATE_FORMAT(created_at, '%Y%m') as year_month_name,
                        SUM(debit_amount) as total_collections
                    ")
                    ->where('partner_id', auth()->user()->partner_id)
                    ->where('transactable', 'App\Models\LoanRepayment')
                    ->whereBetween('created_at', $this->getDateRange())
                    ->groupByRaw("year_month_name, value_label")
                    ->orderBy('year_month_name')
                    ->get();
            });
    }

    private function getLineChartModel(): LineChartModel
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        if ($this->selectedPeriod === 'weekly') {
            // Calculate total weeks in current month
            $totalWeeks = ceil($startDate->diffInWeeks($endDate->endOfMonth()));
            // Initialize weeks array
            $weeks = [];

            for ($week = 0; $week < $totalWeeks; $week++) {
                $weekEnd = $startDate->copy()->addWeeks($week)->endOfWeek();
                $weeks[$weekEnd->weekOfYear] = ["As at: " . $weekEnd->format('M d') => 0];
            }

            $this->getWeeklyRecords()
                ->each(function ($record) use (&$weeks) {
                    $week = $weeks[$record->week_number];
                    $weekKey = array_key_first($week);
                    $weeks[$record->week_number][$weekKey] = $record->total_collections;
                });

            $lineChartModel = $this->makeLineChartModel('Weekly Repayments', false);

            // Add data points
            foreach ($weeks as $weekOfYear => $week) {
                $label = array_key_first($week);
                $lineChartModel->addPoint($label, $week[$label]);
            }
        } else if ($this->selectedPeriod === 'monthly') {
            $monthCount = $startDate->diffInMonths($endDate, true);
            $months = collect();

            for ($i = $monthCount; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[$date->format('Y M')] = 0;
            }

            $records = $months->merge($this->getMonthlyRecords()->pluck('total_collections', 'value_label'));

            $lineChartModel = $this->makeLineChartModel('Monthly Repayments', false);

            // Add data points
            foreach ($records as $label => $weekAmount) {
                $lineChartModel->addPoint($label, $weekAmount);
            }
        } else {
            // Existing daily logic
            $records = $this->getDates($endDate->diffInDays($startDate, true))
                ->mapWithKeys(function ($date) {
                    return [$date => 0];
                })
                ->merge(
                    $this->getRecords()->pluck('total_collections', 'value_label')
                );

            $lineChartModel = $this->makeLineChartModel('Daily Repayments', false);

            foreach ($records as $monthDay => $dayValue) {
                $lineChartModel->addPoint($monthDay, $dayValue);
            }
        }

        return $lineChartModel
            ->setColors(['#3ad88f', '#3ad88f'])
            ->setXAxisVisible(true)
            ->setYAxisVisible(true);
    }

    public function setPeriod($period): void
    {
        cache()->forget($this->cacheKey . "_$period");

        $this->selectedPeriod = $period;
    }

    private function getSummary(): \Illuminate\Support\Collection
    {
        $query = Loan::query();

        if ($this->startDate && $this->endDate) {
            $query->whereRelation('loan_repayments', function ($query) {
                $query->whereBetween('Transaction_Date', $this->getDateRange());
            });
        }

        $records = $query->get()
            ->map(function (Loan $loan) {
                $principalPaid = $loan->totalPrincipalPaid();
                $interestPaid  = $loan->totalInterestPaid();
                $penaltiesPaid = $loan->penaltiesPaid();
                $feesPaid = $loan->totalFees();

                return [
                    'principal_paid' => $principalPaid,
                    'interest_paid'  => $interestPaid,
                    'penalties_paid' => $penaltiesPaid,
                    'fees_paid' => $feesPaid,
                    'total_paid'    => $principalPaid + $interestPaid + $penaltiesPaid + $feesPaid
                ];
            });

        return collect([
            'principal_paid' => $records->sum('principal_paid'),
            'interest_paid'  => $records->sum('interest_paid'),
            'penalties_paid' => $records->sum('penalties_paid'),
            'fees_paid' => $records->sum('fees_paid'),
            'total_paid'    => $records->sum('total_paid')
        ]);
    }

    private function getDateRange()
    {
        if ($this->selectedPeriod === 'weekly') {
            return [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ];
        }

        if ($this->selectedPeriod === 'monthly') {
            return [
                Carbon::parse($this->startDate)->subYear()->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ];
        }

        return [
            Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
            Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
        ];
    }
}
