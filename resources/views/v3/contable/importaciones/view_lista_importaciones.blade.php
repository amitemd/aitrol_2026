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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarImportacion();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-contenido">
                        <table id="tableImportaciones" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.secuenciaimportacion')}}</th>
                                    <th>{{trans('v3/new_tabla.proveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.fechapedido')}}</th>
                                    <th>{{trans('v3/new_tabla.numerodefactura')}}</th>
                                    <th>{{trans('v3/new_tabla.serie')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th>Kardex</th>
                                    <th>{{trans('v3/new_tabla.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.secuenciaimportacion')}}</th>
                                    <th>{{trans('v3/new_tabla.proveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.fechapedido')}}</th>
                                    <th>{{trans('v3/new_tabla.numerodefactura')}}</th>
                                    <th>{{trans('v3/new_tabla.serie')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th>Kardex</th>
                                    <th>{{trans('v3/new_tabla.accion')}}</th>

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
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
                            aria-controls="general" aria-selected="true"><i class="fa fa-user"
                                aria-hidden="true"></i>General</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ubicacion-tab" data-toggle="tab" href="#ubicacion" role="tab"
                            aria-controls="ubicacion" aria-selected="false"><i class="fa fa-map-marker"
                                aria-hidden="true"></i>Ubicacion&oacute;</a>
                    </li>
                </ul>
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="action"
                        value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveCliente">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            @include('v3.contable.clientes.view_general')
                        </div>
                        <div class="tab-pane fade" id="ubicacion" role="tabpanel" aria-labelledby="ubicacion-tab">
                            @include('v3.contable.clientes.view_ubicacion')
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i>
                    Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCliente"><i class="fas fa-save"></i>
                    Guardar</button>
            </div>
        </div>
    </div>
</div>
