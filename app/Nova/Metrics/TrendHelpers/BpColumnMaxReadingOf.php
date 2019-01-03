<?php

declare(strict_types=1);

namespace App\Nova\Metrics\TrendHelpers;

use App\BloodPressureReading;
use Cake\Chronos\Chronos;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendDateExpressionFactory;

class BpColumnMaxReadingOf extends Trend
{
    public static function boot()
    {
        return new self();
    }

    /**
     * @param Request $request
     * @param string  $maxByColumn
     *
     * @return BpTrendResult
     */
    public function bpColumnMaxReadingOf(Request $request, string $maxByColumn): BpTrendResult
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
            ->select(DB::raw("{$expression} as date_result, max({$maxByColumn}) as aggregate"))
            ->groupby('date')
            ->orderBy('date')
            ->get();

        /** @var array $possibleDateResults */
        $possibleDateResults = $this->getAllPossibleDateResults(
            $this->getAggregateStartingDate($request, $unit),
            Chronos::now(),
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

        return (new BpTrendResult($myResult->value))->trend($myResult->trend);
    }
}
