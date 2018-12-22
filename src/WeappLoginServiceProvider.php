<?php

namespace jmluang\weapp;

use Illuminate\Support\ServiceProvider;
use jmluang\weapp\repositories\LoginRepository;

class WeappLoginServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // publish config file to /app/config
            __DIR__ . '/config/weapp.php' => config_path('weapp.php'),
        ], 'config');

        // publish databases
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WeappLoginInterface::class, LoginRepository::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [WeappLoginInterface::class];
    }
}
