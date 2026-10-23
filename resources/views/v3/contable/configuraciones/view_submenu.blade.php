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
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-danger btn-sm" onclick="goBack('menu');"><i class="fas fa-arrow-circle-left"></i> <span class="botones">{{trans('v3/new_tabla.regresar')}}</span></a>
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarSubMenu();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_menu_" value="{{config('data.id_menu')}}">
                        <table id="tableSubMenu" class="table table-bordered table-striped tablaDatos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Menú</th>
                                    <th>Nombre</th>
                                    <th>Icono</th>
                                    <th>Controlador</th>
                                    <th>Funcion</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Menú</th>
                                    <th>Nombre</th>
                                    <th>Icono</th>
                                    <th>Controlador</th>
                                    <th>Funcion</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Cargando...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="id_menu" name="id_menu" value="{{config('data.id_menu')}}">
                    <input type="hidden" id="estado" name="estado_menu">
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveSubMenu">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Menu</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                            </div>
                                            <select class="form-control select2" id="id_pertenece_menu" name="id_pertenece_menu" style="width: 88%;">
                                                <option value="">--Seleccione--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="nombre_menu" name="nombre_menu" class="form-control" placeholder="Nombre del submenú">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Función</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="funcion_menu" name="funcion_menu" class="form-control" placeholder="Función del submenú">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Orden</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-sort-numeric-up-alt"></i></span>
                                            </div>
                                            <input type="text" id="orden_menu" name="orden_menu" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Orden del submenú">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Icono</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span onclick="iconModal()" class="input-group-text"><i class="fas fa-puzzle-piece"></i></span>
                                            </div>
                                            <input type="text" id="icon_menu" name="icon_menu" class="form-control" placeholder="Icono del submenú">
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
                <button type="button" class="btn btn-primary" id="btnGuardarSubMenu"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_icono" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true" style="z-index: 1400;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="rt"></div>
        </div>
    </div>
</div>