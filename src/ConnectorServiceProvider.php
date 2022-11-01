<?php

namespace Awesome\Connector;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Awesome\Connector\Contracts\Connector as ConnectorContract;

class ConnectorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/connect.php' => base_path('config/connect.php')
            ], 'config');
        }
    }

    public function register()
    {
        $this->app->singleton(ConnectorContract::class, function () {
            return new Connector();
        });

        $this->app->alias(ConnectorContract::class, 'connector');
    }

    public function provides()
    {
        return [
            'connector',
            ConnectorContract::class
        ];
    }
}
