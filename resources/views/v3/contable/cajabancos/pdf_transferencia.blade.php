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

    .th {
      text-align: center;
    }

    * {
      font-size: small;
    }
  </style>

</head>
@php
$suma_debe =0;
$suma_haber =0;
$subtotal = 0;
$iva = 0;
$impuesto = 0;
$tl_sniva = 0;
$total = 0;
$registro22 = Sis_medico\Empresa::where('id',$registro->empresa)->first();
@endphp

<body>

  <div id="page_pdf">
    <table width="100%;border-bottom: 1px solid black;">
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
          $detalles_banc = Sis_medico\Ct_Detalle_Deposito_Bancario::where('deposito_bancario_id',$registro->id)->get();
          $numero = number_format($registro->valor_origen,2,'.','');
          @endphp <td style="width:50%">
            <span class="h3" style="padding:2px;font-weight:bold;font-size:medium;"><strong> TRASFERENCIA</strong></span>
            <span class="h3" style="font-weight:bold;font-size:small;"><strong> No : {{$numero_factura}}</strong></span>
            <span class="h3" style="font-weight: bold;"><strong> Por : $ *********{{$registro->total_deposito}}</strong></span>
          </td>
      </tr>
    </table>
    <table width="100%;border:1px solid back">
      <tr>
        <th class="th" style="border-right-style: solid;width:60%;">
          Concepto <br>
          {{$registro->concepto}}
        </th>
        <th class="th" style="border-right-style: solid">
          Tipo <br>
          {{$registro->tipo}}
        </th>
        <th class="th" style="border-right-style: solid">
          Fecha <br>
          {{date('d/m/Y', strtotime($registro->fecha_asiento))}}
        </th>
        <th class="th" style="border-right-style: solid">
          Glosa <br>
          {{$registro->glosa}}
        </th>
        <th class="th" style="border-right-style: solid">
          Asiento <br>
          {{$registro->id_asiento}}
        </th>
        <th class="th">
          Estado <br>
          @if($registro->estado==1)Activa @else Anulada @endif
        </th>

      </tr>

    </table>
    <table width="100%;border:1px solid back">
      <tr>
        <th class="th" style="border-right-style: solid;">
          Beneficiario <br>
          {{$registro->beneficiario}}
        </th>
        <th class="th" style="border-right-style: solid">
          R.U.C / C.I <br>
          {{$registro->ruc}}
        </th>
        <th class="th" style="border-right-style: solid">
          Cheque No <br>
          {{$registro->id_cuenta_origen}}
        </th>
        <th class="th" style="border-right-style: solid">
          Cuenta <br>
          {{$registro->id_asiento}}
        </th>
        <th class="th" style="border-right-style: solid">
          Fecha Ch <br>
          {{date('d/m/Y', strtotime($registro->fecha_cheque))}}
        </th>
        <th class="th" style="border-right-style: solid">
          Divisa <br>
          Dolar
        </th>
        <th class="th">
          Valor <br>
          {{$numero}}
        </th>

      </tr>

    </table>
    <table width="100%;border:1px solid back">
      <tr>
        <th class="th">
          Cuenta Destino <br>
          {{$variable2->nombre}}
        </th>
      </tr>
    </table>
    <table width="100%;border:1px solid back" style="border-left: none;border-right:none;">
      <tr>
        <th>
          Codigo
        </th>
        <th>
          Cuenta
        </th>
        <th>
          Div
        </th>
        <th>
          Valor
        </th>
        <th>
          Debe
        </th>
        <th>
          Haber
        </th>
      </tr>

      @foreach($detalle_asiento as $value)
      @php
      $var = Sis_medico\Plan_Cuentas::where('id',$value->id_plan_cuenta)->first();
      $suma_debe+=$value->debe;
      $suma_haber+=$value->haber;
      @endphp
      <tr>
        <td>{{$value->id_plan_cuenta}}</td>
        <td>{{$var->nombre}}</td>
        <td>$</td>
        <td>{{$detalle->valor}}</td>
        <td>{{$value->debe}}</td>
        <td>{{$value->haber}}</td>
      </tr>
      @endforeach
    </table>
    @php
    $suma_cont = number_format($suma_debe,2,'.','');
    $suma1_cont = number_format($suma_haber,2,'.','');
    @endphp
    <div style="display:block;float:right !important;margin-right:420px!important">
      <span style="font-weight: bold;">Sumas:</span>
      <span style="margin:180px">{{$suma_cont}} </span>
      <span style="margin:70px">{{$suma1_cont}} </span>
    </div>






  </div>
</body>

</html>