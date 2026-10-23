<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Cobro</title>
</head>
<style>
    *{
        margin: 6px;
        font-size: 23px;
        text-transform: uppercase;
        font-family: sans-serif;
        font-weight: bolder;
    }
    .izquierda{
        text-align: right;
    }

    .center{
        text-align: center;
    }
    .text_peq{
        font-size: 19px!important;
    }

</style>
<body>
    @php
        $recargo = $orden->recargo_p/100;
        $descuento = $orden->descuento_p/100;
        $r = 1 + $recargo - ($descuento*$recargo);
        $pr = [10, 11, 12];
    @endphp
    <div class="center">
        @if(!is_null($empresa->logo))
            <img src="{{base_path().'/storage/app/logo/logo_factura_labs.jpg'}}" style="width:300px;height: 150px">
        @endif
        <p>{{$empresa->id}}</p>
        <p>{{$empresa->razonsocial}}</p>
        <p>{{$empresa->nombrecomercial}}</p>
        <p>Recibo de Cobro: {{$orden->id}}</p>
    </div>
    <div class="text_peq">
        <p>Ciudad: {{$empresa->ciudad}}</p>
        <p>direccion: {{$empresa->direccion}}</p>
        <p>email: {{$empresa->email}}</p>
        @if($orden->id_protocolo!=null)

          <p>@if($orden->id_protocolo!=null) Protocolo: {{$orden->protocolo->nombre}} @endif</p>
        @endif
        <br>
    </div>

        <p style="text-align:center;">Datos del paciente</p>
        <br>
    <div class="text_peq">
        <p>paciente:  {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</p>
        <p>cedula: {{$orden->id_paciente}}</p>
        <p>edad: {{$age}} </p>
        <p>sexo: @if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif </p>
        <p>fecha: {{substr($orden->created_at,0,10)}}</p>
        <p>hora: {{substr($orden->created_at,10,10)}}</p>

        @if(empty($orden->seguro->nombre)) <p> Seguro: n/a </p> @else <p>Seguro:{{$orden->seguro->nombre}}</p> @endif
        <p>Doctor solicita: @if($orden->doctor->uso_sistema!='1')@if($orden->doctor->id!='GASTRO'){{$orden->doctor->apellido1}} @if($orden->doctor->apellido2!='(N/A)' && $orden->doctor->apellido2!='.'){{$orden->doctor->apellido2}}@endif {{$orden->doctor->nombre1}} @if($orden->doctor->nombre2!='(N/A)' && $orden->doctor->nombre2!='.'){{$orden->doctor->nombre2}}@endif @endif @endif   </p>
    </div>
    <br>
    @php $xcant=0; @endphp
    <table style="width :100%;">
        <tr>
            <th>No.</th>
            <th>Examen</th>
            <th>D</th>
            <th>Valor</th>
        </tr>
        @foreach($detalles as $detalle)
        @php
            $xcant++;
            $examen_agr = DB::table('examen_agrupador_sabana')->where('id_examen',$detalle->id_examen)->first();
            $agrupador = DB::table('examen_agrupador_labs')->where('id',$examen_agr->id_examen_agrupador_labs)->first();
         @endphp
        <tr>
            <td style="font-size: 18px;">{{$xcant}}</td>
            <td style="font-size: 18px;">{{$detalle->nombre}}</td>
            <td style="font-size: 18px;" class="izquierda">{{number_format($detalle->valor_descuento,4)}}</td>
            <td style="font-size: 18px;" class="izquierda">@if($orden->cobrar_pac_pct < 100) $ {{number_format($detalle->valor_con_oda,4)}} @else $ {{number_format($detalle->valor,4)}} @endif</td>
        </tr>
        @endforeach
    </table>

    <div style="text-align: right;">
        <p>Subtotal: @if($orden->cobrar_pac_pct < 100) {{number_format($orden->valor_con_oda,4)}} @else {{number_format($orden->valor,4)}} @endif</p>
        <p>Descuento: (-)$ {{number_format($orden->descuento_valor,4)}}</p>
        @if($orden->recargo_valor > 0 )
            <p>Fee Administrativo: {{number_format($orden->recargo_valor,4)}}</p>
        @endif
        <p>Total: @if($orden->cobrar_pac_pct < 100) {{number_format(round($orden->total_con_oda,2),2)}} @else {{number_format(round($orden->total_valor,2),2)}} @endif</p>@if($orden->cobrar_pac_pct < 100)
        <p>Oda: {{ $orden->valor - $orden->valor_con_oda }} </p>
        @endif
    </div>
    <br>
    <table style="width :100%;">
        <tr>
            <th>Forma Pago</th>
            <th>FEE</th>
            <th>VALOR</th>
        </tr>
        @if(!is_null($orden->detalle_forma_pago))
            @foreach($forma_pago as $value)
            @php
                $valor_neto = $value->valor+$value->p_fi;
            @endphp
            <tr class="text_peq">
                <td>{{$value->tipo_pago->nombre}}</td>
                <td>$ {{$value->p_fi}}</td>
                <td>$ {{$valor_neto}}</td>
            </tr>
            @endforeach
        @endif
    </table>

    <p style="font-size: 14px;">***Su Factura será enviada a su correo</p>
    @if($orden->estado == 1)
    @php   
        $url_2 ='https://ieced.siaam.ec/sis_medico/public/api/reedireccionar';
    @endphp
    <div class="text_peq">
        <p>Somos una empresa ecoamigable y no entregamos resultados en físico, Puedes revisarlo en nuestra App "LABS"</p>
        <p>Escanee el código:</p>
        <div style="width:100%;text-align:center;">
            <img  style="width: 200px;height: 200px; text-align: center;" src="data:image/png;base64, {{ DNS2D::getBarcodePNG($url_2, 'QRCODE')}}" alt="barcode"   />
        </div>
        <p>si es primera vez que ingresa puede  acceder con:<br><span>usuario:</span>  {{$usuario_mail->email}}<br><span>clave:</span>  {{$usuario_mail->id}} </p>
    </div>
    @endif


</body>
</html>
