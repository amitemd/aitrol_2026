<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_menu.inicio') }}</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php
            if (config('data.permisos')->id_submenu_permiso != '') {
                if (config('data.accesos') != null && count(config('data.accesos')) > 0) {
                    foreach (config('data.accesos') as $row) {
                        $colorIcon = '';
                        if ($row->cantidad > 30) {
                            $color = $row->color_menu;
                            if ($color == '') {
                                $color = '#17a2b8';
                            }
                            if ($row->icon_menu != '') {
                                $colorIcon = explode('">', $row->icon_menu);
                            }
                            echo '<div class="col-lg-2 col-6"><div class="small-box" style="background:' . $color . '"><div class="inner" style="color:white;"><b>' . ucwords(trans('v3/new_menu.' . $row->controlador_auditoria)) . '</b><p></p></div><div class="icon">' . $row->icon_menu . '</div><a href="v3/' . strtolower($row->controlador_auditoria) . '/' . strtolower($row->submodulo_auditoria) . '" class="small-box-footer">' . substr(trans('v3/new_submodulo.' . $row->submodulo_auditoria), 0, 17) . ' <i class="fas fa-arrow-circle-right"></i></a></div></div>';
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
    <center>
        <div class="card text-center w-75 card-outline card-info">
            <div class="card-header"><b><?= trans('v3/new_varios.bienvenido') ?></b></div>
            <div class="card-body">
                <?php
                $dias = [trans('v3/new_dias.domingo'), trans('v3/new_dias.lunes'), trans('v3/new_dias.martes'), trans('v3/new_dias.miercoles'), trans('v3/new_dias.jueves'), trans('v3/new_dias.viernes'), trans('v3/new_dias.sabado')];
                $meses = [trans('v3/new_meses.enero'), trans('v3/new_meses.febrero'), trans('v3/new_meses.marzo'), trans('v3/new_meses.abril'), trans('v3/new_meses.mayo'), trans('v3/new_meses.junio'), trans('v3/new_meses.julio'), trans('v3/new_meses.agosto'), trans('v3/new_meses.septiembre'), trans('v3/new_meses.octubre'), trans('v3/new_meses.noviembre'), trans('v3/new_meses.diciembre')];
                $today = getdate();
                $hora = $today['hours'];
                $saludo = '';
                if ($hora < 6) {
                    $saludo = trans('v3/new_varios.madrugar');
                } elseif ($hora < 12) {
                    $saludo = trans('v3/new_varios.dias');
                } elseif ($hora <= 18) {
                    $saludo = trans('v3/new_varios.tardes');
                } else {
                    $saludo = trans('v3/new_varios.noches');
                }
                ?>
                <p class="card-text"><b><?= $saludo ?></b><?= ucwords(strtolower(config('data.usuario')->nombre1 . ' ' . config('data.usuario')->apellido1)) ?></p>
            </div>
            <div class="card-footer text-muted"><b><?= trans('v3/new_varios.ultimoacceso') ?></b><?= $dias[date('w', strtotime(config('data.ultimaConexion')->created_at))] . ' ' . date('d', strtotime(config('data.ultimaConexion')->created_at)) . ' ' . trans('v3/new_varios.de') . ' ' . $meses[date('n', strtotime(config('data.ultimaConexion')->created_at)) - 1] . ', ' . date('Y', strtotime(config('data.ultimaConexion')->created_at)) . ' ' . trans('v3/new_varios.alas') . ' ' . date('H:i:s', strtotime(config('data.ultimaConexion')->created_at)) ?></div>
        </div>
    </center>
</section>
<?php
if (config('data.permisos')->id_submenu_permiso != '') {
?>
    <section>
        <input type="hidden" id="chart" value="{{ config('data.chart') }}">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{trans('v3/new_titulos.opciones')}}</h3>
                    <div class="card-tools"><a href="javascript:;" onclick="dibujarCanvas('bar')" class="btn btn-info btn-xs"><i class="fa-solid fa-chart-simple fa-lg"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="dibujarCanvas('line')" class="btn btn-success btn-xs"><i class="fa-solid fa-chart-line fa-lg"></i></a></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card <?= config('data.chart') == '' ? 'collapsed-card' : '' ?>">
                <div class="card-header">
                    <h3 class="card-title">{{trans('v3/new_titulos.ventaspublicasprivadasrelacionadas')}}</h3>
                    <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-<?= config('data.chart') == '' ? 'plus' : 'minus' ?>"></i></button><button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button></div>
                </div>
                <div class="card-body">
                    <div class="chart" id="chartVentas"><canvas id="lineVentas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card <?= config('data.chart') == '' ? 'collapsed-card' : '' ?>">
                <div class="card-header">
                    <h3 class="card-title">{{trans('v3/new_titulos.comprasygastos')}}</h3>
                    <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-<?= config('data.chart') == '' ? 'plus' : 'minus' ?>"></i></button><button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button></div>
                </div>
                <div class="card-body">
                    <div class="chart" id="chartCompras"><canvas id="lineCompras" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card <?= config('data.chart') == '' ? 'collapsed-card' : '' ?>">
                <div class="card-header">
                    <h3 class="card-title">{{trans('v3/new_titulos.utilidades')}}</h3>
                    <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-<?= config('data.chart') == '' ? 'plus' : 'minus' ?>"></i></button><button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button></div>
                </div>
                <div class="card-body">
                    <div class="chart" id="chartUtilidades"><canvas id="lineVentasUtilidades" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas></div>
                </div>
            </div>
        </div>
    </section>
<?php
}
?>