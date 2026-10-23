<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin-top: 60px;
            margin-left: 30px;
            margin-right: 30px;

        }

        #header {
            position: fixed;
            left: 20px;
            top: -10px;
            right: 20px;
            height: 150px;
            text-align: center;
        }

        /* #content { position: fixed; left: 20px; top: 10px; right: 20px; height: 150px; } */
        #footer {
            position: fixed;
            left: 20px;
            bottom: -90px;
            right: 20px;
            height: 150px;
        }


        table {
            width: 100%;
            border: black 2px solid;
        }



        .small {
            font-size: x-small;
            font-weight: bold;
        }

        .texto {
            font-size: 8;

        }

        .alinear {

            text-align: center !important;
        }

        .alinear_iz {

            text-align: right !important;
        }

        .alinear_der {

            text-align: left !important;
        }
    </style>

    <title>Solicitud de Permiso Laboral</title>

</head>

<body>

    <div>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead>
                <tr class="texto">
                    <th colspan="3">

                        <div class="center">
                         
                           
                            <img src='{{base_path().'/storage/app/logo/logos_empresas.png'}}' width="500" height="100" srcset="">
                        </div>
                       
                        <div style="text-align: center;">
                            
                        </div>
                        <br>
                    </th>
                    <th class="alienar" colspan="5">

                        <div style="text-align: center;">
                            {{trans('tecnicof.humanresource')}}
                        </div>
                        <div style="text-align: center;">
                            {{trans('tecnicof.personnelaction')}}
                        </div>
                        <div style="text-align: center;">
                            N°. {{$registro->id}}
                        </div>
                        <br>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="texto">
                    <td class="alinear_der" colspan="3">
                        <div class="col-md-4">
                            <b> {{trans('tecnicof.nameandsurname')}}: </b>
                            {{$registro->nombre->nombre1}} {{$registro->nombre->nombre2}} {{$registro->nombre->apellido1}} {{$registro->nombre->apellido2}}
                        </div>
                    </td>
                    <td class="alinear_der" colspan="3">
                        <div class="col-md-4">
                            <b>{{trans('tecnicof.id')}}:</b>
                            {{$registro->cedula}}
                        </div>

                    </td>
                    <td class="alinear_der" colspan="2">
                        <div class="col-md-4">
                            <b> {{trans('tecnicof.dateofregistration')}}: </b>{{$registro->fecha_registro}}
                        </div>

                    </td>
                </tr>
                @php
                    $nomina = Sis_medico\Ct_Nomina::where('id_user',$registro->cedula)->first();
                    
                    @endphp
                <tr class="texto">
                    <td class="alinear_der" colspan="2">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.company')}}:</b>
                            @if(!is_null($nomina))
                            <span>{{$nomina->empresa->nombrecomercial}}</span>
                            @else
                                {{$registro->nombre->servicios}}
                            @endif
                        </div>
                    </td>
                    <td class="alinear_der" colspan="3">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.position')}}:</b> {{$registro->cargo}}
                        </div>
                    </td>
                    <td class="alinear_der" colspan="3">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.department')}}:</b> {{$registro->departamento}}
                        </div>
                    </td>
                </tr>
                <tr class="texto">
                    <td class="alinear_der" colspan="4">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.leaverequested')}}: </b>{{$registro->tipo_permiso}}
                        </div>
                    </td>
                    <td class="alinear_der" colspan="4">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.state')}}:</b> @if($registro->estado_solicitud == "1") APROBADO @else NO APROBADO @endif
                        </div>
                    </td>
                </tr>
                <tr class="texto">
                    <td class="alinear" colspan="2">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.leaveindays')}}</b>
                        </div>
                    </td>
                    <td class="alinear" colspan="6">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.leaveinhours')}}</b>
                        </div>
                    </td>
                    <!-- <td class="alinear" colspan="4">
                        <div class="col-md-3">
                            <b> {{trans('tecnicof.forgotten')}} </b>
                        </div>
                    </td> -->
                </tr>
                <tr class="texto">
                    <td class="alinear_der" colspan="1">

                        <div class="col-2">
                            <b>{{trans('tecnicof.from')}}:</b> {{$registro->fecha_desde}}
                        </div>

                    </td>
                    <td class="alinear_der" colspan="1">

                        <div class="col-2">
                            <b>{{trans('tecnicof.to')}}:</b> {{$registro->fecha_hasta}}
                        </div>

                    </td>
                    <td class="alinear_der" colspan="3">

                        <div class="col-2">
                            <b> {{trans('tecnicof.enterofici')}}:</b> {{$registro->ora_ingresa}}
                        </div>

                    </td>
                    <td class="alinear_der" colspan="3">
                        <div class="col-2">
                            <b> {{trans('tecnicof.exitofici')}}:</b> {{$registro->ora_salida}}
                        </div>
                    </td>
                    
                    <!-- <td class="alinear_der" colspan="2">

                        <div class="col-2">
                            <b> {{trans('tecnicof.enter')}}:</b> {{$registro->hora_ingresomar}}
                        </div>
                    </td>
                    <td class="alinear_der" colspan="2">
                        <div class="col-2">
                            <b> {{trans('tecnicof.leave')}}:</b> {{$registro->hora_salidamar}}
                        </div>

                    </td> -->
                    
                </tr>
                <tr class="texto">
                    <td class="alinear_der" colspan="8">
                        <b>{{trans('tecnicof.justification')}}:</b>
                        @if(!is_null($registro->justificacion_final)){{$registro->justificacion_final}} @endif
                    </td>

                </tr>
                <tr class="texto">
                    <td class="alinear_der" colspan="8">
                        <p>*{{trans('tecnicof.hourly')}}</p>
                        <p>*{{trans('tecnicof.attached')}}</p>
                    </td>

                </tr>
                <tr class="texto">
                    <td class="alinear_der" colspan="8">
                        <b>{{trans('tecnicof.remarks')}}:</b> {{$registro->observaciones}}

                    </td>

                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>