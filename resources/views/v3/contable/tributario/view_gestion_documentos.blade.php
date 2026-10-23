<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('v3/admin')}}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Buscador</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 col-sm-2">
                            <div class="form-group">
                                <label>Desde</label>
                                <div class="input-group mb-3">
                                    <input type="date" id="desde" name="desde" class="form-control" value="{{ config('data.fecha_hoy') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="form-group">
                                <label>Hasta</label>
                                <div class="input-group mb-3">
                                    <input type="date" id="hasta" name="hasta" class="form-control" value="{{ config('data.fecha_hoy') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="form-group">
                                <label>Tipo</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="tipo" name="tipo">
                                        <option value="01" selected>Facturas</option>
                                        <option value="03">Liquidaciones de compra</option>
                                        <option value="04">Notas de crédito</option>
                                        <option value="06">Guías de remisión</option>
                                        <option value="07">Comprobante de retención</option>
                                        <option value="">Todos los documentos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="form-group">
                                <label>Estado</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="estado" name="estado">
                                        <option value="">--Seleccionar--</option>
                                        <?php
                                        if (config('data.pasos') != null && count(config('data.pasos')) > 0) {
                                            foreach (config('data.pasos') as $row) {

                                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 col-sm-6">
                            <label>&nbsp;</label>
                            <div class="input-group mb-6">
                                <button type="button" id="btnBuscarDocumentos" class="btn btn-primary">
                                    <i class="fa fa-search"></i> <span class="botones">Buscar</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-8 col-sm-6 align-self-end">
                            <small class="text-muted">Rango máximo de 92 días. Incluye facturas, liquidaciones, notas de crédito, guías y retenciones.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{config('data.titulo')}}</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                    <table id="tableGestionDocumentos" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gestionar</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Total</th>
                                <th>Ruc</th>
                                <th>Nombre</th>
                                <th>Fecha autorización</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Gestionar</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Total</th>
                                <th>Ruc</th>
                                <th>Nombre</th>
                                <th>Fecha autorización</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="loading"></div>
@include('v3.contable.tributario.view_modal_documento')
<script>
    $(document).off('click.gestionXsd', '.js-validar-xsd-documento')
        .on('click.gestionXsd', '.js-validar-xsd-documento', function () {
            var boton = $(this);
            var tipo = boton.data('tipo');
            var id = boton.data('id');
            boton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validando...');
            cargarModalDocumento('verLogDocumento', { tipo: tipo, id: id }, 'Gestión de ' + tipo + ': ' + id);
        });

    $(document).off('click.gestionSri', '.js-consultar-sri-documento')
        .on('click.gestionSri', '.js-consultar-sri-documento', function () {
            var boton = $(this);
            var tipo = boton.data('tipo');
            var id = boton.data('id');
            boton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
            $.ajax({
                url: $('#base_url').val() + '/v3/' + $('#controlador').val() + '/consultarSri',
                type: 'POST',
                dataType: 'json',
                headers: { 'X-CSRF-TOKEN': $('#token').val() },
                data: { tipo: tipo, id: id },
                success: function (respuesta) {
                    var nivel = respuesta.status === 'success' ? 'success' : 'info';
                    mensajes(nivel, respuesta.message || 'Consulta finalizada.');
                    buscarDocumentos();
                    cargarModalDocumento('verLogDocumento', { tipo: tipo, id: id }, 'Gestión de ' + tipo + ': ' + id);
                },
                error: function (xhr) {
                    mensajes('error', mensajeErrorAjax(xhr, 'No fue posible consultar el documento en el SRI.'));
                    boton.prop('disabled', false).html('<i class="fa fa-cloud"></i> Consultar y sincronizar con SRI');
                }
            });
        });
</script>
