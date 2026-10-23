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


Route::match(['get', 'post'],'cie_10_3/search', 'cie_10\Cie_10_3Controller@search')->name('cie_10_3.search');
Route::match(['get', 'post'],'cie_10_4/search', 'cie_10\Cie_10_4Controller@search')->name('cie_10_4.search');
/*
esto me crea
cie_10_3.index
cie_10_3.create
cie_10_3.edit
cie_10_3.update
cie_10_3.store
*/
Route::resource('cie_10_3', 'cie_10\Cie_10_3Controller');
Route::resource('cie_10_4', 'cie_10\Cie_10_4Controller');







