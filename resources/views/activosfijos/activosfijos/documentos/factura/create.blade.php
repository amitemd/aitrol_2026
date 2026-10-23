@extends('activosfijos.documentos.factura.base')
@section('action-content')
<style type="text/css">
    .input-number {
        width: 80%;
        height: 20px;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
            <li class="breadcrumb-item"><a href="#">Documentos</a></li>
            <li class="breadcrumb-item"><a href="javascript:goBack();">Factura Activo Fijo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Nueva Factura de Activo Fijos</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="return location.reload(true);" class="btn btn-primary btn-gray">
                    Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
    </div>
    <div class="box-body dobra">
        <form class="form-vertical" id="form">
            {{ csrf_field() }}
            <div class="header row">
                <input type="hidden" name="details_details" id="details_count" value="0"> 
                <div id="form_acc">
                    
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">Id</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="id" id="id" value="" readonly>
                        @if ($errors->has('id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero" class=" label_header">Número</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="numero" id="numero" value="" readonly>
                        @if ($errors->has('numero'))
                        <span class="help-block">
                            <strong>{{ $errors->first('numero') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo" class="label_header">Tipo</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="tipo_transaccion" id="tipo_transaccion" value="ACT-FA" readonly>
                        @if ($errors->has('tipo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento_id" class="label_header">Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="asiento_id" id="asiento_id" value="" readonly>
                        @if ($errors->has('asiento_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('asiento_id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">Fecha Asiento</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha_asiento" type="date" class="form-control" name="fecha_asiento" value="@php echo date('Y-m-d');@endphp" required>
                        @if ($errors->has('fecha_asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_caduca" class="label_header">Fecha Caduca</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha_caduca" type="date" class="form-control" name="fecha_caduca" value="@php echo date('Y-m-d');@endphp" required>
                        @if ($errors->has('fecha_caduca'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_caduca') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-6  px-1">
                    <div class="col-md-12 px-0">
                        <label for="proveedor" class="label_header">Proveedor</label>
                    </div>
                    <div class="col-md-12 px-0">
                        {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                            value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                        <select class="form-control" id="proveedor" name="proveedor" required>
                            <option value="">Seleccione...</option>
                            @foreach ($proveedor as $value)
                            <option value="{{ $value->id }}">{{ $value->razonsocial }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('proveedor') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="divisas" class="label_header">Divisas</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="divisas" name="divisas" class="form-control">
                            @foreach($divisas as $value)
                            <option value="{{$value->id}}">{{$value->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="termino" class="label_header">Término</label>
                    </div>
                    <div class="col-md-12 px-0">
                        
                        <select id="termino" name="termino" class="form-control select2_cuentas" required>
                            <option value="">Seleccione...</option>
                            @foreach($term as $t)
                                <option value="{{$t->id}}">{{$t->nombre}}</option>
                            @endforeach
                           <!--  <option value="4">30 Dias</option>
                            <option value="9">60 Dias</option>
                            <option value="9">Credito</option> -->
                        </select>
                    
                    </div>
                </div>
                <div class="col-md-2 col-xs-2 col-1 px-1">
                    <label class=" label_header">Credito Tributario</label>
                    <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " required style="width: 100%; height: 22px">
                        <option value="">Seleccione...</option>
                        @foreach($c_tributario as $value)
                        <option value="{{$value->codigo}}">{{$value->codigo}}-{{$value->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="ord_compra" class=" label_header">O. Compra</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group">
                            <input type="text" class="form-control" name="ord_compra" id="ord_compra" value="">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('ord_compra').value = '';"></i>
                            </div>
                        </div>
                        @if ($errors->has('ord_compra'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ord_compra') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="nro_autorizacion" class=" label_header">Autorización</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group">
                            <input type="text" class="form-control" name="nro_autorizacion" id="nro_autorizacion" value="">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('nro_autorizacion').value = '';"></i>
                            </div>
                        </div>
                        @if ($errors->has('nro_autorizacion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nro_autorizacion') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_compra" class=" label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="date" class="form-control col-md-12" name="fecha_compra" id="fecha_compra" value="@php echo date('Y-m-d');@endphp">
              
                        @if ($errors->has('fecha_compra'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_compra') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="serie_factura" class=" label_header">Serie</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group">
                            <input type="text" class="form-control" onkeyup="agregar_serie()" name="serie_factura" id="serie_factura" value="" maxlength="7">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie_factura').value = '';"></i>
                            </div>
                        </div>
                        @if ($errors->has('serie_factura'))
                        <span class="help-block">
                            <strong>{{ $errors->first('serie_factura') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="secuencia" class=" label_header">Secuencia</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group">
                            <input type="text" class="form-control" name="secuencia" id="secuencia" value="" onchange="ingresar_cero();" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia').value = '';"></i>
                            </div>
                        </div>
                        @if ($errors->has('secuencia'))
                        <span class="help-block">
                            <strong>{{ $errors->first('secuencia') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

          

                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo_comprobante" class="label_header">Tipo comprobante</label>
                    </div>
                    <div class="col-md-12 px-0">
                        {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                            value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                        <select class="form-control select2_cuentas" id="tipo_comprobante" name="tipo_comprobante">
                            <option value="">Seleccione...</option>
                            @foreach ($tipos_comp as $value)
                            <option value="{{ $value->codigo }}">{{ $value->codigo }} - {{ $value->nombre }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('tipo_comprobante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo_comprobante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>



            </div>
            <div class="col-md-12 table-responsive">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class='well-dark'>
                            <th width="5%" class="" tabindex="0">&nbsp;</th>
                            <th width="%" class="" tabindex="0">Codigo</th>
                            <th width="35%" class="" tabindex="0">Descripción del Activos</th>
                            <th width="10%" class="" tabindex="0">Cantidad</th>
                            <th width="10%" class="" tabindex="0">Costo</th>
                            <th width="10%" class="" tabindex="0">% Desc</th>
                            <th width="10%" class="" tabindex="0">Descuento</th>
                            <th width="10%" class="" tabindex="0">Total</th>
                            <th width="5%" class="" tabindex="0">IVA</th>
                            <th width="10%" class="" tabindex="0">
                                <button onclick="nuevo()" type="button" class="btn btn-success btn-gray" id="btn_agregaf">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                    </tbody>
                    <tfoot class='well'>
                        <tr>
                            <td colspan="5"></td>
                            <td colspan="2" class="text-right">Subtotal</td>
                            <td id="td_subtotal" class="text-right px-1">0.00</td>
                            <input type="hidden" name="subtotal" id="subtotal" value="0" class="hidden">
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td colspan="2" class="text-right">Descuento</td>
                            <td id="td_descuento" class="text-right px-1">0.00</td>
                            <input type="hidden" name="total_descuento" id="total_descuento" value="0" class="hidden">
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td colspan="2" class="text-right">Impuestos</td>
                            <td id="td_tarifa_iva" class="text-right px-1">0.00</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" value="0" class="hidden">
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td id="td_totalfinal" class="text-right px-1">0.00</td>
                            <input type="hidden" name="totalfinal" id="totalfinal" value="0" class="hidden">
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>
                </div>
            </div>
    </div>
    <div class="modal fade" id="md-activo-fijo">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Activo Fijos</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos del activo fijo</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Codigo</label>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo" name="mdcodigo" placeholder="Codigo" maxlength="16">
                                    <input type="hidden" id="mdid" name="mdid">
                                </div>
                                <div class="col-xs-1"><span>-</span></div>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo_num" name="mdcodigo_num" placeholder="Codigo" maxlength="16" onchange="ingresar_cero2();">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Nombre</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdnombre" name="mdnombre" placeholder="Nombre">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Descripción</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mddescripcion" name="mddescripcion" placeholder="Descripción">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Tipo</label>
                                <div class="col-xs-10">
                                    <select id="mdtipo" name="mdtipo" class="form-control form-control-sm select2_cuentas" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Categoria</label>
                                <div class="col-xs-10">
                                    <select id="mdgrupo" name="mdgrupo" class="form-control select2_cuentas" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($sub_tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Responsable</label>
                                <div class="col-xs-10">
                                    <select id="mdresponsable" name="mdresponsable" class="form-control form-control-sm select2_color" style="width: 100%;"  onchange="guardar_responsable();">
                                        <option value="">Seleccione...</option>
                                        @foreach($af_responsables as $responsable)
                                            <option value="{{$responsable->nombre}}">{{$responsable->nombre}}</option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Ubicación</label>
                                <div class="col-xs-10">
                                    <input type="text" name="mdubicacion" id="mdubicacion" class="form-control" placeholder="Ubicación">
                                </div>
                            </div> <br>
                            
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="modal-body">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos generales</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Marca</label>
                                <div class="col-xs-10">
                                    <select id="mdmarca" name="mdmarca" class="form-control select2_color" style="width: 100%;" required onchange="guardar_marca();" >
                                        <option value="">Seleccione...</option>
                                        @foreach($marcas as $value)
                                        <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Color</label>
                                <div class="col-xs-10">
                                    <select id="mdcolor" name="mdcolor" class="form-control select2_color" style="width:100%;" required onchange="guardar_color();" >
                                        <option value="">Seleccione...</option>
                                        @foreach($af_colores as $colores)
                                            <option value="{{$colores->nombre}}">{{$colores->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Modelo</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdmodelo" name="mdmodelo" placeholder="Modelo">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Serie</label>
                                <div class="col-xs-10">
                                    <select id="mdserie" name="mdserie" class="form-control select2_color" style="width:100%;" required onchange="guardar_serie();">
                                        <option value="">Seleccione...</option>
                                        @foreach($af_series as $series)
                                            <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Procedencia</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdprocedencia" name="mdprocedencia" placeholder="Procedencia">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Accesorios</label>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="mdaccesorios" id="mdaccesorios" value="1">
                                </div>
                            </div> <br>

                            <div class="form-group">
                                <input name='contador_items' id='contador_items' type='hidden' value="0">
                                <div class="col-md-12 table-responsive">
                                    <table id="items" class="table table table-bordered table-hover dataTable"  role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead class="thead-dark">
                                            <tr class='well-darks'>
                                                <th width="30%" tabindex="0">Nombre</th>
                                                <th width="20%" tabindex="0">Marca</th>
                                                <th width="20%" tabindex="0">Modelo</th>
                                                <th width="20%" tabindex="0">Serie</th>
                                                <th width="10%" tabindex="0">
                                                    <button type="button" class="btn btn-success btn-gray agregar_items">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="fila-fija">
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_guardarm" class="btn btn-primary" onclick="guardar_acc();">Agregar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    </form>
    </div>



    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script src="{{ asset ("/js/icheck.js") }}"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <script type="text/javascript">
        function guardar_acc(){
            let cont = document.getElementById('contador_items').value;
            let capturarId = document.getElementById('details_count').value;

            let nombre_ac = document.querySelectorAll("#nombre_ac");

            let input = "";
            for(let i = 0; i < cont ; i++){
                input += `<input name='${capturarId}_details_acc[]' id='nom_ac' type='hidden' value="${nombre_ac[i].value}">`;                
            }
           
            document.getElementById('form_acc').innerHTML = input;
            mdguardar();

        }
    </script>

    <script type="text/javascript">
        $('.select2_color').select2({
            tags: true
        });
        


        $("#proveedor").select2();
        $('#tipo_comprobante').val('01'); // Select the option with a value of '1'
        $('#tipo_comprobante').trigger('change');

        var num_row = 1;
        nuevo();

        var fila = $("#mifila").html();
        var existeCliente = false;
        $('.select2_cuentas').select2({
            tags: false
        });
        function agregar_serie() {
        var serie = $('#serie_factura').val();
        if ((serie.length) == 3) {
            $('#serie_factura').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie_factura').val('');
            swal("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
        }
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }

        function nuevo() {
            // var nuevafila = $("#mifila").html();
            var nuevafila = columna();
            // nuevafila.replace("_x", "_"+num_row);
            var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
            rowk.innerHTML = nuevafila;
            rowk.className = "well";
            $('.select2_cuentas').select2({
                tags: false
            });
            num_row += 1;
        }
       

        function md_activo(id) {
            let capturarId = document.getElementById('details_count');
            capturarId.value = id;

            if ($('#codigo_' + id).val() != null) {
                $('#mdcodigo').val($('#codigo_' + id).val());
                $('#mdcodigo_num').val($('#codigo_num_' + id).val());
                $('#mdnombre').val($('#descrip_prod_' + id).val());
                $('#mddescripcion').val($('#descripcion_' + id).val());
                // $('#mdgrupo').val($('#grupo_'+id).val());
                // GRUPO
                $('#mdgrupo').val($('#grupo_' + id).val());
                $('#mdgrupo').trigger('change');
                // TIPO
                $('#mdtipo').val($('#tipo_' + id).val());
                $('#mdtipo').trigger('change');
                // $('#mdtipo').val($('#tipo_'+id).val());
                // TIPO
                $('#mdresponsable').val($('#responsable_' + id).val());
                $('#mdresponsable').trigger('change');
                // $('#mdresponsable').val($('#responsable_'+id).val());
                // TIPO
                $('#mdproducto').val($('#producto_' + id).val());
                $('#mdproducto').trigger('change');
                // $('#mdproducto').val($('#producto_'+id).val());
                $('#mdcreditofiscal').val($('#creditofiscal_' + id).val());
                // TIPO
                $('#mdcreditofiscal').val($('#creditofiscal_' + id).val());
                $('#mdcreditofiscal').trigger('change');
                // $('#mdmarca').val($('#marca_'+id).val());
                // MARCA
                $('#mdmarca').val($('#marca_' + id).val());
                $('#mdmarca').trigger('change');

                $('#mdcolor').val($('#color_' + id).val());
                
                $('#mdmodelo').val($('#modelo_' + id).val());
                $('#mdserie').val($('#serie_activo_' + id).val());
                $('#mdaccesorios').val($('#accesorios_'+id).val());
                $('#mdprocedencia').val($('#procedencia_' + id).val());
                $('#mdubicacion').val($('#ubicacion_' + id).val());
                $('#nombre_ac').val($('#nombre_ac'+id).val());
                // $('#mdcantidad').val($('#cantidad_'+id).val());
            } else {
                $('#mdcodigo').val("");
                $('#mdnombre').val("");
                $('#mdcodigo_num').val("");
                $('#mddescripcion').val("");
                // $('#mdgrupo').val("");
                // $('#mdtipo').val("");
                $('#mdgrupo').select2();
                $('#mdtipo').select2();
                $('#mdresponsable').select2();
                $('#mdproducto').select2();
                $('#mdmarca').select2();
                // $('#mdresponsable').val("");
                // $('#mdproducto').val("");
                // $('#mdcreditofiscal').val("");
                $('#mdcolor').val("");
                $('#mdmarca').val("");
                $('#mdmodelo').val("");
                $('#mdserie').val("");
                $('#mdprocedencia').val("");
                $('#mdubicacion').val("");
                $('#nombre_ac').val("");
                $('#mdaccesorios').val("");

            }


            $('#mdid').val(id);
            $('#md-activo-fijo').modal('show');
        }


        function mdguardar() {
            var id = $('#mdid').val();
            $('#codigo_' + id).val($('#mdcodigo').val());
            $('#codigo_num_'+id).val($('#mdcodigo_num').val());
            $('#descrip_prod_' + id).val($('#mdnombre').val());
            $('#descripcion_' + id).val($('#mddescripcion').val());
            $('#grupo_' + id).val($('#mdgrupo').val());
            $('#tipo_' + id).val($('#mdtipo').val());
            $('#responsable_' + id).val($('#mdresponsable').val());
            $('#marca_' + id).val($('#mdmarca').val());
            $('#producto_' + id).val($('#mdproducto').val());
            $('#creditofiscal_' + id).val($('#mdcreditofiscal').val());
            $('#color_' + id).val($('#mdcolor').val());
            $('#modelo_' + id).val($('#mdmodelo').val());
            $('#serie_activo_' + id).val($('#mdserie').val());
            $('#procedencia_' + id).val($('#mdprocedencia').val());
            $('#ubicacion_'+id).val($('#mdubicacion').val());
            $('#accesorios_'+id).val($('#mdaccesorios').val());

            $('#md-activo-fijo').modal('hide');

        }

        function descuento(id) {
            let ids = $('#' + id).val('0');
            //alert(id)
            calcular_todo();
        }

        function calcular_todo() {
            var miTabla = document.getElementById("agregar_cuentas");
            var subtotal = "0";
            var acumdesc = "0";
            var descuento = "0";
            var cant = 0;
            var costo = 0;
            var iva = 0;
            var descpor = 0;
            var desc = 0;
            var total = 0;
            var subto = 0;
            for (i = 0; i < miTabla.rows.length; i++) {
                // item        =   miTabla.rows[i].getElementsByTagName("input")[0].value;
                cant = miTabla.rows[i].getElementsByTagName("input")[1].value;
                costo = miTabla.rows[i].getElementsByTagName("input")[2].value;
                // if(item==""){ alert("Ingrese los datos del activo fijo"); return;}
                if (cant == null || parseFloat(cant) == 0) {
                    return;
                }
                if (costo == null || parseFloat(costo) == 0) {
                    return;
                }
                subto = parseFloat(cant) * parseFloat(costo);

                descpor = miTabla.rows[i].getElementsByTagName("input")[3].value;
                desc = miTabla.rows[i].getElementsByTagName("input")[4].value;
                if (parseFloat(descpor) != 0) {
                    descuento = (parseFloat(subto) * parseFloat(descpor) / 100);
                }
                if (parseFloat(desc) != 0) {
                    descuento = parseFloat(desc);
                }
                total = parseFloat(subto) - parseFloat(descuento);
                acumdesc = parseFloat(acumdesc) + parseFloat(descuento);
                miTabla.rows[i].getElementsByTagName("input")[5].value = total;
                subtotal = parseFloat(subtotal) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[5].value);
                var ivas = miTabla.rows[i].getElementsByTagName("input")[6].checked;
                if (ivas) {
                    iva += (parseFloat((cant * costo) - descuento )  * 12) / 100;
                }
                //alert(s);

            }

            //iva = (parseFloat (subtotal) *  12)/100;    
            var totalfinal;
            $("#subtotal").val(subtotal);
            $("#td_subtotal").html(devuelvefloat(subtotal, 2));
            $("#total_descuento").val(acumdesc);
            $("#td_descuento").html(devuelvefloat(acumdesc, 2));
            $("#tarifa_iva1").val(iva);
            $("#td_tarifa_iva").html(devuelvefloat(iva, 2));
            totalfinal = parseFloat(subtotal) + parseFloat(iva);
            $("#totalfinal").val(totalfinal);
            $("#td_totalfinal").html(devuelvefloat(totalfinal, 2));
            // alert(acumcant);
        }

        function columna() {
            var columna = "";
            return columna += "<tr style='display:none' id='mifila'>" +
                "<td style='max-width:100px;'>" +
                "<button type='button' class='btn btn-info btn-gray btn-xs' onclick='md_activo(" + num_row + ")' >" +
                "<i class='glyphicon glyphicon-pencil' aria-hidden='true'></i> " +
                "</button>" +
                "</td>" +
                "<td>" +
                "<input class='form-control text-right' name='codigo[]' id='codigo_" + num_row + "' type='text' style='width: 80%;height:20px;' " +
                "readonly> " +
                "</td> " +
                "<td>" +
                "<textarea rows='3' name='descrip_prod[]' id='descrip_prod_" + num_row + "'  class='form-control px-1 desc_producto' " +
                " placeholder='Detalle del activo' readonly></textarea> " +
                "<textarea rows='2' name='observacion_prod[]' id='observacion_prod_" + num_row + "'  class='form-control px-1 desc_producto' " +
                " placeholder='Observación' readonly></textarea> " +
                "</td>" +
                "<td>" +
                "<input class='form-control text-right' name='cantidad[]' id='cantidad_" + num_row + "' type='text' style='width: 80%;height:20px;' onchange='calcular_todo()' " +
                "onkeypress='return isNumberKey(event)' " +
                "onblur='this.value=parseFloat(this.value).toFixed(0);' value='0' " +
                "required> " +
                "</td> " +
                "<td> " +
                "<input class='form-control text-right' name='costo[]' id='costo_" + num_row + "' type='text' style='width: 80%;height:20px;' onchange='calcular_todo()' " +
                "onkeypress='return isNumberKey(event)' " +
                "onblur='this.value=parseFloat(this.value).toFixed(2);' value='0' " +
                "required> " +
                "</td>" +
                "<td> " +
                "<input class='form-control text-right' name='descpor[]' id='descpor_" + num_row + "' type='text' style='width: 80%;height:20px;' onchange='descuento(\"desc_" + num_row + "\")' " +
                "onkeypress='return isNumberKey(event)' " +
                "onblur='this.value=parseFloat(this.value).toFixed(2);' value='0' " +
                "required> " +
                "</td>" +
                "<td> " +
                "<input class='form-control text-right' name='desc[]' id='desc_" + num_row + "' type='text' style='width: 80%;height:20px;' onchange='descuento(\"descpor_" + num_row + "\")' " +
                "onkeypress='return isNumberKey(event)' " +
                "onblur='this.value=parseFloat(this.value).toFixed(2);' value='0' " +
                "required> " +
                "</td> " +
                "<td> " +
                "<input class='form-control px-1 text-right' name='total[]' id='total_" + num_row + "' type='text' style='height:20px;' " +
                "onkeypress='return isNumberKey(event)' value='0.00' " +
                "onblur='this.value=parseFloat(this.value).toFixed(2);' required> " +
                "</td> " +
                "<td> " +
                "<input type='checkbox' name='iva[]' id='iva' onclick='calcular_todo()' > " +
                "</td> " +
                "<td> " +
                "<button type='button' class='btn btn-danger btn-gray delete' onclick=deleteRow(this)> " +
                "<i class='glyphicon glyphicon-trash' aria-hidden='true'></i> " +
                "</button> " +
                "<input name='descripcion[]' id='descripcion_" + num_row + "' type='hidden' >" +
                "<input name='tipo[]' id='tipo_" + num_row + "' type='hidden' >" +
                "<input name='grupo[]' id='grupo_" + num_row + "' type='hidden' >" +
                "<input name='responsable[]' id='responsable_" + num_row + "' type='hidden' >" +
                "<input name='marca[]' id='marca_" + num_row + "' type='hidden' >" +
                "<input name='producto[]' id='producto_" + num_row + "' type='hidden' >" +
                "<input name='creditofiscal[]' id='creditofiscal_" + num_row + "' type='hidden' >" +
                "<input name='color[]' id='color_" + num_row + "' type='hidden' >" +
                "<input name='modelo[]' id='modelo_" + num_row + "' type='hidden' >" +
                "<input name='serie[]' id='serie_activo_" + num_row + "' type='hidden' >" +
                "<input name='procedencia[]' id='procedencia_" + num_row + "' type='hidden' >" +
                "<input name='ubicacion[]' id='ubicacion_" + num_row + "' type='hidden' >" +
                "<input name='codigo_num[]' id='codigo_num_"+num_row+"' type='hidden'>"+
                "<input name='accesorios[]' id='accesorios_"+num_row+"' type='hidden'>"+
                "</td> " +
                "</tr>";
        }

        $(".btn_add").click(function() {
            //console.log($("#form").serialize())
            if ($("#form").valid()) {
                $(".print").css('visibility', 'visible');
                $(".btn_add").attr("disabled", true);
                $(".delete").attr("disabled", true);
                $("#mifila").html("");
                $.ajax({
                    url: "{{route('afDocumentoFactura.store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: $("#form").serialize(),
                    success: function(data) {
                        // console.log(data);
                        $("#asiento_id").val(data.asiento);
                        $("#id").val(data.id);
                        $("#numero").val(data.numero);
                        swal("Registro agregado con éxito");
                        $('#form :input').prop('readonly', true);
                        $('#iva').prop('disabled', true);
                        $('select').prop('disabled', true);
                        $('input').prop('readonly',true);
                        $('#btn_guardarm').attr("disabled", true);
                        $('#btn_agregaf').attr("disabled",true);


                    },
                    error: function(data) {
                        console.log(data.responseText);
                    }
                });
            } else {
                swal("Tiene campos vacios");
                console.log($("#form").serialize());
            }

        });


        function goBack() {
            window.history.back();
        }

        function devuelvefloat(cant, decimales) {
            var tmp = null;
            $.ajax({
                url: "{{route('transferenciabancaria.devuelvefloat')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                async: false,
                data: {
                    cantidad: cant,
                    decimales: decimales
                },
                success: function(data) {
                    tmp = data.valor;
                },
                error: function(data) {
                    console.error(data.responseText);
                }
            });
            return tmp;
        }
    </script>


    <script type="text/javascript">
        function deleteRow(row) {
            var d = row.parentNode.parentNode.rowIndex;
            calcular_todo();
            document.getElementById('example2').deleteRow(d);
        }



        function guardar_color(){

            $.ajax({
                type: 'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url:"{{route('documentofactura.guardar_color')}}",
                data: $("#form").serialize(),
                datatype: 'json',
                success: function(data){
                    console.log(data);
                    //alert(data)
                },
                error: function(data){
                    //console.log(data);
                    //alert(data)
                }
            }); 
            
        }

        function guardar_serie(){

            $.ajax({
                type: 'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url:"{{route('documentofactura.guardar_serie')}}",
                data: $("#form").serialize(),
                datatype: 'json',
                success: function(data){
                    console.log(data);
                    //alert(data)
                },
                error: function(data){
                    //console.log(data);
                    //alert(data)
                }
            });
        }

        function guardar_responsable(){
            $.ajax({
                type: 'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url:"{{route('documentofactura.guardar_responsable')}}",
                data: $("#form").serialize(),
                datatype: 'json',
                success: function(data){
                    console.log(data);
                    //alert(data)
                },
                error: function(data){
                    //console.log(data);
                    //alert(data)
                }
            });
        }


        function ingresar_cero() {
            var secuencia_factura = $('#secuencia').val();
            var digitos = 9;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 10) {
                    swal("Error!", "Valor no permitido", "error");
                    $('#secuencia').val('');

                } else {

                    var concadenate = parseInt(digitos - longitud);
                    switch (longitud) {
                        case 1:
                            secuencia = '00000000';
                            break;
                        case 2:
                            secuencia = '0000000';
                            break;
                        case 3:
                            secuencia = '000000';
                            break;
                        case 4:
                            secuencia = '00000';
                            break;
                        case 5:
                            secuencia = '0000';
                            break;
                        case 6:
                            secuencia = '000';
                            break;
                        case 7:
                            secuencia = '00';
                            break;
                        case 8:
                            secuencia = '0';
                            break;
                        case 9:
                            secuencia = '';
                    }
                    $('#secuencia').val(secuencia + secuencia_factura);
                }


            } else {
                swal("Error!", "Valor no permitido", "error");
                $('#secuencia').val('');
            }


        }

        function ingresar_cero2() {
            var secuencia_factura = $('#mdcodigo_num').val();
            var digitos = 6;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 7) {
                    swal("Error!", "Valor no permitido", "error");
                    $('#mdcodigo_num').val('');

                } else {

                    var concadenate = parseInt(digitos - longitud);
                    switch (longitud) {
                        case 1:
                            secuencia = '00000';
                            break;
                        case 2:
                            secuencia = '0000';
                            break;
                        case 3:
                            secuencia = '000';
                            break;
                        case 4:
                            secuencia = '00';
                            break;
                        case 5:
                            secuencia = '0';
                            break;
                        case 6:
                            secuencia = '';
                    }
                    $('#mdcodigo_num').val(secuencia + secuencia_factura);
                }


            } else {
                swal("Error!", "Valor no permitido", "error");
                $('#mdcodigo_num').val('');
            }
        }


        function guardar_marca(){

            $.ajax({
                type: 'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url:"{{route('documentofactura.guardar_marca')}}",
                data: $("#form").serialize(),
                datatype: 'json',
                success: function(data){
                    console.log(data);
                    //alert(data)
                },
                error: function(data){
                    //console.log(data);
                    //alert(data)
                }
            });
        }


        $('.agregar_items').on('click', function() {
            agregar_items();
        });

        function deleteRow(btn) {
            var row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }

        function agregar_items() {
            var id = document.getElementById('contador_items').value;
            var tr = `<tr class="columnas"> 
                            <td>
                                <input required name="nombre_ac[]" id="nombre_ac${id}" class="form-control" style="height:25px;width:90%;" autocomplete="off">
                            </td>

                            <td>
                                <select id="marca_ac${id}" name="marca_ac[]"  class="form-control select2_color"  onchange="guardar_marca();" style="width:80%; height:25px;">
                                    <option value="">Seleccione..</option>
                                    @foreach($marcas as $value)
                                        <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td> 
                                <input type="text" name="modelo_ac[]" id="modelo_ac${id}" class="form-control cant" style="height:25px;width:75%;" autocomplete="off">
                            </td>

                            <td>
                                <select id="serie_ac${id}" name="serie_ac[]" class="form-control select2_color" onchange="guardar_serie();" style="width:80%; height:25px;">
                                    <option value="">Seleccione</option>
                                    @foreach($af_series as $series)
                                        <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                            </td>                    
                        </tr> `;
            $('#items').append(tr);
            $('.select2_color').select2({
                tags: true
            });

            var ids = id;
            id++;
            document.getElementById('contador_items').value= id;

        }
    
    </script>

</section>
@endsection