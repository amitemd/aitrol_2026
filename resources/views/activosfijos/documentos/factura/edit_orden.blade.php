@extends('activosfijos.documentos.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<style>
    .input-number {
        width: 80%;
        height: 20px;
    }

    .content {
        font-family: 'PT Sans', sans-serif !important;
    }

    .table-responsive .form-control {
        border-radius: 5px !important;
        /* padding: 5px; */
    }

    .block {
        display: block;
    }

    .none {
        display: none;
    }

    .my_scroll_div {
        overflow-y: auto;
        max-height: 600px;
    }
</style>




<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:goBack();">{{trans('contableM.Orden')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.nuevo')}}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('contableM.prefac')}}</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="return location.reload(true);" class="btn btn-primary btn-gray">
                    {{trans('contableM.nuevo')}}
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
    </div>

    <form class="form-vertical" enctype="multipart/form-data" id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid">

            <div class="box-body dobra">
                <div class="header row">

                    <div class="col-md-12">
                        <div class="row">
                            <input type="hidden" name="details_details" id="details_count" value="0">
                            <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
                            <input type="hidden" name="estado" id="estado" value="actualizar">
                            <div id="form_acc">

                            </div>


                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="text" class="form-control" name="id" id="id" value="" readonly>
                                    @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-4  col-md-4  px-1">
                                <div class="col-md-12 px-0">
                                    <label for="proveedor" class="label_header">{{trans('contableM.proveedor')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    {{-- <input id="proveedor" type="text" class="form-control" name="proveedor"
                                        value="{{ $empresa->id }} - {{ $empresa->nombrecomercial }}" readonly> --}}
                                    <select class="form-control select2_cuentas" style="width:100%" id="proveedor" onchange="llenarCampo(); cargar_autorizacion();" name="proveedor">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach ($proveedor as $value)
                                        <option value="{{ $value->id }}">{{ $value->razonsocial }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('orden_venta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proveedor') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="serie_factura" class=" label_header">{{trans('contableM.serie')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control validar" onkeyup="agregar_serie(); llenarCampo();" name="serie_factura" id="serie_factura" value="" maxlength="7">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('serie_factura').value = '';"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="secuencia" class=" label_header">{{trans('contableM.secuenciafactura')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control validar" name="secuencia" id="secuencia" value="" onchange="ingresar_cero('secuencia', 10); llenarCampo()" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia').value = '';"></i>
                                        </div>
                                    </div>
                                    @if ($errors->has('secuencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('secuencia') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6 col-md-2 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="fecha_compra" class=" label_header">{{trans('contableM.fecha')}}</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    <input type="date" class="form-control col-md-12 validar" name="fecha_compra" id="fecha_compra" value="@php echo date('Y-m-d');@endphp">

                                    @if ($errors->has('fecha_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_compra') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            
                            <div class="form-group col-xs-12 col-md-12 px-1">
                                <div class="col-md-12 px-0">
                                    <label for="concepto" class=" label_header">{{trans('contableM.concepto')}}</label>
                                </div>
                                <div class="col-md-12 px-0">

                                    <input type="text" class="form-control validar" name="concepto" id="concepto" value="" width="100%" autocomplete="off">

                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="pre_orden" id="pre_orden" value="1">
                        </div>
                    </div>

                    <div id="output">
                    </div>

                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <input type="hidden" name="contador" id="contador" value="0">


                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>
                                <th width="30%" class="" tabindex="0">{{trans('contableM.descripcionactivo')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.Costo')}}</th>
                                <th width="10%" class="" tabindex="0">% {{trans('contableM.prctdesc')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.descuento')}}</th>
                                <th width="10%" class="" tabindex="0">{{trans('contableM.total')}}</th>
                                <th width="5%" class="" tabindex="0">{{trans('contableM.iva')}}</th>
                                <th width="10%" class="" tabindex="0">
                                    <button onclick="crearFila();" type="button" class="btn btn-success btn-gray btn-sm">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>

                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">

                        </tbody>
                        <input type="hidden" id="iva_global" value="{{$iva->iva}}">
                        <tfoot class=''>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}} 12%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}} 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.descuento')}}</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.subtotal')}}</td>
                                <td id="base" class="text-right px-1">0.00</td>

                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>



                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right">{{trans('contableM.tarifaiva')}}</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>

                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"><strong>{{trans('contableM.total')}}</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                            <tr>
                                <td colspan="7"></td>
                                <td colspan="2" class="text-right"></td>
                                <td id="copagoTotal" class="text-right px-1"></td>
                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardar(event)" id="boton_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.actualizar')}}
                    </button>
                </div>

            </div>
        </div>


        <div class="modal fade bs-example-modal-lg " id="modal_datos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content my_scroll_div" id="datos_activo">

                </div>
            </div>
        </div>



        <!--MODAL-->

        <!--Modal fin-->

        <input name='contador_items' id='contador_items' type='hidden' value="1">
    </form>
</section>

@include('activosfijos.documentos.factura.mdactivo2')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        buscar_orden();
        crearFila(0, {});
    });

    function buscar_orden() {
        // alert("hola");
        var orden = $("#id_orden").val();
        $.ajax({
            type: 'get',
            url: "{{route('documentofactura.buscar_orden')}}",
            datatype: 'json',
            data: {
                'orden': orden
            },
            success: function(data) {

                llenarInputs(data.cab);
            },
            error: function(data) {

            }
        })
    }

    const llenarInputs = (data) => {
        console.log(data);
        document.getElementById("concepto").value = data.observacion;
        document.getElementById("fecha_compra").value = data.fecha_compra;
        document.getElementById("serie_factura").value = data.serie;
        document.getElementById("secuencia").value = data.secuencia;
        document.getElementById("subtotal_121").value = data.subtotal12;
        document.getElementById("subtotal_01").value = data.subtotal0;
        document.getElementById("descuento1").value = data.descuento;
        document.getElementById("base1").value = data.subtotal;
        document.getElementById("tarifa_iva1").value = data.impuesto;
        document.getElementById("total1").value = data.total;
        document.getElementById("subtotal_12").innerHTML = parseFloat(data.subtotal12).toFixed(2);
        document.getElementById("subtotal_0").innerHTML = parseFloat(data.subtotal0).toFixed(2);
        document.getElementById('base').innerHTML = parseFloat(data.subtotal).toFixed(2);
        document.getElementById('descuento').innerHTML = parseFloat(data.descuento).toFixed(2);
        document.getElementById('tarifa_iva').innerHTML = parseFloat(data.impuesto).toFixed(2);
        document.getElementById('total').innerHTML = parseFloat(data.total).toFixed(2);

        $('#proveedor').empty();
        $('#proveedor').append(`<option selected value="${data.id_proveedor}" >${data.nombre_proveedor}</option>`);
        $("#proveedor").change();

        $('#agregar_cuentas').empty();
        document.getElementById('contador_items').value = 1;

        const details = data.detalles.map(detalle => {
            crearFila(1, detalle);
        });
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;
        //  console.log(campo)
        for (let i = 0; i < campo.length; i++) {
            //console.log(`${campo[i].name}: ${campo[i].value}`);
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

    function agregar_serie() {
        var serie = $('#serie_factura').val();
        if ((serie.length) == 3) {
            $('#serie_factura').val(serie + '-');
        } else if ((serie.length) > 7) {
            $('#serie_factura').val('');
            swal("Error!", "trans('contableM.seriecorrectamente')", "error");
        }
    }

    const sumarFecha = (e) => {

        let day = e.options[e.selectedIndex].text;
        let date = new Date();
        let subday = day.substring(0, 2).trim();
        if (Number.isInteger(parseInt(subday))) {
            date.setDate(date.getDate() + parseInt(subday));
            var fechaImp = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            console.log(fechaImp);
            document.getElementById("fecha_caduca").value = fechaImp;
            document.getElementById("fecha_caduca").readOnly = true;
        } else {
            document.getElementById("fecha_caduca").value = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            document.getElementById("fecha_caduca").readOnly = false;
        }

    }

    function ingresar_cero(ids, longitud) {

        let id = document.getElementById(ids);
        let cero = "";
        let concat = "";
        if (parseInt(id.value) > 0) {
            if (id.value.length < longitud) {
                while (concat.length != longitud) {
                    cero = "0" + cero;
                    concat = cero + id.value;
                }
                id.value = concat;
            } else {
                alerta("Error!", "error", "trans('contableM.valorincorrecto')");
                id.value = "";
            }
        } else {
            alerta("Error!", "error", "trans('contableM.valorincorrecto')");
            id.value = "";
        }
    }


    function guardar_color(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_color')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {},
            error: function(data) {
                //alert(data)
            }
        });

    }

    function guardar_marca(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_marca')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_serie(id) {

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_serie')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_responsable(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/documentofactura/guardar_responsable')}}/" + id,
            data: $("#crear_factura").serialize(),
            datatype: 'json',
            success: function(data) {
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function ingresar_cero2() {
        var secuencia_factura = $('#mdcodigo_num').val();
        var digitos = 6;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if (secuencia_factura > 0) {
            var longitud = parseInt(secuencia_factura.length);
            if (longitud > 7) {
                swal("Error!", "trans('contableM.valornoper')", "error");
                $('#mdcodigo_num').val('');

            } else {

                var concadenate = parseInt(digitos - longitud);
                switch (longitud) {
                    case 1:
                        secuencia = '00000';
                        break;
                    case 2:
                        secuencia = '0000';
                        break;
                    case 3:
                        secuencia = '000';
                        break;
                    case 4:
                        secuencia = '00';
                        break;
                    case 5:
                        secuencia = '0';
                        break;
                    case 6:
                        secuencia = '';
                }
                $('#mdcodigo_num').val(secuencia + secuencia_factura);
            }


        } else {
            swal("Error!", "trans('contableM.valornoper')", "error");
            $('#mdcodigo_num').val('');
        }
    }

    function alerta(title, icon, text) {
        Swal.fire({
            icon: '' + icon,
            title: '' + title,
            html: '' + text
        })
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
</script>



<script type="text/javascript">
    $('#modal_datosactivo').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });

        $('.select2_cuentas2').select2({
            tags: false
        });

        $('.select2_color').select2({
            tags: true
        });


    });

    const crearFila = (req = 0, data = {}) => {
        var id = document.getElementById('contador_items').value;
        let fila = `
            <tr >
                <td>
                    <button type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick="modal_activo(${id}, event)"> <i class="glyphicon glyphicon-edit"></i></button>  
                </td>
                <td>
                    <input id="codigo${id}" class="form-control validar" type="text" style="width: 80%;height:20px;" name="codigo[]" readonly value="${req === 1 ? data.codigo : '' }">
                </td>
                <td>
                    <input id="descrip_prod${id}" class="form-control validar" type="text" style="width: 93%;height:20px;" name="descrip_prod[]" readonly value="${req === 1 ? data.nombre : '' }">
                    <textarea  rows="3" name="observacion[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cant validar" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.cantidad : 0}"  name="cantidad[]" >

                </td>
                <td>
                    <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id})" id="precio${id}"  value="${req === 1 ? data.precio : 0.00}" type="text" class="pneto form-control validar" name="precio[]" style="width: 80%;height:20px;" placeholder="0.00">
                </td>

                <td>
                    <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.porct_descuento : '0.00'}" name="descpor[]" >
                    <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" name="maxdesc[]" >
                </td>
                <td>
                    <input onchange="descuento(${id});"  id="desc${id}" class="form-control text-right desc" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="${req === 1 ? data.descuento : '0.00'}" name="desc[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" style="height:20px;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="${req === 1 ? data.precio_neto : '0.00'}" name="precioneto[]" >
                </td>
                <td>
                    <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" style="width: 80%;height:20px;" name="valoriva[]" id="valoriva${id}" ${req === 1 ? data.iva === 1 ? 'checked' : '' : ''} >
                    <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" value="${req === 1 ? data.val_iva : '0.00'}" >
                    <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="${req === 1 ? data.total_valor : '0.00'}">
                    <input id="check_iva${id}" type="hidden" name="check_iva[]" value="${req === 1 ? data.iva : '0'}">

                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarModalUni(${id})">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `

        $('#agregar_cuentas').append(fila);
        $('.select2_cuentas').select2({
            tags: false
        });

        if (req === 1) {

            $("#datos_activo").append(modal_crear(id, req, data.activo_fijo));
            let modal_datos_a = document.getElementById("modal_datos_a" + id);
            modal_datos_a.classList.remove("block");
            modal_datos_a.classList.add("none");
            console.log("Activo fijo", data.activo_fijo);


            $(`#mdtipo${id} option[value="${data.activo_fijo.tipo_id}"]`).attr("selected", true);
            $(`#mdtipo${id}`).change();
            $(`#mdmarca${id} option[value="${data.activo_fijo.marca}"]`).attr("selected", true);
            $(`#mdcolor${id} option[value="${data.activo_fijo.color}"]`).attr("selected", true);
            $(`#mdserie${id} option[value="${data.activo_fijo.serie}"]`).attr("selected", true);
            $(`#mdresponsable${id} option[value="${data.activo_fijo.responsable}"]`).attr("selected", true);
            $(`#mdubicacion${id} option[value="${data.activo_fijo.ubicacion}"]`).attr("selected", true);

            setTimeout(() => {
                $(`#mdgrupo${id} option[value="${data.activo_fijo.subtipo_id}"]`).attr("selected", true);
            }, 1000);


            if (data.activo_fijo.accesorios.length > 0) {
                const accesorio = data.activo_fijo.accesorios.map(access => {
                    agregar_items(id, req, access);

                });
            }



        }

        id++;
        document.getElementById('contador_items').value = id;
    }

    function select_iva(id) {
        var check = document.getElementById("valoriva" + id);
        var check_af = document.getElementById("check_iva" + id);

        if (check.checked) {
            check_af.value = 1;
        } else {
            check_af.value = 0;
        }
    }

    function totalProducto(id) {
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let descpor = document.getElementById("descpor" + id).value;
        let desc = document.getElementById("desc" + id)
        let precioneto = document.getElementById("precioneto" + id)
        let val_iva = document.getElementById("val_iva" + id)
        let total_valor = document.getElementById("total_valor" + id)
        let subtotal0 = document.getElementById("subtotal_12")
        let subtotal12 = document.getElementById("subtotal_0")

        let iva_global = document.getElementById("iva_global").value


        let preXcantidad = (precio * cantidad);
        let porcDescuento = (descpor / 100) * preXcantidad;
        let precioTotal = (preXcantidad) - porcDescuento;
        let valoriva = 0;


        if ($('#valoriva' + id).prop('checked')) {

            valoriva = parseFloat(precioTotal * (1000 * parseFloat(iva_global)));
            valoriva = valoriva / 1000;

        }

        let total = precioTotal + valoriva;

        val_iva.value = valoriva;
        desc.value = porcDescuento;
        precioneto.value = precioTotal;
        total_valor.value = total;

        totalGlobal();

    }


    function descuento(id) {
        let descpor = document.getElementById("descpor" + id);
        let cantidad = document.getElementById("cantidad" + id).value;
        let precio = document.getElementById("precio" + id).value;
        let desc = document.getElementById("desc" + id).value;
        let preXcantidad = (precio * cantidad);
        let preXcantidadV = 0;
        if (preXcantidad == 0) {
            preXcantidadV = 1;
        } else {
            preXcantidadV = preXcantidad;
        }
        let descuento = (desc * 100) / preXcantidadV;
        descpor.value = descuento.toFixed(2);
        totalProducto(id);
    }



    function totalGlobal() {
        let precioneto = document.querySelectorAll(".precioneto");
        let desc = document.querySelectorAll(".desc");
        let vliva = document.querySelectorAll(".val_iva");
        let tot_valor = document.querySelectorAll(".total_valor");
        let cantidad = document.querySelectorAll(".cant");
        let precio = document.querySelectorAll(".pneto");

        let total = 0;
        let descuento = 0;
        let iva = 0;
        let totv = 0;
        let subtotal0 = 0;
        let subtotal12 = 0;

        for (let i = 0; i < precioneto.length; i++) {
            total += parseFloat(precioneto[i].value);
            descuento += parseFloat(desc[i].value);

            iva += parseFloat(vliva[i].value);
            totv += parseFloat(tot_valor[i].value);

            if (parseFloat(vliva[i].value) > 0) {
                subtotal12 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            } else {
                subtotal0 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
            }

        }

        document.getElementById("subtotal_12").innerHTML = parseFloat(subtotal12).toFixed(2);
        document.getElementById("subtotal_121").value = parseFloat(subtotal12).toFixed(2);

        document.getElementById("subtotal_0").innerHTML = parseFloat(subtotal0).toFixed(2);
        document.getElementById("subtotal_01").value = parseFloat(subtotal0).toFixed(2);

        document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
        document.getElementById('base1').value = parseFloat(total).toFixed(2);

        document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
        document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);

        document.getElementById('tarifa_iva').innerHTML = parseFloat(iva).toFixed(2);
        document.getElementById('tarifa_iva1').value = parseFloat(iva).toFixed(2);

        document.getElementById('total').innerHTML = parseFloat(totv).toFixed(2);
        document.getElementById('total1').value = parseFloat(totv).toFixed(2);

    }

    $('body').on('click', '.delete', function() {
        $(this).parent().parent().remove();
        totalGlobal();
    });


    function goBack() {
        window.history.back();
    }
</script>

<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }



    const validarTodo = () => {
        let camposVacios = [];

        if (document.getElementById("proveedor").value == '') {
            camposVacios.push('proveedor')
        }


        if (document.getElementById("secuencia").value == '') {
            camposVacios.push('secuencia')
        }

        if (document.getElementById("concepto").value == '') {
            camposVacios.push('concepto')
        }
        if (camposVacios.length > 0) {
            $('#boton_guardar').prop("disabled", false);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `trans('contableM.sigcamvacios'): ${camposVacios.map(i=>i)}`
            })

            return true;
        }

        return false;
    }

    const guardar = (e) => {
        $('#boton_guardar').prop("disabled", true);
        e.preventDefault();
        let proveedor = $('#proveedor').val();
        let serie = $('#serie').val();
        let secuencia = $('#secuencia_factura').val();
        let sec_imp = $('#secuencia_importacion').val();

        let resp = validarTodo();

        if (!validar_campos()) {
            if (!resp) {


                $.ajax({
                    url: "{{route('documentofactura.guardar_orden')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    datatype: 'json',
                    data: $("#crear_factura").serialize(),

                    success: function(data) {
                        if (data.respuesta == 'success') {
                            alerta('Exito', data.respuesta, data.msj)
                            $('#asiento_id').val(data.id_asiento)
                        } else {
                            alerta('Error', data.respuesta, data.msj)
                        }
                    },
                    error: function(data) {

                    }
                });
            }
        } else {
            //btn_guardar.style.display = 'initial'
            $('#boton_guardar').prop("disabled", false);
            alerta('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }


    function cargar_autorizacion() {
        $.ajax({
            type: 'post',
            url: "{{ route('documentofactura.buscar_proveedor') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_proveedor': proveedor.value
            },
            success: function(data) {
                $('#nro_autorizacion').val(data.autorizacion);
                $('#serie_factura').val(data.serie);
                $('#fecha_caduca').val(data.f_caduca);
            },
            error: function(data) {
                console.log(data);
            }
        })
    }



    const llenarCampo = () => {
        let proveedor = document.getElementById("proveedor");
        let serie = document.getElementById("serie_factura").value;
        let secuencia = document.getElementById("secuencia").value;
        let value = proveedor.options[proveedor.selectedIndex].text;
        if (value != '') {
            document.getElementById("concepto").value = `ACT-FA # ${value} ${serie}-${secuencia}`
        }
        //alert(proveedor.value);
    }
</script>
@endsection