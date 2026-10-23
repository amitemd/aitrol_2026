@extends('contable.ventas.base')
@section('action-content')
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<style type="text/css">
    .input-number {
        width: 80%;
        height: 20px;
    }

    .hidden-paginator {

        display: none;

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
        justify-content: space-between;
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .hidden-paginator {

        display: none;

    }
</style>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content">
    <div class="modal fade bd-example-modal-lg" id="ver_detalle" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div style="border-radius: 12px;" class="modal-content" id="content">
            </div>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Ventas</a></li>
            <li class="breadcrumb-item"><a href="../ventas">Registro de Factura de Insumos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
        </ol>
    </nav>
    <div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div style="border-radius: 12px;" class="modal-content" id="content">

            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Nueva Factura de Insumos.</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray">
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
        <form class="form-vertical" method="post" action="{{route('insumos_search')}}" id="form">
            {{ csrf_field() }}
            <div class="header row">
                <div class="form-group col-xs-6 col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="id" class=" label_header">Id</label>
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
                        <label for="numero" class=" label_header">N&uacute;mero</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="text" class="form-control" name="numero" id="numero" onchange="ingresar_cero()" value="">
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
                        <input type="text" class="form-control" name="tipo" id="tipo" value="VENFA-CO" readonly>
                        @if ($errors->has('tipo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tipo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6 col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="asiento" class="label_header">Asiento</label>
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
                        <label for="fecha_asiento" class="label_header">Fecha</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="fecha" type="text" class="form-control" name="fecha_asiento" value="{{ old('fecha_asiento') }}" required>
                        @if ($errors->has('fecha_asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2 px-1">
                    <div class="col-md-12 px-0">
                        <label for="orden_venta" class="label_header">Orden de Venta</label>
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
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header">Empresa</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input type="hidden" name="empresaf" id="empresaf" value="{{$empresa->id}}">
                        <input id="empresa" type="text" class="form-control" name="empresa" value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly>
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
                            <input class="electros" @if($empresa->electronica==1) @else disabled @endif id="toggleswitch" type="checkbox">

                            <span class="slider round"></span>
                        </label>
                        <input type="hidden" id="electronica" name="electronica" value="0">
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-1  px-1">
                    <div class="col-md-12 px-0">
                        <label for="sucursal" class="label_header">Sucursal</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $value)
                            <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-3  col-md-1  px-1">
                    <div class="col-md-12 px-0">
                        <label for="punto_emision" class="label_header">P. Emision</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="punto_emision" id="punto_emision" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-3  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="numero_autorizacion" class="label_header"># Autorización</label>
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
                <div class="form-group col-xs-3  col-md-1  px-1">
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
                <div class="form-group col-xs-3  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="recaudador" class="label_header">Recaudador</label>
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
                <div class="form-group col-xs-3  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="vendedor" class="label_header">Vendedor</label>
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
                        <label for="cliente" class="label_header text-left">Cliente</label>
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" value="{{ old('identificacion_cliente') }}" onchange="reloadCliente()" required placeholder="Identificacion">
                        @if ($errors->has('identificacion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" value="{{ old('nombre_cliente') }}" onchange="reloadCliente()" required placeholder="Nombre">
                        @if ($errors->has('nombre_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente" value="{{ old('direccion_cliente') }}" required placeholder="Dirección">
                        @if ($errors->has('direccion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente" value="{{ old('ciudad_cliente') }}" required placeholder="Ciudad">
                        @if ($errors->has('ciudad_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="mail_cliente" type="text" class="form-control" name="mail_cliente" value="{{ old('mail_cliente') }}" required placeholder="Mail">
                        @if ($errors->has('mail_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_cliente') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="col-md-2 px-0">
                        <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente" value="{{ old('telefono_cliente') }}" required placeholder="Teléfono">
                        <input id="tipo_cliente" type="hidden" class="form-control" name="tipo_cliente" value="{{ old('tipo_cliente') }}" required placeholder="tipo">
                        @if ($errors->has('telefono_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono_cliente') }}</strong>
                        </span>
                        @endif
                        <input type="hidden" value="{{$valorIva}}" name="ivareal" id="ivareal" class="form-control">
                    </div>
                </div>
                <div class="form-group col-xs-12  px-1">
                    <label for="concepto" class="label_header">Descripción General</label>
                    <input class="form-control" name="concepto" id="concepto" maxlength="100" placeholder="Ingrese concepto...">
                </div>
                <div class="col-md-12 px-1">
                    <label class="label_header text-left">Información Adicional</label>
                </div>
                <div class="col-md-2 px-1">
                    <label class="label_header">Dirección</label>
                    <input name="direccioninfo" type="text" class="form-control" placeholder="Ingrese Direccion">
                </div>
                <div class="col-md-2  px-1">
                    <label class="label_header">Email</label>
                    <input name="emailinfo" type="text" class="form-control" placeholder="Ingrese email">
                </div>
                <div class="col-md-2  px-1">
                    <label class="label_header">Paciente</label>
                    <input type="text" class="form-control" name="pacienteinfo" placeholder="Ingrese Paciente">
                </div>
                <div class="col-md-2  px-1">
                    <label class="label_header">Seguro</label>
                    <input type="text" class="form-control" name="segurosinfo" placeholder="Ingrese Seguros">
                </div>
                <div class="col-md-2  px-1">
                    <label class="label_header">Procedimiento</label>
                    <input type="text" class="form-control" name="procedimientoinfo" placeholder="Ingrese Procedimiento">
                </div>
                <div class="col-md-2 px-1">
                    <label class="label_header">Fecha Procedimiento</label>
                    <div class="input-group date ">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" value="{{ old('fecha_proced') }}" name="fechaprocedimientoinfo" id="fecha_proced" class="form-control" placeholder='Fecha de Proce.'>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_desde" class="label_header">Desde</label>
                        <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="0">
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group date">
                            <div class="input-group-addon">

                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control dates" name="fecha_desde" id="fecha_desde" value="@if(!is_null($fecha_desde)) {{$fecha_desde}} @endif" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = '';"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="fecha_hasta" class="label_header">Hasta</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control dates" name="fecha_hasta" value="@if(!is_null($fecha_hasta)) {{$fecha_hasta}} @endif" id="fecha_hasta" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = '';"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="id_seguro" class="label_header">Seguro</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="id_seguro" id="id_seguro">
                            <option value="1">Seguro ...</option>
                            @foreach($seguros as $seguro)
                            <option @if(isset($secure)) @if(($secure)==$seguro->id) selected="selected" @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="tipo_servicio" class="label_header">Tipo Servicio</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <select class="form-control" name="tipo_servicio" id="tipo_servicio" required>
                            <option @if(($tipox)==1) selected="selected" @endif value="1">Consulta</option>
                            <option @if(($tipox)==2) selected="selected" @endif value="2">Procedimiento</option>
                            <option @if(($tipox)==3) selected="selected" @endif value="3">Todos</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <button type="button" class="btn btn-primary btn-sm search btn-gray" id="boton_buscar" style="margin-top:10px"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
                </div>
                @if(isset($ordenes))
                @if(count($ordenes)>0)
                <div class="form-group col-md-1 col-xs-2">
                    <button type="button" class="btn btn-primary btn-sm btn-gray" id="butonexcel" onclick="excelPreview()" style="margin-top:10px">
                        <span class="glyphicon glyphicon-pdf" aria-hidden="true"></span> Preview Excel</button>
                </div>
                <!-- La otra forma de humana -->
                <div class="form-group col-md-2 col-xs-2" style="text-align: center;">
                    <label class="control-label col-md-12">Ambulatorio</label>
                    <input type="checkbox" name="amBu" id="amBu" class="ambulatorio col-md-12" value="0">
                </div>
                @endif
                @endif
            </div>
            <div class="col-md-12" id="items" style=" height: 400px; overflow-y: scroll;">
                @if(isset($ordenes))
                @php
                $contador=0;
                @endphp
                @foreach($ordenes as $x)
                @if(!is_null($x))
                @php
                //dd($x);
                $count=0;
                @endphp
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <input type="checkbox" name="activo[]" value="1" class="relactivo" />
                        <input type="hidden" name="veractivo[]" class="veractivo" value="0">
                        <input type="hidden" class="pt" value="{{$x->id_paciente}}">
                        <input type="hidden" class="patients" value="{{$x->id}}">
                        {{$x->nombres_paciente}} - {{substr($x->fecha_procedimiento,0,10)}} #recibo : @if(isset($x->orden_venta)) {{$x->orden_venta}} @endif
                        <div class="panel-body" style="padding:0;">
                            <div class="col-md-12 table-responsive " style="padding:0 !important;">
                                <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                                    <thead>
                                        <tr class="well-dark">
                                            <th width="35%" tabindex="0">Descripci&oacute;n del Producto</th>
                                            <th width="10%" tabindex="0">Cantidad</th>
                                            <th width="10%" tabindex="0">Precio</th>
                                            <th width="10%" tabindex="0">Cobrar Seguro</th>
                                            <th width="10%" tabindex="0">% Desc</th>
                                            <th width="10%" tabindex="0">Descuento</th>
                                            <th width="10%" tabindex="0">Precio Neto</th>
                                            <th width="5%" tabindex="0">IVA</th>
                                            <th width="10%" tabindex="0">
                                                <button type="button" class="btn btn-warning btn-gray anadir" onclick="nuevo('{{$contador}}','{{$x->id}}','{{$x->id_paciente}}')"> <i class="fa fa-plus"></i> </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="entrega{{$contador}}">
                                        @foreach($x->detalles as $z)
                                        @if($z->extendido>0)
                                        <!-- NECESITO QUE NO SE VEAN ESTOS ITEMS EN LA CONGLOMERADA -->
                                        @php
                                        $validate= \Sis_medico\ParametersConglomerada::dontSee($z->id_ct_productos);
                                        $productofind= \Sis_medico\Ct_productos::where('codigo',$z->id_ct_productos)->first();
                                        @endphp
                                        @if(!$validate)
                                        <tr>
                                            <td style="max-width:100px;">
                                                <input type="hidden" class="codigo_producto" />
                                                <input type="hidden" name="nombre_producto[]" value="<?= $productofind->nombre; ?>" />
                                                <select class="form-control select2 productos" name="producto<?= $count; ?>" style="width:100%; height:20px;" required onchange="verificar(this); verificar_stock(this);darNombreProducto(this,'<?= $count; ?>');">
                                                    <option> </option>
                                                    @if($productofind!=null)
                                                    <option selected="selected" value="{{$z->id_ct_productos}}">{{$productofind->nombre}}</option>
                                                    @else
                                                    <option value="{{$z->id_ct_productos}}"> {{$z->id_ct_productos}}</option>
                                                    @endif
                                                </select>
                                                <input type="hidden" class="id_patient" value="{{$x->id_paciente}}">
                                                <input type="hidden" name="verid[]" class="verid" value="1">
                                                <textarea wrap="hard" rows="3" class="form-control px-1 desc_producto" placeholder="Detalle del producto">{{$z->detalle}}</textarea>
                                                <input type="hidden" name="iva[]" class="iva" />
                                            </td>
                                            <td style="text-align:center;">
                                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="@if(!is_null($z->cantidad)) 1 @endif" required onchange="verificar_stock(this);">
                                                @if($z->id_ct_productos == 'LABS')
                                                <button style="margin-top: 12px!important;" onclick="modalDetalle(event, <?= $x->id; ?>)" type="button" class="btn btn-success btn-succes " data-remote=" {{ route('ventas.modalDetalleInsumos', ['id'=> $x->id ]) }} " class="btn btn-info btn-sm" data-toggle="modal" data-target="#ver_detalle">
                                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Ver Detalles
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                $precio=$z->descuento>0?$z->precio-$z->descuento: $z->copago;
                                                if($precio==0){
                                                    if($z->precio>0){
                                                        $precio=$z->precio-$z->descuento;
                                                    }
                                                    else
                                                        $precio=0.00;
                                                }                                                                           
                                                @endphp
                                                <input type="text" class="form-control pneto" onkeypress="return isNumberKey(event)" style="width:40%;display:inline;height:20px;" value="{{$precio}}">
                                                <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(3);" value="0" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                            </td>
                                            <td>
                                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(3);" value="0" required>
                                            </td>
                                            <td>
                                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(3);" value="0.00" required>
                                            </td>
                                            <td>
                                                <input class="form luffy" type="checkbox" @if($z->check_iva>0) selected="selected" @endif style="width: 80%;height:20px;" name="valoriva[]" value="@if($z->check_iva!=null){{$z->check_iva}}@endif">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-gray delete">
                                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @php $contador++; @endphp
                @endforeach
                @else
                No hay datos.
                @endif
            </div>
            <div>
                <table class="table table-bordered table-hover dataTable">
                    <tfoot class='well'>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal {{$porcentajeIva}}%</td>
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
                            <td colspan="2" class="text-right">Subtotal 0%</td>
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
                            <td colspan="2" class="text-right">Descuento</td>
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
                            <td colspan="2" class="text-right">Subtotal sin Impuesto</td>
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
                            <td colspan="2" class="text-right">Tarifa Iva {{$porcentajeIva}}%</td>
                            <td id="tarifa_iva" class="text-right px-1">0.00</td>
                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td id="total" class="text-right px-1">0.00</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td colspan="2" class="text-right"><strong>Por Cobrar Seguro</strong></td>
                            <td id="copagoTotal" class="text-right px-1">0.00</td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-12" style="height:30px;">
                <div class="row head-title">
                    <div class="col-md-12 cabecera">
                        <label class="color_texto">Forma de Pago</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 table-responsive ">
                <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                    <thead>
                        <tr>
                            <th width="20%" style="text-align: center;">Metodo</th>
                            <th width="10%" style="text-align: center;">Fecha</th>
                            <th width="15%" style="text-align: center;">Tipo Tarjeta</th>
                            <th width="10%" style="text-align: center;">Número</th>
                            <th width="15%" style="text-align: center;">Banco</th>
                            <th width="10%" style="text-align: center;">Cuenta</th>
                            <th width="10%" style="text-align: center;">Girado a</th>
                            <th width="10%" style="text-align: center;">Valor</th>
                            <th width="10%" style="text-align: center;">Valor Base</th>
                            <th width="5%" style="text-align: center;">
                                <button id="btn_pago" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_pago">
                        <tr style="display:none" id="mifila">
                            <td style="max-width:100px;">
                                <input type="hidden" class="codigo_producto" />
                                <select class="form-control select2 productos" name="bodega" style="width:100%; height:20px;" required onchange="verificar(this);verificar_stock(this);">
                                    <option> </option>
                                    @foreach($productos as $value)
                                    <option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->nombre}}" data-descuento="{{$value->mod_desc}}" data-descripcion="{{$value->descripcion}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                                <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onchangue="verificar_stock(this);" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" onchange="verificar_stock(this)">
                                    <option> </option>
                                    @foreach ($bodegas as $value)
                                    @if (!is_null($value))
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control select2_precio pneto" style="width:70%;display:inline;" required>
                                    <option value="0"> </option>
                                </select>
                                <button type="button" class="btn btn-info btn-gray btn-xs cp">
                                    <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" required>
                            </td>
                            <td>
                                <input class="form luffy" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">
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
            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button id="boton_guardar" type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">
    function darNombreProducto(comp, count) {
        alert($('select[name="producto' + count + '"] option:selected').text());
        $('#nombre_producto' + count).val($('select[name="producto' + count + '"] option:selected').text());
    }

    function modalDetalle(e, id) {
        e.preventDefault();
        $.ajax({
            type: "get",
            url: "{{ route('ventas.modalDetalleInsumos', ['id'=>" + id + "]) }}",
            data: {
                'id': id,
            },
            datatype: "html",
            success: function(datahtml, data) {
                console.log(data);
                $("#content").html(datahtml);
                $("#ver_detalle").modal("show");
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function ingresar_cero() {
        var secuencia_factura = $('#numero').val();
        console.log(secuencia_factura);
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
                console.log(secuencia_factura.length)
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
    var fila = $("#mifila").html();
    $(function() {
        $('.dates').datetimepicker({
            format: 'YYYY/MM/DD',
        });
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
    var input = document.getElementById('toggleswitch');
    input.addEventListener('change', function() {
        if (this.checked) {
            console.log("ahora");
            $("#electronica").val(1);
            $('#numero_autorizacion').attr('readonly', true);
            $('#numero').attr('readonly', true);
        } else {
            $("#electronica").val(0);
            $('#numero_autorizacion').attr('readonly', false);
            $('#numero').attr('readonly', false);
        }
    });
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
    $("#identificacion_paciente").autocomplete({
        classes: {
            "ui-autocomplete": "dob_autocomplete",
        },
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
                console.log(ui.item);
                $("#nombre_paciente").val(ui.item.nombre);
                $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
            } else {
                swal("No Existe el paciente");
            }
        },
        selectFirst: true,
        minLength: 3,
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
                    }
                });
            },
            change: function(event, ui) {
                console.log(ui);
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

    function revision_total(id) {
        var valor = $('#valor' + id).val();
        ntotal = valor * 1;
        $('#valor_base' + id).val(ntotal.toFixed(3));
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
                    }
                });
            },
            change: function(event, ui) {
                if (ui.item != null) {
                    console.log(ui.item);
                    $("#nombre_paciente").val(ui.item.nombre);
                    $("#id_seguro option[value=" + ui.item.seguro + "]").attr('selected', 'selected');
                } else {
                    swal("No Existe el paciente");
                }
            },
            selectFirst: true,
            minLength: 1,
        });
    });
    $('body').on('click', '.delete', function() {
        console.log($(this));
        $(this).parent().parent().remove();
        totales(0);
    });
    $(document).ready(function() {
        $(".pneto").change()
        limpiar();
        $('.select2_cliente').select2({
            tags: false
        });
        $('#iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%'
        });
        $('.select2').select2({
            ajax: {
                url: '{{route("venta.selectsearch")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    console.log(data);
                    return {
                        results: data
                    };
                }
            }
        });
    });
    var areYouReallySure = false;

    function areYouSure() {
        if (allowPrompt) {
            if (!areYouReallySure && true) {
                areYouReallySure = true;
                var confMessage = "Estas saliendo del sitio, ¿Estas seguro que deseas salir?";
                return confMessage;
            }
        } else {
            allowPrompt = true;
        }
    }
    var allowPrompt = true;
    window.onbeforeunload = areYouSure;
    $('body').on('click', '.luffy', function() {
        totales(0);
    });
    $('body').on('click', '.cp', function() {
        var clase = $(this).prev().attr('class');
        var html = '<input type="text" class="form-control pneto" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" style="width:40%;display:inline;height:20px;">' +
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
            $(this).parent().append(html);
            verificar($(this).parent().prev().prev().children().closest('.select2'));
            $(this).prev().remove();
            $(this).remove();
        }
    });

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $(e).val(); //$('option:selected',e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");
        $(e).parent().children().closest(".codigo_producto").val($('option:selected', e).data("name"));
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

            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    $('body').on('blur', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('active', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.pneto', function() {
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())).toFixed(3) - descuento - copago;
        total = redondeafinal(total, 3);
        $(this).parent().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        // console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().next().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.productos', function() {
        console.log(this);
    });

    function cnx(e) {
        var cant = $(e).val();
        var precio = $(e).parent().next().children().val();
        var copago = $(e).parent().next().next().children().val();
        var descuento = $(e).parent().next().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(e).parent().next().next().next().next().next().children().val(total);
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num); // to fixed num 
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }
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
        console.log("el descuento maximo debe de ser", m, pdesc);
        if (parseFloat(pdesc) > parseFloat(m)) {
            swal("El descuento no puede ser mayor a " + m + "%");
            $(this).val(m).focus();
        }
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
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
        total = redondeafinal(total);
        $(this).parent().next().children().val(total);
        totales(0);
    });
    $(document).on('keydown', '.select2', function(e) {
        if (e.originalEvent && e.which == 40) {
            e.preventDefault();
            $(this).siblings('select').select2('open');
        }
    });
    $('.select').select2({
        selectOnClose: true
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
        var descuentosub0 = 0;
        var descuentosub12 = 0;
        var d = 0;
        var copagoTotal = 0;
        if (e == 0) {
            $('.cneto').each(function(i, obj) {
                var veractivo = $(this).parent().parent().parent().parent().hasClass('noacti');
                if (veractivo == false) {
                    cnx(this);
                    var cant = $(this).val();
                    $(this).parent().prev().find('.productos').attr('name', 'codigo[]');
                    $(this).parent().prev().find('.codigo_producto').attr('name', 'nombre[]');
                    $(this).parent().prev().find('.id_patient').attr('name', 'paciente[]');
                    $(this).attr('name', 'cantidad[]');
                    var e = $(this).parent().prev().children().closest(".select2_cuentas");
                    var precio1 = 0;
                    var precio2 = 0;
                    var precio3 = 0;
                    var precio4 = 0;
                    var precio5 = 0;
                    var precioAut = 0;
                    var tipo = $("#tipo_cliente").val();
                    var precio = $(this).parent().next().children().val();
                    $(this).parent().next().children().attr('name', 'precio[]');
                    var copago = $(this).parent().next().next().children().val();
                    $(this).parent().next().next().children().attr('name', 'copago[]');
                    var descuento = $(this).parent().next().next().next().next().children().val();
                    $(this).parent().next().next().next().next().children().attr('name', 'descuento[]');
                    d = parseFloat(d) + parseFloat(descuento);
                    var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                    precio = precio != null ? precio : 0;
                    var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);
                    if (iva == 1) {
                        subt12.push(total);
                        sb12 = sb12 + total;
                        descuentosub12 += parseFloat(descuento);
                    } else {
                        subt0.push(total);
                        sb0 = sb0 + total;
                        descuentosub0 += parseFloat(descuento);
                    }
                    copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                    $("#subtotal_12").html(sb12.toFixed(2));
                    $("#subtotal_0").html(sb0.toFixed(2));
                    $("#descuento").html(d.toFixed(2));
                    var descuento_total = descuentosub12 + descuentosub0;
                    var sum = sb12 + sb0 - descuento_total;
                    $("#base").html(sum.toFixed(2));
                    var iva = $("#ivareal").val();
                    var ti = iva * sb12;
                    if (d > 0) {
                        if (sb12 > 0) {
                            ti = iva * (sb12 - descuentosub12);
                        }
                    }
                    ti = redondeafinal(ti, 3);
                    $("#tarifa_iva").html(ti.toFixed(2, 2));
                    var t = sb12 + sb0 + ti - d;
                    var totax = sum + ti;
                    totax = redondeafinal(totax, 3);
                    copagoTotal = redondeafinal(copagoTotal, 3);
                    sb12 = redondeafinal(sb12, 3);
                    sb0 = redondeafinal(sb0, 3);
                    d = redondeafinal(d, 3);
                    $("#total").html(totax.toFixed(3, 2));
                    $("#copagoTotal").html(copagoTotal.toFixed(3, 2));
                    $("#subtotal_121").val(sb12);
                    $("#subtotal_01").val(sb0);
                    $("#descuento1").val(d);
                    $("#tarifa_iva1").val(ti);
                    $("#total1").val(totax);
                    $("#totalc").val(copagoTotal);
                } else {
                    $(this).parent().prev().find('.codigo_producto').attr('name', '');
                    $(this).parent().prev().find('.id_patient').attr('name', '');
                    $(this).parent().prev().find('.productos').attr('name', '');
                    $(this).attr('name', '');
                    $(this).parent().next().children().attr('name', '');
                    $(this).parent().next().next().children().attr('name', '');
                    $(this).parent().next().next().next().next().children().attr('name', '');
                }
            });
        }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;
        return true;
    }

    function nuevo(id, data, patient) {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("entrega" + id).insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = "id_orden[]";
        input.value = data;
        rowk.innerHTML = fila;
        rowk.append(input);
        var input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = "paciente[]";
        input2.value = patient;
        rowk.append(input);
        rowk.className = "well";
        $('.select2').select2({
            tags: false
        });
    }

    function verificar_stock(e) {
        let fila = e.parentElement.parentElement;
        let codigoProducto = fila.children[0].children[1].value;
        let bodega = fila.children[1].children[1];
        let cantidad = fila.children[1].children[0].value;
        console.log(fila.children[1].children);
        console.log(`codigo : ${codigoProducto} - bodega: ${bodega.value} - cantidad: ${cantidad}`);
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
                    console.log(data)
                    if (data.respuesta == 'no') {
                        swal("Error!", `${data.msj}`, "error");
                        //   alert(data.mensaje)
                        bodega.value = null;
                        console.log(bodega.value);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    }
    $(".btn_add").click(function() {
        if ('#id_seguro' == '') {
            alertas('error', 'error', 'Debe seleccionar un seguro');
            return;
        }
        if ($("#form").valid()) {
            document.querySelector(".btn_add").style.display = 'none';
            $(".print").css('visibility', 'visible');
            $("#mifila").html("");
            $.ajax({
                url: "{{route('ventas_storeVarios')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    console.log(data);
                    $("#asiento").val(data.idasiento);
                    $("#id").val(data.idventa);
                    $("#numero").val(data.idventa);
                    if (data.respuesta == "existe") {
                        swal(`${data.mensaje}`);
                        document.querySelector(".btn_add").style.display = 'block';
                    } else {
                        swal("Guardado con Exito!");
                    }
                },
                error: function(data) {
                    console.log(data.responseText);
                    swal("Ocurrio un error!");
                }
            });
        } else {
            swal("Existen campos vacios");
        }
    });
    $('.search').click(function() {
        $('form#form').submit();
    });
    $('body').on('click', '#preview', function() {
        var desde = $("#fecha_desde").val();
        var hasta = $("#fecha_hasta").val();
        var seguro = $("#id_seguro").val();
        var tipo = $("#tipo_servicio").val();
        console.log(desde);
        $.ajax({
            url: "{{route('venta.preview')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                finicio: desde,
                ffin: hasta,
                seguro: seguro,
                tipo: tipo
            },
            success: function(data) {
                console.log(data);
                var myhtml = "<table  class='table table-bordered table-hover dataTable'><thead><td># ODA</td><td>Fecha de cita</td>" +
                    "<td>Paciente</td>" +
                    "<td>Pend Factura</td>" +
                    "<td></td>" +
                    "</thead><tbody>";
                data.forEach(function(dat) {
                    console.log("asdasdas", dat);
                    var nombre = dat.apellido1 + " " + dat.apellido2 + " " + dat.nombre1;
                    console.log(nombre);
                    var valor = dat.valor_final != null ? dat.valor_final : 0;
                    var ht = "<tr><td width='20%'>" + dat.idventa + "</td><td width='20%'>" + dat.fechaini + "</td>" +
                        "<td width='40%'>" + nombre + "</td>" +
                        "<td width='20%'>" + valor + "</td>" +
                        "<td><input type='checkbox' class='prd_ch' checked name='prd_ch[]' /><input type='hidden' class='prd_activo' value='1' name='prd_activo[]' /><input type='hidden' class='prd_val' value='" + valor + "' name='prd_val[]' /><input type='hidden' value='" + dat.hc_id_procedimiento + "' name='prd_id[]' /></td></tr>";
                    myhtml = myhtml + ht;
                });
                myhtml = myhtml + "</tbody></table>";
                $("#datos").html(myhtml);
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
    });

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        return [year, month, day].join('-');
    }

    function checkvalues() {
        var vals = $(".ids").val();
        var vsl = document.getElementsByClassName('ids');
        var arr = Array.from(vsl);
        var n = 0;
        var val = $("#items").last().find('.ids');
        arr = $("#items").last().find('.select2');
        arr = [];
        $("#items").last().find('.ids').each(function() {});
        $("#items").last().find('.select2').each(function() {
            $(this).find('option[value="' + arr[n] + '"]').attr('selected', 'selected').change()
            n++;
        });
    }

    function reloadCliente(data) {
        var sd = $(data).val();
    }

    function consultas() {
        var e = document.createElement("table");
        var nuevafila = $("#mifila").html();
        var rowk = e.insertRow(-1);
        rowk.innerHTML = nuevafila;
        rowk.className = "well";
        var html = '<div class="panel panel-default">' +
            '<div class="panel-body" style="padding:0;"><div class="col-md-12 table-responsive" style="padding:0 !important;"><table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">' +
            '<thead><tr class="well-dark"><th width="35%" class="" tabindex="0">Descripci&oacute;n del Producto</th>' +
            '<th width="10%" tabindex="0">Cantidad</th><th width="10%"  tabindex="0">Precio</th>' +
            '<th width="10%" tabindex="0">Cobrar Seguro</th><th width="10%"  tabindex="0">% Desc</th>' +
            '<th width="10%" tabindex="0">Descuento</th><th width="10%"  tabindex="0">Precio Neto</th>' +
            '<th width="5%" tabindex="0">IVA</th><th width="10%"  tabindex="0">' +
            '</th></tr>' +
            '</thead><tbody>' +
            e.innerHTML +
            '</tbody></table>' +
            '<!--<textarea rows="2" name="obs_paciente[]" maxlength="150" class="form-control px-1 desc_producto" placeholder="Observacion del Paciente"></textarea>-->' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        return html;

    }

    function nuevoItem(nombre, item, id_procedimiento) {
        var e = document.createElement("table");
        if (item != "") {
            item.forEach(function(dat) {
                var val = dat.codigo;
                var nuevafila = $("#mifila").html() + "<input type='hidden' class='ids' value='" + val + "'><input type='hidden' name='id_hc_proc[]' value='" + id_procedimiento + "'>";
                var rowk = e.insertRow(-1);
                rowk.innerHTML = nuevafila;
                rowk.className = "well";
                $('.select2').select2({
                    tags: false
                });
            });
        }

        var html = '<div class="panel panel-default">' +
            '<div class="panel-heading"><input type="checkbox" name="activo[]" class="relactivo" checked /><input type="hidden" name="veractivo[]" class="veractivo" value="1"><input type="hidden" name="hc_procedimiento[]" value="' + id_procedimiento + '">&nbsp;&nbsp;&nbsp;' + nombre + '</div>' +
            '<div class="panel-body" style="padding:0;"><div class="col-md-12 table-responsive" style="padding:0 !important;"><table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">' +
            '<thead><tr class="well-dark"><th width="35%" class="" tabindex="0">Descripci&oacute;n del Producto</th>' +
            '<th width="10%" class="" tabindex="0">Cantidad</th><th width="10%" class="" tabindex="0">Precio</th>' +
            '<th width="10%" class="" tabindex="0">Cobrar Seguro</th><th width="10%" class="" tabindex="0">% Desc</th>' +
            '<th width="10%" class="" tabindex="0">Descuento</th><th width="10%" class="" tabindex="0">Precio Neto</th>' +
            '<th width="5%" class="" tabindex="0">IVA</th><th width="10%" class="" tabindex="0">' +
            '</th></tr>' +
            '</thead><tbody>' +
            e.innerHTML +
            '</tbody></table>' +
            '<input type="hidden" name="nom_paciente[]" value="' + nombre + '" />' +
            '<textarea rows="2" name="obs_paciente[]" maxlength="150" class="form-control px-1 desc_producto" placeholder="Observacion del Paciente"></textarea>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        return html;
    }
    $('body').on('click', '.relactivo', function() {
        if ($(this).prop("checked") == true) {
            $(this).parent().find('.veractivo').val(1);
            $(this).parent().find('.patients').attr('name', 'id_orden[]');
            $(this).parent().find('.desc_producto').attr('name', 'descrip_prod[]');
            $(this).parent().find('.table').removeClass('noacti');
            totales(0);
        } else if ($(this).prop("checked") == false) {
            $(this).parent().find('.veractivo').val(0);
            $(this).parent().find('.patients').attr('name', '');
            $(this).parent().find('.desc_producto').attr('name', '');
            $(this).parent().find('.table').addClass('noacti');
            totales(0);
        }
    });
    $('body').on('click', '.ambulatorio', function() {
        if ($(this).prop("checked") == true) {
            //$(this).parent().find('.veractivo').val(1);
            $(this).val(1)
        } else if ($(this).prop("checked") == false) {
            $(this).val(0)
        }
    });
    $('body').on('click', '.prd_ch', function() {
        /*console.log($(this).parent());
        if($(this).prop("checked") == true){
            console.log("Checkbox is checked.");
            $(this).parent().find('.veractivo').val(1);
        }
        else if($(this).prop("checked") == false){
            console.log("Checkbox is unchecked.");
            $(this).parent().find('.veractivo').val(0);
        }*/
        var total = 0;
        if ($(this).prop('checked')) {
            $(this).parent().find('.prd_activo').val(1);
        } else {
            $(this).parent().find('.prd_activo').val(0);
        }
        $(".prd_ch").each(function() {
            //$(this).find('option[value="'+arr[n]+'"]').attr('selected', 'selected').change()
            //n++;

            if ($(this).prop('checked')) {
                var valor = $(this).parent().find('.prd_val').val();
                console.log('valor: ', valor);
                //valor = parseFloat(valor);
                total = parseFloat(total) + parseFloat(valor);
            }
        });
        $('.pneto').val(total);
        totales(0);
    });
    $('#btn_pago').click(function(event) {
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.innerHTML = '<td><select class="form-control pago" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');"><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="form-control input-number" value="<?= date('Y-m-d'); ?>" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 117px;"></td><td><select  id="tipo_tarjeta' + id + '" name="tipo_tarjeta' + id + '" style="width: 175px;height:25px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="form-control" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="width: 80%;height:20px;" autocomplete="off" class="form-control" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="form-control" style="width: 80%;height:20px;"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="form-control text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);"></td><td><input class="form-control input-number" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,' + id + ',2);"></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;
    });
    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor) {
        var dato_pago1 = "dato_pago" + valor;
        var nombre_pago2 = 'visibilidad_pago' + valor;
        //alert("entra");
        document.getElementById(dato_pago1).style.display = 'none';
        document.getElementById(nombre_pago2).value = 0;
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

    function goBack() {
        location.href = "{{route('venta_index2')}}";
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

    function goNew() {
        location.href = "{{route('insumos')}}";
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
        iva = subtotal_12 * '{{$valorIva}}';
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
            error: function(data) {
                console.log(data);
            }
        })
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
            success: function(data) {},
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
    function excelPreview() {
        var fechaini = $("#fecha_desde").val();
        var fechaini = new Date(fechaini).valueOf();
        var fechafin = $("#fecha_hasta").val();
        var fechafin = new Date(fechafin).valueOf();
        var tipo = $("#tipo_servicio").val();
        var seguro = $("#id_seguro").val();
        var id_empresa = $("#empresaf").val();
        var sucursal = $("#punto_emision").val();
        console.log(fechaini + fechafin + tipo + seguro + "para enviar");
        url = "{{ url('contable/venta/previewExcell/')}}/" + fechaini + "/" + fechafin + "/" + seguro + "/" + tipo + "/" + id_empresa;
        window.open(url, '_blank');
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
    $(function() {
        $('#fecha_proced').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    });
</script>
@endsection