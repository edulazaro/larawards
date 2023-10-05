<?php

namespace EduLazaro\Larawards;

use Illuminate\Support\ServiceProvider;
use EduLazaro\Larawards\Console\Commands\MakeAwardCommand;

class LarawardsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__. '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([MakeAwardCommand::class]);
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function hola()
    {
        echo("holda");
    }
}