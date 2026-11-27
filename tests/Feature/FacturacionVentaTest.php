<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class FacturacionVentaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registra_factura_con_descuento_y_tarifa_fiscal_sin_modal(): void
    {
        [$usuario, $datos] = $this->prepararFactura();

        $this->actingAs($usuario)->get(route('ventas.documentos.create'))
            ->assertOk()->assertSee('data-factura', false)->assertDontSee('data-ajax-form', false);
        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => $datos['numero'], 'subtotal' => 1800, 'impuesto' => 270, 'total' => 2070]);

        $fiscal = json_decode(DB::table('com_documentos')->value('datos_factura'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('05', $fiscal['cliente']['tipo_sri']);
        $this->assertSame('01', $fiscal['forma_pago']);
        $this->assertSame(2070, $fiscal['total_pago_centavos']);

        $this->from(route('ventas.documentos.create'))->post(route('ventas.documentos.store'), $datos)
            ->assertRedirect(route('ventas.documentos.create'))->assertSessionHasErrors('numero');
    }

    public function test_descuenta_por_bodega_reusa_el_iva_del_producto_y_reversa_la_salida(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $sucursal = DB::table('erp_sucursales')->where('id_empresa', $usuario->id_empresa)->value('id');
        $this->actingAs($usuario);
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'P1', 'nombre' => 'Producto fiscal', 'unidad' => 'unidad',
        ]);
        $this->putJson(route('inventarios.productos.update', $producto), [
            'codigo' => 'P1', 'nombre' => 'Producto fiscal', 'unidad' => 'unidad', 'activo' => true, 'iva_codigo' => '4',
        ])->assertSuccessful();

        $datos['lineas'] = [];
        foreach (['Principal', 'Auxiliar'] as $nombre) {
            $bodega = DB::table('inv_bodegas')->insertGetId([
                'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'codigo' => $nombre, 'nombre' => $nombre,
            ]);
            DB::table('inv_existencias')->insert([
                'id_empresa' => $usuario->id_empresa, 'id_producto' => $producto, 'id_bodega' => $bodega,
                'cantidad' => 5000, 'costo_promedio' => 10000,
            ]);
            $datos['lineas'][] = ['id_producto' => $producto, 'id_bodega' => $bodega, 'cantidad' => '1', 'precio' => '10', 'iva_codigo' => '0'];
        }

        $datos['aplicar_propina'] = '1';
        $datos['lineas'][1]['descuento'] = '2';
        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => $datos['numero'], 'impuesto' => 270, 'propina' => 180, 'total' => 2250]);
        $documento = DB::table('com_documentos')->value('id');
        $this->get(route('ventas.documentos.show', $documento))->assertOk()
            ->assertSee('Bodega: Principal')->assertSee('Bodega: Auxiliar')->assertSee('Propina');
        foreach ($datos['lineas'] as $linea) {
            $this->assertDatabaseHas('inv_existencias', ['id_producto' => $producto, 'id_bodega' => $linea['id_bodega'], 'cantidad' => 4000]);
        }
        $this->postJson(route('ventas.documentos.anular', $documento), ['motivo' => 'Corrección de cantidad'])->assertSuccessful();
        foreach ($datos['lineas'] as $linea) {
            $this->assertDatabaseHas('inv_existencias', ['id_producto' => $producto, 'id_bodega' => $linea['id_bodega'], 'cantidad' => 5000]);
        }
    }

    public function test_rechaza_iva_no_configurado_y_rebaja_superior_al_importe(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario);
        $datos['lineas'][0]['iva_codigo'] = '3';
        $this->postJson(route('ventas.documentos.store'), $datos)->assertUnprocessable()->assertJsonValidationErrors('lineas.0.iva_codigo');
        $datos['lineas'][0]['iva_codigo'] = '4';
        $datos['lineas'][0]['descuento'] = '22';
        $this->postJson(route('ventas.documentos.store'), $datos)->assertUnprocessable()->assertJsonValidationErrors('lineas.0.descuento');
        $this->assertDatabaseCount('com_documentos', 0);
    }

    public function test_procesa_el_vencimiento_una_vez_y_permite_confirmarlo(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos + [
            'frecuencia' => 'mensual', 'primera_fecha' => '2026-01-31',
        ])->assertRedirect(route('ventas.documentos.index'));

        $this->travelTo(Carbon::parse('2026-01-31 12:00', 'America/Guayaquil'));
        $this->artisan('aitrol:facturas-programar')->assertSuccessful();
        $this->artisan('aitrol:facturas-programar')->assertSuccessful();
        $this->assertDatabaseCount('com_documentos', 1);
        $this->assertDatabaseCount('com_facturas_programadas', 1);
        $this->assertDatabaseHas('com_recurrencias', ['proxima_fecha' => '2026-02-28']);
        $borrador = DB::table('com_facturas_programadas')->value('id');

        $this->get(route('ventas.facturas-recurrentes.index'))->assertOk()->assertSee('Revisar borrador');
        $this->get(route('ventas.documentos.create', ['borrador' => $borrador]))->assertOk()->assertSee('Servicio de soporte');
        $datos['numero'] = '001-001-000000002';
        $datos['fecha'] = '2026-01-31';
        $this->post(route('ventas.documentos.store'), $datos + ['borrador' => $borrador])->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_facturas_programadas', ['id' => $borrador, 'id_documento' => DB::table('com_documentos')->where('numero', $datos['numero'])->value('id')]);
        $this->get(route('ventas.documentos.create', ['borrador' => $borrador]))->assertNotFound();
    }

    /** @return array{User, array} */
    private function prepararFactura(): array
    {
        $this->travelTo(Carbon::parse('2026-01-15 12:00', 'America/Guayaquil'));
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de pruebas', 'direccion' => 'Av. Central']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresa->id, 'codigo' => '001', 'nombre' => 'Matriz']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $cliente = DB::table('erp_clientes')->insertGetId([
            'id_empresa' => $empresa->id, 'tipo_identificacion' => 'cedula', 'identificacion' => '0912345678', 'nombre' => 'Cliente de pruebas',
        ]);
        DB::table('erp_parametros_fiscales')->insert([
            'id_empresa' => $empresa->id, 'concepto' => 'iva', 'codigo' => '4', 'nombre' => 'IVA 15%',
            'unidad' => 'porcentaje', 'valor' => 15, 'vigente_desde' => '2026-01-01',
        ]);

        return [$usuario, [
            'numero' => '001-001-000000001', 'fecha' => '2026-01-15', 'id_tercero' => $cliente, 'forma_pago' => '01',
            'lineas' => [['descripcion' => 'Servicio de soporte', 'cantidad' => '2', 'precio' => '10', 'descuento' => '2', 'iva_codigo' => '4']],
        ]];
    }
}
