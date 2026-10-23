@extends('contable.nota_credito_cliente.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<style>
  .nc-page .box {
    border: 0;
    border-radius: 5px;
    box-shadow: 0 2px 12px rgba(25, 48, 66, .10);
  }

  .nc-page .box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    min-height: 78px;
    padding: 16px 22px;
    border-bottom: 1px solid #e2e8ec;
  }

  .nc-page .box-header > .col-md-7,
  .nc-page .box-header > .col-md-5 {
    float: none;
    width: auto;
    padding: 0;
  }

  .nc-page .box-header h5 {
    margin: 0;
    color: #283b4a;
    font-size: 17px;
  }

  .nc-page .box-header > .col-md-5 {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
  }

  .nc-page .box-header > .col-md-5 > div {
    display: contents;
  }

  .nc-page .box-header .btn {
    min-height: 37px;
    margin: 0;
    padding: 8px 14px;
    border: 0;
    border-radius: 5px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(0, 0, 0, .14);
    transition: transform .2s ease, box-shadow .2s ease;
  }

  .nc-page .box-header .btn:hover,
  .nc-page .box-header .btn:focus {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 9px rgba(0, 0, 0, .18);
  }

  .nc-page .box-header .nc-action-old {
    background: #6c7b86;
  }

  .nc-page .box-header .nc-action-standard {
    background: #267fba;
  }

  .nc-page .box-header .nc-action-inventory {
    background: #158a55;
  }

  .nc-page .box-header .btn .fa {
    margin-right: 6px;
  }

  .nc-page .head-title {
    margin: 0;
  }

  .nc-page .cabecera {
    min-height: 33px;
    padding: 7px 14px;
    background: #637887;
  }

  .nc-page .head-title + .box-body {
    padding: 18px 22px;
  }

  .nc-page #buscar_not_cred {
    display: grid;
    grid-template-columns: 75px minmax(180px, 1fr) 55px minmax(180px, 1fr) 75px minmax(220px, 1fr);
    gap: 14px 16px;
    align-items: end;
    width: 100%;
  }

  .nc-page #buscar_not_cred > div {
    float: none;
    width: auto;
    min-width: 0;
    margin: 0;
    padding: 0;
  }

  .nc-page #buscar_not_cred > div:nth-of-type(7) {
    grid-column: 1;
  }

  .nc-page #buscar_not_cred > div:nth-of-type(8) {
    grid-column: 2 / span 4;
  }

  .nc-page #buscar_not_cred > div:nth-of-type(9) {
    grid-column: 6;
  }

  .nc-page #buscar_not_cred label {
    display: flex;
    align-items: center;
    min-height: 38px;
    margin: 0;
    color: #263746;
    font-size: 13px;
    font-weight: 700;
  }

  .nc-page #buscar_not_cred .form-control,
  .nc-page #buscar_not_cred .select2-selection--single {
    width: 100%;
    height: 38px;
    border: 1px solid #c8d5dd;
    border-radius: 5px;
    background: #fff;
    box-shadow: none;
  }

  .nc-page #buscar_not_cred .form-control:focus,
  .nc-page #buscar_not_cred .select2-container--focus .select2-selection--single {
    border-color: #2784c0;
    box-shadow: 0 0 0 2px rgba(39, 132, 192, .13);
  }

  .nc-page #buscar_not_cred .select2-container {
    width: 100% !important;
  }

  .nc-page #buscar_not_cred .select2-selection__rendered {
    padding-left: 12px;
    line-height: 36px;
  }

  .nc-page #buscar_not_cred .select2-selection__arrow {
    height: 36px;
  }

  .nc-page #buscarAsiento {
    width: 100%;
    height: 38px;
    border: 0;
    border-radius: 5px;
    background: #267fba;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(38, 127, 186, .25);
  }

  .nc-page #buscarAsiento:hover,
  .nc-page #buscarAsiento:focus {
    background: #1d6da3;
    color: #fff;
  }

  .nc-page #tablaCredito thead th {
    vertical-align: middle;
    background: #637887;
    color: #fff;
  }

  .nc-page #tablaCredito tbody td {
    vertical-align: middle;
  }

  .nc-page #tablaCredito th:last-child,
  .nc-page #tablaCredito td:last-child {
    min-width: 82px;
    text-align: center;
  }

  .nc-page #tablaCredito td:last-child {
    padding: 8px 6px;
  }

  .nc-page #tablaCredito td:last-child > .btn,
  .nc-page #tablaCredito td:last-child > .dropdown {
    display: table;
    float: none;
    margin: 4px auto;
  }

  .nc-page #tablaCredito td:last-child .btn {
    display: inline-flex;
    float: none;
    align-items: center;
    justify-content: center;
    width: 40px;
    min-width: 40px;
    height: 36px;
    min-height: 36px;
    margin: 2px;
    padding: 0;
    border: 1px solid transparent;
    border-radius: 5px;
    color: #fff;
    box-shadow: 0 2px 4px rgba(24, 45, 61, .14);
    transition: background-color .18s ease, box-shadow .18s ease, transform .18s ease;
  }

  .nc-page #tablaCredito td:last-child .btn:hover,
  .nc-page #tablaCredito td:last-child .btn:focus {
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 7px rgba(24, 45, 61, .20);
  }

  .nc-page #tablaCredito td:last-child > a[title="Visualizar"] {
    background: #2c7fb4;
    border-color: #276f9e;
  }

  .nc-page #tablaCredito td:last-child > a[title="Visualizar"]:hover {
    background: #246b99;
  }

  .nc-page #tablaCredito td:last-child a[href^="javascript:anular"] {
    background: #d9534f;
    border-color: #c84541;
  }

  .nc-page #tablaCredito td:last-child a[href^="javascript:anular"]:hover {
    background: #c7433f;
  }

  .nc-page #tablaCredito td:last-child .dropdown-toggle {
    background: #26934f;
    border-color: #218243;
  }

  .nc-page #tablaCredito td:last-child .dropdown-toggle:hover {
    background: #207d43;
  }

  .nc-page #tablaCredito td:last-child .dropdown-menu {
    right: 50%;
    left: auto;
    min-width: 146px;
    padding: 7px;
    border: 1px solid #d9e1e6;
    border-radius: 6px;
    text-align: center;
    transform: translateX(50%);
    box-shadow: 0 8px 20px rgba(24, 45, 61, .18);
  }

  .nc-page #tablaCredito td:last-child .dropdown-menu .btn {
    margin: 2px;
  }

  .nc-page #tablaCredito td:last-child .dropdown-menu .btn-success {
    background: #2c7fb4;
    border-color: #276f9e;
  }

  .nc-page #tablaCredito td:last-child .dropdown-menu .btn-danger {
    background: #d9534f;
    border-color: #c84541;
  }

  .nc-page #tablaCredito td:last-child .btn.col-md-6,
  .nc-page #tablaCredito td:last-child .btn.col-xs-6 {
    width: auto;
    min-width: 92px;
    padding: 0 9px;
    white-space: nowrap;
  }

  .nc-page #tablaCredito td:last-child a.btn[title="Descargar RIDE"] {
    background: #d9534f !important;
    border-color: #c84541 !important;
    color: #fff !important;
  }

  .nc-page #tablaCredito td:last-child a.btn[title="Descargar RIDE"]:hover,
  .nc-page #tablaCredito td:last-child a.btn[title="Descargar RIDE"]:focus {
    background: #c7433f !important;
    border-color: #b83b37 !important;
    color: #fff !important;
  }

  .nc-page #tablaCredito td:last-child a.btn.nc-log-button {
    background: #586f7c !important;
    border-color: #4b606b !important;
    color: #fff !important;
  }

  .nc-page #tablaCredito td:last-child a.btn.nc-log-button:hover,
  .nc-page #tablaCredito td:last-child a.btn.nc-log-button:focus {
    background: #465b66 !important;
    border-color: #3d5059 !important;
    color: #fff !important;
  }

  .nc-page #tablaCredito td:last-child .fa,
  .nc-page #tablaCredito td:last-child .glyphicon {
    margin: 0;
    font-size: 13px;
  }

  #log_factura {
    z-index: 1060;
  }

  #log_factura .modal-dialog {
    width: 92%;
    max-width: 900px;
    margin: 40px auto;
  }

  #log_factura .modal-content {
    min-height: 130px;
    overflow: hidden;
    border: 0;
    border-radius: 7px;
    box-shadow: 0 14px 40px rgba(0, 0, 0, .28);
  }

  .nc-log-loading,
  .nc-log-error {
    padding: 45px 25px;
    color: #526774;
    text-align: center;
  }

  .nc-log-loading .fa {
    margin-right: 7px;
    color: #267fba;
  }

  .nc-log-error {
    color: #b43c3c;
  }

  @media (max-width: 1100px) {
    .nc-page #buscar_not_cred {
      grid-template-columns: 75px 1fr 65px 1fr;
    }

    .nc-page #buscar_not_cred > div:nth-of-type(5) { grid-column: 1; }
    .nc-page #buscar_not_cred > div:nth-of-type(6) { grid-column: 2 / span 3; }
    .nc-page #buscar_not_cred > div:nth-of-type(7) { grid-column: 1; }
    .nc-page #buscar_not_cred > div:nth-of-type(8) { grid-column: 2 / span 2; }
    .nc-page #buscar_not_cred > div:nth-of-type(9) { grid-column: 4; }
  }

  @media (max-width: 767px) {
    .nc-page .box-header {
      align-items: flex-start;
      flex-direction: column;
    }

    .nc-page .box-header > .col-md-5,
    .nc-page .box-header .btn {
      width: 100%;
    }

    .nc-page #buscar_not_cred {
      display: block;
    }

    .nc-page #buscar_not_cred > div {
      margin-bottom: 8px;
    }

    .nc-page #buscar_not_cred label {
      min-height: auto;
      margin-top: 6px;
    }
  }
</style>

<script type="text/javascript">
  $(function() {
    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });
</script>
<div class="content nc-page">
  <div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <!--<li class="breadcrumb-item"><a href="#">Credito Cliente</a></li>-->
      <li class="breadcrumb-item active" aria-current="page">Nota Crédito Cliente </li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-7">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h5><b>NOTAS CRÉDITO CLIENTES</b></h5>
      </div>
      <div class="col-md-5 text-right">
        <!--<button onclick="location.href='{{route('nota_credito_cliente.create')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
              </button>-->
        <div style="">
          <button type="button" onclick="location.href='{{route('nota_credito_cliente.create')}}'" class="btn nc-action-old">
            <i class="fa fa-history" aria-hidden="true"></i>Agregar Nota de Cr&eacute;dito antiguo
          </button>
        </div>
        <button type="button" onclick="location.href='{{route('nota_credito_cliente.create2')}}'" class="btn nc-action-standard">
          <i class="fa fa-file-text-o" aria-hidden="true"></i>Agregar Nota de Cr&eacute;dito
        </button>
        <button type="button" onclick="location.href='{{route('nota_credito_cliente.inventario.create')}}'" class="btn nc-action-inventory" title="Devuelve al inventario únicamente los productos que salieron en la factura">
          <i class="fa fa-cubes" aria-hidden="true"></i> Nota de Cr&eacute;dito con Inventario
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR NOTAS DE CRÉDITOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscar_not_cred" action="{{route('nota_credito_cliente.search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('contableM.asiento')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_asiento" name="buscar_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" autocomplete="off" placeholder="Ingrese asiento..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" autocomplete="off" placeholder="Ingrese número de Nota Crédito..." />
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id_cliente">{{trans('contableM.cliente')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-3">
          <select class="form-control select2_cuentas" style="width: 100%;" name="id_cliente" id="id_cliente">
            <option value="">Seleccione...</option>
           
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="concepto">{{trans('contableM.detalle')}}: </label>
        </div>
        <div class="form-group col-md-8 col-xs-10 container-4">
          <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" autocomplete="off" placeholder="Ingrese el detalle..." />
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
      <form method="GET" id="xls_reporte_nota_credito_cliente" action="{{route('nota_credito_cliente.exportar_excel')}}">
        <input type="hidden" name="buscar_asiento2" id="buscar_asiento2" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif">
        <input type="hidden" name="numero2" id="numero2" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif">
        <input type="hidden" name="id_cliente2" id="id_cliente2" value="@if(isset($searchingVals)){{$searchingVals['id_cliente']}}@endif">
        <input type="hidden" name="concepto2" id="concepto2" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif">
        <!--  <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
        </button> -->
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO NOTAS DE CRÉDITOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  @php
                  $id_auth = Auth::user()->id;
                  $id_empresa= Session::get('id_empresa');
                  $deEmpresa = Sis_medico\De_Empresa::where('id_empresa',$id_empresa)->first();
                  @endphp
                  <table id="tablaCredito" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tablaCredito_info">
                    <thead>
                      <tr class='well-dark'>

                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Nota Crédito</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Asiento</th>
                        <th width="15%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cliente')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.tipo')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Comprobante</th>
                        <th width="25%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Crédito</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.totaldeudas')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Abono</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.electronica')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="tablaCredito" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $sum_total_deudas = 0;
                      $sum_total_cred = 0;
                      $sum_total_abono = 0;
                      @endphp
                      @foreach ($nota_credito as $value)
                      @php
                      $fech = substr($value->fecha, 0, 10);
                      $fech_inver = date("d/m/Y",strtotime($fech));
                      $sum_total_deudas = $sum_total_deudas+$value->total_deudas;
                      $sum_total_cred = $sum_total_cred+$value->total_credito;
                      $sum_total_abono = $sum_total_abono+$value->total_abonos;
                      @endphp
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->id_asiento_cabecera)){{$value->id_asiento_cabecera}}@endif</td>
                        <td>@if(isset($value->cliente)) @if(!is_null($value->cliente->nombre)){{$value->cliente->nombre}}@endif @endif</td>
                        <td>@if(!is_null($fech_inver)){{$fech_inver}}@endif</td>
                        <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                        <td>@if(!is_null($value->nro_comprobante)){{$value->nro_comprobante}}@endif</td>
                        <td>@if(!is_null($value->concepto)){{$value->concepto}}@endif</td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_credito))
                          {{$value->total_credito}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_deudas))
                          {{$value->total_deudas}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_abonos))
                          {{$value->total_abonos}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                        <td>
                          @if($value->electronica==1)
                          SI
                          @else
                          NO
                          @endif
                        </td>
                        <td>
                          <a class="btn btn-success btn-gray" title="Visualizar" href="{{route('nota_credito_cliente.edit2',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                          @if($value->punto_emision!=100)
                          @if($value->electronica==1)
                          <a target="_blank" title="Descargar RIDE" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf', 'documento' => 'nota_credito']) }}" class="btn btn-success btn-margin btn-gray col-md-6 col-xs-6" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">RIDE</a>
                          <a href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante', 'documento' => 'nota_credito']) }}" title="Ver log" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray nc-log-button" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log</a>
                          @endif
                          <a href="javascript:anular({{$value->id}})" class="btn btn-danger btn-gray"> <i class="fa fa-remove"></i> </a>
                          @if(Auth::user()->id=="1316262193")
                          <button type="button" class="btn btn-primary" onclick="perm('{{$value->id}}')"> <i class="fa fa-file"></i> </button>
                          @endif
                          @endif
                          @if($value->punto_emision==100)
                            @if($value->electronica==1)
                            <!-- <a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log Factura</a> -->
                            @endif
                          @if($value->doc_electronico==7 || $value->doc_electronico==9)
                            <a href="javascript:anular({{$value->id}})" title="Anular" class="btn btn-danger btn-gray"> <i class="fa fa-remove"></i> </a>
                          @elseif($value->doc_electronico==5)
                          <div class="dropdown">
                            <button class="btn-gray btn btn-success dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">✓<i class="fa fa-caret-down"></i></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                              <a href="/../../api_doc_electronico?opcion=descargarXML&clave={{ $value->nro_autorizacion }}" class="btn btn-success btn-gray">
                                <i class="fa fa-file-code-o"></i></a>
                              <a href="/../../api_doc_electronico?opcion=generarPdf&clave={{ $value->nro_autorizacion }}" class="btn btn-danger btn-gray">
                                <i class="fa fa-file-pdf-o"></i></a>
                              <a href="javascript:anular({{$value->id}})" title="Anular" class="btn btn-danger btn-gray"> <i class="fa fa-remove"></i> </a>
                            </div>
                          </div>
                          @elseif($value->doc_electronico==10)
                          <div class="dropdown">
                            <a href="javascript:anular({{$value->id}})" title="Anular" class="btn btn-danger btn-gray"> <i class="fa fa-remove"></i> </a>
                          </div>
                          @endif
                          <!-- <a href="{{route('notaCredito.buscador_cliente',['id' => $value->id])}}" class="btn btn-danger btn-gray">
                                    <i class="fa fa-edit"></i></a> -->
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5">&nbsp;</td>
                        <td><b>{{trans('contableM.total')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_deudas, 2, '.', '')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_cred, 2, '.', '')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_abono, 2, '.', '')}}</b></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} / {{count($nota_credito) + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} de {{$nota_credito->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $nota_credito->appends(Request::only(['id_asiento','id','id_cliente','concepto']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/hospital/cleave/dist/cleave.min.js')}}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script type="text/javascript">
  $(document).ready(function() {

    $(document).on('click', '.nc-log-button', function(event) {
      event.preventDefault();

      var url = $(this).attr('href');
      var modal = $('#log_factura');
      var contenido = modal.find('.modal-content');

      modal.appendTo('body');
      contenido.html('<div class="nc-log-loading"><i class="fa fa-spinner fa-spin"></i>Cargando log...</div>');
      modal.modal('show');

      $.get(url)
        .done(function(respuesta) {
          contenido.html(respuesta);
        })
        .fail(function() {
          contenido.html('<div class="nc-log-error"><i class="fa fa-exclamation-circle"></i> No fue posible cargar el log.</div>');
        });
    });

    $('#log_factura').on('hidden.bs.modal', function() {
      $(this).find('.modal-content').empty();
      $(this).removeData('bs.modal');
    });

    $('#tablaCredito').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false
    });
    $('.select2').select2({
      tags: false
    });

    $('.select2_cuentas').select2({
      placeholder: "Escriba el nombre del cliente",
        allowClear: true,
      ajax: {
        url: '{{route("venta.clientesearch")}}',
        data: function (params) {
        var query = {
            search: params.term,
            type: 'public'
        }
        return query;
        },
        processResults: function (data) {
            // Transforms the top-level key of the response object from 'items' to 'results'
            console.log(data);
            return {
                results: data
            };
        }
      }
    });

  });

  function perm(id) {
    //alert(id);
    $.ajax({
      type: 'get',
      url: "{{route('nota_credito_reenviar')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'id': id
      },
      success: function(data) {
        console.log(data);

      },
      error: function(data) {
        console.log(data);
      }
    })
  }

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta nota de credito?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        let ids = "";
        var acumulate = "";
        var mensaje = "";

        let msj_ingr = "";
        let msj_ret = "";
        let msj_cruce = "";
        let msj_che = "";
        let msj_cruce_cue = "";
        let msj_credito = "";
        let mensajeaux = "";

        test(id);

      }
    })
  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/cliente/nota_credito/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.href = "{{route('nota_credito_cliente.index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }

  
</script>

@endsection
