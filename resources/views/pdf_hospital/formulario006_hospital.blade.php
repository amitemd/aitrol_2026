<?php
ob_start(function ($html) {
    return preg_replace('/(>\s*)N\/A(\s*<)/i', '$1$2', $html);
});
?>
<!DOCTYPE html>
<html>
@php
$fecha=date('Y-m-d');
$hora = date('H:i:s');
@endphp

@php
$usuarioLogueado = auth()->user();
$rd = rand();
$logoPath = public_path('assets/images/logo_certificado_borja.png');
$logoSrc = is_file($logoPath)
    ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
    : '';
$doctor = $doctor ?: (object) array(
    'id' => '', 'nombre1' => '', 'nombre2' => '',
    'apellido1' => '', 'apellido2' => '',
);
$professionalSex = optional($firma)->sexo ?: optional($doctor)->sexo;
$professionalPrefix = (string) $professionalSex === '2' ? 'Dra.' : 'Dr.';
$signatureProfessional = $epicrisisSignature['professional'] ?: $doctor;
$signatureSex = optional($signatureProfessional)->sexo;
$signaturePrefix = (string) $signatureSex === '2' ? 'Dra.' : 'Dr.';
$signatureDate = substr((string) $epicrisisSignature['signed_at'], 0, 10);
$signatureTime = substr((string) $epicrisisSignature['signed_at'], 11, 8);
@endphp

<?php
$fechaNacimiento = new DateTime($data->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($data->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($data->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');
?>

<head>
    <meta charset="utf-8">
    <title>formulario 006</title>
    <style>
        @page {
            margin: 15 0;
        }

        /* Define now the real margins of every page in the PDF */
        body {
            margin-top: 40px;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 1px;
            font-size: 10px;
        }

        /* Define the header rules */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        /* Define the footer rules */
        footer {
            display: table;
            position: fixed;
            bottom: 30px;
            left: 0cm;
            right: 0cm;
            width: 100%;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            border-top: 1px solid #999;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            padding-top: 8px;
            table-layout: fixed;
        }
        footer > span { display: table-cell; width: 50%; }
        footer .document-name { text-align: right; }
        .page-number:after { content: counter(page); }

        table {
            width: 100px;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
        }

        p {
            padding-top: 0;
            padding-bottom: 0;
            margin: 0;
        }

        div {
            padding-top: 0;
            padding-bottom: 0;
        }

        .txt_vertical {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
            letter-spacing: 2px;
            word-wrap: break-word;
        }

        .td-border {
            border: 1px solid;
        }

        .td-not-border-bottom {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-top {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-right {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-top: 1px solid;
        }

        .td-not-border-left {
            border-bottom: 1px solid;
            border-top: 1px solid;
            border-right: 1px solid;
        }

        .td-border-top {
            border-top: 1px solid;
        }

        .td-border-bottom {
            border-bottom: 1px solid;
        }

        .td-border-left {
            border-left: 1px solid;
        }

        .td-border-right {
            border-right: 1px solid;
        }

        .bg-blue {
            background-color: #DDEBF7;
        }

        .bg-green {
            background-color: #BADAC8;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        .form006-signature-cell {
            padding: 3px;
            vertical-align: middle;
        }

        .form006-electronic-signature {
            width: 100%;
            border: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .form006-electronic-signature td {
            border: 0;
            padding: 1px 3px;
            vertical-align: middle;
            font-size: 8px;
            line-height: 1.1;
        }

        .form006-electronic-signature .qr-cell {
            width: 70px;
            text-align: center;
        }

        .form006-qr {
            width: 64px;
            height: 64px;
        }

        .form006-signature-name {
            font-size: 10px;
            font-weight: 700;
        }

        .form006-signature-status,
        .form006-no-signature,
        .form006-no-seal {
            font-weight: 700;
        }

        .form006-signature-hash {
            margin-top: 2px;
            font-size: 7px;
            text-align: center;
        }

        .form006-manual-signature {
            max-width: 150px;
            max-height: 68px;
        }

        .form006-seal-image {
            max-width: 125px;
            max-height: 72px;
        }
    </style>
</head>

<body>
    <footer><span>SNS-MSP / HCU-form.006 / 2021</span><span class="document-name">EPICRISIS (<span class="page-number"></span>)</span></footer>
    <header>

    </header>
    <main>

        @php
        $id_empresa = Session::get('id_empresa');
        $agenda = Sis_medico\Agenda::find($data->id_agenda);
$empresa = Sis_medico\Empresa::find($id_empresa);
        
        @endphp
        <div class="box-body card" style="text-align: center; padding:0; margin:0;">
            @if($logoSrc !== '')
            <p><img src="{{ $logoSrc }}" alt="Clínica Borja" style="width:150px;height: 50px"></p>
            @endif
        </div><br>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="12" class="bg-blue"><b>A. DATOS DEL ESTABasdasdasdLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width=118 colspan=2 class="bg-green text-center" valign=top style='width:105pt;border:solid windowtext 1.0pt;
                    padding:0cm 5.4pt 0cm 5.4pt;'>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal;'><b><span>INSTITUCIÓN DEL SISTEMA </span></b></p>
                    </td>
                    <td colspan="2" class="bg-green text-center"><b>UNICÓDIGO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                    <td colspan="2" class="bg-green text-center"><b>No. HOJA</b></td>
                </tr>
                <tr>
                    <td colspan="2" valign=top style='width:88.3pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal'><span style='font-size:12px;'>{{optional(optional($historia)->seguro)->nombre ?: 'No registrado'}} </span></p>
                    </td>
                    <td colspan="2" class="text-center"  style='font-size:12px;'>{{optional($empresa)->unicodigo ?: 'No registrado'}}</td>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal'><span style='font-size:12px;'>{{optional($empresa)->nombrecomercial ?: 'No registrado'}} </span></p>
                    </td>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal'><span style='font-size:12px;'>{{$data->id_paciente}}</span></p>
                    </td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" colspan="1" class="bg-green text-center">
                        <p align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>SEXO</span></b></p>
                    </td>
                    <td rowspan="2" colspan="1" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center"><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->apellido1}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->apellido2}}</span></p>
                    </td>
                    <td colspan="2">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->nombre1}}</span></p>
                    </td>
                    <td colspan="1">
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                        text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->nombre2}}</span></p>
                    </td>
                    <td colspan="1" valign=top style='width:56.25pt;border-top:none;border-left:
                    none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
                    padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                    text-align:center;line-height:normal'><span style='font-size:9.0pt'>@if($data->sexo=='1')MASCULINO @elseif($data->sexo=='2')FEMENINO @endif</span></p>
                    </td>
                    <td class="text-center" style="font-size: 11px;" colspan="1">{{$edad}}</td>
                    <td colspan="1" class="text-center">{{$diaNacimiento}}</td>
                    <td colspan="1"></td>
                    <td colspan="1" class="text-center">{{$mesNacimiento}}</td>
                    <td colspan="1" class="text-center">{{date('Y', strtotime($data->fecha_nacimiento))}}</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td width=576 colspan=8 class="bg-blue" valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt;'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><b><span style='font-size:10.0pt'>RESUMEN DE CUADRO CLINICO</span></b></p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width=600 height=100 colspan=8 valign=top style='width:500pt;height: 150pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;'><span class="parrafo2" style='font-size:11px !important;line-height: 1.2 !important;'>
                                <?php echo $epicrisis->cuadro_clinico ?></span> <span class="parrafo2" style='font-size:11px !important;line-height: 1.2 !important;'>
                                @if($epicrisis_original !=null) <?php echo $epicrisis_original->favorable_des; ?>@else <?php echo $epicrisis->favorable_des; ?> @endif </span></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td width=576 colspan=8 valign=top class="bg-blue" style='width:432.05pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt;'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><b><span style='font-size:10.0pt'>C. RESUMEN DE EVOLUCIÓN Y COMPLICACIONES</span></b></p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width=288 colspan=4 valign=top class="bg-blue" style='width:216.025pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt;'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><b><span style='font-size:9pt'>Evolución</span></b></p>
                    </td>
                    <td width=288 colspan=4 valign=top class="bg-blue" style='width:216.025pt;border-top:none;
                    border-rigth:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
                    padding:0cm 5.4pt 0cm 5.4pt;'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><b><span style='font-size:9.0pt'>Complicación</span></b></p>
                    </td>
                </tr>
                <tr>
                    <td width=288 colspan=4 valign=top style='width:216.025pt;border:solid windowtext 1.0pt;
                    border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><span style='font-size:11px !important'> @if($epicrisis_original != null) {{$epicrisis->ep_resumen_evolucion}} @else  {{$epicrisis->ep_resumen_evolucion}} @endif</span> <br></p>
                    </td>
                    <td width=288 colspan=4 valign=top style='width:216.025pt;border-top:none;
                    border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
                    padding:0cm 5.4pt 0cm 5.4pt'>
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal'><span style='font-size:11px'> @if($epicrisis_original != null)  {{$epicrisis->complicacion}} @else {{$epicrisis->complicacion}} @endif <br></span><br></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>D. HALLAZGOS RELEVANTES DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 70px;">
                        <p class=MsoNormal style='margin-top: 0;margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    1.2 !important'><span class="parrafo2" style='font-size:11px !important'><b>Hallazgos:</b>
                                <?php echo trim((string) $epicrisis->hallazgo) !== ''
                                    ? $epicrisis->hallazgo
                                    : ($epicrisis->conclusion ?: (optional($protocolo)->conclusion ?? '')); ?></span></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="page-break-before">

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-blue"><b>DATOS DEL USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                    <td class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td class="bg-green text-center"><b>EDAD</b></td>
                    <td class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td class="bg-green"><b>NÚMERO DE ARCHIVO</b></td>
                </tr>
                <tr style="height: 15px;">
                    <td>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                            text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->apellido1}}</span></p>
                    </td>
                    <td>
                        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
                            text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->nombre1}}</span></p>
                    </td>
                    <td class="text-center" style="font-size: 11px;" colspan="1">{{$edad}}</td>
                    <td class="text-center">{{$data->id_paciente}}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>E. RESUMEN DE TRATAMIENTO Y PROCEDIMIENTOS TERAPÉUTICOS </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 100px;">
                        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:10px !important;'><?php echo trim((string) $epicrisis->resumen) !== ''
                            ? $epicrisis->resumen
                            : (optional($protocolo)->hallazgos ?? ''); ?> <br><br><br></span></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td style="font-size: 14px;" class="bg-blue"><b>F. INDICACIONES DE ALTA / EGRESO </b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="width:14%; height: 50px;"><?php echo ($epicrisis->indicaciones_alta) ?></td>
                </tr>
                <tr>
                    <td class="td-border" style="width:10%; height: 15px;">PRÓXIMO CONTROL</td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="2" style="font-size: 12px;" class="bg-blue"><b>CONDICIÓN Y PRONÓSTICO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%; height: 28px;"><b>Condición:</b> <?php echo $epicrisis->condicion ?? ''; ?></td>
                    <td style="width: 50%; height: 28px;"><b>Pronóstico:</b> <?php echo $epicrisis->pronostico ?? ''; ?></td>
                </tr>
            </tbody>
        </table>
        <br>

        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="2" style="font-size: 14px;" class="bg-blue"><b>G. DIAGNOSTICO DE ALTA / EGRESO </b></td>
                    <td style="font-size: 14px;" class="bg-blue text-center"><b>CIE</b></td>
                </tr>
            </thead>
            <tbody>

                @foreach ($cie10 as $key=>$val)
                @php
                //dd($cie10e);
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                $texto = '';
                if(!is_null($c3)){
                    $texto = $c3->descripcion;
                } elseif(!is_null($c4)){
                    $texto = $c4->descripcion;
                } else {
                    $texto = 'Diagnóstico no encontrado';
                }
                @endphp

                <tr>

                    <td style="width: 30%; font-size: 8;" class="text-center"><b>DIAGNÓSTICO PRINCIPAL</b></td>
                    <td style="width: 75%;">{{$texto}}</td>
                    <td class="text-center td-border">{{$val->cie10}}</td>

                </tr>
                @endforeach

                @foreach ($cie10e as $key=>$val)
                @php
                //dd($cie10e);
                $c3 = Sis_medico\Cie_10_3::find($val->cie10);
                $c4 = Sis_medico\Cie_10_4::find($val->cie10);
                $texto = '';
                if(!is_null($c3)){
                    $texto = $c3->descripcion;
                } elseif(!is_null($c4)){
                    $texto = $c4->descripcion;
                } else {
                    $texto = 'Diagnóstico no encontrado';
                }
                @endphp
                <tr>
                    <td style="width: 30%; font-size: 8;" class="text-center"><b>DIAGNÓSTICO SECUNDARIO</b></td>
                    <td style="width: 75%;">{{$texto}}</td>
                    <td class="text-center td-border" style="width: 17%;">{{$val->cie10}}</td>
                </tr>

                @endforeach

                <tr>
                    <td style="width: 30%; font-size: 8;" class="text-center"><b>CAUSA EXTERNA</b></td>
                    <td style="width: 75%;"></td>
                    <td class="text-center td-border" style="width: 17%;"></td>
                </tr>


            </tbody>
        </table>
        
        <br>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="7" style="font-size: 12px;" class="bg-blue"><b>H. CONDICIÓN DE ALTA / EGRESO</b></td>
                    <td style="font-size: 12px;" class="bg-blue text-center"><b>VIVO</b></td>
                    <td style=" width: 6%;">&nbsp;</td>
                    <td class="bg-blue" style="font-size: 12px; width: 9%;"><b>FALLECIDO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" style="height: 18px; width: 12%;"><b>ALTA MÉDICA</b></td>
                    <td style="width: 4%;" class="text-center">@if($epicrisis->alta == 'MEDICA') X @else @if($epicrisis->alta == null) X @endif @endif</td>
                    <td class="text-center" rowspan="2" style="height: 18px;"><b>DISCAPACIDAD</b></td>
                    <td class="text-center" rowspan="2" colspan="2" style="height: 18px; ">{{$epicrisis->discapacidad}}</td>
                    <td class="text-center" style="height: 18px;"><b>RETIRO NO AUTORIZADO</b></td>
                    <td class="text-center" style="height: 18px; width: 4%;">@if($epicrisis->retiro == 'NO AUTORIZADO') X @endif</td>
                    <td class="text-center" style="height: 18px; width: 8%;"><b>DEFUNCIÓN MENOS DE 48 HORAS</b></td>
                    <td class="text-center" style="height: 18px;">@if($epicrisis->defuncion == 'MENOS DE 48H') X @endif </td>
                    <td class="text-center" style="height: 18px; width: 8%;"><b>DÍAS DE ESTADIA</b></td>
                    <td class="text-center" style="height: 18px; width: 4%; font-size: 10;">{{$epicrisis->dias_estadia}}</td>

                </tr>
                <tr>

                    <td class="text-center" style="height: 18px; width: 12%;"><b>ALTA VOLUNTARIA</b></td>
                    <td class="text-center">@if($epicrisis->alta == 'VOLUNTARIA') X @endif</td>
                    <td class="text-center" style="height: 18px;"><b>AUTORIZADO</b></td>
                    <td class="text-center" style="height: 18px; width: 4%;">@if($epicrisis->retiro == 'AUTORIZADO') X @endif</td>
                    <td class="text-center" style="height: 18px; width: 18%;"><b>DEFUNCIÓN MAS DE 48 HORAS</b></td>
                    <td class="text-center">@if($epicrisis->defuncion == 'MAS DE 48H') X @endif </td>
                    <td class="text-center" style="height: 18px; width: 12%;"><b>DÍAS DE REPOSO</b></td>
                    <td class="text-center" style="font-size: 10;">{{$epicrisis->dias_incapacidad}}</td>

                </tr>
            </tbody>
        </table>


        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-blue"><b>I. MÉDICOS TRATANTES</b></td>
                </tr>
            </thead>
            <tr>
                <td colspan="2" class="bg-green text-center"><b>NOMBRE Y APELLIDOS</b></td>
                <td class="bg-green text-center"><b>ESPECIALIDAD</b></td>
                <td style="width: 35%; height: 15px;" class="bg-green text-center"><b>SELLO Y NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN DEL PROFESIONAL</b></td>
                <td class="bg-green text-center"><b>PERÍODO DE RESPONSABILIDAD</b></td>
            </tr>
            <tr>
                <td class="bg-green text-center" style="width: 1%; height: 20px;"><b>1.</b></td>
                <td style="width: 30%; height: 30px;">@if(!empty($doctor->id)) {{$professionalPrefix}} {{$doctor->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}} @endif </td>
                <td class="text-center"> @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif</td>
                <td class="text-center"> @if(!empty($doctor->id)) {{$professionalPrefix}} {{$doctor->apellido1}} @if($doctor->apellido2!='N/A'){{$doctor->apellido2}}@endif {{$doctor->nombre1}} @if($doctor->nombre2!='N/A'){{$doctor->nombre2}}@endif @endif<br>
                    @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                    Cedula No. {{optional($doctor)->id ?: 'No registrado'}}</td>
                <td>{{substr((string) $solicitud->fecha_ingreso, 0, 10)}} @if($solicitud->fecha_salida) al {{substr((string) $solicitud->fecha_salida, 0, 10)}} @endif</td>
            </tr>

            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <td colspan="5" style="font-size: 14px;" class="bg-blue"><b>J. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
                </tr>
            </thead>

            <tr>
                <td style="width:14%; height: 10px;" class="td-border bg-green text-center"><b>FECHA</b></td>
                <td style="width:8%; height: 10px;" class="td-border bg-green text-center"><b>HORA</b></td>
                <td style="width:20%" class="td-border bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td style="width:20%" class="td-border bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td style="width:15%" class="td-border bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
            </tr>
            <tr>
                <td class="td-border text-center" style="width:5%; height: 18px;">
                    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                            normal'><span style='font-size: 12px;'>{{$signatureDate}}</span></p>
                </td>
                <td class="td-border text-center" style="width:5%; height: 18px; font-size:12px;">{{$signatureTime}}</td>
                <td class="td-border" style="width:14%; height: 18px;">
                    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                            normal'><span style='font-size: 12px;'>@if(!empty($signatureProfessional->id)) {{$signaturePrefix}} {{$signatureProfessional->nombre1}} @endif</span></p>
                </td>
                <td class="td-border" style="width:14%; height: 18px;">
                    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                            normal'><span style='font-size: 12px;'>@if(!empty($signatureProfessional->id)) {{$signatureProfessional->apellido1}} @endif</span></p>
                </td>
                <td class="td-border" style="width:14%; height: 18px;">
                    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                            normal'><span style='font-size: 12px;'>@if(!empty($signatureProfessional->id)) {{$signatureProfessional->apellido2}} @endif</span></p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td-border bg-green text-center"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
                <td colspan="2" class="td-border bg-green text-center"><b>FIRMA</b></td>
                <td class="td-border bg-green text-center"><b>SELLO</b></td>
            </tr>
            <tr style="height: auto;">
                <td colspan="2" class="td-border text-center" style="width:12%; height:78px; font-size: 12px;">{{optional($signatureProfessional)->id ?: 'No registrado'}}</td>
                <td colspan="2" class="td-border form006-signature-cell" style="width:25%; text-align: center;">
                    @if(!empty($epicrisisSignature['electronic']))
                        <table class="form006-electronic-signature">
                            <tr>
                                <td class="qr-cell">
                                    @if(!empty($epicrisisSignature['qr_source']))
                                        <img class="form006-qr" src="{{$epicrisisSignature['qr_source']}}" alt="QR de validación de firma electrónica">
                                    @endif
                                </td>
                                <td>
                                    Validar la firma escaneando el QR<br>
                                    Firmado electrónicamente por:<br>
                                    <span class="form006-signature-name">{{$epicrisisSignature['name']}}</span><br>
                                    {{$epicrisisSignature['specialty']}}<br>
                                    <span class="form006-signature-status">{{$epicrisisSignature['status']}}</span>
                                </td>
                            </tr>
                        </table>
                        @if(!empty($epicrisisSignature['register']))
                            <div>REGISTRO PROFESIONAL: {{$epicrisisSignature['register']}}</div>
                        @endif
                        <div class="form006-signature-hash">HUELLA: {{strtoupper(substr($epicrisisSignature['fingerprint'], 0, 24))}}</div>
                    @elseif(!empty($epicrisisSignature['signature_image']))
                        <img class="form006-manual-signature" src="{{$epicrisisSignature['signature_image']}}" alt="Firma manuscrita registrada"><br>
                        <span class="form006-signature-name">{{$epicrisisSignature['name']}}</span><br>
                        {{$epicrisisSignature['specialty']}}
                    @else
                        <span class="form006-no-signature">SIN FIRMA</span>
                    @endif
                </td>
                <td class="td-border" style="width:25%; height:78px; text-align: center;">
                    @if(!empty($epicrisisSignature['seal_image']))
                        <img class="form006-seal-image" src="{{$epicrisisSignature['seal_image']}}" alt="Sello registrado del profesional">
                    @else
                        <span class="form006-no-seal">SIN SELLO</span>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tbody>
                <tr>
                    <td class="bg-green" style="width:24%; height:17px;">ELABORADO POR:</td>
                    <td style="width:38%; height:17px;"> @if(!empty($signatureProfessional->id)) {{$signaturePrefix}} {{$signatureProfessional->apellido1}} @if($signatureProfessional->apellido2!='N/A'){{$signatureProfessional->apellido2}}@endif {{$signatureProfessional->nombre1}} @if($signatureProfessional->nombre2!='N/A'){{$signatureProfessional->nombre2}}@endif @endif</td>
                    <td class="bg-green" style="width:25%; height:17px;">REVISADO POR:</td>
                    <td style="width:30%; height:17px;">  @if(!empty($signatureProfessional->id)) {{$signaturePrefix}} {{$signatureProfessional->apellido1}} @if($signatureProfessional->apellido2!='N/A'){{$signatureProfessional->apellido2}}@endif {{$signatureProfessional->nombre1}} @if($signatureProfessional->nombre2!='N/A'){{$signatureProfessional->nombre2}}@endif @endif</td>
                </tr>
            </tbody>
        </table>
