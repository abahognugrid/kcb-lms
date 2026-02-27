<?php

namespace App\Exports;

use App\Actions\OtherReports\GetBorrowersReportDetailsAction;
use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class BorrowersExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return Collection
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.borrowers-report', [
            'records' => app(GetBorrowersReportDetailsAction::class)->filters($this->filters)->execute(),
            'partner' => auth()->user()->partner,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Borrowers';
    }
}
