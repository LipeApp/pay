<?php

namespace Lipe\Payment;

use Illuminate\Support\ServiceProvider;
use Lipe\Payment\Services\PaymentService;

class PaymentGatewaysServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payment-gateways.php', 'payment-gateways'
        );

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'payment-gateways');
        $this->loadRoutesFrom(__DIR__.'/routes/admin.php');

        $this->publishes([
            __DIR__.'/../config/payment-gateways.php' => config_path('payment-gateways.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/payment-gateways'),
        ], 'views');
    }
} 