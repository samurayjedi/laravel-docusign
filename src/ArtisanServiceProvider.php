<?php

namespace Samurayjedi\LaravelDocusign;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Samurayjedi\LaravelDocusign\Console\RouteCommand;
use Samurayjedi\LaravelDocusign\Console\SetupCommand;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            SetupCommand::class,
            RouteCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SetupCommand::class,
            RouteCommand::class,
        ];
    }
}