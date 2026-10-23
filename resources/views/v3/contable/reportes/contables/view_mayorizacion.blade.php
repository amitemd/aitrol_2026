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
                                            <td><a href="javascript:;" class="btn btn-primary" onclick="buscarMayores()"><i class="fas fa-search"></i> <span class="botones">Buscar</span></a></td>
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
                    <div class="row" id="contenidoMayor">
                        <?php
                        $html = '';
                        $cuentasMayo = [];
                        foreach (config('data.cuentas') as $row) {
                            $cuentasMayo[$row->nombre_cuenta][] = $row;
                        }
                        foreach ($cuentasMayo as $cuenta => $detalles) {
                            $html .= '<div class="col-md-6">
                            <div class="card card-outline card-info collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title"><label>' . $cuenta . ' | ' . $detalles[0]->plan . '</label></h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                    <div class="col-12 col-sm-12">
                                       <table class="table table-bordered table-striped" width="100%">
                                           <tr>
                                               <th>Fecha</th>
                                               <th>Asiento</th>
                                               <th>Descripción</th>
                                               <th>Debe</th>
                                               <th>Haber</th>
                                           </tr>';
                            $sumDebe = 0;
                            $sumHaber = 0;
                            foreach ($detalles as $detalle) {
                                $sumDebe += $detalle->debe;
                                $sumHaber += $detalle->haber;
                                $html .= '<tr>
                                           <td>' . $detalle->fecha_asiento . '</td>
                                           <td align="center"><a href="javascript:;" onclick="visualizarAsiento(\'' . $detalle->id . '\');">' . $detalle->id . '</a></td>
                                           <td>' . $detalle->nombre_cuenta . '</td>
                                           <td align="right">' . $detalle->debe . '</td>
                                           <td align="right">' . $detalle->haber . '</td>
                                       </tr>';
                            }
                            $html .= '<tfoot><tr><td colspan="2"><b>Totales</b><td><td align="right"><b>' . number_format($sumDebe, 4) . '</b></td><td align="right"><b>' . number_format($sumHaber, 4) . '</b></td></tr></tfoot></table></div></div></div></div></div>';
                        }
                        echo $html;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.contabilidad.view_visualizar_asiento')