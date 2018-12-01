<?php

declare(strict_types=1);

namespace App\Nova\Metrics;

use App\BloodPressureReading;
use Cake\Chronos\Chronos;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;

/**
 * Class BloodPressureReadingsPerDay.
 */
class BloodPressureReadingsPerDay extends Trend
{
    /** @var string */
    public $name = 'Systolic Readings';

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
    public function uriKey(): string
    {
        return 'blood-pressure-readings-per-day';
    }

    /**
     * @param Request $request
     *
     * @return bpTrendResult
     */
    public function customCountByDays(Request $request): bpTrendResult
    {
        /** @var BloodPressureReading $myBp */
        $myBp = app(BloodPressureReading::class);

        $column = 'date';
        $unit = Trend::BY_DAYS;
        $timezone = $request->timezone;

        /** @var string $expression */
        $expression = (string) TrendDateExpressionFactory::make(
            $myBp->newQuery(), $column, $unit, $timezone
        );

        /** @var Collection $myResult */
        $results = $myBp
            ->newQuery()
            ->select(DB::raw("{$expression} as date_result, max(systolic) as aggregate"))
            ->groupby('date')
            ->orderBy('date')
            ->get();

        /** @var array $possibleDateResults */
        $possibleDateResults = $this->getAllPossibleDateResults(
            $startingDate = $this->getAggregateStartingDate($request, $unit),
            $endingDate = Chronos::now(),
            $unit,
            $timezone,
            'true' === $request->twelveHourTime
        );

        $results = array_merge($possibleDateResults, $results->mapWithKeys(function ($result) use ($request, $unit) {
            return [
                $this->formatAggregateResultDate(
                    $result->date_result, $unit, 'true' === $request->twelveHourTime
                ) => round($result->aggregate, 0),
            ];
        })->all());

        if (\count($results) > $request->range) {
            array_shift($results);
        }

        $myResult = $this->result()->trend(
            $results
        );

        return (new bpTrendResult($myResult->value))->trend($myResult->trend);
    }
}
