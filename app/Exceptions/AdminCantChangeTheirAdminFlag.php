<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class AdminCantChangeTheirAdminFlag extends Exception
{
    const MESSAGE = 'Another Admin will need to change the admin value for you.';

    public function __construct(string $message = self::MESSAGE, int $code = 0, Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
