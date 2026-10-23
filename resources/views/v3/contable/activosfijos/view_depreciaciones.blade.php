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
                                <a href="javascript:;" class="btn btn-danger btn-sm" onclick="HistorialDepreciacioes();"><i class="fas fa-clock"></i> <span class="botones">{{trans('v3/new_tabla.Historial')}}</span></a>
                            </div>
                            <div style="position: absolute; right: 100px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="CrearDep();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tableDepreciaciones" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th> {{trans('v3/new_tabla.idactivo')}}</th>
                                    <th> {{trans('v3/new_tabla.codigo')}}</th>
                                    <th> {{trans('v3/new_tabla.nombre')}} </th>
                                    <th> {{trans('v3/new_tabla.tasa')}}</th>
                                    <th> {{trans('v3/new_tabla.vidautil')}}</th>
                                    <th> {{trans('v3/new_tabla.costo')}}</th>
                                    <th> {{trans('v3/new_tabla.depreciacionAcumulada')}} </th>
                                    <th> {{trans('v3/new_tabla.saldoinicial')}} </th>
                                    <th> {{trans('v3/new_tabla.saldoActual')}} </th>
                                    <th> ... </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> {{trans('v3/new_tabla.idactivo')}}</th>
                                    <th> {{trans('v3/new_tabla.codigo')}}</th>
                                    <th> {{trans('v3/new_tabla.nombre')}} </th>
                                    <th> {{trans('v3/new_tabla.tasa')}}</th>
                                    <th> {{trans('v3/new_tabla.costo')}}</th>
                                    <th> {{trans('v3/new_tabla.vidautil')}}</th>
                                    <th> {{trans('v3/new_tabla.depreciacionAcumulada')}} </th>
                                    <th> {{trans('v3/new_tabla.saldoinicial')}} </th>
                                    <th> {{trans('v3/new_tabla.saldoActual')}} </th>
                                    <th> ... </th>
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
                <h4 class="modal-title" id="titulo_modal_depreciacion"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
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

                    <div class="card card-default">
                        <div class="card-body">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                        <div class="row">
                                            <table class="table table-bordered table-striped tablaDatos" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th> {{trans('v3/new_tabla.id')}}</th>
                                                        <th> {{trans('v3/new_tabla.anio')}}</th>
                                                        <th> {{trans('v3/new_tabla.mes')}}</th>
                                                        <th> {{trans('v3/new_tabla.fecha')}}</th>
                                                        <th> {{trans('v3/new_tabla.asiento')}}</th>
                                                        <th> {{trans('v3/new_tabla.observacion')}}</th>
                                                        <th> {{trans('v3/new_tabla.totalDepreciado')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tablaDetalles">
                                                </tbody>
                                            </table>
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
            </div>
        </div>
    </div>
</div>

@include('v3.contable.contabilidad.view_visualizar_asiento')
