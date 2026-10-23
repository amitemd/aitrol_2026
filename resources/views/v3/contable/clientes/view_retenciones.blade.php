<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_menu.inicio') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Buscador</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Desde</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="desde" name="desde" class="form-control" value="<?php echo date("Y-m-d") ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="hasta" name="hasta" class="form-control" value="<?php echo date("Y-m-d") ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarRetencionesClientes()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
                                        </tr>
                                    </table>
                                </div>
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
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarRetencion();"><i
                                        class="fas fa-plus-circle"></i> <span
                                        class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-listretenciones">
                        <input type="hidden" id="id_" value="{{ config('data.id_proveedor') }}">
                        <table id="tableRetenciones" class="table table-bordered table-striped tablaDatos"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{ trans('v3/new_tabla.id') }}</th>
                                    <th>{{ trans('v3/new_tabla.fecha') }}</th>
                                    <th>{{ trans('v3/new_tabla.asiento') }}</th>
                                    <th>{{ trans('v3/new_tabla.cliente') }}</th>
                                    <th>{{ trans('v3/new_tabla.descripcion') }}</th>
                                    <th>{{ trans('v3/new_tabla.#compra') }}</th>
                                    <th>{{ trans('v3/new_tabla.comprobante') }}</th>
                                    <th>{{ trans('v3/new_tabla.totalrfir') }} </th>
                                    <th>{{ trans('v3/new_tabla.totalrfiva') }} </th>
                                    <th>{{ trans('v3/new_tabla.total') }}</th>
                                    <th>{{ trans('v3/new_tabla.creadapor') }}</th>
                                    <th>{{ trans('v3/new_tabla.estado') }}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ trans('v3/new_tabla.id') }}</th>
                                    <th>{{ trans('v3/new_tabla.fecha') }}</th>
                                    <th>{{ trans('v3/new_tabla.asiento') }}</th>
                                    <th>{{ trans('v3/new_tabla.cliente') }}</th>
                                    <th>{{ trans('v3/new_tabla.descripcion') }}</th>
                                    <th>{{ trans('v3/new_tabla.#compra') }}</th>
                                    <th>{{ trans('v3/new_tabla.comprobante') }}</th>
                                    <th>{{ trans('v3/new_tabla.totalrfir') }} </th>
                                    <th>{{ trans('v3/new_tabla.totalrfiva') }} </th>
                                    <th>{{ trans('v3/new_tabla.total') }}</th>
                                    <th>{{ trans('v3/new_tabla.creadapor') }}</th>
                                    <th>{{ trans('v3/new_tabla.estado') }}</th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_retencionesClientes"><i class="fas fa-edit"></i> </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3" id="contenidoHtml"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')
