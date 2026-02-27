<?php

namespace App\Livewire\Dashboard;

use App\Models\Loan;
use Livewire\Component;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;

class LoanProductAgeDistributionChart extends Component
{
    public function render()
    {
        // Fetch loan data with relationships
        $loans = Loan::with(['product', 'customer'])->get();

        // Chart 1: Loan Disbursements by Type & Purpose
        $disbursementCharts = $this->generateDisbursementCharts($loans);

        // Chart 2: Repayment Performance by Loan Product
        $repaymentCharts = $this->generateRepaymentCharts($loans);

        return view('livewire.dashboard.loan-product-age-distribution-chart', compact(
            'disbursementCharts',
            'repaymentCharts'
        ));
    }

    private function generateDisbursementCharts($loans)
    {
        $charts = [];

        // Group loans by product first
        $loansByProduct = $loans->groupBy(function ($loan) {
            return $loan->product->Name ?? 'Unknown Product';
        });

        foreach ($loansByProduct as $productName => $productLoans) {
            $chart = (new PieChartModel())
                ->setTitle("$productName")
                ->setJsonConfig([
                    'chart.height' => 300
                ])
                ->setAnimated(false);

            // Group loans by age category within this product
            $loansByAge = $productLoans->groupBy(function ($loan) {
                if (empty($loan->customer->Date_of_Birth)) {
                    return 'Unknown Age';
                }

                $birthDate = \Carbon\Carbon::parse($loan->customer->Date_of_Birth);
                $age = $birthDate->age; // This calculates the current age

                return $age <= 35 ? '≤ 35 years' : '> 35 years';
            });

            foreach ($loansByAge as $ageCategory => $loanGroup) {
                $totalAmount = $loanGroup->sum('Credit_Amount');
                $chart->addSlice($ageCategory, $totalAmount, $this->getRandomColor());
            }

            $charts[$productName] = $chart;
        }

        return $charts;
    }

    private function generateRepaymentCharts($loans)
    {
        $charts = [];

        // Group loans by product first
        $loansByProduct = $loans->where('Credit_Account_Status', Loan::ACCOUNT_STATUS_FULLY_PAID_OFF)->groupBy(function ($loan) {
            return $loan->product->Name ?? 'Unknown Product';
        });

        foreach ($loansByProduct as $productName => $productLoans) {
            $chart = (new PieChartModel())
                ->setTitle("$productName")
                ->setJsonConfig([
                    'chart.height' => 300
                ])
                ->setAnimated(false);

            // Group loans by age category within this product
            $loansByAge = $productLoans->groupBy(function ($loan) {
                if (empty($loan->customer->Date_of_Birth)) {
                    return 'Unknown Age';
                }

                $birthDate = \Carbon\Carbon::parse($loan->customer->Date_of_Birth);
                $age = $birthDate->age; // This calculates the current age

                return $age <= 35 ? '≤ 35 years' : '> 35 years';
            });

            foreach ($loansByAge as $ageCategory => $loanGroup) {
                $totalAmount = $loanGroup->sum('Credit_Amount');
                $chart->addSlice($ageCategory, $totalAmount, $this->getRandomColor());
            }

            $charts[$productName] = $chart;
        }

        return $charts;
    }

    protected function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
