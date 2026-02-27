<?php

namespace App\Http\Controllers\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    public function create(): View
    {
        return view('loan-repayments.create');
    }
}
