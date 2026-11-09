@php
    $bloques = isset($payload['bloques']) && is_array($payload['bloques']) ? $payload['bloques'] : array();
    $vitales = isset($payload['signos_vitales']) && is_array($payload['signos_vitales']) ? $payload['signos_vitales'] : array();
    $antecedentes = isset($payload['antecedentes']) && is_array($payload['antecedentes']) ? $payload['antecedentes'] : array();
    $personales = isset($antecedentes['personales']) && is_array($antecedentes['personales']) ? $antecedentes['personales'] : array();
    $familiares = isset($antecedentes['familiares']) && is_array($antecedentes['familiares']) ? $antecedentes['familiares'] : array();
    $estoma = isset($payload['examen_estomatognatico']) && is_array($payload['examen_estomatognatico']) ? $payload['examen_estomatognatico'] : array();
    $indicadores = isset($payload['indicadores']) && is_array($payload['indicadores']) ? $payload['indicadores'] : array();
    $ihos = isset($indicadores['ihos']) && is_array($indicadores['ihos']) ? $indicadores['ihos'] : array();
    $ihosPiezas = isset($ihos['piezas']) && is_array($ihos['piezas']) ? $ihos['piezas'] : array();
    $ihosTotales = isset($ihos['totales']) && is_array($ihos['totales']) ? $ihos['totales'] : array();
    $indices = isset($payload['indices']) && is_array($payload['indices']) ? $payload['indices'] : array();
    $cpod = isset($indices['cpod']) && is_array($indices['cpod']) ? $indices['cpod'] : array();
    $ceod = isset($indices['ceod']) && is_array($indices['ceod']) ? $indices['ceod'] : array();
    $examenes = isset($payload['examenes_complementarios']) && is_array($payload['examenes_complementarios']) ? $payload['examenes_complementarios'] : array();
    $diagnosticos = isset($payload['diagnosticos']) && is_array($payload['diagnosticos']) ? $payload['diagnosticos'] : array();
    $odontogramaPayload = isset($payload['odontograma']) && is_array($payload['odontograma']) ? $payload['odontograma'] : array();
    $odontogramaPiezasPayload = isset($odontogramaPayload['piezas']) && is_array($odontogramaPayload['piezas']) ? $odontogramaPayload['piezas'] : array();
    $detallesOdontograma = array();
    $detallesOdontogramaPorPieza = array();
    $evoluciones = isset($payload['evoluciones']) && is_array($payload['evoluciones']) ? $payload['evoluciones'] : array();
    $prof = isset($payload['profesional']) && is_array($payload['profesional']) ? $payload['profesional'] : array();
    $get = function($array, $key, $default = '') { return is_array($array) && array_key_exists($key, $array) ? $array[$key] : $default; };
    $nombreDoctor = $doctor ? trim($doctor->nombre1.' '.$doctor->nombre2.' '.$doctor->apellido1.' '.$doctor->apellido2) : '';
    $ordenPiezasPdf = $denticion == 'temporal'
        ? array('55','54','53','52','51','61','62','63','64','65','85','84','83','82','81','71','72','73','74','75')
        : array('18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28','48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38');
    foreach ($ordenPiezasPdf as $fdiPdf) {
        $piezaPdf = isset($odontogramaPiezasPayload[$fdiPdf]) && is_array($odontogramaPiezasPayload[$fdiPdf]) ? $odontogramaPiezasPayload[$fdiPdf] : array();
        $detallesPiezaPdf = isset($piezaPdf['detalles']) && is_array($piezaPdf['detalles']) ? $piezaPdf['detalles'] : array();
        foreach ($detallesPiezaPdf as $detallePdf) {
            if (!is_array($detallePdf)) {
                continue;
            }
            $diagnosticosPdf = array();
            $procedimientosPdf = array();
            $listaDiagnosticosPdf = isset($detallePdf['diagnosticos']) && is_array($detallePdf['diagnosticos']) ? array_slice($detallePdf['diagnosticos'], 0, 2) : array();
            foreach ($listaDiagnosticosPdf as $indiceDiagnosticoPdf => $diagnosticoPdf) {
                if (!is_array($diagnosticoPdf)) {
                    continue;
                }
                $textoDiagnosticoPdf = trim((string) $get($diagnosticoPdf, 'descripcion'));
                if ($textoDiagnosticoPdf == '') {
                    $textoDiagnosticoPdf = trim((string) $get($diagnosticoPdf, 'cie10'));
                }
                if ($textoDiagnosticoPdf != '') {
                    $diagnosticosPdf[] = $textoDiagnosticoPdf;
                    $procedimientoDiagnosticoPdf = trim((string) $get($diagnosticoPdf, 'procedimiento_realizado'));
                    if ($procedimientoDiagnosticoPdf == '' && $indiceDiagnosticoPdf === 0) {
                        $procedimientoDiagnosticoPdf = trim((string) $get($detallePdf, 'procedimiento_realizado'));
                    }
                    $procedimientosPdf[] = $procedimientoDiagnosticoPdf;
                }
            }
            if (!count($diagnosticosPdf) && $get($detallePdf, 'diagnostico') != '') {
                $diagnosticosPdf[] = $get($detallePdf, 'diagnostico');
                $procedimientosPdf[] = trim((string) $get($detallePdf, 'procedimiento_realizado'));
            }
            if (!isset($detallesOdontogramaPorPieza[$fdiPdf])) {
                $detallesOdontogramaPorPieza[$fdiPdf] = array(
                    'fecha' => $get($detallePdf, 'fecha', $get($bloques, 'fecha_atencion')),
                    'fdi' => $fdiPdf,
                    'caras' => array(),
                    'diagnosticos' => array_slice($diagnosticosPdf, 0, 2),
                    'procedimientos' => array_slice($procedimientosPdf, 0, 2),
                    'doctor' => trim((string) $get($detallePdf, 'doctor', $nombreDoctor)),
                );
            }
            $caraPdf = trim((string) $get($detallePdf, 'nombre_cara', $get($detallePdf, 'cara')));
            if ($caraPdf != '' && !in_array($caraPdf, $detallesOdontogramaPorPieza[$fdiPdf]['caras'])) {
                $detallesOdontogramaPorPieza[$fdiPdf]['caras'][] = $caraPdf;
            }
        }
    }
    foreach ($ordenPiezasPdf as $fdiPdf) {
        if (!isset($detallesOdontogramaPorPieza[$fdiPdf])) {
            continue;
        }
        $detallePiezaAgrupadoPdf = $detallesOdontogramaPorPieza[$fdiPdf];
        $detallesOdontograma[] = array(
            'fecha' => $detallePiezaAgrupadoPdf['fecha'],
            'fdi' => $fdiPdf,
            'cara' => implode(', ', $detallePiezaAgrupadoPdf['caras']),
            'diagnosticos' => $detallePiezaAgrupadoPdf['diagnosticos'],
            'procedimientos' => $detallePiezaAgrupadoPdf['procedimientos'],
            'doctor' => $detallePiezaAgrupadoPdf['doctor'],
        );
    }
    $filasDetallesPdf = max(5, count($detallesOdontograma));
    $checked = function($value) { return $value ? 'X' : ''; };
    $nombrePaciente = $paciente ? trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2) : '';
    $institucion = $seguro ? $seguro->nombre : $get($bloques, 'institucion_sistema', '');
    $unicodigo = $get($bloques, 'unicodigo', $empresa ? $empresa->unicodigo : '');
    $establecimiento = $get($bloques, 'establecimiento', $empresa ? ($empresa->nombre_form ? $empresa->nombre_form : $empresa->nombrecomercial) : '');
    $sexo = $paciente ? ($paciente->sexo == 1 ? 'H' : ($paciente->sexo == 2 ? 'M' : '')) : '';
    $tipoAtencion = $get($bloques, 'tipo_atencion', 'Primera');
    $antMarcados = function($lista) {
        $txt = array();
        foreach ($lista as $item) {
            if (isset($item['marcado']) && $item['marcado']) {
                $txt[] = (isset($item['nombre']) ? $item['nombre'] : '').(isset($item['descripcion']) && $item['descripcion'] != '' ? ': '.$item['descripcion'] : '');
            }
        }
        return implode(' | ', $txt);
    };
    $regionTexto = function($lista) {
        $txt = array();
        foreach ($lista as $item) {
            if ((isset($item['patologia']) && $item['patologia']) || (isset($item['descripcion']) && $item['descripcion'] != '')) {
                $txt[] = (isset($item['region']) ? $item['region'] : '').': '.(isset($item['descripcion']) ? $item['descripcion'] : '');
            }
        }
        return implode(' | ', $txt);
    };
    $examenTexto = function($lista) {
        $txt = array();
        foreach ($lista as $item) {
            if ((isset($item['solicitado']) && $item['solicitado']) || (isset($item['resultado']) && $item['resultado']) || (isset($item['informe']) && $item['informe'] != '')) {
                $txt[] = (isset($item['examen']) ? $item['examen'] : '').': '.(isset($item['informe']) ? $item['informe'] : '');
            }
        }
        return implode(' | ', $txt);
    };
    $modoPeriodontal = isset($modoPeriodontal) ? (bool) $modoPeriodontal : false;
    $periodontogramaSvgData = isset($periodontogramaSvgData) ? $periodontogramaSvgData : '';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>SNS-MSP / HCU-form.033 / 2021 - {{ $modoPeriodontal ? 'Periodontologia' : 'Odontologia' }}</title>
<style>
    @page { size: A4 portrait; margin: 7mm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 9.3px; line-height: 1.16; color: #000; margin: 0; }
    .pagina { page-break-after: always; }
    .pagina:last-child { page-break-after: auto; }
    table { border-collapse: collapse; width: 100%; }
    td, th { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }
    th { background: #e8e8e8; font-weight: bold; text-transform: uppercase; font-size: 8.4px; }
    .mb { margin-bottom: 5px; }
    .encabezado { width: 100%; margin-bottom: 6px; }
    .encabezado td { border: 0; padding: 0; }
    h1 { font-size: 13.4px; margin: 0; text-transform: uppercase; }
    .sub { font-size: 8.7px; color: #333; }
    .cod-form { border: 1.5px solid #000; padding: 4px 9px; font-weight: bold; text-align: center; white-space: nowrap; }
    .titulo-bloque { position: relative; background: #000; color: #fff; font-weight: bold; font-size: 9px; padding: 3px 6px; text-transform: uppercase; }
    .aclara { position: static; float: none; display: inline; margin-left: 8px; font-weight: normal; font-size: 6.5px; color: #ddd; text-transform: none; white-space: nowrap; }
    .campo { min-height: 14px; border-bottom: 1px dotted #999; padding: 1px 2px; }
    .lineas { min-height: 38px; line-height: 15px; }
    .centro { text-align: center; }
    .der { text-align: right; }
    .chk { display: inline-block; border: 1px solid #000; width: 9px; height: 9px; line-height: 9px; text-align: center; font-size: 7px; margin-right: 3px; }
    .odo-nota { font-size: 8px; font-weight: bold; text-align: center; padding: 3px; border: 1px solid #000; border-bottom: none; background: #e8e8e8; }
    .r { color: #c00000; } .a { color: #0033a0; }
    .odontograma-box { border: 1px solid #000; text-align: center; padding: 2px 0 0; height: 330px; overflow: hidden; }
    .odontograma-box img { width: 100%; height: 326px; object-fit: contain; }
    .simbo td { font-size: 8px; padding: 2px 3px; }
    .simbo .s { font-family: Courier, monospace; font-weight: bold; font-size: 10px; text-align: center; width: 24px; }
    .small { font-size: 7.8px; }
    .encabezado-segunda { margin-bottom: 6px; }
    .encabezado-segunda td { padding: 4px 6px; }
    .encabezado-segunda .titulo { font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .encabezado-segunda .dato { font-size: 8.2px; }
    .indicadores td { height: 18px; }
    .detalle-pieza td { min-height: 27px; height: 27px; }
    .tratamiento td { min-height: 29px; height: 29px; }
    .firma-final td { height: 44px; }
</style>
</head>
<body>
<div class="pagina">
    <table class="encabezado"><tr><td><h1>Historia Clinica Unica - {{ $modoPeriodontal ? 'Periodontologia' : 'Odontologia' }}</h1><div class="sub">Ministerio de Salud Publica del Ecuador - Sistema Nacional de Salud</div></td><td style="width:35%;"><div class="cod-form">SNS-MSP / HCU-form.033 / 2021</div></td></tr></table>

    <div class="titulo-bloque">A. Datos del establecimiento y usuario / paciente</div>
    <table class="mb">
        <tr><th style="width:14%;">Institucion del sistema</th><th style="width:9%;">Unicodigo</th><th style="width:17%;">Establecimiento de salud</th><th style="width:12%;">N. historia clinica</th><th style="width:12%;">N. archivo</th><th style="width:6%;">Hoja</th><th style="width:10%;">Fecha</th><th style="width:8%;">Hora</th></tr>
        <tr><td><div class="campo">{{ $institucion }}</div></td><td><div class="campo">{{ $unicodigo }}</div></td><td><div class="campo">{{ $establecimiento }}</div></td><td><div class="campo">{{ $paciente ? $paciente->id : '' }}</div></td><td><div class="campo">{{ $historia->hcid }}</div></td><td class="centro"><div class="campo">{{ $get($bloques, 'nro_hoja', 1) }}</div></td><td><div class="campo">{{ $get($bloques, 'fecha_atencion') }}</div></td><td><div class="campo">{{ $get($bloques, 'hora_atencion') }}</div></td></tr>
        <tr><th>Primer apellido</th><th>Segundo apellido</th><th>Primer nombre</th><th colspan="2">Segundo nombre</th><th>Sexo H/M</th><th>Edad</th><th>Atencion</th></tr>
        <tr><td>{{ $paciente ? $paciente->apellido1 : '' }}</td><td>{{ $paciente ? $paciente->apellido2 : '' }}</td><td>{{ $paciente ? $paciente->nombre1 : '' }}</td><td colspan="2">{{ $paciente ? $paciente->nombre2 : '' }}</td><td class="centro">{{ $sexo }}</td><td class="centro">{{ $edad }}</td><td class="centro"><span class="chk">{{ $tipoAtencion == 'Primera' ? 'X' : '' }}</span>1a <span class="chk">{{ $tipoAtencion != 'Primera' ? 'X' : '' }}</span>Subs.</td></tr>
    </table>

    <div class="titulo-bloque">B. Motivo de consulta <span class="aclara">registrar en version del informante</span></div>
    <table class="mb"><tr><td class="lineas">{{ $get($bloques, 'motivo_consulta') }}</td></tr></table>

    <div class="titulo-bloque">C. Enfermedad o problema actual <span class="aclara">Embarazo: SI <span class="chk">{{ $get($bloques, 'embarazo') == 'SI' ? 'X' : '' }}</span> NO <span class="chk">{{ $get($bloques, 'embarazo') == 'NO' ? 'X' : '' }}</span></span></div>
    <table class="mb"><tr><td class="lineas">{{ $get($bloques, 'enfermedad_actual') }}</td></tr></table>

    <div class="titulo-bloque">D. Antecedentes personales y familiares <span class="aclara">marcar y describir abajo</span></div>
    <table class="mb"><tr><th style="width:20%;">Personales</th><td>{{ $antMarcados($personales) }}</td></tr><tr><th>Familiares</th><td>{{ $antMarcados($familiares) }}</td></tr></table>

    <div class="titulo-bloque">F. Signos vitales</div>
    <table class="mb"><tr><th>Presion arterial</th><th>Frecuencia cardiaca</th><th>Temperatura</th><th>Frecuencia respiratoria</th></tr><tr><td class="centro">{{ $get($vitales, 'presion_arterial') }}</td><td class="centro">{{ $get($vitales, 'frecuencia_cardiaca') }}</td><td class="centro">{{ $get($vitales, 'temperatura') }}</td><td class="centro">{{ $get($vitales, 'frecuencia_respiratoria') }}</td></tr></table>

    <div class="titulo-bloque">G. Examen del sistema estomatognatico <span class="aclara">sin hallazgos: sin patologia aparente</span></div>
    <table class="mb"><tr><td class="lineas">{{ $regionTexto($estoma) }}</td></tr></table>

    @if($modoPeriodontal)
        <div class="titulo-bloque">H. Periodontograma <span class="aclara">cuatro cuadrantes · seis sitios por pieza</span></div>
        <div class="odo-nota"><span class="r">ROJO: MARGEN GINGIVAL</span> - <span class="a">AZUL: PROFUNDIDAD DE SONDAJE</span> - MORADO: BOLSA PERIODONTAL</div>
        <div class="odontograma-box">@if(!empty($periodontogramaSvgData))<img src="{{ $periodontogramaSvgData }}" alt="Periodontograma">@else<div class="small">No existen mediciones periodontales guardadas.</div>@endif</div>
    @else
        <div class="titulo-bloque">H. Odontograma <span class="aclara">denticion {{ $denticion }}</span></div>
        <div class="odo-nota">PINTAR CON: <span class="a">AZUL PARA TRATAMIENTO REALIZADO</span> - <span class="r">ROJO PARA PATOLOGIA ACTUAL</span>@if($denticion != 'temporal') - MOVILIDAD (1-3) Y RECESION (1-4): SOLO EN DIENTES DEFINITIVOS @endif</div>
        <div class="odontograma-box">@if(!empty($odontogramaSvgData))<img src="{{ $odontogramaSvgData }}" alt="Odontograma">@elseif(!empty($odontogramaImg))<img src="{{ $odontogramaImg }}" alt="Odontograma">@else<div class="small">No se pudo generar el odontograma.</div>@endif</div>

        <div class="titulo-bloque" style="margin-top:4px;">Simbologia del odontograma</div>
        <table class="simbo mb">
            <tr><td class="s r">*</td><td>Sellante necesario</td><td class="s a">*</td><td>Sellante realizado</td><td class="s r">X</td><td>Extraccion indicada</td><td class="s a">X</td><td>Perdida por caries</td></tr>
            <tr><td class="s a">X</td><td>Perdida otra causa</td><td class="s r">A</td><td>Endodoncia por realizar</td><td class="s a">A</td><td>Endodoncia realizada</td><td class="s r">O</td><td>Corona indicada</td></tr>
            <tr><td class="s a">O</td><td>Corona realizada</td><td class="s r">#</td><td>Caries superficie</td><td class="s a">#</td><td>Obturado superficie</td><td class="s a">--</td><td>Protesis fija/removible/total</td></tr>
        </table>
    @endif
</div>

<div class="pagina">
    <table class="encabezado-segunda">
        <tr>
            <td style="width:48%;"><div class="titulo">Formulario 033 - {{ $modoPeriodontal ? 'Periodontologia' : 'Odontologia' }}</div><div class="dato">Continuacion de la historia clinica unica</div></td>
            <td style="width:32%;"><strong>Paciente:</strong> {{ $nombrePaciente }}</td>
            <td style="width:20%;"><strong>Historia:</strong> {{ $historia->hcid }}<br><strong>Fecha:</strong> {{ $get($bloques, 'fecha_atencion') }}</td>
        </tr>
    </table>
    <div class="titulo-bloque">I. Indicadores de salud bucal</div>
    <table class="mb indicadores">
        <tr><th colspan="4" style="width:46%;">Higiene oral simplificada</th><th rowspan="2" style="width:18%;">Enfermedad periodontal</th><th rowspan="2" style="width:18%;">Tipos de oclusion</th><th rowspan="2" style="width:18%;">Nivel de fluorosis</th></tr>
        <tr><th>Piezas dentales</th><th>Placa 0-1-2-3</th><th>Calculo 0-1-2-3</th><th>Gingivitis 0-1</th></tr>
        @for($i = 0; $i < 6; $i++)
            @php $row = isset($ihosPiezas[$i]) ? $ihosPiezas[$i] : array(); @endphp
            <tr><td class="centro">{{ $get($row, 'fdi') }}</td><td class="centro">{{ $get($row, 'placa') }}</td><td class="centro">{{ $get($row, 'calculo') }}</td><td class="centro">{{ $get($row, 'gingivitis') }}</td>@if($i == 0)<td>{{ $get($indicadores, 'periodontal') }}</td><td>{{ $get($indicadores, 'oclusion') }}</td><td>{{ $get($indicadores, 'fluorosis') }}</td>@elseif($i < 3)<td></td><td></td><td></td>@else<td colspan="3" class="small">Piezas ausentes: registrar con raya. El promedio no se aproxima al inmediato superior.</td>@endif</tr>
        @endfor
        <tr><th class="der">TOTALES (promedios)</th><td class="centro">{{ $get($ihosTotales, 'placa') }}</td><td class="centro">{{ $get($ihosTotales, 'calculo') }}</td><td class="centro">{{ $get($ihosTotales, 'gingivitis') }}</td><td colspan="3">IHOS total: {{ $get($ihosTotales, 'total') }}</td></tr>
    </table>

    <div class="titulo-bloque">J. Indices CPO - ceo <span class="aclara">no contabilizar terceros molares</span></div>
    <table class="mb" style="width:60%;"><tr><th></th><th class="centro">C</th><th class="centro">P/E</th><th class="centro">O</th><th class="centro">TOTAL</th></tr>@if($denticion != 'temporal')<tr><th>CPOD definitivos</th><td class="centro">{{ $get($cpod, 'c') }}</td><td class="centro">{{ $get($cpod, 'p') }}</td><td class="centro">{{ $get($cpod, 'o') }}</td><td class="centro">{{ $get($cpod, 'total') }}</td></tr>@endif @if($denticion != 'permanente')<tr><th>ceod temporales</th><td class="centro">{{ $get($ceod, 'c') }}</td><td class="centro">{{ $get($ceod, 'e') }}</td><td class="centro">{{ $get($ceod, 'o') }}</td><td class="centro">{{ $get($ceod, 'total') }}</td></tr>@endif</table>

    <div class="titulo-bloque">K. Planes de diagnostico, terapeutico y educacional</div>
    <table class="mb"><tr><th>Plan diagnostico</th><th>Plan terapeutico</th><th>Plan educacional</th></tr><tr><td class="lineas">{{ $get($bloques, 'plan_diagnostico') }}</td><td class="lineas">{{ $get($bloques, 'plan_terapeutico') }}</td><td class="lineas">{{ $get($bloques, 'plan_educacional') }}</td></tr></table>

    <div class="titulo-bloque">L. Pedido de examenes complementarios - M. Informe de examenes</div>
    <table class="mb"><tr><td class="lineas">{{ $examenTexto($examenes) }}</td></tr></table>

    @if(!$modoPeriodontal)
    <div class="titulo-bloque">N. Diagnostico y procedimiento por pieza</div>
    <table class="mb">
        <tr><th style="width:3%;">N.</th><th style="width:8%;">Fecha</th><th style="width:5%;">Pieza</th><th style="width:9%;">Caras</th><th style="width:29%;">Diagnostico CIE-10</th><th style="width:27%;">Procedimiento asociado</th><th style="width:19%;">Doctor</th></tr>
        @for($i = 0; $i < $filasDetallesPdf; $i++)
            @php $detalleFilaPdf = isset($detallesOdontograma[$i]) ? $detallesOdontograma[$i] : array(); @endphp
            <tr class="detalle-pieza">
                <td class="centro">{{ $i + 1 }}</td><td class="centro">{{ $get($detalleFilaPdf, 'fecha') }}</td><td class="centro">{{ $get($detalleFilaPdf, 'fdi') }}</td><td>{{ $get($detalleFilaPdf, 'cara') }}</td>
                <td>@foreach($get($detalleFilaPdf, 'diagnosticos', array()) as $indiceDxPdf => $diagnosticoFilaPdf)<div>{{ $indiceDxPdf + 1 }}. {{ $diagnosticoFilaPdf }}</div>@endforeach</td>
                <td>@foreach($get($detalleFilaPdf, 'procedimientos', array()) as $indiceProcPdf => $procedimientoFilaPdf)<div>{{ $indiceProcPdf + 1 }}. {{ $procedimientoFilaPdf != '' ? $procedimientoFilaPdf : 'Pendiente' }}</div>@endforeach</td>
                <td>{{ $get($detalleFilaPdf, 'doctor') }}</td>
            </tr>
        @endfor
    </table>
    @endif

    <div class="titulo-bloque">O. Tratamiento <span class="aclara">sin complicaciones: registrar no presenta complicaciones</span></div>
    <table class="mb"><tr><th style="width:4%;">S.</th><th style="width:10%;">Fecha</th><th style="width:26%;">Diagnostico y complicaciones</th><th style="width:26%;">Procedimientos</th><th style="width:22%;">Prescripciones</th><th style="width:12%;">Firma</th></tr>@for($i = 0; $i < 8; $i++)@php $ev = isset($evoluciones[$i]) ? $evoluciones[$i] : array(); @endphp<tr class="tratamiento"><td class="centro">{{ $i + 1 }}</td><td>{{ $get($ev, 'fecha') }}</td><td>{{ trim($get($ev, 'diagnostico').' '.$get($ev, 'complicaciones')) }}</td><td>{{ $get($ev, 'procedimientos') }}</td><td>{{ $get($ev, 'prescripciones') }}</td><td>{{ $get($ev, 'firma') }}</td></tr>@endfor</table>

    <table class="firma-final"><tr><th style="width:35%;">Nombre del profesional</th><th style="width:25%;">Codigo MSP/ACESS</th><th style="width:25%;">Firma y sello</th><th style="width:15%;">N. de hoja</th></tr><tr><td>{{ $nombreDoctor }}</td><td>{{ $get($prof, 'codigo_msp_acess', $doctor && isset($doctor->codigo_msp_acess) ? $doctor->codigo_msp_acess : '') }}</td><td></td><td class="centro">{{ $get($bloques, 'nro_hoja', 1) }}</td></tr></table>
</div>
</body>
</html>
