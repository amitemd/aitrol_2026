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
//BALANCE GENERALs
Route::match(['get', 'post'], 'contable/contabilidad/ats', 'contable\AtsController@index')->name('ats.index');

Route::match(['get', 'post'], 'contable/contabilidad/ats/show', 'contable\AtsController@show')->name('ats.show');

Route::match(['get', 'post'], 'contable/contabilidad/ats/store', 'contable\AtsController@store')->name('ats.store');

Route::match(['get', 'post'], 'contable/contabilidad/docelectronicos', 'contable\AtsController@docelectronicos')->name('docelectronicos.index');

Route::match(['get', 'post'], 'contable/modal/proveedor', 'contable\AtsController@modal_proveedor')->name('proveedor.modal_proveedor');

Route::match(['get', 'post'], 'contable/proveedor/store', 'contable\AtsController@store_proveedor')->name('proveedor.store_proveedor');
Route::match(['get', 'post'], 'contable/contabilidad/guardarArchivo', 'contable\AtsController@guardarArchivo')->name('docelectronicos.guardarArchivo');
Route::match(['get', 'post'], 'contable/contabilidad/guardarRegistros', 'contable\AtsController@guardarRegistros')->name('docelectronicos.guardarRegistros');
Route::match(['get', 'post'], 'contable/contabilidad/importarArchivoXls', 'contable\AtsController@importarArchivoXls')->name('docelectronicos.importarArchivoXls');

Route::match(['get', 'post'], 'contable/contabilidad/traedocumento', 'contable\AtsController@traeDocumento')->name('docelectronicos.traedocumento');

Route::match(['get', 'post'], 'tributacion/formulario_107', 'tributacion\TributacionController@formulario_107')->name('formulario_107');

//Route::match(['get', 'post'], 'tributacion/buscar_empreados_107', 'tributacion\TributacionController@buscar_empreados_107')->name('buscar_empreados_107');

Route::match(['get', 'post'], 'tributacion/{opcion}', 'tributacion\TributacionController@index');


Route::match(['get', 'post'], 'contable/proveedor/guardar/ajax', 'contable\AtsController@guardar_store')->name('proveedor.guardar_store');

Route::get('contable/tributacion/descargar_xml', 'contable\AtsController@descargar_xml')->name('tributacion.descargar_xml');
