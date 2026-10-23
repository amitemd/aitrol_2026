<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.titulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{trans('v3/new_varios.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.titulo'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body hero-factura">
                        <form id="frm_guia_remision">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.guiaExiste')->id)? config('data.guiaExiste')->id : '' }}">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarGuiaRemision" />
                            <input type="hidden" id="d" name="d" />
                            <div class="row hero-datosfactura">
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.sucursalEmpresa')}}
                                    </label>
                                    <select class="form-control select2" style="width: 100%;" id="sucursal_empresa"
                                        name="sucursal_empresa">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                            foreach (config('data.sucursales') as $value) {
                                                echo '<option ' . (isset(config('data.guiaExiste')->establecimiento) ? config('data.guiaExiste')->establecimiento == $value->codigo_sucursal ? 'selected' : '' : '') . ' value="' . $value->id . '">' . $value->codigo_sucursal . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.tipoDeGuia')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="tipo_guia" name="tipo_guia">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        if (config('data.tipoGuia') != '' && count(config('data.tipoGuia')) > 0) {
                                            foreach (config('data.tipoGuia') as $value) {
                                                echo '<option ' . (isset(config('data.guiaExiste')->tipo_guia) ? config('data.guiaExiste')->tipo_guia == $value->id_catalogo ? 'selected' : '' : '') . ' value="' . $value->id_catalogo . '">' . $value->catalogo . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row hero-cliente">
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_varios.ciruc')}}
                                    </label>
                                    <select id="js-data-nombre-ruc" name="nombre_ruc" class="js-data-nombre-ruc form-control" style="width: 100%;" autocomplete="off">
                                        @if(isset(config('data.guiaExiste')->ci_ruc_trasnportista))
                                        <option value="{{ isset(config('data.guiaExiste')->id_transportista) ? config('data.guiaExiste')->id_transportista : '' }}" selected>
                                            {{ config('data.guiaExiste')->ci_ruc_trasnportista }}
                                        </option>
                                        @endif
                                    </select>

                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.email')}}</label>
                                    <input id="email_transportista" type="email" class="form-control validar" value="{{ isset(config('data.guiaExiste')->email_transportista) ? config('data.guiaExiste')->email_transportista : '' }}" name="email_transportista">
                                </div>


                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.direcciondepartida')}}</label>
                                    <input id="direccion_partida" type="text" class="form-control" value="{{ isset(config('data.guiaExiste')->direccion_partida)? config('data.guiaExiste')->direccion_partida : '' }}" name="direccion_partida">
                                </div>
                            </div>

                            <div class="row hero-paciente">
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.placatransportista')}}</label>
                                    <input type="text" class="form-control col-md-12 validar" name="placa" value="{{ isset(config('data.guiaExiste')->placa) ? config('data.guiaExiste')->placa : '' }}" id="placa">
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.fechaInicio')}}</label>
                                    <input type="date" class="form-control col-md-12" value="{{ isset(config('data.guiaExiste')->fecha_ini)? config('data.guiaExiste')->fecha_ini : date('Y-m-d') }}" name="fecha_ini" id="fecha_ini" onchange="validacioneFecha()" readonly>

                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.fechaFin')}}</label>
                                    <input type="date" class="form-control col-md-12" name="fecha_fin" id="fecha_fin" onchange="validacioneFecha()" value="{{ isset(config('data.guiaExiste')->fecha_fin) ? config('data.guiaExiste')->fecha_fin : date('Y-m-d') }}">

                                </div>
                            </div>

                            <div class="row hero-seguro">
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.ci_Ruc')}}</label>
                                    <input type="text" class="form-control cedula_destinatario" name="ci_destinatario" id="ci_destinatario" maxlength="13" minlength="10" autocomplete="off" oninput="this.value = this.value.replace(/\D+/g, '')" value="{{ isset(config('data.guiaExiste')->ci_destinatario) ? config('data.guiaExiste')->ci_destinatario : '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.direccionDestino')}}</label>
                                    <input id="direccion_destinatario" type="text" class="form-control" value="{{ isset(config('data.guiaExiste')->direccion_destinatario)? config('data.guiaExiste')->direccion_destinatario : '' }}" name="direccion_destinatario">
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.tipodocumento')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="tipo" name="tipo">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        if (config('data.tipoDoc') != '' && count(config('data.tipoDoc')) > 0) {
                                            foreach (config('data.tipoDoc') as $value) {
                                                echo '<option ' . (isset(config('data.guiaExiste')->catalogo) ? config('data.guiaExiste')->catalogo == $value->catalogo ? 'selected' : '' : '') . ' value="' . $value->id_catalogo . '">' . $value->catalogo . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.fechaAutorizacion')}}</label>
                                    <input id="fecha_autorizacion_destinatario" type="date" class="form-control validar" name="fecha_autorizacion_destinatario" value="{{ isset(config('data.guiaExiste')->fecha_autorizacion_destinatario) ? config('data.guiaExiste')->fecha_autorizacion_destinatario : '' }}" onchange="fecha_aut(this)">
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.email')}}</label>
                                    <input id="email_traslado_destinatario" type="email" class="form-control validar" name="email_traslado_destinatario" value="{{ isset(config('data.guiaExiste')->email_traslado_destinatario) ? config('data.guiaExiste')->email_traslado_destinatario : '' }}" value="">
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-normal">{{trans('v3/new_tabla.codigoEstable')}}</label>
                                    <input id="codigo_est_destino" type="text" class="form-control validar" name="codigo_est_destino" value="001">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.nodoc')}}</label>
                                    <select id="num_doc_destino" name="num_doc_destino" style="width:100%" class="form-control validar validaciones select2_no_docs" {{ isset(config('data.guiaExiste')->numero_concat)? 'disabled' : '' }}>
                                        @if (isset(config('data.guiaExiste')->numero_concat))
                                        <?php
                                        $numeroConcatenado = config('data.guiaExiste')->numero_concat;
                                        $parte1 = substr($numeroConcatenado, 0, 3);
                                        $parte2 = substr($numeroConcatenado, 3, 3);
                                        $parte3 = substr($numeroConcatenado, 6);
                                        $numeroFormateado = "$parte1-$parte2-$parte3";
                                        ?>
                                        <option value="{{ config('data.guiaExiste')->id_venta }}" selected>
                                            {{ $numeroFormateado }}
                                        </option>
                                        @endif
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.ruta')}}</label>
                                    <input id="ruta" type="text" class="form-control validar" value="{{ isset(config('data.guiaExiste')->ruta)? config('data.guiaExiste')->ruta : '' }}" name="ruta" />
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.razonSocial')}}</label>
                                    <input id="razon_social_destinatario" type="text" class="form-control validar" value="{{ isset(config('data.guiaExiste')->razon_social_destinatario)? config('data.guiaExiste')->razon_social_destinatario : '' }}" name="razon_social_destinatario">
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.motivoDeTraslado')}}</label>
                                    <input id="motivo_traslado_destinatario" type="text" class="form-control validar" name="motivo_traslado_destinatario" value="{{ isset(config('data.guiaExiste')->motivo_traslado_destinatario) ? config('data.guiaExiste')->motivo_traslado_destinatario : '' }}">
                                </div>
                            </div>

                            <div style="margin-top: 2%">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 150px;">{{trans('v3/new_tabla.producto')}}</th>
                                                <th style="width: 300px;">{{trans('v3/new_tabla.detalle1')}} </th>
                                                <th style="width: 160px;">{{trans('v3/new_tabla.detalle2')}}</th>
                                                <th style="width: 160px;">{{trans('v3/new_tabla.detalle3')}}</th>
                                                <th style="width: 160px;">{{trans('v3/new_tabla.cantidad')}}</th>
                                                <th style="width: 160px;">{{trans('v3/new_tabla.codprincipal')}}</th>
                                                <th style="width: 160px;">{{trans('v3/new_tabla.codadicional')}}</th>
                                                <th style="width: 50px;text-align: center;" id="btnagregarAdicional">
                                                    <i style="color:#007BFF" class="fa-solid fa-circle-plus"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalleFactura">
                                            @if (isset(config('data.guiaExiste')->detalle))

                                            @foreach (config('data.guiaExiste')->detalle as $value)
                                            <tr id="fila{{ $value->id }}">
                                                <td>
                                                    <select id="producto{{ $value->id }}" name="producto[]" class="form-control validaciones nuevo_producto select2_cuentas nueva_vali_producto" require style="width: 100%" onchange="cargar_codigos(this,{{ $value->id }})">
                                                        <option value="{{ $value->detalleProducto->id }}">
                                                            {{ $value->detalleProducto->nombre }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td><input id="descripcion{{ $value->id }}" value="{{ $value->descripcion }}" class="form-control text-right nueva_vali_descrip" type="text" name="descripcion[]" required></td>
                                                <td><input id="observacion{{ $value->id }}" value="{{ $value->observacion }}" class="form-control text-right " type="text" name="observacion[]" required></td>
                                                <td> <input id="detalle3{{ $value->id }}" value="{{ $value->detalle3 }}" class="form-control text-right " type="text" name="detalle3[]"></td>
                                                <td> <input id="cantidad{{ $value->id }}" value="{{ $value->cantidad }}" class="form-control text-right " type="number" value="1" name="cantidad[]" onchange="validateZ(this);" onBlur="cantidadVathis(this)" required></td>
                                                <td><input readonly id="cod_principal{{ $value->id }}" value="{{ $value->detalleProducto->codigo }}" type="text" class="form-control validar" name="cod_principal[]" required></td>
                                                <td><input readonly id="cod_adicional{{ $value->id }}" value="{{ $value->detalleProducto->codigo }}" class="form-control px-1 text-right precioneto" type="text" name="cod_adicional[]" required></td>
                                                <td>
                                                    <i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila('<?= $value->id; ?>')" aria-hidden="true"></>
                                                </td>
                                            </tr>
                                            @endforeach

                                            @endif

                                        </tbody>
                                    </table>
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 60%;" valign="top">

                                                <a href="javascript:;" style="color:white" class="btn btn-warning" id="btnaregartransportista"><i class="fa-solid fa-circle-xmark"></i>
                                                    {{trans('v3/new_tabla.transportista')}}</a>
                                                <a href="javascript:;" class="btn btn-danger" id="btneliminar"><i class="fa-solid fa-circle-xmark"></i>{{trans('v3/new_tabla.cancelar')}}</a>
                                                <a href="javascript:;" class="btn btn-success" id="btnguardarGuia"><i class="fa-solid fa-save"></i>
                                                    @if (isset(config('data.guiaExiste')->id))
                                                    {{trans('v3/new_tabla.editar')}}
                                                    @else
                                                    {{trans('v3/new_tabla.guardar')}}
                                                    @endif
                                        </tr>
                                        <tr valign="top">
                                            <td colspan="3">
                                                <div style="position: relative;">
                                                    <div style="position: absolute;top:-191px;">
                                                        <input type="hidden" id="contadorAdicionales" value="0">
                                                        <table width="100%" cellspacing="0" cellspacing="0">
                                                            <tr>
                                                                <td alig ="left">
                                                                    <table id="tablaAdicionales" width="100%" cellspacing="0" cellspacing="0"></table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class=" modal fade modalTotal" id="modal_transportista">
    <div class="modal-dialog modal-lg" id="contenidoHtml">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.registrartransporte')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form id="frm_modaltransportista">
                        {{ csrf_field() }}
                        <input type="hidden" id="actionSaveTransportista" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveTransportista" />
                        <input type="hidden" id="d" name="d" />
                        <input type="hidden" id="id" name="id">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.cedulaoruc')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="text" maxlength="13" minlength="10" class="form-control" id="ci_ruc" name="ci_ruc" oninput="this.value = this.value.replace(/\D+/g, '')">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.razonsocial')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="razon_social" name="razon_social">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.telefono')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="telefono1" name="telefono1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.email')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.placa')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="placa" name="placa">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.tipodocumento')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <select name="tipo_documento" id="tipo_documento"
                                            class="form-control select2_tipoDoc" style="width: 88%;">
                                            <option value="">--{{trans('v3/new_varios.seleccione')}}--</option>
                                            <?php
                                            if (config('data.documentoGuia') != '' && count(config('data.documentoGuia')) > 0) {
                                                foreach (config('data.documentoGuia') as $value) {
                                                    echo '<option value="' . $value->valor . '" >' . $value->catalogo . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_tabla.direccion')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="direccion" name="direccion">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-3">
                                    <label>{{trans('v3/new_varios.rise')}}</label>
                                    <div class="input-group">
                                        <input type="checkbox" id="rise" name="rise" style="margin-left:10px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group" style="justify-content: center;align-items:center">
                            <a href="javascript:;" id="btntransportistaNuevo" class="btn btn-primary">{{trans('v3/new_varios.guardar')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>