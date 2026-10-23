<html>


@php
//$fecha = date("Y-m-d")  $anamnesis->historiaclinica->agenda->fechaini;
//$hora = date('H:i:s');
$fecha = substr($anamnesis->historiaclinica->agenda->fechaini,0,10);
$hora = substr($anamnesis->historiaclinica->agenda->fechaini,11);
@endphp

<?php

$fechaNacimiento = new DateTime($anamnesis->historiaclinica->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

?>

<head>
    <title> ANAMENESIS </title>
    <style>
        table,
        td,
        th {
            border: 1px solid;
        }

        table {
            width: 100%;
            border: 2px solid;
            border-collapse: collapse;
            white-space: normal;
        }

        @page {
            margin: 0.1cm 0.5cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {}

        header {}

        table {}

        .titulo {}

        span {}

        p {}

        footer {

            /*background-image: url(""); 
            background-size: 50%; */
        }

        .table1 {
            font-size: 12px;
            text-align: center;
        }

        .table2 {
            font-size: 12px;
            text-align: center;
        }

        .table3 {
            font-size: 12px;
            text-align: center;
        }

        .table4 {
            font-size: 12px;
            text-align: center;
        }

        .table5 {
            font-size: 12px;
            text-align: center;
        }

        .table6 {
            font-size: 12px;
            text-align: center;
        }

        .table7 {
            font-size: 10px;
            text-align: center;
        }

        .table8 {
            font-size: 12px;
            text-align: center;
        }

        .table9 {
            font-size: 12px;
            text-align: center;
        }

        .table10 {
            font-size: 12px;
            text-align: center;
        }

        .table11 {
            font-size: 12px;
            text-align: center;
        }

        .table12 {
            font-size: 12px;
            text-align: center;
        }
        .page-break-before {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <main>
        @php
        $id_empresa = Session::get('id_empresa');
        $empresa = Sis_medico\Empresa::find($id_empresa);

        @endphp
        <div class="box-body card" style="text-align: center">
            @if(!is_null($empresa))
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 75px">
            @endif
        </div><br>
        <table class="table1">
            <tr>
                <TH bgcolor="#B4A7FD" ; COLSPAN="6" ; align="left" style="font-size:12px">A. DATOS DEL ESTABLECIMIENTO Y USUARIO PACIENTE</th>
            </tr>

            <tr>
                <td bgcolor="#BBFDAC" align="center"><b>INSTITUCIÓN DEL SISTEMA</b></td>
                <td bgcolor="#BBFDAC" align="center"><b>UNICODIGO</b></td>
                <td bgcolor="#BBFDAC" align="center"><b>ESTABLECIMIENTO DE SALUD</b></td>
                <td bgcolor="#BBFDAC" align="center"><b>NUMERO DE HISTORIA CLINICA</b></td>
                <td bgcolor="#BBFDAC" align="center"><b>NUMERO DE ARCHIVO</b></td>
                <td bgcolor="#BBFDAC" align="center"><b>No.HOJA</b></td>
            </tr>
            <tr>
                <td>{{$anamnesis->historiaclinica->seguro->nombre}} </td>
                <td>@isset($anamnesis->historiaclinica->agenda->empresa){{$anamnesis->historiaclinica->agenda->empresa->unicodigo}} @endif</td>
                <td>@isset($anamnesis->historiaclinica->agenda->empresa){{$anamnesis->historiaclinica->agenda->empresa->nombrecomercial}} @endif</td>
                <td>{{$anamnesis->historiaclinica->paciente->id}}</td>
                <td></td>
                <td>1</td>
            </tr>
            </tbody>
        </table>
        <table class="table2">
            <tbody>
                <tr>
                    <td bgcolor="#BBFDAC" align="center"><b>PRIMER APELLIDO</b></td>
                    <td bgcolor="#BBFDAC" align="center"><b>SEGUNDO APELLIDO</b></td>
                    <td bgcolor="#BBFDAC" align="center"><b>PRIMER NOMBRE</b></td>
                    <td bgcolor="#BBFDAC" align="center"><b>SEGUNDO NOMBRE</b></td>
                    <td bgcolor="#BBFDAC" align="center"><b>SEXO</b></td>
                    <td bgcolor="#BBFDAC" align="center"><b>EDAD</b></td>
                </tr>
                <tr>
                    <td>{{$anamnesis->historiaclinica->paciente->apellido1}}</td>
                    <td>{{$anamnesis->historiaclinica->paciente->apellido2}}</td>
                    <td>{{$anamnesis->historiaclinica->paciente->nombre1}}</td>
                    <td>{{$anamnesis->historiaclinica->paciente->nombre2}}</td>
                    <td style="height: 25px;">@if($anamnesis->historiaclinica->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                    <td>{{$edad}}</td>
                </tr>
            </tbody>
        </table>


        <table class="table3">
            <tbody>
                <tr>
                    <th bgcolor="#B4A7FD" ; align="left">B. MOTIVOS DE LA CONSULTA</th>
                    <th bgcolor="#B4A7FD" ; align="left">PRIMERA</th>
                    <th>@if($anamnesis->motivo_consulta == '0') X @endif</th>
                    <th bgcolor="#B4A7FD" ; align="left">SUBSECUENTE</th>
                    <th>@if($anamnesis->motivo_consulta == '1') X @endif</th>
                </tr>
                <tr>
                    <td colspan="5" align="left" ><div style="font-size: 10px !important;"><?php echo $anamnesis->motivo_evolucion; ?></div></td>
                </tr>
            </tbody>
        </table>

        <table class="table4">

            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="20" ; align="left" style="font-size:12px">C. ANTECEDENTES PATOLÓGICOS PERSONALES</th>
            </tr>
            <tr>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">1.CARDIOPATÍA</td>
                <th style="height: 45px;" class="text-center"><b>@if($anamnesis->ap_cardiopatia == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">2. <br> HIPERTENSIÓN</td>
                <th class="text-center"><b>@if($anamnesis->ap_hipertension == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">3.ENF.C.<br> VASCULAR</td>
                <th class="text-center"><b>@if($anamnesis->ap_vascular == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">4. ENDÓCRINO <br>METABOLICO</td>
                <th class="text-center"><b>@if($anamnesis->ap_endocrino == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">5. CÁNCER</td>
                <th class="text-center"><b>@if($anamnesis->ap_cancer == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">6. TUBERCULOSIS</td>
                <th class="text-center"><b>@if($anamnesis->ap_tuberculosis == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">7. ENF. <br> MENTAL</td>
                <th class="text-center"><b>@if($anamnesis->ap_enf_mental == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">8. ENF. <br> INFECCIOSA</td>
                <th class="text-center"><b>@if($anamnesis->ap_enf_infecciosa == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">9. MAL <br> FORMACIÓN</td>
                <th class="text-center"><b>@if($anamnesis->ap_mal_formacion == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">10. OTRO</td>
                <th class="text-center"><b>@if($anamnesis->ap_otro == '1') X @endif</b></th>
            </tr>

            <tr>
                <th colspan="20" style="font-size:10px">{{$anamnesis->historiaclinica->paciente->antecedentes_pat}}</th>
            </tr>


        </table>

        <table class="table5">
            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="20" ; align="left">D. ANTECEDENTES PATOLÓGICOS FAMILIARES</th>
            </tr>
            <tr>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">1.CARDIOPATÍA</td>
                <th class="text-center"><b>@if($anamnesis->af_cardiopatia == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">2. <br> HIPERTENSIÓN</td>
                <th class="text-center"><b>@if($anamnesis->af_hipertension == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">3.ENF.C.<br> VASCULAR</td>
                <th class="text-center"><b>@if($anamnesis->af_vascular == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">4. ENDÓCRINO <br>METABOLICO</td>
                <th class="text-center"><b>@if($anamnesis->af_endocrino == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">5. CÁNCER</td>
                <th class="text-center"><b>@if($anamnesis->af_cancer == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">6. TUBERCULOSIS</td>
                <th class="text-center"><b>@if($anamnesis->af_tuberculosis == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">7. ENF. <br> MENTAL</td>
                <th class="text-center"><b>@if($anamnesis->af_enf_mental == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">8. ENF. <br> INFECCIOSA</td>
                <th height="30"><b>@if($anamnesis->af_infecciosa == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">9. MAL <br> FORMACIÓN</td>
                <th class="text-center"><b>@if($anamnesis->af_mal_formacion == '1') X @endif</b></th>
                <td bgcolor="#BBFDAC" align="center" style="font-size:8px">10. OTRO</td>
                <th class="text-center"><b>@if($anamnesis->af_otro == '1') X @endif</b></th>
            </tr>

            <tr>
                <td colspan="20" style="font-size:10px">{{$anamnesis->historiaclinica->paciente->antecedentes_fam}}</td>
            </tr>
        </table>

        <table class="table6">
            <tr>
                <th bgcolor="#B4A7FD" ; align="left" style="font-size:12px">E. ENFERMEDAD O PROBLEMA ACTUAL </th>
                <th bgcolor="#B4A7FD" ; align="right" style="font-size:8px">CRONOLOGIA-LOCALIZACIÒN-CARACTERISTICAS- IDENTIDAD-FRECUENCIA <br>-FACTORES AGRAVANTES</th>
            </tr>
            @php 
                $txt_html  = '' ;
                $txt_html = trim($anamnesis->problema_actual);
                $txt_html = preg_replace('/\s+/', ' ', $txt_html);
            @endphp
            <tr>
                <td colspan="2" align="left" style="font-size:10px"><div style="font-size: 10px !important;"><?php echo $txt_html; ?></div></td>
            </tr>

        </table>

        <table class="table7">
            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="12" ; align="left" style="font-size:12px">F. CONSTANTES VITALES Y ANTROPOMETRIA</th>
            </tr>
            <tr>
                <td bgcolor="#BBFDAC" align="center">FECHA</td>
                <td bgcolor="#BBFDAC" align="center">HORA</td>
                <td bgcolor="#BBFDAC" align="center">Temperatura (Cº)</td>
                <td bgcolor="#BBFDAC" align="center">Presión Arterial (mmHg)</td>
                <td bgcolor="#BBFDAC" align="center">Frecuencia Respiratoria</td>
                <td bgcolor="#BBFDAC" align="center">Peso (Kg)</td>
                <td bgcolor="#BBFDAC" align="center">Talla (cm)</td>
                <td bgcolor="#BBFDAC" align="center">IMC(Kg/mª)</td>
                <td bgcolor="#BBFDAC" align="center">Perimetro Abdominal(cm)</td>
                <td bgcolor="#BBFDAC" align="center">Hemoglobina Capilar (g/dl)</td>
                <td bgcolor="#BBFDAC" align="center">Glucosa Capilar (mg/dl)</td>
                <td bgcolor="#BBFDAC" align="center">Pulsioximetri</td>
            </tr>
            <tr>
                <th style="height: 20px;">{{ substr($anamnesis->historiaclinica->fecha_atencion, 0, 10) }}</th>
                <th>{{$hora}}</th>
                <th>{{$anamnesis->historiaclinica->temperatura}}</th>
                <th>{{$anamnesis->historiaclinica->presion}}</th>
                <th>{{$anamnesis->frecuencia_respiratoria}}</th>
                <th>{{$anamnesis->historiaclinica->peso}}</th>
                <th>{{$anamnesis->historiaclinica->altura}}</th>
                <th>{{$anamnesis->imc_kg}}</th>
                <th>{{$anamnesis->perimetro_abdominal}}</th>
                <th>{{$anamnesis->hemoglobina_capilar}}</th>
                <th>{{$anamnesis->glucosa_capilar}}</th>
                <th>{{$anamnesis->pulsioximetri}}</th>
            </tr>
        </table>

        <table class="table8">
            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="15" ; align="left" style="font-size:12px">G. REVISION ACTUAL DE ORGANOS Y SISTEMAS </th>
            </tr>
            <tr>
                <th><b> 1 </b></th>
                <td bgcolor="#BBFDAC" align="center"> PIEL - ANEXOS</td>
                <th class="text-center"><b>@if($anamnesis->piel == '1') X @endif</b></th>
                <th><b> 3 </b></th>
                <td bgcolor="#BBFDAC" align="center"> RESPIRATORIO </td>
                <th class="text-center"><b>@if($anamnesis->respiratorio == '1') X @endif</b></th>
                <th><b> 5 </b></th>
                <td bgcolor="#BBFDAC" align="center"> DIGESTIVO </td>
                <th class="text-center"><b>@if($anamnesis->digestivo == '1') X @endif</b></th>
                <th><b> 7 </b></th>
                <td bgcolor="#BBFDAC" align="center"> MÚSCULO - ESQUELÉTICO </td>
                <th class="text-center"><b>@if($anamnesis->musculo == '1') X @endif</b></th>
                <th><b> 9 </b></th>
                <td bgcolor="#BBFDAC" align="center"> HEMO - LINFÀTICO </td>
                <th class="text-center"><b>@if($anamnesis->hemo_linfatico == '1') X @endif</b></th>
            </tr>

            <tr>
                <th><b> 2 </b></th>
                <td bgcolor="#BBFDAC" align="center"> ORGANOS DE LOS SENTIDOS</td>
                <th class="text-center"><b>@if($anamnesis->organos == '1') X @endif</b></th>
                <th><b> 4 </b></th>
                <td bgcolor="#BBFDAC" align="center"> CARDIO - VASCULAR </td>
                <th class="text-center"><b>@if($anamnesis->cardio_vascular == '1') X @endif</b></th>
                <th><b> 6 </b></th>
                <td bgcolor="#BBFDAC" align="center"> GENITO - URINARIO </td>
                <th class="text-center"><b>@if($anamnesis->genito == '1') X @endif</b></th>
                <th><b> 8 </b></th>
                <td bgcolor="#BBFDAC" align="center"> ENDOCRINO </td>
                <th class="text-center"><b>@if($anamnesis->endocrino == '1') X @endif</b></th>
                <th><b> 10 </b></th>
                <td bgcolor="#BBFDAC" align="center"> NERVIOSO </td>
                <th class="text-center"><b>@if($anamnesis->nervioso == '1') X @endif</b></th>
            </tr>
            <tr>
                <td colspan="15" align="left"></td>
            </tr>
        </table>
        <table style"width: 100%;>
            <tr>
                <td style="text-align: left;width:50%; font-size: 11px">SNS-MSP / HCU-form.002 / 2021</td>
                <td style="text-align: right;width:50%;font-size: 11px"><b>CONSULTA EXTERNA - ANAMNESIS (1) </b></td>
            </tr>
        </table>
        <div class="page-break-before"></div>

        <table class="table9">
            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="15" ; align="left" style="font-size:12px">H. EXAMEN FISICO </th>
            </tr>
            <tr>
                <th bgcolor="#B4A7FD" ; COLSPAN="9" ; align="left" style="font-size:12px">REGIONAL</th>
                <th bgcolor="#B4A7FD" ; COLSPAN="6" ; align="left" style="font-size:12px">SISTEMICO</th>
            </tr>
            <tr>
                <th><b> 1R </b></th>
                <td bgcolor="#BBFDAC" align="center"> PIEL - FANERAS</td>
                <th class="text-center"><b>@if($anamnesis->piel_faneras == '1') X @endif</b></th>
                <th><b> 6R </b></th>
                <td bgcolor="#BBFDAC" align="center"> BOCA </td>
                <th class="text-center"><b>@if($anamnesis->boca == '1') X @endif</b></th>
                <th><b> 11R </b></th>
                <td bgcolor="#BBFDAC" align="center"> ABDOMEN </td>
                <th class="text-center"><b>@if($anamnesis->abdomen == '1') X @endif</b></th>
                <th><b> 1S </b></th>
                <td bgcolor="#BBFDAC" align="center"> ORGANOS DE LOS <br> SENTIDOS </td>
                <th class="text-center"><b>@if($anamnesis->organo_sentidos == '1') X @endif</b></th>
                <th><b> 6S </b></th>
                <td bgcolor="#BBFDAC" align="center"> URINARIO </td>
                <th class="text-center"><b>@if($anamnesis->urinario == '1') X @endif</b></th>
            </tr>

            <tr>
                <th><b> 2R </b></th>
                <td bgcolor="#BBFDAC" align="center"> CABEZA </td>
                <th class="text-center"><b>@if($anamnesis->cabeza == '1') X @endif</b></th>
                <th><b> 7R </b></th>
                <td bgcolor="#BBFDAC" align="center"> OROFARINGE </td>
                <th class="text-center"><b>@if($anamnesis->orofaringe == '1') X @endif</b></th>
                <th><b> 12R </b></th>
                <td bgcolor="#BBFDAC" align="center"> COLUMNA <br> VERTEBRAL </td>
                <th class="text-center"><b>@if($anamnesis->columna_vertebral == '1') X @endif</b></th>
                <th><b> 2S </b></th>
                <td bgcolor="#BBFDAC" align="center"> RESPIRATORIO </td>
                <th class="text-center"><b>@if($anamnesis->ex_respiratorio == '1') X @endif</b></th>
                <th><b> 7S </b></th>
                <td bgcolor="#BBFDAC" align="center"> MUSCULO <br> ESQUELETICO </td>
                <th class="text-center"><b>@if($anamnesis->musculo_esqueletico == '1') X @endif</b></th>
            </tr>
            <tr>
                <th><b> 3R </b></th>
                <td bgcolor="#BBFDAC" align="center"> OJOS </td>
                <th class="text-center"><b>@if($anamnesis->ojos == '1') X @endif</b></th>
                <th><b> 8R </b></th>
                <td bgcolor="#BBFDAC" align="center"> CUELLO </td>
                <th class="text-center"><b>@if($anamnesis->cuello == '1') X @endif</b></th>
                <th><b> 13R </b></th>
                <td bgcolor="#BBFDAC" align="center"> INGLE - PERINÉ </td>
                <th class="text-center"><b>@if($anamnesis->ingle_perine == '1') X @endif</b></th>
                <th><b> 3S </b></th>
                <td bgcolor="#BBFDAC" align="center"> CARDIO <br> VASCULAR </td>
                <th class="text-center"><b>@if($anamnesis->ex_cardio_vascular == '1') X @endif</b></th>
                <th><b> 8S </b></th>
                <td bgcolor="#BBFDAC" align="center"> ENDÓCRINO </td>
                <th class="text-center"><b>@if($anamnesis->ex_endocrino == '1') X @endif</b></th>
            </tr>
            <tr>
                <th><b> 4R </b></th>
                <td bgcolor="#BBFDAC" align="center"> OÍDOS </td>
                <th class="text-center"><b>@if($anamnesis->oidos == '1') X @endif</b></th>
                <th><b> 9R </b></th>
                <td bgcolor="#BBFDAC" align="center"> AXILAS - MAMAS </td>
                <th class="text-center"><b>@if($anamnesis->axilad_mamas == '1') X @endif</b></th>
                <th><b> 14R </b></th>
                <td bgcolor="#BBFDAC" align="center"> MIEMBROS <br> SUPERIORES </td>
                <th class="text-center"><b>@if($anamnesis->miembros_superiores == '1') X @endif</b></th>
                <th><b> 4S </b></th>
                <td bgcolor="#BBFDAC" align="center"> DIGESTIVO </td>
                <th class="text-center"><b>@if($anamnesis->ex_digestivo == '1') X @endif</b></th>
                <th><b> 9S </b></th>
                <td bgcolor="#BBFDAC" align="center"> HEMO - LINFÁTICO </td>
                <th class="text-center"><b>@if($anamnesis->ex_hemo_linfatico == '1') X @endif</b></th>
            </tr>
            <tr>
                <th><b> 5R </b></th>
                <td bgcolor="#BBFDAC" align="center"> NARIZ </td>
                <th class="text-center"><b>@if($anamnesis->nariz == '1') X @endif</b></th>
                <th><b> 10R </b></th>
                <td bgcolor="#BBFDAC" align="center"> TÓRAX </td>
                <th class="text-center"><b>@if($anamnesis->torax == '1') X @endif</b></th>
                <th><b> 15R </b></th>
                <td bgcolor="#BBFDAC" align="center"> MIEMBROS <br> INFERIORES </td>
                <th class="text-center"><b>@if($anamnesis->miembros_inferiores == '1') X @endif</b></th>
                <th><b> 5S </b></th>
                <td bgcolor="#BBFDAC" align="center"> GENITAL </td>
                <th class="text-center"><b>@if($anamnesis->genital == '1') X @endif</b></th>
                <th><b> 10S </b></th>
                <td bgcolor="#BBFDAC" align="center"> NEUROLÓGICO </td>
                <th class="text-center"><b>@if($anamnesis->neurologico == '1') X @endif</b></th>
            </tr>

            <tr>
                <td colspan="15" align="left"></td>
            </tr>
        </table>

        <table class="table10">

            <tr>

                <td bgcolor="#B4A7FD" colspan="5" style="font-size:12px"><b>I. DIAGNOSTICO</b></td>
                <td><b>CIE-10</b></td>
                <td><b>PRE</b></td>
                <td><b>DEF</b></td>
            </tr>


            <tbody>
                @php $contador = 1; @endphp
                @foreach ($cie10 as $key => $val)
                @php
                $c10 = Sis_medico\Cie_10_3::find($val->cie10);
                if (is_null($c10)) {
                $c10 = Sis_medico\Cie_10_4::find($val->cie10);
                }
                @endphp

                <tr>

                    <td colspan="1" class="td-border bg-green"><b>{{ $contador }}</b></td>
                    <td colspan="4" class="td-border">{{ $c10->descripcion }}</td>
                    <td class="text-center td-border">{{ $c10->id }}</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'PRESUNTIVO') X @endif</td>
                    <td class="text-center td-border">@if ($val->presuntivo_definitivo == 'DEFINITIVO') X @endif</td>
                    @php $contador++; @endphp

                </tr>

                @endforeach
            </tbody>


        </table>
        <table class="table11">
            <tr>
                <th bgcolor="#B4A7FD" ; align="left" style="font-size:12px">J. PLAN DE TRATAMIENTO </th>
            </tr>
            <tr>
                <td align="left" style="height: 35px;"><?php echo $anamnesis->indicacion_evolucion; ?> </td>
            </tr>
        </table>

        <table class="table12">
            <tr>
                <th bgcolor="#B4A7FD" ; colspan="5" align="left" style="font-size:12px">K. DATOS DEL PROFESIONAL RESPONSABLE </th>
            </tr>

            <tr>
                <td bgcolor="#BBFDAC" align="center"> <b>FECHA <br> (aaaa- mm -dd)</b></td>
                <td bgcolor="#BBFDAC" align="center"> <b>HORA <br> (hh:mm) </b></td>
                <td bgcolor="#BBFDAC" align="center"> <b> PRIMER NOMBRE</b></td>
                <td bgcolor="#BBFDAC" align="center"> <b> PRIMER APELLIDO</b></td>
                <td bgcolor="#BBFDAC" align="center"> <b> SEGUNDO APELLIDO </b></td>
            </tr>
            <tr>
                <td style="height: 35px;">{{$fecha}}</td>
                <td>{{$hora}}</td>
                <td>{{$anamnesis->historiaclinica->doctor_1->nombre1}}</td>
                <td>{{$anamnesis->historiaclinica->doctor_1->apellido1}}</td>
                <td>{{$anamnesis->historiaclinica->doctor_1->apellido2}}</td>
            </tr>

            <tr>
                <td bgcolor="#BBFDAC" align="center" colspan="1"> <b> NUMERO DE DOCUMENTO DE IDENTIFICACION </b></td>
                <td bgcolor="#BBFDAC" align="center" colspan="2"> <b> FIRMA </b></td>
                <td bgcolor="#BBFDAC" align="center" colspan="2"> <b> SELLO </b></td>
            </tr>
            <tr>
                @php $especialidad = Sis_medico\Especialidad::find( $anamnesis->historiaclinica->agenda->espid)
                @endphp

                <td colspan="1" style="height: 35px;">{{$anamnesis->historiaclinica->doctor_1->id}}</td>
                <td colspan="2" class="text-center" style="height: 50px;">
                    <p style="text-align: center;">
                        @php
                        $ruta='';
                        if(!is_null($doctor))
                        $firma = Sis_medico\Firma_Usuario::where('id_usuario',$doctor->id)->first();
                        if(!is_null($firma)){
                        $ruta = $firma->firma;
                        }
                        @endphp
                        @if($ruta !='')
                        <img style='height: auto; width: 95px; margin-top: 5px; ' src="{{base_path().'/storage/app/avatars/'.$ruta}}" align=center hspace=12>

                        @else
                        <br><br><br>
                        <b style="font-size: 10px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A') {{$firma->doctor->apellido2}} @endif {{$firma->doctor->nombre1}}</b><br>
                        @endif @endif
                    </p>
                </td>
                <td colspan="2" class="text-center" style="height: 50px;">
                    @if( !is_null($firma) ) 
                    @if(!is_null($firma->doctor)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @if($firma->doctor->nombre2!='N/A'){{$firma->doctor->nombre2}}@endif @endif<br>
                    @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                    @if(!is_null($firma->doctor)) Cedula No. {{$firma->doctor->id}} @endif
                    @endif
                </td>
            </tr>
            
        </table>
        <table style"width: 100%;>
            <tr>
                <td style="text-align: left;width:50%; font-size: 11px">SNS-MSP / HCU-form.002 / 2021</td>
                <td style="text-align: right;width:50%;font-size: 11px"><b>CONSULTA EXTERNA -  EXAMEN FÍSICO Y PRESCRIPCIONES (2)</b></td>
            </tr>
        </table>
    </main>
</body>

</html>