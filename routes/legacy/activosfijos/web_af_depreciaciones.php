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

//Documento Factura

Route::resource('afDepreciacion', 'activosfijos\DepreciacionController');
Route::match(['get', 'post'],'activojifos/depreciacion/buscar', 'activosfijos\DepreciacionController@buscar')->name('activosfijos.depreciacion.buscar');
Route::match(['get','post'],'activofijo/depreciacion/pdf_depreciacionesmen', 'activosfijos\DepreciacionController@pdf_depreciacionesmen')->name('activofjo.depreciacion.pdf_depreciacionesmen');

Route::match(['get', 'post'],'activofijo/store/depreciacion', 'activosfijos\DepreciacionController@store_depreciacion')->name('activosfijos.depreciacion.store_depreciacion');

Route::match(['get', 'post'],'activofijo/saldo_inicial_dep/{nombre}', 'activosfijos\DepreciacionController@saldo_inicial_dep')->name('activosfijos.depreciacion.saldo_inicial_dep');

Route::match(['get', 'post'],'activofijo/dep_acumulada/{nombre}', 'activosfijos\DepreciacionController@dep_acumulada')->name('activosfijos.depreciacion.dep_acumulada');
Route::match(['get', 'post'],'activofijo/index_depreciaciones', 'activosfijos\DepreciacionController@index_depreciaciones')->name('activosfijos.depreciacion.index_depreciaciones');
route::match(['get', 'post'],'activofijo/ver_depreciacion/{id}', 'activosfijos\DepreciacionController@ver_depreciacion')->name('activofjo.depreciacion.ver_depreciacion');

Route::match(['get', 'post'], 'activofijo/anular_depreciacion/{id}', 'activosfijos\DepreciacionController@anular_depreciacion')->name('activofjo.depreciacion.anular_depreciacion');

