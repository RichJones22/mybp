<?php

declare(strict_types=1);

namespace App\Nova;

use App\Nova\Metrics\Trends\Readings\Types\BpmReading;
use App\Nova\Metrics\Trends\Readings\Types\DiastolicReading;
use App\Nova\Metrics\Trends\Readings\Types\SystolicReading;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;

class BloodPressureReading extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\BloodPressureReading';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'date';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'date', 'systolic', 'diastolic', 'bpm', 'user_id',
    ];

    public static $with = [
        'user',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable()->hideFromIndex(),
            DateTime::make('Date Recorded', 'date')
                ->format('lll') // see moments.js for a list of formats.
                ->creationRules('unique:blood_pressure_readings,date')
                ->sortable(),
            Number::make('Systolic')->sortable(),
            Number::make('Diastolic')->sortable(),
            Number::make('bpm')->sortable(),
            Number::make('user_id')
                ->hideFromIndex()
                ->hideFromDetail()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new SystolicReading(),
            new DiastolicReading(),
            new BpmReading(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
