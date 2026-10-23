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
                                <a href="javascript:;" class="btn btn-danger btn-sm" onclick="goBack();"><i class="fas fa-arrow-circle-left"></i> <span class="botones">{{trans('v3/new_tabla.regresar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="ibox-content">
                            <form id="frm_">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveDepreciacion" />
                                <input type="hidden" id="d" name="d" />
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.buscador')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_tabla.Tipo')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="tipo" name="tipo">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</optio>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.periodo')}}</label>
                                                        <input type="month" class="form-control" id="periodo" name="periodo" value="<?= date('Y-m'); ?>" />
                                                    </div>
                                                    <div class="col-md-2" style="margin-top: auto;">
                                                        <a href="javascript:;" class="btn btn-primary" onclick="buscarDatosDep()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label class="font-normal">{{trans('v3/new_tabla.observacion')}}</label>
                                                        <input type="text" class="form-control" id="observacion" name="observacion" value="" />
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_tabla.sucursal')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="sucursal_empresa" name="sucursal">
                                                            <option value="">-{{trans('v3/new_tabla.seleccione')}}</option>
                                                            <?php
                                                            if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                                                foreach (config('data.sucursales') as $value) {
                                                                    echo '<option value="' . $value->id . '">' . $value->codigo_sucursal . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3" style="margin-top: auto;">
                                                        <button type="button" class="btn btn-primary" id="btnGuardarDepreciacion"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <table id="tableListaDepreciaciones" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>{{trans('v3/new_tabla.seleccione')}}</th>
                                                <th>{{trans('v3/new_tabla.fechacompra')}}</th>
                                                <th>{{trans('v3/new_tabla.codigo')}}</th>
                                                <th>{{trans('v3/new_tabla.nombre')}}</th>
                                                <th>{{trans('v3/new_tabla.tipo')}}</th>
                                                <th>{{trans('v3/new_tabla.facturaSaldo')}}</th>
                                                <th>{{trans('v3/new_tabla.diasCompra')}}</th>
                                                <th>{{trans('v3/new_tabla.vidautil')}}</th>
                                                <th>{{trans('v3/new_tabla.tasaPorcentaje')}}</th>
                                                <th>{{trans('v3/new_tabla.costo')}}</th>
                                                <th>{{trans('v3/new_tabla.depreciacionAcumulada')}}</th>
                                                <th>{{trans('v3/new_tabla.saldoActual')}}</th>
                                                <th>{{trans('v3/new_tabla.valorDepreciar')}}</th>
                                                <th>{{trans('v3/new_tabla.saldodespues')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>