@extends('guia.base')
@section('action-content')
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ trans('guia.guiaremision') }}</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-7">
                <h3 class="box-title">{{ trans('guia.guiaremision') }}</h3>
            </div>

            <div class="col-md-2 text-right">
                <a href="{{ route('index_guia_remision') }}" class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> {{ trans('guia.nuevaguiaremision') }}
                </a>
            </div>
        </div>

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{ trans('guia.buscadorguiaremision') }}</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="form_busqueda" action="{{route('guia_remision_buscador')}}">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="fecha_ini">Desde</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="date" id="desde" name="desde" />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="fecha_fin">Hasta</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="date" id="hasta" name="hasta" />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="estado">Transportista</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4 text-right">
                        <input class="form-control" type="text" id="transportista" name="transportista" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="fecha_ini">No. guia</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="num_guia" name="num_guia" />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="fecha_fin">No. Doc</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4">
                        <input class="form-control" type="text" id="num_doc" name="num_doc" />
                    </div>
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="estado">Destinatario</label>
                    </div>
                    <div class="form-group col-md-3 col-xs-10 container-4 text-right">
                        <input class="form-control" type="text" id="destinatario" name="destinatario" />
                    </div>
                </div>
                <div class="col-md-2 col-xs-2 col-xs-10 container-4">
                    <button type="button" id="buscarEmpleado" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                </div>
            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">{{ trans('guia.listaguiaremision') }}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="resultados">
                    </div>
                    <div id="contenedor" width="100%">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" width="100%">
                                            <thead>
                                                <tr class='well-dark'>
                                                    <th width="3%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ID</th>
                                                    <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: active to sort column asceding"> {{ trans('guia.numguia') }}</th>
                                                    <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: active to sort column asceding"> {{ trans('guia.fechaemision') }}</th>
                                                    <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: active to sort column asceding"> {{ trans('guia.transportista') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.direccionpartida') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.placa') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.destinatario') }}</th>
                                                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.direcciondestino') }}</th>
                                                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.codestablecimiento') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.numdocumento') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.infotributaria') }}</th>
                                                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.estadosri') }}</th>
                                                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{ trans('guia.accion') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ct_detalle as $key=>$value)
                                                <tr>
                                                    <td>{{$value->id}}</td>
                                                    @if($value->clave_acceso!='')
                                                    <td>{{$value->establecimiento.'-'.$value->punto_emision.'-'.str_pad($value->num_secuencial,9,0,STR_PAD_LEFT)}}</td>
                                                    @else
                                                    <td>&nbsp;</td>
                                                    @endif
                                                    <td>{{$value->created_at}}</td>
                                                    <td>@if(isset($value->razon_social) != '') {{$value->razon_social}} @else {{ $value->nombres.' '.$value->apellidos }} @endif</td>
                                                    <td>{{$value->direccion_partida}}</td>
                                                    <td>{{$value->placa}}</td>
                                                    <td> @if(isset($value->razon_social_destinatario) == true) {{$value->razon_social_destinatario}} @endif</td>
                                                    <td>{{$value->direccion_destinatario}}</td>
                                                    <td align="center">{{$value->codigo_est_destino}}</td>
                                                    <td>{{$value->num_doc_destino}}</td>
                                                    <td>
                                                        <?php
                                                        if ($value->clave_acceso != '' && $value->estado == 5) {
                                                        ?>
                                                            <a class="btn btn-primary btn-margin" onclick="verInfoTributaria('<?= $value->clave_acceso; ?>')"><i class="fa fa-eye" aria-hidden="true"></i> Mostrar</a>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    if ($value->estado == -1) {
                                                    ?>
                                                        <td style="text-align: center;">
                                                            <a class="btn btn-primary btn-margin" target="_blank" onclick="confirmar('<?= $value->id; ?>')"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i> <span id="changeText">Enviar</span> </a>
                                                        </td>
                                                    <?php
                                                    } elseif ($value->estado == 0) {
                                                    ?>
                                                        <td style="text-align: center;">
                                                            <a class="btn btn-primary btn-margin text-center"><i class="fa fa-spinner" aria-hidden="true"></i> En proceso</a>
                                                        </td>
                                                    <?php
                                                    } elseif ($value->estado == 5) {
                                                    ?>
                                                        <td style="text-align: center;">
                                                            <table style="text-align: center;" width="100%">
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <a href="../../api_doc_electronico?opcion=descargarXML&clave=<?= $value->clave_acceso; ?>" class="btn btn-info"><i class="fa fa-file-code-o"></i> XML</a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <a href="../../api_doc_electronico?opcion=generarPdf&clave=<?= $value->clave_acceso; ?>" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    <?php
                                                    } elseif ($value->estado == 7) {
                                                    ?>
                                                        <td style="text-align: center;">
                                                            <a class="btn btn-danger btn-margin text-center" onclick="verErrorXml('<?= $value->id ?>')"><i class="fa fa-close" aria-hidden="true"></i> Errores</a>
                                                        </td>
                                                    <?php
                                                    } elseif ($value->estado == 9) {
                                                    ?>
                                                        <td style="text-align: center;">
                                                            <a class="btn btn-danger btn-margin text-center" onclick="verIErrorTributaria('<?= $value->clave_acceso; ?>')"><i class="fa fa-close" aria-hidden="true"></i> Errores</a>
                                                        </td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td>

                                                        </td>
                                                    <?php
                                                    }
                                                    if ($value->estado != 5) {
                                                    ?>
                                                        <td>
                                                            <a class="btn btn-warning btn-margin" href="{{route('guia_remision_update',['id'=>$value->id])}}"><i class="fa fa-refresh" aria-hidden="true"></i> Actualizar</a>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($ct_detalle->currentPage() - 1) * $ct_detalle->perPage())}} / {{count($ct_detalle) + (($ct_detalle->currentPage() - 1) * $ct_detalle->perPage())}} de {{$ct_detalle->total()}} {{trans('contableM.registros')}}</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $ct_detalle->appends(Request::only(['id','concepto','sec_importacion']))->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="modalInfoTributaria" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Información Tributaria</h3>
            </div>
            <div class="modal-body">
                <p><b>Estado:</b> <input type="text" class="form-control text-right" id="estadoInfoT" readonly /></p>
                <p><b>No. Aut:</b> <input type="text" class="form-control text-right" id="numAutorizacionInfoT" readonly /></p>
                <p><b>Fecha. Aut:</b> <input type="text" class="form-control text-right" id="fechaAutorizacionInfoT" readonly /></p>
                <p><b>No. Documento:</b> <input type="text" class="form-control text-right" id="numDocInfoT" readonly /></p>
                <p><b>Tipo Doc:</b> <input type="text" class="form-control text-right" id="tipoDocInfoT" readonly /></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalErrorTributaria" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Información Tributaria</h3>
            </div>
            <div class="modal-body">
                <p><b>Estado:</b> <input type="text" class="form-control text-right" id="estadoErrorT" readonly /></p>
                <p><b>Clave Acceso:</b> <input type="text" class="form-control text-right" id="claveAccesoErrorT" readonly /></p>
                <p><b>Mensajes:</b> <textarea class="form-control" id="mensajeErrorT" readonly></textarea></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script>
    $(document).ready(function() {
        $('#buscarEmpleado').click(function() {
            if ($('#desde').val() != '') {
                if ($('#hasta').val() == '') {
                    Swal.fire({
                        title: 'Debe ingresar la fecha de inicio y final',
                        confirmButtonText: 'Ok',
                    });
                    return false;
                }

            }
            $.ajax({
                url: "{{route('guia_remision_buscador')}}",
                type: 'post',
                data: $('#form_busqueda').serialize(),
                dataType: 'json',
                success: function(json) {
                    $('#tabla tbody').html('');
                    var tabla = '';
                    $.each(json, function(i, item) {
                        var transportista = '';
                        var numDoc = '';
                        tabla += '<tr>';
                        tabla += '<td>' + item.id + '</td>';
                        if (item.clave_acceso != '') {
                            if (item.num_secuencial != 0)
                                tabla += '<td>' + item.establecimiento + '-' + item.punto_emision + '-' + PadLeft(item.num_secuencial, 9) + '</td>';
                            else
                                tabla += '<td>&nbsp;</td>';
                        } else
                            tabla += '<td>&nbsp;</td>';
                        tabla += '<td>' + item.created_at + '</td>';
                        console.log(item.razon_social);
                        if (item.razon_social !== null) {
                            transportista = item.razon_social;
                        } else {
                            transportista = item.nombres + ' ' + item.apellidos;
                        }
                        tabla += '<td>' + transportista + '</td>';
                        tabla += '<td>' + item.direccion_partida + '</td>';
                        tabla += '<td>' + item.placa + '</td>';
                        tabla += '<td>' + item.razon_social_destinatario + '</td>'
                        tabla += '<td>' + item.direccion_destinatario + '</td>'
                        tabla += '<td align="center">' + item.codigo_est_destino + '</td>'
                        if (item.num_doc_destino !== null) {
                            numDoc = item.num_doc_destino;
                        }
                        tabla += '<td>' + numDoc + '</td>';
                        if (item.clave_acceso != '' && item.estado == 5) {
                            tabla += '<td><a class = "btn btn-primary btn-margin" onclick = "verInfoTributaria(\'' + item.clave_acceso + '\')" ><i class="fa fa-eye" aria-hidden="true" ></i> Mostrar</a></td>';
                        } else {
                            tabla += '<td>&nbsp;</td>';
                        }
                        if (item.estado == -1) {
                            tabla += '<td>&nbsp;</td><td style="text-align:center;"><a class="btn btn-primary btn-margin" onclick="confirmar(\'' + item.id + '\');"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i> <span id="changeText">Enviar</span></td>';
                        } else if (item.estado == 0) {
                            tabla += '<td style="text-align:center;"><a class="btn btn-primary btn-margin text-center"> <i class="fa fa-spinner" aria-hidden="true"></i> En proceso</a></td>';
                        } else if (item.estado == 5) {
                            tabla += '<td style="text-align:center;"><table style="text-align: center;"width="100%"><tr><td style="text-align: center;"><a href="../../api_doc_electronico?opcion=descargarXML&clave=' + item.clave_acceso + '" class="btn btn-info"> <i class="fa fa-file-code-o"></i> XML</a></td></tr><tr><td> &nbsp;</td></tr><tr><td style="text-align: center;"><a href = "../../api_doc_electronico?opcion=generarPdf&clave=' + item.clave_acceso + '"class = "btn btn-danger" ><i class="fa fa-file-pdf-o"></i> PDF</a></td></tr></table></td>';
                        } else if (item.estado == 7) {
                            tabla += '<td style="text-align: center;"><a class="btn btn-danger btn-margin text-center" onclick="verErrorXml(\'' + item.id + '\')"><i class="fa fa-close" aria-hidden="true"></i> Errores</a></td>';
                        } else if (item.estado == 9) {
                            tabla += '<td style="text-align: center;"><a class="btn btn-danger btn-margin text-center" onclick="verIErrorTributaria(\'' + item.clave_acceso + '\')"><i class="fa fa-close" aria-hidden="true"></i> Errores</a></td><td>&nbsp;</td>';
                        } else {
                            tabla += '<td>&nbsp;</td>';
                        }
                        if (item.estado != 5) {
                            tabla += '<td><a class="btn btn-warning btn-margin" href="update?id=' + item.id + '"><i class="fa fa-refresh" aria-hidden="true"></i> Actualizar</a></td>';
                        } else {
                            tabla += '<td>&nbsp;</td>';
                        }
                        tabla += '</tr>'
                    });
                    $('#tabla tbody').html(tabla);
                }
            });
        });
    });

    function PadLeft(value, length) {
        try {
            return (value.toString().length < length) ? PadLeft("0" + value, length) : value;
        } catch (error) {
            mensaje(error, 'error', '', '');
        }
    }

    function verErrorXml(num) {
        $.ajax({
            url: "{{ route('sri_electronico/errorGeneral') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                id: num
            },
            dataType: 'json',
            success: function(json) {
                var texto = '';
                $.each(JSON.parse(json.descripcion_error), function(i, item) {
                    texto += item + '<br/>';
                });
                Swal.fire({
                    icon: 'error',
                    html: texto,
                    title: 'Error al generar el XML',
                    confirmButtonText: 'Ok',
                });
            }
        });
    }

    function verIErrorTributaria(clave) {
        $.ajax({
            url: "{{ route('sri_electronico/errorTributario') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#estadoErrorT').val(json.xml.estado);
                $('#claveAccesoErrorT').val(json.xml.comprobantes.comprobante.claveAcceso);
                $('#mensajeErrorT').val(json.xml.MensajesDb.mensajeDb);
                $('#modalErrorTributaria').modal('show');
            }
        });
    }

    function verInfoTributaria(clave) {
        $.ajax({
            url: "{{ route('sri_electronico/infoTributario') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#estadoInfoT').val(json.estado[0]);
                $('#numAutorizacionInfoT').val(json.claveAccesoConsultada[0]);
                var fechaAut = json.fechaAutorizacion[0].split('T');
                var horaAut = fechaAut[1].split('-');
                $('#fechaAutorizacionInfoT').val(fechaAut[0] + ' ' + horaAut[0]);
                $('#numDocInfoT').val(json.numeroDocumento);
                $('#tipoDocInfoT').val('Guia de Remisón');
                $('#modalInfoTributaria').modal('show');
            }
        });
    }
    const enviar = (id) => {
        $.ajax({
            type: 'post',
            url: "{{ route('send_information_guia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                if (!data.error) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verificación correcta',
                    }).then(() => {
                        $("#changeIcon").removeClass('fa fa-send').addClass('fa fa-spinner');
                        $("#changeText").text('En proceso');
                    })
                }
            },
            error: function(data) {}
        })
    }
    const confirmar = (id) => {
        Swal.fire({
            title: 'Esta seguro de queres enviar al sri?',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            denyButtonText: 'Cerrar',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                enviar(id);
            }
        });
    }
</script>
@endsection