<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Traits\HasChart;
use App\Traits\ExportsData;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Asantibanez\LivewireCharts\Models\LineChartModel;

class IncomeChart extends Component
{
    use ExportsData, HasChart;

    public string $selectedPeriod = 'daily';

    protected string $cacheKey = 'income_chart_';

    public function mount(): void
    {
        $this->cacheKey .= Auth::user()->partner_id;

        $this->startDate = now()->subDays(29)->startOfDay()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        $lineChartModel = $this->getLineChartModel();

        return view('livewire.dashboard.income-chart', compact('lineChartModel'));
    }

    protected function getDailyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . '_daily', [60, 5], function () {
                return JournalEntry::selectRaw('
                    DATE_FORMAT(journal_entries.created_at, "%b %d") as value_label,
                    DAY(journal_entries.created_at) as day,
                    SUM(credit_amount) - SUM(debit_amount) as total_income
                ')
                    ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.type_letter', 'I')
                    ->when(Auth::user()->partner_id, function ($query, $partner_id) {
                        $query->where('journal_entries.partner_id', $partner_id);
                    })
                    ->whereBetween('journal_entries.created_at', $this->getDateRange())
                    ->groupBy('day', 'value_label')
                    ->orderBy('day')
                    ->get();
            });

        return $result->get();
    }

    protected function getWeeklyRecords()
    {
        return cache()
            ->flexible($this->cacheKey . '_weekly', [60, 5], function () {
                return JournalEntry::selectRaw("
                    WEEKOFYEAR(journal_entries.created_at) as week_number,
                    CONCAT('As at: ', DATE_FORMAT(journal_entries.created_at, '%b %d')) as value_label,
                    SUM(credit_amount) - SUM(debit_amount) as total_income
                ")
                    ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.type_letter', 'I')
                    ->when(Auth::user()->partner_id, function ($query, $partner_id) {
                        $query->where('journal_entries.partner_id', $partner_id);
                    })
                    ->whereBetween('journal_entries.created_at', $this->getDateRange())
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
                    DATE_FORMAT(journal_entries.created_at, '%Y %b') as value_label,
                    DATE_FORMAT(journal_entries.created_at, '%Y%m') as year_month_name,
                    SUM(credit_amount) - SUM(debit_amount) as total_income
                ")
                    ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id')
                    ->where('accounts.type_letter', 'I')
                    ->when(Auth::user()->partner_id, function ($query, $partner_id) {
                        $query->where('journal_entries.partner_id', $partner_id);
                    })
                    ->whereBetween('journal_entries.created_at', $this->getDateRange())
                    ->groupByRaw("year_month_name, value_label")
                    ->orderBy('year_month_name')
                    ->get();
            });
    }

    protected function getRecords()
    {
        return match ($this->selectedPeriod) {
            'monthly' => $this->getMonthlyRecords(),
            'weekly' => $this->getWeeklyRecords(),
            default => $this->getDailyRecords()
        };
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

            $lineChartModel = $this->makeLineChartModel('Weekly Income', false);

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

            $records = $months->merge($this->getMonthlyRecords()->pluck('total_income', 'value_label'));

            $lineChartModel = $this->makeLineChartModel('Monthly Income', false);

            // Add data points
            foreach ($records as $label => $weekAmount) {
                $lineChartModel->addPoint($label, $weekAmount);
            }
        } else {
            $records = $this->getDates($endDate->diffInDays($startDate, true))
                ->mapWithKeys(function ($date) {
                    return [$date => 0];
                })
                ->merge(
                    $this->getRecords()->pluck('total_income', 'value_label')
                );

            $lineChartModel = $this->makeLineChartModel('Daily Income', false);

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

    private function getDateRange()
    {
        return match ($this->selectedPeriod) {
            'weekly' => [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ],
            'monthly' => [
                Carbon::parse($this->startDate)->subYear()->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ],
            default => [
                Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->endDate)->endOfDay()->toDateTimeString()
            ]
        };
    }
}
