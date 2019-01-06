<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Trends\Readings;

use App\Nova\Metrics\Trends\Readings\ReadingsHelpers\BpColumnMaxReadingOf;
use App\Nova\Metrics\Trends\Readings\ReadingsHelpers\BpTrendResult;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;

class ReadingsBase extends Trend
{
    /** @var string */
    protected $columnToMax;

    /**
     * Calculate the value of the metric.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this
            ->customCountByDays($request)
            ->showMaxValue();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return BpColumnMaxReadingOf::BpColumnMaxReadingRanges;
    }

    /**
     * @return int
     */
    public function cacheFor(): int
    {
//         return now()->addMinutes(5);

        return 0; // overrides cacheFor in Metric, which returns an int.
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'bpm-reading';
    }

    /**
     * @param Request $request
     *
     * @return BpTrendResult
     */
    public function customCountByDays(Request $request): BpTrendResult
    {
        return BpColumnMaxReadingOf::boot()
            ->bpColumnMaxReadingOf($request, $this->columnToMax);
    }
}
