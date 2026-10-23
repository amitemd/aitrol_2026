<?php

Route::match(['get', 'post'],'preparaciones/index','preparaciones\preparacionesController@index')->name('preparaciones.index');

Route::match(['get', 'post'],'preparaciones/mostrar/pdf', 'preparaciones\preparacionesController@mostrar_pdf')->name('preparaciones.mostrar_pdf');
Route::get('preparaciones/crear','preparaciones\preparacionesController@crear')->name('preparaciones.crear');
Route::post('preparaciones/guardar', 'preparaciones\preparacionesController@guardar_preparaciones')->name('preparaciones.guardar');