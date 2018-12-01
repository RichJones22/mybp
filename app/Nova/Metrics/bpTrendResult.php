<?php

declare(strict_types=1);

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\TrendResult;

/**
 * Class bpTrendResult.
 */
class bpTrendResult extends TrendResult
{
    /**
     * bpTrendResult constructor.
     *
     * @param string|null $value
     */
    public function __construct(?string $value = null)
    {
        parent::__construct($value);
    }

    /**
     * @return $this|TrendResult
     */
    public function showMaxValue()
    {
        if (\is_array($this->trend)) {
            return $this->result(max($this->trend));
        }

        return $this;
    }
}
