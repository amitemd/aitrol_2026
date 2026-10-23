<table width="100%">
    <tr>
        <td>Clave Consultada:</td>
        <td>{{ $result->RespuestaAutorizacionComprobante->claveAccesoConsultada }}</td>
    </tr>
    <tr>
        <td>Estado:</td>
        <td>{{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado }}</td>
    </tr>
    <tr>
        <td>Ambiente:</td>
        <td>{{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente }}</td>
    </tr>
    <tr>
        <td>Fecha autorización:</td>
        <td>{{ date('Y-m-d H:i:s', strtotime($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion }}))</td>
    </tr>
    <tr>
        <td>Número de autorización:</td>
        <td>{{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion }}</td>
    </tr>
</table>
<form class="form-vertical " id="crear_factura" role="form" method="post">
    {{csrf_field()}}
    <input id="electronica" type="hidden" name="electronica" value="1" />
    <div class="box box-solid">
        <div class="box-header header_new">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-sm-9 col-6">
                        <div class="box-title"><b>FACTURA DE COMPRA</b></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="header row">
                <div class="col-md-12">
                    <div class="header row">
                        <div class="col-xs-3 col-3 px-0">
                            <label class=" label_header">Tipo de Comprobante</label>
                            <select onchange="cambioTipo(this)" name="tipo_comprobante" id="tipo_comprobante" class="form-control select2" style="width: 100%;heigth: 22px">
                                <option value="">Seleccione...</option>
                                {{$selectComprobente}}
                            </select>
                        </div>
                        <div class="col-xs-3 col-3 px-1">
                            <label class=" label_header">Credito Tributario</label>
                            <select name="credito_tributario" id="cred_tributario" class="form-control select2" style="width: 100%; heigth: 22px">
                                <option value="">Seleccione...</option>
                                {{$selectCreditoTributario}}
                            </select>
                        </div>
                        @if (isset($iva_param))
                        <input type="text" name="ivareal" id="ivareal" class="hidden" value="{{$iva_param->iva}}">
                        @endif
                        <div class="col-xs-2 px-1">
                            <label class=" label_header">Fecha Facturación</label>
                            <div class="input-group col-md-12">
                                <input id="fecha" type="date" class="form-control col-md-12" name="fecha" onchange="swetAlertDate()" value="{{$fechaEmision}}">
                                <input id="f_autorizacion" type="hidden" name="f_autorizacion" value="{{$fechaEmision}}">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Término</label>
                            <select onchange="sumarFecha(this)" class="form-control select2" name="termino" id="termino" class="form-control ">
                                <option value="">Seleccione...</option>
                                {{$selectTerminos}}
                            </select>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Caducidad</label>
                            <div class="input-group col-md-12">
                                <input id="f_caducidad" type="date" class="form-control   col-md-12" name="f_caducidad" value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">Id</label>
                            <div class="input-group">
                                <input id="proveedor" type="text" class="form-control  " name="proveedor" value="{{$comprobante->infoTributaria->ruc}}" onchange="cambiar_proveedor()">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('proveedor').value = ''; cambiar_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-4 px-1">
                            <label class=" label_header">Proveedor</label>
                            <select class="form-control select2" style="width: 100%;" onchange="cambiar_nombre_proveedor(); llenarCampo()" name="nombre_proveedor" id="nombre_proveedor">
                                <option value="">Seleccione...</option>
                                {{$selectProveedor}}
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6 px-1">
                            <label class=" label_header">Dirección</label>
                            <div class="input-group">
                                <input id="direccion_proveedor" type="text" class="form-control" name="direccion_proveedor" value="{{$comprobante->infoTributaria->dirMatriz}}">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = ''; cambiar_nombre_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row  ">
                        <div class="col-xs-6 px-1">
                            <label class=" label_header">Autorización</label>
                            <div class="input-group">
                                <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="{{$result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion}}">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" value=""></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 px-1">
                            <label class=" label_header">Serie</label>
                            <div class="input-group">
                                <input id="serie" maxlength="25" type="text" class="form-control" value="{{$comprobante->infoTributaria->estab}}-{{$comprobante->infoTributaria->ptoEmi}}" name="serie" onkeyup="agregar_serie()">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" value=""></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 px-1">
                            <label class=" label_header">Secuencia Factura</label>
                            <div class="input-group">
                                <input id="secuencia_factura" maxlength="30" type="text" class="form-control" value="{{$comprobante->infoTributaria->secuencial}}" name="secuencia_factura">
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = ''"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Tipo</label>
                            <select class="form-control select2" style="width: 100%;" name="tipo" id="tipo">
                                <option value="">Seleccione...</option>
                                <option value="1">FACT-COMPRA</option>
                                <option value="2">COM-FACT</option>
                            </select>
                        </div>
                        <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                        <!--Punto de Emision-->
                        <input type="hidden" name="sucursal_final" id="sucursal_final">
                    </div>
                </div>
                <div class="col-md-8 px-1">
                    <label class=" label_header">Concepto</label>
                    <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion">
                </div>
                <div class="col-md-2 px-1">
                    <label class="label_header">Sucursal (Empresa)</label>
                    <select name="sucursal" id="sucursal" class="form-control" onchange="cargarSucursal(this)">
                        <option value="">Seleccione</option>
                        <?php
                            foreach ($sucursales as $value) {
                                $select = '';
                                if ($value->codigo_sucursal == '100')
                                    $select = 'selected';
                                echo '<option value="' . $value->id . '" ' . $select . '>' . $value->codigo_sucursal . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 px-1">
                    <label class=" label_header">P. Emision (Empresa)</label>
                    <select name="p_emision" id="p_emision" class="form-control">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div id="output">
                </div>
            </div>
            <div class="col-md-12 table-responsive" style="width: 100%;">
                <input type="hidden" name="contador" id="contador" value="0">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr>
                            <th width="35%" class="" tabindex="0">Descripci&oacute;n</th>
                            <th width="35%" class="" tabindex="0">Producto</th>
                            <th width="10%" class="" tabindex="0">Cantidad</th>
                            <th width="10%" class="" tabindex="0">Precio</th>
                            <th width="10%" class="" tabindex="0">% Desc</th>
                            <th width="10%" class="" tabindex="0">Descuento</th>
                            <th width="10%" class="" tabindex="0">Precio Neto</th>
                            <th width="5%" class="" tabindex="0">IVA</th>
                            <th width="10%" class="" tabindex="0">
                                <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="agregar_cuentas">
                        @foreach ($comprobante->detalles->detalle as $row)
                        <tr class="wello">
                            <td style="max-width:100px;">
                                {{ $row->descripcion }}
                            </td>
                            <td style="max-width:100px;">
                                <input type="hidden" id="codigo_{{ $count }}" name="codigo[]" class="codigo_producto" />
                                <input type="hidden" id="porcentaje_{{ $count }}" name="porcentaje[]" value="{{ $row->impuestos->impuesto->tarifa }}" class="codigo_producto" />

                                <select id="nombre_{{ $count }}" name="nombre[]" onchange="asignarCodigo($count)" class="form-control select2 productos" style="width:100%" required>
                                    <option value=""> </option>
                                </select>
                                <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                <input type="hidden" value="{{ $row->impuestos->impuesto->valor }}" id="iva_{{ $count }}" name="iva[]" class="iva" />
                            </td>
                            <td>
                                <input class="form-control text-right cneto" onblur="calcular();" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{ $row->cantidad }}" id="cantidad_{{ $count }}" name="cantidad[]" required>
                                <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                    <option value=""> </option>
                                    {{ $selectBodegas }}
                                </select>
                            </td>
                            <td id="tprecio" style="max-width:100px;">
                                <input type="text" class="form-control pneto" style="width: 80%;height:20px;" id="precio_{{ $count }}" name="precio[]" placeholder="0.00" value="{{ $row->precioUnitario }}">
                            </td>
                            <td>
                                <input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" id="pordescuento_{{ $count }}" name="descpor[]" required>
                                <input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                            </td>
                            <td>
                                <input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" id="descuento_{{ $count }}" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{ $row->descuento }}" name="desc[]" required>
                            </td>
                            <td>
                                <input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="{{ $row->precioTotalSinImpuesto }}" onblur="this.value=parseFloat(this.value).toFixed(2);" id="total_{{ $count }}" name="precioneto[]" required>
                            </td>
                            <td>
                                <input class="form" type="checkbox" style="width: 80%;height:20px;" {{ $cheiva }} name="valoriva[]" disabled>
                            </td>
                            <td>
                                <button id="eliminar1" type="button" class="btn btn-danger btn-gray delete">
                                    <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        <tr style="display:none" id="mifila">
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal 12%</td>
                            <td id="subtotal_12_" class="text-right px-1">0.00</td>
                            <input type="hidden" name="subtotal_12" id="subtotal_12" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal 0%</td>
                            <td id="subtotal_0_" class="text-right px-1">0.00</td>
                            <input type="hidden" name="subtotal_0" id="subtotal_0" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Descuento</td>
                            <td id="descuentoTotal_" class="text-right px-1">0.00</td>
                            <input type="hidden" name="descuentoTotal" id="descuentoTotal" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Subtotal</td>
                            <td id="base" class="text-right px-1">0.00</td>
                            <input type="hidden" name="base_" id="base_" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                            <td id="tarifa_iva" class="text-right px-1">0.00</td>
                            <input type="hidden" name="tarifa_iva_" id="tarifa_iva_" class="hidden">
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td id="totalPagar" class="text-right px-1">0.00</td>
                            <input type="hidden" name="totalPagar_" id="totalPagar_" class="hidden">
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
        <div class="col-3" style="text-align: center;">
            <div class="row">
                <a class="btn btn-success btn-gray" id="btncerrarModal" style="margin-left: 3px;" href="javascript:;">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Cerrar
                </a>
                <a class="btn btn-success btn-gray" href="javascript:crear_compra()" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                </a>
            </div>
        </div>
    </div>
</form>
<div class="modal fade" id="crear_proveedor" data-keyboard="false" tabindex=null role="dialog"
    aria-labelledby="myModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".select2").select2();
        $(".productos").select2({
            noResults: function() {
                return "No hay resultado";
            },
            searching: function() {
                return "Buscando..";
            },
            placeholder: "--Seleccione--",
            minimumInputLength: 3,
            ajax: {
                url: "route('compras.buscarProducto')",
                type: "get",
                dataType: json,
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(response) {
                    if (response.length == 0) {
                        return {
                            results: [{
                                id: 0,
                                text: 'El producto que busca no existe'
                            }]
                        };
                    } else {
                        return {
                            results: response
                        };
                    }
                },
                cache: true
            }
        });
        calcular();
    });
    const cargarSucursal = (e) => {
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_sucur': e.value
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (id_sucursal != 0) {
                        $("#p_emision").empty();
                        $.each(data, function(key, registro) {
                            var select = '';
                            if (registro.codigo_caja == '100')
                                select = 'selected';
                            $("#p_emision").append('<option value=' + registro.codigo_caja + ' ' + select + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
                        });
                    } else {
                        $("#p_emision").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>