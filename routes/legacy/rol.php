<?php

Route::match(['get', 'post'],'historial/rol', 'RolController@historial_rol')->name('historial.rol');
Route::match(['get', 'post'],'historial/rol_lista', 'RolController@rol_lista')->name('rol.lista');
