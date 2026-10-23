@extends('contable.facturacion.base')
@section('action-content')
<style type="text/css">
    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .alerta_guardado {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .disableds {
        display: none;
    }

    .disableds2 {
        display: none;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;

    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .card-header {
        border-radius: 6px 6px 0 0;
        background-color: #3c8dbc;
        border-color: #b2b2b2;
        padding: 8px;
        font-family: 'Roboto', sans-serif;
    }

    .col-md-6 {
        margin-top: 7px;
    }
</style>
<style type="text/css">
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

    .disableds {
        display: none;
    }

    .dogde {
        width: 100%;
        height: 20px;
    }

    .disableds2 {
        display: none;
    }

    .wells {
        background-color: #E3F2FD;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;

    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }

    .borde {
        border: 2px solid #3c8dbc;
    }
    .s{
        font-size: 17px!important;
    }
    .visible{
        display: none;
    }
</style>
<!-- 
http://192.168.75.125/sis_medico_prb/public/contable/facturacion/agenda/21526 -->
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Guardado Correctamente
    </div>
    <div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Ingrese Cedula y Ruc
    </div>
    @php 
                            $sp= 'Consulta';
                            if($tipo=='0'){
                                $sp= 'Consulta';
                            }else{

                                $sp= 'Procedimiento';
                            }
                        @endphp
    <div class="box box s">
        <div class="header box-header with-border">
            <div class="box-title col-md-12">
                <b style="font-size: 16px;">Crear Recibo de Cobro {{$sp}}</b>
            </div>
        </div>
        @php 
                    $stringarm="agenda/".$agenda->id."/edit/".$agenda->id_doctor1;
                   
                 @endphp
        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical" id="crear_form" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="0.00"> 
                <input type="hidden" name="empresa" id="id_empresa" value="{{$empresa->id}}">
                <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                <input type="hidden" name="id_doctor" id="id_doctor" value="{{$agenda->id_doctor1}}">
                <input type="hidden" name="tipo_dato" value="{{$tipo}}">
                <input type="hidden" value="{{$iva->iva}}" name="ivareal" id="ivareal" class="form-control">
                <div class="col-md-12">
                    <div class="row dobra">

                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <label class="col-md-8">Datos Principales</label>
                                        <div class="col-md-2" style="text-align: center;">
                                            <button class="btn btn-primary btn-gray visible" id="printer" type="button" onclick="fun()"> <i class="fa fa-print"></i> </button>
                                        </div>
                                        <div class="col-md-2" style="text-align: right;">
                                        <button onclick="return location.href='{{url($stringarm)}}'" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-arrow-left"></i>
                                            </button>
                                            <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:210px;height:70px">
                                            <div> &nbsp; </div>
                                            <label> &nbsp; </label>
                                            <label>{{$empresa->nombrecomercial}}</label>
                                            <label>{{$empresa->direccion}}</label>

                                        </div>
                                        <div class="col-md-2">

                                            <div class="input-group input-group-sm mb-6">
                                                <label for="numero">Fecha de Procedimiento</label>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_procedimiento" value="{{date('Y-m-d',strtotime($agenda->fechaini))}}" readonly class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                            <label for="numero">Fecha de Emision</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"> <i class="fa fa-date"></i> </span>
                                                </div>
                                                <input type="date" name="f_emision" value="{{date('Y-m-d')}}" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                                            </div>
                                        </div>

                                         <div class="col-md-6">
                                            <label>Concepto</label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="observacion" id="observacion" cols="3" rows="3"></textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label> Datos</label>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group col-md-12" style="background-color: #3d7ba8; color: white;">
                                            <label> Datos de Paciente</label>
                                            <input type="hidden" id="id_store" value="0">
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="idpaciente" class="col-md-4">Cédula</label>
                                            <div class="input-group  col-md-8">
                                                <input id="idpaciente" maxlength="10" type="text" class="form-control input-sm" name="idpaciente" value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif" placeholder="Cédula" onchange="verificar_numero_cedula()">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('idpaciente').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-7 col-xs-12">
                                            <label for="nombres" class="col-md-3">Paciente</label>
                                            <div class="input-group col-md-9">
                                                <input type="text" class="form-control input-sm" name="nombres" id="nombres" value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-5 col-xs-12">
                                            <label for="id_seguro" class="col-md-4 control-label">Seguro</label>
                                            <div class="input-group col-md-8">
                                                <select class="form-control input-sm" name="id_seguro" id="id_seguro" required onchange="seguro(),cargar_nivel()">
                                                    <option value="">Seleccione ...</option>
                                                    @foreach($seguros as $seguro)
                                                    <option @if($agenda->id_seguro == $seguro->id) selected @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div id="ident_nivel" class="col-md-3 col-xs-5">
                                            <label for="id_nivel" class="col-md-3 control-label">Nivel</label>
                                            <div class="input-group col-md-9">
                                                <select name="id_nivel" id="id_nivel" class="form-control input-sm">
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4 col-xs-12">
                                            <div class="col-md-7">
                                                <label>¿Posee ODA?</label>
                                                <input type="radio" name="interesado" value="si"  id="interesadoPositivo"> Sí
                                                <input type="radio" name="interesado" value="no" id="interesadoNegativo" > No
                                            </div>

                                            <div class="col-md-5">
                                                <input type="text" name="oda" class="form-control input-sm"  placeholder="# ODA" id="oda">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            &nbsp;
                                        </div>
                                        <div class="form-group col-md-12" style="background-color: #d4850e; color: white;">
                                            <label> Datos de cliente</label>
                                        </div>
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">Identificación</label>
                                            <div class="input-group col-md-7">
                                                <select id="tipo_identificacion" name="tipo_identificacion" class="form-control input-sm" required autofocus="">
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='4') selected='selected' @endif @endif value="4">RUC</option>
                                                    <option  @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='5' || $ct_cliente->tipo=='05') selected='selected' @endif @endif value="5">CEDULA</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='6') selected='selected' @endif @endif  value="6">PASAPORTE</option>
                                                    <option @if(!is_null($ct_cliente) && $ct_cliente!=Array()) @if($ct_cliente->tipo=='8') selected='selected' @endif @endif value="8">CEDULA EXTRANJERA</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--Ruc/Cedula-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="cedula" class="col-md-5 control-label">Ruc/Cédula</label>
                                            <div class="input-group col-md-7">
                                                <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@else{{old('cedula')}}@endif" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Razon Social-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="razon_social" class="col-md-5 control-label">Razon Social</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="razon_social" id="razon_social" value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre}}@endif" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required> 
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Ciudad-->
                                        
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="ciudad" class="col-md-5 control-label">Ciudad</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Direccion-->
                                        <div class="form-group col-md-66 col-xs-6">
                                            <label for="direccion" class="col-md-5 control-label">Dirección</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="direccion" id="direccion" value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Telefono-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="telefono" class="col-md-5 control-label">Teléfono</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Email-->
                                        <div class="form-group col-md-6 col-xs-12">
                                            <label for="email" class="col-md-5 control-label">Mail</label>
                                            <div class="input-group col-md-7">
                                                <input type="text" class="form-control input-sm" name="email" id="email" value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif" placeholder="Mail">
                                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-12" style="background-color: #028484;height: 10px;">
                        </div> -->
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-body" style="padding:0;">

                                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well' style="color: black;">

                                                <th width="35%" tabindex="0">Descripci&oacute;n del Producto</th>
                                                <th width="10%" tabindex="0">Cantidad</th>
                                                <th width="10%" tabindex="0">Precio</th>
                                                <th width="10%" tabindex="0">Cobrar Paciente</th>
                                                <th width="10%" tabindex="0">% Desc</th>
                                                <th width="10%" tabindex="0">Descuento</th>
                                                <th width="10%" tabindex="0">Precio Neto</th>
                                                <th width="5%" tabindex="0">IVA</th>
                                                <th width="10%" tabindex="0">
                                                    <button onclick="nuevoa()" type="button" class="btn btn-success btn-gray">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="agregar_cuentas">
                                            
                                            <tr>

                                                <td style="max-width:100px;">
                                                    @if($tipo==0)
                                                    <input type="hidden" name="codigo[]" class="codigo_producto" />
                                                    @else 
                                                    <input type="hidden" name="codigo[]" class="codigo_producto" value="CONSULTA" />
                                                    @endif
                                                    <select name="nombre[]" class="form-control select2" style="width:100%" required onchange="verificar(this)">
                                                        <option> </option>
                                                        @foreach($productos as $value)
                                                        <option @if($tipo==0) @if($value->codigo=='CONSULTA') selected='selected' @endif @endif  value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                                        @endforeach
                                                    </select>
                                                    <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                                    <input type="hidden" class="precioOriginal" value="0">
                                                    <input type="hidden" name="iva[]" class="iva" />
                                                </td>
                                                <td>
                                                    <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]"  required>

                                                </td>
                                                <td>
                                                  <input class="form-control text-right pneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" value="0.00" required> 
<!--                                                     <select name="precio[]" class="form-control select2_precio pneto"
                                                        style="width:60%;height:20px;display:inline;" required>
                                                        <option value="0"> </option>
                                                    </select>
                                                    <button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp">
                                                        <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                                    </button> -->

                                                </td>
                                                <td>
                                                    <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="100" name="copago[]"  required>
                                                    <input class="copaged" type="hidden" name="copaged[]"  value="0.00">
                                                    <button type="button" class="btn btn-info btn-gray btn-xs cp" >
                                                        <i class="fa fa-percent" aria-hidden="true"></i>
                                                    </button>
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
                                                    <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-xs btn-gray delete">
                                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs btn-gray showme" disabled>
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                            

                                        </tbody>

                                    </table>
                                    <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr style="display: none;">
                                                
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

                                        <tbody>

                                            <input type="hidden" name="ti_factura" value="{{$tipo}}" />


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
                                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
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
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td colspan="2" class="text-right"><strong>Por Cobrar Seguro</strong></td>
                                                <td id="copagoTotal" class="text-right px-1">0.00</td>
                                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="form-group col-md-12">
                                    <label for="pago" class="col-md-6 control-label">Caja de Cobro:</label>
                                    <div class="input-group col-md-6">
                                        <select id="caja" name="caja" class="form-control" required>
                                            
                                          <option value="">Seleccione...</option>
                                          <option value="Torre 1">Torre 1</option>
                                          <option value="Torre 2">Torre 2</option>
                                          <option value="Pentax">PENTAX</option>
                                          <option value="Torre 2 Nocturno">Torre 2 Nocturno</option>
                                        </select>
                                      </div>
                                  </div>
                        <div class="col-md-12 table-responsive ">
                                           
                                            <div class="col-md-12">
                                            <label> Formas de Pago</label>
                                            
                                            <button id="btn_pago" type="button" class="btn btn-success btn-xs btn-gray" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                            </div>
                                            <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                                            <table id="example1" role="grid" class="table table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">

                                                <thead>
                                                    <tr>
                                                        <th style="text-align: center;">Metodo</th>
                                                        <th style="text-align: center;">Fecha</th>
                                                        <th style="text-align: center;">Tipo</th>
                                                        <th style="text-align: center;">Número</th>
                                                        <th style="text-align: center;">Banco</th>
                                                        <th style="text-align: center;">Posee Fi</th>
                                                        <th style="text-align: center;">Cuenta</th>
                                                        <th style="text-align: center;">Girado</th>
                                                        <th style="text-align: center;">Valor</th>
                                                        <th style="text-align: center;">Valor B</th>
                                                        <th style="text-align: center;">
                                                         
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="agregar_pago">
                                                    <tr style="display:none" id="mifila">

                                                        <td style="max-width:100px;">
                                                            <input type="hidden" name="codigo[]" class="codigo_producto" />
                                                            <select name="nombre[]" class="form-control select2" style="width:100%;" required onchange="verificar(this)">
                                                                <option> </option>
                                                                @foreach($productos as $value)
                                                                    <option value="{{$value->nombre}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}" data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}">{{$value->codigo}} | {{$value->descripcion}}</option>
                                                                @endforeach

                                                            </select>
                                                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                                            <input type="hidden" name="iva[]" class="iva" />
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                                        </td>
                                                        <td>
                                                         <input class="form-control text-right pneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="precio[]" required>
                                                         <!--   <select name="precio[]" class="form-control select2_precio pneto"
                                                                style="width:60%;height:20px;display:inline;" required>
                                                                <option value="0"> </option>
                                                            </select>
                                                            <button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp">
                                                                <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                                            </button> -->
                                                        </td>
                                                        <td>
                                                        <input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="100" name="copago[]" required>
                                                        <input class="copaged" type="hidden" name="copaged[]"  value="0.00">
                                                    <button type="button" class="btn btn-info btn-gray btn-xs cp" disabled>
                                                        <i class="fa fa-percent" aria-hidden="true"></i>
                                                    </button>
                                                    <!-- <input type="hidden" name="sources[]" class="noFetch" value="$"> -->
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
                                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-gray btn-xs delete">
                                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-xs btn-gray showme">
                                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                    <tr style="display:none" id="mifilaf">

                                                        <td style="max-width:100px;">
                                                            <input type="hidden" name="codigo[]" class="codigo_producto" value="deducible" />
                                                            <input class="form-control day inpu-sm" style="width: 80%;height:20px;" type="text" name="nombre[]" value="DEDUCIBLE">
                                                            <textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto input-sm" placeholder="Detalle del producto"></textarea>
                                                            <input type="hidden" name="iva[]" class="iva" />
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right cnetox cneto input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="cantidad[]" required>
                                                        </td>
                                                        <td>
                                                            <input style="width: 80%; height: 20px;" class="pnetox form-control input-sm text-right" onblur="this.value=parseFloat(this.value).toFixed(3);" name="precio[]" type="text" value="0.00">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right copago input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="copago[]" readonly>
                                                            <input class="copaged" type="hidden" name="copaged[]"  value="0.00">
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right pdesc input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required readonly>
                                                            <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right desc input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control px-1 text-right input-sm" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled>

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-xs btn-gray delete">
                                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                        </div>
                        <div class="col-md-12" style="text-align:center; margin-top:10px;">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Valor Total: </label>
                                    <input class="form-control" type="text" id="valorTotals" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Valor Pagado: </label>
                                    <input class="form-control" type="text" id="sobrante" onchange="changeSobrante(this)"  placeholder="$" >
                                </div>
                                <div class="col-md-4">
                                    <label>Diferencia: </label>
                                    <input class="form-control" type="text" id="diferencia" readonly value="0.00" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                            <button class="btn btn-success btn-gray saves" type="button" onclick="guardar(this)"> <i class="fa fa-save"></i> </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script type="text/javascript">
    function verificar_numero_cedula() {
        cedula = validarCedula($('idpaciente').val());
    }
    function changeSobrante(e){
        var total= parseFloat($("#valorTotals").val());
        var nuevo= parseFloat($(e).val());
        tot=0;
        if(total>0){
            if(nuevo<=total){
                var tot= (total-nuevo);
            }else{
                var tot= (total-nuevo) * (-1);
            }
            
            $('#diferencia').val(tot.toFixed(2,2));
        }else{
            $(e).val('0.00');
            swal('Ingrese valores en los productos');
        }
        
    }
    function cargar_nivel() {

        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();

        $.ajax({
            type: 'post',
            url: "{{route('lista_nivel.seguro')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro': xseguro,
                'id_empresa': id_emp
            },
            success: function(data) {
                console.log(data);

                if (data.value != 'no') {
                    //if(data.count()>0){

                    //alert("Prueba Ingreso");

                    if (xseguro != 0) {

                        //Muestra el Nivel del Seguro
                        //document.getElementById("ident_nivel").style.visibility = "visible";  
                        $("#id_nivel").empty();
                        $.each(data, function(key, registro) {
                            $("#id_nivel").append('<option value=' + registro.id_nivel + '>' + registro.nombre + '</option>');
                        });

                    } else {

                        $("#id_nivel").empty();

                    }

                } else {

                    //alert("Prueba Ingreso 2");
                    //Oculta el Nivel del Seguro
                    document.getElementById("ident_nivel").style.visibility = "hidden";

                }

            },
            error: function(data) {

            }
        });

    }

    function soloNumeros(e) {
        // capturamos la tecla pulsada
        var teclaPulsada = window.event ? window.event.keyCode : e.which;

        // capturamos el contenido del input
        var valor = e.value;
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
    var fila = $("#mifila").html();
    var fila2 = $("#mifilaf").html();

    function nuevoa() {
        var nuevafila = $("#mifila").html();
        
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }

    function nuevoa() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        $('.select2').select2({
            tags: false
        });
    }

    function nuevo_deducible(data,s,source) {
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);

        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        var input = document.createElement('input');
        var input2 = document.createElement('input');
        input.type = 'hidden';
        input.name = "codigoref[]";
        input.value = data;
        input.className = "findme";
        input2.type = 'hidden';
        input2.name = "changd[]";
        input2.value = source;
        input2.className = "sir";
        rowk.innerHTML = fila2;
        rowk.append(input);
        rowk.append(input2);
        rowk.className = "wells";
    }

    $('body').on('click', '.cp', function() {
        //console.log($(this));
        //console.log($(this).prev().attr('class'));
        var clase = $(this).prev().attr('class');//console.log("vt");console.log(clase);
        var html =
            '<input type="text" class="form-control vt_pneto"  name="vt_precio[]" style="width:40%;display:inline;height:20px;">' +
            '<button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp">' +
            '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>' +
            '</button>';
        console.log($(this).parent());
        if (clase.includes('select2_precio')) {
            $(this).parent().append(html);
            $(this).prev().remove();
            $(this).remove();

        } else {
            /*html =
                '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;height:20px;display:inline;" autofocus active required>' +
                '<option value="0"> </option></select>' +
                '<button type="button" class="btn btn-info btn-gray boton_desh btn-xs cp" >' +
                '<i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>';*/
            // $(this).parent().empty();
            // $(this).parent().append(html);
            $(this).parent().append(html);
            verificar($(this).parent().prev().prev().children().closest('.select2_cuentas'));
            //$(this).prev().remove();
            $(this).remove();

        }


    });

    $('body').on('change', '.vt_pneto', function() {
        valor_neto = $(this).parent().prev().children().val();
        if ( valor_neto != null ){
            valor_neto = parseFloat(valor_neto);
            if(valor_neto > 0){    
                valor_pagar_paciente = $(this).val();
                if(valor_pagar_paciente > 0){
                    valor_pagar_paciente = parseFloat(valor_pagar_paciente);
                    porcentaje = valor_pagar_paciente / valor_neto * 100;
                    porcentaje = Math.round(porcentaje * 100) / 100;
                    $(this).parent().find(".copago").val(porcentaje);
                    elemento_copago = $(this).parent().find(".copago");
                    console.log("PILAS VT");
                    /////////////
                    var cant = elemento_copago.parent().prev().prev().children().val();//console.log(cant);
                    var precio = elemento_copago.parent().prev().children().val();

                    var copago = elemento_copago.val();
                    
                    var copaged= elemento_copago.parent().find('.copaged');
                
                    //alert(copaged);
                    var fetch= elemento_copago.parent().find('.noFetch').val();
                    //console.log(fetch);
                        //console.log("copago", copago);
                    var descuento = elemento_copago.parent().next().next().children().val();
                    var copage=  parseFloat(copago) /100;
                    if(copage==0){
                        copage=1;
                    }
                    var total = ((parseInt(cant) * parseFloat(precio)) - descuento) * copage;
                    var total2= ((parseInt(cant) * parseFloat(precio)) - descuento) - ((parseInt(cant) * parseFloat(precio)) - descuento) * copage;
                    //console.log("cant:"+cant+" precio:"+precio+" desc:"+descuento+" copago: "+copago+" total2:"+total2+" total:"+total);
                    console.log(elemento_copago.parent().next().next().next().children());console.log("total"+total2);
                    copaged.val(total2);
                    if(copago<=0){
                        elemento_copago.parent().find('.copaged').val(0);
                        console.log('error');
                    }
                    //console.log(total);
                    elemento_copago.parent().next().next().next().children().val(total.toFixed(2));

                    totales(0);
                }
            } 
        }       
    });    
    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        //console.log($(e).parent().next().next().children().closest(".cp"));

        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            //console.log("modifica precio");
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            //console.log("no modifca el precio");
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
         /* if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        } */
        //$(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
        }
        //cargarPrecios
        var tipo = $("#tipo_cliente").val();
        var selected = "selected=''";
        $.ajax({
            type: 'post',
            url: "{{route('contable_precio_prod.tarifario_codigo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_emp': '{{$empresa->id}}',
                'id_prod': codigo,
                'id_seg': $("#id_seguro").val(),
                'id_niv': $("#id_nivel").val()
            },
            success: function(data) {

                //alert(data.nivel);
                console.log(data);
                console.log('aqui va los precios de los productos')
                $(e).parent().next().next().children().find('option').remove();
             /*    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + data.producto.id_producto + ' ' + selected + '>' + data.producto.precio_producto + '</option>'); */
                $(e).parent().next().next().children().val(data.producto.precio_producto);
              /*   $(e).parent().next().next().children().closest(".pneto").trigger('change'); */
                $(e).parent().find('.precioOriginal').val(data.producto.precio_producto);
               
               /*  $.each(data, function(key, value) {
                    console.log(value)
                    $(e).parent().next().next().children().closest(".pneto").append('<option value=' + value.precio_prod + ' ' + selected + '>' + value.precio_prod + '</option>');

                }); */
                totales(0);
 
            },
            error: function(data) {
                console.log(data.responseText);
            }
        });
        totales(0)

    }

    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto
    $('body').on('change', '.pneto', function() {
        // verificar(this);
        var cant = $(this).parent().prev().children().val();
        var ts= $(this).val();
        $(this).parent().prev().prev().find('.precioOriginal').val(ts);
        var copago = $(this).parent().next().children().val();
        var copage= $(this).parent().next().find('.copaged').val();
        if(copage!=0){
            copage= copago/100;
        }else{
            copage=1;
        }
        var descuento = $(this).parent().next().next().next().children().val();
        var total = ((parseInt(cant) * parseFloat($(this).val())) - descuento)*copage;
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
        totales(0);
    });
    $('body').on('change', '.cneto', function() {
        // verificar(this);
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        // console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        var copage = $(this).parent().next().next().find('.copaged');
        if(copage!=0){
            copage= copago/100;
        }else{
            copage=1;
        }
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = ((parseInt(cant) * (precio)) - descuento)*copage;
        $(this).parent().next().next().next().next().next().children().val(total.toFixed(2, 2));
        totales(0);
    });
    $('body').on('change', '.pnetox', function() {
        // verificar(this);
        var cant = 1;
        var copago = $(this).parent().next().children().val();
        var descuento = $(this).parent().next().next().next().children().val();
        var total = (parseInt(cant) * parseFloat($(this).val())) - descuento;
        $(this).parent().next().next().next().next().children().val(total.toFixed(2));
        //totales(0);
        var code = $(this).parent().parent().find('.findme').val();
        var ant = $(this).parent().parent().prev().find('.precioOriginal').val();
        //$(this).parent().parent().prev().find('.cp').click();
        var s = ant - $(this).val();
        //console.log(s);
        if ($("#id_seguro").val() == '4') {
            $(this).parent().parent().prev().find('.pneto').val(s.toFixed(2, 2));
            totales(1);
        }else{
            totales(0);
        }
        
    });
    $('body').on('change', '.cnetox', function() {
        // verificar(this);
        var cant = 1;
        var precio = $(this).parent().next().children().val();
        // console.log("this", $(this).parent().next().children().val());
        var copago = $(this).parent().next().next().children().val();
        var copage=  parseFloat(copago) /100;
        //console.log("copago", copago);
        var descuento = $(this).parent().next().next().next().next().children().val();
        var total = ((parseInt(cant) * (precio)) - descuento)*copage;
        $(this).parent().next().next().next().next().next().children().val(total.toFixed(2, 2));
        //totales(0);
        //console.log($(this).parent().parent().prev().find('.pneto').html());
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    $('body').on('change', '.copago', function() {
        //verificar(this);
        if($('#oda').val()!='0' && $("#oda").val()!=''){
            var cant = $(this).parent().prev().prev().children().val();
            var precio = $(this).parent().prev().children().val();

            var copago = $(this).val();
            
            var copaged= $(this).parent().find('.copaged');
        
            //alert(copaged);
            var fetch= $(this).parent().find('.noFetch').val();
            //console.log(fetch);
                //console.log("copago", copago);
            var descuento = $(this).parent().next().next().children().val();
            var copage=  parseFloat(copago) /100;
            if(copage==0){
                copage=1;
            }
            var total = ((parseInt(cant) * parseFloat(precio)) - descuento) * copage;
            var total2= ((parseInt(cant) * parseFloat(precio)) - descuento) - ((parseInt(cant) * parseFloat(precio)) - descuento) * copage;
            copaged.val(total2);
            if(copago<=0){
                $(this).parent().find('.copaged').val(0);
                console.log('error');
            }
            console.log($(this).parent().next().next().next().children());console.log("total"+total2);
            $(this).parent().next().next().next().children().val(total.toFixed(2));

            totales(0);
        }
 
    });


    $('body').on('change', '.pdesc', function() {

        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
        var pdesc = $(this).val();
        //console.log("el descuento maximo debe de ser", m, pdesc);
        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = $(this).parent().prev().children().val();
        var copage=  parseFloat(copago) /100;
        var total = ((parseInt(cant) * parseFloat(precio)) - descuento)*copage;
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
        var copage = $(this).parent().prev().prev().find('.noFetch').val();
        var cope= parseFloat(copago) /100;
        var total = ((parseInt(cant) * parseFloat(precio)) - descuento) * cope;
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
    $('body').on('change', '.fbase', function() {
        var total_pagos = 0;
        $('.fbase').each(function(i, obj) {
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
        var finaly=0;
        var d = 0;
        var final=0;
        var copas=0;
        var s=0;
        var total=0;
        var copagoTotal = 0;
        if (e == 0) {
            //console.log('sumar')
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
                var st= parseFloat(copago)/100;
                if(st<=0){
                    st= 1;
                }
                var copage = $(this).parent().next().next().find('.copaged').val();
                if(copage==undefined){
                    copage=0;
                }
                var descuento = $(this).parent().next().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
                //console.log(iva);
                /* finaly += (parseInt(cant) * parseFloat(precio)) - parseFloat(0); */
                var total = ((parseInt(cant) * parseFloat(precio)) - parseFloat(0)) * (st);
                if($("#oda").val()!='0' && $("#oda").val()!=''){
                    if(copago==0){
                     total= ((parseInt(cant) * parseFloat(precio)) - parseFloat(0)) - ((parseInt(cant) * parseFloat(precio)) - parseFloat(0));
                    }
                }

                if (iva == 1) {
                    //console.log(subt12);
                    subt12.push(total);
                    sb12 = sb12 + total;
                } else {
                    subt0.push(total);
                    sb0 = sb0 + total;
                }
                if($("#oda").val()!='0' && $("#oda").val()!=''){
                   
                    if(copago==0){
                        copagoTotal= parseFloat(copagoTotal)+((parseInt(cant) * parseFloat(precio)) - parseFloat(0));
                    }else{
                        copagoTotal = parseFloat(copagoTotal) + parseFloat(copage);
                    }
                }
                console.log(sb0,total);
                $("#subtotal_12").html(sb12.toFixed(2));
                $("#subtotal_0").html(sb0.toFixed(2));
                $("#descuento").html(d.toFixed(2));
                $("#base").html(sb12.toFixed(2));
                var iva = $("#ivareal").val();
                var ti = iva * sb12;
                $("#tarifa_iva").html(ti.toFixed(2));
                var t = (sb12 + sb0 + ti - d);
                $("#total").html(t.toFixed(2));
                $('#valorTotals').val(t.toFixed(2,2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                $("#subtotal_121").val(sb12.toFixed(2));
                $("#subtotal_01").val(sb0.toFixed(2));
                $("#descuento1").val(d.toFixed(2));
                $("#tarifa_iva1").val(ti.toFixed(2));
                $("#total1").val(t.toFixed(2));
                $("#totalc").val(copagoTotal.toFixed(2));
            });
        }else{
            //console.log("entra aqui");

            /*   
             YA SE COMO AHCERLO PUEDES PREGUNTAR SI TIENE COPAGO LA COSA ES DISTINTA EN LA SUMA DEL SUBTOTAL
             
            */
        }
    }
    $(document).ready(function() {
        limpiar();
        obtener_fecha();
        $('.select2').select2({
            tags: false
        });
        cargar_nivel();
        @if($tipo=='0')
            $('.select2').trigger('change');
        @endif


    });
    // Accedemos al botón
    var emailInput = document.getElementById('oda');

    // evento para el input radio del "si"
    document.getElementById('interesadoPositivo').addEventListener('click', function(e) {
        console.log('Vamos a habilitar el input text');
        emailInput.disabled = false;
        $('.copago').prop('disabled', false);
        $('.copago').each(function(){
            $(this).val(100);
        });
        emailInput.value = "";
    });

    // evento para el input radio del "no"
    document.getElementById('interesadoNegativo').addEventListener('click', function(e) {
        console.log('Vamos a deshabilitar el input text');
        emailInput.disabled = "disabled";
        $('.copago').prop('disabled', 'disabled');
        emailInput.value = "";
    });
    
    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#numero_oda").val('0');

    }
    $('body').on('click', '.delete', function() {
        //console.log($(this));
        $(this).parent().parent().remove();
        totales(0);
    });
    $('body').on('click', '.chx', function() {
        //console.log($(this));
        $(this).parent().parent().find('.iva').val(1);
        totales(0);
    });
    $('body').on('click', '.showme', function() {
        //console.log($(this));
        //$(this).parent().parent().remove();
        //totales(0);
        //console.log($(this).parent().parent().children().find('.codigo_producto').val());
        var codigo = $(this).parent().parent().children().find('.codigo_producto').val();
        var nombre = $(this).parent().parent().children().find('.select2');
        var name_espace = $('option:selected', nombre).data('name');
        var getSource = $(this).parent().parent().find('.noFetch').val();
        //console.log(getSource);
        if (codigo != "") {
            //console.log(name_espace);
            nuevo_deducible(codigo, name_espace,getSource);
            $(this).parent().parent().next().find('.day').val('DEDUCIBLE + ' + name_espace);
        } else {
            swal("Falta ingresar producto");
        }

    });
    $('body').on('click', '.deducible', function() {
        // verificar(this);
        $(this).parent().parent().find('.deducible').show();
        console.log("hear");
    });
    $('#btn_pago').click(function(event) {
        id = document.getElementById('contador_pago').value;
        var midiv_pago = document.createElement("tr")
        midiv_pago.setAttribute("id", "dato_pago" + id);
        midiv_pago.innerHTML = '<td><select class="dogde" name="id_tip_pago' + id + '" id="id_tip_pago' + id + '" style="width: 100px;height:20px" onchange="revisar_componentes(this,' + id + ');" required><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input type="hidden" id="visibilidad_pago' + id + '" name="visibilidad_pago' + id + '" value="1"></td><td><input type="date" class="dogde input-number" value="{{date('Y-m-d')}}" name="fecha_pago' + id + '" id="fecha_pago' + id + '" style="width: 120px;"></td><td><select  id="tipo_tarjeta' + id + '"  class="dogde" name="tipo_tarjeta' + id + '" style="width: 175px;height:20px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input  type="text" name="numero_pago' + id + '" id="numero_pago' + id + '" style="width: 100px;" ></td><td><select class="dogde" name="id_banco_pago' + id + '" id="id_banco_pago' + id + '" style="width: 90px;height:20px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input  style="text-align:center;" type="checkbox" name="fi'+id+'" id="fi'+id+'" onchange="revision_total('+id+')" value="0" ></td><td><input  autocomplete="off" class="dogde" name="id_cuenta_pago' + id + '" id="id_cuenta_pago' + id + '" ></td><td><input class="dogde"  type="text" id="giradoa' + id + '" name="giradoa' + id + '"></td><td><input class="dogde text-right input-number fpago" type="text" id="valor' + id + '" name="valor' + id + '" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(' + id + ')" onkeypress="return soloNumeros(this);" required></td><td><input class="dogde input-number fbase" type="text" readonly id="valor_base' + id + '" name="valor_base' + id + '" required onkeypress="return soloNumeros(event);" ></td><td><button style="text-align:center;" type="button" onclick="eliminar_form_pag(' + id + ')" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_pago').value = id;

    });

    function revision_total(id) {
        var fi = document.getElementById("fi" + id);
        var valor = $('#valor' + id).val();
        if(valor>0){
            if (fi.checked == true) {
            tipo = $('#id_tip_pago' + id).val();
            if (tipo == '4') {
                ntotal = valor * 1.07;
            } else if (tipo == '6') {
                ntotal = valor * 1.02;
            } else {
                ntotal = valor * 1;
            }
            $('#valor_base' + id).val(ntotal.toFixed(2));
            var tos= parseFloat($('#total1').val());
            var permiso= ntotal- valor;
            if(permiso<0){
                permiso= permiso * -1;
            }
            permiso= permiso.toFixed(2,2);
            if(valor>0){
                snew(permiso);
            }

            } else {
                ntotal = valor * 1;
                $('#valor_base' + id).val(ntotal.toFixed(2));
            }
            suma_total();
        }

    }
    function snew(a){
        var poe='<tr>'+'<td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" value="FEE-" /><select name="nombre[]" class="form-control select2" style="width:100%;" required onchange="verificar(this)"><option> </option>@foreach($productos as $value) @if($value->codigo=="FEE-")<option @if($value->codigo=="FEE-") selected="selected" @endif>{{$value->codigo}} | {{$value->descripcion}}</option> @endif @endforeach</select><textarea wrap="hard" rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea><input type="hidden" name="iva[]" class="iva" /></td>'+'<td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required></td>'+'<td><input class="form-control text-right pneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="'+a+'" name="precio[]" required></td>'+'<td><input class="form-control text-right copago" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="100" name="copago[]" required><input class="copaged" type="hidden" name="copaged[]"  value="0.00"><button type="button" class="btn btn-info btn-gray btn-xs cp" disabled><i class="fa fa-percent" aria-hidden="true"></i></button></td>'+'<td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td>'+'<td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td>'+'<td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required></td>'+'<td><input class="form chx" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]"></td>'+'<td><button type="button" class="btn btn-danger btn-gray btn-xs delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger btn-xs btn-gray showme"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button></td>'+'</tr>';
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = poe;
        $('.select2').select2({
            tags: false
        });
        totales(0);
    }

    function suma_total() {
        var contador = $('#contador_pago').val();
        var sumador = 0;
        var sumador_sin = 0;

        for (var i = 0; i < contador; i++) {
            if ($('#visibilidad_pago' + i).val() == 1) {
                sumador_sin = sumador_sin + parseFloat($('#valor_base' + i).val());
                sumador = sumador + parseFloat($('#total' + i).val());
            }
        }

        $('#total_sin_tarjeta').val(sumador_sin.toFixed(2));
        $('#total_final1').val(sumador.toFixed(2));
        
    }

    function revisar_componentes(e, id) {
        metodo = $('#id_tip_pago' + id).val();
        if (metodo == 1) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', true);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', true);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 2) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 3) {
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', true);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', true);
            revision_total(id);
        } else if (metodo == 4) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 5) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        } else if (metodo == 6) {
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#numero" + id).prop('disabled', false);
            $("#tipo_tarjeta" + id).prop('disabled', false);
            $("#id_banco" + id).prop('disabled', false);
            $("#fi" + id).prop('disabled', false);
            revision_total(id);
        }
    }

    function validar_copago() {
        // Get the checkbox
        var checkBox = document.getElementById("copago");
        // Get the output text
        var text = document.getElementById("dato_copago");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true) {
            document.getElementById("div_copago2").style.display = "block";
            $('#valor_copago').val('0');
            text.style.display = "block";
        } else {
            $('#valor_copago').val('0');
            document.getElementById("div_copago2").style.display = "none";
            text.style.display = "none";
        }
    }

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor) {
        var dato_pago1 = "dato_pago" + valor;
        var nombre_pago2 = 'visibilidad_pago' + valor;
        document.getElementById(dato_pago1).style.display = 'none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();

        recalcular_fpago();

    }
    function recalcular_fpago(){
        var total_pagos = 0;
        $('.fbase').each(function(i, obj) {
            total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        });
        console.log('lo ultimo en la forma de pago es : '+total_pagos);
        $("#valor_totalPagos").val(total_pagos);
    }

    function obtener_fecha() {

        //obtenemos la fecha actual
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        $("#fecha").val(today);

    }


    /*function obtener_num_factura(){
        $.ajax({
            url:"{{route('num_fact.consulta')}}",
            type: 'get',
            datatype: 'json',
            success: function(data){
               console.log(data);
               $('#nfactura').val(data);
            },
            error: function(data){
                console.log(data);
            }
        })
    }*/

    //Sucursal Empresa Agenda
    /*function obtener_sucursal(){

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

    //obtener Caja
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

    }*/

    function crear_factura() {
        $('#crear_recibo').button('loading');

        var formulario = document.forms["crear_form"];

        //Datos Generales
        var id_emp = formulario.id_empresa.value;
        /*var sucurs = formulario.sucursal.value;
        var punt_emision = formulario.punto_emision.value;*/


        //Datos Paciente
        var cedula = formulario.idpaciente.value;
        var nombre_paciente = formulario.nombres.value;
        var seguro_paciente = formulario.id_seguro.value;


        //Datos Clientes
        var tipo_identicacion = formulario.tipo_identificacion.value;
        var ced_cliente = formulario.cedula.value;
        var raz_social = formulario.razon_social.value;
        var ciud_cliente = formulario.ciudad.value;
        var dire_cliente = formulario.direccion.value;
        var telf_cliente = formulario.telefono.value;
        var email_cliente = formulario.email.value;
        var numero_oda = formulario.numero_oda.value;
        var copago = formulario.copago;




        //Concepto y Nota

        //var concepto = formulario.concepto.value;
        //var nota = formulario.nota.value;

        //Pago
        var pago = formulario.pago.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";

        //Datos Generales

        if (id_emp == "") {

            msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }
        //alert(copago.checked);
        if (copago.checked == true) {

            if (numero_oda == '0' || numero_oda == '') {
                msj = msj + "Por favor, Ingrese el número de Oda<br/>";
            }
        }

        /*if(sucurs == ""){

           msj = msj + "Por favor, Seleccione la Sucursal<br/>";
        }

        if(punt_emision == ""){
           msj = msj + "Por favor, Seleccione el Punto de Emision<br/>";
        }*/

        //Paciente
        if (cedula == "") {
            msj += "Por favor,Ingrese la cedula del paciente<br/>";
        }
        if (nombre_paciente == "") {
            msj += "Por favor,Ingrese el nombre del paciente<br/>";
        }
        if (seguro_paciente == "") {
            msj += "Por favor,Seleccione el seguro paciente<br/>";
        }

        //Cliente
        if (tipo_identicacion == "") {
            msj += "Por favor,Selecione el Tipo de Identificaciòn<br/>";
        }
        if (ced_cliente == "") {
            msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if (raz_social == "") {
            msj += "Por favor,Ingrese la razon social<br/>";
        }
        if (ciud_cliente == "") {
            msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }

        if (dire_cliente == "") {
            msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if (telf_cliente == "") {
            msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if (email_cliente == "") {
            msj += "Por favor,Ingrese el email del cliente<br/>";
        }


        //Pago
        if (pago == "") {
            msj += "Por favor,Ingrese el pago\n";
        }
        if (caja_pago == "") {
            msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        var i;
        var max = document.getElementById('agregar_pago').rows.length;
        for (i = 0; i < max; i++) {
            var tipo_pago = document.getElementById('id_tip_pago' + i).value;
            var numero = document.getElementById('numero' + i).value;
            var id_banco = document.getElementById('id_banco' + i).value;
            var tipo_tarjeta = document.getElementById('tipo_tarjeta' + i).value;
            if (tipo_pago == '2') {

                if (numero == "") {
                    msj += "Por favor,Ingrese el número del cheque<br/>";
                }
                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
            if (tipo_pago == '4') {
                if (tipo_tarjeta == "") {
                    msj += "Por favor,Ingrese el Tipo de tarjeta<br/>";
                }

                if (id_banco == "") {
                    msj += "Por favor,Seleccione el Banco<br/>";
                }
            }
        }

        if (msj != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
            return false;
        }

        if (msj2 != "") {
            $('#crear_recibo').button('reset');
            swal({
                title: "Error!",
                type: "error",
                html: msj2
            });
            return false;
        }

        var fecha = document.getElementById('fecha').value;

        var unix = Math.round(new Date(fecha).getTime() / 1000);

        $.ajax({
            type: 'post',
            url: "{{route('facturacion.guardar_orden')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data) {

                //console.log(data);
                $("#ride").attr("href", );
                window.open("{{asset('/comprobante/orden/venta')}}/" + data.id_orden, '_blank ');
                window.location.href = "{{asset('/agenda/calendario/')}}/{{$agenda->id_doctor1}}/" + unix;
                $('#crear_recibo').button('reset');

            },
            error: function(data) {
                console.log(data);

            }
        });
    }

    function seguro() {
        var seguro = $('#id_seguro').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/seguro/')}}" + '/' + seguro,
            datatype: 'html',
            success: function(data) {
                if (data == 1) {

                } else {

                }
                pagar();

            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    function pagar() {
        var seguro = $('#id_seguro').val();
        var doctor = $('#id_doctor').val();
        $.ajax({
            type: 'get',
            url: "{{asset('contable/facturacion/verificar/pago/')}}" + '/' + seguro + '/' + doctor,
            datatype: 'html',
            data: $("#obs_id").serialize(),
            success: function(data) {
                $('#pago').val(data.trim());
            },
            error: function() {
                //alert('error al cargar');
            }
        });
    }

    $("#cedula").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('facturacion.buscar_cliente')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 3,
    });

    function buscar() {
        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;
        if (cedula_1.length < 10) {
            alert('La Cantidad de Digitos no es Correcta');
        }
        var cedula = validarCedula($('#cedula').val());
        if (cedula == false && pasaporte != 6 && pasaporte != 8) {
            /* $('#cedula').val('');
            $('#razon_social').val('');
            $('#ciudad').val('');
            $('#direccion').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#caja').val(''); */

        } else {

            $.ajax({
                type: 'post',
                url: "{{route('facturacion.cliente')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data) {
                    if (data.value != 'no') {
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        $('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }

    }
    function guardar(e){
       if($('#crear_form').valid()){
        recalcular_fpago();
        var valor_totalp= parseFloat($('#valor_totalPagos').val());
        var total_pago= valor_totalp.toFixed(2,2);
        if(total_pago==parseFloat($("#total1").val())){
            $.ajax({
                type: 'post',
                url: "{{route('facturacion.store_new')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#crear_form").serialize(),
                success: function(data) {
                    console.log(data);
                    swal('Mensaje','Guardado correctamente','success');
                    $(e).attr('disabled','disabled');
                    $('#printer').removeClass('visible');
                    $('#id_store').val(data.id);
                    window.open('{{url("comprobante/orden/venta/")}}/'+data.id,'_blank');
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }else{
            swal('Mensaje','No coinciden los valores de pago con el total, Total pago: '+total_pago+' Total Recibo: '+$('#total1').val(),'error');
        }
        
       }
        
                
    }
    function fun(){
        var id= $("#id_store").val();
        if(id!=0){
            window.open('{{url("comprobante/orden/venta/")}}/'+id,'_blank');
        }else{
            swal("Compruebe si el guardado fue correcto");
        }
        
    }

</script>
@endsection