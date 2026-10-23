<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Cruce de valores a favor</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">cruce de valores a favor</li>
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
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveCruceValores" />
                                <input type="hidden" id="d" name="d" />
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Datos del cruce de valores</h3>
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
                                                <input type="text" class="form-control" id="tipo" name="tipo" value="CLI-CR-AF" readonly/>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-normal">Asiento</label>
                                                <input type="text" class="form-control text-right" id="id_asiento" name="id_asiento" readonly/>
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
                                        <h3 class="card-title">Datos del cliente</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row hero-cliente"> 
                                            <div class="col-md-6">
                                                <label class="font-normal">Cliente</label>
                                                <select class="form-control select2" style="width: 100%;" id="id_cliente" name="id_cliente" onchange="buscar_anticipos()">
                                                    <option value="">--Seleccione--</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-normal">Concepto</label>
                                                <input type="text" class="form-control text-left" id="concepto" name="concepto"  />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <input type="hidden" name="total_suma_a" id="total_suma_a">
                                <input type="hidden" name="saldoax" id="saldoax">
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="10" style="text-align: center; width: 30%;">Detalles de valores a favor</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                <th style="text-align: center; width: 4%;">Fecha</th>
                                                <th style="text-align: center; width: 3%;">Tipo</th>
                                                <th style="text-align: center; width: 4%;">Número</th>
                                                <th style="text-align: center; width: 12%;">Concepto</th>
                                                <th style="text-align: center; width: 1%;">Div</th>
                                                <th style="text-align: center; width: 4%;">Saldo</th>
                                                <th style="text-align: center; width: 4%;">Abono</th>
                                                <th style="text-align: center; width: 4%;">Nuevo saldo</th>

                                                </tr>
                                            </thead>
                                            <tbody id="detalleValoresFavor"></tbody>
                                        </table>
                                        <table style="width: 100%;"> 
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <th style="text-align: center;">Total</th>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_anticipos" name="total_anticipos" readonly/></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div><br><br>
                                
                                <input type="hidden" name="total_suma_cv" id="total_suma_cv">
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                            <thead>
                                                <tr style="background-color: #F1F1F1">
                                                    <th colspan="9" style="text-align: center; width: 30%;">Detalle de deudas del cliente</th>
                                                </tr>
                                                <tr style="background-color: #F1F1F1">
                                                    <th style="text-align: center; width: 4%;">Fecha</th>
                                                    <th style="text-align: center; width: 3%;">Tipo</th>
                                                    <th style="text-align: center; width: 4%;">Número</th>
                                                    <th style="text-align: center; width: 12%;">Concepto</th>
                                                    <th style="text-align: center; width: 1%;">DIV</th>
                                                    <th style="text-align: center; width: 4%;">Saldo</th>
                                                    <th style="text-align: center; width: 4%;">Abono</th>
                                                    <th style="text-align: center; width: 4%;">Saldo Base</th>

                                                </tr>
                                            </thead>
                                            <tbody id="detalleDeudaCliente">
                                                @php $cont=0; @endphp
                                                @foreach (range(1, 1) as $i)
                                                <tr>
                                                    <td> <input class="form-control" type="text" readonly> </td>
                                                    <td> <input class="form-control" type="text" readonly> </td>
                                                    <td> <input class="form-control" type="text" readonly> </td>
                                                    <td> <input class="form-control" type="text" readonly> </td>
                                                    <td> <input class="form-control" type="text" value="$" readonly> </td>
                                                    <td> <input class="form-control" type="text" readonly></td>
                                                    <td> <input class="form-control" type="text" readonly></td>
                                                    <td> <input class="form-control" type="text" readonly></td>

                                                </tr>
                                                @php $cont = $cont +1; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div><br>
                                <div style='text-align: center; margin-bottom: 2%'>
                                    <button type="button" class="btn btn-danger" onclick="regresar('/v3/clientes/crucedevalores')">
                                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar
                                    </button>
                                    <a href="javascript:;" class="btn btn-success" id="btnguardarCruceValores"><i class="fa-solid fa-save"></i> Guardar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>