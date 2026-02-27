<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanApplicationReportController extends Controller
{
    public function index()
    {
        return view('reports.loans.applications');
    }
}
