

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Cierre de Caja</title>
  <style>


    #page_pdf{
      width: 106%;
      margin-top: -40px;
      margin-left: -50px;
    }

    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      margin-bottom: 10px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    #detalle_totales span{
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura{
      width: 25%;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 31%;
    }

    .info_cliente{
      width: 69%;
    }

    .textright{
      padding-left: 3;
    }


    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    table{
       border-collapse: collapse;
       font-size: 8pt;
       font-family: 'arial';
       width: 100%;
    }


    table tr:nth-child(odd){
       background: #FFF;
    }

    table td{
      padding: 4px;
      font-size: 8pt !important;

    }

    table th{
       text-align: left;
       color:#3d7ba8;
       font-size: 7pt;
    }


    .totals_wrapper{
      width:100%;
    }
    .totals_label{
      display: inline-block;
      vertical-align: top;
      width:85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }
    .totals_value{
      display: inline-block;
      vertical-align: top;
      width:14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }
    .totals_separator{
      width:100%;
      height:1px;
      clear: both;
    }

    .separator{
      width:100%;
      height:60px;
      clear: both;
    }




    .details_title{
      background: #3d7ba8;
      color:#FFF;
      padding: 5px;
    }

    .round td{

    }
    #factura_detalle th .details_title{
      height: 50px !important;
    }
    #factura_detalle th {
      vertical-align: middle;
    }
  </style>
</head>
@php
  $subtotal   = 0;
  $iva    = 0;
  $impuesto   = 0;
  $tl_sniva   = 0;
  $total    = 0;
@endphp

<body>

  <div id="page_pdf">
    <div style="text-align: center;float: left;width: 50%">
      <img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}"  style="width:250px;height: 100px">
    </div>
    <div style="text-align: center; font-size:0.6em;float: left;width: 50%">
      R.U.C.: {{$empresa->id}}<br/>
      Nombre Comercial: {{$empresa->nombrecomercial}}<br/>
      Teléfono: {{$empresa->telefono1}}<br/>
      Dir.Matriz: {{$empresa->direccion}}<br/>

    </div>
    <div style="clear: both;"></div>
    <div style="float: left;width: 25%"><span style="font-size: 24px;"><b>Desde: </b> {{$fecha}} </span></div>
    <div style="float: left;width: 25%"><span style="font-size: 24px;"><b>Hasta: </b> {{$fecha_hasta}}</span></div>
    <div style="float: left;width: 25%"><span style="font-size: 24px;"><b>Caja: </b> {{$caja}}</span></div>
    <div style="clear: both;"></div>

    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0" style="text-align: center;">
      <thead>
        <tr>
          <th ><div class="details_title">Fecha Cita</div></th>
          <th ><div class="details_title">Hora Cita</div></th>
          <th ><div class="details_title">Paciente</div></th>
          <th ><div class="details_title">Cts</div></th>
          <th ><div class="details_title">Admision</div></th>
          <th ><div class="details_title">Doctor</div></th>
          <th ><div class="details_title">Procedimiento</div></th>
          <th ><div class="details_title">Seguro/Convenio</div></th>
          <!--<th ><div class="details_title">Tipo Cita</div></th>-->
          <th ><div class="details_title">Comprobante / Factura</div></th>
          <th ><div class="details_title">Referencia</div></th>
          <th ><div class="details_title">Efectivo</div></th>
          <th ><div class="details_title">Tarjeta</div></th>
          <!-- <th ><div class="details_title">7% T/C</div></th>
          <th ><div class="details_title">2% T/D</div></th> -->
          <th ><div class="details_title">Tran/Dep</div></th>
          <th ><div class="details_title">Cheque</div></th>
          <th ><div class="details_title">DataLink</div></th>
          <th ><div class="details_title">Pago APP</div></th>
          <th ><div class="details_title">pend fc seg</div></th>
          <th ><div class="details_title">Total Vta</div></th>
          <th ><div class="details_title">Honor. Medicos</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
        @php
          $tefectivo = 0;
          $ttarjeta = 0;
          $acum_efectivo = 0;
          $acum_datalink = 0;
          $acum_tcredito = 0;
          $acum_p7= 0;
          $acum_p2= 0;
          $acum_tran= 0;
          $acum_cheque= 0;
          $acum_pago_app= 0;
          $acum_oda= 0;
          $acum_total= 0;
          $acum_honorario= 0;
          $xcont=0;
        @endphp
        @foreach ($ordenes as $value)
          @php
            $pagos = $value->pagos;
            $efectivo  = 0;
            $datalink = 0;
            $tcredito = 0;
            $p7 = 0;
            $p2 = 0;
            $tran = 0;
            $cheque = 0;
            $pago_app = 0;
            $total = 0;
            $referencia = "";
            foreach($pagos as $pago){
              $total += $pago->valor;
              if($pago->tipo == '1'){
                $efectivo += $pago->valor;
              }
              if($pago->tipo == '9'){
                $datalink += $pago->valor;
              }
              if($pago->tipo == '10'){
                $pago_app += $pago->valor;
              }

              if($pago->tipo == 4){
                $va = $pago->valor/(1 +$pago->p_fi);
                $po = $va * $pago->p_fi;
                $tcredito += $va;
                $p7 += $po;
              }

              if($pago->tipo == 6){
                $va = $pago->valor/(1+$pago->p_fi);
                $po = $va * $pago->p_fi;
                $tcredito += $va;
                $p2 += $po;
              }

              if($pago->tipo == 3 || $pago->tipo == 5){
                $tran += $pago->valor;
              }

              if($pago->tipo == 2 ){
                $cheque += $pago->valor;
              }

            }
            if($efectivo > 0){
              $referencia = "CASH";
            }
            if($tcredito > 0){
              if(!is_null($referencia)){
                $referencia = $referencia."+Tarjeta ";
              }else{
                $referencia = "+Tarjeta ";
              }
            }
            if($tran > 0){
              if(!is_null($referencia)){
                $referencia = $referencia."+TRAN/DEP";
              }else{
                $referencia = "+TRAN/DEP";
              }
            }
            if($cheque > 0){
              if(!is_null($referencia)){
                $referencia = $referencia."+CH";
              }else{
                $referencia = "+CH";
              }
            }
            if($pago_app > 0){
              if(!is_null($referencia)){
                $referencia = $referencia."+Pago_APP";
              }else{
                $referencia = "+Pago_APP";
              }
            }
            $total += $value->valor_oda;
            $honorario = $total - $p2 - $p7;
            $acum_efectivo = $acum_efectivo + $efectivo;
            $acum_datalink = $acum_datalink + $datalink;
            $acum_tcredito = $acum_tcredito + $tcredito;
            $acum_p7 = $acum_p7 + $p7;
            $acum_p2 = $acum_p2 + $p2 ;
            $acum_tran = $acum_tran + $tran;
            $acum_cheque = $acum_cheque + $cheque;
            $acum_pago_app = $acum_pago_app + $pago_app;
            $acum_oda = $acum_oda + $value->valor_oda;
            $acum_total = $acum_total + $total;
            $acum_honorario = $acum_honorario + $honorario;
            $xcont ++;
          @endphp
          <tr class="round">
            <td style="font-size: 6pt !important;" >{{substr($value->agenda->fechaini, 0,10)}} </td>
            <td style="font-size: 7pt !important;" >{{substr($value->agenda->fechaini, 11,5)}} </td>
            <td style="font-size: 6pt !important;" >{{$value->agenda->paciente->apellido1}} @if($value->agenda->paciente->apellido2 != 'N/A'){{$value->agenda->paciente->apellido2}} @endif {{$value->agenda->paciente->nombre1}} @if($value->agenda->paciente->nombre2 != 'N/A'){{$value->agenda->paciente->nombre2}} @endif</td>
            <td style="font-size: 7pt !important;">{{$value->agenda->cortesia}}</td>
            <td style="font-size: 7pt !important;">{{$value->usercrea->apellido1}} {{$value->usercrea->nombre1}}</td>
            <td style="font-size: 7pt !important;">@if($value->agenda->doctor1!=null){{$value->agenda->doctor1->apellido1}} {{$value->agenda->doctor1->nombre1}}@endif</td>
            <!-- <td style="font-size: 7pt !important;">@if($value->agenda->proc_consul == 0)CONSULTA @else($value->agenda->proc_consul == 1) PROCEDIMIENTO @endif</td>  -->
            <td style="font-size: 7pt !important;">
              @if($value->agenda->doctor1!=null) 
                @if($value->agenda->doctor1->id == '4444444444') LABORATORIO 
                  @elseif($value->agenda->proc_consul == 0) CONSULTA 
                    @else($value->agenda->proc_consul == 1) PROCEDIMIENTO 
                @endif
              @else
                @if($value->agenda->proc_consul == 0) CONSULTA 
                  @else($value->agenda->proc_consul == 1) PROCEDIMIENTO
                @endif
              @endif </td> 
            @php  
              $seguro_recibo = \Sis_medico\Seguro::where('id', $value->id_seguro)->first();  
              $comprobante_fact = \Sis_medico\Ct_ventas::where('orden_venta', $value->id)->first();
            @endphp
            <td style="font-size: 7pt !important;" >{{$seguro_recibo->nombre}} </td>
          <!-- <td style="font-size: 7pt !important;"> @if($value->agenda->tipo_cita == 0) PRIMERA VEZ @else CONSECUTIVO @endif</td>-->
            <td style="font-size: 7pt !important;" >{{$value->id}} <br> @if(!is_null($comprobante_fact)) Fact/. {{$comprobante_fact->nro_comprobante}} @endif</td>
            <td style="font-size: 7pt !important;" >{{$referencia}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$efectivo)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$tcredito)}}</td>
            <!-- <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$p7)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$p2)}}</td> -->
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$tran)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$cheque)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$datalink)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$pago_app)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$value->valor_oda)}}</td>
            <td style="font-size: 7pt !important;" >{{sprintf("%.2f",$total)}}</td>
            <td style="font-size: 7pt !important;">{{sprintf("%.2f",$honorario)}}</td>
          </tr>
        @endforeach
          <tr>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td ></td>
            <td >{{sprintf("%.2f",$acum_efectivo)}}</td>
            <td >{{sprintf("%.2f",$acum_tcredito)}}</td>
            <!-- <td >{{sprintf("%.2f",$acum_p7)}}</td>
            <td >{{sprintf("%.2f",$acum_p2)}}</td> -->
            <td >{{sprintf("%.2f",$acum_tran)}}</td>
            <td >{{sprintf("%.2f",$acum_cheque)}}</td>
            <td >{{sprintf("%.2f",$acum_datalink)}}</td>
            <td >{{sprintf("%.2f",$acum_pago_app)}}</td>
            <td >{{sprintf("%.2f",$acum_oda)}}</td>
            <td >{{sprintf("%.2f",$acum_total)}}</td>
            <td>{{sprintf("%.2f",$acum_honorario)}}</td>
          </tr>
      </tbody>
    </table>
    <div style="clear: both; font-weight: bold;"> PENDIENTES EN GENERAR RECIBO DE COBRO</div>
    <div style="float: left;width: 25%"><span style="font-size: 24px;"><b>Desde: </b> {{$fecha}} </span></div>
    <div style="float: left;width: 25%"><span style="font-size: 24px;"><b>Hasta: </b> {{$fecha_hasta}}</span></div>
    <div style="clear: both;"></div>

    <table id="pendientes_detalle" border="0" cellpadding="0" cellpadding="0" style="text-align: center;">
      <thead>
        <tr>
          <th>
            <div class="details_title">Fecha Cita</div>
          </th>
          <th>
            <div class="details_title">Hora Cita</div>
          </th>
          <th>
            <div class="details_title">Paciente</div>
          </th>
          <th>
            <div class="details_title">Cts</div>
          </th>
          <th>
            <div class="details_title">Admision</div>
          </th>
          <th>
            <div class="details_title">Doctor</div>
          </th>
          <th>
            <div class="details_title">Procedimiento</div>
          </th>
          <th>
            <div class="details_title">Seguro/Convenio</div>
          </th>
          <!--<th ><div class="details_title">Tipo Cita</div></th>-->

        </tr>
      </thead>
      <tbody id="detalle_pendientes">
        @php
        $usuario_sesion = Auth::user()->id;
        $facturas_pendientes = Sis_medico\Agenda::leftjoin('ct_orden_venta as orden', function ($join) {
        $join->on(function($query){
        $query->on('orden.id_agenda','agenda.id')
        ->where('orden.estado', '=', '1');
        });
        })
        ->join('paciente as p', 'p.id', 'agenda.id_paciente')
        ->join('users as u', 'agenda.id_usuariomod', 'u.id')
        ->leftjoin('apps_agenda as app','app.id_agenda','agenda.id')
        ->whereNull('app.id')
        ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
        ->join('seguros as s', 's.id', 'agenda.id_seguro')
        ->where('s.tipo', '<>', '0')
          ->where('agenda.proc_consul', '<', '2' ) ->whereRaw('(agenda.omni = "%NO%" OR agenda.omni IS NULL)')
            ->where('agenda.estado', '<>', '0')
              ->whereNotNull('agenda.id_doctor1')
              ->whereNull('orden.id')
              ->where('agenda.estado_cita', '4')
              ->where('agenda.id_doctor1', '<>', '4444444444')
                ->where('u.id', $usuario_sesion)
                ->select('agenda.*', 'p.nombre1 as nombre1', 'p.apellido1 as apellido1', 'p.apellido2 as apellido2', 'u.nombre1 as unombre1', 'u.apellido1 as uapellido1', 'u.apellido2 as uapellido2', 'orden.id as orden')->get();
                @endphp

                @foreach($facturas_pendientes as $facturas)
                @if($facturas->orden==null)
                @if($facturas->doctor1->apellido1!='HUMANLABS')
                @php $segurox= \Sis_medico\Historiaclinica::where('id_agenda',$facturas->id)->first(); @endphp
                @if(!is_null($segurox))
                @if($segurox->seguro->tipo!=0)
                <tr>
                  <!--td> <a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->id}}</a></td-->
                  <td style="font-size: 7pt !important; text-align: left;"> {{date('d/m/Y',strtotime($facturas->fechaini))}} </td>
                  <td style="font-size: 7pt !important; text-align: left;">{{date('H:i:s',strtotime($facturas->fechaini))}}</td>
                  <td style="font-size: 7pt !important; text-align: left;">{{$facturas->apellido1}} {{$facturas->apellido2}} {{$facturas->nombre1}} </td>
                  <td style="font-size: 7pt !important; text-align: left;">@if($facturas->cortesia=="NO")<span class="label label-danger">{{$facturas->cortesia}}</span> @else <span class="label label-success">{{$facturas->cortesia}}</span> @endif</td>
                  <td style="font-size: 7pt !important; text-align: left;">{{$facturas->uapellido1}} {{$facturas->uapellido2}} {{$facturas->unombre1}}</td>
                  <td style="font-size: 7pt !important; text-align: left;">@if($facturas->doctor1!=null){{$facturas->doctor1->apellido1}} {{$facturas->doctor1->nombre1}}@endif</td>
                  <td style="font-size: 7pt !important; text-align: left;">@if($facturas->proc_consul == 0)CONSULTA @else($facturas->proc_consul == 1) PROCEDIMIENTO @endif</td>
                  <td style="font-size: 7pt !important; text-align: left;"> @if(!is_null($segurox)) {{$segurox->seguro->nombre}} @else {{$facturas->seguro->nombre}} @endif </td>
                </tr>
                @endif
                @endif
                @endif
                @endif
                @endforeach
      </tbody>

    </table>
    <br> 
    @if(!is_null($arqueo_caja))
      <div style="clear: both;"></div>
      <table style="width: 30%; text-align: center !important; ">
        <tr style="background-color: #3D7BA8">
            <td colspan="8">
                <center><label style="color: white; !important; font-size: 18px !important; font-weight: bold !important;">DETALLE ARQUEO DE CAJA</label></center>
            </td>
        </tr>

        <tr>
          <td style="font-size: 18px !important;  font-weight: bold;">Efectivo a recaudar:</td>
          <td style="font-size: 18px !important">${{$arqueo_caja->valor_efectivo}}</td>
        </tr>
        <tr>
          <td style="font-size: 18px !important; font-weight: bold !important; ">Vales:</td>
          <td style="font-size: 18px !important">${{$arqueo_caja->vales}}</td>
        </tr>
        <tr>
          <td style="font-size: 18px !important; font-weight: bold !important; ">Base:</td>
          <td style="font-size: 18px !important">${{$arqueo_caja->base}}</td>
        </tr>
      </table>
      <div style="clear: both;"></div>
      <table style="width: 30%; text-align: center !important; ">
        <thead>
          <tr>
            <th style="font-size: 18px; text-align: center;" class="details_title"> Denominacion </th>
            <th style="font-size: 18px; text-align: center;" class="details_title"> Cantidad </th>
            <th style="font-size: 18px; text-align: center;" class="details_title"> Total </th>
          </tr>
        </thead>
        <tbody id="detalle_productos"> @php $total_final = 0; @endphp
          
          @foreach($arqueo_caja->detalles as $detalle)
          @if($detalle->cantidad != 0)
            @php $total = $detalle->cantidad * $detalle->valor; $total_final += $total; @endphp
            <tr>
              <td style="font-size: 18px; text-align: center;"><b>{{ $detalle->denominacion }}</b></td>
              <td style="font-size: 18px; text-align: center;">{{ $detalle->cantidad }}</td>
              <td style="font-size: 18px; text-align: center;">${{ $total }}</td>
            </tr>
          @endif
          @endforeach
         
          <tr>
            <td style="font-size: 18px; text-align: center;"><b>SUMAN</b></td>
            <td style="font-size: 18px;"></td>
            <td style="font-size: 18px; text-align: center;">${{ $total_final }}</td>
          </tr>
        </tbody>
      </table>
    @endif

    <div class="col-md-12" style="margin-left: 50%">
      <br><br><br>
      <span style="font-size: 18px;border-top: solid 1px;"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Elaborado Por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
      <span style="font-size: 16px;"> {{Auth::user()->nombre1}} {{Auth::user()->apellido1}} {{Auth::user()->apellido2}}</span>
    </div>
    <br>
    @php
    $fecha = date("Y-m-d H:i:s");
    @endphp
    <div style="width: 50%; border: none !important;text-align:left;margin-top:2%">
      <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">FECHA IMPRESION:</b> {{$fecha}}</center>
    </div>

  </div>

</body>
</html>
