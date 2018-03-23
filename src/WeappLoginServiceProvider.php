<?php

namespace jmluang\weapp;

use Illuminate\Support\ServiceProvider;

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
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoginInterface::class, LoginService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [LoginInterface::class];
    }
}
