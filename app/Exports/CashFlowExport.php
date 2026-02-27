<?php

namespace App\Exports;

use App\Actions\Reports\GetCashFlowDetailsAction;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashFlowExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        return view('excel.cash-flow-report', [
            'journalEntries' => app(GetCashFlowDetailsAction::class)
                ->filters($this->filters)
                ->execute(),
            'partner' => auth()->user()->partner,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Cash Flow';
    }
}
