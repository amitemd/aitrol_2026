<style>

</style>
<div class="modal-content" style="overflow-y: scroll;" id="myModal">
    <div class="modal-header">
        <input type="hidden">
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">{{ trans('contableM.gestionar_documentos')}}</h3>
    </div>
    <div class="modal-body">
        <div class="box-body">
            @php
            $fechaEmision = date('Y-d-m', strtotime($comprobante->infoFactura->fechaEmision));
            $mes = date('m');
            $anio = date('Y');
            @endphp
            <table width="100%">
                <tr>
                    <td>{{ trans('contableM.clave_consultada')}}:</td>
                    <td> {{ $result->RespuestaAutorizacionComprobante->claveAccesoConsultada }}</td>
                </tr>
                <tr>
                    <td>{{ trans('contableM.estado')}}:</td>
                    <td> {{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->estado }}</td>
                </tr>
                <tr>
                    <td>{{ trans('contableM.ambiente')}}:</td>
                    <td> {{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->ambiente }}</td>
                </tr>
                <tr>
                    <td>{{ trans('contableM.fechaautoriza')}}:</td>
                    <td> {{ date('Y-m-d H:i:s', strtotime($result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->fechaAutorizacion)) }}</td>
                </tr>
                <tr>
                    <td>{{ trans('contableM.numauto')}}:</td>
                    <td> {{ $result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion }}</td>
                </tr>
            </table>
            <form class="form-vertical " id="compras_formulario" role="form" method="post">
                {{ csrf_field() }}
                <input id="electronica" type="hidden" name="electronica" value="1" />
                <div class="col-md-2 text-left">
                    <fieldset>
                        <legend class="legend">Periodo</legend>
                        <div style="display: flex; justify-content: space-around;">
                            <input style="width: 50%;" id="periodo_mes" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'mes')" type="text" class="form-control" value="{{$mes}}">
                            <input style="width: 50%;" id="periodo_anio" onkeypress="return isNumberKey(event)" onchange="validarPeriodo(this, 'anio')" type="text" class="form-control" value="{{$anio}}">
                        </div>
                    </fieldset>
                </div>
                <div class="box box-solid">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-sm-9 col-6">
                                    <div class="box-title"><b>{{ trans('contableM.factura_compra') }}</b></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="header row">
                            <div class="col-md-12">
                                <div class="header row">
                                    <div class="col-xs-3 col-3 px-0">
                                        <label class=" label_header">{{ trans('contableM.tipo_comprobante') }}</label>
                                        <select onchange="cambioTipo(this)" name="tipo_comprobante" id="tipo_comprobante" class="form-control select2" style="width: 100%;heigth: 22px">
                                            <option value="">{{ trans('contableM.seleccione') }}...</option>
                                            @if(count($tipo_comprobante)>0)
                                            @foreach ($tipo_comprobante as $comp)
                                            <option {{ $comp->codigo == $docCod ? 'selected' : '' }} value="{{$comp->codigo}}"> {{ $comp->codigo}} {{ $comp->nombre }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-3 px-1">
                                        <label class=" label_header">{{ trans('contableM.creditotributario') }}</label>
                                        <select name="credito_tributario" id="credito_tributario" class="form-control select2" style="width: 100%; heigth: 22px">
                                            <option value="">{{ trans('contableM.seleccione') }}...</option>
                                            @if(count($credito_tributario)>0)
                                            @foreach($credito_tributario as $cred)
                                            <option value="{{$cred->codigo}}">{{$cred->codigo}} {{ $cred->nombre }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    @if (isset($iva_param))
                                    <input type="text" name="ivareal" id="ivareal" class="hidden" value="{{$iva_param->iva}}">';
                                    @endif
                                    <div class="col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.fechafacturacion') }}</label>
                                        <div class="input-group col-md-12">
                                            <input id="fecha" type="date" class="form-control col-md-12" name="fecha" onchange="swetAlertDate()" value="{{ $fechaEmision }}">
                                            <input id="f_autorizacion" type="hidden" name="f_autorizacion" value="{{ $fechaEmision }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.termino') }}</label>
                                        <select onchange="sumarFecha(this)" class="form-control select2" name="termino" id="termino" class="form-control ">
                                            <option value="">{{ trans('contableM.seleccione') }}...</option>
                                            @if(count($termino)>0)
                                            @foreach ($termino as $value)
                                            <option value="{{$value->id}}">{{ $value->nombre }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.fechacaducidad') }}</label>
                                        <div class="input-group col-md-12">
                                            <input id="f_caducidad" type="date" class="form-control   col-md-12" name="f_caducidad" value=" {{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="label_header">Id</label>
                                        <div class="input-group">
                                            <input id="proveedor" type="text" class="form-control  " name="proveedor" value=" {{$comprobante->infoTributaria->ruc}} " onchange="cambiar_proveedor()">
                                            <div class="input-group-addon ">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById(proveedor).value = ''; cambiar_proveedor()"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-4 px-1">
                                        <label class=" label_header">{{ trans('contableM.proveedor') }}</label>
                                        <select class="form-control select2" style="width: 100%;" onchange="cambiar_nombre_proveedor(); llenarCampo()" name="nombre_proveedor" id="nombre_proveedor">
                                            <option value="">{{ trans('contableM.seleccione') }}...</option>
                                            @php
                                            $proveedor = Sis_medico\Proveedor::find($comprobante->infoTributaria->ruc);
                                            @endphp
                                            @if(count($proveedores) > 0)
                                            @foreach ($proveedores as $row)
                                            @if($proveedor!='')
                                            <option {{ $row->id == $proveedor->id ? 'selected' : '' }} value="{{$row->id}}">{{ $row->razonsocial }} </option>
                                            @else
                                            <option value="{{$row->id}}">{{ $row->razonsocial }} </option>
                                            @endif
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xs-6 px-1">
                                        <label class=" label_header">{{ trans('contableM.direccion') }} </label>
                                        <div class="input-group">
                                            <input id="direccion_proveedor" type="text" class="form-control" name="direccion_proveedor" value="{{ $comprobante->infoTributaria->dirMatriz }} ">
                                            <div class="input-group-addon ">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById(direccion_proveedor).value = ''; cambiar_nombre_proveedor()"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row  ">
                                    <div class="col-xs-6 px-1">
                                        <label class=" label_header">{{ trans('contableM.autorizacion') }}</label>
                                        <div class="input-group">
                                            <input id="autorizacion" type="text" class="form-control" name="autorizacion" value="{{$result->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion}}">
                                            <div class="input-group-addon ">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" value=""></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.serie') }}</label>
                                        <div class="input-group">
                                            <input id="serie" maxlength="25" type="text" class="form-control" value="{{ $comprobante->infoTributaria->estab }}  - {{ $comprobante->infoTributaria->ptoEmi }} " name="serie" onkeyup="agregar_serie()">
                                            <div class="input-group-addon ">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" value=""></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.secuenciafactura') }}</label>
                                        <div class="input-group">
                                            <input id="secuencia_factura" maxlength="30" type="text" class="form-control" value=" {{$comprobante->infoTributaria->secuencial}} " name="secuencia_factura">
                                            <div class="input-group-addon ">
                                                <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById(secuencia_factura).value = '';"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class=" label_header">{{ trans('contableM.tipo') }}</label>
                                        <select class="form-control select2" style="width: 100%;" name="tipo" id="tipo">
                                            <option value="">{{ trans('contableM.seleccione') }}...</option>
                                            <option value="1">FACT-COMPRA</option>
                                            <option value="2">COM-FACT</option>
                                            <option value="3">COM-ACTIVO-FIJO</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="id_empresa" id="id_empresa" value="{{ $empresa->id }}">
                                    <!--Punto de Emision-->
                                    <input type="hidden" name="sucursal_final" id="sucursal_final">
                                </div>
                            </div>
                            <div class="col-md-8 px-1">
                                <label class=" label_header">{{ trans('contableM.concepto') }}</label>
                                <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion">
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="label_header">{{ trans('contableM.sucursal') }} ({{ trans('contableM.empresa') }})</label>
                                <select name="sucursal" id="sucursal" class="form-control" onchange="cargarSucursal(this)">
                                    <option value="">{{ trans('contableM.seleccione') }}...</option>
                                    @foreach ($sucursales as $value)
                                    <option value="{{$value->id}}">{{ $value->codigo_sucursal }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 px-1">
                                <label class=" label_header">{{ trans('contableM.pemision') }} ({{ trans('contableM.empresa') }})</label>
                                <select name="p_emision" id="p_emision" class="form-control">
                                    <option value="">{{ trans('contableM.seleccione') }}...</option>
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
                                        <th width="35%" class="" tabindex="0">{{ trans('contableM.Descripcion') }}</th>
                                        <th width="35%" class="" tabindex="0">{{ trans('contableM.producto') }}</th>
                                        <th width="10%" class="" tabindex="0">{{ trans('contableM.cantidad') }}</th>
                                        <th width="10%" class="" tabindex="0">{{ trans('contableM.precio') }}</th>
                                        <th width="10%" class="" tabindex="0">% Desc</th>
                                        <th width="10%" class="" tabindex="0">{{ trans('contableM.descuento') }}</th>
                                        <th width="10%" class="" tabindex="0">{{ trans('contableM.precioneto') }}</th>
                                        <th width="5%" class="" tabindex="0">{{ trans('contableM.iva') }}</th>
                                        <th width="10%" class="" tabindex="0">
                                            <button onclick="nuevo()" type="button" class="btn btn-success btn-gray">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="agregar_cuentas">
                                    @php
                                    $count = 0;
                                    $cheiva = '';
                                    @endphp
                                    @foreach ($comprobante->detalles->detalle as $row)
                                        @if ($row->impuestos->impuesto->tarifa > 0)
                                            @php
                                                $cheiva = 'checked';
                                            @endphp
                                        @endif
                                    <tr class="wello">
                                        <td style="max-width:100px;">
                                            {{ $row->descripcion }}
                                        </td>
                                        <td style="max-width:100px;">
                                            <input type="hidden" id="codigo_{{ $count }}" name="codigo[]" class="codigo_producto" />
                                            <input type="hidden" id="porcentaje_{{ $count }}" name="porcentaje[]" value="{{ $row->impuestos->impuesto->tarifa }}" class="codigo_producto" />
                                            <select id="nombre_{{ $count }}" name="nombre[]" onchange="asignarCodigo({{$count}})" class="form-control select2 productos" style="width:100%" required>
                                                <option value=""> </option>
                                                @if(count($productos)>0)}
                                                @foreach($productos as $producto)
                                                <option value="{{$producto->nombre}}" data-name="{{$producto->nombre}}" data-codigo="{{$producto->codigo}}" data-descuento="{{$producto->mod_desc}}" data-precio="{{$producto->mod_precio}}" data-maxdesc="{{$producto->descuento}}" data-iva="{{$producto->iva}}">{{$producto->codigo}} | {{$producto->nombre}}</option>
                                                @endforeach
                                                @endif

                                            </select>
                                            <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                            <input type="hidden" value="{{ $row->impuestos->impuesto->valor }}" id="iva_{{ $count }}" name="iva[]" class="iva" />
                                        </td>
                                        <td>
                                            <input class="form-control text-right cneto" onblur="calcular()" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="{{ $row->cantidad }}" id="cantidad_{{ $count }}" name="cantidad[]" required>
                                            <select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required>
                                                <option value=""> </option>
                                                @if(count($bodegas) > 0)
                                                @foreach ($bodegas as $value)
                                                <option value="{{$value->id}}">{{ $value->nombre }}</option>
                                                @endforeach
                                                @endif
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
                                    @php
                                    $count++;
                                    @endphp
                                    @endforeach
                                </tbody>
                                <tfoot class="">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right">{{ trans('contableM.subtotal12') }}%</td>
                                        <td id="subtotal_12_" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="subtotal_12" id="subtotal_12" class="hidden">
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right">{{ trans('contableM.subtotal0') }}%</td>
                                        <td id="subtotal_0_" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="subtotal_0" id="subtotal_0" class="hidden">
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right">{{ trans('contableM.descuento') }}</td>
                                        <td id="descuentoTotal_" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="descuentoTotal" id="descuentoTotal" class="hidden">
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right">{{ trans('contableM.subtotal') }}</td>
                                        <td id="base" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="base_" id="base_" class="hidden">
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right">{{ trans('contableM.tarifa') }} Iva 12%</td>
                                        <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="tarifa_iva_" id="tarifa_iva_" class="hidden">
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2" class="text-right"><strong>{{ trans('contableM.total') }}</strong></td>
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
                            <a class="btn btn-success btn-gray" id="btncerrarModal" onclick="cerrar();" style="margin-left: 3px;" href="javascript:;">
                                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ trans('contableM.cerrar') }}
                            </a>
                            <a class="btn btn-success btn-gray" onclick="crear_compra();" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal fade" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModal" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" id="myModalProveedor">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="sweetalert2.all.min.js"></script>
<script>
// 
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
                url: '{{route("compras.buscarProducto")}}',
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
        //calcular();
    });

    function asignarCodigo(num) {
        if ($('#nombre_' + num).val() != '')
            $('#codigo_' + num).val($('#nombre_' + num).val());
    }

    function cerrar(){
        $("#myModal").modal('hide');
    }

    function crear_compra() {
        console.log($('#compras_formulario').serialize());
        //$("#boton_guardar").attr("disabled", "disabled");
        let boton = document.getElementById('boton_guardar');
        boton.style.display = "none";
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var observacion = $("#observacion").val();
        var proveedor = $("#proveedor").val();
        var op_compra = $("#o_compra").val();
        var fecha_caducidad = $("#f_caducidad").val();
        var credito_tributario = $("#credito_tributario").val();
        var tipo_comprobante = $("#tipo_comprobante").val();
        var secuencia_factura = $("#secuencia_factura").val();
        var formulario = document.forms["compras_formulario"];
        //var observacion = formulario.observacion.value;
        //var credito_tributario = formulario.credito_tributario.value;
        //var proveedor = formulario.proveedor.value;
        var termino = $("#termino").val();
        //var secuencia = formulario.secuencia_factura.value;
        var secuencia = $("#secuencia_factura").val();
        var nro_autorizacion = $("#autorizacion").val();
        //var tipo_comprobante = formulario.tipo_comprobante.value;
        var fecha = $("#fecha").val();
        var f_caducidad = $("#f_caducidad").val();
        //var secuencia_factura = formulario.secuencia_factura.value;
        var serie = $("#serie").val();
        var totaliva = $("#tarifa_iva1").val();
        var msj = "";
        if (observacion == "") {
            msj += "trans('contableM.porfallenecamobserva')<br/>";
        }
        if (termino == "") {
            msj += "trans('contableM.porfallenetermino') <br/>";
        }
        if (credito_tributario == "") {
            msj += "trans('contableM.ingrecredittribu')<br/>";
        }
        if (secuencia == "" && tipo_comprobante != '03') {
            msj += "trans('contableM.porfallenecampsecuencia')<br/>";
        }
        if (tipo_comprobante == "") {
            msj += "trans('contableM.porfallencamptipocom')<br/>";
        }
        if (fecha == "") {
            msj += "trans('contableM.porfallenefechafac')<br/>";
        }
        if (f_caducidad == "") {
            msj += "trans('contableM.porfallenefechacadufact')<br/>";
        }
        if (secuencia_factura == "" && tipo_comprobante != '03') {
            msj += "trans('contableM.porfallenesecuencfact')<br/>";
        }
        if (serie == "" && tipo_comprobante != '03') {
            msj += "trans('contableM.porfalleneseriefac')<br/>";
        }
        if (nro_autorizacion == "" && tipo_comprobante != '03') {
            msj += "Por favor, Llene campo de número de autorización<br/>";
        }
        if ((nro_autorizacion.length != 10 && nro_autorizacion.length != 49) && tipo_comprobante != '03') {
            msj += "Por favor, El número de autorización debe contener 10 o 49 dígitos<br/>";
        }
        if ($('#sucursal').val() == '' && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione el la sucursal del la empresa<br/>";
        }
        if ($('#p_emision').val() == '' && tipo_comprobante != '03') {
            msj += "Por favor, Seleccione el punto de emision de la empresa<br/>";
        }
        let obsDetalle = document.getElementsByName("descrip_prod[]");
        let obsBodega = document.getElementsByName("bodega[]");
        let array = [];
        let contador = obsDetalle.length;
        for (let index = 0; index < contador; index++) {
            if (tipo_comprobante == '03') {
                if (obsBodega[index].value == '') {
                    array.push(' Ingrese la bodega ' + Number(index + 1));
                }
            }
        }
        if (array.length > 0) {
            msj += array.join(' ');
        }
        if (msj != "") {
            boton.style.display = "initial"
        }
        if (msj == "") {
            if (!validarFechasPeriodo()) {
                boton.style.display = "initial"
                alertas("error", "Error en fechas", "La fecha del periodo con la fecha de facturacion no coinciden")
                return;
            }

          
            if ($("#compras_formulario").valid()) {
                $.ajax({
                    type: 'post',
                    url: "{{route('comprasri_store')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#compras_formulario').serialize(),
                    success: function(data) {
                        if (data != `{{trans('contableM.coincidenfac')}}`) {
                            if (data.errores == 'si') {
                                boton.style.display = "initial";
                                Swal.fire({
                                    title: "Error!",
                                    type: "error",
                                    html: data.msj
                                });
                                return;
                            }
                            secuencia_f(data);
                            if (confirm('¿Desea agregar retenciones?')) {
                                $.ajax({
                                    type: "get",
                                    url: "{{route('retenciones_modal_retenciones')}}",
                                    data: {
                                        'id_proveedor': proveedor,
                                        'secuencia': secuencia_factura,
                                        'id_compra': data[0],
                                        'total_iva': totaliva
                                    },
                                    datatype: "html",
                                    success: function(datahtml, data) {
                                        $("#content").html(datahtml);
                                        // $("#boton_guardar").attr("disabled", "disabled");
                                    },
                                    error: function() {
                                        alert('error al cargar');
                                    }
                                });
                            } else {
                                Swal.fire(`{{trans('contableM.correcto')}}!`, "trans('contableM.facguardacorrec')", "success");
                                $("#id_fc").val(data.id);
                                $("#id_asiento").val(data.id_asiento);
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }
                        } else {
                            Swal.fire("Informe", data, "error");
                            $('#boton_guardar').removeAttr("disabled");
                            boton.style.display = "initial"
                        }
                    },
                    error: function(data) {
                        boton.style.display = "initial"
                        $('#boton_guardar').removeAttr("disabled");
                        Swal.fire("Error!", data, "error");
                    }
                });
            } else {
                $('#boton_guardar').removeAttr("disabled");
                boton.style.display = "initial";
            }
        } else {
            $('#boton_guardar').removeAttr("disabled");
            boton.style.display = "initial";
            Swal.fire({
                title: "Error!",
                type: "error",
                html: msj
            });
        }
    }
    function validarFechasPeriodo() {
        let periodo_mes = document.getElementById("periodo_mes").value;
        let periodo_anio = document.getElementById("periodo_anio").value;
        let f_autorizacion = document.getElementById("f_autorizacion").value;
        const fechArray = f_autorizacion.split("-");
        let fecha = document.getElementById("fecha").value;
        const fechaArray = fecha.split("-");
        let fechAuto = `${fechArray[0]}-${fechArray[1]}`;
        let fechasAuto = `${fechaArray[0]}-${fechaArray[1]}`;
        let fechPeriodo = `${periodo_anio}-${periodo_mes}`;
        console.log(fechPeriodo,fechasAuto);
        if (fechPeriodo == fechasAuto) {
            return true;
        } else {
            return false;
        }
    }
    function secuencia_f(secuencia_factura) {
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 10) {
                Swal.fire("Error!", "trans('contableM.valornoper')", "error");
                $('#id_fc').val('');
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
                $('#id_fc').val(secuencia + secuencia_factura);
            }
        } else {
            Swal.fire("Error!", "trans('contableM.valornoper')", "error");
            $('#id_fc').val('');
        }
    }
    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    function validarPeriodo(e, type){
        const fecha = new Date();
        const anioActual = fecha.getFullYear();
        const mesActual = fecha.getMonth() + 1;

        if (type == "mes") {
            if (parseInt(e.value) > 12) {
                e.value = mesActual;
                if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                    e.value = "0" + parseInt(e.value);
                }
                alertas("error", "Error en mes", "El mes del periodo no existe")
                return;
            } else if (parseFloat(e.value) <= 0) {
                e.value = mesActual;
                if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                    e.value = "0" + parseInt(e.value);
                }
                alertas("error", "Error en mes", "El mes del periodo no puede ser menor o igual a 0")
                return;
            } else if (parseInt(e.value) >= 1 && parseInt(e.value) <= 9) {
                e.value = "0" + parseInt(e.value);
                return;
            }
        }

        if (type == "anio") {
            if (parseInt(e.value) > 2200) {
                e.value = anioActual;
                alertas("error", "Error en año", "El año del periodo no esta permitido")
                return;
            } else if (parseInt(e.value) < 2015) {
                e.value = anioActual;
                alertas("error", "Error en año", "El año del periodo no esta permitido")
                return;
            }
        }
    }
    function calcularCompra(e) {
        pDescuento(e);
        var cant = $('#cantidad_' + e).val();
        var copago = 0;
        var descuento = $("#descuento_" + e).val();
        var total = (parseInt(cant) * parseFloat($('#precio_' + e).val())) - descuento - copago;
        $("#total_" + e).val(parseFloat(total).toFixed(2));
        
        totales(0);
    }
    function verificatotales() {
        var valor_totalfinal = parseFloat($("#total_final1").val());
        if (isNaN(valor_totalfinal)) {
            valor_totalfinal = 0;
        }
        var valor_credito = parseFloat($("#valor_tarjetacredito").val());
        if (isNaN(valor_credito)) {
            valor_credito = 0;
        }
        var valor_efectivo = parseFloat($("#valor_efectivo").val());
        if (isNaN(valor_efectivo)) {
            valor_efectivo = 0;
        }
        var valor_cheque = parseFloat($("#valor_cheque").val());
        if (isNaN(valor_cheque)) {
            valor_cheque = 0;
        }
        var valor_debito = parseFloat($("#valor_tarjetadebito").val());
        if (isNaN(valor_debito)) {
            valor_debito = 0;
        }
        var totales = valor_credito + valor_efectivo + valor_cheque + valor_debito;
        if ((totales) != NaN) {
            if (totales == valor_totalfinal) {
                return 'ok';
            } else {
                return false;
            }
        }
    }
    $('body').on('change', '.cneto', function() {
        // verificar(this);
        var cant = $(this).val();
        var precio = $(this).parent().next().children().val();
        var copago = 0;
        var descuento = $(this).parent().next().next().next().children().val();
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

        $(this).parent().next().next().next().children().val(total.toFixed(2));

        totales(0);
    });


    $('body').on('change', '.pdesc', function() {

        var m = $(this).next().val();
        var cant = $(this).parent().prev().prev().children().val();
        var precio = $(this).parent().prev().children().val();
        var pdesc = $(this).val();

        var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
        $(this).parent().next().children().val(descuento.toFixed(2));
        var copago = 0;
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().next().children().val(total);
        totales(0);
    });
    $('body').on('change', '.desc', function() {
        var m = verificar(this);
        var cant = $(this).parent().prev().prev().prev().children().val();
        var precio = $(this).parent().prev().prev().children().val();
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
        var copago = 0;
        var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
        total = redondeafinal(total);
        $(this).parent().next().children().val(total);
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
        var descuentosub0 = 0;
        var descuentosub12 = 0;
        var sb12 = 0;
        var sb0 = 0;
        var d = 0;
        var copagoTotal = 0;
        if (e == 0) {
            $('.cneto').each(function(i, obj) {
                var cant = $(this).val();
                var precio1 = 0;
                var precio2 = 0;
                var precio3 = 0;
                var precio4 = 0;
                var precio5 = 0;
                var precioAut = 0;
                var precio = $(this).parent().next().children().val();
                var copago = 0;
                var descuento = $(this).parent().next().next().next().children().val();
                d = parseFloat(d) + parseFloat(descuento);
                var iva = $(this).parent().next().next().next().next().next().children().prop('checked');

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
                $("#base1").val(sum);
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
                $("#total").html(totax.toFixed(2, 2));
                $("#copagoTotal").html(copagoTotal.toFixed(2));
                sb12 = redondeafinal(sb12);
                $("#subtotal_121").val(sb12);
                sb0 = redondeafinal(sb0);
                $("#subtotal_01").val(sb0);
                d = redondeafinal(d);
                $("#descuento1").val(d);
                $("#tarifa_iva1").val(ti);
                $("#total1").val(totax);
                $("#totalc").val(copagoTotal.toFixed(2));
            });
        }
    }
</script>