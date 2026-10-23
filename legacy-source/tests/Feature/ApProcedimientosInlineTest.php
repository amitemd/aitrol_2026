<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\ApProcedimiento;
use Sis_medico\Http\Controllers\archivo_plano\ApProcedimientosController;
use Sis_medico\User;
use Tests\TestCase;

class ApProcedimientosInlineTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';
        // Pruebas del catálogo: no arrancar observadores de otros módulos.
        $app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function ($app) {
            $providers = array_values(array_filter($app['config']->get('app.providers'), function ($provider) {
                return strpos($provider, 'Illuminate\\') === 0;
            }));
            $app['config']->set('app.providers', $providers);
        });
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'catalog_test', 'database.connections.catalog_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        $this->assertSame(realpath(__DIR__.'/../../'), realpath(base_path()));
        $this->assertSame(realpath(base_path('app/Http/Controllers/archivo_plano/ApProcedimientosController.php')),
            realpath((new \ReflectionClass(ApProcedimientosController::class))->getFileName()));
        Schema::create('ct_configuraciones', function (Blueprint $table) {
            $table->integer('id'); $table->string('iva')->nullable(); $table->decimal('ice', 10, 2)->default(0);
        });
        // Una tasa distinta al antiguo valor fijo detecta regresiones.
        DB::table('ct_configuraciones')->insert(['id' => 3, 'iva' => '0.14']);
        Schema::create('seguros', function (Blueprint $table) {
            $table->integer('id'); $table->string('nombre'); $table->integer('inactivo');
        });
        Schema::create('convenio', function (Blueprint $table) {
            $table->integer('id_nivel'); $table->integer('id_seguro');
            $table->string('id_empresa'); $table->integer('estado');
        });
        Schema::create('ap_procedimiento_nivel', function (Blueprint $table) {
            $table->integer('id_procedimiento'); $table->integer('cod_conv');
        });
        Schema::create('ap_tipo_examen', function (Blueprint $table) {
            $table->string('tipo'); $table->integer('estado');
        });
        Schema::create('ap_procedimiento', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo'); $table->string('codigo'); $table->text('descripcion');
            $table->float('cantidad'); $table->decimal('valor', 10, 4);
            $table->decimal('iva', 10, 2)->nullable(); $table->decimal('iva_nuevo', 10, 2)->default(0);
            $table->integer('estado')->default(1); $table->integer('id_seguro')->nullable();
            $table->string('id_usuariocrea'); $table->string('id_usuariomod');
            $table->string('ip_creacion'); $table->string('ip_modificacion');
            $table->timestamps();
        });
        DB::table('seguros')->insert([
            ['id' => 2, 'nombre' => 'IESS', 'inactivo' => 1],
            ['id' => 5, 'nombre' => 'MSP', 'inactivo' => 1],
        ]);
        DB::table('ap_tipo_examen')->insert([
            ['tipo' => 'EQ', 'estado' => 1], ['tipo' => 'IME', 'estado' => 1], ['tipo' => 'OLD', 'estado' => 0],
        ]);
        ApProcedimiento::create([
            'id' => 1, 'tipo' => 'EQ', 'codigo' => '600001', 'descripcion' => 'CISTOSCOPIO',
            'cantidad' => 0, 'valor' => 0, 'iva' => 0, 'iva_nuevo' => 0, 'estado' => 1,
            'id_seguro' => 5, 'id_usuariocrea' => 'original', 'id_usuariomod' => 'original',
            'ip_creacion' => 'original', 'ip_modificacion' => 'original',
        ]);
        Auth::setUser(new User(['id' => 'editor', 'id_tipo_usuario' => 1]));
    }

    protected function tearDown(): void
    {
        DB::purge('catalog_test');
        parent::tearDown();
    }

    private function saveFields(array $fields)
    {
        return (new ApProcedimientosController())->guardarInline(
            Request::create('/administracion/procedimientos/inline/1', 'POST', $fields), 1
        );
    }

    public function testPartialSaveDefaultsQuantityAndPreservesInsuranceAndCreationAudit(): void
    {
        $response = $this->saveFields(['descripcion' => 'CISTOSCOPIO EDITADO', 'estado' => 0]);
        $this->assertSame(200, $response->getStatusCode());
        $item = ApProcedimiento::findOrFail(1);
        $this->assertEquals(1, $item->cantidad);
        $this->assertSame('EQ', $item->tipo);
        $this->assertEquals(5, $item->id_seguro);
        $this->assertEquals(1, $item->estado);
        $this->assertSame('original', $item->id_usuariocrea);
        $this->assertSame('original', $item->ip_creacion);
        $this->assertSame('editor', $item->id_usuariomod);
    }

    public function testSaveAndReloadPreservesLargeCodesExactly(): void
    {
        $code = '23090111490422721';
        $response = $this->saveFields(['codigo' => $code, 'tipo' => 'IME', 'cantidad' => 3, 'valor' => '693.1234']);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($code, $response->getData(true)['item']['codigo']);
        $item = ApProcedimiento::findOrFail(1);
        $this->assertSame($code, $item->codigo);
        $this->assertSame('IME', $item->tipo);
        $this->assertEquals(693.1234, $item->valor);
        $this->assertEquals(3, $item->cantidad);
    }

    public function testIvaYesAndNoApplyToBothCalculationFields(): void
    {
        $this->saveFields(['aplica_iva' => '1']);
        $item = ApProcedimiento::findOrFail(1);
        $this->assertEquals(0.14, $item->iva);
        $this->assertEquals(0.14, $item->iva_nuevo);
        $this->saveFields(['aplica_iva' => '0']);
        $item->refresh();
        $this->assertEquals(0, $item->iva);
        $this->assertEquals(0, $item->iva_nuevo);
    }

    public function testSavingYesUsesCurrentConfigurationInsteadOfOldItemRates(): void
    {
        ApProcedimiento::findOrFail(1)->update(['iva' => 0.12, 'iva_nuevo' => 0.15]);
        foreach (['0.14' => 0.14, '16' => 0.16, '0.12' => 0.12, '0' => 0] as $configured => $expected) {
            DB::table('ct_configuraciones')->where('id', 3)->update(['iva' => $configured]);
            $response = $this->saveFields(['aplica_iva' => '1', 'valor' => '25.0000', 'iva' => 99, 'iva_nuevo' => 99]);
            $this->assertSame(200, $response->getStatusCode());
            $item = ApProcedimiento::findOrFail(1);
            $this->assertEquals($expected, $item->iva);
            $this->assertEquals($expected, $item->iva_nuevo);
            $this->assertEquals(25, $item->valor);
        }
    }

    public function testOtherFieldAutosaveDoesNotRewriteExistingRates(): void
    {
        ApProcedimiento::findOrFail(1)->update(['iva' => 0.12, 'iva_nuevo' => 0.15]);
        DB::table('ct_configuraciones')->delete();
        $this->saveFields(['valor' => '25.0000']);
        $item = ApProcedimiento::findOrFail(1);
        $this->assertEquals(0.12, $item->iva);
        $this->assertEquals(0.15, $item->iva_nuevo);
        $this->assertEquals(25, $item->valor);
        $this->assertSame(200, $this->saveFields(['aplica_iva' => '0'])->getStatusCode());
        $item->refresh();
        $this->assertEquals(0, $item->iva);
        $this->assertEquals(0, $item->iva_nuevo);
    }

    public function testInvalidOrMissingIvaConfigurationPreventsPartialSave(): void
    {
        $original = ApProcedimiento::findOrFail(1)->getAttributes();
        foreach ([null, 'no-numerico', '-0.15', '101', 'missing'] as $invalid) {
            if ($invalid === 'missing') {
                DB::table('ct_configuraciones')->delete();
            } else {
                DB::table('ct_configuraciones')->where('id', 3)->update(['iva' => $invalid]);
            }
            try {
                $this->saveFields(['aplica_iva' => '1', 'valor' => '25.0000']);
                $this->fail('No debe guardar con una configuración de IVA inválida.');
            } catch (\Illuminate\Validation\ValidationException $error) {
                $this->assertArrayHasKey('aplica_iva', $error->errors());
            }
            $this->assertSame($original, ApProcedimiento::findOrFail(1)->getAttributes());
        }
    }

    public function testCreateAndEditFormsAlsoUseSystemIvaInsteadOfSubmittedRate(): void
    {
        $previousIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        try {
            $controller = new ApProcedimientosController();
            $fields = ['txttipo' => 'EQ', 'txtcodigo' => '600099', 'txtdescripcion' => 'PRUEBA IVA',
                'txtcantidad' => 2, 'txtvalor' => '100.0000', 'txtiva' => '1', 'txtestado' => '1'];
            $response = $controller->store(Request::create('/', 'POST', $fields));
            $this->assertSame(302, $response->getStatusCode());
            $item = ApProcedimiento::where('codigo', '600099')->firstOrFail();
            $this->assertEquals(0.14, $item->iva);
            $this->assertEquals(0.14, $item->iva_nuevo);
            $this->assertEquals(100, $item->valor);

            DB::table('ct_configuraciones')->where('id', 3)->update(['iva' => '17']);
            $fields['id_procedimiento'] = $item->id;
            $fields['txtiva'] = '0.15'; // Un formulario antiguo no impone su tasa.
            $response = $controller->update(Request::create('/', 'POST', $fields));
            $this->assertSame(302, $response->getStatusCode());
            $item->refresh();
            $this->assertEquals(0.17, $item->iva);
            $this->assertEquals(0.17, $item->iva_nuevo);
            $this->assertEquals(100, $item->valor);

            $fields['txtiva'] = '0';
            $controller->update(Request::create('/', 'POST', $fields));
            $item->refresh();
            $this->assertEquals(0, $item->iva);
            $this->assertEquals(0, $item->iva_nuevo);
        } finally {
            if ($previousIp === null) {
                unset($_SERVER['REMOTE_ADDR']);
            } else {
                $_SERVER['REMOTE_ADDR'] = $previousIp;
            }
        }
    }

    public function testInvalidFieldsDoNotPartiallyUpdateTheRecord(): void
    {
        foreach ([
            ['cantidad' => 0], ['cantidad' => -1], ['valor' => '1000000'],
            ['valor' => '1.12345'], ['tipo' => 'OLD'], ['tipo' => 'INVALID'],
            ['codigo' => '9223372036854775808'], ['codigo' => 'abc'],
            ['descripcion' => ' '], ['aplica_iva' => '0.15'], ['codigo' => ['1']],
        ] as $invalid) {
            $response = $this->saveFields($invalid + ['descripcion' => 'NO GUARDAR']);
            $this->assertSame(422, $response->getStatusCode(), json_encode($invalid));
            $this->assertSame('CISTOSCOPIO', ApProcedimiento::findOrFail(1)->descripcion);
        }
    }

    public function testDeleteDeactivatesAndRejectsDelayedAutosave(): void
    {
        $controller = new ApProcedimientosController();
        $response = $controller->eliminarInline(Request::create('/', 'POST'), 1);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals(0, ApProcedimiento::findOrFail(1)->estado);
        $this->assertSame(409, $this->saveFields(['valor' => '88'])->getStatusCode());
        $this->assertEquals(0, ApProcedimiento::findOrFail(1)->valor);
        $view = $controller->index(Request::create('/', 'GET'));
        $this->assertSame(0, $view->getData()['procedimientos']->total());
    }

    public function testUnauthorizedUserCannotUpdateDeleteOrSearchCatalog(): void
    {
        Auth::setUser(new User(['id' => 'reader', 'id_tipo_usuario' => 3]));
        $this->assertSame(403, $this->saveFields(['valor' => '10'])->getStatusCode());
        $this->assertSame(403, (new ApProcedimientosController())->eliminarInline(Request::create('/', 'POST'), 1)->getStatusCode());
        $this->assertEquals(1, ApProcedimiento::findOrFail(1)->estado);
        $this->assertEquals(0, ApProcedimiento::findOrFail(1)->valor);
    }

    public function testSearchByCodeAndPaginationRetainFilter(): void
    {
        $view = (new ApProcedimientosController())->index(Request::create(
            '/administracion/procedimientos', 'GET', ['descripcion' => '600001']
        ));
        $data = $view->getData();
        $this->assertSame(1, $data['procedimientos']->total());
        $this->assertStringContainsString('descripcion=600001', $data['procedimientos']->url(2));
        $this->assertEquals(['EQ', 'IME'], $data['tipos']->values()->all());
    }

    public function testInsuranceUsesOnlyItemAssignmentAndNullDisplaysBlank(): void
    {
        // Todos comparten nivel/convenios: eso no debe asignarles seguros.
        DB::table('convenio')->insert([
            ['id_nivel' => 1, 'id_seguro' => 2, 'id_empresa' => 'A', 'estado' => 1],
            ['id_nivel' => 1, 'id_seguro' => 5, 'id_empresa' => 'A', 'estado' => 1],
        ]);
        foreach ([2 => null, 3 => 2, 4 => 999] as $id => $idSeguro) {
            $item = ApProcedimiento::findOrFail(1)->replicate();
            $item->id = $id;
            $item->codigo = (string) (600000 + $id);
            $item->id_seguro = $idSeguro;
            $item->save();
        }
        foreach ([1, 2, 3, 4] as $id) {
            DB::table('ap_procedimiento_nivel')->insert(['id_procedimiento' => $id, 'cod_conv' => 1]);
        }
        ApProcedimiento::findOrFail(1)->update(['iva' => 0, 'iva_nuevo' => 0.15]);
        session(['id_empresa' => 'A']);
        $controller = new ApProcedimientosController();
        $all = $controller->index(Request::create('/', 'GET'))->getData();
        $this->assertSame(4, $all['procedimientos']->total());
        $this->assertSame(['MSP', '', 'IESS', ''], $all['procedimientos']->pluck('nombre_seguro')->all());
        $this->assertEquals([2, 5], $all['seguros']->pluck('id')->all());

        $data = $controller->index(Request::create('/', 'GET', [
            'descripcion' => '600001', 'tipo' => 'EQ', 'iva' => 'si', 'seguro' => 5,
        ]))->getData();
        $this->assertSame(1, $data['procedimientos']->total());
        $this->assertSame('MSP', $data['procedimientos']->first()->nombre_seguro);
        foreach (['descripcion=600001', 'tipo=EQ', 'iva=si', 'seguro=5'] as $parameter) {
            $this->assertStringContainsString($parameter, $data['procedimientos']->url(2));
        }
        foreach ([5 => [1], 2 => [3]] as $insurance => $expectedIds) {
            $filtered = $controller->index(Request::create('/', 'GET', ['seguro' => $insurance]))->getData();
            $this->assertEquals($expectedIds, $filtered['procedimientos']->pluck('id')->all());
        }
        foreach ([['tipo' => 'IME', 'seguro' => 5], ['iva' => 'no', 'seguro' => 5]] as $filter) {
            $this->assertSame(0, $controller->index(Request::create('/', 'GET', $filter))
                ->getData()['procedimientos']->total());
        }
        session(['id_empresa' => 'B']);
        $data = $controller->index(Request::create('/', 'GET', ['seguro' => 5]))->getData();
        $this->assertEquals([1], $data['procedimientos']->pluck('id')->all());
        $this->assertSame('MSP', $data['procedimientos']->first()->nombre_seguro);
        session()->forget('id_empresa');
    }

    public function testInsuranceAutosavesAndCanBeClearedToNull(): void
    {
        $response = $this->saveFields(['id_seguro' => '2']);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('2', $response->getData(true)['item']['id_seguro']);
        $this->assertEquals(2, ApProcedimiento::findOrFail(1)->id_seguro);

        $response = $this->saveFields(['id_seguro' => '']);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', $response->getData(true)['item']['id_seguro']);
        $this->assertNull(ApProcedimiento::findOrFail(1)->id_seguro);
        $controller = new ApProcedimientosController();
        $this->assertSame('', $controller->index(Request::create('/', 'GET'))
            ->getData()['procedimientos']->first()->nombre_seguro);
        $this->assertSame(0, $controller->index(Request::create('/', 'GET', ['seguro' => 2]))
            ->getData()['procedimientos']->total());

        $this->saveFields(['id_seguro' => '5']);
        $this->assertEquals(5, ApProcedimiento::findOrFail(1)->id_seguro);
        $this->assertSame(1, $controller->index(Request::create('/', 'GET', ['seguro' => 5]))
            ->getData()['procedimientos']->total());
        DB::table('seguros')->insert(['id' => 7, 'nombre' => 'INACTIVO', 'inactivo' => 0]);
        foreach (['999', '7', 'abc', ['5']] as $invalid) {
            $this->assertSame(422, $this->saveFields(['id_seguro' => $invalid])->getStatusCode());
            $this->assertEquals(5, ApProcedimiento::findOrFail(1)->id_seguro);
        }
    }
}
