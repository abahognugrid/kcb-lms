<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetAuditTrailReportDetailsAction;
use App\Exports\AuditTrailExport;
use App\Traits\ExportsData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AuditTrailReport extends Component
{
    use ExportsData, WithPagination;

    public ?string $search = '';

    public ?string $event = '';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reports.audit-trail-report', [
            'records' => $this->getReportData(),
            'eventOptions' => $this->getEventOptions(),
        ]);
    }

    public function printReport(): void
    {
        $this->generatePdfReport('pdf.audit-trail');
    }

    public function excelExport(): void
    {
        $this->generateExcelReport(AuditTrailExport::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEvent(): void
    {
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    private function getReportData()
    {
        return app(GetAuditTrailReportDetailsAction::class)
            ->paginate()
            ->filters($this->getFilters())
            ->execute();
    }

    protected function addFilters(): array
    {
        return [
            'search' => $this->search,
            'event' => $this->event,
            'isAdmin' => Auth::user()->is_admin,
        ];
    }

    private function getEventOptions(): array
    {
        return [
            '' => 'All Events',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
        ];
    }

    public function getFilename(): string
    {
        return str(self::class)->afterLast('\\')->snake()->toString().'_'.now()->toDateString().'.xlsx';
    }
}
