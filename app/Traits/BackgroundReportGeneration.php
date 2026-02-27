<?php

namespace App\Traits;

use App\Jobs\GenerateReportJob;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait BackgroundReportGeneration
{
    protected function dispatchReportGeneration(string $exportType, ?string $exportClass = null, ?string $pdfView = null): void
    {
        $reportType = $this->getReportType();
        $filters = $this->addFilters();
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

        session()->flash('success', 'Your PDF report is being generated in the background. You will be notified when it\'s ready.');
    }

    protected function generateExcelReport(string $class): void
    {
        $this->dispatchReportGeneration('excel', $class);

        session()->flash('success', 'Your Excel report is being generated in the background. You will be notified when it\'s ready.');
    }
}
