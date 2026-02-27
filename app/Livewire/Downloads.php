<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Downloads extends Component
{
    use WithPagination;

    public string $search = '';
    public string $reportTypeFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'reportTypeFilter' => ['except' => ''],
    ];

    public function render()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('type', 'App\Notifications\ReportGeneratedNotification')
            ->whereJsonDoesntContainKey('data->error')
            ->whereJsonContains('data->generated_by', $user->id)
            ->when($this->search, function ($query) {
                $query->where('data->filename', 'like', '%'.$this->search.'%');
            })
            ->when($this->reportTypeFilter, function ($query) {
                $query->whereJsonContains('data->report_type', $this->reportTypeFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $reportTypes = $user->notifications()
            ->where('type', 'App\Notifications\ReportGeneratedNotification')
            ->whereJsonDoesntContainKey('data->error')
            ->whereJsonContains('data->generated_by', $user->id)
            ->get()
            ->pluck('data.report_type')
            ->unique()
            ->filter()
            ->values();

        return view('livewire.downloads', [
            'notifications' => $notifications,
            'reportTypes' => $reportTypes,
        ]);
    }

    public function downloadReport(string $notificationId)
    {
        $user = Auth::user();

        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->whereJsonContains('data->generated_by', $user->id)
            ->firstOrFail();

        $filePath = $notification->data['file_path'];
        $filename = $notification->data['filename'];

        if (!Storage::exists($filePath)) {
            session()->flash('error', 'Report file not found. It may have been deleted.');
            return redirect()->back();
        }

        return Storage::download($filePath, $filename);
    }

    public function deleteReport(string $notificationId): void
    {
        $user = Auth::user();

        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->whereJsonContains('data->generated_by', $user->id)
            ->firstOrFail();

        $filePath = Arr::get($notification->data, 'file_path', '');

        // Delete the file from storage
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete the notification
        $notification->delete();

        session()->flash('success', 'Report deleted successfully.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedReportTypeFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'reportTypeFilter']);
        $this->resetPage();
    }
}
