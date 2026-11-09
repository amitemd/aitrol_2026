<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Route;

class HospitalEpisodeFormCatalog
{
    public static function canAuthor($user, $code)
    {
        if (!$user) return false;

        $roles = ['DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO', 'SISTEMAS', 'SISTEMA'];
        // Sistemas is included for every form because it administers the clinical platform.
        if (in_array($code, ['020', '021', '022', '060', '113', '114', '115', '120', '122'], true)) {
            $roles = array_merge($roles, ['ENFERMEROS', 'ENFERMERO', 'ENFERMERA', 'ENFERMERÍA', 'ENFERMERIA']);
        }
        if ($code === '033') $roles = array_merge($roles, ['ODONTÓLOGOS', 'ODONTOLOGOS', 'ODONTÓLOGO', 'ODONTOLOGO']);
        if ($code === '038') $roles = array_merge($roles, ['TRABAJO SOCIAL', 'TRABAJADOR SOCIAL', 'TRABAJADORA SOCIAL']);
        if ($code === '051') $roles = array_merge($roles, ['OBSTETRICES', 'OBSTETRIZ']);
        if ($code === '051A') $roles = array_merge($roles, ['OBSTETRICES', 'OBSTETRIZ', 'NUTRICIONISTAS', 'NUTRICIONISTA']);
        if ($code === '121') $roles = array_merge($roles, ['PSICÓLOGOS', 'PSICOLOGOS', 'PSICÓLOGO', 'PSICOLOGO']);

        return $user->hasAnyUserTypeName($roles);
    }

    public static function definitions()
    {
        return [
            self::form('001', 'Admisión - alta egreso', 'Ingreso y egreso', 'Ingreso y atención'),
            self::form('002', 'Consulta externa / anamnesis-examen físico', 'Consulta externa', 'Ingreso y atención'),
            self::form('003', 'Hospitalización - anamnesis-examen físico', 'Hospitalización', 'Ingreso y atención'),
            self::form('005', 'Evolución y prescripciones', 'Hospitalización y emergencia', 'Ingreso y atención'),
            self::form('006', 'Epicrisis', 'Al egreso', 'Ingreso y atención'),
            self::form('007', 'Interconsulta - solicitud', 'Según indicación', 'Ingreso y atención'),
            self::form('007B', 'Interconsulta - informe', 'Según indicación', 'Ingreso y atención'),
            self::form('008', 'Emergencia', 'Emergencia', 'Ingreso y atención'),

            self::form('010A', 'Laboratorio clínico - solicitud', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('010B', 'Laboratorio clínico - informe', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('011', 'Hoja para incorporar resultados de exámenes', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('012A', 'Imagenología - solicitud', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('012B', 'Imagenología - informe', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('013A', 'Anatomía patológica - solicitud', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('013B', 'Anatomía patológica - informe', 'Según indicación', 'Diagnóstico y apoyo'),
            self::form('054', 'Concentrado de laboratorio', 'Seguimiento de resultados', 'Diagnóstico y apoyo'),
            self::form('055', 'Concentrado de exámenes especiales', 'Seguimiento de resultados', 'Diagnóstico y apoyo'),

            self::form('020', 'Constantes vitales y balance hídrico', 'Hospitalización y emergencia', 'Enfermería'),
            self::form('021', 'Notas de enfermería', 'Hospitalización y emergencia', 'Enfermería'),
            self::form('022', 'Administración de medicamentos', 'Hospitalización y emergencia', 'Enfermería', '2021', ['015', '015/2007', 'Kardex']),
            self::form('120', 'Intervenciones de enfermería', 'Hospitalización y emergencia', 'Enfermería'),

            self::form('016', 'Atención inmediata al recién nacido', 'Neonatología y obstetricia', 'Materno-infantil'),
            self::form('028', 'Test de Denver II', 'Pediatría', 'Materno-infantil'),
            self::form('028A', 'Atención al niño-niña menor de 2 meses', 'Pediatría', 'Materno-infantil'),
            self::form('028A1', 'Curvas de crecimiento · niña menor de 5 años', 'Pediatría', 'Materno-infantil'),
            self::form('028A2', 'Curvas de crecimiento · niño menor de 5 años', 'Pediatría', 'Materno-infantil'),
            self::form('028A3', 'Curvas de crecimiento · niña de 5 a 9 años', 'Pediatría', 'Materno-infantil'),
            self::form('028A4', 'Curvas de crecimiento · niño de 5 a 9 años', 'Pediatría', 'Materno-infantil'),
            self::form('028B', 'AIEPI · niño-niña menor de 2 meses', 'Pediatría', 'Materno-infantil'),
            self::form('028C', 'Atención integral del niño-niña de 2 meses a 9 años', 'Pediatría', 'Materno-infantil'),
            self::form('028D', 'AIEPI · niño-niña de 2 meses a 9 años', 'Pediatría', 'Materno-infantil'),
            self::form('051', 'Materna perinatal', 'Gineco-obstetricia', 'Materno-infantil'),
            self::form('051A', 'Curvas de ganancia de peso de la mujer gestante', 'Control prenatal', 'Materno-infantil', '2011'),

            self::form('017', 'Protocolo quirúrgico', 'Cirugía', 'Cirugía y anestesia'),
            self::form('018', 'Pre anestésico', 'Cirugía', 'Cirugía y anestesia'),
            self::form('018A', 'Transanestésico', 'Cirugía', 'Cirugía y anestesia'),
            self::form('019', 'Cuidados post anestésico', 'Cirugía', 'Cirugía y anestesia'),
            self::form('060', 'Lista de verificación de cirugía segura', 'Cirugía', 'Cirugía y anestesia'),

            self::form('113', 'Bitácora de UCI adulto', 'Cuidados intensivos de adultos', 'Cuidados críticos'),
            self::form('114', 'Bitácora de UCI neonatal', 'Cuidados intensivos neonatales', 'Cuidados críticos'),
            self::form('115', 'Bitácora de UCI pediátrica', 'Cuidados intensivos pediátricos', 'Cuidados críticos'),

            self::form('033', 'Odontología', 'Odontología', 'Atención especializada'),
            self::form('038', 'Trabajo social', 'Trabajo social', 'Atención especializada'),
            self::form('056', 'Atención adolescentes', 'Medicina del adolescente', 'Atención especializada'),
            self::form('056A', 'Crecimiento y Tanner · adolescente mujer', 'Medicina del adolescente', 'Atención especializada'),
            self::form('056B', 'Crecimiento y Tanner · adolescente hombre', 'Medicina del adolescente', 'Atención especializada'),
            self::form('057', 'Atención al adulto mayor', 'Geriatría', 'Atención especializada'),
            self::form('057A', 'Escalas geriátricas', 'Geriatría', 'Atención especializada'),
            self::form('078', 'Historia clínica ocupacional', 'Salud ocupacional', 'Atención especializada', '2019'),
            self::form('116', 'Certificado de discapacidad-condición discapacitante', 'Calificación de discapacidad', 'Atención especializada'),
            self::form('121', 'Psicología clínica', 'Psicología', 'Atención especializada'),

            self::form('024', 'Consentimiento informado', 'Anestesia general/inhalatoria o raquídea', 'Consentimientos y continuidad', '2021', ['Anestesia', 'Inhalatoria', 'Raquídea']),
            self::form('053', 'Referencia, derivación, contra-referencia, referencia inversa', 'Según indicación', 'Consentimientos y continuidad'),
            self::form('117', 'Certificado médico', 'Según indicación', 'Consentimientos y continuidad'),
            self::form('118', 'Solicitud de sangre', 'Medicina transfusional', 'Consentimientos y continuidad'),
            self::form('119', 'Transferencia', 'Traslado en ambulancia', 'Consentimientos y continuidad'),
            self::form('122', 'Transfusión sanguínea-registro-seguimiento', 'Medicina transfusional', 'Consentimientos y continuidad'),
            self::form('ANEXO2', 'Atención prehospitalaria', 'Ambulancia y emergencia', 'Consentimientos y continuidad'),
            self::form('ANEXO1', 'Ficha familiar', 'Según indicación', 'Salud familiar y comunitaria', 'HCU vigente'),
        ];
    }

    private static function form($code, $name, $context, $group, $edition = '2021', array $aliases = [])
    {
        return [
            'code' => $code,
            'name' => $name,
            'context' => $context,
            'group' => $group,
            'edition' => $edition,
            'aliases' => $aliases,
        ];
    }

    public static function availableReports(array $pdfLinks)
    {
        $reportTypes = [
            '001' => ['name' => 'Admisión', 'group' => 'Ingreso y atención'],
            '002' => ['name' => 'Anamnesis', 'group' => 'Ingreso y atención'],
            '005' => ['name' => 'Evolución y prescripciones', 'group' => 'Ingreso y atención'],
            '008' => ['name' => 'Emergencia', 'group' => 'Emergencia'],
            '007' => ['name' => 'Interconsulta', 'group' => 'Órdenes e informes'],
            '007B' => ['name' => 'Informe de interconsulta', 'group' => 'Órdenes e informes'],
            '010A' => ['name' => 'Solicitud de laboratorio', 'group' => 'Órdenes e informes'],
            '010B' => ['name' => 'Informe de laboratorio', 'group' => 'Órdenes e informes'],
            '012A' => ['name' => 'Solicitud de imagenología', 'group' => 'Órdenes e informes'],
            '012B' => ['name' => 'Informe de imagenología', 'group' => 'Órdenes e informes'],
            '013B' => ['name' => 'Informe de anatomía patológica', 'group' => 'Órdenes e informes'],
            '017' => ['name' => 'Protocolo quirúrgico', 'group' => 'Cirugía y anestesia'],
            '018' => ['name' => 'Preanestésico', 'group' => 'Cirugía y anestesia'],
            '018A' => ['name' => 'Transanestésico', 'group' => 'Cirugía y anestesia'],
            '019' => ['name' => 'Postanestésico', 'group' => 'Cirugía y anestesia'],
            '006' => ['name' => 'Epicrisis', 'group' => 'Egreso'],
        ];
        $reports = [];
        foreach ($reportTypes as $code => $type) {
            foreach ($pdfLinks[$code] ?? [] as $pdf) {
                if (empty($pdf['url'])) continue;
                $reports[] = [
                    'code' => $code, 'name' => $type['name'], 'group' => $type['group'],
                    'url' => $pdf['url'], 'label' => $pdf['label'] ?? null,
                    'official' => $pdf['official'] ?? true, 'status' => $pdf['status'] ?? 'PDF disponible',
                    'preview_label' => $pdf['preview_label'] ?? 'Ver aquí',
                ];
            }
        }
        return $reports;
    }

    public static function find($code)
    {
        foreach (self::definitions() as $form) {
            if ($form['code'] === (string) $code) return $form;
        }
        return null;
    }

    public static function fields($code)
    {
        $fields = [
            '001' => ['fecha_admision' => 'Fecha y hora de admisión', 'procedencia' => 'Procedencia', 'direccion_residencia' => 'Dirección de residencia', 'telefono_contacto' => 'Teléfono de contacto', 'cobertura' => 'Cobertura o seguro', 'ubicacion' => 'Servicio, sala y cama', 'fecha_egreso' => 'Fecha y hora de egreso', 'diagnosticos_egreso' => 'Diagnósticos de egreso y CIE-10', 'condicion_egreso' => 'Condición y destino al egreso'],
            '002' => ['motivo_consulta' => 'Motivo de consulta', 'antecedentes' => 'Antecedentes personales y familiares', 'enfermedad_actual' => 'Enfermedad o problema actual', 'constantes' => 'Constantes vitales y antropometría', 'examen_fisico' => 'Examen físico', 'diagnosticos' => 'Diagnósticos y CIE-10', 'plan' => 'Plan de manejo', 'prescripciones' => 'Prescripciones e indicaciones'],
            '003' => ['motivo_consulta' => 'Motivo de consulta', 'antecedentes_personales' => 'Antecedentes patológicos personales', 'antecedentes_familiares' => 'Antecedentes patológicos familiares', 'enfermedad_actual' => 'Enfermedad o problema actual', 'revision_sistemas' => 'Revisión de órganos y sistemas', 'signos_vitales' => 'Constantes vitales y antropometría', 'examen_fisico_marcas' => 'Examen físico · casillas', 'examen_fisico' => 'Examen físico · descripción', 'body_marks' => 'Esquema corporal', 'analisis' => 'Análisis', 'diagnostico' => 'Diagnóstico', 'plan_tratamiento' => 'Plan de tratamiento y prescripciones'],
            '005' => ['fecha_hora' => 'Fecha y hora de la evolución', 'evolucion' => 'Notas de evolución', 'diagnosticos' => 'Diagnósticos', 'prescripciones' => 'Farmacoterapia e indicaciones', 'plan' => 'Plan de manejo'],
            '006' => ['resumen_clinico' => 'Resumen clínico', 'resumen_evolucion' => 'Evolución y complicaciones', 'hallazgos' => 'Hallazgos relevantes de exámenes', 'tratamiento' => 'Tratamiento realizado', 'indicaciones' => 'Indicaciones al alta', 'diagnosticos_egreso' => 'Diagnósticos de egreso', 'condicion_egreso' => 'Condición al egreso'],
            '007' => ['especialidad_solicitada' => 'Especialidad solicitada', 'motivo' => 'Motivo de interconsulta', 'pregunta_clinica' => 'Pregunta al interconsultor', 'resumen_clinico' => 'Resumen clínico y diagnóstico', 'resultados' => 'Resultados de exámenes y procedimientos relevantes', 'diagnostico' => 'Diagnóstico y CIE-10 (presuntivo o definitivo)', 'plan' => 'Plan terapéutico realizado'],
            '007B' => ['especialidad' => 'Especialidad que responde', 'hallazgos' => 'Hallazgos de la evaluación', 'criterio_clinico' => 'Resumen del criterio clínico', 'diagnostico' => 'Diagnóstico', 'plan_diagnostico' => 'Plan diagnóstico propuesto', 'recomendaciones' => 'Recomendaciones y plan terapéutico'],
            '008' => ['motivo' => 'Motivo de atención', 'antecedentes' => 'Antecedentes personales y familiares', 'evaluacion' => 'Enfermedad actual y examen físico', 'signos_vitales' => 'Constantes vitales y antropometría', 'trauma' => 'Examen de trauma o paciente crítico', 'examenes_complementarios' => 'Exámenes complementarios', 'diagnostico' => 'Diagnósticos presuntivos y definitivos', 'plan' => 'Plan de tratamiento y destino', 'condicion_egreso' => 'Condición al egreso de Emergencia'],
            '010A' => ['servicio' => 'Servicio y prioridad de atención', 'diagnostico' => 'Diagnóstico y CIE-10', 'tratamiento' => 'Tratamiento terapéutico', 'examenes' => 'Exámenes de laboratorio solicitados'],
            '010B' => ['servicio' => 'Servicio y prioridad de atención', 'diagnostico' => 'Diagnóstico y CIE-10', 'resultados' => 'Resultados de exámenes', 'unidades' => 'Unidades y valores de referencia', 'conclusion' => 'Conclusión del informe'],
            '011' => ['fecha_examen' => 'Fecha del examen', 'tipo_examen' => 'Tipo de examen o documento incorporado', 'resultado' => 'Resultado o referencia del documento', 'profesional' => 'Profesional o servicio emisor', 'observaciones' => 'Observaciones'],
            '012A' => ['prioridad' => 'Prioridad', 'estudio' => 'Estudio de imagen solicitado', 'indicacion' => 'Indicación clínica', 'resumen_clinico' => 'Resumen clínico actual', 'diagnostico_presuntivo' => 'Diagnóstico presuntivo'],
            '012B' => ['estudio' => 'Estudio realizado', 'tecnica' => 'Técnica', 'hallazgos' => 'Hallazgos', 'conclusion' => 'Conclusión'],
            '013A' => ['muestra' => 'Muestra solicitada', 'sitio_anatomico' => 'Sitio anatómico', 'antecedentes' => 'Antecedentes relevantes', 'diagnostico_presuntivo' => 'Diagnóstico presuntivo'],
            '013B' => ['muestra' => 'Muestra estudiada', 'macroscopia' => 'Descripción macroscópica', 'microscopia' => 'Descripción microscópica', 'diagnostico' => 'Diagnóstico anatomopatológico'],
            '016' => ['datos_parto' => 'Datos del parto y nacimiento', 'condicion_nacimiento' => 'Condición al nacimiento', 'apgar' => 'APGAR y valoración respiratoria', 'reanimacion' => 'Reanimación y procedimientos', 'antropometria' => 'Antropometría del recién nacido', 'examen_fisico' => 'Examen físico y malformaciones', 'profilaxis' => 'Profilaxis e inmunizaciones', 'destino' => 'Alojamiento, referencia o ingreso neonatal'],
            '017' => ['procedimiento' => 'Procedimiento', 'indicacion' => 'Indicación', 'tecnica' => 'Técnica quirúrgica', 'hallazgos' => 'Hallazgos', 'complicaciones' => 'Complicaciones', 'conclusion' => 'Conclusión'],
            '018' => [
                'diagnosticos' => 'Diagnósticos y CIE-10', 'procedimientos' => 'Procedimiento propuesto',
                'prioridad' => 'Tipo y prioridad del procedimiento', 'riesgo_quirurgico' => 'Riesgo quirúrgico',
                'antecedentes_personales' => 'Antecedentes patológicos personales', 'antecedentes_anestesicos' => 'Antecedentes anestésicos',
                'antecedentes_quirurgicos' => 'Antecedentes quirúrgicos', 'alergias' => 'Alergias',
                'transfusiones' => 'Transfusiones', 'habitos' => 'Hábitos', 'antecedentes_familiares' => 'Antecedentes familiares',
                'signos_vitales' => 'Signos vitales y antropometría', 'via_aerea' => 'Valoración de la vía aérea',
                'examen_fisico' => 'Examen físico', 'resultados' => 'Resultados de laboratorio, imagen y gabinete',
                'asa' => 'Clasificación ASA', 'riesgos' => 'Índices de riesgo', 'ayuno' => 'Ayuno',
                'indicaciones' => 'Indicaciones', 'plan_anestesico' => 'Plan anestésico', 'observaciones' => 'Observaciones',
                'profesional' => 'Profesional responsable',
            ],
            '018A' => [
                'medidas' => 'Fecha, talla, peso, IMC, grupo y factor', 'diagnostico_pre' => 'Diagnóstico preoperatorio y CIE-10',
                'diagnostico_post' => 'Diagnóstico postoperatorio y CIE-10', 'cirugia_propuesta' => 'Cirugía propuesta',
                'cirugia_realizada' => 'Cirugía realizada', 'servicio_prioridad' => 'Servicio, prioridad y quirófano',
                'equipo_quirurgico' => 'Equipo quirúrgico', 'monitorizacion' => 'Registro transanestésico',
                'drogas' => 'Drogas administradas', 'tecnica_anestesica' => 'Técnica anestésica',
                'via_aerea' => 'Sistema, aparato y vía aérea', 'accesos' => 'Accesos vasculares',
                'liquidos' => 'Reposición de volumen', 'perdidas' => 'Pérdidas', 'tiempos' => 'Tiempos transanestésicos',
                'tecnicas_especiales' => 'Técnicas especiales', 'temperatura' => 'Mantenimiento de temperatura',
                'incidentes' => 'Incidentes', 'laboratorio' => 'Resultados de laboratorio', 'observaciones' => 'Observaciones',
                'egreso' => 'Condiciones y destino al egreso', 'profesional' => 'Profesional responsable',
            ],
            '019' => [
                'especialidad' => 'Especialidad', 'diagnostico' => 'Diagnóstico', 'cirugia' => 'Cirugía realizada',
                'tecnica_anestesica' => 'Técnica anestésica', 'recepcion' => 'Entrega y recepción del paciente',
                'escalas_ingreso' => 'Escalas al ingreso (Aldrete, Bromage, Ramsay, EVA, Steward)',
                'monitorizacion' => 'Monitorización postanestésica', 'ingresos' => 'Ingresos', 'eliminacion' => 'Eliminación',
                'evolucion' => 'Evolución postanestésica', 'prescripcion' => 'Prescripción',
                'farmacos' => 'Administración de fármacos', 'signos_egreso' => 'Constantes vitales de entrega',
                'escalas_egreso' => 'Escalas al egreso', 'destino' => 'Condición y destino al egreso',
                'profesional' => 'Profesional que autoriza el egreso',
            ],
            '020' => ['fecha_hora' => 'Fecha y hora del registro', 'constantes' => 'Constantes vitales', 'ingresos' => 'Ingresos de líquidos', 'egresos' => 'Egresos de líquidos', 'balance' => 'Balance'],
            '021' => ['fecha_hora' => 'Fecha y hora', 'valoracion' => 'Valoración de enfermería', 'problemas' => 'Problemas o diagnósticos de enfermería', 'intervenciones' => 'Intervenciones y cuidados realizados', 'respuesta' => 'Respuesta y evolución del paciente', 'educacion' => 'Educación al paciente o familia', 'responsable' => 'Profesional responsable'],
            '022' => ['alergias' => 'Alergia a medicamentos', 'medicamentos' => 'Medicamentos y administraciones'],
            '120' => ['fecha_hora' => 'Fecha y hora', 'valoracion' => 'Valoración y necesidades identificadas', 'intervenciones' => 'Intervenciones de enfermería realizadas', 'respuesta' => 'Respuesta y evolución del paciente', 'pendientes' => 'Cuidados pendientes y continuidad', 'responsable' => 'Profesional responsable'],
            '024' => [
                'tipo_consentimiento' => 'Tipo de consentimiento anestésico',
                'fecha' => 'Fecha', 'hora' => 'Hora', 'tipo_atencion' => 'Tipo de atención',
                'diagnostico' => 'Diagnóstico', 'cie10' => 'Código CIE-10',
                'procedimiento' => 'Procedimiento recomendado', 'consiste' => '¿En qué consiste?',
                'realiza' => '¿Cómo se realiza?', 'duracion_estimada' => 'Duración estimada',
                'beneficios' => 'Beneficios esperados', 'riesgos_frecuentes' => 'Riesgos frecuentes (poco graves)',
                'riesgos_graves' => 'Riesgos poco frecuentes (graves)',
                'riesgos_especificos' => 'Riesgos específicos relacionados con el paciente',
                'alternativas' => 'Alternativas al procedimiento',
                'manejo_posterior' => 'Manejo posterior al procedimiento',
                'consecuencias' => 'Consecuencias si no se realiza el procedimiento',
                'observaciones' => 'Observaciones de la conversación',
            ],
            '028' => ['edad_evaluacion' => 'Edad cronológica y edad corregida', 'personal_social' => 'Área personal-social', 'motor_fino' => 'Motricidad fina-adaptativa', 'lenguaje' => 'Lenguaje', 'motor_grueso' => 'Motricidad gruesa', 'resultado' => 'Resultado e interpretación', 'conducta' => 'Conducta, orientación y seguimiento'],
            '028A' => ['motivo' => 'Motivo de atención', 'antecedentes' => 'Antecedentes perinatales y familiares', 'alimentacion' => 'Alimentación y lactancia', 'crecimiento' => 'Crecimiento y antropometría', 'desarrollo' => 'Desarrollo y signos de alarma', 'examen_fisico' => 'Examen físico', 'diagnosticos' => 'Diagnósticos y riesgos', 'plan' => 'Plan, consejería y seguimiento'],
            '028A1' => ['fecha_control' => 'Fecha y edad del control', 'peso' => 'Peso para la edad', 'talla' => 'Talla para la edad', 'perimetro_cefalico' => 'Perímetro cefálico', 'imc' => 'Índice de masa corporal', 'interpretacion' => 'Interpretación y conducta'],
            '028A2' => ['fecha_control' => 'Fecha y edad del control', 'peso' => 'Peso para la edad', 'talla' => 'Talla para la edad', 'perimetro_cefalico' => 'Perímetro cefálico', 'imc' => 'Índice de masa corporal', 'interpretacion' => 'Interpretación y conducta'],
            '028A3' => ['fecha_control' => 'Fecha y edad del control', 'peso' => 'Peso para la edad', 'talla' => 'Talla para la edad', 'imc' => 'Índice de masa corporal', 'interpretacion' => 'Interpretación y conducta'],
            '028A4' => ['fecha_control' => 'Fecha y edad del control', 'peso' => 'Peso para la edad', 'talla' => 'Talla para la edad', 'imc' => 'Índice de masa corporal', 'interpretacion' => 'Interpretación y conducta'],
            '028B' => ['evaluacion' => 'Evaluación AIEPI y clasificación', 'signos_peligro' => 'Signos generales de peligro', 'problemas' => 'Problemas de salud identificados', 'alimentacion' => 'Lactancia y alimentación', 'tratamiento' => 'Tratamiento y medidas inmediatas', 'referencia' => 'Referencia, consejería y seguimiento'],
            '028C' => ['motivo' => 'Motivo de atención', 'antecedentes' => 'Antecedentes y esquema de inmunización', 'crecimiento' => 'Crecimiento y estado nutricional', 'desarrollo' => 'Desarrollo, visión y audición', 'examen_fisico' => 'Examen físico', 'diagnosticos' => 'Diagnósticos y riesgos identificados', 'plan' => 'Plan, consejería y seguimiento'],
            '028D' => ['evaluacion' => 'Evaluación AIEPI y clasificación', 'signos_peligro' => 'Signos generales de peligro', 'problemas' => 'Problemas de salud identificados', 'nutricion' => 'Nutrición, anemia y crecimiento', 'tratamiento' => 'Tratamiento y medidas inmediatas', 'referencia' => 'Referencia, consejería y seguimiento'],
            '033' => ['motivo' => 'Motivo de consulta odontológica', 'antecedentes' => 'Antecedentes, alergias y riesgo médico', 'examen' => 'Examen del sistema estomatognático', 'odontograma' => 'Odontograma y hallazgos por pieza', 'diagnosticos' => 'Diagnósticos odontológicos', 'plan' => 'Plan de tratamiento', 'evolucion' => 'Evolución y procedimientos realizados'],
            '038' => ['motivo' => 'Motivo de intervención social', 'grupo_familiar' => 'Composición y dinámica familiar', 'vivienda' => 'Vivienda y entorno', 'situacion_economica' => 'Situación económica, educativa y laboral', 'red_apoyo' => 'Red de apoyo y protección', 'diagnostico_social' => 'Diagnóstico social', 'plan' => 'Plan de intervención y seguimiento'],
            '051' => ['antecedentes_obstetricos' => 'Antecedentes personales, familiares y obstétricos', 'gestacion_actual' => 'Datos de la gestación actual', 'controles' => 'Controles prenatales y exámenes', 'riesgos' => 'Factores de riesgo y alertas maternas', 'examen' => 'Examen materno y obstétrico', 'diagnosticos' => 'Diagnósticos', 'plan' => 'Plan, tratamiento y seguimiento'],
            '051A' => ['fecha_control' => 'Fecha y semana de gestación', 'peso_pregestacional' => 'Peso pregestacional', 'talla' => 'Talla', 'imc' => 'IMC pregestacional o del primer trimestre', 'peso_actual' => 'Peso actual y ganancia acumulada', 'clasificacion' => 'Clasificación de la ganancia de peso', 'conducta' => 'Orientación y conducta nutricional'],
            '053' => ['motivo' => 'Motivo de referencia', 'diagnostico' => 'Diagnóstico', 'resumen_clinico' => 'Resumen clínico', 'establecimiento_destino' => 'Establecimiento de destino', 'respuesta' => 'Respuesta o contrarreferencia'],
            '054' => ['periodo' => 'Período del concentrado', 'examenes' => 'Exámenes realizados', 'resultados' => 'Resultados seriados', 'unidades_referencias' => 'Unidades y valores de referencia', 'observaciones' => 'Observaciones'],
            '055' => ['periodo' => 'Período del concentrado', 'examenes' => 'Exámenes y procedimientos especiales', 'resultados' => 'Resultados seriados', 'conclusiones' => 'Conclusiones', 'observaciones' => 'Observaciones'],
            '056' => ['motivo' => 'Motivo de atención', 'antecedentes' => 'Antecedentes personales, familiares y psicosociales', 'habitos' => 'Hábitos, nutrición, actividad y sueño', 'salud_mental' => 'Salud mental, violencia y riesgos', 'salud_sexual' => 'Salud sexual y reproductiva', 'examen_fisico' => 'Examen físico', 'diagnosticos' => 'Diagnósticos y riesgos', 'plan' => 'Plan, consejería y seguimiento'],
            '056A' => ['fecha_control' => 'Fecha y edad del control', 'antropometria' => 'Peso, talla e IMC', 'crecimiento' => 'Curvas de crecimiento', 'tanner' => 'Estadios de Tanner', 'menarquia' => 'Menarquia y desarrollo puberal', 'interpretacion' => 'Interpretación y conducta'],
            '056B' => ['fecha_control' => 'Fecha y edad del control', 'antropometria' => 'Peso, talla e IMC', 'crecimiento' => 'Curvas de crecimiento', 'tanner' => 'Estadios de Tanner', 'desarrollo_puberal' => 'Desarrollo puberal', 'interpretacion' => 'Interpretación y conducta'],
            '057' => ['motivo' => 'Motivo de atención', 'antecedentes' => 'Antecedentes y síndromes geriátricos', 'medicacion' => 'Medicación y conciliación', 'funcional' => 'Valoración funcional', 'cognitivo_afectivo' => 'Valoración cognitiva y afectiva', 'nutricional' => 'Valoración nutricional', 'social' => 'Valoración social y red de apoyo', 'riesgos' => 'Riesgos identificados', 'plan' => 'Plan integral de cuidados'],
            '057A' => ['barthel' => 'Índice de Barthel', 'lawton_brody' => 'Escala de Lawton y Brody', 'pfeiffer' => 'Cuestionario de Pfeiffer', 'yesavage' => 'Escala de Yesavage', 'mini_nutricional' => 'Valoración nutricional', 'riesgo_caidas' => 'Riesgo de caídas', 'interpretacion' => 'Interpretación integral y conducta'],
            '060' => ['identificacion' => 'Identificación del paciente y procedimiento', 'sitio' => 'Sitio y lateralidad', 'equipo' => 'Confirmación del equipo', 'profilaxis' => 'Profilaxis y seguridad', 'recuento' => 'Recuento final', 'incidentes' => 'Incidentes'],
            '078' => ['puesto_trabajo' => 'Puesto, tareas y antigüedad', 'exposiciones' => 'Riesgos y exposiciones ocupacionales', 'antecedentes' => 'Antecedentes personales y laborales', 'examen_fisico' => 'Examen físico ocupacional', 'examenes' => 'Exámenes complementarios', 'aptitud' => 'Conclusión de aptitud laboral', 'restricciones' => 'Restricciones y recomendaciones'],
            '113' => ['fecha_turno' => 'Fecha, hora y turno', 'antropometria' => 'Antropometría', 'bitacora' => 'Bitácora, constantes y resultados', 'ventilacion' => 'Ventilación mecánica', 'procedimientos' => 'Procedimientos y oxigenación', 'examenes' => 'Solicitud y resultados de exámenes', 'cuidados' => 'Cuidados generales e interconsultas', 'enfermeria' => 'Informe de enfermería'],
            '114' => ['fecha_turno' => 'Fecha, hora y turno', 'soporte' => 'Soporte respiratorio y accesos', 'constantes' => 'Constantes y monitorización', 'neurologico' => 'Estado neurológico y dolor', 'nutricion' => 'Nutrición y tolerancia', 'balance' => 'Ingresos, egresos y balance', 'medicacion' => 'Medicación e infusiones', 'eventos' => 'Eventos, procedimientos y novedades'],
            '115' => ['fecha_turno' => 'Fecha, hora y turno', 'soporte' => 'Soporte respiratorio y accesos', 'constantes' => 'Constantes y monitorización', 'neurologico' => 'Estado neurológico y dolor', 'nutricion' => 'Nutrición y tolerancia', 'balance' => 'Ingresos, egresos y balance', 'medicacion' => 'Medicación e infusiones', 'eventos' => 'Eventos, procedimientos y novedades'],
            '116' => ['tipo_evaluacion' => 'Tipo y motivo de evaluación', 'condicion_salud' => 'Condición de salud y antecedentes', 'deficiencias' => 'Deficiencias y estructuras o funciones afectadas', 'actividad_participacion' => 'Limitaciones de actividad y participación', 'factores_contextuales' => 'Factores ambientales y personales', 'calificacion' => 'Resultado de la calificación', 'recomendaciones' => 'Recomendaciones y seguimiento'],
            '117' => ['motivo' => 'Motivo de certificación', 'diagnostico' => 'Diagnóstico o condición de salud', 'fecha_inicio' => 'Fecha de inicio', 'fecha_fin' => 'Fecha de finalización', 'reposo' => 'Días de reposo o incapacidad', 'limitaciones' => 'Limitaciones y recomendaciones', 'observaciones' => 'Observaciones'],
            '118' => ['diagnostico' => 'Diagnóstico e indicación transfusional', 'hemocomponente' => 'Sangre o hemocomponente solicitado', 'cantidad' => 'Cantidad o unidades', 'prioridad' => 'Prioridad y fecha requerida', 'antecedentes' => 'Antecedentes transfusionales y obstétricos', 'reacciones' => 'Reacciones transfusionales previas', 'requisitos' => 'Requisitos especiales y pruebas', 'responsable' => 'Profesional solicitante'],
            '119' => ['origen_destino' => 'Origen, destino y motivo del traslado', 'condicion' => 'Condición clínica para el traslado', 'diagnosticos' => 'Diagnósticos', 'procedimientos' => 'Procedimientos y tratamiento realizados', 'medicacion' => 'Medicamentos, infusiones y oxígeno', 'soporte' => 'Soporte, dispositivos y precauciones', 'acompanamiento' => 'Personal acompañante y entrega-recepción'],
            '121' => ['motivo' => 'Motivo de consulta o interconsulta', 'antecedentes' => 'Antecedentes personales, familiares y psicosociales', 'evaluacion' => 'Evaluación psicológica y examen mental', 'instrumentos' => 'Instrumentos o pruebas aplicadas', 'diagnostico' => 'Impresión diagnóstica y CIE-10', 'plan' => 'Plan de intervención y seguimiento'],
            '122' => ['fecha_hora' => 'Fecha y hora de la transfusión', 'hemocomponente' => 'Hemocomponente, unidad, volumen y compatibilidad', 'verificacion' => 'Verificación de identidad y controles previos', 'constantes' => 'Constantes antes, durante y después', 'administracion' => 'Inicio, velocidad, término y responsable', 'reacciones' => 'Reacciones adversas y manejo', 'seguimiento' => 'Resultado y seguimiento transfusional'],
            'ANEXO1' => ['domicilio' => 'Ubicación y datos del domicilio', 'integrantes' => 'Integrantes del núcleo familiar', 'determinantes' => 'Condiciones y determinantes de salud', 'riesgos' => 'Riesgos familiares y comunitarios', 'familiograma' => 'Familiograma o descripción familiar', 'seguimiento' => 'Intervenciones y seguimiento'],
            'ANEXO2' => ['evento' => 'Evento, lugar y mecanismo', 'evaluacion_inicial' => 'Evaluación inicial y prioridad', 'constantes' => 'Constantes y monitorización', 'trauma' => 'Evaluación de trauma', 'procedimientos' => 'Procedimientos y tratamiento prehospitalario', 'medicacion' => 'Medicamentos y fluidos', 'traslado' => 'Traslado, destino y tiempos', 'entrega' => 'Condición y entrega-recepción'],
        ];
        return isset($fields[$code]) ? $fields[$code] : [];
    }

    public static function forEpisode($episodeId, array $availableKeys, $hasSurgicalWorkflow, $readOnly = false, array $draftCounts = [], array $pdfLinks = [], $canComplete003 = true, ?array $authorableCodes = null)
    {
        $forms = self::definitions();
        // The emergency link is a PDF, not an editor. Keep capture and viewing separate.
        $linked = ['005' => 'evolution', '006' => 'epicrisis'];

        foreach ($forms as &$form) {
            $code = $form['code'];
            $form['url'] = Route::has('hospital.documents.form.show')
                ? route('hospital.documents.form.show', ['code' => $code, 'id_solicitud' => $episodeId]) : null;
            $form['embedded_url'] = Route::has('hospital.documents.form.show')
                ? route('hospital.documents.form.show', ['code' => $code, 'id_solicitud' => $episodeId, 'embedded' => 1]) : null;
            $form['status'] = $readOnly ? 'Solo lectura' : 'Borrador disponible';
            $form['native'] = false;
            $form['saved_count'] = (int) ($draftCounts[$code] ?? 0);
            $form['can_complete'] = !$readOnly && ($code !== '003' || $canComplete003)
                && ($authorableCodes === null || in_array($code, $authorableCodes, true));
            $form['action'] = !$form['can_complete'] ? 'Ver '.$code
                : ($form['saved_count'] ? 'Continuar '.$code : 'Completar '.$code);
            $form['template_preview_url'] = Route::has('hospital.documents.form.pdf')
                ? route('hospital.documents.form.pdf', ['code' => $code, 'id_solicitud' => $episodeId, 'template' => 1]) : null;
            $form['draft_preview_url'] = $form['saved_count'] && Route::has('hospital.documents.form.pdf')
                ? route('hospital.documents.form.pdf', ['code' => $code, 'id_solicitud' => $episodeId]) : null;
            $form['pdf_links'] = $pdfLinks[$code] ?? [];
            $form['preview_url'] = count($form['pdf_links'])
                ? $form['pdf_links'][0]['url']
                : null;
            $form['preview_is_official'] = count($form['pdf_links']) > 0 && ($form['pdf_links'][0]['official'] ?? true);
            $form['preview_label'] = $form['pdf_links'][0]['preview_label'] ?? 'Ver aquí';
            $form['workflow_url'] = $hasSurgicalWorkflow && in_array($code, ['017', '018', '018A', '019', '060'], true)
                ? '#formularios-quirurgicos' : null;

            if (isset($linked[$code]) && ($code !== '005' || !$readOnly)
                && in_array($linked[$code], $availableKeys, true)
                && Route::has('hospital.documents.link')) {
                $form['url'] = route('hospital.documents.link', [
                    'id_solicitud' => $episodeId, 'key' => $linked[$code],
                ]);
                $form['action'] = ($form['can_complete'] ? 'Completar ' : 'Ver ').$code;
                $form['status'] = 'Módulo clínico';
                $form['native'] = true;
                // Evolución y Epicrisis conservan sus pantallas compartidas y se abren aparte.
                $form['embedded_url'] = null;
            }
            if (in_array($code, ['010A', '010B'], true) && Route::has('hospital.laboratory.index')) {
                $form['url'] = route('hospital.laboratory.index', ['id_solicitud' => $episodeId, 'form' => $code]);
                $form['embedded_url'] = route('hospital.laboratory.index', [
                    'id_solicitud' => $episodeId, 'form' => $code, 'embedded' => 1,
                ]);
                $form['action'] = $readOnly ? 'Ver laboratorio' : ($code === '010A' ? 'Solicitar laboratorio' : 'Ingresar resultados');
                $form['native'] = true;
            }
            if (in_array($code, ['005', '006'], true)) $form['embedded_url'] = null;
            if ($form['preview_url']) {
                $form['status'] = $form['pdf_links'][0]['status'] ?? 'PDF disponible';
            } elseif ($code === '003' && $form['saved_count']) {
                $form['status'] = 'Borrador guardado';
            } elseif ($code === '003' && !$form['can_complete'] && !$readOnly) {
                $form['status'] = 'Requiere médico o Sistemas';
            } elseif ($code === '003' && !$readOnly) {
                $form['status'] = 'Por completar';
            } elseif (!$readOnly) {
                $form['status'] = $form['saved_count'] ? 'Borrador guardado' : 'Sin registro';
            }
            $form['hint'] = HospitalEpisodeFormLayout::guidance($code);
            if (!$form['native'] && $code !== '003') {
                $form['hint'] .= $form['can_complete']
                    ? ' Use «'.$form['action'].'» para llenar el borrador de este episodio.'
                    : ' Abra «Ver '.$code.'» para consultar los datos guardados; la edición requiere un profesional autorizado y un episodio abierto.';
            }
            if ($code === '003') {
                $form['hint'] = $form['saved_count']
                    ? 'La valoración inicial está guardada como borrador; revísela antes de validarla.'
                    : ($readOnly ? 'No hay una valoración inicial guardada.'
                        : ($form['can_complete']
                            ? 'Complete la valoración inicial. Puede comenzar tras abrir el episodio, antes o después de asignar cama.'
                            : 'Un médico o usuario de Sistemas autorizado puede completar el borrador antes o después de asignar cama.'));
            } elseif ($code === '005' && !$form['preview_url'] && !$form['saved_count'] && !$readOnly) {
                $form['hint'] = 'Registre la evolución durante la atención.';
            } elseif ($code === '006' && !$form['preview_url'] && !$form['saved_count'] && !$readOnly) {
                $form['hint'] = 'La epicrisis se completa al egreso.';
            }
        }
        unset($form);

        return $forms;
    }
}
