@extends('contable.ventas.base')
@section('action-content')
<?php

use Sis_medico\De_Empresa;

$deEmpresa = De_Empresa::getDatos($empresa->id);
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<style type="text/css">
    .input-number {
        width: 80%;
        height: 20px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.ventas')}}</a></li>
            <li class="breadcrumb-item"><a href="../ventas">Registro de Factura de Ventas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.nuevo')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <!--
            <div class="col-md-9">
                <h3 class="box-title">Nueva Factura de Venta</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray">
                    {{trans('contableM.nuevo')}}
                </button>
            </div>
             -->
            <div class="col-md-11" style="display: flex; justify-content: space-between;">
                <a class="btn btn-primary" target="_blank" href="{{url('contable/clientes/editar')}}/{{ $fact_venta->identificacion }}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i> Revisar Cliente</a>
                <button onclick="goBack()" class="btn btn-danger">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
    </div>
    <div class="box-body dobra">
        <form class="form-vertical" id="form">
            {{ csrf_field() }}
            <div class="header row">
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
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
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero" class=" label_header">{{trans('contableM.numero')}}</label>
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
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo" class="label_header">{{trans('contableM.tipo')}}</label>
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
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento" class="label_header">{{trans('contableM.asiento')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="asiento" id="asiento" value="">
                        @if ($errors->has('asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_asiento" class="label_header">{{trans('contableM.fecha')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group date ">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="fecha_asiento" id="fecha" class="form-control" placeholder='Fecha' required>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="orden_venta" class="label_header">{{trans('contableM.ordenventa')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="orden_venta" type="text" class="form-control" name="orden_venta" value="{{ $fact_venta->id }}">
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-1 px-1">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header">Electrónica</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <label class="switch">
                            <input class="electros" <?php $estado = '';
                                                    if ($empresa->electronica == 0) {
                                                        $estado = 'disabled';
                                                    }
                                                    if ($deEmpresa != '') {
                                                        $estado = 'checked';
                                                    }
                                                    echo $estado; ?> id="toggleswitch" type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <input type="hidden" name="electronica" id="electronica" value="0">
                    <input type="hidden" name="tipo_electronica" id="tipo_electronica" value="<?php $elec = $empresa->electronica;
                                                                                                if ($elec == 0) {
                                                                                                    if ($deEmpresa != '') $elec = 1;
                                                                                                } else {
                                                                                                    $elec = 1;
                                                                                                }
                                                                                                echo $elec; ?>">
                </div>
                <div class="form-group col-xs-6  col-md-1  px-1">
                    <div class="col-md-12 px-0">
                        <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()">
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $value)
                            <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="punto_emision" id="punto_emision">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero_autorizacion" class="label_header">{{trans('contableM.NAUTORIZACION')}}</label>
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
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="divisas" class="label_header">{{trans('contableM.divisass')}}</label>
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
                        <label for="recaudador" class="label_header">{{trans('contableM.Recaudador')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="recaudador" name="recaudador" class="form-control">
                            <option value="">Seleccione...</option>
                            @foreach($user_recaudador as $value)
                            <option value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_recaudador" id="cedula_recaudador" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="vendedor" class="label_header">{{trans('contableM.vendedor')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select id="vendedor" name="vendedor" class="form-control">
                            <option value="">Seleccione...</option>
                            @foreach($user_vendedor as $value)
                            <option value="{{$value->nombre1}} {{$value->apellido1}}" data-id="{{$value->id}}" data-name="{{$value->nombre1}} {{$value->apellido1}}">{{$value->nombre1}} {{$value->apellido1}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" class="form-control input-sm" name="cedula_vendedor" id="cedula_vendedor" value="{{ old('cedula_recaudador')}}" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <div class="col-md-12 px-0">
                        <label for="cliente" class="label_header text-left">{{trans('contableM.cliente')}}</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" value="{{ $fact_venta->identificacion }}" onchange="reloadCliente()" required placeholder="Identificacion">
                        @if ($errors->has('identificacion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" value="{{ $fact_venta->razon_social }}" onchange="reloadCliente()" required placeholder="Nombre">
                        @if ($errors->has('nombre_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente" value="{{ $fact_venta->direccion }}" required placeholder="Dirección" onchange="guardarCiudad()">
                        @if ($errors->has('direccion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente" value="{{ $fact_venta->ciudad }}" required placeholder="Ciudad" onchange="guardarCiudad()">
                        @if ($errors->has('ciudad_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="mail_cliente" type="text" class="form-control" name="mail_cliente" value="{{ $fact_venta->email }}" required placeholder="Mail">
                        @if ($errors->has('mail_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente" value="{{ $fact_venta->telefono }}" required placeholder="Teléfono">
                        <input id="tipo_cliente" type="hidden" class="form-control" name="tipo_cliente" value="{{ old('tipo_cliente') }}" required placeholder="tipo">
                        @if ($errors->has('telefono_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <div class="col-md-12 px-0">
                        <label for="paciente" class="label_header text-left">{{trans('contableM.paciente')}}</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_paciente" type="text" class="form-control" name="identificacion_paciente" value="{{ old('identificacion_paciente') }}" required placeholder="Cédula">
                        @if ($errors->has('identificacion_paciente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_paciente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="nombre_paciente" type="text" class="form-control" name="nombre_paciente" value="{{$fact_venta->agenda->paciente->nombre1}} {{$fact_venta->agenda->paciente->nombre2 }} {{$fact_venta->agenda->paciente->apellido1 }} {{$fact_venta->agenda->paciente->apellido2 }}" required placeholder="Nombre">
                        @if ($errors->has('nombre_paciente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_paciente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <select class="form-control" name="id_seguro" id="id_seguro">
                            <option value="">Seguro ...</option>
                            @foreach($seguros as $seguro)
                            <option value="{{$seguro->id}}" @if($fact_venta->id_seguro == $seguro->id) selected="selected" @endif>{{$seguro->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="procedimiento" type="text" class="form-control" name="procedimiento" value="" required placeholder="Procedimiento">
                        <!-- <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="0"> -->
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <div class="input-group date ">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="{{ date('Y/m/d', strtotime($fact_venta->agenda->fechaini)) }}" name="fecha_proced" id="fecha_proced" class="form-control" placeholder='Fecha de Proce.'>
                            <input type="hidden" value="{{$valorIva}}" name="ivareal" id="ivareal" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 px-0">
                        <select class="form-control" name="tipo_consulta" id="tipo_consulta">
                            <option value=""> --- Tipo --- </option>
                            <option value="1" @if($fact_venta->agenda->proc_consul==0) selected="selected" @endif>Consulta</option>
                            <option value="2" @if($fact_venta->agenda->proc_consul==1) selected="selected" @endif>Procedimiento</option>
                        </select>
                    </div>
                </div>
            </div>
            @php
            $post=0;
            $id_auth = Auth::user()->id;
            @endphp
            <div class="col-md-12 table-responsive">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class='well-dark'>
                            <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                            <th width="35%" class="" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.cobrarseguro')}}</th>
                            <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                            <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                            <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                            <th width="10%" class="" tabindex="0">
                                <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        @if($fact_venta->agenda->proc_consul==1)
                        @foreach($fact_venta_detalle as $detalle)
                        <tr class="well">
                            @php
                            $dataCode= \Sis_medico\ParametersConglomerada::recibo($detalle->descripcion);
                            @endphp
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option value="{{$value->nombre}}" @if($value->codigo==$detalle->descripcion) selected="selected" @elseif($value->nombre==$detalle->descripcion) selected="selected" @elseif($dataCode==$value->codigo) selected="selected" @elseif($value->codigo==$detalle->cod_prod) selected="selected" @endif data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->nombre}} </option>
                                    @endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">{{$detalle->descripcion}}</textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$detalle->cantidad}}" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar(this)">
                                    <option> </option>
                                    @foreach ($bodega_in as $value)
                                    @if (!is_null($value))
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td id="tprecio">
                                <input type="text" class="form-control pneto" name="precio[]" onblur="this.value=parseFloat(this.value).toFixed(2);" style="width:40%;display:inline;height:20px;" value="{{number_format($detalle->precio,2, '.', '')}}">
                                <button type="button" class="btn btn-info btn-gray btn-xs cp" disabled>
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($detalle->valor_oda,2, '.', '')}}" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($detalle->p_dcto,2, '.', '')}}" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="{{number_format($detalle->descuento,2, '.', '')}}" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="{{number_format($detalle->total ,2, '.', '')}}" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form chef" type="checkbox" {{$detalle->iva == 1 ? 'checked' : ''}} style="width: 80%;height:20px;" name="valoriva[]" disabled>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @elseif($fact_venta->agenda->proc_consul==0)
                        @foreach($fact_venta_detalle as $detalle)
                        <tr class="well">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option @if($value->codigo==$detalle->cod_prod) selected="selected" @endif value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(this)">
                                    <option> </option>
                                    @foreach ($bodega_in as $value)
                                    @if (!is_null($value))
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td id="tprecio">
                                <input type="text" class="form-control pneto" name="precio[]" onblur="this.value=parseFloat(this.value).toFixed(2);" style="width:40%;display:inline;height:20px;" value="@if(isset($fact_venta->detalles[0])){{number_format($fact_venta->detalles[0]->precio,2, '.', '')}}@endif">
                                <button type="button" class="btn btn-info btn-gray btn-xs cp" disabled>
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($fact_venta->valor_oda,2, '.', '')}}" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="@if(isset($fact_venta->descuento)){{number_format($fact_venta->descuento,2, '.', '')}}@endif" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="@if(isset($fact_venta->detalles[0])){{number_format($fact_venta->detalles[0]->total ,2, '.', '')}} @endif" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form chef" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        <tr style="display:none" id="mifila">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(this)">
                                    <option> </option>
                                    @foreach ($bodega_in as $value)
                                    @if (!is_null($value))
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endif
                                    @endforeach
                                </select>
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
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form chef" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class='well'>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal')}} {{$porcentajeIva}}%</td>
                            <td id="subtotal_12" class="text-right px-1">0.00</td>
                            <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                            <td id="subtotal_0" class="text-right px-1">0.00</td>
                            <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                            <td id="descuento" class="text-right px-1">0.00</td>
                            <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.SubtotalsinImpuesto')}}</td>
                            <td id="base" class="text-right px-1">0.00</td>
                            <input type="hidden" name="base1" id="base1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">{{trans('contableM.tarifa')}} {{$porcentajeIva}}%</td>
                            <td id="tarifa_iva" class="text-right px-1">0.00</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                        </tr>
                        <!--<tr>
                            <td></td><td></td><td></td><td></td><td></td>
                            <td colspan="2" class="text-right">Transporte</td>
                            <td id="transporte" class="text-right px-1">0.00</td>
                            <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                        </tr>-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                            <td id="total" class="text-right px-1">0.00</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>{{trans('contableM.PorCobrarSeguro')}}</strong></td>
                            <td id="copagoTotal" class="text-right px-1">0.00</td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div id="divDiasPlazo" class="form-group col-xs-6 col-md-2  px-1" style="margin-bottom:15px;">
            </div>
            <div class="col-md-12" style="height:30px;">
                <div class="row head-title">
                    <div class="col-md-12 cabecera">
                        <label class="color_texto">{{trans('contableM.formadepago')}}</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 table-responsive ">
                <!-- <input name="contador_pago" id="contador_pago" type="hidden" value="0"> -->
                <table id="example1" role="grid" class="table table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                    <thead>
                        <tr>
                            <th width="20%" style="text-align: center;">{{trans('contableM.Metodo')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.fecha')}}</th>
                            <th width="15%" style="text-align: center;">{{trans('contableM.TipoTarjeta')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.numero')}}</th>
                            <th width="15%" style="text-align: center;">{{trans('contableM.banco')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.Cuenta')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.giradoa')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.valor')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.ValorBase')}}</th>
                            <th width="5%" style="text-align: center;"><!-- <button id="btn_pago" type="button" class="btn btn-success btn-gray"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button> --></th>
                        </tr>
                    </thead>
                    <tbody id="agregar_pago">
                        @php $id = 0;
                        $valor_total_pagos = 0;@endphp
                        @foreach($ct_for_pag as $for_pago)
                        <tr>
                            <td>
                                <select class="form-control" name="id_tip_pago{{$id}}" id="id_tip_pago{{$id}}" style="width: 100px;height:20px" onchange="revisar_componentes(this,{{$id}});">
                                    <option value="">Seleccione</option>
                                    @foreach($tipo_pago as $value)
                                    <option @if($for_pago->tipo == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="visibilidad_pago{{$id}}" name="visibilidad_pago{{$id}}" value="1">
                            </td>
                            <td>
                                <input type="date" class="form-control input-number" @if(!is_null($for_pago->fecha)) value="{{$for_pago->fecha}}" @endif name="fecha_pago{{$id}}" id="fecha_pago{{$id}}" style="width: 120px;">
                            </td>
                            <td>
                                <select id="tipo_tarjeta{{$id}}" name="tipo_tarjeta{{$id}}" style="width: 175px;height:25px">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipo_tarjeta as $tipo_t)
                                    <option @if($for_pago->tipo_tarjeta == $tipo_t->id) selected @endif value="{{$tipo_t->id}}">{{$tipo_t->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="numero_pago{{$id}}" id="numero_pago{{$id}}" style="width: 100px;" @if(!is_null($for_pago->numero)) value="{{$for_pago->numero}}" @endif ></td>
                            <td>
                                <select class="form-control" name="id_banco_pago{{$id}}" id="id_banco_pago{{$id}}" style="width: 90px;height:20px">
                                    <option value="">Seleccione...</option>
                                    @foreach($lista_banco as $value)
                                    <option @if($for_pago->banco == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input style="width: 80%;height:20px;" autocomplete="off" class="form-control" name="id_cuenta_pago{{$id}}" id="id_cuenta_pago{{$id}}" @if(!is_null($for_pago->cuenta)) value="{{$for_pago->cuenta}}" @endif ></td>
                            <td><input class="form-control" style="width: 80%;height:20px;" type="text" id="giradoa{{$id}}" name="giradoa{{$id}}"></td>
                            <td><input class="form-control text-right input-number fpago" type="text" id="valor{{$id}}" name="valor{{$id}}" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="revision_total({{$id}})" onkeypress="return soloNumeros(this); " @if(!is_null($for_pago->valor)) value="{{$for_pago->valor}}" @endif></td>
                            <td><input class="form-control input-number" type="text" readonly id="valor_base{{$id}}" name="valor_base{{$id}}" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,{{$id}},2);" @if(!is_null($for_pago->valor)) value="{{$for_pago->valor}}" @endif></td>
                            <td><button style="text-align:center;" type="button" onclick="eliminar_form_pag({{$id}})" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                        </tr>
                        @php
                        $id++;
                        if(!is_null($for_pago->valor)){
                        $valor_total_pagos += $for_pago->valor;
                        }
                        @endphp
                        @endforeach
                    </tbody>
                    <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="{{$valor_total_pagos}}">
                    <input name="contador_pago" id="contador_pago" type="hidden" value="{{$id}}">
                </table>
            </div>
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-success btn_add" onclick="guardarVenta()">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<!--  <form class="form-vertical " id="crear_factura" role="form" method="POST" style="display:none">
    {{ csrf_field() }}
        <div class="box box-primary box-solid " style="background-color: white;">

            <div class="box-body" style="background-color: #ededed;">
                <div class="alert alert-danger oculto">
                   <ul id="ms_error"></ul>
                </div>
                <div class="col-md-12">&nbsp;</div>
                <div class="row">
                    <div class="col-sm-6" style="padding-left: 30px">
                        <div class="card-header">
                           <label style="color: white">DATOS PACIENTE</label>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">&nbsp;</div>
                             <--Cedula del Paciente--
                            <div class="col-md-10 col-xs-6">
                                <label for="ced_paciente" class="col-md-3">Cédula:</label>
                                <div class="input-group col-md-9">
                                    <input  id="ced_paciente" maxlength="10" type="number" class="form-control" name="ced_paciente"  placeholder="Cédula"  class="ced_paciente" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange ="obtener_datos_paciente()">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                       <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ced_paciente').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Nombre Paciente--
                            <div class="col-md-10 col-xs-6">
                                <label for="nomb_paciente" class="col-md-3 control-label">{{trans('contableM.paciente')}}:</label>
                                <div class="input-group col-md-9">
                                    <input  type="text" class="form-control" name="nomb_paciente" id="nomb_paciente" placeholder="Apellidos y Nombres"  class="nomb_paciente" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="{{old('nomb_paciente')}}">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                      <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nomb_paciente').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Seguro Paciente--
                            <div class="col-md-10 col-xs-6" >
                                <label for="seguro" class="col-md-3 control-label">Seguro:</label>
                                <div class="input-group col-md-9">
                                    <select class="form-control input-sm" name="id_seguro" id="id_seguro">
                                        <option value="">Seleccione ...</option>
                                        @foreach($seguros as $seguro)
                                            <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Procedimiento echo al Paciente--
                            <div class="col-md-10 col-xs-6">
                                <label for="procedimiento" class="col-md-3 control-label">Procedimiento:</label>
                                <div class="input-group col-md-9">
                                    <input id="procedimiento" name="procedimiento"  type="text" class="form-control"  value="{{old('procedimiento')}}" placeholder="Procedimiento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                      <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('procedimiento').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Fecha de Procedimiento echoPaciente--
                            <div class="col-md-10 col-xs-6">
                                <label class="col-md-3 control-label">Fecha de Procedimiento:</label>
                                <div class="input-group date col-md-9">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fecha_proced') }}" name="fecha_proced" id="fecha_proced" class="form-control input-sm">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                      <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('fecha_proced').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" style="padding-left: 30px">
                        <div class="card-header">
                           <label style="color: white">DATOS CLIENTE</label>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">&nbsp;</div>
                            <--Código del Cliente--
                            <div class="col-md-10 col-xs-6">
                                <label for="id" class="col-md-3 control-label">{{trans('contableM.cliente')}}:</label>
                                <div class="input-group col-md-9">
                                    <select id="cliente" name="cliente" class="form-control select2_cliente" style="width: 100%" >
                                        <option value="">Seleccione...</option>
                                        @foreach($clientes as $value)
                                            <option value="{{$value->identificacion}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Ruc/Cid del Cliente--
                            <div class="col-md-10 col-xs-6">
                                <label for="ruc_cedula" class="col-md-3 control-label">{{trans('contableM.ruc')}}/{{trans('contableM.cedula')}}</label>
                                <div class="input-group col-md-9">
                                    <input id="ruc_cedula" name="ruc_cedula" type="text" class="form-control" maxlength="13"  value="{{ old('ruc_cedula') }}" placeholder="RUC/CID" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                       <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ruc_cedula').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Direccion del Cliente--
                            <div class="col-md-10 col-xs-6">
                                <label for="direccion" class="col-md-3 control-label">{{trans('contableM.direccion')}}</label>
                                <div class="input-group col-md-9">
                                    <input  type="text" class="form-control input-sm" name="direccion" id="direccion" value="{{ old('direccion')}}" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                           <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Ciudad del Cliente--
                            <div class="col-md-10 col-xs-6">
                                <label for="ciudad" class="col-md-3 control-label">{{trans('contableM.ciudad')}}</label>
                                <div class="input-group  col-md-9">
                                    <input  type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="{{ old('ciudad')}}" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Email del Cliente--
                            <div class="col-md-10 col-xs-6">
                                <label for="email" class="col-md-3 control-label">Mail</label>
                                <div class="input-group col-md-9">
                                    <input type="text" class="form-control input-sm" name="email" id="email" value="{{ old('email') }}" placeholder="Mail" >
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Telefono del Cliente--
                            <div class="col-md-10 col-xs-6">
                                    <label for="telefono" class="col-md-3 control-label">Teléfono</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-sm-12" style="padding-left: 30px">
                        <div class="card-header">
                            <label style="color: white">DATOS RECAUDADOR</label>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">&nbsp;</div>
                            <--Recaudador--

                        </div>
                    </div>
                    <div class="col-md-12" style="padding-top: 10px"></div>
                    <div class="col-sm-12" style="padding-left: 30px">
                        <div class="card-header">
                           <label style="color: white">DATOS SRI FACTURA DE VENTA</label>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">&nbsp;</div>
                            <--No Pre_impreso--
                            <div class="col-md-10 col-xs-6">
                                <label for="autorizacion" class="col-md-2 control-label">No. Preimpreso</label>
                                <div class="input-group col-md-9">
                                    <input  type="number" class="form-control input-sm" name="autorizacion" id="autorizacion" value="{{ old('autorizacion')}}" placeholder="Autorización" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;" readonly>
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Autorizacion--
                            <div class="col-md-10 col-xs-6">
                                <label for="autorizacion" class="col-md-2 control-label">No. Autorización</label>
                                <div class="input-group col-md-9">
                                    <input  type="number" class="form-control input-sm" name="autorizacion" id="autorizacion" value="{{ old('autorizacion')}}" placeholder="Autorización" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;" readonly>
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('autorizacion').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Serie--
                            <div class="col-md-10 col-xs-6">
                                <label for="serie" class="col-md-2 control-label">No. Serie</label>
                                <div class="input-group col-md-9">
                                    <input  type="number" class="form-control input-sm" name="serie" id="serie" value="{{ old('serie')}}" placeholder="Serie" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;" readonly>
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding-top: 3px"></div>
                            <--Autorizacion--
                            <div class="col-md-10 col-xs-6">
                                <label for="validez" class="col-md-2 control-label">Válidez</label>
                                <div class="input-group col-md-9">
                                    <input  type="text" class="form-control input-sm" name="validez" id="validez" value="{{ old('validez')}}" placeholder="Válidez" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly>
                                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('validez').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">&nbsp;</div>
                <-- Observaciones --
                <div class="col-md-12" style="padding-left: 30px">
                    <div class="row ">
                        <div class="card-header">
                            <label style="color: white">DETALLE ASIENTO</label>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12" style="padding-top: 6px"></div>
                            <label for="nota" class="col-md-1 control-label" style="font-size: 13px">{{trans('contableM.detalle')}}</label>
                            <div class="col-md-6">
                              <input id="nota" type="text" class="form-control" name="nota" value="{{ old('nota') }}" style="padding-left: 2px" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="padding-top: 3px"></div>
                <div class="table-responsive col-md-12">
                    <input name='contador' type="hidden" value="0" id="contador">
                    <table id="example2" role="grid" aria-describedby="example2_info">
                        <caption><b>Detalle de Productos</b></caption>
                        <thead class="cabecera">
                            <tr style="position: relative;">
                              <th style="width: 5%; text-align: center;">{{trans('contableM.codigo')}}</th>
                              <th style="width: 12%; text-align: center;">Descripción del Producto</th>
                              <th style="width: 5%; text-align: center;">{{trans('contableM.cantidad')}}</th>
                              <th style="width: 8%; text-align: center;">{{trans('contableM.precio')}}</th>
                              <th style="width: 3%; text-align: center;">Des%</th>
                              <th style="width: 8%; text-align: center;">Desc.</th>
                              <th style="width: 5%; text-align: center;">{{trans('contableM.precioneto')}}</th>
                              <th style="width: 1%; text-align: center;">{{trans('contableM.iva')}}</th>
                              <th style="width: 1%; text-align: center;">{{trans('contableM.accion')}}</th>
                            </tr>
                        </thead>
                        <tbody id="crear">
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <div class="box-footer" style="background: #ededed;">
                                <button type="button" id="busqueda" class="btn btn-primary size_text">
                                Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <--Calculo de Valores --
                <div class="col-md-12" style="padding-top: 20px">
                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">{{trans('contableM.subtotal12')}}</label>
                                    <input class="col-md-6" type="text" name="subtotal" id="subtotal" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" disabled>
                                    <input type="text" name="subtotal1" id="subtotal1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Subtotal 0%:</label>
                                    <input class="col-md-6" type="text" name="subtotal2" id="subtotal2" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" readonly>
                                    <input type="text" name="subtotal_2" id="subtotal_2" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px" >
                                <div class="row">
                                    <label class="col-md-6">{{trans('contableM.descuento')}}</label>
                                    <input class="col-md-6" type="text" name="descuento" id="descuento" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" disabled>
                                    <input type="text" name="descuento1" id="descuento1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">{{trans('contableM.BaseImponible')}}</label>
                                    <input class="col-md-6" type="text" name="base_imponible" id="base_imponible" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" disabled>
                                    <input type="text" name="base_imponible1" id="base_imponible1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">{{trans('contableM.tarifaiva')}}</label>
                                    <input class="col-md-6" type="text" name="impuesto" id="impuesto" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" disabled>
                                    <input type="text" name="impuesto1" id="impuesto1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">Transporte</label>
                                    <input class="col-md-6" type="text" name="transporte" id="transporte" onkeyup="suma_totales()" value="0.00">
                                    <input type="text" name="transporte1" id="transporte1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                                <div class="row">
                                    <label class="col-md-6">{{trans('contableM.total')}}</label>
                                    <input class="col-md-6" type="text" name="total" id="total" value="0.00" style="border: 1px solid #85929e;border-radius: 3px;background-color: white" disabled>
                                    <input type="text" name="total1" id="total1" class="hidden">
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 5px">
                               <input type="text" name="cost_vent_merc" id="cost_vent_merc" class="hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>-->

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    var fila = $("#mifila").html();
    var existeCliente = false;
    $('.select2_cuentas').select2({
        tags: false
    });
    totales(0);
    $("#identificacion_cliente").val("{{$fact_venta->identificacion}}");
    $("#identificacion_paciente").val("{{$fact_venta->agenda->id_paciente}}");
    $("#agregar_cuentas").find('.select2_cuentas').each(function() {
        //$( this ).addClass( "foo" );
        //$(this).change();
    });
    window.onload = function() {
        setTimeout(function() {
            totales(0);
        }, 1500);
        setTimeout(() => {
            diasPlazo()
        }, 2000);
        // diasPlazo();
    }
    const diasPlazo = () => {
        let electronica = document.getElementById("electronica").value;
        let divDias = document.getElementById("divDiasPlazo");
        if (electronica == 1) {
            let html = `<div class="col-md-12 px-0">
                        <label for="diasPlazo" class="label_header">Dias Plazo</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input style="text-align: center;text-transform: uppercase;" type="number" class="form-control" name="diasPlazo" id="diasPlazo" value="30">
                    </div>`
            $("#divDiasPlazo").empty();
            $("#divDiasPlazo").append(html);
            // divDias.insertAdjacentHTML('afterbegin', html)
        } else {
            $("#divDiasPlazo").empty();
        }
    }
    $(document).on("focus", "#nombre_cliente", function() {
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
                totales(0);
            },
            selectFirst: true,
            minLength: 1,
        });
    });
    $(document).on("focus", "#identificacion_cliente", function() {
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
                totales(0);
            },
            selectFirst: true,
            minLength: 1,
        });
    });

    $(document).ready(function() {
        var tipo_electronica = $('#tipo_electronica').val();
        if (tipo_electronica == 1) {
            $('#toggleswitch').prop('checked', true);
            $("#electronica").val(1);
            $('#numero_autorizacion').attr('readonly', true);
            $('#numero').attr('readonly', true);
            document.getElementById("sucursal").disabled = true;
            document.getElementById("punto_emision").disabled = true;
        }
        diasPlazo();
    });
    var input = document.getElementById('toggleswitch');
    //var outputtext = document.getElementById('status');
    input.addEventListener('change', function() {
        if (this.checked) {
            $("#electronica").val(1);
            $('#numero_autorizacion').attr('readonly', true);
            $('#numero').attr('readonly', true);
            //$('#sucursal').attr('readonly', true);
            //$('#punto_emision').attr('readonly', true);
            document.getElementById("sucursal").disabled = true;
            document.getElementById("punto_emision").disabled = true;
        } else {
            $("#electronica").val(0);
            $('#numero_autorizacion').attr('readonly', false);
            $('#numero').attr('readonly', false);
            //$('#sucursal').attr('readonly', false);
            //$('#punto_emision').attr('readonly', false);
            document.getElementById("sucursal").disabled = false;
            document.getElementById("punto_emision").disabled = false;
        }
        diasPlazo();
    });
    $(document).on("focus", "#identificacion_paciente", function() {
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
    });
    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
        totales(0);
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
        var tipo_electronica = $('#tipo_electronica').val();
        if (tipo_electronica == 1) {
            $('#toggleswitch').prop('checked', true);
            $("#electronica").val(1);
            $('#numero_autorizacion').attr('readonly', true);
            $('#numero').attr('readonly', true);
            document.getElementById("sucursal").disabled = true;
            document.getElementById("punto_emision").disabled = true;
            diasPlazo();
        }
        <?php
        if ($deEmpresa != '') {
        ?>
            if (tipo_electronica == 1) {
                $('#toggleswitch').prop('checked', true);
                $("#electronica").val(1);
                $('#numero_autorizacion').attr('readonly', true);
                $('#numero').attr('readonly', true);
                document.getElementById("sucursal").disabled = false;
                document.getElementById("punto_emision").disabled = false;
                diasPlazo();
            } else {
                document.getElementById("sucursal").disabled = true;
                document.getElementById("punto_emision").disabled = true;
            }
        <?php
        }
        ?>
        $(".select2_cuentas").trigger('change');

    });
    $('body').on('click', '.cp', function() {
        var clase = $(this).prev().attr('class');
        var html = '<input type="text" class="form-control pneto" name="precio[]" onblur="this.value=parseFloat(this.value).toFixed(2);" style="width:40%;display:inline;height:20px;">' +
            '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
            '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
            '</button>';
        if (clase.includes('select2_precio')) {
            $(this).parent().append(html);
            $(this).prev().remove();
            $(this).remove();
        } else {
            html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" autofocus active required>' +
                '<option value="0"> </option></select>' +
                '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
                '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';
            // $(this).parent().empty();
            // $(this).parent().append(html);
            $(this).parent().append(html);
            verificar($(this).parent().prev().prev().children().closest('.select2_cuentas'));
            $(this).prev().remove();
            $(this).remove();
        }
    });
    /*function changePrecio(i, e){
        var html = '<input type="text" class="form-control pneto" style="width:40%;display:inline;height:20px;">'+
                    '<button type="button" class="btn btn-info btn-gray btn-xs" onclick="changePrecio(this,2)">'+
                    '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>'+
                    '</button>';
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
            verificar($("#tprecio").prev().prev().children().closest('.select2_cuentas'));
        }
    }*/
    function verificar_stock(e) {
        let fila = e.parentElement.parentElement;
        let codigoProducto = fila.children[0].children[0].value;
        let bodega = fila.children[1].children[1];
        let cantidad = fila.children[1].children[0].value;
        if (bodega.value != '' && codigoProducto != '' && cantidad > 0) {
            $.ajax({
                type: 'get',
                url: "{{ route('fact_contable.verificarStock') }}",
                datatype: 'json',
                data: {
                    'cod_producto': codigoProducto,
                    'bodega': bodega.value,
                    'cantidad': cantidad
                },
                success: function(data) {
                    if (data.respuesta == 'no') {
                        swal("Error!", `${data.msj}`, "error");
                        //   alert(data.mensaje)
                        bodega.value = null;
                    }
                },
                error: function(data) {}
            });
        }
    }
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

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");
        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
        if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "true");
            $(e).parent().next().next().next().next().children().attr("readonly", "true");
            // $(e).parent().next().next().next().next().children().val(0);
            // $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            //$(e).parent().next().next().next().next().next().children().val(0);
            //$(e).parent().next().next().next().next().children().val(0);
        }
        $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
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
                // $(e).parent().next().next().children().find('option').remove();
                $.each(data, function(key, value) {
                    if (tipo == value.nivel) {
                        selected = "selected";
                    } else {
                        selected = "";
                    }
                    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
                });
            },
            error: function(data) {}
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
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('active', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        // verificar(this);
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        var copago = $(this).parent().next().next().children().val();
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.copago', function() {
        verificar(this);
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();
        var copago = $(this).val();
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.pdesc', function() {
        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var pdesc = $(this).val();
        if (parseFloat(pdesc) > parseFloat(m)) {
            swal("El descuento no puede ser mayor a " + m + "%");
            $(this).val(m).focus();
        }
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        descuento = redondeafinal(descuento);
        $(this).parent().next().children().val(descuento);
        var copago = $(this).parent().prev().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().children().val(total);
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
        //alert(total_pagos);
        $("#valor_totalPagos").val(total_pagos);
    });

    function verValores() {
        $('.cneto').each(function(i, obj) {
            verificar(obj);
        });
    }
    verValores();
    totales(0);

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function totales(e) {
        var subt12 = [];
        var subt0 = [];
        var descuento = [];
        var descuentosub0 = 0;
        var descuentosub12 = 0;
        var sb12 = 0;
        var sb0 = 0;
        var d = 0;
        var copagoTotal = 0;
        if (e == 0) {
            $('.cneto').each(function(i, obj) {
                var cant = $(this).val();
                var e = $(this).parent().prev().children().closest(".select2_cuentas");
                var precio1 = 0;
                var precio2 = 0;
                var precio3 = 0;
                var precio4 = 0;
                var precio5 = 0;
                var precioAut = 0;
                var tipo = $("#tipo_cliente").val();
                var precio = $(this).parent().next().children().val();
                var copago = $(this).parent().next().next().children().val();
                var descuento = $(this).parent().next().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                precio = precio != null ? precio : 0;
                var total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
                if (iva) {
                    subt12.push(total);
                    sb12 = sb12 + total;
                    descuentosub12 += parseFloat(descuento);
                } else {
                    subt0.push(total);
                    sb0 = sb0 + total;
                    descuentosub0 += parseFloat(descuento);
                }
                copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                //aqui falta
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                var descuento_total = descuentosub12 + descuentosub0;
                var sum = sb12 + sb0 - descuento_total;
                sum = redondeafinal(sum);
                $("#base").html(sum.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;
                if (d > 0) {
                    if (sb12 > 0) {
                        ti = iva * (sb12 - descuentosub12);
                    }
                }
                ti = redondeafinal(ti);
                $("#tarifa_iva").html(ti.toFixed(2));
                var t = sb12 + sb0 + ti - d;
                var totax = sum + ti;
                totax = redondeafinal(totax);
                $("#total").html(totax.toFixed(2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                sb12 = redondeafinal(sb12);
                $("#subtotal_121").val(sb12);
                sb0 = redondeafinal(sb0);
                d = redondeafinal(d);
                $("#subtotal_01").val(sb0);
                $("#descuento1").val(d);
                $("#tarifa_iva1").val(ti);
                $("#total1").val(totax);
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

    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        rowk.className = "well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }
    //$(".btn_add").click(function() {
    function guardarVenta() {
        //if(validarCedula($('#identificacion_cliente').val())){
        if ($('#id_seguro').val() == '') {
            alertas("error", "Error...", "Seleccione un seguro");
            return;
        }
        document.getElementById('contador_pago').value = '{{ count($ct_for_pag) }}';
        if ($("#form").valid()) {
            $(".print").css('visibility', 'visible');
            $(".btn_add").attr("disabled", true);
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
                    if (data.status == 'error') {
                        alertas("error", "Error...", data.msj);
                        return;
                    }
                    $("#asiento").val(data.idasiento);
                    $("#id").val(data.idventa);
                    $("#numero").val(data.idventa);
                    // swal("Guardado con Exito!");
                    let ingreso = "";
                    if (data.id_comprobante_ingreso != "") {
                        ingreso = `<a href="{{url("contable/cliente/comprobante/ingresos")}}/${data.id_comprobante_ingreso}" target="_blank"><b>COMP. INGRESO ${data.id_comprobante_ingreso}</b></a>`;
                    }
                    alertas("success", "Exito...", `Guardado con exito<br>${ingreso}`)
                },
                error: function(data) {
                    swal("Ocurrio un error");
                    $(".btn_add").removeAttr('disabled');
                }
            });
        } else {
            swal("Tiene campos vacios");
        }
        // }else{
        //     alert("Identificación del cliente incorrecta");
        // }
    }

    function revision_total(id) {
        var valor = $('#valor' + id).val();
        ntotal = valor * 1;
        $('#valor_base' + id).val(ntotal.toFixed(2));
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
    $('#btn_pago').click(function(event) {
        document.getElementById('contador_pago').value = '{{ count($ct_for_pag) }}';
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.innerHTML = '<td><select class="form-control" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');"><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="form-control input-number" value="{{date("Y-m-d")}}" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '" name="tipo_tarjeta' + id + '" style="width: 175px;height:25px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="form-control" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="width: 80%;height:20px;" autocomplete="off" class="form-control" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="form-control" style="width: 80%;height:20px;"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="form-control text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);"></td><td><input class="form-control input-number" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,' + id + ',2);"></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;
    });

    function reloadCliente() {
        if (!existeCliente) {
            //swal("epa");
        }
    }

    function goBack() {
        location.href = "{{ route('ventas.index_cierre') }}";
    }
    $('body').on('click', '.chef', function() {
        //$(this).parent().parent().remove();
        totales(0);
    });

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
            msj += "Por favor,Selecione la divisa\n";
        }
        //Datos Generales
        if (id_emp == "") {
            msj += "Por favor, Seleccione la Empresa\n";
        }
        if (sucurs == "") {
            msj += "Por favor, Seleccione la Sucursal\n";
        }
        if (punt_emision == "") {
            msj += "Por favor, Seleccione el Punto de Emision\n";
        }
        //Paciente
        if (cedula == "") {
            msj += "Por favor,Ingrese la cedula del paciente\n";
        }
        if (nombre_paciente == "") {
            msj += "Por favor,Ingrese el nombre del paciente\n";
        }
        if (seguro_paciente == "") {
            msj += "Por favor,Seleccione el seguro paciente\n";
        }
        if (proced == "") {
            msj += "Por favor,Ingrese los Procedimientos del paciente\n";
        }
        if (fech_proced == "") {
            msj += "Por favor,Seleccione la fecha de procedimientos\n";
        }
        //Cliente
        if (cliente == "") {
            msj += "Por favor,Selecione el cliente\n";
        }
        if (ruc_ced_cliente == "") {
            msj += "Por favor,Ingrese el ruc del cliente\n";
        }
        if (direccion == "") {
            msj += "Por favor,Ingrese la direccion del cliente\n";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente\n";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente\n";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente\n";
        }
        //Vendedor/Recaudador
        if (recaud == "") {
            msj += "Por favor,Selecione el recaudador\n";
        }
        if (ced_recaudador == "") {
            msj += "Por favor,Ingrese la cedula del recaudador\n";
        }
        //Detalle Asiento
        if (det_asiento == "") {
            msj += "Por favor,Ingrese el detalle del asiento\n";
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
                        swal({
                                title: `{{trans('proforma.GuardadoCorrectamente')}}`,
                                buttons: true,
                            })
                            .then((value) => {
                                location.href = "{{route('venta_index')}}";
                            });
                    },
                    error: function(data) {}
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
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad' + id).val("0.00");
            $('#desc' + id).val("0.00");
            $('#extendido' + id).val("0.00");
            return false;
        }
        if (num.length == 0) {
            alert("Cantidad no permitida");
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
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0) {
            alert("Cantidad no permitida");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var st = $('#stock' + id).val();
        var nu = parseInt(elemento.value, 10);
        if (nu > st) {
            alert("Cantidad no debe ser mayor al stock");
            $('#cantidad' + id).val("0");
            $('#total_acum' + id).val('');
        }
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 1 || numero > 999999999) {
            alert("Cantidad no permitida");
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
            alert("Total no permitido");
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
            alert("Debe ingresar el código, nombre, cantidad del producto");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var prec = elemento.value;
        if (prec.length == 0) {
            alert("Precio no permitido");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 1 || numero > 999999999) {
            alert("Precio no permitido");
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
                $('#ruc_cedula').val(data.client_identificacion);
                $('#direccion').removeAttr('disabled');
                $('#direccion').val(data.client_direccion);
                $('#identif_cliente').val(data.client_identificacion);
                $('#telefono').val(data.client_telefono);
                $('#email').val(data.client_email);
                $('#ciudad').val(data.client_ciudad);
            },
            error: function(data) {}
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
            }
        })
    });*/
    $("#vendedor").change(function() {
        $('#cedula_vendedor').val($('option:selected', $(this)).data("id"));
    });
    //Obtengo datos del Recaudador de la Tabla Usuarios
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
            error: function(data) {}
        })
    });

    function ingresar_cero() {
        var secuencia_factura = $('#numero').val();
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $('#numero').val('');
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
                }
                $('#numero').val(secuencia + secuencia_factura);
            }
        } else {
            swal("Error!", "Valor no permitido", "error");
            $('#numero').val('');
        }
    }
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
            success: function(data) {},
            error: function(data) {}
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
                $('#codigo' + id).val(data.cod_product);
                $('#cost_prod' + id).val(data.cost_vent);
                //$('#iva_obt'+id).val(data.iva_prod);
                if (data.iva_prod == '1') {
                    $('#iva' + id).prop("checked", true);
                }
            },
            error: function(data) {}
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
            error: function(data) {}
        })
    }
    /* function obtener_num_factura(){
         $.ajax({
             url:"{{route('numero_factura')}}",
             type: 'get',
             datatype: 'json',
             success: function(data){
                $('#nfactura').val(data);
             },
             error: function(data){
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
        //$("#fecha_proced").val(fe_proced);
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
            error: function(data) {}
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
            error: function(data) {}
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
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#punto_emision").empty();
                        var select = '';
                        $.each(data, function(key, registro) {
                            select = '';
                            if (registro.codigo_caja == 100)
                                select = 'selected';
                            $("#punto_emision").append('<option value=' + registro.id + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#punto_emision").empty();
                    }
                }
            },
            error: function(data) {}
        })
    }

    function guardarCiudad() {
        ciudad = document.getElementById('ciudad_cliente').value;
        cedula = document.getElementById('identificacion_cliente').value;
        direccion = document.getElementById('direccion_cliente').value;
        $.ajax({
            type: 'post',
            url: "{{ route('ventas.guardarCiudad') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'ciudad': ciudad,
                'direccion': direccion,
                'cedula': cedula
            },
            success: function(data) {},
            error: function(data) {}
        })
        // alert(id);
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
    $(function() {
        $('#fecha_proced').datetimepicker({
            format: 'YYYY/MM/DD'
        });
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });
</script>
@endsection