<?php

use Aitrol\Configuracion\Infrastructure\Http\EmpresaController;
use Aitrol\Configuracion\Infrastructure\Http\MenuController;
use Aitrol\Configuracion\Infrastructure\Http\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'can:administrar-menu'])
    ->prefix('configuraciones/menu')->name('configuracion.menu.')
    ->group(function (): void {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/{menu}', [MenuController::class, 'update'])->whereNumber('menu')->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->whereNumber('menu')->name('destroy');
    });

Route::middleware(['web', 'auth', 'auth.session', 'can:administrar-configuracion'])
    ->prefix('configuraciones')->name('configuracion.')
    ->group(function (): void {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->whereNumber('usuario')->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->whereNumber('usuario')->name('usuarios.update');
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
    });
