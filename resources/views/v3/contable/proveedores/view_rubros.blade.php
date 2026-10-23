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
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarRubros();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableRubros" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.codigo')}}</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentadebe')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentahaber')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.codigo')}}</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentadebe')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentahaber')}}</th>
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
<div class="modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Crear / Visualizar Rubros</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body">
                        <form id="frm_">
                            {{csrf_field()}}
                            <input type="hidden" id="d" name="d" value="|">
                            <input type="hidden" id="estado" name="estado">
                            <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveRubros">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('v3/new_varios.codigo')}}</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                </div>
                                                <input type="text" id="codigo" name="codigo" class="form-control" placeholder="{{trans('v3/new_varios.codigo')}}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('v3/new_tabla.nombre')}}</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                </div>
                                                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="{{trans('v3/new_tabla.nombre')}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('v3/new_varios.debe')}}</label>
                                            <div class="input-group mb-3">
                                                <select name="debe" id="debe" class="form-control select2" style="width: 100%;">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('v3/new_varios.haber')}}</label>
                                            <div class="input-group mb-3">
                                                <select name="haber" id="haber" class="form-control select2" style="width: 100%;">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label>{{trans('v3/new_varios.tipo')}}</label>
                                            <div class="input-group mb-3">
                                                <select class="form-control select2" id="tipo" name="tipo" style="width: 100%;">
                                                    <option value="">--Seleccione--</option>
                                                    <option value="1">{{trans('v3/new_varios.ingreso')}}</option>
                                                    <option value="0">{{trans('v3/new_varios.egreso')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" id="cheestado">
                                                <label class="custom-control-label" for="cheestado">{{trans('v3/new_tabla.estado')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarRubros"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>