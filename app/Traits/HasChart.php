<?php

namespace App\Traits;

use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait HasChart
{
    /**
     * @param int $upTo
     * @param string $format
     * @return Collection
     */
    protected function getDates(int $upTo = 29, string $format = 'M d'): \Illuminate\Support\Collection
    {
        $dates = collect();

        for ($i = $upTo; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format($format));
        }

        return $dates;
    }

    protected function getMonths(int $upTo = 11, string $format = 'Y M'): Collection
    {
        $months = collect();

        for ($i = $upTo; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format($format)] = 0;
        }

        return $months;
    }

    protected function makeLineChartModel(?string $chartTitle = null, bool $multiLine = true): LineChartModel
    {
        $model = (new LineChartModel());

        if ($chartTitle) {
            $model->setTitle($chartTitle);
        }

        $model->setAnimated(true);

        if ($multiLine) {
            $model->multiLine();
        }

        return $model->setSmoothCurve();
    }

    protected function forgetCacheKey(?string $cacheKey = null): void
    {
        if (! $cacheKey) {
            $cacheKey = $this->cacheKey;
        }

        cache()->forget($cacheKey);
    }
}
