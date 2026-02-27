<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class PaymentHistoryVelocityReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.others.payment-history-velocity', [
            'loan' => null
        ]);
    }
}
