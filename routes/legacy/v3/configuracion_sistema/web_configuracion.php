<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['general']], function () {
    /*Ubicaciones*/
    Route::get('v3/configuracionessistema', 'v3\configuracion_sistema\ConfiguracionesSistemaController@index');
    Route::match(['get', 'post'], 'v3/configuracionessistema/{submodulo}', 'v3\configuracion_sistema\ConfiguracionesSistemaController@index');
    
});
