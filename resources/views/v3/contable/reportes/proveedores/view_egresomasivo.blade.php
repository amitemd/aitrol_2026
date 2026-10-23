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

            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{trans('v3/new_tabla.buscador')}}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3 col-sm-3">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.desde')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="desde" name="desde" class="form-control" value="{{config('data.d')!=''?date('Y-m-d'):''}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 col-sm-3">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.hasta')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="hasta" name="hasta" class="form-control" value="{{config('data.h')!=''?date('Y-m-d'):''}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-4">
                                <label>{{trans('v3/new_tabla.proveedor')}}</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2_proveedor" id="id_proveedor" name="id_proveedor">
                                        <option value="">--Seleccione--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 col-sm-2">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarEgresoMasivo()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
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
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableProveedoresEgresoMasivo" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.secuencia')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.fecheque')}}</th>
                                    <th>{{trans('v3/new_tabla.nocheque')}}</th>
                                    <th>{{trans('v3/new_tabla.giradoa')}}</th>
                                    <th>{{trans('v3/new_tabla.valor')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.secuencia')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.fecheque')}}</th>
                                    <th>{{trans('v3/new_tabla.nocheque')}}</th>
                                    <th>{{trans('v3/new_tabla.giradoa')}}</th>
                                    <th>{{trans('v3/new_tabla.valor')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
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
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="ciudad" name="ciudad">
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveProveedor">
            </div>
            </form>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarProveedor"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modalTotal" id="modalImg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_img"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_img" style="text-align: center;">
                    {{csrf_field()}}
                    <img id="url_logo" name="url_logo" style="width: auto; height: 200px;">
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')