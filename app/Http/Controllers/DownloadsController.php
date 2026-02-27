<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadsController extends Controller
{
    public function index(): View
    {
        return view('downloads.index');
    }
    public function download(Request $request, string $notificationId)
    {
        $user = Auth::user();

        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->whereJsonContains('data->generated_by', $user->id)
            ->firstOrFail();

        $filePath = $notification->data['file_path'];
        $filename = $notification->data['filename'];

        if (!Storage::exists($filePath)) {
            return redirect()->route('downloads.index')
                ->with('error', 'Report file not found. It may have been deleted.');
        }

        return Storage::download($filePath, $filename);
    }
}
