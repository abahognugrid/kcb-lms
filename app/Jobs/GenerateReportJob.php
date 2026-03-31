<?php

namespace App\Jobs;

use App\Actions\Reports\GetDisbursementReportDetailsAction;
use App\Actions\Reports\GetDueLoanReportDetailsAction;
use App\Actions\Reports\GetLoanApplicationReportDetailsAction;
use App\Actions\Reports\GetLoanArrearsReportDetailsAction;
use App\Actions\Reports\GetOutstandingLoanReportDetailsAction;
use App\Actions\Reports\GetPortfolioAtRiskReportDetailsAction;
use App\Actions\Reports\GetRepaymentReportDetailsAction;
use App\Livewire\Reports\DisbursementReport;
use App\Livewire\Reports\DueLoansReport;
use App\Livewire\Reports\LoanApplicationReport;
use App\Livewire\Reports\LoanArrearsReport;
use App\Livewire\Reports\OutstandingLoanReport;
use App\Livewire\Reports\PortfolioAtRiskReport;
use App\Livewire\Reports\RepaymentReport;
use App\Models\User;
use App\Notifications\ReportGeneratedNotification;
use App\Services\PdfGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300; // 5 minutes

    protected User $user;

    public function __construct(
        protected string $reportType,
        protected string $exportType, // 'pdf' or 'excel'
        protected array $filters,
        protected int $userId,
        protected ?int $partnerId,
        protected string $componentClass,
        protected ?string $exportClass = null,
        protected ?string $pdfView = null
    ) {
        $this->user = User::findOrFail($this->userId);
    }

    public function handle(): void
    {
        try {
            $filename = $this->generateFilename();
            $filePath = $this->getStoragePath($filename);

            if ($this->exportType === 'pdf') {
                $this->generatePdf($filePath);
            } else {
                $this->generateExcel($filePath);
            }

            // Store file metadata and notify user
            $this->notifyUser($filename, $filePath);
        } catch (\Exception $e) {
            Log::error('Report generation failed', [
                'report_type' => $this->reportType,
                'export_type' => $this->exportType,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    protected function generatePdf(string $filePath): void
    {
        $pdf = app(PdfGeneratorService::class)
            ->view($this->pdfView, $this->getPdfViewData())
            ->make();

        Storage::put($filePath, $pdf->output());
    }

    /**
     * @throws \Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function generateExcel(string $filePath): void
    {
        if (! $this->exportClass) {
            throw new \Exception('Export class not provided for Excel generation');
        }

        Excel::store(new $this->exportClass($this->filters), $filePath);
    }

    /**
     * @throws \Exception
     */
    protected function getPdfViewData(): array
    {
        // Get the action class from the component class
        $actionClass = $this->getActionClass();

        $records = app($actionClass)
            ->filters($this->filters)
            ->execute();

        return [
            'records' => $records,
            'partnerName' => $this->user->partner?->Institution_Name,
            'filters' => $this->formatFilters(),
            'user' => $this->user, // Add user to view data for print footer
        ];
    }

    protected function getActionClass(): string
    {
        // Map component classes to their corresponding action classes
        return [
            LoanApplicationReport::class => GetLoanApplicationReportDetailsAction::class,
            OutstandingLoanReport::class => GetOutstandingLoanReportDetailsAction::class,
            RepaymentReport::class => GetRepaymentReportDetailsAction::class,
            DisbursementReport::class => GetDisbursementReportDetailsAction::class,
            LoanArrearsReport::class => GetLoanArrearsReportDetailsAction::class,
            DueLoansReport::class => GetDueLoanReportDetailsAction::class,
            PortfolioAtRiskReport::class => GetPortfolioAtRiskReportDetailsAction::class,
            'App\Livewire\Reports\PortfolioAtRiskReport' => 'App\Actions\Reports\GetPortfolioAtRiskReportDetailsAction',
            'App\Livewire\Reports\FullPaymentReport' => 'App\Actions\Reports\GetFullPaymentReportDetailsAction',
            'App\Livewire\Reports\BlacklistedCustomersReport' => 'App\Actions\Reports\GetBlacklistedCustomerReportDetailsAction',
            'App\Livewire\Reports\DelinkedCustomersReport' => 'App\Actions\Reports\GetDelinkedCustomerReportDetailsAction',
            'App\Livewire\Reports\PaidOffLoansReport' => 'App\Actions\Reports\GetPaidOffLoansReportDetailsAction',
            'App\Livewire\Reports\ProvisionsReport' => 'App\Actions\Reports\GetProvisionsReportDetailsAction',
            'App\Livewire\Reports\ExternalAccountsReport' => 'App\Actions\Reports\GetExternalAccountsReportDetailsAction',
            'App\Livewire\Reports\WrittenOffLoansReport' => 'App\Actions\Reports\GetWrittenOffLoansReportDetailsAction',
            'App\Livewire\Reports\LoanAgeingReport' => 'App\Actions\Reports\GetLoanAgeingReportDetailsAction',
            'App\Livewire\Reports\CashFlowReport' => 'App\Actions\Reports\GetCashFlowDetailsAction',
            'App\Livewire\Reports\AuditTrailReport' => 'App\Actions\Reports\GetAuditTrailReportDetailsAction',
            'App\Livewire\Reports\PendingDisbursementReport' => 'App\Actions\Reports\GetPendingDisbursementReportDetailsAction',
            'App\Livewire\OtherReports\IncomeReport' => 'App\Actions\OtherReports\GetIncomeReportDetailsAction',
            'App\Livewire\OtherReports\TransactionReport' => 'App\Actions\OtherReports\GetTransactionsReportDetailsAction',
            'App\Livewire\OtherReports\CreditLimitsReport' => 'App\Actions\OtherReports\GetCreditLimitsReportDetailsAction',
            'App\Livewire\OtherReports\DailyReconciliationReport' => 'App\Actions\OtherReports\GetDailyReconciliationReportDetailsAction',
            'App\Livewire\FinancialReports\TrialBalance' => 'App\Actions\FinancialReports\GetTrialBalanceReportDetailsAction',

        ][$this->componentClass] ?? throw new \Exception('Unknown component class');
    }

    protected function formatFilters(): array
    {
        $filters = $this->filters;

        if (isset($filters['startDate'])) {
            $filters['startDate'] = \Carbon\Carbon::parse($filters['startDate'])->format('Y-m-d');
        }

        if (isset($filters['endDate'])) {
            $filters['endDate'] = \Carbon\Carbon::parse($filters['endDate'])->format('Y-m-d');
        }

        return $filters;
    }

    protected function generateFilename(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = $this->exportType === 'pdf' ? '.pdf' : '.xlsx';

        return str($this->componentClass)
            ->afterLast('\\')
            ->snake()
            ->replace('_', '-')
            ->toString() . '-' . $timestamp . $extension;
    }

    protected function getStoragePath(string $filename): string
    {
        return "reports/{$this->partnerId}/{$filename}";
    }

    protected function notifyUser(string $filename, string $filePath): void
    {
        $notificationData = [
            'message' => "Your {$this->reportType} report has been generated successfully.",
            'report_type' => $this->reportType,
            'filename' => $filename,
            'file_path' => $filePath,
            'generated_by' => $this->userId,
            'generated_at' => now()->toISOString(),
            'export_type' => $this->exportType,
        ];

        $this->user->notify(new ReportGeneratedNotification($notificationData));
    }

    public function failed(\Throwable $exception): void
    {
        $user = User::find($this->userId);

        if ($user) {
            $notificationData = [
                'message' => "Failed to generate {$this->reportType} report. Please try again.",
                'report_type' => $this->reportType,
                'error' => $exception->getMessage(),
                'generated_by' => $this->userId,
                'generated_at' => now()->toISOString(),
                'export_type' => $this->exportType,
            ];

            $user->notify(new ReportGeneratedNotification($notificationData, true));
        }

        Log::error('Report generation job failed', [
            'report_type' => $this->reportType,
            'export_type' => $this->exportType,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
