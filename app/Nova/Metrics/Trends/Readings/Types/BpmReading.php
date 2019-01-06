<?php

declare(strict_types=1);

namespace App\Nova\Metrics\Trends\Readings\Types;

use App\Nova\Metrics\Trends\Readings\ReadingsBase;

class BpmReading extends ReadingsBase
{
    /** @var string */
    public $name = 'PBM Readings';

    /** @var string */
    protected $columnToMax = 'bpm';

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'bpm-reading';
    }
}
