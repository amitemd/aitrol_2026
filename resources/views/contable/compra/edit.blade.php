@extends('contable.compra.base')
@section('action-content')

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
        min-width: 460px;
        _width: 460px !important;
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
        location.href = "{{ route('compras_index') }}";
    }

    function goBack() {
        location.href = "{{ route('compras_index') }}";
    }
</script>

<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<div class="modal fade" id="modalpedido" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="modalpdf" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Compra</a></li>
            <li class="breadcrumb-item"><a href="../compras">Registro Factura de Compra</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Factura de Compra</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-6">
                            <div class="box-title"><b>FACTURA DE COMPRA</b></div>
                        </div>
                        <div class="col-6" style="text-align: center;">
                            <div class="row">
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$compras->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$compras->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </a>
                                <a class="btn btn-default btn-gray" data-remote="{{ route('compras.subirpdf',['id'=> $compras->id,'parametro'=>'1'])}}" data-toggle="modal" data-target="#modalpdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Subir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body dobra">

            <div class="header row">
                <div class="col-md-12">
                    <div class="header row">

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">P. Emision(Empresa)</label>
                            <input class="col-md-12 col-xs-12 form-control" id="punto_emision" value="{{$compras->punto_emision}}" name="punto_emision" readonly>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">Sucursal(Empresa)</label>
                            <input class="col-md-12 col-xs-12 form-control" id="sucursal" value="{{$compras->sucursal}}" name="sucursal" readonly>
                        </div>


                        <div class="col-md-1 col-xs-1 px-1">
                            <label class="label_header" style="padding-left: 0px">Estado</label>
                            <input class="form-control col-md-12 col-xs-12" style="@if($compras->estado==0) background-color: red; @else background-color: green; @endif">
                        </div>

                        <div class="col-md-1 col-xs-1 px-1">
                            <label class=" label_header">Tipo</label>
                            <input class="form-control " type="text" name="tipo" id="tipo" readonly @if(($compras->tipo==1)) value="COM-FA" @elseif($compras->tipo==2) value="COM-FACT" @else value="COM-FAS" @endif>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha</label>
                            <div class="input-group col-md-12">
                                <input class="form-control col-md-12 col-xs-12" id="fecha" type="date" name="fecha" value="{{$compras->fecha}}">
                            </div>
                        </div>
                        <div class="col-md-1 col-xs-1 px-1">
                            <label class="label_header">ID:</label>
                            <input class="col-md-12 col-xs-12 form-control" id="id_fc" value="{{$compras->id}}" name="id_fc" readonly>
                        </div>
                        <div class="col-md-1 col-xs-1 px-1">
                            <label class="label_header">Asiento: </label>
                            <input class="col-md-12 col-xs-12 form-control" id="ct_c.id_asiento_cabecera" name="id_asiento_cabecera" value="{{$compras->id_asiento_cabecera}}" readonly>
                        </div>
                        <div class="col-md-2 col-3 px-1">
                            <label class=" label_header">Aparece Archivo SRI &nbsp;
                            </label>
                            <div class="input-group col-md-12" style="text-align: center;">
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="archivo_sri" @if($compras->archivo_sri==1) checked @endif disabled>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row  ">

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Id</label>
                            <input id="proveedor" type="text" class="form-control  " name="proveedor" value="{{$compras->proveedor}}" onchange="cambiar_proveedor()" readonly>
                        </div>
                        @php $proveedor = Sis_medico\Proveedor::where('id', $compras->proveedor)->first(); @endphp
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Proveedor</label>
                            <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor()" name="nombre_proveedor" id="nombre_proveedor" disabled>
                                <option @if(isset($proveedor)) value="{{$proveedor->id}} @endif">@if(isset($proveedor)) {{$proveedor->razonsocial}} @endif</option>
                            </select>

                        </div>

                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Dirección</label>
                            <input id="direccion_proveedor" type="text" class="form-control" name="direccion_proveedor" value="{{$compras->proveedorf->direccion}}" readonly>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <div class="col-md-12 px-0">
                                <label for="termino" class="label_header">Término</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select id="termino" name="termino" class="form-control" disabled style="width:100%">
                                    @foreach($termino as $t)
                                    <option @if($t->id==$compras->termino) selected="selected" @endif value="{{$t->id}}">{{$t->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">O. Compra</label>
                                        <div class="input-group">
                                            <input id="o_compra" maxlength="30" type="text" class="form-control  " name="o_compra"
                                            value="" >
                                            <div class="input-group-addon " >
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('o_compra').value = '';"></i>
                                            </div>
                                        </div>
                                    </div>-->

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Caducidad</label>
                            <div class="input-group col-md-12">
                                <input id="f_caducidad" type="date" class="form-control   col-md-12" name="f_caducidad" value="@if(!is_null($compras)){{$compras->f_caducidad}}@endif">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row  ">
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Autorización</label>

                            <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="@if(!is_null($compras)){{$compras->autorizacion}}@endif">



                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Facturación</label>

                            <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@if(!is_null($compras)){{$compras->f_autorizacion}}@endif" readonly>

                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Serie</label>
                            <input id="serie" maxlength="25" type="text" class="form-control  " name="serie" onkeyup="agregar_serie()" value="@if(!is_null($compras)){{$compras->serie}}@endif" readonly>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Secuencia Factura</label>
                            <input id="secuencia_factura" maxlength="30" type="text" class="form-control " name="secuencia_factura" value="@if(!is_null($compras)){{$compras->secuencia_factura}}@endif" readonly>
                        </div>
                        <div class="col-md-2 col-xs-2 col-1 px-1">
                            <label class=" label_header">Credito Tributario</label>
                            <select name="credito_tributario" id="cred_tributario" class="form-control  select2_cuentas " style="width: 100%; height: 22px" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($c_tributario as $value)
                                <option {{ $value->codigo == $compras->credito_tributario ? 'selected' : ''}} value="$value->codigo"> {{$value->codigo}} - {{$value->nombre}}</option>
                                <option value="{{$value->codigo}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2 col-1 px-0">
                            <label class=" label_header">Tipo de Comprobante</label>
                            <select name="tipo_comprobante" id="tipo_comprobante" class="form-control  select2_cuentas " style="width: 100%;height: 22px" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($t_comprobante as $value)
                                <option {{ $value->codigo == $compras->tipo_comprobante ? 'selected' : ''}} value="{{$value->codigo}}"> {{$value->codigo}} - {{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">

                        <input type="hidden" name="sucursal_final" id="sucursal_final">


                    </div>
                </div>
                <div class="col-md-12 px-1">
                    <label class=" label_header">Concepto</label>
                    <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion" value="{{$compras->observacion}}" readonly>
                </div>
                <div id="output">
                </div>

            </div>
            <div class="col-md-12 table-responsive" style="width: 100%;">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
                            <!--<th width="10%" class="" tabindex="0">Codigo</th>-->
                            <th width="35%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                            <th width="10%" class="" tabindex="0">Cantidad</th>
                            <th width="10%" class="" tabindex="0">Precio</th>
                            <th width="10%" class="" tabindex="0">% Desc</th>
                            <th width="10%" class="" tabindex="0">Descuento</th>
                            <th width="10%" class="" tabindex="0">Precio Neto</th>
                            <th width="5%" class="" tabindex="0">IVA</th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        @foreach($compras->detalles as $x)
                        <tr class="wello">
                            <td style="max-width:100px;">
                                <Input type="hidden" name="codigo[]" class="codigo_producto" />
                                <select name="nombre[]" class="form-control select2_cuentas" style="width:100%" disabled onchange="verificar(this)">
                                    @php $productos = Sis_medico\Ct_productos::where('codigo', $x->codigo)->first(); @endphp
                                    <option @if(isset($productos)) value="{{$productos->nombre}}" @endif> @if(isset($productos)) {{$productos->codigo}} | {{$productos->nombre}} @endif</option>
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto">{{$x->detalle}}</textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->cantidad}}" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" disabled>
                                    @php $bodega = Sis_medico\Ct_Bodegas::where('id', $x->bodega)->first(); @endphp
                                    <option @if(isset($bodega)) value="{{$bodega->id}}" @endif> @if(isset($bodega)) {{$bodega->nombre}} @endif</option>
                                </select>
                            </td>
                            <td id="tprecio" style="max-width:100px;">

                                <input type="text" class="form-control pneto" style="width: 80%;height:20px;" name="precio[]" value="{{$x->precio}}" readonly>
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{$x->descuento_porcentaje}}" name="descpor[]" readonly>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{$x->descuento}}" name="desc[]" readonly>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="{{$x->extendido}}" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" readonly>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" @if($x->iva==1) checked @endif disabled>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class=''>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">Subtotal 12%</td>
                            <td id="subtotal_12" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal_12}}@endif</td>
                            <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">Subtotal 0%</td>
                            <td id="subtotal_0" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal_0}}@endif</td>
                            <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">Descuento</td>
                            <td id="descuento" class="text-right px-1">@if(!is_null($compras)){{$compras->descuento}}@endif</td>
                            <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal</td>
                            <td id="base" class="text-right px-1">@if(!is_null($compras)){{$compras->subtotal}}@endif</td>

                            <input type="hidden" name="base1" id="base1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                            <td id="tarifa_iva" class="text-right px-1">@if(!is_null($compras)){{$compras->iva_total}}@endif</td>
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
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td id="total" class="text-right px-1">@if(!is_null($compras)){{$compras->total_final}}@endif</td>
                            <input type="hidden" name="total1" id="total1" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td colspan="2" class="text-right"></td>
                            <td id="copagoTotal" class="text-right px-1"></td>
                            <input type="hidden" name="totalc" id="totalc" class="hidden">
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
    <script src="{{ asset ('/js/icheck.js') }}"></script>
    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script>
        $(document).ready(function() {
            //limpiar();
            //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
            $('.select2_cuentas').select2({
                tags: false
            });
            $('#archivo_sri').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });
            $('#poseexml').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });
            $('.iva').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });
            $('.ice').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });
            $('.smr').on('ifChecked', function(event) {
                $("#archivosri").val(1);
            });
            $('.smr').on('ifUnchecked', function(event) {
                $("#archivosri").val(0);
            });
        });
    </script>
</section>
@endsection