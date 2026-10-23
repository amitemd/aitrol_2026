<?php
$data = config('data.bodegas');
$json_data = json_encode($data);;
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.submodulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.submodulo'))) }}</li>
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
                    <input type="hidden" id="arrayBodegas" value="<?php echo htmlspecialchars($json_data); ?>">
                    <form id="frm_factura">
                        <div class="card-header">
                            <h3 class="card-title">{{ config('data.titulo') }}</h3>
                            <div>
                                <?php
                                if (config('data.empresa')->egreso_kardex == 1) {
                                ?>
                                    <div style="position: absolute; right: 5px;top:7px">
                                        Kardex <input type="checkbox" name="kardex" id="kardex" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="card-body hero-factura">
                            <div class="ibox-content">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveVenta" />
                                <input type="hidden" id="tipo_factura" name="tipo_factura" value="VEN-FA" />
                                <input type="hidden" id="contraentrega" name="contraentrega" />
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.informacionfactura')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.fechaemision')}}</label>
                                                        <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="<?= date('Y-m-d'); ?>" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.establecimiento')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="establecimiento" name="establecimiento" onchange="obtener_caja()">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                            <?php
                                                            if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                                                foreach (config('data.sucursales') as $value) {
                                                                    echo '<option value="' . $value->id . '" >' . $value->codigo_sucursal . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.emision')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="emision" name="emision">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.divisa')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="divisa" name="divisa">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                            <?php
                                                            if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                                foreach (config('data.divisas') as $value) {
                                                                    $select = '';
                                                                    if ($value->descripcion == 'DOLARES') {
                                                                        $select = 'selected';
                                                                    }
                                                                    echo '<option value="' . $value->id . '" ' . $select . '>' . $value->descripcion . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.recaudador')}}:</label>
                                                        <select class="form-control select2" style="width: 100%;" id="recaudador" name="recaudador">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.vendedor:')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="vendedor" name="vendedor">
                                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="font-normal">{{trans('v3/new_varios.diasplazo')}}</label>
                                                        <input type="number" class="form-control text-right" id="dias_plazo" name="dias_plazo" value="30" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.tipofactura')}}</label>
                                                        <input type="text" class="form-control" id="tipo" name="tipo" value="{{config('data.tipoDoc')}}" readonly />
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="font-normal">{{trans('v3/new_varios.concepto')}}</label>
                                                        <input type="text" class="form-control" id="concepto" name="concepto" placeholder=<?= trans('v3/new_varios.observacionadicionaldelafactura'); ?> />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-cliente">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.informaciondelcliente')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.cliente:')}} <a href="javascript:;" id="agregarCliente" class="btn btn-success btn-xs hero-agregarcliente" title=" Agregar cliente"><i class="fa-solid fa-user-plus"></i></a></label>
                                                        <input type="hidden" id="identificacion_cliente" name="identificacion_cliente" />
                                                        <select class="form-control select2" style="width: 100%;" id="id_cliente" name="id_cliente">
                                                            <option value="">{{trans('v3/new_ varios.seleccionar')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.telefono')}}</label>
                                                        <input type="text" class="form-control text-right" id="telefono_cliente" name="telefono_cliente" placeholder={{trans('v3/new_varios.telefonodelcliente')}} />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.email')}}</label>
                                                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" placeholder={{trans('v3/new_varios.e-maildelcliente')}} />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.direccion')}}</label>
                                                        <input type="text" class="form-control" id="direccion_cliente" name="direccion_cliente" placeholder={{trans('v3/new_varios.direcciondelcliente')}} />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-paciente">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.informaciondelpaciente')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.paciente:')}}</label>
                                                        <input type="hidden" id="identificacion_paciente" name="identificacion_paciente" />
                                                        <select class="form-control select2" style="width: 100%;" id="id_paciente" name="id_paciente">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.telefono')}}</label>
                                                        <input type="text" class="form-control text-right" id="telefono_paciente" name="telefono_paciente" placeholder={{trans('v3/new_varios.telefonodelpaciente')}} />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.email')}}</label>
                                                        <input type="email" class="form-control" id="email_paciente" name="email_paciente" placeholder={{trans('v3/new_varios.emaildelpaciente')}} />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.direccion')}}</label>
                                                        <input type="text" class="form-control" id="direccion_paciente" name="direccion_paciente" placeholder={{trans('v3/new_varios.direcciondelpaciente')}} />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-seguro">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.informaciondelseguro')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.seguro')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="id_seguro" name="id_seguro">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <?php
                                                            if (config('data.seguros') != '' && count(config('data.seguros')) > 0) {
                                                                foreach (config('data.seguros') as $seguro) {
                                                                    echo '<option value="' . $seguro->id . '">' . $seguro->nombre . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.nivel')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="nivel" name="nivel">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.tipo')}}</label>
                                                        <select class="form-control select2" style="width: 100%;" id="tipo" name="tipo">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="1">{{trans('v3/new_varios.consulta')}}</option>
                                                            <option value="2">{{trans('v3/new_varios.procedimiento')}}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="font-normal">{{trans('v3/new_varios.procedimiento:')}}</label>
                                                        <input type="text" class="form-control" id="procedimiento" placeholder={{trans('v3/new_varios.procedimiento/consulta')}} />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_varios.fechaprocedimiento')}}</label>
                                                        <input type="date" class="form-control" id="fecha_procedimiento" name="fecha_procedimiento" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered table-hover dataTables-example" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 150px;">{{trans('v3/new_tabla.codigo')}}</th>
                                                    <th style="width: 300px;">{{trans('v3/new_tabla.descripcion')}}</th>
                                                    <th style="width: 160px;">{{trans('v3/new_tabla.cantidad')}}</th>
                                                    <th style="width: 160px;">{{trans('v3/new_tabla.p.unitario')}}</th>
                                                    <th style="width: 160px;">{{trans('v3/new_tabla.descuento')}}</th>
                                                    <th style="width: 160px;">{{trans('v3/new_tabla.total')}}</th>
                                                    <th style="width: 50px;text-align: center;"><a href="javascript:;" id="agregarLinea" class="btn btn-success hero-agregarlinea"><i class="fa-solid fa-circle-plus"></i></a></th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleFactura"></tbody>
                                        </table>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 60%;" valign="top">
                                                    <a href="javascript:;" class="btn btn-primary" id="btnagregarAdicional"><i class="fa-solid fa-circle-plus"></i> {{trans('v3/new_varios.adicional')}}</a>
                                                </td>
                                                <td style="width: 40%;" align="right">
                                                    <table cellspacing="0">
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.Subtotal')}} {{(config('data.impuestoIva')*100)}}%</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="subtotal12" name="subtotal12" readonly /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.subtotal0%')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="subtotal0" name="subtotal0" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.descuento')}}</td>
                                                            <td><input type="hidden" id="baseImponibleTotal" name="baseImponibleTotal" /><input type="text" class="form-control text-right" value="0.00" readonly id="descuentototal" name="descuentototal" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.total')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="subtotal" name="subtotal" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.impuesto')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="iva" name="iva" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.propina')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" id="propina" name="propina" onblur="calcular();" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{trans('v3/new_varios.totalapagar')}}</td>
                                                            <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_pagar" name="total_pagar" /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr valign="top">
                                                <td colspan="3">
                                                    <div style="position: relative;">
                                                        <div style="position: absolute;top:-191px;">
                                                            <input type="hidden" id="contadorAdicionales" value="0">
                                                            <table width="100%" cellspacing="0" cellspacing="0">
                                                                <tr>
                                                                    <td align="left">
                                                                        <table id="tablaAdicionales" width="100%" cellspacing="0" cellspacing="0"></table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top: 2%">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>{{trans('v3/new_varios.formadepago')}}</label>
                                        <table class="table table-striped table-bordered table-hover dataTables-example" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('v3/new_varios.metodo')}}</th>
                                                    <th>{{trans('v3/new_varios.fecha')}}</th>
                                                    <th>{{trans('v3/new_varios.tarjeta')}}</th>
                                                    <th>{{trans('v3/new_varios.numero')}}</th>
                                                    <th>{{trans('v3/new_varios.banco')}}</th>
                                                    <th>{{trans('v3/new_varios.cuenta')}}</th>
                                                    <th>{{trans('v3/new_varios.giradoa')}}</th>
                                                    <th>{{trans('v3/new_varios.valor')}}</th>
                                                    <th style="text-align: center;"><a href="javascript:;" id="btnAgregarMetodoPago" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                                </tr>
                                            </thead>
                                            <tbody id="metodoPago"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <a href="javascript:;" class="btn btn-danger" onclick="goBack('listadeventas')"><i class="fa-solid fa-circle-xmark"></i> {{trans('v3/new_varios.cancelar')}}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="javascript:;" class="btn btn-success" id="btnguardarFactura"><i class="fa-solid fa-save"></i> {{trans('v3/new_varios.guardar')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@include('v3.contable.clientes.view_modal_clientes')