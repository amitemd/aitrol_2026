<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin-top: 50px;
            margin-left: 50px;
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

        body {
            margin-top:    50px;
            margin-left:   20px;
            margin-right:  20px;
            margin-bottom: 1px;
            font-size: 10px;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top:        0.5cm;
            left:       0.5cm;
            right:      0.5cm;
            height:     0.5cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed; 
            bottom:     0cm; 
            left:       0cm; 
            right:      0cm;
            height:     2cm;
        }

        table {
            width: 100%;
        }

        .titulos {
            font-size: 3px;
            font-weight: bold;
        }

        .espacio {

            padding: 15px;
        }

        .alinear {

            text-align: center !important;
        }

        .texto {
            font-size: 8px;

        }

        .altura {
            height: 15px;
        }


        .altura2 {
            height: 20px;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .derecha {
            float: right;
        }

        .izquierda {
            float: left;
        }
    </style>
    <title>{{trans('hospitalizacion.FORMULARIO005')}}</title>
</head>

<body>

    <div>
        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead class="texto alinear espacio">
                <tr>
                    <th width="10%">{{trans('hospitalizacion.ESTABLECIMIENTO')}}</th>
                    <th width="10%">{{trans('hospitalizacion.NOMBRE')}}</th>
                    <th width="10%">{{trans('hospitalizacion.APELLIDO')}}</th>
                    <th width="2%" colspan="2"> {{trans('hospitalizacion.SEXO')}}</th>
                    <th width="5%" rowspan="2">
                        <p>{{trans('hospitalizacion.NUMERODE')}}</p>
                        <p>{{trans('hospitalizacion.HORA')}}</p>
                    </th>
                    <th width="5%" class="texto" rowspan="2">
                        <p>{{trans('hospitalizacion.HISTORIACLINICA')}}</p>
                    </th>
                </tr>
            </thead>

            <tbody class="texto alinear " width="15%">
                <tr>
                    <td rowspan="2">@if(!is_null($empresa)){{$empresa->nombrecomercial}} @endif</td>
                    <td rowspan="2"> @if(!is_null($empresa)){{$solicitud005->paciente->nombre1}} @endif</td>
                    <td rowspan="2">{{$solicitud005->paciente->apellido1}}</td>
                    <td>M</td>
                    <td>F</td>
                </tr>
                @php $paciente = $solicitud005->paciente; @endphp
                <tr>
                    <td>@if($solicitud005->paciente->sexo =='1') X @else @endif</td>
                    <td>@if($solicitud005->paciente->sexo =='2') X @else @endif</td>
                    <td>1</td>
                    <td class="altura">{{$solicitud005->id_paciente}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 11px">

        <table border="1" cellspacing="0" cellpadding="0" style="margin-right:25px;">
            <thead class="texto alinear">
                <tr>
                    <td width="1%" rowspan="2">{{trans('hospitalizacion.FECHA')}}
                    {{trans('hospitalizacion.(DIA/MES/AÑO)')}}
                    </td>
                    <th width="1%" rowspan="2">{{trans('hospitalizacion.HORA')}}</th>
                    <th width="25%" rowspan="1">
                        <p>{{trans('hospitalizacion.EVOLUCION')}}</p>
                    </th>
                    <th width="5%" rowspan="1">
                        <p>{{trans('hospitalizacion.PRESCRIPCIONES')}}</p>
                    </th>
                    <th width="2%" rowspan="1">{{trans('hospitalizacion.MEDICAMENTOS')}}
                    </th>
                </tr>
            </thead>
            <tbody class="texto alinear ">
                <tr>
                    <td width="5%" class="espacio">{{trans('hospitalizacion.FIRMARALPIEDECADANOTADEEVOLUCION')}}</td>
                    <td width="5%">{{trans('hospitalizacion.FIRMARALPIEDECADACONJUNTODEPREINSCRIPCIONES')}}</td>
                    <td width="2%">{{trans('hospitalizacion.REGISTRARADMINISTRACION')}}</td>
                </tr>
                @php
                $form008 = $solicitud005->form008->first();
                @endphp

                @php $j=0; @endphp
                @foreach ($evoluciones as $evol)
                @foreach($detalles as $detalle)


                <tr class="alinear ">
                    <td width="5%" class=" espacio "> @if(!is_null($evol->created_at))
                        @php

                        $time= substr($evol->created_at,0,10);
                        $time1 = date('d-m-Y',strtotime($time));
                        @endphp
                        {{$time1}}
                        @endif
                    </td>
                    <td width="5%">{{substr($evol->created_at,11, 5)}}</td>
                    <td width="5%">{{$evol->cuadro_clinico}}</td>
                    <td width="5%">{{$detalle->nombre}}</td>
                    <td width="5%">{{$detalle->dosis}}</td>

                </tr>

                @php $j++; @endphp
                @endforeach
                @endforeach
                @for($i=0;$i<20-$j;$i++) <tr height="20px">
                    <td width="5%" class="texto espacio "></td>
                    <td width="5%"></td>
                    <td width="5%"></td>
                    <td width="5%"></td>
                    <td width="5%"></td>

                    </tr>
                    @endfor
            </tbody>
        </table>
    </div>
    <footer class="texto">
        <div style="width: 50%;  display: inline-block;">
        {{trans('hospitalizacion.SNS-MSP/HCU-form.005/2008EMERGENCIAS(1)')}}

        </div>

    </footer>
</body>

</html>