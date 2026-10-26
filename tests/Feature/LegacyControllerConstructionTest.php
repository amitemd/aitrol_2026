<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\contable\VentasController;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Tests\TestCase;

class LegacyControllerConstructionTest extends TestCase
{
    public function test_sales_routes_restore_the_active_company_context_before_running_legacy_code(): void
    {
        $middlewares = Route::getRoutes()->getByName('venta_index')?->gatherMiddleware() ?? [];

        $this->assertContains('new.context', $middlewares);
    }

    public function test_route_inventory_can_be_generated_without_querying_application_tables(): void
    {
        ini_set('memory_limit', '1G');

        $this->artisan('route:list', ['--except-vendor' => true])
            ->assertSuccessful();
    }

    public function test_controllers_do_not_query_the_database_while_the_router_constructs_them(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        app(VentasController::class);
        app(EmisionDocumentosController::class);
        app(ApiFacturacionController::class);

        $this->assertSame([], $queries, 'Los constructores de controladores no deben consultar la base de datos.');
    }
}
