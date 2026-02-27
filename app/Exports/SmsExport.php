<?php

namespace App\Exports;

use App\Actions\OtherReports\GetSmsReportDetailsAction;
use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Models\Partner;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class SmsExport implements FromView, WithTitle
{
    public function __construct(protected array $filters)
    {
    }

    /**
     * @return View
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        $records = app(GetSmsReportDetailsAction::class)->filters($this->filters)->execute();

        return view('excel.sms-report', [
            'records' => $records,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'SMS';
    }
}
