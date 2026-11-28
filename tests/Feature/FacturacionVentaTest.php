<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Aitrol\CajaBancos\Application\ConciliacionBancaria;
use Aitrol\Proveedores\Infrastructure\Persistence\EloquentProveedorRepository;
use App\Models\User;
use App\Services\AutorizacionSri;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class FacturacionVentaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_factura_con_existencia_insuficiente_conserva_solo_los_datos_adicionales_escritos(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'P1', 'nombre' => 'Producto fiscal', 'unidad' => 'unidad', 'iva_codigo' => '4',
        ]);
        $bodega = DB::table('inv_bodegas')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => DB::table('erp_sucursales')->where('id_empresa', $usuario->id_empresa)->value('id'),
            'codigo' => 'B01', 'nombre' => 'Principal',
        ]);
        $datos['lineas'][0] += ['id_producto' => $producto, 'id_bodega' => $bodega];
        $datos['informacion_adicional'] = array_fill(0, 5, ['nombre' => '', 'valor' => '']);
        $datos['informacion_adicional'][0] = ['nombre' => 'Pedido', 'valor' => 'REF-123'];
        $formulario = route('ventas.documentos.create');

        $this->actingAs($usuario)->from($formulario)->post(route('ventas.documentos.store'), $datos)
            ->assertRedirect($formulario)->assertSessionHasErrors(['cantidad' => 'La existencia disponible es insuficiente.']);
        $this->withSession(['_old_input' => $datos])->get($formulario)->assertOk()
            ->assertSee('data-factura-adicional-agregar', false)->assertSee('value="REF-123"', false)
            ->assertDontSee('name="informacion_adicional[1][nombre]"', false);
        $this->assertDatabaseCount('com_documentos', 0);
    }

    public function test_los_ajustes_y_la_venta_contabilizan_con_las_cuentas_del_producto(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $empresa = $usuario->id_empresa;
        $integracion = DB::table('co_integraciones')->where('id_empresa', $empresa);
        $reglas = json_decode($integracion->value('reglas'), true, flags: JSON_THROW_ON_ERROR);
        $cuentas = array_intersect_key($reglas, array_flip(['inventario', 'costo_ventas', 'ajustes_inventario']));
        $integracion->update(['reglas' => json_encode(array_diff_key($reglas, $cuentas), JSON_THROW_ON_ERROR)]);
        $plantilla = DB::table('inv_plantillas_contables')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'PROD-001', 'nombre' => 'Partes de PC',
            'cuentas' => json_encode($cuentas, JSON_THROW_ON_ERROR),
        ]);
        $categoria = DB::table('inv_categorias')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'PC', 'nombre' => 'Partes de PC', 'id_plantilla_contable' => $plantilla,
        ]);
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'SSD', 'nombre' => 'Disco SSD', 'unidad' => 'unidad',
            'id_categoria' => $categoria, 'iva_codigo' => '4',
        ]);
        $bodega = DB::table('inv_bodegas')->insertGetId([
            'id_empresa' => $empresa, 'id_sucursal' => DB::table('erp_sucursales')->where('id_empresa', $empresa)->value('id'),
            'codigo' => 'B01', 'nombre' => 'Principal',
        ]);
        $ajustes = route('inventarios.ajustes.store');
        $entrada = ['tipo' => 'ajuste_entrada', 'id_producto' => $producto, 'id_bodega' => $bodega, 'cantidad' => 4,
            'costo_unitario' => 10, 'motivo' => 'Ajuste de entrada', 'clave_operacion' => '408b9e48-8cb3-4a34-ba45-d8275e6f7f25'];

        $this->actingAs($usuario)->postJson($ajustes, $entrada)->assertCreated();
        $this->postJson($ajustes, $entrada)->assertOk();
        $this->postJson($ajustes, array_replace($entrada, ['tipo' => 'ajuste_salida', 'cantidad' => 1, 'motivo' => 'Ajuste de salida', 'clave_operacion' => null]))
            ->assertCreated();
        $this->assertDatabaseHas('inv_existencias', ['id_producto' => $producto, 'id_bodega' => $bodega, 'cantidad' => 3000, 'costo_promedio' => 100000]);
        $this->assertDatabaseCount('inv_kardex', 2);

        $asientos = DB::table('co_asientos')->where('origen', 'inventario.movimiento')->orderBy('id')->pluck('id');
        $this->assertCount(2, $asientos);
        $this->get(route('inventarios.ajustes.index'))->assertOk()
            ->assertSee('Ver asiento')->assertSee(route('contabilidad.asientos.show', $asientos[0]));
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientos[0], 'id_cuenta' => $cuentas['inventario'], 'debe' => 4000, 'haber' => 0]);
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientos[0], 'id_cuenta' => $cuentas['ajustes_inventario'], 'debe' => 0, 'haber' => 4000]);
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientos[1], 'id_cuenta' => $cuentas['inventario'], 'debe' => 0, 'haber' => 1000]);
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientos[1], 'id_cuenta' => $cuentas['ajustes_inventario'], 'debe' => 1000, 'haber' => 0]);

        $datos['lineas'][0] += ['id_producto' => $producto, 'id_bodega' => $bodega];
        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('inv_existencias', ['id_producto' => $producto, 'id_bodega' => $bodega, 'cantidad' => 1000]);
        $asientoVenta = DB::table('co_asientos')->where('origen', 'comercial.documento')->value('id');
        $factura = DB::table('com_documentos')->first();
        $this->get(route('inventarios.movimientos.index'))->assertOk()
            ->assertSee('Ajuste de entrada')->assertSee('Registro de venta')->assertSee($factura->numero)
            ->assertSee('Bodega de origen')->assertSee('Bodega de destino');
        $idAjuste = DB::table('inv_operaciones')->where('tipo', 'ajuste_entrada')->value('id');
        $this->get(route('inventarios.movimientos.show', $idAjuste))->assertOk()->assertSee('Bodega de destino');
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientoVenta, 'id_cuenta' => $cuentas['costo_ventas'], 'debe' => 2000, 'haber' => 0]);
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientoVenta, 'id_cuenta' => $cuentas['inventario'], 'debe' => 0, 'haber' => 2000]);

        DB::table('co_periodos')->where('id_empresa', $empresa)->update(['cerrado_at' => now()]);
        $this->postJson($ajustes, array_replace($entrada, ['clave_operacion' => null]))
            ->assertUnprocessable()->assertJsonValidationErrors('contabilidad');
        $this->assertDatabaseHas('inv_existencias', ['id_producto' => $producto, 'id_bodega' => $bodega, 'cantidad' => 1000]);
        $this->assertDatabaseCount('inv_kardex', 3);
        $this->assertDatabaseCount('co_asientos', 3);
    }

    public function test_la_bodega_principal_es_unica_por_empresa_y_recibe_compras_de_otra_sucursal(): void
    {
        [$usuario] = $this->prepararFactura();
        $empresa = $usuario->id_empresa;
        DB::table('co_integraciones')->where('id_empresa', $empresa)->update(['activa' => false]);
        $matriz = DB::table('erp_sucursales')->where('id_empresa', $empresa)->value('id');
        $norte = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $empresa, 'codigo' => '002', 'nombre' => 'Norte']);
        $persona = DB::table('personas')->insertGetId(['nombre' => 'Proveedor de prueba']);
        $proveedor = DB::table('erp_proveedores')->insertGetId([
            'id_empresa' => $empresa, 'id_persona' => $persona, 'tipo_identificacion' => 'ruc',
            'identificacion' => '0991234567001', 'nombre' => 'Proveedor de prueba',
        ]);
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $empresa, 'codigo' => 'P1', 'nombre' => 'Mercadería', 'unidad' => 'unidad',
        ]);
        $seleccionar = route('configuracion.contexto.update');
        $bodegas = route('inventarios.bodegas.store');
        $this->actingAs($usuario)->post($seleccionar, ['id_sucursal' => $matriz, 'periodo' => 2026])->assertRedirect();
        $this->postJson($bodegas, ['codigo' => 'M01', 'nombre' => 'Almacén Matriz'])->assertCreated();
        $origen = DB::table('inv_bodegas')->where('id_sucursal', $matriz)->value('id');
        $this->assertDatabaseHas('inv_bodegas', ['id' => $origen, 'es_principal' => true]);
        $this->patchJson(route('inventarios.bodegas.estado', $origen), ['activo' => false])
            ->assertUnprocessable()->assertJsonValidationErrors('es_principal');

        $this->post($seleccionar, ['id_sucursal' => $norte, 'periodo' => 2026])->assertRedirect();
        $this->postJson($bodegas, ['codigo' => 'N01', 'nombre' => 'Almacén Norte', 'es_principal' => true])->assertCreated();
        $principal = DB::table('inv_bodegas')->where('id_sucursal', $norte)->value('id');
        $this->assertDatabaseHas('inv_bodegas', ['id' => $origen, 'es_principal' => null, 'activo' => true]);
        $this->assertDatabaseHas('inv_bodegas', ['id' => $principal, 'es_principal' => true]);

        $this->post($seleccionar, ['id_sucursal' => $matriz, 'periodo' => 2026])->assertRedirect();
        $compra = ['numero' => 'FAC-001', 'fecha' => '2026-01-15', 'id_tercero' => $proveedor, 'forma_pago' => '20',
            'lineas' => [['id_producto' => $producto, 'cantidad' => 2, 'precio' => 10]]];
        $this->get(route('compras.documentos.index'))->assertOk()->assertSee('data-workspace-link="compras.documentos.create"', false);
        $this->get(route('compras.documentos.create'))->assertOk()->assertSee('Almacén Norte')
            ->assertSee('Resumen de compra')->assertSee('data-compra', false)->assertDontSee('data-ajax-form', false);
        $this->postJson(route('compras.documentos.store'), $compra + ['id_bodega' => $origen])
            ->assertUnprocessable()->assertJsonValidationErrors('id_bodega');
        $this->post(route('compras.documentos.store'), $compra)->assertRedirect(route('compras.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => 'FAC-001', 'id_sucursal' => $matriz, 'id_bodega' => $principal]);
        $this->assertDatabaseHas('inv_existencias', ['id_bodega' => $principal, 'id_producto' => $producto, 'cantidad' => 2000]);
        $idCompra = DB::table('com_documentos')->where('numero', 'FAC-001')->value('id');
        $this->get(route('compras.documentos.show', $idCompra))->assertOk()->assertSee('Otros con utilización del sistema financiero');

        $this->post($seleccionar, ['id_sucursal' => $norte, 'periodo' => 2026])->assertRedirect();
        $this->postJson(route('inventarios.movimientos.store'), [
            'tipo' => 'transferencia', 'id_producto' => $producto, 'id_bodega' => $principal,
            'id_bodega_destino' => $origen, 'cantidad' => 1, 'motivo' => 'Distribución a Matriz',
        ])->assertCreated();
        $traslado = DB::table('inv_operaciones')->where('tipo', 'transferencia')->value('id');
        $this->get(route('inventarios.movimientos.index', ['id_bodega' => $origen]))->assertOk()->assertSee('Distribución a Matriz');
        $this->post($seleccionar, ['id_sucursal' => $matriz, 'periodo' => 2026])->assertRedirect();
        $this->get(route('inventarios.movimientos.show', $traslado))->assertOk()
            ->assertSeeInOrder(['Bodega de origen', 'Almacén Norte', 'Bodega de destino', 'Almacén Matriz']);
    }

    public function test_compra_carga_xml_y_consulta_la_clave_sri_solo_para_la_empresa_activa(): void
    {
        [$usuario] = $this->prepararFactura();
        $reglas = [];
        foreach (['compras' => 'gasto', 'impuesto_compras' => 'activo', 'proveedores' => 'pasivo'] as $regla => $clase) {
            $reglas[$regla] = DB::table('co_cuentas')->insertGetId([
                'id_empresa' => $usuario->id_empresa, 'codigo' => $regla, 'nombre' => $regla, 'clase' => $clase,
            ]);
        }
        DB::table('co_integraciones')->where('id_empresa', $usuario->id_empresa)
            ->update(['reglas' => json_encode(array_diff_key($reglas, ['compras' => true]), JSON_THROW_ON_ERROR)]);
        $plantilla = DB::table('inv_plantillas_contables')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'CONSUMO', 'nombre' => 'Consumo',
            'cuentas' => json_encode(['compras' => $reglas['compras']], JSON_THROW_ON_ERROR),
        ]);
        $categoria = DB::table('inv_categorias')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'CONSUMO', 'nombre' => 'Consumo',
            'id_plantilla_contable' => $plantilla,
        ]);
        $ruc = '0991234567001';
        $clave = '15012026'.'01'.$ruc.'1'.'001001000000009'.'12345678'.'10';
        $xml = <<<XML
            <factura>
              <infoTributaria><ambiente>1</ambiente><razonSocial>Proveedor XML</razonSocial><ruc>{$ruc}</ruc>
                <claveAcceso>{$clave}</claveAcceso><estab>001</estab><ptoEmi>001</ptoEmi><secuencial>000000009</secuencial><dirMatriz>Dirección XML</dirMatriz></infoTributaria>
              <infoFactura><fechaEmision>15/01/2026</fechaEmision><identificacionComprador>1000000000001</identificacionComprador>
                <moneda>DOLAR</moneda><totalSinImpuestos>18.00</totalSinImpuestos><importeTotal>20.70</importeTotal>
                <pagos><pago><formaPago>19</formaPago><total>10.70</total></pago><pago><formaPago>20</formaPago><total>10.00</total></pago></pagos>
              </infoFactura>
              <detalles><detalle><codigoPrincipal>XML-009</codigoPrincipal><descripcion>Soporte importado</descripcion><cantidad>2.000000</cantidad><precioUnitario>10.000000</precioUnitario>
                <descuento>2.00</descuento><precioTotalSinImpuesto>18.00</precioTotalSinImpuesto>
                <impuestos><impuesto><codigo>2</codigo><codigoPorcentaje>4</codigoPorcentaje><tarifa>15.00</tarifa><valor>2.70</valor></impuesto></impuestos>
              </detalle></detalles>
            </factura>
            XML;
        $formulario = route('compras.documentos.create');
        $importar = route('compras.documentos.importar');
        $this->actingAs($usuario)->get($formulario)->assertOk()
            ->assertSee('<details class="compra-importador" >', false);
        $this->from($formulario)->post($importar, [
            'xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('ajena.xml', str_replace('1000000000001', '1000000000002', $xml)),
        ])->assertSessionHasErrors('xml_compra');
        $this->assertDatabaseMissing('personas', ['identificacion' => $ruc]);
        $archivo = $this->post($importar, ['xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('factura.xml', $xml)]);
        $archivo->assertRedirect();
        $this->get($archivo->headers->get('Location'))->assertOk()->assertSee('XML del proveedor cargado')
            ->assertSee('Proveedor XML')->assertSee('Dirección XML')->assertSee('Soporte importado')->assertSee('Tarjeta de crédito')
            ->assertSee('Crear producto')->assertSee('data-codigo="XML-009" data-iva="4"', false)
            ->assertSee(route('inventarios.productos.store'));
        $persona = DB::table('personas')->where('identificacion', $ruc)->sole();
        $proveedor = DB::table('erp_proveedores')->where('identificacion', $ruc)->sole()->id;
        $this->assertDatabaseHas('erp_proveedores', ['id' => $proveedor, 'id_persona' => $persona->id, 'id_empresa' => $usuario->id_empresa]);
        DB::table('personas')->where('id', $persona->id)->update(['direccion' => 'Dirección de Personas', 'email' => 'compras@example.test']);
        $xmlCedula = str_replace('<identificacionComprador>1000000000001</identificacionComprador>',
            '<tipoIdentificacionComprador>05</tipoIdentificacionComprador><identificacionComprador>1000000000</identificacionComprador>', $xml);
        $archivoCedula = $this->post($importar, [
            'xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('factura-cedula.xml', $xmlCedula),
        ])->assertSessionHasNoErrors();
        $this->get($archivoCedula->headers->get('Location'))->assertOk()->assertSee('XML del proveedor cargado')
            ->assertSee('Dirección de Personas')->assertSee('compras@example.test');
        $this->assertSame(1, DB::table('erp_proveedores')->where('identificacion', $ruc)->count());
        $this->assertSame(1, DB::table('personas')->where('identificacion', $ruc)->count());
        $this->from($formulario)->post($importar, [
            'xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('factura-cedula-ajena.xml',
                str_replace('<identificacionComprador>1000000000</identificacionComprador>', '<identificacionComprador>1000000002</identificacionComprador>', $xmlCedula)),
        ])->assertSessionHasErrors('xml_compra');
        $this->post($importar, [
            'xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('factura-sin-tipo.xml',
                str_replace('<tipoIdentificacionComprador>05</tipoIdentificacionComprador>', '', $xmlCedula)),
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('com_documentos', 0);

        $respuesta = <<<XML
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><RespuestaAutorizacionComprobante>
              <claveAccesoConsultada>{$clave}</claveAccesoConsultada><numeroComprobantes>1</numeroComprobantes>
              <autorizaciones><autorizacion><estado>AUTORIZADO</estado><numeroAutorizacion>{$clave}</numeroAutorizacion>
                <fechaAutorizacion>2026-01-15T12:00:00-05:00</fechaAutorizacion><comprobante><![CDATA[{$xml}]]></comprobante>
              </autorizacion></autorizaciones>
            </RespuestaAutorizacionComprobante></soap:Body></soap:Envelope>
            XML;
        Http::preventStrayRequests();
        Http::fakeSequence('https://celcer.sri.gob.ec/*')->pushStatus(503)->push($respuesta);
        $this->from($formulario)->post($importar, ['clave_compra' => $clave])
            ->assertSessionHasErrors(['clave_compra' => 'No se obtuvo un XML autorizado del SRI. El servicio de autorización del SRI devolvió HTTP 503.']);
        $consulta = $this->post($importar, ['clave_compra' => $clave]);
        $consulta->assertRedirect();
        $this->get($consulta->headers->get('Location'))->assertOk()->assertSee('XML autorizado recibido del SRI');
        Http::assertSent(fn ($peticion) => str_contains($peticion->body(), '<claveAccesoComprobante>'.$clave.'</claveAccesoComprobante>'));
        $token = session('xml_compra.token');
        $importacionParalela = session('xml_compra');
        $producto = $this->postJson(route('inventarios.productos.store'), [
            'codigo' => 'XML-009', 'nombre' => 'Consumo de oficina', 'unidad' => 'Unidad',
            'tipo_producto' => 'no_inventariable', 'stock_minimo' => 5, 'iva_codigo' => 4, 'id_categoria' => $categoria,
        ])->assertCreated()->assertJsonPath('data.tipo_producto', 'no_inventariable')->json('data.id');
        $this->assertDatabaseHas('inv_productos', ['id' => $producto, 'stock_minimo' => 0]);
        $datosCompra = [
            'importacion_compra' => $token, 'numero' => '001-001-000000009', 'fecha' => '2026-01-15', 'id_tercero' => $proveedor,
            'lineas' => [['id_producto' => $producto, 'descripcion' => 'Soporte importado', 'cantidad' => 2, 'precio' => 10, 'descuento' => 2, 'tasa' => 15]],
        ];
        $this->post(route('compras.documentos.store'), $datosCompra)->assertRedirect(route('compras.documentos.index'));
        $compra = DB::table('com_documentos')->sole();
        $this->assertDatabaseHas('com_documentos', ['id' => $compra->id, 'subtotal' => 1800, 'impuesto' => 270, 'total' => 2070, 'id_bodega' => null]);
        $this->assertDatabaseHas('com_lineas', ['id_documento' => $compra->id, 'id_producto' => $producto, 'id_operacion' => null]);
        $this->assertDatabaseCount('inv_operaciones', 0);
        $this->assertDatabaseHas('co_lineas', ['id_cuenta' => $reglas['compras'], 'debe' => 1800, 'haber' => 0]);
        $this->assertDatabaseHas('co_lineas', ['id_cuenta' => $reglas['impuesto_compras'], 'debe' => 270, 'haber' => 0]);
        $this->assertDatabaseHas('co_lineas', ['id_cuenta' => $reglas['proveedores'], 'debe' => 0, 'haber' => 2070]);
        $datos = json_decode($compra->datos_factura, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($clave, $datos['clave_acceso']);
        $this->assertSame(['19', '20'], array_column($datos['pagos'], 'codigo'));
        $this->assertStringContainsString('<autorizacion>', $datos['xml_recibido']);
        $this->assertSame($datos['xml_recibido'], $this->get(route('compras.documentos.xml', $compra->id))
            ->assertDownload('compra-'.$compra->id.'.xml')->streamedContent());

        $mensaje = 'El comprobante 001-001-000000009 ya está registrado como compra de este proveedor.';
        $this->post($importar, ['xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('duplicada.xml', $xml)])
            ->assertRedirect($formulario)->assertSessionHasErrors(['compra_duplicada' => $mensaje])->assertSessionMissing('xml_compra');
        $this->followingRedirects()->post($importar, [
            'xml_compra' => \Illuminate\Http\UploadedFile::fake()->createWithContent('duplicada.xml', $xml),
        ])->assertOk()
            ->assertSee('data-aitrol-mensaje="error">'.$mensaje, false)->assertDontSee('XML del proveedor cargado');
        $this->post($importar, ['clave_compra' => $clave])
            ->assertRedirect($formulario)->assertSessionHasErrors(['compra_duplicada' => $mensaje])->assertSessionMissing('xml_compra');
        Http::assertSentCount(2);
        $this->withSession(['xml_compra' => $importacionParalela])->post(route('compras.documentos.store'), $datosCompra)
            ->assertRedirect($formulario)->assertSessionHasErrors(['compra_duplicada' => $mensaje])->assertSessionMissing('xml_compra');
        $this->assertDatabaseCount('com_documentos', 1);
    }

    public function test_sri_publica_archivos_solo_despues_de_confirmar_la_clave_de_la_factura(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos)->assertRedirect();
        $factura = DB::table('com_documentos')->first();
        $clave = json_decode($factura->datos_factura, true, flags: JSON_THROW_ON_ERROR)['clave_acceso'];
        $ruta = 'ventas.documentos.sri.';
        $urlLog = route($ruta.'log', $factura->id);
        $urlConsultar = route($ruta.'consultar', $factura->id);
        $urlXml = route($ruta.'descargar', [$factura->id, 'xml']);
        $urlPdf = route($ruta.'descargar', [$factura->id, 'pdf']);

        $xml = <<<XML
            <factura><infoTributaria><ambiente>1</ambiente><razonSocial>Empresa de prueba</razonSocial>
            <claveAcceso>{$clave}</claveAcceso><estab>001</estab><ptoEmi>001</ptoEmi><secuencial>000000001</secuencial></infoTributaria>
            <infoFactura><razonSocialComprador>Cliente de prueba</razonSocialComprador><importeTotal>20.70</importeTotal><moneda>USD</moneda></infoFactura>
            <detalles><detalle><descripcion>Servicio de soporte</descripcion></detalle></detalles></factura>
            XML;
        $inicio = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>'
            .'<autorizacionComprobanteResponse xmlns:sri="http://ec.gob.sri.ws.autorizacion"><RespuestaAutorizacionComprobante>'
            ."<claveAccesoConsultada>{$clave}</claveAccesoConsultada>";
        $fin = '</RespuestaAutorizacionComprobante></autorizacionComprobanteResponse></soap:Body></soap:Envelope>';
        $autorizacion = '<numeroComprobantes>1</numeroComprobantes><autorizaciones><autorizacion><estado>AUTORIZADO</estado>'
            ."<numeroAutorizacion>{$clave}</numeroAutorizacion><fechaAutorizacion>2026-01-15T12:01:00-05:00</fechaAutorizacion>"
            .'<comprobante><![CDATA['.$xml.']]></comprobante></autorizacion></autorizaciones>';
        $xmlAjeno = str_replace($clave, str_repeat('9', 49), $xml);
        $rechazo = '<numeroComprobantes>1</numeroComprobantes><autorizaciones><autorizacion><estado>NO AUTORIZADO</estado>'
            .'<mensajes><mensaje><identificador>43</identificador><mensaje>Firma inválida</mensaje></mensaje></mensajes></autorizacion></autorizaciones>';
        Http::preventStrayRequests();
        Http::fakeSequence('https://celcer.sri.gob.ec/*')
            ->push($inicio.$rechazo.$fin)
            ->push($inicio.str_replace($xml, $xmlAjeno, $autorizacion).$fin)
            ->push($inicio.$autorizacion.$fin);

        $this->get(route('ventas.documentos.index'))->assertOk()->assertSee('Ver log')->assertDontSee('Descargar XML');
        $this->get($urlXml)->assertNotFound();
        $this->post($urlConsultar)->assertRedirect($urlLog);
        $this->get($urlLog)->assertOk()->assertSee('No autorizado')->assertSee('Firma inválida')->assertDontSee('Descargar PDF');
        $this->post($urlConsultar)->assertRedirect($urlLog);
        $this->get($urlPdf)->assertNotFound();
        $this->post($urlConsultar)->assertRedirect($urlLog);
        $this->get(route('ventas.documentos.index'))->assertOk()->assertSee('Descargar XML')->assertSee('Descargar PDF');
        $this->get($urlLog)->assertOk()->assertSee('Autorizado')->assertSee('Consulta fallida')->assertSee('Firma inválida');
        $this->assertStringContainsString($xml, $this->get($urlXml)->assertDownload('factura-'.$clave.'.xml')->streamedContent());
        $pdf = $this->get($urlPdf)->assertDownload('factura-'.$clave.'.pdf')->streamedContent();
        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringContainsString('Servicio de soporte', $pdf);
        $this->post($urlConsultar)->assertRedirect($urlLog);
        Http::assertSentCount(3);

        $empresaAjena = EmpresaModel::create(['ruc' => '1000000000002', 'razon_social' => 'Empresa ajena']);
        DB::table('erp_sucursales')->insert(['id_empresa' => $empresaAjena->id, 'codigo' => '001', 'nombre' => 'Matriz']);
        $ajeno = User::factory()->create(['id_empresa' => $empresaAjena->id, 'is_admin' => true]);
        $this->actingAs($ajeno)->get($urlXml)->assertNotFound();
    }

    public function test_registra_factura_con_descuento_y_tarifa_fiscal_sin_modal(): void
    {
        [$usuario, $datos] = $this->prepararFactura();

        $this->actingAs($usuario)->get(route('ventas.documentos.create'))
            ->assertOk()->assertSee('data-factura', false)->assertDontSee('data-ajax-form', false)->assertDontSee('name="numero"', false);
        $this->post(route('ventas.documentos.store'), $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['numero' => '001-001-000000001', 'subtotal' => 1800, 'impuesto' => 270, 'total' => 2070]);

        $documento = DB::table('com_documentos')->first();
        $this->assertDatabaseHas('co_asientos', ['origen' => 'comercial.documento', 'id_origen' => $documento->id, 'total' => 2070]);
        $this->get(route('ventas.documentos.index'))->assertOk()
            ->assertSee('Ver asiento')->assertSee('Ver trazabilidad')->assertSee('Agregar retención')
            ->assertSee('Anular con nota de crédito')->assertSee('Anular factura');
        $this->get(route('ventas.documentos.trazabilidad', $documento->id))->assertOk()->assertSee($documento->numero);
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

    public function test_muestra_el_pago_y_valida_hasta_cinco_datos_adicionales(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario);
        $url = route('ventas.documentos.store');
        $adicional = ['nombre' => 'Orden de compra', 'valor' => str_repeat('a', 299)];
        $this->postJson($url, $datos + ['informacion_adicional' => array_fill(0, 6, $adicional)])
            ->assertUnprocessable()->assertJsonValidationErrors('informacion_adicional');
        $this->postJson($url, $datos + ['informacion_adicional' => [array_replace($adicional, ['valor' => str_repeat('a', 300)])]])
            ->assertUnprocessable()->assertJsonValidationErrors('informacion_adicional.0.valor');
        $this->assertDatabaseCount('com_documentos', 0);

        $this->post($url, $datos + ['plazo_dias' => 15, 'informacion_adicional' => array_fill(0, 5, $adicional)])
            ->assertRedirect(route('ventas.documentos.index'));
        $documento = DB::table('com_documentos')->first();
        $this->get(route('ventas.documentos.show', $documento->id))->assertOk()
            ->assertSee('Forma de pago')->assertSee('Días de crédito')->assertSee('>15</dd>', false)->assertSee('Información adicional')
            ->assertSee($adicional['nombre'])->assertSee($adicional['valor']);
    }

    public function test_anula_el_importe_total_con_nota_de_credito_vinculada(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos + ['aplicar_propina' => true])
            ->assertRedirect(route('ventas.documentos.index'));
        $factura = DB::table('com_documentos')->first();
        $this->get(route('ventas.notas-credito.create', ['origen' => $factura->id, 'total' => 1]))->assertOk()
            ->assertSee($factura->numero);
        $nota = [
            'numero' => 'NC-001', 'fecha' => $datos['fecha'], 'id_documento_origen' => $factura->id,
            'credito_total' => true, 'observacion' => 'Corrección de la factura',
        ];
        $this->postJson(route('ventas.notas-credito.store'), $nota)->assertCreated();
        $this->assertDatabaseHas('com_documentos', [
            'numero' => 'NC-001', 'id_documento_origen' => $factura->id, 'subtotal' => 1800,
            'impuesto' => 270, 'propina' => 180, 'total' => 2250,
        ]);
        $idNota = DB::table('com_documentos')->where('numero', 'NC-001')->value('id');
        $this->assertDatabaseHas('co_asientos', ['origen' => 'comercial.documento', 'id_origen' => $idNota, 'total' => 2250]);
        $asientoNota = DB::table('co_asientos')->where('origen', 'comercial.documento')->where('id_origen', $idNota)->value('id');
        $this->get(route('ventas.documentos.trazabilidad', $factura->id))->assertOk()
            ->assertSee('<details class="trazabilidad-diagrama" data-trazabilidad-diagrama>', false)
            ->assertSeeInOrder(['Diagrama de trazabilidad', $factura->numero, 'Asiento contable', 'Nota de crédito', 'NC-001', 'Asiento contable'])
            ->assertSee(route('ventas.notas-credito.show', $idNota))
            ->assertSee(route('contabilidad.asientos.show', $asientoNota));
    }

    public function test_no_registra_la_factura_si_la_contabilizacion_esta_inactiva(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        DB::table('co_integraciones')->where('id_empresa', $usuario->id_empresa)->update(['activa' => false]);

        $this->actingAs($usuario)->postJson(route('ventas.documentos.store'), $datos)
            ->assertUnprocessable()->assertJsonValidationErrors('contabilidad');
        $this->assertDatabaseCount('com_documentos', 0);
        $this->assertDatabaseCount('co_asientos', 0);
    }

    public function test_contabiliza_una_factura_existente_una_sola_vez(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $id = DB::table('com_documentos')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => DB::table('erp_sucursales')->value('id'),
            'id_cliente' => $datos['id_tercero'], 'tercero' => 'Cliente de pruebas', 'fecha' => $datos['fecha'],
            'tipo' => 'documentos', 'modulo' => 'ventas', 'numero' => '001-001-000000001',
            'subtotal' => 1800, 'impuesto' => 270, 'total' => 2070,
        ]);

        $this->actingAs($usuario)->get(route('ventas.documentos.index'))->assertOk()->assertSee('Generar asiento');
        $url = route('ventas.documentos.contabilizar', $id);
        $this->postJson($url)->assertOk();
        $this->postJson($url)->assertOk();
        $this->assertDatabaseCount('co_asientos', 1);
        $this->assertDatabaseHas('co_asientos', ['origen' => 'comercial.documento', 'id_origen' => $id, 'total' => 2070]);
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
        $this->get(route('inventarios.productos.historial', $producto))->assertOk()
            ->assertSee('001-001-000000001')->assertSee('Últimos movimientos de existencias')->assertSee('Principal');
        $this->postJson(route('ventas.documentos.anular', $documento), ['motivo' => 'Corrección de cantidad'])->assertSuccessful();
        $movimiento = DB::table('com_lineas')->where('id_documento', $documento)->orderBy('id')->first();
        $this->get(route('ventas.documentos.trazabilidad', $documento))->assertOk()
            ->assertSeeInOrder([
                'Diagrama de trazabilidad', 'Anulada', 'Asiento de reversión',
                'Movimiento de inventario', 'Producto fiscal', 'P1 · Principal', 'Salida 1.000',
                'Producto fiscal', 'P1 · Auxiliar', 'Salida 1.000',
                'Reversión de inventario', 'Producto fiscal', 'Entrada 1.000',
            ])
            ->assertSee(route('inventarios.movimientos.show', $movimiento->id_operacion))
            ->assertSee(route('inventarios.movimientos.show', $movimiento->id_operacion_reversa));
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

    public function test_registra_servicio_en_el_historial_y_maneja_configuracion_fiscal_incompleta(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario);
        $producto = DB::table('inv_productos')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'S1', 'nombre' => 'Soporte', 'unidad' => 'unidad',
            'tipo_producto' => 'servicio', 'iva_codigo' => 4,
        ]);
        $datos['lineas'][0]['id_producto'] = $producto;
        $alta = route('ventas.documentos.store');
        DB::table('erp_parametros_fiscales')->where('id_empresa', $usuario->id_empresa)->update(['activo' => false]);
        $this->postJson($alta, $datos)->assertUnprocessable()->assertJsonValidationErrors('lineas.0.iva_codigo');
        DB::table('erp_parametros_fiscales')->where('id_empresa', $usuario->id_empresa)->update(['activo' => true]);
        $configuracion = config('comercial.ventas.facturacion_electronica');
        config(['comercial.ventas.facturacion_electronica' => null]);
        $this->postJson($alta, $datos)->assertUnprocessable()->assertJsonValidationErrors('emisor');
        $this->assertDatabaseCount('com_documentos', 0);
        config(['comercial.ventas.facturacion_electronica' => $configuracion]);
        $this->post($alta, $datos)->assertRedirect(route('ventas.documentos.index'));

        $this->get(route('inventarios.productos.historial', $producto))->assertOk()
            ->assertSee('001-001-000000001')->assertDontSee('Últimos movimientos de existencias');
        $this->assertDatabaseCount('inv_kardex', 0);
    }

    public function test_factura_con_la_serie_sri_de_la_sucursal_activa(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario);
        $matriz = DB::table('erp_sucursales')->where('id_empresa', $usuario->id_empresa)->value('id');
        $edicion = route('configuracion.sucursales.update', $matriz);
        $altaFactura = route('ventas.documentos.store');
        $cambios = ['codigo' => 'PRINCIPAL', 'nombre' => 'Matriz', 'establecimiento_sri' => '007', 'punto_emision_sri' => '009'];
        $this->get(route('configuracion.sucursales.edit', $matriz))->assertOk()
            ->assertSee('Establecimiento SRI')->assertSee('Punto de emisión SRI');
        $this->putJson($edicion, array_replace($cambios, ['punto_emision_sri' => '000']))
            ->assertUnprocessable()->assertJsonValidationErrors('punto_emision_sri');
        $this->putJson($edicion, array_replace($cambios, ['establecimiento_sri' => null, 'punto_emision_sri' => null]))->assertOk();
        $this->postJson($altaFactura, $datos)->assertUnprocessable()->assertJsonValidationErrors('emisor');
        $this->putJson($edicion, $cambios)->assertOk();
        $this->assertDatabaseHas('erp_sucursales', ['id' => $matriz, 'id_usuario_modificacion' => $usuario->id] + $cambios);
        $this->post($altaFactura, $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['id_sucursal' => $matriz, 'numero' => '007-009-000000001']);
        $fiscal = json_decode(DB::table('com_documentos')->where('id_sucursal', $matriz)->value('datos_factura'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('007009', substr($fiscal['clave_acceso'], 24, 6));

        $nueva = route('configuracion.sucursales.store');
        $segunda = array_replace($cambios, ['codigo' => 'NORTE', 'nombre' => 'Sucursal Norte']);
        $this->postJson($nueva, $segunda)->assertUnprocessable()->assertJsonValidationErrors('punto_emision_sri');
        $this->postJson($nueva, array_replace($segunda, ['punto_emision_sri' => '010']))->assertOk();
        $norte = DB::table('erp_sucursales')->where('id_empresa', $usuario->id_empresa)->where('codigo', 'NORTE')->value('id');
        $this->assertDatabaseHas('erp_sucursales', ['id' => $norte, 'id_usuario_creacion' => $usuario->id, 'id_usuario_modificacion' => null]);
        $this->get(route('configuracion.sucursales.index'))->assertOk()
            ->assertSee('Creado por')->assertSee('Creado el')->assertSee('Modificado por')->assertSee('Modificado el');
        $this->post(route('configuracion.contexto.update'), ['id_sucursal' => $norte, 'periodo' => 2026])->assertRedirect(route('dashboard'));
        $this->post($altaFactura, $datos)->assertRedirect(route('ventas.documentos.index'));
        $this->assertDatabaseHas('com_documentos', ['id_sucursal' => $norte, 'numero' => '007-010-000000001']);
    }

    public function test_importa_la_retencion_de_venta_desde_xml_o_desde_el_sri_y_evita_duplicados(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos)->assertRedirect();
        $id = DB::table('com_documentos')->value('id');
        $this->get(route('ventas.retenciones.create', ['origen' => $id]))->assertOk()
            ->assertSee('Número de documento')->assertSee('Importar por XML o clave de acceso')
            ->assertSee('data-retencion-campo="base_retencion"', false)->assertSee('value="18.00"', false)
            ->assertSee('data-base-iva="2.70"', false)->assertSee('Valor retenido')->assertSee('Total retenido')
            ->assertSee('data-retencion-catalogo', false)->assertSee(route('ventas.retenciones.catalogo'));
        $this->getJson(route('ventas.retenciones.opciones', 'origenes'))->assertOk()
            ->assertJsonPath('results.0.base_renta', '18.00')->assertJsonPath('results.0.base_iva', '2.70')
            ->assertJsonPath('pagination.more', false);
        $this->postJson(route('ventas.retenciones.store'), [
            'id_documento_origen' => $id, 'numero' => '001-002-000000004', 'fecha' => $datos['fecha'],
            'lineas' => [
                ['descripcion' => 'Retención de bienes', 'tipo_retencion' => 'renta', 'codigo_retencion' => '312',
                    'base_retencion' => '999.00', 'porcentaje_retencion' => '1.75'],
                ['descripcion' => 'Retención de IVA', 'tipo_retencion' => 'iva', 'codigo_retencion' => '1', 'porcentaje_retencion' => '30'],
            ],
        ])->assertCreated();
        $retencionManual = DB::table('com_documentos')->where('numero', '001-002-000000004')->value('id');
        $this->assertDatabaseHas('com_lineas', ['id_documento' => $retencionManual, 'tipo_retencion' => 'renta', 'base_retencion' => 1800, 'total' => 32]);
        $this->assertDatabaseHas('com_lineas', ['id_documento' => $retencionManual, 'tipo_retencion' => 'iva', 'base_retencion' => 270, 'total' => 81]);
        $asiento = DB::table('co_asientos')->where('origen', 'comercial.documento')->where('id_origen', $retencionManual)->value('id');
        $clientes = DB::table('co_cuentas')->where('codigo', 'clientes')->value('id');
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asiento, 'id_cuenta' => $clientes, 'haber' => 113]);
        $this->get(route('ventas.documentos.trazabilidad', $id))->assertOk()->assertSee('USD 19.57');
        $this->get(route('ventas.retenciones.recibida', ['origen' => $id]))->assertOk()
            ->assertSee('Archivo XML')->assertSee('Clave de acceso');
        $serie = '001002000000005';
        $base = '150120260709123456780011'.$serie.'123456781';
        $clave = $base.AutorizacionSri::digitoVerificador($base);
        $xml = <<<XML
            <comprobanteRetencion id="comprobante" version="2.0.0">
                <infoTributaria><ambiente>1</ambiente><tipoEmision>1</tipoEmision><ruc>0912345678001</ruc>
                    <claveAcceso>{$clave}</claveAcceso><codDoc>07</codDoc><estab>001</estab><ptoEmi>002</ptoEmi><secuencial>000000005</secuencial>
                </infoTributaria>
                <infoCompRetencion><fechaEmision>15/01/2026</fechaEmision><tipoIdentificacionSujetoRetenido>04</tipoIdentificacionSujetoRetenido><identificacionSujetoRetenido>1000000000001</identificacionSujetoRetenido></infoCompRetencion>
                <docsSustento><docSustento><codDocSustento>01</codDocSustento><numDocSustento>001001000000001</numDocSustento><fechaEmisionDocSustento>15/01/2026</fechaEmisionDocSustento>
                    <retenciones><retencion><codigo>1</codigo><codigoRetencion>312</codigoRetencion><baseImponible>10.00</baseImponible><porcentajeRetener>2.00</porcentajeRetener><valorRetenido>0.20</valorRetenido></retencion></retenciones>
                </docSustento></docsSustento>
            </comprobanteRetencion>
            XML;
        $ruta = route('ventas.retenciones.importar');
        $this->postJson($ruta, ['xml_retencion' => UploadedFile::fake()->createWithContent('retencion.xml', str_replace('1000000000001', '1000000000002', $xml))])
            ->assertUnprocessable()->assertJsonValidationErrors('xml_retencion');
        $this->postJson($ruta, ['xml_retencion' => UploadedFile::fake()->createWithContent('retencion.xml', $xml)])->assertOk();
        $this->assertDatabaseHas('com_documentos', ['id_documento_origen' => $id, 'numero' => '001-002-000000005', 'total' => 20]);
        $this->postJson($ruta, ['xml_retencion' => UploadedFile::fake()->createWithContent('retencion.xml', $xml)])->assertUnprocessable();

        $segundaBase = str_replace($serie, '001002000000006', $base);
        $segundaClave = $segundaBase.AutorizacionSri::digitoVerificador($segundaBase);
        $xml = str_replace([$clave, '000000005</secuencial>'], [$segundaClave, '000000006</secuencial>'], $xml);
        Http::fake(['celcer.sri.gob.ec/*' => Http::response(
            "<RespuestaAutorizacionComprobante><claveAccesoConsultada>{$segundaClave}</claveAccesoConsultada><numeroComprobantes>1</numeroComprobantes><autorizaciones><autorizacion><estado>AUTORIZADO</estado><numeroAutorizacion>{$segundaClave}</numeroAutorizacion><fechaAutorizacion>2026-01-15T12:00:00</fechaAutorizacion><comprobante><![CDATA[{$xml}]]></comprobante></autorizacion></autorizaciones></RespuestaAutorizacionComprobante>"
        )]);
        $this->postJson($ruta, ['clave_acceso' => $segundaClave, 'id_documento_origen' => $id])->assertOk();
        $retencion = DB::table('com_documentos')->where('numero', '001-002-000000006')->value('id');
        $this->get(route('ventas.retenciones.show', $retencion))->assertOk()->assertSee('Autorizada en el SRI');
    }

    public function test_la_retencion_de_venta_requiere_asiento_para_reducir_el_saldo(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos)->assertRedirect();
        $id = DB::table('com_documentos')->value('id');
        DB::table('co_integraciones')->where('id_empresa', $usuario->id_empresa)->update(['activa' => false]);

        $this->postJson(route('ventas.retenciones.store'), [
            'id_documento_origen' => $id, 'numero' => '001-002-000000004', 'fecha' => $datos['fecha'],
            'lineas' => [['descripcion' => 'Retención de renta', 'tipo_retencion' => 'renta', 'codigo_retencion' => '312', 'porcentaje_retencion' => '2']],
        ])->assertUnprocessable()->assertJsonValidationErrors('contabilidad');

        $this->assertDatabaseMissing('com_documentos', ['modulo' => 'ventas', 'tipo' => 'retenciones']);
        $this->get(route('ventas.documentos.trazabilidad', $id))->assertOk()->assertSee('USD 20.70');
    }

    public function test_genera_el_xml_ats_de_retencion_con_su_propio_secuencial(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos)->assertRedirect();
        DB::table('co_integraciones')->where('id_empresa', $usuario->id_empresa)->update(['activa' => false]);
        app(EloquentProveedorRepository::class)->registrarDesdeFactura('1792049504001', 'Proveedor & Asociados', 'Quito');
        $proveedor = DB::table('erp_proveedores')->where('id_empresa', $usuario->id_empresa)->value('id');
        $this->post(route('compras.documentos.store'), [
            'numero' => '052-070-000054468', 'fecha' => $datos['fecha'], 'id_tercero' => $proveedor, 'forma_pago' => '20',
            'lineas' => [['descripcion' => 'Servicio', 'cantidad' => 1, 'precio' => 10, 'tasa' => 15]],
        ])->assertRedirect(route('compras.documentos.index'));
        $compra = DB::table('com_documentos')->where('modulo', 'compras')->value('id');
        $this->get(route('compras.retenciones.create', ['origen' => $compra]), ['X-Aitrol-Fragmento' => 'contenido'])->assertOk()
            ->assertSee('Sustento tributario')->assertDontSee('name="numero"', false)->assertDontSee('Archivo XML')
            ->assertSee('data-ajax-form', false)->assertDontSee('<!doctype html>', false)
            ->assertSee('value="10.00"', false)->assertSee('data-base-iva="1.50"', false)->assertSee('Total retenido')
            ->assertSee('data-retencion-catalogo', false)->assertSee(route('compras.retenciones.catalogo'));
        $this->postJson(route('compras.retenciones.store'), [
            'fecha' => $datos['fecha'], 'id_documento_origen' => $compra, 'codigo_sustento' => '02', 'parte_relacionada' => 'NO',
            'lineas' => [['tipo_retencion' => 'renta', 'codigo_retencion' => '312', 'base_retencion' => '999.00', 'porcentaje_retencion' => '1.75']],
        ])->assertOk();
        $registro = DB::table('com_documentos')->where('modulo', 'compras')->where('tipo', 'retenciones')->first();
        $this->assertSame('001-001-000000001', $registro->numero);
        $fiscal = json_decode($registro->datos_factura, true, flags: JSON_THROW_ON_ERROR);
        $xml = AutorizacionSri::leerXml($fiscal['xml_retencion']);
        $this->assertSame('2.0.0', (string) $xml['version']);
        $this->assertSame('07', (string) $xml->infoTributaria->codDoc);
        $this->assertSame('Proveedor & Asociados', (string) $xml->infoCompRetencion->razonSocialSujetoRetenido);
        $this->assertMatchesRegularExpression('/\A15012026071000000000001[12][0-9]{25}\z/', (string) $xml->infoTributaria->claveAcceso);
        $sustento = $xml->docsSustento->docSustento;
        $this->assertSame('052070000054468', (string) $sustento->numDocSustento);
        $this->assertSame('20', (string) $sustento->pagos->pago->formaPago);
        $this->assertSame('10.00', (string) $sustento->retenciones->retencion->baseImponible);
        $this->assertSame('0.18', (string) $sustento->retenciones->retencion->valorRetenido);
        $this->get(route('compras.retenciones.xml', $registro->id))->assertOk()->assertHeader('Content-Type', 'application/xml');

        $emitir = route('compras.retenciones.emitir', $registro->id);
        $this->postJson($emitir)->assertUnprocessable()->assertJsonValidationErrors('firma');
        $this->travelBack();
        $this->post(route('configuracion.contexto.update'), [
            'id_sucursal' => DB::table('erp_sucursales')->value('id'), 'periodo' => 2026,
        ])->assertRedirect();
        DB::table('erp_identidad_empresas')->insert([
            'id_empresa' => $usuario->id_empresa, 'certificado_cifrado' => $this->certificadoParaRetencion(),
        ]);
        $clave = $fiscal['clave_acceso'];
        $autorizacion = "<RespuestaAutorizacionComprobante><claveAccesoConsultada>{$clave}</claveAccesoConsultada><numeroComprobantes>0</numeroComprobantes></RespuestaAutorizacionComprobante>";
        Http::fake([
            'celcer.sri.gob.ec/*RecepcionComprobantesOffline' => Http::response('<RespuestaRecepcionComprobante><estado>RECIBIDA</estado></RespuestaRecepcionComprobante>'),
            'celcer.sri.gob.ec/*AutorizacionComprobantesOffline' => function () use (&$autorizacion) {
                return Http::response($autorizacion);
            },
        ]);
        $this->postJson($emitir)->assertOk()->assertJsonPath('message', 'Recibida · pendiente de autorización');
        $this->postJson($emitir)->assertOk();
        $recepciones = Http::recorded(fn ($peticion) => str_ends_with($peticion->url(), 'RecepcionComprobantesOffline'));
        $this->assertCount(1, $recepciones);
        $xmlSoap = AutorizacionSri::leerXml($recepciones->first()[0]->body());
        $binario = base64_decode((string) $xmlSoap->xpath('//*[local-name()="xml"]')[0], true);
        $firmado = new DOMDocument;
        $this->assertTrue($firmado->loadXML($binario, LIBXML_NONET));
        $nodos = new DOMXPath($firmado);
        $nodos->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $cert = $nodos->evaluate('string(//ds:X509Certificate)');
        $this->assertSame(1, openssl_verify(
            $nodos->query('//ds:SignedInfo')->item(0)->C14N(),
            base64_decode($nodos->evaluate('string(//ds:SignatureValue)'), true),
            "-----BEGIN CERTIFICATE-----\n".chunk_split($cert, 64, "\n")."-----END CERTIFICATE-----\n",
            OPENSSL_ALGO_SHA1,
        ));
        foreach (['PropiedadesComprobante', 'CertificadoComprobante'] as $id) {
            $this->assertSame(
                base64_encode(sha1($nodos->query("//*[@Id='{$id}']")->item(0)->C14N(), true)),
                $nodos->evaluate("string(//ds:Reference[@URI='#{$id}']/ds:DigestValue)"),
            );
        }
        $digestDocumento = $nodos->evaluate("string(//ds:Reference[@URI='#comprobante']/ds:DigestValue)");
        $nodos->query('//ds:Signature')->item(0)->remove();
        $this->assertSame(base64_encode(sha1($firmado->documentElement->C14N(), true)), $digestDocumento);

        $autorizacion = "<RespuestaAutorizacionComprobante><claveAccesoConsultada>{$clave}</claveAccesoConsultada><numeroComprobantes>1</numeroComprobantes><autorizaciones><autorizacion><estado>AUTORIZADO</estado><numeroAutorizacion>{$clave}</numeroAutorizacion><fechaAutorizacion>2026-01-15T12:00:00</fechaAutorizacion><comprobante><![CDATA[{$binario}]]></comprobante></autorizacion></autorizaciones></RespuestaAutorizacionComprobante>";
        $this->postJson($emitir)->assertOk()->assertJsonPath('message', 'Autorizada en el SRI');
        $this->get(route('compras.retenciones.xml', $registro->id))->assertOk()->assertStreamed();
    }

    private function certificadoParaRetencion(): string
    {
        $config = tempnam(sys_get_temp_dir(), 'aitrol-ret-');
        file_put_contents($config, "[req]\ndistinguished_name = dn\n[dn]\n");
        try {
            $opciones = ['config' => $config, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048];
            $privada = openssl_pkey_new($opciones);
            $solicitud = openssl_csr_new(['commonName' => 'Certificado de prueba'], $privada, $opciones);
            $certificado = openssl_csr_sign($solicitud, null, $privada, 10, $opciones, 7);
            $this->assertTrue(openssl_pkcs12_export($certificado, $pkcs12, $privada, 'prueba'));

            return Crypt::encryptString(json_encode(['pkcs12' => base64_encode($pkcs12), 'clave' => 'prueba'], JSON_THROW_ON_ERROR));
        } finally {
            unlink($config);
        }
    }

    public function test_el_catalogo_busca_solo_el_tipo_y_la_vigencia_solicitados(): void
    {
        [$usuario] = $this->prepararFactura();
        $this->actingAs($usuario);
        $ruta = 'compras.retenciones.catalogo';
        $this->getJson(route($ruta, ['tipo' => 'renta', 'fecha' => '2026-11-28']))
            ->assertOk()->assertJsonCount(15, 'results')->assertJsonPath('pagination.more', true);
        $this->getJson(route($ruta, ['tipo' => 'renta', 'fecha' => '2026-02-28', 'q' => '312']))
            ->assertOk()->assertJsonPath('results.0.id', '312')->assertJsonPath('results.0.text', '312 · Adquisición de bienes muebles corporales · 1.75 %');
        $this->getJson(route($ruta, ['tipo' => 'renta', 'fecha' => '2026-03-01', 'q' => '312']))
            ->assertOk()->assertJsonPath('results.0.text', '312 · Adquisición de bienes muebles corporales · 2 %');
        $this->getJson(route($ruta, ['tipo' => 'iva', 'fecha' => '2026-11-28', 'q' => '312']))
            ->assertOk()->assertJsonCount(0, 'results');
        $this->getJson(route($ruta, ['tipo' => 'iva', 'fecha' => '2026-11-28', 'q' => '50']))
            ->assertOk()->assertJsonPath('results.0.id', '11');
        $this->getJson(route('ventas.retenciones.catalogo', ['tipo' => 'renta', 'fecha' => '2026-03-01', 'q' => 'bienes muebles']))
            ->assertOk()->assertJsonFragment(['id' => '312', 'descripcion' => 'Adquisición de bienes muebles corporales', 'porcentaje' => '2']);
    }

    public function test_el_cobro_confirmado_baja_el_saldo_y_se_concilia_por_separado(): void
    {
        [$usuario, $datos] = $this->prepararFactura();
        $this->actingAs($usuario)->post(route('ventas.documentos.store'), $datos)->assertRedirect();
        $factura = DB::table('com_documentos')->value('id');
        $sucursal = DB::table('erp_sucursales')->value('id');
        $banco = DB::table('cb_cuentas')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'codigo' => 'BAN-1', 'nombre' => 'Banco de prueba',
            'tipo' => 'banco', 'moneda' => 'USD',
        ]);
        $cuentaContable = DB::table('co_cuentas')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'codigo' => 'banco', 'nombre' => 'Banco', 'clase' => 'activo', 'efectivo' => true,
        ]);
        DB::table('co_integraciones')->where('id_empresa', $usuario->id_empresa)->update(['tesoreria' => json_encode([$banco => $cuentaContable])]);
        $detalle = route('ventas.documentos.trazabilidad', $factura);
        $this->get($detalle)->assertOk()->assertSee('USD 20.70')->assertDontSee('Cobros registrados');
        $cobro = [
            'id_cuenta' => $banco, 'fecha' => $datos['fecha'], 'importe' => '10.00', 'referencia' => 'TX-123',
            'clave_operacion' => '69f226f5-b575-4106-b4cb-dabfd7608a2f',
        ];
        $ruta = route('ventas.documentos.cobro.store', $factura);
        $this->postJson($ruta, $cobro)->assertOk();
        $this->postJson($ruta, $cobro)->assertOk();
        $this->assertDatabaseCount('cb_operaciones', 1);
        $this->postJson($ruta, array_replace($cobro, ['clave_operacion' => 'd34baa06-d857-4be4-825a-5ef6be6c94de', 'importe' => '11.00']))
            ->assertUnprocessable()->assertJsonValidationErrors('importe');
        $this->get($detalle)->assertOk()->assertSee('USD 10.70')->assertSee('Pendiente de conciliación');
        $asientoCobro = DB::table('co_asientos')->where('origen', 'tesoreria.operacion')->value('id');
        $clientes = DB::table('co_cuentas')->where('codigo', 'clientes')->value('id');
        $this->assertDatabaseHas('co_lineas', ['id_asiento' => $asientoCobro, 'id_cuenta' => $clientes, 'haber' => 1000]);

        $extracto = app(ConciliacionBancaria::class)->importar([
            'id_cuenta' => $banco, 'nombre' => 'Extracto de enero',
            'lineas' => [['fecha' => $datos['fecha'], 'referencia' => 'TX-123', 'descripcion' => 'Transferencia', 'importe' => '10.00']],
        ]);
        $linea = DB::table('cb_lineas_extracto')->where('id_extracto', $extracto->id)->value('id');
        app(ConciliacionBancaria::class)->conciliar($linea, DB::table('cb_asientos')->value('id'));
        $this->get($detalle)->assertOk()->assertSee('Conciliado')->assertSee('USD 10.70');
        $this->postJson(route('ventas.documentos.anular', $factura), ['motivo' => 'Anular después de cobrar'])->assertConflict();
    }

    /** @return array{User, array} */
    private function prepararFactura(): array
    {
        $this->travelTo(Carbon::parse('2026-01-15 12:00', 'America/Guayaquil'));
        $empresa = EmpresaModel::create(['ruc' => '1000000000001', 'razon_social' => 'Empresa de pruebas', 'direccion' => 'Av. Central']);
        DB::table('erp_sucursales')->insert([
            'id_empresa' => $empresa->id, 'codigo' => 'PRINCIPAL', 'nombre' => 'Matriz',
            'establecimiento_sri' => '001', 'punto_emision_sri' => '001',
        ]);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $reglas = [];
        foreach (['clientes' => 'activo', 'ventas' => 'ingreso', 'impuesto_ventas' => 'pasivo', 'propinas_por_pagar' => 'pasivo',
            'costo_ventas' => 'gasto', 'inventario' => 'activo', 'ajustes_inventario' => 'gasto', 'retencion_ventas' => 'activo'] as $regla => $clase) {
            $reglas[$regla] = DB::table('co_cuentas')->insertGetId([
                'id_empresa' => $empresa->id, 'codigo' => $regla, 'nombre' => $regla, 'clase' => $clase,
            ]);
        }
        DB::table('co_periodos')->insert(['id_empresa' => $empresa->id, 'nombre' => '2026', 'desde' => '2026-01-01', 'hasta' => '2026-12-31']);
        DB::table('co_integraciones')->insert([
            'id_empresa' => $empresa->id, 'activa' => true, 'fecha_inicio' => '2026-01-01',
            'reglas' => json_encode($reglas, JSON_THROW_ON_ERROR), 'tesoreria' => '{}',
        ]);
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
