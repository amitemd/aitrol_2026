@extends('contable.ventas.base')
@section('action-content')

<style type="text/css">
.input-number{
    width:80%;
    height:20px;
}
/*
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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

    .alerta_guardado{
       position: absolute;
       z-index: 9999;
       bottom: 100px;
       right: 20px;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 6px;
        background-color: white;
    }

    .card1 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        background-color: white;
    }

    .card-header{
        border-radius: 6px 6px 0 0;
        background-color: #3c8dbc;
        border-color: #b2b2b2;
        padding: 2px;
        padding-left:6px;
    }

    .card-body{
        border-radius: 10px;
        width: 100%;
        border-color: #b2b2b2;
        background-color: #ffffff;
    }

    .alerta_correcto{
      position: absolute;
      z-index:9999;
      bottom: 50px;
    }

    .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }

    .swal-title {
       margin: 0px;
       font-size: 5px;

    }

    table th{
       text-align: left;
       padding: 2px;
       background: #3c8dbc;
       color: #FFF;
    }

    table tr:nth-child(odd){
       background: #FFF;
    }

    table td{
       padding: 2px;
    }

    .cabecera{
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }
*/
</style>

<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<!--<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />-->

<div class="modal fade bd-example-modal-lg" id="modal_retenciones" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"  role="document">
        <div class="modal-content" id="content">
        </div>
    </div>
</div>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Consultas/Procedimientos</a></li>
            <li class="breadcrumb-item"><a href="#" onclick="goBack()">Historia Clínica</a></li>
            <li class="breadcrumb-item active" aria-current="page">Factura de Venta</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-10">
                <h3 class="box-title">Factura de Venta</h3>
            </div>

            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
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
                        <input type="text" class="form-control" name="numero" id="numero" value="">
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
                        <input id="fecha" type="text" class="form-control" name="fecha_asiento" value="{{ old('fecha_asiento') }}" required>
                        @if ($errors->has('fecha_asiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6  col-md-2  px-1">
                    <div class="col-md-12 px-0">
                        <label for="orden_venta" class="label_header">{{trans('contableM.ordenventa')}}</label>
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
                <div class="form-group col-xs-6  col-md-4  px-1">
                    <div class="col-md-12 px-0">
                        <label for="empresa" class="label_header">{{trans('contableM.empresa')}}</label>
                    </div>
                    <div class="col-md-12 px-0">
                        <input id="empresa" type="text" class="form-control" name="empresa" value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly>
                        @if ($errors->has('orden_venta'))
                        <span class="help-block">
                            <strong>{{ $errors->first('fecha_asiento') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="form-group col-xs-6  col-md-1  px-1">
                <div class="col-md-12 px-0">
                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
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
            <div class="form-group col-xs-6  col-md-1  px-1">
                <div class="col-md-12 px-0">
                    <label for="punto_emision" class="label_header">Punto de Emision ({{trans('contableM.empresa')}})</label>
                </div>
                <div class="col-md-12 px-0">
                    <select class="form-control" name="punto_emision" id="punto_emision" required>
                        <option value="">Seleccione...</option> 
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-6  col-md-2  px-1">
                <div class="col-md-12 px-0">
                    <label for="numero_autorizacion" class="label_header">{{trans('contableM.NAUTORIZACION')}}</label>
                </div>
                <div class="col-md-12 px-0">
                    <input id="numero_autorizacion" type="text" class="form-control"  name="numero_autorizacion" value="{{ old('numero_autorizacion') }}"  >
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
                        <select id="recaudador" name="recaudador" class="form-control" required>
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
                        <select id="vendedor" name="vendedor" class="form-control" required>
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
                        <input id="identificacion_cliente" type="text" class="form-control" name="identificacion_cliente" value="{{ old('identificacion_cliente') }}" onchange="reloadCliente()" required placeholder="Identificacion">
                        @if ($errors->has('identificacion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_cliente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="nombre_cliente" type="text" class="form-control"  name="nombre_cliente" value="{{ old('nombre_cliente') }}"  onchange="reloadCliente()" required placeholder="Nombre">
                    @if ($errors->has('nombre_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_cliente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="direccion_cliente" type="text" class="form-control"  name="direccion_cliente" value="{{ old('direccion_cliente') }}"   required placeholder="Dirección">
                    @if ($errors->has('direccion_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_cliente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="ciudad_cliente" type="text" class="form-control"  name="ciudad_cliente" value="{{ old('ciudad_cliente') }}"   required placeholder="Ciudad">
                    @if ($errors->has('ciudad_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_cliente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="mail_cliente" type="text" class="form-control"  name="mail_cliente" value="{{ old('mail_cliente') }}"   required placeholder="Mail">
                    @if ($errors->has('mail_cliente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mail_cliente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="telefono_cliente" type="text" class="form-control"  name="telefono_cliente" value="{{ old('telefono_cliente') }}"   required placeholder="Teléfono">
                    <input id="tipo_cliente" type="hidden" class="form-control"  name="tipo_cliente" value="{{ old('tipo_cliente') }}"   required placeholder="tipo">
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
                        <input id="identificacion_paciente" type="text" class="form-control" name="identificacion_paciente" value="@if(!is_null($procedimiento)){{$procedimiento->historia->paciente->id}}@endif" required placeholder="Cédula">
                        @if ($errors->has('identificacion_paciente'))
                        <span class="help-block">
                            <strong>{{ $errors->first('identificacion_paciente') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col-md-2 px-0">
                    <input id="nombre_paciente" type="text" class="form-control"  name="nombre_paciente" value="{{ $procedimiento->historia->paciente->apellido1}} @if($procedimiento->historia->paciente->apellido2 != "(N/A)"){{ $procedimiento->historia->paciente->apellido2}}@endif {{ $procedimiento->historia->paciente->nombre1}} @if($procedimiento->historia->paciente->nombre2 != "(N/A)"){{ $procedimiento->historia->paciente->nombre2}}@endif"   required placeholder="Nombre">
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
                            <option  @if($procedimiento->historia->paciente->id_seguro == $seguro->id) selected @endif
                                value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                @php
                                    $agenda = DB::table('agenda')->where('id',$procedimiento->historia->id_agenda)->first();
                                    $dia =  Date('N',strtotime($agenda->fechaini));
                                    $mes =  Date('n',strtotime($agenda->fechaini));
                                @endphp
                                @php
                                    $dia =  Date('N',strtotime($agenda->fechaini));
                                    $mes =  Date('n',strtotime($agenda->fechaini));
                                @endphp
                                @php
                                  $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
                                  $mas = true;
                                  $texto = "";

                                  foreach($adicionales as $value2)
                                  {
                                    if($mas == true){
                                     $texto = $texto.$value2->procedimiento->nombre  ;
                                     $mas = false;
                                    }
                                    else{
                                      $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                    }
                                  }
                                @endphp
                <div class="col-md-2 px-0">
                    <input id="procedimiento" type="text" class="form-control"  name="procedimiento" value="@if($procedimiento->procedimiento_completo!=null){{$procedimiento->procedimiento_completo->nombre_general}}@elseif(!is_null($texto)) {{$texto}} @endif"   required placeholder="Procedimiento">
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
                            <input type="text" value="{{$agenda->fechaini}}" name="fecha_proced" id="fecha_proced" class="form-control" placeholder='Fecha de Proce.'>
                            <input type="hidden" value="{{$iva->iva}}" name="ivareal" id="ivareal" class="form-control">

                        </div>
                    </div>
                    <div class="col-md-2 px-0">
                        <select class="form-control" name="tipo_consulta" id="tipo_consulta">
                            <option value=""> --- Tipo --- </option>
                            <option value="1">{{trans('contableM.Consulta')}}</option>
                            <option value="2">{{trans('contableM.Procedimiento')}}</option>

                        </select>
                    </div>
                    <input type="hidden" name="mov_paciente" id="mov_paciente" class="form-control" value="{{$procedimiento->id}}">

                </div>
            </div>

            <div class="col-md-12 table-responsive">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
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
                    <tbody id="agregar_producto">
                        <tr style="display:none" id="mifila">
                            <td style="max-width:100px;">
                                <input type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" required onchange="verificar(this)" >
                                        <option> </option>
                                        @foreach($productos as $value)
                                            <option value="{{$value->nombre}}"  data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-maxdesc="{{$value->descuento}}"  data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                        @endforeach
                                          
                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required >
                                </td>
                                <td>
                                <select  name="precio[]" class="form-control select2_precio pneto" style="width:100%" required  >
                                        <option value="0"> </option>
                                        
                                          
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                                <td>
                                    <input class="form" type="checkbox" style="width: 80%;height:20px;"  name="valoriva[]">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" >
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
                            <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
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
                            <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
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
            <div class="col-md-12" style="height:30px;">
                <div class="row head-title">
                    <div class="col-md-12 cabecera">
                        <label class="color_texto">{{trans('contableM.formadepago')}}</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 table-responsive ">
                <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                    <thead>
                        <tr class='well-dark'>
                            <th width="20%" style="text-align: center;">{{trans('contableM.tipo')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.fecha')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.numero')}}</th>
                            <th width="15%" style="text-align: center;">{{trans('contableM.banco')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.Cuenta')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.giradoa')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.valor')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('contableM.ValorBase')}}</th>
                            <th width="5%" style="text-align: center;"><button id="btn_pago" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button></th>
                        </tr>
                    </thead>
                    <tbody id="agregar_pago">
                    </tbody>
                </table>
            </div>

            <!--<div class="col-md-12" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-2 col-xs-2">
                                            <label for="rfiva" class="control-label" class="control-label">R.F.IVA:</label>
                                            <div class="input-group">
                                                <input id="rfiva" name="rfiva" type="text" class="form-control" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <label for="tipo_riva" class="control-label">Tipo R.iva</label>
                                            <select class="form-control input-sm" name="tipo_riva" id="tipo_riva" onchange="obtener_valor_rete_iva()" required>
                                                <option value="">Seleccione...</option>
                                                @foreach($rete_iva as $value_iva)
                                                   <option value="{{$value_iva->id}}">{{$value_iva->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                           <label for="rf_renta" class="control-label" class="control-label">R.F.Renta:</label>
                                            <div class="input-group">
                                                <input id="rf_renta" name="rf_renta" type="text" class="form-control" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <label for="tipo_rfuente" class="control-label">Tipo R.FUENTE</label>
                                            <select class="form-control input-sm" name="tipo_rfuente" id="tipo_rfuente"  onchange="obtener_valor_rete_fuente()" required>
                                                <option value="">Seleccione...</option>
                                                @foreach($rete_fuente as $value_fuente)
                                                   <option value="{{$value_fuente->id}}">{{$value_fuente->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="total_pagado" class="control-label" class="control-label">Total Pagado:</label>
                                            <div class="input-group">
                                             <input id="total_pagado" name="total_pagado" type="text" class="form-control" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-2">
                                            <label for="valor_pago1" class="control-label" class="control-label">Suma Pago:</label>
                                            <div class="input-group">
                                            <input id="valor_pago1" name="valor_pago1" type="text" class="form-control" value="0.00">
                                            <input type="text" name="valor_pago" id="valor_pago" class="hidden">
                                            </div>
                                        </div>
                                    </div>
                                </div>-->


            <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>
            </div>
        </form>
</div>
    </section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<!--<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>-->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>



<script type="text/javascript">

$(document).on("focus","#nombre_cliente",function() {
        $("#nombre_cliente").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                  type: 'GET',
                  url: "{{route('ventas.buscarcliente')}}",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response(data);
                  }
                } );
            },
            change:function(event, ui){
                $("#identificacion_cliente").val(ui.item.id);

                $("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);
                $("#tipo_cliente").val(ui.item.tipo);
                
            },
            selectFirst: true,
            minLength: 3,
        } );

    });

    $(document).on("focus","#identificacion_cliente",function() {
        $("#identificacion_cliente").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                  type: 'GET',
                  url: "{{route('ventas.buscarclientexid')}}",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response(data);
                  }
                } );
            },
            change:function(event, ui){
                $("#nombre_cliente").val(ui.item.nombre);

                $("#direccion_cliente").val(ui.item.direccion);
                $("#ciudad_cliente").val(ui.item.ciudad);
                $("#mail_cliente").val(ui.item.mail);
                $("#telefono_cliente").val(ui.item.telefono);
                $("#tipo_cliente").val(ui.item.tipo);
                
            },
            selectFirst: true,
            minLength: 1,
        } );
    });
    

    $(document).ready(function(){

        limpiar();

        $('.select2_cliente').select2({
            tags: false
        });

        

        $('#iva').iCheck({
           checkboxClass: 'icheckbox_flat-blue',
           increaseArea: '20%' // optional
        });

        //Llamando a la Funcion Numero Factura
      //  obtener_num_factura();

        //Obtenemos los Insumos Utilizados
        obtener_prod_mov_paciente();

    });

    function goBack() {
      window.history.back();
    }

    function crear_factura_venta(){

        //$('#crear_factura').submit();

        var secuen_factura = $("#nfactura").val();

        var id_cli = $("#cliente").val();


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


        if(divisas == ""){
           msj += "Por favor,Selecione la divisa\n";
        }

       //Datos Generales

        if(id_emp == ""){
           msj += "Por favor, Seleccione la Empresa\n";
        }

        if(sucurs == ""){
           msj += "Por favor, Seleccione la Sucursal\n";
        }

        if(punt_emision == ""){
           msj += "Por favor, Seleccione el Punto de Emision\n";
        }

        //Paciente
        if(cedula == ""){
           msj += "Por favor,Ingrese la cedula del paciente\n";
        }
        if(nombre_paciente == ""){
           msj += "Por favor,Ingrese el nombre del paciente\n";
        }
        if(seguro_paciente == ""){
           msj += "Por favor,Seleccione el seguro paciente\n";
        }
        if(proced == ""){
           msj += "Por favor,Ingrese los Procedimientos del paciente\n";
        }
        if(fech_proced == ""){
           msj += "Por favor,Seleccione la fecha de procedimientos\n";
        }

        //Cliente
        if(cliente == ""){
           msj += "Por favor,Selecione el cliente\n";
        }
        if(ruc_ced_cliente == ""){
           msj += "Por favor,Ingrese el ruc del cliente\n";
        }
        if(direccion == ""){
           msj += "Por favor,Ingrese la direccion del cliente\n";
        }
        if(ciud_cliente == ""){
           msj += "Por favor,Ingrese la ciudad del cliente\n";
        }

        if(telf_cliente == ""){
           msj += "Por favor,Ingrese el telefono del cliente\n";
        }

        //Recaudador
        if(recaud == ""){
           msj += "Por favor,Selecione el recaudador\n";
        }
        if(ced_recaudador == ""){
           msj += "Por favor,Ingrese la cedula del recaudador\n";
        }

        //Detalle Asiento
        if(det_asiento == ""){
           msj += "Por favor,Ingrese el detalle del asiento\n";
        }


        //Obtenemos el valor de Contador de la Forma de Pago
        var ct_fp = document.getElementById('contador_pago').value;

        //alert(ct_fp);

        //Obtenemos el valor tOtal a Pagar en la Factura
        var val_tot = parseFloat($('#total1').val());


        //Obtenemos la suma de los Pagos realizados
        var sum_pa = parseFloat($('#valor_pago').val());

        if(msj == ""){

            if (valor_total>0){

                if(ct_fp>0){

                    if(sum_pa == val_tot){

                        $.ajax({
                            type: 'post',
                            url:"{{route('ventas_store')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $("#crear_factura").serialize(),
                            success: function(data){
                                //console.log(data);
                                if (confirm('¿Se genero la Factura desea ingresar retenciones?')) {

                                    $.ajax({
                                       type: "get",
                                        url: "{{route('retencion_ventas')}}",
                                        data:{'id_cl':id_cli,'secuencia': secuen_factura,'id_venta':data},
                                        datatype: "html",
                                        success: function(datahtml, data){
                                            //console.log(datahtml);

                                            $("#content").html(datahtml);
                                            $("#modal_retenciones").modal("show");

                                        },
                                        error:  function(){
                                            alert('error al cargar');
                                        }

                                    });
                                } else {
                                    swal(`{{trans('contableM.correcto')}}!`, "Factura guardada correctamente", "success");
                                    location.href = "{{route('venta_index')}}";
                                }

                                /*swal({
                                        title: `{{trans('proforma.GuardadoCorrectamente')}}`,
                                        buttons: true,
                                })
                                .then((value) => {
                                    location.href ="{{route('venta_index')}}";
                                });*/

                            },
                            error: function(data){
                                   console.log(data);
                            }
                        })

                    }else{

                        if(sum_pa > val_tot){

                           sup_av = (sum_pa-val_tot).toFixed(2);

                           swal({ title: "Existe un superavit de "+sup_av+" en la cobertura de la deuda",
                           buttons: true,
                           });

                        }else{

                            if(sum_pa < val_tot){

                                sal = (val_tot-sum_pa).toFixed(2);

                                swal({ title: "Existe un saldo de "+sal+" en la cobertura de la deuda",
                                buttons: true,
                                });
                            }
                        }
                    }

                }else{

                    swal({ title: "No Existen Valores de Forma de Pago ingresado",
                       buttons: true,
                    });

                }

            }else{

                swal({ title: "Calcular el total a pagar",
                       buttons: true,
                 });

            }
        }else{
           alert(msj);
        }

    }

    //Valida el valor Ingresado en el Campo Cantidad
    function validarcantidad(elemento,id){

        var cod = $('#codigo'+id).val();
        var nomb = $('#nombre'+id).val()

        if((cod.length == 0)&&(nomb.length == 0)){
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            return false;
        }

        var num = elemento.value;
        if (num.length == 0){

            alert("Cantidad no permitida");
            $('#cantidad'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");

            return false;
        }

        var st = $('#stock'+id).val();
        var nu = parseInt(elemento.value,10);

        if(nu > st){
            alert("Cantidad no debe ser mayor al stock");
            $('#cantidad'+id).val("0");
            $('#total_acum'+id).val('');
        }


        var numero = parseInt(elemento.value,10);
        //Validamos que se cumpla el rango
        if(numero<1 || numero>999999999){
            alert("Cantidad no permitida");
            //$('#cantidad'+id).val("");
            $('#cantidad'+id).val("0");
            return false;
        }

        $('#total_acum'+id).val(numero);

        return true;
    }

    //No usada por el momento
    function validartotal(elemento,id){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 || numero>999999999){
            alert("Total no permitido");
            $('#total'+id).val("");
            return false;
        }
        return true;
    }


    function calculo_valores(id){

      total = 0;
      descuento_total = 0;

        cantidad = parseFloat($('#cantidad'+id).val());
        precio = parseFloat($('#precio'+id).val());
        descuento = parseFloat($('#desc_porcentaje'+id).val());

        total = cantidad * precio;

        descuento_total = (total * descuento)/100;

          $('#desc'+id).val(descuento_total);
          $('#extendido'+id).val(total);

          $('#desc1'+id).val(descuento_total);
          $('#extendido1'+id).val(total);

          //suma_totales();

    }

    //Calcula el Total
    function total_calculo(id){

      total = 0;
      descuento_total = 0;

        cantidad = parseFloat($('#cantidad'+id).val());
        precio = parseFloat($('#precio'+id).val());
        descuento = parseFloat($('#desc_porcentaje'+id).val());

        total = cantidad * precio;

        descuento_total = (total * descuento)/100;

          $('#desc'+id).val(descuento_total.toFixed(2));
          $('#extendido'+id).val(total.toFixed(2));

          $('#desc1'+id).val(descuento_total.toFixed(2));
          $('#extendido1'+id).val(total.toFixed(2));

          suma_totales();

    }

   function suma_totales(){

      contador  =  0;
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

      $("#agregar_producto tr").each(function(){
        $(this).find('td')[0];

            visibilidad = $(this).find('#visibilidad'+contador).val();

            //iva_p = parseInt($(this).find('#iva_obt'+contador).val());
            cost_prod = parseFloat($(this).find('#cost_prod'+contador).val());
            cantidad = parseFloat($(this).find('#cantidad'+contador).val());
            valor = parseFloat($(this).find('#precio'+contador).val());
            descu = parseFloat($(this).find('#desc1'+contador).val());
            pre_neto = parseFloat($(this).find('#extendido1'+contador).val());
            total = cantidad * valor;


            if(visibilidad == 1){

                if($('#iva'+contador).prop('checked')){

                    subtotal_12 = subtotal_12 + total;

                    if(total>0){

                      val_cost_prod = val_cost_prod+cost_prod;
                    }

                    if(descu>0){
                      descu1 = descu1 + descu;
                    }

                }else{

                   subtotal_0 = subtotal_0 + total;

                   if(total>0){
                    val_cost_prod = val_cost_prod+cost_prod;
                   }

                   if(descu>0){
                    descu1 = descu1 + descu;
                   }


               }

            }

            contador = contador+1;

      });

      sum_subt = subtotal_12+subtotal_0;

      iva = subtotal_12 * 0.12;

      base_imponible = subtotal_12;

      trans = parseFloat($('#transporte').val());

      if(trans>0){
        total_fin = (sum_subt - descu1)+trans+iva;
      }else{
        total_fin = (sum_subt - descu1)+iva;
      }

      //Campos Visibles
      $('#subtotal').val(sum_subt);
      $('#impuesto').val(iva.toFixed(2));
      $('#descuento').val(descu1.toFixed(2));
      $('#total').val(total_fin.toFixed(2));
      $('#base_imponible').val(base_imponible.toFixed(2));
      $('#subtotal').val(subtotal_12.toFixed(2));
      $('#subtotal2').val(subtotal_0.toFixed(2));

      //Total Valor Pagado
      $('#total_pagado').val(total_fin.toFixed(2));


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


    //Suma valor de Forma de Pago
    function suma_valor_forma_pago(){

        contador_pag  =  0;
        sum_vbase = 0;


        $("#agregar_pago tr").each(function(){
           $(this).find('td')[0];

            visibilidad_pag = $(this).find('#visibilidad_pago'+contador_pag).val();
            val_p = parseFloat($(this).find('#valor'+contador_pag).val());

            if(visibilidad_pag == 1){

               sum_vbase = sum_vbase + val_p;

            }

            contador_pag = contador_pag+1;

        });


        //Campos Visibles
        $('#valor_pago1').val(sum_vbase.toFixed(2));

        //Campos Oculto
        $('#valor_pago').val(sum_vbase.toFixed(2));

    }


    //Valida Precio
    function validarprecio(elemento, id){

        var cod = $('#codigo'+id).val();
        var nomb = $('#nombre'+id).val()

        if((cod.length == 0)&&(nomb.length == 0)){
            alert("Debe ingresar el código, nombre, cantidad del producto");
            $('#precio'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");
            return false;
        }

        var prec = elemento.value;
        if (prec.length == 0){

            alert("Precio no permitido");
            $('#precio'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");

            return false;
        }

        var numero = parseInt(elemento.value,10);
        //Validamos que se cumpla el rango
        if(numero<1 || numero>999999999){
            alert("Precio no permitido");
            $('#precio'+id).val("0");
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id){

        var desc = elemento.value;
        if (desc.length == 0){

            alert("Rango de descuento permitido (0% - 100%)");
            $('#desc_porcentaje'+id).val("0");
            $('#desc'+id).val("0");
            return false;
        }

        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<0 || numero>100){
            alert("Rango de descuento permitido (0% - 100%)");
            $('#desc_porcentaje'+id).val("0");
            $('#desc'+id).val("0");
            return false;
        }
        return true;
    }

    //Obtengo el Ruc o Cedula del Cliente Seleccionado
    $("#cliente").change(function(){
        $.ajax({
            type: 'post',
            url:"{{route('ventas_buscar_identificacion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cliente"),
            success: function(data){
                $('#ruc_cedula').val(data.client_identificacion);
                $('#direccion').removeAttr('disabled');
                $('#direccion').val(data.client_direccion);
                $('#identif_cliente').val(data.client_identificacion);
                $('#telefono').val(data.client_telefono);
                $('#email').val(data.client_email);
                $('#ciudad').val(data.client_ciudad);
            },
            error: function(data){
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

    //Obtenemos Valor retencion Iva
    function obtener_valor_rete_iva(){

        //Obtenemos el id Tipo Retencion Iva
        var id_tipo_riva = $("#tipo_riva").val();

        var imp_iva = parseFloat($("#impuesto1").val());
        var tot_fact = parseFloat($("#total1").val());

         $.ajax({
            type: 'post',
            url:"{{route('obtener_porcentaje_retencion_iva')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_tip_ret_iva': id_tipo_riva},
            success: function(data){
                //console.log(data);
                if(data.value!='no resultados'){

                    var porcent_ret_iva = parseFloat(data[0])/100;
                    var total_reten_iva = (imp_iva)*(porcent_ret_iva);
                    $("#rfiva").val(total_reten_iva.toFixed(2));
                    //$("#cuen_cl_iva").val(data[1]);
                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }

    //Obtenemos Valor retencion Fuente
    function obtener_valor_rete_fuente(){

        //Obtenemos el id Tipo Retencion Fuente
        var id_tipo_rfuente = $("#tipo_rfuente").val();

        var sub_total_12  = parseFloat($("#impuesto1").val());

        var sub_total_0 = parseFloat($("#impuesto1").val());

        //Obtenemos el Valor de los Subtotales
        var sum_Subtot = sub_total_12+sub_total_0;

        var tot_fact = parseFloat($("#total1").val());


        $.ajax({
            type: 'post',
            url:"{{route('obtener_porcentaje_retencion_fuente')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_tip_ret_fuente': id_tipo_rfuente},
            success: function(data){
                //console.log(data);
                if(data.value!='no resultados'){

                    var porcent_ret_fuente = parseFloat(data[0])/100;
                    var total_reten_fuente = (sum_Subtot)*(porcent_ret_fuente);
                    $("#rf_renta").val(total_reten_fuente.toFixed(2));
                    //$("#cuen_cl_fuent").val(data[1]);


                    //obtener_nuevo_saldo();


                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }

    //Calculo de Nuevo Saldo
    /*function obtener_nuevo_saldo(){

        var valor_rfiva = parseFloat($("#rfiva").val());
        var valor_rf_renta = parseFloat($("#rf_renta").val());
        var tot_fact = parseFloat($("#total_factura").val());

        var total_val_ret = valor_rfiva+valor_rf_renta;
        var val_nuevo_saldo = tot_fact-total_val_ret;

        var id = 0;
        if(val_nuevo_saldo!=NaN){
           $("#nuevo_saldo").val(val_nuevo_saldo);
           $("#n_saldo"+id).val(val_nuevo_saldo);
           $("#total_abonos").val(total_val_ret);
           $("#abono"+id).val(total_val_ret);
        }

    }*/

    $("#vendedor").change(function(){
        $('#cedula_vendedor').val($('option:selected',$(this)).data("id"));
    });

    //Obtengo datos del Recaudador de la Tabla Usuarios
    $("#recaudador").change(function(){
        $.ajax({
            type: 'post',
            url:"{{route('recaudador.identificacion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#recaudador"),
            success: function(data){
                $('#cedula_recaudador').val(data.recaudador_cedula);
            },
            error: function(data){
                console.log(data);
            }
        })
    });

    //Actualiza la direccion del Cliente
    $("#direccion").change(function(){
        $.ajax({
            type: 'post',
            url:"{{route('update_direccion_client')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            //data: $("#identif_cliente"),
            data: {'ident_cliente': $("#identif_cliente").val(),'direc_cliente': $("#direccion").val()},
            success: function(data){
              //console.log(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    });

    //Obtengo el Codigo del Producto por el nombre del producto Ingresado
    function cambiar_nombre(id){
        $.ajax({
            type: 'post',
            url:"{{route('ventas_buscar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre"+id).val()},
            success: function(data){

              $('#codigo'+id).val(data.cod_product);
              $('#cost_prod'+id).val(data.cost_vent);
              //$('#iva_obt'+id).val(data.iva_prod);
              if(data.iva_prod == '1'){
                    $('#iva'+id).prop("checked", true);
              }

            },
            error: function(data){
                //Sconsole.log(data);
            }
        })
    }


    //Obtengo el Nombre del Producto por el codigo del producto Ingresado
    function cambiar_codigo(id){
        $.ajax({
            type: 'post',
            url:"{{route('ventas_buscar_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codigo':$("#codigo"+id).val()},
            success: function(data){

                $('#nombre'+id).val(data);
            },
            error: function(data){
                //console.log(data);
            }
        })
    }


    function obtener_num_factura(){
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
    }


    function obtener_prod_mov_paciente(){

        $.ajax({
            type: 'post',
            url:"{{route('ventas_buscar_insumos')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_hc_proced':$("#mov_paciente").val()},
            success: function(data){

                //console.log(data);

                if(data.value != 'no resultados'){

                    subtotal_0 = 0;
                    subtotal_12 = 0;
                    val_cost_prod1 = 0;
                    val_cost_prod2 = 0;
                    iva = 0;
                    sum_subt = 0;
                    base_imponible = 0;
                    total_fin = 0;
                    cost_prod_total = 0;

                    for(var i=0;i<data[0].length;i++){

                        //Crear Registros de los productos Seleccionados
                        crear_reg_mov_productos(i);
                        console.log("el valor es: ",data[0][i].codigo+" | " + data[0][i].nombre);
                        $("#nombre"+i).val(data[0][i].nombre).change();
                       
                        $('#codigo'+i).val(data[0][i].codigo);
                        //$('#nombre'+i).val(data[0][i].nombre);
                        $('#cantidad'+i).val(data[0][i].total);
                        //$('#precio'+i).val(data[0][i].cost_vent);
                        total = (data[0][i].total)*(data[0][i].suma);

                        //calculo_valores(i);
                       /* if(data[0][i].iva == '1'){
                            document.getElementById('iva'+i).checked = true;
                            total_calculo(i);
                            document.getElementById('eliminar'+i).disabled=true;

                        }else{

                           total_calculo(i);
                           document.getElementById('eliminar'+i).disabled=true;
                        }*/

                    }
                    //totales(0);



                }


            },
            error: function(data){
                console.log(data);
            }
        })
    }

    function limpiar(){

       //obtenemos la fecha actual
        var now = new Date();
        var day =("0"+now.getDate()).slice(-2);
        var month=("0"+(now.getMonth()+1)).slice(-2);
        var today=now.getFullYear()+"-"+(month)+"-"+(day);
        $("#fecha").val(today);

    }

    //Completa la cedula del Paciente por minimo 3 digitos
    $(".ced_paciente").autocomplete({
        source: function( request, response ) {
            $.ajax( {
              url: "{{route('autocomple_paciente_cedula')}}",
              dataType: "json",
              data: {
                term: request.term
              },
              success: function( data ) {
                response(data);
              }
            } );
        },
        minLength: 1,
    } );

//funciones venta create_factura
    $('body').on('click', '.delete', function () {
        console.log($(this));
    
        $(this).parent().parent().remove();
        totales(0);
    });

    function verificar(e){
        var iva = $('option:selected',e).data("iva");
        var codigo = $('option:selected',e).data("codigo");
        var usadescuento = $('option:selected',e).data("descuento");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
      //  console.log(usadescuento);
        if(usadescuento){
            $(e).parent().next().next().next().next().next().children().attr("disabled", "disabled");
            $(e).parent().next().next().next().next().children().attr("disabled", "disabled");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        }else{
            $(e).parent().next().next().next().next().next().children().removeAttr("disabled");
            $(e).parent().next().next().next().next().children().removeAttr("disabled");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        }
   
        if(iva == '1'){
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
        }else{
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }

        //cargarPrecios
        //console.log(codigo);
        var tipo = $("#tipo_cliente").val();
        console.log("tipo", tipo);
        tipo = tipo != "" ? tipo : 1;
        var selected = "";
        $.ajax({
            type: 'post',
            url:"{{route('precios')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {id:codigo},
            success: function(data){
                $(e).parent().next().next().children().find('option').remove(); 
                $.each(data, function (key, value) {
                    console.log("tipo", tipo, "nivel", value.nivel);
                    if(tipo == value.nivel){
                        selected = "selected";
                    }else{
                        selected = "";
                    }
                    $(e).parent().next().next().children().append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
                });
            },
            error: function(data){
                    console.log(data);
            }
        })


    }

//cantidad
//precio
//copago
//%descuento
//descuento
//precioneto

$('body').on('change', '.pneto', function () {
        verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        console.log("copago", copago);  
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant)* parseFloat($(this).val())) - descuento - copago;
        console.log("total",total);
        console.log($(this).parent().next().next().next().next().children());
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
       // verificar(this);
    //    totales(0);
    });

    $('body').on('blur', '.pneto', function () {
        verificar(this);
        var cant = $(this).parent().prev().children().val();
        var copago = $(this).parent().next().children().val();
        console.log("copago", copago);  
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant)* parseFloat($(this).val())) - descuento - copago;
        console.log("total",total);
        console.log($(this).parent().next().next().next().next().children());
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
       // verificar(this);
    //    totales(0);
    });
   /*$('body').on('blur', '.cneto', function () {
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = (parseInt(cant)* parseFloat(precio)) - descuento - copago;
        $(this).parent().next().next().next().next().next().children().val(total.toFixed(2));
        verificar(this);
    });*/
    $('body').on('change', '.cneto', function () {
       // verificar(this);
       console.log("sa");
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
       // console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = (parseInt(cant)* parseFloat(precio)) - descuento - copago;
        $(this).parent().next().next().next().next().next().children().val(total.toFixed(2));
        
       // totales(0);
    });

    $('body').on('blur', '.copago', function () {
        verificar(this);
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();

        var copago = $(this).val();
        console.log("copago", copago);  
        var descuento = $(this).parent().next().next().children().val();
        var total = (parseInt(cant)* parseFloat(precio)) - descuento - copago;
        console.log(total);
        $(this).parent().next().next().next().children().val(total.toFixed(2));
        
        totales(0);
    });


    $('body').on('blur', '.pdesc', function () {
        verificar(this);
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var pdesc = $(this).val();
        var descuento = (parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = $(this).parent().prev().children().val(); 
        var total = (parseInt(cant)* parseFloat(precio)) - descuento - copago;
        $(this).parent().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('blur', '.desc', function () {
        var cant = $(this).parent().prev().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().prev().children().val();
        var descuento = $(this).val();
        verificar(this);
        console.log(cant, precio);
        var pdesc = 0;
        if(cant== 0 || precio == 0){
            pdesc = 0;
        }else{
            pdesc =(descuento * 100)/(parseInt(cant)* parseFloat(precio));
        }
        //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
        $(this).parent().prev().children().val(pdesc);
        var copago = $(this).parent().prev().prev().children().val(); 
        var total = (parseInt(cant)* parseFloat(precio)) - descuento - copago;
        $(this).parent().next().children().val(total.toFixed(2));
        totales(0);
    });

    function totales(e){
        console.log("entre... permiso!");
        var subt12=[];
        var subt0=[];
        var descuento = [];
        var sb12 = 0;
        var sb0 = 0;
        var d = 0;
        var copagoTotal = 0;
        if(e==0){
            $('.cneto').each(function(i, obj) {
            var cant = $(this).val();
            var e = $(this).parent().prev().children().closest(".select2_cuentas");
            var edsa = $(this).parent().prev().children().closest(".select2_cuentas");
           console.log(edsa);
            var precio1 = 0;
            var precio2 = 0;
            var precio3 = 0;
            var precio4 = 0;
            var precio5 = 0;
            var precioAut = 0;
            var tipo = $("#tipo_cliente").val();
            console.log("el e es: ", e.val());
          
               
            var precio = $(this).parent().next().children().val();
            var copago = $(this).parent().next().next().children().val();
            var descuento = $(this).parent().next().next().next().next().children().val();
            d = parseFloat(d) + parseFloat(descuento);
            var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
            var total = (parseInt(cant)* parseFloat(precio))-parseFloat(0)-parseFloat(copago);
            if(iva == 1){
                subt12.push(total);
                sb12 = sb12+ total;
            }else{
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
            var t = sb12+ sb0+ti-d;
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

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function nuevo(){
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_producto").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = nuevafila;
        rowk.className="well";
        
    }


    $(".btn_add").click(function(){
         
         if($("#form").valid()){
           $(".print").css('visibility', 'visible');
           $(".btn_add").attr("disabled", true);
           $("#mifila").html("");
             $.ajax({
                 url:"{{route('ventas_store')}}",
                 headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                 type: 'POST',
                 datatype: 'json',
                 data: $("#form").serialize(),
                 success: function(data){
                     console.log(data);
                     $("#asiento").val(data.idasiento);
                     $("#id").val(data.idventa);
                     $("#numero").val(data.idventa);
                     swal("Guardado con Exito!");

                 },
                 error: function(data){
                     console.log(data);
                     swal("Ocurrio un error");
                 }
             });
         }
       
   });

//

    //Obtengo datos del paciente por la cedula
    function obtener_datos_paciente(){
        $.ajax({
            type: 'post',
            url:"{{route('obtener_info_paciente')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data:{'ced_paciente': $("#ced_paciente").val()},
            success: function(data){
                //console.log(data);
                if(data.texto != ''){
                  $('#nomb_paciente').val(data.texto);
                  $('#seguro').val(data.nomb_seg);
                }

                if(data.nomb_seg != ''){
                  $('#seguro').val(data.nomb_seg);
                }

            },
            error: function(data){
                console.log(data);
            }
        })
    }

function obtener_sucursal(){

        var id_seleccionado = $("#id_empresa").val();

        $.ajax({
            type: 'post',
            url:"{{route('sucursal.empresa')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_emp': id_seleccionado},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_seleccionado!=0){
                        $("#sucursal").empty();

                        $.each(data,function(key, registro) {
                            $("#sucursal").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'</option>');

                        });
                    }else{
                        $("#sucursal").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }

    function obtener_caja(){

        var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url:"{{route('caja.sucursal')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_sucur': id_sucursal},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_sucursal!=0){
                        $("#punto_emision").empty();

                        $.each(data,function(key, registro) {
                            $("#punto_emision").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'-'+registro.codigo_caja+'</option>');

                        });
                    }else{
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }


    //Completa 2 Decimales a la izquierda
    function completa_decimales(elemento,id,nDec){

        var cod = $('#codigo'+id).val();
        var nomb = $('#nombre'+id).val();
        var num = elemento.value;

        var n = parseFloat(elemento.value);
        var s;

        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);


        if((cod.length == 0)&&(nomb.length == 0)){
            alert("Debe ingresar el código, nombre del producto");
            $('#cantidad'+id).val("0.00");
            $('#desc'+id).val("0.00");
            $('#extendido'+id).val("0.00");
            return false;
        }


        if (num.length == 0){

            alert("Cantidad no permitida");
            $('#cantidad'+id).val("0");
            $('#desc'+id).val("0");
            $('#extendido'+id).val("0");

            return false;
        }

        $('#cantidad'+id).val(s);

    }

    //Completa 2 decimales a la izquierda y redondea el valor a dos decimales
    function redondea_precio(elemento,id,nDec){

       /* var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#precio'+id).val(s);*/
        elemento.value=parseFloat(elemento.value).toFixed(2);


    }

    //Redondea descuento a 2 decimales
    function redondea_descuento(elemento,id,nDec){

        var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#desc_porcentaje'+id).val(s);


    }


    //Completa 2 decimales a la izquierda
    function redondea_valor(elemento,id,nDec){

     /*   var n = parseFloat(elemento.value);
        var s;
        console.log(n);
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        console.log(n);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        console.log(s);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        console.log(s);

        $('#valor'+id).val(s);
*/

elemento.value=parseFloat(elemento.value).toFixed(2);
    }

    //Completa 2 decimales a la izquierda
    function redondea_valor_base(elemento,id,nDec){

       /* var n = parseFloat(elemento.value);
        var s;

        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);

        $('#valor_base'+id).val(s);*/
        elemento.value=parseFloat(elemento.value).toFixed(2);

    }


    //Desabilita Componentes
    function desabilita_componente(elemento,id){

        var id_tipo = parseInt(elemento.value);

        if ((id_tipo == 1)){

           $('#fecha'+id).val("");

           document.getElementById('fecha'+id).disabled=true;
           document.getElementById('numero'+id).disabled=true;
           document.getElementById('id_banco'+id).disabled=true;

        }else{

          if((id_tipo == 2)||(id_tipo == 3)){

            document.getElementById('fecha'+id).disabled=false;
            document.getElementById('numero'+id).disabled=false;
            document.getElementById('id_banco'+id).disabled=false;

          }

        }
    }


    //AgregA Elementos a la tabla nuevo_productos a Facturar
    function  crear_reg_mov_productos(contador){

        id= document.getElementById('contador').value;

        var midiv = document.createElement("tr")
            midiv.setAttribute("id","dato"+id);
            midiv.className="well";

        midiv.innerHTML = ''+//'<td><input type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"><input type="text" class="hidden" id="id_prod'+id+'" name="id_prod'+id+'"><input type="text" class="hidden" name="cost_prod'+id+'" id="cost_prod'+id+'">
        //<input name="nombre'+id+'" class="nombre" id="nombre'+id+'"  onchange="cambiar_nombre('+id+')"></td>
        //<td><input type="text" style="width: 110px;" id="cantidad'+id+'" value="0.00" onkeyup="total_calculo('+id+')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="return completa_decimales(this,'+id+',2);" name="cantidad'+id+'" autofocus></td>
        //<td><input type="text" style="width: 110px;" id="precio'+id+'" name="precio'+id+'" value="0.00" onkeyup="total_calculo('+id+')" onchange="return redondea_precio(this,'+id+',2);"></td>
        //<td><input type="text" style="width: 110px;" id="desc_porcentaje'+id+'" name="desc_porcentaje'+id+'" value="0.00" onkeyup="total_calculo('+id+')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  autofocus onchange="return redondea_descuento(this,'+id+',2);"></td>
        //<td><input type="text" style="width: 110px;" id="desc'+id+'" name="desc'+id+'"  value="0.00" disabled><input type="text" class="hidden" name="desc1'+id+'" id="desc1'+id+'"></td><td><input type="text" name="extendido'+id+'" id="extendido'+id+'" value="0.00"  value="" disabled><input type="text" class="hidden" name="extendido1'+id+'" id="extendido1'+id+'"></td><td><input type="checkbox" id="iva'+id+'" name="iva'+id+'" disabled></td><td><button  id="eliminar'+id+'" type="button" onclick="eliminar_registro('+id+')" class="btn btn-warning btn-margin">Eliminar</button></td>';
        '<td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" />'+
        //'<div class="row"><div class="col-xs-2"><input name="codigo'+id+'" class="form-control codigo" style="height:20px;" id="codigo'+id+'" disabled></div>'+
        //'<div class="col-xs-8"><input name="nombre'+id+'" style="height:20px;" class="form-control nombre" id="nombre'+id+'" disabled></div></div>'+
        '<select  name="nombre[]" id="nombre'+id+'"  class="form-control" style="width:100%" required  onchange="verificar(this)">'+
        '<option> </option>'+
        '@foreach($productos as $value)'+
        //'<option value="{{$value->codigo}} | {{$value->descripcion}}" @if($value->codigo ==  "codigo'+id+'") selected="selected" @endif data-name="{{$value->nombre}}" data-codigo="{{$value->id}}" data-descuento="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>'+
        '<option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-maxdesc="{{$value->descuento}}"  data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>'+
        '@endforeach'+
        '</select>'+
        '<textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>'+
        '<input type="hidden" name="iva[]" class="iva" />'+
        '</td>'+
        '<td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required></td>'+
        '<td><select  name="precio[]" class="form-control select2_precio pneto" style="width:100%" required><option value="0"> </option></select></td>'+
        '<td><input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" name="copago[]" required></td>'+
        '<td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required></td>'+
        '<td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td>'+
        '<td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required></td>'+
        '<td><input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]"></td>'+
        '<td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        

            document.getElementById('agregar_producto').appendChild(midiv);
            id = parseInt(id);
            id = id+1;
            document.getElementById('contador').value = id;

            //Completa el codigo del Producto al Ingresar
            $(".codigo").autocomplete({
                source: function( request, response ) {
                    $.ajax( {
                      url: "{{route('ventas_completa_codigo')}}",
                      dataType: "json",
                      data: {
                        term: request.term
                      },
                      success: function( data ) {
                        response(data);
                      }
                    } );
                },
                selectFirst: true,
                minLength: 1,
            } );


            //Completa el nombre  del Producto al Ingresar
            $(".nombre").autocomplete({
              source: function( request, response ) {
                $.ajax( {
                  url: "{{route('ventas_buscar_nombre')}}",
                  //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  dataType: "json",
                  //type: 'post',
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response(data);
                  }
                } );
              },
              selectFirst: true,
              minLength: 3,
            });

            //$('body').on('change', '.pneto', function () {
        $('.pneto').each(function(){
            verificar(this);
        console.log("me meti jajajaj");
        var cant = $(this).parent().prev().children().val();
        console.log(cant);

        var copago = $(this).parent().next().children().val();
        console.log("precio",$(this).val());
        console.log(copago);
        
        var descuento = $(this).parent().next().next().next().children().val();
        console.log(descuento);
        var total = (parseInt(cant)* parseFloat($(this).val())) - descuento - copago;
        console.log(total);
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
   //    totales(0);
   
    });

           totales(0);

    }



    //Agregamos la Forma de Pago a la Factura de Venta
    $('#btn_pago').click(function(event){

        id= document.getElementById('contador_pago').value;


        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_pago"+id);

       //     midiv_pago.innerHTML = '<td><select name="id_tip_pago'+id+'" id="id_tip_pago'+id+'" style="width: 175px;height:25px" onchange="desabilita_componente(this,'+id+');"><option value="">Seleccione</option> @foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago'+id+'" name="visibilidad_pago'+id+'" value="1"></td><td><input type="date" class="input-number" value="{{date('Y-m-d')}}" name="fecha'+id+'" id="fecha'+id+'" style="width: 110px;"></td><td><div><input type="text" name="numero'+id+'" id="numero'+id+'" style="width: 100px;" required></div></td><td><select name="id_banco'+id+'" id="id_banco'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><select id="id_cuenta'+id+'" name="id_cuenta'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($cuentas as $value)<option value="{{$value->id}}" style="width: 175px;height:25px">{{$value->nombre}}</option>@endforeach</select></td><td><div><input type="text" id="valor'+id+'" name="valor'+id+'" style="width: 100px;" onchange="return redondea_valor(this,'+id+',2);" onkeyup="suma_valor_forma_pago()"></div></td><td><div><input type="text" id="valor_base'+id+'" name="valor_base'+id+'" style="width: 100px;" onchange="return redondea_valor_base(this,'+id+',2);"></div></td><td><button type="button" onclick="eliminar_form_pag('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';
       midiv_pago.innerHTML = '<td width="20%"><select class="form-control" name="id_tip_pago[]" style="width:90%;" id="id_tip_pago'+id+'" onchange="desabilita_componente(this,'+id+');"><option value="">Seleccione</option> @foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago'+id+'" name="visibilidad_pago'+id+'" value="1"></td>'+
        '<td  width="10%"><input type="date" class="form-control input-number" value="{{date('Y-m-d')}}" name="fecha_pago[]" id="fecha'+id+'" ></td>'+
        '<td width="10%"><div><input type="text" class="form-control input-number" name="numero_pago[]" id="numero'+id+'" required></div></td>'+
        '<td  width="15%"><select class="form-control" name="id_banco_pago[]" id="id_banco'+id+'" style="width:90%;" ><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td>'+
        '<td width="10%"><input class="form-control text-right" type="text" style="width: 80%;height:20px;"  name="id_cuenta_pago[]"></td>'+
        '<td width="10%"><input class="form-control text-right" type="text" style="width: 80%;height:20px;"  name="giradoa[]"></td>'+
        //'<td width="20%"><select class="form-control" id="id_cuenta'+id+'" name="id_cuenta_pago[]" ><option value="">Seleccione...</option>@foreach($cuentas as $value)<option value="{{$value->id}}" >{{$value->nombre}}</option>@endforeach</select></td>'+
        '<td width="10%"><div><input class="form-control text-right input-number" type="text" id="valor'+id+'" name="valor[]"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"></div></td>'+
        '<td width="10%"><div><input class="form-control text-right input-number" type="text" id="valor_base'+id+'" name="valor_base[]"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"></div></td>'+
        '<td width="5%"><button type="button" id="btn_forma" class="btn-danger btn-gray delete" onclick="eliminar_form_pag('+id+')"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        //                  <button type="button" onclick="eliminar_form_pag('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';
//
midiv_pago.className="well";
        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador_pago').value = id;

    });


    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor)
    {
        var dato_pago1 = "dato_pago"+valor;
        var nombre_pago2 = 'visibilidad_pago'+valor;
        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        suma_valor_forma_pago();

    }

    //Elimina Registro de la Tabla Productos
    function eliminar_registro(valor)
    {
        var dato1 = "dato"+valor;
        var nombre2 = 'visibilidad'+valor;
        document.getElementById(dato1).style.display='none';
        document.getElementById(nombre2).value = 0;
        suma_totales();
    }


</script>

<script type="text/javascript">

    $(function () {

        $('#fecha_proced').datetimepicker({
            format: 'YYYY/MM/DD HH:mm'
        });

    });


</script>

</section>

@endsection
