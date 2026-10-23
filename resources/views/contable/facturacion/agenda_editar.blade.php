@extends('contable.facturacion.base')
@section('action-content')
<style type="text/css">

        .alerta_correcto{
          position: absolute;
          z-index: 9999;
          bottom: 100px;
          right: 20px;
        }

        .alerta_guardado{
          position: absolute;
          z-index: 9999;
          bottom: 100px;
          right: 20px;
        }

        .disableds{
            display: none;
        }
        .disableds2{
            display: none;
        }
        .disableds3{
            display: none;
        }
        .has-cc span img{
            width:2.775rem;
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
        .has-cc .form-control-cc2{
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
        .cvc_help{
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

        .card-header{
            border-radius: 6px 6px 0 0;
            background-color: #3c8dbc;
            border-color: #b2b2b2;
            padding: 8px;
            font-family: 'Roboto', sans-serif;
        }

        .col-md-6{
            margin-top: 7px;
        }
</style>
<style type="text/css">
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
        .disableds{
            display: none;
        }
        .disableds2{
            display: none;
        }
        .disableds3{
            display: none;
        }
        .has-cc span img{
            width:2.775rem;
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
        .has-cc .form-control-cc2{
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
        .cvc_help{
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
        .cabecera{
            background-color: #3c8dbc;
            border-radius: 8px;
            color: white;
        }
        .borde{
            border:2px solid #3c8dbc;
        }
</style>

    <section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
           Guardado Correctamente
    </div>
    <div id="msj_ingreso" class="alert alert-success alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
            Ingrese Cedula y Ruc
    </div>
    <div class="box box-primary box-solid">
        <div class="header box-header with-border" >
            <div class="box-title col-md-12" >
                <b style="font-size: 16px;">Editar Recibo de Cobro</b>
            </div>
        </div>
        <div class="box-body">
            <a href="#" target="_blank" id="ride"></a>
            <form class="form-vertical size_text" id="crear_form" method="POST" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">

                <div class="col-md-12">&nbsp;</div>

                <div class="col-sm-12">
                    <div class="card-header">
                        <b style="color: white">DATOS GENERALES</b>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">&nbsp;</div>
                        <!--Listado de Empresas a Facturar-->
                        <div class="col-md-5 col-xs-6">
                            <label for="id_empresa" class="control-label">Empresa</label>
                             <select class="form-control input-sm" name="id_empresa" id="id_empresa"  onchange="" required>
                                @foreach($empresas as $value)
                                    <option  @if($orden->id_empresa == $value->id) selected @endif @if(old('id_empresa')==$value->id) selected @endif value="{{$value->id}}">{{$value->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2" style="padding-left: 12px;padding-right: 2px;">
                            <label  for="fecha_emision" class="control-label">Fecha de Emisión:</label>
                            <div class="input-group date">
                                <input  type="date" class="form-control" name="fecha" id="fecha"
                                value="{{$orden->fecha_emision}}" autocomplete="off">
                                <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('fecha').value = '';"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php $paciente = $orden->agenda->paciente; @endphp
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">DATOS PACIENTE</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-6 col-xs-6">
                                    <label for="id" class="col-md-2">Cédula</label>
                                    <div class="input-group  col-md-9">
                                        <input id="idpaciente" maxlength="10" type="text" class="form-control" name="idpaciente"
                                        value="@if($paciente != Array() && !is_null($paciente)){{$paciente->id}}@elseif($id != 0){{$id}}@else{{old('idpaciente')}}@endif" placeholder="Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('idpaciente').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Nombre Paciente-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="nombres" class="col-md-2">Paciente</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="nombres" id="nombres"
                                        value="@if($paciente != Array()){{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif @endif" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 3px"></div>
                                <!--seguro-->
                                <div class="col-md-6 col-xs-6" >
                                    <label for="id_seguro" class="col-md-2 control-label">Seguro</label>
                                    <div class="input-group col-md-9">
                                        <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="seguro()">
                                            <option value="">Seleccione ...</option>
                                            @foreach($seguros as $seguro)
                                                <option  @if($orden->id_seguro == $seguro->id) selected @endif @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div  id="div_copago" class="col-md-6 col-xs-3" style="@if($orden->seguro->copago == 0) display: none; @endif">
                                    <label for="copago" class="col-md-2 control-label">Posee ODA</label>
                                    <div class="input-group col-md-9">
                                        <input id="copago"  type="checkbox"  name="copago" placeholder="Posee ODA" onclick="validar_copago();" @if($orden->oda=='1') checked @endif @if($orden->valor_oda>0 || $orden->numero_oda>0) checked @endif>
                                    </div>
                                </div>

                                 <div class="col-md-12" style="padding-top: 3px"></div>


                                <div id="dato_copago" class="col-md-6 col-xs-6 oculto ">
                                    <label for="valor_copago" class="col-md-2">Valor Copago($)</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="valor_copago" id="valor_copago"
                                        value="{{$orden->valor_oda}}" placeholder="Valor de Copago" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloNumeros(event);">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('valor_copago').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                                <div id="div_copago2" class="col-md-6 col-xs-3" style="@if($orden->seguro->copago == 0) display: none; @endif">
                                    <label for="numero_oda" class="col-md-2 control-label">No. ODA(#)</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="numero_oda" id="numero_oda"  value="{{$orden->numero_oda}}" placeholder="numero_oda" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onkeypress="return soloNumeros(event);">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('numero_oda').value = '';"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">DATOS CLIENTE</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-6 col-xs-6">
                                    <label for="cedula" class="col-md-2 control-label">Identificación</label>
                                    <div class="input-group col-md-9">
                                        <select id="tipo_identificacion" name="tipo_identificacion" onchange="borrar()" class="form-control" autofocus="">
                                            <option {{ $orden->tipo_identificacion == 4 ? 'selected' : ''}} value="4">RUC</option>
                                            <option {{ $orden->tipo_identificacion == 5 ? 'selected' : ''}} value="5">CEDULA</option>
                                            <option {{ $orden->tipo_identificacion == 6 ? 'selected' : ''}} value="6">PASAPORTE</
                                            <option {{ $orden->tipo_identificacion == 8 ? 'selected' : ''}} value="8">CEDULA EXTRANJERA</option>
                                        </select>
                                    </div>
                                </div>
                                <!--Ruc/Cedula-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="cedula" class="col-md-2 control-label">Ruc/Cédula</label>
                                    <div class="input-group col-md-9">
                                        <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula"
                                        value="@if($ct_cliente != Array() && !is_null($ct_cliente)){{$ct_cliente->identificacion}}@else{{old('cedula')}}@endif" placeholder="Ruc/Cédula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();" required>
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Razon Social-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="razon_social" class="col-md-2 control-label">Razon Social</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="razon_social" id="razon_social"
                                        value="@if(!is_null($ct_cliente)){{$ct_cliente->nombre}}@endif" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Ciudad-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="ciudad" class="col-md-2 control-label">Ciudad</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="ciudad" id="ciudad" value="@if(!is_null($ct_cliente)){{$ct_cliente->ciudad_representante}}@endif" placeholder="Ciudad" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding-top: 3px"></div>
                                <!--Direccion-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="direccion" class="col-md-2 control-label">Dirección</label>
                                    <div class="input-group col-md-9">
                                        <input  type="text" class="form-control input-sm" name="direccion" id="direccion" value="@if(!is_null($ct_cliente)){{$ct_cliente->direccion_representante}}@endif" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Telefono-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="telefono" class="col-md-2 control-label">Teléfono</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="telefono" id="telefono" value="@if(!is_null($ct_cliente)){{$ct_cliente->telefono1_representante}}@endif" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Email-->
                                <div class="col-md-6 col-xs-6">
                                    <label for="email" class="col-md-2 control-label">Mail</label>
                                    <div class="input-group col-md-9">
                                        <input type="text" class="form-control input-sm" name="email" id="email" value="@if(!is_null($ct_cliente)){{$ct_cliente->email_representante}}@endif" placeholder="Mail" >
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                <!--Observacion-->
                                <div class="col-md-12" style="margin-top: 5px;">
                                    <label for="observacion" class="col-md-1 control-label">Observaciones</label>
                                    <div class="input-group col-md-10">
                                        <textarea class="form-control input-md" name="observacion" id="observacion">{{$orden->observacion}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12 col-xs-12"></div>
                <div class="form-group col-md-12 col-xs-12">
                    @if(isset($agenda))
                        @if(($agenda->proc_consul)==1)
                            <input type="hidden" name="tipo_dato" id="tipo_dato" value="{{$agenda->proc_consul}}">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-12" style="padding-left: 30px">
                                        <div class="card-header">
                                        <b style="font-size: 12px;color: white">PROCEDIMIENTOS</b>
                                        </div>


                                        @php
                                        $arr_cobrar = [];
                                        $arr_conf = [];
                                        $arr_usados = [];
                                        $esinsumo = 0;
                                        foreach($procedimientos as $productosConfi){
                                            $arr_conf[$productosConfi->id] = $productosConfi->cantidad;
                                        }
                                        if($insumos_pistoleados == null){
                                            $esinsumo = 0;
                                        }else{
                                            $esinsumo = 1;
                                            foreach($insumos_pistoleados as $productosUsados){
                                                if(array_key_exists($productosUsados->id, $arr_usados)){
                                                    $arr_usados[$productosUsados->id] = $arr_usados[$productosUsados->id] + 1;
                                                }else{
                                                    $arr_usados[$productosUsados->id] = 1;
                                                }
                                            }

                                            foreach($insumos_pistoleados as $insumos){
                                                //$key = array_key_exists($insumos->id, $arr_conf);
                                                /*if(array_key_exists($insumos->id, $arr_cobrar)){
                                                    if(array_key_exists($insumos->id, $arr_cobrar)){
                                                        $arr_cobrar[$insumos->id] = $arr_cobrar[$insumos->id] + 1;

                                                }else{
                                                    $arr_cobrar[$insumos->id] = 1;
                                                }*/
                                                if(array_key_exists($insumos->id, $arr_conf)){
                                                    $adicional = $arr_usados[$insumos->id] - $arr_conf[$insumos->id];
                                                    if($adicional > 0){
                                                        $arr_cobrar[$insumos->id] = $adicional;
                                                    }
                                                    $esinsumo = 1;
                                                }else{
                                                    $arr_cobrar[$insumos->id] = $arr_usados[$insumos->id];
                                                }

                                            }
                                        }





                                        $productos_cobrar = Sis_medico\Ct_productos::whereIn('id', array_keys($arr_cobrar))->get();
                                        @endphp
                                        <div style="display:none">
                                        {{$insumos_pistoleados}}
                                        </div>

                                        <div class="card-body">
                                            <div class="col-md-12">&nbsp;</div>
                                            <div class="table-responsive col-md-12">
                                            <input name="contador_procedimiento" id="contador_procedimiento" type="hidden" value="{{sizeof($procedimientos)}}">
                                            <table id="example1" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                                <thead style="background-color: #FFF3E3">
                                                    <tr>
                                                    <th style="width: 10%; text-align: center;">Procedimiento</th>
                                                    <th style="width: 10%; text-align: center;">Precio</th>
                                                    <th style="width:10%;  text-align: center">Cantidad</th>
                                                    <th style="width: 10%; text-align: center;">Cobrar Seguro</th>
                                                    <th style="width: 10%; text-align: center;">Descuento</th>
                                                    <th style="width: 10%; text-align: center;">Iva</th>
                                                    <th style="width: 10%; text-align: center;">Precio Neto</th>
                                                    <th style="width: 10%; text-align: center;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="agregar_procedimiento" style="text-align: center;">
                                                <!-- aqui van los procedimientos, debo de traerlos por query los producots y todo -->
                                                @php $cont = 0; @endphp

                                                @if($esinsumo == 0)
                                                @foreach($procedimientos as $procedimiento)

                                                <tr id="dato_procedimiento{{$cont}}">
                                                    <td>
                                                        <input required type="hidden" id="visibilidad_procedimiento{{$cont}}" name="visibilidad_procedimiento{{$cont}}" value="1">
                                                        <input type="text" name="procedimiento{{$cont}}" value="{{$procedimiento->nombre}}"  >
                                                    </td>
                                                    <td>
                                                    <input type="hidden" id="id_procedimiento{{$cont}}" class="productoid" value="{{$procedimiento->idproducto}}">
                                                        <select id="valor_pro{{$cont}}" name="valor_pro{{$cont}}" class="precioid" style="width: 100px;" onchange="revision_total_pro('{{$cont}}') ">
                                                            @php
                                                                $precio_producto = Sis_medico\PrecioProducto::where('codigo_producto', $procedimiento->codproducto)->get();
                                                            @endphp
                                                            @foreach($precio_producto as $precio)
                                                            <option>{{$precio->precio}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="1" name="cantidad{{$cont}}" id="cantidad{{$cont}}" style="width: 100px;" onchange="revision_total_pro('{{$cont}}')" required>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="0.00" name="copago{{$cont}}" id="copago{{$cont}}" style="width: 100px;" required>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="0.00" name="desc{{$cont}}" id="desc{{$cont}}" onchange="revision_total_pro('{{$cont}}')"  style="width: 100px;" required>
                                                    </td>

                                                    <td>
                                                        <input required style="text-align:center;" type="checkbox" name="iva{{$cont}}" id="iva{{$cont}}" onchange="revision_total_pro('{{$cont}}')" value="{{$procedimiento->iva}}" @if($procedimiento->iva) checked @endif disabled>
                                                        <input type="hidden" value="{{$procedimiento->iva}}" name="llevaiva{{$cont}}" />
                                                    </td>
                                                    <td>
                                                        <input required type="text" id="total_procedimiento{{$cont}}" name="total_procedimiento{{$cont}}" style="width: 100px;" onkeypress="return soloNumeros(event);"  onchange="return redondea_valor_base(this,'{{$cont}}',2);" value="0.00">
                                                    </td>
                                                    <td>
                                                        <button type="button" onclick="eliminar_form_procedimiento('{{$cont}}')" class="btn btn-warning btn-margin">Eliminar </button>
                                                    </td>
                                                </tr>
                                                @php $cont++; @endphp
                                                @endforeach

                                            @else
                                                @foreach($productos_cobrar as $procedimiento)

                                                <tr id="dato_procedimiento{{$cont}}">
                                                    <td>
                                                        <input required type="hidden" id="visibilidad_procedimiento{{$cont}}" name="visibilidad_procedimiento{{$cont}}" value="1">
                                                        <input type="text" name="procedimiento{{$cont}}" value="{{$procedimiento->nombre}}"  >
                                                    </td>
                                                    <td>
                                                    <input type="hidden" id="id_procedimiento{{$cont}}" class="productoid" value="{{$procedimiento->id}}">
                                                        <select id="valor_pro{{$cont}}" name="valor_pro{{$cont}}" class="precioid" style="width: 100px;" onchange="revision_total_pro('{{$cont}}') ">
                                                            @php
                                                                $precio_producto = Sis_medico\PrecioProducto::where('codigo_producto', $procedimiento->codigo)->get();
                                                            @endphp
                                                            @foreach($precio_producto as $precio)
                                                            <option>{{$precio->precio}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="1" name="cantidad{{$cont}}" id="cantidad{{$cont}}" style="width: 100px;" onchange="revision_total_pro('{{$cont}}')" required>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="0.00" name="copago{{$cont}}" id="copago{{$cont}}" style="width: 100px;" required>
                                                    </td>
                                                    <td>
                                                        <input required type="text" value="0.00" name="desc{{$cont}}" id="desc{{$cont}}" onchange="revision_total_pro('{{$cont}}')"  style="width: 100px;" required>
                                                    </td>

                                                    <td>
                                                        <input required style="text-align:center;" type="checkbox" name="iva{{$cont}}" id="iva{{$cont}}" onchange="revision_total_pro('{{$cont}}')" value="{{$procedimiento->iva}}" @if($procedimiento->iva) checked @endif disabled>
                                                        <input type="hidden" value="{{$procedimiento->iva}}" name="llevaiva{{$cont}}" />
                                                    </td>
                                                    <td>
                                                        <input required type="text" id="total_procedimiento{{$cont}}" name="total_procedimiento{{$cont}}" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'{{$cont}}',2);">
                                                    </td>
                                                    <td>
                                                        <button type="button" onclick="eliminar_form_procedimiento('{{$cont}}')" class="btn btn-warning btn-margin">Eliminar </button>
                                                    </td>
                                                </tr>
                                                @php $cont++; @endphp
                                                @endforeach

                                                @endif
                                                </tbody>
                                                <tfoot style="text-align: center;">
                                                    <tr>
                                                        <td colspan="4">Total:</td>
                                                        <td><input type="text" name="total_pro" id="total_pro" readonly="readonly"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-2 col-xs-2">
                                                    <div class="box-footer" >
                                                    <button type="button" id="btn_procedimiento" class="btn btn-primary size_text">
                                                    Agregar
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 30px">
                            <div class="card-header">
                              <b style="font-size: 12px;color: white">FORMAS DE PAGO</b>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-4 col-xs-6">
                                    <label for="pago" class="col-md-6 control-label">Valor a Pagar</label>
                                    <div class="input-group col-md-6">
                                        <input type="hidden" name="total_sin_tarjeta" id="total_sin_tarjeta" value="0">
                                        <input readonly="readonly" onkeypress="return soloNumeros(event);" type="text" class="form-control input-sm" name="pago" id="total_final1" value="0" placeholder="Pago">
                                        <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('pago').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-group col-md-6">
                                    <label for="pago" class="col-md-2 control-label">Caja de Cobro:</label>
                                    <div class="input-group col-md-10">
                                        <select  id="caja" name="caja" class="form-control"required>
                                            <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('caja').value = '';"></i>
                                          <option value="">Seleccione...</option>
                                          <option @if($orden->caja=='Torre 1') selected @endif value="Torre 1">Torre 1</option>
                                          <option @if($orden->caja=='Torre 2') selected @endif value="Torre 2">Torre 2</option>
                                          <option @if($orden->caja=="Torre 2 Nocturno") selected @endif value="Torre 2 Nocturno">Torre 2 Nocturno</option>
                                        </select>
                                      </div>
                                  </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="height: 15px;">&nbsp;</div>
                        <div class= col-md-12">
                            <div class="table-responsive col-md-12">
                                    <input name="contador_pago" id="contador_pago" type="hidden" value="{{$xcantidad}}">
                                    <table id="example1" role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #FFF3E3">
                                            <tr>
                                               <th style="width: 4%; text-align: center;">Metodo</th>
                                               <th style="width: 10%; text-align: center;">Fecha</th>
                                               <th style="width: 7%; text-align: center;">Tipo</th>
                                               <th style="width: 10%; text-align: center;">Número Transaccion</th>
                                               <th style="width: 7%; text-align: center;">Banco</th>
                                               <th style="width: 7%; text-align: center;">Posee Fi</th>
                                               <th style="width: 7%; text-align: center;">Valor Base</th>
                                               <th style="width: 7%; text-align: center;">Valor</th>
                                               <th style="width: 7%; text-align: center;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agregar_pago">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2 col-xs-2">
                                            <div class="box-footer" >
                                              <button type="button" id="btn_pago" class="btn btn-primary size_text">
                                              Agregar
                                              </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="form-group col-md-12 col-xs-12"></div>
            <div class="form-group col-xs-6" style="text-align: center;">
                <div class="col-md-6 col-md-offset-4">
                    <button id="crear_recibo" data-loading-text="Generando..." class="btn btn-primary" onclick="crear_factura()">
                    Actualizar Recibo
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script type="text/javascript">

    function soloNumeros(e)
    {
        // capturamos la tecla pulsada
        var teclaPulsada=window.event ? window.event.keyCode:e.which;

        // capturamos el contenido del input
        var valor=e.value;

        // 45 = tecla simbolo menos (-)
        // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
        // Modificamos el contenido del mismo añadiendo el simbolo menos al
        // inicio
        if(teclaPulsada==45 && valor.indexOf("-")==-1)
        {
            document.getElementById("inputNumero").value="-"+valor;
        }

        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if(teclaPulsada==13 || (teclaPulsada==46 && valor.indexOf(".")==-1))
        {
            return true;
        }

        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    $(document).ready(function(){
        limpiar();
        validar_copago();
        cargar_tabla();
        suma_total();
        //obtener_fecha();
    });

    function limpiar(){
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        //$("#numero_oda").val('0');

    }
    function totales(){
        var contador = $("#contador_procedimiento").val();
        for (let index = 0; index < contador; index++) {
            //const element = array[index];
            //
            revision_total_pro(index);
        }
    }

    function revision_total_pro(id){
        var fi = document.getElementById("iva"+id);
        var valor = $('#valor_pro'+id).val();
        var nvalor = valor *1;
        //$('#valor_pro'+id).val(nvalor.toFixed(2));
        var descuento = $('#desc'+id).val();
        valor = valor - descuento;
        var cantidad = $('#cantidad'+id).val();


        if(fi.checked == true){
            tipo = $('#id_tip_pago'+id).val();
            ntotal = ((valor) *1.12) *cantidad;
            $('#total_procedimiento'+id).val(ntotal.toFixed(2));
        }else{
            ntotal = ((valor)*cantidad);
            $('#total_procedimiento'+id).val(ntotal.toFixed(2));
        }
        suma_total_pro();
        verificar_valores();

    }
    function verificar_valores(){
        console.log("revisar valore");
        var fee= 0;
        var unfee = false;
        var restar = 0;
        var band = 1;
        $(".fee").each(function() {
            var res = parseFloat($('#valor_base'+$(this).data("id")).val()) - parseFloat($('#total'+$(this).data("id")).val());
            if(res==0){
                band = band*1;
            }else{
                band = band*0;
            }
            console.log("restar ",restar);
            if($(this).prop('checked')){
                unfee = true;
                //console.log("check");
                var valorfee = $('#valor_base'+$(this).data("id")).val();
                var tipo = $('#id_tip_pago'+$(this).data("id")).val();
                //console.log("1.- "+ valorfee);
                if(tipo == '4'){
                    valorfee = parseFloat(valorfee) * 0.07;
                    fee = fee + valorfee;
                }else if(tipo == '6'){
                    valorfee = parseFloat(valorfee) * 0.02;
                    fee = fee + valorfee;
                }else{
                    valorfee = 0;
                    fee = fee + valorfee;
                    $(this).prop('checked',false);
                }
                if($("input[data-tipo='fee']").val()>0){
                    console.log("- existe");

                    var total = parseFloat($('#valor_base'+$(this).data("id")).val())+ parseFloat(valorfee);
                    $("#total"+$(this).data("id")).val(parseFloat(total).toFixed(2));
                    var i= $("input[data-tipo='fee']").data('idrel');
                    $("#dato_procedimiento"+i).show();
                    var valoractual = $("#total_procedimiento"+i).val();
                    //console.log(valoractual);
                    valoractual = parseFloat(valoractual)+ parseFloat(valorfee);
                    $("#total_procedimiento"+i).val(parseFloat(fee).toFixed(2));
                    $("#valor_pro"+i).val(parseFloat(fee).toFixed(2));
                   // revision_total_pro(i);
                    //var pago =
                }else{
                    //console.log("- no existe");
                    //console.log($(this).data("id"));
                    var total = parseFloat($('#valor_base'+$(this).data("id")).val())+ parseFloat(valorfee);
                    //console.log(total);
                    $('#total'+$(this).data("id")).val(parseFloat(total).toFixed(2));
                    id= document.getElementById('contador_procedimiento').value;

                    var midiv_pago = document.createElement("tr")
                    midiv_pago.setAttribute("id","dato_procedimiento"+id);

                    midiv_pago.innerHTML = '<td><input required type="hidden" id="visibilidad_procedimiento'+id+'" data-idrel="'+id+'" data-tipo="fee" name="visibilidad_procedimiento'+id+'" value="1"><input type="text" name="procedimiento'+id+'" value="FEE ADMINISTRATIVO" class="feead"></td><td><input required type="text" id="valor_pro'+id+'" name="valor_pro'+id+'" style="width: 100px;"  value="'+parseFloat(fee).toFixed(2)+'" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td><td><input required type="text" value="0.00" name="copago'+id+'" id="copago'+id+'" style="width: 100px;" required></td> <td><input required type="text" id="desc'+id+'" name="desc'+id+'" style="width: 100px;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td> <td><input required style="text-align:center;" type="checkbox" name="iva'+id+'" id="iva'+id+'" onchange="revision_total_pro('+id+')" value="0" ></td><td><input required type="text" id="total_procedimiento'+id+'" name="total_procedimiento'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" value="'+parseFloat(valorfee).toFixed(2)+'" onchange="return redondea_valor_base(this,'+id+',2);"></td><td><button type="button" onclick="eliminar_form_procedimiento('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';

                    document.getElementById('agregar_procedimiento').appendChild(midiv_pago);
                    id = parseInt(id);
                    id = id+1;
                    document.getElementById('contador_procedimiento').value = id;

                    //revision_total_pro(id-1);
                }
            }else{
                console.log("unckeck");
                console.log(fee);
                if(!unfee){
                    var i= $("input[data-tipo='fee']").data('idrel');
                    var valoractual = $("#total_procedimiento"+i).val();
                    //console.log(valoractual);
                    valoractual = parseFloat(valoractual)+ parseFloat(valorfee);
                    $("#total_procedimiento"+i).val(parseFloat(0).toFixed(2));
                    $("#valor_pro"+i).val(parseFloat(0).toFixed(2));

                    //$("#dato_procedimiento"+i).hide();
                    if(band){
                        console.log("hide"+i);
                        $("#dato_procedimiento"+i).hide();
                    }else{
                        console.log("adss");
                        $("#dato_procedimiento"+i).show();
                    }
                }
            }
        });
    }
    function cargar_tabla(){

        $.ajax({
            type: 'get',
            url:"{{route('factura.listado',['id' => $orden->id])}}",
            datatype: 'json',
            success: function(data){
                console.log(data);
                $('#agregar_pago').empty().html(data);

            },
            error: function(data){
                console.log(data);

            }
        });

    }
    function eliminar_form_procedimiento(valor)
    {
        var dato_pago1 = "dato_procedimiento"+valor;
        var nombre_pago2 = 'visibilidad_procedimiento'+valor;
        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();
        suma_total_pro();
    }

    $('#btn_pago').click(function(event){

        id= document.getElementById('contador_pago').value;


        var midiv_pago = document.createElement("tr")
            midiv_pago.setAttribute("id","dato_pago"+id);

        midiv_pago.innerHTML = '<td><select required name="id_tip_pago'+id+'" id="id_tip_pago'+id+'" style="width: 175px;height:25px" onchange="revisar_componentes(this,'+id+');"><option value="">Seleccione</option>@foreach($tipo_pago as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select><input required type="hidden" id="visibilidad_pago'+id+'" name="visibilidad_pago'+id+'" value="1"></td><td><input required type="date" class="input-number" value="{{date('Y-m-d')}}" name="fecha'+id+'" id="fecha'+id+'" style="width: 110px;"></td><td><select required id="tipo_tarjeta'+id+'" name="tipo_tarjeta'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach</select></td><td><input required type="text" name="numero'+id+'" id="numero'+id+'" style="width: 100px;" required></td><td><select required name="id_banco'+id+'" id="id_banco'+id+'" style="width: 175px;height:25px"><option value="">Seleccione...</option>@foreach($lista_banco as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input required style="text-align:center;" type="checkbox" name="fi'+id+'" id="fi'+id+'" onchange="revision_total('+id+')" value="0" ></td><td><input required type="text" id="valor_base'+id+'" name="valor_base'+id+'" style="width: 100px;"  value="0" onchange="revision_total('+id+')" onkeypress="return soloNumeros(event);"></td><td><input required type="text" id="total'+id+'" name="total'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'+id+',2);"></td><td><button type="button" onclick="eliminar_form_pag('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';

        document.getElementById('agregar_pago').appendChild(midiv_pago);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador_pago').value = id;

    });

    function revision_total(id){
        var fi = document.getElementById("fi"+id);
        var valor = $('#valor_base'+id).val();
        if(fi.checked == true){
            tipo = $('#id_tip_pago'+id).val();
            if(tipo == '4'){
                ntotal = valor *1.07;
            }else if(tipo == '6'){
                ntotal = valor *1.02;
            }else{
                ntotal = valor*1;
            }
            $('#total'+id).val(ntotal.toFixed(2));
            suma_total();
        }else{
            ntotal = valor*1;
            $('#total'+id).val(ntotal.toFixed(2));
            suma_total();
        }
        revision_total_pro(0);
    }
    function redondea_valor_base(e,id,val){
        return parseFloat(e.value).toFixed(val);
    }
    function suma_total_pro(){
        var contador = $('#contador_procedimiento').val();
        var sumador = 0;
        var copago = 0;

        for (var i = 0; i < contador; i++) {
            if($('#visibilidad_procedimiento'+i).val() == 1){
                sumador = sumador + parseFloat($('#total_procedimiento'+i).val());
                copago = copago+parseFloat($('#copago'+i).val());
            }
        }
        $('#total_pro').val(sumador.toFixed(2));
        $('#valor_copago').val(copago.toFixed(2));
    }
    function suma_total(){
        var contador = $('#contador_pago').val();
        var sumador = 0;
        var sumador_sin = 0;

        for (var i = 0; i < contador; i++) {
            if($('#visibilidad_pago'+i).val() == 1){
                sumador_sin = sumador_sin + parseFloat($('#valor_base'+i).val());
                sumador = sumador + parseFloat($('#total'+i).val());
            }
        }

        $('#total_sin_tarjeta').val(sumador_sin.toFixed(2));
        $('#total_final1').val(sumador.toFixed(2));
    }
    $('#btn_procedimiento').click(function(event){

            id= document.getElementById('contador_procedimiento').value;


            var midiv_pago = document.createElement("tr")
                midiv_pago.setAttribute("id","dato_procedimiento"+id);

            midiv_pago.innerHTML = '<td><input required type="hidden" id="visibilidad_procedimiento'+id+'" name="visibilidad_procedimiento'+id+'" value="1"><input type="text" name="procedimiento'+id+'" ></td><td><input required type="text" id="valor_pro'+id+'" name="valor_pro'+id+'" style="width: 100px;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td><td><input required type="text" value="0.00" name="copago'+id+'" id="copago'+id+'" style="width: 100px;" required></td> <td><input required type="text" id="desc'+id+'" name="desc'+id+'" style="width: 100px;"  value="0.00" onchange="revision_total_pro('+id+') " onkeypress="return soloNumeros(event);"></td> <td><input required style="text-align:center;" type="checkbox" name="iva'+id+'" id="iva'+id+'" onchange="revision_total_pro('+id+')" value="0" ></td><td><input required type="text" id="total_procedimiento'+id+'" name="total_procedimiento'+id+'" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'+id+',2);"></td><td><button type="button" onclick="eliminar_form_procedimiento('+id+')" class="btn btn-warning btn-margin">Eliminar </button></td>';

            document.getElementById('agregar_procedimiento').appendChild(midiv_pago);
            id = parseInt(id);
            id = id+1;
            document.getElementById('contador_procedimiento').value = id;
    });

    function revisar_componentes(e, id){
        metodo = $('#id_tip_pago'+id).val();
        if(metodo == 1){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', true);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', true);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 2){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 3){
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', true);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', true);
            revision_total(id);
        }else if(metodo == 4){
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }else if(metodo == 5){
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }else if(metodo == 6){
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#numero"+id).prop('disabled', false);
            $("#tipo_tarjeta"+id).prop('disabled', false);
            $("#id_banco"+id).prop('disabled', false);
            $("#fi"+id).prop('disabled', false);
            revision_total(id);
        }
    }
    function validar_copago() {
      // Get the checkbox
      var checkBox = document.getElementById("copago");
      // Get the output text
      var text = document.getElementById("dato_copago");

      // If the checkbox is checked, display the output text
      if (checkBox.checked == true){
        document.getElementById("div_copago2").style.display="block";
        //$('#valor_copago').val('0');
        text.style.display = "block";
      } else {
        $('#valor_copago').val('0');
        $('#numero_oda').val('0');
        document.getElementById("div_copago2").style.display="none";
        text.style.display = "none";
      }
    }

    //Elimina Registro de la Tabla Forma de Pago
    function eliminar_form_pag(valor)
    {
        var dato_pago1 = "dato_pago"+valor;
        var nombre_pago2 = 'visibilidad_pago'+valor;
        document.getElementById(dato_pago1).style.display='none';
        document.getElementById(nombre_pago2).value = 0;
        suma_total();

    }
    function obtener_fecha(){

        //obtenemos la fecha actual
        var now = new Date();
        var day =("0"+now.getDate()).slice(-2);
        var month=("0"+(now.getMonth()+1)).slice(-2);
        var today=now.getFullYear()+"-"+(month)+"-"+(day);
        $("#fecha").val(today);

    }

    function crear_factura(){
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
        //Pago
        var pago = formulario.pago.value;
        var caja_pago = formulario.caja.value;

        var msj = "";
        var msj2 = "";

        //Datos Generales

        if(id_emp == ""){

           msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }
       //alert(copago.checked);
        if(copago.checked==true){

            if(numero_oda =='0' || numero_oda ==''){
                msj = msj + "Por favor, Ingrese el número de Oda<br/>";
            }
        }

        //Paciente
        if(cedula == ""){
           msj += "Por favor,Ingrese la cedula del paciente<br/>";
        }
        if(nombre_paciente == ""){
           msj += "Por favor,Ingrese el nombre del paciente<br/>";
        }
        if(seguro_paciente == ""){
           msj += "Por favor,Seleccione el seguro paciente<br/>";
        }

        //Cliente
        if(tipo_identicacion == ""){
           msj += "Por favor,Selecione el Tipo de Identificaciòn<br/>";
        }
        if(ced_cliente == ""){
           msj += "Por favor,Ingrese la cedula del cliente<br/>";
        }
        if(raz_social == ""){
           msj += "Por favor,Ingrese la razon social<br/>";
        }
        if(ciud_cliente == ""){
           msj += "Por favor,Ingrese la ciudad del cliente<br/>";
        }

        if(dire_cliente == ""){
           msj += "Por favor,Ingrese la direccion cliente<br/>";
        }
        if(telf_cliente == ""){
           msj += "Por favor,Ingrese el telefono del cliente<br/>";
        }
        if(email_cliente == ""){
           msj += "Por favor,Ingrese el email del cliente<br/>";
        }


        //Pago
        if(pago == ""){
           msj += "Por favor,Ingrese el pago\n";
        }
        if(caja_pago == ""){
           msj += "Por favor,Ingrese la caja a la cual se va a pagar<br/>";
        }

        var i;var max=document.getElementById('agregar_pago').rows.length;
        for (i = 0; i < max; i++) {
          
          var tipo_pago = document.getElementById('id_tip_pago'+i).value;
          var numero = document.getElementById('numero'+i).value;
          var id_banco = document.getElementById('id_banco'+i).value;
          var tipo_tarjeta = document.getElementById('tipo_tarjeta'+i).value;
          
          if(tipo_pago=='2'){

            if(numero==""){
                msj += "Por favor,Ingrese el número del cheque<br/>";
            }
            if(id_banco==""){
                msj += "Por favor,Seleccione el Banco<br/>";
            }
          }
          
          if(tipo_pago=='4'){
            if(tipo_tarjeta==""){
                msj += "Por favor,Ingrese el Tipo de tarjeta<br/>";
            }

            if(id_banco==""){
                msj += "Por favor,Seleccione el Banco<br/>";
            }
          }
        
        
        }

        if(msj != ""){
            $('#crear_recibo').button('reset');
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        if(msj2 != ""){
            $('#crear_recibo').button('reset');
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj2
                });
            return false;
        }

        var fecha = document.getElementById('fecha').value;

        var unix =  Math.round(new Date(fecha).getTime()/1000);


        $.ajax({
            type: 'post',
            url:"{{route('factura.actualizar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data){
                console.log(data);
                $("#ride").attr("href", );
                //$("#ride").click();
                window.open("{{asset('/comprobante/orden/venta')}}/"+data.id_orden, '_blank ');
                window.location.href = "{{asset('/agenda/calendario/')}}/{{$orden->agenda->id_doctor1}}/"+unix;
                $('#crear_recibo').button('reset');
            },
            error: function(data){
                console.log(data);

            }
        });
    }

    function seguro(){
        var seguro =  $('#id_seguro').val();
        $.ajax({
            type: 'get',
            url:"{{asset('contable/facturacion/verificar/seguro/')}}"+'/'+seguro,
            datatype: 'html',
            success: function(data){
                if(data==1 ){
                    document.getElementById("div_copago").style.display="block";

                }else{
                    document.getElementById("div_copago").style.display="none";
                    document.getElementById("div_copago2").style.display="none";
                    document.getElementById("dato_copago").style.display="none";
                    //$('#valor_copago').val('');
                    //$('#valor_copago2').val('');
                    $("#copago").prop("checked", false);
                }
                pagar();

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function pagar(){
        var seguro =  $('#id_seguro').val();
        var doctor =  $('#id_doctor').val();
        $.ajax({
            type: 'get',
            url:"{{asset('contable/facturacion/verificar/pago/')}}"+'/'+seguro+'/'+doctor,
            datatype: 'html',
            data: $("#obs_id").serialize(),
            success: function(data){
                $('#pago').val(data.trim());
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    $("#cedula").autocomplete({
        source: function( request, response ) {
          $.ajax( {
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url: "{{route('facturacion.buscar_cliente')}}",
            dataType: "json",
            data: {
              term: request.term
            },
            success: function( data ) {
              response(data);
            }
          } );
        },
        minLength: 3,
    });
    function buscar(){

        var cedula_1 = $('#cedula').val();
        var pasaporte = parseInt($('#tipo_identificacion').val());
        var alerta = 0;

        if(cedula_1.length < 10){
            alert('La Cantidad de Digitos no es Correcta');
        }

        var cedula = validarCedula($('#cedula').val());
        if(cedula == false && pasaporte !=  6 && pasaporte !=  8){
            $('#cedula').val('');
            $('#razon_social').val('');
            $('#ciudad').val('');
            $('#direccion').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#caja').val('');

        }else{

            $.ajax({
                type: 'post',
                url:"{{route('facturacion.cliente')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                datatype: 'json',
                data: $("#cedula"),
                success: function(data){
                    if(data.value != 'no'){
                        $('#razon_social').val(data.nombre);
                        $('#ciudad').val(data.ciudad);
                        $('#direccion').val(data.direccion);
                        $('#telefono').val(data.telefono);
                        $('#email').val(data.email);
                        $('#caja').val(data.caja);
                    }
                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }
            });

        }

    }
</script>
@endsection
