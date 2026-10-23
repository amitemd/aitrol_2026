<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_menu.inicio') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</li>
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
                                    <label>Año</label>
                                    <div class="input-group mb-3">
                                        <select id="anio" name="anio" class="form-control">
                                            <?php
                                            $anio = date('Y');
                                            $anio_ = $anio - 5;
                                            for ($i = $anio; $i >= $anio_; $i--) {
                                                echo '<option value="' . $i . '">' . $i . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-6">
                                    <table class="text-center">
                                        <tr>
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarAsientos()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
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
    <?php
    $anio1 = date('Y', strtotime(config('data.desde')));
    $anio2 = $anio1 - 1;
    $arrayanio1 = [];
    $arrayanio2 = [];
    $names_2024 = [];
    $names_2023 = [];
    foreach (config('data.ingresosOperativos') as $row) {
        if ($row->anio == $anio1) {
            $names_2024[$row->nombre] = [
                'tipo' => $row->tipo,
                'ene' => $row->ene,
                'feb' => $row->feb,
                'mar' => $row->mar,
                'abr' => $row->abr,
                'may' => $row->may,
                'jun' => $row->jun,
                'jul' => $row->jul,
                'ago' => $row->ago,
                'sep' => $row->sep,
                'oct' => $row->oct,
                'nov' => $row->nov,
                'dic' => $row->dic,
                'total' => $row->total,
            ];
        } else {
            $names_2023[$row->nombre] = [
                'tipo' => $row->tipo,
                'ene' => $row->ene,
                'feb' => $row->feb,
                'mar' => $row->mar,
                'abr' => $row->abr,
                'may' => $row->may,
                'jun' => $row->jun,
                'jul' => $row->jul,
                'ago' => $row->ago,
                'sep' => $row->sep,
                'oct' => $row->oct,
                'nov' => $row->nov,
                'dic' => $row->dic,
                'total' => $row->total,
            ];
        }
    }
    foreach ($names_2024 as $name => $data) {
        $arrayanio1[] = [
            'anio' => $anio1,
            'tipo' => $data['tipo'],
            'nombre' => $name,
            'ene' => $data['ene'],
            'feb' => $data['feb'],
            'mar' => $data['mar'],
            'abr' => $data['abr'],
            'may' => $data['may'],
            'jun' => $data['jun'],
            'jul' => $data['jul'],
            'ago' => $data['ago'],
            'sep' => $data['sep'],
            'oct' => $data['oct'],
            'nov' => $data['nov'],
            'dic' => $data['dic'],
            'total' => $data['total'],
        ];
        if (!array_key_exists($name, $names_2023)) {
            $arrayanio2[] = [
                'anio' => $anio2,
                'tipo' => '',
                'nombre' => $name,
                'ene' => "0.00",
                'feb' => "0.00",
                'mar' => "0.00",
                'abr' => "0.00",
                'may' => "0.00",
                'jun' => "0.00",
                'jul' => "0.00",
                'ago' => "0.00",
                'sep' => "0.00",
                'oct' => "0.00",
                'nov' => "0.00",
                'dic' => "0.00",
                'total' => "0.00",
            ];
        }
    }
    foreach ($names_2023 as $name => $data) {
        $arrayanio2[] = [
            'anio' => $anio2,
            'tipo' => $data['tipo'],
            'nombre' => $name,
            'ene' => $data['ene'],
            'feb' => $data['feb'],
            'mar' => $data['mar'],
            'abr' => $data['abr'],
            'may' => $data['may'],
            'jun' => $data['jun'],
            'jul' => $data['jul'],
            'ago' => $data['ago'],
            'sep' => $data['sep'],
            'oct' => $data['oct'],
            'nov' => $data['nov'],
            'dic' => $data['dic'],
            'total' => $data['total'],
        ];
        if (!array_key_exists($name, $names_2024)) {
            $arrayanio1[] = [
                'anio' => $anio1,
                'tipo' => '',
                'nombre' => $name,
                'ene' =>  "0.00",
                'feb' =>  "0.00",
                'mar' =>  "0.00",
                'abr' =>  "0.00",
                'may' =>  "0.00",
                'jun' =>  "0.00",
                'jul' =>  "0.00",
                'ago' =>  "0.00",
                'sep' =>  "0.00",
                'oct' =>  "0.00",
                'nov' =>  "0.00",
                'dic' =>  "0.00",
                'total' =>  "0.00",
            ];
        }
    }
    foreach ($arrayanio1 as $key => $val1) {
        foreach ($arrayanio2 as $i => $val2) {
            if ($val1['nombre'] == $val2['nombre']) {
                $temp = $arrayanio2[$key];
                $arrayanio2[$key] = $val2;
                $arrayanio2[$i] = $temp;
                break;
            }
        }
    }
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ config('data.titulo') }}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="font-size: 10px;">
                            <tbody>
                                <tr>
                                    <th>Descripción</th>
                                    <th style="text-align: center;">Flujo de caja {{$anio2}}</th>
                                    <th style="text-align: center;">Flujo de caja {{$anio1}}</th>
                                </tr>
                                <tr>
                                    <td>Ingresos operativos</td>
                                    <td>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Nombre</td>
                                                <td>Ene</td>
                                                <td>Feb</td>
                                                <td>Mar</td>
                                                <td>Abr</td>
                                                <td>May</td>
                                                <td>Jun</td>
                                                <td>Jul</td>
                                                <td>Ago</td>
                                                <td>Sep</td>
                                                <td>Oct</td>
                                                <td>Nov</td>
                                                <td>Dic</td>
                                                <td>Total</td>
                                            </tr>
                                            @foreach ($arrayanio2 as $value)
                                            <tr>
                                                <td>
                                                    {{ $value['nombre'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['ene'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['feb'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['mar'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['abr'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['may'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['jun'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['jul'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['ago'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['sep'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['oct'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['nov'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['dic'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['total'] }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <td>Nombre</td>
                                                <td>Ene</td>
                                                <td>Feb</td>
                                                <td>Mar</td>
                                                <td>Abr</td>
                                                <td>May</td>
                                                <td>Jun</td>
                                                <td>Jul</td>
                                                <td>Ago</td>
                                                <td>Sep</td>
                                                <td>Oct</td>
                                                <td>Nov</td>
                                                <td>Dic</td>
                                                <td>Total</td>
                                            </tr>
                                            @foreach ($arrayanio1 as $value)
                                            <tr>
                                                <td>
                                                    {{ $value['nombre'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['ene'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['feb'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['mar'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['abr'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['may'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['jun'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['jul'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['ago'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['sep'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['oct'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['nov'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['dic'] }}
                                                </td>
                                                <td align="right">
                                                    {{ $value['total'] }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>