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

//consulta máster

Route::match(['get', 'post'], 'historiaclinica/full_control', 'hc_admision\FullcontrolController@index')->name('historia_clinica.fullcontrol');
//Taylor//
//Route::get('contable\Porcentaje_renta\index', 'PorcentajeRentaController.phper@index');
//Route::match(['get', 'post'], 'contable/acreedores/informes/cartera/pagar', 'contable\InformesAcreedorController@index_cartera')->name('carterap.index');
Route::match(['get', 'post'], 'contable/Porcentaje_renta/index', 'contable\PorcentajeRentaController@index')->name('Porcentaje.index');
Route::match(['get', 'post'], 'contable/Porcentaje_renta/create', 'contable\PorcentajeRentaController@create')->name('Porcentaje.create');
Route::match(['get', 'post'], 'contable/Porcentaje_renta/edit/{id}', 'contable\PorcentajeRentaController@edit')->name('Porcentaje.edit');
//Route::get('contable/tipo/porcentaje_imp_renta/editar/{id}', 'contable\Porcentaje_Impuesto_RentaController@editar')->name('porcentaje_imp_renta.editar');

Route::match(['get', 'post'], 'contable/Porcentaje_renta/guardar', 'contable\PorcentajeRentaController@guardar')->name('Porcentaje.guardar');
Route::match(['get', 'post'], 'contable/Porcentaje_renta/buscar', 'contable\PorcentajeRentaController@buscar')->name('Porcentaje.buscar');
Route::match(['get', 'post'], 'contable/Porcentaje_renta/actualizar', 'contable\PorcentajeRentaController@actualizar')->name('Porcentaje.actualizar');