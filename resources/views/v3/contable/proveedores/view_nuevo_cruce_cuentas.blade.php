<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Cruce de cuentas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">cruce de cuentas</li>
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
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveCruceCuentasProveedor" />
                                <input type="hidden" id="d" name="d" />
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos del cruce de cuentas</h3>
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
                                                <input type="text" class="form-control" id="tipo" name="tipo" value="CRP-CUENTAS" readonly/>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Asiento</label>
                                                <input type="text" class="form-control text-right" id="id_asiento" name="id_asiento" readonly/>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <label class="font-normal">Fecha de Cruce</label>
                                                <input type="date" class="form-control" id="fechacc" name="fechacc" value="<?= date('Y-m-d'); ?>" />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Establecimiento</label>
                                                <select class="form-control select2" style="width: 100%;" id="sucursalcc" name="sucursalcc">
                                                    <option value="">--Seleccione--</option>
                                                    <?php
                                                    $sucursales = config('data.sucursales');
                                                    if (!empty($sucursales) && count($sucursales) > 0) {
                                                        if (count($sucursales) === 1) {
                                                            $sucursalUnica = $sucursales[0];
                                                            echo "<option value='" . $sucursalUnica->codigo_sucursal . "' selected>" . $sucursalUnica->codigo_sucursal . ' | ' . $sucursalUnica->nombre_sucursal . "</option>";
                                                        } else {
                                                            foreach ($sucursales as $suc) {
                                                                echo "<option value='" . $suc->codigo_sucursal . "'>" . $suc->codigo_sucursal . ' | ' . $suc->nombre_sucursal . "</option>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos de la factura</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-cliente"> 
                                            <div class="col-md-6">
                                                <label class="font-normal">Factura</label>
                                                <select class="form-control select2" style="width: 100%;" id="comprobante_compra" name="comprobante_compra">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Valor</label>
                                                <input type="text" class="form-control text-right" id="info_valor" name="info_valor" placeholder="$0.00" readonly/>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="font-normal">Fecha de Factura</label>
                                                <input type="date" class="form-control" id="fecha_factcc" name="fecha_factcc" readonly/>
                                            </div>
                                        </div>
                                        <div class="row hero-cliente"> 
                                            <div class="col-md-6">
                                                <label class="font-normal">Concepto</label>
                                                <input type="text" class="form-control" id="conceptop" name="conceptop" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-normal">Proveedor</label>
                                                <input type="text" class="form-control" id="proveedorcc" name="proveedorcc" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="6" style="text-align: center; width: 30%;">Detalle de cuentas en el haber</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                    <th style="text-align: center; width: 30%;">Codigo</th>
                                                    <th style="text-align: center; width: 35%;">Detalle</th>
                                                    <th style="text-align: center; width: 10%;">Divisa</th>
                                                    <th style="text-align: center; width: 10%;">Valor</th>
                                                    <th style="text-align: center; width: 10%;">Total Base</th>
                                                    <th style="text-align: center;" id="agregarLineaCruceProv"><a href="javascript:;" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleCruceCuentasprov"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <!-- <th style="text-align: center;">Subtotal</th>
                                                            <th style="text-align: center;">Impuesto</th> -->
                                                            <th style="text-align: center;">Total</th>
                                                        </tr>
                                                        <tr>
                                                            <!-- <td><input type="text" class="form-control text-right" value="0.00" readonly id="subtotal" name="subtotal" readonly/></td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="impuesto" name="impuesto" /></td> -->
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="totalp" name="totalp" readonly/></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div><br><br>
                                <div style='text-align: center'>
                                    <button type="button" class="btn btn-danger" onclick="regresar('/v3/proveedores/crucedecuentas')">
                                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar
                                    </button>
                                    <a href="javascript:;" class="btn btn-success" id="btnguardarCruceProv"><i class="fa-solid fa-save"></i> Guardar</a>
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