<?php

namespace Tests\Feature;

use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Support\Facades\Route;
use Sis_medico\Http\Controllers\contable\VentasController;
use Tests\TestCase;

class MissingAccountingConfigurationViewTest extends TestCase
{
    public function test_missing_iva_configuration_message_can_be_rendered(): void
    {
        Route::get('/_test/errors/missing-iva', fn () => response()->view('errors.204', [
            'mensajes' => 'No se ha configurado el porcentaje del IVA.',
        ]));

        $response = $this->get('/_test/errors/missing-iva');

        $response
            ->assertOk()
            ->assertSeeText('Configuración requerida')
            ->assertSeeText('No se ha configurado el porcentaje del IVA.')
            ->assertSeeText('Volver al inicio');
    }

    public function test_sales_listing_is_not_blocked_by_the_iva_configuration_guard(): void
    {
        $controller = $this->app->make(VentasController::class);
        $dispatcher = $this->app->make(ControllerDispatcher::class);

        $indexMiddleware = $dispatcher->getMiddleware($controller, 'index');
        $storeMiddleware = $dispatcher->getMiddleware($controller, 'store');

        $this->assertSame(['auth'], $indexMiddleware);
        $this->assertCount(2, $storeMiddleware);
        $this->assertSame('auth', $storeMiddleware[0]);
        $this->assertIsCallable($storeMiddleware[1]);
    }
}
