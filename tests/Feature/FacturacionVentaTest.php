<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class FacturacionVentaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_factura_con_existencia_insuficiente_mantiene_los_datos_adicionales_plegados(): void
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
            ->assertSee('<details>', false)->assertDontSee('<details open', false)->assertSee('value="REF-123"', false);
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
        $this->actingAs($usuario)->from($formulario)->post($importar, [
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
        $producto = $this->postJson(route('inventarios.productos.store'), [
            'codigo' => 'XML-009', 'nombre' => 'Consumo de oficina', 'unidad' => 'Unidad',
            'tipo_producto' => 'no_inventariable', 'stock_minimo' => 5, 'iva_codigo' => 4, 'id_categoria' => $categoria,
        ])->assertCreated()->assertJsonPath('data.tipo_producto', 'no_inventariable')->json('data.id');
        $this->assertDatabaseHas('inv_productos', ['id' => $producto, 'stock_minimo' => 0]);
        $this->post(route('compras.documentos.store'), [
            'importacion_compra' => $token, 'numero' => '001-001-000000009', 'fecha' => '2026-01-15', 'id_tercero' => $proveedor,
            'lineas' => [['id_producto' => $producto, 'descripcion' => 'Soporte importado', 'cantidad' => 2, 'precio' => 10, 'descuento' => 2, 'tasa' => 15]],
        ])->assertRedirect(route('compras.documentos.index'));
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
        $this->get(route('ventas.documentos.trazabilidad', $factura->id))->assertOk()->assertSee('NC-001');
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
