<?php

namespace App\Actions\OtherReports;

use App\Models\Transaction;
use App\Notifications\SmsNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GetSmsReportDetailsAction
{
    protected string $startDate = '';
    protected string $endDate = '';
    protected ?int $partnerId;
    protected int $perPage = 0;

    public function execute(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = DatabaseNotification::query()
            ->with('notifiable')
            ->where('type', SmsNotification::class)
            ->when($this->partnerId, function ($query) {
                $query->where('partner_id', $this->partnerId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
            });

        $query->latest();

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    public function paginate($perPage = 100): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());
        $this->partnerId = Arr::get($details, 'partnerId', Auth::user()?->partner_id);

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        if (Carbon::parse($this->startDate)->isAfter($this->endDate)) {
            $this->startDate = $this->endDate;
        }

        return $this;
    }
}
