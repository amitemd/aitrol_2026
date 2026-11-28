<?php

use Aitrol\Contabilidad\Infrastructure\Http\ContableController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

foreach ([false, true] as $api) {
    Route::prefix(($api ? 'api/v1/' : '').'contabilidad')->name(($api ? 'api.v1.' : '').'contabilidad.')
        ->middleware($api ? ['api', 'auth:sanctum', 'empresa', 'throttle:60,1'] : ['web', 'auth', 'auth.session', 'empresa'])
        ->group(function () use ($api): void {
            $permiso = fn (string $opcion, string $accion): array => [
                'permiso:contabilidad.'.$opcion.','.$accion,
                ...($api ? [CheckAbilities::class.':contabilidad:'.($accion === 'leer' ? 'leer' : 'escribir')] : []),
            ];
            foreach (['asientos', 'cuentas', 'periodos'] as $seccion) {
                Route::get($seccion, [ContableController::class, 'index'])->defaults('seccion', $seccion)
                    ->middleware($permiso($seccion.'.index', 'leer'))->name($seccion.'.index');
                Route::post($seccion, [ContableController::class, 'store'])->defaults('seccion', $seccion)
                    ->middleware($permiso($seccion.'.index', 'escribir'))->name($seccion.'.store');
                Route::get($seccion.'/{registro}', [ContableController::class, 'show'])->defaults('seccion', $seccion)->where('registro', '[1-9][0-9]{0,17}')
                    ->middleware($permiso($seccion.'.index', 'leer'))->name($seccion.'.show');
                $accion = ['asientos' => 'revertir', 'cuentas' => 'editar', 'periodos' => 'cerrar'][$seccion];
                Route::match([$accion === 'editar' ? 'PUT' : 'POST'], $seccion.'/{registro}'.($accion === 'editar' ? '' : '/'.$accion), [ContableController::class, 'accion'])
                    ->defaults('seccion', $seccion)->defaults('accion', $accion)->where('registro', '[1-9][0-9]{0,17}')
                    ->middleware($permiso($seccion.'.index', 'actualizar'))->name($seccion.'.'.$accion);
                if (! $api) {
                    Route::get($seccion.'/crear', [ContableController::class, 'formulario'])->defaults('seccion', $seccion)
                        ->middleware($permiso($seccion.'.index', 'escribir'))->name($seccion.'.create');
                    Route::get($seccion.'/{registro}/'.$accion, [ContableController::class, 'formulario'])->where('registro', '[1-9][0-9]{0,17}')
                        ->defaults('seccion', $seccion)->defaults('accion', $accion)->middleware($permiso($seccion.'.index', 'actualizar'))->name($seccion.'.'.$accion.'.formulario');
                    Route::get($seccion.'/opciones/cuentas', [ContableController::class, 'opciones'])->defaults('clase_opcion', $seccion === 'cuentas' ? 'grupos' : 'asientos')
                        ->middleware($permiso($seccion.'.index', 'escribir'))->name($seccion.'.opciones');
                }
            }
            if (! $api) {
                Route::get('cuentas/codigo-auxiliar', [ContableController::class, 'codigoAuxiliar'])
                    ->middleware($permiso('cuentas.index', 'escribir'))->name('cuentas.codigo-auxiliar');
            }
            Route::get('integraciones', [ContableController::class, 'integracion'])
                ->middleware($permiso('integraciones.index', 'leer'))->name('integraciones.index');
            Route::put('integraciones', [ContableController::class, 'actualizarIntegracion'])
                ->middleware($permiso('integraciones.index', 'actualizar'))->name('integraciones.update');
            if (! $api) {
                Route::get('integraciones/opciones/cuentas', [ContableController::class, 'opciones'])->defaults('clase_opcion', 'integracion')
                    ->middleware($permiso('integraciones.index', 'actualizar'))->name('integraciones.opciones');
                Route::get('reportes/opciones/cuentas', [ContableController::class, 'opciones'])->defaults('clase_opcion', 'reportes')
                    ->middleware($permiso('reportes.mayorizacion', 'leer'))->name('reportes.opciones');
            }
            foreach (array_keys(config('contabilidad.reportes')) as $reporte) {
                Route::get('reportes/'.$reporte, [ContableController::class, 'reporte'])->defaults('reporte', $reporte)
                    ->middleware($permiso('reportes.'.$reporte, 'leer'))->name('reportes.'.$reporte);
                Route::get('reportes/'.$reporte.'/exportar', [ContableController::class, 'exportar'])->defaults('reporte', $reporte)
                    ->middleware($permiso('reportes.'.$reporte, 'leer'))->name('reportes.'.$reporte.'.exportar');
            }
        });
}
