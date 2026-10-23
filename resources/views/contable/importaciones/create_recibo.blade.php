@extends('contable.compras_pedidos.base')
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

    /* CSS CARGANDO */
    .loader-wrapper {
        width: 220px;
        height: 220px;
    }

    .swal2-container {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-direction: row;
        flex-direction: row;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 10px;
        background-color: transparent;
        z-index: 1060;
    }

    .loader {
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        border: 34px solid #162534;
        border-top-color: #4bc8eb;
        border-bottom-color: #f13a8f;
        border-radius: 50%;
        animation: rotate 5s linear infinite;
    }

    .loader-inner {
        border-top-color: #36f372;
        border-bottom-color: #fff;
        animation-duration: 2.5s;
    }

    @keyframes rotate {
        0% {
            transform: scale(1) rotate(360deg);
        }

        50% {
            transform: scale(.8) rotate(-360deg);
        }

        100% {
            transform: scale(1) rotate(360deg);
        }
    }

    .cargando {
        text-align: center;
        color: white;
        font-size: 38px;
        text-transform: uppercase;
        font-weight: 700;
        font-family: inherit;
    }

    /* FIN CSS CARGANDO */
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<style>
    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }
</style>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.COMPRA')}}</a></li>
            <li class="breadcrumb-item"><a href="../compras">{{trans('contableM.regisimporta')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.newrecibodeimporta')}}</li>
        </ol>
    </nav>
    <!-- ANIMACION DE CARGANDO-->
    <div id="cargando" class="swal2-container swal2-center swal2-backdrop-show" style="overflow-y: auto; display:none;" >
        <div id="cargando_hijo" class="loader-wrapper">
            <div class="loader">
                <div class="loader loader-inner"></div>
            </div>
            <div class="cargando">{{trans('contableM.guardando')}}...</div>
        </div>
    </div>
    <!-- FIN DE ANIMACION DE CARGANDO-->
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b>{{trans('contableM.recibodecompra-')}}</b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('importaciones.index')}}">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12 col-xs-12 px-1">
                        <label class="">{{trans('contableM.buscadordeordenimporta')}}</label>
                        <br>
                        <select id="selecOrden" style="width:50%!important" class="form-control select2_cuentas select2-hidden-accessible" class="form-control">
                            <option value="">{{trans('agenda.seleccione')}}...</option>
                            @foreach($cab->cruce as $value)
                            @foreach($value->compra->detalles as $det)
                            <option data-proveedor="{{$value->compra->proveedor}}" data-direccion="{{$value->compra->direccion_proveedor}}" data-gasto="{{$det->id_gasto}}" data-precio="{{$det->precio}}" data-fecha="{{$value->compra->fecha}}" data-descuento="{{$det->descuento}}" data-total="{{$det->total}}" value="{{$value->compra->id}}" data-neto="{{$value->compra->total_final}}">{{$value->compra->proveedor_da->nombrecomercial}} | {{$cab->observacion}}
                            </option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="header row">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="form-group col-xs-7  col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <select class="form-control validar" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                        <option value="">{{trans('agenda.seleccione')}}...</option>
                                        @foreach ($sucursales as $value)
                                        <option value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="form-group col-xs-6  col-md-2  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}} ({{trans('contableM.empresa')}})</label>
                                    <input type="hidden" id="electronica" name="electronica" value="0">
                                </div>
                                <div class="col-md-12 px-0">
                                    <select class="form-control validar" name="punto_emision" id="punto_emision" required>
                                        <option value="">{{trans('agenda.seleccione')}}...</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.proveedor')}}</label>
                                <select class="form-control select2_proveedor validaciones" style="width: 100%;" onchange="buscarDireccion();" name="proveedor" id="proveedor">

                                </select>
                            </div>
                            <div class="col-md-3 col-xs-3 px-1">
                                <label class=" label_header">{{trans('contableM.direccion')}}</label>
                                <div class="input-group">
                                    <input id="direccion_proveedor" type="text" class="form-control validar" name="direccion_proveedor">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-1 px-1">
                                <label class=" label_header">{{trans('contableM.fechapedido')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="f_autorizacion" type="date" class="form-control col-md-12" name="f_autorizacion" onblur="validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>





                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row  ">

                            <div class="col-md-2 col-xs-1 px-1">
                                <label class=" label_header">{{trans('contableM.asiento')}}</label>
                                <div class="input-group col-md-12">
                                    <input id="id_asiento" type="text" class="form-control col-md-12" readonly name="id_asiento" value="">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.serie')}}</label>
                                <div class="input-group">
                                    <input id="serie" maxlength="25" type="text" class="form-control validar " name="serie" onblur="agregar_serie()" onkeyup="agregar_serie()" autocomplete="off">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie').value = '';"></i>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-2 col-xs-2 px-1">
                                <label class=" label_header">{{trans('contableM.SecuenciaPedido')}}</label>
                                <div class="input-group">
                                    <input id="secuencia_factura" maxlength="30" type="text" class="form-control validar " name="secuencia_factura" onchange="ingresar_cero()" autocomplete="off">
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                            <input type="text" name="ivareal" id="ivareal" class="hidden" value="0.12">
                            <div class="col-md-6 px-1">
                                <label class=" label_header">{{trans('contableM.concepto')}}</label>
                                <input autocomplete="off" type="text" class="form-control col-md-12 validar" name="observacion" id="observacion">
                            </div>
                            <input type="hidden" name="id_empresa" id="id_empresa" value="{{$empresa->id}}">
                            <input type="hidden" name="sucursal_final" id="sucursal_final">
                        </div>
                    </div>
                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input name='contador_items' id='contador_items' type='hidden' value="1">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <input type="hidden" name="id_importacion" value="{{ $id_importacion }}">
                        <thead>
                            <tr>
                                <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                                <th width="25%" class="" tabindex="0">{{trans('contableM.DescripciondelProducto')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="20%" class="" tabindex="0">{{trans('contableM.precio')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="15%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.precioneto')}}</th>
                                <th width="20%" class="" tabindex="0">
                                    <button onclick="crearFila()" type="button" class="btn btn-success btn-gray">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            <!-- Se crean  -->
                        </tbody>
                        <tfoot class=''>
                            <!-- <tr>
                                <td colspan="3"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal12')}}%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal0')}}%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr> -->
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <!-- <tr>
                                <td colspan="3"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr> -->
                            <!--<tr>
                            <td></td><td></td><td></td><td></td><td></td>
                            <td colspan="2" class="text-right">Transporte</td>
                            <td id="transporte" class="text-right px-1">0.00</td>
                            <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                        </tr>-->
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <!-- <tr>
                                <td colspan="2"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr> -->
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardarImportacion(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>s

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
    $('.select2_proveedor').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("importaciones.proveedores")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    const crearFila = () => {
        var id = document.getElementById('contador_items').value;
        let fila = `
                <tr >
                                <td style="max-width:100px;">
                                    <Input id="codigo${id}" type="hidden" name="codigo[]" class="codigo_producto" />
                                    <select id="producto${id}" name="producto[]" class="form-control select2_cuentas validaciones" style="width:100%" required onchange="verificar(this)">
                                        <option> </option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->id}}" >{{$value->nombre}}</option>
                                        @endforeach

                                    </select>
                                    <textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea>
                                    <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad[]" required>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="precio${id}" value="0.00" type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required>
                                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required>
                                </td>
                                <td>
                                    <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required>
                                </td>
                                <td>
                                    <input readonly onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" required>
                                </td>
                               
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });
        id++;
        document.getElementById('contador_items').value = id;
    }

    const buscarDireccion = () => {
        console.log("Buscar Direccion")
        let proveedor = document.getElementById('proveedor').value;
        $.ajax({
            type: 'get',
            url: "{{route('importaciones.direccion')}}",
            datatype: 'json',
            data: {
                'proveedor': proveedor
            },
            success: function(data) {
                if (data.status == "success") {
                    document.getElementById('direccion_proveedor').value = data.direccion;
                }
            },
            error: function(data) {

            }
        })
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)

        let preXcantidad = (precio * cantidad)
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        desc.value = porcDescuento.toFixed(2)
        precioneto.value = precioTotal.toFixed(2);
        totalGlobal();

    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let total = 0;
        let descuento = 0;
        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value)
            descuento += parseFloat(desc[i].value)
        }

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

        document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('base1').value = parseFloat(total).toFixed(2);

        document.getElementById('total').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('total1').value = parseFloat(total).toFixed(2);
    }

    const cargando = accion => {
        let divCargando = document.getElementById("cargando");
        // let boton = document.getElementById("boton_guardar");
        // if (accion == 1) {
        //     let content = `<div id="cargando_hijo" class="loader-wrapper"> 
        //                         <div class="loader">
        //                             <div class="loader loader-inner"></div>
        //                         </div>
        //                         <div class="cargando">{{trans('contableM.guardando')}}...</div>
        //                     </div>`;


        //     divCargando.classList.add("swal2-container", "swal2-center", "swal2-backdrop-show")
        //     // boton.style.display="none";
        //     $('#cargando').append(content);
        // } else {
        //     let hijo = document.getElementById("cargando_hijo");
        //     divCargando.removeChild(hijo)
        //     divCargando.classList.remove("swal2-container", "swal2-center", "swal2-backdrop-show")
        //     // boton.style.display="initial";
        // }

        
        if (accion == 1) {
            divCargando.style.display = "flex";
        } else {
            divCargando.style.display = "none";
        }
    }

    const validarProducto = () => {
        let validacion = document.querySelectorAll('.validaciones')
        let validar = false;

        for (let i = 0; i < validacion.length; i++) {
            let spans = validacion[i].parentElement.children[2];
            if (validacion[i].value == '') {
                spans.style.border = '2px solid #CD6155';
                spans.style.borderRadius = '4px';
                validar = true;
            } else {
                spans.style.border = '1px solid #d2d6de';
                spans.style.borderRadius = '0px';
            }
        }
        return validar;
    }


    function guardarImportacion(e) {
        e.preventDefault();
        //cargando(1);
        let btn = document.getElementById('boton_guardar');
        btn.style.display = "none";
        if (!validar_campos()) {
            if (!validarProducto()) {
                var formulario = document.forms["crear_factura"];
                var observacion = formulario.observacion.value;
                var proveedor = formulario.proveedor.value;
                var secuencia = formulario.secuencia_factura.value;
                //var serie = formulario.serie.value;
                var msj = "";
                if (observacion == "") {
                    msj += "{{trans('contableM.porfallenecamobserva')}}<br/>";
                }
                if (secuencia == "") {
                    msj += "{{trans('contableM.porfallenecampsecuencia')}}<br/>";
                }
                if (msj != "") {
                    // cargando(0);
                    alertas('error', 'Error!..', msj)
                    btn.style.display = "initial"
                } else {
                    $.ajax({
                        type: 'post',
                        url: "{{route('importaciones.store_recibo')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        datatype: 'json',
                        data: $('#crear_factura').serialize(),
                        success: function(data) {
                          
                                if(data.respuesta == "success") {
                                    alertas(data.respuesta, data.titulos, data.msj)
                                    document.getElementById('id_asiento').value = data.id_asiento;
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 2000)
                                }else{
                                    alertas(data.respuesta, data.titulos, data.msj)
                                    btn.style.display = "initial";
                                }
                               
                            
                        },
                        error: function(data) {
                            btn.style.display = "initial";
                            // cargando(0)
                        }
                    });
                }
            } else {
                // cargando(0);
                btn.style.display = "initial";
                $('#boton_guardar').prop("disabled", false);
                alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
            }
        } else {
            // cargando(0);
            btn.style.display = "initial";
            $('#boton_guardar').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    var atr = document.getElementById("selecOrden");

    atr.onchange = function(event) {
        var datas = event.target.options[event.target.selectedIndex].dataset;
        var proveedor = datas.proveedor;
        var precio = datas.precio;
        var id_gasto = datas.gasto;
        var direccion = datas.direccion;
        var neto = datas.neto;

        $("#proveedor").val(proveedor);
        $("#proveedor").change();

        $("#producto1").val(id_gasto);
       
        // $(".select2_cuentas").trigger("changue");

        $("#precio1").val(datas.precio)
        $(`#producto1 option[value='${id_gasto}']`).prop('selected', true).trigger('change');

        $("#direccion_proveedor").val(direccion);
        $("#direccion_proveedor").change();

        $("#precioneto1").val(datas.neto);
        $("#precioneto1").change();
        document.getElementById("total").innerHTML = datas.total;


    };

    function obtener_caja() {

        var id_sucursal = $("#sucursal").val();

        $.ajax({
            type: 'post',
            url: "{{ route('caja.sucursal') }}",
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
                            $("#punto_emision").append('<option value=' + registro.id + '>' +
                                registro.codigo_sucursal + '-' + registro.codigo_caja +
                                '</option>');

                        });
                    } else {
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data) {

            }
        })

    }
</script>

<script type="text/javascript">
    var fila = $("#mifila").html();
    $(document).ready(function() {

        limpiar();

        crearFila();

        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });



        $('.iva').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

        $('.ice').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });
    $('.smr').on('ifChecked', function(event) {
        $("#archivosri").val(1);
    });
    $('.smr').on('ifUnchecked', function(event) {
        $("#archivosri").val(0);
    });

    function verificar(e) {
        var iva = $('option:selected', e).data("iva");
        var codigo = $('option:selected', e).data("codigo");
        var usadescuento = $('option:selected', e).data("descuento");
        var max = $('option:selected', e).data("maxdesc");
        var modPrecio = $('option:selected', e).data("precio");

        $(e).parent().children().closest(".codigo_producto").val(codigo);
        $(e).parent().children().closest(".iva").val(iva);
        //
        /*
        if (modPrecio) {
            //$(e).parent().next().next().closest(".cp");
            //
            $(e).parent().next().next().children().find(".cp").removeAttr("disabled");
        } else {
            //
            $(e).parent().next().next().children().find(".cp").attr("disabled", "disabled");
        }
        if (!usadescuento) {
            $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
            $(e).parent().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().next().children().val(0);
        } else {
            $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().children().removeAttr("readonly");
            $(e).parent().next().next().next().next().next().children().val(0);
            $(e).parent().next().next().next().next().children().val(0);
        }
         $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
        */

        if (iva == '1') {
            $(e).parent().next().next().next().next().next().next().children().attr("checked", "checked");
        } else {
            $(e).parent().next().next().next().next().next().next().children().removeAttr("checked");
        }
    }

    function redondeafinal(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        //
        if (decimales === 0) //con 0 decimales
            return signo * Math.round(num);
        // round(x * 10 ^ decimales)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        // x * 10 ^ (-decimales)
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function agregarElement(data) {
        var id = "";
        var contador = 0;

        for (i = 0; i < data[3].length; i++) {
            //
            var midiv = document.createElement("tr");
            var extendido = parseFloat(data[3][i].precio) * parseFloat(data[3][i].cantidad_total);
            midiv.innerHTML =
                '<tr style="display:none" id="mifila"><td style="max-width:100px;"><input type="hidden" name="codigo[]" class="codigo_producto" /><select  id="f' +
                contador +
                '" name="nombre[]" class="form-control select2s" style="width:100%" required onchange="verificar(this)"><option> </option>@foreach($productos as $value)<option value="{{$value->codigo}}" data-name="{{$value->nombre}}" data-codigo="{{$value->codigo}}"data-descuento="{{$value->mod_desc}}" data-precio="{{$value->mod_precio}}" data-maxdesc="{{$value->descuento}}" data-iva="{{$value->iva}}" >{{$value->codigo}} | {{$value->nombre}}</option>@endforeach</select><textarea rows="3" name="descrip_prod[]" class="form-control px-1 desc_producto" placeholder="Detalle del producto"></textarea><input type="hidden" name="iva[]" class="iva" /></td><td><input class="form-control text-right cneto" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="' +
                data[3][i].cantidad_total +
                '" name="cantidad[]"  required><select name="bodega[]" class="form-control select2_bodega bodega" style="width: 80%;margin-top: 5px;" required ><option> </option>@foreach($bodega as $value)<option value="{{$value->id}}">{{$value->nombre}}</option>@endforeach</select></td><td><input type="text" class="pneto form-control" name="precio[]" style="width: 80%;height:20px;" value="' +
                data[3][i].precio +
                '" placeholder="0.00"></td><td><input class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="descpor[]" required><input class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" required></td><td><input class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" name="desc[]" required></td><td><input class="form-control px-1 text-right" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto[]" value="' +
                extendido +
                '" required></td><td><input class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" disabled></td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>';

            document.getElementById('agregar_cuentas').appendChild(midiv);
            //$(".select2s").val(data[3][i].codigo);
            //$(".select2s").select2().trigger('change');
            $("#f" + contador).val(data[3][i].codigo);
            $("#f" + contador).select2().trigger('change');
            contador++;
        }


        totales(0);

    }

    $('body').on('click', '.delete', function() {
        //

        $(this).parent().parent().remove();
        totales(0);
    });
    $('body').on('click', '.form', function() {
        //

        //$(this).parent().parent().remove();
        totales(0);
    });
    //cantidad
    //precio
    //copago
    //%descuento
    //descuento
    //precioneto



    //new change to round be
    //eduardo me dijo que quemara esas bodegas en totbodega
    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }

    function crea_td(contador) {

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr")
        midiv.setAttribute("id", "dato" + id);
        midiv.innerHTML = `
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigo${id}" class="codigo form-control" id="codigo${id}" onchange="cambiar_codigo(${id})"/>
                        </td>
                        <td>
                            <input   style=" width: 100%; height: 80%;" name="codigoref${id}" class="form-control" id="codigoref${id}"/>
                        </td> 
                        <td>
                            <input type="hidden" id="visibilidad${id}" name="visibilidad${id}" value="1">
                            <input name="nombre${id}"  class="nombre form-control" id="nombre${id}" onchange="cambiar_nombre(${id})" style="width: 100%; height: 80%;">
                        </td>
                        <td>
                            <select class="form-control select2" id="bodega${id}" required name="bodega${id}" style="width: 100%; height: 78%;"> 
                            @foreach($bodega as $value) 
                                <option value="{{$value->id}}">{{$value->nombre}}</option>  
                            @endforeach
                            </select>
                        </td>
                        <td> 
                            <input class="form-control" style=" width: 100%; height: 80%;" type="text" id="cantidad${id}" required value="0.00" onchange="total_calculo(${id}); redondea_cantidad(this, ${id},2);" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="cantidad${id}"> 
                        </td>
                        <td> 
                            <select class="form-control" id="empaque${id}" name="empaque${id}" style="width: 100%; height: 80%;"> 
                                <option value="unidad">Unidad</option> 
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" id="precio${id}" name="precio${id}" value="0.00" onchange="total_calculo(${id}); validarprecio(this,${id}); " onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" style="width: 100%; height: 80%;">
                        </td>
                        <td> 
                            <input class="form-control" type="text" name="desc_porcentaje${id}" id="desc_porcentaje${id}" onchange="total_calculo(${id})" value="0.00" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="redondea_descuento(this,${id},2);" style="width: 100%; height: 80%;">
                        <td> 
                            <input class="form-control" type="text" name="desc${id}" id="desc${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="desc_${id}" id="desc_${id}" >
                        </td> 
                        <td>
                            <input class="form-control" name="extendido${id}" id="extendido${id}" value="0" style="width: 100%; height: 80%;" disabled>
                            <input type="text" class="hidden" name="extendido_${id}" id="extendido_${id}"> 
                            <input type="hidden" name="ivaver${id}" id="ivaver${id}"> 
                        </td>
                        <td> 
                            <input type="checkbox" id="ice${id}" name="ice${id}" value="1">
                        </td>
                        <td>
                            <button id="eliminar${id}" type="button" onclick="javascript:eliminar_registro(${id})" class="btn btn-danger btn-gray delete btn-xs">
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>`;
        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;
        document.getElementById('contador').value = id;

        $(".nombre").autocomplete({
            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('compra_nombre')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
        });

        $(".codigo").autocomplete({

            source: function(request, response) {
                var id_empresa = $("#id_empresa").val();
                $.ajax({
                    url: "{{route('compra_codigo')}}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        'id_empresa': id_empresa,
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
        });

    }

    function limpiar() {
        $("#datos_tarjeta_credito").hide();
        $("#datos_tarjeta_debito").hide();
        $("#datos_cheque").hide();
        $("#valor_tarjetadebito").val('');
        $("#valor_cheque").val('');
        $("#valor_efectivo").val('');
        $("#valor_tarjetacredito").val('');
        $("#retenciones").val('0.00');
        $("#tabla_rubros").hide();
    }

    function eliminar_registro(valor) {
        var nombre2 = "visibilidad" + valor;
        var dato1 = "dato" + valor;
        $("#visibilidad" + valor).val(0);
        //document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display = 'none';
        suma_totales();
    }

    function crear_compra(e) {
        var valor_total = $('#total_final1').val();
        var val_efe = parseInt($("#valor_efectivo").val());
        var val_che = parseInt($("#valor_cheque").val());
        var val_tarj_cred = parseInt($("#valor_tarjetacredito").val());
        var val_tarj_debi = parseInt($("#valor_tarjetadebito").val());
        var val_cal = verificatotales();
        var formulario = document.forms["crear_factura"];
        var observacion = formulario.observacion.value;
        var proveedor = formulario.proveedor.value;
        var secuencia = formulario.secuencia_factura.value;
        var serie = formulario.serie.value;
        var totaliva = $("#tarifa_iva1").val();
        var msj = "";
        if (observacion == "") {
            msj += "{{trans('contableM.porfallenecamobserva')}}<br/>";
        }
        if (secuencia == "") {
            msj += "{{trans('contableM.porfallenecampsecuencia')}}<br/>";
        }
        if (serie == "") {
            msj += "{{trans('contableM.porfalleneseriefac')}}<br/>";
        }
        if (msj == "") {
            if ($("#crear_factura").valid()) {
                $(e).hide();
                $.ajax({
                    type: 'post',
                    url: "{{route('contable.compra_pedido_store.create')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data) {

                        swal("{{trans('contableM.exito')}}", `{{trans('contableM.Guardadoconexito')}}`, "success");
                        setTimeout(function() {

                            location.href = "{{route('contable.compraspedidos.index')}}";
                        }, 1000);


                    },
                    error: function(data) {

                        swal("Error!", data, "error");
                    }
                });
            }

        } else {
            swal({
                title: "Error!",
                type: "error",
                html: msj
            });
        }

    }

    function goBack() {
        window.history.back();
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

    function ivas(id) {
        $("#ivaver" + id).val(1);
        //
    }

    function validarcantidad(elemento, id) {

        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()
        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Debe ingresar el código", "{{trans('contableM.nomprod')}}", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }
        var num = elemento.value;
        if (num.length == 0) {
            swal("{{trans('contableM.cantidadnopermi')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#cantidad' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            suma_totales();
            return false;
        }


        var numero = parseInt(elemento.value, 10);
        if (numero < -1 || numero > 999999999) {
            swal("{{trans('contableM.cantidadnopermi')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#cantidad' + id).val("0");
            return false;
        }
        return true;
    }

    function validartotal(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("{{trans('contableM.totalnopermi')}}");
            $('#total' + id).val("0");
            return false;
        }
        return true;
    }

    function validarprecio(elemento, id) {

        var cod = $('#codigo' + id).val();
        var nomb = $('#nombre' + id).val()

        if ((cod.length == 0) && (nomb.length == 0)) {
            swal("Error!", "{{trans('contableM.debeingrecodnomcantprodu')}}", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            return false;
        }

        var prec = elemento.value;
        if (prec.length == 0) {

            swal("¡Error!", "{{trans('contableM.ingresedenuevo')}}", "error");
            $('#precio' + id).val("0");
            $('#desc' + id).val("0");
            $('#extendido' + id).val("0");
            total_calculo(id);
            //suma_totales();
            return false;
        }

        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("{{trans('contableM.precionopermi')}}");
            $('#precio' + id).val("0");
            return false;
        }
        return true;
    }

    function validardescuento(elemento, id) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 100) {
            swal("{{trans('contableM.tangdedescuenpermi')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#desc_porcentaje' + id).val("0");
            return false;
        }
        return true;
    }


    function validartransporte(elemento) {
        var numero = parseInt(elemento.value, 10);
        //Validamos que se cumpla el rango
        if (numero < -1 || numero > 999999999) {
            swal("{{trans('contableM.transponopermitido')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#transporte').val("0");
            return false;
        }
        return true;
    }
    $('input[name=numero_debito]').change(function() {
        swal(this);
    });

    function redondea_precio(elemento, id, nDec) {

        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        //alert(decimal);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#precio' + id).val(s);
            }
        } else {
            $('#precio' + id).val(n.toFixed(2, 2));
        }
    }

    function redondea_cantidad(elemento, id, nDec) {

        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#cantidad' + id).val(s);
            } else {
                $('#cantidad' + id).val(n.toFixed(2, 2));
            }
        }
        $('#cantidad' + id).val(s);
    }

    function redondea_descuento(elemento, id, nDec) {
        var n = parseFloat(elemento.value);
        var s;
        var d = elemento.value;
        var en = String(d);
        arr = en.split("."); // declaro el array
        entero = arr[0];
        decimal = arr[1];
        n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
        s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
        s = s.substr(0, s.indexOf(".") + nDec + 1);
        if (decimal != null) {
            if ((decimal.length) > 2) {
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#desc_porcentaje' + id).val(s);
            } else {
                $('#desc_porcentaje' + id).val(n.toFixed(2, 2));
            }
        } else {
            $('#desc_porcentaje' + id).val(s);
        }

    }

    function validar_retenciones(elemento) {
        var numero = parseInt(elemento.value, 10);
        var total_final = parseFloat($('#total_final').val());
        //Validamos que se cumpla el rango
        if (numero < 0 || numero > 999999999) {
            swal("{{trans('contableM.valderetenopermi')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#retenciones').val("0");
            return false;
        }
        if (numero > total_final) {
            swal("{{trans('contableM.nomontosupervalfac')}}", "{{trans('contableM.porfaingresadenuevo')}}", "error");
            $('#retenciones').val("0");
            return false;
        }
        return true;
    }

    function buscar_factura() {
        var contador = parseInt($("#contador").val());
        if (isNaN(contador)) {
            contador = 0;
        }
        if (!isNaN($("#factura_nombre").val())) {
            $.ajax({
                type: 'post',
                url: "{{route('compra_buscar_factura')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'id_factura': $("#factura_nombre").val()
                },
                success: function(data) {
                    //
                    if (data.value != 'no resultados') {
                        if ($("#proveedor").val() != "") {
                            if (data[0] != $("#proveedor").val()) {
                                swal("Error", "{{trans('contableM.ingresounpedideotro')}}");
                            } else {
                                $('#proveedor').val(data[0]);
                            }
                        }
                        //$('#proveedor').val(data[0]);
                        $('#direccion_proveedor').val(data[4]);
                        $('#fecha').val(data[1]);
                        $('#f_caducidad').val(data[2]);
                        $('#id_empresa').val(data[6]);
                        for (var i = 0; i < data[3].length; i++) {
                            crea_td(i);
                            $('#codigo' + contador).val(data[3][i].codigo);
                            $('#nombre' + contador).val(data[3][i].nombre);
                            /* $('#bodega'+i).val(data[3][i].id_bodega);*/
                            $('#cantidad' + contador).val(data[3][i].cantidad_total);
                            $('#total' + contador).val(data[3][i].cantidad_total);
                            $('#precio' + contador).val(data[3][i].precio);
                            if (data[3][i].iva == '1') {
                                document.getElementById('iva' + i).checked = true;
                            }
                            total_calculo(contador);
                            cambiar_proveedor();
                            contador++;
                        }
                        suma_totales()
                        $("#contador").val(data[3].length);
                        $('#nombre_proveedor').val(data[0]).trigger("change");
                    } else {
                        $('#proveedor').val('');
                    }
                },
                error: function(data) {

                    swal("Error!", "{{trans('contableM.nocoinciproddelpedidoconstock')}} <br>",
                        "error");
                }
            })
        }

    }

    function total_calculo(id) {

        total = 0;
        descuento_total = 0;
        dec = 0;
        cantidad = parseInt($("#cantidad" + id).val());
        precio = parseFloat($("#precio" + id).val());
        descuento = parseFloat($("#desc_porcentaje" + id).val());

        //alert(descuento);
        total = cantidad * precio;
        descuento_total = (total * descuento) / 100;
        $('#desc_' + id).val(descuento_total.toFixed(2));
        dec = parseFloat($("#desc_" + id).val());
        //alert(descuento_total);
        $('#desc' + id).val(descuento_total.toFixed(2));
        $('#extendido' + id).val(total.toFixed(2));
        $('#extendido_' + id).val(total.toFixed(2));
        //
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
        var iva_par = $('#iva_par').val();
        //var iva_par = $('#iva_par').val();
        var prop = "";
        $("#det_recibido tr").each(function() {
            //$(this).find('td')[0];

            cantidad = parseInt($(this).find('#cantidad' + contador).val());
            valor = parseFloat($(this).find('#precio' + contador).val());
            descu = parseFloat($(this).find('#desc_' + contador).val());
            pre_neto = parseFloat($(this).find('#extendido_' + contador).val());
            visibilidad = parseInt($('#visibilidad' + contador).val());
            total = pre_neto;

            //
            if (visibilidad == 1) {
                if ($('#iva' + contador).prop('checked')) {
                    var iva_par = 0.12;
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "{{trans('contableM.ingresacodnomproser')}}", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }
                    //iva = sub * 0.12;

                    iva1 = pre_neto * iva_par;
                    iva = iva + iva1;

                } else {

                    prop = +"no entra en el iva";

                    if (total > 0) {
                        sub = sub + total;
                    }
                }

                /*if(iva == 1){
                    sub = sub + total;
                    iva = sub * 0.12;
                }*/
                //sub = sub + iva;
                if (descu > 0) {
                    descu1 = descu1 + descu;
                }




            }
            contador = contador + 1;

        });






        //iva = (sub- descu) * iva_par;

        base_imponible = sub;

        //iva = sub * iva_par;
        //iva = subtotal_12 * iva_par;

        trans = parseInt($('#transporte').val());
        //total_fin = (sub - descu1)+trans+iva;


        //iva = (sub - descu1) * iva_par;




        //total_fin = (sub - descu1)+trans+iva;


        //total_fin = (sub - descu1);
        //iva = sub * 0.12;
        //total = sub + iva;

        /*if(iva == 1){
            subtotal_12 = subtotal_12 + total;
        }else{
            subtotal_0 = subtotal_0 + total;
        }*/

        if (trans > 0) {
            total_fin = sub + trans + iva;
        } else {
            total_fin = sub + iva;
        }
        if (descu1 > 0) {
            total_fin = total_fin - descu1;
        }

        $('#iva_final').val(iva.toFixed(2, 2));

        //
        $('#subtotal_final').val(sub.toFixed(2, 2));


        $('#descuento').val(descu1.toFixed(2, 2));


        $('#total_final').val(total_fin.toFixed(2, 2));


        $('#base_imponible').val(base_imponible);


        //Campos Oculto
        $('#subtotal1').val(sub);
        $("#total_b").html(total_fin.toFixed(2, 2));
        $("#subtotal_b").html(sub.toFixed(2, 2));
        $("#iva_b").html(iva.toFixed(2, 2));
        $("#descuento_b").html(descu1.toFixed(2, 2));
        $('#descuento1').val(descu1);
        $('#total1').val(total_fin);
        $('#base_imponible1').val(base_imponible);
        $('#transporte1').val(trans);
        //$('#total1').val(total_fin);

        //evaluar(); 401


    }

    function suma_totales2() {

        contador = 0;
        iva = 0;
        total = 0;
        sub = 0;
        descu1 = 0;
        total_fin = 0;
        descu = 0;
        cantidad = 0;

        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                cantidad = parseFloat($(this).find('#cantidad' + contador).val());
                valor = parseFloat($(this).find('#precio' + contador).val());
                descu = parseFloat($(this).find('#desc' + contador).val());
                pre_neto = parseFloat($(this).find('#extendido1' + contador).val());
                total = cantidad * valor;
                if ($('#iva' + contador).prop('checked')) {
                    $('#ivaver' + contador).val(1);
                    var iva_par = $('#iva_par').val();
                    var cod = $('#codigo' + contador).val();
                    var nomb = $('#nombre' + contador).val();
                    if ((cod.length == 0) && (nomb.length == 0)) {
                        swal("Error!", "{{trans('contableM.ingresacodnomproser')}}", "error");
                        $('#iva' + contador).prop('checked', false);
                    }
                    if (total > 0) {
                        sub = sub + total;
                    }

                    if (descu > 0) {
                        iva1 = (pre_neto - descu) * (0.12);

                        iva = iva + iva1;
                    } else {
                        iva1 = pre_neto * 0.12;
                        iva = iva + iva1;
                    }

                } else {
                    if (total > 0) {
                        sub = sub + total;

                    }
                }
                if (descu > 0) {
                    descu1 = descu1 + descu;

                }
                //aask to change discount to values
                // discount values with level headers
                // why i dont use values the function laravl view
                //



            }
            //contador = contador + 1;
        });
        var ivaf = parseFloat($("#iva_par").val());

        var dsiva = sub;
        if (iva > 0) {
            dsiva = sub * ivaf;
        }
        if (descu1 > 0) {
            if (iva > 0) {
                dsiva = (sub - descu1) * ivaf;

            } else {
                dsiva = (sub - descu1);

            }

        }
        subt = sub;
        //
        trans = parseFloat($('#transporte').val());
        total_fin = parseFloat((sub - descu1) + dsiva);
        if (descu1 > 0) {
            subt = sub - descu1;
        }
        if (!isNaN(sub)) {
            $('#subtotal_final').val(sub.toFixed(2));
        }
        if (!isNaN(descu1)) {
            $('#descuento').val(descu1.toFixed(2));
        }
        if (!isNaN(dsiva)) {
            $('#iva_final').val(dsiva.toFixed(2));
        }
        if (!isNaN(total_fin)) {
            $('#total_final').val(total_fin.toFixed(2));
        }
        $('#subtotal_final1').val(sub.toFixed(2));
        if (descu1 == NaN) {
            decu1 = 0;
        }
        $('#descuento1').val(descu1.toFixed(2));
        $('#iva_final1').val(dsiva.toFixed(2));
        $('#total_final1').val(total_fin.toFixed(2));


    }

    function cambiar_nombre(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre" + id).val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#codigo' + id).val(data.value);
                    if (data.iva == '1') {
                        $('#ivaver' + id).val(1);
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                    }
                } else {
                    $('#codigo' + id).val(" ");
                }
            },
            error: function(data) {

            }
        })
    }

    function cambiar_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('compra_codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val(),
                'id_empresa': $("#id_empresa").val(),
            },
            success: function(data) {

                if (data.value != "no") {
                    $('#nombre' + id).val(data.value);
                    if (data.iva == '1') {
                        document.getElementById('iva' + id).checked = true;
                        document.getElementById('iva' + id).disabled = true;
                        $('#ivaver' + id).val(1);
                    }
                } else {
                    $('#nombre' + id).val(" ");
                }
            },
            error: function(data) {

            }
        })
    }
    $("#proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_identificacion')}}",
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

    function cambiar_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#proveedor").val()
            },
            success: function(data) {

                if (data.value != "no") {
                    $('#nombre_proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#autorizacion').val(data.autorizacion);
                } else {
                    $('#nombre_proveedor').val(" ");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#autorizacion').val("");
                }
            },
            error: function(data) {

            }
        })
    }

    $("#nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_buscar_nombreproveedor')}}",
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

    function cambiar_nombre_proveedor(e) {
        /* array_d =$('#nombre_proveedor').select2('data');
        for(i in array_d){
            
            text_e = array_d[i]['text'];
        }
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                    $('#serie').val(data.serie);
                    $('#f_caducidad').val(data.caducidad);
                    $('#autorizacion').val(data.autorizacion);

                } else {
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                    $('#serie').val("");
                    $('#f_caducidad').val("");
                    $('#autorizacion').val("");
                }

            },
            error: function(data) {
                
            }
        }); */
        var direccion = $('option:selected', e).data("direccion")

        $('#direccion_proveedor').val(direccion);
    }

    function ingresar_cero() {
        let secuencia = $('#secuencia_factura').val(); //obtener el valor ingresado
        let numero = ""; //se encargara agregar los 0
        let final_secuencia = ""; //la concatenacion de ambas variabes 

        if (parseInt(secuencia) > 0) {
            if (secuencia.length > 10) {
                alertas("error", "Error!", "{{trans('contableM.valornoper')}}", );
                $('#secuencia_factura').val('');
            } else {
                if (secuencia.length < 10) {
                    while (final_secuencia.length != 10) {
                        numero = "0" + numero
                        //
                        final_secuencia = numero + secuencia
                    }
                    $('#secuencia_factura').val(final_secuencia);
                } else {
                    $('#secuencia_factura').val(secuencia);
                }
            }
        } else {
            alertas("error", "Error!", "{{trans('contableM.valornoper')}}", );
            $('#secuencia_factura').val('');
        }
    }

    function agregar_serie() {
        var serie = $('#serie').val();
        if ((serie.length) == 3) {
            $('#serie').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie').val('');
            swal("Error!", "Ingrese la serie correctamente: 000-000", "error");
        }

    }


    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>

            var fecha1 = '<?= $min_date; ?>';
            let fechaAc = new Date();
            var fecha2 = document.getElementById("f_autorizacion").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            const fechaFormateada = fechaAc.toISOString().split('T')[0];
            if (fechaFinal < fechaInicial) {
                document.getElementById("f_autorizacion").value = fechaFormateada;
                return new swal("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error")
        }
        <?php
        }
        ?>
    }

</script>


@endsection