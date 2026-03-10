<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::query();

        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->search) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest()->paginate(50);

        return view('logs.index', compact('logs'));
    }
}
