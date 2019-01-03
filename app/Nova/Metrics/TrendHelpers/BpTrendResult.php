<?php

declare(strict_types=1);

namespace App\Nova\Metrics\TrendHelpers;

use Laravel\Nova\Metrics\TrendResult;

/**
 * Class BpTrendResult.
 */
class BpTrendResult extends TrendResult
{
    /**
     * BpTrendResult constructor.
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
        if (\is_array($this->trend) && \count($this->trend) > 0) {
            return $this->result(max($this->trend));
        }

        return $this;
    }
}
