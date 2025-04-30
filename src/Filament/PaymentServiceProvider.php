<?php

namespace Lipe\Payment\Filament;

use Filament\PluginServiceProvider;
use Lipe\Payment\Filament\Resources\PaymentResource;
use Spatie\LaravelPackageTools\Package;

class PaymentServiceProvider extends PluginServiceProvider
{
    public static string $name = 'payment';

    protected array $resources = [
        PaymentResource::class,
    ];

    protected array $pages = [
        //
    ];

    protected array $widgets = [
        //
    ];

    protected array $styles = [
        //
    ];

    protected array $scripts = [
        //
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('payment');
    }
} 