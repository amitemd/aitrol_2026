<section class="content-header">
    <input type="hidden" id="rutaImagen" value="{{str_replace('public/','', asset('storage/app/v3/compras'))}}" />
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
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarCompras()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
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
                        <h3 class="card-title">{{trans('v3/new_submodulo.'.config('data.titulo'))}}</h3>
                        <!--<div>
                            <div style="position: absolute; right: 5px;top:7px"><a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarCompra();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a></div>
                        </div>-->
                    </div>
                    <input type="hidden" id="pilozopendejo" value="{{asset('v3/utilidades/descargaArchivo/')}}">
                    <div class="card-body"><input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableCompras" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.no.comprobante')}}</th>
                                    <th>{{trans('v3/new_tabla.proveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.no.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.tipo')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>{{trans('v3/new_tabla.valor0%')}}</th>
                                    <th>{{trans('v3/new_tabla.valor12%')}}</th>
                                    <th>{{trans('v3/new_tabla.iva')}}</th>
                                    <th>{{trans('v3/new_tabla.total')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>{{trans('v3/new_tabla.creado')}}</th>
                                    <th>{{trans('v3/new_tabla.anulado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.no.comprobante')}}</th>
                                    <th>{{trans('v3/new_tabla.proveedor')}}</th>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.no.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.tipo')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>{{trans('v3/new_tabla.valor0%')}}</th>
                                    <th>{{trans('v3/new_tabla.valor12%')}}</th>
                                    <th>{{trans('v3/new_tabla.iva')}}</th>
                                    <th>{{trans('v3/new_tabla.total')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>{{trans('v3/new_tabla.creado')}}</th>
                                    <th>{{trans('v3/new_tabla.anulado')}}</th>
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
<div class="modal fade modalTotal" id="myModalDocumento">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_compra"><i class="fas fa-edit"></i> </h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3" id="contenidoHtml"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="modal-footer justify-content-between"><button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button></div>
        </div>
    </div>
</div>
<div class="modal fade modalTotal" id="myModalRetencion">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_retencion"><i class="fas fa-edit"></i> </h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3" id="contenidoHtmlRetencion"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="modal-footer justify-content-between"><button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button></div>
        </div>
    </div>
</div>
<div class="modal fade modalTotal" id="myModalArchivo">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_archivo"><i class="fas fa-edit"></i> </h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3" id="visualizadorPdfCompras"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="modal-footer justify-content-between"><button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button></div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')