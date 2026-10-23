<?php

//rutas para la conexion con el AS400

Route::match(['get', 'post'], '/api/facturacion/electronica', 'ApiFacturacionController@envio')->name('apifacturacion.envio');

Route::get('/importar/ventas/{nombre}', 'ImportarController@ventas');

Route::get('/pagina/resultados/externos/imprimir/{id}', 'PaginaLabsController@resultados_externos')->name('pagina_labs.resultados');
Route::get('/pagina/resultados_dr_externo/imprimir/{id}', 'PaginaLabsController@resultados_dr_externos')->name('pagina_labs.resultados_dr_externos');
Route::get('/labs/cotizacion/externos/imprimir/{id}', 'PaginaLabsController@cotizacion_externos')->name('pagina_labs.cotizacion_externos');

Route::get('tributario', ['sri_electronico\InicioController@show']);
