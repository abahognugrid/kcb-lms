<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class TicketsExport implements FromView
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $query = Ticket::with(['user', 'agent']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return view('tickets.excel', [
            'tickets' => $query->orderByDesc('created_at')->get(),
            'partnerName' => Auth::user()->partner->name ?? 'N/A',
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
