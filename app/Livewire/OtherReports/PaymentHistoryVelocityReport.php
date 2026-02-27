<?php

namespace App\Livewire\OtherReports;

use App\Actions\OtherReports\GetPaymentHistoryVelocityReportDetailsAction;
use App\Exports\PaymentHistoryVelocityExport;
use App\Models\Loan;
use App\Services\PdfGeneratorService;
use App\Traits\ExportsData;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PaymentHistoryVelocityReport extends Component
{
    use ExportsData, WithPagination;

    public ?int $loanId = null;

    public string $searchTerm = '';

    public function mount(?int $loanId)
    {
        $this->loanId = $loanId;
        $this->startDate = empty($this->getLoan()) ? now()->startOfMonth()->toDateString() : $this->getLoan()?->Credit_Account_Date->toDateString();
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.payment-history-velocity-report', [
            'records' => $this->getReportData(),
        ]);
    }

    public function printReport()
    {
        return app(PdfGeneratorService::class)
            ->view('pdf.payment-history-velocity', [
                'records' => app(GetPaymentHistoryVelocityReportDetailsAction::class)
                    ->filters($this->getFilters())
                    ->execute(),
                'loan' => $this->getLoan(),
                'partnerName' => auth()->user()?->partner->Institution_Name,
                'filters' => $this->getFormattedDateFilters(),
            ])
            ->streamFromLivewire();
    }

    public function excelExport(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filters = $this->getFilters();
        $filters['loanId'] = $this->loanId;

        return Excel::download(new PaymentHistoryVelocityExport($filters), $this->getExcelFilename());
    }

    private function getReportData()
    {
        return app(GetPaymentHistoryVelocityReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())->execute();
    }

    protected function getFilters(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'searchTerm' => $this->searchTerm,
            'loanId' => $this->loanId
        ];
    }

    /**
     * @return mixed
     */
    public function getLoan(): mixed
    {
        return Loan::with('customer', 'loan_product')->select('Maturity_Date', 'Credit_Account_Date', 'Customer_ID', 'Credit_Account_Status', 'Credit_Account_Reference', 'Loan_Product_ID', 'Credit_Amount', 'Currency')->find($this->loanId);
    }
}
