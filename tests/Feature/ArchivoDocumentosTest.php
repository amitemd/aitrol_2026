<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use App\Services\EmpresaActiva;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function test_la_factura_muestra_los_datos_de_emision_y_la_informacion_adicional_debajo_de_las_lineas(): void
    {
        [$usuario, $sucursal] = $this->prepararContexto();
        $id = $this->documento($usuario->id_empresa, $sucursal, 'ventas', 'documentos', 'FACTURA-01');
        $detalle = "Desmontaje\nRevisión <técnica>";
        DB::table('com_documentos')->where('id', $id)->update([
            'datos_factura' => json_encode([
                'emisor' => ['razon_social' => 'Emisor al facturar', 'direccion' => 'Matriz anterior'],
                'cliente' => ['nombre' => 'Cliente al facturar', 'email' => 'compras@example.test'],
                'clave_acceso' => str_repeat('1', 49), 'ambiente' => '1', 'forma_pago' => '01', 'plazo_dias' => 0,
                'informacion_adicional' => [['nombre' => 'Trabajo realizado', 'valor' => $detalle]],
            ], JSON_THROW_ON_ERROR),
        ]);

        $this->actingAs($usuario)->get(route('clientes.documentos.facturas'))->assertOk()
            ->assertSeeInOrder(['Emisor al facturar', 'Matriz anterior', 'Cliente al facturar', 'compras@example.test', 'Días de crédito'])
            ->assertSee('Ver log')
            ->assertSeeInOrder(['Concepto <impreso> FACTURA-01', 'Información adicional', 'Trabajo realizado', $detalle, 'USD 20.00'])
            ->assertDontSee($detalle, false);
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

    public function test_la_compra_permite_subir_un_respaldo_y_verlo_desde_el_menu(): void
    {
        Storage::fake('local');
        [$usuario, $sucursal] = $this->prepararContexto();
        $id = $this->documento($usuario->id_empresa, $sucursal, 'compras', 'documentos', 'COMPRA-01');
        $this->actingAs($usuario);
        $subir = route('compras.documentos.respaldo.store', $id);
        $lista = route('compras.documentos.index');

        $this->get($lista)->assertOk()->assertSeeInOrder([
            'Subir archivo', 'Generar retención', 'Ver trazabilidad', 'dropdown-divider', 'Anular',
        ], false);
        $this->get(route('compras.documentos.respaldo.create', $id))->assertOk()
            ->assertSee('Selecciona un PDF o una imagen')->assertSee('accept=".pdf,.jpg,.jpeg,.png,.webp"', false);
        $this->postJson($subir, ['archivo' => UploadedFile::fake()->createWithContent('invalido.html', '<html>Documento</html>')])
            ->assertUnprocessable()->assertJsonValidationErrors('archivo');

        $crearPdf = fn (): UploadedFile => UploadedFile::fake()->createWithContent('respaldo.pdf', "%PDF-1.4\n%%EOF");
        $this->postJson($subir, ['archivo' => $crearPdf()])->assertOk()->assertJsonPath('message', 'Archivo de respaldo guardado.');
        $this->get($lista)->assertOk()->assertSee('Ver archivo')->assertDontSee('Subir archivo');
        $this->postJson($subir, ['archivo' => $crearPdf()])->assertConflict();
        $this->assertCount(1, Storage::disk('local')->allFiles());
        $this->get(route('compras.documentos.show', ['registro' => $id, 'respaldo' => 1]))->assertOk()
            ->assertSeeInOrder(['Factura de compra', 'Concepto <impreso> COMPRA-01', 'Archivo de respaldo'])
            ->assertSee(route('compras.documentos.respaldo.archivo', $id));
        $this->get(route('compras.documentos.respaldo.archivo', $id))->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $empresaAjena = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Empresa externa']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresaAjena->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);
        $externo = User::factory()->create(['id_empresa' => $empresaAjena->id, 'is_admin' => true]);
        $this->actingAs($externo)->get(route('compras.documentos.respaldo.archivo', $id))->assertNotFound();
    }

    public function test_compras_y_ventas_comparten_la_ficha_y_el_xml_no_sustituye_el_respaldo(): void
    {
        Storage::fake('local');
        [$usuario, $sucursal] = $this->prepararContexto();
        $compra = $this->documento($usuario->id_empresa, $sucursal, 'compras', 'documentos', 'COMPRA-XML');
        $venta = $this->documento($usuario->id_empresa, $sucursal, 'ventas', 'documentos', 'VENTA-01');
        $this->documento($usuario->id_empresa, $sucursal, 'compras', 'retenciones', 'RETENCION-01', $compra);
        DB::table('com_documentos')->where('id', $compra)->update([
            'datos_factura' => json_encode([
                'fuente_compra' => 'archivo',
                'xml_recibido' => '<factura><infoTributaria><razonSocial>Emisor XML</razonSocial></infoTributaria><infoAdicional><campoAdicional nombre="Detalle">Nota del XML</campoAdicional></infoAdicional></factura>',
            ], JSON_THROW_ON_ERROR),
        ]);
        $this->actingAs($usuario);

        $this->get(route('dashboard'))->assertOk()->assertSee('archivo-documentos.css?v=', false);
        $this->get(route('compras.documentos.index'))->assertOk()->assertSee('Subir archivo')->assertDontSee('Ver archivo');
        $this->get(route('compras.documentos.show', ['registro' => $compra, 'respaldo' => 1]))->assertNotFound();
        $this->get(route('compras.documentos.show', $compra))->assertOk()
            ->assertSee('<article class="archivo-documentos__hoja visor-factura" data-bs-theme="light"', false)
            ->assertSee('Descargar XML')
            ->assertSeeInOrder(['Emisor XML', 'Concepto <impreso> COMPRA-XML', 'Información adicional', 'Nota del XML']);
        $this->postJson(route('compras.documentos.respaldo.store', $compra), [
            'archivo' => UploadedFile::fake()->createWithContent('factura.pdf', "%PDF-1.4\n%%EOF"),
        ])->assertOk();
        $this->get(route('compras.documentos.show', ['registro' => $compra, 'respaldo' => 1]))->assertOk()
            ->assertSee('Descargar XML')->assertSee('PDF de respaldo de la compra');
        $this->get(route('compras.documentos.index'))->assertOk()->assertSee('Ver archivo')->assertDontSee('Subir archivo');
        $this->get(route('compras.retenciones.create', ['origen' => $compra]))->assertOk()
            ->assertSee('name="id_documento_origen" value="'.$compra.'"', false);
        $this->get(route('ventas.documentos.show', $venta))->assertOk()
            ->assertSee('<article class="archivo-documentos__hoja visor-factura" data-bs-theme="light"', false)->assertSee('Facturar a');
        $this->get(route('compras.documentos.trazabilidad', $compra))->assertOk()
            ->assertSeeInOrder(['Diagrama de trazabilidad', 'COMPRA-XML', 'Retención', 'RETENCION-01']);
        $this->get(route('ventas.documentos.trazabilidad', $venta))->assertOk()->assertDontSee('RETENCION-01')
            ->assertSee('Este documento todavía no tiene transacciones ni documentos vinculados.');
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
