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

    private function crearEmpresa(string $ruc): int
    {
        $usuario = User::factory()->create(['id_empresa' => null]);
        $datos = ['ruc' => $ruc, 'razon_social' => 'Nueva empresa'];

        $this->actingAs($usuario)->post(route('empresa.inicial'), $datos)->assertRedirect(route('dashboard'));
        $this->post(route('empresa.inicial'), $datos)->assertRedirect(route('dashboard'));

        return (int) $usuario->refresh()->id_empresa;
    }
}
