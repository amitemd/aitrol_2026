<?php

use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ModuleCatalogController;
use App\Http\Controllers\NewSystem\AuthController as NewSystemAuthController;
use App\Http\Controllers\NewSystem\CompanySelectionController as NewSystemCompanySelectionController;
use App\Http\Controllers\NewSystem\DashboardController as NewSystemDashboardController;
use App\Http\Controllers\NewSystem\PasswordResetController as NewSystemPasswordResetController;
use App\Http\Controllers\NewSystem\UserController as NewSystemUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', NewSystemDashboardController::class)
    ->middleware(['new.context', 'secure.auth.response'])
    ->name('new-system.dashboard');

Route::post('/acceder', [NewSystemAuthController::class, 'login'])
    ->middleware(['guest', 'secure.auth.response'])
    ->name('new-system.login.store');

Route::middleware(['guest', 'secure.auth.response'])->group(function (): void {
    Route::get('/recuperar-contrasena', [NewSystemPasswordResetController::class, 'requestForm'])
        ->name('new-system.password.request');
    Route::post('/recuperar-contrasena', [NewSystemPasswordResetController::class, 'sendLink'])
        ->middleware('throttle:new-system-password-reset')
        ->name('new-system.password.email');
    Route::get('/restablecer-contrasena/{token}', [NewSystemPasswordResetController::class, 'resetForm'])
        ->name('new-system.password.reset');
    Route::post('/restablecer-contrasena', [NewSystemPasswordResetController::class, 'reset'])
        ->name('new-system.password.update');
});

Route::redirect('/nuevo', '/');
Route::redirect('/nuevo/login', '/');

Route::get('/mapa-modulos', ModuleCatalogController::class)->name('modules.index');

Route::middleware(['auth', 'throttle:10,1'])
    ->prefix('administracion/despliegue')
    ->name('deployment.')
    ->group(function (): void {
        Route::get('/', [DeploymentController::class, 'index'])->name('index');
        Route::post('/ejecutar', [DeploymentController::class, 'execute'])->name('execute');
    });

Route::middleware(['auth', 'new.context'])->name('new-system.')->group(function (): void {
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
