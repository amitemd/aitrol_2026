<!DOCTYPE html>
<html lang="en">

<head>

  <title>Transacci&oacute;n Bancaria</title>
  <style>
    #page_pdf {
      width: 100%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;

    }

    .info_empresa {
      width: 50%;
      text-align: center;
    }

    .info_factura {
      width: 31%;
    }

    .round {
      border-bottom: dashed;
      overflow: hidden;
      padding-bottom: 15px;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      /*margin-bottom: 10px;*/

    }

    #factura_head {
      margin-top: -50px;
    }

    .h3 {
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      color: black;
      text-align: center;
    }

    .titulo {
      background: #eee;
      padding: 5px;
    }

    .center {
      text-align: center;
    }

    .t9 {
      font-size: 0.9rem
    }

    .p5 {
      padding: 0.5rem;
    }

    .celda {
      float: left;
      border: 1px solid #ccc;
      margin: -1px;
    }
  </style>

</head>
@php
$subtotal = 0;
$iva = 0;
$impuesto = 0;
$tl_sniva = 0;
$total = 0;
$registro22 = Sis_medico\Empresa::where('id',$registro->empresa)->first();
@endphp

<body>

  <div id="page_pdf">
    <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
    <table width="100%;border-bottom: dashed;">
      <tr>
        <td style="width:50%">
          <span class="h3" style="padding:2px;font-weight:bold;font-size:medium;"><strong> {{$registro22->razonsocial}}</strong></span>
          <span class="h3"><strong> {{$registro22->direccion}}</strong></span>
          <span class="h3"><strong> {{$registro22->telefono1}}</strong></span>
        </td>
        @php
        $numero_factura = 0;
        $max_id = intval($registro->numero);
        //dd(strlen($max_id));
        if (strlen($max_id) < 10) { $nu=$max_id + 1; $numero_factura=str_pad($nu, 10, "0" , STR_PAD_LEFT); } $variable=Sis_medico\Plan_Cuentas::where('id',$registro->id_cuenta_origen)->first();
          $variable1 = Sis_medico\Plan_Cuentas::where('id',$registro->id_cuenta_origen)->first();
          $variable2 = Sis_medico\Plan_Cuentas::where('id',$registro->id_cuenta_destino)->first();
          $detalles_banc = Sis_medico\Ct_Detalle_Deposito_Bancario::where('deposito_bancario_id',$registro->id)->get()


          @endphp <td style="width:50%">
            <span class="h3" style="padding:2px;font-weight:bold;font-size:medium;"><strong>{{trans('contableM.Deposito')}}</strong></span>
            <span class="h3" style="font-weight:bold;font-size:small;"><strong> NO : {{$numero_factura}}</strong></span>
            <span class="h3" style="font-weight: bold;"><strong> Por : $ *********{{$registro->total_deposito}}</strong></span>
          </td>
      </tr>
    </table>
    <table width='100%' style="margin-top:10px;border:1px solid #777;border-style:none;border-bottom:dashed;">
      <tr>
        <td style="width: 50%;padding:5px">
          <span class="h3" style="font-weight: bold;"><strong> Girado por : {{$registro->beneficiario}}</strong></span>
          <span class="h3" style="font-weight: bold;"><strong> Concepto : {{$registro->concepto}} </strong></span>
          <span class="h3" style="font-weight: bold;"><strong> De la caja : {{$variable->nombre}}</strong></span>
          <span class="h3" style="font-weight: bold;"><strong> A la cuenta : {{$variable2->nombre}}</strong></span>
        </td>
        <td style="width: 50%;margin:5px">
          <span class="h3" style="font-weight: bold;"><strong> Fecha : {{substr($registro->fecha_asiento,0 , 10)}}</strong></span>
          <span class="h3" style="font-weight: bold;"><strong> Asiento : {{$registro->id_asiento}} </strong></span>
          <span class="h3" style="font-weight: bold;"><strong> Tipo : {{$registro->tipo}}</strong></span>
          <span class="h3" style="font-weight: bold;"><strong> Estado : @if (($registro->estado)==1) {{trans('contableM.activo')}} @elseif(($registro->estado)==0) Inactivo @endif</strong></span>

        </td>

      </tr>
    </table>
    <table width='100%' style="margin-top:10px;border-style:none;">
      <thead style="border-bottom: dashed;margin:2px">
        <tr>
          <th style="font-size: 20px;">Ingreso</th>
          <th style="font-size: 20px;">{{trans('contableM.tipo')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.fecha')}}</th>
          <th style="font-size: 20px;">N: Cheque</th>
          <th style="font-size: 20px;">{{trans('contableM.banco')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.Cuenta')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.Girador')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.div')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.valor')}}</th>
          <th style="font-size: 20px;">{{trans('contableM.ValorBase')}}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($detalles_banc as $value)  
        @php
        $banco_deta = Sis_medico\Ct_Bancos::where('id',$value->banco)->first();
        $tipo_pago = Sis_medico\Ct_Tipo_Pago::where('id',$value->tipo)->first();
        $suma =0;
        $suma += $registro->total_deposito;

        
        @endphp
        @if(isset($value->ingreso))
        @php $detallesx= Sis_medico\Ct_Detalle_Comprobante_Ingreso::where('id_comprobante',$value->ingreso->id_comprobante)->get(); @endphp
        <tr style="margin:30px;">
          <td style="font-size: 20px;">@if(isset($value->ingreso->cabecera_ingreso)) {{$value->ingreso->cabecera_ingreso->id}} @elseif($value->ingreso->cabecera_ingresov) {{$value->ingreso->cabecera_ingresov->id}} @endif / @foreach($detallesx as $p) {{$p->ventas->nro_comprobante}} <br/> @endforeach</td>
          <td style="font-size: 20px;">{{$tipo_pago->nombre}}</td>
          <td style="font-size: 20px;">{{substr($value->ingreso->fecha, 0, 10)}}</td>
          <td style="font-size: 20px;">{{$value->cheque}}</td>
          <td style="font-size: 20px;"> @if($banco_deta!=null) {{$banco_deta->nombre}} @endif</td>
          <td style="font-size: 20px;">{{$value->cuenta}}</td>
          <td style="font-size: 20px;">{{$value->girador}}</td>
          <td style="font-size: 20px;">$</td>
          <td style="font-size: 20px;">{{number_format($value->valor,2,'.',',')}}</td>
          <td style="font-size: 20px;">{{number_format($value->valor_base,2,'.',',')}}</td>
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
    <div style="display:block;float:right !important;margin-right:160px!important">
      <span>Suma:</span>
      <span style="margin:30px">{{number_format($suma,2,'.',',')}}</span>
    </div>




  </div>
</body>

</html>