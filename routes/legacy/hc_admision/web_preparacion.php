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




Route::get('historiaclinica/preparacion/{ag}/{url}', 'hc_admision\PreparacionController@mostrar')->name('preparacion.mostrar');











