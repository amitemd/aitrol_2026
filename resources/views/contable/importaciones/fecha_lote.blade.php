@extends('contable.importaciones.base')
@section('action-content')
<style>
    .container-element {
        box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -moz-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.35);
        padding: 18px 0px;
        border-radius: 7px;
        margin-bottom: 12px;

    }

    .boton {
        border-radius: 7px 7px 0px 0px !important;
        padding: 5px 20px !important;
        border: 2px solid rgba(0, 166, 90, 0.3) !important;
        background: transparent !important;
        color: black !important;
        font-weight: bold !important;
        transition: all 0.3s ease !important;
        width: 150px !important;
        text-align: center;
    }

    /* .boton:hover {
        background-color: #00a65a;
        color: white;
    } */

    /* .nav-item{
        color: #00a65a!important;
    } */

    .nav-item.active {
        border-radius: 7px 7px 0px 0px !important;
        background-color: rgba(0, 166, 90, 0.7) !important;
        color: white !important;
    }

    .nav-tabs {
        border-bottom: 0px solid transparent !important;
    }

    .border_panel {
        margin: 0;
        border: 1px solid rgba(0, 166, 90, 0.3);
        border-radius: 0px 7px 7px 7px
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title"><b>LOTE Y FECHA DE VENCIMIENTO DEL PEDIDO</b> </h3>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>Proveedor:</label>
                        @if(isset($cabecera->proveedor_da)){{$cabecera->proveedor_da->razonsocial}} @endif
                    </div>
                    <div class="col-md-5">
                        <label>Observación:</label>
                        {{$cabecera->observacion}}
                    </div>
                    <div class="col-md-3">
                        <label>Fecha:</label>
                        {{$cabecera->fecha}}
                    </div>
                </div>

            </div>

            <div>
                <!-- <button type="button" id="btn-editar" class="boton boton-activo">Editar</button>
                <button type="button" id="btn-ver" class="boton">Ver</button> -->

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @if($imp_cabecera->inventario == 0)
                    <li class="nav-item active">
                        <a class="boton nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                            <!-- <button type="button" id="btn-editar" class="boton boton-activo">Editar</button> -->
                            Editar
                        </a>
                    </li>
                    @endif
                    <li class="nav-item  @if($imp_cabecera->inventario == 1) active @endif">
                        <a class="boton nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">
                            <!-- <button type="button" id="btn-ver" class="boton">Ver</button> -->
                            Ver
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content border_panel" id="myTabContent">
                @if($imp_cabecera->inventario == 0)
                <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <form action="" id="form_imp" style="display: grid; place-items: center; padding-top: 20px;">
                        <input type="hidden" name="contador" id="contador" value="0">
                        <div class="" style="width: 97%;">
                            <table id="example" class="table" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th width="20%">Codigo</th>
                                        <th width="35%">Descripción del Producto</th>
                                        <th width="5%">Cantidad</th>
                                        <!-- <th width="10%">Saldo</th> -->
                                        <th width="10%">Cant. Fila</th>
                                        <th width="10%">Accion</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cabecera->detalles as $detalle)
                                    <tr>
                                        <td>
                                            {{$detalle->codigo}}
                                            <input type="hidden" id="codigo_{{$detalle->id}}" value="{{$detalle->codigo}}">
                                            <input type="hidden" id="id_{{$detalle->id}}" value="{{$detalle->id}}">
                                        </td>
                                        <td>
                                            {{$detalle->producto->nombre}}
                                            <input type="hidden" id="nombre_{{$detalle->id}}" value="{{$detalle->producto->nombre}}">
                                        </td>
                                        <td>
                                            {{$detalle->cantidad}}
                                            <input type="hidden" id="cantidad_{{$detalle->id}}" value="{{$detalle->cantidad}}">
                                        </td>
                                        {{--<td>
                                        <input type="hidden" id="value_saldo_{{$detalle->id}}" value="{{$detalle->cantidad}}">
                                        <p id="saldo_{{$detalle->id}}">{{$detalle->cantidad}}</p>
                                        </td>--}}
                                        <td>
                                            <input type="text" class="form-control" id="cant_fila_{{$detalle->id}}" name="cant_fila[]" value="1">
                                        </td>
                                        <td>
                                            <button class="btn btn-success" type="button" onclick="calcular_saldo(`{{$detalle->id}}`)">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- FORM -->
                        <div style=" width: 93%">
                            <div class="container-element">
                                <table id="example" class="table" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th width="20%">Codigo</th>
                                            <th width="35%">Descripción del Producto</th>
                                            <th width="5%">Cantidad</th>
                                            <th width="15%">Lote</th>
                                            <th width="15%">Fecha Vencimiento</th>
                                            <th width="10%">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="details">

                                    </tbody>
                                </table>
                            </div>
                            @if($imp_cabecera->inventario == 0)
                            <div style="text-align: center;">
                                <a style="font-weight: 600;" class="btn btn-success" type="button" name="btn_guardar" id="btn_guardar" onclick="guardar();"><i class="fa fa-save"></i> &nbsp;Guardar</a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                @endif
                <div class="tab-pane fade @if($imp_cabecera->inventario == 1) active in @endif" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="col-md-12 border_panel">
                        <table id="example" class="table" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th width="20%">Codigo</th>
                                    <th width="35%">Descripción del Producto</th>
                                    <th width="5%">Cantidad</th>
                                    <th width="20%">Lote</th>
                                    <th width="20%">Fecha Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabecera->detalles as $detalle)
                                <tr>
                                    <td>{{$detalle->codigo}}</td>
                                    <td>{{$detalle->producto->nombre}} </td>
                                    <td>{{$detalle->cantidad}}</td>
                                    <td>
                                        {{$detalle->lote}}
                                    </td>
                                    <td>
                                        {{$detalle->fecha_exp}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>
</section>
@if($imp_cabecera->inventario == 0)
<script>
    function guardar(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('importaciones.guardar_fecha_lote')}}",
            data: $("#form_imp").serialize(),
            datatype: 'json',
            success: function(data) {
                // setTimeout(() => {
                    alertas(data.respuesta, data.respuesta, data.msj)
                    
                    // location.reload();                    
                // }, 1500);

            },
            error: function(data) {
                alertas('error', 'Error', 'Error al guardar los datos')
                //alert(data)
            }
        });
    }
</script>

<script>
    const calcular_saldo = id => {
        var cantidad_producto = $("#cantidad_" + id).val();
        let cant_fila = $("#cant_fila_" + id).val();
        if (parseInt(cant_fila) > parseInt(cantidad_producto)) {
            alertas('error', 'Error', 'La cantidad no puede ser mayor a la cantidad del producto')
            return;
        }
        let value_codigo = document.getElementById("codigo_" + id).value;
        let codigo_producto = document.querySelectorAll(".codigo_producto");

        let validate_codigo = 0;

        if (codigo_producto.length > 0) {
            codigo_producto.forEach(element => {
                if (value_codigo == element.value) {
                    validate_codigo++;
                }
            })
        }
        // console.log(validate_codigo, cant_fila);
        // if ((parseInt(validate_codigo) + parseInt(cant_fila)) > parseInt(cantidad_producto)) {
        //     alertas('error', 'Error', 'La cantidad no puede ser mayor a la cantidad del producto')
        //     return;
        // }

        let data = {
            codigo: $("#codigo_" + id).val(),
            nombre_producto: $("#nombre_" + id).val(),
            id: document.getElementById("contador").value,
            id_detalle: document.getElementById("id_" + id).value
        }

        for (let i = 0; i < parseInt(cant_fila); i++) {
            $("#details").append(crearFilas(data));
            data.id++;
        }

        document.getElementById("contador").value = data.id;

    }


    const crearFilas = data =>
        `<tr id="fila_${data.id}">
                <td>                                            
                    <input name="codigo[]" value="${data.codigo}" class="codigo_producto" type="hidden">
                    ${data.codigo}
                    <input type="hidden" name="id[]" value="${data.id_detalle}">
                </td>
                <td>
                    ${data.nombre_producto}
                </td>
                <td>
                    <input min="0" class="form-control" name="cantidad[]" value="" type="number">
                </td>
                <td>
                    <input type="text" class="form-control" name="lote[]" value="">
                </td>
                <td>
                    <input type="date" class="form-control" name="fecha_venc[]" value="">
                </td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="eliminar_fila(${data.id})">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>`
    const eliminar_fila = id => document.getElementById("fila_" + id).remove();
</script>

@endif


@endsection