@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 15px;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 40px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {
    padding: 0.70px;
    text-align: center;
  }

  .secundario {
    left: 10px;
  }

  .table {
    margin-bottom: -10px;
  }

  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .hidden-paginator {

    display: none;

  }

  .removethe {
    display: none;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.acreedor')}}</a></li>
    </ol>
  </nav>

  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{ route('deudasvspagos.index') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
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
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>


        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor" style="width: 100%;">
            <option value="">Seleccione...</option>
            @foreach($proveedores as $value)
            <option @if(!is_null($id_proveedor)) @if(($id_proveedor)==$value->id) selected='selected' @endif @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endforeach
          </select>

        </div>


        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >{{trans('contableM.Mostrarresumen')}}</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>


      </form>
    </div>
    <!-- /.box-body -->
    <div class="box-body dobra">
      <form method="POST" id="print_reporte_master" action="{{ route('deudasvspagos.excel') }}" target="_blank">
        {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
        <input type="hidden" name="es_fact_dos" id="es_fact_dos">
        <div class="col-md-6 col-xs-9">
          <button type="submit" class="btn btn-success"> <span class="fa fa-file-excel-o"></span>{{trans('contableM.excel')}}</button>
          <button type="submit" class="btn btn-success" formaction="{{route('deudasvspagos.informe_pdf')}}"><span class="fa fa-file-pdf-o"> </span>{{trans('contableM.Pdf')}}</button>
        </div>
      </form>
    </div>

    @if(count($deudas)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial!=''?$empresa->nombrecomercial:''}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.DeudasvsPagos')}}</h4>
            <h4 style="text-align: center;">{{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">{{trans('contableM.telefono')}}: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
              <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
            </dl>
          </div>
        </div> --}}

        <div class="box-body">
          <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
            </dl>
          </div>
          <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial!=''?$empresa->nombrecomercial:''}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.DeudasvsPagos')}}</h4>
            @if(($fecha_desde!=null))
            <h5 style="text-align: center;">Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
            @else
            <h5 style="text-align: center;">Al - {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
            @endif
          </div>
          <div class="col-md-4">
          </div>
          <div class="col-md-12">
            <label class="label label-info">Ref: Es el ID de la Factura</label>
          </div>
          <div class="col-md-12">
            &nbsp;
          </div>
          @php
          $cont=0;
          @endphp
          <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr>
                <th width="30%" style="text-align:center;" tabindex="0" aria-controls="example2">{{trans('contableM.detalles')}}</th>
                <!--<th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2">Mtedo de Pago</th>-->
                <th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2">{{trans('contableM.fecha')}}</th>
                <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2"> </th>
                <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">{{trans('contableM.valor')}}</th>
                <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">{{trans('contableM.Debe')}}</th>
                <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">{{trans('contableM.Haber')}}</th>
                <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">{{trans('contableM.saldo')}}</th>
              </tr>
            </thead>
            <tbody>
              <!-- u can change this query with simple query date 1/12/2020 -->
              @php
              $debet=0;
              $habert=0;
              $valor_contable=0;
              $totalRasumidas=0;
              @endphp
              @foreach($deudas as $value)
              @if(($value)!=null)
              @if(isset($value->estado) && $value->estado>0)
              @php
              $debet+= $value->total_final;
              @endphp
              <tr>
                <td style="color: black; font-weight: bold; text-align: left; ">
                  @if(($value)!=null) @if(($value->secuencia_f)!=null && (($value->numero)!=null)) {{isset($value->proveedorf->nombrecomercial)?$value->proveedorf->nombrecomercial:''}} Fact: # {{$value->numero}} Ref: {{$value->id}} @endif @endif<br>
                  <label id="co{{$value->id}}"> </label>
                  @if($value->valor_contable > 0)
                  @php
                  $fec=new DateTime($value->f_autorizacion);
                  $fec2=new DateTime(date('Y-m-d'));
                  $fultima = date('Y-m-d');
                  $diff = $fec->diff($fec2);
                  $daysf=$diff->format("%r%a");
                  //$habert+= $value->valor_contable;
                  $valor_contable+= $value->valor_contable;
                  @endphp
                  <label class="label @if($daysf>10 && $daysf<20) label-warning @elseif($daysf>=20) label-danger @else label-primary @endif">{{$daysf}} Dias</label>
                  @else
                  @php
                  $fultima = '0012-07-31';
                  /*if(Auth::user()->id=='0921605895')
                  {
                  echo '
                  <pre>';print_r($value);exit;
                  }*/
                  @endphp
                  @endif
                  </span>
                </td>
                <td style="color: black; font-weight: bold;">@if(($value->f_autorizacion)!=null) {{date("d-m-Y", strtotime($value->f_autorizacion))}} @endif</td>
                <td style="color: black; font-weight: bold; text-align: left;">{{$value->id_plan_cuenta}} | {{substr($value->descripcion,0,20)}}<br /> <a href="{{url('/')}}/contable/fact_contable/editar/{{$value->id}}" target="_blank">COM-FA @if(($value->numero)!=null) {{$value->numero}} @endif</td>
                <td style="text-align:right; color: black; font-weight: bold;">@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                <td style="text-align:right; color: black; font-weight: bold;">@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                <td style="text-align:right; color: black; font-weight: bold;">0.00</td>
                <td style="text-align:right; color: black; font-weight: bold;"> {{number_format($value->valor_contable,2,'.',',')}} </td>
              </tr>
              @if(($value->egresos)!=null && (($value->egresos)!='[]'))
              @foreach($value->egresos as $v)
              @if(isset($v->comp_egreso->estado) && $v->comp_egreso->estado==1)
              @php
              $habert+= $v->abono;


              $pagos_tipo_pago = "";
              if(isset($v->comp_egreso)){
              if(isset($v->comp_egreso->bancoa)){
              if(!is_null($v->comp_egreso->bancoa->nombre)){
              $pagos_tipo_pago = $v->comp_egreso->bancoa->nombre . " / " ;
              }
              }
              }

              if(isset($v->comp_egreso)){
              if(isset($v->comp_egreso->tipo_pago)){
              $pagos_tipo_pago = $pagos_tipo_pago . "" . $v->comp_egreso->tipo_pago->nombre;
              }
              }
              @endphp
              @if($v->comp_egreso->id_empresa==$empresa->id)
              <tr>
                @if($value->valor_contable == 0)
                @php
                
                if($fultima < $v->comp_egreso->fecha_comprobante){
                  $fultima = $v->comp_egreso->fecha_comprobante;
                  }
                  $habert+= $value->valor_contable-$v->comp_egreso->rt_asumida_valor;
                  $totalRasumidas+=$v->comp_egreso->rt_asumida_valor;
                  @endphp
                  @endif
                  <td style="text-align: left;">@if(($v->comp_egreso)!='[]') @if(($v->comp_egreso->descripcion)!=null)
                    <p style="margin-bottom: 0px!important;" class="s1"> {{isset($value->proveedorf->nombrecomercial)?$value->proveedorf->nombrecomercial:''}}
                      # {{$v->comp_egreso->descripcion}} Ref: {{$value->numero}} -- Asiento: {{$v->comp_egreso->id_asiento_cabecera}}
                    </p>
                    <label style="font-size: 11px;" for="" class="label label-success">
                      #egreso {{$v->id_comprobante}} @if($pagos_tipo_pago!= "") --Método de pago: {{$pagos_tipo_pago}} @endif
                    </label>
                    @endif
                    @endif
                  </td>
                  <td>@if(isset($v->comp_egreso)) @if(($v->comp_egreso->fecha_comprobante)!=null) {{date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante))}} @endif @endif</td>
                  <td style="text-align: left;">ACR-EG @if(isset($v->comp_egreso)) @if(($v->comp_egreso->secuencia)!=null) {{$v->comp_egreso->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($v->abono)!=null) {{number_format($v->abono-$v->comp_egreso->rt_asumida_valor,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($v->abono)!=null) {{number_format($v->abono-$v->comp_egreso->rt_asumida_valor,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
              </tr>
              @endif
              @endif
              @endforeach
              @endif
              @if(($value->bndebito)!=null && (($value->bndebito)!='[]'))
              @foreach($value->bndebito as $v)
              @php
              $meto_pago = "";
              if(isset($v->cabecera)){
              //dd($v->cabecera->nombre);
              if(!is_null($v->cabecera->nombre)){
              $meto_pago = "-- Método de pago". $v->cabecera->nombre;
              }
              }
              @endphp
              @if(isset($v->cabecera->estado) && $v->cabecera->estado==1)
              @php
              $habert+= $v->abono;
              @endphp
              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $v->cabecera->fecha){
                  $fultima = $v->cabecera->fecha;
                  }
                  $habert+= $value->valor_contable;
                  @endphp
                  @endif
                  <td style="text-align: left;">
                    @if(($v->cabecera)!='[]')
                    @if(($v->cabecera->concepto)!=null)
                    <p style="margin-bottom: 0px!important;" class="s1"> {{isset($value->proveedorf->nombrecomercial)?$value->proveedorf->nombrecomercial:''}} # {{$v->cabecera->concepto}} Ref: {{$value->numero}} -- Asiento: {{$v->cabecera->id_asiento}}</p>
                    <label for="" class="label label-success" style="font-size: 11px;"> # Debito: {{$v->id_debito}} {{$meto_pago}}</label>
                    @endif
                    @endif
                  </td>
                  <td> @if(($v->cabecera)!='[]') @if(($v->cabecera->fecha)!=null) {{date("d-m-Y", strtotime($v->cabecera->fecha))}} @endif @endif</td>
                  <td style="text-align: left;">{{$v->id_plan_cuenta}} | {{substr($v->descripcion,0,20)}}<br /> BAN-ND @if(($v->cabecera)!='[]') @if(($v->cabecera->secuencia)!=null) {{$v->cabecera->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($v->saldo)!=null) {{number_format($v->saldo,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($v->abono)!=null) {{number_format($v->abono,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
              </tr>
              @endif
              @endforeach
              @endif
              @if(($value->masivos)!=null && (($value->masivos)!='[]'))
              @foreach($value->masivos as $v)
              @php
              $eg_mas_pago="";
              if(isset($v->comp_egreso)){
              if(isset($v->comp_egreso->bancoa)){
              if(!is_null($v->comp_egreso->bancoa->nombre)){
              $eg_mas_pago = $v->comp_egreso->bancoa->nombre ." / ";
              }
              }
              }
              if(isset($v->comp_egreso)){
              if(isset($v->comp_egreso->tipo_pago)){
              if(!is_null($v->comp_egreso->tipo_pago->nombre)){
              $eg_mas_pago = $eg_mas_pago ."". $v->comp_egreso->tipo_pago->nombre;
              }
              }
              }
              @endphp
              @if(isset($v->comp_egreso))
              @if(isset($v->comp_egreso->estado) && $v->comp_egreso->estado==1)
              @php
              $habert+= $v->abono;
              @endphp
              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $v->comp_egreso->fecha_comprobante){
                  $fultima = $v->comp_egreso->fecha_comprobante;
                  }
                  @endphp
                  @endif
                  <td style="text-align: left;">
                    @if(($v->comp_egreso)!='[]')
                    @if(($v->comp_egreso->descripcion)!=null)
                    <p style="margin-bottom: 0px!important;" class="s1"> {{isset($value->proveedorf->nombrecomercial)?$value->proveedorf->nombrecomercial:''}} # {{$v->comp_egreso->descripcion}} Ref: {{$value->numero}} #Egreso Masivo: {{$v->id}} -- Asiento: {{$v->comp_egreso->id_asiento_cabecera}}</p>
                    <label style="font-size: 11px;" for="" class="label label-success"> #Egreso Masivo: {{$v->id}} @if($eg_mas_pago!="") -- Método de pago: {{$eg_mas_pago}} @endif</label>
                    @endif
                    @endif
                  </td>
                  <td>@if(($v->comp_egreso)!='[]') @if(($v->comp_egreso->fecha_comprobante)!=null) {{date("d-m-Y", strtotime($v->comp_egreso->fecha_comprobante))}} @endif @endif</td>
                  <td style="text-align: left;">{{$v->id_plan_cuenta}} | {{substr($v->descripcion,0,20)}}<br /> ACR-M @if(($v->comp_egreso)!='[]') @if(($v->comp_egreso->secuencia)!=null) {{$v->comp_egreso->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($v->saldo_base)!=null) {{number_format($v->saldo_base,2,'.',',')}} @else 0.00 @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($v->abono)!=null) {{number_format($v->abono,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
              </tr>
              @endif
              @endif
              @endforeach
              @endif
              @if(($value->cruce)!=null && (($value->cruce)!='[]'))
              @foreach($value->cruce as $x)


              @if(isset($x->cabecera->estado) && ($x->cabecera->estado)==1)
              @php
              //aqui falta

              $habert+= $x->total;
              @endphp
              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $x->cabecera->fecha_pago){
                  $fultima = $x->cabecera->fecha_pago;
                  }
                  @endphp
                  @endif
                  <td style="text-align: left;">@if(($x->cabecera)!='[]') @if(($x->cabecera->detalle)!=null) <p class="s1"> {{$value->proveedorf->nombrecomercial}} # {{$x->cabecera->detalle}} Ref: {{$value->numero}} #cruce: {{$x->id_comprobante}} -- Asiento: {{$x->cabecera->id_asiento_cabecera}}</p> @endif @endif</td>
                  <td>@if(($x->cabecera)!='[]') @if(($x->cabecera->fecha_pago)!=null) {{date("d-m-Y", strtotime($x->cabecera->fecha_pago))}} @endif @endif</td>
                  <td style="text-align: left;">ACR-CR-AF @if(($x->cabecera)!='[]') @if(($x->cabecera->secuencia)!=null) {{$x->cabecera->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($x->total)!=null) {{number_format($x->total,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($x->total)!=null) {{number_format($x->total,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>

              </tr>
              @endif
              @endforeach
              @endif
              @if(($value->retenciones)!=null && (($value->retenciones)!='[]'))
              @foreach($value->retenciones as $xrete)
              @php //dd($xrete); @endphp
              @php $total=0; @endphp
              @if(isset($xrete->estado) && ($xrete->estado)==1)

              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $xrete->fecha){
                  $fultima = $xrete->fecha;
                  }
                  @endphp
                  @endif
                  @if(Auth::user()->id=='0921605895')
                  <?php
                  /*
                  if (isset($xrete->secuencia))
                    if ($value->id = 18028)
                      dd($xrete->secuencia);
*/
                  ?>
                  <td style="text-align: left;">
                    <p class="s1"><?= isset($xrete->secuencia) ? $value->proveedorf->nombrecomercial . ' # ' . $xrete->descripcion . ' Ref: ' . $value->secuencia_f . ' # retencion: ' . $xrete->id . ' -- Asiento: ' . $xrete->id_asiento_cabecera  : '' ?> </p>
                  </td>
                  <td><?= date("d-m-Y", strtotime($xrete->fecha)); ?></td>
                  <td style="text-align: left;">ACR-RE <?= isset($xrete->secuencia) ? $xrete->secuencia : ''; ?></td>
                  <?php
                  $total = ($xrete->valor_fuente) + ($xrete->valor_iva);
                  ?>
                  <td style="text-align:right;"><?= number_format($total, 2, '.', ''); ?></td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">0.00</td>

                  @else

                  <td style="text-align: left;">@if(isset($xrete->secuencia)) @if(($xrete->secuencia)!=null) <p class="s1"> {{$value->proveedorf->nombrecomercial}} # {{$xrete->descripcion}} Ref: {{$value->secuencia_f}} # retencion: {{$xrete->id}} -- Asiento: {{$xrete->id_asiento_cabecera}} </p> @endif @endif</td>
                  <td>@if(isset($xrete->fecha)) {{date("d-m-Y", strtotime($xrete->fecha))}} @endif</td>
                  <td style="text-align: left;">ACR-RE @if(isset($xrete->secuencia)) @if(($xrete->secuencia)!=null) {{$xrete->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($xrete)!='[]') @php $total= ($xrete->valor_fuente)+($xrete->valor_iva); @endphp {{number_format($total,2,'.','')}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($xrete)!='[]') {{number_format($total,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>

                  @endif

                  @php
                  $habert+= $total;
                  @endphp
              </tr>
              @endif
              @endforeach

              @endif
              @php
              $fechas =date("Y-m-d");
              $fechas = date("Y-m-d", strtotime("09-10-2021"));
              //dd($fechas);
              @endphp

              @if(($value->debitoacreedor)!=null && (($value->debitoacreedor)!='[]'))

              @if(!is_null($value->debitoacreedor))
              @foreach($value->debitoacreedor as $xs)

              @if(isset($xs->cabecera->estado) && $xs->cabecera->estado==1)
              @php
              //aqui falta
              $habert+= $xs->cabecera->valor_contable;
              @endphp
              @if(!is_null($xs->cabecera))



              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $xs->cabecera->fecha){
                  $fultima = $xs->cabecera->fecha;
                  }
                  @endphp
                  @endif
                  @php
                  if(($xs->cabecera)!='[]'){
                  if(!is_null($xs->cabecera->fecha)){
                  $fechas = $value->fecha;
                  }else{
                  $fechas = $xs->cabecera->fecha;
                  }
                  }
                  //dd($xs->id_debito_acreedores);



                  // dd($xs);

                  @endphp

                  <td style="text-align: left;">
                    @if(($xs->cabecera)!='[]')
                    <p class="s1"> {{$value->proveedorf->nombrecomercial}} #
                      {{$xs->cabecera->concepto}} Ref: {{$value->secuencia_f}} # Debito {{$xs->id_debito_acreedores}}
                      -- Asiento: {{$xs->id_asiento_cabecera}}
                    </p>
                    @endif
                  </td>
                  <td>{{$fechas}}</td>
                  <td style="text-align: left;">ACR-DB @if(($xs->cabecera)!='[]') @if(($xs->cabecera->secuencia)!=null) {{$xs->cabecera->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($xs->cabecera)!='[]') {{$xs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($xs->cabecera)!='[]') {{number_format($xs->total,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>

              </tr>

              @endif
              @endif
              @endforeach
              @endif
              @endif
              @if(($value->credito_acreedor)!=null && (($value->credito_acreedor)!='[]'))
              @foreach($value->credito_acreedor as $credito)
              @if(isset($credito->estado) && ($credito->estado)==1)
              @php
              //aqui falta
              $habert+= $credito->subtotal;
              @endphp
              <tr>
                @if($value->valor_contable == 0)
                @php
                //aqui falta
                if($fultima < $credito->fecha){
                  $fultima = $credito->fecha;
                  }
                  @endphp
                  @endif
                  <td style="text-align: left;">@if(($credito)!='[]') @if(($credito->secuencia)!=null) <p class="s1"> {{$value->proveedorf->nombrecomercial}} # {{$credito->concepto}} Ref: {{$value->secuencia_f}} #credito: {{$credito->id}} -- Asiento: {{$credito->id_asiento_cabecera}} </p> @endif @endif</td>
                  <td>@if(($credito)!='[]') {{date("d-m-Y", strtotime($credito->fecha))}} @endif</td>
                  <td style="text-align: left;">ACR-NC @if(($credito)!='[]') @if(($credito->secuencia)!=null) {{$credito->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($credito)!='[]') {{$credito->valor_contable}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($credito)!='[]') {{number_format($credito->valor_contable,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>

              </tr>
              @endif
              @endforeach
              @endif
              @if(($value->cruce_cuentas)!=null && (($value->cruce_cuentas)!='[]'))
              @foreach($value->cruce_cuentas as $cruce_cuentas)
              @if(isset($cruce_cuentas->estado) && ($cruce_cuentas->estado)==1)
              @php
              $habert+= $cruce_cuentas->total;
              @endphp
              <tr>
                @if($value->valor_contable == 0)
                @php
                if($fultima < $cruce_cuentas->fecha){
                  $fultima = $cruce_cuentas->fecha;
                  }
                  @endphp
                  @endif
                  <td style="text-align: left;">@if(($cruce_cuentas)!='[]') @if(($cruce_cuentas->secuencia)!=null) <p class="s1"> {{$value->proveedorf->nombrecomercial}} # {{$cruce_cuentas->concepto}} Ref: {{$value->secuencia_f}} #cruce_cuentas: {{$cruce_cuentas->id}} -- Asiento: {{$cruce_cuentas->id_asiento_cabecera}} </p> @endif @endif</td>
                  <td>@if(($cruce_cuentas)!='[]') {{date("d-m-Y", strtotime($cruce_cuentas->fecha))}} @endif</td>
                  <td style="text-align: left;">CRUCE-CUENTAS @if(($cruce_cuentas)!='[]') @if(($cruce_cuentas->secuencia)!=null) {{$cruce_cuentas->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($cruce_cuentas)!='[]') {{$cruce_cuentas->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($cruce_cuentas)!='[]') {{number_format($cruce_cuentas->total,2,'.',',')}} @endif</td>
                  <td style="text-align:right;">0.00</td>

              </tr>
              @endif
              @endforeach
              @endif
              @if($value->valor_contable == 0)
              @php
              $fec=new DateTime($value->f_autorizacion);
              $fec2=new DateTime($fultima);
              $fultima = date('Y-m-d');
              $diff = $fec->diff($fec2);
              $daysf=$diff->format("%r%a");
              @endphp
              <script type="text/javascript">
                $('#co{{$value->id}}').text('{{$daysf}} Dias');
                $('#co{{$value->id}}').addClass('label label-info');
              </script>
              @endif

              @endif
              @endif
              @endforeach

            </tbody>
            <tfoot>
              <tr>
                <td><label>{{trans('contableM.total')}}</label></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:right; font-weight: bold;">{{number_format($debet,2,'.',',')}}</td>
                <td style="text-align:right; font-weight: bold;">{{number_format($habert,2,'.',',')}}</td>
                <td style="text-align:right; font-weight: bold;">{{number_format($valor_contable,2,'.',',')}}</td>
              </tr>
            </tfoot>
          </table>

          <!--<div class="infinite-scroll">

                    <table id=" " class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                      <thead>
                         <tr>
                            <th width="30%" ></th>
                            <th width="10%" ></th>
                            <th width="20%"></th>
                            <th width="10%"></th>
                            <th width="10%"></th>
                            <th width="10%"></th>
                            <th width="10%"></th>
                          </tr>
                      </thead>

                    </table>
                  </div>
                  <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                      <thead>
                        <tr >
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="20%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" style="text-align:center" tabindex="0" aria-controls="example2" colspan="1"></th>
                        </tr>
                      </thead>
                                          
                  </table> 
                  -->

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>


  </div>
  @endif

  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });

  });
  $('#example2').DataTable({
    'paging': false,
    'dom': 'bfrtip',
    'lengthChange': false,
    'searching': true,
    'scrollX': true,
    'scrollY': 450,
    'ordering': false,
    'responsive': true,
    'info': false,
    'autoWidth': true,

  });
  $(function() {
    $('.infinite-scroll').jscroll({
      autoTrigger: true,
      loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
      padding: 0,
      nextSelector: '.pagination li.active + li a',
      contentSelector: 'div.infinite-scroll',
      callback: function() {

        $('div.paginationLinks').remove();

      }
    });
  });
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

  $('#cuenta').on('select2:select', function(e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit();
  });


  $(document).ready(function() {

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });

  });

  function excel() {
    $("#print_reporte_master").submit();
  }
  $("#nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 2,
  });
  $('#fact_contable_check').on('ifChanged', function(event) {
    //aqui funciona si cambio el input time
    if ($(this).prop("checked")) {
      $("#esfac_contable").val(1);
      $("#es_fact_dos").val(1);
    } else {
      $("#esfac_contable").val(0);
    }

  });

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#id_proveedor2').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#id_proveedor2').val("");
          $('#direccion_proveedor').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
</script>
@endsection