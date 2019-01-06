<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Trends\Readings\Types;

use App\Nova\Metrics\Trends\Readings\ReadingsBase;

class SystolicReading extends ReadingsBase
{
    /** @var string */
    public $name = 'Systolic Readings';

    /** @var string */
    protected $columnToMax = 'systolic';

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey(): string
    {
        return 'systolic-reading';
    }
}
