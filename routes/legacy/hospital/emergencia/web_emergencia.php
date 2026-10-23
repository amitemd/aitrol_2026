<?php

Route::get('hospital/detalle/paciente/{id}', 'hospital\HospitalController@detalles')->name('hospital.detallep');
Route::get('hospital/detalle/primerpaso/{id}', 'hospital\emergencia\PrimerPasoController@index')->name('hospital.primerpaso');
Route::get('hospital/detalle/segundopaso/{id}', 'hospital\emergencia\SegundoPasoController@index')->name('hospital.segundopaso');

Route::post('hospital/detalle/segundopaso/store', 'hospital\emergencia\SegundoPasoController@store')->name('hospital.segundopaso_store');

Route::get('hospital/detalle/tercerpaso/{id_sol}', 'hospital\emergencia\TercerPasoController@index')->name('hospital.tercerpaso');
Route::post('hospital/detalle/tercerpaso/store', 'hospital\emergencia\TercerPasoController@store')->name('hospital.tercerpaso_store');

Route::get('hospital/detalle/cuartopaso/{id}', 'hospital\emergencia\CuartoPasoController@index')->name('hospital.cuartopaso');
Route::post('hospital/detalle/cuartopaso/store', 'hospital\emergencia\CuartoPasoController@store')->name('hospital.cuartopaso_store');

Route::get('hospital/detalle/septimopaso/{id_sol}', 'hospital\emergencia\SeptimoPasoController@septimopaso')->name('hospital.septimopaso');
Route::post('hospital/emergencia/septimopaso/store', 'hospital\emergencia\SeptimoPasoController@store')->name('hospital.septimopaso_store');

Route::get('hospital/detalle/octavopaso/{id}', 'hospital\emergencia\OctavoPasoController@index')->name('hospital.octavopaso');
Route::post('hospital/emergencia/octavo/store', 'hospital\emergencia\OctavoPasoController@save')->name('hospital.octavosave');
Route::post('hospital/store_octavo', 'hospital\emergencia\OctavoPasoController@store_octavo')->name('hospital.store_octavo');

Route::get('hospital/detalle/quintopaso/{id_sol}', 'hospital\emergencia\QuintoPasoController@quintopaso')->name('hospital.quintopaso');
Route::post('hospital/emergencia/quintopaso/store', 'hospital\emergencia\QuintoPasoController@store')->name('hospital.quintopaso_store');

Route::get('hospital/detalle/sextopaso/{id_sol}', 'hospital\emergencia\SextoPasoController@sextopaso')->name('hospital.sextopaso');
Route::post('hospital/detalle/sextopaso/store', 'hospital\emergencia\SextoPasoController@store')->name('hospital.sextopaso_store');

Route::get('hospital/detalle/novenopaso/{id}', 'hospital\emergencia\NovenoPasoController@novenopaso')->name('hospital.novenopaso');
Route::post('hospital/detalle/novenopaso/store', 'hospital\emergencia\NovenoPasoController@store')->name('hospital.novenopaso_store');

Route::get('hospital/emergencia/decimopaso/laboratorio/{id}', 'hospital\emergencia\DecimoPasoController@laboratorio')->name('hospital.decimo_laboratorio');

Route::get('hospital/emergencia/decimopaso/procedimiento/{id}/{tipo}', 'hospital\emergencia\DecimoPasoController@procedimiento')->name('hospital.decimo_procedimiento');
Route::get('hospital/emergencia/decimopaso/procedimiento/detalle/solic/{id}', 'hospital\emergencia\DecimoPasoController@procedimiento_detalle')->name('hospital.decimo_procedimiento_detalle');
Route::get('hospital/emergencia/decimopaso/procedimiento/editar/solic/{id}', 'hospital\emergencia\DecimoPasoController@procedimiento_editar')->name('decimopaso.procedimiento_editar');
Route::post('hospital/emergencia/decimopaso/procedimiento/editar/solic/varios_px/id', 'hospital\emergencia\DecimoPasoController@procedimiento_actualizar_pxs')->name('decimopaso.procedimiento_actualizar_pxs');

Route::get('hospital/emergencia/decimopaso/procedimiento/editar/solic/varios_px/imprimir/{id}', 'hospital\emergencia\DecimoPasoController@imprimir_orden_funcional_hospital')->name('decimopaso.imprimir_orden_funcional_hospital');

Route::post('hospital/emergencia/decimopaso/procedimiento/editar/solic/id/update', 'hospital\emergencia\DecimoPasoController@procedimientoendo_actualizar')->name('decimopaso.procedimientoendo_actualizar');

Route::get('hospital/emergencia/decimopaso/procedimiento/crear/{id}/{tipo}', 'hospital\emergencia\DecimoPasoController@procedimiento_crear')->name('decimopaso.procedimiento_crear');

// ELIminar orden de lab
Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/eliminar/{id}', 'hospital\emergencia\DecimoPasoController@elimar_orden')->name('decimopaso.eliminar_orden');

Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/{id}', 'hospital\emergencia\DecimoPasoController@laboratorio_detalle')->name('hospital.decimo_laboratorio_detalle');

Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/solic/editar/pub/{id}', 'hospital\emergencia\DecimoPasoController@laboratorio_orden_editar_pb')->name('hospital.decimo_laboratorio_editar_pb');
Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/{id}', 'hospital\emergencia\DecimoPasoController@laboratorio_orden_crear_pb')->name('hospital.decimo_laboratorio_crear_pb');
Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/agregar/examen/{id_orden}/{id_examen}', 'hospital\emergencia\DecimoPasoController@laboratorio_orden_crear_examen')->name('hospital.decimo_laboratorio_crear_examen');
Route::get('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/quitar/examen/{id_orden}/{id_examen}', 'hospital\emergencia\DecimoPasoController@laboratorio_orden_quitar_examen')->name('hospital.decimo_laboratorio_quitar_examen');
// Buscar examenes publicos otros
Route::post('hospital/emergencia/decimopaso/laboratorio/buscar/examen/publico/otros', 'hospital\emergencia\DecimoPasoController@examenes_buscar_publicos_otros')->name('decimopaso.examenes_buscar_publicos_otros');
Route::get('hospital/emergencia/decimopaso/laboratorio/listar/examenes/publicos/{id_orden}', 'hospital\emergencia\DecimoPasoController@laboratorio_listar_otros')->name('hospital.decimo_laboratorio_listar_otros');
// PARTICULARES
Route::get('particulares/hospital/emergencia/decimopaso/laboratorio/crear/{id}', 'hospital\emergencia\DecimoPasoController@laboratorio_orden_crear_part')->name('decimopaso.laboratorio_orden_crear_part');
Route::match(['get', 'post'], 'particulares/decimopaso/emergencia/laboratorio/buscar/examen/{solic}', 'hospital\emergencia\DecimoPasoController@buscar_examenes')->name('decimopaso.buscar_examenes');

Route::get('hospital/detalle/onceavopaso/{id_sol}', 'hospital\emergencia\OnceavoPasoController@onceavopaso')->name('hospital.onceavopaso');
Route::match(['get', 'post'], 'hospital/cie10/agregar', 'hospital\emergencia\OnceavoPasoController@agregar_cie10')->name('hospital.agregar_cie10');
Route::match(['get', 'post'], 'cie/cargar_tabla_cie', 'hospital\emergencia\OnceavoPasoController@cargar_tabla_cie')->name('hospital.cargar_tabla_cie_hos');

Route::get('hospital/detalle/doceavopaso/{id_sol}', 'hospital\emergencia\DoceavoPasoController@doceavopaso')->name('hospital.doceavopaso');
Route::match(['get', 'post'], 'hospital/cie10/agregar_alta', 'hospital\emergencia\DoceavoPasoController@agregar_cie10_alta')->name('hospital.agregar_cie10_alta');
Route::match(['get', 'post'], 'cie/cargar_tabla_cie_alta/', 'hospital\emergencia\DoceavoPasoController@cargar_tabla_cie_alta')->name('hospital.cargar_tabla_cie_alta');

Route::get('hospital/detalle/treceavopaso/{id_sol}', 'hospital\emergencia\TreceavoPasoController@treceavopaso')->name('hospital.treceavopaso');
Route::post('hospital/detalle/treceavopaso/store', 'hospital\emergencia\TreceavoPasoController@store')->name('hospital.treceavopaso_store');

Route::get('hospital/detalle/catorceavo', 'hospital\emergencia\CatorceavoPasoController@index')->name('hospital.catorceavo');
Route::post('hospital/guardar/detalle/catorceavo', 'hospital\emergencia\CatorceavoPasoController@store')->name('hospital.guardar_catorceavo');

// PDF FORMULARIO008
Route::get('hospital/emergencia/formulario008_pdf/{id}', 'hospital\Formulario008Controller@formulario008_pdf')->name('hospital.formulario008_pdf');
// PDF FORMULARIO005
Route::get('hospital/habitacion/formulario005_pdf/{id}', 'hospital\hospitalizacion\HospitalizacionController@formulario005_pdf')->name('habitacion.formulario005_pdf');

// master Hospitalizacion

Route::get('hospital/hospitalizacion/index', 'hospital\hospitalizacion\HospitalizacionController@master')->name('hospitalizacion.master');
Route::match(['get', 'post'], 'hospital/hospitalizacion/buscar_hospitalizado', 'hospital\hospitalizacion\HospitalizacionController@buscar_hospitalizado')->name('hospitalizacion.buscar_hospitalizado');
Route::post('hospital/guardar/treavopaso', 'hospital\emergencia\TreceavoPasoController@store')->name('hospital.guardar_terceavo');
// 13VO PASO
Route::match(['get', 'post'], 'hospital/emergencia/13vopaso/guardar_medicina/{solic}', 'hospital\emergencia\TreceavoPasoController@guardar_medicina')->name('13vopaso.guardar_medicina');

Route::match(['get', 'post'], 'hospital/emergencia/13vopaso/receta/actualizar/doctor/{receta}', 'hospital\emergencia\TreceavoPasoController@actualizar_doctor')->name('13vopaso.actualizar_doctor');

Route::get('hospital/emergencia/decimopaso/interconsulta/{id}', 'hospital\emergencia\DecimoPasoController@interconsulta')->name('decimo.interconsulta');

Route::get('hospital/emergencia/decimopaso/interconsulta/crear/{id}', 'hospital\emergencia\DecimoPasoController@crear_interconsulta')->name('decimo.crear_interconsulta');

Route::get('hospital/emergencia/decimopaso/interconsulta/editar/{id_inter}', 'hospital\emergencia\DecimoPasoController@editar_interconsulta')->name('decimo.editar_interconsulta');

Route::get('hospital/emergencia/decimopaso/interconsulta/detalle/{id_inter}', 'hospital\emergencia\DecimoPasoController@detalle_interconsulta')->name('decimo.detalle_interconsulta');

Route::post('hospital/emergencia/decimopaso/interconsulta/actualizar', 'hospital\emergencia\DecimoPasoController@actualizar_interconsulta')->name('decimo.actualizar_interconsulta');

Route::get('hospital/emergencia/decimopaso/interconsulta/formulario/solicitud/interconsulta/{id}', 'hospital\emergencia\DecimoPasoController@imprimir_interconsulta')->name('decimo.imprimir_interconsulta');

Route::match(['get', 'post'], 'hospital/emergencia/decimopaso/interconsulta/cie10/{id_inter}', 'hospital\emergencia\DecimoPasoController@agregar_cie10inter')->name('decimo.agregar_cie10inter');

Route::get('cie/cargar_tabla_cie_in/{id_inter}', 'hospital\emergencia\DecimoPasoController@cargar_tabla_cie')->name('decimo.cargar_tabla_cie');
Route::get('hosp/decimo/cie/eliminar_cie/{id_inter}', 'hospital\emergencia\DecimoPasoController@elimar_cie10')->name('decimo.elimar_cie10');

Route::get('hospital/modulos/buscador/index', 'hospital\HospitalController@buscador_vista')->name('buscador.ajax_emergencia');

// PDF FORMULARIO008 NUEVO

Route::get('laboratorio/formulario008_nuevo/pdf/{id}', 'hospital\Formulario008Controller@pdf_formulario008_nuevo')->name('hospital.nuevo.formulario008_pdf');
