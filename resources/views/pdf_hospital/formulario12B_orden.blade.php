@php
    $paciente12B = $variables->paciente;
    $empresa12B = $variables->empresa;
    $doctor12B = $variables->doctor1;
    $fechaNacimiento12B = !empty($paciente12B->fecha_nacimiento)
        ? new DateTime($paciente12B->fecha_nacimiento)
        : null;
    $edad12B = $fechaNacimiento12B ? (new DateTime())->diff($fechaNacimiento12B)->y : '';
    $fechaOrden12B = !empty($variables->created_at)
        ? date('Y-m-d', strtotime($variables->created_at))
        : date('Y-m-d');
    $nombreSolicitante12B = $doctor12B
        ? trim($doctor12B->nombre1.' '.$doctor12B->apellido1.' '.$doctor12B->apellido2)
        : '';
@endphp

<div class="page-break-before">
    <table border="1" style="width: 99%">
        <tr>
            <td colspan="14">
                <img src="{{base_path().'/storage/app/logo/cabecera.jpg'}}" style="width:750px;height: 80px;">
            </td>
        </tr>
    </table>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bg-green text-center" style="width: 20%;"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                <td colspan="2" class="bg-green text-center"><b>UNICÓDIGO</b></td>
                <td colspan="2" class="bg-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                <td colspan="4" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 12px;">IESS</td>
                <td colspan="2" class="text-center" style="font-size: 12px;">{{optional($empresa12B)->unicodigo}}</td>
                <td colspan="2" class="text-center" style="font-size: 12px;">{{optional($empresa12B)->nombrecomercial ?: optional($empresa12B)->nombre_corto}}</td>
                <td class="text-center" colspan="4" style="font-size: 12px;">{{$paciente12B->id}}</td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td rowspan="2" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                <td rowspan="2" class="bg-green text-center"><b>SEXO</b></td>
                <td rowspan="2" colspan="2" class="bg-green text-center"><b>EDAD</b></td>
                <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
            </tr>
            <tr>
                <td class="bg-green text-center">H</td>
                <td class="bg-green text-center">D</td>
                <td class="bg-green text-center">M</td>
                <td class="bg-green text-center">A</td>
            </tr>
            <tr>
                <td style="height: 18px;" class="text-center" colspan="2">{{$paciente12B->apellido1}}</td>
                <td class="text-center" colspan="2">{{$paciente12B->apellido2}}</td>
                <td class="text-center" colspan="2">{{$paciente12B->nombre1}}</td>
                <td class="text-center">{{$paciente12B->nombre2}}</td>
                <td class="text-center">@if($paciente12B->sexo == 1) Masculino @elseif($paciente12B->sexo == 2) Femenino @endif</td>
                <td class="text-center" colspan="2">{{$edad12B}}</td>
                <td></td><td></td><td></td><td class="text-center">@if($fechaNacimiento12B) X @endif</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" style="width: 100%">
        <thead>
            <tr><td colspan="10" class="bg-blue"><b>B. DATOS DEL SERVICIO</b></td></tr>
        </thead>
        <tbody>
            <tr>
                <td class="bg-green text-center"><b>PROFESIONAL QUE REALIZA</b></td>
                <td class="bg-green text-center"><b>FECHA DE REALIZACIÓN</b></td>
                <td class="bg-green text-center"><b>PROFESIONAL SOLICITANTE</b></td>
                <td colspan="6" class="bg-green text-center"><b>SERVICIO</b></td>
                <td class="bg-green text-center"><b>ESPECIALIDAD</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td class="text-center">{{$nombreSolicitante12B}}</td>
                <td colspan="6" class="text-center">{{$variables->servicio}}</td>
                <td class="text-center">{{optional($variables->doctor)->ocupacion}}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr><td colspan="21" style="font-size: 14px;" class="bg-blue"><b>C. ESTUDIO DE IMAGENOLOGÍA REALIZADO</b></td></tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">RX CONVENCIONAL</td><td class="text-center" style="width:2%">@if($variables->rx_convencional == 1) X @endif</td>
                <td class="text-center">RX PORTÁTIL</td><td class="text-center" style="width:2%">@if($variables->rx_portatil == 1) X @endif</td>
                <td class="text-center">TOMOGRAFÍA</td><td class="text-center" style="width:2%">@if($variables->tomografia == 1) X @endif</td>
                <td class="text-center">RESONANCIA</td><td class="text-center" style="width:2%">@if($variables->resonancia == 1) X @endif</td>
                <td class="text-center">ECOGRAFÍA</td><td class="text-center" style="width:2%">@if($variables->ecografia == 1) X @endif</td>
                <td class="text-center">MAMOGRAFÍA</td><td class="text-center" style="width:2%">@if($variables->mamografia == 1) X @endif</td>
                <td class="text-center">PROCEDIMIENTO</td><td class="text-center" style="width:2%">@if($variables->procedimiento == 1) X @endif</td>
                <td class="text-center">OTRO</td><td class="text-center" style="width:2%">@if($variables->otros == 1) X @endif</td>
                <td class="text-center">SEDACIÓN</td>
                <td class="text-center" style="width:3%">SÍ</td><td class="text-center" style="width:3%">@if($variables->sedacion === 1 || $variables->sedacion === '1') X @endif</td>
                <td class="text-center">NO</td><td class="text-center" style="width:4%">@if($variables->sedacion === 0 || $variables->sedacion === '0') X @endif</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center"><b>DESCRIPCIÓN</b></td>
                <td colspan="19" style="height: 20px;">{{$variables->descripcion ?: $variables->texto_otros}}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
        <thead><tr><td style="font-size: 14px;" class="bg-blue"><b>D. HALLAZGOS POR IMAGENOLOGÍA</b></td></tr></thead>
        <tbody><tr><td class="td-border" style="height: 105px;"></td></tr></tbody>
    </table>
    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
        <thead><tr><td style="font-size: 14px;" class="bg-blue"><b>E. CONCLUSIONES Y SUGERENCIAS</b></td></tr></thead>
        <tbody><tr><td class="td-border" style="height: 105px;"></td></tr></tbody>
    </table>
    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
        <thead><tr><td colspan="5" style="font-size: 14px;" class="bg-blue"><b>G. DATOS DEL PROFESIONAL RESPONSABLE</b></td></tr></thead>
        <tbody>
            <tr>
                <td class="td-border text-center bg-green"><b>FECHA</b></td>
                <td class="td-border text-center bg-green"><b>HORA</b></td>
                <td class="td-border text-center bg-green"><b>PRIMER NOMBRE</b></td>
                <td class="td-border text-center bg-green"><b>PRIMER APELLIDO</b></td>
                <td class="td-border text-center bg-green"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr><td class="td-border" style="height: 18px;"></td><td class="td-border"></td><td class="td-border"></td><td class="td-border"></td><td class="td-border"></td></tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr><td colspan="2" class="td-border" style="height: 60px;"></td><td colspan="2" class="td-border"></td><td class="td-border"></td></tr>
            <tr>
                <td colspan="5"><b>La aproximación diagnóstica emitida en el presente informe constituye una prueba complementaria al diagnóstico clínico definitivo; se recomienda correlacionar con antecedentes, datos clínicos, exámenes de laboratorio y seguimiento imagenológico.</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>SNS-MSP/HCU-form.012B/2021</b></td>
                <td colspan="3" class="text-right"><b>IMAGENOLOGÍA - INFORME (1)</b></td>
            </tr>
        </tbody>
    </table>
</div>
