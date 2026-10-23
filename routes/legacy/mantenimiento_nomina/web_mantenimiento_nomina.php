<?php
Route::match(['get', 'post'], 'area_rh/index', 'mantenimiento_nomina\Ct_Rh_AreaController@index')->name('area_rh.index');
Route::match(['get', 'post'], 'area_rh/create', 'mantenimiento_nomina\Ct_Rh_AreaController@create')->name('area_rh.create');
Route::match(['get', 'post'], 'area_rh/store', 'mantenimiento_nomina\Ct_Rh_AreaController@store')->name('area_rh.store');
Route::match(['get', 'post'], 'area_rh/edit/{id}', 'mantenimiento_nomina\Ct_Rh_AreaController@edit')->name('area_rh.edit');
Route::match(['get', 'post'], 'area_rh/update', 'mantenimiento_nomina\Ct_Rh_AreaController@update')->name('area_rh.update');
//estado civil
Route::match(['get', 'post'], 'estado_civil/index', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@index')->name('estado_civil.index');
Route::match(['get', 'post'], 'estado_civil/create', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@create')->name('estado_civil.create');
Route::match(['get', 'post'], 'estado_civil/store', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@store')->name('estado_civil.store');
Route::match(['get', 'post'], 'estado_civil/edit/{id}', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@edit')->name('estado_civil.edit');
Route::match(['get', 'post'], 'estado_civil/update', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@update')->name('estado_civil.update');
//Horario
Route::match(['get', 'post'], 'mantenimiento/horario/index', 'mantenimiento_nomina\HorarioController@index')->name('mantenimiento.horario.index');
Route::match(['get', 'post'], 'mantenimiento/horario/create', 'mantenimiento_nomina\HorarioController@create')->name('mantenimiento.horario.create');
Route::match(['get', 'post'], 'mantenimiento/horario/store', 'mantenimiento_nomina\HorarioController@store')->name('mantenimiento.horario.store');
Route::match(['get', 'post'], 'mantenimiento/horario/edit/{id}', 'mantenimiento_nomina\HorarioController@edit')->name('mantenimiento.horario.edit');
Route::match(['get', 'post'], 'mantenimiento/horario/update', 'mantenimiento_nomina\HorarioController@update')->name('mantenimiento.horario.update');

//Nivel academico
Route::match(['get', 'post'], 'nivel_academico/index', 'mantenimiento_nomina\NivelAcademicoController@index')->name('nivel_academico.index');
Route::match(['get', 'post'], 'nivel_academico/create', 'mantenimiento_nomina\NivelAcademicoController@create')->name('nivel_academico.create');
Route::match(['get', 'post'], 'nivel_academico/store', 'mantenimiento_nomina\NivelAcademicoController@store')->name('nivel_academico.store');
Route::match(['get', 'post'], 'nivel_academico/edit/{id}', 'mantenimiento_nomina\NivelAcademicoController@edit')->name('nivel_academico.edit');
Route::match(['get', 'post'], 'nivel_academico/update', 'mantenimiento_nomina\NivelAcademicoController@update')->name('nivel_academico.update');

//pago beneficio
Route::get('nomina_pago_beneficio/index', 'mantenimiento_nomina\PagoBeneficioController@index')->name('pagobeneficio.index');
Route::get('nomina_pago_beneficio/crear','mantenimiento_nomina\PagoBeneficioController@crear')->name('pagobeneficio.crear');
Route::post('nomina_pago_beneficio/store', 'mantenimiento_nomina\PagoBeneficioController@store')->name('pagobeneficio.store');
Route::get('nomina_pago_beneficio/editar/{id}', 'mantenimiento_nomina\PagoBeneficioController@edit')->name('pagobeneficio.edit');
Route::post('nomina_pago_beneficio/update', 'mantenimiento_nomina\PagoBeneficioController@update')->name('pagobeneficio.update');

//Tipo Aporte
Route::get('nomina_tipo_aporte/index', 'mantenimiento_nomina\Tipo_AporteController@index')->name('tipo_aporte.index');
Route::get('nomina_tipo_aporte/crear','mantenimiento_nomina\Tipo_AporteController@crear')->name('tipo_aporte.crear');
Route::post('nomina_tipo_aporte/store', 'mantenimiento_nomina\Tipo_AporteController@store')->name('tipo_aporte.store');
Route::get('nomina_tipo_aporte/editar/{id}', 'mantenimiento_nomina\Tipo_AporteController@edit')->name('tipo_aporte.edit');
Route::post('nomina_tipo_aporte/update', 'mantenimiento_nomina\Tipo_AporteController@update')->name('tipo_aporte.update');

//Tipo rol
Route::get('nomina_tipo_rol/index', 'mantenimiento_nomina\Tipo_RolController@index')->name('tipo_rol.index');
Route::get('nomina_tipo_rol/crear','mantenimiento_nomina\Tipo_RolController@crear')->name('tipo_rol.crear');
Route::post('nomina_tipo_rol/store', 'mantenimiento_nomina\Tipo_RolController@store')->name('tipo_rol.store');
Route::get('nomina_tipo_rol/editar/{id}', 'mantenimiento_nomina\Tipo_RolController@edit')->name('tipo_rol.edit');
Route::post('nomina_tipo_rol/update', 'mantenimiento_nomina\Tipo_RolController@update')->name('tipo_rol.update');