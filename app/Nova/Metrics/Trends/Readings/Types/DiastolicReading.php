<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Trends\Readings\Types;

use App\Nova\Metrics\Trends\Readings\ReadingsBase;

class DiastolicReading extends ReadingsBase
{
    /** @var string */
    public $name = 'Diastolic Readings';

    /** @var string */
    protected $columnToMax = 'diastolic';

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'diastolic-reading';
    }
}
