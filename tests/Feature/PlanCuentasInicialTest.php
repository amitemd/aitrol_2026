<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class PlanCuentasInicialTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cada_empresa_nueva_recibe_su_propio_catalogo_y_jerarquia(): void
    {
        $empresaExistente = EmpresaModel::create(['ruc' => '1000000000011', 'razon_social' => 'Empresa existente']);
        DB::table('co_cuentas')->insert([
            'id_empresa' => $empresaExistente->id, 'codigo' => '1', 'nombre' => 'Cuenta propia', 'clase' => 'activo',
        ]);

        $primera = $this->crearEmpresa('1000000000012');
        $segunda = $this->crearEmpresa('1000000000013');

        $plantilla = DB::table('co_plan_cuentas_plantilla')->get()->keyBy('codigo');
        $this->assertCount(130, $plantilla);
        foreach ([$primera, $segunda] as $empresa) {
            $cuentas = DB::table('co_cuentas')->where('id_empresa', $empresa)->get()->keyBy('codigo');
            $this->assertCount($plantilla->count(), $cuentas);
            foreach ($plantilla as $codigo => $base) {
                $cuenta = $cuentas[$codigo];
                $this->assertSame($base->nombre, $cuenta->nombre);
                $this->assertSame($base->clase, $cuenta->clase);
                $padre = $base->codigo_padre === null ? null : $cuentas[$base->codigo_padre]->id;
                $this->assertSame($padre, $cuenta->id_padre);
                $this->assertSame((bool) $base->auxiliar, (bool) $cuenta->auxiliar);
                $this->assertSame((bool) $base->efectivo, (bool) $cuenta->efectivo);
            }
        }

        $this->assertSame(1, DB::table('co_cuentas')->where('id_empresa', $empresaExistente->id)->count());
        $this->assertDatabaseHas('co_cuentas', ['id_empresa' => $empresaExistente->id, 'codigo' => '1', 'nombre' => 'Cuenta propia']);
    }

    public function test_la_migracion_carga_solo_la_empresa_uno_y_respeta_sus_cuentas(): void
    {
        DB::table('erp_empresas')->insert([
            ['id' => 1, 'ruc' => '1000000000011', 'razon_social' => 'Empresa principal'],
            ['id' => 2, 'ruc' => '1000000000012', 'razon_social' => 'Otra empresa'],
        ]);
        $activo = DB::table('co_cuentas')->insertGetId([
            'id_empresa' => 1, 'codigo' => '1', 'nombre' => 'Activo personalizado', 'clase' => 'activo', 'auxiliar' => false,
        ]);

        $migracion = require base_path('packages/modules/contabilidad/database/migrations/2026_11_28_001809_cargar_plan_de_cuentas_empresa_uno.php');
        $migracion->up();
        $migracion->up();

        $this->assertDatabaseCount('co_cuentas', 130);
        $this->assertDatabaseHas('co_cuentas', ['id' => $activo, 'id_empresa' => 1, 'nombre' => 'Activo personalizado']);
        $this->assertDatabaseHas('co_cuentas', ['id_empresa' => 1, 'codigo' => '1.01', 'id_padre' => $activo]);
        $this->assertDatabaseMissing('co_cuentas', ['id_empresa' => 2]);
    }

    public function test_el_listado_muestra_la_jerarquia_y_solo_permite_gestionar_auxiliares(): void
    {
        $empresa = $this->crearEmpresa('1000000000011');
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa, 'codigo' => '001', 'nombre' => 'Matriz']);

        $cuentas = DB::table('co_cuentas')->where('id_empresa', $empresa)->pluck('id', 'codigo');
        $listado = route('contabilidad.cuentas.index');
        $alta = route('contabilidad.cuentas.store');
        $editarGrupo = route('contabilidad.cuentas.editar.formulario', $cuentas['1.01.01']);
        $editarAuxiliar = route('contabilidad.cuentas.editar.formulario', $cuentas['1.01.01.01']);
        $ramaActivo = 'plan-subcuentas-'.$cuentas['1'];
        $respuesta = $this->get($listado)->assertOk()
            ->assertSee('class="plan-cuentas-raices"', false)
            ->assertSee('data-plan-busqueda', false)
            ->assertSee('aria-controls="'.$ramaActivo.'"', false)
            ->assertSee('aria-expanded="false"', false)->assertDontSee($editarGrupo, false)->assertSee($editarAuxiliar, false);
        $this->assertMatchesRegularExpression('/id="'.$ramaActivo.'"\s+role="list"\s+hidden/', $respuesta->getContent());
        $resultado = $this->get(route('contabilidad.cuentas.index', ['q' => 'Caja general']))->assertOk()
            ->assertSeeInOrder(['Activo corriente', 'Efectivo y equivalentes de efectivo', 'Caja general'])
            ->assertSee('aria-expanded="true"', false)->assertSee('1 coincidencia')
            ->assertDontSee('id="'.$ramaActivo.'" role="list" hidden', false);
        $this->assertMatchesRegularExpression('/data-plan-cuenta="'.$cuentas['1.01.01.03'].'"\s+hidden/', $resultado->getContent());
        $this->get(route('contabilidad.cuentas.show', $cuentas['1.01.01.01']))->assertOk()
            ->assertSeeInOrder(['Creado por', 'Creado el', 'Modificado por', 'Modificado el']);
        $this->get($editarGrupo)->assertForbidden();
        $this->get($editarAuxiliar)->assertOk();

        $datos = ['codigo' => '1.01.01.30', 'nombre' => 'Caja auxiliar', 'activo' => true, 'id_padre' => $cuentas['1.01.01']];
        $this->putJson(route('contabilidad.cuentas.editar', $cuentas['1.01.01']), $datos)->assertForbidden();
        $this->postJson($alta, $datos + ['auxiliar' => false])->assertUnprocessable()->assertJsonValidationErrors('auxiliar');
        $this->postJson($alta, array_replace($datos, ['id_padre' => $cuentas['1.01.01.01']]))
            ->assertUnprocessable()->assertJsonValidationErrors('id_padre');
        $this->postJson($alta, $datos)->assertCreated();
        $this->assertDatabaseHas('co_cuentas', ['id_empresa' => $empresa, 'codigo' => $datos['codigo'], 'auxiliar' => true,
            'clase' => 'activo', 'id_padre' => $cuentas['1.01.01']]);
        $id = DB::table('co_cuentas')->where('id_empresa', $empresa)->where('codigo', $datos['codigo'])->value('id');
        $this->putJson(route('contabilidad.cuentas.editar', $id), array_replace($datos, ['nombre' => 'Caja auxiliar actualizada']))->assertOk();
        $this->assertDatabaseHas('co_cuentas', ['id' => $id, 'nombre' => 'Caja auxiliar actualizada']);
    }

    public function test_el_formulario_propone_y_asigna_el_primer_codigo_libre_del_grupo(): void
    {
        $empresa = $this->crearEmpresa('1000000000011');
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa, 'codigo' => '001', 'nombre' => 'Matriz']);
        $cuentas = DB::table('co_cuentas')->where('id_empresa', $empresa)->pluck('id', 'codigo');
        $padre = ['id_padre' => $cuentas['1.01.01']];
        $alta = route('contabilidad.cuentas.store');
        $this->postJson($alta, $padre + ['codigo' => '1.01.01.06', 'nombre' => 'Caja inactiva', 'activo' => false])->assertCreated();

        $this->get(route('contabilidad.cuentas.create', $padre))->assertOk()->assertSee('value="1.01.01.05"', false);
        $this->getJson(route('contabilidad.cuentas.codigo-auxiliar', $padre))->assertOk()->assertJsonPath('codigo', '1.01.01.05');
        $this->getJson(route('contabilidad.cuentas.codigo-auxiliar', ['id_padre' => $cuentas['2']]))
            ->assertOk()->assertJsonPath('codigo', '2.03');

        $this->postJson($alta, $padre + ['nombre' => 'Caja de ventas'])->assertCreated()->assertJsonPath('message', 'Cuenta auxiliar 1.01.01.05 guardada.');
        $this->postJson($alta, $padre + ['nombre' => 'Caja de cobranzas'])->assertCreated()->assertJsonPath('message', 'Cuenta auxiliar 1.01.01.07 guardada.');
        $this->assertDatabaseHas('co_cuentas', $padre + ['id_empresa' => $empresa, 'codigo' => '1.01.01.07', 'nombre' => 'Caja de cobranzas']);
    }

    public function test_codigos_y_nombres_son_unicos_dentro_de_cada_empresa(): void
    {
        $empresa = $this->crearEmpresa('1000000000011');
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa, 'codigo' => '001', 'nombre' => 'Matriz']);
        $cuentas = DB::table('co_cuentas')->where('id_empresa', $empresa)->pluck('id', 'codigo');
        $alta = route('contabilidad.cuentas.store');
        $datos = ['id_padre' => $cuentas['1.01.01'], 'codigo' => '1.01.01.05', 'nombre' => 'Caja de recepción', 'activo' => true];
        $this->postJson($alta, $datos)->assertCreated();

        $this->postJson($alta, array_replace($datos, ['nombre' => 'Caja distinta']))->assertConflict();
        $this->postJson($alta, ['id_padre' => $cuentas['2'], 'nombre' => 'CAJA  DE RECEPCION'])
            ->assertUnprocessable()->assertJsonValidationErrors('nombre');
        $this->putJson(route('contabilidad.cuentas.editar', $cuentas['1.01.01.01']), array_replace($datos, ['codigo' => '1.01.01.01']))
            ->assertUnprocessable()->assertJsonValidationErrors('nombre');
        $id = DB::table('co_cuentas')->where('id_empresa', $empresa)->where('codigo', $datos['codigo'])->value('id');
        $this->putJson(route('contabilidad.cuentas.editar', $id), $datos)->assertOk();

        $otraEmpresa = $this->crearEmpresa('1000000000012');
        DB::table('erp_sucursales')->insert(['id_empresa' => $otraEmpresa, 'codigo' => '001', 'nombre' => 'Matriz']);
        $otroPadre = DB::table('co_cuentas')->where('id_empresa', $otraEmpresa)->where('codigo', '1.01.01')->value('id');
        $this->getJson(route('contabilidad.cuentas.codigo-auxiliar', ['id_padre' => $cuentas['1.01.01']]))->assertNotFound();
        $this->postJson($alta, array_replace($datos, ['id_padre' => $otroPadre]))->assertCreated();
    }

    private function crearEmpresa(string $ruc): int
    {
        $usuario = User::factory()->create(['id_empresa' => null]);
        $datos = ['ruc' => $ruc, 'razon_social' => 'Nueva empresa'];

        $this->actingAs($usuario)->post(route('empresa.inicial'), $datos)->assertRedirect(route('dashboard'));
        $this->post(route('empresa.inicial'), $datos)->assertRedirect(route('dashboard'));

        return (int) $usuario->refresh()->id_empresa;
    }
}
