<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::match(['get', 'post'], 'contable/contabilidad/libro/diario', 'contable\LibroDiarioController@index')->name('librodiario.index');
Route::get('contable/contabilidad/contra/revisar', 'contable\LibroDiarioController@checkpass')->name('librodiario.checkpass');
Route::get('contable/contra/existencia/modulo', 'contable\LibroDiarioController@existenciaModulo')->name('librodiario.revisar_modulo');
Route::get('contable/contabilidad/libro/revision/{id}', 'contable\LibroDiarioController@revisar')->name('librodiario.revisar');
Route::match(['get', 'post'], 'contable/contabilidad/libro/edit/{id}', 'contable\LibroDiarioController@edit')->name('librodiario.edit');
Route::match(['get', 'post'], 'contable/contabilidad/libro/actualizar/asiento', 'contable\LibroDiarioController@update')->name('librodiarios.update');
Route::get('contable/contabilidad/libro/diario/buscador', 'contable\LibroDiarioController@buscador_secuencia')->name('librodiario.buscador');
Route::get('contable/contabilidad/libro/crear/asiento', 'contable\LibroDiarioController@crear')->name('librodiario.crear');
Route::post('contable/contabilidad/libro/guardar/asiento', 'contable\LibroDiarioController@store')->name('librodiario.store');

Route::match(['get', 'post'], 'contable/contabilidad/libro/problem/asiento', 'contable\LibroDiarioController@problemasAsientos')->name('librodiario.problemasAsientos');

Route::match(['get', 'post'], 'contable/contabilidad/libro/guardar/asiento/cierre/anio', 'contable\LibroDiarioController@store_cierre')->name('librodiario.store_cierre');

Route::post('contable/contabilidad/libro/buscar/asiento', 'contable\LibroDiarioController@buscar')->name('librodiario.buscar_asiento');
Route::match(['get', 'post'], 'contable/descuadrados', 'contable\LibroDiarioController@descuadrados')->name('librodiario.descuadrados');
Route::get('contable/contabilidad/libro/buscar/proveedor', 'contable\LibroDiarioController@buscar_proveedor')->name('librodiario.buscar_proveedor');
Route::match(['get', 'post'], 'contable/contabilidad/libro/buscar/buscar', 'contable\LibroDiarioController@search')->name('libro_contable_search');
Route::get('contable/contabilidad/libro/buscar/fecha', 'contable\LibroDiarioController@buscador_fecha')->name('librodiario.buscador_fecha');
Route::get('contable/contabilidad/libro/diario/buscar_empresa', 'contable\LibroDiarioController@buscar_empresa')->name('librodiario.buscar_empresa');
Route::get('contable/contabilidad/libro/diario/anular/asiento/{id}', 'contable\LibroDiarioController@anular_asiento')->name('librodiario.anular_asiento');
// Route::get('contable/contabilidad/libro/diario/anularasiento_edit/{id}', 'contable\LibroDiarioController@anular_asiento_edit')->name('librodiario.anular_asiento_edit');

// ct globales
Route::match(['get', 'post'], 'contable/contabilidad/configuraciones/globales', 'contable\GlobalesController@index')->name('ctglobales.index');
Route::get('contable/contabilidad/configuraciones/globales/{id}', 'contable\GlobalesController@edit')->name('ctglobales.edit');

Route::match(['get', 'post'], 'contable/contabilidad/configuraciones/globales/edit', 'contable\GlobalesController@editCuenta')->name('diario.edit_cuentas');

Route::match(['get', 'post'], 'contable/contabilidad/final_cierre/revisar', 'contable\LibroDiarioController@cierre')->name('librodiario.cierre');

// apps ieced
Route::get('apps/Dashboard', 'servicios\ServiciosIecedController@dashboard')->name('dashboard.apps');
Route::match(['get', 'post'], 'apps/charlas', 'servicios\AppsController@index')->name('charlasapps.index');
Route::get('apps/charlas/create', 'servicios\AppsController@create')->name('charlasapps.create');
Route::get('apps/charlas/edit/{id}', 'servicios\AppsController@edit')->name('charlasapps.edit');
Route::post('apps/charlas/store', 'servicios\AppsController@store')->name('charlasapps.store');
Route::post('apps/charlas/update', 'servicios\AppsController@update')->name('charlasapps.update');
Route::match(['get', 'post'], 'apps/banners', 'servicios\AppsController@index_banners')->name('bannersapps.index');
Route::get('apps/banners/create', 'servicios\AppsController@create_banners')->name('bannersapps.create');
Route::get('apps/banners/edit/{id}', 'servicios\AppsController@edit_banners')->name('bannersapps.edit');
Route::post('apps/banners/store', 'servicios\AppsController@store_banners')->name('bannersapps.store');
Route::post('apps/banners/update', 'servicios\AppsController@update_banners')->name('bannersapps.update');
// apps membresias
Route::match(['get', 'post'], 'apps/membresias', 'servicios\AppsController@index_membresias')->name('membresiasapps.index');
Route::get('apps/membresias/create', 'servicios\AppsController@create_membresias')->name('membresiasapps.create');
Route::get('apps/membresias/edit/{id}', 'servicios\AppsController@edit_membresias')->name('membresiasapps.edit');
Route::post('apps/membresias/store', 'servicios\AppsController@store_membresias')->name('membresiasapps.store');
Route::post('apps/membresias/update', 'servicios\AppsController@update_membresias')->name('membresiasapps.update');

// apps membresias
Route::match(['get', 'post'], 'apps/solicitudes', 'servicios\AppsController@index_solicitudes')->name('solicitudes_apps.index');
Route::get('apps/solicitudes/create', 'servicios\AppsController@create_solicitudes')->name('solicitudes_apps.create');
Route::get('apps/solicitudes/edit/{id}', 'servicios\AppsController@show_solicitudes')->name('solicitudes_apps.edit');
Route::post('apps/solicitudes/store', 'servicios\AppsController@store_solicitudes')->name('solicitudes_apps.store');
Route::post('apps/solicitudes/update', 'servicios\AppsController@update_solicitudes')->name('solicitudes_apps.update');

// apps informacion
Route::match(['get', 'post'], 'apps/informacion', 'servicios\AppsController@index_informacion')->name('apps_informacion.index');
Route::get('apps/informacion/create', 'servicios\AppsController@create_informacion')->name('apps_informacion.create');
Route::get('apps/informacion/edit/{id}', 'servicios\AppsController@edit_informacion')->name('apps_informacion.edit');
Route::post('apps/informacion/store', 'servicios\AppsController@store_informacion')->name('apps_informacion.store');
Route::post('apps/informacion/update', 'servicios\AppsController@update_informacion')->name('apps_informacion.update');
// apps agenda
Route::match(['get', 'post'], 'apps/plan_familiar', 'servicios\AppsController@index_familiar')->name('apps_familiar.index');
Route::get('apps/plan_familiar/show/{id}', 'servicios\AppsController@show_familiar')->name('apps_familiar.show');
Route::post('apps/plan_familiar/update', 'servicios\AppsController@update_familiar')->name('apps_familiar.update');

Route::match(['get', 'post'], 'apps/agenda', 'servicios\AppsController@index_agenda')->name('agendaapps.index');

Route::match(['get', 'post'], 'contable/contabilidad/asientos/noExiste', 'contable\LibroDiarioController@AsientoNoExiste')->name('librodiario.AsientoNoExiste');

Route::match(['get', 'post'], 'contable/contabilidad/asientos/planConfiguraciones', 'contable\LibroDiarioController@planConfiguraciones')->name('librodiario.planConfiguraciones');
Route::match(['get', 'post'], 'contable/contabilidad/asientos/Cuentas/BusquedaCuenta', 'contable\LibroDiarioController@buscarPlanCuentasEmpresa')->name('diario.LibroDiario.buscarPlanCuentasEmpresa');

// Cuentas Configurar
Route::match(['get', 'post'], 'contable/diario/cuentasconfigurar', 'contable\LibroDiarioController@Actualizacion_Cuenta')->name('LibroDiario.ActualiacionCuenta');

Route::match(['get', 'post'], 'contable/masivo/factura/activo/fijo/{id_empresa}', 'ImportacionesController@masivoActivoFijo')->name('importaciones.masivoActivoFijo');

Route::get('contable/index_dobleAnulacion', 'contable\LibroDiarioController@index_dobleAnulacion')->name('diario.index_dobleAnulacion');

Route::resource('contable/cierre/mensual', 'contable\CierreController', [
    'names' => [
        'index' => 'cierre.mensual.index',
        'create' => 'cierre.mensual.create',
        'store' => 'cierre.mensual.store',
        'show' => 'cierre.mensual.show',
        'edit' => 'cierre.mensual.edit',
        'update' => 'cierre.mensual.update',
        'destroy' => 'cierre.mensual.destroy',
    ],
])->only(['index', 'create', 'store', 'edit', 'update']);
