<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;

class GnugridCommissionsAndFeesController extends Controller
{
    public function commissions()
    {
        $commissions = JournalEntry::whereRaw("LOWER(account_name) LIKE ?", ['%gnugrid%'])->latest()->paginate(15);
        return view('gnugrid.commissions', compact('commissions'));
    }

    public function feesBreakdown()
    {
        $fees = config('lms.fees-breakdown');

        return view('gnugrid.fees-breakdown', compact('fees'));
    }
}
