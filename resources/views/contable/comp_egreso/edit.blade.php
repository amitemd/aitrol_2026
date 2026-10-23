@extends('contable.comp_egreso.base')
@section('action-content')
<?php

use Sis_medico\Contable;
?>
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

    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
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
</style>
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
    function enviar_correo() {

        Swal.fire({
            title: '¿Desea enviar correo a {{$comprobante_egreso->proveedor->razonsocial}} ?',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return fetch(`{{route('egreso_enviar_email',['id'=>$comprobante_egreso->id])}}`)
                    .then(response => {
                        //console.log(response);
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire("Bien!", "Envio correcto", "success");
            }

        })

    }
</script>
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('acreedores_cegreso')}}">Comprobante de Egreso</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuevo Comprobante de Egreso</li>
        </ol>
    </nav>
    <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Guardado Correctamente
    </div>
    <form class="form-vertical " method="post" id="form_guardado">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="box-title "><b>VISUALIZADOR COMP. DE EGRESOS ACREEDORES</b></div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">

                                @if($comprobante_egreso->anulado_tipo != 1)
                                @if($empresa->id != "0992704152001")
                                <div class=" col-md-2  px-1" style="margin: 0px 30px;">
                                    <label class="col-md-12 label_header" for="ret_asumida">Rt. Asumidas</label>
                                    @if($comprobante_egreso->rt_asumida_estado == 1)
                                    <input style="text-align: center;" class="form-control" disabled type="text" id="ret_asumida" value="SI">
                                    @else
                                    <input style="text-align: center;" class="form-control" disabled type="text" id="ret_asumida" value="NO">
                                    @endif
                                </div>
                                @endif
                                @endif

                                @if(!is_null($comprobante_egreso->id_asiento_cabecera) || $comprobante_egreso->id_asiento_cabecera != "")
                                <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',[$comprobante_egreso->id_asiento_cabecera])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                    <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diaro
                                </a>
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('librodiario.edit',['id'=>$comprobante_egreso->id_asiento_cabecera])}}" target="_blank">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;&nbsp;Editar Asiento diaro
                                </a>
                                @endif
                                @if($comprobante_egreso->estado==1)
                                <button type="button" onclick="enviar_correo()" class="btn btn-default btn-gray"><i class="fa fa-envelope"></i> &nbsp; &nbsp; Enviar </button>
                                @endif
                                <button type="button" class="btn btn-success  btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                </button>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="row header">
                    <div class="col-md-12 px-1">
                        <div class="form-row ">
                            <div class=" col-md-2 px-1">
                                <label class="label_header">Estado</label>
                                <div style="@if($comprobante_egreso->anulado_tipo == 1) background-color: yellow; @elseif(($comprobante_egreso->estado)==1) background-color: green; @else background-color: red;  @endif " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="col-md-12 label_header" for="id_factura">ID:</label>
                                <input class="form-control " type="text" name="id_factura" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->id}} @endif" id="id_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">

                                <label class="label_header" for="numero_factura">Número</label>
                                <input class="form-control " type="text" id="numero_factura" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->secuencia}} @endif" name="numero_factura" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="tipo">Tipo:</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">Asiento</label>
                                <input class="form-control " type="text" id="asiento" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->id_asiento_cabecera}} @endif" name="asiento" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">Fecha: </label>
                                <input disabled class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="@if(!is_null($comprobante_egreso)){{$comprobante_egreso->fecha_comprobante}}@endif">
                            </div>
                        </div>
                        <div class="form-row " id="no_visible">
                            <div class=" col-md-10 px-0">
                                <label class="label_header" for="acreedor">Concepto:</label>
                                <input class="form-control  col-md-12" type="text" name="aaa" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->descripcion}} @endif" autocomplete="off" id="aaa">
                            </div>
                            <div class=" col-md-2 px-0">
                                <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                <label style="margin: 0px;" class="container col-md-12">Cheque entregado
                                    <input disabled type="checkbox" id="cheque_entregado" name="cheque_entregado" @if(!is_null($comprobante_egreso)) @if($comprobante_egreso->check==1) checked @endif @endif>
                                    <span class="checkmark"></span>
                                </label>
                                @if($comprobante_egreso->anulado_tipo != 1)
                                @if($empresa->id != "0992704152001")
                                <div style="text-align: center;">
                                    <label style="font-size:19px;" class="col-md-12">Rt. Asumidas
                                        <input onchange="validar_ch_asumidas()" @if(!is_null($comprobante_egreso)) @if($comprobante_egreso->rt_asumida_estado==1) checked @endif @endif class="spropety" type="checkbox" id="ch_rt_asumidas" name="ch_rt_asumidas">
                                    </label>
                                </div>
                                @endif
                                @endif
                            </div>
                            @php
                            $bancos = Sis_medico\Ct_Caja_Banco::where('id', $comprobante_egreso->id_caja_banco)->first();
                            @endphp
                            <div class=" col-md-4  px-0">
                                <label class="col-md-12 label_header" for="ruc">Banco:</label>
                                <select disabled class="form-control " name="banco" id="banco">
                                    <option value="0">@if(!is_null($bancos)) {{$bancos->nombre}} @endif</option>

                                </select>
                            </div>
                            <div class=" col-md-2  px-0">
                                <label class="col-md-12 label_header" for="secuencia">Divisas:</label>
                                <input type="text" class="form-control" value="DOLARES" readonly>
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="secuencia">Cambio:</label>
                                <input disabled class="form-control " type="text" name="secuencia" id="secuencia" value="1.00">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header control-label" for="numero_cheque">Cheque No:</label>
                                <input class="form-control " type="text" name="numero_cheque" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->no_cheque}} @endif" id="numero_cheque">
                            </div>
                            <div class=" col-md-2  px-1">
                                <label class="col-md-12 label_header" for="fecha_cheque">Fecha Cheque: </label>
                                <input disabled class="form-control " type="text" name="fecha_cheque" id="fecha_cheque" value="@if(!is_null($comprobante_egreso)){{$comprobante_egreso->fecha_cheque}}@endif">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label for="valor" class="label_header">Valor</label>
                                <input class="form-control " type="text" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($comprobante_egreso)) @if(($comprobante_egreso->valor_pago)>0) {{$comprobante_egreso->valor_pago}} @else @if(isset($comprobante_egreso->asiento_cabecera)) {{$comprobante_egreso->asiento_cabecera->valor}} @endif @endif @endif" readonly>

                            </div>
                            <div class=" col-md-3 px-0">
                                {{ csrf_field() }}
                                <input type="hidden" name="superavit" id="superavit" value="0">
                                <label class="col-md-12 label_header" for="acreedor">Acreedor: </label>
                                <input disabled type="text" id="nombre_proveedor" name="nombre_proveedor" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->beneficiario}} @endif" class="form-control form-control-sm nombre_proveedor " onchange="cambiar_nombre_proveedor()">
                            </div>

                            <div class=" col-md-4 px-1">
                                <label class="col-md-12 label_header" for="ruc">RUC:</label>
                                <input disabled class="form-control " type="text" name="id_proveedor" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->id_proveedor}} @endif" id="id_proveedor">
                            </div>
                            <div class=" col-md-4  px-1">
                                <label class="col-md-12 label_header" for="girado"> Girado a:</label>
                                <input disabled class="form-control " type="text" name="giradoa" id="giradoa" value="@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->beneficiario}} @endif">
                            </div>
                            <div class="col-md-4 px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.formasdepago')}}</label>
                                <select name="formas_pago" id="formas_pago" class="form-control" disabled>
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    <?php
                                    foreach ($formas_pago as $value) {
                                        $select = '';
                                        if ($value->id == $comprobante_egreso->id_pago)
                                            $select = 'selected';
                                        echo '<option value="' . $value->id . '" ' . $select . '>' . $value->nombre . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $sucursalAsiento = Contable::obtenerSucursalAsiento($comprobante_egreso->id_asiento_cabecera);
                            ?>
                            <div class="col-md-<?= $sucursalAsiento != '' ? '4' : '3'; ?> px-1">
                                <label for="" class="control-label label_header">{{trans('contableM.sucursal')}}</label>
                                <select name="sucursal" id="sucursal" class="form-control" <?= $sucursalAsiento != '' ? 'disabled' : ''; ?>>
                                    <option value="">{{trans('agenda.seleccione')}}..</option>
                                    <?php
                                    foreach ($sucursales as $sucursal) {
                                        $select = '';
                                        if ($sucursal->codigo_sucursal == $sucursalAsiento)
                                            $select = 'selected';
                                        echo '<option value="' . $sucursal->codigo_sucursal . '" ' . $select . '>' . $sucursal->codigo_sucursal . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            if ($sucursalAsiento == '') {
                                echo '<div class="col-md-1">
                                <label for="" class="control-label">&nbsp;</label>
                                <a href="javascript:;" class="btn btn-info" id="btnactualizarSucursalAsiento"><i class="fa fa-save"></i></a>
                                
                            </div>';
                            }
                            ?>
                            <div style="display:none;" id="contenedor_asumidas" class=" col-md-2 px-1">
                                <label for="valor" class="label_header">Retenciones Asumidas</label>
                                <input onchange="verificar_valor({{$comprobante_egreso->id}});" class="form-control " type="text" autocomplete="off" name="rt_asumidas" id="rt_asumidas" value="@if(!is_null($comprobante_egreso->rt_asumida_valor)) {{$comprobante_egreso->rt_asumida_valor}} @else 0.00 @endif" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                            </div>

                            <input type="hidden" name="total_suma" id="total_suma">
                        </div>
                    </div>
                    @if(count($detalle_egreso) > 0)

                    <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL PROVEEDOR</label>

                    <input type="hidden" name="id_compra" id="id_compra">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <div class="table-responsive col-md-12 px-1 " style="min-height: 250px; max-height: 250px; width: 100%;">
                        <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" style="width: 100%;" aria-describedby="example2_info">
                            <thead style="background-color: #9E9E9E; color: white;">
                                <tr style="position: relative;">
                                    <th style="width: 14.28%; text-align: center;">Vence</th>
                                    <th style="width: 14.28%; text-align: center;">Tipo</th>
                                    <th style="width: 14.28%; text-align: center;">Número</th>
                                    <th style="width: 14.28%; text-align: center;">Concepto</th>
                                    <th style="width: 14.28%; text-align: center;">Div</th>
                                    <th style="width: 14.28%; text-align: center;">Saldo</th>
                                    <th style="width: 14.28%; text-align: center;">Abono</th>
                                </tr>
                            </thead>
                            <tbody id="crear">
                                @php $cont=0; @endphp
                                @if($comprobante_egreso->tipo!=2)
                                @foreach($detalle_egreso as $value)
                                <tr>
                                    <td> @if(!is_null($comprobante_egreso->fecha_comprobante)) {{$comprobante_egreso->fecha_comprobante}} @endif </td>
                                    <td> COM-FA </td>
                                    <td> {{$value->id_secuencia}} </td>
                                    <td> {{$value->observacion}} </td>
                                    <td> $ </td>
                                    <td style="text-align: center;"> {{$value->saldo_base}} </td>
                                    <td style="text-align: center;"> {{$value->abono}} </td>
                                </tr>
                                @php $cont = $cont +1; @endphp
                                @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <label class="col-md-12 label_header" style="color: white;" for="nota">Nota: </label>
                        <textarea class="form-control" name="nota" cols="3" rows="3">@if(!is_null($comprobante_egreso)) {{$comprobante_egreso->comentarios}} @endif</textarea>
                    </div>
                    <!-- <div class="col-md-12" style="text-align: center; top: 5px;">
                                        <button  formaction="{{route('egresoacreedor_update',['id'=>$comprobante_egreso->id])}}" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> Actualizar </button>
                                </div> -->
                    <div class="col-md-12" style="text-align: center; top: 5px;">
                        <button formaction="{{route('egresoacreedor.update_comprobante_observacion',['id'=>$comprobante_egreso->id])}}" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> Actualizar </button>
                        @endif
                    </div>
                </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#cheque_entregado').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });
        retencion_asumidas_mostrar();
        $('#btnactualizarSucursalAsiento').click(function() {
            if ($('#sucursal').val() == '') {
                alerta("error", "Seleccione una sucursal");
            } else {
                $.ajax({
                    url: "{{route('actualizar_sucursal_asiento')}}",
                    type: 'post',
                    data: {
                        _token: $('input[name=_token]').val(),
                        id_asiento: '<?= $comprobante_egreso->id_asiento_cabecera; ?>',
                        sucursal: $('#sucursal').val()
                    },
                    dataType: 'json',
                    success: function(json) {
                        if (json.mns == 'ok')
                            location.reload();
                    }
                });
            }
        });
    });

    function validar_ch_asumidas() {
        let ch_rt_asumidas = document.getElementById("ch_rt_asumidas");
        console.log(ch_rt_asumidas);
        let banco = document.getElementById("banco").value;
        if (ch_rt_asumidas.checked) {
            Swal.fire({
                title: 'Información',
                text: "Esta seguro que desea realizar esta acción",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (banco == "" || banco == null) {
                        alerta("error", "Seleccione el banco");
                        ch_rt_asumidas.checked = false;
                    } else {
                        retencion_asumidas_mostrar();
                    }
                } else {
                    ch_rt_asumidas.checked = false;
                }
            })
        } else {
            retencion_asumidas_mostrar();
            let id_factura = document.getElementById("id_factura").value;
            retencion_asumidas(id_factura);
        }
    }

    function retencion_asumidas_mostrar() {
        let ch_rt_asumidas = document.getElementById("ch_rt_asumidas").checked;
        let contenedor_asumidas = document.getElementById("contenedor_asumidas");
        let rt_asumidas = document.getElementById("rt_asumidas");
        if (ch_rt_asumidas) {
            contenedor_asumidas.style.display = "block";
        } else {
            contenedor_asumidas.style.display = "none";
            //rt_asumidas.value= "0.00";
        }
    }

    function retencion_asumidas(id) {
        let id_comprobante = id;
        let ch_rt_asumidas = document.getElementById("ch_rt_asumidas").checked;
        let valor = document.getElementById("rt_asumidas").value;
        let banco = document.getElementById("banco").value;
        let estado = 0;
        if (ch_rt_asumidas) {
            estado = 1;
        } else {
            estado = 0;
        }
        $.ajax({
            type: 'get',
            url: "{{route('acreedores_cegreso.asumidas')}}",
            datatype: 'json',
            data: {
                'id': id,
                'estado': estado,
                'rt_asumidas': valor,
                'banco': banco,
                'id_proveedor': document.getElementById("id_proveedor").value,
                'fecha_hoy': document.getElementById("fecha_hoy").value,
                'total_favor': document.getElementById("valor_cheque").value,
            },
            success: function(data) {
                console.log(data);
                let rt_input = document.getElementById("ret_asumida");
                let mensaje = "";

                if (data.estado == 1) {
                    mensaje = "SI"
                } else {
                    mensaje = "NO"
                }
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Se Actualizo Correctamente',
                    showConfirmButton: false,
                    timer: 1500
                })
                setTimeout(function() {
                    location.reload(true);
                }, 1600);
                rt_input.value = mensaje;
                //swal('Actualizado');

            },
            error: function(data) {}
        });
    }


    function verificar_valor(id) {
        let rt_asumidas = document.getElementById("rt_asumidas");
        let valor_cheque = document.getElementById("valor_cheque");
        let banco = document.getElementById("banco").value;

        if (valor_cheque.value == "") {
            alerta("error", "Ingrese  el campo valor cheque");

            //rt_asumidas.value="0.00";
        } else if (parseFloat(rt_asumidas.value) > parseFloat(valor_cheque.value)) {
            alerta("error", "El valor de la retencion no debe ser mayor que el Valor del Cheque");

            rt_asumidas.value = "@if(!is_null($comprobante_egreso->rt_asumida_valor)){{$comprobante_egreso->rt_asumida_valor}}@else 0.00 @endif";
        } else {
            if (banco == "" || banco == null) {
                alerta("error", "Seleccione el banco");
                rt_asumidas.value = "@if(!is_null($comprobante_egreso->rt_asumida_valor)){{$comprobante_egreso->rt_asumida_valor}}@else 0.00 @endif";
            } else {
                retencion_asumidas(id);
            }

        }
    }

    function buscar_factura() {

    }


    function alerta(icon, msj) {
        Swal.fire({
            position: 'center',
            icon: `${icon}`,
            title: `${msj}`,
            showConfirmButton: false,
            timer: 2000
        })
    }
</script>

@endsection