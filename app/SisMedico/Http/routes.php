<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//rutas accessibles slo si el usuario no se ha logueado
Route::group(['middleware' => 'guest'], function () {

	Route::get('login', 'Auth\AuthController@getLogin');
	Route::post('login', ['as' =>'login', 'uses' => 'Auth\AuthController@postLogin']); 
	// Registration routes...
	Route::get('register', 'Auth\AuthController@getRegister');
	Route::post('register', ['as' => 'auth/register', 'uses' => 'Auth\AuthController@postRegister']);


});

//rutas accessibles solo si el usuario esta autenticado y ha ingresado al sistema
Route::group(['middleware' => 'auth'], function () {

	Route::get('/', 'HomeController@index');
    Route::get('home', 'HomeController@index');
    Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);
    Route::get('listado_usuarios/{page?}', 'UsuariosController@listado_usuarios');
	Route::get('listado_publicaciones/{id?}', 'PublicacionesController@listado_publicaciones');
	Route::get('descargar_publicacion/{id}', 'PublicacionesController@descargar_publicacion');
	Route::get('buscar_usuarios/{pais}/{dato?}', 'UsuariosController@buscar_usuarios');
	  //leccion 13
    Route::get('form_enviar_correo', 'CorreoController@crear');
    Route::post('enviar_correo', 'CorreoController@enviar');
    Route::post('cargar_archivo_correo', 'CorreoController@store');

    Route::get('reportes', 'PdfController@index');
    Route::get('crear_reporte_porpais/{tipo}', 'PdfController@crear_reporte_porpais');
    Route::get('listado_graficas', 'GraficasController@index');
    Route::get('grafica_registros/{anio}/{mes}', 'GraficasController@registros_mes');
    Route::get('grafica_publicaciones', 'GraficasController@total_publicaciones');


    

});


//rutas accessibles solo para el usuario administrador

Route::group(['middleware' => 'usuarioAdmin'], function () {
      
      Route::get('form_nuevo_usuario', 'UsuariosController@form_nuevo_usuario');
      Route::post('agregar_nuevo_usuario', 'UsuariosController@agregar_nuevo_usuario');
      Route::get('form_editar_usuario/{id}', 'UsuariosController@form_editar_usuario');
      Route::post('editar_usuario', 'UsuariosController@editar_usuario');
      Route::post('subir_imagen_usuario', 'UsuariosController@subir_imagen_usuario');
      Route::post('cambiar_password', 'UsuariosController@cambiar_password');
      //leccion 9
      Route::get('form_cargar_datos_usuarios', 'UsuariosController@form_cargar_datos_usuarios');
      Route::post('cargar_datos_usuarios', 'UsuariosController@cargar_datos_usuarios');
      //leccion 10
      Route::get('form_educacion_usuario/{id}', 'EducacionController@form_educacion_usuario');
      Route::post('agregar_educacion_usuario', 'EducacionController@agregar_educacion');
      Route::get('borrar_educacion/{id}', 'EducacionController@borrar_educacion');

      //leccion 11
      Route::get('form_publicaciones_usuario/{id}', 'PublicacionesController@form_publicaciones_usuario');
      Route::post('agregar_publicacion_usuario', 'PublicacionesController@agregar_publicacion');
      Route::get('borrar_publicacion/{id}', 'PublicacionesController@borrar_publicacion');

      //leccion 11 repetida
	  Route::get('form_proyectos_usuario/{id}', 'ProyectosController@form_proyectos_usuario');
	  Route::post('agregar_proyectos_usuario', 'ProyectosController@agregar_proyectos_usuario');
	  Route::get('borrar_proyecto/{id}', 'ProyectosController@borrar_proyecto');


});




Route::group(['middleware' => 'usuarioStandard'], function () {	
     
    Route::get('info_datos_usuario/{id}', 'UsuariosController@info_datos_usuario');
    

});










