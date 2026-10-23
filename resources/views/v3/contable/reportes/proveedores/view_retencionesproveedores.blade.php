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
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.desde')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="desde" name="desde" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.hasta')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="hasta" name="hasta" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <label>{{trans('v3/new_tabla.proveedor')}}</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="id_proveedor" name="id_proveedor">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label>{{trans('v3/new_tabla.#retencion')}}</label>
                                <div class="input-group mb-3">
                                    <input type="text" id="retencion" name="retencion" class="form-control text-right">
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-4">
                                <label>{{trans('v3/new_tabla.#factura')}}</label>
                                <div class="input-group mb-3">
                                    <input type="text" id="secuencia" name="secuencia" class="form-control text-right">
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarRetencionesProveedor()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
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
                    <table id="tablaRetencionesProveedor" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>{{trans('v3/new_tabla.fecha')}}</th>
                                <th>{{trans('v3/new_tabla.asiento')}}</th>
                                <th>{{trans('v3/new_tabla.#retencion')}}</th>
                                <th>{{trans('v3/new_tabla.#factura')}}</th>
                                <th>{{trans('v3/new_tabla.acreedor')}}</th>
                                <th>{{trans('v3/new_tabla.ruc')}}</th>
                                <th>{{trans('v3/new_tabla.detalle')}}</th>
                                <th>{{trans('v3/new_tabla.totalrfir')}}</th>
                                <th>{{trans('v3/new_tabla.%')}}</th>
                                <th>{{trans('v3/new_tabla.totalrfiva')}}  </th>
                                <th>{{trans('v3/new_tabla.%')}}</th>
                                <th>{{trans('v3/new_tabla.estado')}}</th>
                                <th>{{trans('v3/new_tabla.creadopor')}}</th>
                                <th>{{trans('v3/new_tabla.anuladopor')}}</th>
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
                                <th id="rfir">0.00</th>
                                <th>&nbsp;</th>
                                <th id="rfiva">0.00</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>