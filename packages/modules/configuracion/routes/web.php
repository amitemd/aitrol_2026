<?php

use Aitrol\Configuracion\Infrastructure\Http\EmpresaController;
use Aitrol\Configuracion\Infrastructure\Http\MenuController;
use Aitrol\Configuracion\Infrastructure\Http\UsuarioController;
use Aitrol\Configuracion\Infrastructure\Http\AccesoController;
use Aitrol\Configuracion\Infrastructure\Http\UbicacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'empresa', 'can:administrar-menu'])
    ->prefix('configuraciones/menu')->name('configuracion.menu.')
    ->group(function (): void {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/{menu}', [MenuController::class, 'update'])->whereNumber('menu')->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->whereNumber('menu')->name('destroy');
    });

Route::middleware(['web', 'auth', 'auth.session', 'empresa', 'can:administrar-configuracion'])
    ->prefix('configuraciones')->name('configuracion.')
    ->group(function (): void {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->whereNumber('usuario')->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->whereNumber('usuario')->name('usuarios.update');
        Route::get('/roles', [AccesoController::class, 'index'])->name('roles.index');
        Route::post('/roles', [AccesoController::class, 'guardarRol'])->name('roles.store');
        Route::put('/roles/{rol}', [AccesoController::class, 'guardarRol'])->whereNumber('rol')->name('roles.update');
        Route::delete('/roles/{rol}', [AccesoController::class, 'eliminarRol'])->whereNumber('rol')->name('roles.destroy');
        Route::get('/usuarios/{usuario}/permisos', [AccesoController::class, 'permisosUsuario'])->whereNumber('usuario')->name('usuarios.permisos');
        Route::put('/usuarios/{usuario}/permisos', [AccesoController::class, 'guardarUsuario'])->whereNumber('usuario')->name('usuarios.permisos.update');
        Route::get('/ubicaciones', [UbicacionController::class, 'index'])->middleware('can:administrar-menu')->name('ubicaciones.index');
        Route::post('/ubicaciones', [UbicacionController::class, 'store'])->middleware('can:administrar-menu')->name('ubicaciones.store');
    });

Route::middleware(['web', 'auth', 'auth.session', 'empresa'])->prefix('configuraciones')->name('configuracion.')->group(function (): void {
    Route::get('/ubicaciones/opciones', [UbicacionController::class, 'opciones'])->name('ubicaciones.opciones');
    Route::get('/empresas', [EmpresaController::class, 'index'])->middleware('permiso:empresas,leer')->name('empresas.index');
    Route::get('/empresas/{empresa}/editar', [EmpresaController::class, 'edit'])->whereNumber('empresa')->middleware('permiso:empresas,actualizar')->name('empresas.edit');
    Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->whereNumber('empresa')->middleware('permiso:empresas,actualizar')->name('empresas.update');
});
