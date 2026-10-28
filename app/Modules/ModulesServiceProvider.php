<?php

namespace App\Modules;

use App\Modules\AccountingFinance\Application\Contracts\JournalEntryLineReadRepository;
use App\Modules\AccountingFinance\Infrastructure\Persistence\LaravelJournalEntryLineReadRepository;
use App\Modules\Billing\Application\Contracts\SalesInvoiceDirectoryRepository;
use App\Modules\Billing\Application\Contracts\SalesInvoiceReadRepository;
use App\Modules\Billing\Infrastructure\Persistence\LaravelSalesInvoiceDirectoryRepository;
use App\Modules\Billing\Infrastructure\Persistence\LaravelSalesInvoiceReadRepository;
use App\Modules\ClinicalCare\Application\Contracts\PatientDirectoryRepository;
use App\Modules\ClinicalCare\Application\Contracts\PatientSearchRepository;
use App\Modules\ClinicalCare\Infrastructure\Persistence\LaravelPatientDirectoryRepository;
use App\Modules\ClinicalCare\Infrastructure\Persistence\LaravelPatientSearchRepository;
use App\Modules\Diagnostics\Application\Contracts\DiagnosisCatalogRepository;
use App\Modules\Diagnostics\Infrastructure\Persistence\LaravelDiagnosisCatalogRepository;
use App\Modules\Hospitalization\Application\Contracts\HospitalizedPatientRepository;
use App\Modules\Hospitalization\Infrastructure\Persistence\LaravelHospitalizedPatientRepository;
use App\Modules\HumanResources\Application\Contracts\EmployeeDirectoryRepository;
use App\Modules\HumanResources\Infrastructure\Persistence\LaravelEmployeeDirectoryRepository;
use App\Modules\Institutional\Application\Contracts\CompanyAccessRepository;
use App\Modules\Institutional\Infrastructure\Persistence\LaravelCompanyAccessRepository;
use App\Modules\InventoryOperations\Application\Contracts\InventoryStockReadRepository;
use App\Modules\InventoryOperations\Infrastructure\Persistence\LaravelInventoryStockReadRepository;
use App\Modules\Shared\Application\ContextMap;
use App\Modules\Shared\Application\Contracts\ArchitectureCatalog;
use App\Modules\Shared\Application\Contracts\RouteReadinessGateway;
use App\Modules\Shared\Infrastructure\Configuration\LaravelArchitectureCatalog;
use App\Modules\Shared\Infrastructure\Routing\LaravelRouteReadinessGateway;
use App\Modules\SupportIntegrations\Application\Contracts\AuditTrailRepository;
use App\Modules\SupportIntegrations\Application\Contracts\RequestArchitectureInspector;
use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Infrastructure\Architecture\ReflectionRequestArchitectureInspector;
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

        $this->app->bind(ArchitectureCatalog::class, LaravelArchitectureCatalog::class);
        $this->app->bind(RouteReadinessGateway::class, LaravelRouteReadinessGateway::class);

        $this->app->bind(
            SalesInvoiceReadRepository::class,
            LaravelSalesInvoiceReadRepository::class,
        );

        $this->app->bind(
            SalesInvoiceDirectoryRepository::class,
            LaravelSalesInvoiceDirectoryRepository::class,
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
            PatientDirectoryRepository::class,
            LaravelPatientDirectoryRepository::class,
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
            InventoryStockReadRepository::class,
            LaravelInventoryStockReadRepository::class,
        );

        $this->app->bind(
            JournalEntryLineReadRepository::class,
            LaravelJournalEntryLineReadRepository::class,
        );

        $this->app->bind(
            EmployeeDirectoryRepository::class,
            LaravelEmployeeDirectoryRepository::class,
        );

        $this->app->bind(
            SystemLogRepository::class,
            LaravelSystemLogRepository::class,
        );

        $this->app->bind(
            AuditTrailRepository::class,
            LaravelAuditTrailRepository::class,
        );

        $this->app->singleton(
            RequestArchitectureInspector::class,
            ReflectionRequestArchitectureInspector::class,
        );
    }
}
