<?php

namespace App\Traits;

use App\Jobs\GenerateReportJob;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait ExportsData
{
    public string $startDate = '';
    public string $endDate = '';

    public function getFilters(array $additionalFilters = []): array
    {
        return array_merge([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            //'loanProductId' => $this->loanProductId,
            'partnerId' => Auth()->user()->partner_id,
            'partnerName' => Auth()->user()->partner?->Institution_Name
        ], array_merge($additionalFilters, $this->addFilters()));
    }

    protected function addFilters(): array
    {
        return [];
    }

    /**
     * @param array $additionalFilters
     * @return array
     */
    protected function getFormattedDateFilters(array $additionalFilters = []): array
    {
        $filters = $this->getFilters($additionalFilters);

        if (isset($filters['startDate'])) {
            $filters['startDate'] = Carbon::parse($filters['startDate'])->format('Y-m-d');
        }

        $filters['endDate'] = Carbon::parse($filters['endDate'])->format('Y-m-d');

        return $filters;
    }
    protected function getPdfFilename(): string
    {
        return $this->getFilename('.pdf');
    }

    protected function getExcelFilename(): string
    {
        return $this->getFilename('.xlsx');
    }

    protected function getFilename(string $extension): string
    {
        return str(self::class)
            ->afterLast('\\')
            ->snake()
            ->replace('_', '-')
            ->toString() . '-' . now()->toDateString() . $extension;
    }

    protected function dispatchReportGeneration(string $exportType, ?string $exportClass = null, ?string $pdfView = null): void
    {
        $reportType = $this->getReportType();
        $filters = $this->getFilters();
        $userId = Auth::id();
        $partnerId = Arr::get($filters, 'partnerId');
        $componentClass = static::class;

        GenerateReportJob::dispatch(
            $reportType,
            $exportType,
            $filters,
            $userId,
            $partnerId,
            $componentClass,
            $exportClass,
            $pdfView
        );
    }

    protected function getReportType(): string
    {
        $className = class_basename(static::class);

        // Remove "Report" from the end
        $className = Str::replaceLast('Report', '', $className);

        // Add spaces between words (camelCase to Title Case)
        $className = preg_replace('/(?<!^)[A-Z]/', ' $0', $className);

        return trim($className);
    }

    protected function generatePdfReport(string $view): void
    {
        $this->dispatchReportGeneration('pdf', null, $view);

        session()->flash('success', 'Your PDF report is being generated in the background.');
    }

    protected function generateExcelReport(string $class): void
    {
        $this->dispatchReportGeneration('excel', $class);

        session()->flash('success', 'Your Excel report is being generated in the background.');
    }
}
