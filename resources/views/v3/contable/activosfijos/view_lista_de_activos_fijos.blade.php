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
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarTipoActivosFijos();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableListadeActivosFijos" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.fechacompra')}}</th>
                                    <th>{{trans('v3/new_tabla.codigo')}}</th>
                                    <th> {{trans('v3/new_tabla.nombre')}} </th>
                                    <th> {{trans('v3/new_tabla.tipo')}} </th>
                                    <th> {{trans('v3/new_tabla.categoria')}} </th>
                                    <th> {{trans('v3/new_tabla.responsable')}} </th>
                                    <th> {{trans('v3/new_tabla.ubicacion')}} </th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.fechacompra')}}</th>
                                    <th>{{trans('v3/new_tabla.codigo')}}</th>
                                    <th> {{trans('v3/new_tabla.nombre')}} </th>
                                    <th> {{trans('v3/new_tabla.tipo')}} </th>
                                    <th> {{trans('v3/new_tabla.categoria')}} </th>
                                    <th> {{trans('v3/new_tabla.responsable')}} </th>
                                    <th> {{trans('v3/new_tabla.ubicacion')}} </th>
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
                <h4 class="modal-title" id="titulo_activos_fijos"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
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
                                <a class="nav-link" id="custom-content-above-algo-tab" data-toggle="pill" href="#custom-content-above-algo" role="tab" aria-controls="custom-content-above-algo" aria-selected="false"><i class="fas fa-user"></i> <span class="botones"> Algo</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-contabilidad-tab" data-toggle="pill" href="#custom-content-above-contabilidad" role="tab" aria-controls="custom-content-above-contabilidad" aria-selected="false"><i class="fas fa-user"></i> <span class="botones"> Contabilidad</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-content-above-archivos-tab" data-toggle="pill" href="#custom-content-above-archivos" role="tab" aria-controls="custom-content-above-archivos" aria-selected="false"><i class="fas fa-map-marked-alt"></i> <span class="botones">Archivos</span></a>
                            </li>
                        </ul>
                        <form id="frm_" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" id="d" name="d" />
                            <input type="hidden" id="id" name="id" />
                            <input type="hidden" id="action" value="v3/{{config('data.controlador')}}/saveActivoFijo" />
                            <input type="hidden" id="images_base64" name="images_base64">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    @include('v3.contable.activosfijos.view_general')
                                </div>
                                <div class="tab-pane fade " id="custom-content-above-algo" role="tabpanel" aria-labelledby="custom-content-above-algo-tab">
                                    @include('v3.contable.activosfijos.view_algo')
                                </div>
                                <div class="tab-pane fade " id="custom-content-above-contabilidad" role="tabpanel" aria-labelledby="custom-content-above-contabilidad-tab">
                                    @include('v3.contable.activosfijos.view_contabilidad')
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-archivos" role="tabpanel" aria-labelledby="custom-content-above-archivos-tab">
                                    <!-- @include('v3.contable.activosfijos.view_archivos') -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarActivoFijo"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js"></script>