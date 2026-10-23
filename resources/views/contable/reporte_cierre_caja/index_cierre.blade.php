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

<div class="modal fade" id="detalle_paquete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>
@php
$id_user = Auth::user()->id;
$tipo_usuario = Auth::user()->id_tipo_usuario;
$agenda_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $id_user)->where('ip_creacion','like','%CIERRE X CAJA%')->first();
@endphp



<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{ route('cierrecaja.reporte')}}">
        {{ csrf_field() }}
        <div class="row">
          <div class="form-group col-md-3 col-xs-5" >
            <label for="numero" class="col-md-3 control-label">Nro</label>
            <div class="col-md-9">
              <input type="text" class="form-control input-sm" name="numero" id="numero" autocomplete="off" value="{{$numero}}">
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5">
            <label for="fecha" class="col-md-3 control-label">{{trans('contableM.Desde')}}</label>
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

          <div class="form-group col-md-3 col-xs-5">
            <label for="fecha_hasta" class="col-md-3 control-label">{{trans('contableM.Hasta')}}</label>
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

          <div class="form-group col-md-3 col-xs-5">
            <label class="col-md-3 control-label">{{trans('contableM.empresa')}}</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="id_empresa" id="id_empresa" style="width: 100%;">
                <option value="">Todas</option>
                @foreach($empresas as $empresa)
                <option @if($request['id_empresa']==$empresa->id) selected="selected" @endif value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-3 col-xs-5" >
            <label for="cedula" class="col-md-3 control-label">Cedula</label>
            <div class="col-md-9">
              <input type="text" class="form-control input-sm" name="cedula" id="cedula" autocomplete="off" value="{{$cedula}}">
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" >
            <label class="col-md-3 control-label">Caja</label>
            <div class="col-md-9">
                <select class="form-control input-sm" name="caja" id="caja" style="width: 100%;">
                <option value="">Todas</option>
                @foreach($cajas as $caja)
                  <option @if($request['caja'] == $caja->id) selected @endif value="{{$caja->id}}">{{$caja->descripcion}}</option>
                @endforeach
              </select>
            </div> 
          </div>
          <div class="form-group col-md-3 col-xs-5" >
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
          <div class="form-group col-md-3 col-xs-5" >
            <label class="col-md-3 control-label">Tipo</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="tipo" id="tipo">
                <option value="">Seleccione ...</option>
                <option @if($request->tipo=='0') selected @endif value="0">CONSULTAS</option>
                <option @if($request->tipo=='1') selected @endif value="1">PROCEDIMIENTOS</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-3 col-xs-5">
            <label class="col-md-3 control-label">Seguros</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="seguro" id="seguro" style="width: 100%;">
                <option value="">Seleccione ...</option>
                @foreach($seguros as $value)
                <option @if($value->id==$request->seguro) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5">
            <label class="col-md-3 control-label">Tipos Seguros</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="tipo_seguro" id="tipo_seguro" style="width: 100%;">
                <option value="">Seleccione ...</option>
                <option @if($request->tipo_seguro=='0') selected @endif value="0">{{trans('contableM.publico')}}</option>
                <option @if($request->tipo_seguro=='1') selected @endif value="1">{{trans('contableM.privado')}}</option>
                <option @if($request->tipo_seguro=='2') selected @endif value="2">{{trans('contableM.particular')}}</option>
              </select>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" >
            <label class="col-md-3 control-label">Usuario:</label>
            <div class="col-md-9">
                <select class="form-control input-sm select2"  style="width: 100%;" name="id_usuario" id="id_usuario">
                  @if(!is_null($usuario)) <option  selected  value="{{$usuario->id}}">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}  </option> @endif
                </select>
            </div>
          </div>
          <div class="form-group col-md-3 col-xs-5" >
            <button type="submit" formaction="{{ route('reporte.index_cierre')}}" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
          </div>
        </div>
          
        <div class="row">
          <div class="form-group col-md-2 col-xs-6">
            <button type="submit" class="btn btn-primary btn-sm" formtarget="_blank">
              <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;PDF</span></button>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('cierrecaja.imprimir_excel')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Excel</span></button>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <button type="submit" class="btn btn-success btn-sm" formaction="{{route('excel.reporte_apps')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Reporte Aplicación</span></button>
          </div>
          @php
          $nfecha = date('Y/m/d');
          $nfecha = strtotime('-3 day', strtotime($nfecha));
          $nfecha = date('Y/m/d',$nfecha);
          @endphp
          @if(date('Y/m/d',strtotime($fecha)) > $nfecha)
            @if($tipo_usuario == 1 || $agenda_permiso)
              <div class="form-group col-md-2 col-xs-6">
              <button type="button" class="btn btn-warning btn-sm" onclick="generar_arqueo_caja();"><span class="fa fa-file" aria-hidden="true" style="font-size: 14px"> {{trans('contableM.arqueocaja')}}</span></button>
              </div>
            @endif
            <div class="form-group col-md-2 col-xs-6">
              <button type="button" class="btn btn-success btn-sm" onclick="generar_arqueo_caja_usuario()"><span class="fa fa-file" aria-hidden="true" style="font-size: 14px"> {{trans('contableM.arqueo_caja_usuario')}}</span></button>
            </div>
          @endif
        </div>

        <!--<div class="form-group col-md-2 col-xs-2" >

          <a type="button" href="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" formtarget="_blank" >
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></a
          <button type="submit" formaction="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" id="boton_buscar">
          <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></button>
        </div>-->
      </form>
      @php 
        $kl=0;
      @endphp
      @foreach($facturas_pendientes as $f)
        @php
          if($f->doctor1->apellido1!='HUMANLABS'){
             $seguroxf= \Sis_medico\Historiaclinica::where('id_agenda',$f->id)->first(); 
            if($seguroxf!=null){
              if($seguroxf->seguro->tipo!=0){
                $kl++;
              }

            }

          }
        @endphp
      @endforeach
      <div class="form-group col-md-12 col-xs-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div class="tab" style="margin-top: 10px;">
            <a href="javascript:openCity(event,'contenedor')" style="margin-top: 10px;" class="btn btn-app tablinks btn-xs">
              <span class="badge bg-blue">{{count($ordenes)}}</span>
              <i class="fa fa-file-text"></i> Recibos
            </a>
            <!-- <button class="tablinks" onclick="openCity(event, 'contenedor2')"><b>Pendientes</b></button> -->
            <a href="javascript:openCity(event,'contenedor2')" style="margin-top: 10px;" class="btn btn-app tablinks btn-xs">
              <span class="badge bg-red">{{$kl}}</span>
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
                      @if($facturas->orden==null)
                      
                      @if($facturas->doctor1->apellido1!='HUMANLABS')
                      @php $segurox= \Sis_medico\Historiaclinica::where('id_agenda',$facturas->id)->first(); @endphp
                      @if(!is_null($segurox))
                      @if($segurox->seguro->tipo!=0)
                      <tr>
                        <td> <a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->id}}</a></td>
                        <td> <a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{date('d/m/Y',strtotime($facturas->fechaini))}}</a> </td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{date('H:i:s',strtotime($facturas->fechaini))}}</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">{{$facturas->apellido1}} {{$facturas->apellido2}}  {{$facturas->nombre1}}</a> </td>
                        <td>@if($facturas->cortesia=="NO")<span class="label label-danger">{{$facturas->cortesia}}</span> @else <span class="label label-success">{{$facturas->cortesia}}</span> @endif</td>
                        <td>{{$facturas->uapellido1}} {{$facturas->uapellido2}}  {{$facturas->unombre1}}</td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">@if($facturas->doctor1!=null){{$facturas->doctor1->apellido1}} {{$facturas->doctor1->nombre1}}@endif</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank">@if($facturas->proc_consul == 0)CONSULTA @else($facturas->proc_consul == 1) PROCEDIMIENTO @endif</a></td>
                        <td><a style="color: black;" href="{{ route('agenda.edit2', ['id' => $facturas->id, 'doctor' => $facturas->id_doctor1])}}" target="_blank"> @if(!is_null($segurox)) {{$segurox->seguro->nombre}} @else {{$facturas->seguro->nombre}} @endif</a> </td>
                      </tr>
                      @endif
                      @endif
                      @endif
                      @endif
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
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Pend Pago</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">PEND FC SEG</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Vta</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Honor. Medicos</th>
                      </tr>
                    </thead>
                    <tbody>
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
                      $acum_pend_pago= 0;
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
                      $pend_pago = 0;
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

                      if($pago->tipo == '7'){
                      $pend_pago += $pago->valor;
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

                      if($pend_pago > 0){
                      $referencia = "Pend Pago";
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
                      $acum_pend_pago = $acum_pend_pago + $pend_pago;
                      $acum_oda = $acum_oda + $value->valor_oda;
                      $acum_total = $acum_total + $total;
                      $acum_honorario = $acum_honorario + $honorario;
                      $xcont ++;
                      $procedimiento = "";
                      if($value->agenda->proc_consul == 1){
                      $pro_c = \Sis_medico\Procedimiento::find($value->agenda->id_procedimiento);
                      if(!is_null($pro_c)){
                      $procedimiento = $pro_c->observacion.'+';
                      }
                      $proced_total = \Sis_medico\AgendaProcedimiento::where('id_agenda', $value->id_agenda)->get();
                      foreach($proced_total as $value_pro){
                      $procedimiento = $procedimiento.$value_pro->procedimiento->observacion.'+';
                      }
                      $procedimiento = substr($procedimiento, 0, -1);
                      }
                      @endphp

                      <tr>
                        <td>

                          <a title="Descargar" target="_blank" class="btn btn-primary btn-xs" formtarget="_blank" id="examenes_externos" href="{{ route('facturacion.imprimir_ride', ['id_orden' => $value->id]) }}">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                          </a>

                          <!-- anterior recibo de cobro -->
                          <a title="Editar" href="{{ route('nuevorecibocobro.editar', ['orden' =>$value->id]) }}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>
                          <!--a href="{{ route('facturaagenda.editn', ['orden' =>$value->id]) }}" class="btn btn-warning  btn-xs">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a-->
                          <!--a class="btn btn-success btn-xs" data-remote="{{route('detalle_paquete.facturacion',['orden' =>$value->id])}}" data-toggle="modal" data-target="#detalle_paquete">
                            <span class="glyphicon glyphicon-plus"></span>
                          </a-->
                          @php 
                            $comprobante_fact = \Sis_medico\Ct_ventas::where('orden_venta', $value->id)->first(); 
                            $proforma = \Sis_medico\Proforma_Cabecera::where('id_orden', $value->id)->first(); 
                          @endphp
                          @if(!is_null($proforma) && !is_null($proforma->path_imagen))
                            <a title="Ver ODA" target="_blank" href="{{route('update_path_imagen.view',['id'=>$proforma->id])}}" class="btn btn-xs btn-success"><i class="fa fa-user-md" aria-hidden="true"></i></a>
                          @endif
                          @if(!is_null($comprobante_fact))
                            <a title="Ver factura" class="btn btn-primary btn-xs" href="{{ route('ventas.comprobante_publico', ['comprobante' => $comprobante_fact->nro_comprobante, 'id_empresa' => $comprobante_fact->id_empresa, 'tipo' => 'pdf']) }}">RIDE</a>
                          @endif
                          
                        </td>
                        <td>{{substr($value->agenda->fechaini, 0,10)}} @if($value->estado=='0') <span style="color: red">Eliminado</span> @endif</td>
                        <td>{{substr($value->agenda->fechaini, 11,5)}} </td>
                        <td>{{$value->agenda->paciente->apellido1}} @if($value->agenda->paciente->apellido2 != 'N/A'){{$value->agenda->paciente->apellido2}} @endif {{$value->agenda->paciente->nombre1}} @if($value->agenda->paciente->nombre2 != 'N/A'){{$value->agenda->paciente->nombre2}} @endif</td>

                        <td>{{$value->agenda->cortesia}}</td>

                        <td>{{$value->usercrea->apellido1}} {{$value->usercrea->nombre1}}</td>

                        <td>@if($value->agenda->doctor1!=null){{$value->agenda->doctor1->apellido1}} {{$value->agenda->doctor1->nombre1}}@endif</td>


                        <!-- <td>@if($value->caja=='LABORATORIO') LABORATORIO @else @if($value->agenda->proc_consul == 0)CONSULTA @else($value->agenda->proc_consul == 1) {{$procedimiento}} @endif @endif</td> -->

                        <td>
                          @if($value->caja=='LABORATORIO') LABORATORIO 
                            @else 
                              @if($value->agenda->doctor1!=null)
                                @if($value->agenda->doctor1->id == '4444444444') LABORATORIO 
                                  @elseif($value->agenda->proc_consul == 0) CONSULTA 
                                    @else($value->agenda->proc_consul == 1) {{$procedimiento}} 
                                @endif 
                              @else
                                @if($value->agenda->proc_consul == 0) CONSULTA 
                                  @else($value->agenda->proc_consul == 1) {{$procedimiento}} 
                                @endif
                              @endif             
                          @endif</td>
                         @php $seguro_recibo = \Sis_medico\Seguro::where('id', $value->id_seguro)->first(); @endphp
                        <td>@if(!is_null($seguro_recibo)) {{$seguro_recibo->nombre}} @endif</td>
                        
                        <td>@if(!is_null($value->id)) {{$value->id}} @endif</td>

                        <td>{{$referencia}}</td>
                        <td>{{sprintf("%.2f",$efectivo)}}</td>
                        <td>{{sprintf("%.2f",$tcredito)}}</td>
                        <!-- <td>{{sprintf("%.2f",$p7)}}</td>
                        <td>{{sprintf("%.2f",$p2)}}</td> -->
                        <td>{{sprintf("%.2f",$tran)}}</td>
                        <td>{{sprintf("%.2f",$cheque)}}</td>
                        <td>{{sprintf("%.2f",$datalink)}}</td>
                        <td>{{sprintf("%.2f",$pago_app)}}</td>
                        <td>{{sprintf("%.2f",$pend_pago)}}</td>
                        <td>{{sprintf("%.2f",$value->valor_oda)}}</td>
                        <td>{{sprintf("%.2f",$total)}}</td>
                        <td>{{sprintf("%.2f",$honorario)}}</td>
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
                        <td>{{sprintf("%.2f",$acum_pend_pago)}}</td>
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


<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="form-group col-md-9">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado_pro_agrup">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <th> ID </th>
                      <th> Fecha Proceso </th>
                      <th> Usuario </th>
                      <th> Caja </th>
                      <th>Denominacion</th>
                      <th> Valor en Efectivo </th>
                      <th> Acción </th>
                    </tr>
                  </thead>
                  @foreach($arqueos as $value)
                  <tbody>
                    <tr>
                      <td> {{$value->id}} </td>
                      <td> {{$value->fecha_proceso}} </td>
                      <td> @if($value->id_usuario != null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @else N/A @endif </td>
                      <td>{{$value->caja}} </td>

                      @php $detalles = $value->detalles;
                      $det = [];
                      if($detalles->count() > 0){
                      $det = $detalles->where('cantidad','!=','0.00');
                      }

                      @endphp

                      <td> @foreach( $det as $detalle) {{$detalle->cantidad}}
                        ( {{$detalle->denominacion}} ) <br> @endforeach </td>

                      <td> {{$value->valor_efectivo}}</td>
                      <td>
                        @if($tipo_usuario == 1)
                        <a href="{{route('ct_arqueo_caja.editar_arqueo', ['id'=>$value->id])}}" class="btn btn-warning"><i class="fa fa-edit">
                            @endif
                          </i></a><br> </br>
                        <form method="POST" id="pdf_arqueo_caja" action="{{ route('cierrecaja.reporte')}}" target="_blank">
                          {{ csrf_field() }}
                          <input type="hidden" name="fecha" id="fecha" value="{{$fecha}}">
                          <input type="hidden" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}">
                          <input type="hidden" name="id_usuario" id="id_usuario" value="{{$value->id_usuario}}">
                          <input type="hidden" name="caja" id="caja" value="{{$value->caja}}">
                          <input type="hidden" name="exportar" id="exportar" value="0">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                              pdf
                            </button>
                          </div>
                        </form>
                      </td>
                    </tr>
                  </tbody>
                  @endforeach
                </table>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  $('#detalle_paquete').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });

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

    openCity(1, 'contenedor');
    $("#fecha_hasta").on("dp.change", function(e) {});
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

   $('.select2').select2({
      placeholder: 'Seleccione Usuario',
      allowClear: true, 
      ajax: {
        url: '{{route("cierrecaja.buscar_usuarios")}}',
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

  function generar_arqueo_caja() {
    var caja = $('#caja').val();
    var fecha = $('#fecha').val();
    var mensaje = '';

    if (caja == '') {
      mensaje = 'seleccione la Caja \n';
    }

    if (fecha == '') {
      mensaje = mensaje + 'seleccione la fecha \n';
    }

    if (mensaje != '') {
      alert(mensaje);
    } else {
      var confirmar = confirm("Desea crear Arqueo por Caja " + caja);
      if (confirmar) {
        $.ajax({
          type: 'post',
          url: "{{route('ct_arqueo_caja.crear_arqueo')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            'caja': caja,
            'fecha': fecha,
          },
          success: function(data) {
            location.href = "{{url('contable/arqueo_caja/editar')}}/" + data;
            //console.log(data);
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    }

  }

  function generar_arqueo_caja_usuario() {
    var caja = $('#caja').val();
    var fecha = $('#fecha').val();
    var mensaje = '';

    if (caja == '') {
      mensaje = 'seleccione la Caja \n';
    }

    if (fecha == '') {
      mensaje = mensaje + 'seleccione la fecha \n';
    }

    if (mensaje != '') {
      alert(mensaje);
    } else {
      var confirmar = confirm("Desea crear Arqueo por Caja " + caja);
      if (confirmar) {
        $.ajax({
          type: 'post',
          url: "{{route('ct_arqueo_caja.crear_arqueo_user')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            'caja': caja,
            'fecha': fecha,
          },
          success: function(data) {
            location.href = "{{url('contable/arqueo_caja/editar')}}/" + data;
            //console.log(data);
          },
          error: function(data) {
            console.log(data);

          }
        });
      }
    }
  }
</script>
@endsection