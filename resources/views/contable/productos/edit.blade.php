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

    .alerta_guardado {
        position: fixed;
        z-index: 9999;
        bottom: 500px;
        left: 990px;
    }
</style>

<div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    Actualizado Correctamente
</div>


<!--Modal Actualiza Valor Total de Paquete Cuando es Seguro Particular -->
<div class="modal fade" id="val_tot_paquete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="tarifario_paquete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="producto_tarifario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>
</div>

<div class="modal fade" id="edit_prod_tar_paq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">

    <div class="box " style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h5><b>EDITAR PRODUCTO O SERVICIO</b></h5>
            </div>
            <!--div class="col-md-1" style="text-align: right;">
                <button onclick="limpiar();" class="btn btn-success " >
                    &nbsp;&nbsp;Nuevo
                </button>
            </div-->
            <div class="col-md-1" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-success btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" id="edit_prod">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $productos->codigo }}">
                    <input type="hidden" name="id_prod" id="id_prod" value="{{$productos->id}}">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <label for="codigo" class="col-md-4 control-label">Codigo</label>
                                    <div class="col-md-4">
                                        <input id="codigo" type="text" class="form-control" name="codigo" value="{{$productos->codigo}}" maxlength="25" required autofocus readonly>

                                    </div>
                                    <!--<div class="col-md-3" >
                                    <button class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">&nbsp;&nbsp;Buscar
                                    </button>
                                </div>-->
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="nombre" class="col-md-4 control-label">Nombre</label>
                                    <div class="col-md-8">
                                        <input id="nombre" type="text" class="form-control" name="nombre" value="{{$productos->nombre}}" style="text-transform:uppercase;" required autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cod_barra" class="col-md-4 control-label">Codigo de Barra</label>
                                    <div class="col-md-8">
                                        <input id="cod_barra" type="text" class="form-control" name="cod_barra" value="{{$productos->codigo_barra}}" style="text-transform:uppercase;" maxlength="25" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                                    <div class="col-md-8">
                                        <textarea rows="1" class="form-control" id="descripcion" name="descripcion">{{$productos->descripcion}}</textarea>
                                    </div>
                                </div>

                                <!--<div class="form-group col-md-12">
                                <label for="clase" class="col-md-4 control-label" >Clase</label>
                                <div class="col-md-4">
                                    <select id="clase" name="clase" class="form-control" >
                                        <option  value="">Seleccione...</option>
                                        <option  {{ $productos->clase == 'contado' ? 'selected' : ''}} value="contado">CONTADO</option>
                                        <option  {{ $productos->clase == 'efectivo' ? 'selected' : ''}} value="efectivo">EFECTIVO</option>
                                    </select>
                                </div>
                            </div>-->

                                <div class="form-group col-md-12">
                                    <label for="grupo" class="col-md-4 control-label">Grupo</label>
                                    <div class="col-md-8">
                                        <select id="grupo" name="grupo" onchange="seleccionar_cuentas()" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option {{ $productos->grupo == '1' ? 'selected' : ''}} value="1">Insumos</option>
                                            <option {{ $productos->grupo == '2' ? 'selected' : ''}} value="2">Medicamentos</option>
                                            <option {{ $productos->grupo == '3' ? 'selected' : ''}} value="3">Servicios</option>
                                            <option {{ $productos->grupo == '4' ? 'selected' : ''}} value="4">Procedimientos</option>
                                            <option {{ $productos->grupo == '5' ? 'selected' : ''}} value="5">Otros</option>
                                            <option {{ $productos->grupo == '6' ? 'selected' : ''}} value="6">Honorario Medico</option>
                                            <option {{ $productos->grupo == '8' ? 'selected' : ''}} value="8">Honorario Anestesiologico</option>
                                            <option {{ $productos->grupo == '7' ? 'selected' : ''}} value="7">Equipo</option>
                                            <option {{ $productos->grupo == '9' ? 'selected' : ''}} value="9">Membresias</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="proveedor" class="col-md-4 control-label">Proveedor</label>
                                    <div class="col-md-8">
                                        <select id="proveedor1" name="proveedor1" class="form-control select2_cuentas">
                                            <option value="">Seleccione...</option>
                                            @foreach($proveedor as $value)
                                            <option {{ $productos->proveedor == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cta_gastos" class="col-md-4 control-label">Cta. Merc./ Gasto</label>
                                    <div class="col-md-8">
                                        <select id="cta_gastos" name="cta_gastos" class="form-control select2_cuentas" style="width: 100%;" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($cuentas as $value)
                                            <option {{$productos->cta_gastos == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cta_ventas" class="col-md-4 control-label">Cta. ventas</label>
                                    <div class="col-md-8">
                                        <select id="cta_ventas" name="cta_ventas" class="form-control select2_cuentas" style="width: 100%;" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($cuentas as $value)
                                            <option {{$productos->cta_ventas == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cta_costos" class="col-md-4 control-label">Cta. Costos</label>
                                    <div class="col-md-8">
                                        <select id="cta_costos" name="cta_costos" class="form-control select2_cuentas" style="width: 100%;" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($cuentas as $value)
                                            <option {{$productos->cta_costos == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="cta_devolucion" class="col-md-4 control-label">Cta. Devolucion</label>
                                    <div class="col-md-8">
                                        <select id="cta_devolucion" name="cta_devolucion" class="form-control select2_cuentas" style="width: 100%;" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($cuentas as $value)
                                            <option {{$productos->cta_devolucion == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if (isset($empresa->union_producto) && $empresa->union_producto == 1) {
                                    $tipoValue = 'Producto';
                                    if ($productos->tipo_producto == 0) {
                                        $tipoValue = 'Servicio';
                                    }
                                ?>
                                    <div class="form-group col-md-12">
                                        <label for="tipo_producto" class="col-md-4 control-label">Tipo de Producto</label>
                                        <input type="hidden" id="tipo_producto" name="tipo_producto" value="<?= $productos->tipo_producto; ?>" />
                                        <div class="col-md-8">
                                            <input type="text" value="<?= $tipoValue; ?>" class="form-control" readonly />
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
                                                <option value="1" {{$productos->tipo_producto == 1 ? 'selected' : ''}}>Producto</option>
                                                <option value="0" {{$productos->tipo_producto == 0 ? 'selected' : ''}}>Servicio</option>
                                            </select>
                                        </div>
                                    </div>

                                <?php
                                }
                                ?>

                                <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                                    <div class="form-group col-md-6">
                                        <label style="padding-left: 15px"> Registro de No. de series &nbsp; <input type="checkbox" id="reg_serie" name="reg_serie" value="1" {{ $productos->reg_serie == '1' ? 'checked' : ''}}> </label> <br>

                                        <label style="padding-left: 15px"> Permitir Modificar Precio &nbsp; <input type="checkbox" id="mod_precio" name="mod_precio" value="1" {{ $productos->mod_precio == '1' ? 'checked' : ''}}> </label> <br>

                                        <label style="padding-left: 15px"> Estado &nbsp; <input type="checkbox" id="estado" name="estado" value="1" {{ $productos->estado_tabla == '1' ? 'checked' : ''}}> </label> <br>

                                        <!--<label for="cta_ventas" class="col-md-4 control-label" >Estado</label>
                                    <div class="col-md-6">
                                        <select id="estado" name="estado" class="form-control" >
                                            <option  {{ $productos->cta_ventas == '1' ? 'selected' : ''}} value="1">Activo</option>
                                            <option  {{ $productos->cta_ventas == '0' ? 'selected' : ''}} value="0">Inactivo</option>
                                        </select>
                                    </div>-->
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label style="padding-left: 15px"> Permitir Modificar Descuento &nbsp; <input type="checkbox" id="mod_desc" name="mod_desc" value="1" {{ $productos->mod_desc == '1' ? 'checked' : ''}}> </label>

                                        <label style="padding-left: 15px"> Aplica IVA &nbsp; <input type="checkbox" id="iva" name="iva" {{ $productos->iva == '1' ? 'checked' : ''}}> </label>
                                    </div>
                                </div>

                                <div class="col-md-12" style="color: red; padding-left: 0px"><b>Descuentos Maximos</b><br><br></div>

                                <div class="form-group col-md-12">
                                    <label for="descuento" class="col-md-5 control-label">% Descuento</label>
                                    <div class="col-md-2">
                                        <input id="descuento" type="text" maxlength="3" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="descuento" value="{{$productos->descuento}}" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="financiero" class="col-md-5 control-label">% Financiero</label>
                                    <div class="col-md-2">
                                        <input id="financiero" type="text" maxlength="3" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="financiero" value="{{$productos->financiero}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-12" style="color: red; padding-left: 0px"><b>Productos</b><br><br></div>
                                <input name='contador_prod' id='contador_prod' type='hidden' value="0">
                                <div class="col-md-12 table-responsive" style=" height: 400px; overflow-y: scroll;">
                                    <table id="examples" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
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
                                            <!--Anthony cambios 7 de junio -->
                                            @foreach($insumo as $value)
                                            @php
                                            $prods= DB::table('producto')->where('id',$value->id_insumo)->first();
                                            @endphp
                                            <tr class="fila-fija">
                                                <td>
                                                    <input type="hidden" style="width:20%;margin-bottom:2px" readonly name="id_insumo[]" class="id_insumo" value="{{$value->id_insumo}}" />
                                                    <input onchange="agregar_id(this)" class="buscador form-control " name="codigo_producto[]" value="@if(!is_null($prods) && $prods!='[]'){{$prods->nombre}}@endif" id="codigo_producto" style="width:94%;margin:0px auto;height:20px" />
                                                </td>
                                                {{-- <td> <input class="" name="estado[]" {{ $value->estado == '1' ? 'checked' : ''}} type="checkbox" /></td> --}}
                                                <td class="eliminar"><button type="button" class="btn btn-danger btn-gray "><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                                            </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12" style="color: red; padding-left:0px;margin-top:15px"><b>Equipo</b><br><br></div>
                                <input name='contador_equipo' id='contador_equipo' type='hidden' value="0">
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
                                            @foreach($equipo as $value)
                                            <tr class="fila-fija">
                                                <td>
                                                    <input type="hidden" name="id_equipo[]" class="id_equipo" value="{{$value->id_equipo}}" />
                                                    <input required name="insumos_producto[]" id="insumos_producto" onchange="agregar_equipo(this)" value="{{$value->codigo_producto}}" class=" buscador_insumos form-control" style="height:25px;width:94%;" />
                                                </td>
                                                {{-- <td> <input class="" name="estado[]" {{ $value->estado == '1' ? 'checked' : ''}} type="checkbox" /></td> --}}
                                                <td class="esconder"><button type="button" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                                            </tr>
                                            @endforeach
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
                                            @foreach($procedimientos as $value)
                                            <tr class="fila-fija">
                                                <td>
                                                    <input type="hidden" name="id_procedimiento[]" class="id_procedimiento" value="{{$value->id_procedimiento}}" />
                                                    <input required name="proce_id[]" id="proce_id" onchange="agregar_procedimiento(this)" value="{{$value->nombre}}" class=" buscador_procedimientos form-control" style="height:25px;width:94%;" />
                                                </td>
                                                <td> <select class="form-control" name="seguro_procedimiento[]"> @foreach($seguros as $s) <option @if($s->id== $value->id_seguro) selected="" @endif value="{{$s->id}}">{{$s->nombre}}</option> @endforeach </select> </td>
                                                <td> <input type="number" class="form-control" style="height:25px;width:94%;" name="precio_procedimiento[]" value="@if($value->precio!=null){{$value->precio}}@else 0 @endif"> </td>
                                                {{-- <td> <input class="" name="estado[]" {{ $value->estado == '1' ? 'checked' : ''}} type="checkbox" /></td> --}}
                                                <td class="esconder"><button type="button" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                                            </tr>
                                            @endforeach
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
                                            <option {{$productos->marca == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="modelo" class="col-md-2 control-label">Modelo</label>
                                    <div class="col-md-4">
                                        <input id="modelo" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="modelo" value="{{$productos->modelo}}" maxlength="25" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="stock_minimo" class="col-md-2 control-label">Stock Minimo</label>
                                    <div class="col-md-4">
                                        <input id="stock_minimo" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="stock_minimo" value="{{$productos->stock_minimo}}" maxlength="25" autofocus>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="fecha_expiracion" class="col-md-2 control-label">Fecha de Expiracion</label>
                                    <div class="col-md-4">
                                        <input id="fecha_expiracion" type="date" class="form-control" name="fecha_expiracion" value="{{$productos->fecha_expiracion}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-12" style="color: red"><b>Comercializacion</b><br><br></div>
                                <div class="form-group col-md-12">
                                    <label for="impuesto_iva_compras" class="col-md-4 control-label">Impuesto IVA Compras</label>
                                    <div class="col-md-8">
                                        <select id="impuesto_iva_compras" name="impuesto_iva_compras" class="form-control select2_cuentas" style="width: 100%" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($impuestos as $value)

                                            <option {{$productos->impuesto_iva_compras == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="impuesto_iva_ventas" class="col-md-4 control-label">Impuesto IVA Ventas</label>
                                    <div class="col-md-8">
                                        <select id="impuesto_iva_ventas" name="impuesto_iva_ventas" class="form-control select2_cuentas" style="width: 100%" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($impuestos as $value)
                                            <option {{$productos->impuesto_iva_ventas == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="impuesto_servicio" class="col-md-4 control-label">Impuesto Servicio</label>
                                    <div class="col-md-8">
                                        <select id="impuesto_servicio" name="impuesto_servicio" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option {{ $productos->impuesto_servicio == '1' ? 'selected' : ''}} value="1">12%</option>
                                            <option {{ $productos->impuesto_servicio == '2' ? 'selected' : ''}} value="2">14%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="impuesto_ice" class="col-md-4 control-label">Impuesto ICE</label>
                                    <div class="col-md-8">
                                        <select id="impuesto_ice" name="impuesto_ice" class="form-control ">
                                            <option value="">Seleccione...</option>
                                            <option {{ $productos->impuesto_ice == '1' ? 'selected' : ''}} value="1">12%</option>
                                            <option {{ $productos->impuesto_ice == '2' ? 'selected' : ''}} value="2">14%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="clasificacion_impuesto_ice" class="col-md-4 control-label">Clasificacion de Impuestos ICE</label>
                                    <div class="col-md-8">
                                        <select id="clasificacion_impuesto_ice" name="clasificacion_impuesto_ice" class="form-control">
                                            <option value="">Seleccione...</option>
                                            <option {{ $productos->clasificacion_impuesto_ice == '1' ? 'selected' : ''}} value="1">12%</option>
                                            <option {{ $productos->clasificacion_impuesto_ice == '2' ? 'selected' : ''}} value="2">14%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="col-md-12" style="color: red; padding-left: 0px"><b>Costos</b><br><br></div>
                                    <div class="form-group col-md-12">
                                        <label for="promedio" class="col-md-4 control-label">Promedio</label>
                                        <div class="col-md-8">
                                            <input id="promedio" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="promedio" value="{{$productos->promedio}}" maxlength="25" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="reposicion" class="col-md-4 control-label">Reposicion</label>
                                        <div class="col-md-8">
                                            <input id="reposicion" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="reposicion" value="{{$productos->reposicion}}" maxlength="25" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="lista" class="col-md-4 control-label">Lista</label>
                                        <div class="col-md-8">
                                            <input id="lista" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="lista" value="{{$productos->lista}}" maxlength="25" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="ultima_compra" class="col-md-4 control-label">Ultima Compra</label>
                                        <div class="col-md-8">
                                            <input id="ultima_compra" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="ultima_compra" value="{{$productos->ultima_compra}}" maxlength="25" autofocus>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!--<div class="col-md-12" style="color: red; padding-left: 0px"><b>Precios</b><br><br></div>
                                <div class="form-group col-md-12">
                                    <label for="precio1" class="col-md-4 control-label" >Precio1</label>
                                    <div class="col-md-8">
                                        <input id="precio1" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio1" value="{{$productos->precio1}}" maxlength="25" autofocus required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio2" class="col-md-4 control-label" >Precio2</label>
                                    <div class="col-md-8">
                                        <input id="precio2" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio2" value="{{$productos->precio2}}" maxlength="25"  autofocus required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio3" class="col-md-4 control-label" >Precio3</label>
                                    <div class="col-md-8">
                                        <input id="precio3" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio3" value="{{$productos->precio3}}" maxlength="25"  autofocus >
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="precio4" class="col-md-4 control-label" >Precio4</label>
                                    <div class="col-md-8">
                                        <input id="precio4" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio4" value="{{$productos->precio4}}" maxlength="25" autofocus >
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="promocion" class="col-md-4 control-label" >Promocion</label>
                                    <div class="col-md-8">
                                        <input id="promocion" type="text" maxlength="13" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="promocion" value="{{$productos->promocion}}" style="text-transform:uppercase;" autofocus >
                                    </div>
                                </div>-->
                                    <h4>Detalle de Precios</h4>
                                    <div class="box-body" style="background: white;">
                                        <input type="hidden" name="cod_prod" id='cod_prod' value="{{ $productos->codigo }}">
                                        <form id="frm_det_prec">
                                            <div class="col-md-12">
                                                <div class="col-md-4">
                                                    <label for="nivel_precio" class="control-label">Nivel</label>
                                                    <input class="form-control input-sm" type="text" name="nivel_precio" id="nivel_precio">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="precio_prod" class="control-label">Precio</label>
                                                    <input type="text" id="precio_prod" name="precio_prod" class="form-control" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="control-label">Acción</label>
                                                    <a onclick="guardar_precio_producto()" class="btn btn-success"> <span class="glyphicon glyphicon-plus"></span></a>
                                                </div>
                                                <div class="box-body">
                                                    <div id="recarga_precio_producto">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                                @php
                                $idp = 0;
                                @endphp
                                <div class="col-md-12" style="color: red; padding-left:0px;margin-top:15px"><b>Detalle Paquete</b><br><br></div>
                                <input name='contador_paquetes' id='contador_paquetes' type='hidden' value="{{$paquetes->count()}}">
                                <input name="cont_paq_temp" id="cont_paq_temp" type="hidden" value="{{$paquetes->count()}}">
                                <input type="hidden" name="id_paquete" id="id_paquete">
                                <input type="hidden" name="iva_prod" id="iva_prod">
                                <input type="hidden" name="id_prod_pr" id="id_prod_pr" value="{{$productos->id}}">
                                <div class="box-body" style="background: white;">
                                    <form id="frm_paq_prod">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <label for="paque_cant" class="col-md-2 control-label">Cant.</label>
                                                <input class="form-control input-sm" type="number" name="paque_cant" id="paque_cant">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="paque_id" class="col-md-2 control-label">Paquete</label>
                                                <input type="text" id="paque_id" name="paque_id" class="buscador_prod_nomb form-control" onchange="buscar_precio(this)">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="precio_paq" class="col-md-2 control-label">PvP</label>
                                                <input type="text" id="precio_paq" name="precio_paq" class="form-control" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="col-md-2 control-label">Acción</label>
                                                <!--<button onclick="guardar_producto_paquete()" id="bagregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"> Agregar</span></button>-->
                                                <a onclick="guardar_producto_paquete()" class="btn btn-success"> <span class="glyphicon glyphicon-plus">Agregar</span></a>
                                            </div>
                                            <div class="box-body">
                                                <div id="recarga_prod_paquete">
                                                </div>
                                            </div>
                                            <!--<div class="form-group col-md-12" >
                                            <table  class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                            <thead class="thead-dark">
                                                <tr class='well-darks'>
                                                    <th width="20%" tabindex="0">Cant.</th>
                                                    <th width="40%" tabindex="0">Paquete</th>
                                                    <th width="20%" tabindex="0">PvP</th>
                                                    <th width="20%" tabindex="0">Acción</th>
                                                    <th width="40%" tabindex="0">Tarifario</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbprodpaq">
                                            </tbody>
                                            </table>
                                        </div>-->
                                        </div>
                                    </form>
                                </div>
                                <!--<div class="col-md-12 table-responsive">
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
                                                <th width="40%" tabindex="0">Tarifario</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalle_paquete">
                                            @foreach($paquetes as $value)
                                            <tr id="dato_det_paq{{$idp}}">
                                                <td>
                                                    <input required type="hidden" id="visibilidad_paquete{{$idp}}" name="visibilidad_paquete{{$idp}}" value="1">
                                                    <input required type="hidden" id="id_prod_paq{{$idp}}" name="id_prod_paq{{$idp}}" value="{{$value->id}}">
                                                    <input type="number" name="paque_cant{{$idp}}" id="paque_cant{{$idp}}" class="form-control"  value="{{$value->cantidad}}" style="height:25px;width:75%;">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="id_paquete{{$idp}}" class="id_paquete{{$idp}}" value="{{$value->id_paquete}}"/><input required name="paque_id{{$idp}}" id="paque_id{{$idp}}" onchange="agregar_paquete(this)"  value="{{$value->nombre}}" class="buscador_paquetes form-control" style="height:25px;width:90%;"/>
                                                </td>
                                                <td>
                                                   <input type="text" name="precio_paq{{$idp}}" id="precio_paq{{$idp}}" class="form-control"  value="{{$value->precio}}" style="height:25px;width:75%;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off">
                                                </td>
                                                
                                                {{-- <td> <input class="" name="estado[]" {{ $value->estado == '1' ? 'checked' : ''}} type="checkbox" /></td> --}}
                                                <td><button type="button" onclick="eliminar_det_paq('{{$idp}}')" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                                                </td>
                                                <td>
                                                   <a id="tar_paq" class="btn btn-success btn-xs" data-remote="{{route('crea_producto_tarifario.paquete',['id_prod_paq' => $value->id,'id_producto' => $value->id_producto,'id_paquete' => $value->id_paquete])}}" data-toggle="modal" data-target="#tarifario_paquete" ><span>Agregar</span> </a>
                                                </td>
                                            </tr>
                                            @php $idp ++; @endphp  
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>-->
                            </div>
                        </div>
                        <br><br>
                        <!--Button Actualizar-->
                        <!--<div class="form-group col-xs-10" style="text-align: center;">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success btn-gray">
                                    Actualizar
                                </button>
                            </div>
                        </div>-->
                        <div class="form-group col-xs-10" style="text-align: center;">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" class="btn btn-success btn-gray" onclick="actualiza_producto({{$productos->id}})">
                                    Actualizar
                                </button>
                            </div>
                        </div>

                        @php

                        $exist_val_part = Sis_medico\Ct_productos::where('id',$productos->id)->first();

                        @endphp
                        <!--Tabla de Productos Tarifario-->
                        <div class="row">
                            <div class="col-md-10">
                                <h3>Producto Tarifario</h3>
                            </div>
                            <div class="col-md-2">
                                <div class="col-md-7">
                                    <a id="crear" class="btn btn-success" href="{{route('crear_tarifario.productos',['id' => $productos->id])}}">Crear Registro </span></a>
                                </div>
                            </div>
                        </div>
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="row" id="listado">
                                <div class="table-responsive col-md-12">
                                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr>
                                                <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro</th>
                                                <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nivel</th>
                                                <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @if(!is_null($seg))

                                            @if($exist_val_part->valor_total_paq != null)

                                            <tr class="well">
                                                <td>PARTICULAR</td>
                                                <td>PARTICULAR</td>
                                                <td>@if(!is_null($exist_val_part)){{$exist_val_part->valor_total_paq}}@endif</td>
                                                <td>
                                                    <a id="val_tot_paq" class="btn btn-success btn-xs" data-remote="{{route('upd_total_valor.paquete',['id_prod' =>$exist_val_part->id])}}" data-toggle="modal" data-target="#val_tot_paquete"><span class="glyphicon glyphicon-edit"></span></a>
                                                    <a class="btn btn-danger btn-xs" id="planillar"> <span class="glyphicon glyphicon-trash"></span> </a>
                                                </td>
                                            </tr>

                                            @endif

                                            @foreach($seg as $value)
                                            @php
                                            $validacion =Sis_medico\Ct_Productos_Tarifario::where('id_producto',$productos_tarifario->id)->where('id_seguro',$value->id_seguro)->where('nivel',$value->nivel)
                                            ->where('estado',1)->first();

                                            $inf_nivel = Sis_medico\Nivel::where('id',$value->nivel)->where('estado',1)->first();

                                            @endphp
                                            <tr class="well">

                                                <td>@if(!is_null($value->seguro)) {{substr($value->seguro->nombre, 0,10)}}@endif</td>
                                                <td>@if(!is_null($inf_nivel)){{$inf_nivel->nombre}}@endif</td>
                                                @if(is_null($validacion))
                                                <td>0.00</td>
                                                @else
                                                <td>{{$validacion->precio_producto}}</td>
                                                @endif

                                                @if(is_null($validacion))

                                                <td>
                                                    <a href="{{ route('actualiza_product_uno.tarifario', ['id' => $productos_tarifario->id,'id_nivel' => $value->nivel])}}" class="btn btn-warning btn-xs" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>

                                                    <a onclick="elimina_producto_tarifario('{{$value->id }}')" class="btn btn-danger btn-xs" id="planillar"> <span class="glyphicon glyphicon-trash"></span> </a>


                                                </td>

                                                @else

                                                <td>
                                                    <a href="{{ route('actualiza_producto.tarifario', ['id_producto' => $productos_tarifario->id, 'id_seguro' => $value->id_seguro,'id_nivel' => $value->nivel])}}" class="btn btn-warning btn-xs" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>

                                                    <a onclick="elimina_producto_tarifario('{{$value->id }}')" class="btn btn-danger btn-xs" id="planillar"> <span class="glyphicon glyphicon-trash"></span> </a>


                                                </td>


                                                @endif
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
    <link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            cargar_product_paquete();
            cargar_precio_producto();

        });
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });
        });


        /*function cargar_product_paquete(){

            var id_pr = $("#id_prod").val();
            
             $.ajax({
                url:"{{route('detalle.carga_paquete')}}/"+id_pr,
                dataType: "json",
                type: 'get',
                success: function(data){
                    
                    var table = document.getElementById("tbprodpaq");

                    $.each(data, function (index, value) {
                       
                        var row = table.insertRow(index);
                        row.id = 'tbpr'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = value.cantid;
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.paquete;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.pvp;
                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = '<a href="#" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                        var cell5 = row.insertCell(4);
                        cell5.innerHTML = '<a id="tar_paq" class="btn btn-success btn-xs" data-remote="{{route('crea_producto_tarifario.paquete',['id_prod_paq' =>'+value.id+','id_producto' => '+value.id_producto+','id_paquete' => '+value.id_paquete+'])}}" data-toggle="modal" data-target="#tarifario_paquete" ><span>Agregar</span> </a>';

                    });

                }

            })
        
        }*/

        function cargar_product_paquete() {

            var id_pr = $("#id_prod").val();

            $.ajax({
                type: "GET",
                url: "{{route('detalle.carga_paquete')}}/" + id_pr,
                data: "",
                datatype: "html",
                success: function(data) {
                    $('#recarga_prod_paquete').html(data);
                },
                error: function() {
                    //alert('error al cargar');
                }
            })

        }

        function cargar_precio_producto() {

            var cod_producto = $("#cod_prod").val();

            $.ajax({
                type: "GET",
                url: "{{route('detalle.carga_precio')}}/" + cod_producto,
                data: "",
                datatype: "html",
                success: function(data) {
                    $('#recarga_precio_producto').html(data);
                },
                error: function() {
                    //alert('error al cargar');
                }
            })


        }


        //Elimina Producto Paquete
        function elimina_producto_paquete(id_pr_paq) {

            Swal.fire({
                title: '¿Desea Eliminar Paquete?',
                text: "No puedes revertir esta acccion!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'get',
                        url: "{{ route('anula_producto.paquete')}}",
                        datatype: 'json',
                        data: {
                            'id_pr_paq': id_pr_paq
                        },
                        success: function(data) {
                            swal({
                                title: "Paquete Eliminado",
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })

                            cargar_product_paquete();

                        },
                        error: function(data) {
                            console.log(data);
                        }

                    });
                }

            })

        }


        //Elimina Precio Producto
        function elimina_producto_precio(id_pr_precio) {

            //alert(id_pr_precio);

            Swal.fire({
                title: '¿Desea Eliminar el Precio?',
                text: "No puedes revertir esta acccion!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'get',
                        url: "{{ route('anula_producto.precio')}}",
                        datatype: 'json',
                        data: {
                            'id_pr_prod': id_pr_precio
                        },
                        success: function(data) {
                            swal({
                                title: "Precio Eliminado",
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })

                            cargar_precio_producto();

                        },
                        error: function(data) {
                            console.log(data);
                        }

                    });
                }

            })

        }


        //Buscador de Paquetes
        $(".buscador_prod_nomb").autocomplete({

            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('buscar_prod.nombre')}}",
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
            change: function(event, ui) {

                $('#id_paquete').val(ui.item.value1);
                $('#iva_prod').val(ui.item.iva_prod);
                //obtener_precio(ui.item.codig_product);

            },

        });


        //Guardar Producto Paquete
        function guardar_producto_paquete() {

            var cant = $("#paque_cant").val();
            var id_prod = $("#id_prod_pr").val();
            var id_prod_paq = $("#id_paquete").val();
            var nomb_prod = $("#paque_id").val();
            var prec = $("#precio_paq").val();
            var iva_prod = $("#iva_prod").val();

            //var formulario = document.forms["frm_paq_prod"];

            //var paq_cantid = formulario.paque_cant.value;
            //Mensaje 
            var msj = "";

            if (cant == "") {

                msj = msj + "Por favor, Ingrese la Cantidad<br/>";

            }

            if (nomb_prod == "") {

                msj = msj + "Por favor, Seleccione el Paquete<br/>";

            }

            if (msj != "") {
                swal({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
                return false;
            }

            Swal.fire({
                title: '¿Desea Agregar un Nuevo Paquete?',
                text: "No puedes revertir esta acccion!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'post',
                        url: "{{route('producto.agregar_paquete')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        //data: $("#frm_paq_prod").serialize(),
                        data: {
                            'cant': cant,
                            'id_prod': id_prod,
                            'id_prod_paq': id_prod_paq,
                            'nomb_prod': nomb_prod,
                            'prec': prec,
                            'iva_prod': iva_prod
                        },
                        success: function(data) {

                            if (data.msj == 'ok') {

                                swal("Error!", "Ya existe el Paquete para este Producto");

                                $('#id_paquete').val("");
                                $('#iva_prod').val("");
                                $('#paque_cant').val("");
                                $('#paque_id').val("");
                                $('#precio_paq').val("");

                            } else {

                                cargar_product_paquete();
                                $('#id_paquete').val("");
                                $('#iva_prod').val("");
                                $('#paque_cant').val("");
                                $('#paque_id').val("");
                                $('#precio_paq').val("");

                            }

                            /*if(data.msj =='ok'){
                                cargar_product_paquete();
                                $('#id_paquete').val("");
                                $('#paque_cant').val("");
                                $('#paque_id').val("");
                                $('#precio_paq').val("");
                            }*/

                            /*var indexr = 10
                            var table = document.getElementById("tbprodpaq");
                            var row = table.insertRow(indexr);
                            row.id = 'tbpr'+data.id;
                            var cell1 = row.insertCell(0);
                            cell1.innerHTML = data.cantidad;
                            var cell2 = row.insertCell(1);
                            cell2.innerHTML = data.nomb_paq;
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = data.precio_pvp;
                            var cell4 = row.insertCell(3);
                            cell4.innerHTML = '<a href="#" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';*/

                        },
                        error: function(data) {

                        }
                    });

                }

            })

        }



        function guardar_precio_producto() {
            var nivel_precio = $("#nivel_precio").val();
            var precio_prod = $("#precio_prod").val();
            var cod_prod = $("#cod_prod").val();
            var msj = "";
            if (nivel_precio == "") {
                msj = msj + "Por favor, Ingrese el Nivel del Producto<br/>";
            }
            if (precio_prod == "") {
                msj = msj + "Por favor, Ingrese el Precio del Producto<br/>";
            }
            if (msj != "") {
                swal({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
                return false;
            }
            Swal.fire({
                title: '¿Desea Agregar un Nuevo Precio del Producto?',
                text: "No puedes revertir esta acccion!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'post',
                        url: "{{route('producto.agregar_precio')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: {
                            'nivel_pre': nivel_precio,
                            'prec_prod': precio_prod,
                            'cod_prod': cod_prod
                        },
                        success: function(data) {
                            if (data.msj == 'ok') {
                                swal("Error!", "Ya existe un precio con el mismo nivel");
                                $('#nivel_precio').val("");
                                $('#precio_prod').val("");
                            } else {
                                cargar_precio_producto();
                                $('#nivel_precio').val("");
                                $('#precio_prod').val("");
                            }
                        },
                        error: function(data) {}
                    });
                }
            })
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

        function actualiza_producto(id_prod) {
            if ($('#tipo_producto').val() == '') {
                swal({
                    title: "Seleccione el tipo de producto",
                    icon: "error",
                    type: 'error'
                })
                return false;
            }
            //alert(id_prod);
            $.ajax({
                type: "post",
                url: "{{route('productos_servicios.update',['id_prod' => $productos->id])}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#edit_prod").serialize(),
                success: function(data) {
                    $("#alerta_guardado").fadeIn(1000);
                    $("#alerta_guardado").fadeOut(3000);
                },
                error: function(data) {
                    //console.log(data);
                }
            });
        };
        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false
        })

        function limpiar() {
            $('').val();
        }
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });
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
        });

        function seleccionar_cuentas() {
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

        function goBack() {
            window.history.back();
        }
        $('body').on('click', '.delete', function() {
            $(this).parent().parent().remove();
        });
        var fila = '<td><input class="form-control input-sm" type="hidden" style="width: 80%;height:20px;" value="0" name="id[]" required>' +
            '<input class="form-control input-sm" type="text" style="width: 50%;height:20px;" placeholder="#" name="nivel[]" required>' +
            '</td><td><input class="form-control input-sm" type="text" style="width: 50%;height:20px;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" required>' +
            '</td><td><input type="hidden" value="1" name="estado[]" />' +
            '<button type="button" class="btn btn-danger btn-gray delete" >' +
            '<i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'; //$("#mifila").html();
        function nuevo() {
            var nuevafila = $("#mifila").html();
            var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            rowk.innerHTML = fila;
            rowk.className = "well";
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

        function agregar_td() {
            var id = document.getElementById('contador_prod').value;
            var tr = '<tr class="fila-fija"><td><input type="hidden" style="width:20%;margin-bottom:2px" readonly name="id_insumo[]" class="id_insumo"/><input onchange="agregar_id(this)" class="buscador form-control"  name="codigo_producto[]" id="codigo_producto" style="width:94%;margin:0px auto;height:20px"/></td><td class="eliminar"><button  type="button"  class="btn btn-danger btn-gray " ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>'
            $('#examples').append(tr);
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
            // Autocompletar de productos
        };
        $(document).on("click", ".eliminar", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });
        // Agregar id
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
        $('.agregar').on('click', function() {
            agregar();
        });

        function agregar() {
            var id = document.getElementById('contador_equipo').value;
            var tr = '<tr>' + '<td><input type="hidden" name="id_equipo[]" class="id_equipo"/><input required name="insumos_producto[]" id="insumos_producto" onchange="agregar_equipo(this)" class=" buscador_insumos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' + '<td class="esconder" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#equipo').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_equipo").value = parseInt(sum);
            //Autocompletar
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
        // Agregar id
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
            var tr = '<tr>' + '<td><input type="hidden" name="id_procedimiento[]" class="id_procedimiento" /><input required name="proce_id[]" id="proce_id" onchange="agregar_procedimiento(this)" class=" buscador_procedimientos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' + '<td><select class="form-control select2" name="seguro_procedimiento[]" style="height:25px;width:94%;"> <option value="">Seleccione ...</option> @foreach($seguros as $value) <option value="{{$value->id}}">{{$value->nombre}}</option> @endforeach </select></td>' + '<td><input type="number" style="height:25px;width:94%;" name="precio_procedimiento[]" placeholder="0.00" class="form-control"></td>' + '<td class="ocultar" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
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


        //Agregar id
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

        $('.agregar_paquetes').on('click', function() {
            agregar_paquetes();
        });

        function agregar_paquetes() {

            id = document.getElementById('contador_paquetes').value;
            id_temp_paq = document.getElementById('cont_paq_temp').value;

            //Creamos la Tabla Temporal
            var midiv_item = document.createElement("tr")
            midiv_item.setAttribute("id", "dato_det_paq" + id);

            midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_paquete' + id + '" name="visibilidad_paquete' + id + '" value="1"><input type="number" name="paque_cant' + id + '" id="paque_cant' + id + '" class="form-control" style="height:25px;width:75%;"></td><td><input type="hidden" name="id_paquete' + id + '" class="id_paquete"><input required name="paque_id' + id + '" id="paque_id' + id + '" onchange="agregar_paquete(this)" class=" buscador_paquetes form-control"  style="height:25px;width:90%;"></td><td><input type="text" name="precio_paq' + id + '" id="precio_paq' + id + '" class="form-control" style="height:25px;width:75%;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off"></td><td style="width: 40px;"><button type="button" onclick="eliminar_det_paq(' + id + ')" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'

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

                    obtener_precio_producto(ui.item.codig_product, ui.item.pos_paq);
                    id_temp_paq = parseInt(id_temp_paq);
                    id_temp_paq = id_temp_paq + 1;
                    document.getElementById('cont_paq_temp').value = id_temp_paq;

                },

                minLength: 2,

            });

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

                    if (data.no_data != 'no') {
                        $('#precio_paq' + data.posic).val(data.prec_prod);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });

        }

        function eliminar_det_paq(valor) {
            var dato_pago1 = "dato_det_paq" + valor;
            var nombre_pago2 = 'visibilidad_paquete' + valor;

            document.getElementById(dato_pago1).style.display = 'none';
            document.getElementById(nombre_pago2).value = 0;

        }

        //Obtener precio Producto Nuevo
        function obtener_precio(cod_prod) {

            $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('contable_precio.prod')}}",
                datatype: 'json',
                data: {
                    'cod_prod': cod_prod
                },
                success: function(data) {

                    if (data.no_data != 'no') {
                        $('#precio_paq').val(data.prec_prod);
                    }

                },
                error: function(data) {
                    console.log(data);
                }
            });

        }


        /*function agregar_paquetes() {
            var id = document.getElementById('contador_paquetes').value;
            var tr = '<tr>' + '<td><input type="number" name="paque_cant[]" id="paque_cant" class="form-control" style="height:25px;width:75%;"></td>' + '<td><input type="hidden" name="id_paquete[]" class="id_paquete"/><input required name="paque_id[]" id="paque_id" onchange="agregar_paquete(this)" class=" buscador_paquetes form-control"  style="height:25px;width:90%;" name="producto[]"/></td>'+'<td><input type="text" name="precio_paq[]" id="precio_paq" class="form-control" style="height:25px;width:75%;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off"></td>' + '<td class="remover" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#paquetes').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_paquetes").value = parseInt(sum);


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
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
            });

        };*/

        $(document).on("click", ".remover", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_paquete(e) {

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


        $('#val_tot_paquete').on('hidden.bs.modal', function() {
            //location.reload();
            $(this).removeData('bs.modal');
        });


        //Limpiamos los valores guuardados en la variables de la modal
        $('#tarifario_paquete').on('hidden.bs.modal', function() {
            //location.reload();
            $(this).removeData('bs.modal');
        });


        $('#edit_prod_tar_paq').on('hidden.bs.modal', function() {
            //location.reload();
            $(this).removeData('bs.modal');
        });

        $('#producto_tarifario').on('hidden.bs.modal', function() {
            //location.reload();
            $(this).removeData('bs.modal');
        });


        //Eliminar Producto Tarifario
        function elimina_producto_tarifario(id_prod_tar) {

            //alert(id_prod_tar);
            Swal.fire({
                title: '¿Desea Eliminar Producto Tarifario?',
                text: "No puedes revertir esta acccion!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'get',
                        url: "{{ route('anula_producto.tarifario')}}",
                        datatype: 'json',
                        data: {
                            'id_prod_tar': id_prod_tar
                        },
                        success: function(data) {
                            console.log(data);
                            //console.log(data);
                            /*swal({
                                title: "Producto Tarifario Eliminado",
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })*/

                            //Swal.fire("Error!", "Producto Tarifario Eliminado");
                            swal({
                                title: "Producto Tarifario Eliminado",
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })

                            window.location.reload(true);

                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });

                }

            })

        }

        function buscar_precio(e) {
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
                    document.getElementById('precio_paq').value = data[0].precio;
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    </script>
</section>
@endsection