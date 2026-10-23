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
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveNotaDebitoCajaBanco" />
                            <input type="hidden" id="cont" value="0" />
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="font-normal">Sucursal
                                        </label>
                                        <select class="form-control select2" style="width: 100%;" id="sucursal_empresa" name="sucursal_empresa">
                                            <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                            <?php
                                            if (config('data.sucursales') != '' && count(config('data.sucursales')) > 0) {
                                                foreach (config('data.sucursales') as $value) {
                                                    echo '<option value="' . $value->id . '">' . $value->codigo_sucursal . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.p.emision(empresa)')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                            <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                        <input type="text" class="form-control" id="concepto" name="concepto" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-normal">Beneficiario</label>
                                        <input type="text" class="form-control" id="beneficiario" name="beneficiario" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="font-normal">{{trans('v3/new_tabla.banco')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="id_banco" name="id_banco">
                                            <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                            <?php
                                            if (config('data.bancos') != '' && count(config('data.bancos')) > 0) {
                                                foreach (config('data.bancos') as $value) {
                                                    echo '<option value="' . $value->id . '" >' . $value->nombre . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="font-normal">{{trans('v3/new_varios.divisa')}}</label>
                                        <select class="form-control select2" style="width: 100%;" id="id_divisa" name="id_divisa">
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
                                    <div class="col-md-4">
                                        <label class="font-normal">{{trans('v3/new_tabla.valor')}}</label>
                                        <input type="text" class="form-control" id="valor_total" name="valor_total" readonly />
                                    </div>
                                </div>
                                <br>
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered table-hover dataTables-example tablaDatos" width="100%">
                                        <thead>
                                            <tr style="background-color: #F1F1F1">
                                                <th colspan="10" style="text-align: center; width: 30%;">Detalles de valores</th>
                                            </tr>
                                            <tr style="background-color: #F1F1F1">
                                                <th style="text-align: center; min-width: 87%;">Nombre</th>
                                                <th style="text-align: center; min-width: 10%;">Valor</th>
                                                <th style="text-align: center; min-width: 3%;" id="agregarlineaNotadeDebitoCajaBanco"><a href="javascript:;" class="btn btn-success"><i class="fa-solid fa-circle-plus"></i></a></th>
                                            </tr>
                                        </thead>
                                        <tbody id="detalleNotadeDebitoCajaBanco"></tbody>
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
                                                        <td><input type="text" class="form-control text-right" value="0.00" readonly id="total_detalle" name="total_detalle" readonly /></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-group col-xs-12 px-1">
                                    <div class="col-md-12 px-0 t8">
                                        <label for="nota" class="label_header">{{trans('v3/new_tabla.observacion')}}</label>
                                    </div>
                                    <div class="col-md-12 px-0">
                                        <textarea class="col-md-12" name="observacion" id="observacion" cols="200" rows="5"></textarea>
                                    </div>
                                    <input type="hidden" id="total_suma">
                                </div>
                            </div>
                            <div style='text-align: center; margin-bottom: 2%'>
                                <button type="button" class="btn btn-danger" onclick="history.back()">
                                    <i class="fa fa-long-arrow-left" aria-hidden="true"></i> {{trans('v3/new_tabla.cancelar')}}
                                </button>
                                <a href="javascript:;" class="btn btn-success" id="btnguardarNotaDebitoCajaBanco"><i class="fa-solid fa-save"></i> {{trans('v3/new_tabla.guardar')}}</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>