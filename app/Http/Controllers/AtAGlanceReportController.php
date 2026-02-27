<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AtAGlanceReportController extends Controller
{
    public function atAGlanceReport(Request $request)
    {
        return view('reports.others.at_a_glance');
    }

    public function dailyReport(Request $request)
    {
        return view('reports.others.daily-report');
    }
}
