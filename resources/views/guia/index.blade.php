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
        <div id="example2_wrapper" class="box-body dobra">
            <div class="ibox-content">
                <div class="table-responsive col-md-12">
                    <table id="tableGuias" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SRI</th>
                                <th>{{ trans('guia.numguia') }}</th>
                                <th>{{ trans('guia.destinatario') }}</th>
                                <th>{{ trans('guia.tipo_guia') }}</th>
                                <th>{{ trans('guia.fechaemision') }}</th>
                                <th>{{ trans('guia.direcciondestino') }}</th>
                                <th>{{ trans('guia.codestablecimiento') }}</th>
                                <th>{{ trans('guia.numdocumento') }}</th>
                                <th>{{ trans('guia.transportista') }}</th>
                                <th>{{ trans('guia.direccionpartida') }}</th>
                                <th>{{ trans('guia.placa') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                        $('#btnEnviar' + id).html('<a class="btn btn-primary btn-xs"><i class="fa fa-fw fa-spinner" title="En proceso"></i></a>');
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