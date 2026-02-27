<?php

namespace App\Http\Controllers;

use App\Actions\OtherReports\GetTransactionsReportDetailsAction;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SmsReportController extends Controller
{
    public function index()
    {
        return view('reports.others.sms');
    }
}
