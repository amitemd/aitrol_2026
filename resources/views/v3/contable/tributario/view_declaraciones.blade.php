<style>
    .bg-blue {
        background-color: #DDEBF7;
    }

    .vertical-center {
        vertical-align: middle;
        text-align: center;
    }

    .oculto {
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
        <form id="frm_declaraciones" method="post">
            {{ csrf_field() }}
            <input type="hidden" id="action1" value="{{url('v3/')}}/{{config('data.controlador')}}/excelTributacion" />
            <input type="hidden" id="action2" value="{{url('v3/')}}/{{config('data.controlador')}}/guardarDeclaracion" />
            <input type="hidden" id="aux" name="aux" />
            <input type="hidden" name="tipoExportar" id="tipoExportar" value="1" />
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Declaraciones/Anexos
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <button type="button" onclick="generarExcel()" class="btn btn-primary btn-sm"><i class="fa-regular fa-file-excel"></i> <span class="botones">Generar Excel</span></a>
                            </div>
                        </div>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="v3/{{config('data.controlador')}}/{{config('data.submodulo')}}">
                        <div class="row">
                            {{ csrf_field() }}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Año</label>
                                    <select class="form-control" id="anio" name="anio" value="{{config('data.anioM') !== null ?config('data.anioM'):''}}">
                                        <option value="">--Seleccionar--</option>
                                        <?php

                                        use Sis_medico\v3\contable\Retenciones;
                                        use Sis_medico\v3\contable\Retenciones_Clientes;

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
                                <div class="form-group">
                                    <label>Mes</label>
                                    <select class="form-control" id="mes" name="mes" value="{{config('data.mesM') !== null?config('data.mesM'):''}}">
                                        <option value="">--Seleccionar--</option>
                                        <?php
                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                        for ($j = 0; $j < sizeof($meses); $j++) {
                                            $numMes = $j + 1;
                                            $select = '';
                                            if (isset(config('data.input')['mes']) && config('data.input')['mes'] == $numMes) {
                                                $select = 'selected';
                                            }
                                            echo '<option value=' . $numMes . ' ' . $select . '>' . $meses[$j] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 oculto">
                                <div class="form-group">
                                    <label>Semestral</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="reservation" name="reservation" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <input type="hidden" id="verificarS" value="{{config('data.checkSemestral')}}">
                                    <input type="hidden" id="fechaR" value="{{config('data.reservation')}}">
                                    <button id="visualizarBtn" type="submit" class="btn btn-primary"><i class="fas fa-eye"></i> Visualizar</button>
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="checkSemestral" name="checkSemestral" value="semestral" onclick="semestral(this);">
                                        <label for="checkSemestral">Semestral</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventas-tab" data-toggle="pill" href="#ventas" role="tab" aria-controls="ventas" aria-selected="true">Ventas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="compras-tab" data-toggle="pill" href="#compras" role="tab" aria-controls="compras" aria-selected="false">Compras</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="formulario_104-tab" data-toggle="pill" href="#formulario_104" role="tab" aria-controls="formulario_104" aria-selected="false">Formulario 104</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="formulario_103-tab" data-toggle="pill" href="#formulario_103" role="tab" aria-controls="formulario_103" aria-selected="false">Formulario 103</a>
                        </li>
                        <!--<li class="nav-item">
                            <a class="nav-link" id="resumen-tab" data-toggle="pill" href="#resumen" role="tab" aria-controls="resumen" aria-selected="false">Resumen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="diario-tab" data-toggle="pill" href="#formulario_103" role="tab" aria-controls="formulario_103" aria-selected="false">Diario</a>
                        </li>-->
                        <li class="nav-item">
                            <a class="nav-link" id="ats_generado-tab" data-toggle="pill" href="#ats_generado" role="tab" aria-controls="ats_generado" aria-selected="false">Ats generados</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="custom-content-below-tabContent">
                        <div class="tab-pane fade show active" id="ventas" role="tabpanel" aria-labelledby="ventas-tab" style="height: 600px;overflow: auto;">
                            <table id="ventas" class="table table-bordered table-striped" style="font-size: 11px;width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align: middle;">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkTodosVentas" onclick="seleccionarTodosVentas(this);"> <label for="checkTodosVentas"></label>
                                                <label for="checkTodosVentas"></label>
                                            </div>
                                        </th>
                                        <th style="vertical-align:middle;">No</th>
                                        <th style="vertical-align:middle;">Asiento</th>
                                        <th style="vertical-align:middle;">Fecha</th>
                                        <th style="vertical-align:middle;">Cliente</th>
                                        <th style="vertical-align:middle;">Ruc</th>
                                        <th style="vertical-align:middle;">Autorización</th>
                                        <th style="vertical-align:middle;">Observación</th>
                                        <th style="vertical-align:middle;">Tipo</th>
                                        <th style="vertical-align:middle;">Documento</th>
                                        <th style="vertical-align:middle;">Base 0%</th>
                                        <th style="vertical-align:middle;">Base diferente 0%</th>
                                        <th style="vertical-align:middle;">Descuento</th>
                                        <th style="vertical-align:middle;">Subtotal</th>
                                        <th style="vertical-align:middle;">Iva</th>
                                        <th style="vertical-align:middle;">Total</th>
                                        <?php
                                        if (config('data.porcentajes') != '' && count(config('data.porcentajes')) > 0) {
                                            foreach (config('data.porcentajes') as $row) {
                                                echo '<th style="vertical-align:middle;">' . $row->nombre . '<br/> ' . $row->valor . '%</th>';
                                            }
                                        }
                                        ?>
                                        <th style="vertical-align:middle;">Total a recibir</th>
                                        <th style="vertical-align:middle;"># Retención</th>
                                        <th style="vertical-align:middle;">Autorización</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalBase0 = 0;
                                    $totalBaseDiferente0 = 0;
                                    $totalDescuento = 0;
                                    $totalSubtotal = 0;
                                    $totalImpuestos = 0;
                                    $totalTotal = 0;
                                    $htmlVentas = '';
                                    if (config('data.ventas') != null && count(config('data.ventas')) > 0) {
                                        $count = 0;
                                        foreach (config('data.ventas') as $row) {
                                            $th = '';
                                            $th = explode('|', Retenciones_Clientes::getDatosRetencionVenta($row, config('data.porcentajes')));
                                            $totalBase0 += $row->subtotal_0;
                                            $totalBaseDiferente0 += $row->subtotal_12;
                                            $totalDescuento += $row->descuento;
                                            $totalSubtotal += $row->subtotal;
                                            $totalImpuestos += $row->impuesto;
                                            $totalTotal += $row->total;
                                            $count++;
                                            $htmlVentas .= '<tr>
                                                <td>
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox" name="seleccionVentas[]" id="checkVentas' . $row->id . '" value="' . $row->id . '">
                                                        <label for="checkVentas' . $row->id . '">
                                                    </div>
                                                    
                                                </td>
                                                <td>' . $count . '</td>
                                                <td>' . $row->id_asiento . '</td>
                                                <td>' . $row->fecha . '</td>
                                                <td>' . $row->nombre . '</td>
                                                <td>' . $row->id_cliente . '</td>
                                                <td>' . $row->nro_autorizacion . '</td>                                        
                                                <td>' . $row->concepto . '</td>
                                                <td>' . $row->tipo . '</td>
                                                <td>' . $row->nro_comprobante . '</td>
                                                <td align="right">' . $row->subtotal_0 . '</td>
                                                <td align="right">' . $row->subtotal_12 . '</td>
                                                <td align="right">' . $row->descuento . '</td>
                                                <td align="right">' . $row->subtotal . '</td>
                                                <td align="right">' . $row->impuesto . '</td>
                                                <td align="right">' . $row->total . '</td>
                                                ' . $th[0] . '
                                                </tr>';
                                        }
                                    }
                                    echo $htmlVentas;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Totales</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="text-align: right;"><?= number_format($totalBase0, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalBaseDiferente0, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalDescuento, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalSubtotal, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalImpuestos, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalTotal, 2); ?></td>
                                        <?php
                                        if (config('data.porcentajes') != '' && count(config('data.porcentajes')) > 0) {
                                            $cont = 0;
                                            foreach (config('data.porcentajes') as $row) {
                                                echo '<td style="text-align: right;">' . number_format(Retenciones_Clientes::sumaRetenciones($row, config('data.input')['anio'], config('data.input')['mes']), 2) . '</td>';
                                                $cont++;
                                            }
                                        }
                                        ?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="compras" role="tabpanel" aria-labelledby="compras-tab" style="height: 600px;overflow: auto;">
                            <table id="compras" class="table table-bordered table-striped" style="font-size: 11px;width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align: middle;">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkTodosCompras" onclick="seleccionarTodosCompras(this);"> <label for="checkTodosCompras">
                                                    <label for="checkTodosVentas"></label>
                                            </div>
                                        </th>
                                        <th style="vertical-align:middle;">No</th>
                                        <th style="vertical-align:middle;">Tipo</th>
                                        <th style="vertical-align:middle;">Asiento</th>
                                        <th style="vertical-align:middle;">Fecha</th>
                                        <th style="vertical-align:middle;">Proveedor</th>
                                        <th style="vertical-align:middle;">Ruc</th>
                                        <th style="vertical-align:middle;">Autorización</th>
                                        <th style="vertical-align:middle;">Observación</th>
                                        <th style="vertical-align:middle;">Documento</th>
                                        <th style="vertical-align:middle;">Base 0%</th>
                                        <th style="vertical-align:middle;">Base diferente 0%</th>
                                        <th style="vertical-align:middle;">Descuento</th>
                                        <th style="vertical-align:middle;">Subtotal</th>
                                        <th style="vertical-align:middle;">Iva</th>
                                        <th style="vertical-align:middle;">Total</th>
                                        <?php
                                        if (config('data.porcentajes') != '' && count(config('data.porcentajes')) > 0) {
                                            foreach (config('data.porcentajes') as $row) {
                                                echo '<th style="vertical-align:middle;">' . $row->nombre . '<br/> ' . $row->valor . '%</th>';
                                            }
                                        }
                                        ?>
                                        <th style="vertical-align:middle;">Total a pagar</th>
                                        <th style="vertical-align:middle;"># Retención</th>
                                        <th style="vertical-align:middle;">Autorización</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalBase0 = 0;
                                    $totalBaseDiferente0 = 0;
                                    $totalDescuento = 0;
                                    $totalSubtotal = 0;
                                    $totalImpuestos = 0;
                                    $totalTotal = 0;
                                    $htmlCompras = '';
                                    if (config('data.compras') != null && count(config('data.compras')) > 0) {
                                        $count = 0;
                                        foreach (config('data.compras') as $row) {
                                            $totalBase0 += $row->subtotal_0;
                                            $totalBaseDiferente0 += $row->subtotal_12;
                                            $totalDescuento += $row->descuento;
                                            $totalSubtotal += $row->subtotal;
                                            $totalImpuestos += $row->impuesto;
                                            $totalTotal += $row->total;
                                            $count++;
                                            $htmlCompras .= '<tr>
                                            <td> <div class="icheck-primary d-inline">
                                                <input type="checkbox" name="seleccionCompras[]" id="checkCompras' . $row->id . '" value="' . $row->id . '">
                                                <label for="checkCompras' . $row->id . '"></label>
                                            </div></td>
                                            <td>' . $count . '</td>
                                            <td>' . $row->nombre . '</td>
                                            <td>' . $row->id_asiento_cabecera . '</td>
                                            <td>' . $row->fecha . '</td>
                                            <td>' . $row->razonsocial . '</td>
                                            <td>' . $row->proveedor . '</td>
                                            <td>' . $row->autorizacion . '</td>
                                            <td>' . $row->observacion . '</td>
                                            <td>' . $row->numero . '</td>
                                            <td align="right">' . $row->subtotal_0 . '</td>
                                            <td align="right">' . $row->subtotal_12 . '</td>
                                            <td align="right">' . $row->descuento . '</td>
                                            <td align="right">' . $row->subtotal . '</td>
                                            <td align="right">' . $row->impuesto . '</td>
                                            <td align="right">' . $row->total . '</td>
                                            ' . Retenciones::getDatosRetencionCompras($row, config('data.porcentajes')) . '
                                            </tr>';
                                        }
                                    }
                                    echo $htmlCompras;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Totales</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="text-align: right;"><?= number_format($totalBase0, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalBaseDiferente0, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalDescuento, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalSubtotal, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalImpuestos, 2); ?></td>
                                        <td style="text-align: right;"><?= number_format($totalTotal, 2); ?></td>
                                        <?php
                                        if (config('data.porcentajes') != '' && count(config('data.porcentajes')) > 0) {
                                            $cont = 0;
                                            foreach (config('data.porcentajes') as $row) {
                                                echo '<td style="text-align: right;">' . number_format(Retenciones::sumaRetenciones($row, config('data.input')['anio'], config('data.input')['mes']), 2) . '</td>';
                                                $cont++;
                                            }
                                        }
                                        ?>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="formulario_104" role="tabpanel" aria-labelledby="formulario_104-tab">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{config('data.titulo')}}</h3>
                                                </div>
                                                <div class="card-body hero-contenido">
                                                    <table id="tableformulario104" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                                                        <tbody>
                                                            <tr>
                                                                <th colspan="2" style="background-color: rgb(206, 227, 231)">RESUMEN DE VENTAS Y OTRAS OPERACIONES DEL PERÍODO QUE DECLARA</th>
                                                                <th colspan="2" style="background-color: rgb(206, 227, 231)">VALOR BRUTO</th>
                                                                <th colspan="2" style="background-color: rgb(206, 227, 231)">VALOR NETO (VALOR BRUTO - N/C)</th>
                                                                <th colspan="2" style="background-color: rgb(206, 227, 231)">IMPUESTO GENERADO</th>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas locales (excluye activos fijos) gravadas tarifa diferente de cero </td>
                                                                <td> 401 </td>
                                                                <td align="center"> <input type="text" id="401" onblur="calcularFormulario104();" class="form-control text-right" value="<?= str_replace(',', '', number_format(isset(config('data.f104')['ventas']['401']) ? config('data.f104')['ventas']['401'] : 0, 2)); ?>" style="width: 70%;" /> </td>
                                                                <td> 411 </td>
                                                                <td align="center"> <input type="text" id="411" onblur="calcularFormulario104();" class="form-control text-right" value="<?= str_replace(',', '', number_format(isset(config('data.f104')['ventas']['411']) ? config('data.f104')['ventas']['411'] : 0, 2)); ?>" style="width: 70%;" /> </td>
                                                                <td> 421 </td>
                                                                <td align="center"> <input type="text" id="421" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas de activos fijos gravadas tarifa diferente de cero </td>
                                                                <td> 402 </td>
                                                                <td align="center"> <input type="text" id="402" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td> 412 </td>
                                                                <td align="center"> <input type="text" id="412" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td> 422 </td>
                                                                <td align="center"> <input type="text" id="422" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas locales (excluye activos fijos) gravadas tarifa diferente de cero (TARIFA VARIABLE) </td>
                                                                <td> 410 </td>
                                                                <td align="center"> <input type="text" id="410" nblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td> 420 </td>
                                                                <td align="center"> <input type="text" id="420" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td> 430 </td>
                                                                <td align="center"> <input type="text" id="430" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> IVA generado en la diferencia entre ventas y notas de crédito con distinta tarifa (ajuste a pagar) </td>
                                                                <td colspan="4"></td>
                                                                <td> 423 </td>
                                                                <td align="center"> <input type="text" id="423" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> IVA generado en la diferencia entre ventas y notas de crédito con distinta tarifa (ajuste a favor) </td>
                                                                <td colspan="4"></td>
                                                                <td> 424 </td>
                                                                <td align="center"> <input type="text" id="424" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas locales (excluye activos fijos) gravadas tarifa 0% que no dan derecho a crédito tributario </td>
                                                                <td> 403 </td>
                                                                <td align="center"> <input type="text" id="403" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>413</td>
                                                                <td align="center"> <input type="text" id="413" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas de activos fijos gravadas tarifa 0% que no dan derecho a crédito tributario </td>
                                                                <td> 404</td>
                                                                <td align="center"> <input type="text" id="404" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>414</td>
                                                                <td align="center"> <input type="text" id="414" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas locales (excluye activos fijos) gravadas tarifa 0% que dan derecho a crédito tributario </td>
                                                                <td>405 </td>
                                                                <td align="center"> <input type="text" id="405" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>415</td>
                                                                <td align="center"> <input type="text" id="415" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ventas de activos fijos gravadas tarifa 0% que dan derecho a crédito tributario </td>
                                                                <td>406 </td>
                                                                <td align="center"> <input type="text" id="406" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>416</td>
                                                                <td align="center"> <input type="text" id="416" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Exportaciones de bienes </td>
                                                                <td>407 </td>
                                                                <td align="center"> <input type="text" id="407" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>417</td>
                                                                <td align="center"> <input type="text" id="417" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Exportaciones de servicios y/o derechos </td>
                                                                <td> 408</td>
                                                                <td align="center"> <input type="text" id="408" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>418</td>
                                                                <td align="center"> <input type="text" id="418" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <th style="background-color: rgb(220, 198, 110)"> TOTAL VENTAS Y OTRAS OPERACIONES</th>
                                                                <th style="background-color: rgb(220, 198, 110)"> 409 </th>
                                                                <td align="center"> <input type="text" id="409" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <th style="background-color: rgb(220, 198, 110)"> 419 </th>
                                                                <td align="center"> <input type="text" id="419" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <th style="background-color: rgb(220, 198, 110)"> 429 </th>
                                                                <td align="center"> <input type="text" id="429" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Transferencias no objeto o exentas de IVA </td>
                                                                <td> 431</td>
                                                                <td align="center"> <input type="text" id="431" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>441</td>
                                                                <td align="center"> <input type="text" id="441" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Notas de crédito tarifa 0% por compensar próximo mes </td>
                                                                <td colspan="2"> </td>
                                                                <td>442</td>
                                                                <td align="center"> <input type="text" id="442" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td colspan="2"> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Notas de crédito tarifa diferente de cero por compensar próximo mes </td>
                                                                <td colspan="2"> </td>
                                                                <td>443</td>
                                                                <td align="center"> <input type="text" id="443" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td> 453 </td>
                                                                <td align="center"> <input type="text" id="453" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td> Ingresos por reembolso como intermediario / valores facturados por operadoras de transporte / ingresos
                                                                    obtenidos por parte de las sociedades de gestión colectiva como intermediarios (informativo) </td>
                                                                <td> 434</td>
                                                                <td align="center"> <input type="text" id="434" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>444</td>
                                                                <td align="center"> <input type="text" id="444" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                                <td>454 </td>
                                                                <td align="center"> <input type="text" id="454" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="14"> </td>
                                                            </tr>
                                                    </table>
                                                    <table id="tableformulario104" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                                                        <tr>
                                                            <th colspan="14" style="background-color: rgb(206, 227, 231)">LIQUIDACIÓN DEL IVA EN EL MES </th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Total transferencias gravadas tarifa diferente de cero a contado este mes </td>
                                                            <td> 480 </td>
                                                            <td align="center"> <input type="text" id="480" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Total transferencias gravadas tarifa diferente de cero a crédito este mes </td>
                                                            <td> 481 </td>
                                                            <td align="center"> <input type="text" id="481" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Total impuesto generado </td>
                                                            <td colspan="4"> (trasládese campo 429) </td>
                                                            <td> 482 </td>
                                                            <td align="center"> <input type="text" id="482" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Impuesto a liquidar del mes anterior </td>
                                                            <td colspan="4"> (trasládese el campo 485 de la declaración del período anterior) </td>
                                                            <td> 483 </td>
                                                            <td align="center"> <input type="text" id="483" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Impuesto a liquidar en este mes </td>
                                                            <td> 484 </td>
                                                            <td align="center"> <input type="text" id="484" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Impuesto a liquidar en el próximo mes </td>
                                                            <td colspan="4"> 482-484 </td>
                                                            <td> 485 </td>
                                                            <td align="center"> <input type="text" id="485" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Mes a pagar el monto de IVA diferente de cero por ventas a crédito de este mes </td>
                                                            <td> 486</td>
                                                            <td align="center"> <input type="text" id="486" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Tamaño COPCI </td>
                                                            <td> 487</td>
                                                            <td align="center"> <input type="text" id="487" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(220, 198, 110)"> TOTAL IMPUESTO A LIQUIDAR EN ESTE MES</th>
                                                            <td colspan="4" style="background-color: rgb(220, 198, 110)"> 483+484 </td>
                                                            <th style="background-color: rgb(220, 198, 110)"> 499 </th>
                                                            <td align="center"> <input type="text" id="499" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"> Total comprobantes de venta emitidos </td>
                                                            <td> 111</td>
                                                            <td align="center"> <input type="text" id="111" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> . </td>
                                                            <td colspan="4"> Total comprobantes de venta anulados </td>
                                                            <td> 113</td>
                                                            <td align="center"> <input type="text" id="113" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(206, 227, 231)">RESUMEN DE ADQUISICIONES Y PAGOS DEL PERÍODO QUE DECLARA</th>
                                                            <th colspan="2" style="background-color: rgb(206, 227, 231)">VALOR BRUTO</th>
                                                            <th colspan="2" style="background-color: rgb(206, 227, 231)">VALOR NETO (VALOR BRUTO - N/C)</th>
                                                            <th colspan="2" style="background-color: rgb(206, 227, 231)">IMPUESTO GENERADO</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Adquisiciones y pagos (excluye activos fijos) gravados tarifa diferente de cero (con derecho a crédito tributario)</td>
                                                            <td> 500 </td>
                                                            <td align="center"> <input type="text" id="500" onblur="calcularFormulario104();" class="form-control text-right" value="<?= str_replace(',', '', number_format(isset(config('data.f104')['compras']['500']) ? config('data.f104')['compras']['500'] : 0, 2)); ?>" style="width: 70%;" /> </td>
                                                            <td> 510</td>
                                                            <td align="center"> <input type="text" id="510" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 520 </td>
                                                            <td align="center"> <input type="text" id="520" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Adquisiciones locales de activos fijos gravados tarifa diferente de cero (con derecho a crédito tributario) </td>
                                                            <td> 501 </td>
                                                            <td align="center"> <input type="text" id="501" onblur="calcularFormulario104();" class="form-control text-right" value="<?= str_replace(',', '', number_format(isset(config('data.f104')['compras']['501']) ? config('data.f104')['compras']['501'] : 0, 2)); ?>" style=" width: 70%;" /> </td>
                                                            <td> 511</td>
                                                            <td align="center"> <input type="text" id="511" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 521 </td>
                                                            <td align="center"> <input type="text" id="521" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Adquisiciones y pagos (excluye activos fijos) gravados tarifa diferente de cero (con derecho a crédito tributario tarifa variable)</td>
                                                            <td> 530 </td>
                                                            <td align="center"> <input type="text" id="530" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 533</td>
                                                            <td align="center"> <input type="text" id="533" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 534 </td>
                                                            <td align="center"> <input type="text" id="534" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Otras adquisiciones y pagos gravados tarifa diferente de cero (sin derecho a crédito tributario)</td>
                                                            <td> 502 </td>
                                                            <td align="center"> <input type="text" id="502" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 512</td>
                                                            <td align="center"> <input type="text" id="512" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 522 </td>
                                                            <td align="center"> <input type="text" id="522" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de servicios y/o derechos gravados tarifa diferente de cero</td>
                                                            <td> 503 </td>
                                                            <td align="center"> <input type="text" id="503" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 513</td>
                                                            <td align="center"> <input type="text" id="513" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 523 </td>
                                                            <td align="center"> <input type="text" id="523" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de bienes (excluye activos fijos) gravados tarifa diferente de cero</td>
                                                            <td> 504 </td>
                                                            <td align="center"> <input type="text" id="504" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 514</td>
                                                            <td align="center"> <input type="text" id="514" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 524 </td>
                                                            <td align="center"> <input type="text" id="524" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de activos fijos gravados tarifa diferente de cero</td>
                                                            <td> 505 </td>
                                                            <td align="center"> <input type="text" id="505" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 515</td>
                                                            <td align="center"> <input type="text" id="515" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 525 </td>
                                                            <td align="center"> <input type="text" id="525" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> IVA generado en la diferencia entre adquisiciones y notas de crédito con distinta tarifa (ajuste en positivo al crédito tributario)</td>
                                                            <td colspan="4"> </td>
                                                            <td> 526 </td>
                                                            <td align="center"> <input type="text" id="526" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> IVA generado en la diferencia entre adquisiciones y notas de crédito con distinta tarifa (ajuste en negativo al crédito tributario)</td>
                                                            <td colspan="4"> </td>
                                                            <td> 527 </td>
                                                            <td align="center"> <input type="text" id="527" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de bienes (incluye activos fijos) gravados tarifa 0%</td>
                                                            <td> 506 </td>
                                                            <td align="center"> <input type="text" id="506" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 516</td>
                                                            <td align="center"> <input type="text" id="516" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de bienes (incluye activos fijos) gravados tarifa 0%</td>
                                                            <td> 507 </td>
                                                            <td align="center"> <input type="text" id="507" onblur="calcularFormulario104();" class="form-control text-right" value="<?= str_replace(',', '', number_format(isset(config('data.f104')['compras']['507']) ? config('data.f104')['compras']['507'] : 0, 2)); ?>" style="width: 70%;" /> </td>
                                                            <td> 517</td>
                                                            <td align="center"> <input type="text" id="517" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Importaciones de bienes (incluye activos fijos) gravados tarifa 0%</td>
                                                            <td> 508 </td>
                                                            <td align="center"> <input type="text" id="508" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 518</td>
                                                            <td align="center"> <input type="text" id="518" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(220, 198, 110)">TOTAL ADQUISICIONES Y PAGOS </th>
                                                            <th style="background-color: rgb(220, 198, 110)"> 509 </th>
                                                            <td align="center"> <input type="text" id="509" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <th style="background-color: rgb(220, 198, 110)"> 519 </th>
                                                            <td align="center"> <input type="text" id="519" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <th style="background-color: rgb(220, 198, 110)"> 459 </th>
                                                            <td align="center"> <input type="text" id="459" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Adquisiciones no objeto de IVA</td>
                                                            <td> 531 </td>
                                                            <td align="center"> <input type="text" id="531" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 541</td>
                                                            <td align="center"> <input type="text" id="541" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Adquisiciones exentas del pago de IVA</td>
                                                            <td> 532 </td>
                                                            <td align="center"> <input type="text" id="532" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 542</td>
                                                            <td align="center"> <input type="text" id="542" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Notas de crédito tarifa 0% por compensar próximo mes</td>
                                                            <td colspan="2"> </td>
                                                            <td> 543 </td>
                                                            <td align="center"> <input type="text" id="543" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Notas de crédito tarifa diferente de cero por compensar próximo mes</td>
                                                            <td colspan="2"> </td>
                                                            <td> 544 </td>
                                                            <td align="center"> <input type="text" id="544" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 554</td>
                                                            <td align="center"> <input type="text" id="554" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Pagos netos por reembolso como intermediario / valores facturados por socios a operadoras de
                                                                transporte / pagos realizados por parte de las sociedades de gestión colectiva</td>
                                                            <td> 535 </td>
                                                            <td align="center"> <input type="text" id="535" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 545</td>
                                                            <td align="center"> <input type="text" id="545" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 555</td>
                                                            <td align="center"> <input type="text" id="555" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Factor de proporcionalidad para crédito tributario</td>
                                                            <td colspan="4"> (411+412+415+416+417+418) / 419</td>
                                                            <td> 563 </td>
                                                            <td align="center"> <input type="text" id="563" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="8"> Crédito tributario aplicable en este período (de acuerdo al factor de proporcionalidad o a su contabilidad) </td>
                                                            <td colspan="4">(520+521+523+524+525+526-527) x 563</td>
                                                            <td> 564 </td>
                                                            <td align="center"> <input type="text" id="564" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"> Total comprobantes de venta recibidos por adquisiciones y pagos (excepto notas de venta) </td>
                                                            <td> 115 </td>
                                                            <td align="center"> <input type="text" id="115" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td colspan="2"> </td>
                                                            <td colspan="4"> Total notas de venta recibidas</td>
                                                            <td> 117 </td>
                                                            <td align="center"> <input type="text" id="117" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Total liquidaciones de compra emitidas (por pagos tarifa 0% de IVA, o por
                                                                reembolsos en relación de dependencia) </td>
                                                            <td> 119 </td>
                                                            <td align="center"> <input type="text" id="119" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="14" style="background-color: rgb(206, 227, 231)"> RESUMEN IMPOSITIVO: AGENTE DE PERCEPCIÓN DEL IMPUESTO AL VALOR AGREGADO </th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"> Impuesto causado </td>
                                                            <td colspan="6"> (si la diferencia de los campos 499-564 es mayor que cero) </td>
                                                            <td> 601 </td>
                                                            <td align="center"> <input type="text" id="601" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6"> Crédito tributario aplicable en este período </td>
                                                            <td colspan="6"> (si la diferencia de los campos 499-564 es menor que cero)</td>
                                                            <td> 602 </td>
                                                            <td align="center"> <input type="text" id="602" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (-) Compensación de IVA por ventas efectuadas en zonas afectadas - Ley de solidaridad, restitución
                                                                de crédito tributario en resoluciones administrativas o sentencias</td>
                                                            <td> 603 </td>
                                                            <td align="center"> <input type="text" id="603" name="603" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (-) Compensación de IVA por ventas efectuadas con medio electrónico y/o IVA devuelto o descontado por
                                                                transacciones realizadas con personas adultas mayores o personas con discapacidad</td>
                                                            <td> 604 </td>
                                                            <td align="center"> <input type="text" id="604" name="604" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> (-) Saldo crédito tributario del mes anterior </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="4"> Por adquisiciones e importaciones</td>
                                                            <td colspan="6"> (trasládese el campo 615 de la declaración del período anterior)</td>
                                                            <td> 605 </td>
                                                            <td align="center"> <input type="text" id="605" name="605" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="4"> Por retenciones en la fuente de IVA que le han sido efectuadas </td>
                                                            <td colspan="6"> (trasládese el campo 617 de la declaración del período anterior)</td>
                                                            <td> 606 </td>
                                                            <td align="center"> <input type="text" id="606" name="606" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="4"> Por compensación de IVA por ventas efectuadas con medio electrónico </td>
                                                            <td colspan="6"> (trasládese el campo 618 de la declaración del período anterior)</td>
                                                            <td> 607 </td>
                                                            <td align="center"> <input type="text" id="607" name="607" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="4"> Por compensación de IVA por ventas efectuadas en zonas afectadas - Ley de solidaridad, restitución
                                                                de crédito tributario en resoluciones administrativas o sentencias </td>
                                                            <td colspan="6"> (trasládese el campo 619 de la declaración del período anterior)</td>
                                                            <td> 608 </td>
                                                            <td align="center"> <input type="text" id="608" name="608" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (-) Compensación de IVA por ventas efectuadas en zonas afectadas - Ley de solidaridad, restitución
                                                                de crédito tributario en resoluciones administrativas o sentencias</td>
                                                            <td> 603 </td>
                                                            <td align="center"> <input type="text" id="603" name="603" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (-) Retenciones en la fuente de IVA que le han sido efectuadas en este período</td>
                                                            <td> 609 </td>
                                                            <td align="center"> <input type="text" id="609" name="609" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (-) IVA devuelto o descontado por transacciones realizadas con personas adultas mayores o
                                                                personas con discapacidad</td>
                                                            <td> 622 </td>
                                                            <td align="center"> <input type="text" id="622" name="622" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">(+) Ajuste por IVA devuelto o descontado por adquisiciones efectuadas con medio electrónico</td>
                                                            <td> 610 </td>
                                                            <td align="center"> <input type="text" id="610" name="610" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (+) Ajuste por IVA devuelto o descontado en adquisiciones efectuadas en zonas afectadas - Ley de solidaridad</td>
                                                            <td> 611 </td>
                                                            <td align="center"> <input type="text" id="611" name="611" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (+) Ajuste por IVA devuelto e IVA rechazado (por concepto de devoluciones de IVA), ajuste de IVA por procesos
                                                                de control y otros (adquisiciones en importaciones), imputables al crédito tributario</td>
                                                            <td> 612 </td>
                                                            <td align="center"> <input type="text" id="612" name="612" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (+) Ajuste por IVA devuelto e IVA rechazado, ajuste de IVA por procesos de control y otros (por concepto
                                                                retenciones en la fuente de IVA), imputables al crédito tributario</td>
                                                            <td> 613 </td>
                                                            <td align="center"> <input type="text" id="613" name="613" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> (+) Ajuste por IVA devuelto por otras instituciones del sector público imputable al crédito tributario en el mes</td>
                                                            <td> 614 </td>
                                                            <td align="center"> <input type="text" id="614" name="614" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14">Saldo crédito tributario para el próximo mes</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Por adquisiciones e importaciones </td>
                                                            <td> 615 </td>
                                                            <td align="center"> <input type="text" id="615" name="615" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Por retenciones en la fuente de IVA que le han sido efectuadas </td>
                                                            <td> 617 </td>
                                                            <td align="center"> <input type="text" id="617" name="617" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Por compensación de IVA por ventas efectuadas con medio electrónico </td>
                                                            <td> 618 </td>
                                                            <td align="center"> <input type="text" id="618" name="618" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10">Por compensación de IVA por ventas efectuadas en zonas afectadas - Ley de solidaridad, restitución de
                                                                crédito tributario en resoluciones administrativas o sentencias judiciales de última instancia </td>
                                                            <td> 619 </td>
                                                            <td align="center"> <input type="text" id="619" name="619" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="4"> SUBTOTAL A PAGAR </th>
                                                            <td colspan="8">Si (601-602-603-604-605-606-607-608-609+610+611+612+613+614) > 0 </td>
                                                            <td> 620 </td>
                                                            <td align="center"> <input type="text" id="620" name="620" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> IVA PRESUNTIVO DE SALAS DE JUEGO (BINGO MECÁNICOS) Y OTROS JUEGOS DE AZAR (Aplica para Ejercicios Anteriores al 2013), RETENCIÓN DE IVA
                                                                EN VENTAS DIFERENTES PORCENTAJES (aplica para Ejercicios posteriores al 2021)</td>
                                                            <td> 621 </td>
                                                            <td align="center"> <input type="text" id="621" name="621" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(220, 198, 110)"> TOTAL IMPUESTO A PAGAR POR PERCEPCIÓN Y RETENCIONES EFECTUADAS EN VENTAS (varios porcentajes)</th>
                                                            <td colspan="4" style="background-color: rgb(220, 198, 110)"> 620+621</td>
                                                            <td style="background-color: rgb(220, 198, 110)"> 699 </td>
                                                            <td align="center"> <input type="text" id="699" name="699" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="10" style="background-color: rgb(206, 227, 231)">IMPUESTO A LA SALIDA DE DIVISAS A EFECTOS DE DEVOLUCIÓN A EXPORTADORES HABITUALES DE BIENES</th>
                                                            <th colspan="2" style="background-color: rgb(206, 227, 231)">VALOR</th>
                                                            <th colspan="2" style="background-color: rgb(206, 227, 231)">ISD PAGADO</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="10"> Importaciones de materias primas, insumos y bienes de capital que sean incorporadas en procesos productivos
                                                                de bienes que se exporten</td>
                                                            <td> 700 </td>
                                                            <td align="center"> <input type="text" id="700" name="700" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                            <td> 701 </td>
                                                            <td align="center"> <input type="text" id="701" name="701" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> </td>
                                                            <td colspan="2" style="background-color: rgb(206, 227, 231)">PORCENTAJE</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Proporción del ingreso neto de divisas desde el exterior al Ecuador, respecto del total de las exportaciones netas de bienes </td>
                                                            <td> 702 </td>
                                                            <td align="center"> <input type="text" id="702" name="702" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="14" style="background-color: rgb(206, 227, 231)">AGENTE DE RETENCIÓN DEL IMPUESTO AL VALOR AGREGADO</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 10% </td>
                                                            <td> 721 </td>
                                                            <td align="center"> <input type="text" id="721" name="721" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 20% </td>
                                                            <td> 723 </td>
                                                            <td align="center"> <input type="text" id="723" name="723" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 30% </td>
                                                            <td> 725 </td>
                                                            <td align="center"> <input type="text" id="725" name="725" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 50% </td>
                                                            <td> 727 </td>
                                                            <td align="center"> <input type="text" id="727" name="727" onblur="calcularFormulario104();" class="form-control text-right" value="0.00" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 70% </td>
                                                            <td> 729 </td>
                                                            <td align="center"> <input type="text" id="729" name="729" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retención del 100% </td>
                                                            <td> 731 </td>
                                                            <td align="center"> <input type="text" id="731" name="731" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8"> TOTAL IMPUESTO RETENIDO </th>
                                                            <td colspan="4"> 721+723+725+727+729+731 </td>
                                                            <td> 799 </td>
                                                            <td align="center"> <input type="text" id="799" name="799" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Devolución provisional de IVA mediante compensación con retenciones efectuadas </td>
                                                            <td> 800 </td>
                                                            <td align="center"> <input type="text" id="800" name="800" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12">Retenciones efectuadas y no pagadas sector público, universidades y escuelas politécnicas</td>
                                                            <td> 802 </td>
                                                            <td align="center"> <input type="text" id="802" name="802" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(220, 198, 110)"> TOTAL IMPUESTO A PAGAR POR RETENCIÓN </th>
                                                            <td colspan="4" style="background-color: rgb(220, 198, 110)"> (799-800-802)</td>
                                                            <td style="background-color: rgb(220, 198, 110)"> 801 </td>
                                                            <td align="center"> <input type="text" id="801" name="801" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(206, 227, 231)"> TOTAL CONSOLIDADO DE IMPUESTO AL VALOR AGREGADO </th>
                                                            <td colspan="4" style="background-color: rgb(206, 227, 231)"> (699+801)</td>
                                                            <td style="background-color: rgb(206, 227, 231)"> 859 </td>
                                                            <td align="center"> <input type="text" id="859" name="859" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="12">Pago previo </th>
                                                            <th> 890 </th>
                                                            <td align="center"> <input type="text" id="890" name="890" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14">Detalle de imputación al pago (para declaraciones sustitutivas) </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Interés</td>
                                                            <td> 897 </td>
                                                            <td align="center"> <input type="text" id="897" name="897" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Impuesto</td>
                                                            <td> 898 </td>
                                                            <td align="center"> <input type="text" id="898" name="898" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2"> </td>
                                                            <td colspan="10"> Multa</td>
                                                            <td> 899 </td>
                                                            <td align="center"> <input type="text" id="899" name="899" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Pago directo en cuenta única del tesoro nacional (uso exclusivo para instituciones y empresas
                                                                del sector público autorizadas) </td>
                                                            <td> 880 </td>
                                                            <td align="center"> <input type="text" id="880" name="880" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> ¿Tiene derecho al pago diferido del IVA a pagar de este ejercicio fiscal conforme al Decreto 1021 del 2020 por la emergencia sanitaria en relación con el COVID-19? </td>
                                                            <td> 801 </td>
                                                            <td align="center"> <input type="text" id="801" name="801" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 1 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (10%) </td>
                                                            <td> 802 </td>
                                                            <td align="center"> <input type="text" id="802" name="802" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 2 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (10%) </td>
                                                            <td> 803 </td>
                                                            <td align="center"> <input type="text" id="803" name="803" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 3 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (20%) </td>
                                                            <td> 804 </td>
                                                            <td align="center"> <input type="text" id="804" name="804" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 4 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (20%) </td>
                                                            <td> 805 </td>
                                                            <td align="center"> <input type="text" id="805" name="805" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 5 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (20%) </td>
                                                            <td> 806 </td>
                                                            <td align="center"> <input type="text" id="806" name="806" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Cuota 6 del Impuesto al Valor Agregado del ejercicio fiscal 2020 (20%) </td>
                                                            <td> 807 </td>
                                                            <td align="center"> <input type="text" id="807" name="807" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="14"> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="14" style="background-color: rgb(206, 227, 231)"> VALORES A PAGAR (luego de imputación al pago en declaraciones sustitutivas) </th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="8" style="background-color: rgb(220, 198, 110)"> TOTAL IMPUESTO A PAGAR </th>
                                                            <td colspan="4" style="background-color: rgb(220, 198, 110)"> (859-898)</td>
                                                            <td style="background-color: rgb(220, 198, 110)"> 902 </td>
                                                            <td align="center"> <input type="text" id="902" name="902" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Interés por mora </td>
                                                            <td> 903 </td>
                                                            <td align="center"> <input type="text" id="903" name="903" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="12"> Multa </td>
                                                            <td> 904 </td>
                                                            <td align="center"> <input type="text" id="904" name="904" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="12"> TOTAL PAGADO </th>
                                                            <td> 999 </td>
                                                            <td align="center"> <input type="text" id="999" name="999" onblur="calcularFormulario104();" class="form-control" style="width: 70%;" /> </td>
                                                        </tr>
                                                    </table>
                                                    </tbody>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="tab-pane fade" id="formulario_103" role="tabpanel" aria-labelledby="formulario_103-tab">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{config('data.titulo')}}</h3>
                                                    <div>
                                                        <div style="position: absolute; right: 5px;top:7px">
                                                            <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarCliente();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body hero-contenido">
                                                    <table id="tableformulario104" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <td colspan="11" style="background-color: rgb(206, 227, 231)" class="text-center"><b>DETALLE DE PAGOS Y RETENCIÓN POR IMPUESTO A LA RENTA</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="11" style="background-color: rgb(206, 227, 231)" class="text-center"><b>POR PAGOS EFECTUADOS A RESIDENTES Y ESTABLECIMIENTOS PERMANENTES</b></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="5" style="width: 60%;"></td>
                                                                <td colspan="3" style="background-color: rgb(206, 227, 231)" class="text-center"><b>BASE IMPONIBLE</b></td>
                                                                <td colspan="3" style="background-color: rgb(206, 227, 231)" class="text-center"><b>VALOR RETENIDO</b></td>
                                                            </tr>

                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">EN RELACIÓN DE DEPENDENCIA QUE SUPERA O NO LA BASE DESGRAVADA</td>
                                                                <td style="width: 1%; background-color: rgb(179, 210, 239)">302</td>
                                                                <td style="width: 1%;">+</td>
                                                                <td align="center"> <input type="text" id="302" name="302" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="width: 1%; background-color: rgb(179, 210, 239)">352</td>
                                                                <td style="width: 1%;">+</td>
                                                                <td align="center"> <input type="text" id="352" name="352" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="6" class="vertical-center" style="padding-top: 180px; width: 12%; background-color: rgb(206, 227, 231)">SERVICIOS</td>
                                                                <td colspan="4" style="font-size: 10px;">NORARIOS PROFESIONALES </td>
                                                                <td style="background-color: rgb(179, 210, 239)">303</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="303" name="303" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">353</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="353" name="353" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">PREDOMINA EL INTELECTO</td>
                                                                <td style="background-color: rgb(179, 210, 239)">304</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="304" name="304" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">354</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="354" name="354" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">PREDOMINA MANO DE OBRA</td>
                                                                <td style="background-color: rgb(179, 210, 239)">307</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="307" name="307" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">357</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="357" name="357" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">UTILIZACIÓN O APROVECHAMIENTO DE LA IMAGEN O RENOMBRE</td>
                                                                <td style="background-color: rgb(179, 210, 239)">308</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="308" name="308" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">358</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="358" name="358" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">PUBLICIDAD Y COMUNICACIÓN</td>
                                                                <td style="background-color: rgb(179, 210, 239)">309</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="309" name="309" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">359</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="359" name="359" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">TRANSPORTE PRIVADO DE PASAJEROS O SERVICIO PÚBLICO O PRIVADO DE CARGA</td>
                                                                <td style="background-color: rgb(179, 210, 239)">310</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="310" name="310" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">360</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="360" name="360" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">A TRAVÉS DE LIQUIDACIONES DE COMPRA (NIVEL CULTURAL O RUSTICIDAD)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">311</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="311" name="311" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">361</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="361" name="361" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">TRANSFERENCIA DE BIENES MUEBLES DE NATURALEZA CORPORAL</td>
                                                                <td style="background-color: rgb(179, 210, 239)">312</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="312" name="312" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">362</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="362" name="362" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">POR REGALÍAS, DERECHOS DE AUTOR, MARCAS, PATENTES Y SIMILARES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">314</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="314" name="314" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">364</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="364" name="364" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="2" class="vertical-center" style="padding-top: 50px; background-color: rgb(206, 227, 231)">ARRENDAMIENTO</td>
                                                                <td colspan="4" style="font-size: 10px;">MERCANTIL</td>
                                                                <td style="background-color: rgb(179, 210, 239)">319</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="319" name="319" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">369</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="369" name="369" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">BIENES INMUEBLES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">320</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="320" name="320" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">370</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="370" name="370" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">SEGUROS Y REASEGUROS (PRIMAS Y CESIONES)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">322</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="322" name="322" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">372</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="372" name="372" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">RENDIMIENTOS FINANCIEROS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">323</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="323" name="323" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">373</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="373" name="373" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">RENDIMIENTOS FINANCIEROS ENTRE INSTITUCIONES DEL SISTEMA FINANCIERO Y ENTIDADES ECONOMÍA POPULAR Y SOLIDARIA</td>
                                                                <td style="background-color: rgb(179, 210, 239)">324</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="324" name="324" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">374</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="374" name="374" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">ANTICIPO DIVIDENDOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">325</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="325" name="325" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">375</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="375" name="375" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS QUE CORRESPONDAN AL IMPTO. A LA RENTA ÚNICO ESTABLECIDO EN EL ART. 27 DE LA LRTI</td>
                                                                <td style="background-color: rgb(179, 210, 239)">326</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="326" name="326" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">376</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="376" name="376" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A PERSONAS NATURALES RESIDENTES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">327</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="327" name="327" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">377</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="377" name="377" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A SOCIEDADES RESIDENTES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">328</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="328" name="328" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">378</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="378" name="378" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A FIDEICOMISOS RESIDENTES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">329</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="329" name="329" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">379</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="379" name="379" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS GRAVADOS DISTRIBUIDOS EN ACCIONES (REINVERSIÓN DE UTILIDADES SIN DERECHO A REDUCCIÓN TARIFA IR)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">330</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="330" name="330" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">380</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="380" name="380" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">DIVIDENDOS GRAVADOS DISTRIBUIDOS EN ACCIONES (REINVERSIÓN DE UTILIDADES SIN DERECHO A REDUCCIÓN TARIFA IR)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">331</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="331" name="331" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td colspan="4" style="background-color: rgb(179, 210, 239)"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">PAGOS DE BIENES O SERVICIOS NO SUJETOS A RETENCIÓN</td>
                                                                <td style="background-color: rgb(179, 210, 239)">332</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="332" name="332" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td colspan="4" style="background-color: rgb(179, 210, 239)"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">ENAJENACIÓN DE DERECHOS REPRESENTATIVOS DE CAPITAL Y OTROS DERECHOS COTIZADOS EN BOLSA ECUATORIANA</td>
                                                                <td style="background-color: rgb(179, 210, 239)">333</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="333" name="333" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">383</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="383" name="383" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">ENAJENACIÓN DE DERECHOS REPRESENTATIVOS DE CAPITAL Y OTROS DERECHOS NO COTIZADOS EN BOLSA ECUATORIANA</td>
                                                                <td style="background-color: rgb(179, 210, 239)">334</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="334" name="334" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">384</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="384" name="384" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 10px;">LOTERÍAS, RIFAS, APUESTAS Y SIMILARES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">335</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="335" name="335" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">385</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="385" name="385" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="2" class="vertical-center" style="padding-top: 35px; background-color: rgb(206, 227, 231)">VENTA DE <br> COMBUSTIBLES</td>
                                                                <td colspan="4" style="font-size: 10px;">A COMERCIALIZADORAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">336</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="336" name="336" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">386</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="386" name="386" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">A DISTRIBUIDORES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">337</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="337" name="337" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">387</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="387" name="387" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="font-size: 10px;">COMPRA LOCAL DE BANANO A PRODUCTOR</td>
                                                                <td style="font-size: 9px; text-align: right;">No. Cajas facturadas</td>
                                                                <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">510</td>
                                                                <td style="width: 10%;"></td>
                                                                <td style="background-color: rgb(179, 210, 239)">338</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="338" name="338" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">388</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="388" name="388" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="font-size: 10px;">LIQUIDACIÓN IMPUESTO ÚNICO A LA VENTA LOCAL DE BANANO DE PRODUCCIÓN PROPIA</td>
                                                                <td style="font-size: 9px; text-align: right;">No. Cajas facturadas</td>
                                                                <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">520</td>
                                                                <td style="width: 10%;"></td>
                                                                <td style="background-color: rgb(179, 210, 239)">339</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="339" name="339" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">389</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="389" name="389" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="font-size: 10px;">IMPUESTO ÚNICO A LA EXPORTACIÓN DE BANANO DE PRODUCCIÓN PROPIA - COMPONENTE 1</td>
                                                                <td style="font-size: 9px; text-align: right;">No. Cajas facturadas</td>
                                                                <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">530</td>
                                                                <td style="width: 10%;"></td>
                                                                <td style="background-color: rgb(179, 210, 239)">340</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="340" name="340" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">390</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="390" name="390" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="font-size: 10px;">IMPUESTO ÚNICO A LA EXPORTACIÓN DE BANANO DE PRODUCCIÓN PROPIA - COMPONENTE 2</td>
                                                                <td style="font-size: 9px; text-align: right;">No. Cajas facturadas</td>
                                                                <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">540</td>
                                                                <td style="width: 10%;"></td>
                                                                <td style="background-color: rgb(179, 210, 239)">341</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="341" name="341" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">391</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="391" name="391" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="font-size: 10px;">IMPUESTO ÚNICO A LA EXPORTACIÓN DE BANANO PRODUCIDO POR TERCEROS</td>
                                                                <td style="font-size: 9px; text-align: right;">No. Cajas facturadas</td>
                                                                <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">550</td>
                                                                <td style="width: 10%;"></td>
                                                                <td style="background-color: rgb(179, 210, 239)">342</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="342" name="342" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">392</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="392" name="392" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="4" class="vertical-center" style="padding-top: 120px; background-color: rgb(206, 227, 231)">OTRAS RETENCIONES</td>
                                                                <td colspan="4" style="font-size: 10px;">APLICABLES EL 1%</td>
                                                                <td style="background-color: rgb(179, 210, 239)">343</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="343" name="343" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">393</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="393" name="393" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">APLICABLES EL 2%</td>
                                                                <td style="background-color: rgb(179, 210, 239)">344</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="344" name="344" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">394</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="394" name="394" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">APLICABLES EL 8%</td>
                                                                <td style="background-color: rgb(179, 210, 239)">345</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="345" name="345" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">395</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="395" name="395" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">APLICABLES A OTROS PORCENTAJES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">346</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="346" name="346" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">396</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="396" name="396" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 12px;"><b>SUBTOTAL OPERACIONES EFECTUADAS EN EL PAIS</b></td>
                                                                <td style="background-color: rgb(179, 210, 239)">349</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="349" name="349" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">399</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="399" name="399" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="11" style="background-color: rgb(206, 227, 231)" class="text-center"><b>POR PAGOS A NO RESIDENTES</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td rowspan="11" class="vertical-center" style="padding-top: 300px; background-color: rgb(206, 227, 231)">CON CONVENIO <br> DE DOBLE <br> TRIBUTACIÓN</td>
                                                                <td colspan="4" style="font-size: 10px;">INTERESES POR FINANCIAMIENTO DE PROVEEDORES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">402</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="402" name="402" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">452</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="452" name="452" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">INTERESES DE CRÉDITOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">403</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="403" name="403" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">453</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="453" name="453" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ANTICIPO DE DIVIDENDOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">404</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="404" name="404" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">454</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="454" name="454" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A PERSONAS NATURALES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">405</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="405" name="405" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">455</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="455" name="455" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A SOCIEDADES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">406</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="406" name="406" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">456</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="456" name="456" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A FIDEICOMISOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">407</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="407" name="407" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">457</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="457" name="457" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ENAJENACIÓN DE DERECHOS REPRESENTATIVOS DE CAPITAL Y OTROS DERECHOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">408</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="408" name="408" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">458</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="458" name="458" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SEGUROS Y REASEGUROS (PRIMAS Y CESIONES)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">409</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="409" name="409" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">459</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="459" name="459" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">410</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="410" name="410" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">460</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="460" name="460" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">411</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="411" name="411" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">461</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="461" name="461" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">OTROS PAGOS AL EXTERIOR NO SUJETOS A RETENCIÓN</td>
                                                                <td style="background-color: rgb(179, 210, 239)">412</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="412" name="412" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)" colspan="3"></td>
                                                            </tr>



                                                            <tr>
                                                                <td rowspan="11" class="vertical-center" style="padding-top: 300px; background-color: rgb(206, 227, 231)">SIN CONVENIO <br> DE DOBLE <br> TRIBUTACIÓN</td>
                                                                <td colspan="4" style="font-size: 10px;">INTERESES POR FINANCIAMIENTO DE PROVEEDORES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">413</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="413" name="413" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">463</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="463" name="463" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">INTERESES DE CRÉDITOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">414</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="414" name="414" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">464</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="464" name="464" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ANTICIPO DE DIVIDENDOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">415</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="415" name="415" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">465</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="465" name="465" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A PERSONAS NATURALES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">416</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="416" name="416" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">466</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="466" name="466" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A SOCIEDADES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">417</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="417" name="417" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">467</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="467" name="467" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A FIDEICOMISOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">418</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="418" name="418" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">468</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="468" name="468" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ENAJENACIÓN DE DERECHOS REPRESENTATIVOS DE CAPITAL Y OTROS DERECHOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">419</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="419" name="419" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">469</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="469" name="469" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SEGUROS Y REASEGUROS (PRIMAS Y CESIONES)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">420</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="420" name="420" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">470</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="470" name="470" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">421</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="421" name="421" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">471</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="471" name="471" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">422</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="422" name="422" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">472</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="472" name="472" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">OTROS PAGOS AL EXTERIOR NO SUJETOS A RETENCIÓN</td>
                                                                <td style="background-color: rgb(179, 210, 239)">423</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="423" name="423" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)" colspan="3"></td>
                                                            </tr>


                                                            <tr>
                                                                <td rowspan="10" class="vertical-center" style="padding-top: 250px; background-color: rgb(206, 227, 231)">EN PARAÍSOS <br> FISCALES O <br> REGÍMENES <br> FISCALES <br> PREFERENTES </td>
                                                                <td colspan="4" style="font-size: 10px;">INTERESES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">424</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="424" name="424" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">474</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="474" name="474" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ANTICIPO DE DIVIDENDOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">425</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="425" name="425" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">475</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="475" name="475" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A PERSONAS NATURALES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">426</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="426" name="426" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">476</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="476" name="476" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A SOCIEDADES</td>
                                                                <td style="background-color: rgb(179, 210, 239)">427</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="427" name="427" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">477</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="477" name="477" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">DIVIDENDOS DISTRIBUIDOS A FIDEICOMISOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">428</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="428" name="428" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">478</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="478" name="478" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>



                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">ENAJENACIÓN DE DERECHOS REPRESENTATIVOS DE CAPITAL Y OTROS DERECHOS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">429</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="429" name="429" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">479</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="479" name="479" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SEGUROS Y REASEGUROS (PRIMAS Y CESIONES)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">430</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="430" name="430" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">480</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="480" name="480" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">431</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="431" name="431" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">481</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="481" name="481" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">SERVICIOS TÉCNICOS, ADMINISTRATIVOS O DE CONSULTORÍA Y REGALÍAS</td>
                                                                <td style="background-color: rgb(179, 210, 239)">432</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="432" name="432" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">482</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="482" name="482" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" style="font-size: 10px;">OTROS PAGOS AL EXTERIOR NO SUJETOS A RETENCIÓN</td>
                                                                <td style="background-color: rgb(179, 210, 239)">433</td>
                                                                <td>+</td>
                                                                <td align="center"> <input type="text" id="433" name="433" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)" colspan="3"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="font-size: 12px;"><b>SUBTOTAL OPERACIONES EFECTUADAS CON EL EXTERIOR</b></td>
                                                                <td style="background-color: rgb(179, 210, 239)">497</td>
                                                                <td>=</td>
                                                                <td align="center"> <input type="text" id="497" name="497" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                <td style="background-color: rgb(179, 210, 239)">498</td>
                                                                <td style="background-color: rgb(179, 210, 239)">=</td>
                                                                <td align="center"> <input type="text" id="498" name="498" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="11"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="background-color: rgb(179, 210, 239)">TOTAL DE RETENCIÓN DE IMPUESTO A LA RENTA</td>
                                                                <td colspan="3" style="text-align: right;" style="background-color: rgb(179, 210, 239)">CAMPOS 399+498</td>
                                                                <td style="background-color: rgb(179, 210, 239)">499</td>
                                                                <td style="background-color: rgb(179, 210, 239)">=</td>
                                                                <td style="background-color: rgb(179, 210, 239)"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="11"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8" style="font-size: 12px;">PAGO PREVIO ( Informativo)</td>
                                                                <td style="background-color: rgb(179, 210, 239)">890</td>
                                                                <td></td>
                                                                <td align="center"> <input type="text" id="890" name="890" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                            </tr>

                                                            <table style="width: 100%;" class="table table-bordered table-striped tablaDatos">
                                                                <tr>
                                                                    <td colspan="10" class="text-center"><b>DETALLE DE IMPUTACIÓN AL PAGO (Para declaraciones sustitutivas)</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center" style="background-color: rgb(179, 210, 239)">INTERÉS</td>
                                                                    <td style="width: 1%;" style="background-color: rgb(179, 210, 239)">897</td>
                                                                    <td>USD</td>
                                                                    <td class="text-center" style="background-color: rgb(179, 210, 239)">IMPUESTO</td>
                                                                    <td class="text-center" style="width: 1%; background-color: rgb(179, 210, 239)">898</td>
                                                                    <td>USD</td>
                                                                    <td class="text-center" style="background-color: rgb(179, 210, 239)">MULTA</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">899</td>
                                                                    <td>USD</td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="10"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7" style="font-size: 11px;">PAGO DIRECTO EN CUENTA ÚNICA DEL TESORO NACIONAL (Uso Exclusivo para Instituciones y Empresas del Sector Público Autorizadas)</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">880</td>
                                                                    <td class="text-center">+</td>
                                                                    <td align="center"> <input type="text" id="880" name="880" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="background-color: rgb(206, 227, 231)" colspan="10"><b>VALORES A PAGAR Y FORMA DE PAGO (luego de imputación al pago en declaraciones sustitutivas)</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6">TOTAL IMPUESTO A PAGAR</td>
                                                                    <td style="text-align: right;">499-898</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">902</td>
                                                                    <td class="text-center">+</td>
                                                                    <td align="center"> <input type="text" id="902" name="902" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7">INTERÉS POR MORA</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">903</td>
                                                                    <td class="text-center">+</td>
                                                                    <td align="center"> <input type="text" id="903" name="903" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7">MULTA</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">904</td>
                                                                    <td class="text-center">+</td>
                                                                    <td align="center"> <input type="text" id="904" name="904" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7" style="background-color: rgb(179, 210, 239)">TOTAL PAGADO</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">999</td>
                                                                    <td class="text-center">=</td>
                                                                    <td align="center"> <input type="text" id="999" name="999" onblur="calcularFormulario();" class="form-control" style="width: 70%; text-align:right" /> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7">MEDIANTE CHEQUE, DÉBITO BANCARIO, EFECTIVO U OTRAS FORMAS DE PAGO</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">905</td>
                                                                    <td colspan="2">USD</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7">MEDIANTE NOTAS DE CRÉDITO</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">907</td>
                                                                    <td colspan="2">USD</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="7">MEDIANTE TÍTULOS DEL BANCO CENTRAL (TBC)</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">925</td>
                                                                    <td colspan="2">USD</td>
                                                                </tr>
                                                            </table>
                                                            <table class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                                                                <tr>
                                                                    <td colspan="6" class="text-center" style="width: 50%; background-color: rgb(179, 210, 239)">DETALLE DE NOTAS DE CRÉDITO CARTULARES</td>
                                                                    <td colspan="3" class="text-center" style="background-color: rgb(179, 210, 239)">DETALLE DE NOTAS DE CRÉDITO DESMATERIALIZADAS</td>
                                                                    <td colspan="3" class="text-center" style="background-color: rgb(179, 210, 239)">TÍTULOS DEL BANCO CENTRAL</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">908</td>
                                                                    <td style="width: 10%;">N/C No</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">910</td>
                                                                    <td style="width: 10%;">N/C No</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">912</td>
                                                                    <td style="width: 10%;">N/C No</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" colspan="3"></td>
                                                                    <td style="background-color: rgb(179, 210, 239)" colspan="3"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="background-color: rgb(179, 210, 239)">909</td>
                                                                    <td>USD</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">911</td>
                                                                    <td>USD</td>
                                                                    <td style="background-color: rgb(179, 210, 239)">913</td>
                                                                    <td>USD</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">915</td>
                                                                    <td colspan="2">USD</td>
                                                                    <td style="background-color: rgb(179, 210, 239)" style="width: 1%;">920</td>
                                                                    <td colspan="2">USD</td>
                                                                </tr>
                                                            </table>
                                                            <table style="width: 100%;" class="table table-bordered table-striped tablaDatos">
                                                                <tr>
                                                                    <td colspan="11" class="text-center" style="font-size: 13px;">DECLARO QUE LOS DATOS PROPORCIONADOS EN ESTE DOCUMENTO SON EXACTOS Y VERDADEROS, POR LO QUE ASUMO LA RESPONSABILIDAD LEGAL QUE DE ELLA SE DERIVEN (Art. 101 de la L.R.T.I.)</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-center" style="height: 125px;"><br><br><br>__________________________________________________<br>FIRMA SUJETO PASIVO / REPRESENTANTE LEGAL</td>
                                                                    <td class="text-center" colspan="5"><br><br><br>____________________________<br>FIRMA CONTADOR</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" style="background-color: rgb(179, 210, 239)" style="width: 15%;">NOMBRE :</td>
                                                                    <td colspan="4" style="width: 30%;"></td>
                                                                    <td colspan="2" style="background-color: rgb(179, 210, 239)" style="width: 20%;">NOMBRE :</td>
                                                                    <td colspan="3" style="width: 30%;"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="background-color: rgb(179, 210, 239); width: 1%;">198</td>
                                                                    <td colspan="3">Cédula de Identidad o No. de Pasaporte</td>
                                                                    <td colspan="2" style="width: 25%;"></td>
                                                                    <td style="background-color: rgb(179, 210, 239); width: 1%;">199</td>
                                                                    <td style="width: 10%;">RUC No.</td>
                                                                    <td colspan="3"></td>
                                                                </tr>
                                                            </table>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="tab-pane fade" id="resumen" role="tabpanel" aria-labelledby="resumen-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                        </div>
                        <div class="tab-pane fade" id="diario" role="tabpanel" aria-labelledby="diario-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                        </div>
                        <div class="tab-pane fade" id="ats_generado" role="tabpanel" aria-labelledby="ats_generado-tab">
                            <div style="padding-bottom: 10px;padding-top: 10px;">
                                <a onclick="guardarVc();" class="btn btn-success">Generar ATS</a>
                            </div>
                            <table class="table table-bordered table-striped" style="font-size: 11px;width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align:middle;">No</th>
                                        <th style="vertical-align:middle;">Periodo</th>
                                        <th style="vertical-align:middle;">Generado</th>
                                        <th style="vertical-align:middle;">Usuario</th>
                                        <!--<th style="vertical-align:middle;">Excel</th>-->
                                        <th style="vertical-align:middle;">Xml</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (config('data.atsGenerados') != null && count(config('data.atsGenerados')) > 0) {
                                        foreach (config('data.atsGenerados') as $row) {
                                            echo '<tr>
                                            <td align="center">' . $row->id . '</td>
                                            <td align="center">' . $row->periodo . '</td>
                                            <td align="center">' . $row->created_at . '</td>
                                            <td align="center">' . $row->usuariocrea . '</td>
                                            <!--<td align="center"><a href="javascript:;" class="btn btn-success"><i class="fa fa-file-excel"></i> Exportar Excel</a></td>-->
                                            <td align="center"><a href="v3/' . config('data.controlador') . '/exportarXml/' . $row->id . '" class="btn btn-primary"><i class="fa fa-file-code"></i> Exportar Xml</a></td>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="vertical-align:middle;">No</th>
                                        <th style="vertical-align:middle;">Periodo</th>
                                        <th style="vertical-align:middle;">Generado</th>
                                        <th style="vertical-align:middle;">Usuario</th>
                                        <!--<th style="vertical-align:middle;">Excel</th>-->
                                        <th style="vertical-align:middle;">Xml</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>