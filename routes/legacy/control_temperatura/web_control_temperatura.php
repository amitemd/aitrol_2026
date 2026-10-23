<?php


Route::get('control_temperatura/index_contemp', 'control_temperatura\ControlNeveraController@index_contemp')->name('control_temperatura.index_contemp');
Route::get('control_temperatura/index', 'control_temperatura\ControlNeveraController@index')->name('control_temperatura.index');
Route::post('control_temperatura/eleccion/tipo', 'control_temperatura\ControlNeveraController@eleccion_tipo_control')->name('eleccion_tipo_control');
Route::match(['get', 'post'],'control_temperatura/eleccion2/tipo2', 'control_temperatura\ControlNeveraController@eleccion_tipo2_control')->name('eleccion-tipo2_control');
Route::get('control_temperatura/areas', 'control_temperatura\ControlNeveraController@areas')->name('control_temperatura.areas');
Route::match(['get', 'post'], 'control_temperatura/create', 'control_temperatura\ControlNeveraController@create')->name('control_temperatura.create');
Route::match(['get', 'post'], 'control_temperatura/store', 'control_temperatura\ControlNeveraController@guardar')->name('control_temperatura.store');

Route::post('control_temperatura/update/nevera', 'control_temperatura\ControlNeveraController@update_nevera')->name('control_temperatura.update');

Route::match(['get', 'post'],'control_temperatura/buscador', 'control_temperatura\ControlNeveraController@buscador')->name('control_temperatura.buscador');



Route::get('excel/mantenimiento/temperatura', 'control_temperatura\ControlNeveraController@excel_temperatura')->name('excel_temperatura.temp');


//index crear para labs


Route::get('control_temperatura/index_contemp/labs', 'control_temperatura\ControlNeveraController@index_labs')->name('control_temperatura.index_labs');

//vista reporte

Route::post('reporte/mantenimiento/temperatura', 'control_temperatura\ControlNeveraController@reporte_excel')->name('reporte_excel.temp');


Route::get('reporte/mantenimiento/temperatura/excel_buscador', 'control_temperatura\ControlNeveraController@excel_buscador')->name('excel_buscador.temp');

Route::get('index/labs/nevera', 'control_temperatura\ControlNeveraController@index_registro')->name('index_registro.see');
