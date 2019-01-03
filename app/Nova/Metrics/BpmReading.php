<?php

declare(strict_types=1);

namespace App\Nova\Metrics;

use App\Nova\Metrics\TrendHelpers\BpColumnMaxReadingOf;
use App\Nova\Metrics\TrendHelpers\BpTrendResult;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;

class BpmReading extends Trend
{
    /** @var string */
    public $name = 'PBM Readings';

    /** @var string */
    private $columnToMax = 'bpm';

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
        return [
            90 => '90 Days',
            60 => '60 Days',
            30 => '30 Days',
            10 => '10 Days',
            5 => '5 Days',
            1 => '1 Days',
        ];
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
