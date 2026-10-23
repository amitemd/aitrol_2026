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

    .centered-img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 250px;
       
        height: 60px;
        
    }

    .hidden {
        display: none;
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
                                        <input type="date" id="desde" name="desde" class="form-control" value="{{ $desde ?? '' }}" />
                                        <small class="form-text text-muted">Dejar vacío para usar la fecha inicial del sistema.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-2">
                                <div class="form-group">
                                    <label>Hasta</label>
                                    <div class="input-group mb-3">
                                        <input type="date" id="hasta" name="hasta" class="form-control" value="{{ $hasta ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarBalanceFinanciero()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
                                            <!--<td><a href="javascript:;" class="btn btn-success" onclick="excelBalanceFinanciero()"><i class="fa-regular fa-file-excel"></i> <span class="botones">Excel</span></a></td>-->
                                            <td><a href="javascript:;" class="btn btn-success" onclick="exportarExcelFincnaciero('balanceFinanciero','Balance Financiero')"><i class="fa-regular fa-file-excel"></i> <span class="botones">Excel</span></a></td>
                                            <td><a href="javascript:;" class="btn btn-danger" onclick="pdfBalanceFinanciero()"><i class="fa-regular fa-file-pdf"></i> <span class="botones">PDF</span></a></td>
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
                    <div class="content">
                        <div style="padding: 0px 0px 0px 0px;font-size: 12px;">
                            <div class="box-body card hidden" style="text-align: center;">
                                <?php
                                $base64Image = '';
                                if (config('data.empresa') !== null) {
                                    $logoPath = asset('logo/' . config('data.empresa')->logo);
                                }
                                ?>
                                @if ($logoPath)
                                <img src="{{ $logoPath }}" class="centered-img" id="logoImage">
                                @endif
                            </div>
                            <table id="balanceFinanciero" style="width: 100%;">
                                <tbody>

                                    <tr>
                                        <td>
                                            <center>
                                                <h3><?= config('data.empresa')->razonsocial; ?></h3>
                                                <h3>Balance financiero</h3>
                                                <h3><span id="desde_">Desde: {{ $desde != '' ? $desde : 'Inicio del sistema' }}</span> <span id="hasta_">Hasta: {{ $hasta ?? '0000-00-00' }}</span></h3>
                                            </center>
                                            <hr>
                                            <center>
                                                <div style="float: center;width: 60%;">
                                                    <h3>Resumen</h3>
                                                    <table id="tableResumen" class="table table-bordered table-striped tablaDatos" style="width: 50%;">
                                                        <thead>
                                                            <tr class="tr">
                                                                <th class="td-border">Descripcion</th>
                                                                <th>Valor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="tr">
                                                                <td>Activos</td>
                                                                <td style="text-align: right;" id="activos">$ 0.00</td>
                                                            </tr>
                                                            <tr class="tr">
                                                                <td>Pasivos</td>
                                                                <td style="text-align: right;" id="pasivos">$ 0.00</td>
                                                            </tr>
                                                            <tr class="tr">
                                                                <td>Patrimonio</td>
                                                                <td style="text-align: right;" id="patrimonio">$ 0.00</td>
                                                            </tr>
                                                            <tr class="tr">
                                                                <td>Diferencia</td>
                                                                <td style="text-align: right;" id="diferencia">$ 0.00</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </center>
                                            <hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 100%;">
                                            <div class="activos" style="float: left;width: 50%;">
                                                <h2>Activos</h2>
                                                <table style="border-collapse:collapse; width: 100%;">
                                                    <tr class="tr">
                                                        <th class="th">Cuenta</th>
                                                        <th class="th">Nombre</th>
                                                        <th class="th">Saldo</th>
                                                        <th class="th">%</th>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td>Total Pasivos</td>
                                                        <td>&nbsp;</td>
                                                        <td style="text-align: right;">$ 0.00</td>
                                                        <td style="text-align: right;">0.00 %</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="pasivos" style="float: right;width: 50%;">
                                                <h2>Pasivos</h2>
                                                <table style="border-collapse:collapse; width: 100%;">
                                                    <tr class="tr">
                                                        <th class="th">Cuenta</th>
                                                        <th class="th">Nombre</th>
                                                        <th class="th">Saldo</th>
                                                        <th class="th">%</th>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td>Total Pasivos</td>
                                                        <td>&nbsp;</td>
                                                        <td style="text-align: right;">$ 0.00</td>
                                                        <td style="text-align: right;">0.00 %</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div>
                                                <div class="patrimonio" style="float: right;width: 50%;">
                                                    <h2>Patrimonio</h2>
                                                    <table style="border-collapse:collapse; width: 100%;">
                                                        <tr class="tr">
                                                            <th class="th">Cuenta</th>
                                                            <th class="th">Nombre</th>
                                                            <th class="th">Saldo</th>
                                                            <th class="th">%</th>
                                                        </tr>
                                                        <tr class="tr">
                                                            <td>Total Patrimonio</td>
                                                            <td>&nbsp;</td>
                                                            <td style="text-align: right;">$ 0.00</td>
                                                            <td style="text-align: right;">0.00 %</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 100%;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if (config('data.empresa')->id_representante !== null) {
                                                            if (config('data.empresa')->persona_nat_jur == '2') {
                                                                if (config('data.empresa')->tipo_representante !== null) {
                                                                    echo config('data.empresa')->pref_repre->titulo_prefijo . ' ' . config('data.empresa')->usuario_representante->nombre1 . ' ' . config('data.empresa')->usuario_representante->nombre2 . ' ' . config('data.empresa')->usuario_representante->apellido1 . ' ' . config('data.empresa')->usuario_representante->apellido2 . '<br>C.I. ' . config('data.empresa')->id_representante . '<br>';
                                                                } else {
                                                                    echo config('data.empresa')->pref_repre->titulo_prefijo . ' ' . config('data.empresa')->usuario_representante->nombre1 . ' ' . config('data.empresa')->usuario_representante->nombre2 . ' ' . config('data.empresa')->usuario_representante->apellido1 . ' ' . config('data.empresa')->usuario_representante->apellido2 . '<br> EN REPRESENTACION DE' . config('data.empresa')->empresa_representante;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td></td>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        if (config('data.empresa')->id_contador !== null) {
                                                            echo config('data.empresa')->pref_cont->titulo_prefijo . ' ' . config('data.empresa')->usuario_contador->nombre1 . ' ' . config('data.empresa')->usuario_contador->nombre2 . ' ' . config('data.empresa')->usuario_contador->apellido1 . ' ' . config('data.empresa')->usuario_contador->apellido2 . '<br>C.I. ' . config('data.empresa')->id_contador . '<br> Registro' . '' . config('data.empresa')->num_registro_contador;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>