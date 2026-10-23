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
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="tableAuditoria" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.sistema')}}</th>
                                    <th>{{trans('v3/new_tabla.navegador')}}</th>
                                    <th>{{trans('v3/new_tabla.controlador')}}</th>
                                    <th>{{trans('v3/new_tabla.funcion')}}</th>
                                    <th>{{trans('v3/new_tabla.modelo')}}</th>
                                    <th>{{trans('v3/new_tabla.vista')}}</th>
                                    <th>{{trans('v3/new_tabla.usuario')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.sistema')}}</th>
                                    <th>{{trans('v3/new_tabla.navegador')}}</th>
                                    <th>{{trans('v3/new_tabla.controlador')}}</th>
                                    <th>{{trans('v3/new_tabla.funcion')}}</th>
                                    <th>{{trans('v3/new_tabla.modelo')}}</th>
                                    <th>{{trans('v3/new_tabla.vista')}}</th>
                                    <th>{{trans('v3/new_tabla.usuario')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
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
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i>{{trans('v3/new_tabla.editar')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.id')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                            </div>
                                            <input type="text" id="id_auditoria" name="id_auditoria" class="form-control text-right" placeholder="Id auditoria" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.controlador')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-mandalorian"></i></span>
                                            </div>
                                            <input type="text" id="controlador_" name="controlador_" class="form-control" placeholder="Controlador" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.modelo')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-object-group"></i></span>
                                            </div>
                                            <input type="text" id="modelo" name="modelo" class="form-control" placeholder="Módelo" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.submodulo')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-object-ungroup"></i></span>
                                            </div>
                                            <input type="text" id="submodulo" name="submodulo" class="form-control" placeholder="Submodulo" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.vista')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-eye"></i></span>
                                            </div>
                                            <input type="text" id="vista" name="vista" class="form-control" placeholder="Vista" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.ip')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-globe-americas"></i></span>
                                            </div>
                                            <input type="text" id="ip" name="ip" class="form-control text-right" placeholder="Ip" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.sistema')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-desktop"></i></span>
                                            </div>
                                            <input type="text" id="sistema" name="sistema" class="form-control" placeholder="Sistema" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.navegador')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-internet-explorer"></i></span>
                                            </div>
                                            <input type="text" id="navegador" name="navegador" class="form-control" placeholder="Navegador" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.version')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                                            </div>
                                            <input type="text" id="version" name="version" class="form-control text-right" placeholder="Version" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.tiempoConsulta')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            <input type="text" id="tiempo" name="tiempo" class="form-control text-right" placeholder="Tiempo" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.usuario')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_tabla.fecha')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" id="fecha" name="fecha" class="form-control text-right" placeholder="Fecha" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Latitud</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend" onclick="verUbicacion();">
                                                <span class="input-group-text"><i class="far fa-compass"></i></span>
                                            </div>
                                            <input type="text" id="latitud" name="latitud" class="form-control text-right" placeholder="Latitud" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Longitud</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend" onclick="verUbicacion()">
                                                <span class="input-group-text"><i class="far fa-compass"></i></span>
                                            </div>
                                            <input type="text" id="longitud" name="longitud" class="form-control text-right" placeholder="Longitud" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Observación <i class="fas fa-database" onclick="verQuery()"></i></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <textarea id="observacion" name="observacion" class="form-control" rows="3" placeholder="Observación" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Query</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <textarea id="query" name="query" class="form-control" rows="3" placeholder="Query" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Parámetros</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <textarea id="parametros" name="parametros" class="form-control" rows="3" placeholder="Parametros" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Obj</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <textarea id="obj" name="obj" class="form-control" rows="3" placeholder="Objeto json" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="map"> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>