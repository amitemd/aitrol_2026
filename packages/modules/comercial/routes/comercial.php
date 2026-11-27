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
