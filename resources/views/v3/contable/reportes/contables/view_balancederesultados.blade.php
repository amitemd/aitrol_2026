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
                                        <input type="hidden" id="c" name="c">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarBalanceResultados()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-info" onclick="buscarBalanceSinCierre()"><i class="fas fa-search"></i> <span class="botones">Con cierre</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-success oculto" onclick="excelBalanceResultado()"><i class="fa-regular fa-file-excel"></i> <span class="botones">Excel</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-danger oculto" onclick="pdfBalanceResultdo()"><i class="fa-regular fa-file-pdf"></i> <span class="botones">PDF</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-warning oculto" onclick="excelResultadoMensual()"><i class="fa-regular fa-file-excel"></i> <span class="botones">Mensual</span></a></td>
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
                    <div style="padding: 0px 0px 0px 0px;font-size: 12px;">
                        <center>
                            <h3><?= config('data.empresa')->razonsocial; ?></h3>
                            <h3>Balance de resultados</h3>
                            <h3><span id="desde_">Desde: 0000-00-00</span> <span id="hasta_">Hasta: 0000-00-00</span></h3>
                        </center>
                        <center>
                            <div style="float: center;width: 60%;">
                                <h3>Resumen</h3>
                                <table id="tableResumen" class="table table-bordered table-striped tablaDatos" style="width: 50%;">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Utilidad/Perdida del ejercicio</td>
                                            <td style="text-align: right;" id="utilidadBruta">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Participación a trabajadores</td>
                                            <td style="text-align: right;" id="participacionTrabajadores">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Utilidad contable</td>
                                            <td style="text-align: right;" id="utilidadContable">$ 0.00</td>
                                        </tr>
                                        <tr id="utilidadGravable"></tr>
                                        <tr>
                                            <td>Impuesto generado</td>
                                            <td style="text-align: right;" id="impuestoGenerado">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Utilidad/Perdida neta</td>
                                            <td style="text-align: right;" id="utilidadPerdida">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </div>
                    <style>
                        .th,
                        .td {
                            border: 1px solid #dddddd;
                            text-align: left;
                            padding: 8px;
                        }

                        .th {
                            background-color: #f2f2f2;
                        }
                    </style>
                    <div style="padding: 0px 0px 0px 0px;font-size: 12px;" id="bodyIngresos">
                        <center>
                            <div style="float: center;width: 60%;">
                                <h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#ingresoExample" role="button" aria-expanded="false" aria-controls="ingresoExample">Ingresos: $ 0.00</h3>
                                <div class="collapse" id="ingresoExample">
                                    <div class="card card-body">
                                        <table style="border-collapse:collapse; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="th">Cuenta</th>
                                                    <th class="th">Nombre</th>
                                                    <th class="th">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="td">Total Ingresos</td>
                                                    <td class="td">&nbsp;</td>
                                                    <td class="td" style="text-align: right;">$ 0.00</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div style="padding: 0px 0px 0px 0px;font-size: 12px;" id="bodyCostos">
                        <center>
                            <div style="float: center;width: 60%;">
                                <h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#costosExample" role="button" aria-expanded="false" aria-controls="costosExample">Costos: $ 0.00</h3>
                                <div class="collapse" id="costosExample">
                                    <div class="card card-body">
                                        <table>
                                            <tr>
                                                <th>Cuenta</th>
                                                <th>Nombre</th>
                                                <th>Saldo</th>
                                            </tr>
                                            <tr>
                                                <td>Total Costos</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;">$ 0.00</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div style="padding: 0px 0px 0px 0px;font-size: 12px;" id="bodyGastos">
                        <center>
                            <div style="float: center;width: 60%;">
                                <h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#gastosExample" role="button" aria-expanded="false" aria-controls="gastosExample">Gastos: $ 0.00</h3>
                                <div class="collapse" id="gastosExample">
                                    <div class="card card-body">
                                        <table>
                                            <tr>
                                                <th>Cuenta</th>
                                                <th>Nombre</th>
                                                <th>Saldo</th>
                                            </tr>
                                            <tr>
                                                <td>Total Gastos</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align: right;">$ 0.00</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>