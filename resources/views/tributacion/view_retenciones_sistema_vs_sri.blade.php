@extends('contable.balance_general.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
            <li class="breadcrumb-item"><a href="../">Retenciones vs SRI</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="get" id="reporte_master" action="confirmarRetenciones">
                <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="d" class="texto col-md-3 control-label">Desde:</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="date" class="form-control input-sm" name="d" id="d" value="<?= $d; ?>" required autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="h" class="texto col-md-3 control-label">Hasta:</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="date" class="form-control input-sm" name="h" id="h" value="<?= $h; ?>" required autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="exportTableToExcel('tablaRetenciones','Retenciones Sistema vs SRI desde {{$d}} - hasta {{$h}}');">
                        <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Exportar
                    </button>
                </div>
            </form>
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="contenedor">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="tablaRetenciones" class="table table-hover" role="grid" aria-describedby="example2_info" style="width: 100%">
                                        <tr>
                                            <td>No.</td>
                                            <td>Fecha</td>
                                            <td>Proveedor</td>
                                            <td>Ruc</td>
                                            <td>Autorizacion</td>
                                            <td>Comprobante</td>
                                            <td>Base Renta</td>
                                            <td>Valor Renta</td>
                                            <td>% Renta</td>
                                            <td>Codigo Renta</td>
                                            <td>Base Renta</td>
                                            <td>Valor Renta</td>
                                            <td>% Renta</td>
                                            <td>Codigo Renta</td>
                                            <td>Base Iva</td>
                                            <td>Valor Iva</td>
                                            <td>% Iva</td>
                                            <td>Codigo Iva</td>
                                            <td>Base Renta</td>
                                            <td>Valor Renta</td>
                                            <td>% Renta</td>
                                            <td>Codigo Renta</td>
                                        </tr>
                                        <?php

                                        use Sis_medico\Ct_detalle_retenciones;
                                        use Sis_medico\Ct_Porcentaje_Retenciones;
                                        use Sis_medico\Http\Controllers\EmisionDocumentosController;

                                        if ($d != '' && $h != '') {
                                            $count = 0;
                                            $html = '';
                                            foreach ($retenciones as $row) {
                                                $count++;
                                                $datelleRetencionRenta = Ct_detalle_retenciones::where('id_retenciones', $row->id)
                                                    ->where('tipo', 'RENTA')
                                                    ->get();
                                                if (count($datelleRetencionRenta) > 0) {
                                                    foreach ($datelleRetencionRenta as $rowd) {
                                                        $html .= '<tr id="tr_' . $count . '" onclick="ponerColor(\'' . $count . '\');">
                                                        <td>' . $count . '</td>
                                                        <td>' . $row->fecha . '</td>
                                                        <td>' . $row->razonsocial . '</td>
                                                        <td style="mso-number-format:\'@\'">' . $row->ruc . '</td>
                                                        <td style="mso-number-format:\'@\'">' . $row->autorizacion . '</td>
                                                        <td>' . $row->nro_comprobante . '</td>';
                                                        $porcentajes = Ct_Porcentaje_Retenciones::where('id', $rowd->id_porcentaje)->first();

                                                        $docXml = EmisionDocumentosController::obtenercomprobanteSRI($row->autorizacion);
                                                        /* echo '<pre>';
                                                        print_r($docXml);
                                                        exit;*/
                                                        $comprobante = '';
                                                        try {
                                                            $comprobante = simplexml_load_string($docXml->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante);
                                                        } catch (Exception $e) {
                                                            $comprobante = '';
                                                        }
                                                        if ($comprobante != '') {
                                                            $html .= '<td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format($rowd->base_imponible, 2))) . '</td>
                                                            <td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format($rowd->totales, 2))) . '</td>                        
                                                            <td>' . $porcentajes->valor . '</td>
                                                            <td>' . $porcentajes->codigo . '</td>';
                                                            if (isset($comprobante->docsSustento->docSustento->retenciones)) {
                                                                foreach ($comprobante->docsSustento->docSustento->retenciones->retencion as $codRet) {
                                                                    if ($porcentajes->codigo == (string)$codRet->codigoRetencion) {
                                                                        $html .= '<td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format((string)$codRet->baseImponible, 2))) . '</td>
                                                                        <td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format((string)$codRet->valorRetenido, 2))) . '</td>                        
                                                                        <td>' . (string)$codRet->porcentajeRetener . '</td>
                                                                        <td>' . (string)$codRet->codigoRetencion . '</td>';
                                                                    }
                                                                }
                                                            } else {
                                                                $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                                                }
                                                $datelleRetencionIva = Ct_detalle_retenciones::where('id_retenciones', $row->id)
                                                    ->where('tipo', 'IVA')
                                                    ->get();
                                                if (count($datelleRetencionIva) > 0) {
                                                    foreach ($datelleRetencionIva as $rowi) {
                                                        $porcentajes = Ct_Porcentaje_Retenciones::where('id', $rowi->id_porcentaje)->first();
                                                        $html .= '<td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format($rowi->base_imponible, 2))) . '</td>
                                                        <td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format($rowi->totales, 2))) . '</td>
                                                        <td>' . $porcentajes->valor . '</td>
                                                        <td>' . $porcentajes->codigo . '</td>';
                                                        if (isset($comprobante->docsSustento->docSustento->retenciones)) {
                                                            foreach ($comprobante->docsSustento->docSustento->retenciones->retencion as $codRet) {
                                                                if ($porcentajes->codigo == (string)$codRet->codigoRetencion) {
                                                                    $html .= '<td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format((string)$codRet->baseImponible, 2))) . '</td>
                                                                    <td style="text-align:right">' . str_replace('.', ',', str_replace(',', '', number_format((string)$codRet->valorRetenido, 2))) . '</td>                        
                                                                    <td>' . (string)$codRet->porcentajeRetener . '</td>
                                                                    <td>' . (string)$codRet->codigoRetencion . '</td>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                                                }
                                                $html .= '</tr>';
                                            }
                                            echo $html;
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function ponerColor(id) {
        var elem = document.getElementById("tr_" + id);
        elem.style.backgroundColor = dame_color_aleatorio();
    }

    function aleatorio(inferior, superior) {
        numPosibilidades = superior - inferior
        aleat = Math.random() * numPosibilidades
        aleat = Math.floor(aleat)
        return parseInt(inferior) + aleat
    }

    function dame_color_aleatorio() {
        hexadecimal = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F")
        color_aleatorio = "#";
        for (i = 0; i < 6; i++) {
            posarray = aleatorio(0, hexadecimal.length)
            color_aleatorio += hexadecimal[posarray]
        }
        return color_aleatorio
    }

    function exportTableToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        // Specify file name
        filename = filename ? filename + '.xls' : 'excel_data.xls';
        // Create download link element
        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            // Setting the file name
            downloadLink.download = filename;

            //triggering the function
            downloadLink.click();
        }
    }
</script>
@endsection