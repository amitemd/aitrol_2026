<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Formulario 005</title>
    <style>
        @page {
            size: legal portrait;
            margin: 15px 18px 15px 18px;
        }

        body {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7px;
            margin: 0;
        }

        table.excel-sheet {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        .excel-sheet td {
            border: 1px solid #000;
            line-height: 1.05;
            overflow: hidden;
            padding: 1px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .blue {
            background: #DDEBF7;
        }

        .green {
            background: #BADAC8;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .title-a {
            font-size: 10px;
        }

        .title-b {
            font-size: 10px;
        }

        .sub-title {
            font-size: 10px;
        }

        .header-small {
            font-size: 7px;
        }

        .note-small {
            font-size: 8px;
        }

        .note-tiny {
            font-size: 6px;
        }

        .data-cell {
            font-size: 7px;
        }

        .footer-left {
            font-size: 10px;
        }

        .footer-right {
            font-size: 10px;
        }

        .official-footer {
            display: table;
            position: fixed;
            right: 0;
            bottom: 30px;
            left: 0;
            width: 100%;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            border-top: 1px solid #999;
            font-size: 10px;
            font-weight: bold;
            line-height: 1;
            padding-top: 8px;
            table-layout: fixed;
        }

        .official-footer span.footer-left,
        .official-footer span.footer-right {
            display: table-cell;
            width: 50%;
        }

        .official-footer .footer-right {
            text-align: right;
        }

        .page-number:after {
            content: counter(page);
        }

        .evolution-edge {
            border-right: 5px solid #fff !important;
        }

        .clinical-grid {
            border-collapse: collapse;
            table-layout: auto;
            width: 100%;
        }

        .clinical-grid td {
            border: 1px solid #000;
            line-height: 1.05;
            padding: 1px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .evolution-content-row td {
            height: 420px;
            padding: 6px;
            vertical-align: top;
            white-space: pre-line;
        }

        .prescription-item {
            border-bottom: 1px solid #aaa;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .prescription-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .evolution-signature-cell {
            height: 105px;
            padding: 5px !important;
            text-align: center;
            vertical-align: bottom !important;
        }

        .signature-block {
            margin: 0 auto;
            text-align: center;
            width: 330px;
        }

        .electronic-signature {
            border-bottom: 1px solid #000;
            border-collapse: collapse;
            margin: 0 auto 3px;
            width: 315px;
        }

        .electronic-signature td {
            border: none;
            font-size: 7.5px;
            line-height: 1.15;
            padding: 0 4px 3px;
            text-align: left;
            vertical-align: middle;
        }

        .electronic-signature .qr-cell {
            padding-left: 0;
            width: 76px;
        }

        .electronic-signature img {
            height: 72px;
            width: 72px;
        }

        .signature-name {
            font-size: 8px;
            font-weight: bold;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .signature-register {
            font-size: 7px;
            line-height: 1.1;
        }

        .signature-status {
            font-size: 7px;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .signature-hash {
            font-family: monospace;
            font-size: 5.5px;
        }

        .blank-space td {
            background: #fff;
            border: none;
        }

        .h-995 td { height: 9.95pt; }
        .h-1575 td { height: 15.75pt; }
        .h-165 td { height: 16.5pt; }
        .h-18 td { height: 18pt; }
        .h-201 td { height: 20.1pt; }
        .h-2495 td { height: 24.95pt; }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
<div class="official-footer">
    <span class="footer-left"><b>SNS-MSP / HCU-form.005 / 2021</b></span>
    <span class="footer-right"><b>EVOLUCIÓN Y PRESCRIPCIONES (<span class="page-number"></span>)</b></span>
</div>
@php
    $paciente = $solicitud005->paciente;
    $establecimiento = !is_null($empresa) ? $empresa->nombrecomercial : '';
    $apellido1 = isset($paciente->apellido1) ? $paciente->apellido1 : '';
    $apellido2 = isset($paciente->apellido2) ? $paciente->apellido2 : '';
    $nombre1 = isset($paciente->nombre1) ? $paciente->nombre1 : '';
    $nombre2 = isset($paciente->nombre2) ? $paciente->nombre2 : '';
    $sexo = isset($paciente->sexo) ? $paciente->sexo : '';
    $institucion = $historia && $historia->seguro ? $historia->seguro->nombre : 'MSP';
    $unicodigo = !is_null($empresa) && !empty($empresa->unicodigo) ? $empresa->unicodigo : '';
    $edad = !empty($paciente->fecha_nacimiento) ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age : '';
    $pages = [];
    $signatureForEvolution = function ($id) use ($signaturesByEvolution) {
        return $signaturesByEvolution[(string) $id] ?? null;
    };
    $signatureForEmergency = function ($id) use ($signaturesByEmergency) {
        return $signaturesByEmergency[(string) $id] ?? null;
    };

    foreach ($evoluciones as $evol) {
        $detallesEvolucion = isset($detallesPorEvolucion) && isset($detallesPorEvolucion[$evol->id])
            ? $detallesPorEvolucion[$evol->id]
            : collect();
        $contenidoEvolucion = isset($contenidoPorEvolucion) && isset($contenidoPorEvolucion[$evol->id])
            ? $contenidoPorEvolucion[$evol->id]
            : (trim(strip_tags((string) $evol->cuadro_clinico)) !== '' ? $evol->cuadro_clinico : $evol->motivo);
        $origenEvolucion = trim((string) ($evol->form005_origin ?? 'HOSPITALIZACIÓN'));
        if ($contenidoEvolucion !== '' && $origenEvolucion !== '') {
            $contenidoEvolucion = $origenEvolucion.' · '.$contenidoEvolucion;
        }
        $fechaEvolucion = '';
        $horaEvolucion = '';
        if (!is_null($evol->created_at)) {
            $fechaBase = substr($evol->created_at, 0, 10);
            $fechaEvolucion = date('Y-m-d', strtotime($fechaBase));
            $horaEvolucion = substr($evol->created_at, 11, 5);
        }

        $prescriptions = [];
        foreach ($detallesEvolucion as $detalle) {
            $prescriptions[] = [
                'prescripcion' => trim((string) $detalle->nombre),
                'medicamento' => trim((string) $detalle->dosis),
            ];
        }
        if (!$prescriptions) $prescriptions[] = ['prescripcion' => '', 'medicamento' => ''];

        $recordedAt = trim($fechaEvolucion.' '.$horaEvolucion);
        $pages[] = [
            'sort_at' => $recordedAt,
            'fecha' => $fechaEvolucion,
            'hora' => $horaEvolucion,
            'evolucion' => $contenidoEvolucion,
            'prescriptions' => $prescriptions,
            'signature' => $signatureForEvolution($evol->id),
        ];
    }

    foreach (($emergencySource['evolutions'] ?? []) as $emergencyEvolution) {
        $fechaEvolucion = substr($emergencyEvolution['recorded_at'], 0, 10);
        $horaEvolucion = substr($emergencyEvolution['recorded_at'], 11, 5);
        $contenidoEvolucion = 'EMERGENCIA · '.$emergencyEvolution['content'];
        $recordedAt = trim($fechaEvolucion.' '.$horaEvolucion);
        $pages[] = [
            'sort_at' => $recordedAt,
            'fecha' => $fechaEvolucion,
            'hora' => $horaEvolucion,
            'evolucion' => $contenidoEvolucion,
            'prescriptions' => [['prescripcion' => '', 'medicamento' => '']],
            'signature' => $signatureForEmergency($emergencyEvolution['id']),
        ];
    }
    usort($pages, function ($left, $right) {
        return strcmp($left['sort_at'], $right['sort_at']);
    });

    if (!$pages) $pages[] = [
        'sort_at' => '', 'fecha' => '', 'hora' => '', 'evolucion' => '',
        'prescriptions' => [['prescripcion' => '', 'medicamento' => '']],
        'signature' => null,
    ];

    $renderCols = function () {
        for ($i = 1; $i <= 20; $i++) {
            echo $i === 12 ? '<col style="width:5px">' : '<col>';
        }
    };

@endphp

@foreach($pages as $pageIndex => $page)
    @php
        $isFirst = $pageIndex == 0;
        $sheetNo = $pageIndex + 1;
    @endphp

    <table class="excel-sheet @if(!$isFirst) page-break @endif">
        <colgroup>
            @php $renderCols(); @endphp
        </colgroup>

        <tr>
            <td colspan="20" class="center" style="height: 72px; padding: 0; border: none; overflow: visible;">
                @include('pdf_hospital.partials.establishment_logo', ['empresa' => $empresa])
            </td>
        </tr>

        @if($isFirst)
            <tr class="h-165">
                <td colspan="20" class="blue left title-a"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
            </tr>
            <tr class="h-201">
                <td colspan="4" class="green center bold header-small">INSTITUCION DEL SISTEMA</td>
                <td colspan="2" class="green center bold header-small">UNICODIGO</td>
                <td colspan="4" class="green center bold header-small">ESTABLECIMIENTO DE SALUD</td>
                <td colspan="5" class="green center bold header-small">NUMERO DE HISTORIA CLINICA UNICA</td>
                <td colspan="3" class="green center bold header-small">NUMERO DE ARCHIVO</td>
                <td colspan="2" class="green center bold header-small">No. HOJA</td>
            </tr>
            <tr class="h-18">
                <td colspan="4" class="center data-cell">{{$institucion}}</td>
                <td colspan="2" class="center data-cell">{{$unicodigo}}</td>
                <td colspan="4" class="center data-cell">{{$establecimiento}}</td>
                <td colspan="5" class="center data-cell">{{$solicitud005->id_paciente}}</td>
                <td colspan="3"></td>
                <td colspan="2" class="center data-cell">{{$sheetNo}}</td>
            </tr>
            <tr class="h-18">
                <td colspan="4" rowspan="2" class="green center bold header-small">PRIMER APELLIDO</td>
                <td colspan="3" rowspan="2" class="green center bold header-small">SEGUNDO APELLIDO</td>
                <td colspan="3" rowspan="2" class="green center bold header-small">PRIMER NOMBRE</td>
                <td colspan="3" rowspan="2" class="green center bold header-small">SEGUNDO NOMBRE</td>
                <td colspan="2" rowspan="2" class="green center bold header-small">SEXO</td>
                <td colspan="1" rowspan="2" class="green center bold header-small">EDAD</td>
                <td colspan="4" class="green center bold header-small">CONDICIÓN EDAD<br>(MARCAR)</td>
            </tr>
            <tr class="h-995">
                <td colspan="1" class="green center bold header-small">H</td>
                <td colspan="1" class="green center bold header-small">D</td>
                <td colspan="1" class="green center bold header-small">M</td>
                <td colspan="1" class="green center bold header-small">A</td>
            </tr>
            <tr class="h-18">
                <td colspan="4" class="center data-cell">{{$apellido1}}</td>
                <td colspan="3" class="center data-cell">{{$apellido2}}</td>
                <td colspan="3" class="center data-cell">{{$nombre1}}</td>
                <td colspan="3" class="center data-cell">{{$nombre2}}</td>
                <td colspan="2" class="center data-cell">@if($sexo == '1') M @elseif($sexo == '2') F @endif</td>
                <td colspan="1" class="center data-cell">{{$edad}}</td>
                <td colspan="1"></td>
                <td colspan="1"></td>
                <td colspan="1"></td>
                <td colspan="1"></td>
            </tr>
            <tr class="h-995 blank-space">
                <td colspan="20"></td>
            </tr>
        @else
            <tr class="h-165">
                <td colspan="20" class="blue left title-a">A. DATOS DEL USUARIO / PACIENTE</td>
            </tr>
            <tr class="h-201">
                <td colspan="5" class="green center bold header-small">PRIMER APELLIDO</td>
                <td colspan="5" class="green center bold header-small">PRIMER NOMBRE</td>
                <td colspan="2" class="green center bold header-small">EDAD</td>
                <td colspan="5" class="green center bold header-small">NUMERO DE HISTORIA CLINICA UNICA</td>
                <td colspan="3" class="green center bold header-small">No. HOJA</td>
            </tr>
            <tr class="h-18">
                <td colspan="5" class="center data-cell">{{$apellido1}}</td>
                <td colspan="5" class="center data-cell">{{$nombre1}}</td>
                <td colspan="2" class="center data-cell">{{$edad}}</td>
                <td colspan="5" class="center data-cell">{{$solicitud005->id_paciente}}</td>
                <td colspan="3" class="center data-cell">{{$sheetNo}}</td>
            </tr>
            <tr class="h-995 blank-space">
                <td colspan="20"></td>
            </tr>
        @endif

        <tr class="h-165">
            <td colspan="8" class="blue left title-b"><b>B. EVOLUCION Y PRESCRIPCIONES</b></td>
            <td colspan="6" class="blue left note-small bold">FIRMAR AL PIE DE CADA EVOLUCION Y PRESCRIPCION</td>
            <td colspan="6" class="blue left note-tiny bold">REGISTRAR CON ROJO LA ADMINISTRACION DE FARMACOS Y COLOCACION DE DISPOSITIVOS MEDICOS</td>
        </tr>
        <tr>
            <td colspan="20" style="border:none; padding:0;">
                <table class="clinical-grid">
                    <colgroup>
                        <col style="width:18%">
                        <col style="width:18%">
                        <col style="width:19%">
                        <col style="width:35%">
                        <col style="width:10%">
                    </colgroup>
                    <tr class="h-1575">
                        <td colspan="3" width="55%" class="blue center sub-title evolution-edge"><b>1. EVOLUCION</b></td>
                        <td colspan="2" width="45%" class="blue center sub-title"><b>2. PRESCRIPCIONES</b></td>
                    </tr>
                    <tr class="h-2495">
                        <td width="18%" class="green center bold header-small">FECHA<br>(aaaa-mm-dd)</td>
                        <td width="18%" class="green center bold header-small">HORA<br>(hh:mm)</td>
                        <td width="19%" class="green center bold header-small evolution-edge">NOTAS DE EVOLUCION</td>
                        <td width="35%" class="green center bold header-small">FARMACOTERAPIA E INDICACIONES<br>(Para enfermeria y otro profesional de salud)</td>
                        <td width="10%" class="green center bold header-small">ADMINISTR.<br>FARMACOS<br>DISPOSITIVO</td>
                    </tr>
                    <tr class="evolution-content-row">
                        <td width="18%" class="center data-cell">{{$page['fecha']}}</td>
                        <td width="18%" class="center data-cell">{{$page['hora']}}</td>
                        <td width="19%" class="data-cell evolution-edge">{{$page['evolucion']}}</td>
                        <td width="35%" class="data-cell">
                            @foreach($page['prescriptions'] as $prescription)
                                <div class="prescription-item">{{$prescription['prescripcion']}}</div>
                            @endforeach
                        </td>
                        <td width="10%" class="center data-cell">
                            @foreach($page['prescriptions'] as $prescription)
                                <div class="prescription-item">{{$prescription['medicamento']}}</div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="evolution-signature-cell">
                            @if($page['signature'])
                                @php $signature = $page['signature']; @endphp
                                <div class="signature-block">
                                    <table class="electronic-signature">
                                        <tr>
                                            <td class="qr-cell">
                                                @if(!empty($signature['qr_source']))
                                                    <img src="{{$signature['qr_source']}}" alt="QR de validación de firma clínica">
                                                @endif
                                            </td>
                                            <td>
                                                Validar la firma escaneando el QR<br>
                                                Firmado electrónicamente por:<br>
                                                <span class="signature-name">{{$signature['name']}}</span><br>
                                                {{$signature['specialty']}}<br>
                                                <span class="signature-status">{{$signature['status']}}</span>
                                            </td>
                                        </tr>
                                    </table>
                                    @if(!empty($signature['register']))
                                        <div class="signature-register">REGISTRO PROFESIONAL: {{$signature['register']}}</div>
                                    @endif
                                    <div class="signature-hash">HUELLA: {{strtoupper(substr($signature['fingerprint'], 0, 24))}}</div>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
@endforeach
</body>

</html>
