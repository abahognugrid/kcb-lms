<?php

namespace App\Exports;

use App\Actions\Reports\GetAuditTrailReportDetailsAction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class AuditTrailExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.audit-trail', [
            'records' => app(GetAuditTrailReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partnerName' => Auth::user()?->partner?->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Audit Trail';
    }
}
