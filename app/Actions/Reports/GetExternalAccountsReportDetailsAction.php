<?php

namespace App\Actions\Reports;

use App\Models\ExternalAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class GetExternalAccountsReportDetailsAction
{
    protected string $startDate = '';

    protected string $endDate = '';

    protected int $perPage = 0;

    protected ?string $serviceProvider = null;

    protected ?int $partnerId;

    public function execute()
    {
        $query = ExternalAccount::query()
            ->with(['partner'])
            ->where('partner_id', $this->partnerId)
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay()->toDateTimeString(),
                    Carbon::parse($this->endDate)->endOfDay()->toDateTimeString(),
                ]);
            })
            ->when($this->serviceProvider, function ($query) {
                $query->where('service_provider', $this->serviceProvider);
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
        $this->serviceProvider = Arr::get($details, 'serviceProvider');
        $this->partnerId = Arr::get($details, 'partnerId', 0);

        if (Carbon::parse($this->endDate)->isFuture()) {
            $this->endDate = now()->toDateString();
        }

        return $this;
    }
}
