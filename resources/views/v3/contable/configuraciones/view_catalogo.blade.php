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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('v3/new_titulos.'.config('data.titulo')) }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <?php
                                if (config('data.id') != '') {
                                ?>
                                    <a href="javascript:;" class="btn btn-danger btn-sm" onclick="goBack('catalogo');"><i class="fas fa-arrow-circle-left"></i> <span class="botones">{{trans('v3/new_tabla.regresar')}}</span></a>
                                <?php
                                }
                                ?>
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarCatalogo();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tableCatalogo" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>Valor</th>
                                    <th>Creación</th>
                                    <th>Modificación</th>
                                    <th>F. creación</th>
                                    <th>F. modificación</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>Valor</th>
                                    <th>Creación</th>
                                    <th>Modificación</th>
                                    <th>F. creación</th>
                                    <th>F. modificación</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
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
<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="id_catalogo" name="id_catalogo">
                    <input type="hidden" id="id_subcatalogo" name="id_subcatalogo" value="{{config('data.id')!=''?config('data.id'):''}}">
                    <input type="hidden" id="estado" name="estado_catalogo">
                    <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveCatalogo">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="catalogo" name="catalogo" class="form-control" placeholder="Nombre del catálogo">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Valor</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="valor" name="valor" class="form-control" placeholder="Valor del catálogo">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox" class="custom-control-input" id="cheestado">
                                            <label class="custom-control-label" for="cheestado">Estado</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCatalogo"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>