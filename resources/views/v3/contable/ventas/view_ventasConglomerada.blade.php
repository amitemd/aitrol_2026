<style>
    #crearCliente {
        cursor: pointer;
    }
</style>
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
                                        <input type="date" id="desde" name="desde" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="hasta" name="hasta" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary"
                                                    onclick="buscarConglomerada()"><i class="fas fa-search"></i> <span
                                                        class="botones">Buscar</span></a></td>
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
                        <h3 class="card-title">{{ trans('v3/new_submodulo.' . config('data.titulo')) }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm"
                                    onclick="agregarConglomerada();"><i class="fas fa-plus-circle"></i> <span
                                        class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{ config('data.id_proveedor') }}">
                        <table id="tableVentasConglomerada" class="table table-bordered table-striped tablaDatos"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{ trans('v3/new_tabla.codigo') }}</th>
                                    <th>{{ trans('v3/new_tabla.numerofactura') }}</th>
                                    <th>{{ trans('v3/new_tabla.tipo') }}</th>
                                    <th>Fecha Factura</th>
                                    <th>Cliente</th>
                                    <th>Paciente</th>
                                    <th>Seguro</th>
                                    <th>Procedimiento</th>
                                    <th>Fecha Procedimiento</th>
                                    <th>Creado por</th>
                                    <th>Anulado por</th>
                                    <th>Electronica</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ trans('v3/new_tabla.codigo') }}</th>
                                    <th>{{ trans('v3/new_tabla.numerofactura') }}</th>
                                    <th>{{ trans('v3/new_tabla.tipo') }}</th>
                                    <th>Fecha Factura</th>
                                    <th>Cliente</th>
                                    <th>Paciente</th>
                                    <th>Seguro</th>
                                    <th>Procedimiento</th>
                                    <th>Fecha Procedimiento</th>
                                    <th>Creado por</th>
                                    <th>Anulado por</th>
                                    <th>Electronica</th>
                                    <th>Acción</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
