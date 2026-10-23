<style>
    input[type=checkbox] {
        vertical-align: middle;
        position: relative;
        bottom: 1px;
        scale: 1.8;
    }

    th {
        text-align: center;
    }

    .valores {
        text-align: right;
    }
</style>
<?php
$divisas = config('data.divisas');
$json_divisas = json_encode($divisas);
$cuentas = config('data.cuentas');
$json_cuentas = json_encode($cuentas);
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.titulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.titulo'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<input type="hidden" id="arrayDivisas" value="<?php echo htmlspecialchars($json_divisas); ?>">
<input type="hidden" id="arrayCuenta" value="<?php echo htmlspecialchars($json_cuentas); ?>">
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                    </div>
                    <div class="card-body hero-factura">
                        <input type="hidden" id="editar_comprobante" value="{{ isset(config('data.comprobante')->id_proveedor) ? config('data.comprobante')->id_proveedor : '' }}">
                        <input type="hidden" id="editar_forma_pago" value="{{ isset(config('data.comprobante')->id_pago) ? config('data.comprobante')->id_pago : '' }}">
                        <input type="hidden" id="editar_sucursal" value="{{ isset(config('data.edit_sucursal')->id) ? config('data.edit_sucursal')->id : '' }}">

                        <input type="hidden" id="editar_banco" value="{{ isset(config('data.edit_banco')->id) ? config('data.edit_banco')->id : '' }}">
                        <input type="hidden" id="editar_fechacheque" value="{{ isset(config('data.comprobante')->fecha_cheque) ? config('data.comprobante')->fecha_cheque : '' }}">
                        <input type="hidden" id="editar_numerocheque" value="{{ isset(config('data.comprobante')->no_cheque) ? config('data.comprobante')->no_cheque : '' }}">
                        <input type="hidden" id="editar_valor" value="{{ isset(config('data.comprobante')->valor) ? config('data.comprobante')->valor : '' }}">
                        <form id="frm_comprobanteegresovarios">
                            {{ csrf_field() }}
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarComprobanteEgresoVario" />
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.comprobante')->id) ? config('data.comprobante')->id : '' }}">
                            <input type="hidden" id="d" name="d" />
                            <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                            <input type="hidden" name="total_favor" id="total_favor">
                            <input type="hidden" name="superavit" id="superavit" value="0">
                            <input type="hidden" name="comprobarx" id="comprobarx" value="0">
                            <input class="form-control" type="hidden" name="total_suma" id="total_suma">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="sucursal">Sucursal</label>
                                                    <select @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control select2" style="width: 100%;" id="sucursal" name="sucursal">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {

                                                            foreach (config('data.sucursales') as $value) {
                                                                echo '<option ' . (isset(config('data.comprobante')->asiento) ? (config('data.comprobante')->asiento->sucursal == $value->codigo_sucursal ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->codigo_sucursal . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                                    <select @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                        <?php

                                                        if (config('data.puntoEmision') != '') {
                                                            echo '<option selected value="' . config('data.puntoEmision')->id . '">' . config('data.puntoEmision')->codigo_caja . '</option>';
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="tipo">Tipo</label>
                                                    <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="fecha_hoy">Fecha:</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control" type="date" min="@if(isset($min_date)){{$min_date}}@endif" name="fecha_hoy" id="fecha_hoy" value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-10">
                                                    <label class="font-normal" class="concepto">Concepto </label>
                                                    <input id="concepto" name="concepto" class="form-control" placeholder="Ingrese el concepto" value="<?= config('data.comprobante') != null ? config('data.comprobante')->descripcion : ''; ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" class="cheque_entregado" style="display: block">Cheque entregado</label>
                                                    <input type="checkbox" @if(isset(config('data.comprobante')->id)) disabled @endif name="cheque_entregado" id="cheque_entregado" @if (isset(config('data.comprobante')->check)) @if (config('data.comprobante')->check == 1) checked @endif
                                                    @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Información Banco</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="banco">Banco</label>
                                                    <select @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control select2" style="width: 100%;" id="banco" name="banco">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                            foreach (config('data.bancos') as $value) {
                                                                $selected = '';
                                                                if (config('data.comprobante') != null) {
                                                                    if (config('data.comprobante')->id_banco == $value->id) {
                                                                        $selected = 'selected';
                                                                    }
                                                                }
                                                                echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->nombre . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="divisas">Divisas</label>
                                                    <select @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control select2" style="width: 100%;" id="divisas" name="divisas">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                            foreach (config('data.divisas') as $value) {

                                                                $selected = '';
                                                                if (config('data.comprobante') != null) {
                                                                    if (config('data.comprobante')->id_divisa == $value->id) {
                                                                        $selected = 'selected';
                                                                    }
                                                                }
                                                                echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="cambio">Cambio</label>
                                                    <input type="text" class="form-control col-md-12 validar valores" name="cambio" id="cambio" value="1.00" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="num_cheque">Num Cheque</label>
                                                    <input type="number" class="form-control col-md-12 validar" name="num_cheque" id="num_cheque" value="<?= config('data.comprobante') != null ? config('data.comprobante')->nro_cheque : ''; ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="fecha_cheque">Fecha Cheque</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif type="date" class="form-control col-md-12 validar" name="fecha_cheque" id="fecha_cheque" value="<?= config('data.comprobante') != null ? config('data.comprobante')->fecha_cheque : ''; ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <label class="font-normal" for="valor">Valor</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif type="text" class="form-control col-md-12 validar text-right" name="valor" id="valor" value="<?= config('data.comprobante') != null ? config('data.comprobante')->valor : ''; ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="beneficiario">Beneficiario</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif type="text" class="form-control col-md-12 validar" name="beneficiario" id="beneficiario" value="<?= config('data.comprobante') != null ? config('data.comprobante')->beneficiario : ''; ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="direccion">Dirección</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif type="text" class="form-control col-md-12 validar" name="direccion" id="direccion" value="<?= config('data.comprobante') != null ? config('data.comprobante')->direccion : ''; ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="ruc">Girado a</label>
                                                    <input @if(isset(config('data.comprobante')->id)) disabled @endif type="text" class="form-control col-md-12 validar" name="girado" id="girado" value="<?= config('data.comprobante') != null ? config('data.comprobante')->beneficiario : ''; ?>">
                                                </div>


                                                <div class="col-md-2">
                                                    <label class="font-normal" for="formas_pago">Formas de Pago</label>
                                                    <select @if(isset(config('data.comprobante')->id)) disabled @endif class="form-control select2" style="width: 100%;" id="forma_pago" name="forma_pago">
                                                        <option value="">--{{ trans('v3/new_varios.seleccionar') }}--</option>
                                                        <?php
                                                        if (config('data.formas_pago') != '' && count(config('data.formas_pago')) > 0) {
                                                            foreach (config('data.formas_pago') as $value) {

                                                                $selected = '';
                                                                if (config('data.comprobante') != null) {
                                                                    if (config('data.comprobante')->id_pago == $value->id) {
                                                                        $selected = 'selected';
                                                                    }
                                                                }
                                                                echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->nombre . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
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
                                    <input type="hidden" name="contador" id="contador" value="0">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr>
                                                <th colspan="7" style="text-align: center">
                                                    DETALLE DEL COMPLEMENTO CONTABLE
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px;">Cuenta</th>
                                                @if(!isset(config('data.comprobante')->id))<th style="width: 300px;">Divisa</th> @endif
                                                <th style="width: 160px;">Debe</th>
                                                <th style="width: 160px;">Haber</th>
                                                <th style="width: 160px;">Saldo Base</th>
                                                @if(!isset(config('data.comprobante')->id)) <th style="width: 160px;"><a href="javascript:;" id="agregarlineacomprobante" class="btn btn-success hero-agregarlineacomprobante"><i class="fa-solid fa-circle-plus"></i></a></th> @endif
                                            </tr>
                                        </thead>
                                        <tbody id="detallecomporbante">
                                            <?php

                                            if (isset(config('data.comprobante')->id)) {
                                                $contador = 0;
                                                $html = '';
                                                foreach (config('data.comprobante')->detalles as $value) {

                                                    $html .= '<tr>
                                                    <td> <select disabled  name="codigo' . $contador . '" id="codigo' . $contador . '" class="form-control select2" style="width:100%"> 
                                                     <option value="">Seleccione</option>';
                                                    foreach (config('data.cuentas') as $values) {
                                                        $html .= '<option ' . ($values->id == $value->codigo ? "selected" : "") . ' value="' . $values->id . '"> ' . $values->plan . ' - ' . $values->nombre . '</option>';
                                                    }
                                                    $html .= '</select>';
                                                    $html .= '<input type="hidden" name="visibilidad' . $contador . '" id="visibilidad' . $contador . '" value="1"> </td>';
                                                    $html .= '<td style="text-align: center;"> <input disabled style="width:100%" type="text" name="debe' . $contador . '" id="debe' . $contador . '" class="form-control" value="' . (!is_null($value->debe) ? $value->debe  : '') . '" onchange="envalor_base(' . $contador . ')"> </td>';
                                                    $html .= '<td style="text-align: center;">';
                                                    $html .= '<input disabled style="width:100%" class="form-control" type="text" id="haber' . $contador . '" name="haber' . $contador . '" value="0.00">';
                                                    $html .= '</td>';
                                                    $html .= '<td style="text-align: center;"><input disabled style=" width: 79%; height: 80%" class="form-control" id="valor_base' . $contador . '" name="valor_base' . $contador . '" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();"></td>';
                                                    $html .= '</tr>';

                                                    $contador++;
                                                }

                                                echo $html;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if(!isset(config('data.comprobante')->id))
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="font-normal" for="debe">Debe</label>
                                        <input type="text" class="form-control" id="debe_final" readonly value="0.00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="font-normal" for="haber">Haber</label>
                                        <input type="text" class="form-control" id="haber_final" readonly value="0.00">
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div style="margin-top: 2%">
                            </div>
                            <div style="display: flex;justify-content:start">
                                <div class="col-md-10">
                                    <label class="font-normal" for="nota">Nota</label>
                                    <textarea id="nota" name="nota" class="form-control" rows="5">{{ isset(config('data.comprobante')->nota) ? config('data.comprobante')->nota : '' }}</textarea>
                                </div>
                            </div>
                            <div style="margin-top: 2%">
                            </div>
                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="goBack('comprobantedeegresovario')"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar</button>
                                <?php
                                if (config('data.comprobante') == '') {
                                    echo '<button type="button" class="btn btn-success" id="btnguardarComprobanteEgresoVarios"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>';
                                }else if(config("data.comprobante")->estado !== 0){
                                    echo '<button type="button" class="btn btn-success" id="btnactualizarComprobanteEgresoVarios"><i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar</button>';
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>