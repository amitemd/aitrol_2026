<?php

use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ModuleCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('modules.index');
});

Route::get('/mapa-modulos', ModuleCatalogController::class)->name('modules.index');

Route::middleware(['auth', 'throttle:10,1'])
    ->prefix('administracion/despliegue')
    ->name('deployment.')
    ->group(function (): void {
        Route::get('/', [DeploymentController::class, 'index'])->name('index');
        Route::post('/ejecutar', [DeploymentController::class, 'execute'])->name('execute');
    });
