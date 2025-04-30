<?php

namespace Lipe\Payment;

use Illuminate\Support\ServiceProvider;
use Lipe\Payment\Gateways\ClickGateway;
use Lipe\Payment\Gateways\IpakYuliGateway;
use Lipe\Payment\Gateways\PaymeGateway;
use Lipe\Payment\Filament\PaymentServiceProvider as FilamentPaymentServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payment-gateways.php', 'payment-gateways'
        );

        $this->app->singleton('payment', function ($app) {
            return new PaymentManager($app);
        });

        $this->app->register(FilamentPaymentServiceProvider::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/payment-gateways.php' => config_path('payment-gateways.php'),
        ], 'payment-gateways-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'payment-gateways-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
