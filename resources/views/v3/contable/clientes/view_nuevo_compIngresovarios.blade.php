<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Comprobante de Ingresos Varios</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">comprobante de ingreso varios</li>
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
                    <div class="card-body hero-factura">
                        <div class="ibox-content">
                            <form id="frm_">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveCompIngresovarios" />
                                <input type="hidden" name="valor_base" id="valor_base" value="0">
                                <input type="hidden" id="d" name="d" value="|">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos del comprobante</h3>
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
                                            <div class="col-md-2">
                                                <label class="font-normal">Número</label>
                                                <input type="text" class="form-control text-right" id="numero" name="numero" readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Tipo</label>
                                                <input type="text" class="form-control" id="tipo" name="tipo" value="BAN-IN" readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Asiento</label>
                                                <input type="text" class="form-control text-right" id="id_asiento" name="id_asiento" readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Fecha</label>
                                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Establecimiento</label>
                                                <select class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal">
                                                    <option value="">--Seleccione--</option>
                                                    <?php
                                                    $sucursales = config('data.sucursales');
                                                    if (!empty($sucursales) && count($sucursales) > 0) {
                                                        if (count($sucursales) === 1) {
                                                            $sucursalUnica = $sucursales[0];
                                                            echo "<option value='" . $sucursalUnica->id . "' selected>" . $sucursalUnica->codigo_sucursal . ' | ' . $sucursalUnica->nombre_sucursal . "</option>";
                                                        } else {
                                                            foreach ($sucursales as $suc) {
                                                                echo "<option value='" . $suc->id . "'>" . $suc->codigo_sucursal . ' | ' . $suc->nombre_sucursal . "</option>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="font-normal">Concepto</label>
                                                <input type="text" class="form-control" id="concepto" name="concepto" />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Caja/Banco</label>
                                                <select class="form-control select2" style="width: 100%;" id="id_banco" name="id_banco">
                                                    <option value="">--Seleccione--</option>
                                                    <?php
                                                    $cajaBanco = config('data.cajaBanco');
                                                    if (!empty($cajaBanco) && count($cajaBanco) > 0) {
                                                        if (count($cajaBanco) === 1) {
                                                            $cajalUnica = $cajaBanco[0];
                                                            echo "<option value='" . $cajaUnica->id . "' selected>" . $cajaUnica->nombre ."</option>";
                                                        } else {
                                                            foreach ($cajaBanco as $cb) {
                                                                echo "<option value='" . $cb->id . "'>" . $cb->nombre . "</option>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Divisas</label>
                                                <select class="form-control select2" style="width: 100%;" id="divisas" name="divisas">
                                                    <option value="">--Seleccione--</option>
                                                    <?php 
                                                        $divisas = config('data.divisas');
                                                        if (!empty($divisas) && count($divisas) > 0) {
                                                            if (count($divisas) === 1) {
                                                                $divisaUnica = $divisas[0];
                                                                echo "<option value='" . $divisaUnica->id . "' selected>" . $divisaUnica->descripcion . "</option>";
                                                            } else {
                                                                foreach ($divisas as $div) {
                                                                    echo "<option value='" . $div->id . "'>" . $div->descripcion . "</option>";
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                                <br>
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="10" style="text-align: center; width: 30%;">Detalles de valores recibidos</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                    <th style="text-align: center; min-width: 11%;">Tipo</th>
                                                    <th style="text-align: center; min-width: 8%;">Fecha</th>
                                                    <th style="text-align: center; min-width: 10%;">Referencia</th>
                                                    <th style="text-align: center; min-width: 15%;">Banco</th>
                                                    <th style="text-align: center; min-width: 14%;">Tarjeta</th>
                                                    <th style="text-align: center; min-width: 10%;">Cuenta</th>
                                                    <th style="text-align: center; min-width: 13%;">Girador</th>
                                                    <th style="text-align: center; min-width: 10%;">Valor</th>
                                                    <th style="text-align: center; min-width: 9%;" id="agregarlineaValoresRecibidosciv"><a href="javascript:;" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleValoresRecibidosciv"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <th style="text-align: center;">Total Ingresos</th>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_ingresos" name="total_ingresos" readonly /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="9" style="text-align: center; width: 30%;">Detalle del complemento contable</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                    <th style="text-align: center; width: 50%;">Codigo</th>
                                                    <th style="text-align: center; width: 15%;">Divisa</th>
                                                    <th style="text-align: center; width: 15%;">Debe</th>
                                                    <th style="text-align: center; width: 15%;">Haber</th>
                                                    <th style="text-align: center; min-width: 9%;" id="agregarLineaciv"><a href="javascript:;" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleComplementociv">
                                            </tbody>
                                        </table>
                                    </div>
                                </div><br><br>
                                <div class="row">
                                    <label class="font-normal">Nota</label>
                                    <div class='col-md-12' style='text-align: left'>
                                        <textarea class="col-md-12 " name="nota" id="nota" cols="200" rows="5"></textarea>
                                    </div>
                                </div>
                                <br>
                                <div style='text-align: center; margin-bottom: 2%'>
                                    <button type="button" class="btn btn-danger" onclick="regresar('/v3/clientes/comprobantedeingresovarios')">
                                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar
                                    </button>
                                    <a href="javascript:;" class="btn btn-success" id="btnguardarCompIngresovarios"><i class="fa-solid fa-save"></i> Guardar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>