@extends('contable.nota_credito_cliente.base')
@section('action-content')
<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;

?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

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

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    .container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .ocultos {
        display: none;
        width: 90%;
    }

    .ocultosp {
        width: 90%;
    }

    .datos td {
        text-align: center;
    }

    .valores {
        text-align: end;
        font-weight: bold;
    }

    .valores input {
        background: none;
        border: 0px;
        text-align: center;
    }
</style>

<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('nota_credito_cliente.index') }}";
    }
</script>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">{{trans('contableM.ncreclien')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_nota_credito_cl">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <!--<div class="box-title "><b>Crear Nota de Crédito Clientes</b></div>-->
                            <h5><b>{{trans('contableM.crearnocredclien-')}}</b></h5>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" id="boton_guardar_nota" onclick="guarda_nota_credito(this)" class="btn  btn-success "><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                </button>
                                <button type="button" class="btn btn-info" onclick="nueva_nota_credito()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <button type="button" class="btn  btn-danger" onclick="goBack()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12">
                        <div class="form-row ">
                            <!--<div class=" col-md-12">
                                <label for="buscar" class="control-label">{{trans('contableM.buscar')}}</label>
                                <input type="text" id="buscar" name="buscar" class="form-control-sm buscar  px0"
                                    onchange="buscar_factura()">
                            </div>-->
                            <div class=" col-md-1 px-1">
                                <label class="label_header">{{trans('contableM.estado')}}</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_nota_credito">{{trans('contableM.id')}}:</label>
                                <input class="form-control " type="text" name="id_nota_credito" id="id_nota_credito" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_secuencia">{{trans('laboratorio.numero')}}:</label>
                                <input class="form-control " type="text" id="numero_secuencia" name="numero_secuencia" readonly>
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="CLI-NC" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}:</label>
                                <input class="form-control " type="text" id="num_asiento" name="num_asiento" readonly>
                                @if(isset($valorIva))
                                <input type="text" name="iva_par" id="iva_par" class="hidden" value="{{$valorIva}}">
                                @endif
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.NoFactura')}} </label>
                                <input class="form-control " type="text" name="nro_factura" id="nro_factura" readonly autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group col-xs-6  col-md-1 px-1">
                            <div class="col-md-12 px-0">
                                <label for="empresa" class="label_header">{{trans('contableM.electronica')}}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <label class="switch">
                                    <input class="electros" id="toggleswitch" type="checkbox">
                                    <span class="slider round"></span>
                                    <input type="hidden" id="electronica" name="electronica">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-7 px-1">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                            <input class="form-control  col-md-12" type="text" maxlength="50" name="concepto" id="concepto" required autocomplete="off">
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="label_header" for="sucursal">{{trans('contableM.sucursal')}}:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                    @foreach($sucursales as $value)
                                    <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="label_header" for="punto_emision">{{trans('contableM.PuntodeEmision')}}:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="punto_emision" id="punto_emision" required>
                                    <option value="">{{trans('agenda.seleccione')}}...</option>
                                </select>
                            </div>
                        </div>
                        <!--  <div style="margin-left: -8%;" class=" col-md-2 px-0">
                            <label class="col-md-12 label_header" for="cliente">{{trans('contableM.cliente')}}: </label>
                            <input type="text" id="id_cliente" name="id_cliente"
                                class="form-control form-control-sm id_cliente  col-md-12">
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="col-md-12 label_header" for="nombre_cliente">&nbsp;</label>
                            <input type="text" id="nombre_cliente" name="nombre_cliente"
                                class="form-control form-control-sm nombre_cliente ">
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="label_header">{{trans('contableM.secuencia')}}</label>
                            <input type="text" class="form-control" name="secuencial" id="secuencial" placeholder="Ingrese secuencial">
                        </div> -->

                    </div>
                    <input type="hidden" id="serviciosFlag" name="serviciosFlag" value="0">
                    <div class="col-md-12">
                        <div class="col-md-2 px-0" style="padding-top: 13px">
                            <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">{{trans('contableM.archidelsri')}}</span>
                            <input style="width:17px;height:17px;padding-top:13px" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" value="1" @if(old('check_archivo_sri')=="1" ) checked @endif>
                        </div>
                        <div class="col-md-6 px-0">
                            <label class="label_header"> {{trans('contableM.factura')}} </label>
                            <select class="form-control select2" style="width: 100%;" onchange="showData(); " name="factura" id="id_factura">

                            </select>
                        </div>

                        {{-- <div class="col-md-2 px-0">
                            <label class="label_header">Tipo de Asiento</label>
                            <select class="form-control" style="width: 100%;" onchange="showData(); " name="tipo_asiento" id="tipo_asiento">
                              
                            </select>
                        </div>
                    --}}

                        <div class="col-md-2 px-0">
                            <label class="label_header">{{trans('contableM.TotalFactura')}}</label>
                            <input type="text" class="form-control" readonly name="total" id="total_final" value="0.00">
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="label_header">{{trans('contableM.saldocontable')}}</label>
                            <input type="text" class="form-control" readonly name="valor_contable" id="valor_contable" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label style="padding: 5px;font-size: 13px;" class="label label-info ">{{trans('contableM.itemcheck1')}}</label>
                </div>
                <div class="col-md-12" style="top: 20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="detalle_deuda">DETALLE DE PRODUCTOS</label>
                        </div>
                        <div class="col-md-6" style="text-align: right;">
                            <button class="btn btn-primary btn-gray" type="button" onclick="seleccionar_todo(); sumaGlobal();"><i class="fa fa-check-circle"></i> &nbsp; Seleccionar Todos </button>
                            <button class="btn btn-primary btn-gray" type="button" onclick="deseleccionar_todo(); sumaGlobal();"><i class="fa fa-remove"></i> &nbsp; Deseleccionar Todos </button>
                            <button class="btn btn-danger btn-gray" type="button" onclick="eliminarFila()"><i class="fa fa-remove"></i> &nbsp; Eliminar Filas </button>
                            <button class="btn btn-warning btn-gray" id="plus" style="display:none" type="button" onclick="agregarFilaServicio();sumaGlobal();"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>


                    </div>


                </div>
                <input type="hidden" id="contador" value="0">
                <div class="col-md-12" style="margin-top: 50px;">
                    <table id="example2" class="table table-hover dataTable" role="grid" style="width: 100%;" aria-describedby="example2_info">
                        <thead style="background-color: #9E9E9E; color: white;">
                            <tr style="position: relative;">
                                <!--<th style="width: 10%; text-align: center;">{{trans('contableM.id')}}</th>-->
                                <th style="text-align: center;">#</th>
                                <th style="text-align: center;">{{trans('contableM.codigo')}}</th>
                                <th style="text-align: center;">{{trans('contableM.cantidad')}}</th>
                                <th style="text-align: center;">{{trans('contableM.nombre')}}</th>
                                <th style="text-align: center;">{{trans('contableM.valor')}}</th>
                                <th style="text-align: center;">{{trans('contableM.iva')}}</th>
                                <th style="text-align: center;">{{trans('contableM.accion')}}</th>
                            </tr>
                        </thead>
                        <tbody id="crear">

                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>

                <div class="col-md-12">
                    <div class="valores">
                        <div>{{trans('contableM.subtotal')}} {{$porcentajeIva}}% <input name="subtotal121" id="subtotal12" type="text" readonly value="0.00"> </div>
                    </div>
                    <div class="valores">
                        <div>{{trans('contableM.subtotal0')}}% <input name="subtotal01" id="subtotal0" type="text" readonly value="0.00"> </div>
                    </div>
                    <div class="valores">
                        <div>{{trans('contableM.descuento')}} <input name="descuento1" id="descuento" type="text" readonly value="0.00"> </div>
                    </div>
                    <div class="valores">
                        <div>{{trans('contableM.subtotal')}} <input name="subtotal1" id="subtotal" type="text" readonly value="0.00"> </div>
                    </div>
                    <div class="valores">
                        <div>{{trans('contableM.impuesto')}} {{$porcentajeIva}}% <input name="impuesto1" id="impuesto" type="text" readonly value="0.00"> </div>
                    </div>
                    <div class="valores">
                        <div>{{trans('contableM.total')}} <input name="total1" id="total" type="text" readonly value="0.00"> </div>
                    </div>
                </div>

                <div class="col-md-12" style="padding-left: 30px; padding-top: 10px">
                    <label for="observaciones">{{trans('contableM.observaciones')}}</label>
                    <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3"></textarea>
                </div>
            </div>
        </div>
    </form>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    function eliminarFila() {
        document.getElementById("crear").innerHTML = "";
        document.getElementById("plus").style.display = "inline";
        document.getElementById("serviciosFlag").value = 1;
    }

    function deleteRow(id) {
        $("#id" + id).empty();
        sumaGlobal();
    }

    function agregarFilaServicio() {
        sumaGlobal();
        let contador = document.getElementById("contador").value;
        let tr = document.createElement("tr");
        tr.setAttribute("id", `id${contador}`);
        tr.style.textAlign = "center";
        tr.innerHTML += `
                <td>#</td>
                <td> 
                    <select class="form-control" style="width:95%" id="servicios${contador}" name="servicios" onchange="cambiarNombre(this,${contador})">
                    <option value="">Seleccione</option>
                    @foreach($plan_cuenta as $value)
                    <option value="{{$value->codigo}}" >{{$value->nombre}}</option>
                    @endforeach
                  </select>
                  <input type='hidden' name='codigo[]' id="codigo${contador}">
                </td>
                <td>
                    <input type="number" style="width:95%;text-align:right" class="cantidad form-control" id="cantidad${contador}" name='cantidad[]' value="1" onchange='sumaGlobal();verificarPrecio(${contador});calcularRubroIva(${contador})'/>
                </td>
                <td>
                    <input type="text" style="width:95%" class="form-control" id="nombre${contador}"  id="nombre" name="nombre[]" />
                </td>
                <td>
                    <input type="number" style="width:95%;text-align:right" onchange='sumaGlobal();verificarPrecio(${contador});' class="form-control precios" id="precio_${contador}" value="0.0" name="precio[]" />
                    <input type='hidden'  class='descuento' name='descuento[]' value='0'>
                </td>
                <td>
                    <input type='hidden' class='iva_producto' id='iva_producto${contador}' name='iva_producto[]' value='0'>
                    <input type='hidden' id='totalProducto${contador}' class='totalProducto' value="0" name='totalProducto[]'>
                    <input  type='checkbox'  id="iva${contador}" name='iva[]' onchange="calcularRubroIva(${contador})">
                    <input type='hidden' id='porct_iva_${contador}' class='iva_porct' name='iva_porct[]' value='0'>
                    <input class='vercheckbox checkProducto' type='hidden' name='verificar[]' value='1'> 
                    </td>
                <td>
                   <button type="button" class="btn btn-danger" onclick="deleteRow(${contador})">
                    <i class="fa fa-trash" aria-hidden="true" ></i>
                    </button>
                </td>
            `;
        document.getElementById("crear").appendChild(tr);
        document.getElementById("contador").value = Number(contador) + 1;
        $('.select2').select2({
            tags: false
        });
    }

    function verificarPrecio(id) {
        let identi = document.getElementById("id_factura").value;
        let precio = document.getElementById("precio_" + id).value;
        let contador = document.getElementById("contador").value;
        $.ajax({
            type: "get",
            url: "{{route('notacredito.verificar_precio')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                'id': identi,
                'precio': precio,
                'cantidad': contador
            },
            success: function(data) {
                if (data.status) {
                    alertas('error', 'Error..', `${data.msj} en el servicio ${id+1}`);
                }

            },
            error: function(data) {
                console.log(data);
            }
        });

    }




    function calcularRubroIva(id) {
        let precio = document.getElementById("precio_" + id).value * $("#cantidad" + id).val();
        let iva = '{{$valorIva}}';
        if (document.getElementById("iva" + id).checked) {
            let calc = precio * iva;
            console.log(document.getElementById("iva_producto" + id).value);
            document.getElementById("porct_iva_" + id).value = calc;
            document.getElementById("iva_producto" + id).value = calc;
            document.getElementById("totalProducto" + id).value = Number(calc) + Number(precio);
        } else {
            document.getElementById("totalProducto" + id).value = 0.0;
            document.getElementById("iva_producto" + id).value = 0.0;
            document.getElementById("total_producto" + id).value = 0.0;
        }
        sumaGlobal();
    }

    function cambiarNombre(e, id) {

        let nombre = e.options[e.selectedIndex].text;

        document.getElementById("nombre" + id).value = nombre;
        document.getElementById("codigo" + id).value = e.value;


    }





    var input = document.getElementById('toggleswitch');
    //var outputtext = document.getElementById('status');
    document.getElementById('electronica').value = 1;
    let electronica = document.getElementById('electronica').value;
    // if(input == false){
    //     electronica.value = 0;
    // }else{
    //     electronica.value= 1;
    // }

    input.addEventListener('change', function() {
        if (this.checked) {
            $("#electronica").val(1);
        } else {
            $("#electronica").val(0);
        }
    });
</script>


<script type="text/javascript">
    $(document).ready(function() {
        document.querySelector('#toggleswitch').checked = true
        $('#check_archivo_sri').attr('checked', true);
        $('.select2').select2({
            placeholder: `{{trans('contableM.selecfac')}}`,
            allowClear: true,
            ajax: {
                url: '{{route("nota_credito_cliente.getcomprobante")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                }
            },

        });

    });

    //BUSQUEDA POR IDENTIFICACION
    $(document).on("focus", "#id_cliente", function() {

        $("#id_cliente").autocomplete({

            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('notacredito.buscarclientexid')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                $("#nombre_cliente").val(ui.item.nombre);
                //buscar_deudas_cliente();
                //suma_deudas_cliente();
                //obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,

        });

    });

    function seleccionar_todo() {
        $('.verificar').each(function() {
            $(this).prop('checked', true);
        });
        adder()

    }

    function deseleccionar_todo() {
        $('.verificar').each(function() {
            $(this).prop('checked', false);
        });
        adder()
    }
    //BUSQUEDA DE FACTURA POR ID CLIENTE Y EMPRESA
    $(document).on("focus", "#nro_factura", function() {
        $("#nro_factura").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('notacredito.obtener_num_fact')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                $("#id_asiento").val(ui.item.num_asiento);
                $("#id_cliente").val(ui.item.cliente);
                $("#nombre_cliente").val(ui.item.nomb_client);
                //obtener_detalle_deudas();
                //obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,
        });
    });

    function _showData() {
        var po = "";
        let contador = 0;
        $.ajax({
            type: "post",
            url: "{{route('nota_credito_cliente.newData2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                "id_factura": $("#id_factura").val()
            },
            success: function(data) {
                $("#example2").empty();
                var total = 0;
                for (i = 0; i < data.length; i++) {
                    total += data[i].cantidad * data[i].precio - data[i].descuento;
                    var codigo = data[i].id_ct_productos;
                    var idx = 0;
                    if (data[i].id_ct_productos == undefined) {
                        codigo = data[i].codigo;
                        idx = data[i].id;
                    }
                    var datx = parseFloat(data[i].precio - data[i].descuento);
                    var row = addNewData(i, codigo, data[i].nombre, data[i].cantidad, datx.toFixed(2, 2), idx);
                    $('#example2').append(row);
                    contador++;
                }
                $('#total_final').val(total.toFixed(2, 2));
            },
            error: function(data) {}
        });
    }

    function showData() {
        var po = "";
        let contador = 0;
        $.ajax({
            type: "get",
            url: "{{route('nota_credito_cliente.newData2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                "id_factura": $("#id_factura").val()
            },
            success: function(data) {
                $("#crear").empty();
                $('#crear').append(data.table);
                $('#total_final').val(parseFloat(data.total).toFixed(2, 2));
                document.getElementById('valor_contable').value = parseFloat(data.valor_contable).toFixed(2, 2)
                sumaGlobal()
            },
            error: function(data) {}
        });
    }

    const actualizarPrecio = (val, id, $ite) => {
        $("#precio_total" + $ite).val(val.value);
        $.ajax({
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: `{{route('precio.update')}}`,
            data: {
                'valor': val.value,
                'id': id,
            },
            dataType: "json",
            success: function(data) {
                if (data.err) {
                    val.value = data.valor;

                    alertas('error', 'Error..', 'Error en la actualización de precios');
                }

                sumaGlobal();

            },
            error: function(data) {
                alertas('error', 'Error..', 'Error en la actualización de precios');
            }

        })

    }

    const validateFactura = () => {
        let id_factura = document.getElementById('id_factura').value;
        //var textinputs = document.querySelectorAll('input[type=text]'); 
        let btn_guardar = document.getElementById('boton_guardar_nota');
        $.ajax({
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: `{{route('clientes.notacredit.validarFactura')}}`,
            data: {
                'id_factura': id_factura
            },
            dataType: "json",
            success: function(data) {
                if (data.status == "error") {

                    btn_guardar.disabled = true
                    alertas(data.status, 'Error..', (`trans('contableM.estafactiene') : <br> ${data.msj} <br> trans('contableM.anuleprimparaconti')`).toUpperCase())

                } else {
                    btn_guardar.disabled = false

                }
            }

        })
    }
    const calcIva = id => {
        console.log(id)
        let precio = document.getElementById('precio_' + id);
        let porct_iva = document.getElementById('porct_iva_' + id);
        let iva_producto = document.getElementById('iva_producto' + id);
        let total_producto = document.getElementById('total_producto' + id)
        let total = parseFloat(precio.value) * (porct_iva.value)
        iva_producto.value = parseFloat(total).toFixed(2);
        total_producto.value = parseFloat(precio.value).toFixed(2);
        sumaGlobal()
    }
    const sumaGlobal = () => {
        let cheks = document.querySelectorAll('.checkProducto');
        let precios = document.querySelectorAll('.precios');
        let valorImpuesto = document.querySelectorAll('.iva_producto')
        let valorDescuento = document.querySelectorAll('.descuento')
        let cantidad = document.querySelectorAll('.cantidad')
        let subtotal0 = 0;
        let subtotal12 = 0;
        let impuesto = 0;
        let descuento = 0;
        let mostrarSubtotal0 = document.getElementById('subtotal0')
        let mostrarSubtotal12 = document.getElementById('subtotal12')
        let mostrarSubtotal = document.getElementById('subtotal')
        let mostrarImpuesto = document.getElementById('impuesto')
        let mostrarDescuento = document.getElementById('descuento')
        let mostrarTotal = document.getElementById('total')
        for (let i = 0; i < cheks.length; i++) {
            if (cheks[i].value == 1) {
                if (valorImpuesto[i].value > 0) {
                    console.log(valorImpuesto[i].value)
                    subtotal12 += parseFloat(precios[i].value) * cantidad[i].value;
                    impuesto += parseFloat(valorImpuesto[i].value);
                } else {
                    subtotal0 += parseFloat(precios[i].value) * cantidad[i].value;
                }
                descuento += parseFloat(valorDescuento[i].value);
            }
        }
        mostrarSubtotal0.value = parseFloat(subtotal0).toFixed(2);
        mostrarSubtotal12.value = parseFloat(subtotal12).toFixed(2);
        mostrarDescuento.value = parseFloat(descuento).toFixed(2);
        mostrarSubtotal.value = parseFloat((subtotal0 + subtotal12)).toFixed(2);
        mostrarImpuesto.value = parseFloat(impuesto).toFixed(2);
        mostrarTotal.value = (parseFloat(parseFloat(subtotal0 + subtotal12) - parseFloat(descuento)) + parseFloat(impuesto)).toFixed(2);


        //document.getElementById('total_final').value = mostrarTotal.value

    }


    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function adder(e) {
        var check = $(e).prop('checked');
        var cantidad = 0;
        var precio = 0;
        var total = 0;
        $('.cantidades').each(function() {
            var pir = $(this).parent().parent().find('.verificar').prop('checked');
            var nodata = $(this).parent().parent().find('.ocultosp');

            if (pir) {
                $(this).parent().parent().find('.vercheckbox').val('1');
                nodata.removeClass('ocultos');
                cantidad = parseFloat($(this).val());
                precio = parseFloat($(this).parent().find('.precios').val());
                total += cantidad * precio;
            } else {
                $(this).parent().parent().find('.vercheckbox').val('0');
                if ($('.ocultosp').hasClass('.ocultos')) {

                } else {
                    nodata.addClass('ocultos');
                }

            }

        });
        sumaGlobal();
        // $('#total_final').val(total.toFixed(2,2));
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function addNewData(pos, codigo, nombre, cantidad, precio, id) {
        var markup = "";
        var num = parseInt(pos) + 1;
        //var fech_emision = fecha;
        //var fech_emis_inver = convertDateFormat(fech_emision);  
        if (cantidad > 1) {
            for (i = 0; i < cantidad; i++) {

                markup += ` <tr class="datos">
                                <td>#</td>
                                <td> 
                                    <p>${codigo}</p> 
                                    <input class='fe' type='hidden' name='id[]' value='${id}'> 
                                    <input type='hidden' name='codigo[]' value='${codigo}'> 
                                    <input class='cantidades' type='hidden' name='cantidad[]' value='1'>
                                    <input class='precios' name='precio[]' type='hidden' value='${precio}' > 
                                    <textarea class='form-control  ocultosp' name='descripcion[]' placeholder='Ingrese descripcion' ></textarea> 
                                    <input type='hidden' name='nombre[]' value='${nombre}'> 
                                </td>
                                <td>1</td>
                                <td><p>${nombre}</p></td>
                                <td><p>${precio}</p></td>
                                <td>
                                    <input class='verificar' onclick='adder(this)' type='checkbox' name='verificarx[]' checked value='0'> 
                                    <input class='vercheckbox' type='hidden' name='verificar[]' value='1'> 
                                </td>
                            </tr>`;
                // markup += "<tr>"+
                // "<td>#</td>"+
                // "<td> <p>"+codigo+"</p> <input class='fe' type='hidden' name='id[]' value='"+id+"'> <input type='hidden' name='codigo[]' value='"+codigo+"'> <input class='cantidades' type='hidden' name='cantidad[]' value='1'> <input class='precios' name='precio[]' type='hidden' value='"+precio+"' > <textarea class='form-control  ocultosp' name='descripcion[]' placeholder='Ingrese descripcion' ></textarea> <input type='hidden' name='nombre[]' value='"+nombre+"'> </td>"+
                // "<td>1</td>"+
                // "<td><p>"+nombre+"</p></td>"+
                // "<td><p>"+precio+"</p></td>"+
                // "<td><input class='verificar' onclick='adder(this)' type='checkbox' name='verificarx[]' checked value='0'> <input class='vercheckbox' type='hidden' name='verificar[]' value='1'> </td>"+
                // "</tr>";
            }
        } else {

            markup = `<tr>
                            <td>#</td>
                            <td> 
                                <p>${codigo}</p> 
                                <input class='fe' type='hidden' name='id[]' value='${id}'> 
                                <input type='hidden' name='codigo[]' value='${codigo}'>
                                <input class='cantidades' type='hidden' name='cantidad[]' value='1'> 
                                <input class='precios' name='precio[]' type='hidden' value='${precio}' > 
                                <textarea class='form-control ocultosp' name='descripcion[]' placeholder='Ingrese descripcion'></textarea> 
                            </td>
                            <td>${cantidad}</td>
                            <td><p>${nombre}</p></td>
                            <td><p>${precio}</p></td>
                            <td>
                                <input class='verificar' type='checkbox' onclick='adder(this)' name='verificarx[]' checked value='0'>
                                <input class='vercheckbox' type='hidden' name='verificar[]' value='1'>
                            </td>
                    </tr>`;

            // markup = "<tr>"+
            //     "<td>#</td>"+
            //     "<td> <p>"+codigo+"</p> <input class='fe' type='hidden' name='id[]' value='"+id+"'> <input type='hidden' name='codigo[]' value='"+codigo+"'> <input class='cantidades' type='hidden' name='cantidad[]' value='1'> <input class='precios' name='precio[]' type='hidden' value='"+precio+"' > <textarea class='form-control ocultosp' name='descripcion[]' placeholder='Ingrese descripcion'></textarea> </td>"+
            //     "<td>"+cantidad+"</td>"+
            //     "<td><p>"+nombre+"</p></td>"+
            //     "<td><p>"+precio+"</p></td>"+
            //     "<td><input class='verificar' type='checkbox' onclick='adder(this)' name='verificarx[]' checked value='0'> <input class='vercheckbox' type='hidden' name='verificar[]' value='1'></td>"+
            // "</tr>";
        }


        return markup;
    }

    function buscar_deudas_cliente() {

        var ced_cliente = $("#id_cliente").val();

        $.ajax({
            type: "post",
            url: "{{route('nota_cred_deudas.buscar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                'id_cliente': ced_cliente
            },
            success: function(data) {
                if (data.value != "false") {
                    $("#crear").empty();
                    var fila = 0;
                    var total = 0;
                    for (i = 0; i < data[4].length; i++) {
                        var row = addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].numero, data[4][i].nro_comprobante, data[4][i].valor_contable, data[4][i].id);
                        $('#example2').append(row);
                        fila = i;
                        //total = total+data[4][i].valor_contable;
                    }
                    $("#contador_a").val(fila);
                    //$("#total_deudas").val(total.toFixed(2));

                } else {
                    $("#crear").empty();
                    $("#total_deudas").val('0.00');
                    $("#total_abonos").val('0.00');
                    $("#total_nuevo_saldo").val('0.00');
                }
            },
            error: function(data) {}
        });

    }

    function suma_deudas_cliente() {
        var id_client = $("#id_cliente").val();

        $.ajax({
            type: "post",
            url: "{{route('suma_deudas.clientes')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: "json",
            data: {
                'id_cliente': id_client
            },
            success: function(data) {
                if (data.value != "false") {
                    $("#total_deudas").val(data.total_deuda);
                } else {
                    $("#total_deudas").val('0.00');
                }
            },
            error: function(data) {}
        });

    }

    //Calcula Total Deuda
    /*function obtener_total_deuda(contador){
        var total_deuda = 0;

        total_deuda = total_deuda+contador;
        alert(total);

        return total_deuda;

    }*/

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


    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function sumar_impuesto() {

        var subtotal = parseFloat($("#subtotal").val());
        if (isNaN(subtotal)) {
            subtotal = 0;
        }
        var impuesto = parseFloat($("#impuesto").val());
        if (isNaN(impuesto)) {
            impuesto = 0;
        }
        $("#impuesto").val(impuesto.toFixed(2, 2));
        var s = subtotal * '{{$valorIva}}';
        var t = s + subtotal;
        var total = subtotal + impuesto;
        if (total == t) {
            $("#total").val(total.toFixed(2, 2));
            if (!isNaN(s)) {
                $('#tar_iva_12').val(s.toFixed(2));
            }
            $("#total_credito").val(total.toFixed(2, 2));
        } else {
            total = subtotal;
            impuesto = 0;
            $("#impuesto").val(impuesto.toFixed(2, 2));
            $("#total").val(total.toFixed(2, 2));
            if (!isNaN(s)) {
                $('#tar_iva_12').val(s.toFixed(2));
            }
            $("#total_credito").val(total.toFixed(2, 2));

        }
    }

    //Calcular Total Credito Ingresando Impuesto
    /*function calculartotalcredito(){

        var sb12 = 0;

        var subtotal = parseFloat($("#subtotal").val());
        var impuesto = parseFloat($("#impuesto").val());
        
        var totalimp = subtotal*impuesto;
        var total_final = subtotal+totalimp; 
       
        if(!isNaN(total_final)){ $('#total').val(total_final.toFixed(2));}
        if(!isNaN(totalimp)){ $('#tar_iva_12').val(totalimp.toFixed(2));}
        $("#total_credito").val(total_final.toFixed(2,2));

    }*/

    //ANADIR REGISTRO A LA TABLA DETALLE DE DEUDAS DEL CLIENTE
    function addNewRow(pos, fecha, valor, factura, fact_numero, observacion, valor_nuevo, id_final) {
        var markup = "";
        var num = parseInt(pos) + 1;
        //var fech_emision = fecha;
        //var fech_emis_inver = convertDateFormat(fech_emision);  

        markup = "<tr>" +
            "<td> <input class='form-control' type='text' name='emision" + pos + "' id='emision" + pos + "' readonly='' value='" + fecha + "'> <input type='hidden' name='id_actualiza" + pos + "' id='id_actualiza" + pos + "' value='" + id_final + "'></td>" +
            "<td> <input class='form-control' type='text' name='vence" + pos + "' id='vence" + pos + "' value='" + fecha + "' readonly=''> </td>" + "<td> <input class='form-control' type='text' name='tipo_a" + pos + "' id='tipo_a" + pos + "' value='VEN-FA' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='numero" + pos + "' id='numero" + pos + "' value='" + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='observacion" + pos + "' id='observacion" + pos + "' value='Fact:" + fact_numero + " Ref: " + observacion + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='div" + pos + "' id='div" + pos + "' value='$' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' name='saldo_a" + pos + "' value='" + valor + "' id='saldo_a" + pos + "' readonly=''> </td>" +
            "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='abono_a" + pos + "' id='abono_a" + pos + "' value='0.00' onchange='validar_td2(" + pos + ")'></td>" +
            "<td> <input style='width: 77%;' class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" + pos + "' value='0.00' id='nuevo_saldo" + pos + "' readonly=''></td>" +
            "</tr>";
        return markup;
    }

    function convertDateFormat(string) {
        var info = string.split('-');
        return info[2] + '/' + info[1] + '/' + info[0];
    }

    //BUSQUEDA POR NOMBRE
    $(document).on("focus", "#nombre_cliente", function() {

        $("#nombre_cliente").autocomplete({

            source: function(request, response) {
                $.ajax({
                    type: 'GET',
                    url: "{{route('notacredito.buscarcliente')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                $("#id_cliente").val(ui.item.id);
                buscar_deudas_cliente();
                suma_deudas_cliente();
            },
            selectFirst: true,
            minLength: 1,
        });

    });

    //ANADIR DETALLE DE RUBROS DE CREDITO
    function crea_td(contador) {

        id = document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id", "dato" + id);

        //Creamos tabla temporal
        midiv.innerHTML = '<td><input required type="hidden" id="visibilidad' + id + '" name="visibilidad' + id + '" value="1"><input style="width: 98%;" onchange="set_codigo(' + id + ')" class="codigo" name="codigo' + id + '" id="codigo' + id + '"></td><td> <input style="width: 98%;" name="rubro' + id + '" id="rubro' + id + '" onchange="set_rubros(' + id + ')" class="rubro"></td><td><input style="width: 98%;" name="detalle' + id + '" id="detalle' + id + '" autocomplete="off"></td><td><input style="width: 98%;background-color: #c9ffe5" name="divisas" id="divisas" value="USD" readonly></td><td><input name="valor' + id + '" style="width: 98%;background-color: #c9ffe5" id="valor' + id + '" onchange="valor_rubro(' + id + ')" value="0.00" autocomplete="off"></td><td><input style="width: 90%;" name="total_base' + id + '" id="total_base' + id + '" value="0.00" readonly></td><td style="width: 40px;"><button type="button" onclick="eliminar_det_rubro(' + id + ')" class="btn btn-light"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';

        document.getElementById('det_recibido').appendChild(midiv);
        id = parseInt(id);
        id = id + 1;

        document.getElementById('contador').value = id;
        //Completa el codigo detalle rubro credito
        $(".codigo").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('rubros_cliente.codigo')}}",
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
        //Completa el nombre detalle rubro credito
        $(".rubro").autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('rubros_cliente.nombre')}}",
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

    }

    //ELIMINA REGISTRO DETALLE RUBRO CREDITO
    function eliminar_det_rubro(valor) {

        var dato_item1 = "dato" + valor;
        var dato_item2 = "visibilidad" + valor;
        document.getElementById(dato_item1).style.display = 'none';
        document.getElementById(dato_item2).value = 0;
        //var contad = document.getElementById('contador').value;
        //contad = contad-1;
        //$("#contador").val(contad);
        sumar();
    }


    //SETEA NOMBRE A INGRESAR CODIGO
    function set_codigo(id) {
        $.ajax({
            type: 'post',
            url: "{{route('rubros_cliente.codigo2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo" + id).val()
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#rubro" + id).val(data[0]);
                }
            },
            error: function(data) {}
        })
    }

    //SETEA CODIGO A INGRESAR NOMBRE
    function set_rubros(id) {
        $.ajax({
            type: 'post',
            url: "{{route('rubros_cliente.nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#rubro" + id).val()
            },
            success: function(data) {
                if (data.value != 'no') {
                    $("#codigo" + id).val(data[0]);
                }
            },
            error: function(data) {}
        })
    }


    //GUARDA NOTA DE CREDITO CLIENTES
    function guarda_nota_credito(e) {
        $('#boton_guardar_nota').attr("disabled", true);
        /*if((sumas ==0)||(sumas<total_credito)){
            msj+="Por favor, La suma de los abonos debe ser igual al Total de Crédito.<br/>";
        }*/
        ///////aqui ando ¿
        if ($('#form_nota_credito_cl').valid()) {
            /*  $(e).hide(); */
            $.ajax({
                type: 'post',
                url: "{{route('nota_cliente_debito.newstore')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#form_nota_credito_cl').serialize(),
                success: function(data) {
                    if (data.status == 'success') {
                        document.getElementById("id_nota_credito").value = data.id
                        document.getElementById("num_asiento").value = data.id_asiento
                        $("#boton_guardar_nota").hide();
                        document.getElementById("numero_secuencia").value = data.numero;
                        let msj_sri = '';
                        if (data.status_sri != '') {
                            msj_sri = `<b>SRI: </b> <br>trans('agenda.estado'): ${data.status_sri} <br> <b>trans('contableM.mensaje'):</b> ${data.msj_sri} <br>`;
                        }
                        alertas(data.status, "Guardado con exito...", `${data.msj} <br> ${msj_sri}`)
                        // if(data.sri!=""){
                        //     if(data.sri.original.comprobante!=""){
                        //         swal('Mensaje','Correcto envio Sri. Comprobante #:'+data.sri.original.comprobante,'success');
                        //         //swal(`{{trans('contableM.correcto')}}!`,"Nota de Crédito generada con exito","success");
                        //         $('#form_nota_credito_cl input').attr('readonly', 'readonly');
                        //         $("#boton_guardar_nota").attr("disabled", true);
                        //     }else{
                        //         // if(data.sri.original.reason!=""){
                        //         //     swal('Mensaje',data.sri.original.reason,'error');
                        //         // }
                        //     }

                        // }else{
                        //     //swal(`{{trans('contableM.correcto')}}!`,"Nota de Crédito generada con exito","success");
                        //    // alertas(ms)
                        //     $('#form_nota_credito_cl input').attr('readonly', 'readonly');
                        //     $("#boton_guardar_nota").attr("disabled", true);
                        // }      
                    } else {
                        alertas(data.status, "Error...", data.msj)
                        $('#boton_guardar_nota').removeAttr("disabled");
                    }
                },
                error: function(data) {
                    $('#boton_guardar_nota').removeAttr("disabled");
                }
            })



        }

    }

    //VERIFICA QUE INGRESE VALORES EN LA TABLA DETALLE DE RUBROS CREDITO
    /*function validar_ing_det_rub(){

        cont_tabla = document.getElementById('contador').value;
        
        if(cont_tabla == 0){
            swal("¡Error!","Ingrese datos en la tabla","error");
        }
    
    }*/


    //SETEA VALORES VALOR,TOTAL BASE,SUBTOTAL
    function valor_rubro(id) {

        var e = parseFloat($("#valor" + id).val());

        if (e == "") {
            e = 0;
        }
        if (isNaN(e)) {
            e = 0;
        }

        $("#valor" + id).val(parseFloat(e).toFixed(2, 2));
        $("#total_base" + id).val(parseFloat(e).toFixed(2, 2));
        $("#subtotal").val(parseFloat(e).toFixed(2, 2));

        sumar();
        //sumar2();

    }

    //Funcion Sumar 
    function sumar() {

        var contador = 0;
        var iva = parseFloat($("#iva_par").val());
        var ivan = 0;
        //var total=0;
        //var totaal=0;
        var sub = 0;
        var valor_d = 0;
        var ivaf = 0;

        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();

            if (visibilidad == 1) {
                valor = parseFloat($(this).find('#valor' + contador).val());
                ivan = ivan + valor;
                sub = sub + valor;
                //totaal= valor+ivan;
                //total= total+totaal; 
            }
            contador = contador + 1;
        });

        //var totalsx = ivan*iva;
        var totalsx = ivan;
        //var total_final= totalsx+ivan; 
        var total_final = totalsx;
        //if(!isNaN(totalsx)){ $('#impuesto').val(totalsx.toFixed(2));}
        //subtotal->Subtotal 0%
        if (!isNaN(sub)) {
            $('#subtotal').val(sub.toFixed(2));
        }
        if (!isNaN(total_final)) {
            $('#total').val(total_final.toFixed(2));
        }
        if (!isNaN(total_final)) {
            $("#total_credito").val(total_final.toFixed(2, 2));
        }
        if (!isNaN(sub)) {
            $('#sub_sin_imp').val(sub.toFixed(2));
        }
        $('#impuesto').val('0.00');
        /*if(total_d>0){
            if(!isNaN(sub)){ $('#subtotal').val(sub.toFixed(2));   }
            if(!isNaN(total_final)){ $('#total').val(total_final.toFixed(2));   }
            if(!isNaN(total_final)){ $("#total_credito").val(total_final.toFixed(2,2));}
            if(total_final > total_d){
                swal("¡Error!","El Total de Crédito no debe superar al Total de la Deuda","error")
                $('#subtotal').val('0.00');
                $('#impuesto').val('0.00');
                $('#total').val('0.00');
            }
        }*/
    }

    //Funcion Sumar 2 
    function sumar2() {
        var contador = 0;
        var iva = parseFloat($("#iva_par").val());
        var ivan = 0;
        var total = 0;
        var totaal = 0;
        var sub = 0;
        var valor_d = 0;
        var ivaf = 0;
        $("#det_recibido tr").each(function() {
            $(this).find('td')[0];
            visibilidad = $(this).find('#visibilidad' + contador).val();
            if (visibilidad == 1) {
                valor = parseFloat($(this).find('#total_base' + contador).val());
                ivan = ivan + valor;
                sub = sub + valor;
                totaal = valor + ivan;
                total = total + totaal;
            }
            contador = contador + 1;
        });
        var totalsx = ivan * iva;
        var total_final = totalsx + ivan;
        if (!isNaN(total)) {
            $('#total_suma').val(total_final.toFixed(2));
        }
        //$("#total_credito").val(total.toFixed(2,2));
    }


    //BUSCAR PARAMETRO ID - NUMERO SECUENCIA - NUMERTO ASIENTO
    function buscar_parametros(id_nota_cred) {

        $.ajax({
            type: 'post',
            url: "{{route('nota_credito_cliente.buscarparametros')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_nota': id_nota_cred
            },
            success: function(data) {
                $("#id_nota_credito").val(data.id_nota);
                $("#numero_secuencia").val(data.secuencia);
                $("#num_asiento").val(data.id_asiento);
            },
            error: function(data) {}
        });

    }

    //CREACION NUEVA NOTA DE CREDITO
    function nueva_nota_credito() {
        location.href = "{{route('nota_credito_cliente.create2')}}";
    }

    //VALIDAR ABONO
    function validar_td2(id) {

        if ((id) != null) {
            var valor = parseFloat($("#total").val());
            var saldo = parseFloat($("#saldo_a" + id).val());
            var abono = parseFloat($("#abono_a" + id).val());
            //var total_deuda = 0;
            suma_totales2()
            var cantidad = parseFloat($("#total_suma_a").val());

            if (valor > 0) {
                if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {

                    if (cantidad <= valor) {

                        if (abono > saldo) {
                            swal("¡Error!", "trans('contableM.elabononodebesupelsaldo')", "error")
                            //$("#abono_a"+id).val('0.00');
                            $("#abono_a" + id).val(saldo.toFixed(2, 2));
                            suma_totales2();
                            //abono=saldo;
                        } else {
                            $("#abono_a" + id).val(abono.toFixed(2, 2));
                            nuevo_saldo = saldo - abono;
                            $("#nuevo_saldo" + id).val(nuevo_saldo.toFixed(2, 2));
                            suma_totales3();
                            //total_deuda = total_deuda+saldo;
                            //$("#total_deudas").val(total_deuda.toFixed(2));
                        }

                    } else {
                        valor = 0;
                        //$("#abono_a"+id).val(valor.toFixed(2,2));
                        $("#abono_a" + id).val('0.00');
                        swal("¡Error!", "trans('contableM.lasumdeabononodebesupeltotalcr')", "error")

                    }

                } else {
                    abono = 0;
                    valor = 0;
                    $("#abono_a" + id).val(valor.toFixed(2, 2));


                }
            } else {
                swal("¡Error!", "trans('contableM.debecalcutotalcre')", "error")
                $("#abono_a" + id).val('0.00');

            }
        }

    }


    function suma_totales2() {
        contador = 0;
        cantidad = 0;
        total = 0;
        $("#crear tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#abono_a" + contador).val());

            if (!isNaN(cantidad)) {
                total += cantidad;
            }

            contador = contador + 1;

        });

        if (isNaN(total)) {
            total = 0;
        }

        $("#total_suma_a").val(total.toFixed(2, 2));
        $("#total_abonos").val(total.toFixed(2, 2));

    }

    function suma_totales3() {

        contador = 0;
        cantidad = 0;
        total = 0;

        $("#crear tr").each(function() {
            $(this).find('td')[0];
            cantidad = parseFloat($("#nuevo_saldo" + contador).val());

            if (!isNaN(cantidad)) {
                total += cantidad;
            }

            contador = contador + 1;

        });

        if (isNaN(total)) {
            total = 0;
        }
        $("#total_nuevo_saldo").val(total.toFixed(2, 2));
    }
    //Obtener Caja Sucursal
    function obtener_caja() {
        var id_sucursal = $("#sucursal").val();
        $.ajax({
            type: 'post',
            url: "{{route('caja.sucursal')}}",
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
                            $("#punto_emision").append(`<option ${registro.codigo_sucursal+'-'+registro.codigo_caja == '001-100' ? 'selected' : ''} value='${registro.id}'>${registro.codigo_sucursal + '-' + registro.codigo_caja}</option>`);
                        });
                    } else {
                        $("#punto_emision").empty();
                    }
                }
            },
            error: function(data) {}
        })

    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

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

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_hoy").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            let fechaAc = new Date();
            if (fechaFinal < fechaInicial) {
                fechaInicial.setDate(fechaInicial.getDate() + 1);
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_hoy").value = fechaFormateada;
                alertas('error', "Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.');
            }
        <?php
        }
        ?>
    }
</script>
@endsection