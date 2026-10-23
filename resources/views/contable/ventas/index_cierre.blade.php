@extends('contable.reporte_cierre_caja.base')
@section('action-content')
<style type="text/css">
  table tr td {
    font-size: 10px;
  }

  th {
    font-size: 12px;
  }

  .tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
  }

  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  .tab button:hover {
    background-color: #ddd;
  }

  .tab button.active {
    background-color: #ccc;
  }

  .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  .tabcontent2 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master">
        {{ csrf_field() }}
        <div class="row">
          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="col-md-3 control-label">Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label">Empresa</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="id_empresa" id="id_empresa">
                <option value="">Todas</option>
                @foreach($empresas as $empresa)
                <option @if($request['id_empresa']==$empresa->id) selected="selected" @endif value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label">Caja</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="caja" id="caja">
                <option value="">Todas</option>
                @foreach($cajas as $caja)
                <option @if($request['caja']==$caja->id) selected @endif value="{{$caja->id}}">{{$caja->descripcion}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label">Doctor</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="doctor" id="doctor">
                <option value="">Seleccione ...</option>
                @foreach($doctores as $doctor)
                <option @if($doctor->id==$request->doctor) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label">Paciente:</label>
            <div class="col-md-9">
              <select class="form-control input-sm select2_paciente" style="width: 100%;" name="id_paciente" id="id_paciente">
                @if(!is_null($usuario_paciente)) <option selected value="{{$usuario_paciente->id}}">{{$usuario_paciente->apellido1}} {{$usuario_paciente->apellido2}} {{$usuario_paciente->nombre1}} {{$usuario_paciente->nombre2}} </option> @endif
              </select>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label">Tipo</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="tipo" id="tipo">
                <option value="">Seleccione ...</option>
                <option @if($request->tipo=='0') selected @endif value="0">CONSULTAS</option>
                <option @if($request->tipo=='1') selected @endif value="1">PROCEDIMIENTOS</option>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3 col-xs-5" style="text-align: left;">
            <button type="submit" formaction="{{ route('ventas.index_cierre')}}" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
          </div>
        </div>

        <!--<div class="form-group col-md-2 col-xs-2" >

          <a type="button" href="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" formtarget="_blank" >
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></a
          <button type="submit" formaction="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" id="boton_buscar">
          <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></button>
        </div>-->
      </form>

      <div class="form-group col-md-12 col-xs-12">
        <div class="form-row">
          <div class="col-md-12">
            &nbsp;
          </div>
          <div class="tab" style="margin-top: 10px;">
            <a href="javascript:openCity(event,'contenedor')" style="margin-top: 10px;" class="btn btn-app tablinks btn-xs">
              <span class="badge bg-blue">{{count($ordenes)}}</span>
              <i class="fa fa-file-text"></i> Recibos
            </a>
            <!-- <button class="tablinks" onclick="openCity(event, 'contenedor2')"><b>Pendientes</b></button> -->
            <a href="javascript:openCity(event,'contenedor2')" style="margin-top: 10px;" class="btn btn-app tablinks btn-xs">
              <span class="badge bg-red">{{count($facturas_pendientes)}}</span>
              <i class="fa fa-file-text"></i> Pendientes
            </a>
          </div>
          <div class="tabcontent" id="contenedor2">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"><span class="glyphicon glyphicon-download-alt" style="font-size: 16px"></span></th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cita</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hora Cita</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Paciente</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cts</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Admision</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Doctor</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimientos</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro/Convenio</th>
                        <!--<th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Cita</th>-->
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Generar Factura</th>

                      </tr>
                    </thead>
                    <tbody>
                      @foreach($facturas_pendientes as $facturas)
                      <tr>
                        <td> <a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->id}}</a></td>
                        <td> <a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{date('d/m/Y',strtotime($facturas->fechaini))}}</a> </td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{date('H:i:s',strtotime($facturas->fechaini))}}</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->apellido2}} {{$facturas->apellido1}} {{$facturas->nombre1}}</a> </td>
                        <td>@if($facturas->cortesia=="NO")<span class="label label-danger">{{$facturas->cortesia}}</span> @else <span class="label label-success">{{$facturas->cortesia}}</span> @endif</td>
                        <td>{{$facturas->uapellido2}} {{$facturas->uapellido1}} {{$facturas->unombre1}}</td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">@if($facturas->doctor1!=null){{$facturas->doctor1->apellido1}} {{$facturas->doctor1->nombre1}}@endif</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">@if($facturas->proc_consul == 0)CONSULTA @else($facturas->proc_consul == 1) PROCEDIMIENTO @endif</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->seguro->nombre}}</a> </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="tabcontent" id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"><span class="glyphicon glyphicon-download-alt" style="font-size: 16px"></span></th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Cita</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hora Cita</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Paciente</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cts</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Admision</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Doctor</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimientos</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro/Convenio</th>
                        <!--<th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Cita</th>-->
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">N° de Comprobante</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Referencia </th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Efectivo </th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tarjeta </th>
                        <!-- <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">7% T/C </th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">2% T/D </th> -->
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tran/Dep</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cheque</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">DataLink</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Pago APP</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">PEND FC SEG</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Vta</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Honor. Medicos</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Generar Factura</th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $tefectivo = 0;
                      $ttarjeta = 0;
                      $acum_efectivo = 0;
                      $acum_datalink = 0;
                      $acum_pago_app = 0;
                      $acum_tcredito = 0;
                      $acum_p7= 0;
                      $acum_p2= 0;
                      $acum_tran= 0;
                      $acum_cheque= 0;
                      $acum_oda= 0;
                      $acum_total= 0;
                      $acum_honorario= 0;
                      $xcont=0;
                      @endphp
                      @foreach ($ordenes as $value)
                      @php

                      $pagos = $value->pagos;
                      $efectivo = 0;
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
                      $acum_pago_app = $acum_pago_app + $pago_app;
                      $acum_tcredito = $acum_tcredito + $tcredito;
                      $acum_p7 = $acum_p7 + $p7;
                      $acum_p2 = $acum_p2 + $p2 ;
                      $acum_tran = $acum_tran + $tran;
                      $acum_cheque = $acum_cheque + $cheque;
                      $acum_oda = $acum_oda + $value->valor_oda;
                      $acum_total = $acum_total + $total;
                      $acum_honorario = $acum_honorario + $honorario;
                      $xcont ++;
                      @endphp
                      <?php
                      $apellidoCliente = $value->agenda->paciente->apellido1 . ' ' . $value->agenda->paciente->apellido2 != 'N/A' ? $value->agenda->paciente->apellido2 : '';
                      $nombreCliente = $value->agenda->paciente->nombre1 . ' ' . $value->agenda->paciente->nombre2 != 'N/A' ? $value->agenda->paciente->nombre2 : '';
                      $naCliente = $apellidoCliente . ' ' . $nombreCliente;
                      ?>
                      <tr>
                        <td>
                          <a target="_blank" class="btn btn-primary btn-xs" formtarget="_blank" id="examenes_externos" href="{{ route('facturacion.imprimir_ride', ['id_orden' => $value->id]) }}">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                          </a>
                          <button type="button" onclick="anular_recibo({{$value->id}});" class="btn btn-danger btn-xs" data-dismiss="modal">
                            <span class="glyphicon glyphicon-trash"></span>
                          </button>
                          <!-- recibo anterior -->
                          <!--a href="{{ route('factura.editar_cp', ['orden' =>$value->id,'valor' =>0]) }}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a-->
                          <a href="{{ route('nuevorecibocobro.editar', ['orden' =>$value->id]) }}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>
                        </td>
                        <td>{{substr($value->agenda->fechaini, 0,10)}} </td>
                        <td>{{substr($value->agenda->fechaini, 11,5)}} </td>
                        <td>{{$value->agenda->paciente->apellido1}} @if($value->agenda->paciente->apellido2 != 'N/A'){{$value->agenda->paciente->apellido2}} @endif {{$value->agenda->paciente->nombre1}} @if($value->agenda->paciente->nombre2 != 'N/A'){{$value->agenda->paciente->nombre2}} @endif</td>
                        <td>{{$value->agenda->cortesia}}</td>

                        <td>{{$value->usercrea->apellido1}} {{$value->usercrea->nombre1}}</td>

                        <td>@if($value->agenda->doctor1!=null){{$value->agenda->doctor1->apellido1}} {{$value->agenda->doctor1->nombre1}}@endif</td>

                        <!-- <td>@if($value->agenda->proc_consul == 0)CONSULTA @else($value->agenda->proc_consul == 1) PROCEDIMIENTO @endif</td> -->

                        <td>
                          @if($value->caja=='LABORATORIO') LABORATORIO
                          @else
                          @if($value->agenda->doctor1!=null)
                          @if($value->agenda->doctor1->id == '4444444444') LABORATORIO
                          @elseif($value->agenda->proc_consul == 0) CONSULTA
                          @else($value->agenda->proc_consul == 1) PROCEDIMIENTO
                          @endif
                          @else
                          @if($value->agenda->proc_consul == 0) CONSULTA
                          @else($value->agenda->proc_consul == 1) PROCEDIMIENTO
                          @endif
                          @endif
                          @endif</td>

                        @php $seguro_recibo = \Sis_medico\Seguro::where('id', $value->id_seguro)->first(); @endphp
                        <td>{{$seguro_recibo->nombre}} </td>
                        <!--<td> @if($value->agenda->tipo_cita == 0) PRIMERA VEZ @else CONSECUTIVO @endif</td>-->
                        <td> {{$value->id}}</td>

                        <td>{{$referencia}}</td>
                        <td>{{sprintf("%.2f",$efectivo)}}</td>
                        <td>{{sprintf("%.2f",$tcredito)}}</td>
                        <!-- <td>{{sprintf("%.2f",$p7)}}</td>
                        <td>{{sprintf("%.2f",$p2)}}</td> -->
                        <td>{{sprintf("%.2f",$tran)}}</td>
                        <td>{{sprintf("%.2f",$cheque)}}</td>
                        <td>{{sprintf("%.2f",$datalink)}}</td>
                        <td>{{sprintf("%.2f",$pago_app)}}</td>
                        <td>{{sprintf("%.2f",$value->valor_oda)}}</td>
                        <td>{{sprintf("%.2f",$total)}}</td>
                        <td>{{sprintf("%.2f",$honorario)}}</td>
                        <td>
                          @php
                          if(!is_null($value)){
                          $facturada = Sis_medico\Ct_ventas::where('orden_venta', $value->id)->where('estado', '<>','0')->count();
                            $orden_vent = Sis_medico\Ct_Orden_Venta::where('id', $value->id)->first();
                            }
                            $t_pago = Sis_medico\Ct_Orden_Venta_pago::where('id_orden', $value->id)->first();
                            $pago_app = "";
                            if(!is_null($t_pago)){
                            $pago_app = $t_pago->metodo->nombre;
                            }

                            @endphp
                            <input type="hidden" name="forma_app" id="forma_app" value="{{$pago_app}}">
                            @if($pago_app != 'PAGO POR APP')
                            @if($facturada>0)
                            <a class="btn btn-primary btn-xs" disabled>
                              <span class="glyphicon glyphicon-edit" aria-hidden="true" style="font-size: 16px"></span></a>
                            @else
                            <a target="_blank" class="btn btn-primary btn-xs" formtarget="_blank" id="examenes_externos" href="{{ route('venta.factura_orden', ['id_orden' => $value->id]) }}">
                              <span class="glyphicon glyphicon-edit" aria-hidden="true" style="font-size: 16px"></span></a>
                            @if($orden_vent->valor_oda == '0')
                            <a target="_blank" class="btn btn-success btn-xs" formtarget="_blank" id="orden_vent" href="{{ route('orden_venta')}}">
                              <span class="glyphicon glyphicon-edit" aria-hidden="true" style="font-size: 16px"></span></a>
                            @endif

                            @php $ct_ven_orden = \Sis_medico\Ct_Ven_Orden::where('orden_venta',$value->id)->where('estado', 1)->first(); @endphp
                            @if(is_null($ct_ven_orden))
                            @if($orden_vent->valor_oda != '0' && $orden_vent->total == '0.00')
                            <a target="_blank" class="btn btn-warning btn-xs" formtarget="_blank" id="orden_vent" onclick="ordenes_crear_nuevo('<?= $value->id; ?>','<?= $value->agenda->paciente->id; ?>','<?= $naCliente; ?>');">
                              <span class="glyphicon glyphicon-paperclip" aria-hidden="true" style="font-size: 16px"></span></a>
                            @endif
                            @endif

                            @endif
                            @else
                            <span style="font-size: 14px">Pagado por APP</span>
                            @endif
                        </td>
                      </tr>
                      @endforeach
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{sprintf("%.2f",$acum_efectivo)}}</td>
                        <td>{{sprintf("%.2f",$acum_tcredito)}}</td>
                        <!-- <td>{{sprintf("%.2f",$acum_p7)}}</td>
                        <td>{{sprintf("%.2f",$acum_p2)}}</td> -->
                        <td>{{sprintf("%.2f",$acum_tran)}}</td>
                        <td>{{sprintf("%.2f",$acum_cheque)}}</td>
                        <td>{{sprintf("%.2f",$acum_datalink)}}</td>
                        <td>{{sprintf("%.2f",$acum_pago_app)}}</td>
                        <td>{{sprintf("%.2f",$acum_oda)}}</td>
                        <td>{{sprintf("%.2f",$acum_total)}}</td>
                        <td>{{sprintf("%.2f",$acum_honorario)}}</td>
                      </tr>
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
              </div>
            </div>
          </div>
        </div>
      </div>
</section>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',

    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
    });

    $("#fecha").on("dp.change", function(e) {
      $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
    });

    $("#fecha_hasta").on("dp.change", function(e) {});
    openCity(1, 'contenedor');
  });

  function openCity(evt, id) {

    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");

    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");

    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(id).style.display = "block";
    evt.currentTarget.className += " active";
  }

  function anular_recibo(id) {

    var confirmar = confirm("Desea eliminar recibo de cobro");

    if (confirmar) {
      $.ajax({
        type: 'post',
        url: "{{route('recibo_cobro.anular')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
          'id_orden': id
        },
        success: function(data) {
          //console.log(data);
        },
        error: function(data) {
          console.log(data);

        }
      });
    }
  }

  function ordenes_crear_nuevo(idOrden, idCliente, nombreCliente) {
    var confirmar = confirm("Desea enviar a conglomerada");
    if (confirmar) {
      $.ajax({
        type: 'post',
        url: "{{route('orden_venta_crear_nuevo')}}",
        datatype: 'json',
        data: {
          _token: $('input[name=_token]').val(),
          id_orden: idOrden,
          idCliente: idCliente,
          nombreCliente: nombreCliente
        },
        success: function(data) {
          Swal.fire("Correcto!", "Guardado Correctamente", "success");
        },
        error: function(data) {
          console.log(data);
        }
      });
    }
  }
  $('.select2_paciente').select2({
    placeholder: 'Seleccione Paciente',
    allowClear: true,
    ajax: {
      url: '{{route("ventas.buscador_paciente")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        console.log(data);
        return {
          results: data
        };
      }
    },
  });
</script>
@endsection