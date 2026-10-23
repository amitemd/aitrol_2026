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
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Clientes</a></li>
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
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('deudasvspagos.cliente') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">*Fecha desde:</label>
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
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
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
          <label class="texto" for="nombre_proveedor">Cliente: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select style="width: 100%;" class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor">

            @if(isset($id_proveedor))
            @php
            $clientex= DB::table('ct_clientes')->where('identificacion',$id_proveedor)->first();
            @endphp
            @if(!is_null($clientex))
            <option selected="selected" value="{{$clientex->identificacion}}">{{$clientex->nombre}}</option>
            @endif
            @endif
          </select>

        </div>


        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;Imprimir&nbsp;</span>
          </button> -->
          <button type="submit" class="btn btn-primary btn-gray" id="boton_buscar">
            <span class="glyphicon glyphicon-search " aria-hidden="true"></span> Buscar
          </button>
          <!--<button type="button" class="btn btn-primary btn-gray" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>-->
          <button type="button" class="btn btn-primary btn-gray" id="btn_corte">
            <span class="fa fa-scissors" aria-hidden="true"></span> Corte a fecha

          </button>
        </div>
        <div class="form-group col-md-6 col-xs-9" style="text-align: right;"></div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('deudasvspagosc.excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      <input type="hidden" name="es_fact_dos" id="es_fact_dos">
    </form>

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
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">Deudas vs Pagos</h4>
            <h4 style="text-align: center;">{{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
              <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
            </dl>
          </div>
        </div> --}}

        <div class="box-body">
          <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
              {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
            </dl>
          </div>
          <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">DEUDAS VS COBROS</h4>
            @if(($fecha_desde!=null))
            <h5 style="text-align: center;">Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
            @else
            <h5 style="text-align: center;">Al - {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
            @endif
          </div>
          <div class="col-md-4">
          </div>
          <div class="col-md-12">
            <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th width="30%" style="text-align:center;" tabindex="0" aria-controls="example2">Detalle</th>
                  <th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2">Fecha</th>
                  <th width="10%" style="text-align:center;" tabindex="0" aria-controls="example2">Asiento</th>
                  <th width="20%" style="text-align:center;" tabindex="0" aria-controls="example2"> </th>
                  <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Valor</th>
                  <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Debe</th>
                  <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Haber</th>
                  <th width="10%" style="text-align:right" tabindex="0" aria-controls="example2">Saldo</th>
                </tr>
              </thead>
              <tbody>
                @php
                $debe=0;
                $haber=0;
                $totales=0;
                @endphp
                @foreach($deudas as $value)
                @if(isset($value))
                @if($value->estado!=0)
                @php
                $debe+=$value->total_final;
                $totales+=$value->valor_contable;
                @endphp
                <?php
                $cuenta = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera', $value->id_asiento)->where('id_plan_cuenta', 'like', '1.%')->first(['id_plan_cuenta', 'descripcion']);
                ?>
                <tr>
                  <td style="color: black; font-weight: bold; text-align: left; font-size: 15px;">@if(($value)!=null) @if(($value->numero)!=null && (($value->numero)!=null)) Fact: # {{$value->nro_comprobante}} Cli: {{$value->cliente->nombre}} Ref: {{$value->id}} @endif @endif</td>
                  <td style="color: black; font-weight: bold;">@if(($value->fecha)!=null) {{date("d-m-Y", strtotime($value->fecha))}} @endif</td>
                  <td style="color: black; font-weight: bold;text-align:center;"><a href="{{url('/')}}/contable/contabilidad/libro/edit/{{$value->id_asiento}}" target="_blank" />@if(($value->id_asiento)!=null) {{$value->id_asiento}} @endif</a></td>
                  <td style="color: black; font-weight: bold; text-align: left;"> @if ($cuenta!=''){{$cuenta->id_plan_cuenta}} | {{$cuenta->descripcion}} <br /> @endif @if(($value->tipo)!=null) <a href="{{url('/')}}/contable/ventas/editar/{{$value->id}}" target="_blank"> {{$value->tipo}} @endif @if(($value->numero)!=null) {{$value->numero}} @endif</a></td>
                  <td style="text-align:right; color: black; font-weight: bold;">@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                  <td style="text-align:right; color: black; font-weight: bold;">@if(($value->total_final)!=null) {{$value->total_final}} @endif</td>
                  <td style="text-align:right; color: black; font-weight: bold;">0.00</td>
                  <td style="text-align:right; color: black; font-weight: bold;"> {{number_format($value->valor_contable,4,'.','')}} </td>
                </tr>

                @if(isset($value->comp_ingreso))
                @foreach($value->comp_ingreso as $v)
                @php
                // dd($v);
                $metod_pago = "";
                $id_comp_ingreso ="";
                // $pagos = $v->ingreso->pago_ingresos->where('id_comprobante',$v->id_comprobante)->first()->tipo_pago->nombre;
                $nombre_pago = array();
                if(isset($v->ingreso)){
                if(isset($v->ingreso->pago_ingresos)){
                $nombre_pago = $v->ingreso->pago_ingresos->where('id_comprobante',$v->id_comprobante);
                //dd($nombre_pago);
                $contadorpago= count($nombre_pago);
                if(!is_null($nombre_pago)){
                foreach($nombre_pago as $nombre_pago){
                if(isset($nombre_pago->tipo_pago)) {

                $metod_pago .= $nombre_pago->tipo_pago->nombre." / ";
                }
                if(isset($nombre_pago->banco)){
                $metod_pago = $metod_pago ." / " . $nombre_pago->banco->nombre;
                }
                }
                }
                }
                }
                //$id_comp_ingreso = "";
                if(isset($v->ingreso)){
                if(!is_null($v->ingreso->id)){
                $id_comp_ingreso = "# INGRESO : " . $v->ingreso->id;
                }
                }
                @endphp
                @if($v->ingreso->estado==1)
                @php
                $haber+= $v->total;
                @endphp
                <?php
                $cuentaCompIngreso = Sis_medico\Ct_Comprobante_Ingreso::join('ct_asientos_detalle as ad', 'ct_comprobante_ingreso.id_asiento_cabecera', 'ad.id_asiento_cabecera')
                  ->where('ct_comprobante_ingreso.id', $v->id_comprobante)
                  ->where('ad.id_plan_cuenta', 'like', '1.%')
                  ->where('ad.descripcion', 'like', '%caja%')
                  ->first(['id_plan_cuenta', 'descripcion', 'ad.id_asiento_cabecera']);
                ?>
                <tr>
                  <td style="text-align: left;">@if(($v->ingreso)!='[]') @if(($v->ingreso->observaciones)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$v->ingreso->observaciones}} Ref: {{$value->numero}} <label for="" class="label label-success" style="font-size: 11px;"> {{$id_comp_ingreso}} {{$metod_pago}}</label> </p> @endif @endif</td>
                  <td>@if(($v->ingreso)!='[]') @if(($v->ingreso->fecha)!=null) {{date("d-m-Y", strtotime($v->ingreso->fecha))}} @endif @endif</td>
                  <td style="color: black; font-weight: bold;text-align:center;">
                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/{{isset($cuentaCompIngreso->id_asiento_cabecera)?$cuentaCompIngreso->id_asiento_cabecera:''}}" target="_blank" />{{isset($cuentaCompIngreso->id_asiento_cabecera)?$cuentaCompIngreso->id_asiento_cabecera:''}} </a>
                  </td>
                  <td style="color: black; font-weight: bold; text-align: left;">
                    @if ($cuentaCompIngreso!=''){{$cuentaCompIngreso->id_plan_cuenta}} | {{$cuentaCompIngreso->descripcion}} <br />
                    @endif
                    <a href="{{url('/')}}/contable/cliente/comprobante/ingresos/{{$v->ingreso->id}}" target="_blank"> <b>
                        CLI-IN @if(($v->ingreso)!='[]') @if(($v->ingreso->id)!=null) {{$v->ingreso->id}} @endif @endif
                      </b>
                    </a>
                  </td>
                  <td style="text-align:right;">@if(($v->total)!=null) {{$v->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($v->total)!=null) {{$v->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                  <td style="text-align: left;">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td style="text-align: left;">&nbsp;</td>
                  <td style="text-align:right;">&nbsp;</td>
                  <td style="text-align:right;">&nbsp;</td>
                  <td style="text-align:right;">&nbsp;</td>
                  <td style="text-align:right;">&nbsp;</td>
                </tr>
                @endif
                @php
                //Notas de credito parcial
                $parcial = Sis_medico\Ct_Nota_Credito_Clientes::where('id_factura', $value->id)->where('estado', 1)->get();
                @endphp
                @if(count($parcial)>0)
                @foreach($parcial as $par)
                @php
                $haber+= $par->total_credito;
                $cuentaNotCredito = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera', $par->id_asiento_cabecera)->where('id_plan_cuenta', 'like', '1.%')->first(['id_plan_cuenta', 'descripcion']);
                @endphp
                <tr>
                  <td style="text-align: left;">
                    <p class="s1"> {{$par->cliente->nombre}} # {{$par->concepto}} Ref: {{$par->nro_comprobante}} </p>
                  </td>
                  <td>{{date("d-m-Y", strtotime($par->fecha))}} </td>
                  <td style="color: black; font-weight: bold;text-align:center;">

                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/{{$par->id_asiento_cabecera}}" target="_blank" />
                    @if(($par->id_asiento_cabecera)!=null) {{$par->id_asiento_cabecera}}
                    @endif
                    </a>
                  </td>
                  <td style="text-align: left;font-weight:bold">
                    @if(!is_null($cuentaNotCredito)) {{$cuentaNotCredito->id_plan_cuenta}} | {{$cuentaNotCredito->descripcion}} @endif<br />
                    <a href="{{url('/')}}/contable/cliente/nota_credito/editar2/{{$par->id}}" target="_blank">
                      {{$par->tipo}} {{$par->secuencia}}
                    </a>
                  </td>
                  <td style="text-align:right;"> {{$par->total_credito}}</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">{{$par->total_credito}}</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endforeach
                @endif
                @if(($value->cruce_cuentas)!=null && (($value->cruce_cuentas)!='[]'))
                @foreach($value->cruce_cuentas as $vs)
                @if($vs->estado==1)
                @php
                $haber+= $vs->total;
                @endphp
                <tr>
                  <td style="text-align: left;">@if(($vs)!='[]') @if(($vs->detalle)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$vs->detalle}} Ref: {{$value->numero}} </p> @endif @endif</td>
                  <td>@if(($vs)!='[]') @if(($vs->fecha)!=null) {{date("d-m-Y", strtotime($vs->fecha))}} @endif @endif</td>
                  <td style="color: black; font-weight: bold;text-align:center;">
                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/{{$vs->id_asiento_cabecera}}" target="_blank" />
                    @if(($vs->id_asiento_cabecera)!=null) {{$vs->id_asiento_cabecera}} @endif
                    </a>
                  </td>
                  <td style="text-align: left;">CRUCE-CUENTAS @if(($vs)!='[]') @if(($vs->secuencia)!=null) {{$vs->secuencia}} @endif @endif</td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>

                </tr>
                @endif
                @endforeach
                @endif
                @if(($value->cruce)!=null && (($value->cruce)!='[]'))
                @foreach($value->cruce as $vs)
                @if($vs->cabecera->estado==1)
                @php
                $haber+= $vs->total;
                $cuentaNotCruceEdit = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera', $vs->cabecera->id_asiento_cabecera)->where('id_plan_cuenta', 'like', '1.%')->first(['id_plan_cuenta', 'descripcion']);
                @endphp
                <tr>
                  <td style="text-align: left;">@if(($vs->cabecera)!='[]') @if(($vs->cabecera->detalle)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$vs->cabecera->detalle}} Ref: {{$value->numero}} #ID: {{$vs->cabecera->id}}</p> @endif @endif</td>
                  <td>@if(($vs->cabecera)!='[]') @if(($vs->cabecera->fecha_pago)!=null) {{date("d-m-Y", strtotime($vs->cabecera->fecha_pago))}} @endif @endif</td>
                  <td style="color: black; font-weight: bold;text-align:center;">
                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/{{$vs->cabecera->id_asiento_cabecera}}" target="_blank" />
                    @if(($vs->cabecera->id_asiento_cabecera)!=null) {{$vs->cabecera->id_asiento_cabecera}}
                    @endif
                    </a>
                  </td>
                  <td style="text-align: left;">
                    @if(!is_null($cuentaNotCruceEdit)) <strong>{{$cuentaNotCruceEdit->id_plan_cuenta}} | {{$cuentaNotCruceEdit->descripcion}}</strong> @endif<br />
                    <a href="{{url('/')}}/contable/cliente/cruce_valores/edit/{{$vs->id_comprobante}}" target="_blank"> <b>
                        CLI-BAN
                        @if(($vs->cabecera)!='[]')
                        @if(($vs->cabecera->secuencia)!=null)
                        {{$vs->cabecera->secuencia}}
                        @endif
                        @endif
                      </b>
                    </a>
                  </td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endif
                @endforeach
                @else
                @endif
                @if(($value->chequepost)!=null && (($value->chequepost)!='[]'))
                @foreach($value->chequepost as $vs)
                @if($vs->cabecera->estado==1)
                @php
                $haber+= $vs->total;
                @endphp
                <tr>
                  <td style="text-align: left;">@if(($vs->cabecera)!='[]') @if(($vs->cabecera->observaciones)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$vs->cabecera->observaciones}} Ref: {{$value->numero}}</p> @endif @endif</td>
                  <td>@if(($vs->cabecera)!='[]') @if(($vs->cabecera->fecha)!=null) {{date("d-m-Y", strtotime($vs->cabecera->fecha))}} @endif @endif</td>
                  <td>&nbsp;</td>
                  <td style="text-align: left;">CLI-CH @if(($vs->cabecera)!='[]') @if(($vs->cabecera->id)!=null) {{$vs->cabecera->id}} @endif @endif</td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($vs->total)!=null) {{$vs->total}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endif
                @endforeach
                @else
                @endif
                @if(($value->credito)!=null && (($value->credito)!='[]'))
                @foreach($value->credito as $vs)
                @if($vs->cabecera->estado==1)
                @php
                $haber+= $vs->cabecera->total_credito;
                @endphp
                <tr>
                  <td style="text-align: left;">@if(($vs->cabecera)!='[]') @if(($vs->cabecera->concepto)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$vs->cabecera->concepto}} Ref: {{$value->numero}}</p> @endif @endif</td>
                  <td>@if(($vs->cabecera)!='[]') @if(($vs->cabecera->fecha)!=null) {{date("d-m-Y", strtotime($vs->cabecera->fecha))}} @endif @endif</td>
                  <td>&nbsp;</td>
                  <td style="text-align: left;">CR-CLIENTE @if(($vs->cabecera)!='[]') @if(($vs->cabecera->id)!=null) {{$vs->cabecera->id}} @endif @endif</td>
                  <td style="text-align:right;">@if(($vs->cabecera->total_credito)!=null) {{$vs->cabecera->total_credito}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($vs->cabecera->total_credito)!=null) {{$vs->cabecera->total_credito}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endif
                @endforeach
                @else
                @endif
                @if(($value->retenciones)!=null && (($value->retenciones)!='[]'))
                @foreach($value->retenciones_2 as $rete_2)
                @if($rete_2->estado==1)
                @php
                $haber+= $rete_2->valor_fuente + $rete_2->valor_iva;
                $cuentaClienteRete = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera', $rete_2->id_asiento_cabecera)->where('id_plan_cuenta', 'like', '1.%')->first(['id_plan_cuenta', 'descripcion']);
                @endphp
                <tr>
                  <td style="text-align: left;">@if(($rete_2)!='[]') @if(($rete_2->secuencia)!=null) <p class="s1"> {{$value->cliente->nombre}} # {{$rete_2->descripcion}} Ref: {{$value->nro_comprobante}} </p> @endif @endif</td>
                  <td>@if(($rete_2)!='[]') {{date("d-m-Y", strtotime($rete_2->fecha))}} @endif</td>
                  <td style="color: black; font-weight: bold;text-align:center;">
                    <br>
                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/{{$rete_2->id_asiento_cabecera}}" target="_blank" />
                    @if(($rete_2->id_asiento_cabecera)!=null) {{$rete_2->id_asiento_cabecera}}
                    @endif
                    </a>
                  </td>
                  <td style="text-align: left;">
                    @if(!is_null($cuentaClienteRete)) <b> {{$cuentaClienteRete->id_plan_cuenta}} | {{$cuentaClienteRete ->descripcion}} </b> @endif<br />
                    <a href="{{url('/')}}/contable/clientes/retenciones/edit/{{$rete_2->id}}" target="_blank"> <b>
                        CLI-RE
                        @if(($rete_2)!='[]')
                        @if(($rete_2->id)!=null)
                        {{$rete_2->id}}
                        @endif
                        @endif
                    </a>
                  </td>
                  <td style="text-align:right;">@if(($rete_2)!='[]') @php $total= ($rete_2->valor_fuente)+($rete_2->valor_iva); @endphp {{number_format($total,4)}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                  <td style="text-align:right;">@if(($rete_2)!='[]') {{number_format($total,4)}} @endif</td>
                  <td style="text-align:right;">0.00</td>
                </tr>
                @endif
                @endforeach
                @endif
                @endif
                @endif

                <?php
                /*echo '<pre>';
                print_r($value);
                exit;

                $debito = Sis_medico\Ct_Nota_Debito_Cliente::where('id_factura', $value->id)->where('estado', '1')->first();
                if ($debito != '') {
                  echo '<pre>';
                  print_r($debito);
                  exit;
                }*/
                ?>

                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td><label>TOTAL :</label></td>
                  <td>&nbsp;</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td style="text-align:right; font-weight: bold;">@if(isset($debe)){{number_format($debe,4,'.','')}}@endif</td>
                  <td style="text-align:right; font-weight: bold;">@if(isset($haber)){{number_format($haber,4,'.','')}}@endif</td>
                  <td style="text-align:right; font-weight: bold;"> @if(isset($totales)){{number_format($totales,4,'.','')}}@endif</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!--                   <div class="infinite-scrolls">
                    @if(count($deudas)>0)
                      <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
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
                    @endif
                  </div>
                  <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                      <thead>
                        <tr >
                          <th width="10%"  rowspan="1" ></th>
                          <th width="10%" tabindex="0" aria-controls="example2"tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="20%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="10%" tabindex="0" aria-controls="example2" colspan="1"></th>
                        </tr>
                      </thead>

                      <tbody>

                      </tbody>
                  </table> -->
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
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    /*$('.select2_cuentas').select2({
      tags: false
    }); */
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });
    $('#btn_corte').click(function() {
      if ($('#fecha_desde').val() == '' || $('#fecha_hasta').val() == '') {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
        });
      } else {
        window.open('<?= url('/'); ?>/contable/cliente/deudasvspagos/cortefecha/' + $('#fecha_desde').val().replaceAll('/', '-') + '/' + $('#fecha_hasta').val().replaceAll('/', '-'), '_self');
      }
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });
    $('.select2_cuentas').select2({
      placeholder: "Escriba el nombre del cliente",
      allowClear: true,
      ajax: {
        url: '{{route("venta.clientesearch")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'

          return {
            results: data
          };
        }
      }
    });

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
  $('#example2').DataTable({
    'paging': false,
    dom: 'lBrtip',
    'lengthChange': false,
    'searching': true,
    "scrollX": true,
    "scrollY": 450,
    'ordering': false,
    responsive: true,
    'info': false,
    'autoWidth': true,
    buttons: [{
        extend: 'copyHtml5',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        title: 'INFORME DEUDAS VS COBROS'
      },
      {
        extend: 'csvHtml5',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        pageSize: 'TABLOID',
        footer: true,
        title: 'INFORME DEUDAS VS COBROS',
        customize: function(doc) {
          doc.pageMargins = [30, 30, 30, 30];
          doc.styles.title = {
            color: 'black',
            fontSize: '16',
            alignment: 'center'
          }
        }
      }
    ],
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