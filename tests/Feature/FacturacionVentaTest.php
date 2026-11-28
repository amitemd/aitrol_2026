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
            ->assertOk()->assertSee('data-factura', false)->assertDontSee('data-ajax-form', false)->assertDontSee('name="numero"', false);
        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => '001-001-000000001', 'subtotal' => 1800, 'impuesto' => 270, 'total' => 2070]);

        $documento = DB::table('com_documentos')->first();
        $fiscal = json_decode($documento->datos_factura, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('05', $fiscal['cliente']['tipo_sri']);
        $this->assertSame('01', $fiscal['forma_pago']);
        $this->assertSame(2070, $fiscal['total_pago_centavos']);

        $clave = $fiscal['clave_acceso'];
        $this->assertMatchesRegularExpression('/\A150120260110000000000011001001000000001[0-9]{8}1[0-9]\z/', $clave);
        $ponderaciones = [2, 3, 4, 5, 6, 7];
        $suma = array_sum(array_map(
            fn (string $digito, int $posicion): int => (int) $digito * $ponderaciones[$posicion % 6],
            str_split(strrev(substr($clave, 0, 48))), range(0, 47),
        ));
        $this->assertSame((string) ((11 - $suma % 11) % 11 === 10 ? 1 : (11 - $suma % 11) % 11), $clave[48]);
        $this->get(route('ventas.documentos.show', $documento->id))->assertOk()->assertSee($clave)->assertSee('Pruebas');

        $this->post(route('ventas.documentos.store'), $datos + ['numero' => '999-999-999999999'])
            ->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => '001-001-000000002']);
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
        $this->assertDatabaseHas('com_documentos', ['numero' => '001-001-000000001', 'impuesto' => 270, 'propina' => 180, 'total' => 2250]);
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
        $datos['fecha'] = '2026-01-31';
        $this->post(route('ventas.documentos.store'), $datos + ['borrador' => $borrador])->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_facturas_programadas', ['id' => $borrador, 'id_documento' => DB::table('com_documentos')->where('numero', '001-001-000000002')->value('id')]);
        $this->get(route('ventas.documentos.create', ['borrador' => $borrador]))->assertNotFound();
    }

    public function test_consulta_y_actualiza_contacto_del_cliente_desde_la_factura(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $cliente = $datos['id_tercero'];
        $persona = DB::table('personas')->insertGetId([
            'nombre' => 'Cliente de pruebas', 'direccion' => 'Av. Principal', 'telefono' => '042345678',
            'email' => 'cliente@example.com',
        ]);
        DB::table('erp_clientes')->where('id', $cliente)->update(['id_persona' => $persona, 'email' => 'anterior@example.com']);
        $contactoUrl = route('ventas.documentos.contacto', $cliente);
        $this->actingAs($usuario)->getJson($contactoUrl)->assertOk()->assertExactJson([
            'direccion' => 'Av. Principal', 'telefono' => '042345678', 'email' => 'cliente@example.com',
        ]);
        $this->patchJson($contactoUrl, ['direccion' => 'Av. Nueva', 'telefono' => '0991234567', 'email' => 'incorrecto'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $contacto = ['direccion' => 'Av. Nueva', 'telefono' => '0991234567', 'email' => 'nuevo@example.com'];
        $this->patchJson($contactoUrl, $contacto)->assertOk();
        $this->assertDatabaseHas('personas', ['id' => $persona] + $contacto);
        $this->assertDatabaseHas('erp_clientes', ['id' => $cliente, 'id_usuario_modificacion' => $usuario->id] + $contacto);

        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $fiscal = json_decode(DB::table('com_documentos')->value('datos_factura'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($contacto, array_intersect_key($fiscal['cliente'], $contacto));

        $empresaAjena = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Otra empresa']);
        $ajeno = DB::table('erp_clientes')->insertGetId([
            'id_empresa' => $empresaAjena->id, 'tipo_identificacion' => 'cedula', 'identificacion' => '0912345678', 'nombre' => 'Cliente ajeno',
        ]);
        $urlAjena = route('ventas.documentos.contacto', $ajeno);
        $this->getJson($urlAjena)->assertNotFound();
        $this->patchJson($urlAjena, $contacto)->assertNotFound();

        $vendedor = User::factory()->create(['id_empresa' => $usuario->id_empresa, 'is_admin' => false]);
        app(\App\Services\EmpresaActiva::class)->prepararPermisos($usuario->id_empresa);
        $permiso = DB::table('permisos')->where('id_empresa', $usuario->id_empresa)
            ->where('opcion', 'ventas.documentos.index')->where('accion', 'escribir')->value('id');
        DB::table('permiso_usuario')->insert([
            'id_empresa' => $usuario->id_empresa, 'id_usuario' => $vendedor->id, 'id_permiso' => $permiso, 'permitido' => true,
        ]);
        $this->actingAs($vendedor)->getJson($contactoUrl)->assertOk()->assertExactJson($contacto);
        $this->patchJson($contactoUrl, $contacto)->assertForbidden();
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
            'id_empresa' => $empresa->id, 'concepto' => 'iva', 'codigo' => '04', 'nombre' => 'IVA 15%',
            'unidad' => 'porcentaje', 'valor' => 15, 'vigente_desde' => '2026-01-01',
        ]);

        return [$usuario, [
            'fecha' => '2026-01-15', 'id_tercero' => $cliente, 'forma_pago' => '01',
            'lineas' => [['descripcion' => 'Servicio de soporte', 'cantidad' => '2', 'precio' => '10', 'descuento' => '2', 'iva_codigo' => '4']],
        ]];
    }
}
