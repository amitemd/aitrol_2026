<?php

use Aitrol\Configuracion\Infrastructure\Http\EmpresaController;
use Aitrol\Configuracion\Infrastructure\Http\MantenimientoServidorController;
use Aitrol\Configuracion\Infrastructure\Http\MenuController;
use Aitrol\Configuracion\Infrastructure\Http\ParametrosEmpresaController;
use Aitrol\Configuracion\Infrastructure\Http\UsuarioController;
use Aitrol\Configuracion\Infrastructure\Http\AccesoController;
use Aitrol\Configuracion\Infrastructure\Http\UbicacionController;
use App\Http\Controllers\ContextoOperativoController;
use App\Http\Controllers\PersonaImportacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'empresa', 'can:administrar-personas'])
    ->prefix('configuraciones/personas/importaciones')->name('configuracion.personas.importaciones.')
    ->group(function (): void {
        Route::get('/', [PersonaImportacionController::class, 'index'])->name('index');
        Route::get('/estado', [PersonaImportacionController::class, 'estado'])->middleware('throttle:60,1')->name('estado');
        Route::post('/', [PersonaImportacionController::class, 'store'])->middleware('throttle:6,1')->name('store');
        Route::post('/token', [PersonaImportacionController::class, 'emitirToken'])->middleware('throttle:6,1')->name('token');
    });

Route::middleware(['web', 'auth', 'auth.session', 'empresa', 'can:administrar-servidor'])
    ->prefix('configuraciones/mantenimiento')->name('configuracion.mantenimiento.')
    ->group(function (): void {
        Route::get('/', [MantenimientoServidorController::class, 'index'])->name('index');
        Route::get('/estado', [MantenimientoServidorController::class, 'estado'])->name('estado');
        Route::post('/{accion}', [MantenimientoServidorController::class, 'ejecutar'])
            ->where('accion', '[a-z]+')->middleware('throttle:12,1')->name('ejecutar');
    });

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
        Route::get('/sucursales', [ContextoOperativoController::class, 'index'])->name('sucursales.index');
        Route::get('/sucursales/crear', [ContextoOperativoController::class, 'formulario'])->name('sucursales.create');
        Route::get('/sucursales/{sucursal}', [ContextoOperativoController::class, 'detalle'])->whereNumber('sucursal')->name('sucursales.show');
        Route::get('/sucursales/{sucursal}/editar', [ContextoOperativoController::class, 'formulario'])->whereNumber('sucursal')->name('sucursales.edit');
        Route::post('/sucursales', [ContextoOperativoController::class, 'guardar'])->name('sucursales.store');
        Route::put('/sucursales/{sucursal}', [ContextoOperativoController::class, 'guardar'])->whereNumber('sucursal')->name('sucursales.update');
        Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}', [UsuarioController::class, 'detalle'])->whereNumber('usuario')->name('usuarios.show');
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
    Route::get('/contexto-operativo', [ContextoOperativoController::class, 'inicio'])->name('contexto.index');
    Route::post('/contexto-operativo', [ContextoOperativoController::class, 'seleccionar'])->name('contexto.update');
    Route::get('/usuarios/{usuario}/foto', [UsuarioController::class, 'foto'])->whereNumber('usuario')->name('usuarios.foto');
    Route::get('/ubicaciones/opciones', [UbicacionController::class, 'opciones'])->name('ubicaciones.opciones');
    Route::get('/empresas', [EmpresaController::class, 'index'])->middleware('permiso:empresas,leer')->name('empresas.index');
    Route::get('/empresas/parametros', [ParametrosEmpresaController::class, 'index'])->middleware('permiso:empresas,leer')->name('empresas.parametros');
    Route::get('/empresas/parametros/crear', [ParametrosEmpresaController::class, 'create'])->middleware('permiso:empresas,actualizar')->name('empresas.parametros.create');
    Route::post('/empresas/parametros', [ParametrosEmpresaController::class, 'guardar'])->middleware('permiso:empresas,actualizar')->name('empresas.parametros.store');
    Route::get('/empresas/parametros/{parametro}/editar', [ParametrosEmpresaController::class, 'editar'])->whereNumber('parametro')
        ->middleware('permiso:empresas,actualizar')->name('empresas.parametros.edit');
    Route::put('/empresas/parametros/{parametro}', [ParametrosEmpresaController::class, 'guardar'])->whereNumber('parametro')
        ->middleware('permiso:empresas,actualizar')->name('empresas.parametros.update');
    Route::get('/empresas/{empresa}/logo', [EmpresaController::class, 'logo'])->whereNumber('empresa')->middleware('permiso:empresas,leer')->name('empresas.logo');
    Route::get('/empresas/{empresa}', [EmpresaController::class, 'detalle'])->whereNumber('empresa')->middleware('permiso:empresas,leer')->name('empresas.show');
    Route::get('/empresas/{empresa}/editar', [EmpresaController::class, 'edit'])->whereNumber('empresa')->middleware('permiso:empresas,actualizar')->name('empresas.edit');
    Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->whereNumber('empresa')->middleware('permiso:empresas,actualizar')->name('empresas.update');
});
