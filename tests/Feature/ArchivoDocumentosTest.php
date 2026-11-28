<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use App\Services\EmpresaActiva;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class ArchivoDocumentosTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_clientes_y_proveedores_muestran_el_tipo_de_documento_y_sus_lineas(): void
    {
        [$usuario, $sucursal] = $this->prepararContexto();
        $this->actingAs($usuario);
        $tipos = ['facturas' => 'documentos', 'notas-de-credito' => 'notas-credito', 'notas-de-debito' => 'notas-debito', 'retenciones' => 'retenciones'];
        foreach (['clientes' => 'ventas', 'proveedores' => 'compras'] as $directorio => $modulo) {
            $origen = null;
            foreach ($tipos as $ruta => $tipo) {
                $numero = $modulo.'-'.$tipo;
                $id = $this->documento($usuario->id_empresa, $sucursal, $modulo, $tipo, $numero, $origen);
                $origen ??= $id;

                $this->get(route($directorio.'.documentos.'.$ruta))->assertOk()
                    ->assertSee('archivo-documentos__cuadricula', false)->assertSee('data-archivo-abrir=', false)
                    ->assertSee('data-archivo-dialogo', false)->assertSee($numero)
                    ->assertSee('Concepto <impreso> '.$numero)->assertDontSee('Concepto <impreso> '.$numero, false)
                    ->assertSee('USD 20.00');
            }
            $this->get(route($directorio.'.documentos.retenciones'))->assertOk()
                ->assertSee('Documento de origen')->assertSee($modulo.'-documentos')->assertSee('Total retenido');
        }
        $this->get(route('clientes.documentos.facturas'))->assertOk()
            ->assertDontSee('compras-documentos')->assertDontSee('ventas-notas-credito');
    }

    public function test_el_archivo_respeta_empresa_sucursal_periodo_y_busqueda(): void
    {
        [$usuario, $sucursal] = $this->prepararContexto();
        $otraSucursal = DB::table('erp_sucursales')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'NORTE', 'nombre' => 'Norte',
        ]);
        $otraEmpresa = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Empresa externa']);
        $sucursalAjena = DB::table('erp_sucursales')->insertGetId([
            'id_empresa' => $otraEmpresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz',
        ]);
        $this->documento($usuario->id_empresa, $sucursal, 'ventas', 'documentos', 'ACTIVA-01');
        $this->documento($usuario->id_empresa, $sucursal, 'ventas', 'documentos', 'OTRO-NUMERO');
        $this->documento($usuario->id_empresa, $otraSucursal, 'ventas', 'documentos', 'ACTIVA-NORTE');
        $this->documento($otraEmpresa->id, $sucursalAjena, 'ventas', 'documentos', 'ACTIVA-AJENA');
        $this->documento($usuario->id_empresa, $sucursal, 'ventas', 'documentos', 'ACTIVA-HISTORICA', fecha: '2025-11-27');

        $this->actingAs($usuario)->post(route('configuracion.contexto.update'), ['id_sucursal' => $sucursal, 'periodo' => 2026])
            ->assertRedirect(route('dashboard'));
        $this->get(route('clientes.documentos.facturas', ['q' => 'ACTIVA', 'desde' => '2026-11-01']))
            ->assertOk()->assertSee('ACTIVA-01')->assertDontSee('OTRO-NUMERO')
            ->assertDontSee('ACTIVA-NORTE')->assertDontSee('ACTIVA-AJENA')->assertDontSee('ACTIVA-HISTORICA');
    }

    public function test_el_archivo_usa_el_permiso_de_lectura_del_directorio(): void
    {
        [$admin, $sucursal] = $this->prepararContexto();
        $sinPermiso = User::factory()->create(['id_empresa' => $admin->id_empresa, 'is_admin' => false]);
        $usuario = User::factory()->create(['id_empresa' => $admin->id_empresa, 'is_admin' => false]);
        $this->documento($admin->id_empresa, $sucursal, 'ventas', 'documentos', 'PERMITIDA-01');
        $ruta = route('clientes.documentos.facturas');

        $this->get($ruta)->assertRedirect(route('dashboard'));
        $this->actingAs($sinPermiso)->get($ruta)->assertForbidden();

        app(EmpresaActiva::class)->prepararPermisos($admin->id_empresa);
        $permiso = DB::table('permisos')->where('id_empresa', $admin->id_empresa)
            ->where('opcion', 'clientes.documentos.facturas')->where('accion', 'leer')->value('id');
        DB::table('permiso_usuario')->insert([
            'id_empresa' => $admin->id_empresa, 'id_usuario' => $usuario->id, 'id_permiso' => $permiso, 'permitido' => true,
        ]);
        $this->actingAs($usuario)->get($ruta)->assertOk()->assertSee('PERMITIDA-01');
        $this->get(route('proveedores.documentos.facturas'))->assertForbidden();
    }

    /** @return array{User, int} */
    private function prepararContexto(): array
    {
        $this->travelTo(Carbon::parse('2026-11-27 12:00', 'America/Guayaquil'));
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de prueba']);
        $sucursal = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $empresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);

        return [$usuario, $sucursal];
    }

    private function documento(int $empresa, int $sucursal, string $modulo, string $tipo, string $numero, ?int $origen = null, string $fecha = '2026-11-27'): int
    {
        $id = DB::table('com_documentos')->insertGetId([
            'id_empresa' => $empresa, 'id_sucursal' => $sucursal, 'modulo' => $modulo, 'tipo' => $tipo,
            'numero' => $numero, 'fecha' => $fecha, 'tercero' => 'Contraparte', 'id_documento_origen' => $origen,
            'subtotal' => 2000, 'impuesto' => 0, 'total' => 2000,
        ]);
        DB::table('com_lineas')->insert([
            'id_empresa' => $empresa, 'id_documento' => $id, 'descripcion' => 'Concepto <impreso> '.$numero,
            'cantidad' => 1000, 'precio' => 200000, 'tasa' => 0, 'subtotal' => 2000, 'impuesto' => 0, 'total' => 2000,
            'tipo_retencion' => 'renta', 'codigo_retencion' => '303', 'base_retencion' => 20000, 'porcentaje_retencion' => 1000,
        ]);

        return $id;
    }
}
