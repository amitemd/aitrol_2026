<?php

Route::group(['middleware' => ['auth']], function () {
    Route::get('contable/entregas-mercaderia', 'contable\EntregaMercaderiaController@index')->name('entregas_mercaderia.index');
    Route::get('contable/entregas-mercaderia/crear', 'contable\EntregaMercaderiaController@create')->name('entregas_mercaderia.create');
    Route::get('contable/entregas-mercaderia/ajax/destinatarios', 'contable\EntregaMercaderiaController@buscarDestinatarios')->name('entregas_mercaderia.ajax_destinatarios');
    Route::get('contable/entregas-mercaderia/ajax/bodegas', 'contable\EntregaMercaderiaController@buscarBodegas')->name('entregas_mercaderia.ajax_bodegas');
    Route::get('contable/entregas-mercaderia/ajax/productos', 'contable\EntregaMercaderiaController@buscarProductos')->name('entregas_mercaderia.ajax_productos');
    Route::get('contable/entregas-mercaderia/ajax/series', 'contable\EntregaMercaderiaController@buscarSeries')->name('entregas_mercaderia.ajax_series');
    Route::post('contable/entregas-mercaderia/ajax/reservar-serie', 'contable\EntregaMercaderiaController@reservarSerie')->name('entregas_mercaderia.ajax_reservar_serie');
    Route::post('contable/entregas-mercaderia/ajax/liberar-serie', 'contable\EntregaMercaderiaController@liberarSerie')->name('entregas_mercaderia.ajax_liberar_serie');
    Route::post('contable/entregas-mercaderia', 'contable\EntregaMercaderiaController@store')->name('entregas_mercaderia.store');
    Route::post('contable/entregas-mercaderia/{id}/anular', 'contable\EntregaMercaderiaController@anular')->name('entregas_mercaderia.anular');
    Route::get('contable/entregas-mercaderia/{id}', 'contable\EntregaMercaderiaController@show')->name('entregas_mercaderia.show');
    Route::get('contable/entregas-mercaderia/{id}/facturar', 'contable\EntregaMercaderiaController@facturar')->name('entregas_mercaderia.facturar');
});
