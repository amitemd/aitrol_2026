@extends('guia.base')
@section('action-content')

<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 20px;
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
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
    }
</style>
<div class="modal fade" id="crear_transportista" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">Guía de Remisión</a></li>
            <li class="breadcrumb-item active" aria-current="page">Actualizar Guía de Remisión</li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_guia_remision" role="form" method="post">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="{{$id->id}}">
        <input type="hidden" id="id_ci_ruc_tr" name="id_ci_ruc_tr" value="{{$id->ci_ruc_trasnportista}}">
        <input type="hidden" id="num_autorizacion_sustento" name="num_autorizacion_sustento">
        <div class="box box-solid">
            <div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b> Guía de Remisión</b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="{{route('guia_remision_index')}}">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
                <div class="header row">
                    <div class="col-md-12">
                        <div class="form-group col-sm-12  col-md-12 px-1 text-center">
                            <label for="generales" style="font-size: 20px;">DATOS GENERALES</label>
                        </div>
                        <div class="form-group  col-sm-12  col-md-4 px-1">
                            <div class="col-md-12 px-0">
                                <label for="sucursal" class="label_header">{{trans('contableM.sucursal')}} ({{trans('contableM.empresa')}})</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select class="form-control validar" name="sucursal" id="sucursal" onchange="obtener_punto_emision();" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                    <option value="">Seleccione</option>
                                    <option selected value="{{$id->establecimiento}}">{{$id->establecimiento}}
                                    <option>
                                        @foreach ($sucursales as $value)
                                    <option value="{{ $value->id }}">{{ $value->codigo_sucursal }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-4  px-1">
                            <div class="col-md-12 px-0">
                                <label for="punto_emision" class="label_header">{{trans('contableM.pemision')}} ({{trans('contableM.empresa')}})</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select readonly class="form-control validar" name="punto_emision" id="punto_emision" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                    <option value="">Seleccione...</option>
                                    @if($id->punto_emision != '' or $id->punto_emision != null)
                                    <option selected value="{{$id->punto_emision}}">{{$id->punto_emision}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-4  px-1">
                            <div class="col-md-12 px-0">
                                <label for="datos_adicionales" class="label_header">Datos Generales</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <select class="form-control select2_demo_2" id="datos_adicionales" name="datos_adicionales" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                    <option value="">----------</option>
                                    <option value="Movilización de insumos médicos" <?php if ($id->datos_adicionales == 'Movilización de insumos médicos') echo 'selected'; ?>>Movilización de insumos médicos</option>
                                    <option value="Movilización de equipos médicos" <?php if ($id->datos_adicionales == 'Movilización de equipos médicos') echo 'selected'; ?>>Movilización de equipos médicos</option>
                                    <option value="Varios otros" <?php if ($id->datos_adicionales == 'Varios otros') echo 'selected'; ?>>Varios otros</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-12 px-1 text-center">
                            <label for="trasportista_traslado" id="transp" style="font-size: 20px;">
                                TRANSPORTISTA Y TRASLADO
                            </label>
                            <a href="{{route('crear_transportista_datos_guia')}}" id="campoHidden" style="display: none;width: 120px;" id="seeI" class="btn btn-info btn-xs" data-toggle="modal" data-target="#crear_transportista"> <i class="fa fa-edit" aria-hidden="true"></i> Crear Transportista</a>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <div class="px-0">
                                <label class="label_header">
                                    Ci/Ruc
                                </label>
                            </div>
                            <div class="px-0">
                                <select id="js-data-nombre-ruc" name="nombre_ruc" class="js-data-nombre-ruc form-control" style="width: 100%;" autocomplete="off"></select>
                            </div>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <div class="px-0">
                                <label class=" label_header">Email(s)</label>
                            </div>
                            <div class="px-0">
                                <div class="input-group">
                                    <input id="email_transportista" type="email" class="form-control validar" name="email_transportista" value="{{$id->email_transportista}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                    <div class="input-group-addon ">
                                        <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('email_transportista').value = '';"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class=" label_header">Dirección de Partida</label>
                            <div class="input-group">
                                <input id="direccion_partida" type="text" class="form-control" name="direccion_partida" value="{{$id->direccion_partida}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_partida').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">Placa</label>
                            <input type="text" class="form-control col-md-12 validar" name="placa" id="placa" value="{{$id->placa}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">Fecha Inicio</label>
                            <input type="date" class="form-control col-md-12" value="<?= isset($id->id) ? date('Y-m-d') : $id->fecha_ini; ?>" name="f_inicio" id="f_inicio" onchange="validacioneFecha()" readonly>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">Fecha Fin</label>
                            <input type="date" class="form-control col-md-12" value="{{$id->fecha_fin}}" name="f_fin" id="f_fin" onchange="validacioneFecha()" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                        </div>
                        <div class="col-md-12 text-center">
                            <label for="generales" style="font-size: 20px;">DATOS DEL DESTINATARIO</label>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">
                                Ci/Ruc
                            </label>
                            <div>
                                <input value="{{$id->ci_destinatario}}" type="text" name="cedula_destinatario" id="cedula_destinatario" onchange="funtionCall(this)" onblur="validarCedulaOri(this)" maxlength="13" class="form-control" oninput="this.value = this.value.replace(/\D+/g, '')" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                            </div>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">Dirección de Destino</label>
                            <div class="input-group">
                                <input id="direccion_destino" value="{{$id->direccion_destinatario}}" type="text" class="form-control validar" name="direccion_destino" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_destino').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-4  col-md-4  px-1">
                            <label class="label_header">Tipo documento</label>
                            <div class="input-group">
                                <select name="tipo_doc" id="tipo_doc" class="form-control" onchange="validateDoc(this)" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                    <option value="">Seleccione</option>
                                    <option {{$id->tipo_documento_destinatario == 1 ? 'selected' : ''}} value="1">Factura</option>
                                </select>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('tipo_doc').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-3  col-md-4  px-1">
                            <label class="label_header">Fecha Autorización</label>
                            <div class="input-group">
                                <input id="fecha_autoriza" type="date" value="{{$id->fecha_autorizacion_destinatario}}" class="form-control validar" name="fecha_autoriza" onchange="fecha_aut(this)" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('fecha_autoriza').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-3  col-md-4  px-1">
                            <label class="label_header">Email(s)</label>
                            <div class="input-group">
                                <input id="email_destina" type="email" class="form-control validar" name="email_destina" value="{{$id->email_traslado_destinatario}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('email_destina').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-3  col-md-4  px-1">
                            <label class="label_header">Código Establecimiento Destino</label>
                            <div class="input-group">
                                <input id="codigo_esta_destino" type="text" class="form-control validar" name="codigo_esta_destino" value="001" value="{{$id->codigo_est_destino}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('codigo_esta_destino').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-3  col-md-4  px-1">
                            <label class="label_header">No. Documento</label>
                            <div class="input-group">
                                <input id="num_documento" type="text" class="form-control validar" disabled name="num_documento" value="{{$id->num_doc_destino}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('num_documento').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">Ruta</label>
                            <div class="input-group">
                                <input id="ruta" type="text" class="form-control validar" name="ruta" value="{{$id->ruta}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('ruta').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">Razon Social</label>
                            <div class="input-group">
                                <input id="razon_social" type="text" class="form-control validar" name="razon_social" value="{{$id->razon_social_destinatario}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('razon_social').value = '';"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-3 px-1">
                            <label class="label_header">Motivo del traslado</label>
                            <div class="input-group">
                                <input id="motivo_trasla" type="text" class="form-control validar" name="motivo_trasla" value="{{$id->motivo_traslado_destinatario}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('motivo_trasla').value = '';"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 table-responsive" style="width: 100%;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <!--<th width="10%" class="" tabindex="0">{{trans('contableM.codigo')}}</th>-->
                                <th tabindex="0">Producto</th>
                                <th tabindex="0">Detalle1</th>
                                <th tabindex="0">Detalle2</th>
                                <th tabindex="0">Detalle3</th>
                                <th tabindex="0">{{trans('contableM.cantidad')}}</th>
                                <th tabindex="0">COD PRINCIPAL.</th>
                                <th tabindex="0">COD. ADICIONAL</th>
                                <th tabindex="0">
                                    <button onclick="crearFila()" type="button" class="btn btn-success btn-gray" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                            @php $contador = 0; @endphp
                            @foreach($detalle as $value)
                            <tr id="fila{{$contador}}">
                                <td style="max-width:100px;">
                                    <select id="producto{{$contador}}" name="producto[]" class="form-control select2_editar product" style="width: 110px" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                        @foreach($productos as $p)
                                        @php $selected =""; @endphp
                                        @php if($value->cod_principal == $p->codigo){
                                        $selected = "selected";
                                        }
                                        @endphp
                                        <option {{$selected}} value="{{$p->id}}">{{$p->nombre}} | {{$p->codigo}}</option>
                                        @endforeach

                                    </select>
                                </td>
                                <td>
                                    <input id="descripcion{{$contador}}" value="{{$value->descripcion}}" class="form-control text-right " type="text" style="width: 80%;height:20px;" name="descripcion[]" onchange="validateZ(this)" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <input id="observacion{{$contador}}" value="{{$value->observacion}}" class="form-control text-right " type="text" style="width: 80%;height:20px;" name="observacion[]" onchange="validateZ(this)" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <input id="detalle3${id}" class="form-control text-right " type="text" style="width: 80%;height:20px;" name="detalle3[]" value="{{ $value->detalle3 }}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <input id="cantidad{{$contador}}" value="{{$value->cantidad}}" class="form-control text-right " type="number" style="width: 80%;height:20px;" name="cantidad[]" onchange="validateZ(this)" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <input id="cod_principal${{$contador}}" value="{{$value->cod_principal}}" type="text" class="form-control validar" name="cod_principal[]" style="width: 80%;height:20px;" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <input id="cod_adicional${{$contador}}" value="{{$value->cod_adicional}}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" name="cod_adicional[]" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarTd('{{$contador}}')" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            @php
                            $contador ++;
                            @endphp
                            @endforeach
                            <input type="hidden" id="contador_items" name="contador_items" value="{{$contador}}" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardar(event)" id="boton_guardar" <?= $id->estado == 0 ? 'disabled' : ($id->estado == 5 ? 'disabled' : ''); ?>><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    const limpiar = () => {

        let contador = document.getElementById("contador_items");

        if (parseInt(contador.value) > 0) {
            contador = 0;
            $('#agregar_cuentas').empty();
        }
    }

    const crearFilaForEach = (data) => {

        let id = document.getElementById('contador_items').value;
        let t;
        console.log(data);
        for (t = 0; t < data.length; t++) {
            let fila = `
                <tr id="fila${t}">
                    <td >
                        <select id="producto${t}" name="producto[]" class="form-control select2_cuentas validaciones" style="width:100%;height:20px" required >
                            <option></option>
                            @foreach($productos as $value)
                            <option value="{{$value->codigo}}" >{{$value->nombre}}</option>
                            @endforeach

                        </select>
                    </td>
                    <td>
                        <input  id="descripcion${t}"  class="form-control text-right " type="text" style="width: 80%;height:20px;" name="descripcion[]" required >
                    </td>
                    <td>
                        <input  id="observacion${t}"  class="form-control text-right " type="text" style="width: 80%;height:20px;" name="observacion[]" >
                    </td>
                    <td>
                        <input  id="detalle3${t}"  class="form-control text-right " type="text" style="width: 80%;height:20px;"  name="detalle3[]" onchange="validateZ(this);">
                    </td>
                    <td>
                        <input  id="cantidad${t}"  class="form-control text-right " type="number" style="width: 80%;height:20px;" value="1" name="cantidad[]" onchange="validateZ(this);" onBlur="cantidadVathis(this)" required >
                    </td>
                    <td>
                        <input readonly id="cod_principal${t}" type="text" class="form-control validar" name="cod_principal[]" style="width: 80%;height:20px;" required >
                    </td>
                    <td>
                        <input readonly id="cod_adicional${t}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" name="cod_adicional[]" required >
                    </td>
                   
                    <td>
                        <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarTd(${t})">
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `
            $('#agregar_cuentas').append(fila);
            $(`#cod_principal${t}`).val(data[t]['id_ct_productos']);
            $(`#cod_adicional${t}`).val(data[t]['id_ct_productos']);
            $(`#producto${t}`).select2().val(data[t]['id_ct_productos']).trigger('change');
            $('.select2_cuentas').select2({
                tags: false
            });

        }
        document.getElementById('contador_items').value = t;
    }





    $('.select2').select2({
        tags: false
    });

    $(".select_2_nombres").select2({
        tags: false
    })

    const validateDoc = (value) => {

        let num = document.getElementById("tipo_doc").value;
        if (num == 1) {
            document.getElementById("num_documento").disabled = false;
        } else if (num == 2) {
            document.getElementById("num_documento").disabled = true;
        }


    }

    const obtener_punto_emision = () => {
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





    $("input[name=dato_traspo]").change((value) => {

        const datoTrasportista = $('input[name="dato_traspo"]:checked').val();

        $.ajax({
            type: 'post',
            url: "{{ route('ci_nombres_function') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'name_ced': datoTrasportista
            },
            success: function(data) {
                $("#nombre_ruc").empty();
                $.each(data['data'], function(key, registro) {
                    let nombre = '';
                    if (registro.full_name != undefined) {
                        nombre = registro.full_name;
                    } else {
                        nombre = registro.cedula;
                    }
                    $("#nombre_ruc").append('<option >' +
                        "Seleccione" +
                        '</option>', '<option value=' + registro.id + '>' +
                        nombre +
                        '</option>');
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    const handleChange = (e) => {

        $.ajax({
            type: 'post',
            url: "{{ route('llenar_productos_guia_remision') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto': e
            },
            success: function(data) {

                if (data['query'].length > 0) {
                    let fecha = data['query'][0]['created_at'];
                    let fechaNew = fecha.split(' ');
                    document.getElementById("fecha_autoriza").value = fechaNew[0];
                }

                crearFilaForEach(data['query']);
            },
            error: function(data) {

            }
        })

    }


    $("#num_documento").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('ci_nombres_destinatario')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term,
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    if (data.id == '') {
                        alertas("error", "La factura de venta no se encuentra", ["Error"]);
                        $("#num_documento").val('');
                    }

                    console.log(data);
                    response(data);
                }
            })
        },
        minLength: 3,
        select: function(data, ui) {
            $("#codigo_esta_destino").val("001");
            let arr = ui.item.label.split('-').map((val, i) => {
                if (i == 0) {
                    if (val.length != 3) {
                        alertas('error', 'Error', ['Erron en formato 000-000-000000000']);
                    }
                } else if (i == 1) {

                    if (val.length != 3) {
                        alertas('error', 'Error', ['Erron en formato 000-000-000000000']);
                    }
                } else if (i == 2) {
                    if (val.length != 9) {
                        let vao = PadLeft(parseInt(val), 9);
                        console.log(vao.length);
                        return vao.length == 9 ? PadLeft(parseInt(val), 9) : alertas('error', 'Error', ['Erron en formato 000-000-000000000']);

                    }
                }
                return val;
            }).join("-");

            let sad = ui.item.autorizacion;
            $("#num_autorizacion_sustento").val(sad);
            limpiar();
            handleChange(arr);
            ui.item.label = arr;
            ui.item.value = arr;
        }
    });


    function PadLeft(value, length) {
        try {
            return (value.toString().length < length) ? PadLeft("0" + value, length) : value;
        } catch (error) {
            mensaje(error, 'error', '', '');
        }
    }



    const validacioneFecha = () => {
        let fechaIni = new Date($("#f_inicio").val());
        let fechaFin = new Date($("#f_fin").val());
        fechaIni.setDate(fechaIni.getDate() + 1);
        fechaFin.setDate(fechaFin.getDate() + 1);
        let fechaHoy = new Date();
        if (fechaIni < fechaHoy) {
            alert("La fecha inicio no puede ser menor que el dia de hoy");
            $("#f_inicio").val('');
        }

        if (fechaFin < fechaIni) {
            alert("La fecha fin no pueser ser menor");
            $("#f_fin").val('');
        }
    };

    const cargar_codigos = (value, id) => {
        $.ajax({
            type: 'post',
            url: "{{ route('guia_remision_codigo') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_producto': value.value
            },
            success: function(datos) {
                /*let arr = $("#agregar_cuentas").find('td').length;
                $("#agregar_cuentas").find('td').each(function(index, data) {
                    let codigo = $('#cod_principal' + index).val();
                    if (datos.producto.codigo == codigo) {

                        let num = Number($("#cantidad" + index).val()) + 1;
                        $("#cantidad" + index).val(num);
                        alertas('info', 'Correcto', ['El producto que esta seleccionado ya se encuentra en la lista , el item se sumara']);
                        eliminarTd(id);
                    }
                    let aux = Number(index + 1);
                    if (datos.producto.codigo != codigo) {
                        if (arr == aux) {
                            $("#cod_principal" + id).val(datos.producto.codigo);
                            $("#cod_adicional" + id).val(datos.producto.codigo);
                            return false;
                        }

                    }
                });*/
                $("#cod_principal" + id).val(datos.producto.codigo);
                $("#cod_adicional" + id).val(datos.producto.codigo);
            },
            error: function(data) {
                console.log(data);
            }
        });


    };

    $(document).ready(function() {
        $('.select2_editar').select2({
            tags: false
        });
        $(".product").select2().trigger("change");
        let id = document.getElementById("id_ci_ruc_tr").value;
        $.ajax({
            type: 'post',
            url: "{{ route('agregar_opcion_cedula_datos_guia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                console.log(data);
                let option = new Option(data.razon_social, data.id, true, true);
                $("#js-data-nombre-ruc").append(option).trigger('change');
            },
            error: function(data) {
                console.log(data);
            }
        });

    });


    const crearFila = () => {

        var id = document.getElementById('contador_items').value;
        let fila = `
                <tr id="fila${id}">
                                <td style="max-width:200px;">
                                    <select id="producto${id}" name="producto[]" class="form-control select2_cuentas validaciones" style="width:100%" onchange="cargar_codigos(this,${id})" required >
                                        <option> </option>
                                        @foreach($productos as $value)
                                        <option value="{{$value->id}}" >{{$value->nombre}}</option>
                                        @endforeach

                                    </select>
                                </td>
                                <td>
                                    <input  id="descripcion${id}"  class="form-control text-right " type="text" style="width: 80%;height:20px;" name="descripcion[]" required >
                                </td>
                                <td>
                                    <input  id="observacion${id}"  class="form-control text-right " type="text" style="width: 80%;height:20px;" name="observacion[]"  required >
                                </td>
                                <td>
                                    <input  id="detalle3${id}"  class="form-control text-right " type="text" style="width: 80%;height:20px;" name="detalle3[]">
                                </td>
                                <td>
                                    <input  id="cantidad${id}"  class="form-control text-right " type="number" style="width: 80%;height:20px;" name="cantidad[]" onchange="validateZ(this);" onBlur="cantidadVathis(this)" required >
                                </td>
                                <td>
                                    <input readonly id="cod_principal${id}" type="text" class="form-control validar" name="cod_principal[]" style="width: 80%;height:20px;" required >
                                </td>
                                <td>
                                    <input readonly id="cod_adicional${id}" class="form-control px-1 text-right precioneto" type="text" style="height:20px;" name="cod_adicional[]" required >
                                </td>
                               
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarTd(${id})">
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
    const eliminarTd = (data) => {
        $("#fila" + data).remove();
    }
    const validateZ = (val) => {
        console.log(val.id);
        if (val.value < 0) {
            $("#" + val.id).val(0);
        }
    }
    const cantidadVathis = (e) => {
        if (e.value == '' || e.value == 0) {
            alertas('error', 'Error', ['La cantidad no puede ser vacia']);
            e.value = 1;
        }

    }

    const validarForm = () => {

        let formulario = document.forms["crear_guia_remision"];
        let array = [];
        if (formulario.sucursal.value == '') {
            array.push('sucursal');
        }
        if (formulario.datos_adicionales.value == '') {
            array.push('datos adicionales');
        }
        if (formulario.nombre_ruc.value == '') {
            array.push('nombre o ci del trasportista');
        }
        if (formulario.email_transportista.value == '') {
            array.push('email del trasportista');
        }
        if (formulario.direccion_partida.value == '') {
            array.push('direccion de partida');
        }
        if (formulario.placa.value == '') {
            array.push('placa');
        }
        if (formulario.f_inicio.value == '') {
            array.push('fecha Inicio');
        }
        if (formulario.f_fin.value == '') {
            array.push('fecha Fin');
        }
        if (formulario.ruta.value == '') {
            array.push('Ruta');
        }
        if (formulario.cedula_destinatario.value == '') {
            array.push('nombre ci del destinatario');
        }
        if (formulario.direccion_destino.value == '') {
            array.push('dirección destino');
        }
        if (formulario.email_destina.value == '') {
            array.push('email destinatario');
        }
        if (formulario.codigo_esta_destino.value == '') {
            array.push('codigo destinatario');
        }
        if (document.getElementById("tipo_doc").value != '') {
            if (formulario.fecha_autoriza.value == '') {
                array.push('fecha autorización');
            }
            if (formulario.num_documento.value == '') {
                array.push('fecha numero documento');
            }
        }
        if (formulario.motivo_trasla.value == '') {
            array.push('motivo traslado');
        }
        if ($("#agregar_cuentas").children().length == 0) {
            array.push('Agregue un producto');
        } else {
            let contador = document.getElementById("contador_items").value;
            for (let index = 0; index < contador; index++) {
                if (document.getElementById("descripcion" + index).value == '') {
                    //array.push(`Agregue la descripción en la fila ${index}`);
                    array.push(`Debe agregar al menos un detalle en la fila ${index}`);
                }
                if (document.getElementById("producto" + index).value == '') {
                    array.push(`Agregue el producto en la fila ${index}`);
                }

            }
        }

        return array;
    }

    function guardar(e) {
        e.preventDefault();
        document.getElementById('boton_guardar').disabled = true;
        let valForm = validarForm();
        if (valForm.length == 0) {
            $.ajax({
                type: 'post',
                url: "{{route('guia_remision_save_update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $('#crear_guia_remision').serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, [data.msj]);
                    if (data.respuesta == "success") {
                        setTimeout(function() {
                            window.location.href = "{{ route('guia_remision_index')}}";
                        }, 1500)
                    } else {
                        document.getElementById('boton_guardar').disabled = false;
                    }
                },
                error: function(data) {
                    document.getElementById('boton_guardar').disabled = false;
                }
            });
        } else {
            alertas("error", "Porfavor llenes los siguientes campos", [valForm]);
            document.getElementById('boton_guardar').disabled = false;
        }
    }
    const alertas = (icon, title, msj) => {

        if (msj.length == 0) {
            Swal.fire({
                icon: icon,
                title: title,
                html: msj
            })
        } else {


            Swal.fire({
                icon: icon,
                title: title,
                html: msj.join()
            })

        }

    }

    $(document).ready(function() {
        $('.js-data-nombre-ruc').select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "{{route('transportista_datos_guia')}}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data) {
                    if (data.length == 0) {
                        var coordenadas = $("#transp").position();
                        $('#campoHidden').css({
                            position: 'absolute',
                            top: '6px',
                            left: parseFloat(coordenadas.left) + 220 + 'px',
                        });
                        document.getElementById("campoHidden").style.display = "block";
                    }
                    return {
                        results: $.map(data, function(item) {

                            return {
                                text: item.nombreappe,
                                id: item.id
                            }
                            var option = new Option(item.nombreappe, item.id);
                            studentSelect.append(option).trigger('change');
                        })
                    };
                },
            },
            language: {
                inputTooShort: function() {
                    return 'Por favor ingrese 3 caracteres....';
                },
            },

        });
    });

    var studentSelect = $('.js-data-nombre-ruc').on("change", function() {
        $.ajax({
            type: 'post',
            url: "{{route('llenar_campos_transportista_datos_guia')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                id: this.value
            },
            success: function(data) {
                document.getElementById("campoHidden").style.display = "none";
                $("#email_transportista").val(data.data.email);
                $("#placa").val(data.data.placa);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    const funtionCall = (e) => {
        console.log(e.value.length);
        if (e.value.length != 10 && e.value.length != 13) {
            e.value = '';
            alertas("error", "Cédula tiene que tener 10 caracteres Pasaporte 13 caracteres", ['Corregir la cédula']);
        }
    }
</script>


@endsection