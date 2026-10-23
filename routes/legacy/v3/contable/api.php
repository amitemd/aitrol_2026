<?php

use Illuminate\Support\Facades\Route;
// use Sis_medico\Http\Controllers\v3\Api\ApiEnvioCorreos;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


$router->group(['prefix' => 'v1'], function ($router) {
    //$router->post('enviocorreo/', [ApiEnvioCorreos::class, 'index']);
    echo 'hola';
});
*/
//Route::get('v1/enviocorreo', 'v3\Api\ApiEnvioCorreosController@index');
// Route::get('v1/enviocorreo', function () {
//     dd("Hola");
// });


/*
| Estos prototipos no se cargan desde RouteServiceProvider. Se mantienen
| declarados solo como reserva segura: si se habilitan en el futuro, las
| operaciones con efectos exigen POST y sus controladores responden 410.
*/
Route::group(['prefix' => 'v1'], function () {
    Route::post('enviocorreo', 'v3\contable\Apis\ApiEnvioCorreosController@index');
    Route::post('emisionDocumentos', 'v3\contable\Apis\ApiEmisionDocumentosController@index');
});
