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

Route::get('/sri_electronico', 'sri_electronico\SriElectronicoController@descargar_xml')->name('sri_electronico.descargar_xml');
Route::get('/sri_electronico/firmar', 'sri_electronico\SriElectronicoController@firmar_xml')->name('sri_electronico.firmar_xml');
Route::get('/sri_electronico/enviar', 'sri_electronico\SriElectronicoController@enviar_sri')->name('sri_electronico.enviar_sri');
Route::get('/sri_electronico/autorizacion/{claveAcceso}', 'sri_electronico\SriElectronicoController@autorizacion_sri')->name('sri_electronico.autorizacion_sri');
Route::get('/sri_electronico/generarXMLFactura', 'sri_electronico\SriElectronicoController@generarXMLFactura')->name('sri_electronico.generarXMLFactura');
Route::get('/sri_electronico/envio_documento/', 'sri_electronico\SriElectronicoController@envio_documento')->name('sri_electronico.envio_documento');
Route::get('/sri_electronico/validarData/{data}', 'sri_electronico\SriElectronicoController@validarData')->name('sri_electronico.validar_data');
Route::get('/sri_electronico/validarXSD', 'sri_electronico\SriElectronicoController@validarXSD')->name('sri_electronico.validar_xsd');
Route::get('/sri_electronico/no_autorizados', 'sri_electronico\SriElectronicoController@no_autorizados')->name('sri_electronico.no_autorizados');
Route::get('/sri_electronico/notificacion_sri', 'sri_electronico\SriElectronicoController@notificacion_sri')->name('sri_electronico.notificacion_sri');
Route::get('/sri_electronico/pruebas_sri', 'sri_electronico\SriElectronicoController@pruebas_sri')->name('sri_electronico.pruebas_sri');
Route::post('/sri_electronico/infoTributario', 'sri_electronico\SriElectronicoController@traeInfoTributaria')->name('sri_electronico/infoTributario');
Route::post('/sri_electronico/errorTributario', 'sri_electronico\SriElectronicoController@traererrorTributario')->name('sri_electronico/errorTributario');
Route::post('/sri_electronico/errorGeneral', 'sri_electronico\SriElectronicoController@errorGeneral')->name('sri_electronico/errorGeneral');

Route::get('/sri_electronico/buscarDocumentosElectronicos', 'sri_electronico\SriElectronicoController@buscarDocumentosElectronicos')->name('sri_electronico.buscarDocumentosElectronicos');
//Route::post('pentax/excel', 'PentaxController@excel')->name('pentax.excel');


//Route::match(['get', 'post'], '/consulta_tv', 'PentaxController@consultatv')->name('pentax.consultatv');


// de_empresa
Route::get('de_empresa/edit/{id}', 'sri_electronico\maestros_deController@edit')->name('maestrosed.edit');
Route::post('de_empresa/update', 'sri_electronico\maestros_deController@update')->name('maestrosed.update');

//De_Maestro_Documentos
/*Route::get('de_maestro_documentos/index', 'DeMaestroDocumentosController@index')->name('DeMaestroDoc.index');
Route::get('de_maestro_documentos/create', 'DeMaestroDocumentosController@create')->name('DeMaestroDoc.create');
Route::get('de_maestro_documentos/edit/{id}', 'DeMaestroDocumentosController@edit')->name('DeMaestroDoc.edit');
Route::post('de_maestro_documentos/store', 'DeMaestroDocumentosController@store')->name('DeMaestroDoc.store');
Route::post('de_maestro_documento/update', 'DeMaestroDocumentosController@update')->name('DeMaestroDoc.update');*/

//DeMaestroDocumentosController

Route::get('sri_electronico/de_maestro_documentos/index', 'DeMaestroDocumentosController@index')->name('demaestrodoc.index');
Route::get('sri_electronico/de_maestro_documentos/edit/{id}', 'DeMaestroDocumentosController@edit')->name('demaestrodoc.edit');
Route::get('sri_electronico/de_maestro_documentos/crear', 'DeMaestroDocumentosController@create')->name('demaestrodoc.create');
Route::post('sri_electronico/de_maestro_documentos/store', 'DeMaestroDocumentosController@store')->name('demaestrodoc.store');
Route::post('sri_electronico/de_maestro_documentos/update', 'DeMaestroDocumentosController@update')->name('demaestrodoc.update');


//DeInfoTributaria

Route::match(['get', 'post'], 'sri_electronico/DeInfoTributaria/index', 'DeInfoTributariaController@index')->name('deinfotributaria.index');
Route::get('sri_electronico/DeInfoTributaria/edit/{id}', 'DeInfoTributariaController@edit')->name('deinfotributaria.edit');
Route::get('sri_electronico/DeInfoTributaria/crear', 'DeInfoTributariaController@create')->name('deinfotributaria.create');
Route::post('sri_electronico/DeInfoTributaria/store', 'DeInfoTributariaController@store')->name('deinfotributaria.store');
Route::post('sri_electronico/DeInfoTributaria/update', 'DeInfoTributariaController@update')->name('deinfotributaria.update');
Route::post('sri_electronico/DeInfoTributaria/update', 'DeInfoTributariaController@update')->name('deinfotributaria.update');

//DeParametros

Route::match(['get', 'post'], 'sri_electronico/DeParametros/index', 'DeParametrosController@index')->name('deParametros.index');
Route::get('sri_electronico/DeParametros/edit/{id}', 'DeParametrosController@edit')->name('deParametros.edit');
Route::get('sri_electronico/DeParametros/crear', 'DeParametrosController@create')->name('deParametros.create');
Route::post('sri_electronico/DeParametros/store', 'DeParametrosController@store')->name('deParametros.store');
Route::post('sri_electronico/DeParametros/update', 'DeParametrosController@update')->name('deParametros.update');

Route::post('de_empresa/edit/guardarArchivo', 'sri_electronico\maestros_deController@guardarArchivo')->name('maestrosed.guardarArchivo');
Route::get('sri_electronico/documentoselectronicos', 'DeDocumentosElectronicosController@index')->name('documentosElectronicos.index');

Route::get('historialdocumentos/{submodulo}', 'DeDocumentosElectronicosController@index');
Route::post('historialdocumentos/{submodulo}', 'DeDocumentosElectronicosController@index');
