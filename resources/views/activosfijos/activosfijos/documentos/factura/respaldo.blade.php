@extends('activosfijos.documentos.factura.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<style>
    .input-number {
        width: 80%;
        height: 20px;
    }

    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }
</style>



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

    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">

            <div class="box-body dobra">
                <div class="header row">

                    <div class="col-md-12">
                        <div class="row">
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
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-xs-6  col-md-6  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="proveedor" class="label_header">Proveedor</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas" id="proveedor" name="proveedor" required width="100%">
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
                                    <select id="divisas" name="divisas" class="form-control select2_cuentas">
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
                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label class="label_header">Credito Tributario</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " required>
                                        <option value="">Seleccione...</option>
                                        @foreach($c_tributario as $value)
                                        <option value="{{$value->codigo}}">{{$value->codigo}}-{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
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
                    </div>

                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input name='contador_items' id='contador_items' type='hidden' value="1">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="10%" class="" tabindex="0">Codigo</th>
                                <th width="30%" class="" tabindex="0">Descripción del Activo</th>
                                <th width="10%" class="" tabindex="0">Cantidad</th>
                                <th width="10%" class="" tabindex="0">Costo</th>
                                <th width="10%" class="" tabindex="0">% Desc</th>
                                <th width="10%" class="" tabindex="0">Descuento</th>
                                <th width="10%" class="" tabindex="0">Total</th>
                                <th width="5%" class="" tabindex="0">IVA</th>
                                <th width="5%" class="" tabindex="0">
                                    <button onclick="crearFila();" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">

                        </tbody>
                        <tfoot class=''>
                            <!--tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">Subtotal 12%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr-->

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Subtotal</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>
                </div>

            </div>
        </div>































        <div class="modal fade bs-example-modal-lg " id="modal_datosactivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="datos_activo">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 style="margin:0;">Activo Fijos</h3>
                        </div>
                        <div class="modal-body">
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
                                    <select id="mdtipo" name="mdtipo" class="form-control form-control-sm select2_cuentas2" style="width: 100%;" required>
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
                                    <select id="mdgrupo" name="mdgrupo" class="form-control select2_cuentas2" style="width: 100%;" required>
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
                                    <select id="mdresponsable" name="mdresponsable" class="form-control form-control-sm select2_color" style="width: 100%;" onchange="guardar_responsable();">
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
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Marca</label>
                                <div class="col-xs-10">
                                    <select id="mdmarca" name="mdmarca" class="form-control select2_color" style="width: 100%;" required onchange="guardar_marca();">
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
                                    <select id="mdcolor" name="mdcolor" class="form-control select2_color" style="width:100%;" required onchange="guardar_color();">
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>




























        <!--MODAL-->

        <!--Modal fin-->


    </form>



</section>




<script type="text/javascript">
    $('#modal_datosactivo').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });

        $('.select2_cuentas2').select2({
            tags: false
        });

        $('.select2_color').select2({
            tags: true
        });


        crearFila();
    });


    const crearFila = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                    <button id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id})"> <i class="glyphicon glyphicon-edit"></i></button>  
                </td>
                <td>
                    <input id="codigo${id}" class="form-control" type="text" style="width: 80%;height:20px;" name="codigo[]" required>
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control" type="text" style="width: 93%;height:20px;" name="descrip_prod[]" required>
                    <textarea rows="3" name="observacion[]" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>

                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva${id}" value="1">
                    <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                    <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor">

                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        id++;
        document.getElementById('contador_items').value = id;
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)
        let val_iva = document.getElementById("val_iva" + id)
        let total_valor = document.getElementById("total_valor" + id)

        let preXcantidad = (precio * cantidad)
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        let valoriva = 0;


        if ($('#valoriva' + id).prop('checked')) {
            valoriva = precioTotal * 0.12;
        }

        let total = precioTotal + valoriva;

        val_iva.value = valoriva.toFixed(2);
        desc.value = porcDescuento.toFixed(2);
        precioneto.value = precioTotal.toFixed(2);
        total_valor.value = total.toFixed(2);

        totalGlobal();

    }

    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let vliva = document.querySelectorAll(".val_iva");
        let tot_valor = document.querySelectorAll(".total_valor");

        let total = 0;
        let descuento = 0;
        let iva = 0;
        let totv = 0;

        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value);
            // console.log(total);
            descuento += parseFloat(desc[i].value);

            iva += parseFloat(vliva[i].value);

            totv += parseFloat(tot_valor[i].value);
        }

        document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('base1').value = parseFloat(total).toFixed(2);

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

        document.getElementById('tarifa_iva').innerHTML = parseFloat(iva).toFixed(2);
        document.getElementById('tarifa_iva1').value = parseFloat(iva).toFixed(2);

        document.getElementById('total').innerHTML = parseFloat(totv).toFixed(2);
        document.getElementById('total1').value = parseFloat(totv).toFixed(2);

    }

    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
        totales(0);
    });


    function goBack() {
        window.history.back();
    }


    function _modal_activo() {
        $.ajax({
            type: 'get',
            url: "{{url('af/new/modal_activo')}}",
            datatype: 'json',
            success: function(data) {
                $('#datos_activo').empty().html(data);
                $('#modal_datosactivo').modal();
            },
            error: function(data) {
                //console.log(data);
            }
        });
    }

    function guardar_color() {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('documentofactura.guardar_color')}}",
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });

    }

    function guardar_serie() {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('documentofactura.guardar_serie')}}",
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_responsable() {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('documentofactura.guardar_responsable')}}",
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
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
</script>

<script>
    const modal_activo = (id) =>{
        console.log("Hola Mundo")
    }
</script>
@endsection