<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\LoanProductFee;

class BorrowersReportController extends Controller
{
    public function index()
    {
        return view('reports.borrowers.borrowers-report');
    }
}
