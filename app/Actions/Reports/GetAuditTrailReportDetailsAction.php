<?php

namespace App\Actions\Reports;

use App\Models\Audit;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetAuditTrailReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected ?string $search = null;

    protected ?string $event = null;

    protected ?int $partnerId;

    protected bool $isAdmin = false;

    public function execute()
    {
        $query = Audit::query()->with('user')
            ->when(! $this->isAdmin && $this->partnerId, function ($query) {
                // Partner scoping - admin sees all, others see only their partner's data
                $query->where(function ($partnerQuery) {
                    $partnerQuery->where('partner_id', $this->partnerId)
                        ->orWhereHas('user', function ($subQuery) {
                            $subQuery->where('partner_id', $this->partnerId);
                        });
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ]);
            })
            ->when($this->event, function ($query) {
                $query->where('event', $this->event);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->whereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhere('url', 'like', '%' . $this->search . '%')
                        ->orWhere('user_agent', 'like', '%' . $this->search . '%')
                        ->orWhere('auditable_type', 'like', '%' . $this->search . '%')
                        ->orWhere('event', 'like', '%' . $this->search . '%');
                });
            });

        $query->latest('created_at');

        if ($this->perPage > 0) {
            return $query->paginate($this->perPage);
        }

        return $query->get();
    }

    public function paginate($perPage = 50): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function filters(array $details): self
    {
        $this->startDate = Arr::get($details, 'startDate', now()->startOfMonth()->toDateString());
        $this->endDate = Arr::get($details, 'endDate', now()->toDateString());

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        $this->search = Arr::get($details, 'search');
        $this->event = Arr::get($details, 'event');
        $this->partnerId = Arr::get($details, 'partnerId', 0);
        $this->isAdmin = (bool) Arr::get($details, 'isAdmin', false);

        return $this;
    }
}
