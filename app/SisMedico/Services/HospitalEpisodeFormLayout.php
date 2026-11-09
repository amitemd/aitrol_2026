<?php

namespace Sis_medico\Services;

/**
 * Layouts for episode drafts, separate from the signed/native clinical reports.
 * Section names follow the Borja reference forms where supplied. Free-text
 * values stay together: we never infer checkboxes, diagnoses or measurements.
 */
class HospitalEpisodeFormLayout
{
    public static function guidance($code)
    {
        $instructions = [
            '001' => 'Admisión registra el ingreso y completa el alta-egreso cuando termina la atención.',
            '002' => 'El profesional registra la consulta externa, el examen físico, los diagnósticos y las prescripciones.',
            '003' => 'El médico completa la valoración inicial; puede prepararse antes o después de asignar cama.',
            '005' => 'El médico registra la evolución y las prescripciones durante la atención.',
            '006' => 'El médico completa la epicrisis al egreso, con el resumen de la atención y las indicaciones de alta.',
            '007' => 'El médico solicitante completa el motivo, la especialidad y el cuadro clínico de la interconsulta.',
            '007B' => 'El especialista que responde completa los hallazgos, el criterio clínico y el plan de la interconsulta.',
            '008' => 'El médico de Emergencia completa la evaluación, los diagnósticos, el tratamiento y la condición de egreso.',
            '010A' => 'Use «Solicitar laboratorio» para crear o completar la orden del episodio. Los exámenes seleccionados llenan automáticamente el 010A.',
            '010B' => 'Use «Ingresar resultados» y abra el área de la orden en Control de laboratorio. Al guardar, los resultados, unidades y referencias aparecen en el 010B; se indica si están pendientes de certificar.',
            '011' => 'Incorpore la referencia de resultados o documentos diagnósticos agregados a la historia clínica.',
            '012A' => 'El médico solicitante registra el estudio de imagen, la indicación clínica y la prioridad.',
            '012B' => 'Se registra el informe de imagenología: estudio, técnica, hallazgos y conclusión.',
            '013A' => 'El médico solicitante identifica la muestra, el sitio anatómico y el diagnóstico presuntivo.',
            '013B' => 'Se registra el informe de Patología: muestra, macroscopia, microscopia y diagnóstico.',
            '016' => 'El equipo de obstetricia o neonatología registra la condición, atención inmediata y destino del recién nacido.',
            '017' => 'El cirujano completa el procedimiento, los hallazgos y las complicaciones después de la intervención.',
            '018' => 'El anestesiólogo completa la valoración, el riesgo y el plan antes del procedimiento.',
            '018A' => 'El anestesiólogo registra la técnica, los medicamentos, la monitorización y los incidentes durante la anestesia.',
            '019' => 'El anestesiólogo registra la recuperación, la analgesia y el destino del paciente.',
            '020' => 'Enfermería, el médico o Sistemas registra constantes por fecha, turno y hora; antropometría, ingresos y eliminaciones en ml, balance de 24 horas, cuidados y dispositivos.',
            '021' => 'Enfermería registra la valoración, los problemas, cuidados, respuesta del paciente y educación brindada.',
            '022' => 'Enfermería, el médico o Sistemas registra alergias, los medicamentos prescritos y cada administración con fecha, hora y responsable. Es el formulario vigente que reemplaza al antiguo Kardex 015/2007.',
            '120' => 'Enfermería registra las intervenciones realizadas, la respuesta del paciente y los cuidados pendientes para asegurar continuidad.',
            '024' => 'Seleccione anestesia general/inhalatoria o raquídea. Revise los textos recuperados de V2, complete los riesgos particulares del paciente y genere el anverso/reverso para firma, negativa o revocatoria.',
            '028' => 'Registre la evaluación del desarrollo infantil por áreas, su interpretación y la conducta de seguimiento.',
            '028A' => 'Registre la atención integral del niño o niña menor de 2 meses, crecimiento, alimentación, examen y seguimiento.',
            '028A1' => 'Registre y evalúe las curvas de crecimiento de la niña menor de 5 años.',
            '028A2' => 'Registre y evalúe las curvas de crecimiento del niño menor de 5 años.',
            '028A3' => 'Registre y evalúe las curvas de crecimiento de la niña de 5 a 9 años.',
            '028A4' => 'Registre y evalúe las curvas de crecimiento del niño de 5 a 9 años.',
            '028B' => 'Registre la evaluación y clasificación AIEPI del niño o niña menor de 2 meses.',
            '028C' => 'Registre la atención integral del niño o niña de 2 meses a 9 años, crecimiento, desarrollo, examen y plan.',
            '028D' => 'Registre la evaluación y clasificación AIEPI del niño o niña de 2 meses a 9 años.',
            '033' => 'Odontología registra antecedentes, examen estomatognático, odontograma, diagnóstico y tratamiento.',
            '038' => 'Trabajo Social documenta la evaluación familiar, socioeconómica, redes de apoyo y plan de intervención.',
            '051' => 'Obstetricia registra los antecedentes, la gestación actual, riesgos, controles y plan materno-perinatal.',
            '051A' => 'Registre el IMC inicial y la ganancia de peso de la gestante en cada control prenatal.',
            '053' => 'El médico registra el motivo de referencia, el resumen clínico y el establecimiento de destino; la respuesta corresponde a la contrarreferencia.',
            '054' => 'Concentre cronológicamente los resultados de laboratorio relevantes para el episodio.',
            '055' => 'Concentre cronológicamente los resultados de exámenes y procedimientos especiales.',
            '056' => 'Registre la atención integral del adolescente, incluyendo riesgos, salud mental y salud sexual y reproductiva.',
            '056A' => 'Registre crecimiento y desarrollo puberal de la adolescente mujer mediante curvas y estadios de Tanner.',
            '056B' => 'Registre crecimiento y desarrollo puberal del adolescente hombre mediante curvas y estadios de Tanner.',
            '057' => 'Registre la valoración geriátrica integral: funcional, cognitiva, afectiva, nutricional y social.',
            '057A' => 'Registre los resultados de las escalas geriátricas y su interpretación clínica integral.',
            '060' => 'El equipo quirúrgico registra la verificación antes de la anestesia, antes de la incisión y antes de salir del quirófano.',
            '078' => 'Salud ocupacional registra el puesto, exposiciones, evaluación clínica y conclusión de aptitud laboral.',
            '113' => 'El equipo de UCI adulto registra la bitácora, monitorización, ventilación, balance, procedimientos y cuidados del turno.',
            '114' => 'Enfermería y el equipo de UCI neonatal registran monitorización, soporte, balance, medicación y eventos del turno.',
            '115' => 'Enfermería y el equipo de UCI pediátrica registran monitorización, soporte, balance, medicación y eventos del turno.',
            '116' => 'El equipo autorizado documenta la evaluación y el resultado de la calificación de discapacidad.',
            '117' => 'El profesional registra exclusivamente los datos clínicos necesarios para emitir el certificado médico.',
            '118' => 'El médico registra la indicación, hemocomponente, prioridad, antecedentes y requisitos transfusionales.',
            '119' => 'El equipo registra la condición, soporte y entrega-recepción durante la transferencia asistencial.',
            '121' => 'Psicología registra la evaluación, instrumentos aplicados, impresión diagnóstica y plan de intervención.',
            '122' => 'El equipo registra la verificación, administración, monitorización, reacciones y seguimiento de la transfusión.',
            'ANEXO1' => 'Registre la composición familiar, determinantes, riesgos e intervenciones de seguimiento.',
            'ANEXO2' => 'El personal prehospitalario registra el evento, evaluación, tratamiento, traslado y entrega del paciente.',
        ];

        return $instructions[$code] ?? '';
    }

    /** Each page contains sections; a row may have one or several writing cells. */
    public static function pages($code)
    {
        $s = function ($title, array $rows, $height = 24) {
            return ['title' => $title, 'rows' => $rows, 'height' => $height];
        };
        $layouts = [
            '001' => [[
                $s('B. REGISTRO DE ADMISIÓN', [['fecha_admision', 'procedencia'], ['direccion_residencia'], ['telefono_contacto', 'cobertura']], 18),
                $s('C. UBICACIÓN DEL PACIENTE', [['ubicacion']], 28),
                $s('D. ALTA - EGRESO', [['fecha_egreso'], ['diagnosticos_egreso'], ['condicion_egreso']], 24),
            ]],
            '002' => [[
                $s('B. MOTIVO, ANTECEDENTES Y PROBLEMA ACTUAL', [['motivo_consulta'], ['antecedentes'], ['enfermedad_actual']], 28),
                $s('C. CONSTANTES VITALES Y EXAMEN FÍSICO', [['constantes'], ['examen_fisico']], 34),
                $s('D. DIAGNÓSTICOS, PLAN Y PRESCRIPCIONES', [['diagnosticos'], ['plan', 'prescripciones']], 32),
            ]],
            '003' => [[
                $s('B. MOTIVO DE CONSULTA', [['motivo_consulta']], 24),
                $s('C. ANTECEDENTES PATOLÓGICOS PERSONALES Y FAMILIARES', [['antecedentes_personales'], ['antecedentes_familiares']], 28),
                $s('D. ENFERMEDAD O PROBLEMA ACTUAL', [['enfermedad_actual']], 40),
                $s('E. REVISIÓN DE ÓRGANOS Y SISTEMAS', [['revision_sistemas']], 30),
                $s('F. CONSTANTES VITALES Y ANTROPOMETRÍA', [['signos_vitales']], 20),
            ], [
                $s('G. EXAMEN FÍSICO', [['examen_fisico']], 48),
                $s('H. ANÁLISIS', [['analisis']], 34),
                $s('I. DIAGNÓSTICOS', [['diagnostico']], 28),
                $s('J. PLAN DE TRATAMIENTO Y PRESCRIPCIONES', [['plan_tratamiento']], 45),
            ]],
            '005' => [[
                $s('B. EVOLUCIÓN Y PRESCRIPCIONES', [['fecha_hora'], ['evolucion', 'prescripciones']], 62),
                $s('DIAGNÓSTICOS Y PLAN DE MANEJO', [['diagnosticos', 'plan']], 42),
            ]],
            '006' => [[
                $s('B. RESUMEN DEL CUADRO CLÍNICO', [['resumen_clinico']], 42),
                $s('C. RESUMEN DE EVOLUCIÓN Y COMPLICACIONES', [['resumen_evolucion']], 52),
                $s('D. HALLAZGOS RELEVANTES DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS', [['hallazgos']], 45),
            ], [
                $s('E. RESUMEN DE TRATAMIENTO Y PROCEDIMIENTOS TERAPÉUTICOS', [['tratamiento']], 40),
                $s('F. INDICACIONES DE ALTA / EGRESO', [['indicaciones']], 40),
                $s('G. DIAGNÓSTICO DE ALTA / EGRESO', [['diagnosticos_egreso']], 28),
                $s('H. CONDICIÓN DE ALTA / EGRESO', [['condicion_egreso']], 25),
            ]],
            '007' => [[
                $s('B. CARACTERÍSTICAS DE LA SOLICITUD, MOTIVO Y PRIORIDAD DE ATENCIÓN', [['especialidad_solicitada'], ['motivo'], ['pregunta_clinica']], 15),
                $s('C. CUADRO CLÍNICO ACTUAL', [['resumen_clinico']], 28),
                $s('D. RESULTADOS DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS RELEVANTES', [['resultados']], 24),
                $s('E. DIAGNÓSTICO', [['diagnostico']], 19),
                $s('F. PLAN TERAPÉUTICO REALIZADO', [['plan']], 26),
            ]],
            '007B' => [[
                $s('B. CUADRO CLÍNICO DE INTERCONSULTA', [['especialidad'], ['hallazgos']], 25),
                $s('C. RESUMEN DEL CRITERIO CLÍNICO', [['criterio_clinico']], 27),
                $s('D. DIAGNÓSTICO', [['diagnostico']], 22),
                $s('E. PLAN DIAGNÓSTICO PROPUESTO', [['plan_diagnostico']], 27),
                $s('F. PLAN TERAPÉUTICO PROPUESTO', [['recomendaciones']], 30),
            ]],
            '008' => [[
                $s('C. INICIO DE ATENCIÓN', [['motivo']], 25),
                $s('E. ANTECEDENTES PATOLÓGICOS PERSONALES Y FAMILIARES', [['antecedentes']], 35),
                $s('F. ENFERMEDAD O PROBLEMA ACTUAL / H. EXAMEN FÍSICO', [['evaluacion']], 48),
                $s('G. CONSTANTES VITALES Y ANTROPOMETRÍA', [['signos_vitales']], 25),
            ], [
                $s('I. EXAMEN FÍSICO DE TRAUMA / CRÍTICO', [['trauma']], 25),
                $s('K. EXÁMENES COMPLEMENTARIOS', [['examenes_complementarios']], 25),
                $s('L / M. DIAGNÓSTICOS PRESUNTIVOS Y DEFINITIVOS', [['diagnostico']], 25),
                $s('N. PLAN DE TRATAMIENTO', [['plan']], 38),
                $s('O. CONDICIÓN AL EGRESO DE EMERGENCIA', [['condicion_egreso']], 22),
            ]],
            '010A' => [[
                $s('B. SERVICIO Y PRIORIDAD DE ATENCIÓN', [['servicio', 'diagnostico'], ['tratamiento']], 22),
                $s('C. LISTADO DE EXÁMENES SOLICITADOS', [['examenes']], 95),
            ]],
            '010B' => [[
                $s('B. SERVICIO Y PRIORIDAD DE ATENCIÓN', [['servicio', 'diagnostico']], 22),
                $s('C. RESULTADO DE EXÁMENES', [['resultados', 'unidades']], 85),
                $s('CONCLUSIÓN / OBSERVACIONES DEL INFORME', [['conclusion']], 30),
            ]],
            '011' => [[
                $s('B. IDENTIFICACIÓN DEL EXAMEN INCORPORADO', [['fecha_examen', 'tipo_examen'], ['profesional']], 28),
                $s('C. RESULTADO O REFERENCIA DOCUMENTAL', [['resultado']], 65),
                $s('D. OBSERVACIONES', [['observaciones']], 38),
            ]],
            '012A' => [[
                $s('B. SERVICIO Y PRIORIDAD DE ATENCIÓN', [['prioridad']], 15),
                $s('C. ESTUDIO DE IMAGENOLOGÍA SOLICITADO', [['estudio']], 25),
                $s('D. MOTIVO DE LA SOLICITUD', [['indicacion']], 30),
                $s('E. RESUMEN CLÍNICO ACTUAL', [['resumen_clinico']], 40),
                $s('F. DIAGNÓSTICO', [['diagnostico_presuntivo']], 27),
            ]],
            '012B' => [[
                $s('C. ESTUDIO DE IMAGENOLOGÍA REALIZADO', [['estudio'], ['tecnica']], 20),
                $s('D. HALLAZGOS POR IMAGENOLOGÍA', [['hallazgos']], 70),
                $s('E. CONCLUSIONES Y SUGERENCIAS', [['conclusion']], 38),
            ]],
            '013A' => [[
                $s('MUESTRA PARA ANATOMÍA PATOLÓGICA', [['muestra', 'sitio_anatomico']], 28),
                $s('RESUMEN CLÍNICO Y ANTECEDENTES', [['antecedentes']], 62),
                $s('DIAGNÓSTICO PRESUNTIVO', [['diagnostico_presuntivo']], 42),
            ]],
            '013B' => [[
                $s('MUESTRA ESTUDIADA', [['muestra']], 18),
                $s('DESCRIPCIÓN MACROSCÓPICA', [['macroscopia']], 40),
                $s('DESCRIPCIÓN MICROSCÓPICA', [['microscopia']], 45),
                $s('DIAGNÓSTICO ANATOMOPATOLÓGICO', [['diagnostico']], 40),
            ]],
            '016' => [[
                $s('B. PARTO Y CONDICIÓN AL NACIMIENTO', [['datos_parto'], ['condicion_nacimiento'], ['apgar']], 32),
                $s('C. ATENCIÓN INMEDIATA', [['reanimacion'], ['profilaxis']], 32),
                $s('D. ANTROPOMETRÍA Y EXAMEN', [['antropometria'], ['examen_fisico']], 35),
                $s('E. DESTINO DEL RECIÉN NACIDO', [['destino']], 24),
            ]],
            '017' => [[
                $s('C. PROCEDIMIENTO', [['procedimiento'], ['indicacion']], 22),
                $s('F. TIEMPOS QUIRÚRGICOS / DESCRIPCIÓN DEL PROCEDIMIENTO', [['hallazgos'], ['tecnica']], 47),
            ], [
                $s('G. COMPLICACIONES DEL PROCEDIMIENTO QUIRÚRGICO', [['complicaciones']], 65),
                $s('CONCLUSIÓN DEL PROCEDIMIENTO', [['conclusion']], 65),
            ]],
            '018' => [[
                $s('B. REGISTRO DE VALORACIÓN PREANESTÉSICA', [['diagnosticos', 'procedimientos'], ['prioridad', 'riesgo_quirurgico']], 22),
                $s('C. ANAMNESIS', [['antecedentes_personales'], ['antecedentes_anestesicos', 'antecedentes_quirurgicos'], ['alergias', 'transfusiones'], ['habitos', 'antecedentes_familiares']], 40),
            ], [
                $s('D. EXAMEN FÍSICO', [['signos_vitales', 'via_aerea'], ['examen_fisico']], 36),
                $s('E. RESULTADOS', [['resultados']], 25),
                $s('F. ESCALAS E ÍNDICES', [['asa', 'riesgos']], 18),
                $s('G. AYUNO / H. INDICACIONES / I. PLAN', [['ayuno', 'indicaciones'], ['plan_anestesico'], ['observaciones'], ['profesional']], 24),
            ]],
            '018A' => [[
                $s('A / B. DATOS Y SERVICIO', [['medidas'], ['diagnostico_pre', 'diagnostico_post'], ['cirugia_propuesta', 'cirugia_realizada'], ['servicio_prioridad', 'equipo_quirurgico']], 18),
                $s('D. REGISTRO TRANSANESTÉSICO', [['monitorizacion']], 40),
                $s('E / F. DROGAS Y TÉCNICA ANESTÉSICA', [['drogas'], ['tecnica_anestesica', 'via_aerea']], 34),
            ], [
                $s('G / H / I. ACCESOS, VOLÚMENES Y PÉRDIDAS', [['accesos', 'liquidos', 'perdidas']], 20),
                $s('K / L / M. TIEMPOS Y TÉCNICAS', [['tiempos', 'tecnicas_especiales'], ['temperatura']], 18),
                $s('N / O / P. INCIDENTES, LABORATORIO Y OBSERVACIONES', [['incidentes'], ['laboratorio'], ['observaciones']], 30),
                $s('R. EGRESO Y RESPONSABLE', [['egreso'], ['profesional']], 20),
            ]],
            '019' => [[
                $s('B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO', [['especialidad', 'diagnostico'], ['cirugia', 'tecnica_anestesica']], 20),
                $s('C. ENTREGA - RECEPCIÓN DEL PACIENTE', [['recepcion'], ['escalas_ingreso']], 26),
                $s('D. MONITORIZACIÓN', [['monitorizacion']], 48),
            ], [
                $s('E / F. INGRESOS Y ELIMINACIÓN', [['ingresos', 'eliminacion']], 24),
                $s('G. EVOLUCIÓN POSTANESTÉSICA', [['evolucion', 'prescripcion', 'farmacos']], 38),
                $s('H. CONDICIÓN DE EGRESO', [['signos_egreso', 'escalas_egreso'], ['destino']], 25),
                $s('I. PROFESIONAL QUE AUTORIZA EL EGRESO', [['profesional']], 18),
            ]],
            '020' => [[
                $s('REGISTRO DE CONSTANTES VITALES', [['fecha_hora'], ['constantes']], 32),
                $s('BALANCE HÍDRICO', [['ingresos', 'egresos'], ['balance']], 40),
            ]],
            '021' => [[
                $s('B. VALORACIÓN DE ENFERMERÍA', [['fecha_hora'], ['valoracion'], ['problemas']], 34),
                $s('C. INTERVENCIONES Y RESPUESTA', [['intervenciones'], ['respuesta']], 42),
                $s('D. EDUCACIÓN Y RESPONSABLE', [['educacion'], ['responsable']], 28),
            ]],
            '022' => [[
                $s('ALERGIA A MEDICAMENTOS', [['alergias']], 14),
                $s('B. ADMINISTRACIÓN DE MEDICAMENTOS PRESCRITOS', [['medicamentos']], 112),
            ]],
            '024' => [[
                $s('B. IDENTIFICACIÓN DEL CONSENTIMIENTO', [['tipo_consentimiento'], ['fecha', 'hora', 'tipo_atencion'], ['diagnostico', 'cie10']], 18),
                $s('C. INFORMACIÓN SOBRE EL PROCEDIMIENTO', [['procedimiento'], ['consiste'], ['realiza'], ['duracion_estimada']], 24),
                $s('D. BENEFICIOS Y RIESGOS', [['beneficios'], ['riesgos_frecuentes'], ['riesgos_graves'], ['riesgos_especificos']], 25),
            ], [
                $s('E. ALTERNATIVAS Y CUIDADOS POSTERIORES', [['alternativas'], ['manejo_posterior'], ['consecuencias']], 28),
                $s('F. OBSERVACIONES DE LA CONVERSACIÓN', [['observaciones']], 34),
            ]],
            '028' => [[
                $s('B. EDAD PARA LA EVALUACIÓN', [['edad_evaluacion']], 18),
                $s('C. ÁREAS DEL DESARROLLO', [['personal_social', 'motor_fino'], ['lenguaje', 'motor_grueso']], 35),
                $s('D. RESULTADO E INTERPRETACIÓN', [['resultado']], 35),
                $s('E. CONDUCTA Y SEGUIMIENTO', [['conducta']], 35),
            ]],
            '028C' => [[
                $s('B. MOTIVO Y ANTECEDENTES', [['motivo'], ['antecedentes']], 30),
                $s('C. CRECIMIENTO Y DESARROLLO', [['crecimiento'], ['desarrollo']], 30),
                $s('D. EXAMEN FÍSICO', [['examen_fisico']], 38),
                $s('E. DIAGNÓSTICOS Y PLAN', [['diagnosticos'], ['plan']], 32),
            ]],
            '033' => [[
                $s('B. MOTIVO Y ANTECEDENTES', [['motivo'], ['antecedentes']], 28),
                $s('C. EXAMEN Y ODONTOGRAMA', [['examen'], ['odontograma']], 42),
                $s('D. DIAGNÓSTICOS Y PLAN', [['diagnosticos'], ['plan']], 30),
                $s('E. EVOLUCIÓN Y PROCEDIMIENTOS', [['evolucion']], 35),
            ]],
            '038' => [[
                $s('B. MOTIVO Y GRUPO FAMILIAR', [['motivo'], ['grupo_familiar']], 30),
                $s('C. SITUACIÓN SOCIAL', [['vivienda'], ['situacion_economica'], ['red_apoyo']], 28),
                $s('D. DIAGNÓSTICO Y PLAN SOCIAL', [['diagnostico_social'], ['plan']], 38),
            ]],
            '051' => [[
                $s('B. ANTECEDENTES Y GESTACIÓN ACTUAL', [['antecedentes_obstetricos'], ['gestacion_actual']], 32),
                $s('C. CONTROLES Y FACTORES DE RIESGO', [['controles'], ['riesgos']], 32),
                $s('D. EXAMEN Y DIAGNÓSTICOS', [['examen'], ['diagnosticos']], 35),
                $s('E. PLAN Y SEGUIMIENTO', [['plan']], 32),
            ]],
            '053' => [[
                $s('REFERENCIA', [['establecimiento_destino'], ['motivo']], 25),
                $s('RESUMEN CLÍNICO', [['resumen_clinico']], 40),
                $s('DIAGNÓSTICO', [['diagnostico']], 25),
                $s('CONTRARREFERENCIA / RESPUESTA', [['respuesta']], 42),
            ]],
            '054' => [[
                $s('B. PERÍODO Y EXÁMENES', [['periodo'], ['examenes']], 32),
                $s('C. RESULTADOS SERIADOS', [['resultados'], ['unidades_referencias']], 62),
                $s('D. OBSERVACIONES', [['observaciones']], 35),
            ]],
            '055' => [[
                $s('B. PERÍODO Y EXÁMENES ESPECIALES', [['periodo'], ['examenes']], 32),
                $s('C. RESULTADOS', [['resultados']], 52),
                $s('D. CONCLUSIONES Y OBSERVACIONES', [['conclusiones'], ['observaciones']], 38),
            ]],
            '057' => [[
                $s('B. MOTIVO, ANTECEDENTES Y MEDICACIÓN', [['motivo'], ['antecedentes'], ['medicacion']], 30),
                $s('C. VALORACIÓN GERIÁTRICA INTEGRAL', [['funcional'], ['cognitivo_afectivo'], ['nutricional'], ['social']], 27),
                $s('D. RIESGOS Y PLAN INTEGRAL', [['riesgos'], ['plan']], 38),
            ]],
            '060' => [[
                $s('ENTRADA · ANTES DE LA INDUCCIÓN DE LA ANESTESIA', [['identificacion'], ['sitio']], 26),
                $s('PAUSA QUIRÚRGICA · ANTES DE LA INCISIÓN', [['equipo'], ['profilaxis']], 26),
                $s('SALIDA · ANTES DE SALIR DEL QUIRÓFANO', [['recuento'], ['incidentes']], 26),
            ]],
            '078' => [[
                $s('B. PUESTO Y EXPOSICIONES', [['puesto_trabajo'], ['exposiciones']], 32),
                $s('C. EVALUACIÓN CLÍNICA OCUPACIONAL', [['antecedentes'], ['examen_fisico'], ['examenes']], 30),
                $s('D. APTITUD, RESTRICCIONES Y RECOMENDACIONES', [['aptitud'], ['restricciones']], 38),
            ]],
            '114' => [[
                $s('B. TURNO, SOPORTE Y MONITORIZACIÓN', [['fecha_turno'], ['soporte'], ['constantes']], 30),
                $s('C. VALORACIÓN Y NUTRICIÓN', [['neurologico'], ['nutricion']], 30),
                $s('D. BALANCE Y MEDICACIÓN', [['balance'], ['medicacion']], 35),
                $s('E. EVENTOS Y NOVEDADES', [['eventos']], 32),
            ]],
            '115' => [[
                $s('B. TURNO, SOPORTE Y MONITORIZACIÓN', [['fecha_turno'], ['soporte'], ['constantes']], 30),
                $s('C. VALORACIÓN Y NUTRICIÓN', [['neurologico'], ['nutricion']], 30),
                $s('D. BALANCE Y MEDICACIÓN', [['balance'], ['medicacion']], 35),
                $s('E. EVENTOS Y NOVEDADES', [['eventos']], 32),
            ]],
            '117' => [[
                $s('B. MOTIVO Y CONDICIÓN CERTIFICADA', [['motivo'], ['diagnostico']], 38),
                $s('C. VIGENCIA Y REPOSO', [['fecha_inicio', 'fecha_fin'], ['reposo']], 28),
                $s('D. LIMITACIONES Y OBSERVACIONES', [['limitaciones'], ['observaciones']], 42),
            ]],
            '118' => [[
                $s('B. INDICACIÓN TRANSFUSIONAL', [['diagnostico'], ['hemocomponente', 'cantidad'], ['prioridad']], 32),
                $s('C. ANTECEDENTES Y REACCIONES', [['antecedentes'], ['reacciones']], 34),
                $s('D. REQUISITOS Y RESPONSABLE', [['requisitos'], ['responsable']], 35),
            ]],
            '119' => [[
                $s('B. ORIGEN, DESTINO Y CONDICIÓN', [['origen_destino'], ['condicion']], 32),
                $s('C. DIAGNÓSTICOS Y TRATAMIENTO', [['diagnosticos'], ['procedimientos'], ['medicacion']], 30),
                $s('D. SOPORTE Y ENTREGA-RECEPCIÓN', [['soporte'], ['acompanamiento']], 38),
            ]],
            'ANEXO1' => [[
                $s('B. DOMICILIO E INTEGRANTES', [['domicilio'], ['integrantes']], 34),
                $s('C. DETERMINANTES Y RIESGOS', [['determinantes'], ['riesgos']], 36),
                $s('D. FAMILIOGRAMA Y SEGUIMIENTO', [['familiograma'], ['seguimiento']], 38),
            ]],
            'ANEXO2' => [[
                $s('B. EVENTO Y EVALUACIÓN INICIAL', [['evento'], ['evaluacion_inicial'], ['constantes']], 30),
                $s('C. TRAUMA Y TRATAMIENTO', [['trauma'], ['procedimientos'], ['medicacion']], 30),
                $s('D. TRASLADO Y ENTREGA', [['traslado'], ['entrega']], 38),
            ]],
        ];

        $layouts['120'] = [[
            $s('B. VALORACIÓN E INTERVENCIONES DE ENFERMERÍA', [['fecha_hora'], ['valoracion'], ['intervenciones']], 34),
            $s('C. RESPUESTA Y CONTINUIDAD DE CUIDADOS', [['respuesta'], ['pendientes'], ['responsable']], 34),
        ]];
        $layouts['028A'] = [[
            $s('B. MOTIVO, ANTECEDENTES Y ALIMENTACIÓN', [['motivo'], ['antecedentes'], ['alimentacion']], 28),
            $s('C. CRECIMIENTO, DESARROLLO Y EXAMEN', [['crecimiento'], ['desarrollo'], ['examen_fisico']], 30),
            $s('D. DIAGNÓSTICOS Y PLAN', [['diagnosticos'], ['plan']], 34),
        ]];
        $layouts['028A1'] = [[
            $s('B. CONTROL Y CURVAS DE CRECIMIENTO', [['fecha_control'], ['peso', 'talla'], ['perimetro_cefalico', 'imc']], 32),
            $s('C. INTERPRETACIÓN Y CONDUCTA', [['interpretacion']], 45),
        ]];
        $layouts['028A2'] = $layouts['028A1'];
        $layouts['028A3'] = [[
            $s('B. CONTROL Y CURVAS DE CRECIMIENTO', [['fecha_control'], ['peso', 'talla'], ['imc']], 36),
            $s('C. INTERPRETACIÓN Y CONDUCTA', [['interpretacion']], 45),
        ]];
        $layouts['028A4'] = $layouts['028A3'];
        $layouts['028B'] = [[
            $s('B. EVALUACIÓN Y CLASIFICACIÓN AIEPI', [['evaluacion'], ['signos_peligro'], ['problemas']], 34),
            $s('C. ALIMENTACIÓN, TRATAMIENTO Y REFERENCIA', [['alimentacion'], ['tratamiento'], ['referencia']], 34),
        ]];
        $layouts['028D'] = [[
            $s('B. EVALUACIÓN Y CLASIFICACIÓN AIEPI', [['evaluacion'], ['signos_peligro'], ['problemas']], 34),
            $s('C. NUTRICIÓN, TRATAMIENTO Y REFERENCIA', [['nutricion'], ['tratamiento'], ['referencia']], 34),
        ]];
        $layouts['051A'] = [[
            $s('B. EVALUACIÓN NUTRICIONAL INICIAL', [['fecha_control'], ['peso_pregestacional', 'talla'], ['imc']], 32),
            $s('C. GANANCIA DE PESO', [['peso_actual'], ['clasificacion']], 38),
            $s('D. ORIENTACIÓN Y CONDUCTA', [['conducta']], 38),
        ]];
        $layouts['056'] = [[
            $s('B. MOTIVO Y ANTECEDENTES', [['motivo'], ['antecedentes']], 30),
            $s('C. EVALUACIÓN INTEGRAL', [['habitos'], ['salud_mental'], ['salud_sexual'], ['examen_fisico']], 28),
            $s('D. DIAGNÓSTICOS Y PLAN', [['diagnosticos'], ['plan']], 34),
        ]];
        $layouts['056A'] = [[
            $s('B. CRECIMIENTO Y DESARROLLO PUBERAL', [['fecha_control'], ['antropometria'], ['crecimiento'], ['tanner'], ['menarquia']], 27),
            $s('C. INTERPRETACIÓN Y CONDUCTA', [['interpretacion']], 42),
        ]];
        $layouts['056B'] = [[
            $s('B. CRECIMIENTO Y DESARROLLO PUBERAL', [['fecha_control'], ['antropometria'], ['crecimiento'], ['tanner'], ['desarrollo_puberal']], 27),
            $s('C. INTERPRETACIÓN Y CONDUCTA', [['interpretacion']], 42),
        ]];
        $layouts['057A'] = [[
            $s('B. ESCALAS FUNCIONALES Y COGNITIVAS', [['barthel', 'lawton_brody'], ['pfeiffer', 'yesavage']], 36),
            $s('C. NUTRICIÓN Y RIESGOS', [['mini_nutricional', 'riesgo_caidas']], 34),
            $s('D. INTERPRETACIÓN INTEGRAL', [['interpretacion']], 38),
        ]];
        $layouts['113'] = [[
            $s('B. TURNO, ANTROPOMETRÍA Y BITÁCORA', [['fecha_turno'], ['antropometria'], ['bitacora']], 32),
            $s('C. VENTILACIÓN Y PROCEDIMIENTOS', [['ventilacion'], ['procedimientos']], 38),
        ], [
            $s('D. EXÁMENES Y CUIDADOS GENERALES', [['examenes'], ['cuidados']], 48),
            $s('E. INFORME DE ENFERMERÍA', [['enfermeria']], 48),
        ]];
        $layouts['116'] = [[
            $s('B. EVALUACIÓN Y CONDICIÓN DE SALUD', [['tipo_evaluacion'], ['condicion_salud'], ['deficiencias']], 32),
            $s('C. ACTIVIDAD, PARTICIPACIÓN Y CONTEXTO', [['actividad_participacion'], ['factores_contextuales']], 38),
            $s('D. CALIFICACIÓN Y RECOMENDACIONES', [['calificacion'], ['recomendaciones']], 34),
        ]];
        $layouts['121'] = [[
            $s('B. MOTIVO Y ANTECEDENTES', [['motivo'], ['antecedentes']], 34),
            $s('C. EVALUACIÓN PSICOLÓGICA', [['evaluacion'], ['instrumentos']], 42),
            $s('D. DIAGNÓSTICO Y PLAN', [['diagnostico'], ['plan']], 36),
        ]];
        $layouts['122'] = [[
            $s('B. HEMOCOMPONENTE Y VERIFICACIÓN', [['fecha_hora'], ['hemocomponente'], ['verificacion']], 32),
            $s('C. ADMINISTRACIÓN Y MONITORIZACIÓN', [['constantes'], ['administracion']], 38),
            $s('D. REACCIONES Y SEGUIMIENTO', [['reacciones'], ['seguimiento']], 36),
        ]];

        return $layouts[$code] ?? [];
    }

    public static function fieldSections($code)
    {
        $sections = [];
        foreach (self::pages($code) as $page) {
            foreach ($page as $section) {
                foreach ($section['rows'] as $row) {
                    foreach ($row as $key) $sections[$key] = $section['title'];
                }
            }
        }
        return $sections;
    }

    /** Wrap before rendering: Dompdf cannot split a table cell across pages. */
    public static function writingLines($value, $columns = 100)
    {
        $lines = [];
        foreach (preg_split('/\R/u', (string) $value) as $paragraph) {
            // mb_str_split also preserves long unbroken values without clipping.
            $line = '';
            foreach (preg_split('/(\s+)/u', $paragraph, -1, PREG_SPLIT_DELIM_CAPTURE) as $word) {
                if (mb_strlen($line.$word, 'UTF-8') > $columns && $line !== '') {
                    $lines[] = rtrim($line);
                    $line = '';
                }
                while (mb_strlen($word, 'UTF-8') > $columns) {
                    $lines[] = mb_substr($word, 0, $columns, 'UTF-8');
                    $word = mb_substr($word, $columns, null, 'UTF-8');
                }
                $line .= $word;
            }
            $lines[] = rtrim($line);
        }
        return $lines;
    }
}
