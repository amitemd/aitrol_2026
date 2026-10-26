<?php

use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ModuleCatalogController;
use App\Http\Controllers\NewSystem\AuthController as NewSystemAuthController;
use App\Http\Controllers\NewSystem\CompanySelectionController as NewSystemCompanySelectionController;
use App\Http\Controllers\NewSystem\DashboardController as NewSystemDashboardController;
use App\Http\Controllers\NewSystem\UserController as NewSystemUserController;
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

Route::prefix('nuevo')->name('new-system.')->group(function (): void {
    Route::get('/login', [NewSystemAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [NewSystemAuthController::class, 'login'])
        ->middleware('throttle:new-system-login')
        ->name('login.store');

    Route::middleware(['auth', 'new.context'])->group(function (): void {
        Route::get('/', NewSystemDashboardController::class)->name('dashboard');
        Route::post('/salir', [NewSystemAuthController::class, 'logout'])->name('logout');

        Route::get('/empresas', [NewSystemCompanySelectionController::class, 'index'])
            ->name('companies.index');
        Route::post('/empresas/seleccionar', [NewSystemCompanySelectionController::class, 'store'])
            ->name('companies.store');

        Route::resource('usuarios', NewSystemUserController::class)
            ->parameters(['usuarios' => 'usuario'])
            ->names('users')
            ->except(['show', 'destroy'])
            ->middleware('can:manage-new-system-users');
    });
});
