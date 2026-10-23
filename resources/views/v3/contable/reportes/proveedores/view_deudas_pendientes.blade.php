
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
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Desde</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="desde" name="desde" class="form-control" value="{{config('data.d')==''?date('Y-m-d'):''}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="hasta" name="hasta" class="form-control" value="{{config('data.h')==''?date('Y-m-d'):''}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label>Proveedor</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="id_proveedor" name="id_proveedor">
                                        <option value="">--Seleccione--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarDeudaPendientes()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a> &nbsp;&nbsp;&nbsp;
                                    <div style="display: none;" id="oculto">
                                        <a href="javascript:;" class="btn btn-success ocultar" onclick="excelDeudasPendientes()"><i class="fas fa-file-excel"></i> Excel</a>
                                    </div>
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
                    <table id="tablaDeudasPendientes" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Días</th>
                                <th>Vencimiento</th>
                                <th>Dias Vencidos</th>
                                <th>Tipo</th>
                                <th>Número</th>
                                <th>Detalle</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>EG</th>
                                <th>RT</th>
                                <th>DB/TRANS</th>
                                <th>CRUCE</th>
                                <th>N/C</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="reporteAjax">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>