<?php

namespace App\Exports;

use App\Actions\Reports\GetExternalAccountsReportDetailsAction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExternalAccountsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters) {}

    /**
     * @return Collection
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetExternalAccountsReportDetailsAction::class)->filters($this->filters)->execute();

        return view('excel.external-accounts-report', [
            'records' => $records,
            'partnerName' => auth()->user()?->partner->Institution_Name,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'External Accounts';
    }
}
