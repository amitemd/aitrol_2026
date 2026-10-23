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
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregarProveedor" onclick="agregarProveedor();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableProveedores" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.ruc')}}</th>
                                    <th>{{trans('v3/new_tabla.razonSocial')}}</th>
                                    <th>{{trans('v3/new_tabla.nombreComercial')}}</th>
                                    <th>{{trans('v3/new_tabla.email')}}</th>
                                    <th>{{trans('v3/new_tabla.tipoProveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.logo')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>F. creado</th>
                                    <th>Creado</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.ruc')}}</th>
                                    <th>{{trans('v3/new_tabla.razonSocial')}}</th>
                                    <th>{{trans('v3/new_tabla.nombreComercial')}}</th>
                                    <th>{{trans('v3/new_tabla.email')}}</th>
                                    <th>{{trans('v3/new_tabla.tipoProveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.logo')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>F. creado</th>
                                    <th>Creado</th>
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
                <h4 class="modal-title" id="titulo_modalProveedor"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-content-above-general-tab" data-toggle="pill" href="#custom-content-above-general" role="tab" aria-controls="custom-content-above-general" aria-selected="true"><i class="fas fa-user"></i> <span class="botones">General</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-direccion-tab" data-toggle="pill" href="#custom-content-above-direccion" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="fas fa-map-marked-alt"></i> <span class="botones"> {{trans('v3/new_varios.ubicacion')}}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-bank-tab" data-toggle="pill" href="#custom-content-above-bank" role="tab" aria-controls="custom-content-above-bank" aria-selected="false"><i class="fas fa-bank"></i> <span class="botones"> {{trans('v3/new_varios.infoBancaria')}}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-otros-tab" data-toggle="pill" href="#custom-content-above-otros" role="tab" aria-controls="custom-content-above-otros" aria-selected="false"><i class="fa fa-files-o"></i> <span class="botones">{{trans('v3/new_varios.documentosOtros')}}</span></a>
                            </li>
                        </ul>
                        <form id="frm_">
                            {{csrf_field()}}
                            <input type="hidden" id="d" name="d" value="|">
                            <input type="hidden" id="estado" name="estado">
                            <input type="hidden" id="ciudad" name="ciudad">
                            <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveProveedor">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    @include('v3.contable.proveedores.view_general')
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-direccion" role="tabpanel" aria-labelledby="custom-content-above-direccion-tab">
                                    @include('v3.contable.proveedores.view_ubicacion')
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-bank" role="tabpanel" aria-labelledby="custom-content-above-bank-tab">
                                    @include('v3.contable.proveedores.view_bancarios')
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-otros" role="tabpanel" aria-labelledby="custom-content-above-otros-tab">
                                    @include('v3.contable.proveedores.view_otros')
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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