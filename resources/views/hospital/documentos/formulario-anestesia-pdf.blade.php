<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario {{ $form['code'] }} · Episodio {{ $solicitud->id }}</title>
    <style>
        @page { margin: {{ $form['code'] === '018A' ? '2mm 2mm 8mm' : '7mm 8mm 12mm' }}; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: {{ $form['code'] === '018A' ? '5pt' : '7pt' }}; }
        .page, .form-page { page-break-before: always; }
        .page.first, .form-page.is-first { page-break-before: auto; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: .55pt solid #333; padding: 1.1mm; vertical-align: top; word-wrap: break-word; }
        .document-logo { height: 23mm; overflow: hidden; text-align: center; line-height: 0; }
        .document-logo > img { max-width: 55mm; max-height: 20mm; }
        .identity { margin-bottom: 1.5mm; text-align: center; }
        .identity-band { background: #dceaf3; font-size: 8pt; font-weight: bold; text-align: left; }
        .official-identity td { padding: .7mm; }
        .labels, .label, .subhead { background: #d9f0d1; font-size: 6pt; font-weight: bold; text-align: center; }
        .band { background: #d6d0ff; font-size: 7.4pt; font-weight: bold; text-align: left; }
        .form-018a .band, .form-018a .identity-band { background: #a9cbe2; }
        .form-018a { font-size: 4.2pt; }
        .form-018a td, .form-018a th { padding: .28mm .45mm; line-height: 1.05; }
        .form-018a .band, .form-018a .identity-band { padding: .4mm; font-size: 5pt; }
        .form-018a .labels, .form-018a .label { font-size: 4.1pt; }
        .form-018a .official-identity td { padding: .2mm .35mm; }
        .form-018a .document-logo { height: 14mm; }
        .form-018a .document-logo > img { max-height: 13mm; }
        .form-018a .chart img { height: 48mm; }
        .form-019 { font-size: 5pt; }
        .form-019 td, .form-019 th { padding: .4mm .55mm; line-height: 1.05; }
        .form-019 .band, .form-019 .identity-band { padding: .45mm; font-size: 5.5pt; }
        .form-019 .labels, .form-019 .label { font-size: 4.8pt; }
        .form-019 .official-identity td { padding: .25mm .4mm; }
        .form-019 .document-logo { height: 12mm; }
        .form-019 .document-logo > img { max-height: 11mm; }
        .form-019 .chart img { height: 18mm; }
        .form-019 .h10 { height: 7mm; }
        .form-019 .h12 { height: 8mm; }
        .form-019 .h18 { height: 11mm; }
        .form-019 .h28 { height: 18mm; }
        .form-019 .h36 { height: 23mm; }
        .dense td { padding: .35mm .5mm; }
        .value { white-space: pre-wrap; line-height: 1.35; }
        .center { text-align: center; }
        .middle { vertical-align: middle; }
        .h6 { height: 6mm; } .h8 { height: 8mm; } .h10 { height: 10mm; } .h12 { height: 12mm; }
        .h14 { height: 14mm; } .h16 { height: 16mm; } .h18 { height: 18mm; } .h22 { height: 22mm; }
        .h28 { height: 28mm; } .h36 { height: 36mm; } .h48 { height: 48mm; }
        .chart { padding: 0; text-align: center; vertical-align: middle; }
        .chart img { display: block; width: 100%; height: 22mm; }
        .check-grid { font-size: 6pt; line-height: 1.55; }
        .signature { height: 8mm; }
        .footer { position: fixed; right: 0; bottom: -8mm; left: 0; border-top: .4pt solid #777; padding-top: 1mm; font-size: 6pt; font-weight: bold; }
        .footer table, .footer td { border: 0; padding: 0; }
        .page-number:after { content: counter(page); }
        .state { margin: 0 0 1.5mm; color: #6a5317; font-size: 6pt; text-align: right; }
        .no-border td { border: 0; }
    </style>
</head>
<body class="form-{{ strtolower($form['code']) }}">
@php
    $empresa = $empresa ?? null;
    $patient = $solicitud->paciente;
    $payload = $entry ? (array) $entry->payload : [];
    $suggestions = $emergencySource['suggestions'] ?? [];
    $legacyFields = [
        '018' => ['examen_fisico' => 'evaluacion', 'riesgos' => 'riesgo', 'plan_anestesico' => 'plan'],
        '018A' => ['tecnica_anestesica' => 'tecnica', 'drogas' => 'medicamentos'],
        '019' => ['recepcion' => 'condicion', 'prescripcion' => 'analgesia'],
    ];
    $v = function ($key) use ($payload, $suggestions, $legacyFields, $form) {
        $legacyKey = $legacyFields[$form['code']][$key] ?? null;
        if (array_key_exists($key, $payload)) return trim((string) $payload[$key]);
        if ($legacyKey && array_key_exists($legacyKey, $payload)) return trim((string) $payload[$legacyKey]);
        return trim((string) ($suggestions[$key] ?? ''));
    };
    $companyName = trim((string) (($empresa->nombrecomercial ?? null) ?: ($empresa->razonsocial ?? null) ?: 'ESTABLECIMIENTO DE SALUD'));
    $patientSex = (string) ($patient->sexo ?? '') === '1' ? 'MASCULINO' : ((string) ($patient->sexo ?? '') === '2' ? 'FEMENINO' : '');
    $patientBirth = (string) ($patient->fecha_nacimiento ?? '');
    $patientAge = '';
    if ($patientBirth !== '') { try { $patientAge = (new DateTime($patientBirth))->diff(new DateTime())->y.' AÑOS'; } catch (Exception $e) {} }
    $state = $entry ? 'BORRADOR SIN FIRMA · REGISTRO #'.$entry->id : 'FORMATO SIN LLENAR';
@endphp

@if($form['code'] === '018')
<div class="footer"><table><tr><td>SNS-MSP / HCU-form.018 / 2021</td><td style="text-align:right">PRE ANESTÉSICO (<span class="page-number"></span>)</td></tr></table></div>
<section class="form-page is-first">
    @include('hospital.documentos.formulario-anestesia-identidad')
    <p class="state">{{ $state }}</p>
    <table>
        <tr><td colspan="12" class="band">B. REGISTRO DE VALORACIÓN PREANESTÉSICA</td></tr>
        <tr class="labels"><td colspan="9">DIAGNÓSTICOS</td><td colspan="3">CIE</td></tr>
        <tr><td colspan="9" class="value h8">{{ $v('diagnosticos') }}</td><td colspan="3"></td></tr>
        <tr><td colspan="3" class="label">PROCEDIMIENTO/S PROPUESTO/S</td><td colspan="9" class="value h10">{{ $v('procedimientos') }}</td></tr>
        <tr><td class="label">ELECTIVA</td><td class="center">{{ stripos($v('prioridad'), 'elect') !== false ? 'X' : '' }}</td><td colspan="2" class="label">EMERGENCIA</td><td class="center">{{ stripos($v('prioridad'), 'emerg') !== false ? 'X' : '' }}</td><td class="label">URGENCIA</td><td class="center">{{ stripos($v('prioridad'), 'urgen') !== false ? 'X' : $v('prioridad') }}</td><td class="label">RIESGO QUIRÚRGICO</td><td class="label">BAJO</td><td class="center">{{ stripos($v('riesgo_quirurgico'), 'bajo') !== false ? 'X' : '' }}</td><td class="label">MODERADO / ALTO</td><td class="value">{{ $v('riesgo_quirurgico') }}</td></tr>
        <tr><td colspan="12" class="band">C. ANAMNESIS</td></tr>
        <tr><td colspan="12" class="label">ANTECEDENTES PATOLÓGICOS PERSONALES</td></tr>
        <tr class="labels"><td colspan="4">DIAGNÓSTICOS</td><td colspan="4">TIEMPO DE EVOLUCIÓN</td><td colspan="4">TRATAMIENTO</td></tr>
        <tr><td colspan="12" class="value h18">{{ $v('antecedentes_personales') }}</td></tr>
        <tr><td colspan="3" class="label">ANESTÉSICOS</td><td colspan="9" class="value h10">{{ $v('antecedentes_anestesicos') }}</td></tr>
        <tr><td colspan="3" class="label">QUIRÚRGICOS</td><td colspan="9" class="value h10">{{ $v('antecedentes_quirurgicos') }}</td></tr>
        <tr><td colspan="3" class="label">ALÉRGICOS</td><td colspan="9" class="value h10">{{ $v('alergias') }}</td></tr>
        <tr><td colspan="3" class="label">TRANSFUSIONES</td><td colspan="9" class="value h10">{{ $v('transfusiones') }}</td></tr>
        <tr><td colspan="3" class="label">HÁBITOS</td><td colspan="9" class="value h10">{{ $v('habitos') }}</td></tr>
        <tr><td colspan="12" class="label">ANTECEDENTES PATOLÓGICOS FAMILIARES</td></tr>
        <tr><td colspan="12" class="value h10">{{ $v('antecedentes_familiares') }}</td></tr>
    </table>
</section>
<section class="page">
    <table>
        <tr><td colspan="12" class="band">D. EXAMEN FÍSICO</td></tr>
        <tr><td colspan="2" class="label">CONSTANTES VITALES</td><td colspan="10" class="value">{{ $v('signos_vitales') }}</td></tr>
        <tr><td colspan="2" class="label">ANTROPOMETRÍA</td><td colspan="10" class="value">{{ $v('signos_vitales') }}</td></tr>
        <tr><td colspan="2" rowspan="3" class="label middle">VÍA AÉREA</td><td colspan="10" class="labels">APERTURA BUCAL · DISTANCIA TIROMENTONEANA · MALLAMPATI</td></tr>
        <tr><td colspan="10" class="value h12">{{ $v('via_aerea') }}</td></tr>
        <tr><td colspan="10" class="labels">PROTRUSIÓN MANDIBULAR · PERÍMETRO Y MOVILIDAD CERVICAL · INTUBACIÓN DIFÍCIL</td></tr>
        @foreach(['TÓRAX', 'CORAZÓN', 'PULMONES', 'ABDOMEN', 'EXTREMIDADES', 'SISTEMA NERVIOSO CENTRAL', 'EQUIVALENTE METABÓLICO (METS)'] as $examLabel)
        <tr><td colspan="3" class="label">{{ $examLabel }}</td><td colspan="9" class="value">{{ $loop->first ? $v('examen_fisico') : '' }}</td></tr>
        @endforeach
        <tr><td colspan="12" class="band">E. RESULTADOS DE LABORATORIO, IMAGEN Y GABINETE</td></tr>
        <tr><td colspan="12" class="value h14">{{ $v('resultados') }}</td></tr>
        <tr><td colspan="12" class="band">F. ESCALAS E ÍNDICES</td></tr>
        <tr><td colspan="2" class="label">ESTADO FÍSICO ASA</td><td colspan="4" class="value">{{ $v('asa') }}</td><td colspan="2" class="label">RIESGO CARDÍACO</td><td colspan="4" class="value">{{ $v('riesgos') }}</td></tr>
        <tr><td colspan="2" class="label">RIESGO PULMONAR</td><td colspan="4" class="value">{{ $v('riesgos') }}</td><td colspan="2" class="label">RIESGO TROMBOEMBÓLICO</td><td colspan="4" class="value">{{ $v('riesgos') }}</td></tr>
        <tr><td colspan="12" class="band">G. TIEMPO DE ÚLTIMA INGESTA</td></tr>
        <tr><td colspan="2" class="label">LÍQUIDOS CLAROS</td><td colspan="4" class="value">{{ $v('ayuno') }}</td><td colspan="2" class="label">LECHE MATERNA / SÓLIDOS</td><td colspan="4" class="value">{{ $v('ayuno') }}</td></tr>
        <tr><td colspan="12" class="band">H. INDICACIONES</td></tr><tr><td colspan="12" class="value h14">{{ $v('indicaciones') }}</td></tr>
        <tr><td colspan="12" class="band">I. PLAN ANESTÉSICO</td></tr><tr><td colspan="12" class="value h14">{{ $v('plan_anestesico') }}</td></tr>
        <tr><td colspan="12" class="band">J. OBSERVACIONES</td></tr><tr><td colspan="12" class="value h12">{{ $v('observaciones') }}</td></tr>
        <tr><td colspan="12" class="band">K. DATOS DEL PROFESIONAL RESPONSABLE</td></tr>
        <tr class="labels"><td colspan="2">FECHA / HORA</td><td colspan="5">NOMBRE Y APELLIDO</td><td colspan="2">IDENTIFICACIÓN</td><td colspan="3">FIRMA / SELLO</td></tr>
        <tr><td colspan="2">{{ $entry ? $entry->updated_at : '' }}</td><td colspan="5" class="value">{{ $v('profesional') }}</td><td colspan="2"></td><td colspan="3" class="signature"></td></tr>
    </table>
</section>

@elseif($form['code'] === '018A')
<div class="footer"><table><tr><td>SNS-MSP / HCU-form.018A / 2021</td><td style="text-align:right">TRANSANESTÉSICO (<span class="page-number"></span>)</td></tr></table></div>
<section class="form-page is-first">
    @include('hospital.documentos.formulario-anestesia-identidad')
    <p class="state">{{ $state }}</p>
    <table class="dense">
        <tr><td class="label">FECHA</td><td colspan="2" class="value">{{ $entry ? $entry->updated_at : '' }}</td><td class="label">TALLA / PESO / IMC</td><td colspan="3" class="value">{{ $v('medidas') }}</td><td class="label">GRUPO Y FACTOR</td><td colspan="2" class="value">{{ $v('medidas') }}</td><td class="label">CONSENTIMIENTO</td><td class="center">SÍ</td></tr>
        <tr><td colspan="12" class="band">B. SERVICIO Y PRIORIDAD DE ATENCIÓN</td></tr>
        <tr><td colspan="2" class="label">DIAGNÓSTICO PREOPERATORIO</td><td colspan="3" class="value">{{ $v('diagnostico_pre') }}</td><td class="label">CIE</td><td></td><td colspan="2" class="label">CIRUGÍA PROPUESTA</td><td colspan="3" class="value">{{ $v('cirugia_propuesta') }}</td></tr>
        <tr><td colspan="2" class="label">DIAGNÓSTICO POSTOPERATORIO</td><td colspan="3" class="value">{{ $v('diagnostico_post') }}</td><td class="label">CIE</td><td></td><td colspan="2" class="label">CIRUGÍA REALIZADA</td><td colspan="3" class="value">{{ $v('cirugia_realizada') }}</td></tr>
        <tr><td colspan="2" class="label">ANESTESIÓLOGO / CIRUJANO</td><td colspan="3" class="value">{{ $v('equipo_quirurgico') }}</td><td colspan="2" class="label">AYUDANTES / INSTRUMENTISTA</td><td colspan="3" class="value">{{ $v('equipo_quirurgico') }}</td><td class="label">PRIORIDAD / QUIRÓFANO</td><td class="value">{{ $v('servicio_prioridad') }}</td></tr>
        <tr><td colspan="12" class="band">C. SERVICIO Y PRIORIDAD DE ATENCIÓN</td></tr>
        <tr class="labels"><td>CABEZA</td><td>ÓRGANOS</td><td>CUELLO</td><td>COLUMNA</td><td>TÓRAX</td><td>ABDOMEN</td><td>PELVIS</td><td>EXT. SUPERIORES</td><td>EXT. INFERIORES</td><td colspan="3">PERINEAL / OTROS</td></tr>
        <tr><td colspan="12" class="band">D. REGISTRO TRANSANESTÉSICO</td></tr>
        <tr><td colspan="12" class="chart"><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/record_transanestesico.png'))) }}" alt="Gráfico transanestésico"></td></tr>
        <tr><td colspan="2" class="label">ANOTACIONES</td><td colspan="10" class="value">{{ $v('monitorizacion') }}</td></tr>
        <tr><td colspan="12" class="band">E. DROGAS ADMINISTRADAS</td></tr>
        <tr><td class="center">1</td><td colspan="2" class="value">{{ $v('drogas') }}</td><td class="center">2</td><td colspan="2"></td><td class="center">3</td><td colspan="2"></td><td class="center">4</td><td colspan="2"></td></tr>
        @for($drugRow = 0; $drugRow < 3; $drugRow++)<tr><td class="center">{{ 5 + $drugRow * 4 }}</td><td colspan="2"></td><td class="center">{{ 6 + $drugRow * 4 }}</td><td colspan="2"></td><td class="center">{{ 7 + $drugRow * 4 }}</td><td colspan="2"></td><td class="center">{{ 8 + $drugRow * 4 }}</td><td colspan="2"></td></tr>@endfor
        <tr><td colspan="12" class="band">F. TÉCNICA ANESTÉSICA</td></tr>
        <tr class="labels"><td colspan="4">GENERAL</td><td colspan="4">REGIONAL</td><td colspan="4">SEDO - ANALGESIA</td></tr>
        <tr><td class="label">SISTEMA / APARATO</td><td colspan="3" class="value">{{ $v('via_aerea') }}</td><td class="label">ASEPSIA / ANESTÉSICO</td><td colspan="3" class="value">{{ $v('tecnica_anestesica') }}</td><td colspan="4" rowspan="3" class="value">{{ $v('tecnica_anestesica') }}</td></tr>
        <tr><td class="label">MANEJO VÍA AÉREA</td><td colspan="3" class="value">{{ $v('via_aerea') }}</td><td class="label">TRONCULAR / NEUROAXIAL</td><td colspan="3" class="value">{{ $v('tecnica_anestesica') }}</td></tr>
        <tr><td class="label">INDUCCIÓN / MANTENIMIENTO</td><td colspan="3" class="value">{{ $v('tecnica_anestesica') }}</td><td class="label">POSICIÓN / ACCESO</td><td colspan="3"></td></tr>
        <tr><td colspan="4" class="band">G. ACCESOS VASCULARES</td><td colspan="4" class="band">H. REPOSICIÓN VOLÉMICA</td><td colspan="4" class="band">I. PÉRDIDAS</td></tr>
        <tr><td colspan="4" class="value h10">{{ $v('accesos') }}</td><td colspan="4" class="value">{{ $v('liquidos') }}</td><td colspan="4" class="value">{{ $v('perdidas') }}</td></tr>
        <tr><td colspan="6" class="band">J. DATOS DEL RECIÉN NACIDO</td><td colspan="6" class="band">K. TIEMPOS TRANSCURRIDOS</td></tr>
        <tr><td colspan="6">APGAR · 1 / 5 / 10 MINUTOS</td><td colspan="6" class="value">{{ $v('tiempos') }}</td></tr>
    </table>
</section>
<section class="page">
    <table class="dense">
        <tr><td colspan="12" class="band">L. TÉCNICAS ESPECIALES</td></tr>
        <tr class="labels"><td colspan="2">HEMODILUCIÓN</td><td colspan="2">AUTOTRANSFUSIÓN</td><td colspan="2">HIPOTENSIÓN</td><td colspan="2">HIPOTERMIA</td><td colspan="4">CIRCULACIÓN EXTRACORPÓREA</td></tr>
        <tr><td colspan="12" class="value h8">{{ $v('tecnicas_especiales') }}</td></tr>
        <tr><td colspan="12" class="band">M. MANTENIMIENTO DE TEMPERATURA CORPORAL</td></tr>
        <tr><td colspan="12" class="value h8">{{ $v('temperatura') }}</td></tr>
        <tr><td colspan="12" class="band">N. INCIDENTES</td></tr>
        <tr class="labels"><td colspan="3">ACTIVIDAD ELÉCTRICA SIN PULSO</td><td colspan="3">ARRITMIA / ANAFILAXIA</td><td colspan="3">ASISTOLIA / ISQUEMIA</td><td colspan="3">TROMBOEMBOLIA / NEUMOTÓRAX</td></tr>
        <tr><td colspan="12" class="value h12">{{ $v('incidentes') }}</td></tr>
        <tr><td colspan="12" class="band">O. RESULTADO DE EXÁMENES DE LABORATORIO</td></tr>
        <tr class="labels"><td>HORA</td><td>pH</td><td>PO2</td><td>PCO2</td><td>HCO3</td><td>EB</td><td>SAT. O2</td><td>LACTATO</td><td>GLUCOSA</td><td>Na</td><td>K / Cl</td><td>HB / OTRO</td></tr>
        @for($labRow = 0; $labRow < 6; $labRow++)<tr><td>{{ $labRow === 0 ? $v('laboratorio') : '' }}</td>@for($labCell = 0; $labCell < 11; $labCell++)<td></td>@endfor</tr>@endfor
        <tr><td colspan="12" class="band">P. OBSERVACIONES</td></tr><tr><td colspan="12" class="value h18">{{ $v('observaciones') }}</td></tr>
        <tr><td colspan="12" class="band">Q. CONDICIONES DE EGRESO</td></tr>
        <tr><td colspan="2" class="label">CONDICIONES AL SALIR</td><td colspan="2">EXTUBADO / INTUBADO</td><td colspan="2" class="label">CONDUCIDO A</td><td colspan="6" class="value">{{ $v('egreso') }}</td></tr>
        <tr><td colspan="2" class="label">CONSTANTES VITALES DE ENTREGA</td><td colspan="10" class="value">{{ $v('egreso') }}</td></tr>
        <tr><td colspan="12" class="band">R. DATOS DEL PROFESIONAL RESPONSABLE</td></tr>
        <tr><td class="label">HORA</td><td>{{ $entry ? $entry->updated_at : '' }}</td><td colspan="2" class="label">NOMBRE Y APELLIDO</td><td colspan="4" class="value">{{ $v('profesional') }}</td><td class="label">FIRMA</td><td colspan="2" class="signature"></td><td class="label">SELLO</td></tr>
    </table>
</section>

@else
<div class="footer"><table><tr><td>SNS-MSP / HCU-form.019 / 2021</td><td style="text-align:right">CUIDADOS POST ANESTÉSICO (<span class="page-number"></span>)</td></tr></table></div>
<section class="form-page is-first">
    @include('hospital.documentos.formulario-anestesia-identidad')
    <p class="state">{{ $state }}</p>
    <table>
        <tr><td colspan="6" class="band">B. PROCEDIMIENTO QUIRÚRGICO ANESTÉSICO</td></tr>
        <tr><td class="label">ESPECIALIDAD</td><td colspan="2" class="value h8">{{ $v('especialidad') }}</td><td class="label">DIAGNÓSTICO</td><td colspan="2" class="value">{{ $v('diagnostico') }}</td></tr>
        <tr><td class="label">CIRUGÍA REALIZADA</td><td colspan="2" class="value h10">{{ $v('cirugia') }}</td><td class="label">TÉCNICA ANESTÉSICA</td><td colspan="2" class="value">{{ $v('tecnica_anestesica') }}</td></tr>
        <tr><td colspan="6" class="band">C. ENTREGA - RECEPCIÓN DEL PACIENTE</td></tr>
        <tr><td class="label">FECHA / HORA / RESPONSABLES</td><td colspan="2" class="value h12">{{ $v('recepcion') }}</td><td class="label">ALDRETE / BROMAGE / RAMSAY / EVA / STEWARD</td><td colspan="2" class="value">{{ $v('escalas_ingreso') }}</td></tr>
        <tr><td colspan="6" class="band">D. MONITORIZACIÓN</td></tr>
        <tr><td colspan="6" class="chart"><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/record_postanestesico.png'))) }}" alt="Gráfico postanestésico"></td></tr>
        <tr><td class="label">REGISTRO Y PUNTAJES SERIADOS</td><td colspan="5" class="value h18">{{ $v('monitorizacion') }}</td></tr>
        <tr class="labels"><td>ESCALA</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5 / 6</td></tr>
        @foreach(['ALDRETE MODIFICADO', 'BROMAGE', 'RAMSAY', 'EVA', 'STEWARD', 'OTRO'] as $scale)
        <tr><td class="label">{{ $scale }}</td><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
        @endforeach
    </table>
</section>
<section class="page">
    <table>
        <tr><td colspan="3" class="band">E. INGRESOS</td><td colspan="3" class="band">F. ELIMINACIÓN</td></tr>
        <tr><td colspan="3" class="value h28">{{ $v('ingresos') }}</td><td colspan="3" class="value">{{ $v('eliminacion') }}</td></tr>
        <tr><td colspan="6" class="band">G. EVOLUCIÓN POSTANESTÉSICA</td></tr>
        <tr class="labels"><td>FECHA / HORA</td><td colspan="2">EVOLUCIÓN</td><td>PRESCRIPCIÓN</td><td colspan="2">ADMINISTRACIÓN DE FÁRMACOS</td></tr>
        <tr><td class="center">{{ $entry ? $entry->updated_at : '' }}</td><td colspan="2" class="value h36">{{ $v('evolucion') }}</td><td class="value">{{ $v('prescripcion') }}</td><td colspan="2" class="value">{{ $v('farmacos') }}</td></tr>
        <tr><td colspan="6" class="band">H. CONDICIÓN DE EGRESO</td></tr>
        <tr><td class="label">CONSTANTES VITALES DE ENTREGA</td><td colspan="2" class="value h10">{{ $v('signos_egreso') }}</td><td class="label">ALDRETE / BROMAGE / RAMSAY / EVA / STEWARD / OTRO</td><td colspan="2" class="value">{{ $v('escalas_egreso') }}</td></tr>
        <tr><td class="label">CONDICIÓN AL SALIR Y DESTINO</td><td colspan="5" class="value h18">{{ $v('destino') }}</td></tr>
        <tr><td colspan="6" class="band">I. DATOS DEL PROFESIONAL QUE AUTORIZA EL EGRESO</td></tr>
        <tr><td class="label">FECHA Y HORA</td><td>{{ $entry ? $entry->updated_at : '' }}</td><td class="label">NOMBRE / IDENTIFICACIÓN</td><td colspan="3" class="value">{{ $v('profesional') }}</td></tr>
        <tr><td colspan="3" class="center signature">FIRMA</td><td colspan="3" class="center signature">SELLO</td></tr>
    </table>
</section>
@endif
</body>
</html>
