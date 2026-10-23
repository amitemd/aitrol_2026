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
                        <h3 class="card-title">Buscador</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 col-sm-2">
                                <div class="form-group">
                                    <label>Desde</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="desde" name="desde" class="form-control" value="<?=config('data.desde')!==null ? config('data.desde') : '';?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 col-sm-2">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="hasta" name="hasta" class="form-control"  value="<?=config('data.hasta')!==null ? config('data.hasta') : date('Y-m-d');?>"/>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="plan_desde" value="<?=config('data.cuentadesde')!==null ? config('data.cuentadesde') : '';?>">
                            <div class="col-4 col-sm-4">
                                <div class="form-group">
                                    <label>Cta. desde</label>
                                    <div class="input-group mb-3">
                                        <select class="form-control select2" name="cta_desde" id="cta_desde">
                                            <option value="">--Seleccionar--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-4">
                                <div class="form-group">
                                    <label>Cta hasta</label>
                                    <div class="input-group mb-3">
                                        <select class="form-control select2" name="cta_hasta" id="cta_hasta" disabled>
                                            <option value="">--Seleccionar--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-sm-3">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarCuentasLibroMayor()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
                                        </tr>
                                    </table>
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
                    <table id="tableLibroMayor" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Asiento</th>
                                <th>Año</th>
                                <th>Mes</th>
                                <th>Información</th>
                                <th>Cuenta</th>
                                <th>Nombre de la cuenta</th>
                                <th>Detalle</th>
                                <th>Identificación</th>
                                <th>Socio</th>
                                <th>Debe</th>
                                <th>Haber</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>Totales</th>
                                <th>&nbsp;</th>
                                <td align="right" id="totalDebe">0.00</td>
                                <td align="right" id="totalHaber">0.00</td>
                                <th>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.contabilidad.view_visualizar_asiento')