<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8"><title>{{ $code }} · Laboratorio clínico</title>
<style>
    @page { margin:24px 25px 36px; }
    body { font-family:DejaVu Sans,sans-serif; font-size:9px; color:#17202a; }
    h1 { font-size:15px; margin:0 0 6px; }
    h2 { font-size:10px; background:#ded9f4; border:1px solid #606775; padding:5px; margin:14px 0 0; }
    table { width:100%; border-collapse:collapse; table-layout:fixed; }
    th, td { border:1px solid #7d8790; padding:5px; vertical-align:top; word-wrap:break-word; }
    th { background:#dcf0e3; font-size:8px; text-align:left; }
    thead { display:table-header-group; } tr { page-break-inside:avoid; }
    .value { white-space:pre-wrap; word-wrap:break-word; }
    .status { padding:7px; background:#f0f4f7; border:1px solid #adb9c4; margin:8px 0; }
    .muted { color:#52606c; } .check { text-align:center; font-weight:bold; }
    footer { position:fixed; right:0; bottom:30px; left:0; color:#000; border-top:1px solid #999; font-family:Arial,Helvetica,sans-serif; font-size:10px; font-weight:700; line-height:1; padding-top:8px; }
    footer table { border:0; }
    footer td { border:0; padding:0; font-weight:bold; }
</style></head><body>
@php
    $order = $report['order'];
    $patient = $solicitud->paciente;
    $name = trim(implode(' ', array_filter([$patient->apellido1 ?? '', $patient->apellido2 ?? '', $patient->nombre1 ?? '', $patient->nombre2 ?? ''], function ($part) { return $part !== '' && !in_array($part, ['N/A', '(N/A)']); })));
    $doctor = $order->doctor;
    $doctorName = $doctor ? trim(($doctor->apellido1 ?? '').' '.($doctor->apellido2 ?? '').' '.($doctor->nombre1 ?? '').' '.($doctor->nombre2 ?? '')) : '';
@endphp
<h1>LABORATORIO CLÍNICO · {{ $code === '010A' ? 'SOLICITUD' : 'INFORME' }}</h1>
<div>Formulario {{ $code }} · Orden #{{ $order->id }} · Episodio #{{ $solicitud->id }}</div>
<div class="status">
    @if($code === '010A')
        {{ (int) $order->estado === 1 ? 'SOLICITUD REGISTRADA' : 'SOLICITUD PENDIENTE DE CONFIRMAR' }}
    @else
        <strong>{{ $report['complete'] ? 'RESULTADOS CERTIFICADOS' : 'INFORME PARCIAL / PENDIENTE DE CERTIFICACIÓN' }}</strong><br>
        {{ $report['recorded'] }} de {{ $report['expected'] }} resultados registrados · {{ $report['certified'] }} certificados.
    @endif
</div>
<h2>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</h2>
<table><tr><th>ESTABLECIMIENTO</th><th>IDENTIFICACIÓN / HISTORIA CLÍNICA</th><th>FECHA DE SOLICITUD</th></tr>
<tr><td>{{ $order->empresa->nombrecomercial ?? $order->empresa->razonsocial ?? '' }}</td><td>{{ $solicitud->id_paciente }}</td><td>{{ $order->fecha_orden }}</td></tr></table>
<table><tr><th style="width:65%">APELLIDOS Y NOMBRES</th><th>FECHA DE NACIMIENTO</th></tr>
<tr><td>{{ $name }}</td><td>{{ $patient->fecha_nacimiento ?? '' }}</td></tr></table>
<h2>B. DATOS DE LA SOLICITUD</h2>
<table><tr><th>ÁMBITO REGISTRADO EN LA ORDEN</th><th>PROFESIONAL SOLICITANTE</th></tr>
<tr><td>{{ (int) $order->est_amb_hos === 1 ? 'Hospitalización' : 'Ambulatorio / emergencia' }}</td><td>{{ $doctorName }}</td></tr></table>
@if($code === '010A')
    <h2>C. LISTADO DE EXÁMENES SOLICITADOS</h2>
    <table><thead><tr><th style="width:7%">MARCA</th><th>EXAMEN SOLICITADO</th></tr></thead><tbody>
    @forelse($report['exams'] as $exam)<tr><td class="check">X</td><td>{{ $exam->nombre }}</td></tr>@empty<tr><td colspan="2">No hay exámenes seleccionados.</td></tr>@endforelse
    </tbody></table>
    <h2>OBSERVACIONES DE LA SOLICITUD</h2>
    <table>@foreach(\Sis_medico\Services\HospitalEpisodeFormLayout::writingLines(\Sis_medico\Services\HospitalEpisodeLaboratoryService::plain($order->observacion ?? ''), 100) as $line)<tr><td class="value">{{ $line ?: ' ' }}</td></tr>@endforeach</table>
@else
    <h2>C. RESULTADO DE EXÁMENES</h2>
    <table><thead><tr><th style="width:19%">EXAMEN</th><th style="width:19%">PARÁMETRO</th><th style="width:21%">RESULTADO</th><th style="width:9%">UNIDAD</th><th style="width:18%">REFERENCIA</th><th style="width:14%">ESTADO</th></tr></thead><tbody>
    @foreach($report['rows'] as $row)
        @php
            $cells = [];
            foreach (['exam'=>22, 'parameter'=>22, 'value'=>24, 'unit'=>10, 'reference'=>21, 'state'=>16] as $key=>$width) $cells[$key] = array_chunk(\Sis_medico\Services\HospitalEpisodeFormLayout::writingLines($row[$key], $width), 12);
            $chunks = max(array_map('count', $cells));
        @endphp
        @for($chunk = 0; $chunk < $chunks; $chunk++)
            <tr>@foreach($cells as $key => $parts)<td class="value">{{ implode("\n", $parts[$chunk] ?? []) }}</td>@endforeach</tr>
        @endfor
    @endforeach
    </tbody></table>
    @if($report['details']->isNotEmpty())
        <h2>DETALLES COMPLEMENTARIOS REGISTRADOS</h2>
        <table><thead><tr><th>EXAMEN</th><th>DETALLE</th><th>RESULTADO</th><th>OBSERVACIÓN</th></tr></thead><tbody>
        @foreach($report['details'] as $detail)
            @php
                $parts = [];
                foreach ([$report['exams']->firstWhere('id', $detail->id_examen)->nombre ?? '', $detail->campo1 ?? '', $detail->campo2 ?? '', $detail->campo3 ?? ''] as $value) $parts[] = array_chunk(\Sis_medico\Services\HospitalEpisodeFormLayout::writingLines(\Sis_medico\Services\HospitalEpisodeLaboratoryService::plain($value), 28), 12);
                $chunks = max(array_map('count', $parts));
            @endphp
            @for($chunk = 0; $chunk < $chunks; $chunk++)<tr>@foreach($parts as $cell)<td class="value">{{ implode("\n", $cell[$chunk] ?? []) }}</td>@endforeach</tr>@endfor
        @endforeach
        </tbody></table>
    @endif
    <p class="muted">Los valores y la certificación corresponden a lo guardado en Control de laboratorio. Las filas pendientes aún no tienen resultado registrado.</p>
@endif
<footer>
    <table><tr>
        <td>SNS-MSP / HCU-form.{{ $code }} / 2021</td>
        <td style="text-align:right">LABORATORIO CLÍNICO - {{ $code === '010A' ? 'SOLICITUD' : 'INFORME' }} (1)</td>
    </tr></table>
</footer>
</body></html>
