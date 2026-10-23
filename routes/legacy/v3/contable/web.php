<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['operacion.v3', 'general']], function () {

    Route::get('v3/planes', 'v3\contable\RegistroController@index');
    Route::get('v3/registro_usuario/{id}', 'v3\contable\RegistroController@registro_usuario')
        ->where('id', '[0-9]+');
    Route::get('v3/registro_empresa/{id}', 'v3\contable\RegistroController@registro_empresa')
        ->where('id', '[0-9]+');
    Route::post('v3/guardar_usuario', 'v3\contable\RegistroController@guardar_usuario')
        ->name('registro.guardar_usuario');
    Route::get('v3/buscar_usuario/{id}', 'v3\contable\RegistroController@buscar_usuario')
        ->where('id', '[A-Za-z0-9_-]+');

    /* API interna: requiere sesion v3 y se limita a la empresa activa. */
    Route::get('api/v1/tributario', 'v3\contable\Apis\ApiInicioController@show')
        ->name('v3.api.tributario');
    /*Documentacion tecnica*/
    Route::get('v3/documentacion', 'v3\contable\DocumentacionController@index')
        ->name('v3.documentacion');
    Route::get('v3/documentacion/{documento}', 'v3\contable\DocumentacionController@detalle')
        ->where('documento', '[a-z0-9_-]+')
        ->name('v3.documentacion.detalle');

    /*Operacion y monitoreo*/
    Route::get('v3/operacion-monitoreo', 'v3\contable\OperacionMonitoreoController@index')
        ->name('v3.operacion-monitoreo');
    Route::post('v3/operacion-monitoreo/errores/{firma}/corregir', 'v3\contable\OperacionMonitoreoController@marcarErrorCorregido')
        ->where('firma', '[a-f0-9]{40}')
        ->name('v3.operacion-monitoreo.errores.corregir');

    /*Matriz de madurez*/
    Route::get('v3/matriz-madurez', 'v3\contable\MatrizMadurezController@index')
        ->name('v3.matriz-madurez');

    /*Admin*/
    Route::get('v3/admin', 'v3\contable\AdminController@index');
    Route::match(['get', 'post'], 'v3/admin/{submodulo}', 'v3\contable\AdminController@index');

    /*Config*/
    Route::get('v3/configuraciones', 'v3\contable\ConfiguracionesController@index');
    Route::match(['get', 'post'], 'v3/configuraciones/{submodulo}', 'v3\contable\ConfiguracionesController@index');

    /*Utilidades*/
    Route::get('v3/utilidades', 'v3\contable\UtilidadesController@index');
    Route::match(['get', 'post'], 'v3/utilidades/{submodulo}', 'v3\contable\UtilidadesController@index');
    Route::post('v3/utilidades/subirArchivo', 'v3\contable\UtilidadesController@subirArchivo');
    Route::post('v3/utilidades/verArchivo', 'v3\contable\UtilidadesController@verArchivo');
    Route::get('v3/utilidades/descargaArchivo/{filename}', 'v3\contable\UtilidadesController@descargaArchivo');

    /*Contabilidad*/
    Route::get('v3/contabilidad', 'v3\contable\ContabilidadController@index');
    Route::match(['get', 'post'], 'v3/contabilidad/{submodulo}', 'v3\contable\ContabilidadController@index');

    /*Proveedores*/
    Route::get('v3/proveedores', 'v3\contable\ProveedoresController@index');
    Route::match(['get', 'post'], 'v3/proveedores/{submodulo}', 'v3\contable\ProveedoresController@index');

    /*Clientes*/
    Route::get('v3/clientes', 'v3\contable\ClientesController@index');
    Route::match(['get', 'post'], 'v3/clientes/{submodulo}', 'v3\contable\ClientesController@index');

    /*Ventas*/
    Route::get('v3/ventas', 'v3\contable\VentasController@index');
    Route::match(['get', 'post'], 'v3/ventas/{submodulo}', 'v3\contable\VentasController@index');
    Route::match(['get', 'post'], 'v3/ventas/{submodulo}/{extra}', 'v3\contable\VentasController@index');

    /*Reportes*/
    Route::get('v3/reportes', 'v3\contable\ReportesController@index');
    Route::match(['get', 'post'], 'v3/reportes/{submodulo}', 'v3\contable\ReportesController@index');
    Route::match(['get', 'post'], 'v3/reportes/{submodulo}/{extra}', 'v3\contable\ReportesController@index');

    /*Compras*/
    Route::get('v3/compras', 'v3\contable\ComprasController@index');
    Route::match(['get', 'post'], 'v3/compras/{submodulo}', 'v3\contable\ComprasController@index');

    /*RecursosHumanos*/
    Route::get('v3/recursoshumanos', 'v3\contable\RecursosHumanosController@index');
    Route::match(['get', 'post'], 'v3/recursoshumanos/{submodulo}', 'v3\contable\RecursosHumanosController@index');

    /*CajaBanco*/
    Route::get('v3/cajabancos', 'v3\contable\CajaBancosController@index');
    Route::match(['get', 'post'], 'v3/cajabancos/{submodulo}', 'v3\contable\CajaBancosController@index');
    Route::get('v3/cajabancos/{submodulo}/{id}', 'v3\contable\CajaBancosController@index');

    /*GuiadeRemision*/
    Route::get('v3/guiaderemision', 'v3\contable\GuiadeRemisionController@index');
    Route::match(['get', 'post'], 'v3/guiaderemision/{submodulo}', 'v3\contable\GuiadeRemisionController@index');

    /*ActivosFijos*/
    Route::get('v3/activosfijos', 'v3\contable\ActivosFijosController@index');
    Route::match(['get', 'post'], 'v3/activosfijos/{submodulo}', 'v3\contable\ActivosFijosController@index');

    /*Inventario*/
    Route::get('v3/inventario', 'v3\contable\InventarioController@index');
    Route::match(['get', 'post'], 'v3/inventario/{submodulo}', 'v3\contable\InventarioController@index');

    /*EmisionDocumentos*/
    Route::get('v3/opcionesdocumentos', 'v3\contable\OpcionesDocumentosController@index');
    Route::match(['get', 'post'], 'v3/opcionesdocumentos/{submodulo}', 'v3\contable\OpcionesDocumentosController@index');

    /*Tributario*/
    Route::get('v3/tributario', 'v3\contable\TributarioController@index');
    Route::match(['get', 'post'], 'v3/tributario/{submodulo}', 'v3\contable\TributarioController@index');
    Route::match(['get', 'post'], 'v3/tributario/{submodulo}/{id}', 'v3\contable\TributarioController@index');

    /*Importaciones*/
    Route::get('v3/importaciones', 'v3\contable\ImportacionesController@index');
    Route::match(['get', 'post'], 'v3/importaciones/{submodulo}', 'v3\contable\ImportacionesController@index');
});
