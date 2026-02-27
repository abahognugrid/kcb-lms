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

class LoansReleasedChart extends Component
{
    use HasChart, ExportsData;

    public string $selectedPeriod = 'daily';

    protected string $cacheKey = 'loans_released_chart_';

    public function mount(): void
    {
        $this->cacheKey .= auth()->user()->partner_id;
        $this->startDate = now()->subDays(29)->startOfDay()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.dashboard.loans-released-chart', [
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
                return Loan::selectRaw('
                        DATE_FORMAT(created_at, "%b %d") as value_label,
                        DAY(created_at) as day,
                        SUM(facility_amount_granted) as total_amount
                    ')
                    ->has('loan_disbursement')
                    ->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
                    ])
                    ->groupBy('day', 'value_label')
                    ->orderBy('day')
                    ->get();
            });
    }

    protected function getWeeklyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . '_weekly', [60, 5], function () {
                return Loan::selectRaw('
                        WEEKOFYEAR(created_at) as week_number,
                        CONCAT("As at: ", DATE_FORMAT(created_at, "%b %d")) as value_label,
                        SUM(facility_amount_granted) as total_amount
                    ')
                    ->has('loan_disbursement')
                    ->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
                    ])
                    ->groupBy(['week_number', 'value_label'])
                    ->orderBy('week_number')
                    ->get();
            });
    }

    protected function getMonthlyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . "_monthly", [60, 5], function () {
                return Loan::selectRaw('
                        DATE_FORMAT(created_at, "%Y %b") as value_label,
                        DATE_FORMAT(created_at, "%Y%m") as year_month_name,
                        SUM(facility_amount_granted) as total_amount
                    ')
                    ->has('loan_disbursement')
                    ->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                        Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
                    ])
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
                    $weeks[$record->week_number][$weekKey] = $record->total_amount;
                });

            $lineChartModel = $this->makeLineChartModel('Loans Disbursed - Weekly', false);

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

            $records = $months->merge($this->getMonthlyRecords()->pluck('total_amount', 'value_label'));

            $lineChartModel = $this->makeLineChartModel('Loans Disbursed - Monthly', false);

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
                    $this->getRecords()->pluck('total_amount', 'value_label')
                );

            $lineChartModel = $this->makeLineChartModel('Loans Disbursed - Daily', false);

            foreach ($records as $monthDay => $dayValue) {
                $lineChartModel->addPoint($monthDay, $dayValue);
            }
        }

        return $lineChartModel
            ->setColors(['#3ad88f'])
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
        return collect([
            'today' => Loan::query()
                ->has('loan_disbursement')
                ->whereDate('credit_account_date', Carbon::today())
                ->sum('facility_amount_granted'),
            //            'today_count' => Loan::query()
            //                ->has('loan_disbursement')
            //                ->whereDate('credit_account_date', Carbon::today())
            //                ->count(),
            'week' => Loan::query()
                ->has('loan_disbursement')
                ->whereBetween('credit_account_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->toDateString(),
                ])
                ->sum('facility_amount_granted'),
            //            'week_count' => Loan::query()
            //                ->whereBetween('credit_account_date', [
            //                    Carbon::now()->startOfWeek(),
            //                    Carbon::now()->toDateString(),
            //                ])
            //                ->count(),
            'month' => Loan::query()
                ->has('loan_disbursement')
                ->whereBetween('credit_account_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->toDateString(),
                ])
                ->sum('facility_amount_granted'),
            //            'month_count' => Loan::query()
            //                ->whereBetween('credit_account_date', [
            //                    Carbon::now()->startOfMonth(),
            //                    Carbon::now()->toDateString(),
            //                ])
            //                ->count(),
            'year' => Loan::query()
                ->has('loan_disbursement')
                ->whereYear('credit_account_date', Carbon::now()->year)
                ->sum('facility_amount_granted'),
            //            'year_count' => Loan::query()
            //                ->whereYear('credit_account_date', Carbon::now()->year)
            //                ->count(),
        ]);
    }
}
