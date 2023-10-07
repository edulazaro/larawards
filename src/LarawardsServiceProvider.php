<?php

namespace EduLazaro\Larawards;

use Illuminate\Support\ServiceProvider;

use EduLazaro\Larawards\Models\Reward;
use EduLazaro\Larawards\Observers\RewardObserver;
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
        Reward::observe(RewardObserver::class);

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
}