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
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> Índices financieros
                    <div>
                        <div style="position: absolute; right: 5px;top:7px">
                            <a href="{{ url('v3/') }}/{{ config('data.controlador') }}/crearGuia" class="btn btn-primary btn-sm"><i class="fa-regular fa-file-excel"></i> <span class="botones">Generar Excel</span></a>
                        </div>
                    </div>
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{ csrf_field() }}
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Año</label>
                            <select class="form-control select2" id="anio" name="anio">
                                <option value="">--Seleccionar--</option>
                                <?php
                                $inicio = date('Y') - 10;
                                for ($i = date('Y'); $i > $inicio; $i--) {
                                    $select = '';
                                    if (isset(config('data.input')['anio']) && config('data.input')['anio'] == $i) {
                                        $select = 'selected';
                                    }
                                    echo '<option value="' . $i . '" ' . $select . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label>&nbsp;</label>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="verIndices('FI')"><i class="fas fa-eye"></i> Visualizar</button>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="estadofinanciero-tab" onclick="verIndices('FI');" data-toggle="pill" href="#estadofinanciero" role="tab" aria-controls="estadofinanciero" aria-selected="true">Estado de situación general</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="estadoresultado-tab" onclick="verIndices('RE');" data-toggle="pill" href="#estadoresultado" role="tab" aria-controls="estadoresultado" aria-selected="false">Estado de resultados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ratios-tab" data-toggle="pill" onclick="verIndices('RA');" href="#ratios" role="tab" aria-controls="ratios" aria-selected="false">Ratios</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade show active" id="estadofinanciero" role="tabpanel" aria-labelledby="estadofinanciero-tab" style="height: 600px;overflow: auto;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 50%;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align:middle;">&nbsp;</th>
                                        <th style="vertical-align:middle;">2023</th>
                                        <th style="vertical-align:middle;">2024</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:middle;">Total activo</td>
                                        <td style="vertical-align:middle;">0</td>
                                        <td style="vertical-align:middle;">0</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 20%;">
                                <thead>
                                    <tr>
                                        <td style="text-align: center;">Analisis Horizontal</td>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align:middle;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>Valor ABS</td>
                                                    <td>Valor REL</td>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 20%;">
                                <thead>
                                    <tr>
                                        <td style="text-align: center;">Analisis Vertical</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:middle;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>Valor ABS</td>
                                                    <td>Valor REL</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="estadoresultado" role="tabpanel" aria-labelledby="estadoresultado-tab" style="height: 600px;overflow: auto;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 50%;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align:middle;">&nbsp;</th>
                                        <th style="vertical-align:middle;">2023</th>
                                        <th style="vertical-align:middle;">2024</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:middle;">Total activo</td>
                                        <td style="vertical-align:middle;">0</td>
                                        <td style="vertical-align:middle;">0</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 20%;">
                                <thead>
                                    <tr>
                                        <td style="text-align: center;">Analisis Horizontal</td>
                                    </tr>
                                    <tr>
                                        <th style="vertical-align:middle;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>Valor ABS</td>
                                                    <td>Valor REL</td>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped" style="float:left;font-size: 10px;width: 20%;">
                                <thead>
                                    <tr>
                                        <td style="text-align: center;">Analisis Vertical</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:middle;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>Valor ABS</td>
                                                    <td>Valor REL</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ratios" role="tabpanel" aria-labelledby="ratios-tab"></div>
                </div>
            </div>
        </div>
    </div>
</section>