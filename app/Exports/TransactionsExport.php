<?php

namespace App\Exports;

use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Models\Partner;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return View
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.transactions-report', [
            'records' => app(GetTransactionsReportDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Transactions';
    }
}
