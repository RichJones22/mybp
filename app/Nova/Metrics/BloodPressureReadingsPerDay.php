<?php

namespace App\Nova\Metrics;

use App\BloodPressureReading;
use Cake\Chronos\Chronos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;
use Laravel\Nova\Metrics\TrendResult;

class BloodPressureReadingsPerDay extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
//        $mData =  $this->countByDays($request, BloodPressureReading::class, 'date');

        /** @var BloodPressureReading $myBp */
        $myBp = app(BloodPressureReading::class);

        $column = 'date';
        $unit = Trend::BY_DAYS;
        $timezone = $request->timezone;

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

        $possibleDateResults = $this->getAllPossibleDateResults(
            $startingDate = $this->getAggregateStartingDate($request, $unit),
            $endingDate = Chronos::now(),
            $unit,
            $timezone,
            $request->twelveHourTime === 'true'
        );
        
        $results = array_merge($possibleDateResults, $results->mapWithKeys(function ($result) use ($request, $unit) {
            return [$this->formatAggregateResultDate(
                $result->date_result, $unit, $request->twelveHourTime === 'true'
            ) => round($result->aggregate, 0)];
        })->all());

        if (count($results) > $request->range) {
            array_shift($results);
        }

        return $this->result()->trend(
            $results
        );
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
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'blood-pressure-readings-per-day';
    }
}
