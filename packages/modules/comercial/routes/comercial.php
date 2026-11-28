<?php

use Aitrol\Comercial\Infrastructure\Http\AprobacionOrdenCompraController;
use Aitrol\Comercial\Infrastructure\Http\ComercialController;
use Aitrol\Comercial\Infrastructure\Http\DevolucionCompraController;
use Aitrol\Comercial\Infrastructure\Http\FirmaLocalFacturaController;
use Aitrol\Comercial\Infrastructure\Http\RecepcionCompraController;
use Aitrol\Comercial\Infrastructure\Http\SolicitudCompraController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

foreach (config('comercial') as $modulo => $datos) {
    foreach ([false, true] as $api) {
        $prefijo = $api ? 'api/v1/' : '';
        $nombre = $api ? 'api.v1.' : '';
        $middleware = $api ? ['api', 'auth:sanctum', 'empresa', 'throttle:60,1'] : ['web', 'auth', 'auth.session', 'empresa'];
        Route::prefix($prefijo.$modulo)->name($nombre.$modulo.'.')->middleware($middleware)
            ->group(function () use ($modulo, $datos, $api): void {
                $permisos = fn (string $seccion, string $accion): array => array_merge(
                    ['permiso:'.$modulo.'.'.$seccion.'.index,'.$accion],
                    $api ? [CheckAbilities::class.':'.$modulo.':'.($accion === 'leer' ? 'leer' : 'escribir')] : [],
                );

                if ($modulo === 'compras') {
                    $lecturaOrden = $permisos('ordenes-compra', 'leer');
                    $decidirCompra = [
                        ...$lecturaOrden, 'permiso:compras.aprobaciones.index,actualizar',
                        ...($api ? [CheckAbilities::class.':compras:aprobar'] : []),
                    ];
                    Route::get('presupuestos', [SolicitudCompraController::class, 'presupuestos'])
                        ->middleware($lecturaOrden)->name('presupuestos.index');
                    Route::put('presupuestos', [SolicitudCompraController::class, 'guardarPresupuesto'])
                        ->middleware(['can:administrar-configuracion',
                            ...($api ? [CheckAbilities::class.':compras:leer,compras:presupuestar'] : []),
                        ])->name('presupuestos.update');
                    if (! $api) {
                        Route::get('presupuestos/{presupuesto}/solicitar', [SolicitudCompraController::class, 'create'])
                            ->where('presupuesto', '[1-9][0-9]{0,17}')->middleware($permisos('ordenes-compra', 'escribir'))->name('solicitudes.create');
                    }
                    Route::get('solicitudes', [SolicitudCompraController::class, 'index'])->middleware($lecturaOrden)->name('solicitudes.index');
                    Route::post('solicitudes', [SolicitudCompraController::class, 'store'])
                        ->middleware([...$lecturaOrden, ...$permisos('ordenes-compra', 'escribir')])->name('solicitudes.store');
                    Route::prefix('solicitudes/{solicitud}')->name('solicitudes.')->where(['solicitud' => '[1-9][0-9]{0,17}'])
                        ->middleware($lecturaOrden)->group(function () use ($permisos, $decidirCompra): void {
                            Route::get('/', [SolicitudCompraController::class, 'show'])->name('show');
                            Route::post('decidir', [SolicitudCompraController::class, 'decidir'])->middleware($decidirCompra)->name('decidir');
                            Route::post('generar-orden', [SolicitudCompraController::class, 'convertir'])
                                ->middleware($permisos('ordenes-compra', 'escribir'))->name('convertir');
                            Route::post('cancelar', [SolicitudCompraController::class, 'cancelar'])
                                ->middleware($permisos('ordenes-compra', 'actualizar'))->name('cancelar');
                        });
                    Route::get('ordenes-compra/aprobaciones', [AprobacionOrdenCompraController::class, 'index'])
                        ->middleware($lecturaOrden)->name('aprobaciones.index');
                    Route::get('ordenes-compra/{orden}/aprobacion', [AprobacionOrdenCompraController::class, 'show'])
                        ->where('orden', '[1-9][0-9]{0,17}')->middleware($lecturaOrden)->name('aprobaciones.show');
                    Route::post('ordenes-compra/{orden}/aprobacion', [AprobacionOrdenCompraController::class, 'decidir'])
                        ->where('orden', '[1-9][0-9]{0,17}')->middleware($decidirCompra)->name('aprobaciones.decidir');
                    $gestionarPolitica = [
                        'can:administrar-configuracion',
                        ...($api ? [CheckAbilities::class.':compras:leer,compras:configurar-aprobaciones'] : []),
                    ];
                    Route::get('ordenes-compra/politica-aprobacion', [AprobacionOrdenCompraController::class, 'politica'])
                        ->middleware($gestionarPolitica)->name('aprobaciones.politica');
                    Route::put('ordenes-compra/politica-aprobacion', [AprobacionOrdenCompraController::class, 'guardarPolitica'])
                        ->middleware($gestionarPolitica)->name('aprobaciones.politica.update');
                    Route::get('reportes/pendientes', [DevolucionCompraController::class, 'pendientes'])
                        ->middleware($permisos('reportes', 'leer'))->name('reportes.pendientes');
                    Route::prefix('documentos/{registro}/devoluciones')->name('documentos.devoluciones.')
                        ->where(['registro' => '[1-9][0-9]{0,17}'])->middleware($permisos('documentos', 'leer'))
                        ->group(function () use ($permisos): void {
                            Route::get('/', [DevolucionCompraController::class, 'index'])->name('index');
                            Route::post('/', [DevolucionCompraController::class, 'store'])
                                ->middleware([...$permisos('notas-credito', 'escribir'), 'permiso:inventarios.movimientos.index,escribir'])->name('store');
                        });
                    Route::prefix('ordenes-compra/{orden}/recepciones')->name('ordenes-compra.recepciones.')
                        ->where(['orden' => '[1-9][0-9]{0,17}', 'recepcion' => '[1-9][0-9]{0,17}'])
                        ->middleware($permisos('ordenes-compra', 'leer'))->group(function () use ($permisos): void {
                            $movimientos = [...$permisos('ordenes-compra', 'actualizar'), 'permiso:inventarios.movimientos.index,escribir'];
                            Route::get('/', [RecepcionCompraController::class, 'index'])->name('index');
                            Route::post('/', [RecepcionCompraController::class, 'store'])->middleware($movimientos)->name('store');
                            Route::post('/facturar', [RecepcionCompraController::class, 'facturar'])
                                ->middleware($permisos('documentos', 'escribir'))->name('facturar');
                            Route::post('/{recepcion}/anular', [RecepcionCompraController::class, 'anular'])->middleware($movimientos)->name('anular');
                        });
                }

                if (! $api) {
                    Route::get('retenciones/catalogo', [ComercialController::class, 'catalogoRetenciones'])
                        ->middleware($permisos('retenciones', 'escribir'))->name('retenciones.catalogo');
                }
                if ($modulo === 'compras' && ! $api) {
                    Route::post('documentos/importar-xml', [ComercialController::class, 'importarCompra'])
                        ->middleware([...$permisos('documentos', 'escribir'), 'throttle:12,1'])->name('documentos.importar');
                    Route::get('documentos/{registro}/xml', [ComercialController::class, 'descargarXmlCompra'])
                        ->whereNumber('registro')->name('documentos.xml');
                    Route::get('documentos/{registro}/respaldo', [ComercialController::class, 'formularioRespaldoCompra'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), ...$permisos('documentos', 'actualizar')])
                        ->name('documentos.respaldo.create');
                    Route::post('documentos/{registro}/respaldo', [ComercialController::class, 'guardarRespaldoCompra'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), ...$permisos('documentos', 'actualizar'), 'throttle:12,1'])
                        ->name('documentos.respaldo.store');
                    Route::get('documentos/{registro}/respaldo/archivo', [ComercialController::class, 'archivoRespaldoCompra'])
                        ->whereNumber('registro')->middleware($permisos('documentos', 'leer'))->name('documentos.respaldo.archivo');
                    Route::post('retenciones/{registro}/emitir', [ComercialController::class, 'emitirRetencion'])
                        ->whereNumber('registro')->middleware([...$permisos('retenciones', 'leer'), ...$permisos('retenciones', 'actualizar'), 'throttle:12,1'])
                        ->name('retenciones.emitir');
                }

                if (! $api) {
                    Route::get('documentos/{registro}/trazabilidad', [ComercialController::class, 'trazabilidad'])
                        ->defaults('modulo', $modulo)->whereNumber('registro')
                        ->middleware($permisos('documentos', 'leer'))->name('documentos.trazabilidad');
                }
                if ($modulo === 'ventas') {
                    Route::post('documentos/{registro}/firma-local', [FirmaLocalFacturaController::class, 'store'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), ...$permisos('documentos', 'actualizar')])
                        ->name('documentos.firma-local.store');
                    Route::get('documentos/{registro}/firma-local/xml', [FirmaLocalFacturaController::class, 'download'])
                        ->whereNumber('registro')->middleware($permisos('documentos', 'leer'))->name('documentos.firma-local.xml');
                    Route::post('documentos/{registro}/firma-local/enviar', [FirmaLocalFacturaController::class, 'enviar'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), ...$permisos('documentos', 'actualizar'), 'throttle:6,1,sri-envio'])
                        ->name('documentos.firma-local.enviar');
                    Route::post('documentos/{registro}/cobro', [ComercialController::class, 'registrarCobro'])
                        ->whereNumber('registro')->middleware(['permiso:ventas.documentos.index,leer',
                            ...($api ? [CheckAbilities::class.':ventas:cobrar', 'permiso:ventas.documentos.index,actualizar'] : []),
                            'permiso:caja-bancos.movimientos.index,escribir'])->name('documentos.cobro.store');
                    if ($api) {
                        Route::get('documentos/{registro}/firma-local', [FirmaLocalFacturaController::class, 'show'])
                            ->whereNumber('registro')->middleware($permisos('documentos', 'leer'))->name('documentos.firma-local.show');
                    }
                }
                if ($modulo === 'ventas' && ! $api) {
                    Route::get('documentos/{registro}/cobro', [ComercialController::class, 'formularioCobro'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), 'permiso:caja-bancos.movimientos.index,escribir'])
                        ->name('documentos.cobro.create');
                    Route::get('retenciones/recibida', [ComercialController::class, 'formularioRetencionRecibida'])
                        ->middleware($permisos('retenciones', 'escribir'))->name('retenciones.recibida');
                    Route::post('retenciones/importar', [ComercialController::class, 'importarRetencion'])
                        ->middleware([...$permisos('retenciones', 'escribir'), 'throttle:12,1'])->name('retenciones.importar');
                    Route::get('documentos/{registro}/sri', [ComercialController::class, 'logSri'])
                        ->whereNumber('registro')->name('documentos.sri.log');
                    Route::post('documentos/{registro}/sri/consultar', [ComercialController::class, 'consultarSri'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'actualizar'), 'throttle:12,1'])
                        ->name('documentos.sri.consultar');
                    Route::get('documentos/{registro}/sri/{formato}', [ComercialController::class, 'descargarSri'])
                        ->whereNumber('registro')->whereIn('formato', ['pdf', 'xml'])->name('documentos.sri.descargar');
                    Route::post('documentos/{registro}/contabilizar', [ComercialController::class, 'contabilizarVenta'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'actualizar'), 'permiso:contabilidad.asientos.index,escribir'])
                        ->name('documentos.contabilizar');
                    Route::get('documentos/cliente/{cliente}/contacto', [ComercialController::class, 'contactoFactura'])
                        ->whereNumber('cliente')->middleware($permisos('documentos', 'escribir'))->name('documentos.contacto');
                    Route::patch('documentos/cliente/{cliente}/contacto', [ComercialController::class, 'actualizarContactoFactura'])
                        ->whereNumber('cliente')->middleware([...$permisos('documentos', 'escribir'), 'permiso:clientes.lista,actualizar'])
                        ->name('documentos.contacto.actualizar');
                    Route::get('facturas-recurrentes', [ComercialController::class, 'recurrentes'])
                        ->middleware($permisos('documentos', 'escribir'))->name('facturas-recurrentes.index');
                    Route::post('facturas-recurrentes/{recurrencia}/estado', [ComercialController::class, 'estadoRecurrencia'])
                        ->whereNumber('recurrencia')->middleware($permisos('documentos', 'actualizar'))->name('facturas-recurrentes.estado');
                }
                if (! $api) {
                    Route::get('retenciones/{registro}/xml', [ComercialController::class, 'descargarRetencion'])
                        ->defaults('modulo', $modulo)->whereNumber('registro')
                        ->middleware($permisos('retenciones', 'leer'))->name('retenciones.xml');
                }

                foreach ($datos['secciones'] as $seccion => [, , $alta]) {
                    Route::get($seccion, [ComercialController::class, 'index'])
                        ->defaults('modulo', $modulo)->defaults('seccion', $seccion)
                        ->middleware($permisos($seccion, 'leer'))->name($seccion.'.index');
                    if ($seccion === 'reportes') {
                        Route::get('reportes/exportar', [ComercialController::class, 'exportar'])
                            ->defaults('modulo', $modulo)->defaults('seccion', $seccion)
                            ->middleware($permisos($seccion, 'leer'))->name('reportes.exportar');

                        continue;
                    }
                    if ($alta !== null) {
                        Route::post($seccion, [ComercialController::class, 'store'])
                            ->defaults('modulo', $modulo)->defaults('seccion', $seccion)
                            ->middleware($permisos($seccion, 'escribir'))->name($seccion.'.store');
                        Route::post($seccion.'/{registro}/anular', [ComercialController::class, 'anular'])
                            ->defaults('modulo', $modulo)->defaults('seccion', $seccion)->where('registro', '[1-9][0-9]{0,17}')
                            ->middleware($permisos($seccion, 'actualizar'))->name($seccion.'.anular');

                        if (! $api) {
                            Route::get($seccion.'/crear', [ComercialController::class, 'crear'])
                                ->defaults('modulo', $modulo)->defaults('seccion', $seccion)
                                ->middleware($permisos($seccion, 'escribir'))->name($seccion.'.create');
                            Route::get($seccion.'/{registro}/anular', [ComercialController::class, 'formularioAnulacion'])
                                ->defaults('modulo', $modulo)->defaults('seccion', $seccion)->where('registro', '[1-9][0-9]{0,17}')
                                ->middleware($permisos($seccion, 'actualizar'))->name($seccion.'.confirmar');
                            Route::get($seccion.'/opciones/{catalogo}', [ComercialController::class, 'opciones'])
                                ->defaults('modulo', $modulo)->defaults('seccion', $seccion)->whereIn('catalogo', ['terceros', 'origenes', 'productos', 'bodegas'])
                                ->middleware($permisos($seccion, 'escribir'))->name($seccion.'.opciones');
                        }
                    }
                    Route::get($seccion.'/{registro}', [ComercialController::class, 'show'])
                        ->defaults('modulo', $modulo)->defaults('seccion', $seccion)->where('registro', '[1-9][0-9]{0,17}')
                        ->middleware($permisos($seccion, 'leer'))->name($seccion.'.show');
                }
            });
    }
}
