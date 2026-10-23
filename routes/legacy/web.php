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

Route::get('lang/{lang}', function ($lang) {
    Session::put('lang', $lang);

    return Redirect::back();
})->middleware('web')->name('change_lang');

Route::get('/', 'DashboardController@index')->middleware('auth');
Auth::routes();
Route::get('user/verification/reset', 'Auth\ForgotPasswordController@user')->name('auth.user');
Route::post('user/password/recover/', 'Auth\ForgotPasswordController@recover')->name('auth.user.recover');

// Route::resource('gcalendar', 'gCalendarController');
// Route::get('oauth', ['as' => 'oauthCallback', 'uses' => 'gCalendarController@oauth']);

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('modal_revisar/{id}', 'DashboardController@modal_revisar')->name('dashboard.modal_revisar'); // modalrevisar
Route::post('modal_revisar/consulta', 'DashboardController@consulta')->name('dashboard.consulta');
Route::get('/profile', 'ProfileController@index');
// Administración de Usuarios
Route::post('user-management/subir_imagen_usuario', 'UserManagementController@subir_imagen_usuario')->name('user-management.subir_imagen_usuario');

Route::post('user-management/actualizar/paciente/', 'UserManagementController@update_paciente_publico')->name('user-management.update_paciente');
Route::match(['get', 'post'], 'user-management/search', 'UserManagementController@search')->name('user-management.search');
Route::resource('user-management', 'UserManagementController');
Route::get('user-management/editarreunion/{index}', 'UserManagementController@maxsearch')->name('doctor.max');
Route::post('user-management/{doctor}', 'UserManagementController@max')->name('user-management.max');
Route::post('doctor/horario/{id}', 'UserManagementController@creahorario')->name('user-management.creahorario');
Route::post('doctor/horario/actualiza/{id}', 'UserManagementController@editahorario')->name('user-management.editahorario');

// administración de Tipos de usuario
Route::match(['get', 'post'], 'tipo_usuario-management/search', 'Tipo_UsuarioManagementController@search')->name('tipo_usuario-management.search');
Route::resource('tipo_usuario-management', 'Tipo_UsuarioManagementController');

Route::resource('employee-management', 'EmployeeManagementController');
Route::match(['get', 'post'], 'employee-management/search', 'EmployeeManagementController@search')->name('employee-management.search');

Route::resource('system-management/department', 'DepartmentController');
Route::post('system-management/department/search', 'DepartmentController@search')->name('department.search');

Route::resource('system-management/division', 'DivisionController');
Route::post('system-management/division/search', 'DivisionController@search')->name('division.search');

Route::resource('system-management/country', 'CountryController');
Route::post('system-management/country/search', 'CountryController@search')->name('country.search');

Route::resource('system-management/state', 'StateController');
Route::post('system-management/state/search', 'StateController@search')->name('state.search');

Route::resource('system-management/city', 'CityController');
Route::post('system-management/city/search', 'CityController@search')->name('city.search');

Route::get('system-management/report', 'ReportController@index');
Route::post('system-management/report/search', 'ReportController@search')->name('report.search');
Route::post('system-management/report/excel', 'ReportController@exportExcel')->name('report.excel');
Route::post('system-management/report/pdf', 'ReportController@exportPDF')->name('report.pdf');

Route::get('avatars/{name}', 'EmployeeManagementController@load');
Route::get('logo/{name}', 'EmployeeManagementController@load_logo');
// rutas de los seguros
Route::resource('form_enviar_seguro', 'SeguroController');
Route::match(['get', 'post'], 'seguro/search', 'SeguroController@search')->name('seguro.search');
Route::get('seguro/subseguro/{id}', 'SeguroController@listasubseguro')->name('seguro.subseguro');
Route::get('seguro/subseguro/{id}/crear/', 'SeguroController@subsegurocreate')->name('subseguro.create');
Route::post('seguro/subseguro/guardar', 'SeguroController@subseguroguardar')->name('subseguro.store');
Route::get('seguro/subseguro/{id}/modificar/', 'SeguroController@subseguroedit')->name('subseguro.edit');
Route::post('seguro/subseguro/editar/modificacion', 'SeguroController@subseguroupdate')->name('subseguro.update');

// rutas de paciente
Route::match(['GET', 'POST'], 'paciente/search', 'PacienteController@search')->name('paciente.search');
Route::resource('paciente', 'PacienteController');
Route::post('paciente/subir_imagen_usuario', 'PacienteController@subir_imagen_usuario')->name('paciente.subir_imagen_usuario');
Route::match(['PUT', 'PATCH'], 'paciente/{id}/0', 'PacienteController@updatefamiliar')->name('paciente.updatefamiliar');
// buscar por nombre
Route::get('agenda/pacientepornombre/{id_doc}/{fecha}/{sala}', 'PacienteController@buscaxnombre')->name('paciente.buscaxnombre');
Route::post('agenda/buscarpaciente', 'PacienteController@search2')->name('paciente.search2');
Route::match(['get', 'post'], 'paciente/historia_clinica/{id_paciente}', 'PacienteController@historiaclinica')->name('paciente.historia');
Route::match(['get', 'post'], 'paciente/nombre/', 'PacienteController@nombre')->name('paciente.nombre');
Route::match(['get', 'post'], 'paciente/nombre2/', 'PacienteController@nombre2')->name('paciente.nombre2');
Route::match(['get', 'post'], 'agenda/paciente/pornombre', 'PacienteController@pacientexnombre')->name('paciente.pacientexnombre'); // paciente por nombre 04/04/2018

// rutas para la agenda
Route::match(['get', 'post'], 'agenda/search', 'AgendaController@search')->name('agenda.search');
Route::match(['get', 'post'], 'reporte/agenda_diario', 'AgendaController@reportediario')->name('agenda.reportediario');
Route::post('reporte/excel', 'AgendaController@excel')->name('agenda.excel');
Route::resource('agenda', 'AgendaController');
Route::get('agenda/calendario/{index}', 'AgendaController@agenda')->name('agenda.agenda');
Route::get('agenda/calendario/{index}/{fecha}', 'AgendaController@agenda2')->name('agenda.fecha');

// suspendidas
Route::get('agenda/calendario/suspendidas/{index}/{i}', 'AgendaController@suspendidas')->name('agenda.suspendidas');

// Route::get('agenda/horario/doctores', 'VdoctorController@agenda')->name('agenda.agenda2');
Route::match(['get', 'post'], 'agenda/horario/doctores', 'VdoctorController@agenda')->name('agenda.agenda2');
Route::get('agenda/doctor/horario/', 'AgendaController@agenda4')->name('agenda.agenda4');
Route::get('agenda/paciente/{id}/{i}/{fecha}/{sala}', 'AgendaController@paciente')->name('agenda.paciente');
Route::get('agenda/paciente/{id}/', 'AgendaController@paciente')->name('agenda.paciente2');
Route::get('agenda/createconsulta/{index}/{i}', 'AgendaController@createconsulta')->name('agenda.consulta');
Route::get('agenda/createconsulta/{index}', 'AgendaController@createconsulta')->name('agenda.consulta2');
Route::post('agenda/guardar', 'AgendaController@guardar')->name('agenda.guardar');
Route::get('agenda/calendario/nuevo/{id}', 'AgendaController@nuevo')->name('agenda.nuevo');
Route::get('agenda/calendario/nuevo/{id}/{fecha}/{i}', 'AgendaController@nuevo')->name('agenda.nuevo2');

Route::get('agenda/editarreunion/{index}', 'AgendaController@reunionsearch')->name('reunion.search');
Route::get('agenda/reunion/{index}', 'AgendaController@reunionedit')->name('reunion.edit2');
Route::match(['get', 'post'], 'agenda/{agenda}/edit/{doctor}', 'AgendaController@edit2')->name('agenda.edit2');
Route::match(['get', 'post'], 'agenda/edit/pre/{agenda}/{doctor}', 'AgendaController@edit2_pre')->name('agenda.edit2_pre');

Route::post('agenda/reunion/{index}', 'AgendaController@updatereunion2')->name('agenda.updatereunion2');
Route::post('agenda/{index}/{doctor}', 'AgendaController@update2')->name('agenda.update2');
Route::get('agenda/horario/doctores/{id}', 'AgendaController@detalle')->name('agenda.detalle');

Route::get('agenda/consulta_agenda/{doctor}/{fecha}', 'AgendaController@consulta_ag')->name('agenda.consulta_ag');
Route::post('agenda/guardar_otro_seguro', 'AgendaController@guardar_otro_seguro')->name('agenda.guardar_otro_seguro');

// del lado del doctor
Route::get('agendar_reunion', 'VdoctorController@guardar')->name('agenda.reuniondoctor');
Route::get('validaconfir', 'VdoctorController@validaconfir')->name('agenda.validaconfir');
Route::get('agenda/horario/doctores/historiaclinica/{id}', 'VdoctorController@detalle')->name('agenda.detalle2');
Route::get('agenda/horario/doctores/historiaclinica/3/{id}', 'VdoctorController@detalle3')->name('agenda.detalle3'); // PROVISIONAL VT
Route::get('agenda/anterior/{id}', 'VdoctorController@anterior')->name('agenda.mostrar');
Route::get('agenda/anteriorhc/{id}', 'VdoctorController@hcagenda')->name('agenda.hcagenda');
Route::get('agenda/anterior-procedimieno/{id}', 'VdoctorController@anteriorprocedimiento')->name('agenda.mostrar2');
Route::get('agendar_doctor/{id}/{i}', 'VdoctorController@agendar_doctor')->name('agendar_dr.agendar_doctor');
Route::get('agendar_doctor/agendar', 'VdoctorController@crear_cita_dr')->name('agendar_dr.actualizar');
Route::get('agendar_reunion/{id}/{i}', 'VdoctorController@agendar_reunion')->name('agendar_dr.reunion');

// rutas para la empresa
Route::resource('empresa', 'EmpresaController')->names([
    'index' => 'empresa.catalogo.index',
]);
Route::post('empresa/search', 'EmpresaController@search')->name('empresa.search');
Route::get('logo/{name}', 'EmpresaController@load');
Route::post('empresa/subir_logo', 'EmpresaController@subir_logo')->name('empresa.subir_logo');
Route::match(['get', 'post'], 'empresa/buscarcontador', 'EmpresaController@buscarcontador')->name('empresa.buscarcontador');
Route::match(['get', 'post'], 'administracion/empresa/buscarcontador', 'EmpresaController@buscar_usuario')->name('empresa.buscar_usuario');

// administración de hospitales
Route::resource('hospital-management', 'HospitalManagementController');
Route::post('hospital-management/search', 'HospitalManagementController@search')->name('hospital-management.search');

// administración de salas
// Route::resource('sala-management', 'SalaManagementController');
Route::match(['get', 'post'], 'sala-management/{hospital}/listasalas', 'SalaManagementController@listasalas')->name('sala-management.listasalas');
Route::get('sala-management/{hospital}/crear', 'SalaManagementController@crear')->name('sala-management.crear');
Route::post('sala-management/{hospital}/grabar', 'SalaManagementController@grabar')->name('sala-management.grabar');
Route::get('sala-management/{hospital}/{sala}/editar', 'SalaManagementController@editar')->name('sala-management.editar');
Route::match(['PUT', 'PATCH'], 'sala-management/{hospital}/{sala}', 'SalaManagementController@actualizar')->name('sala-management.actualizar');
// Administracion de camillas
Route::get('camilla-management/{hospital}/listascamillas', 'CamillaManagementController@listascamillas')->name('camilla-management.listascamillas');
Route::get('camilla-management/{hospital}/crear', 'CamillaManagementController@crear')->name('camilla-management.crear');
Route::post('camilla-management/{hospital}/grabar', 'CamillaManagementController@grabar')->name('camilla-management.grabar');
Route::get('camilla-management/{hospital}/{camilla}/editar', 'CamillaManagementController@editar')->name('camilla-management.editar');
Route::match(['PUT', 'PATCH'], 'camilla-management/{hospital}/{camilla}', 'CamillaManagementController@actualizar')->name('camilla-management.actualizar');

// perfil de usuario
Route::get('perfil/editar/', 'AgendaController@perfil')->name('perfil.editar');

// administración de procedimientos
Route::match(['get', 'post'], 'procedimiento/search', 'ProcedimientoController@search')->name('procedimiento.search');
Route::resource('procedimiento', 'ProcedimientoController');
Route::get('procedimiento/sugerido/{id}', 'ProcedimientoController@sugerido')->name('procedimiento.sugerido');
Route::post('procedimiento/procedimientos_guardar/', 'ProcedimientoController@procedimientoguardar')->name('procedimiento.procedimientoguardar');

// administracion de especialidades
Route::match(['get', 'post'], 'especialidad/search', 'EspecialidadController@search')->name('especialidad.search');
Route::resource('especialidad', 'EspecialidadController');

// administracion de admisiones
Route::resource('admisiones', 'AdmisionController');
Route::match(['GET', 'HEAD'], 'admisiones/{index}/{cita}/admision/{url_doc}/{un}/{seguro}', 'AdmisionController@admision')->name('admisiones.admision');
Route::match(['GET', 'HEAD'], 'admisiones/{index}/{cita}/admision/{url_doc}/{un}', 'AdmisionController@admision')->name('admisiones.admision2');
Route::match(['GET', 'HEAD'], 'admisiones/{index}/actualizar/{seguro}', 'AdmisionController@actualizar')->name('admisiones.actualizar');
Route::match(['GET', 'HEAD'], 'admisiones/{index}/actualizar/', 'AdmisionController@actualizar')->name('admisiones.actualizar2');
Route::get('hc/{name}', 'AdmisionController@load');
Route::post('admision/{id}/{cita}/{historia}', 'AdmisionController@update_doctor')->name('admisiones.update_doctor');
Route::get('busca_principal/{id_paciente}/{id_usuario}/{historia}', 'AdmisionController@busca_principal')->name('admisiones.busca_principal');
Route::get('editar_principal/{id_paciente}', 'AdmisionController@editar_principal')->name('admisiones.editar_principal');
Route::get('crear_principal/{id_paciente}', 'AdmisionController@crear_principal')->name('admisiones.crear_principal');
Route::get('buscar_usuario/{id}', 'AdmisionController@buscar_usuario')->name('admisiones.buscar_usuario');
Route::get('editar_principal/update/data', 'AdmisionController@actualiza_pr')->name('admisiones.actualiza_pr');
Route::get('crear_principal/update/data', 'AdmisionController@crea_pr')->name('admisiones.crea_pr');
Route::get('select_sseguro/{seguro}/{parentesco}/{cita}/{old}', 'AdmisionController@select_sseguro')->name('admisiones.select_sseguro');

// historia clinica
Route::post('historiaclinica/guardar/', 'VdoctorController@actualizar')->name('historiaclinica.guardar');
Route::post('historiaclinica/generar/foto/', 'VdoctorController@foto2')->name('historiaclinica.fotos');
Route::post('agenda/generar/archivo/subida/dato', 'AgendaController@archivo567')->name('agenda.archivo5');
Route::get('historiaclinica/foto/mostrar', 'VdoctorController@foto')->name('procedimiento.imagen');
Route::get('agenda/datos/foto/mostrar', 'AgendaController@foto567')->name('agenda.imagen567');
Route::get('agenda/datos/foto/eliminar/{id}', 'AgendaController@eliminarfoto')->name('agenda.eliminarfoto');

// horarios
Route::resource('horario', 'HorarioController');
Route::get('horario/actualizar/{id}/{start}/{end}/{extra}', 'HorarioController@actualizar');
Route::get('horario/actualizar/', 'HorarioController@actualizar')->name('horario.actualizar');
Route::get('horario/actualizar2/{id}/{start}/{end}/{extra}', 'HorarioController@actualizar2');
Route::get('horario/actualizar2/', 'HorarioController@actualizar2')->name('horario.actualizar2');
Route::get('horario/eliminar/{id}', 'HorarioController@eliminar');
Route::get('horario/eliminar2/{id}', 'HorarioController@eliminarunico');
Route::get('horario/eliminar/', 'HorarioController@eliminar')->name('horario.eliminar');
Route::get('horario/eliminar2/', 'HorarioController@eliminarunico')->name('horario.eliminar2');

// modificar los horarios vista de administradores
Route::get('modificar/horario/doctores/', 'HorarioController@index_admin')->name('horario.index_admin');
Route::get('modificar/horario/doctores_doctor/{id}', 'HorarioController@index_admin_ingreso')->name('horario.doctor');
Route::post('modificar/horario/doctor2/unico', 'HorarioController@unicodia2')->name('horario.unico2');
Route::get('modificar/horario/doctor/agregar/horario', 'HorarioController@dato_agregar2')->name('horario.agregar_modal2');
Route::get('modificar/horario/doctor/agregar/horario/{start}/{end}/{id}', 'HorarioController@dato_agregar2');
Route::post('modificar/horario/agregar/nuevo/modal', 'HorarioController@agregarmodal2')->name('horario.agregar_enviar2');

Route::get('agenda/editarcortesia/{id}/{c}', 'VdoctorController@actualizacortesia')->name('vdoctor.cortesia');

Route::match(['get', 'post'], 'cortesia/search', 'CortesiaController@search')->name('cortesia.search');
Route::resource('cortesia', 'CortesiaController');
Route::get('cortesia/crear/{id}', 'CortesiaController@crear2')->name('cortesia.crear2');
Route::get('cortesia/crear/', 'CortesiaController@crear2')->name('cortesia.crear3');
Route::get('cortesia/editarcortesia/{id}/{i}', 'CortesiaController@editarcortesia')->name('cortesia.editarcortesia');

// preagenda
Route::get('agendaprocedimiento', 'PreAgendaController@preagenda')->name('preagenda.procedimiento');
Route::get('agendaprocedimiento/{fecha}', 'PreAgendaController@preagenda2')->name('preagenda.procedimiento2');

Route::resource('preagenda', 'PreAgendaController');
Route::get('agendaprocedimiento/nuevo/', 'PreAgendaController@nuevo')->name('preagenda.nuevo');
Route::match(['get', 'post'], 'agenda_procedimiento/pentax_procedimiento', 'PreAgendaController@pentax')->name('preagenda.pentax');
Route::get('agendaprocedimiento/nuevo/{fecha}/{i}/{sala}', 'PreAgendaController@nuevo')->name('preagenda.nuevo2');

Route::get('pre_agenda/regresar/{agenda}/{unix}/{url_doctor}', 'PreAgendaController@regresar')->name('preagenda.regresar');

Route::get('agenda/seleccionar/{id}', 'PacienteController@seleccionar')->name('paciente.seleccionar');
Route::get('hc_agenda/{name}', 'PreAgendaController@load');
Route::get('preagenda/datos/eliminar/{id}', 'PreAgendaController@eliminarfoto')->name('preagenda.eliminarfoto');

// cambio de horarios en preagenda
Route::get('preagenda/cambio/horario/', 'PreAgendaController@cambiarhorario')->name('preagenda.actualizarhorario');
Route::get('agenda/cambio/horario/', 'AgendaController@cambiarhorario')->name('agenda.actualizarhorario');
Route::get('agenda/cambio/horario/{id}/{inicio}/{fin}', 'AgendaController@cambiarhorario');
Route::get('preagenda/cambio/horario/{id}/{inicio}/{fin}', 'PreAgendaController@cambiarhorario');

// agregar horario dia unico al doctor
Route::post('horario/doctor/unico', 'HorarioController@unicodia')->name('horario.unico');
Route::get('horario/doctor/eliminar/{id}', 'HorarioController@eliminarunico')->name('unico.eliminar');
Route::get('horario/doctor/validar/{id}', 'HorarioController@validarhorario')->name('horario.validarhorario');
Route::get('horario/doctor/validar2/{id}', 'HorarioController@validarhorario2')->name('horario.validarhorario2');

// modal para agregar horarios del doctor
Route::get('horario/doctor/agregar/horario', 'HorarioController@dato_agregar')->name('horario.agregar_modal');
Route::get('horario/doctor/agregar/horario/{start}/{end}', 'HorarioController@dato_agregar');

// modal para agregar
Route::post('horario/agregar/nuevo/modal', 'HorarioController@agregarmodal')->name('horario.agregar_enviar');

// modal de etiquetas
Route::get('agenda/etiquetas/datos/{id}/{seguro}/{alergia}', 'AdmisionController@modaletiquetas')->name('admision.etiqueta2');
Route::get('agenda/etiquetas/datos/{id}', 'AdmisionController@modaletiquetas')->name('admision.etiqueta');

// ingreso de datos del paciente
Route::post('agenda/horario/doctores/datos/ingreso', 'CombinadoController@ingreso')->name('admision_datos.doctor');

// observaciones generales
Route::resource('observacion', 'ObservacionController');
Route::get('observacion/search/{fecha}', 'ObservacionController@search')->name('observacion.search');
Route::get('observacion/cantidad/hoy', 'ObservacionController@cantidad')->name('observacion.cantidad');
Route::get('observacion/inactiva/{id}', 'ObservacionController@inactiva')->name('observacion.inactiva');
Route::get('observacion/activa/{id}', 'ObservacionController@activa')->name('observacion.activa');

// nuevo preagenda
Route::match(['get', 'post'], 'preagenda/salas_todas/', 'PreAgendaController@salas_todas')->name('preagenda.salas_todas');
Route::match(['get', 'post'], 'preagenda/salas_todas/excel', 'PreAgendaController@to_excel')->name('preagenda.to_excel');

// ocupar sala
Route::get('preagenda/salas_todas/validar_hora', 'PreAgendaController@validar_hora')->name('validar_hora');
Route::get('preagenda/salas_todas/ocupar_sala', 'PreAgendaController@ocupar_sala')->name('ocupar_sala');
Route::get('preagenda/salas_todas/guardar_sala', 'PreAgendaController@guardar_sala')->name('guardar_sala');
Route::get('preagenda/salas_todas/modal_modificar/{id}', 'PreAgendaController@modal_modificar')->name('agenda.calendario');
Route::get('preagenda/salas_todas/guardar_modificaciones_sala', 'PreAgendaController@guardar_modificaciones_sala')->name('guardar_modificaciones_sala');
// pdf's 18092018
Route::resource('manual', 'ManualController');
Route::get('manual/subir/{id}', 'ManualController@subir')->name('manual.subir');
Route::post('manual/subir/cargar', 'ManualController@cargar_file')->name('manual.cargar_file');
Route::get('manual/descargar/{name}', 'ManualController@load')->name('manual.load');
Route::get('manual/modal/{id}', 'ManualController@modal')->name('manual.modal');
Route::get('manual/eliminar/{id}', 'ManualController@eliminar_manual')->name('manual.eliminar_manual');

// IMPORTAR CIE10
Route::get('importar', 'ImportarController@importar')->name('importar.load');

Route::get('importar/masivo/producto/new/{nombre}/{funcion}', 'ImportarController@guardarExcelAle')->name('importar.guardarExcelAle');

// IMPORTAR GENERICOS
Route::get('importar_generico', 'ImportarController@importar_generico')->name('importar.generico');

// IMPORTAR EXAMENES_ANDRES
Route::get('importar_examenes', 'ImportarController@importar_examenes')->name('importar.examenes');

// CARGA PACIENTES DEL SCI
Route::get('sci/importar', 'ImportarController@sci_importar')->name('csi.sci_importar');
// restablecimiento de clave

// IMPORTAR EXAMENES sabana
Route::get('importar_sabana', 'ImportarController@importar_sabana')->name('importar.sabana');

// IMPORTAR EXAMENES referido
Route::get('importar_referido', 'ImportarController@importar_referido')->name('importar.referido');

// importar_valores2019
Route::get('importar_valores2019', 'ImportarController@importar_valores2019')->name('importar.valores2019');

// corregir Principio Activo
Route::get('corregir_principio', 'ImportarController@corregir_principio')->name('corregir.principio');

// ESTADISTICOS PROCEDIMIENTOS REFERIDOS
Route::get('procedimientos_referidos', 'ImportarController@cargar_estad')->name('importar.cargar_estad');

// ESTADISTICOS PROCEDIMIENTOS REFERIDOS
Route::get('importar_vademecum', 'ImportarController@vademecum');
// CAMPOS PRODUCTOS
Route::get('actualizar_campos', 'ImportarController@actualizar_campos')->name('actualizar_campos');
// IMPORTAR PRODUCTOS 2020
Route::get('relacionar_campos', 'ImportarController@relacionar_campos')->name('relacionar_campos');
// RELACIONAR CAMPOS
Route::get('importar_producto', 'ImportarController@importar_precios')->name('importar_productos');
// ACTUALIZAR CAMPOS 2020
Route::get('actualizar_ct_productos', 'ImportarController@actualizar_ct_productos')->name('actualizar_ct_productos');
// ACTUALIZAR CAMPOS2020
Route::get('actulizar_impuesto', 'ImportarController@actulizar_impuesto')->name('actulizar_impuesto');
// SALAS_TODAS
Route::get('salas_todas/actualiza/{id}/{inicio}/{fin}/{sala}', 'PreAgendaController@desplazamiento')->name('salas_todas.desplazamiento');
Route::get('salas_todas/intervalo/{id}/{inicio}/{fin}', 'PreAgendaController@intervalo')->name('salas_todas.intervalo');

Route::match(['get', 'post'], 'salas_todas/buscar/', 'PreAgendaController@salas_todas_ajax')->name('salas_todas.buscar_ajax');
// SALAS TODAS last
Route::get('salas_todas/{id}', 'PreAgendaController@st_cargar')->name('salas_todas.cargar');

// PACIENTES CONSULTA
Route::get('pacientes/consulta', 'PacienteController@consulta')->name('pacientes.consulta');
Route::match(['GET', 'POST'], 'pacientes/search_consulta', 'PacienteController@search_consulta')->name('pacientes.search_consulta');

// importar_valores ehg
Route::get('importar_valores_seguro', 'ImportarController@importar_valores_seguro')->name('importar.importar_valores_seguro');

Route::get('importar_medicinas', 'ImportarController@importar_medicinas')->name('importar.importar_medicinas');
Route::get('verificar_medicinas', 'ImportarController@verificar_medicinas')->name('importar.verificar_medicinas');
Route::get('subir_medicinas_iess', 'ImportarController@subir_medicinas_iess')->name('importar.subir_medicinas_iess');

Route::match(['get', 'post'], 'agenda/horario/doctores/completo', 'VdoctorController@agenda_completa')->name('agenda_doctor.visualizarcompleta');

Route::get('/paciente_actualizacion', 'UserManagementController@actualar_paciente_solo')->name('user.actualar_paciente_solo');

// agenda validacion de procedimientos por dias
Route::get('preagenda/pentax/maximo_dia/{dia}', 'PreAgendaController@maximo_dia')->name('preagenda.maximo_dia');
Route::get('preagenda/pentax/agendar_pnombre/{inicio}/{sala}', 'PreAgendaController@agenda_pnombre')->name('preagenda.agenda_pnombre');
Route::post('preagenda/pentax/agendar_pnombre/cargar', 'PreAgendaController@pnombre_guardar')->name('preagenda.pnombre_guardar');
// reuniones desde la vista de mes
Route::get('agenda/reunion/nuevo/{id}/{fecha}/{i}', 'AgendaController@nuevo_reunion')->name('agenda.nuevo_reunion');
Route::post('agenda/reunion/nuevo/guardar', 'AgendaController@nuevo_reunion_guardar')->name('agenda.nuevo_reunion_guardar');

// reporte de estado de situacion financiera interna
Route::get('/financiero', 'FinancieroController@estadosituacioni')->name('financiero.estado_situacion_i');

// Route::match(['get', 'post'], 'contable/balance_comprobacion', 'contable\BalanceComprobacionController@index')->name('balance_comprobacion.index');
// Route::get('/procedimientos', 'DashboardController@index')->name('dashboard');

// NUEVO PACIENTES
Route::get('nuevo_agenda/paciente/{id}', 'AgendaController@existe_usuario')->name('agenda.existe_usuario');
Route::get('nuevo_agenda/paciente/ver/{cedula}', 'PacienteController@ver_copia_cedula')->name('paciente.ver_copia_cedula');
Route::post('nuevo_agenda/subir_copia', 'PacienteController@subir_copia')->name('paciente.subir_copia');
Route::post('principal/guardar', 'PacienteController@guardar_principal')->name('paciente.guardar_principal');
Route::post('opcional/guardar', 'PacienteController@guardar_opcional')->name('paciente.guardar_opcional');
// Route::post('opcional/guardar', 'PacienteController@pr')->name('paciente.guardar_opcional');

// Horario Sala

Route::get('horario/sala/{id}', 'HorarioSalaController@index_admin_ingreso')->name('horario.sala');
Route::get('horarios/salas', 'HorarioSalaController@index')->name('horario.index_sala');
Route::get('horarios/salas/crear', 'HorarioSalaController@crear')->name('salas.crear');
Route::post('modificar/horario/sala/unico', 'HorarioSalaController@unicodia2')->name('horario_sala.unico2');
Route::get('horario/sala/actualizar/{id}/{start}/{end}/{extra}', 'HorarioSalaController@actualizar');
Route::get('horario/sala/actualizar/', 'HorarioSalaController@actualizar')->name('horario_sala.actualizar');
Route::get('modificar/horario/sala/agregar/horario', 'HorarioSalaController@dato_agregar2')->name('horario_sala.agregar_modal2');
Route::get('modificar/horario/sala/agregar/horario/{start}/{end}/{id}', 'HorarioSalaController@dato_agregar2');
Route::post('modificar/horario/sala/agregar/nuevo/modal', 'HorarioSalaController@agregarmodal2')->name('horario_sala.agregar_enviar2');
Route::get('horario/sala/actualizar2/{id}/{start}/{end}/{extra}', 'HorarioSalaController@actualizar2');
Route::get('horario/sala/actualizar2/', 'HorarioSalaController@actualizar2')->name('horario_sala.actualizar2');
Route::get('horario/sala/eliminar/', 'HorarioSalaController@eliminar')->name('horario_sala.eliminar');
Route::get('horario/sala/eliminar2/', 'HorarioSalaController@eliminarunico')->name('horario_sala.eliminar2');
Route::match(['get', 'post'], 'estadistica/formulario', 'FormularioController@formulario')->name('estadistica.formulario');
Route::match(['get', 'post'], 'estadistica/formulario_guardar', 'FormularioController@formulario_guardar')->name('formulario.guardar');
Route::match(['get', 'post'], 'reportecomisiones', 'ReporteComisionesController@reporte_comisiones')->name('reporte.reporte_comisiones');
Route::match(['get', 'post'], 'reportecomisiones/ingreso', 'ReporteComisionesController@reporte_comisiones_ingreso')->name('reporte_comisiones.ingreso');

Route::get('/prueba/query', 'ImportarController@nuevo_seguro')->name('pruebas_query');

Route::get('/segruro/nuevo/convenio', 'ImportarController@nuevo_seguro_convenio')->name('nuevo.convenio');

Route::get('/masivo/saldos/iniciales', 'ImportarController@masivoSaldos')->name('importar.masivoSaldos');

// Route::get('/limpiar_cache', function () {
//     //$exitCode = Artisan::call('cache:clear');
//     $exitCode = Artisan::call('config:clear');
//     //$exitCode = Artisan::call('cache:config');
//     //$exitCode = Artisan::call('views:clear');
// });

// update agenda

Route::get('agenda/modal/cambios/{id_agenda}', 'AgendaController@modal_cambios_agenda')->name('agenda.modal_cambios_agenda');
Route::post('agenda/modal/cambio/update', 'AgendaController@update_cambios')->name('agenda.update_cambios');

Route::get('masivo_inv/{nombre}', 'ImportarController@masivo_inv')->name('importar.masivo_inv');

Route::match(['get', 'post'], 'masivo/cambio_producto', 'ImportarController@cambiarProducto')->name('cambio_producto');

Route::match(['get', 'post'], 'masivo/masivo_arreglar/{id_empresa}', 'ImportarController@masivo_arreglar')->name('updateCodigo');

Route::match(['get', 'post'], 'masivo/productos/portoviejo/{excel}', 'ImportarController@crearProductoPortoviejo')->name('crearProductoPortoviejo');

Route::match(['get', 'post'], 'traslados/productos/doc', 'ImportarController@traslados')->name('inportar.traslados');

Route::match(['get', 'post'], 'masivo/pedido/cambiar', 'ImportarController@masivoArreglarPedido')->name('inportar.masivoArreglarPedido');

Route::get('api_doc_electronico/', 'EmisionDocumentosController@index');
Route::post('api_doc_electronico/', 'EmisionDocumentosController@index');
// Route::match(['get', 'post'], 'api_doc_electronico/{opcion}', 'EmisionDocumentosController@index');
Route::match(['get', 'post'], 'apifacturaopciones/{opcion}', 'EmisionDocumentosController@opcionesFactura');
Route::match(['get', 'post'], 'apiretencionopciones/{opcion}', 'EmisionDocumentosController@opcionesRetenciones');
Route::match(['get', 'post'], 'apipacienteopciones/{opcion}', 'EmisionDocumentosController@opcionesPaciente');
Route::match(['get', 'post'], 'apinotacreditoclienteopciones/{opcion}', 'EmisionDocumentosController@opcionesNotasCreditoCliente');
Route::get('opciones/{opcion}', 'EmisionDocumentosController@index');

Route::match(['get', 'post'], 'masivo/productos/traslado/empresa', 'ImportarController@traslado_productos')->name('importar.traslado_productos');

Route::match(['get', 'post'], 'masivo/reporte/producto/compras', 'ImportarController@masivoProductoRe')->name('importar.masivoProductoRe');

Route::match(['get', 'post'], 'masivo/cuentas/traslado/empresa', 'ImportarController@plan_cuentas')->name('importar.plan_cuentas');

Route::match(['get', 'post'], 'masivo/plan_cuenta/empresa/{empresa}', 'ImportarController@masivo_cuentas_empresa')->name('inportar.masivo_cuentas_empresa');

Route::match(['get', 'post'], 'masivo/excel/cuenta_empresa/{excel}', 'ImportarController@excel_cuenta_empresa')->name('inportar.excel_cuenta_empresa');
Route::post('convenios_publicos_isspol/guardarCie10', 'AgendaController@guardarCie10_isspol')->name('agenda.guardarCie10_isspol');

Route::match(['get', 'post'], 'producto/diferente/serie', 'ImportarController@productoSerieDiferente')->name('importar.serie.id_producto');
Route::match(['get', 'post'], 'masivo/guardar/cabecera_movimiento', 'ImportarController@masivoguardar_id_cab')->name('importar.masivoguardar_cabecera_movimiento');

Route::match(['get', 'post'], 'masivo/nota/credito/asientos', 'ImportarController@ncAsientos')->name('importar.ncAsientos');

Route::match(['get', 'post'], 'masivo/nota/credito/arreglarNCC', 'ImportarController@arreglarNCC')->name('importar.arreglarNCC');

Route::match(['get', 'post'], 'masivo/excel/actualizar_valores_ap', 'ImportarController@actualizar_valores_ap')->name('importar.actualizar_valores_ap');

Route::match(['get', 'post'], 'masivo/productos/gastritech/{nombre}', 'Insumos\CargaInicialController@masivoExcelGastritech')->name('cargaInicial.masivoExcelGastritech');
Route::get('agenda/guardar/triaje/telefonico', 'AgendaController@triaje_telefonico')->name('agenda.triaje_telefonico');
Route::get('importaciones/importar_examenes_comisiones/{id_doctor}/{tipo}', 'ImportarController@importar_examenes_comisiones')->name('importar_examenes_comisiones_tipo');
// DECIMO TERCER SUELDO
Route::get('importar_valorespago', 'ImportarController@importar_valorespago')->name('importar.importar_valorespago');
Route::get('importaciones/importar_examenes_comisiones/{id_doctor}', 'ImportarController@importar_examenes_comisiones')->name('importar_examenes_comisiones');
// Ruta de mantenimiento de tiempo examen pasante
Route::post('json/event/json/busqueda', 'AgendaController@buscador_json')->name('agenda.buscador_json');
Route::get('examen_mantenimiento', 'laboratorio\ExamenController@examen_tiempo_index')->name('examen_mt.index');
Route::post('examen_mantenimiento', 'laboratorio\ExamenController@examen_tiempo_search')->name('examen_mt.search');
Route::match(['get', 'post'], 'examen_mantenimiento/actualizar', 'laboratorio\ExamenController@examen_tiempo_update')->name('examen_mt.update');

Route::get('sub_login/{id}', 'Auth\LoginController@login_externo');
// editar hc_formulario_013 -> Pasante
Route::get('formulario_013/{id}', 'hc_admision\ApBitacoraController@formulario_013_edit')->name('formulario013.edit');
Route::post('formulario_013/update', 'hc_admision\ApBitacoraController@formulario_013_update')->name('formulario013.update');

// Masivo creacion de productos en insumos y contabilidad
Route::match(['get', 'post'], 'masivo/productos/guardar_prod/{excel}/{id}', 'ImportarController@guardar_excel_producto')->name('importar.guardar_excel_producto');

// Masivo productos a servicios en facturas
Route::match(['get', 'post'], 'contable/productos/detalleKardex', 'DashboardController@index');

Route::get('contabilidad/', 'contable\ContabilidadController@index');
Route::match(['get', 'post'], 'contabilidad/{opcion}', 'contable\ContabilidadController@index');

/* modificar productos por estado */
Route::get('opcionesProductos/', 'contable\ProductosController@opcionesProductos');
Route::post('opcionesProductos/{opcion}', 'contable\ProductosController@opcionesProductos');
Route::match(['get', 'post'], 'masivo/subir_procedimientos', 'ImportarController@subir_procedimientos')->name('importar.subir_procedimientos');

Route::get('chatbot/envio_mensajes', 'chatbot\ChatBotController@enviar_mensajes');

Route::get('imagenes/descarga/thumb/{filename}', 'hc_admision\VideoController@thumbs');
Route::get('importaciones/importar_valor_contable', 'ImportarController@importar_valor_contable')->name('importar.importar_valor_contable');
Route::get('firmador/index', 'Apis\FirmadorController@index')->name('firmador.index');
Route::get('firmador/create', 'Apis\FirmadorController@create')->name('firmador.create');
Route::get('firmador/edit/{id}', 'Apis\FirmadorController@edit')->name('firmador.edit');
Route::post('firmador/store', 'Apis\FirmadorController@store')->name('firmador.store');
Route::post('firmador/update', 'Apis\FirmadorController@update')->name('firmador.update');

Route::match(['get', 'post'], 'manualcontroller/ver/archivo/{id}', 'ManualController@ver_archivo')->name('manual.ver_archivo');

// DASHBOARD LABS F582026

Route::get('dashboard/labs/index', 'dashboard\DashboardLabs@index')->name('dashboard.labs.index');
Route::get('dashboard/labs/detalle', 'dashboard\DashboardLabs@detalle')->name('dashboard.labs.detalle');
Route::get('dashboard/labs/exportar', 'dashboard\DashboardLabs@exportar')->name('dashboard.labs.exportar');

Route::get('/dashboard/gastroclinica', 'DashboardController@gastroclinica')
    ->name('dashboard.gastroclinica.index');
Route::post('/dashboard/gastroclinica/rotacion', 'DashboardController@rotacion')
    ->name('dashboard.gastroclinica.rotacion');
Route::post('/dashboard/gastroclinica/ventas', 'DashboardController@ventas')
    ->name('dashboard.gastroclinica.ventas');
Route::get('/dashboard/gastroclinica/exportar/{analisis}', 'DashboardController@exportar')
    ->name('dashboard.gastroclinica.exportar');

Route::get(
    '/dashboard/exportar-ventas-vendedor',
    'DashboardController@exportarVentasVendedor'
)->name('dashboard.exportar_ventas_vendedor');

Route::post(
    '/dashboard/detalle-ventas-vendedor',
    'DashboardController@detalleVentasVendedor'
)->name('dashboard.detalle_ventas_vendedor');

Route::get(
    '/dashboard/conversion-cotizaciones-vendedor/buscar-vendedores',
    'DashboardController@buscarVendedoresConversion'
)->name('dashboard.buscar_vendedores_conversion');

Route::post(
    '/dashboard/conversion-cotizaciones-vendedor',
    'DashboardController@filtrarConversionCotizacionesVendedor'
)->name('dashboard.conversion_cotizaciones_vendedor');

Route::post(
    '/dashboard/conversion-cotizaciones-vendedor/empresas',
    'DashboardController@detalleConversionCotizacionesVendedor'
)->name('dashboard.conversion_cotizaciones_vendedor_empresas');

Route::post('/dashboard/filtrar-ventas-general', 'DashboardController@filtrarVentasGeneral')->name('dashboard.filtrar_ventas_general');
Route::post('/dashboard/filtrar-ventas-vendedor',
    'DashboardController@filtrarVentasVendedor')
    ->name('dashboard.filtrar_ventas_vendedor')
    ->middleware('auth');

Route::post(
    '/dashboard/filtrar-ventas-producto',
    'DashboardController@filtrarVentasPorProducto'
)->name('dashboard.filtrar_ventas_producto');

Route::post(
    '/dashboard/ventas-producto/link-pdf/obtener',
    'DashboardController@obtenerLinkPdfVentaProducto'
)->name('dashboard.ventas_producto_link_pdf_obtener');

Route::post('/dashboard/ventas-producto/link-pdf/guardar', 'DashboardController@guardarLinkPdfVentaProducto')->name('dashboard.ventas_producto_link_pdf_guardar');
Route::post('/dashboard/ventas-producto/sincronizar-drive', 'DashboardController@sincronizarLinksDriveVentasProducto')->name('dashboard.ventas_producto_sincronizar_drive');
Route::get(
    '/dashboard/buscar-productos-ventas',
    'DashboardController@buscarProductosVentasProducto'
)->name('dashboard.buscar_productos_ventas');
