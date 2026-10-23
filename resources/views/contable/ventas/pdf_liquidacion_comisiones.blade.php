<!DOCTYPE html>
@php
$data = date("Y-m-d");
$idusuario = Auth::user()->id;
$usuarioNombre = Sis_medico\user::where('id',$idusuario)->first();
$fecha = date("Y-m-d H:i:s");
@endphp
@section('action-content')
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Liquidacion de comisiones</title>
    <style>
        @page {
            margin: 20px 20px;
        }


        .table {
            border-collapse: collapse;
            padding: 1px;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
            color: black;
            font-size: 16px;
        }

        .th {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            color: white;
            background-color: #0033cc;
        }

        .new_exl_cabecera {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 20px;
            color: black;
            background-color: #8DBEFB;
        }

        table tr td {

            padding: 3px;
 
        }

        #container {
            margin: 15px 15px;
        }

        .estilos {
            font-weight: bold;
            color: black;
        }

        .saltoDePagina {
            display: block;
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div>
        <div>
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:160px;height: 90px; margin-left: 11px;"><br>
            <span style="font-size: 14px;">
                @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @endif
            </span>
            @yield('action-content')
        </div>
    </div>
    <div>
        <center><label style="font-size: 35px; color: black;">LIQUIDACION DE COMISIONES</label></center>
    </div>
    <div>                   
        <center><label style="font-size: 25px">Desde: {{substr($cabecera->fecha_inicio,0,10)}} - Hasta: {{substr($cabecera->fecha_fin,0,10)}} </label></center>
    </div><br>
    
    <table class="table" style="width: 100%; border: none !important;">
        <thead>
            <tr>
                <th class="new_exl_cabecera">No.</th>
                <th class="new_exl_cabecera">Fecha Procedimiento</th>
                <th class="new_exl_cabecera">Identificaci&oacute;n</th>
                <th class="new_exl_cabecera">Paciente</th>
                <th class="new_exl_cabecera">Procedimiento</th>
                <th class="new_exl_cabecera">Seguro</th>
                <th class="new_exl_cabecera">Secuencia Factura</th>
                <th class="new_exl_cabecera">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total_final_1 = 0; $contador = 0; $total_final_1 = 0; 
            @endphp
            @foreach($detalle as $value)
                @if(($value)!=null)
                    @php 
                        $contador++; $total_final_1 += $value->valor_total; 
                        $paciente = Sis_medico\Paciente::find($value->id_paciente);
                        $producto = Sis_medico\Ct_productos::where('codigo', $value->codigo_producto)->first();
                        $seguro = Sis_medico\Seguro::find($value->seguro);
                    @endphp
                    <tr>
                        <td>{{$contador}}</td>
                        <td>@if(!is_null($value->fecha_procedimiento)) {{date("d-m-Y", strtotime($value->fecha_procedimiento))}} @endif</td>
                        <td>@if(!is_null($value->id_paciente)) {{$value->id_paciente}} @endif</td>
                        <td> @if(!is_null($paciente)) {{$paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}} @endif @endif </td>
                        <td> @if(!is_null($producto)) {{$producto->nombre}} @endif </td>
                        <td> @if(!is_null($seguro)) {{$seguro->nombre}} @endif </td>
                        <td> @if(!is_null($value->nro_comprobante)) {{$value->nro_comprobante}} @endif </td>
                        <td style="text-align: right;"> @if(!is_null($value->valor_total)) {{$value->valor_total}} @endif </td>
                    </tr>
                @endif 
            @endforeach
            <tr>
                <td class="new_exl_cabecera" colspan="7" style="text-align: right;">
                    TOTAL
                </td>
                <td class="new_exl_cabecera" style="text-align: right;">
                    $ {{ number_format($total_final_1, 2, ',', ' ')}}
                </td>
            </tr>
        </tbody>
    </table><br>
    <label >Calculo de Comision: &nbsp; &nbsp; &nbsp; </label>
    <label>Subtotal: &nbsp;$ </label><label>{{number_format($cabecera->total,2,'.',',')}}</label>
    <label>&nbsp;&nbsp;X &nbsp;&nbsp; Porcentaje: </label><label>{{number_format($cabecera->porcentaje,2,'.',',')}}&nbsp;%</label>
    <label>&nbsp;&nbsp;= &nbsp;&nbsp;Total Comision: &nbsp;$ </label><label>{{number_format($cabecera->total_comision,2,'.',',')}}</label>
    <br>
    <div style="width: 100%; border: none !important;text-align:left;margin-top:2%">
        <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">{{trans('contableM.nombre')}}:</b>{{$usuarioNombre->nombre1}} {{$usuarioNombre->nombre2}} {{$usuarioNombre->apellido1}} {{$usuarioNombre->apellido2}} &nbsp;&nbsp;<b style="font-size: 18px;">{{trans('contableM.fecha')}}</b> {{$fecha}}</label>
    </div><br>
    <div class="saltoDePagina"></div>
</body>

</html>