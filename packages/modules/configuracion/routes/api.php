<?php

use Aitrol\Configuracion\Infrastructure\Http\UsuarioController;
use App\Http\Controllers\PersonaApiController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::post('api/v1/personas/consultar', PersonaApiController::class)
    ->middleware(['api', 'auth:sanctum', 'empresa', 'throttle:60,1', 'can:consultar-personas'])
    ->name('api.v1.personas.consultar');

Route::prefix('api/v1/usuarios')->name('api.v1.usuarios.')
    ->middleware(['api', 'auth:sanctum', 'empresa', 'can:integrar-usuarios', 'throttle:60,1'])
    ->group(function (): void {
        Route::get('/', [UsuarioController::class, 'index'])
            ->middleware(CheckAbilities::class.':usuarios:leer')->name('index');
        Route::get('/{usuario}', [UsuarioController::class, 'show'])->where('usuario', '[1-9][0-9]{0,17}')
            ->middleware(CheckAbilities::class.':usuarios:leer')->name('show');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->where('usuario', '[1-9][0-9]{0,17}')
            ->middleware(CheckAbilities::class.':usuarios:escribir')->name('update');
    });
