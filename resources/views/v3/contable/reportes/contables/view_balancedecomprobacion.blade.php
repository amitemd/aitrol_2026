<style>
    .custom-header {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
    }
</style>
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
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Desde</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="desde" name="desde" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="hasta" name="hasta" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarBalanceComprobacion()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarBalanceAnulados()"><i class="fas fa-close"></i> <span class="botones">Anulados</span></a></td>
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
                    <table id="tableBalanceComprobacion" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr class="custom-header">
                                <th colspan="6">Balance de Comprobación</th>
                            </tr>
                            <tr class="custom-header">
                                <th colspan="6">{{config('data.empresa')->razonsocial}}</th>
                            </tr>
                            <tr class="custom-header">
                                <th colspan="6" id="fecha_balance">Desde: -- hasta: --</th>
                            </tr>
                            <tr>
                                <th>Plan</th>
                                <th>Descripción</th>
                                <th>Debe</th>
                                <th>Haber</th>
                                <th>Deudor</th>
                                <th>Acreedor</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoBalanceComprobacion">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Totales</th>
                                <th id="totalDebe" style="text-align: right;">Debe</th>
                                <th id="totalHaber" style="text-align: right;">Haber</th>
                                <th id="totalDeudor" style="text-align: right;">Deudor</th>
                                <th id="totalAcreedor" style="text-align: right;">Acreedor</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>