<?php

namespace App\Modules;

use App\Modules\Billing\Application\Contracts\SalesInvoiceReadRepository;
use App\Modules\Billing\Infrastructure\Persistence\LaravelSalesInvoiceReadRepository;
use App\Modules\Shared\Application\ContextMap;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ContextMap::class,
            fn (Application $app): ContextMap => new ContextMap(
                (array) $app['config']->get('modular_architecture', []),
            ),
        );

        $this->app->bind(
            SalesInvoiceReadRepository::class,
            LaravelSalesInvoiceReadRepository::class,
        );
    }
}
