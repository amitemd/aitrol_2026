<?php

namespace App\Modules;

use App\Modules\Billing\Application\Contracts\SalesInvoiceReadRepository;
use App\Modules\Billing\Infrastructure\Persistence\LaravelSalesInvoiceReadRepository;
use App\Modules\ClinicalCare\Application\Contracts\PatientSearchRepository;
use App\Modules\ClinicalCare\Infrastructure\Persistence\LaravelPatientSearchRepository;
use App\Modules\Diagnostics\Application\Contracts\DiagnosisCatalogRepository;
use App\Modules\Diagnostics\Infrastructure\Persistence\LaravelDiagnosisCatalogRepository;
use App\Modules\Hospitalization\Application\Contracts\HospitalizedPatientRepository;
use App\Modules\Hospitalization\Infrastructure\Persistence\LaravelHospitalizedPatientRepository;
use App\Modules\Institutional\Application\Contracts\CompanyAccessRepository;
use App\Modules\Institutional\Infrastructure\Persistence\LaravelCompanyAccessRepository;
use App\Modules\Shared\Application\ContextMap;
use App\Modules\SupportIntegrations\Application\Contracts\AuditTrailRepository;
use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Infrastructure\Logging\LaravelSystemLogRepository;
use App\Modules\SupportIntegrations\Infrastructure\Persistence\LaravelAuditTrailRepository;
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

        $this->app->bind(
            CompanyAccessRepository::class,
            LaravelCompanyAccessRepository::class,
        );

        $this->app->bind(
            PatientSearchRepository::class,
            LaravelPatientSearchRepository::class,
        );

        $this->app->bind(
            DiagnosisCatalogRepository::class,
            LaravelDiagnosisCatalogRepository::class,
        );

        $this->app->bind(
            HospitalizedPatientRepository::class,
            LaravelHospitalizedPatientRepository::class,
        );

        $this->app->bind(
            SystemLogRepository::class,
            LaravelSystemLogRepository::class,
        );

        $this->app->bind(
            AuditTrailRepository::class,
            LaravelAuditTrailRepository::class,
        );
    }
}
