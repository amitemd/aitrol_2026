<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('v3/new_submodulo.'.(config('data.submodulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.'.(config('data.submodulo'))) }}</li>
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
                    </div>
                    <div class="card-body">
                        <form id="frm_">
                            {{ csrf_field() }}
                            <input type="hidden" id="d" name="d" value="|" />
                            <input type="hidden" id="id" name="id" value="" />
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveNotaDebito" />
                            <input type="hidden" id="cont" value="0" />
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.id')}}</label>
                                        <input type="text" class="form-control" id="id" name="id" disabled readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.tipo')}}</label>
                                        <input type="text" class="form-control" id="tipo_c" name="tipo_c" readonly value="BAN-ND" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.asiento')}}</label>
                                        <input type="text" class="form-control" id="id_asiento" name="id_asiento" disabled readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                        <input type="text" class="form-control" id="concepto_c" name="concepto_c" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.banco')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="id_banco" name="id_banco">
                                            <option value="">{{trans('v3/new_tabla.--seleccione--')}}</option>
                                            <?php
                                            if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                foreach (config('data.bancos') as $value) {
                                                    echo '<option value="' . $value->id . '" >' . $value->nombre . '</option>';
                                                }
                                            } //
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_varios.divisa')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="id_divisa" name="id_divisa">
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
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.cambio')}}</label>
                                        <input type="text" class="form-control" id="cambio" name="cambio" value="0.00" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.valor')}}</label>
                                        <input type="text" class="form-control" id="valor" name="valor" />
                                    </div>
                                    <div class="col-md-2">
                                        <br>
                                        <a type="button" onclick="aplicarDeuda();" class="btn btn-primary">{{trans('v3/new_tabla.aplicarDeuda')}}</a>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_varios.acreedor')}}</label>
                                        <select class="form-control select2_provi" onchange="cargarDeudas();" style="width: 100%;" id="id_acreedor" name="id_acreedor">
                                            <option value="">{{trans('v3/new_varios.seleccionar')}}</option>
                                            <?php
                                            if (config('data.proveedores') != '' && count(config('data.proveedores')) > 0) {
                                                foreach (config('data.proveedores') as $value) {
                                                    $select = '';
                                                    echo '<option value="' . $value->id . '" ' . $select . '>' . $value->descripcion . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <label class="label_header text-left">{{trans('v3/new_tabla.detallededeudasconelproveedor')}}</label>
                                        </div>
                                        <div class="table-responsive col-md-12" style="max-height: 250px; ">
                                            <input type="hidden" name="contador" id="contador" value="0" />
                                            <table id="example2" class="table table-striped table-bordered table-hover" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                                <thead style="position: sticky;top: 0;z-index: 2;background-color: #f9f9f9;">
                                                    <tr>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.vence')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.tipo')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.numero')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.concepto')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.div')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.saldo')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.abono')}}</th>
                                                        <th style="text-align: center;">{{trans('v3/new_tabla.saldoBase')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detalleDeuda">

                                                </tbody>
                                                <tfoot>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="col-md-12 mt-2">
                                        <input type="hidden" name="contador" id="contador" value="0" />
                                        <table id="example2" class="table table-striped table-bordered table-hover" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.totalDebito')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.debitoaplicado')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.totaldeudas')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.totalabonos')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.nuevosaldo')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.deficit')}}</th>
                                                    <th style="text-align: center;">{{trans('v3/new_tabla.debitoaFavor')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleDeuda2">
                                                <tr>
                                                    <td>
                                                        <input type="text" id="total_debito" name="total_debito" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="debito_aplicado" name="debito_aplicado" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="comprobarx" value="2">
                                                        <input type="text" id="total_deudas" name="total_deudas"class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="total_abono" name="total_abono" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="nuevo_saldo" name="nuevo_saldo" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="deficit" name="deficit" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="debito_favor" id="debito_favor" class="form-control text-right" value="0.00" readonly>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group col-xs-12 px-1">
                                        <div class="col-md-12 px-0 t8">
                                            <label for="nota" class="label_header">{{trans('v3/new_tabla.nota')}}</label>
                                        </div>
                                        <div class="col-md-12 px-0">
                                            <textarea class="col-md-12" name="nota" id="nota" cols="200" rows="5"></textarea>
                                            <input type="hidden" id="saldo_final">
                                            <input type="hidden" id="proveedor">
                                            <input type="hidden" id="sobrante">
                                            <input type="hidden" id="total_favor">
                                            <input type="hidden" id="superavit">
                                            <input type="hidden" id="egreso_retenciones">
                                        </div>
                                        <input type="hidden" id="total_suma">
                                    </div>
                                </div>
                                <a href="javascript:;" class="btn btn-danger" onclick="window.history.back();"><i class="fa-solid fa-circle-xmark"></i>{{trans('v3/new_tabla.cancelar')}}</a>
                                <a href="javascript:;" class="btn btn-success" id="btnguardarNotaDebito"><i class="fa-solid fa-save"></i>{{trans('v3/new_tabla.guardar')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>