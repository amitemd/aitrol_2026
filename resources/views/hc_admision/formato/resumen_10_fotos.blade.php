<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Procedimiento</title>
</head>
<style>
    .negr {
        border: 1px solid black;
        height: 10px;
        font-size: 12px;
        margin: 0px;
    }

    .negrFont {
        border: 1px solid black;
        font-weight: bold;
        font-size: 14px;
    }

    .onlyDown {
        border-bottom: 1px solid black;
    }

    .nospacing {
        border-spacing: 0px;
        margin: 0;
        padding: 0;
        margin-left: -15px;
    }
    .final td{
            border-style: solid;
            border-width: thin;
            margin: 0;
            padding: 2px 10px;
          }
</style>

<body>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <img style="margin: 0;width: 700px;" src="{{base_path().'/public/formato/header.jpg'}}">
            </div>
            <div class="table table-responsive col-md-12 " style="text-align: center;">
                <table class="table table-responsive nospacing" style="width: 700px;">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th style="width: 150px;">&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th style="width: 100px;">&nbsp;</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="negrFont">Paciente:</td>
                            <td style="width: 100px;" class="negr">{{ $paciente->apellido1}} @if($paciente->apellido2 != '(N/A)'){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != '(N/A)' ){{ $paciente->nombre2}}@endif</td>
                            <td class="negrFont">Edad</td>

                            <td class="negr">{{$edad}} años</td>
                            <td class="negrFont">Identificacion: </td>
                            <td colspan="2" class="negr">{{$paciente->id}}</td>
                        </tr>
                        <tr>
                            <td class="negrFont">Procedimiento: </td>
                            <td class="negr" colspan="6">
                                <b style="font-size: 12px;">@if($procedimiento_completo != null){{$procedimiento_completo->nombre_completo}}@else<?php
$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->get();

$mas   = true;
$texto = "";

foreach ($adicionales as $value2) {
    if ($mas == true) {
        $texto = $texto . $value2->procedimiento->nombre;
        $mas   = false;
    } else {
        $texto = $texto . ' + ' . $value2->procedimiento->nombre;
    }
}
?><?php echo e($texto); ?>@endif</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="negrFont">Endoscopia: </td>
                            <td class="negr" colspan="2"> @if(isset($doctor1)) {{$doctor1->apellido1}} {{$doctor1->nombre1}} @endif</td>
                            <td class="negrFont"> Anestesiologo: </td>
                            <td class="negr" colspan="3"> @if(isset($anestesiologo)) {{$anestesiologo->apellido1}} {{$anestesiologo->nombre1}} @endif </td>
                        </tr>
                        <tr>
                            <td class="negrFont">Ayudante: </td>
                            <td class="negr""> @if(isset($ayudante)) {{$ayudante->apellido1}} {{$ayudante->nombre1}} @endif </td>
                           <td class=" negrFont"> Instrumento: </td>
                            <td class="negr"> {{$protocolo->instrumento}} </td>
                            <td class="negrFont">Fecha:</td>
                            <td colspan="2" class="negr">@if($protocolo->fecha == null){{date('d/m/Y', strtotime($historia->fecha_atencion))}}@else{{date('d/m/Y', strtotime($protocolo->fecha))}}@endif</td>
                        </tr>
                        <tr>
                            <td class="negrFont">Premedicacion: </td>
                            <td class="negr" colspan="6">
                                @if(!is_null($protocolo->premedicacion)) {{$protocolo->premedicacion}} @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="negrFont">Referido por : </td>
                            <td class="negr" colspan="6">
                                @if(!is_null($protocolo->referido_por)) {{$protocolo->referido_por}} @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>
            <table style="margin-left: -23px;padding: 0;table-layout:fixed; width:750px;">

                @php $i = 0; $j = 0;@endphp
                @foreach($imagenes as $value)
                @if($i < 6) @if($j==0) <tr style="padding: 0; margin:0;">
                    @endif
                    @if($value->seleccionado_recortada == 1)
                    <td style="margin: 0;padding: 0 0px 0px 0px; width: 300px;">
                        <div style="position:relative;overflow: hidden; width: 300px; height:200px; margin: 0; padding: 0; ">
                            <img style="margin: 0;width: 200px; height: 205px;" src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
                        </div>
                    </td>
                    @else
                    <td style="margin: 0;padding: 0 0px 0px 0px;width: 300px;">
                        <div style="position:relative;overflow: hidden; width: 300px; height: 200px; margin: 0; padding: 0;  ">
                            <img style="margin: 0;width: 200px; height: 205px;position: absolute; " src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
                        </div>
                    </td>
                    @endif
                    @if($j >= 2)
                    </tr>
                    @php $j=0-1; @endphp
                    @endif
                    @php $i = $i+1;$j = $j+1; @endphp
                    @endif
                    @endforeach


                    </tr>
            </table>
            <div style="page-break-after: always"></div>
            <div class="col-md-12">
                &nbsp;
            </div>

            <div style="width: 107%;margin-left: -17px;border: 1px black solid;">
                <span>Motivo</span>
            </div>
            <div id="motivo" style="width: 107%;margin-left: -17px;border: 1px black solid;font-size: 10px !important;line-height: 1.2 !important;">
                <span style='font-size:10px !important;line-height: 1.2 !important;margin:0;'><?php echo $protocolo->motivo; ?></span>
            </div>
            <hr>
            <div style="width: 107%;margin-left: -17px;border: 1px black solid;">
                <span>Hallazgos</span>
            </div>
            <div id="hallazgos" style="width: 107%;margin-left: -17px;border: 1px black solid;font-size: 10px !important;line-height: 1.2 !important;">
                <span style='font-size:10px !important;line-height: 1.2 !important;margin:0;'><?php echo $protocolo->hallazgos; ?></span>
            </div>
            <hr>
            <table width="716px;" class="final">
                <tr>
                    <td >Conclusiones </td>
                </tr>
                <tr>
                    <td style="font-size: 10px !important;font-family: 'Source Sans Pro',sans-serif;line-height: 1.2 !important;" >
                        <span style='font-size:10px !important;line-height: 1.2 !important;margin:0;'><?php echo $protocolo->conclusion; ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div id="footer">
            <br>
            <br>
            <div style="width: 100%;">
                @if(!is_null($protocolo->procedimiento->id_doctor_responsable))
                    @php
                        if(!is_null($protocolo->procedimiento->id_doctor_responsable)){
                            $firma_2 =  \Sis_medico\Firma_Usuario::where('id_usuario', $protocolo->procedimiento->id_doctor_responsable)->get();
                        }else{
                            $firma_2 = null;
                        }
                        $responsable = \Sis_medico\User::find($protocolo->procedimiento->id_doctor_responsable);
                        //dd($responsable);
                    @endphp
                    <div style="width: 325px; display: inline-block;">
                        <p style="text-align: center;">
                            @if((!is_null($protocolo->procedimiento->id_doctor_examinador2)))
                                @if($firma != "[]")
                                <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
                                <b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
                                @else
                                    <br><br><br><br><br>
                                    <b>DR.{{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
                                @endif
                            @else
                                <br><br><br><br><br>
                                <b>DR. CARLOS ROBLES MEDRANDA</b><br>
                            @endif
                        </p>
                    </div>
                    <div style="width: 325px;display: inline-block;">
                        <p style="text-align: center;">
                            @if((!is_null($protocolo->procedimiento->id_doctor_responsable)))
                                @if($firma_2 != "[]")
                                <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma_2[0]->nombre}}" style="" align=center hspace=12><br>
                                <b>DR. {{$responsable->nombre1}} {{$responsable->apellido1}} {{$responsable->apellido2}} (RESPONSABLE)</b><br>
                                @else
                                    <br><br><br><br><br>
                                    <b>DR.{{$responsable->nombre1}} {{$responsable->apellido1}} {{$responsable->apellido2}} (RESPONSABLE)</b><br>
                                @endif
                            @endif
                        </p>
                    </div>
                @else
                <div style="width: 650px; display: inline-block;">
                    <p style="text-align: center;">
                        @if((!is_null($protocolo->procedimiento->id_doctor_examinador2)))
                            @if($firma != "[]")
                            <img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
                            <b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
                            @else
                                <br><br><br><br><br>
                                <b>DR.{{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
                            @endif
                        @else
                            <br><br><br><br><br>
                            <b>DR. CARLOS ROBLES MEDRANDA</b><br>
                        @endif
                    </p>
                </div>

                @endif
            </div>
        </div>


    </div>
</body>

</html>