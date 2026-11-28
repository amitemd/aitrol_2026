<?php

use Aitrol\Comercial\Infrastructure\Http\ComercialController;
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

                if ($modulo === 'compras' && ! $api) {
                    Route::get('retenciones/catalogo', [ComercialController::class, 'catalogoRetenciones'])
                        ->middleware($permisos('retenciones', 'escribir'))->name('retenciones.catalogo');
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
                if ($modulo === 'ventas' && ! $api) {
                    Route::get('documentos/{registro}/cobro', [ComercialController::class, 'formularioCobro'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), 'permiso:caja-bancos.movimientos.index,escribir'])
                        ->name('documentos.cobro.create');
                    Route::post('documentos/{registro}/cobro', [ComercialController::class, 'registrarCobro'])
                        ->whereNumber('registro')->middleware([...$permisos('documentos', 'leer'), 'permiso:caja-bancos.movimientos.index,escribir'])
                        ->name('documentos.cobro.store');
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
