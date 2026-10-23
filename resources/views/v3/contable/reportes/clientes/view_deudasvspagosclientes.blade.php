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
                                        <input type="date" id="desde" name="desde" class="form-control" value="{{config('data.d')==''?date('Y-m-d'):''}}" />
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
                                        <input type="date" id="hasta" name="hasta" class="form-control" value="{{config('data.h')==''?date('Y-m-d'):''}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-4">
                                <label>{{trans('v3/new_tabla.cliente')}}</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="id_cliente" name="id_cliente">
                                        <option value="">--Seleccione--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 col-sm-2">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarDeudavsPagosClientes()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
                                </div>
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
                    <table id="tablaDeudavsPagosClientes" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>{{trans('v3/new_tabla.fecha')}}</th>
                                <th> {{trans('v3/new_tabla.tipo')}} <br> {{trans('v3/new_tabla.comprobante')}}</th>
                                <th>{{trans('v3/new_tabla.detalle')}}</th>
                                <th>{{trans('v3/new_tabla.asiento')}}</th>
                                <th>{{trans('v3/new_tabla.cliente')}}</th>
                                <th>{{trans('v3/new_tabla.cuenta')}} </th>
                                <th>{{trans('v3/new_tabla.valor')}}</th>
                                <th> {{trans('v3/new_tabla.deudas')}}</th>
                                <th>{{trans('v3/new_tabla.abonos')}} </th>
                                <th>{{trans('v3/new_tabla.saldo')}}</th>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th id="total_deuda">0.00</th>
                                <th id="total_abonos">0.00</th>
                                <th id="sumsaldo">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.contabilidad.view_visualizar_asiento')