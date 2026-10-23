@extends('contable.comp_egreso_masivo.base')
@section('action-content')
@php
$date = date('Y-m-d');
$h = date('Y-m',strtotime($date));
@endphp
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

        .alerta_correcto {
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
        }
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

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* .form-control{
            background-color: #f5f5f5;
        }*/

    /* On mouse-over, add a grey background color */
    .container:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .text {
        color: white;
        padding: 10px;
        background-color: green;
        font-size: 15px;
        font-family: helvetica;
        font-weight: bold;
        text-transform: uppercase;
    }

    * {
        font-family: var(--fuentePrincipal);
    }

    .input-text {
        background: white !important;
        border-radius: 13px;
    }

    .url-compras {
        /* background: #00c0ef; */
        padding: 2px 10px;
        color: white;
        font-weight: bolder;
        border-radius: 10px;
    }

    .contenedor-box {
        box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -moz-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
        padding: 18px 30px;
        border-radius: 7px;
        margin-bottom: 12px;
    }
</style>
<input type="hidden" id="fechita" value="{{$date}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('comp_egreso_masivo.index')}}">Comprobante de Egreso Masivo</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Nuevo Comprobante de Egreso Masivo</li>
        </ol>
    </nav>
    <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Guardado Correctamente
    </div>
    <form method="POST" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 col-sm-9 col-6">
                            <div class="box-title "><b>COMP. DE EGRESOS DE ACREEDORES MASIVOS</b></div>
                        </div>
                        <div class="col-md-4 text-left">
                            <span class="parpadea text" id="boton">{{$h}}</span>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" id="botong" class="btn btn-success" onclick="guardarEgreso()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </button>
                                <a type="button" class="btn btn-info " href="{{route('comp_egreso_masivo.newCreate2')}}" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                </a>
                                <a type="button" class="btn btn-danger" href="{{ route('comp_egreso_masivo.index') }}" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12">
                        <div class="form-row ">
                            <div class=" col-md-2 px-1">
                                <label class="label_header">Estado</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value=" ACR-EG-MA " readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control " type="text" id="asiento" name="asiento" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input class="form-control input-sm " id="fecha_hoy" onblur="validarPeriodo();validarCierre();" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date)) : '' }}" type="date" name="fecha_hoy" value="{{date('Y-m-d')}}">
                                <input type="hidden" name="total_favor" id="total_favor">
                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-10 px-0">
                                <label class="label_header" for="acreedor">Concepto:</label>
                                <input class="form-control  col-md-12 validar" type="text" name="concepto" autocomplete="off" id="aaa">
                            </div>
                            <div class=" col-md-2 px-0">
                                <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                <!-- <label class="container col-md-12">Cheque entregado -->
                                <label class="label_header" for="acreedor">Cheque entregado</label>
                                <div style="text-align:center">
                                    <input style="width:24px; height:24px;" class="spropety" type="checkbox" id="cheque_entregado" checked name="cheque_entregado">
                                </div>
                                <!-- <span class="checkmark"></span> -->

                                </label>
                            </div>
                            <div class=" col-md-4  px-0">
                                <label class="col-md-12 label_header" for="ruc">Banco:</label>
                                <select class="form-control validar" name="banco" id="banco">
                                    <option value="0">Seleccione..</option>
                                    @foreach($bancos as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-0">
                                <label class="col-md-12 label_header" for="secuencia">Divisas:</label>
                                <select class="form-control col-md-12 validar" name="divisas" id="divisas">
                                    <option value="0">Seleccione...</option>
                                    @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="secuencia">Cambio:</label>
                                <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header control-label" for="numero_cheque">Cheque No:</label>
                                <input class="form-control " type="number" name="numero_cheque" id="numero_cheque">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="fecha_cheque">Fecha Cheque: </label>
                                <input class="form-control validar" type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="valor" class="label_header">Valor</label>
                                <input class="form-control " type="text" autocomplete="off" readonly value="0.00" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="abono" class="label_header">Abono</label>
                                <input class="form-control " type="text" autocomplete="off" readonly value="0.00" name="abono" id="abono" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="girado"> Girado a:</label>
                                <input class="form-control " type="text" name="giradoa" id="giradoa">
                            </div>
                            <div class="col-md-4 px-1">
                                <label for="" class="control-label label_header">Formas de Pago</label>
                                <select name="formas_pago" id="formas_pago" class="form-control validar">
                                    <option value="">Seleccione..</option>
                                    @foreach($formas_pago as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                                <select name="sucursal" id="sucursal" class="form-control" require>
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->codigo_sucursal}}">{{$sucursal->codigo_sucursal}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="total_suma" id="total_suma">
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 16px;">
                        <div class="col-md-12" style="padding: 0px;">
                            <label class="label_header" for="detalle_deuda">BUSCADOR</label>
                        </div>
                        <div class="col-md-12" style="padding: 10px; display: flex;justify-content: center;">
                            <div class="col-md-6">
                                <div class="col-md-3">
                                    <label for="acreedor"><abbr title="PUEDE BUSCAR LOS COMPROBANTE POR: ACREEDOR, FACTURA, RUC">BUSCADOR: </abbr></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-text" id="buscador" placeholder="Escriba como minimo 3 caracteres..." onkeyup="buscadorDetalles()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!---------------------------------------CAMBIOS BUSCADOR DE FACTURAS---------------------------------------------->
                    <div class="col-md-12 contenedor-box">
                        <div class="col-md-12" style="padding: 0px;">
                            <label class="label_header" for="detalle_deuda">DETALLES DE BUSCADOR</label>
                        </div>

                        <div class="table-responsive col-md-12 px-1">
                            <table id="example2" class="px-1" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 5%; text-align: center;">Compra</th>
                                        <th style="width: 8%; text-align: center;">Acreedor</th>
                                        <th style="width: 6%; text-align: center;">Vence</th>
                                        <th style="width: 5%; text-align: center;">Tipo</th>
                                        <th style="width: 15%; text-align: center;">Concepto</th>
                                        <th style="width: 2%; text-align: center;">Div</th>
                                        <th style="width: 10%; text-align: center;">Saldo</th>
                                        <!-- <th style="width: 10%; text-align: center;">Abono</th> -->
                                        <th style="width: 10%; text-align: center;">Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody id="mostrar_detalle">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- <a href="http://127.0.0.1:8000/contable/contabilidad/libro/revision/3825" class="label label-info">3825</a> -->
                    <!---------------------------------------FIN CAMBIOS BUSCADOR DE FACTURAS---------------------------------------------->

                    <div class="col-md-12 ">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL PROVEEDOR</label>

                        <input type="hidden" name="id_compra" id="id_compra">

                        <div class="table-responsive col-md-12 px-1">
                            <table id="detalle_proveedor" class="px-1" style="width: 100%;" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;">
                                    <tr style="position: relative;">
                                        <th style="width: 5%; text-align: center;">Compra</th>
                                        <th style="width: 8%; text-align: center;">Acreedor</th>
                                        <th style="width: 6%; text-align: center;">Vence</th>
                                        <th style="width: 5%; text-align: center;">Tipo</th>
                                        <th style="width: 15%; text-align: center;">Concepto</th>
                                        <th style="width: 2%; text-align: center;">Div</th>
                                        <th style="width: 10%; text-align: center;">Saldo</th>
                                        <th style="width: 6%; text-align: center;">Abono</th>
                                        <th style="width: 5%; text-align: center;">Accion</th>

                                    </tr>
                                </thead>
                                <tbody id="crear">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8" style="text-align: end;">Total Abono:</th>
                                        <th><input style="text-align: end;border: none;background: none;" name="abono_total" type="text" readonly id="abono_total" value="0.00"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">
    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_hoy").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal < fechaInicial) {
                swal.fire("Recuerde!", 'Este registro no se puede modificar, ya que la fecha seleccionada corresponde a un período cerrado contablemente. Para hacer cambios, elija una fecha dentro del período contable vigente.', "error");
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_hoy").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    function validarPeriodo() {
        $fechaAc = new Date();
        $mes = $fechaAc.getUTCMonth() + 1;
        var fechaInput = document.getElementById("fecha_hoy").value;
        var partesFecha = fechaInput.split('/');
        var fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        var d = new Date(fechaFormateada);
        var month = d.getMonth() + 1;
        console.log(month);
        var ty = document.getElementById("fechita").value;
        if ($mes != month) {
            return swal.fire("Recuerde!", '<?= trans('contableM.lafechaingreestafueradelperio'); ?>', "error")
        }
    }

    const buscadorDetalles = () => {
        let buscador = document.getElementById("buscador").value;
        // console.log(buscador.length)

        if (buscador.length <= 3) {
            $("#mostrar_detalle").empty();
            return;
        }
        $.ajax({
            url: `{{ route('comp_egreso_masivo.buscadorDetalles') }}`,
            type: "get",
            dataType: "json",
            data: {
                'buscador': buscador
            },
            success: function(data) {
                $("#mostrar_detalle").empty();
                if (data.status == 'success') {
                    $("#mostrar_detalle").append(data.table);
                }
            },
            error: function(data) {
                return;
            }
        })
    }
    const agregarFila = id => {
        let id_compra = document.getElementById(id);
        if (id_compra != null) {
            alertas("error", "Error...", `Ya se encuentra agregada la factura`);
            return
        }
        let url_compra = `{{url('contable/compras/editar/${id}')}}`;
        let proveedor = document.getElementById("proveedor_" + id).value
        let fecha_cad = document.getElementById("fecha_" + id).value
        let tipo = document.getElementById("tipo_" + id).value
        let factura = document.getElementById("factura_" + id).value
        let saldo = document.getElementById("saldo_" + id).value
        let buttonPlud = `<button id="button_${proveedor.replace(/\s/g, '')}" style='padding: 1px 20px;border-radius: 16px;' type='button' onclick='agregarAbono(${id},"${proveedor}")' class='btn btn-warning'>
                        <i class="fa fa-plus"></i>
                    </button>`;


        let variableP = document.getElementById(`button_${proveedor.replace(/\s/g, '')}`);


        let fila = `
            <tr class='tr_${proveedor.replace(/\s/g, '')}' >
                <td style='text-align: center;'>
                    <a class='label label-info url-compras' target='_blank' href='${url_compra}'>${id}</a>
                    <input type='hidden' name='id_compra[]' id="${id}" value='${id}'>
                </td>
                <td> <input class='form-control'  value='${proveedor}'></td>
                <td> <input class='form-control' id='' type='text' value='${fecha_cad}' readonly> </td>
                <td> <input class='form-control' type='text' id='' value='${tipo}' readonly> </td>
                <td> <input class='form-control' id='' type='text' value='${factura}' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5;' type='text' value='$' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5; width: 100%' type='text' id='' value='${saldo}' disabled> </td>
                <td> 
                    <input name='abono[]' class='form-control abonos validar' style='background-color: #fff; width: 100%; font-weight: 700;' type='text' id='abono_${id}' 
                    value='${saldo}' onblur="setNumber(this)" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="calcularAbonos()"> 
                </td>
                <td style='text-align: center;'>
                    <button style='padding: 1px 20px;border-radius: 16px;' type='button' onclick='eliminarFila(this)' class='btn btn-danger'>
                        <i class="fa fa-trash"></i>
                    </button>

                    ${ variableP === null ?  buttonPlud  : ''}
                   
                </td> 
            </tr>`;



        let variable = document.getElementsByClassName(`tr_${proveedor.replace(/\s/g, '')}`);


        console.log(variable);
        if (variable.length == 0) {
            $("#crear").append(fila);
        } else {
            $(variable[variable.length - 1]).after(fila);
        }


        calcularAbonos();
        $('.tr_' + id).css({
            background: '#d4d4d4'
        });
    }


    const eliminarFila = e => {
        /*  let fila_delete = document.querySelectorAll(".tr_" + id)
         
         if (fila_delete != null || fila_delete != undefined) {
             fila_delete.forEach(element => {
                 element.remove();
                
             });
             $('.tr_'+proovedor).css({background:'#FFFFFF'});
         } */
        $(e).parent().parent().remove();
        calcularAbonos();
    }

    function setNumber(e) {
        if (e.value == "") {
            e.value = "0.00";
        }
    }
    const calcularAbonos = () => {
        let abonos = document.querySelectorAll(".abonos");
        let abono_total = document.getElementById("abono_total")
        let abono_nuevo = document.querySelectorAll(".abonos_agregar");
        let nuevo_valor = 0;
        if (abonos.length == 0) {
            abono_total.value = "0.00";
            document.getElementById("valor_cheque").value = "0.00";
            return;
        }
        let sumaTotal = 0;
        for (let i = 0; i < abonos.length; i++) {
            sumaTotal += parseFloat(abonos[i].value)
        }
        for (const n of abono_nuevo) nuevo_valor += parseFloat(n.value)



        abono_total.value = sumaTotal.toFixed(2);
        document.getElementById("valor_cheque").value = sumaTotal.toFixed(2);
        document.getElementById("abono").value = nuevo_valor.toFixed(2);
    }
    const guardarEgreso = () => {
        var sucursal = $("#sucursal").val();
        if (sucursal == 0 || sucursal == "") {
            alertas("error", "Error...", "Seleccione la sucursal")
            return;
        }
        if (validarDatos()) {
            alertas("error", "Error...", "Faltan campos por llenar")
            return;
        }
        let abonos = document.querySelectorAll(".abonos");
        if (abonos.length == 0) {
            alertas("error", "Error...", "Debe seleccionar al menos un comprobante para guardar")
            return;
        }
        Swal.fire({
            icon: 'question',
            text: 'Esta segur@ de guardar los datos?',
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'post',
                    url: "{{route('compra.egresoMasivo.newStore2')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            text: "Cargando...",
                            imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                            imageWidth: 200,
                            imageHeight: 200,
                            imageAlt: 'Custom image',
                        })
                    },
                    data: $('#form_guardado').serialize(),
                    success: function(data) {
                        Swal.close();
                        if (data.status != "success") {
                            alertas("error", "Error...", data.msj)
                            return;
                        }
                        let url_asiento = `<a href="{{url('contable/contabilidad/libro/revision/${data.datos.id_asiento}')}}" target="_blank">${data.datos.id_asiento}<a>`
                        let url_comprobante = `{{url('contable/comp_egreso_masivo/pdf_egreso_masivo/${data.datos.id_comprobante}')}}`;
                        var win = window.open(url_comprobante, '_blank');
                        // Cambiar el foco al nuevo tab (punto opcional)
                        win.focus();
                        document.getElementById("id_factura").value = data.datos.id_comprobante;
                        document.getElementById("numero_factura").value = data.datos.numero;
                        document.getElementById("asiento").value = data.datos.id_asiento;
                        alertas("success", "Exito...", `${data.msj}<br> Asiento: <b>${url_asiento}</b>`)
                    },
                    error: function(data) {}
                })
            }
        })
    }




    function eliminarFilaAbono(id_eliminar) {


        let variable = document.getElementsByClassName(`tr_abono_${id_eliminar}`);
        while (variable.length > 0) variable[0].remove();
        calcularAbonos();
    }

    function agregarAbono(id, proovedor) {

        let html = `
            <tr class='tr_abono_${proovedor.replace(/\s/g, '')}' >
                <td style="display:flex;justify-content:center;align-items: center;flex-direction:column">
                    <span style="background-color:#138535" class="badge badge-pill badge-primary">${id}-Abono</span>
                </td>
                <td colspan="2" style="background-color:#eafcff;border: 1px solid #ccc;">
                <span style="font-weight:bold">Agregar Abono</span>
                </td>
                <td colspan="4"  style="background-color:#eafcff;border: 1px solid #ccc;">    
                    <input type="hidden" name='proveedor[]' value="${proovedor}">
                    ${proovedor}
                </td>
                <td>
                    <input name='agg_abono[]' class='form-control abonos_agregar' style='background-color: #fff; width: 100%; font-weight: 700;' type='text' id='agregar_abono${id}'
                    onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="calcularAbonos()"> 
                </td>
                <td style="text-align:center">
                    <a href='javascript:eliminarFilaAbono("${proovedor.replace(/\s/g, '')}")'>
                        <i class="fa fa-minus-circle" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
        `;

        let variable = document.getElementsByClassName(`tr_abono_${proovedor.replace(/\s/g, '')}`);
        if (variable.length == 0) {
            let variable_ver = document.getElementsByClassName(`tr_${proovedor.replace(/\s/g, '')}`);

            if (variable_ver.length != 0) {
                $(variable_ver[variable_ver.length - 1]).after(html);
            }

        }
    }
</script>
@endsection