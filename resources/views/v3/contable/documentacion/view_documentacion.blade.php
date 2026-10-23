<style>
    .docs-hero {
        background: linear-gradient(135deg, #243b53 0%, #3c6997 100%);
        border-radius: 8px;
        color: #fff;
    }
    .doc-card {
        background: #fff;
        border-left: 0;
        border-right: 0;
        border-bottom: 0;
        border-top: 4px solid #3c6997;
        cursor: pointer;
        height: 100%;
        text-align: left;
        transition: box-shadow .15s ease, transform .15s ease;
        width: 100%;
    }
    .doc-card:hover { box-shadow: 0 .35rem .75rem rgba(0, 0, 0, .15) !important; transform: translateY(-2px); }
    .doc-card:focus { box-shadow: 0 0 0 .2rem rgba(60, 105, 151, .3); outline: 0; }
    .doc-icon {
        align-items: center;
        border-radius: 8px;
        display: flex;
        font-size: 1.25rem;
        height: 44px;
        justify-content: center;
        width: 44px;
    }
    .flow-step { position: relative; }
    .flow-number {
        align-items: center;
        background: #e8f1f8;
        border-radius: 50%;
        color: #24557a;
        display: flex;
        font-weight: 700;
        height: 34px;
        justify-content: center;
        width: 34px;
    }
    .docs-path { word-break: break-all; }
    .markdown-content { color: #273444; font-size: .95rem; line-height: 1.65; }
    .markdown-content h3:first-child { margin-top: 0 !important; }
    .markdown-content h3, .markdown-content h4 { color: #244f73; }
    .markdown-content code { background: #eef2f6; border-radius: 3px; color: #b52b4a; padding: 2px 5px; }
    .markdown-content pre code { background: transparent; color: #273444; padding: 0; }
    .markdown-content table { font-size: .9rem; }
    .functional-map td, .functional-map th { vertical-align: top; }
    .document-loading { min-height: 220px; }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7"><h1>Centro de documentacion v3</h1></div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Documentacion</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container-fluid">
        <div class="card docs-hero shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2><i class="fas fa-book mr-2"></i>Referencia tecnica y operativa</h2>
                        <p class="lead mb-2">Documentacion basada en la estructura real encontrada en la version v3.</p>
                        <span><i class="far fa-calendar-alt mr-2"></i>Actualizada: {{ $fechaDocumentacion }}</span>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <a href="{{ route('v3.matriz-madurez') }}" class="btn btn-light mr-2">
                            <i class="fas fa-chart-bar mr-1"></i>Madurez
                        </a>
                        <a href="{{ route('v3.operacion-monitoreo') }}" class="btn btn-outline-light">
                            <i class="fas fa-heartbeat mr-1"></i>Monitoreo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($documentosV3 as $documento)
                <div class="col-lg-4 col-md-6 mb-3">
                    <button type="button"
                            class="card doc-card doc-open shadow-sm mb-0"
                            data-url="{{ route('v3.documentacion.detalle', ['documento' => $documento['id']]) }}"
                            data-title="{{ $documento['titulo'] }}"
                            aria-label="Abrir {{ $documento['titulo'] }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <span class="doc-icon bg-{{ $documento['color'] }} text-white mr-3"><i class="{{ $documento['icono'] }}"></i></span>
                                <div>
                                    <h5 class="mb-1">{{ $documento['titulo'] }}</h5>
                                    <div class="small text-primary docs-path mb-2">{{ $documento['archivo'] }}</div>
                                    <p class="text-muted mb-0">{{ $documento['descripcion'] }}</p>
                                    <div class="small text-primary mt-2"><i class="fas fa-eye mr-1"></i>Abrir documento</div>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-0"><h3 class="card-title"><i class="fas fa-sitemap mr-2"></i>Flujo general de una peticion v3</h3></div>
            <div class="card-body">
                <div class="row">
                    @foreach ($flujoV3 as $indice => $paso)
                        <div class="col-lg col-md-6 flow-step mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="flow-number mr-2">{{ $indice + 1 }}</span>
                                <i class="{{ $paso['icono'] }} text-primary mr-2"></i>
                                <strong>{{ $paso['titulo'] }}</strong>
                            </div>
                            <div class="small text-muted">{{ $paso['detalle'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-th-large mr-2"></i>Opciones funcionales confirmadas</h3>
                <div class="card-tools"><span class="badge badge-info p-2">{{ $cantidadOpcionesV3 }} entradas reales de controladores v3</span></div>
            </div>
            <div class="card-body border-top pb-2">
                <p class="text-muted mb-2">Se muestran pantallas a las que realmente se puede ingresar. No se incluyen acciones internas de guardado, consultas AJAX, impresion o descarga.</p>
                <input type="search" id="buscarOpcionFuncional" class="form-control" placeholder="Buscar opcion, ruta o funcion...">
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover functional-map mb-0" id="tablaOpcionesFuncionales">
                    <thead class="thead-light"><tr><th>Modulo</th><th>Opcion real</th><th>Ingreso directo</th><th>Para que sirve</th></tr></thead>
                    <tbody>
                        @foreach ($modulosV3 as $modulo)
                            @foreach ($modulo['opciones'] as $opcion)
                                <tr>
                                    <td><strong>{{ $modulo['nombre'] }}</strong></td>
                                    <td>{{ $opcion['nombre'] }}</td>
                                    <td><a href="{{ url($opcion['ruta']) }}"><code>{{ $opcion['ruta'] }}</code></a></td>
                                    <td>{{ $opcion['responsabilidad'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div id="sinOpcionesFuncionales" class="alert alert-info m-3 d-none">No se encontraron opciones con ese criterio.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header border-0"><h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Reglas para cambios seguros</h3></div>
                    <div class="card-body">
                        @foreach ($reglasDesarrollo as $indice => $regla)
                            <div class="d-flex {{ !$loop->last ? 'mb-3' : '' }}">
                                <span class="badge badge-success mr-3 mt-1">{{ $indice + 1 }}</span><span>{{ $regla }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm border-warning">
                    <div class="card-header border-0"><h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Documentacion pendiente</h3></div>
                    <div class="card-body">
                        @foreach ($pendientesDocumentacion as $pendiente)
                            <div class="d-flex {{ !$loop->last ? 'mb-3' : '' }}">
                                <i class="far fa-square text-warning mr-3 mt-1"></i><span>{{ $pendiente }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalDocumento" tabindex="-1" role="dialog" aria-labelledby="modalDocumentoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h4 class="modal-title" id="modalDocumentoTitulo"><i class="fas fa-book-open text-primary mr-2"></i>Documento</h4>
                    <div class="small text-muted docs-path" id="modalDocumentoArchivo"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="documentoCargando" class="document-loading d-flex align-items-center justify-content-center">
                    <div class="text-center text-muted">
                        <i class="fas fa-circle-notch fa-spin fa-2x mb-3"></i>
                        <div>Cargando documento...</div>
                    </div>
                </div>
                <div id="documentoError" class="alert alert-danger d-none mb-0"></div>
                <article id="documentoContenido" class="markdown-content d-none"></article>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).off('input.documentacion', '#buscarOpcionFuncional').on('input.documentacion', '#buscarOpcionFuncional', function () {
        var texto = ($(this).val() || '').toLowerCase();
        var visibles = 0;
        $('#tablaOpcionesFuncionales tbody tr').each(function () {
            var mostrar = texto === '' || $(this).text().toLowerCase().indexOf(texto) !== -1;
            $(this).toggle(mostrar);
            if (mostrar) visibles++;
        });
        $('#sinOpcionesFuncionales').toggleClass('d-none', visibles !== 0);
    });

    $(document).on('click', '.doc-open', function () {
        var boton = $(this);
        var modal = $('#modalDocumento');

        $('#modalDocumentoTitulo').html('<i class="fas fa-book-open text-primary mr-2"></i>' + $('<div>').text(boton.data('title')).html());
        $('#modalDocumentoArchivo').text('');
        $('#documentoCargando').removeClass('d-none').addClass('d-flex');
        $('#documentoError').addClass('d-none').text('');
        $('#documentoContenido').addClass('d-none').empty();
        modal.modal('show');

        $.ajax({
            url: boton.data('url'),
            method: 'GET',
            dataType: 'json'
        }).done(function (respuesta) {
            $('#modalDocumentoTitulo').html('<i class="fas fa-book-open text-primary mr-2"></i>' + $('<div>').text(respuesta.titulo).html());
            $('#modalDocumentoArchivo').text(respuesta.archivo);
            $('#documentoContenido').html(respuesta.contenido).removeClass('d-none');
        }).fail(function (respuesta) {
            var mensaje = respuesta.status === 403
                ? 'La sesion no tiene permisos para consultar este documento.'
                : 'No fue posible cargar el documento. Intente nuevamente.';
            $('#documentoError').text(mensaje).removeClass('d-none');
        }).always(function () {
            $('#documentoCargando').addClass('d-none').removeClass('d-flex');
        });
    });

    $('#modalDocumento').on('hidden.bs.modal', function () {
        $('#documentoContenido').empty().addClass('d-none');
        $('#documentoError').addClass('d-none').text('');
    });
</script>
