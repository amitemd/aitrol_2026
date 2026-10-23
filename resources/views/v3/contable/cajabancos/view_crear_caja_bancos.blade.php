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
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarCajabancos();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id')}}">
                        <table id="tableCrearCajaBancos" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.codigo')}}</th>
                                    <th>{{trans('v3/new_tabla.nombre')}}</th>
                                    <th>{{trans('v3/new_tabla.clase')}}</th>
                                    <th>{{trans('v3/new_tabla.grupo')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentamayor')}}</th>
                                    <th>{{trans('v3/new_tabla.sucursal')}}</th>
                                    <th>{{trans('v3/new_tabla.formadepago')}}</th>
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
                                    <th>{{trans('v3/new_tabla.clase')}}</th>
                                    <th>{{trans('v3/new_tabla.grupo')}}</th>
                                    <th>{{trans('v3/new_tabla.cuentamayor')}}</th>
                                    <th>{{trans('v3/new_tabla.sucursal')}}</th>
                                    <th>{{trans('v3/new_tabla.formadepago')}}</th>
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
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i>{{trans('v3/new_tabla.crearVisualizarCajaBanco')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveCajaBanco">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.codigo')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="codigo" name="codigo" class="form-control text-right" placeholder="{{trans('v3/new_varios.codigo')}}">
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
                                                    <label>{{trans('v3/new_varios.numero_cuenta')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.numero_cuenta')}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.clase')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="clase" name="clase" style="width: 100%;">
                                                            <option value="">-- {{trans('v3/new_varios.seleccionar')}} --</option>
                                                            <option value="1">{{trans('v3/new_varios.cuenta_bancaria')}}</option>
                                                            <option value="2">{{trans('v3/new_varios.cuenta_caja')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.grupo')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2 grupo" id="grupo" name="grupo" style="width: 100%;">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            if (config('data.grupo') != null && count(config('data.grupo')) > 0) {
                                                                foreach (config('data.grupo') as $row) {
                                                                    echo '<option value="' . $row->id_plan . '">' . $row->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.cuenta_mayor')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2 cuenta_mayor" id="cuenta_mayor" name="cuenta_mayor" style="width: 100%;">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.sucursal')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="sucursal" name="sucursal" style="width: 100%;">
                                                            <option value="">-- {{trans('v3/new_varios.seleccionar')}} --</option>
                                                            <?php
                                                            foreach (config('data.sucursales') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->nombre_sucursal . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.divisa')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="divisa" name="divisa" style="width: 100%;">
                                                            <option value="">-- {{trans('v3/new_varios.seleccionar')}} --</option>
                                                            <?php
                                                            foreach (config('data.divisa') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.forma_pago')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="formas_pago" name="formas_pago" style="width: 100%;">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                            <?php
                                                            foreach (config('data.formas_pago') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.ultimo_cheque')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="ultimo_cheque" name="ultimo_cheque" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="{{trans('v3/new_varios.ultimo_cheque')}}">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_varios.comentario')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="comentarios" name="comentarios" class="form-control" placeholder="{{trans('v3/new_varios.comentario')}}">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCajaBanco"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>