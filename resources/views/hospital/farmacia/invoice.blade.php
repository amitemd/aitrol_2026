@extends('layouts.app-template-h')
@section('content')
<style type="text/css">
    .input-number {
        width: 80%;
        height: 20p x;
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
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<!--created by A. Chilan-->
<div class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Ventas')}}</a></li>
            <li class="breadcrumb-item"><a href="../ventas">{{trans('wfact_venta.Registro de Factura de Ventas')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('wfact_venta.nueva')}}</li>
        </ol>
    </nav>
    <div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" id="content">

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="card-title">{{trans('wfact_venta.nueva_fact_venta')}}</h3>
                    </div>
                    <div class="col-md-1 text-right">
                        <button onclick="window.location.reload(false);" class="btn btn-primary btn-gray btn-sm">
                            {{trans('wfact_venta.nuevo')}}
                        </button>
                    </div>
                    <div class="col-md-1 text-right">
                        <button onclick="goBack()" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('wfact_venta.regresar')}}
                        </button>
                    </div>
                </div>

            </div>

        </div>
        <div class="card-body dobra">
            <form class="form-vertical" id="form">
                {{ csrf_field() }}
                <div class="header row">
                    <div class="form-group col-xs-6 col-md-2">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" control-label">{{trans('wfact_venta.Id')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="id" id="id" value="">
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" control-label">{{trans('wfact_venta.Numero')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="numero" id="numero" value="" onchange="ingresar_cero()">
                            @if ($errors->has('numero'))
                            <span class="help-block">
                                <strong>{{ $errors->first('numero') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="control-label">{{trans('wfact_venta.Tipo')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="tipo" id="tipo" value="VEN-FA" readonly>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6 col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="control-label">{{trans('wfact_venta.Asiento')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control" name="asiento" id="asiento" readonly value="">
                            @if ($errors->has('asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="control-label">{{trans('wfact_venta.Fecha')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha" type="text" class="form-control input-sm flatpickr-date-time active" name="fecha_asiento" value="{{date('Y-m-d H:i:s')}}" required>
                            @if ($errors->has('fecha_asiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="orden_venta" class="control-label">{{trans('wfact_venta.orden_venta')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="orden_venta" type="text" class="form-control" name="orden_venta" value="{{ old('orden_venta') }}">
                            @if ($errors->has('orden_venta'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="empresa" class="control-label">{{trans('wfact_venta.empresa')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="empresa" type="text" class="form-control" name="empresa" value="{{ $empresa->nombrecomercial }}" readonly>
                            @if ($errors->has('orden_venta'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-1 ">
                        <div class="col-md-12 px-0">
                            <label for="sucursal" class="control-label">{{trans('wfact_venta.sucursal')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                <option value="">{{trans('wfact_venta.seleccione')}}</option>
                                @foreach($sucursales as $value)
                                <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-1 ">
                        <div class="col-md-12 px-0">
                            <label for="punto_emision" class="control-label">{{trans('wfact_venta.p_emision')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="punto_emision" id="punto_emision" required>
                                <option value="">{{trans('wfact_venta.seleccione')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="numero_autorizacion" class="control-label"># {{trans('wfact_venta.autorizacion')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="numero_autorizacion" type="text" class="form-control" name="numero_autorizacion" value="{{ old('numero_autorizacion') }}">
                            @if ($errors->has('orden_venta'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_asiento') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="divisas" class="control-label">{{trans('wfact_venta.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select id="divisas" name="divisas" class="form-control">
                                @foreach($divisas as $value)
                                <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="recaudador" class="control-label">{{trans('wfact_venta.recaudador')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select id="recaudador" name="recaudador" class="form-control">
                                <option value="">{{trans('wfact_venta.seleccione')}}</option>
                                @foreach($user_recaudador as $value)
                                <option value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" class="form-control input-sm" name="cedula_recaudador" id="cedula_recaudador" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>
                    <div class="form-group col-xs-6  col-md-2 ">
                        <div class="col-md-12 px-0">
                            <label for="vendedor" class="control-label">{{trans('wfact_venta.vendedor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select id="vendedor" name="vendedor" class="form-control">
                                <option value="">{{trans('wfact_venta.seleccione')}}</option>
                                @foreach($user_vendedor as $value)
                                <option value="{{$value->nombre1}} {{$value->apellido1}}" data-id="{{$value->id}}" data-name="{{$value->nombre1}} {{$value->apellido1}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" class="form-control input-sm" name="cedula_vendedor" id="cedula_vendedor" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="cliente" class="control-label text-left">{{trans('wfact_venta.Cliente')}}</label>
                    </div>
                    <div class="col-md-2 form-group">
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" autocomplete="off" value="{{ old('identificacion_cliente') }}" onchange="reloadCliente(this)" required placeholder="{{trans('wfact_venta.identificacion')}}">
                        @if ($errors->has('identificacion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 form-group">
                        <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" autocomplete="off" value="{{ old('nombre_cliente') }}" onchange="reloadCliente()" required placeholder="{{trans('wfact_venta.nombre')}}">
                        @if ($errors->has('nombre_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 form-group">
                        <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente" value="{{ old('direccion_cliente') }}" required placeholder="{{trans('wfact_venta.direccion')}}">
                        @if ($errors->has('direccion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 form-group">
                        <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente" value="{{ old('ciudad_cliente') }}" required placeholder="{{trans('wfact_venta.ciudad')}}">
                        @if ($errors->has('ciudad_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 form-group">
                        <input id="mail_cliente" type="text" class="form-control" name="mail_cliente" value="{{ old('mail_cliente') }}" required placeholder="{{trans('wfact_venta.correo')}}">
                        @if ($errors->has('mail_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_cliente') }}</strong>
                        </span>
                        @endif

                    </div>
                    <div class="col-md-2 form-group">
                        <input type="hidden" name="valid" id="valid" value="0">
                        <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente') }}" required placeholder="{{trans('wfact_venta.telefono')}}">
                        <input id="tipo_cliente" type="hidden" class="form-control" name="tipo_cliente" value="{{ old('tipo_cliente') }}" required placeholder="tipo">
                        <!--<span style="color: white;">{{$t_factura}}</span>-->
                        @if ($errors->has('telefono_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono_cliente') }}</strong>
                        </span>
                        @endif
                        <input type="hidden" value="{{$iva->iva}}" name="ivareal" id="ivareal" class="form-control">

                    </div>
                    <div class="col-md-12">
                        <label class="control-label text-left">{{trans('wfact_venta.informacion_adicional')}}</label>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">{{trans('wfact_venta.direccion')}}</label>
                        <input name="direccioninfo" type="text" class="form-control" placeholder="{{trans('wfact_venta.ingrese_direccion')}}">
                    </div>
                    <div class="col-md-2 ">
                        <label class="control-label">Email</label>
                        <input name="emailinfo" type="text" class="form-control" placeholder="{{trans('wfact_venta.ingrese_email')}}">
                    </div>
                    <div class="col-md-2 ">
                        <label class="control-label">{{trans('wfact_venta.Paciente')}}</label>
                        <input type="text" class="form-control" name="pacienteinfo" placeholder="{{trans('wfact_venta.ingrese_paciente')}}">
                    </div>
                    <div class="col-md-2 ">
                        <label class="control-label">{{trans('wfact_venta.seguro')}}</label>
                        <input type="text" class="form-control" name="segurosinfo" placeholder="{{trans('wfact_venta.ingrese_seguros')}}">
                    </div>
                    <div class="col-md-2 ">
                        <label class="control-label">{{trans('wfact_venta.Procedimiento')}}</label>
                        <input type="text" class="form-control" name="procedimientoinfo" placeholder="{{trans('wfact_venta.ingrese_procedimiento')}}">
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">{{trans('wfact_venta.fecha_procedimiento')}}</label>
                        <input type="text" class="form-control input-sm flatpickr-date-time active" name="fechaprocedimiento">
                    </div>

                </div>

                @php

                $id_auth = Auth::user()->id_tipo_usuario;
                $contador=0;
                //dd($pacientes);
                @endphp

             
                <div class="col-md-12 table-responsive" style=" height: 300px; overflow-y: scroll; padding-top: 10px;">

                    <input type="hidden" name="tipo_factura" value="1">
                    <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                        <thead>
                            <tr class="well-dark">
                                <th width="35%" tabindex="0">{{trans('wfact_venta.descri_producto')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.cantidad')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.precio')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.cobrar_seguro_item')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.porcentaje_descuento')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.descuento')}}</th>
                                <th width="10%" tabindex="0">{{trans('wfact_venta.precio_neto')}}</th>
                                <th width="5%" tabindex="0">{{trans('wfact_venta.iva')}}</th>
                                <th width="10%" tabindex="0">
                                    <button type="button" class="btn btn-warning btn-gray anadir btn-sm" onclick="nuevox()"> <i class="fa fa-plus"></i> </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="entrega">
                            
                            <tr>
                                <td style="max-width:100px;">

                                    <input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <input type="hidden" class="id_patient" value="">
                                    <input type="hidden" name="verid[]" class="verid" value="1">
                                    <select class="form-control select2 productos"  name="nombre[]" required onchange="verificar(this)">
                                    <option value="">{{trans('wfact_venta.seleccione')}}</option>
                                    @foreach($productos as $value)
                                        <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-valor="{{$value->precio1}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                        @endforeach
                                       
                                    </select>
                                    <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="{{trans('wfact_venta.detalle_prod')}}"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                    <input type="hidden" name="bodega[]" value="">
                                    <input type="hidden" name="id_inv_serie[]" value="">
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" name="cantidad[]" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                </td>
                                <td>
                                 
                                    <input type="text" class="form-control pneto" name="precio[]" onkeypress="return isNumberKey(event)" style="display:inline;" value="0.00">
                                   
                                </td>
                                <td>
                                    <input  name="copago[]" class="form-control text-right copago" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text"  onkeypress="return isNumberKey(event)" name="descpor[]"  onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                    <input name="maxdesc[]" class="form-control text-right maxdesc" type="hidden"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text"  onkeypress="return isNumberKey(event)" name="desc[]" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                                </td>
                                <td>

                                    <input class="form-control px-1 text-right" type="text"  onkeypress="return isNumberKey(event)" name="precioneto[]" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" required>
                                </td>
                                <td>
                                    <input class="form luffy" type="checkbox">

                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray btn-sm delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>

                </div>

                
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr style="display: none;">
                            <!--<th width="10%" class="" tabindex="0">Codigo</th>-->
                            <th width="35%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="10%" tabindex="0"></th>
                            <th width="5%" tabindex="0"></th>
                            <th width="10%" tabindex="0">

                            </th>
                        </tr>
                    </thead>

                    <tbody id="agregar_cuentas">

                        <input type="hidden" name="ti_factura" value="1" />


                    </tbody>
                    <tfoot class='well'>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal 12%</td>
                            <td id="subtotal_12" class="text-right">0.00</td>
                            <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal 0%</td>
                            <td id="subtotal_0" class="text-right">0.00</td>
                            <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('wfact_venta.descuento')}}</td>
                            <td id="descuento" class="text-right">0.00</td>
                            <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('wfact_venta.subtotal_sin_impuesto')}}</td>
                            <td id="base" class="text-right">0.00</td>
                            <input type="hidden" name="base1" id="base1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('wfact_venta.tarifa_iva')}} 12%</td>
                            <td id="tarifa_iva" class="text-right">0.00</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                        </tr>
                        <!--<tr>
                                <td></td><td></td><td></td><td></td><td></td>
                                <td colspan="2" class="text-right">Transporte</td>
                                <td id="transporte" class="text-right">0.00</td>
                                <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                         </tr>-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('wfact_venta.total')}}</strong></td>
                            <td id="total" class="text-right">0.00</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('wfact_venta.por_cobrar_seguro')}}</strong></td>
                            <td id="copagoTotal" class="text-right">0.00</td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>
                </table>
                <div class="col-md-12" style="height:30px;">
                    <div class="row head-title">
                        <div class="col-md-12 cabecera">
                            <label class="color_texto">{{trans('wfact_venta.forma_pago')}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 table-responsive ">
                    <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                    <table id="example1" role="grid" class="table table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">

                        <thead>
                            <tr>
                                <th width="20%" style="text-align: center;">{{trans('wfact_venta.metodo')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.Fecha')}}</th>
                                <th width="15%" style="text-align: center;">{{trans('wfact_venta.tipo_tarjeta')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.Numero')}}</th>
                                <th width="15%" style="text-align: center;">{{trans('wfact_venta.banco')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.cuenta')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.girado_a')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.valor')}}</th>
                                <th width="10%" style="text-align: center;">{{trans('wfact_venta.valor_base')}}</th>
                                <th width="5%" style="text-align: center;"><button id="btn_pago" type="button" class="btn btn-success btn-sm btn-gray">

                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button></th>
                            </tr>
                        </thead>
                        <tbody id="agregar_pago">

                            <tr style="display:none" id="mifila">

                                <td style="max-width:100px;">
                                    <input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select name="nombre[]" class="form-control select" style="width:100%;" required onchange="verificar(this)">
                                        <option value="">{{trans('wfact_venta.seleccione')}}</option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-valor="{{$value->precio1}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                        @endforeach

                                    </select>
                                    <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control desc_producto" placeholder="{{trans('wfact_venta.detalle_prod')}}"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                    <input type="hidden" name="bodega[]" value="">
                                    <input type="hidden" name="id_inv_serie[]" value="">
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                </td>
                                <td>
                                    <select name="precio[]" class="form-control select2_precio pneto" style="width:60%;display:inline;" required>
                                        <option value="0"> </option>


                                    </select>
                                    <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                        <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                    </button>
                                </td>
                                <td>
                                    <input class="form-control text-right copago" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right" type="text" style="" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>


                </div>
                <div class="form-group col-xs-12" style="text-align: center;">
                    <div class="col-md-12 col-md-offset-4" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn-gray btn-sm btn_add">
                            <i class="fa fa-save" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('wfact_venta.guardar')}}
                        </button>
                    </div>
                </div>
            </form> 
        </div>
    </div>

    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/icheck.js") }}"></script>
    <script src="{{ asset ('/js/jquery-ui.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

    <script type="text/javascript">
        var fila = $("#mifila").html();
        var existeCliente = false;
        //function use tap with select2
        $(document).on('keydown', '.select2', function(e) {
            if (e.originalEvent && e.which == 40) {
                e.preventDefault();
                $(this).siblings('select').select2('open');
            }
        });
            $("#nombre_cliente").autocomplete({

                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarcliente')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                change: function(event, ui) {
                    $("#identificacion_cliente").val(ui.item.id);
                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);
                    $("#tipo_cliente").val(ui.item.tipo);
                    $('.select').trigger('change');
                },
                selectFirst: true,
                minLength: 1,
            });

            $("#identificacion_cliente").autocomplete({

                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarclientexid')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            //console.log("identificacion_cliente", data.id);
                            if (data.id == "") {
                                //swal("el cliente no existe");
                                existeCliente = false;
                            } else {
                                existeCliente = true;
                            }
                        }
                    });
                },
                change: function(event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);

                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);
                    $("#tipo_cliente").val(ui.item.tipo);

                },
                selectFirst: true,
                minLength: 1,
            });

            $("#identificacion_paciente").autocomplete({

                source: function(request, response) {
                    $.ajax({
                        type: 'GET',
                        url: "{{route('ventas.buscarpaciente')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            if (data.id == "") {
                                //swal("el cliente no existe");
                                existeCliente = false;
                            }
                        }
                    });
                },
                change: function(event, ui) {
                    if (ui.item != null) {
                        //console.log(ui.item);
                        $("#nombre_paciente").val(ui.item.nombre);
                        $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                    } else {
                        swal("No Existe el paciente");
                    }
                    /*$("#direccion_cliente").val(ui.item.direccion);
                    $("#ciudad_cliente").val(ui.item.ciudad);
                    $("#mail_cliente").val(ui.item.mail);
                    $("#telefono_cliente").val(ui.item.telefono);*/
                },
                selectFirst: true,
                minLength: 1,
            });



        $('body').on('click', '.delete', function() {
            //console.log($(this));

            $(this).parent().parent().remove();
            totales(0);
        });
        $('body').on('click', '.chx', function() {
            //console.log($(this));

            //$(this).parent().parent().remove();
            totales(0);
        });

        $('body').on('click', '.previewomni', function() {
            //$(this).html("ocultar");
            var texto = $(this).html();
            //console.log(texto);
            if (texto == "Ver datos") {
                $(this).html("Ocultar");
                $(this).parent().find('.datos').show();
            } else {
                $(this).html("Ver datos");
                $(this).parent().find('.datos').hide();
            }
        });
        $(document).ready(function() {


            limpiar();

            $('.select2_cliente').select2({
                tags: false
            });

            $('#iva').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%'
            });

            $(".select").trigger('change');

            //$(".pneto").change();
            //verificar();
        });
        //totales(0);
        $('body').on('click', '.cp', function() {
            //console.log($(this));
            //console.log($(this).prev().attr('class'));
            var clase = $(this).prev().attr('class');
            var html = '<input type="text" class="form-control pneto" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" style="width:40%;display:inline;height:20px;">' +
                '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
                '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
                '</button>';
            //console.log($(this).parent());
            if (clase.includes('select2_precio')) {
                $(this).parent().append(html);
                $(this).prev().remove();
                $(this).remove();

            } else {
                html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" autofocus active required>' +
                    '<option value="0"> </option></select>' +
                    '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                // $(this).parent().empty();
                // $(this).parent().append(html);
                $(this).parent().append(html);
                verificar($(this).parent().prev().prev().children().closest('.select'));
                $(this).prev().remove();
                $(this).remove();

            }


        });
        /*function changePrecio(i, e){
            var html = '<input type="text" class="form-control pneto" style="width:40%;display:inline;height:20px;">'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,2)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>'+
                        '</button>';
                        console.log(i.parent());
            if(e==1){
                
                $("#tprecio").empty();
                $("#tprecio").append(html);
            }else{
                html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" required>'+
                        '<option value="0"> </option></select>'+
                        '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,1)">'+
                        '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
                $("#tprecio").empty();
                $("#tprecio").append(html);
                console.log($("#tprecio").prev().prev().children().closest('.select'));
                verificar($("#tprecio").prev().prev().children().closest('.select'));
            }
        }*/

        //Desabilita Componentes
        function desabilita_componente(elemento, id) {

            var id_tipo = parseInt(elemento.value);

            if ((id_tipo == 1) || (id_tipo == 4) || (id_tipo == 5) || (id_tipo == 6)) {

                $('#fecha' + id).val("");

                document.getElementById('fecha' + id).disabled = true;
                document.getElementById('numero' + id).disabled = true;
                document.getElementById('id_banco' + id).disabled = true;
                document.getElementById('id_cuenta_pago' + id).disabled = true;
                document.getElementById('giradoa' + id).disabled = true;

            } else {

                if ((id_tipo == 2) || (id_tipo == 3)) {

                    document.getElementById('fecha' + id).disabled = false;
                    document.getElementById('numero' + id).disabled = false;
                    document.getElementById('id_banco' + id).disabled = false;
                    document.getElementById('id_cuenta_pago' + id).disabled = false;
                    document.getElementById('giradoa' + id).disabled = false;

                }

            }
        }

        function ingresar_cero() {
            var secuencia_factura = $('#numero').val();
            secuencia_factura = secuencia_factura.trim();
            var digitos = 9;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 10) {
                    swal("{{trans('wfact_venta.error')}}", "{{trans('wfact_venta.valor_no_permitido')}}", "error");
                    $('#numero').val('');

                } else {
                    var concadenate = parseInt(digitos - longitud);
                    switch (longitud) {
                        case 1:
                            secuencia = '000000000';
                            break;
                        case 2:
                            secuencia = '00000000';
                            break;
                        case 3:
                            secuencia = '0000000';
                            break;
                        case 4:
                            secuencia = '000000';
                            break;
                        case 5:
                            secuencia = '00000';
                            break;
                        case 6:
                            secuencia = '0000';
                            break;
                        case 7:
                            secuencia = '000';
                            break;
                        case 8:
                            secuencia = '00';
                            break;
                        case 9:
                            secuencia = '0';
                    }
                    $('#numero').val(secuencia + secuencia_factura);
                }


            } else {
                swal("{{trans('wfact_venta.error')}}", "{{trans('wfact_venta.valor_no_permitido')}}", "error");
                $('#numero').val('');
            }
        }

        function verificar(e) {
            var iva = $('option:selected', e).data("iva");
            var codigo = $('option:selected', e).data("codigo");
            var usadescuento = $('option:selected', e).data("descuento");
            var max = $('option:selected', e).data("maxdesc");
            var modPrecio = $('option:selected', e).data("precio");
            var valorInicial = $('option:selected', e).data("valor");

            $(e).parent().children().closest(".codigo_producto").val(codigo);
            $(e).parent().children().closest(".iva").val(iva);
            if ($(e).parent().next().next().children().closest(".pneto").is('input') && valorInicial !== undefined) {
                $(e).parent().next().next().children().closest(".pneto").val(parseFloat(valorInicial || 0).toFixed(2));
            }
            //console.log($(e).parent().next().next().children().closest(".cp"));

            if (modPrecio) {
                //$(e).parent().next().next().closest(".cp");
                //console.log("modifica precio");
                $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
            } else {
                //console.log("no modifca el precio");
                $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
            }
            if (!usadescuento) {
                $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
                $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
                $(e).parent().next().next().next().next().children().val(0);
                $(e).parent().next().next().next().next().next().children().val(0);
            } else {
                $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
                $(e).parent().next().next().next().next().children().removeAttr("readonly");
                $(e).parent().next().next().next().next().next().children().val(0);
                $(e).parent().next().next().next().next().children().val(0);
            }
            $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);

            if (iva == '1') {
                $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
            } else {
                $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
            }

            //cargarPrecios
            var tipo = $("#tipo_cliente").val();
            var selected = "";

            $.ajax({
                type: 'post',
                url: "{{route('precios')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    id: codigo
                },
                success: function(data) {
                    $(e).parent().next().next().children().find('option').remove();
                    $.each(data, function(key, value) {
                        if (tipo == value.nivel) {
                            selected = "selected";
                        } else {
                            selected = "";
                        }
                        $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
                    });
                    //totales(0);
                    $(".cneto").change();
                },
                error: function(data) {
                    console.log(data.responseText);
                }
            });

        }

        //cantidad
        //precio
        //copago
        //%descuento
        //descuento
        //precioneto
        $('body').on('blur', '.pneto', function() {
            // verificar(this);
            var cant = $(this).parent().prev().children().val();
            var copago = $(this).parent().next().children().val();
            var descuento = $(this).parent().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
            $(this).parent().next().next().next().next().children().val(total.toFixed(2));
            totales(0);
        });
        $('body').on('active', '.pneto', function() {
            // verificar(this);
            var cant = $(this).parent().prev().children().val();
            var copago = $(this).parent().next().children().val();
            var descuento = $(this).parent().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
            $(this).parent().next().next().next().next().children().val(total.toFixed(2));
            totales(0);
        });
        $('body').on('change', '.pneto', function() {
            // verificar(this);
            var cant = $(this).parent().prev().children().val();
            var copago = $(this).parent().next().children().val();
            var descuento = $(this).parent().next().next().next().children().val();
            var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
            $(this).parent().next().next().next().next().children().val(total.toFixed(2));
            totales(0);
        });
        $('body').on('change', '.cneto', function() {
            // verificar(this);
            var cant = $(this).val();
            var precio = $(this).parent().next().children().val();
            // console.log("this", $(this).parent().next().children().val());
            var copago = $(this).parent().next().next().children().val();
            //console.log("copago", copago);
            var descuento = $(this).parent().next().next().next().next().children().val();
            var total = (parseInt(cant) * (precio)) - descuento - copago;
            $(this).parent().next().next().next().next().next().children().val(total.toFixed(2, 2));

            totales(0);
        });

        $('body').on('change', '.copago', function() {
            verificar(this);
            var cant = $(this).parent().prev().prev().children().val();
            var precio = $(this).parent().prev().children().val();

            var copago = $(this).val();
            //console.log("copago", copago);
            var descuento = $(this).parent().next().next().children().val();
            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
            //console.log(total);
            $(this).parent().next().next().next().children().val(total.toFixed(2));

            totales(0);
        });


        $('body').on('change', '.pdesc', function() {

            var m = $(this).next().val();
            var cant = $(this).parent().prev().prev().prev().children().val();
            var precio = $(this).parent().prev().prev().children().val();
            var pdesc = $(this).val();
            //console.log("el descuento maximo debe de ser", m, pdesc);
            if (parseFloat(pdesc) > parseFloat(m)) {
                swal("El descuento no puede ser mayor a " + m + "%");
                $(this).val(m).focus();

            }
            var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
            $(this).parent().next().children().val(descuento.toFixed(2));
            var copago = $(this).parent().prev().children().val();
            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
            $(this).parent().next().next().children().val(total.toFixed(2));
            totales(0);
        });
        $('body').on('change', '.desc', function() {
            var m = verificar(this);
            var cant = $(this).parent().prev().prev().prev().prev().children().val();
            var precio = $(this).parent().prev().prev().prev().children().val();
            /*if(pdesc> m){
                swal("El descuento no puede ser mayor a "+m+"%");
                $(this).val(m);
            }*/
            var descuento = $(this).val();
            verificar(this);
            //console.log(cant, precio);
            var pdesc = 0;
            if (cant == 0 || precio == 0) {
                pdesc = 0;
            } else {
                pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
            }
            //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
            $(this).parent().prev().children().val(pdesc);
            var copago = $(this).parent().prev().prev().children().val();
            var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
            $(this).parent().next().children().val(total.toFixed(2));
            totales(0);
        });

        $('body').on('change', '.fpago', function() {
            var total_pagos = 0;
            $('.fpago').each(function(i, obj) {
                total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
            });
            $("#valor_totalPagos").val(total_pagos);
        });

        function totales(e) {
            var subt12 = [];
            var subt0 = [];
            var descuento = [];
            var sb12 = 0;
            var sb0 = 0;
            var d = 0;
            var copagoTotal = 0;
            if (e == 0) {
                $('.cneto').each(function(i, obj) {
                    var cant = $(this).val();
                    var e = $(this).parent().prev().children().closest(".select");
                    var precio1 = 0;
                    var precio2 = 0;
                    var precio3 = 0;
                    var precio4 = 0;
                    var precio5 = 0;
                    var precioAut = 0;
                    var tipo = $("#tipo_cliente").val();
                    //console.log("el e es: ", e.val());


                    var precio = $(this).parent().next().children().val();
                    //console.log(precio + " el precio es ");
                    if (precio == null) {
                        precio = 0;
                    }
                    var copago = $(this).parent().next().next().children().val();
                    var descuento = $(this).parent().next().next().next().next().children().val();
                    d = parseFloat(d) + parseFloat(descuento);
                    var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                    //console.log(iva);
                    var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);
                    if (iva == 1) {
                        //console.log(subt12);
                        subt12.push(total);
                        sb12 = sb12 + total;
                    } else {
                        subt0.push(total);
                        sb0 = sb0 + total;
                    }
                    copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                    //aqui falta
                    $("#subtotal_12").html(sb12.toFixed(2));
                    $("#subtotal_0").html(sb0.toFixed(2));
                    $("#descuento").html(d.toFixed(2));
                    $("#base").html(sb12.toFixed(2));
                    var iva = $("#ivareal").val();
                    var ti = iva * sb12;
                    $("#tarifa_iva").html(ti.toFixed(2));
                    var t = sb12 + sb0 + ti - d;
                    $("#total").html(t.toFixed(2));
                    $("#copagoTotal").html(copagoTotal.toFixed(2));
                    $("#subtotal_121").val(sb12.toFixed(2));
                    $("#subtotal_01").val(sb0.toFixed(2));
                    $("#descuento1").val(d.toFixed(2));
                    $("#tarifa_iva1").val(ti.toFixed(2));
                    $("#total1").val(t.toFixed(2));
                    $("#totalc").val(copagoTotal.toFixed(2));
                });
            }
        }

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }   
        
        function nuevox() {
            var nuevafila = $("#mifila").html();
            var rowk = document.getElementById("entrega").insertRow(-1);
            //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
            rowk.innerHTML = fila;
            //rowk.className="well";
            $('.select2_cuentas').select2({
                tags: false
            });
        }

        function nuevof(id, patient, procedimiento, id_agenda) {
            var nuevafila = $("#mifila").html();
            var rowk = document.getElementById("agregar_cuentas" + id).insertRow(-1);
            //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
            rowk.innerHTML = fila;
            var input = document.createElement('input');
            var input2 = document.createElement('input');
            var input3 = document.createElement('input');
            input.type = 'hidden';
            input.name = "paciente[]";
            input.value = patient;
            input2.type = 'hidden';
            input2.name = "hc_procedimiento[]";
            input2.value = procedimiento;
            input3.type = 'hidden';
            input3.name = "id_agenda[]";
            input3.value = id_agenda;
            rowk.append(input);
            rowk.append(input2);
            rowk.append(input3);
            rowk.className = "well";
            $('.select').select2({
                tags: false
            });
            //reloadCliente();
            //console.log(patient);
            //$('#mifila tr:last').before("<input type='hidden' name='patient[]' value="+patient+">")

        }

        function actualizarCodigosProductos() {
            $('.codigo_producto').each(function(i, obj) {
                if ($(this).val() == "") {
                    var item = $(this).parent().find('.select').val();
                    var ite = $(this).parent().find('.select').find("option[value='CONSULTA']").data("codigo");
                    console.log(item, ite);
                    $(this).val(ite);
                }
            });

        }
        $(".btn_add").click(function() {
            //actualizarCodigosProductos();
            if ($("#form").valid()) {
                $(".print").css('visibility', 'visible');
                //$(".btn_add").attr("disabled", true);
                $("#mifila").html("");
               
                $.ajax({
                    url: "{{route('ventas_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: $("#form").serialize(),
                    success: function(data) {
                        if (!data || !data.idventa) {
                            Swal.fire("No se pudo guardar", data && data.mensaje ? data.mensaje : "La factura no fue confirmada por el servidor.", "error");
                            return;
                        }

                            $("#asiento").val(data.idasiento);
                            $("#id").val(data.idventa);
                            $("#numero").val(data.idventa);
                            Swal.fire("Guardado con Exito!");
                            //ocation.href=""+data.id;
                             window.open("{{url('contable/ventas/comprobante/no.tributario/')}}/"+data.idventa, '_blank');


                        /* $("#asiento").val(data.idasiento);
                         $("#id").val(data.idventa);
                         $("#numero").val(data.idventa);
                         swal("Guardado con Exito!");*/
                    },
                    error: function(data) {
                        console.log(data.responseText);
                        Swal.fire("No se pudo guardar", "Revise los datos de la factura e inténtelo nuevamente.", "error");
                    }
                });
            } else {
                swal("Tiene campos vacios");
                console.log($("#form").serialize());
            }

        });



        $('#btn_pago').click(function(event) {
            id = document.getElementById('contador_pago').value;
            var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id", "dato_pago" + id);
            midiv_pago.innerHTML = '<td><select class="form-control" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="" onchange="revisar_componentes(this,' + id + ');"><option value="">{{trans('wfact_venta.seleccione')}}</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="form-control input-number" value="{{date('Y-m-d')}}" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '" name="tipo_tarjeta' + id + '" style="width: 175px;height:25px"><option value="">{{trans('wfact_venta.seleccione')}}</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="" ></td><td><select class="form-control" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;"><option value="">{{trans('wfact_venta.seleccione')}}</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="width: 80%;;" autocomplete="off" class="form-control" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="form-control" style="width: 80%;;"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="form-control text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);"></td><td><input class="form-control input-number" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,' + id + ',2);"></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

            document.getElementById('agregar_pago').appendChild(midiv_pago);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador_pago').value = id;

        });

        function reloadCliente(data) {
            //ventas.getPrices
            var sd = $(data).val();

            //codigo_producto
            //console.log(sd);
            $('.cneto').each(function(i, obj) {
                //var codigo= $(data).parent().children().closest(".codigo_producto").val(codigo);
                var e = $(this).parent().prev().children().closest(".select");
                var codigo = $('option:selected', e).data("codigo");
                var precio = $(this).parent().next().children();
                console.log(codigo);
                $.ajax({
                    type: 'get',
                    url: "{{route('ventas.getPrices')}}",
                    datatype: 'json',
                    data: {
                        'id_cliente': sd,
                        'codigo_producto': codigo
                    },
                    success: function(data) {
                        console.log(data);
                        if (data != "error" && data != "vacio") {
                            //var precio =  $(this).parent().next().children().html();
                            //console.log(precio+"dasdaa");
                            var precios = precio.val(data);

                        }

                    },
                    error: function(data) {
                        console.log(data);
                    }
                })

            });
            totales(0);

        }

        function revisar_componentes(e, id) {
            metodo = $('#id_tip_pago' + id).val();
            if (metodo == 1) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', true);
                $("#id_banco_pago" + id).prop('disabled', true);
                $("#id_cuenta_pago" + id).prop('disabled', true);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 2) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 3) {
                $("#tipo_tarjeta" + id).prop('disabled', true);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', true);
                revision_total(id);
            } else if (metodo == 4) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            } else if (metodo == 5) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            } else if (metodo == 6) {
                $("#tipo_tarjeta" + id).prop('disabled', false);
                $("#numero_pago" + id).prop('disabled', false);
                $("#id_banco_pago" + id).prop('disabled', false);
                $("#id_cuenta_pago" + id).prop('disabled', false);
                //$("#fi"+id).prop('disabled', false);
                revision_total(id);
            }
        }


        function soloNumeros(e) {
            // capturamos la tecla pulsada
            var teclaPulsada = window.event ? window.event.keyCode : e.which;

            // capturamos el contenido del input
            var valor = e.value;

            // 45 = tecla simbolo menos (-)
            // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
            // Modificamos el contenido del mismo añadiendo el simbolo menos al
            // inicio
            console.log("indexof", valor);
            if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
                document.getElementById("inputNumero").value = "-" + valor;
            }

            // 13 = tecla enter
            // 46 = tecla punto (.)
            // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
            // punto
            if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
                return true;
            }

            // devolvemos true o false dependiendo de si es numerico o no
            return /\d/.test(String.fromCharCode(teclaPulsada));
        }

        function goBack() {
            window.history.back();
        }

        function crear_factura_venta() {

            //$('#crear_factura').submit();
            var valor_total = $('#total1').val();

            var formulario = document.forms["crear_factura"];

            //Datos Cabecera Factura
            var divisas = formulario.divisas.value;

            //Datos Generales
            var id_emp = formulario.id_empresa.value;
            var sucurs = formulario.sucursal.value;
            var punt_emision = formulario.punto_emision.value;


            //Datos Paciente
            var cedula = formulario.ced_paciente.value;
            var nombre_paciente = formulario.nomb_paciente.value;
            var seguro_paciente = formulario.id_seguro.value;
            var proced = formulario.procedimiento.value;
            var fech_proced = formulario.fecha_proced.value;


            //Datos Clientes
            var cliente = formulario.cliente.value;
            var ruc_ced_cliente = formulario.ruc_cedula.value;
            var direccion = formulario.direccion.value;
            var ciud_cliente = formulario.ciudad.value;
            var email_cliente = formulario.email.value;
            var telf_cliente = formulario.telefono.value;


            //Datos Recaudador
            var recaud = formulario.recaudador.value;
            var ced_recaudador = formulario.cedula_recaudador.value;

            //Detalle de Asiento
            var det_asiento = formulario.nota.value;

            var msj = "";


            if (divisas == "") {
                msj += "{{trans('wfact_venta.seleccione_divisa')}}\n";
            }


            //Datos Generales

            if (id_emp == "") {
                msj += "{{trans('wfact_venta.seleccione_empresa')}}\n";
            }

            if (sucurs == "") {
                msj += "{{trans('wfact_venta.seleccione_sucursal')}}\n";
            }

            if (punt_emision == "") {
                msj += "{{trans('wfact_venta.seleccione_p_emision')}}\n";
            }


            //Paciente
            if (cedula == "") {
                msj += "{{trans('wfact_venta.seleccione_cedula')}}\n";
            }
            if (nombre_paciente == "") {
                msj += "{{trans('wfact_venta.seleccione_nombre_paciente')}}\n";
            }
            if (seguro_paciente == "") {
                msj += "{{trans('wfact_venta.seleccione_seguro_paciente')}}\n";
            }
            if (proced == "") {
                msj += "{{trans('wfact_venta.seleccione_proc_paciente')}}\n";
            }
            if (fech_proced == "") {
                msj += "{{trans('wfact_venta.seleccione_fecha_proc')}}\n";
            }

            //Cliente
            if (cliente == "") {
                msj += "{{trans('wfact_venta.seleccione_cliente')}}\n";
            }
            if (ruc_ced_cliente == "") {
                msj += "{{trans('wfact_venta.ingrese_ruc_cliente')}}\n";
            }
            if (direccion == "") {
                msj += "{{trans('wfact_venta.ingrese_direccion_cliente')}}\n";
            }
            if (ciud_cliente == "") {
                msj += "{{trans('wfact_venta.ingrese_ciudad_cliente')}}\n";
            }
            if (email_cliente == "") {
                msj += "{{trans('wfact_venta.ingrese_correo_cliente')}}\n";
            }
            if (telf_cliente == "") {
                msj += "{{trans('wfact_venta.ingrese_telefono_cliente')}}\n";
            }

            //Vendedor/Recaudador
            if (recaud == "") {
                msj += "{{trans('wfact_venta.seleccione_recaudador')}}\n";
            }
            if (ced_recaudador == "") {
                msj += "{{trans('wfact_venta.seleccione_cedula_recaudador')}}\n";
            }

            //Detalle Asiento
            if (det_asiento == "") {
                msj += "{{trans('wfact_venta.ingrese_detalle_asiento')}}\n";
            }

            if (msj == "") {

                if (valor_total > 0) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('ventas_store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $("#crear_factura").serialize(),
                        success: function(data) {
                            //console.log(data);

                            swal({
                                    title: "Guardado correctamente",
                                    buttons: true,
                                })
                                .then((value) => {
                                    location.href = "{{route('venta_index')}}";
                                });

                        },
                        error: function(data) {
                            console.log(data);
                        }
                    })
                } else {

                    swal({
                        title: "Calcular el total a pagar",
                        buttons: true,

                    });

                }
            } else {
                alert(msj);
            }

        }


        //Completa 2 Decimales a la izquierda
        function completa_decimales(elemento, id, nDec) {

            var cod = $('#codigo' + id).val();
            var nomb = $('#nombre' + id).val();
            var num = elemento.value;

            var n = parseFloat(elemento.value);
            var s;
            //n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            //s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);


            if ((cod.length == 0) && (nomb.length == 0)) {
                alert("{{trans('wfact_venta.ingrese_producto')}}");
                $('#cantidad' + id).val("0.00");
                $('#desc' + id).val("0.00");
                $('#extendido' + id).val("0.00");
                return false;
            }


            if (num.length == 0) {

                alert("{{trans('wfact_venta.cantidad_no_permitida')}}");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");

                return false;
            }

            $('#cantidad' + id).val(s);

        }

        //Completa 2 decimales a la izquierda y redondea el valor a dos decimales
        function redondea_precio(elemento, id, nDec) {

            var n = parseFloat(elemento.value);
            var s;

            n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);

            $('#precio' + id).val(s);


        }

        //Redondea descuento a 2 decimales 
        function redondea_descuento(elemento, id, nDec) {

            var n = parseFloat(elemento.value);
            var s;

            n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
            s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
            s = s.substr(0, s.indexOf(".") + nDec + 1);

            $('#desc_porcentaje' + id).val(s);


        }

        //Valida el valor Ingresado en el Campo Cantidad
        function validarcantidad(elemento, id) {

            var cod = $('#codigo' + id).val();
            var nomb = $('#nombre' + id).val()

            if ((cod.length == 0) && (nomb.length == 0)) {
                alert("{{trans('wfact_venta.ingrese_producto')}}");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");
                return false;
            }

            var num = elemento.value;
            if (num.length == 0) {

                alert("{{trans('wfact_venta.cantidad_no_permitida')}}");
                $('#cantidad' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");

                return false;
            }

            var st = $('#stock' + id).val();
            var nu = parseInt(elemento.value, 10);

            if (nu > st) {
                alert("{{trans('wfact_venta.cantidad_mayor_stock')}}");
                $('#cantidad' + id).val("0");
                $('#total_acum' + id).val('');
            }


            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1 || numero > 999999999) {
                alert("{{trans('wfact_venta.cantidad_no_permitida')}}");
                $('#cantidad' + id).val("0");
                return false;
            }

            $('#total_acum' + id).val(numero);

            return true;
        }

        //No usada por el momento
        function validartotal(elemento, id) {
            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1 || numero > 999999999) {
                alert("{{trans('wfact_venta.total_no_permitido')}}");
                $('#total' + id).val("");
                return false;
            }
            return true;
        }


        //Calcula el Total
        function total_calculo(id) {

            total = 0;
            descuento_total = 0;

            cantidad = parseFloat($('#cantidad' + id).val());
            precio = parseFloat($('#precio' + id).val());
            descuento = parseFloat($('#desc_porcentaje' + id).val());

            total = cantidad * precio;

            descuento_total = (total * descuento) / 100;

            $('#desc' + id).val(descuento_total.toFixed(2));
            $('#extendido' + id).val(total.toFixed(2));

            $('#desc1' + id).val(descuento_total.toFixed(2));
            $('#extendido1' + id).val(total.toFixed(2));

            suma_totales();

        }

        function suma_totales() {

            contador = 0;
            iva1 = 0;
            iva = 0;
            total = 0;
            sub = 0;
            descu1 = 0;
            total_fin = 0;
            descu = 0;
            trans = 0;
            subtotal_0 = 0;
            subtotal_12 = 0;
            base_imponible = 0;
            val_cost_prod = 0;
            cost_prod = 0;
            iva_p = 0;
            vent_tar_12 = 0;
            vent_tar_0 = 0;
            sum1 = 0;
            sum2 = 0;

            $("#crear tr").each(function() {
                $(this).find('td')[0];
                visibilidad = $(this).find('#visibilidad' + contador).val();
                if (visibilidad == 1) {
                    //iva_p = parseInt($(this).find('#iva_obt'+contador).val());
                    cost_prod = parseFloat($(this).find('#cost_prod' + contador).val());
                    cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                    valor = parseFloat($(this).find('#precio' + contador).val());
                    descu = parseFloat($(this).find('#desc1' + contador).val());
                    pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                    total = cantidad * valor;

                    if ($('#iva' + contador).prop('checked')) {

                        subtotal_12 = subtotal_12 + total;

                        if (total > 0) {

                            val_cost_prod = val_cost_prod + cost_prod;
                        }

                        if (descu > 0) {
                            descu1 = descu1 + descu;
                        }

                    } else {

                        subtotal_0 = subtotal_0 + total;

                        if (total > 0) {
                            val_cost_prod = val_cost_prod + cost_prod;
                        }

                        if (descu > 0) {
                            descu1 = descu1 + descu;
                        }

                    }


                }

                contador = contador + 1;

            });

            sum_subt = subtotal_12 + subtotal_0;

            iva = subtotal_12 * 0.12;

            base_imponible = subtotal_12;

            trans = parseFloat($('#transporte').val());

            if (trans > 0) {
                total_fin = (sum_subt - descu1) + trans + iva;
            } else {
                total_fin = (sum_subt - descu1) + iva;
            }

            $('#subtotal').val(sum_subt);
            $('#impuesto').val(iva.toFixed(2));
            $('#descuento').val(descu1.toFixed(2));
            $('#total').val(total_fin.toFixed(2));
            $('#base_imponible').val(base_imponible.toFixed(2));
            $('#subtotal').val(subtotal_12.toFixed(2));
            $('#subtotal2').val(subtotal_0.toFixed(2));

            //Campos Oculto
            $('#subtotal1').val(subtotal_12.toFixed(2));
            $('#subtotal_2').val(subtotal_0.toFixed(2));
            $('#impuesto1').val(iva.toFixed(2));
            $('#descuento1').val(descu1.toFixed(2));
            $('#total1').val(total_fin.toFixed(2));
            $('#base_imponible1').val(base_imponible.toFixed(2));
            $('#transporte1').val(trans);
            $('#cost_vent_merc').val(val_cost_prod);
        }

        //Valida Precio
        function validarprecio(elemento, id) {

            var cod = $('#codigo' + id).val();
            var nomb = $('#nombre' + id).val()

            if ((cod.length == 0) && (nomb.length == 0)) {
                alert("{{trans('wfact_venta.ingrese_producto_cantidad')}}");
                $('#precio' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");
                return false;
            }

            var prec = elemento.value;
            if (prec.length == 0) {

                alert("{{trans('wfact_venta.precio_no_permitido')}}");
                $('#precio' + id).val("0");
                $('#desc' + id).val("0");
                $('#extendido' + id).val("0");
                return false;
            }

            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1 || numero > 999999999) {
                alert("{{trans('wfact_venta.precio_no_permitido')}}");
                $('#precio' + id).val("0");
                return false;
            }
            return true;
        }

        function validardescuento(elemento, id) {

            var desc = elemento.value;
            if (desc.length == 0) {

                alert("Rango de descuento permitido (0% - 100%)");
                $('#desc_porcentaje' + id).val("0");
                $('#desc' + id).val("0");
                return false;
            }

            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 0 || numero > 100) {
                alert("Rango de descuento permitido (0% - 100%)");
                $('#desc_porcentaje' + id).val("0");
                $('#desc' + id).val("0");
                return false;
            }
            return true;
        }

        //Obtengo el Ruc o Cedula del Cliente Seleccionado
        $("#cliente").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_identificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#cliente"),
                success: function(data) {
                    if (!isNaN(data)) {
                        $('#ruc_cedula').val(data.client_identificacion);
                        $('#direccion').removeAttr('disabled');
                        $('#direccion').val(data.client_direccion);
                        $('#identif_cliente').val(data.client_identificacion);
                        $('#telefono').val(data.client_telefono);
                        $('#email').val(data.client_email);
                        $('#ciudad').val(data.client_ciudad);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        //Obtengo datos del vendedor de la Tabla Usuarios
        /*$("#vendedor").change(function(){
            $.ajax({
                type: 'post',
                url:"{{route('vendedor.identificacion')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#vendedor"),
                success: function(data){
                    $('#cedula_vendedor').val(data.vendedor_cedula);
                },
                error: function(data){
                    console.log(data);
                }
            })
        });*/

        $("#vendedor").change(function() {
            $('#cedula_vendedor').val($('option:selected', $(this)).data("id"));
        });

        $("#recaudador").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('recaudador.identificacion')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#recaudador"),
                success: function(data) {
                    $('#cedula_recaudador').val(data.recaudador_cedula);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        //Actualiza la direccion del Cliente
        $("#direccion").change(function() {
            $.ajax({
                type: 'post',
                url: "{{route('update_direccion_client')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                //data: $("#identif_cliente"),
                data: {
                    'ident_cliente': $("#identif_cliente").val(),
                    'direc_cliente': $("#direccion").val()
                },
                success: function(data) {
                    //console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });


        //Obtengo el Codigo del Producto por el nombre del producto Ingresado
        function cambiar_nombre(id) {

            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_codigo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': $("#nombre" + id).val()
                },
                success: function(data) {
                    //console.log(data);
                    $('#codigo' + id).val(data.cod_product);
                    $('#cost_prod' + id).val(data.cost_vent);
                    //$('#iva_obt'+id).val(data.iva_prod);
                    if (data.iva_prod == '1') {
                        $('#iva' + id).prop("checked", true);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }


        //Obtengo el Nombre del Producto por el codigo del producto Ingresado
        function cambiar_codigo(id) {
            $.ajax({
                type: 'post',
                url: "{{route('ventas_buscar_nombre2')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'codigo': $("#codigo" + id).val()
                },
                success: function(data) {
                    $('#nombre' + id).val(data);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }


        /* function obtener_num_factura(){
             $.ajax({
                 url:"{{route('numero_factura')}}",
                 type: 'get',
                 datatype: 'json',
                 success: function(data){
                    //console.log(data);
                    $('#nfactura').val(data);
                 },
                 error: function(data){
                     console.log(data);
                 }
             })
         }*/


        function limpiar() {

            //obtenemos la fecha actual
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);
            var today = now.getFullYear() + "-" + (month) + "-" + (day);
            var fe_proced = new Date('Y-m-d H:i:s');
            $("#fecha").val(today);
            $("#fecha_proced").val(fe_proced);


        }

        //Completa la cedula del Paciente por minimo 3 digitos
        $(".ced_paciente").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('autocomple_paciente_cedula')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
        });


        //Obtengo datos del paciente por la cedula
        function obtener_datos_paciente() {
            $.ajax({
                type: 'post',
                url: "{{route('obtener_info_paciente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: {
                    'ced_paciente': $("#ced_paciente").val()
                },
                success: function(data) {
                    //console.log(data);
                    if (data.texto != '') {
                        $('#nomb_paciente').val(data.texto);
                    }

                    if (data.id_seg != '') {
                        $('#id_seguro').val(data.id_seg);
                    }

                    /*if(data.trim() == 'error'){
                      alert("No existe el Paciente");
                      $('#ced_paciente').val('');
                      $('#nomb_paciente').val('');
                      $('#id_seguro').val('');
                    }*/

                },
                error: function(data) {
                    console.log(data);
                }
            })
        }

        function obtener_sucursal() {

            var id_seleccionado = $("#id_empresa").val();

            $.ajax({
                type: 'post',
                url: "{{route('sucursal.empresa')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_emp': id_seleccionado
                },
                success: function(data) {
                    //console.log(data);

                    if (data.value != 'no') {
                        if (id_seleccionado != 0) {
                            $("#sucursal").empty();

                            $.each(data, function(key, registro) {
                                $("#sucursal").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '</option>');

                            });
                        } else {
                            $("#sucursal").empty();

                        }

                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })



        }

        function obtener_caja() {

            var id_sucursal = $("#sucursal").val();

            $.ajax({
                type: 'post',
                url: "{{route('caja.sucursal')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_sucur': id_sucursal
                },
                success: function(data) {
                    //console.log(data);

                    if (data.value != 'no') {
                        if (id_sucursal != 0) {
                            $("#punto_emision").empty();

                            $.each(data, function(key, registro) {
                                $("#punto_emision").append('<option value=' + registro.id + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');

                            });
                        } else {
                            $("#punto_emision").empty();

                        }

                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })

        }


        $('#busqueda').click(function(event) {
            id = document.getElementById('contador').value;
            var midiv = document.createElement("tr")
            midiv.setAttribute("id", "dato" + id);
            midiv.innerHTML = '<td><input name="codigo' + id + '" class="codigo" id="codigo' + id + '" style="width: 110px;" onchange="cambiar_codigo(' + id + ')"/><input type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input type="text" class="hidden" id="id_prod' + id + '" name="id_prod' + id + '"><input type="text" class="hidden" name="cost_prod' + id + '" id="cost_prod' + id + '"></td><td><input name="nombre' + id + '" class="nombre" id="nombre' + id + '"  onchange="cambiar_nombre(' + id + ')"></td><td> <input type="text" style="width: 110px;" id="cantidad' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="return completa_decimales(this,' + id + ',2);" name="cantidad' + id + '"></td><td><input type="text" style="width: 110px;" id="precio' + id + '" name="precio' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onchange="return redondea_precio(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc_porcentaje' + id + '" name="desc_porcentaje' + id + '" value="0.00" onkeyup="total_calculo(' + id + ')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  onchange="return redondea_descuento(this,' + id + ',2);"></td><td><input type="text" style="width: 110px;" id="desc' + id + '" name="desc' + id + '"  value="0.00" disabled><input type="text" class="hidden" name="desc1' + id + '" id="desc1' + id + '"></td><td><input type="text" name="extendido' + id + '" id="extendido' + id + '" value="0.00"  value="" disabled><input type="text" class="hidden" name="extendido1' + id + '" id="extendido1' + id + '"></td><td><input type="checkbox" id="iva' + id + '" name="iva' + id + '" disabled></td><td><button type="button" onclick="eliminar_registro(' + id + ')" class="btn btn-warning btn-margin">Eliminar</button></td>';
            document.getElementById('crear').appendChild(midiv);
            id = parseInt(id);
            id = id + 1;
            document.getElementById('contador').value = id;

            //Completa el codigo del Producto al Ingresar
            $(".codigo").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('ventas_completa_codigo')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 1,
            });
            //Completa el nombre  del Producto al Ingresar
            $(".nombre").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('ventas_buscar_nombre')}}",
                        //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()}, 
                        dataType: "json",
                        //type: 'post',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                selectFirst: true,
                minLength: 3,
            });


        });

        //Elimina Registro de la Tabla Productos
        function eliminar_registro(valor) {
            var dato1 = "dato" + valor;
            var nombre2 = 'visibilidad' + valor;
            document.getElementById(dato1).style.display = 'none';
            document.getElementById(nombre2).value = 0;
            suma_totales();
        }

        function revision_total(id) {

            var valor = $('#valor' + id).val();
            ntotal = valor * 1;
            $('#valor_base' + id).val(ntotal.toFixed(2));

        }
    </script>


    <script type="text/javascript">
      
    </script>

    </div>
@endsection
