@extends('contable.cierre_caja.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 14px;
    font-weight: bold;
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

  .right_text {
    text-align: right;
  }

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {}

  .salida {
    background-color: #FF886E;
    color: white;
    font-weight: bold;
  }

  .entrada {
    background-color: #91FF56;
    color: white;
    font-weight: bold;
  }

  .boldi {
    font-weight: bold;
  }

  .fin {
    background-color: #C1341B;
    color: white;
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

  .tabcontent3 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  .tabcontent4 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  td.details-control {
    background: url('{{asset("mas.png")}}') no-repeat center center;
    width: 100px;
  }

  tr.shown td.details-control {
    background: url('{{asset("menos.png")}}') no-repeat center center;
    width: 100px;
  }
</style>
@php
  $seguros = Sis_medico\Seguro::where('inactivo','1')->get();
  $usuario = Auth::user()->id;
  $usuario_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $usuario)->where('estado', '1')->where('cierre_caja_labs', '1')->first();
  $empresa_labs= Sis_medico\Empresa::where('prioridad_labs', '1')->first();
@endphp
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Main content -->
<section class="content">
  <!-- Ventana modal factura agrupada -->
  <input type="hidden" name="ruc_labs" id="ruc_labs" value="{{$empresa_labs->id}}">
  <div class="modal fade fullscreen-modal" id="modal_agrupada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content" id="datos_agrupada">
      </div>
    </div>
  </div>
  <div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="datos_factura">
      </div>
    </div>
  </div>
  <div class="modal fade bs-example-modal-lg" id="modalDocs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="contentDocs">
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal" id="modalIngreso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ingreso de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formingreso" action="{{route('c_caja.store')}}" method="POST">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input type="hidden" name="inicial" value="0">
                <input class="form-control" type="text" name="fechaTime" id="fechaTime">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime" id="valorTime">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime" id="observacionTime" cols="3" rows="3"></textarea>
              </div>

          </form>

        </div>
      </div>
      <div class="modal-footer">

        <button type="button" onclick="sumiter()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>

  </div>
  <div class="modal fade bd-example-modal" id="modalSalida" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Salida de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="pors" method="POST" action="{{route('c_caja.store_salida')}}">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input class="form-control" type="text" name="fechaTime2" id="fechaTime2">
                <input type="hidden" name="val" value="1">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime2" id="valorTime2">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime2" id="observacionTime2" cols="3" rows="3"></textarea>
              </div>
            </div>
          </form>

        </div>
        <div class="modal-footer">

          <button type="button" onclick="porsumit()" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>

  </div>
  <div class="modal fade bs-example-modal-lg" id="modal_forma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="div_forma">

      </div>
    </div>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Cierre de Caja</a></li>
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
    
    @if(is_null($usuario_permiso) and $rol_usuario == '5' )
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('c_caja.index') }}">
        {{ csrf_field() }}

        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
              <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
          </div> -->
          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">Fecha: </label>
            <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha_desde" autocomplete="off" value="{{date('Y/m/d',strtotime($fecha))}}">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <!--  <div class="form-group col-md-2 col-xs-2" style="text-align: right;">
            <button class="btn btn-info btn-gray" onclick="return $('#reporte_master').submit()"> <i class="fa fa-search"></i> </button>
          </div> -->
        <div class="col-md-2" style="display: none;">
            <button formaction="{{ route('orden.cierre_caja')}}" id="btn_cierre" type="submit" class="btn btn-success btn-gray"> Reporte Cierre </button>
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <a class="btn btn-primary btn-xs" onclick="factura_agrupada();">Factura Agrupada</a>
          </div>
          <div class="form-group col-md-2 col-xs-6" >
            <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('cierrecaja.nr_cierre_laboratorio') }}" formtarget="_blank"><span class="glyphicon glyphicon-stats" aria-hidden="true"> NUEVO RPT CIERRE</button>
          </div>
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-2">
                <label class="col-md-12"> Usuario: </label>
                <div class="col-md-12">
                  <select class="form-control select2"  style="width: 100%;" name="id_usuario" id="id_usuario">
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <label class="col-md-12"># Orden</label>
                <div class="col-md-12">
                    <input type="text" style="width: 100%;" class="form-control" name="ordenid" id="ordenid" placeholder="Ingrese # Orden" >
                </div>
              </div>
              <div class="col-md-2">
                <label class="col-md-12">Tipo</label>
                <div class="col-md-12">
                  <select class="form-control" style="width: 100%;" name="facturado" id="facturado">
                      <option value="">Seleccione...</option>
                      <option value="1">Facturados</option>
                      <option value="2">No Facturados</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <label class="col-md-12">Seguro</label>
                <div class="col-md-12">
                    <select class="form-control sel_seguro" name="id_seguro" id="id_seguro" style="width: 100%;">
                      <option value="">Seleccione</option>
                  @foreach($seguros as $seguro)
                  <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <br>
              <button class="btn btn-info" type="button" onclick="noway()"> <i class="fa fa-search"></i> </button>
            </div>
          </div>
      </form>
    </div>
    @endif
    <!-- /.box-body -->

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>

            <div class="box-body">
              <div class="col-md-1">
                <dl>
                  <dd><img src="@if(!is_null($empresa->logo)) {{asset('/logo').'/'.$empresa->logo}} @endif" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
                </dl>
              </div>
              <div class="col-md-2">
                <dl>
                  <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                  <dd>&nbsp; {{$empresa->id}}</dd>
                </dl>
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-7">
                    <h4 style="text-align: center;">Cierre de Caja</h4>
                  </div>
                  <!--  -->
                  <div class="col-md-3">

                    @if(!is_null($ingresoCaja))

                    @if(count($cierreFinal)>0)
                    @else
                    <button type="button" class="btn btn-success btn-gray" style="display: none;" data-toggle="modal" data-target="#modalIngresoF"> Ingreso de Caja </button>
                    @endif
                    @else
                    <button type="button" class="btn btn-success btn-gray" data-toggle="modal" data-target="#modalIngreso"> Ingreso Inicial de Caja </button>
                    @endif
                  </div>

                  @if(!is_null($cierreFinal))
                  <div class="col-md-2">
                    <span class="label label-danger">CAJA CERRADA</span>
                  </div>
                  @else
                  @if(!is_null($cierreFinal))
                  @else
                  <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-gray" data-toggle="modal" style="display: none;" data-target="#modalSalida"> Salida de Caja </button>
                  </div>
                  @endif
                  @endif
                </div>
              </div>
              @if(count($datosFinal)>0)
                @if(!is_null($cierreFinal))
                <div class="col-md-12">
                <span class="label label-default">RESUMEN DE CAJA</span>
                <div class="col-md-12">
                  &nbsp;
                </div>
                  <div class="row">
                      <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-money"></i> Efectivo </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['efectivo'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>
                       <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-money"></i> Cheque </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['cheque'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>


                      <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-newspaper-o"></i> Deposito </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['deposito'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>

                      <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-newspaper-o"></i> Transferencia </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['transferencia'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>

                      <div class="form-group col-md-2">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-credit-card"></i> Tarjeta de Credito </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['credito'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>
                      <div class="form-group col-md-2">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-credit-card"></i> Tarjeta de Debito </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['debito'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>

                      <div class="form-group col-md-2">
                          <div class="row">
                            <div class="col-md-12">
                              <label> <i class="fa fa-file-text"></i> Pendiente de Pago </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['pendiente'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>
                      <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label>  No Facturados </label>
                            </div>
                            <div class="col-md-12" style="background-color: red;color: white;">
                              <span >{{number_format($datosFinal['no_facturados_cant'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>
                      <div class="form-group col-md-1">
                          <div class="row">
                            <div class="col-md-12">
                              <label>  Facturados </label>
                            </div>
                            <div class="col-md-12">
                              <span>{{number_format($datosFinal['facturados_cant'],2,'.','')}}</span>
                            </div>
                          </div>



                      </div>

                  </div>
                </div>
                @endif
              @endif
              
              @if(!is_null($usuario_permiso) || $rol_usuario == '1' || $rol_usuario == '20')
                <form method="POST" >
                  {{ csrf_field() }}
                  <div class="form-group col-md-12 col-xs-12" style="margin: 5px">
                    <a class="btn btn-primary btn-xs" onclick="factura_agrupada();">Factura Agrupada</a>
                  </div>
                  <div class="col-md-8">
                    <div class="row">
                      <div class="col-md-6">
                        <label for="ct_fecha_desde" class="col-md-12 control-label">Desde:</label>
                        <div class="col-md-12">
                          <div class="input-group date col-md-12">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control col-md-12" name="ct_fecha_desde" id="ct_fecha_desde" >
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('ct_fecha_desde').value = ''; "></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label for="ct_fecha_hasta" class="col-md-12 control-label">Hasta:</label>
                        <div class="col-md-12">
                          <div class="input-group date col-md-12">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control col-md-12" name="ct_fecha_hasta" id="ct_fecha_hasta" autocomplete="off">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('ct_fecha_hasta').value = ''; "></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-2">
                        <label class="col-md-12"> Usuario: </label>
                        <div class="col-md-12">
                          <select class="form-control select2"  style="width: 100%;" name="id_usuario" id="id_usuario">
                          </select>
                        </div>
                      </div>

                      <div class="col-md-2">
                        <label class="col-md-12"># Orden</label>
                        <div class="col-md-12">
                            <input type="text" style="width: 100%;" class="form-control" name="ordenid" id="ordenid" placeholder="Ingrese # Orden" >
                        </div>
                      </div>

                      <div class="col-md-2">
                        <label class="col-md-12">Tipo</label>
                        <div class="col-md-12">
                          <select class="form-control" style="width: 100%;" name="facturado" id="facturado">
                              <option value="">Seleccione...</option>
                              <option value="1">Facturados</option>
                              <option value="2">No Facturados</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-2">
                        <label class="col-md-12">Seguro</label>
                        <div class="col-md-12">
                            <select class="form-control sel_seguro" name="id_seguro" id="id_seguro" style="width: 100%;">
                              <option value="">Seleccione</option>
                              @foreach($seguros as $seguro)
                                <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                              @endforeach
                            </select>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <br>
                    <button class="btn btn-info" type="button" onclick="noway()" id="enviar"> <i class="fa fa-search"></i> </button>
                      </div>
                      <div class="col-md-1">
                        <br>
                        <button class="btn btn-success" type="submit" formaction="{{route('cierrecaja.ct_reporte_cierre_excel')}}"> <i class="fa fa-file-excel-o">&nbsp;&nbsp;EXCEL</i> </button>
                      </div>
                      <div class="col-md-1">
                        <br>
                        <button class="btn btn-success" type="submit" formaction="{{ route('cierrecaja.ct_reporte_cierre_pdf') }}" formtarget="_blank"> <i class="fa fa-file-pdf-o">&nbsp;&nbsp;PDF</i> </button>
                      </div>
                </form>
              @endif
                </div>
              </div>
              <div class="col-md-12">
                &nbsp;
              </div>

              <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Recibo de Cobro</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Informe</a>
                    </li>

              </ul>

              <div class="row">
                <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="table table-responsive col-md-12">
                      <div class="content ">
                        <table id="examples2" class="compact dataTable nowrap stripe " role="grid" aria-describedby="example2_info" style="width:100%;">
                          <thead>
                            <tr>
                              <th style="width: 2%;">#</th>
                              <th style="width: 8%;" aria-controls="example2">Fecha.</th>
                              <th style="width: 5.83%;">Seguro</th>
                              <th style="width: 10%;" aria-controls="example2">Paciente</th>
                              <th style="width: 5.83%;">Efectivo</th>
                              <th style="width: 5.83%;">Cheque</th>
                              <th style="width: 5.83%;">Deposito</th>
                              <th style="width: 5.83%;">Trans</th>
                              <th style="width: 5.83%;">Credito</th>
                              <th style="width: 5.83%;">Debito</th>
                              <th style="width: 5.83%;">P. Pago</th>
			      <th style="width: 5.83%;">Online</th>
			      <th style="width: 5.83%;">DataLink</th>
                              <th style="width: 5.83%;">Oda</th>

                              <th style="width: 5.83%;">Subir Documento</th>
                              <th style="width: 5.83%;">Comprobante</th>
                              <th style="width: 10%;">Usuario</th>
                              <th style="width: 5.83%;">Opciones</th>

                            </tr>
                          </thead>
                          <tbody>

                          </tbody>
                          <tfoot>
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
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>

                            </tr>
                          </tfoot>
                        </table>

                      </div>

                    </div>
                  </div>
                  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="table table-responsive col-md-12">
                    <div class="content ">
                      <table id="examples3" class="compact dataTable nowrap stripe " role="grid" aria-describedby="example2_info" style="width:100%;">
                        <thead>
                          <tr>
                            <th style="width: 2%;">#</th>
                            <th style="width: 8%;" aria-controls="example2">Fecha</th>
                            <th style="width: 5.83%;">Seguro</th>
                            <th style="width: 10%;" aria-controls="example2">Paciente</th>
                            <th style="width: 5.83%;">Efectivo</th>
                            <th style="width: 5.83%;">Cheque</th>
                            <th style="width: 5.83%;">Deposito</th>
                            <th style="width: 5.83%;">Transferencia</th>
                            <th style="width: 5.83%;">Credito</th>
                            <th style="width: 5.83%;">Debito</th>
                            <th style="width: 5.83%;">P. Pago</th>
                            <th style="width: 5.83%;">Oda</th>
                            <th style="width: 5.83%;">Información</th>
                            <th style="width: 5.83%;">Comprobante</th>

                          </tr>
                        </thead>
                        <tbody>

                      </tbody>
                      <tfoot>
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
                          <td></td>
                          <td></td>
                          <td></td>

                        </tr>
                      </tfoot>
                    </table>

                  </div>

                </div>

              </div>
            </div>



              </div>
              @if(!is_null($cierreFinal))
              @else
              @php
              $id_auth    = Auth::user()->id;
              @endphp
              <div class="col-md-12" style="text-align: center;">
                <button class="btn btn-info btn-gray" type="button" onclick=" getCierre('{{$id_auth}}','{{$fecha}}')" > Cierre de Caja </button>
              </div>
              @endif
            </div>

        <!-- /.box-body -->
      </div>
      <!-- /.box Anthony del futuro arreglar 21 de mayo hice edte cambio para que restara bien pero no resta bien porque las facturas están dañadas desde un comienzo -->
    </div>
  </div>



  </div>
  <div class="modal fade bd-example-modal" id="modalCierre" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="contentCierre" role="document">

    </div>

  </div>

  </div>
  <div class="modal fade bd-example-modal" id="modalIngresoF" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ingreso de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formingresof" action="{{route('c_caja.store')}}" method="POST">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input type="hidden" name="inicial" value="1">
                <input class="form-control" type="text" name="fechaTime" id="fechaTimef">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime" id="valorTimef">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime" id="observacionTimef" cols="3" rows="3"></textarea>
              </div>

          </form>

        </div>
      </div>
      <div class="modal-footer">

        <button type="button" onclick="sumiterf()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  function recargar(){
    let fecha_desde = localStorage.getItem("ct_fecha_desde");
    let fecha_hasta = localStorage.getItem("ct_fecha_hasta");
    if(fecha_desde != '' && fecha_hasta != ''){
          $('#examples2').dataTable().fnDestroy();
          document.getElementById("ct_fecha_desde").value = fecha_desde;
          document.getElementById("ct_fecha_hasta").value = fecha_hasta;
          bindTable();
    }
  }
  $(document).ready(function() {
   // recargar();
   // $('#examples2').dataTable().fnDestroy();
    $('.select2_cuentas').select2({
      tags: false
    });

    $('body').on('hidden.bs.modal', '.modal', function() {
      $(this).removeData('bs.modal');
    });
    var users= '{{Auth::user()->id}}';
    @if(Auth::user()->id_tipo_usuario==20 || Auth::user()->id_tipo_usuario==1 || Auth::user()->id_tipo_usuario==5)

    var users="";
      var table = $('#examples2').DataTable({
      paging: false,
      dom: 'lBrtip',
      buttons: [
        // {
        //   extend: 'copyHtml5',
        //   footer: true
        // },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
        },
        // {
        //   extend: 'csvHtml5',
        //   footer: true
        // },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      "searching": false,
      processing: true,
      serverSide: true,
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}",
      "columns": [{
          "data": "id_orden",
          "orderable": false
        },{
          "data": "fecha"
        },
        {
          "data": function(data) {

            const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
            return `${data.seguro} <br> ${nivel}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {

            const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
            const paciente = data.paciente;
            return `${paciente} <br> ${cedula}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataEfectivo=0;
            for(i=0; i<data.length;i++){
                dataEfectivo+= data[i].dataEfectivo;
            }
            if(data.facturado == "Cierre Caja"){

              return dataEfectivo;
            }
            dataEfectivo= data.dataEfectivo;
            return dataEfectivo;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataCheque=0;
            for(i=0; i<data.length;i++){
                dataCheque+= data[i].dataCheque;
            }
            if(data.facturado == "Cierre Caja"){

              return dataCheque;
            }
            dataCheque= data.dataCheque;
            return dataCheque;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataDeposito=0;
            for(i=0; i<data.length;i++){
                dataDeposito+= data[i].dataDeposito;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDeposito;
            }
            dataDeposito= data.dataDeposito;
            return dataDeposito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTransferencia=0;
            for(i=0; i<data.length;i++){
                dataTransferencia+= data[i].dataTransferencia;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTransferencia;
            }
            dataTransferencia= data.dataTransferencia;
            return dataTransferencia;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTarjetaCredito=0;
            for(i=0; i<data.length;i++){
                dataTarjetaCredito+= data[i].dataTarjetaCredito;
              }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaCredito;
            }
            dataTarjetaCredito= data.dataTarjetaCredito;
            return dataTarjetaCredito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            //console.log(data,'en debito');
            var dataTarjetaDebito=0;
            for(i=0; i<data.length;i++){
                //console.log(data[i]);
                dataTarjetaDebito+= data[i].dataTarjetaDebito;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaDebito;
            }
            dataTarjetaDebito= data.dataTarjetaDebito;
            return dataTarjetaDebito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var pendientePago=0;
            for(i=0; i<data.length;i++){
                pendientePago+= data[i].dataPendientePago;
            }
            if(data.facturado == "Cierre Caja"){

              return pendientePago;
            }
            pendientePago= data.dataPendientePago;
            return pendientePago;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var online=0;
            for(i=0; i<data.length;i++){
                online+= data[i].online;
            }
            if(data.facturado == "Cierre Caja"){

              return online;
            }
            online= data.online;
            return online;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataDatalink=0;
            for(i=0; i<data.length;i++){
                dataDatalink+= data[i].dataDatalink;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDatalink;
            }
            dataDatalink= data.dataDatalink;
            return dataDatalink;
          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var oda=0;
            for(i=0; i<data.length;i++){
                oda+= data[i].oda;
            }
            if(data.facturado == "Cierre Caja"){

              return oda;
            }
            oda= data.oda;
            return oda;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            console.log(data);
            let boton1 = '<a class="btn btn-danger btn-xs" onclick="subirDocumento(' + data.id_orden + ')" ><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>';
            let boton2 = data.estadoDocs !== null ?  '<a class="btn btn-warning btn-xs" target="_blank" onclick="redigirDocs(' + data.id_orden + ')"  ><i class="fa fa-eye" aria-hidden="true"></i></a>' : '';
            return ' <div style="text-align: center;">'+boton1+''+boton2+'</div>';
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "numero",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "usuario",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            buttonnew = "#";
            var check = "";
            var newfor = null;
            var ride="-10";
            var c1="0";
            var c2="0";
            var c3="0";
            var url_observacion = "javascript:text_observacion(" + data.id_orden + ")";
            var observacion = '<a class="btn btn-success btn-xs" href="' + url_observacion + '"> <i class="fa fa-comment"></i>';
            var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
            if (typeof(data.orden) == 'object') {
              var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
              if (typeof(data.orden.id) == 'undefined') {
                buttonnew = "#";
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
              } else {
                bs = "#";
                //console.log(data.orden);
                var newbu2 = '<a style="" class="btn btn-danger btn-xs" href="' + bs + '""> SRI </a>';
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
                var newfor = "-1";
                if (data.orden.fecha_envio != null && data.orden.comprobante != null) {
                  ride=data.orden.comprobante
                  ride= ride.split('-');
                  c1= parseInt(ride[0]);
                  c2= parseInt(ride[1]);
                  c3= parseInt(ride[2]);
                  console.log(c3, ride[2]);

                } else {
                  newfor = data.orden.id;
                  check = "<input type='checkbox' class='ses' value='" + data.orden.id + "' >";
                }

              }
            }
            if(data.facturado == "Facturado"){
              newbu = "";
            }
            if(c1!=0){
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a style="" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI </a> <a class="btn btn-warning btn-xs" onclick="pdf_ride(' + c1 + ','+c2+','+c3+')" > RIDE</a> ' + observacion + ' </div>';
            }else{
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a style="" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI </a> ' + observacion + ' </div>';
            }
          
          },
          "targets": 0,
          "orderable": false
        }

      ],
      "order": [
        [1, 'asc']
      ],
      "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        var intVal = function(i) {
          return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        };
        var cases=0;
        if (data.facturado == "Cierre Caja") {
           cases=1;
        }
        total3=0;
        total4=0;
        total5=0;
        total6=0;
        total7=0;
        total8=0;
        total9=0;
        total10=0;
	total11=0;
        total12=0;
        for(i=0; i<data.length; i++){
          if(data[i].facturado!='Cierre Caja'){
            total3+=  parseFloat(data[i].dataEfectivo);
            total4+= parseFloat(data[i].dataCheque);
            total5+= parseFloat(data[i].dataDeposito);
            total6+= parseFloat(data[i].dataTransferencia);
            total7+= parseFloat(data[i].dataTarjetaCredito);
            total8+= parseFloat(data[i].dataTarjetaDebito);
            total9+= parseFloat(data[i].dataPendientePago);
            total10+= parseFloat(data[i].online);
	    total11+= parseFloat(data[i].dataDatalink);
            total12+= parseFloat(data[i].oda);
          }
          
        }
        

        $(api.column(3).footer()).html(
          '<label>Totales</label>'
        );
        $(api.column(4).footer()).html(
          '$' + total3.toFixed(2, 2)
        );
        $(api.column(5).footer()).html(
          '$' + total4.toFixed(2, 2)
        );
        $(api.column(6).footer()).html(
          '$' + total5.toFixed(2, 2)
        );
        $(api.column(7).footer()).html(
          '$' + total6.toFixed(2, 2)
        );
        $(api.column(8).footer()).html(
          '$' + total7.toFixed(2, 2)
        );
        $(api.column(9).footer()).html(
          '$' + total8.toFixed(2, 2)
        );
        $(api.column(10).footer()).html(
          '$' + total9.toFixed(2, 2)
        );
        $(api.column(11).footer()).html(
          '$' + total10.toFixed(2, 2)
        );
	$(api.column(12).footer()).html(
          '$' + total11.toFixed(2, 2)
        );
        $(api.column(13).footer()).html(
          '$' + total12.toFixed(2, 2)
        );
        var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;

      }
    });
    @else
    var table = $('#examples2').DataTable({
      paging: false,
      "searching": false,
      processing: true,
      serverSide: true,
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      buttons: [
        // {
        //   extend: 'copyHtml5',
        //   footer: true
        // },
         {
          extend: 'excelHtml5',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
        },
        // {
        //   extend: 'csvHtml5',
        //   footer: true
        // },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}&idUser=" +users+"",

      "columns": [{
          "data": "id_orden",
          "orderable": false
        },{
          "data": "fecha"
        },
        {
          "data": "seguro",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {

            const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
            const paciente = data.paciente;
            return `${paciente} <br> ${cedula}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {

            const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
            return `${data.seguro} <br> ${nivel}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataEfectivo=0;
            for(i=0; i<data.length;i++){
                dataEfectivo+= data[i].dataEfectivo;
            }
            if(data.facturado == "Cierre Caja"){

              return dataEfectivo;
            }
            dataEfectivo= data.dataEfectivo;
            return dataEfectivo;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataCheque=0;
            for(i=0; i<data.length;i++){
                dataCheque+= data[i].dataCheque;
            }
            if(data.facturado == "Cierre Caja"){

              return dataCheque;
            }
            dataCheque= data.dataCheque;
            return dataCheque;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataDeposito=0;
            for(i=0; i<data.length;i++){
                dataDeposito+= data[i].dataDeposito;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDeposito;
            }
            dataDeposito= data.dataDeposito;
            return dataDeposito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTransferencia=0;
            for(i=0; i<data.length;i++){
                dataTransferencia+= data[i].dataTransferencia;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTransferencia;
            }
            dataTransferencia= data.dataTransferencia;
            return dataTransferencia;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTarjetaCredito=0;
            for(i=0; i<data.length;i++){
                dataTarjetaCredito+= data[i].dataTarjetaCredito;
              }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaCredito;
            }
            dataTarjetaCredito= data.dataTarjetaCredito;
            return dataTarjetaCredito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            //console.log(data,'en debito');
            var dataTarjetaDebito=0;
            for(i=0; i<data.length;i++){
                //console.log(data[i]);
                dataTarjetaDebito+= data[i].dataTarjetaDebito;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaDebito;
            }
            dataTarjetaDebito= data.dataTarjetaDebito;
            return dataTarjetaDebito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var pendientePago=0;
            for(i=0; i<data.length;i++){
                pendientePago+= data[i].dataPendientePago;
            }
            if(data.facturado == "Cierre Caja"){

              return pendientePago;
            }
            pendientePago= data.dataPendientePago;
            return pendientePago;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var online=0;
            for(i=0; i<data.length;i++){
                online+= data[i].online;
            }
            if(data.facturado == "Cierre Caja"){

              return online;
            }
            online= data.online;
            return online;
          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataDatalink=0;
            for(i=0; i<data.length;i++){
                dataDatalink+= data[i].dataDatalink;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDatalink;
            }
            dataDatalink= data.dataDatalink;
            return dataDatalink;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var oda=0;
            for(i=0; i<data.length;i++){
                oda+= data[i].oda;
            }
            if(data.facturado == "Cierre Caja"){

              return oda;
            }
            oda= data.oda;
            return oda;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            if (data.facturado == "Inicio de Caja") {
              return ' <span class="label label-info">' + data.facturado + '</span>';
            } else if (data.facturado == "Cierre Caja") {
              return '<span class="label label-danger">' + data.facturado + '</span>';
            } else if (data.facturado == "No Facturado") {
              return ' <span class="label label-warning">' + data.facturado + '</span>';
            } else if (data.facturado == "Orden Publica") {
              return ' <span class="label label-primary">' + data.facturado + '</span>';
            } else {
              return ' <span class="label label-default">' + data.facturado + '</span>';
            }

          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "numero",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "usuario",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            buttonnew = "#";
            var check = "";
            var newfor = null;
            var ride="-10";
            var c1="0";
            var c2="0";
            var c3="0";
            var url_observacion = "javascript:text_observacion(" + data.id_orden + ")";
            var observacion = '<a class="btn btn-success btn-xs" href="' + url_observacion + '"> <i class="fa fa-comment"></i>';
            var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
            if (typeof(data.orden) == 'object') {
              var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
              if (typeof(data.orden.id) == 'undefined') {
                buttonnew = "#";
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
              } else {
                bs = "#";
                //console.log(data.orden);
                var newbu2 = '<a class="btn btn-danger btn-xs" href="' + bs + '""> SRI </a>';
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
                var newfor = "-1";
                if (data.orden.fecha_envio != null && data.orden.comprobante != null) {
                  ride=data.orden.comprobante
                  ride= ride.split('-');
                  c1= parseInt(ride[0]);
                  c2= parseInt(ride[1]);
                  c3= parseInt(ride[2]);
                  console.log(c3, ride[2]);

                } else {
                  newfor = data.orden.id;
                  check = "<input type='checkbox' class='ses' value='" + data.orden.id + "' >";
                }

              }
            }
            if(c1!=0){
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> <a class="btn btn-warning btn-xs" onclick="pdf_ride(' + c1 + ','+c2+','+c3+')" > RIDE</a> ' + observacion + ' </div>';
            }else{
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> ' + observacion + ' </div>';
            }
          
          },
          "targets": 0,
          "orderable": false
        }

            ],
            "order": [
              [1, 'asc']
            ],
            "footerCallback": function(row, data, start, end, display) {
              var api = this.api();
              //console.log(data);

              // Remove the formatting to get integer data for summation
              var intVal = function(i) {
                return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '') * 1 :
                  typeof i === 'number' ?
                  i : 0;
              };
        var cases=0;
        if (data.facturado == "Cierre Caja") {
           cases=1;
        }
        //console.log(cases,data);
        // Total over all pages

              /*           total3 = api
                .column(4)
                .data()
                .reduce(function(a, b) {
                  if(cases!=1){
                    return parseFloat(a) + parseFloat(b);
                  }
                
                }, 0);
               */
              total3 = 0;
              total4 = 0;
              total5 = 0;
              total6 = 0;
              total7 = 0;
              total8 = 0;
              total9 = 0;
              total10 = 0;
              total11 = 0;
        total12=0;
        for(i=0; i<data.length; i++){
          if(data[i].facturado!='Cierre Caja'){
            total3+=  parseFloat(data[i].dataEfectivo);
                    total4 += parseFloat(data[i].dataCheque);
                    total5 += parseFloat(data[i].dataDeposito);
                    total6 += parseFloat(data[i].dataTransferencia);
                    total7 += parseFloat(data[i].dataTarjetaCredito);
                    total8 += parseFloat(data[i].dataTarjetaDebito);
                    total9 += parseFloat(data[i].dataPendientePago);
                    total10 += parseFloat(data[i].online);
                    total11 += parseFloat(data[i].dataDatalink);
                    total12 += parseFloat(data[i].oda);
                  }

                }

                /*    total4 = api
                     .column(5)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0);
                   total5 = api
                     .column(6)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0);
                   total6 = api
                     .column(7)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0);
                   total7 = api
                     .column(8)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0);
                   total8 = api
                     .column(9)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0);
                   total9 = api
                     .column(10)
                     .data()
                     .reduce(function(a, b) {
                       return parseFloat(a) + parseFloat(b);
                     }, 0); */
                // Total over this page

        // Update footer
        $(api.column(3).footer()).html(
          '<label>Totales</label>'
                );
                $(api.column(4).footer()).html(
                  '$' + total3.toFixed(2, 2)
                );
                $(api.column(5).footer()).html(
                  '$' + total4.toFixed(2, 2)
                );
                $(api.column(6).footer()).html(
                  '$' + total5.toFixed(2, 2)
                );
                $(api.column(7).footer()).html(
                  '$' + total6.toFixed(2, 2)
                );
                $(api.column(8).footer()).html(
                  '$' + total7.toFixed(2, 2)
                );
                $(api.column(9).footer()).html(
                  '$' + total8.toFixed(2, 2)
                );
                $(api.column(10).footer()).html(
                  '$' + total9.toFixed(2, 2)
                );
                $(api.column(11).footer()).html(
                  '$' + total10.toFixed(2, 2)
                );
                $(api.column(12).footer()).html(
                  '$' + total11.toFixed(2, 2)
                );
                $(api.column(13).footer()).html(
                  '$' + total12.toFixed(2, 2)
                );
                var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;

      }
    });
    @endif

          //tursi 
          var users = '{{Auth::user()->id}}';
          var table2 = $('#examples3').DataTable({
              dom: 'lBrtip',
              paging: false,
              buttons: [
                // {
                //   extend: 'copyHtml5',
                //   footer: true
                // },
                {
                  extend: 'excelHtml5',
                  footer: true,
                  title: 'HUMANLABS {{$fecha}}',
                },
                // {
                //   extend: 'csvHtml5',
                //   footer: true
                // },
                {
                  extend: 'pdfHtml5',
                  orientation: 'landscape',
                  pageSize: 'LEGAL',
                  footer: true,
                  title: 'HUMANLABS {{$fecha}}',
                  customize: function(doc) {
                    doc.styles.title = {
                      color: 'black',
                      fontSize: '17',
                      alignment: 'center'
                    }
                  }
                }
              ],
              processing: true,
              serverSide: true,
              responsive: true,
              'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
              },
              "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}&idUser=" + users + "",

              "columns": [{
                  "data": "id_orden",
                  "orderable": false
                }, {
                  "data": "fecha"
                },
                {
                  "data": "seguro",
                  "targets": 0,
                  "orderable": false
                },
        {
          "data": function(data) {

            const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
            const paciente = data.paciente;
            return `${paciente} <br> ${cedula}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
            return `${data.seguro} <br> ${nivel}`;
          },

          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataEfectivo=0;
            for(i=0; i<data.length;i++){
                dataEfectivo+= data[i].dataEfectivo;
            }
            if(data.facturado == "Cierre Caja"){

              return dataEfectivo;
            }
            dataEfectivo= data.dataEfectivo;
            return dataEfectivo;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataCheque=0;
            for(i=0; i<data.length;i++){
                dataCheque+= data[i].dataCheque;
            }
            if(data.facturado == "Cierre Caja"){

              return dataCheque;
            }
            dataCheque= data.dataCheque;
            return dataCheque;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {

          "data": function(data) {
            var dataDeposito=0;
            for(i=0; i<data.length;i++){
                dataDeposito+= data[i].dataDeposito;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDeposito;
            }
            dataDeposito= data.dataDeposito;
            return dataDeposito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTransferencia=0;
            for(i=0; i<data.length;i++){
                dataTransferencia+= data[i].dataTransferencia;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTransferencia;
            }
            dataTransferencia= data.dataTransferencia;
            return dataTransferencia;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTarjetaCredito=0;
            for(i=0; i<data.length;i++){
                dataTarjetaCredito+= data[i].dataTarjetaCredito;
              }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaCredito;
            }
            dataTarjetaCredito= data.dataTarjetaCredito;
            return dataTarjetaCredito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            //console.log(data,'en debito');
            var dataTarjetaDebito=0;
            for(i=0; i<data.length;i++){
                //console.log(data[i]);
                dataTarjetaDebito+= data[i].dataTarjetaDebito;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaDebito;
            }
            dataTarjetaDebito= data.dataTarjetaDebito;
            return dataTarjetaDebito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var pendientePago=0;
            for(i=0; i<data.length;i++){
                pendientePago+= data[i].dataPendientePago;
            }
            if(data.facturado == "Cierre Caja"){

              return pendientePago;
            }
            pendientePago= data.dataPendientePago;
            return pendientePago;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var online=0;
            for(i=0; i<data.length;i++){
                online+= data[i].online;
            }
            if(data.facturado == "Cierre Caja"){

              return online;
            }
            online= data.online;
            return online;
          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataDatalink=0;
            for(i=0; i<data.length;i++){
                dataDatalink+= data[i].dataDatalink;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDatalink;
            }
            dataDatalink= data.dataDatalink;
            return dataDatalink;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var oda=0;
            for(i=0; i<data.length;i++){
                oda+= data[i].oda;
            }
            if(data.facturado == "Cierre Caja"){

              return oda;
            }
            oda= data.oda;
            return oda;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            if (data.facturado == "Inicio de Caja") {
              return ' <span class="label label-info">' + data.facturado + '</span>';
            } else if (data.facturado == "Cierre Caja") {
              return '<span class="label label-danger">' + data.facturado + '</span>';
            } else if (data.facturado == "No Facturado") {
              return ' <span class="label label-warning">' + data.facturado + '</span>';
            } else if (data.facturado == "Orden Publica") {
              return ' <span class="label label-primary">' + data.facturado + '</span>';
            } else {
              return ' <span class="label label-default">' + data.facturado + '</span>';
            }

          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "numero",
          "targets": 0,
          "orderable": false
        }

      ],
      "order": [
        [1, 'asc']
      ],
      "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        //console.log(data);

        // Remove the formatting to get integer data for summation
        var intVal = function(i) {
          return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        };
        var cases=0;
        if (data.facturado == "Cierre Caja") {
           cases=1;
        }
        total3=0;
        total4=0;
        total5=0;
        total6=0;
        total7=0;
        total8=0;
        total9=0;
        total10=0;
        total11=0;
        total12=0;
        for(i=0; i<data.length; i++){
          if(data[i].facturado!='Cierre Caja'){
            total3+=  parseFloat(data[i].dataEfectivo);
            total4+= parseFloat(data[i].dataCheque);
            total5+= parseFloat(data[i].dataDeposito);
            total6+= parseFloat(data[i].dataTransferencia);
            total7+= parseFloat(data[i].dataTarjetaCredito);
            total8+= parseFloat(data[i].dataTarjetaDebito);
            total9+= parseFloat(data[i].dataPendientePago);
            total10+= parseFloat(data[i].online);
            total11+= parseFloat(data[i].dataDatalink);
            total12+= parseFloat(data[i].oda);
          }

        }
        $(api.column(3).footer()).html(
          '<label>Totales</label>'
        );
        $(api.column(4).footer()).html(
                    '$' + total3.toFixed(2, 2)
                  );
                  $(api.column(5).footer()).html(
                    '$' + total4.toFixed(2, 2)
                  );
                  $(api.column(6).footer()).html(
                    '$' + total5.toFixed(2, 2)
                  );
                  $(api.column(7).footer()).html(
                    '$' + total6.toFixed(2, 2)
                  );
                  $(api.column(8).footer()).html(
                    '$' + total7.toFixed(2, 2)
                  );
                  $(api.column(9).footer()).html(
                    '$' + total8.toFixed(2, 2)
                  );
                  $(api.column(10).footer()).html(
                    '$' + total9.toFixed(2, 2)
                  );
                  $(api.column(11).footer()).html(
                    '$' + total10.toFixed(2, 2)
                  );
                  $(api.column(12).footer()).html(
                    '$' + total11.toFixed(2, 2)
                  );
                  $(api.column(13).footer()).html(
                    '$' + total12.toFixed(2, 2)
                  );
                  var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;

                }
    });
    $('#examples2 tbody').on('click', 'td.details-control', function() {
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
      } else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
      }
    });
   /*  setInterval(function() {
      table.ajax.reload();
    }, 40000); */

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

    $('.sel_seguro').select2({
      placeholder: 'Seleccione Seguro',
      allowClear: true,


    });
    setInterval( function () {
      $('#examples2').DataTable().clear().destroy();
      bindTable();
    }, 600000 );

          });
        $('#cierreCaja').on('hidden.bs.modal', function() {
          $(this).removeData('bs.modal');

        });
        $('body').on('click', '.ses', function() {
          if ($(this).prop('checked')) {
            guarda_sesion_factura($(this).val(), this);
          } else {

          }
        });
        var array_ordenes = [];

        function eper() {
          $('.ses').each(function() {

            array_ordenes.push({
              'orden': $(this).value
            });
          });
          console.log(array_ordenes);
        }

  function format(d) {
    // `d` is the original data object for the row
    console.log(d);
    var arrayPusher = d.forma_pago;
    console.log(arrayPusher)
    var datahtml = "<td> No hay datos que mostrar </td>"
    if ((arrayPusher.length) > 0) {
      datahtml = "";
      for (var car of arrayPusher) {
        //console.log(car);
        var forma = "No tiene";
        if (car.id_tipo_pago == '1') {
          forma = "Efectivo";
        } else if (car.id_tipo_pago == '2') {
          forma = "Cheque";
        } else if (car.id_tipo_pago == '3') {
          forma = "Deposito";
        } else if (car.id_tipo_pago == '4') {
          forma = "Tarjeta Credito";
        } else if (car.id_tipo_pago == '5') {
          forma = "Transferencia Bancaria";
        } else if (car.id_tipo_pago == '6') {
          forma = "Tarjeta de Debito";
        } else if (car.id_tipo_pago == '7') {
          forma = "Pendiente Pago";
        }
        datahtml += "<tr> <td>" + car.fecha + "</td> <td>" + forma + "</td> <td>" + car.valor + "</td> </tr>";
      }
    } else {
      return "<span> No existen detalles que mostrar.</span>"
    }
    var eq = d.orden;
    var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + d.orden.id + "/" + "1";
    var newfor = d.orden.id;
    return ' <label> Examen orden # ' + d.orden.id + ' </label>  &nbsp; &nbsp; <a  target="_blank" class="btn btn-danger btn-xs" href="' + buttonnew + '"> <i class="fa fa-edit"> </i> </a> &nbsp;  <button type="button" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')"> <i class="fa fa-file"> </i> </button> <table class="table table-striped dataTable" border="0" style="padding-left:50px;"> <thead> <tr> <th>Fecha</th> <th>Detalle</th> <th>Valor</th> </tr> </thead> <tboby>' + datahtml + ' </tbody> </table>';
  }

  function emitir_sri(id_orden) {
       
    if (id_orden == '-1' || id_orden == null) {
      Swal.fire("Mensaje", "Ya se encuentra facturado", "error"); 
    }
    if (id_orden != '' && typeof(id_orden) != 'undefined' && id_orden != null && id_orden != '-1') {
      $.ajax({
        type: 'get',
        url: "{{url('facturacion_labs/info_factura')}}/" + id_orden,
        datatype: 'json',
        success: function(data) {
          $('#datos_factura').empty().html(data);
          $('#modal_datosfacturas').modal();
        },
        error: function(data) {
          //console.log(data);
        }
      });
    }
   
  }
  $(function() {

    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',


    });
    $('#ct_fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',
      
    });
    $('#ct_fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',


    });
    $('#fechaTime').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechacierre').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechaTime2').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechaTimef').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
      return $('#reporte_master').submit();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
      return $('#reporte_master').submit();
    });

  });

  function sendend(url) {
    console.log("entra aqui")
    window.open("www.google.com", '_blank');
  }
  function pdf_ride(c1,c2,c3){
    var ruc_labs = $('#ruc_labs').val();
    console.log(c3);
    if(c1==0){
      alert('No existe RIDE.')
    }else{
      var url="{{url('/contable/ventas/ride_pdf/')}}/"+'00'+c1+'-'+'00'+c2+'-'+ c3.toString().padStart(9, 0)+'/'+ruc_labs+'/'+'pdf';
      window.open(url, '_blank');
    }
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function excel() {
    $("#print_reporte_master").submit();
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

  function forma_url(urlf) {
    //console.log(urlf);
    if (urlf != null) {
      var urls = "{{url('contable/facturacion/modal/cierre_caja')}}/" + urlf;
      $.ajax({
        type: 'get',
        url: urls,
        datatype: 'json',
        success: function(data) {
          $('#div_forma').empty().html(data);
          $('#modal_forma').modal();
        },
        error: function(data) {
          //console.log(data);
        }
      });
    }

  }

  function sumiter() {
    $("#formingreso").submit();
    console.log("aa");
  }

  function sumiterf() {
    $("#formingresof").submit();
  }

  function porsumit() {
    $("#pors").submit();
  }

  function porsumit2() {
    $("#pore").submit();
  }

  function cierreCaja() {
    let fecha = document.getElementById('fecha_desde').value;
    $.ajax({
      type: 'post',
      url: "{{route('orden.cierre_caja')}}",
      data: {
        'fecha': fecha
      },
      success: function(data) {},
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function guarda_sesion_factura(id_orden, e) {
    $.ajax({
      type: 'get',
      url: "{{url('facturacion_labs/añadir_factura')}}/" + id_orden,
      datatype: 'json',
      success: function(data) {
        if (data == 'ok') {
          $(e).css("background-color", "green");
        } else {
          alert("No se puede agregar la orden, seguro o nivel diferente");
          $(e).prop('checked', false);
        }
      },
      error: function(data) {
        alert("Error no se pudo agregar orden");
      }
    });
  }

  function factura_agrupada() {
    $.ajax({
      type: 'get',
      url: "{{url('facturacion_labs/modal_factura_agrupada')}}",
      datatype: 'json',
      success: function(data) {
        $('#datos_agrupada').empty().html(data);
        $('#modal_agrupada').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });

  }

  function eliminar(){
      //alert("hola");
      $.ajax({
            type: 'post',
            url: "{{ url('facturacion_labs/factura_agrup/eliminar_sesion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
      success: function(data){
          //alert("sucess");
          if(data=='ok'){
            factura_agrupada();
          };
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }
  
  function datos_factura_agrupada() {
    //alert(cuenta);
    $.ajax({
      type: 'get',
      url: "{{ url('facturacion_labs/datos_factura_agrupada')}}",
      datatype: 'json',
      success: function(datahtml) {
        //alert("sucess");
        $("#datos_factura_agrup").empty().html(datahtml);

      },
      error: function() {
        alert('error al cargar');
      }
    });
  }

  function cierre_mensaje() {
    Swal.fire("Mensaje", "Error", "error");
  }

  function noway(e) {

      if(document.getElementById("ct_fecha_desde").value != '' && document.getElementById("ct_fecha_hasta").value){
         localStorage.setItem("ct_fecha_desde", document.getElementById("ct_fecha_desde").value);
         localStorage.setItem("ct_fecha_hasta", document.getElementById("ct_fecha_hasta").value);
      }
      $('#examples2').DataTable().clear().destroy();
      bindTable();

  }
  function bindTableNew() {
    
    var table = $('#examples2').DataTable({
      "searching": false,
      paging: false,
      processing: true,
      serverSide: true,
      responsive: true,
      buttons: [
        // {
        //   extend: 'copyHtml5',
        //   footer: true
        // },
         {
          extend: 'excelHtml5',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
        },
        // {
        //   extend: 'csvHtml5',
        //   footer: true
        // },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}&idUser=" +user+"&ordenid="+ordenid+"&facturado="+facturado,

      "columns": [{
          "data": "id_orden"
        },{
          "data": "fecha"
        },
        {
          "data": "seguro",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
            const paciente = data.paciente;
            return `${paciente} <br> ${cedula}`;
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
            return `${data.seguro} <br> ${nivel}`;
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataEfectivo",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataCheque",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataDeposito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTransferencia",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTarjetaCredito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTarjetaDebito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataPendientePago",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "ponline",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataDatalink",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "oda",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            if (data.facturado == "Inicio de Caja") {
              return ' <span class="label label-info">' + data.facturado + '</span>';
            } else if (data.facturado == "Cierre Caja") {
              return '<span class="label label-danger">' + data.facturado + '</span>';
            } else if (data.facturado == "No Facturado") {
              return ' <span class="label label-warning">' + data.facturado + '</span>';
            } else if (data.facturado == "Orden Publica") {
              return ' <span class="label label-primary">' + data.facturado + '</span>';
            } else {
              return ' <span class="label label-default">' + data.facturado + '</span>';
            }

          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "numero",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "usuario",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            buttonnew = "#";
            var check = "";
            var newfor = null;
            var newbu = '<a href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
            if (typeof(data.orden) == 'object') {
              var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
              if (typeof(data.orden.id) == 'undefined') {
                buttonnew = "#";
                var newbu = '<a href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
              } else {
                bs = "#";
                //console.log(data.orden);
                var newbu2 = '<a href="' + bs + '""> SRI </a>';
                var newbu = '<a href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
                var newfor = "-1";
                if (data.orden.fecha_envio != null && data.orden.comprobante != null) {

                } else {
                  newfor = data.orden.id;
                  check = "<input type='checkbox' class='ses' value='" + data.orden.id + "' >";
                }

              }
            }

            return ' <div style="text-align: center;"> ' + check + ' </div> <div class="dropdown"><button class="btn btn-info btn-xs dropdown-toggle" type="button" id="about-us" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Listado<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="about-us"><li>' + newbu + '</i> </a></li><li><a onclick="emitir_sri(' + newfor + ')" > SRI</a></li></ul></div>';
          },
          "targets": 0,
          "orderable": false
        }

      ],
      "order": [
        [0, 'asc']
      ],
      "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        //console.log(data);

        // Remove the formatting to get integer data for summation
        var intVal = function(i) {
          return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        };

        // Total over all pages

        total3=0;
        total4=0;
        total5=0;
        total6=0;
        total7=0;
        total8=0;
        total9=0;
        total10=0;
        total11=0;
        total12=0;
        for(i=0; i<data.length; i++){
          if(data[i].facturado!='Cierre Caja'){
            total3+=  parseFloat(data[i].dataEfectivo);
            total4+= parseFloat(data[i].dataCheque);
            total5+= parseFloat(data[i].dataDeposito);
            total6+= parseFloat(data[i].dataTransferencia);
            total7+= parseFloat(data[i].dataTarjetaCredito);
            total8+= parseFloat(data[i].dataTarjetaDebito);
            total9+= parseFloat(data[i].dataPendientePago);
            total10+= parseFloat(data[i].online);
            total11+= parseFloat(data[i].dataDatalink);
            total12+= parseFloat(data[i].oda);
          }
          
        }

        // Update footer
        $(api.column(3).footer()).html(
          '<label>Totales</label>'
        );
        $(api.column(4).footer()).html(
          '$' + total3.toFixed(2, 2)
        );
        $(api.column(5).footer()).html(
          '$' + total4.toFixed(2, 2)
        );
        $(api.column(6).footer()).html(
          '$' + total5.toFixed(2, 2)
        );
        $(api.column(7).footer()).html(
          '$' + total6.toFixed(2, 2)
        );
        $(api.column(8).footer()).html(
          '$' + total7.toFixed(2, 2)
        );
        $(api.column(9).footer()).html(
          '$' + total8.toFixed(2, 2)
        );
        $(api.column(10).footer()).html(
          '$' + total9.toFixed(2, 2)
        );
        $(api.column(11).footer()).html(
          '$' + total10.toFixed(2, 2)
        );
        $(api.column(12).footer()).html(
          '$' + total11.toFixed(2, 2)
        );
        $(api.column(13).footer()).html(
          '$' + total12.toFixed(2, 2)
        );
        var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;
        
      }
    });
  }
  function bindTable() {
    var user= $('#id_usuario').val();
    var ordenid=$('#ordenid').val();
    var seguroid=$('#id_seguro').val();
    var facturado=$('#facturado').val();
    var ct_fecha_desde = $('#ct_fecha_desde').val();
    var ct_fecha_hasta = $('#ct_fecha_hasta').val();
    var table = $('#examples2').DataTable({
      "searching": false,
      dom: 'lBrtip',
      paging: false,
      processing: true,
      serverSide: true,
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      buttons: [
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'HUMANLABS {{$fecha}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}&idUser=" +user+"&ordenid="+ordenid+"&facturado="+facturado+"&idSeguro="+seguroid+"&ct_fecha_desde="+ct_fecha_desde+"&ct_fecha_hasta="+ct_fecha_hasta,

      "columns": [{
          "data": "id_orden",
          "orderable": false
        },{
          "data": "fecha"
        },
        {
          "data": function(data) {
            const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
            return `${data.seguro} <br/> ${nivel}`;
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
            const paciente = data.paciente;
            return `${paciente} <br> ${cedula}`;
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataEfectivo=0;
            for(i=0; i<data.length;i++){
                dataEfectivo+= data[i].dataEfectivo;
            }
            if(data.facturado == "Cierre Caja"){

              return dataEfectivo;
            }
            dataEfectivo= data.dataEfectivo;
            return dataEfectivo;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
           
          "data": function(data) {
            var dataCheque=0;
            for(i=0; i<data.length;i++){
                dataCheque+= data[i].dataCheque;
            }
            if(data.facturado == "Cierre Caja"){

              return dataCheque;
            }
            dataCheque= data.dataCheque;
            return dataCheque;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          
          "data": function(data) {
            var dataDeposito=0;
            for(i=0; i<data.length;i++){
                dataDeposito+= data[i].dataDeposito;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDeposito;
            }
            dataDeposito= data.dataDeposito;
            return dataDeposito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTransferencia=0;
            for(i=0; i<data.length;i++){
                dataTransferencia+= data[i].dataTransferencia;
            }
            if(data.facturado == "Cierre Caja"){

              return dataTransferencia;
            }
            dataTransferencia= data.dataTransferencia;
            return dataTransferencia;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataTarjetaCredito=0;
            for(i=0; i<data.length;i++){
                dataTarjetaCredito+= data[i].dataTarjetaCredito;
              }
            if(data.facturado == "Cierre Caja"){

              return dataTarjetaCredito;
            }
            dataTarjetaCredito= data.dataTarjetaCredito;
            return dataTarjetaCredito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            //console.log(data);
            var dataTarjetaDebito=0;
            for(i=0; i<data.length;i++){
                dataTarjetaDebito+= data[i].dataTarjetaDebito;
            }
            if(data.facturado == "Cierre Caja"){
             
              return dataTarjetaDebito;
            }
            dataTarjetaDebito= data.dataTarjetaDebito;
            return dataTarjetaDebito;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var pendientePago=0;
            for(i=0; i<data.length;i++){
                pendientePago+= data[i].dataPendientePago;
            }
            if(data.facturado == "Cierre Caja"){
              
              return pendientePago;
            }
            pendientePago= data.dataPendientePago;
            return pendientePago;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var online=0;
            for(i=0; i<data.length;i++){
                online+= data[i].online;
            }
            if(data.facturado == "Cierre Caja"){

              return online;
            }
            online= data.online;
            return online;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var dataDatalink=0;
            for(i=0; i<data.length;i++){
                dataDatalink+= data[i].dataDatalink;
            }
            if(data.facturado == "Cierre Caja"){
              return dataDatalink;
            }
            dataDatalink= data.dataDatalink;
            return dataDatalink;
          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            var oda=0;
            for(i=0; i<data.length;i++){
                oda+= data[i].oda;
            }
            if(data.facturado == "Cierre Caja"){

              return oda;
            }
            oda= data.oda;
            return oda;

          },
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {

            let boton1 = '<a class="btn btn-danger btn-xs" onclick="subirDocumento(' + data.id_orden + ')" ><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>';
            let boton2 = data.estadoDocs !== null ?  '<a class="btn btn-warning btn-xs" target="_blank" onclick="redigirDocs(' + data.id_orden + ')"  ><i class="fa fa-eye" aria-hidden="true"></i></a>' : '';
            return ' <div style="text-align: center;">'+boton1+''+boton2+'</div>';
          },
          "targets": 0,
          "orderable": false
        },
        {
          "data": "numero",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "usuario",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            buttonnew = "#";
            var check = "";
            var newfor = null;
            var ride="-10";
            var c1="0";
            var c2="0";
            var c3="0";
            var url_observacion = "javascript:text_observacion(" + data.id_orden + ")";
            var observacion = '<a class="btn btn-success btn-xs" href="' + url_observacion + '"> <i class="fa fa-comment"></i>';
            var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
            if (typeof(data.orden) == 'object') {
              var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
              if (typeof(data.orden.id) == 'undefined') {
                buttonnew = "#";
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
              } else {
                bs = "#";
                //console.log(data.orden);
                var newbu2 = '<a style="" class="btn btn-danger btn-xs" href="' + bs + '""> SRI </a>';
                var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
                var newfor = "-1";
                if (data.orden.fecha_envio != null && data.orden.comprobante != null) {
                  ride=data.orden.comprobante
                  ride= ride.split('-');
                  c1= parseInt(ride[0]);
                  c2= parseInt(ride[1]);
                  c3= parseInt(ride[2]);
                  console.log(c3, ride[2]);

                } else {
                  newfor = data.orden.id;
                  check = "<input type='checkbox' class='ses' value='" + data.orden.id + "' >";
                }

              }
            }
            if(c1!=0){
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a style="" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> <a class="btn btn-warning btn-xs" onclick="pdf_ride(' + c1 + ','+c2+','+c3+')" > RIDE</a> ' + observacion + ' </div>';
            }else{
              return ' <div style="text-align: center;"> ' + check + '  ' + newbu + '</i> </a><a style="" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> ' + observacion + ' </div>';
            }
          
          },
          "targets": 0,
          "orderable": false
        }

      ],
      "order": [
        [1, 'asc']
      ],
      "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        //console.log(data);

        // Remove the formatting to get integer data for summation
        var intVal = function(i) {
          return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        };
        var cases=0;
        if (data.facturado == "Cierre Caja") {
           cases=1;
        }
        //console.log(cases,data);
        // Total over all pages
       
        /*           total3 = api
          .column(4)
          .data()
          .reduce(function(a, b) {
            if(cases!=1){
              return parseFloat(a) + parseFloat(b);
            }
          
          }, 0);
         */
        total3=0;
        total4=0;
        total5=0;
        total6=0;
        total7=0;
        total8=0;
        total9=0;
        total10=0;
        total11=0;
        total12=0;
        for(i=0; i<data.length; i++){
          if(data[i].facturado!='Cierre Caja'){
            total3+=  parseFloat(data[i].dataEfectivo);
            total4+= parseFloat(data[i].dataCheque);
            total5+= parseFloat(data[i].dataDeposito);
            total6+= parseFloat(data[i].dataTransferencia);
            total7+= parseFloat(data[i].dataTarjetaCredito);
            total8+= parseFloat(data[i].dataTarjetaDebito);
            total9+= parseFloat(data[i].dataPendientePago);
            total10+= parseFloat(data[i].online);
            total11+= parseFloat(data[i].dataDatalink);
            total12+= parseFloat(data[i].oda);
          }
          
        }
        $(api.column(3).footer()).html(
          '<label>Totales</label>'
        );
        $(api.column(4).footer()).html(
          '$' + total3.toFixed(2, 2)
        );
        $(api.column(5).footer()).html(
          '$' + total4.toFixed(2, 2)
        );
        $(api.column(6).footer()).html(
          '$' + total5.toFixed(2, 2)
        );
        $(api.column(7).footer()).html(
          '$' + total6.toFixed(2, 2)
        );
        $(api.column(8).footer()).html(
          '$' + total7.toFixed(2, 2)
        );
        $(api.column(9).footer()).html(
          '$' + total8.toFixed(2, 2)
        );
        $(api.column(10).footer()).html(
          '$' + total9.toFixed(2, 2)
        );
        $(api.column(11).footer()).html(
          '$' + total10.toFixed(2, 2)
        );
        $(api.column(12).footer()).html(
          '$' + total11.toFixed(2, 2)
        );
        $(api.column(13).footer()).html(
          '$' + total12.toFixed(2, 2)
        );
        var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;
        
      }
    });
  }
  function getCierre(id_orden,date){
    $.ajax({
      type: 'get',
      url:"{{url('contable/facturacion/modal/cierre')}}/"+id_orden+"/"+date,
      datatype: 'json',
      success: function(data){
        $('#contentCierre').empty().html(data);
        $('#modalCierre').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });   
  }

  async function text_observacion(id) {
    try {
      const { value: text } = await Swal.fire({
                      input: 'textarea',
                      inputPlaceholder: 'Ingrese observacion...',
                      inputAttributes: {
                        'aria-label': 'Ingrese observacion'
                      },
                      showCancelButton: true
                    })
                    if (text) {
                        $.ajax({
                          type: 'get',
                          url:"{{ url('contable/cierre_caja/observacion/')}}/"+id,
                          datatype: 'json',
                          data: {'observacion':text},
                          success: function(data){
                            Swal.fire("Correcto!","Observacion ingresada","success");
                            //location.href ="{{route('comprobante_ingreso.index')}}";
                          },
                          error: function(data){
                            console.log(data);
                          }
                        }); 
                    }
    } catch(err) {
      console.log(err);
    }
  }

  const subirDocumento = (e) => {
    $.ajax({
        type: 'get',
        url: "{{route('subir_documento_laboratorio')}}",
        datatype: 'json',
        data: {
            'id': e
        },
        success: function (data) {
          console.log(data);
          $('#contentDocs').empty().html(data);
          $('#modalDocs').modal();
        },
        error: function (data) {
            console.log(data);
        }
    });
  }
 

  const redigirDocs = (e) =>{
    location.href =`{{url("laboratorio/subir/documento/ver")}}?id=${e}`;
}
</script>
@endsection