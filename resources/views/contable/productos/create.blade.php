@extends('contable.productos.base')
@section('action-content')

<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 300px;
        width: 25%;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000px;
        font-size: 11px;
        float: left;
        display: none;
        min-width: 160px;
        _width: 140px;
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

<div class="modal fade" id="detalle_paquet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 50%;">
        </div>
    </div>
</div>

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        window.history.back();
    }
</script>

<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h5><b>FICHERO DE PRODUCTO O SERVICIO</h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('productos_servicios_store') }}">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group col-md-12{{ $errors->has('codigo') ? ' has-error' : '' }}">
                                <label for="codigo" class="col-md-4 control-label">Codigo</label>
                                <div class="col-md-4">
                                    <input id="codigo" type="text" class="form-control" name="codigo" value="{{old('codigo')}}" style="text-transform:uppercase;" maxlength="25" required autofocus>
                                    @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                <label for="nombre" class="col-md-4 control-label">Nombre</label>
                                <div class="col-md-8">
                                    <input id="nombre" type="text" class="form-control" name="nombre" value="{{old('nombre')}}" style="text-transform:uppercase;" required autofocus>
                                    @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="cod_barra" class="col-md-4 control-label">Codigo de Barra</label>
                                <div class="col-md-8">
                                    <input id="cod_barra" type="text" class="form-control" name="cod_barra" value="{{ old('cod_barra') }}" style="text-transform:uppercase;" maxlength="25" autofocus>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                                <div class="col-md-8">
                                    <textarea rows="1" class="form-control" id="descripcion" name="descripcion"></textarea>
                                </div>
                            </div>
                            <!--<div class="form-group col-md-12">
                                <label for="clase" class="col-md-4 control-label" >Clase</label>
                                <div class="col-md-4">
                                    <select id="clase" name="clase" class="form-control" >
                                        <option  value="">Seleccione...</option>
                                        <option  value="1">MERCADERIA</option>
                                        <option  value="2">MATERIALES</option>
                                    </select>
                                </div>
                            </div>-->
                            <div class="form-group col-md-12">
                                <label for="grupo" class="col-md-4 control-label">Grupo</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_grupo" onchange="cambiar_cuentas()" name="grupo" id="grupo" style="width: 100%;">
                                        <option value="">Seleccione...</option>
                                        <option value="1">Insumos</option>
                                        <option value="2">Medicamentos</option>
                                        <option value="4">Procedimientos</option>
                                        <option value="3">Servicios</option>
                                        <option value="5">Otros</option>
                                        <option value="6">Honorario</option>
                                        <option value="7">Equipo</option>
                                        <option value="8">Honorario Anestesiologico</option>
                                        <option value="9">Membresias</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="proveedor" class="col-md-4 control-label">Proveedor</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="proveedor" id="proveedor" style="width: 100%;">
                                        <option value="">Seleccione...</option>
                                        @foreach($proveedor as $value)
                                        <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="cta_gastos" class="col-md-4 control-label">Cta. Merc./ Gasto</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="cta_gastos" id="cta_gastos" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="cta_ventas" class="col-md-4 control-label">Cta. ventas</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="cta_ventas" id="cta_ventas" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="cta_costos" class="col-md-4 control-label">Cta. Costos</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="cta_costos" id="cta_costos" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="cta_devolucion" class="col-md-4 control-label">Cta. Devolucion</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="cta_devolucion" id="cta_devolucion" style="width: 100%;" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($cuentas as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <?php
                            if (isset($empresa->union_producto) && $empresa->union_producto == 1) {
                            ?>
                                <div class="form-group col-md-12">
                                    <label for="tipo_producto" class="col-md-4 control-label">Tipo de Producto</label>
                                    <input type="hidden" id="tipo_producto" name="tipo_producto" value="0" />
                                    <div class="col-md-8">
                                        <input type="text" value="Servicio" class="form-control" readonly />
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="form-group col-md-12">
                                    <label for="tipo_producto" class="col-md-4 control-label">Tipo de Producto</label>
                                    <div class="col-md-8">
                                        <select class="form-control select2_cuentas" name="tipo_producto" id="tipo_producto" style="width: 100%;" required>
                                            <option value="">Seleccione...</option>
                                            <option value="1">Producto</option>
                                            <option value="0">Servicio</option>
                                        </select>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                                <div class="form-group col-md-6">
                                    <label style="padding-left: 15px"> Registro de No. de series &nbsp; <input type="checkbox" id="reg_serie" name="reg_serie" value="1"> </label> <br>
                                    <label style="padding-left: 15px"> Permitir Modificar Precio &nbsp; <input type="checkbox" id="mod_precio" name="mod_precio" value="1"> </label> <br>
                                    <label style="padding-left: 15px"> Estado &nbsp; <input type="checkbox" id="estado" name="estado" value="1" checked> </label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label style="padding-left: 15px"> Permitir Modificar Descuento &nbsp; <input type="checkbox" id="mod_desc" name="mod_desc" value="1"> </label>
                                    <label style="padding-left: 15px"> Aplica IVA &nbsp; <input type="checkbox" id="iva" name="iva" value="1"> </label><br />
                                </div>
                            </div>
                            <div class="col-md-12" style="color: red; padding-left: 0px"><b>Descuentos Maximos</b><br><br></div>
                            <div class="form-group col-md-12">
                                <label for="descuento" class="col-md-4 control-label">% Descuento</label>
                                <div class="col-md-2">
                                    <input id="descuento" type="text" maxlength="3" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="descuento" value="{{ old('descuento') }}" autofocus>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="financiero" class="col-md-4 control-label">% Financiero</label>
                                <div class="col-md-2">
                                    <input id="financiero" type="text" maxlength="3" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="financiero" value="{{ old('financiero') }}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-12" style="color: red; padding-left: 0px"><b>Productos</b><br><br></div>
                            <input name='contador_prod' id='contador_prod' type='hidden' value="0">
                            <div class="col-md-12 table-responsive">
                                <table id="example2" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="40%" tabindex="0">Producto</th>
                                            <th width="20%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_td">
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
                            <br />
                            <div class="col-md-12" style="color: red; padding-left:0px;margin-top:15px"><b>Equipo</b><br><br></div>
                            <input name='contador_insumo' id='contador_insumo' type='hidden' value="0">
                            <div class="col-md-12 table-responsive">
                                <table id="equipo" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="40%" tabindex="0">Equipo</th>
                                            <th width="20%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fila-fija">
                                            <td>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12" style="color: red; padding-left:0px;margin-top:15px"><b>Procedimientos</b><br><br></div>
                            <input name='contador_procedimientos' id='contador_procedimientos' type='hidden' value="0">
                            <div class="col-md-12 table-responsive">
                                <table id="procedimientos" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="40%" tabindex="0">Procedimientos</th>
                                            <th width="20%" tabindex="0">Seguro</th>
                                            <th width="20%" tabindex="0">Honorarios</th>
                                            <th width="20%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_procedimientos">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fila-fija">
                                            <td>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12" style="color: red"><b>Datos Generales</b><br><br></div>
                            <div class="form-group col-md-12">
                                <label for="marca" class="col-md-2 control-label">Marca</label>
                                <div class="col-md-4">
                                    <select id="marca" name="marca" class="form-control">
                                        <option value="">Seleccione...</option>
                                        @foreach($marca as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="modelo" class="col-md-2 control-label">Modelo</label>
                                <div class="col-md-4">
                                    <input id="modelo" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="modelo" value="{{ old('modelo') }}" maxlength="25" autofocus>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="stock_minimo" class="col-md-2 control-label">Stock Minimo</label>
                                <div class="col-md-4">
                                    <input id="stock_minimo" type="text" maxlength="3" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="stock_minimo" value="{{ old('stock_minimo') }}" autofocus>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="fecha_expiracion" class="col-md-2 control-label">Fecha de Expiracion</label>
                                <div class="col-md-4">
                                    <input id="fecha_expiracion" type="date" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="fecha_expiracion" value="{{ old('fecha_expiracion') }}" autofocus>
                                </div>
                            </div>

                            <div class="col-md-12" style="color: red"><b>Comercializacion</b><br><br></div>

                            <div class="form-group col-md-12">
                                <label for="impuesto_iva_compras" class="col-md-4 control-label">Impuesto IVA Compras</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="impuesto_iva_compras" id="impuesto_iva_compras" style="width: 100%;">
                                        <option value="">Seleccione...</option>
                                        @foreach($impuestos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="impuesto_iva_ventas" class="col-md-4 control-label">Impuesto IVA Ventas</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_cuentas" name="impuesto_iva_ventas" id="impuesto_iva_ventas" style="width: 100%;">
                                        <option value="">Seleccione...</option>
                                        @foreach($impuestos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="impuesto_servicio" class="col-md-4 control-label">Impuesto Servicio</label>
                                <div class="col-md-8">
                                    <select id="impuesto_servicio" name="impuesto_servicio" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option value="1">12%</option>
                                        <option value="2">14%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="impuesto_ice" class="col-md-4 control-label">Impuesto ICE</label>
                                <div class="col-md-8">
                                    <select id="impuesto_ice" name="impuesto_ice" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option value="1">12%</option>
                                        <option value="2">14%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="clasificacion_impuesto_ice" class="col-md-4 control-label">Clasificacion de Impuestos ICE</label>
                                <div class="col-md-8">
                                    <select id="clasificacion_impuesto_ice" name="clasificacion_impuesto_ice" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option value="1">12%</option>
                                        <option value="2">14%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12" style="color: red; padding-left: 0px"><b>Costos</b><br><br></div>
                                <div class="form-group col-md-12">
                                    <label for="promedio" class="col-md-4 control-label">Promedio</label>
                                    <div class="col-md-8">
                                        <input id="promedio" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="promedio" value="0" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="reposicion" class="col-md-4 control-label">Reposicion</label>
                                    <div class="col-md-8">
                                        <input id="reposicion" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="reposicion" value="0" maxlength="25" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="lista" class="col-md-4 control-label">Lista</label>
                                    <div class="col-md-8">
                                        <input id="lista" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="lista" value="0" maxlength="25" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="ultima_compra" class="col-md-4 control-label">Ultima Compra</label>
                                    <div class="col-md-8">
                                        <input id="ultima_compra" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="ultima_compra" value="0" maxlength="25" autofocus>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!--div class="col-md-12" style="color: red; padding-left: 0px"><b>Precios</b><br><br></div>

                                <div class="form-group col-md-12">
                                    <label for="precio1" class="col-md-4 control-label" >Precio1</label>
                                    <div class="col-md-8">
                                        <input id="precio1" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio1" value="0" autofocus required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio2" class="col-md-4 control-label" >Precio2</label>
                                    <div class="col-md-8">
                                        <input id="precio2" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio2" value="0" autofocus required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio3" class="col-md-4 control-label" >Precio3</label>
                                    <div class="col-md-8">
                                        <input id="precio3" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio3" value="0" autofocus >
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio4" class="col-md-4 control-label" >Precio4</label>
                                    <div class="col-md-8">
                                        <input id="precio4" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio4" value="0" autofocus >
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="promocion" class="col-md-4 control-label" >Promocion</label>
                                    <div class="col-md-8">
                                        <input id="promocion" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="promocion" value="0" autofocus >
                                    </div>
                                </div>-->
                                <!--<h4>Detalle de Precios</h4>-->

                                <!--<div class="col-md-12">
                                    
                                    <div class="col-md-6">
                                     <label for="nivel" class="col-md-2 control-label">PvP</label>
                                     <input class="form-control input-sm" type="text" value="PvP" id="nivel" name="nivel" readonly>
                                    </div>

                                    <div class="col-md-6">
                                     <label for="precio" class="col-md-2 control-label" >Precio</label>
                                     <input class="form-control input-sm" type="text" id="precio" name="precio" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off" required>
                                    </div>

                                </div>-->
                                <h4>Detalle de Precios</h4>
                                <div class="col-md-12 table-responsive">
                                    <input type="hidden" name="contador" id="contador" value="0">
                                    <input type="hidden" name="total" id="total" value="0">
                                    <table id="example2" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead class="thead-dark">
                                            <tr class='well-darks'>
                                                <th width="40%" class="" tabindex="0">PvP/Nivel</th>
                                                <th width="40%" class="" tabindex="0">Precio</th>
                                                <th width="20%" class="" tabindex="0">
                                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="agregar_cuentas">
                                            <tr class="wells" id="mifila">
                                                <td>
                                                    <input class="form-control input-sm" type="text" style="width: 50%;height:20px;" value="" name="nivel[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control input-sm" type="text" style="width: 50%;height:20px;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" required>
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
                            </div>
                            <div class="col-md-12" style="color: red; padding-left: 0px"><b>Detalle Paquete</b><br><br></div>
                            <input name='contador_paquetes' id='contador_paquetes' type='hidden' value="0">
                            <input name="cont_paq_temp" id="cont_paq_temp" type="hidden" value="0">

                            <div class="col-md-12 table-responsive">
                                <table id="paquetes" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="20%" tabindex="0">Cant.</th>
                                            <th width="40%" tabindex="0">Paquete</th>
                                            <th width="20%" tabindex="0">PvP</th>
                                            <th width="20%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_paquetes">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle_paquete">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success btn-gray">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    $("#codigo").blur(function() {
        //alert("Valida Codigo");
        buscarCodigoProducto();
    });

    function buscarCodigoProducto() {
        var codig = $("#codigo").val();
        $.ajax({
            type: 'post',
            url: "{{route('buscar_codigo.producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: {
                'cod_prod': codig
            },
            success: function(data) {
                console.log(data.existe);
                if (data.existe != null) {

                    swal("Error!", "El codigo del Producto ya se encuentra registrado");
                    $('#codigo').val("");

                }
            },
            error: function(data) {
                console.log(data);
            }
        });


    }
    //Retorna con dos Decimales
    function checkformat(entry) {
        var test = entry.value;
        if (!isNaN(test)) {
            entry.value = parseFloat(entry.value).toFixed(2);
        }
        if (isNaN(entry.value) == true) {
            entry.value = '0.00';
        }
        if (test < 0) {
            entry.value = '0.00';
        }
    }

    function buscar_prod() {
        $.ajax({
            type: 'post',
            url: "{{route('contable_find_insumo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            type: 'POST',
            datatype: 'json',
            data: $("#codigo_prod_0"),
            success: function(data) {
                console.log(data);
                if (data != 'no') {
                    // $('#codigo').val(data[0].codigo);
                    $('#nombre').val(data[0].nombre);
                    $('#descripcion').val(data[0].descripcion)
                    $('#marca').val(data[0].id_marca)
                    $('#stock_minimo').val(data[0].minimo)
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    };
    var fila = $("#mifila").html();
    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
    });

    function cambiar_cuentas() {
        var grupo = parseInt($("#grupo").val());
        switch (grupo) {
            case 1:
                $('#cta_gastos').val('1.01.03.01.02').trigger('change');
                $('#cta_ventas').val('4.1.01.02').trigger('change');
                $('#cta_costos').val('5.1.01.05').trigger('change');
                $('#cta_devolucion').val('4.1.07.01').trigger('change');
                $('#impuesto_iva_compras').val('1.01.05.01.01').trigger('change');
                $('#impuesto_iva_ventas').val('2.01.07.01.01').trigger('change');
                $('#impuesto_servicio').val('1').trigger('change');

                break;
            case 2:
                $('#cta_gastos').val('1.01.03.01.02').trigger('change');
                $('#cta_ventas').val('4.1.01.02').trigger('change');
                $('#cta_costos').val('5.1.01.05').trigger('change');
                $('#cta_devolucion').val('4.1.07.01').trigger('change');
                $('#impuesto_iva_compras').val('1.01.05.01.01').trigger('change');
                $('#impuesto_iva_ventas').val('2.01.07.01.01').trigger('change');
                break;
            case 3:
                $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                $('#cta_ventas').val('4.1.01.02').trigger('change');
                $('#cta_costos').val('5.1.01.07').trigger('change');
                $('#cta_devolucion').val('4.1.07.01').trigger('change');
                $('#impuesto_iva_compras').val('1.01.01.1.01').trigger('change');
                $('#impuesto_iva_ventas').val('1.01.01.1.01').trigger('change');
                $('#impuesto_servicio').val('1').trigger('change');
                $('#impuesto_ice').val('1').trigger('change');
                $('#clasificacion_impuesto_ice').val('1').trigger('change');

                break;
            case 4:
                $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                $('#cta_ventas').val('4.1.01.02').trigger('change');
                $('#cta_costos').val('5.1.01.07').trigger('change');
                $('#cta_devolucion').val('4.1.07.01').trigger('change');

                break;
            case 5:
                $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                $('#cta_ventas').val('4.1.01.02').trigger('change');
                $('#cta_costos').val('5.1.01.07').trigger('change');
                $('#cta_devolucion').val('4.1.07.01').trigger('change');
                $('#impuesto_iva_compras').val('1.01.01.1.01').trigger('change');
                $('#impuesto_iva_ventas').val('1.01.01.1.01').trigger('change');
                $('#impuesto_servicio').val('1').trigger('change');
                $('#impuesto_ice').val('1').trigger('change');
                $('#clasificacion_impuesto_ice').val('1').trigger('change');

                break;
        }
    }

    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        //rowk.className="well";
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    $('.agregar_td').on('click', function() {
        agregar_td();
    });
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
    });

    function agregar_td() {
        var id = document.getElementById('contador_prod').value;
        var tr = '<tr class="fila-fija"><td><input type="hidden" style="width:20%;margin-bottom:2px" readonly name="id_insumo[]" class="id_insumo"/><input onchange="agregar_id(this)" class="buscador form-control"  name="codigo_producto[]" id="codigo_producto" style="width:94%;height:20px"/></td><td class="eliminar"><button  type="button"  class="btn btn-danger btn-gray " ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>'
        /*
        var id = document.getElementById('contador_prod').value;
        var tr= '<tr class="fila-fija"><td><select class="form-control select2_cuentas"  name="contador_prod_'+id+'"  id="contador_prod_'+id+'" onchange="buscar_prod()" style="width: 100%;"><option value="">Seleccione...</option> @foreach($productos_insumos as $value) <option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach  </select></td><td class="eliminar"><button  type="button"  class="btn btn-danger btn-gray " ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>'
        */
        /*
        var tr='<tr>'+'<td margin:0px><input required   style="height:25px;width:250px;rgba(0, 0, 0, 0);" name="codigo_prod_'+id+'"  id="codigo_prod_'+id+'" onchange="buscar_prod()" placeholder="Buscar Insumo..."/></td>'+'<td class="eliminar" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
          '</tr>'+*/
        $('#example2').append(tr);
        var variable = 1;
        var sum = parseInt(id) + parseInt(variable);
        document.getElementById("contador_prod").value = parseInt(sum);
        $(".buscador").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable_find_producto')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

    };
    $(document).on("click", ".eliminar", function() {
        var parent = $(this).parents().get(0);
        $(parent).remove();
    });

    $('.agregar').on('click', function() {
        agregar();
    });

    function agregar() {
        var id = document.getElementById('contador_insumo').value;
        var tr = '<tr>' + '<td><input type="hidden" name="id_equipo[]" class="id_equipo"/><input required name="insumos_producto[]" id="insumos_producto" onchange="agregar_equipo(this)" class=" buscador_insumos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' + '<td class="esconder" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
        '</tr>' +
        $('#equipo').append(tr);
        var variable = 1;
        var sum = parseInt(id) + parseInt(variable);
        document.getElementById("contador_insumo").value = parseInt(sum);

        $(".buscador_insumos").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable_find_insumo')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });
    };
    $(document).on("click", ".esconder", function() {
        var parent = $(this).parents().get(0);
        $(parent).remove();
    });

    function agregar_id(e) {
        $.ajax({
            type: 'post',
            url: "{{route('contable_find_id')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                if (data[0] != "") {
                    $(e).parent().parent().find('.id_insumo').val(data[0].id);
                }
                if (data.value != 'no resultados') {
                    console.log(data);
                } else {}
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function agregar_equipo(e) {
        $.ajax({
            type: 'post',
            url: "{{route('contable_find_id_equipo')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                if (data[0] != "") {
                    $(e).parent().parent().find('.id_equipo').val(data[0].id);
                }
                if (data.value != 'no resultados') {
                    console.log(data);
                } else {}
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    $('.agregar_procedimientos').on('click', function() {
        agregar_procedimientos();
    });

    function agregar_procedimientos() {
        var id = document.getElementById('contador_procedimientos').value;
        var tr = '<tr>' + '<td><input type="hidden" name="id_procedimiento[]" class="id_procedimiento" /><input required name="proce_id[]" id="proce_id" onchange="agregar_procedimiento(this)" class=" buscador_procedimientos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' + '<td><select class="form-control select2" style="height:25px;width:94%;" name="seguro_procedimiento[]" > <option value="">Seleccione ...</option> @foreach($seguros as $value) <option value="{{$value->id}}">{{$value->nombre}}</option> @endforeach </select></td>' + '<td><input type="number" style="height:25px;width:94%;" name="precio_procedimiento[]" placeholder="0.00" class="form-control"></td>' + '<td class="ocultar" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
        '</tr>' +
        $('#procedimientos').append(tr);
        var variable = 1;
        var sum = parseInt(id) + parseInt(variable);
        document.getElementById("contador_procedimientos").value = parseInt(sum);
        $(".buscador_procedimientos").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable_find_procedimientos')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

    };
    $(document).on("click", ".ocultar", function() {
        var parent = $(this).parents().get(0);
        $(parent).remove();
    });

    function agregar_procedimiento(e) {
        $.ajax({
            type: 'post',
            url: "{{route('contable_find_id_procedimiento')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                if (data[0] != "") {
                    $(e).parent().parent().find('.id_procedimiento').val(data[0].id);
                }
                if (data.value != 'no resultados') {
                    console.log(data);
                } else {}
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
    $('.agregar_paquetes').on('click', function() {
        agregar_paquetes();
    });

    function agregar_paquetes() {
        id = document.getElementById('contador_paquetes').value;
        id_temp_paq = document.getElementById('cont_paq_temp').value;
        //Creamos la Tabla Temporal
        var midiv_item = document.createElement("tr")
        midiv_item.setAttribute("id", "dato" + id);
        midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_paquete' + id + '" name="visibilidad_paquete' + id + '" value="1"><input type="number" name="paque_cant' + id + '" id="paque_cant' + id + '" class="form-control" style="height:25px;width:75%;"></td><td><input type="hidden" name="id_paquete' + id + '" class="id_paquete"><input type="hidden" name="iva_prod' + id + '" class="iva_prod"><input required name="paque_id' + id + '" id="paque_id' + id + '" onchange="agregar_valores(this); buscar_precio(this,' + id + ')" class=" buscador_paquetes form-control"  style="height:25px;width:90%;"></td><td><input type="text" name="precio_paq' + id + '" id="precio_paq' + id + '" class="form-control" style="height:25px;width:75%;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off"></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_paquete(' + id + ')" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
        document.getElementById('detalle_paquete').appendChild(midiv_item);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador_paquetes').value = id;
        $(".buscador_paquetes").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable_find_paquete')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'posicion_paq': id_temp_paq
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                //obtener_precio_producto(ui.item.codig_product,ui.item.pos_paq);
                id_temp_paq = parseInt(id_temp_paq);
                id_temp_paq = id_temp_paq + 1;
                document.getElementById('cont_paq_temp').value = id_temp_paq;
            },
            minLength: 2,
        });
    }

    function eliminar_item_paquete(valor) {
        var dato_item1 = "dato" + valor;
        var dato_item2 = 'visibilidad_paquete' + valor;
        document.getElementById(dato_item1).style.display = 'none';
        document.getElementById(dato_item2).value = 0;
    }
    //Obtener Precio del Producto
    function obtener_precio_producto(cod_prod, posi) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('contable_precio_prod.codigo')}}",
            datatype: 'json',
            data: {
                'cod_prod': cod_prod,
                'posicion': posi
            },
            success: function(data) {
                //console.log(data);
                //response(data);
                if (data.no_data != 'no') {
                    //alert(data.prec_prod)
                    //alert(data.posic);     
                    $('#precio_paq' + data.posic).val(data.prec_prod);
                }

            },
            error: function(data) {
                console.log(data);
            }
        });

    }

    $(document).on("click", ".remover", function() {
        var parent = $(this).parents().get(0);
        $(parent).remove();
    });

    function agregar_valores(e) {

        $.ajax({
            type: 'post',
            url: "{{route('contable_find_id_paquete')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {

                if (data[0] != "") {

                    $(e).parent().parent().find('.id_paquete').val(data[0].id);
                    $(e).parent().parent().find('.iva_prod').val(data[0].iva);

                }

            },
            error: function(data) {
                console.log(data);
            }
        });

    }

    function buscar_precio(e, id) {
        $.ajax({
            type: 'post',
            url: "{{route('productos_servicios.buscar_precio')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                document.getElementById('precio_paq' + id).value = data[0].precio;
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>
@endsection