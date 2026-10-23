<?php
$iva = 0;
if (config('data.impuestos') != '' && count(config('data.impuestos'))) {
    foreach (config('data.impuestos') as $row) {
        if ($row->catalogo == 'Iva') {
            $iva = $row->valor;
        }
    }
}
?>
<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Saldos Iniciales</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">saldos iniciales</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <!-- <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                Electrónica<input type="checkbox" name="my-checkbox" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">
                            </div>
                        </div> -->
                    </div>
                    <div class="card-body hero-factura">
                        <div class="ibox-content">
                            <form id="frm_factura">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveVenta" />
                                <input type="hidden" id="d" name="d" />
                                <input type="hidden" id="tipo_factura" name="tipo_factura" value="VEN-FA" />
                                <input type="hidden" id="contraentrega" name="contraentrega" />
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos de saldos iniciales</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-datosfactura">
                                            <div class="col-md-2">
                                                <label class="font-normal">ID</label>
                                                <input type="text" class="form-control text-right" id="id" name="id" readonly />
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Número</label>
                                                <input type="text" class="form-control text-right" id="numero" name="numero" readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Tipo</label>
                                                <input type="text" class="form-control" id="tipo" name="tipo" value="ACR-DB" readonly/>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Asiento</label>
                                                <input type="text" class="form-control text-right" id="asiento" name="asiento" readonly/>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Fecha</label>
                                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                            </div>
                                        </div>
                                        <div class="row hero-cliente">
                                            <div class="col-md-3">
                                                <label class="font-normal">Sucursal</label>
                                                <select class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal" onchange="obtener_caja()">
                                                    <option value="">-- Seleccione --</option>
                                                    <?php
                                                    foreach (config('data.sucursales') as $row) {
                                                        echo '<option value="' . $row->id . '" >' . $row->codigo_sucursal . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Punto de emisión</label>
                                                <select class="form-control select2" style="width: 100%;" id="emision" name="emision">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Acreedor</label>
                                                <select class="form-control select2" id="id_proveedor" name="id_proveedor">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Concepto</label>
                                                <input type="text" class="form-control" id="concepto" name="concepto" /></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <br><br>
                                
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="6" style="text-align: center;">Detalle de rubros</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                    <th style="text-align: center; width: 15%;">Rubro</th>
                                                    <th style="text-align: center; width: 40%;">Detalle</th>
                                                    <th style="text-align: center; width: 15%;">Divisa</th>
                                                    <th style="text-align: center; width: 10%;">Valor</th>
                                                    <th style="text-align: center; width: 10%;">Total Base</th>
                                                    <th style="text-align: center;" id="agregarLinea"><i class="fas fa-plus-square fa-lg"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleSaldosIniciales"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <th style="text-align: center;">Total</th>
                                                            <th><input type="text" class="form-control text-right" value="0.00" readonly id="total" name="total" /></th>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>