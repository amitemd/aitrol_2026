<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'hospital/inicio', 'hospital\HospitalController@index')->name('hospital.index');
Route::match(['get', 'post'], 'hospital/inicio/iniciobusca', 'hospital\HospitalController@iniciobusca')->name('hospital.iniciobusca');
Route::match(['get', 'post'], 'hospital/inicio/buscar', 'hospital\HospitalController@buscar')->name('hospital.buscar');
Route::match(['get', 'post'], 'hospital/farmacia', 'hospital\HospitalController@farmacia')->name('hospital.farmacia');
Route::match(['get', 'post'], 'hospital/farmacia/buscadorfarmacia', 'hospital\HospitalController@buscadorfarmacia')->name('hospital.buscadorfarmacia');
// GESTION DE CUARTOS
Route::match(['get', 'post'], 'hospital/gestioncuartos', 'hospital\CuartoController@cuartos')->name('hospital.gcuartos');
Route::match(['get', 'post'], 'hospital/gestioncuartos_hab', 'hospital\CuartoController@cuartos_habitacion')->name('hospital.gcuartos_habitacion');
Route::match(['get', 'post'], 'hospital/gestioncuartos/vistac', 'hospital\HospitalController@vistac')->name('hospital.vistac');
Route::match(['get', 'post'], 'hospital/admcuarto/ver/{id}/{id_paciente}', 'hospital\CuartoController@admcuarto')->name('hospital.admcuarto');
Route::match(['get', 'post'], 'hospital/admcuarto/ver/paciente', 'hospital\CuartoController@paciente')->name('hospital.paciente');
// G.C Modal Prescripcion Doctor
Route::post('hospital/modalprescripcion/guardarm', 'hospital\CuartoController@guardarm')->name('hospital.guardarm');
Route::match(['get', 'post'], 'hospital/modalprescripcion/{id}', 'hospital\CuartoController@modalprescripcion')->name('hospital.prescripcion');
Route::match(['get', 'post'], 'hospital/modalprescripcion2/autocompletarmodal', 'hospital\CuartoController@autocompletarmodal')->name('hospital.autocompletarmodal22');
Route::match(['get', 'post'], 'hospital/modalprescripcion2/autocompletarmodal2', 'hospital\CuartoController@autocompletarmodal2')->name('hospital.autocompletarmodal2');
Route::match(['get', 'post'], 'hospital/agregar_descripcion', 'hospital\CuartoController@agregar_descripcion')->name('hospital.agregar_descripcion');
// G.C Modal Costo A pagar
Route::match(['get', 'post'], 'hospital/costo/{id}/{id_cama}', 'hospital\CuartoController@costo')->name('hospital.costo');
Route::match(['get', 'post'], 'hospital/costo/generados/', 'hospital\CuartoController@costos_generados')->name('hospital.costos_generados');
Route::match(['get', 'post'], 'hospital/modalservicio/{id}', 'hospital\CuartoController@modalservicio')->name('hospital.servicio');
Route::match(['get', 'post'], 'hospital/salvar', 'hospital\CuartoController@salvar')->name('hospital.salvar');
// G.C Modal Prescripcion Enfermero
Route::match(['get', 'post'], 'hospital/enfermeria/sumnistrar', 'hospital\CuartoController@suministar')->name('hospital.medicamento_enfermeria');
Route::match(['get', 'post'], 'hospital/enfermeria/{id}/{id_evolucion}', 'hospital\CuartoController@modalenfermeria')->name('hospital.modalenfermeria');

Route::match(['get', 'post'], 'hospital/admasigncuarto/{id}/{idc}/{ids}', 'hospital\CuartoController@admasigncuarto')->name('hospital.admasigncuarto');
Route::get('hospital/admcuarto/autocomplete', 'hospital\CuartoController@auto')->name('hospital.auto');

Route::get('hospital/admcuarto/autocomplete2', 'hospital\CuartoController@auto2')->name('hospital.auto2');
Route::match(['get', 'post'], 'hospital/admcuarto/guardar', 'hospital\CuartoController@cuartog')->name('hospital.cuartog');

Route::match(['get', 'post'], 'hospital/farmacia/agregarp', 'hospital\HospitalController@agregarp')->name('hospital.agregarp');

Route::match(['get', 'post'], 'hospital/quirofano/modalq', 'hospital\HospitalController@modalq')->name('hospital.modalq');

Route::match(['get', 'post'], 'hospital/admcuarto/liberar', 'hospital\CuartoController@liberar')->name('hospital.eliminar');

// MARCAS HOSPITAL INICIO
Route::match(['get', 'post'], 'hospital/marcas', 'hospital\MarcasController@marcas')->name('hospital.marcas');
Route::match(['get', 'post'], 'hospital/tablamarca/buscador', 'hospital\MarcasController@buscadorm')->name('hospital.buscadorm');

// TIPO DE PRODUCTO HOSPITAL INICIO
Route::match(['get', 'post'], 'hospital/tipoproducto', 'hospital\tipoproductoController@tipoproducto')->name('hospital.tipoproducto');
Route::match(['get', 'post'], 'hospital/tipoproducto/buscadortipo', 'hospital\tipoproductoController@buscadortipo')->name('hospital.buscadortipo');

// PRODUCTO HOSPITAL INICIO
Route::match(['get', 'post'], 'hospital/producto', 'hospital\ProductoController@producto')->name('hospital.producto');
Route::match(['get', 'post'], 'hospital/producto/buscador', 'hospital\ProductoController@buscador')->name('hospital.buscador');
Route::match(['get', 'post'], 'hospital/producto/pedidos', 'hospital\ProductoController@pedidosproductos')->name('hospital.pedidosproductos');

// PROVEEDORES HOSPITAL INICIO
Route::match(['get', 'post'], 'hospital/proveedores', 'hospital\ProveedoresController@proveedores')->name('hospital.proveedores');
Route::match(['get', 'post'], 'hospital/proveedores/buscadort', 'hospital\ProveedoresController@buscadort')->name('hospital.buscadort');

// BODEGA
Route::match(['get', 'post'], 'hospital/producto/bodegap', 'hospital\BodegaController@bodegap')->name('hospital.bodegap');
Route::match(['get', 'post'], 'hospital/producto/bodegap/buscadorbo', 'hospital\BodegaController@buscadorbo')->name('hospital.buscadorbo');

// QUIROFANO
Route::match(['get', 'post'], 'hospital/quirofano/paciente', 'hospital\AgendaQController@pacientea')->name('hospital.pacientea');
Route::match(['get', 'post'], 'hospital/quirofano/agenda', 'hospital\AgendaQController@agenda')->name('hospital.agenda');
Route::get('hospital/quirofano/autocomplete', 'hospital\AgendaQController@autocomplete')->name('hospital.autocomplete');
Route::get('hospital/quirofano/autocomplete2', 'hospital\AgendaQController@autocomplete2')->name('hospital.autocomplete2');
Route::match(['get', 'post'], 'hospital/datospacientq/', 'hospital\AgendaQController@datospacientq')->name('hospital.datospacientq');

// emergencias
// Route::match(['get', 'post'],'hospital/emergencia', 'hospital\HospitalController@emergencia')->name('hospital.emergencia');
Route::match(['get', 'post'], 'hospital/emergencia', 'hospital\Formulario008Controller@emergencialista')->name('hospital.emergencia');

Route::match(['get', 'post'], 'hospital/emergencia/questionario/{id}', 'hospital\HospitalController@questionario')->name('hospital.questionario');
Route::match(['get', 'post'], 'hospital/emergencia/buscadore', 'hospital\HospitalController@buscadore')->name('hospital.buscadore');
Route::get('hospital/emergencia/autocompletar', 'hospital\HospitalController@autocompletar')->name('hospital.autocompletar');
Route::get('hospital/emergencia/autocompletar2', 'hospital\HospitalController@autocompletar2')->name('hospital.autocompletar2');
Route::get('hospital/emergencia/autocompletar3', 'hospital\HospitalController@autocompletar3')->name('hospital.autocompletar3');
Route::get('hospital/emergencia/autocompletar4', 'hospital\HospitalController@autocompletar4')->name('hospital.autocompletar4');
Route::match(['get', 'post'], 'hospital/emergencia/registrome', 'hospital\HospitalController@registrome')->name('hospital.registrome');
Route::match(['get', 'post'], 'hospital/emergencia/agregarpa', 'hospital\HospitalController@agregarpa')->name('hospital.agregarpa');
Route::match(['get', 'post'], 'hospital/emergencia/registropac', 'hospital\HospitalController@registropac')->name('hospital.registropac');

// Emergencias Formualrio008
Route::match(['get', 'post'], 'hospital/emergencia/ingreso008/{id_solicitud}', 'hospital\Formulario008Controller@ingreso008')->name('hospital.ingreso008');
// Ver resultado del formualrio 008
Route::get('hospital/emergencia/formulario08/{id_paciente}', 'hospital\Formulario008Controller@formulario08')->name('hospital.formulario08');

// Formulario Manchester
Route::get('hospital/emergencia/formulariomanchester', 'hospital\FormularioManchesterController@formulariomanchester')->name('hospital.formulariomanchester');
// Guarda Formulario Manchester
Route::post('hospital/emergencia/guardar_paciente_manchester', 'hospital\FormularioManchesterController@guardar_manchester')->name('manchester.guardar');
Route::post('hospital/emergencia/update_paciente_manchester', 'hospital\FormularioManchesterController@update_manchester')->name('manchester.update');
// muestra los resultados
Route::get('hospital/emergencia/resultadomanchester/{id}', 'hospital\FormularioManchesterController@resultadomanchester')->name('hospital.resultadomanchester');

// Formulario 008 desde aqui en adelante
// Buscar Paciente por nombre
Route::get('hospital/emergencia/paciente', 'hospital\Formulario008Controller@buscar_paciente')->name('buscar_paciente');
// Buscar Tipo de Emergencia por nombre
// Permite Obtener Informacion del Paciente
Route::post('hospital/emergencia/buscar_paciente', 'hospital\Formulario008Controller@obtener_informacion')->name('obtener_informacion');
Route::get('hospital/emergencia/buscar/{id}', 'hospital\Formulario008Controller@busca_paciente')->name('formulario008.busca_paciente');
// Permite Obtener Informacion del Tipo de Emergencia
// Guardar paciente a emergencia en la tabla ingreso_emer008
Route::post('hospital/emergencia/guardar_paciente', 'hospital\Formulario008Controller@guardar')->name('emergencia.guardar');

// Guardar paciente a emergencia en la tabla hosp_atencion_fomulario008
Route::post('hospital/emergencia/guardar_atencio_motivo', 'hospital\Formulario008Controller@guardar_atencio_motivo')->name('emergencia.atencio_motivo');
// Guardar paciente a emergencia en la tabla hosp_revision_formulario008
Route::post('hospital/emergencia/guardar_enfer_actual_revision', 'hospital\Formulario008Controller@guardar_enferm_actual_revsion')->name('emergerncia.enfer_actual');
// Guardar paciente a emergencia en la tabla hosp_accidente_formulario008
Route::post('hospital/emergencia/guardar_accd_viol_intx', 'hospital\Formulario008Controller@guardar_accd_viol_intx')->name('emergencia.accd_viol_intx');
// Guardar paciente a emergencia en la tabla hosp_antecedentes_formulario008
Route::post('hospital/emergencia/guardar_antece_personal_familiar', 'hospital\Formulario008Controller@guardar_ante_pers_familiar')->name('emergencia.ante_pers_familiar');
// Guardar paciente a emergencia en la tabla hosp_signos_vitales_formulario008
Route::post('hospital/emergencia/guardar_signos_vitales', 'hospital\Formulario008Controller@guardar_signos_vitales')->name('emegerncia.signos_vitales');
// Guardar paciente a emergencia en la tabla hosp_obstetrica_formulario008
Route::post('hospital/emergencia/guardar_emer_obstetrica', 'hospital\Formulario008Controller@guardar_emer_obstetrica')->name('emergencias.obstetrica');
// Modal de Tratamiento
Route::get('hospital/emergencia/tratamiento/modal', 'hospital\Formulario008Controller@modal_tratamiento')->name('emergencia.tratamiento.modal');
// Guardar paciente a emergencia en la tabla hosp_tratamiento_formulario008
Route::post('hospital/emergencia/guardar_emergencia_tratamiento', 'hospital\Formulario008Controller@guardar_tratamiento')->name('guardar.tratamiento');
// Guardar paciente a emergencia en la tabla hosp_formulario008_alta
Route::post('hospital/emergencia/guardar_formulario008_alta', 'hospital\Formulario008Controller@guardar_formulario008_alta')->name('guardar.alta');
// HISTORIAL Atencion formulario 008
Route::get('hospital/emergencia/historial_atencion/{id_paciente}', 'hospital\Formulario008Controller@historial_atencion')->name('emergencia.atencion');
// HISTORIAL Revision formulario 008
Route::get('hospital/emergencia/historial_revision/{id_paciente}', 'hospital\Formulario008Controller@historial_revision')->name('emergencia.revision');
// HISTORIAL Accidente formulario 008
Route::get('hospital/emergencia/historial_accidente/{id_paciente}', 'hospital\Formulario008Controller@historial_accidente')->name('emergencia.accidente');
// HISTORIAL Antecendentes formulario 008
Route::get('hospital/emergencia/historial_antecendentes/{id_paciente}', 'hospital\Formulario008Controller@historial_antecendentes')->name('emergencia.antecendentes');
// HISTORIAL Signos_vitales formulario 008
Route::get('hospital/emergencia/historial_signos_vitales/{id_paciente}', 'hospital\Formulario008Controller@historial_signos_vitales')->name('emergencia.signos_vitales');
// HISTORIAL Obstetrica formulario 008
Route::get('hospital/emergencia/historial_obstetrica/{id_paciente}', 'hospital\Formulario008Controller@historial_obstetrica')->name('emergencia.obstetrica');
// HISTORIAL Tratamiento formulario 008
Route::get('hospital/emergencia/historial_tratamiento/{id_paciente}', 'hospital\Formulario008Controller@historial_tratamiento')->name('emergencia.tratamiento');
// HISTORIAL Alta formulario 008
Route::get('hospital/emergencia/historial_alta/{id_paciente}', 'hospital\Formulario008Controller@historial_alta')->name('emergencia.alta');

// formulario 005
// Emergencias Formualrio005_mostrarresultados
Route::get('hospital/formulario05/resultado/{id_paciente}', 'hospital\HospitalController@resultado')->name('hospital.resultado');
// Emergencias Formualrio005_formulario005+variable
Route::get('hospital/emergencia/formulario05/{id_paciente}', 'hospital\HospitalController@formulario05')->name('hospital.formulario05');
// Emergencias Formualrio005_modaleditar+variable
Route::get('hospital/formulario05/modaleditar/{id}', 'hospital\HospitalController@modaleditar')->name('hospital.modaleditar');
// Emergencias Formualrio005_editar+variable
Route::post('hospital/emergencia/editarevolucion/{id}', 'hospital\HospitalController@editarevolucion')->name('hospital.editarevolucion');
// Emergencias Diagnostico_005 guardar
Route::post('hospital/emergencia/diagnostico005', 'hospital\HospitalController@diagnostico005')->name('hospital.diagnostico005');
// Emergencias Formualrio005_guardar_evolucion005
Route::post('hospital/emergencia/formuarioevolucion', 'hospital\HospitalController@formuarioevolucion')->name('hospital.formuarioevolucion');
// Emergencias Formualrio005_mostrar_resultados_diagnostico
Route::get('hospital/formulario05/resultado_diagnostico/{id_paciente}', 'hospital\HospitalController@resultado_diagnostico')->name('hospital.resultado_diagnostico');
// Emergencias Formualrio005_modal_diagnostico_editar
Route::get('hospital/formulario05/modaleditar_diagnostico/{id}', 'hospital\HospitalController@modaleditar_diagnostico')->name('hospital.modaleditar_diagnostico');
// Emergencias Formualrio005_editar_diagnostico_005
Route::post('hospital/emergencia/editardiagnostico/{id}', 'hospital\HospitalController@editardiagnostico')->name('hospital.editardiagnostico');
// Emergencias Formualrio005_medidas_generales
Route::post('hospital/emergencia/medidas_generales', 'hospital\HospitalController@medidas_generales')->name('hospital.medidas_generales');
// Emergencias Formualrio005_medidas_generales_resultado
Route::get('hospital/formulario05/resultado_generales/{id_paciente}', 'hospital\HospitalController@resultado_generales')->name('hospital.resultado_generales');
// Emergencias Formualrio005_medidas_generales_editar
Route::get('hospital/emergencia/editar_generales/{id}', 'hospital\HospitalController@editar_generales')->name('hospital.editar_generales');
// Emergencias Formualrio005_medidas_generales_editar_modal
Route::post('hospital/emergencia/editar_gene/{id}', 'hospital\HospitalController@editar_gene')->name('hospital.editar_gene');
// Emergencias Formualrio005_tratamiento
Route::post('hospital/emergencia/tratamiento', 'hospital\HospitalController@tratamiento')->name('hospital.tratamiento');
// Emergencias Formualrio005_tratamiento_resultados
Route::get('hospital/formulario05/mostrar_resultadotratamiento/{id_paciente}', 'hospital\HospitalController@mostrar_resultadotratamiento')->name('hospital.mostrar_resultadotratamiento');
// Emergencias Formualrio005_tratamiento_resultados
Route::get('hospital/emergencia/modal_tratamiento/{id}', 'hospital\HospitalController@modal_tratamiento')->name('hospital.modal_tratamiento');
// Emergencias Formualrio005_tratamiento_resultados_editar
Route::post('hospital/emergencia/editar_tratamiento/{id}', 'hospital\HospitalController@editar_tratamiento')->name('hospital.editar_tratamiento');
// Emergencias Formualrio005_plan
Route::match(['get', 'post'], 'hospital/emergencia/plan', 'hospital\HospitalController@plan')->name('hospital.plan');
// Emergencias Formualrio005_plan_resultado
Route::get('hospital/formulario05/resultado_plan/{id_paciente}', 'hospital\HospitalController@resultado_plan')->name('hospital.resultado_plan');
// Emergencias Formualrio005_plan_resultado_modal_editar
Route::get('hospital/emergencia/modal_editarplan/{id}', 'hospital\HospitalController@modal_editarplan')->name('hospital.modal_editarplan');
// Emergencias Formualrio005_plan_resultado_modal_editar_
Route::post('hospital/emergencia/editar_plan/{id}', 'hospital\HospitalController@editar_plan')->name('hospital.editar_plan');
// Emergencias Formualrio005_medicamentos
Route::post('hospital/emergencia/medicamentos', 'hospital\HospitalController@medicamentos')->name('hospital.medicamentos');
// Emergencias Formualrio005_medicamentos_resultado
Route::get('hospital/emergencia/medicamentos_resultado/{id_paciente}', 'hospital\HospitalController@medicamentos_resultado')->name('hospital.medicamentos_resultado');
// Emergencias Formualrio005_medicamentos_resultado_editar
Route::get('hospital/emergencia/modal_medicamentos/{id}', 'hospital\HospitalController@modal_medicamentos')->name('hospital.modal_medicamentos');
// Emergencias Formualrio005_medicamentos_resultado_editar
Route::post('hospital/emergencia/editar_medi/{id}', 'hospital\HospitalController@editar_medi')->name('hospital.editar_medi');
// Emergencias Formualrio005_salas
Route::post('hospital/emergencia/salas', 'hospital\HospitalController@salas')->name('hospital.salas');
// Emergencias Formualrio005_salas_resultado
Route::get('hospital/emergencia/salas_resultado/{id_paciente}', 'hospital\HospitalController@salas_resultado')->name('hospital.salas_resultado');
// Emergencias Formualrio005_salas_resultado_modal
Route::get('hospital/emergencia/editar_salas/{id}', 'hospital\HospitalController@editar_salas')->name('hospital.editar_salas');
// Emergencias Formualrio005_salas_resultado_modal_editar
Route::post('hospital/emergencia/editar_modal/{id}', 'hospital\HospitalController@editar_modal')->name('hospital.editar_modal');
// Emergencias Formulario005_autocompletar_cie10
Route::get('hospital/emergencia/autocompletarcie', 'hospital\HospitalController@autocompletarcie')->name('hospital.autocompletarcie');

// Cuartos Formulario053
Route::get('hospital/habitacion/formulario053/{id}/{id_paciente}', 'hospital\Formulario053Controller@formulario053')->name('hospital.formulario053');
Route::post('hospital/habitacion/guardar_informacion', 'hospital\Formulario053Controller@guardar_informacion')->name('hospital.guardar_informacion');
Route::post('hospital/habitacion/formulario053_hallazgos', 'hospital\Formulario053Controller@formulario053_hallazgos')->name('hospital.formulario053_hallazgos');
Route::post('hospital/habitacion/formulario053_diagnostico', 'hospital\Formulario053Controller@formulario053_diagnostico')->name('hospital.formulario053_diagnostico');
Route::post('hospital/habitacion/formulario053_tratamiento', 'hospital\Formulario053Controller@formulario053_tratamiento')->name('hospital.formulario053_tratamiento');
Route::get('hospital/habitacion/formulario053_autucompletar', 'hospital\Formulario053Controller@formulario053_autucompletar')->name('hospital.formulario053_autucompletar');
Route::post('hospital/habitacion/formulario053_referencia', 'hospital\Formulario053Controller@formulario053_referencia')->name('hospital.formulario053_referencia');
Route::get('hospital/habitacion/formulario053_resultado/{id}/{id_paciente}', 'hospital\Formulario053Controller@formulario053_resultado')->name('hospital.formulario053_resultado');
Route::get('hospital/darkorligth/mode', 'hospital\ServiciosAdminController@enable')->name('hospital.enableDark');

/* Route::get('hospital/detalle/paciente/{id}', 'hospital\HospitalController@detalles')->name('hospital.detallep');
Route::get('hospital/detalle/primerpaso', 'hospital\HospitalController@primer_paso')->name('hospital.primerpaso');
Route::get('hospital/detalle/segundopaso', 'hospital\HospitalController@segundo_paso')->name('hospital.segundopaso');
Route::get('hospital/detalle/tercerpaso', 'hospital\HospitalController@tercer_paso')->name('hospital.tercerpaso');
Route::get('hospital/detalle/cuartopaso', 'hospital\HospitalController@cuarto_paso')->name('hospital.cuartopaso'); */

// formulario 005
Route::get('hospital/form_005/index/{id_solicitud}', 'hospital\Formulario005Controller@index_005')->name('formulario005.index_005');
Route::get('hospital/formu_0051/f5_evolucion/{id}', 'hospital\Formulario005Controller@f5_evolucion')->name('formulario005.f5_evolucion');

Route::match(['get', 'post'], 'hospital/formu_005/guardar_evolucion/{id_evol}', 'hospital\Formulario005Controller@guardar_evolucion')->name('formulario005.guardar_evolucion');

Route::get('hospital/formu_005/f5_diagnostico', 'hospital\Formulario005Controller@f5_diagnostico')->name('formulario005.f5_diagnostico');
Route::get('hospital/formu_005/f5_medidas_generales', 'hospital\Formulario005Controller@f5_medidas_generales')->name('formulario005.f5_medidas_generales');
Route::get('hospital/formu_005/f5_tratamiento', 'hospital\Formulario005Controller@f5_tratamiento')->name('formulario005.f5_tratamiento');
Route::get('hospital/formu_005/f5_plan', 'hospital\Formulario005Controller@f5_plan')->name('formulario005.f5_plan');
Route::get('hospital/formu_005/f5_medicamentos/{id}', 'hospital\Formulario005Controller@f5_medicamentos')->name('formulario005.f5_medicamentos');
Route::get('hospital/formu_005/f5_examenes', 'hospital\Formulario005Controller@f5_examenes')->name('formulario005.f5_examenes');
Route::get('hospital/formu_005/f5_salas', 'hospital\Formulario005Controller@f5_salas')->name('formulario005.f5_salas');
Route::post('hospital/cuarto/obtener/paciente', 'hospital\CuartoController@getSources')->name('cuartos.get_paciente');
Route::get('hospital/cuarto/modal', 'hospital\CuartoController@modal_paciente')->name('cuartos.modal_paciente');
Route::get('hospital/cuarto/paciente/{id}/{id_s}', 'hospital\CuartoController@asignar_paciente')->name('cuartos.asignar_paciente');

Route::get('hospital/evolucion/enfermeria/{id}', 'hospital\Formulario005Controller@evolucion_enfermeria')->name('formulario005.evolucion_enfermeria');
Route::get('hospital/evolucion/enfermeria/crear_evolucion_enfer/{id}', 'hospital\Formulario005Controller@crear_evolucion_enfer')->name('formulario005.crear_evolucion_enfer');
Route::get('hospital/evolucion/enfermeria/detalle/{id}', 'hospital\Formulario005Controller@evol_detalle_enfermeria')->name('formulario005.evol_detalle_enfermeria');
Route::match(['get', 'post'], 'hospital/evolucion/enfermeria/guardar/evolucion/{id_evol}', 'hospital\Formulario005Controller@guardar_evolucion_enfermeria')->name('formulario005.guardar_evolucion_enfermeria');

Route::post('hospitalizacion/guardar/paciente', 'hospital\CuartoController@save_paciente')->name('hospitalizacion.store');
Route::get('hospitalizacion/paciente/{id}/{id_d}', 'hospital\CuartoController@show_cama')->name('hospitalizacion.show');
Route::get('hospitalizacion/get/genericos', 'hospital\HospitalController@genericos')->name('hospitalizacion.genericos');

Route::get('hospital/formu_005/evolucion/detalle/{id}', 'hospital\Formulario005Controller@evolucion_detalle')->name('formulario005.evolucion_detalle');

Route::get('hospital/receta/detalle/{id}', 'hospital\Formulario005Controller@receta_detalle')->name('formulario005.receta_detalle');

Route::get('hospital/formu_005/crear_evolucion/{id}', 'hospital\Formulario005Controller@crear_evolucion')->name('formulario005.crear_evolucion');

Route::get('hospital/receta/crear_receta/{id}', 'hospital\Formulario005Controller@crear_receta')->name('formulario005.crear_receta');
Route::match(['get', 'post'], 'hospital/receta/formu_005/f05_medicina_guardar/{id_rec}', 'hospital\Formulario005Controller@f05_medicina_guardar')->name('formulario005.f05_medicina_guardar');
Route::get('hospital/formu_005/eliminar_medicina/{id_detalle}', 'hospital\Formulario005Controller@eliminar_medicina')->name('formulario005.eliminar_medicina');
Route::match(['get', 'post'], 'hospital/form005/editar_medicina/{id_detalle}', 'hospital\Formulario005Controller@editar_medicina')->name('formulario005.editar_medicina');
// pdf formulario 005
Route::match(['get', 'post'], 'hospital/form005/pdf_formulario005', 'hospital\Formulario005Controller@pdf_formulario005', 'form_005.pdf');
Route::get('hospital/hospitalizacion/buscar_hospitalizadodescargo_medicinas/{id_solic}', 'hospital\hospitalizacion\HospitalizacionController@descargo_medicina')->name('hospitalizacion.descargo_medicina');

Route::get('hospital/receta/descargo/enfermeria/{id}/{id_solicitud}', 'hospital\hospitalizacion\HospitalizacionController@descargo_enfermeria_detalle')->name('hospitalizacion.descargo_enfermeria_detalle');

Route::post('hospital/receta/descargo/enfermeria/detalle/store', 'hospital\hospitalizacion\HospitalizacionController@descargo_enfermeria_detalle_store')->name('hospitalizacion.descargo_enfermeria_detalle_store');

Route::get('hospital/free/habitation', 'hospital\CuartoController@freehabitation')->name('hospitalizacion.freehabitation');

// quirofano
Route::get('hospital/quirofano/{tipo}', 'hospital\QuirofanoController@quirofano')->name('hospital.quirofano');

Route::get('hospital/quirofano/buscador/{tipo}/{id_solicitud}', 'hospital\QuirofanoController@quirofano_paciente')->name('quirofano.quirofano_paciente');
Route::match(['get', 'post'], 'hospital/quirofano/buscar_quirofano/{tipo}', 'hospital\QuirofanoController@buscar_quirofano')->name('quirofano.buscar_quirofano');
Route::get('hospital/quirofano/ordenes/funcionales/{id_solicitud}', 'hospital\QuirofanoController@index_funcionales')->name('quirofano.index_funcionales');
Route::get('hospital/quirofano/crear_funcionales/{id}', 'hospital\QuirofanoController@crear_funcionales')->name('formulario005.crear_funcionales');
Route::get('hospital/quirofano/armar/estudio/{id_solicitud}', 'hospital\QuirofanoController@armar_estudio')->name('quirofano.armar_estudio');
Route::get('hospital/quirofano/ver/estudio/{id_solicitud}', 'hospital\QuirofanoController@ver_estudio')->name('quirofano.ver_estudio');
// quirofano-ecografia
Route::get('hospital/quirofano/ecografia/{id_solicitud}', 'hospital\QuirofanoController@ecografia')->name('quirofano.ecografia');
Route::get('hospital/quirofano/historial/', 'hospital\QuirofanoController@editar')->name('quirofano.editar_ecografia');
Route::get('hospital/quirofano/historial/{id_procedimiento}/{id_paciente}', 'hospital\QuirofanoController@editar');

Route::get('hospital/ingresar_evolucion/editar', 'hospital\QuirofanoController@editar_evolucion')->name('quirofano.editar_evolucion');
Route::get('hospital/ingresar_evolucion/editar/{id_procedimiento}/{id_paciente}', 'hospital\QuirofanoController@editar_evolucion');

Route::get('hospital/agregar_evolucion/', 'hospital\QuirofanoController@agregar_evolucion')->name('quirofano.agregar_evolucion');
Route::get('hospital/agregar_evolucion/{id_procedimiento}', 'hospital\QuirofanoController@agregar_evolucion');

Route::get('hospital/funcional/editar/', 'hospital\QuirofanoController@editar_funcional')->name('quirofano.editar_funcional');
Route::get('hospital/funcional/editar/{id_procedimiento}/{id_paciente}', 'hospital\QuirofanoController@editar_funcional');

Route::get('hospital/asignar/quirofano', 'hospital\CuartoController@modal_quirofano')->name('hospitalizacion.modal_quirofano');
Route::post('hospital/asignar/save/quirofano', 'hospital\CuartoController@cirugia')->name('hospitalizacion.save_quirofano');

// Farmacia
Route::match(['get', 'post'], 'hospital/master_farmacia', 'hospital\FarmaciaController@index')->name('hospital.master_farmacia');
Route::match(['get', 'post'], 'hospital/farmacia/buscar_farmcia', 'hospital\FarmaciaController@buscar_medicina')->name('farmacia.buscar_medicina');

// Examenes
Route::get('hospital/mostrar/examenes/{id}', 'hospital\Formulario005Controller@cargar_examenes')->name('hospitalizacion.cargar_examenes');
Route::get('hospital/mostrar/resultados/imprimir/{id_orden}', 'hospital\Formulario005Controller@imprimir_resultado')->name('hospitalizacion.imprimir');
Route::get('hospital/mostrar/resultados/puede/{id_orden}', 'hospital\Formulario005Controller@puede_imprimir')->name('hospitalizacion.puede_imprimir');
Route::get('hospital/mostrar/factura', 'hospital\FarmaciaController@invoice')->name('hospital.invoice');
// Epicrisis
Route::get('hospital/quirofano/index/{id_solicitud}', 'hospital\QuirofanoController@index_epicrisis')->name('quirofano.index_epi');
Route::get('hospital/quirofano/epicrisis/{id}', 'hospital\QuirofanoController@epicrisis')->name('quirofano.epicrisis');
Route::get('hospital/epicrisis/crear_epicrisis/{id}', 'hospital\QuirofanoController@crear_epicrisis')->name('quirofano.crear_epicrisis');
Route::get('hospital/quirofano/epicrisis/detalle/{id}', 'hospital\QuirofanoController@epicrisis_detalle')->name('quirofano.detalle_epicrisis');
Route::match(['get', 'post'], 'hospital/epicrisis/guardar_epicrisis/{id_epi}', 'hospital\QuirofanoController@guardar_epicrisis')->name('quirofano.guardar_epicrisis');

// Modal Enfermeria
Route::get('hospital/modal/imagenes', 'hospital\CuartoController@modal_imagenes')->name('cuartos.imagenes');
Route::post('hospital/modal/guardar_imagenes', 'hospital\CuartoController@guardar_imagenes')->name('cuartos.guardar_imagenes');

// Tipo Emergencia
Route::get('hospital/tipo_emergencia/index', 'hospital\TipoEmergenciaController@index')->name('tipoemergencia.index');
Route::get('hospital/tipo_emergencia/crear', 'hospital\TipoEmergenciaController@crear')->name('tipoemergencia.crear');
Route::post('hospital/tipo_emergencia/guardar', 'hospital\TipoEmergenciaController@guardar')->name('tipoemergencia.guardar');
Route::get('hospital/tipo_emergencia/editar/{id}', 'hospital\TipoEmergenciaController@editar')->name('tipoemergencia.editar');
Route::match(['get', 'post'], 'hospital/tipo_emergencia/actualizar', 'hospital\TipoEmergenciaController@actualizar')->name('tipoemergencia.actualizar');
Route::match(['get', 'post'], 'hospital/tipo_emergencia/eliminar/tipo/{id_tipo}', 'hospital\TipoEmergenciaController@eliminar_tipoe')->name('tipoemergencia.eliminar_tipoe');

// Prioridad Emergencia
Route::get('hospital/prioridad_emergencia/index', 'hospital\PrioridadEmergenciaController@index')->name('prioridademergencia.index');
Route::get('hospital/prioridad_emergencia/crear', 'hospital\PrioridadEmergenciaController@crear')->name('prioridademergencia.crear');
Route::post('hospital/prioridad_emergencia/guardar', 'hospital\PrioridadEmergenciaController@guardar')->name('prioridademergencia.guardar');
Route::match(['get', 'post'], 'hospital/prioridad_emergencia/editar/{id}', 'hospital\PrioridadEmergenciaController@editar')->name('prioridademergencia.editar');
Route::match(['get', 'post'], 'hospital/prioridad/actualizar', 'hospital\PrioridadEmergenciaController@actualizar_pri')->name('prioridad.actualizar_pri');
Route::match(['get', 'post'], 'hospital/prioridad_emergencia/eliminar/prioridade/{id_tipo}', 'hospital\PrioridadEmergenciaController@eliminar_prioridade')->name('prioridademergencia.eliminar_prioridad');
// guardarImagen
Route::post('guardar/imagen/infermeria/{id}', 'hospital\Formulario005Controller@guardarimagen')->name('tipoemergencia.guardarimagen');
Route::get('ver/pdf/evolucion/{id}', 'hospital\Formulario005Controller@verpdf')->name('tipoemergencia.verpdf');
Route::match(['get', 'post'], 'ag_hospitalizacion/{id_sala}', 'hospital\CuartoController@agenda_hospital')->name('cuarto.agenda_hospital');
// guardar alergia
Route::post('guardar/alergia/paciente', 'hospital\QuirofanoController@guardar_alergia')->name('quirofano.guardar_alergia');
// uci
Route::match(['get', 'post'], 'hospital/uci/index', 'hospital\uci\UciController@index')->name('uci.index');
Route::match(['get', 'post'], 'hospital/uci/index_uci/{id_solicitud}', 'hospital\uci\UciController@index_uci')->name('uci.index_uci');
// ingreso por cualquier modulo
Route::match(['get', 'post'], 'hospital/ingreso/modulos', 'hospital\HospitalController@ingreso_modulos')->name('hospital.ingreso_modulos');
Route::match(['get', 'post'], 'hospital/ingreso/admision/{id_paso}', 'hospital\HospitalController@admision')->name('hospital.admision');

// principal modulos
Route::match(['get', 'post'], 'hospital/modulos/index/{id_paso}', 'hospital\HospitalController@index_modulos')->name('hospital.index_modulos');
Route::match(['get', 'post'], 'hospital/traspaso/modulo/{id_solicitud}', 'hospital\HospitalController@traspaso_modulo')->name('hospital.traspaso_modulo');
Route::match(['get', 'post'], 'hospital/guardar/traspaso', 'hospital\HospitalController@guardar_traspaso')->name('hospital.guardar_traspaso');
Route::match(['get', 'post'], 'hospital/subir/formularios/{id_solicitud}', 'hospital\HospitalController@subir_formularios')->name('hospital.subir_formularios');
Route::match(['get', 'post'], 'hospital/guardar/formularios', 'hospital\HospitalController@guardar_formularios')->name('hospital.guardar_formularios');
Route::match(['get', 'post'], 'hospital/detalle/formularios/{id_solicitud}', 'hospital\HospitalController@detalle_formularios')->name('hospital.detalle_formularios');
Route::get('hospital/descarga/formulario/{name}', 'hospital\HospitalController@descarga_formulario')->name('hospital.descarga_formulario');
Route::get('hospital/eliminar/formulario/{id}', 'hospital\HospitalController@eliminar_formulario')->name('hospital.eliminar_formulario');
Route::match(['get', 'post'], 'hospital/ver/formulario/{id}', 'hospital\HospitalController@ver_formulario')->name('hospital.ver_formulario');

Route::match(['get', 'post'], 'hospitalizacion/detalle/planilla/{id_proc}', 'hospital\hospitalizacion\HospitalizacionController@detalle_planilla')->name('hospitalizacion.detalle_planilla');

Route::match(['get', 'post'], 'hospital/interconsulta/buscar/tarifario', 'hospital\emergencia\DecimoPasoController@buscarTarifario')->name('decimopaso.buscarTarifario');

Route::match(['get', 'post'], 'hospital/interconsulta/inter_tarifario_det/{id}', 'hospital\emergencia\DecimoPasoController@inter_tarifario_det')->name('decimopaso.inter_tarifario_det');

Route::match(['get', 'post'], 'hospital/interconsulta/guardar/det_tarifario', 'hospital\emergencia\DecimoPasoController@guardar_det_tarifario')->name('decimopaso.guardar_det_tarifario');

// Reporte de enfermeria
Route::get('hospital/enfermeria_reporte/pdf_medicamentos/{id}', 'hospital\enfermeria\ReportesEnfermeriaController@pdf_medicamentos')->name('hospital.pdf_medicamentos');

Route::get('hospital/notas/enfermeria/{id}', 'hospital\Formulario005Controller@notas_enfermeria')->name('formulario005.notas_enfermeria');
Route::get('hospital/crear/nota/enfermeria/{id}', 'hospital\Formulario005Controller@crear_nota_enfermeria')->name('formulario005.crear_nota_enfermeria');
Route::match(['get', 'post'], 'hospital/nota/enfermeria/detalle/{id}', 'hospital\Formulario005Controller@nota_enfermeria_detalle')->name('formulario005.nota_enfermeria_detalle');
Route::match(['get', 'post'], 'hospital/guardar/nota/enfermeria/{id}', 'hospital\Formulario005Controller@guardar_nota_enfermeria')->name('formulario005.guardar_nota_enfermeria');

// LISTADO PACIENTE
Route::match(['get', 'post'], 'hospital/listado/paciente/{id_paciente}', 'hospital\HospitalController@listado_paciente')->name('hospial.listado_paciente');
Route::get('hospital/vtlaboratorio/modal/{id}', 'hospital\HospitalController@modal_laboratorio')->name('hospital.modal_laboratorio');
Route::get('hospital/consultas_listado/modal/{id}', 'hospital\HospitalController@modal_consultas')->name('hospital.modal_consultas');
Route::match(['get', 'post'], 'hospital/imagenes/view/{id_solicitud}', 'hospital\ImagenesController@viewImagenes')->name('hospital.viewImagenes');
Route::post('hospital/imagenes/store/image/', 'hospital\ImagenesController@storeImage')->name('hospital.storeImage');
Route::get('hospital/vtimagen/modal/{id}', 'hospital\HospitalController@modal_estudio')->name('hospital.modal_estudio');

Route::match(['get', 'post'], 'hospital/imagenes/view/box/fotos', 'hospital\ImagenesController@verFotosBox')->name('hospital.imagenes.box.verFotos');

Route::get('historiaclinica/video/mostar_foto/eliminar2/{id}', 'hospital\ImagenesController@mostrar_foto_eliminar2')->name('hc4_mostrar_foto_eliminar2');
// pdf nuevos hospital
Route::get('formulario007/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario007')->name('formulario.007');
Route::get('informe007/pdf/{id}', 'pdf_hospital\Formulario007Controller@informe007')->name('informe.007');
Route::get('formulario005/pdf', 'pdf_hospital\Formulario007Controller@formulario005')->name('formulario.005');
Route::match(['get', 'post'], 'formulario12B/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario12B')->name('formulario.12B'); // // este walter 002
Route::get('formulario12A/pdf', 'pdf_hospital\Formulario007Controller@formulario12A')->name('formulario.12A');
Route::get('formulario10B/pdf', 'pdf_hospital\Formulario007Controller@formulario10B')->name('formulario.10B');
Route::get('formulario10A/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario10A')->name('formulario.10A');
Route::get('formulario017/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario017')->name('formulario.017.por_id'); // REALIZADO
Route::match(['get', 'post'], 'formulario006/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario006')->name('formulario.006'); // realizado->revision
Route::match(['get', 'post'], 'formulario017/pdf', 'pdf_hospital\Formulario007Controller@formulario017')->name('formulario.017'); // REALIZADO

Route::get('hospital/formulario001/pdf/{id}', 'hospital\FormulariosPDFController@formulario001_pdf')->name('formulario.001');
Route::get('hospital/formulario024/pdf', 'hospital\FormulariosPDFController@formulario024_pdf')->name('formulario.024');
Route::get('hospital/consentimiento/{id_paciente}/{id_empresa}/{id_agenda}', 'hospital\FormulariosPDFController@documentos_pdf')->name('consentimiento.documentos_pdf');
Route::post('hospital/consentimiento/descargar_pdf/{id_empresa}', 'hospital\FormulariosPDFController@descargar_pdf_consentimiento')->name('consentimiento.descargar_pdf');
Route::get('planillar_hospital/{id}', 'hospital\HospitalController@planillar')->name('hospital.planillar');
Route::get('planillar_hospital/detalle/{id}', 'hospital\HospitalController@planillar_detalle')->name('hospital.planillar_detalle');
Route::get('planillar_hospital/detalle/aplicar/{id}', 'hospital\HospitalController@planillar_detalle_aplicar')->name('hospital.planillar_detalle_aplicar');
Route::get('planillar_hospital/todos/detalle/aplicar/{id}', 'hospital\HospitalController@aplicar_todos')->name('hospital.aplicar_todos');

// RECIBO DE COBRO
Route::get('rc_hospital/crear/{id_solicitud}', 'hospital\HospitalController@crear_recibo_hospital')->name('hospital.crear_recibo_hospital');
Route::get('rc_hospital/listar/{id_solicitud}', 'hospital\HospitalController@listar_recibo_hospital')->name('hospital.listar_recibo_hospital');
Route::get('rc_hospital/editar/{id_solicitud}', 'hospital\HospitalController@editar_recibo_hospital')->name('hospital.editar_recibo_hospital');
Route::get('rc_hospital/eliminar/{id_solicitud}', 'hospital\HospitalController@eliminar_recibo_hospital')->name('hospital.eliminar_recibo_hospital');

Route::get('rc_hospital/detalles/{id}', 'hospital\HospitalController@detalles_rc')->name('hospital.detalles_rc');
Route::get('rc_hospital/forma_pago/{id}', 'hospital\HospitalController@formas_pg_rc')->name('hospital.formas_pg_rc');
Route::get('rc_hospital/carga_planilla/recibo/{id_detalle}/{id_recibo}', 'hospital\HospitalController@cargar_detalle_recibo')->name('hospital.cargar_detalle_recibo');
// //
Route::get('anamnesis/anamnesis/crear/{id}', 'pdf_hospital\Formulario007Controller@crear_anamnesis')->name('formato.anamnesis_crear');
Route::get('formulario002/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario002')->name('formulario.002');
Route::match(['get', 'post'], 'anamnesis/anamnesis/002/{id}', 'pdf_hospital\Formulario007Controller@anamenesis_index')->name('formato.anamenesis_index');
Route::post('anamnesis/formulario/guardar', 'pdf_hospital\Formulario007Controller@anamenesis_guardar')->name('formato.anamenesis_guardar');
Route::post('anamnesis/formulario/actualiza_evo/guardar', 'pdf_hospital\Formulario007Controller@anamenesis_guardar_evo')->name('formulario007.anamenesis_guardar_evo');

Route::match(['get', 'post'], 'formatos_modal_24/{id}', 'pdf_hospital\Formulario007Controller@modal_formatos_24')->name('formulario007.modal_formatos_24');
// / formulario 019
Route::get('formulario/019/{id_historia}', 'pdf_hospital\Formulario007Controller@pdf_019')->name('formato.pdf_019');

// / formulario 007 completo
Route::match(['get', 'post'], 'formulario007_unificado/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario007_unificado')->name('formulario007.formulario007_unificado');
// pdf sucursales
Route::get('iess_informe_tecnico/pdf/{id}', 'pdf_hospital\Formulario007Controller@informe_tecnico')->name('formulario007.informe_tecnico');
// subir a sucursales
Route::get('iess_informe_tecnico/vista/{id}', 'pdf_hospital\Formulario007Controller@formato_tecnico')->name('formulario007.formato_tecnico');
Route::post('iess_informe_tecnico/guardar', 'pdf_hospital\Formulario007Controller@guardar_auditoria')->name('formulario007.guardar_auditoria');
Route::match(['get', 'post'], 'iess_informe_tecnico/buscarProtocolo', 'pdf_hospital\Formulario007Controller@buscarProtocolo')->name('formulario007.buscarProtocolo');
Route::get('iess_informe_tecnico/eliminar_protocolo/{id}', 'pdf_hospital\Formulario007Controller@eliminar_protocolo')->name('formulario007.eliminar_protocolo');
