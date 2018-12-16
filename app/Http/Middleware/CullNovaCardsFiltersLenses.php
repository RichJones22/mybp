<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nova\Events\ServingNova;

class CullNovaCardsFiltersLenses
{
    private $cull = [
        '/nova-api/users/filters',
        '/nova-api/users/lenses',
        '/nova-api/users/cards',
        '/nova-api/blood-pressure-readings/filters',
        '/nova-api/blood-pressure-readings/lenses',
        '/nova-api/blood-pressure-readings/cards',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\in_array($request->getRequestUri(), $this->cull, true)) {
            return response($request);
        }

        ServingNova::dispatch($request);

        return $next($request);
    }
}
