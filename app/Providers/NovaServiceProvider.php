<?php

declare(strict_types=1);

namespace App\Providers;

use App\Nova\BloodPressureReading;
use App\Nova\Metrics\BloodPressureReadingsPerDay;
use App\Nova\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Register the Nova routes.
     */
    protected function routes()
    {
        Nova::routes();

        // since we are using the Laravel framework auth, there is
        // no need to load Nova's auth
//                ->withAuthenticationRoutes()
//                ->withPasswordResetRoutes()
//                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return \in_array($user->email, [
                \App\User::all()->toArray()
            ], true);
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new BloodPressureReadingsPerDay(),
//            new Help(),
        ];
    }

    /**
     * Displays Resources on left panel.
     * Checks if the NovaRequest user is an Admin.
     */
    protected function resources()
    {
        // we are overriding this guy, so don't load the parent.
        // parent::resources();

        $resources = [
            BloodPressureReading::class,
        ];

        $adminResources = [
            User::class,
        ];

        // if user is an Admin load both $resources and $adminResources;
        // else just load $resources...
        $isAdmin = app(NovaRequest::class)->user()->admin;
        if ($isAdmin) {
            Nova::resources(array_merge($resources, $adminResources));
        } else {
            Nova::resources($resources);
        }
    }
}
