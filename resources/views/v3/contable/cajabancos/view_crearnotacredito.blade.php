<style>
    .center {
        text-align: center
    }

    .colorselect {
        background-color: #A5A4A4 !important;
        color: white;
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.titulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_varios.inicio') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.titulo'))) }}</li>
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
                    <div class="card-body hero-factura">
                        <form id="frm_depositoBancario">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id"
                                value="{{ isset(config('data.registroGu')->id) ? config('data.registroGu')->id : '' }}">
                            <input type="hidden" id="action"
                                value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarDepositoBancario" />
                            <input type="hidden" id="d" name="d" />
                            <div class="row hero-datosfactura">
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.tipo')}}</label>
                                    <input type="text" class="form-control" value="BAN-DP" readonly name="tipo"
                                        id="tipo">
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.fecha')}}</label>
                                    <input type="date" class="form-control" name="fecha_asiento" id="fecha_asiento"
                                        value="{{ isset(config('data.registroGu')->fecha_asiento) ? config('data.registroGu')->fecha_asiento : date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal">{{trans('v3/new_tabla.concepto')}}</label>
                                    <input type="text" class="form-control" name="concepto" id="concepto"
                                        value="{{ isset(config('data.registroGu')->concepto) ? config('data.registroGu')->concepto : '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="font-normal">{{trans('v3/new_tabla.sucursal')}}</label>
                                    <select class="form-control select2" style="width: 100%;" id="sucursal_empresa"
                                        name="sucursal_empresa">
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
                                    <select class="form-control select2" style="width: 100%;" id="punto_emision"
                                        name="punto_emision">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div style="margin-top: 2%;">
                            </div>
                            <div class="row hero-datosfactura">
                                <div class="col-md-3">
                                    <label for="id_banco" class="font-normal">{{trans('v3/new_tabla.banco')}}</label>
                                    <select class="form-control select2" style="width: 100%" name="id_banco"
                                        id="id_banco">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        foreach (config('data.banco') as $row) {
                                            echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-normal" for="divisa">{{trans('v3/new_tabla.divisa')}}</label>
                                    <select class="form-control select2" name="divisa" id="divisa">
                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                        <?php
                                        foreach (config('data.divisas') as $row) {
                                            echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <label for="cambio" class="font-normal">{{trans('v3/new_tabla.cambio')}}</label>
                                    <input type="text" class="form-control" name="cambio" id="cambio"
                                        onkeypress="return isNumber(event)">
                                </div>

                                <div class="col-md-3">
                                    <label for="valor" class="font-normal">{{trans('v3/new_tabla.valor')}}</label>
                                    <input type="text" class="form-control" name="valor" id="valor"
                                        onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <div style="margin-top: 2%;">
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: auto;">
                                    <table
                                        class="table table-striped table-bordered table-hover dataTables-example tablaDatos"
                                        width="100%">
                                        <thead>

                                            <th class="center">{{trans('v3/new_tabla.nombre')}}</th>
                                            <th class="center">{{trans('v3/new_tabla.cuentadebe')}}</th>
                                            <th class="center">{{trans('v3/new_tabla.cuentahaber')}}</th>
                                            <th class="center">
                                                <button onclick="nuevo()" type="button"
                                                    class="btn btn-success btn-xs">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>

                                        </thead>
                                        <tbody id="agregar_cuentas">
                                            <tr class="well">
                                                <td>
                                                    <select  name="nombre[]"
                                                        class="form-control select2" style="width:100%">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                        <?php
                                                        foreach (config('data.plan_cuentas') as $row) {
                                                            echo '<option data-name="' . $row->nombre . '" value="' . $row->id . '">' . $row->id . '-' . $row->nombre . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control input-sm debe" disabled="true"
                                                        type="text" onkeypress="return isNumber(event)"
                                                        value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="debe[]">
                                                </td>
                                                <td>
                                                    <input class="form-control input-sm haber" type="text"
                                                        onkeypress="return isNumber(event)" value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();"
                                                        name="haber[]">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger delete btn-xs">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <input type="hidden" value="0" name="contador">
                                            <tr style="display:none" id="mifila">
                                                <td>
                                                    <select name="nombre[]" class="form-control select2"
                                                        style="width:100%">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                        <?php
                                                        foreach (config('data.plan_cuentas') as $row) {
                                                            echo '<option data-name="' . $row->nombre . '" value="' . $row->id . '">' . $row->id . '-' . $row->nombre . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control debe" type="text"
                                                        onkeypress="return isNumber(event)" disabled="true"
                                                        value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);"
                                                        name="debe[]">
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text"
                                                        onkeypress="return isNumber(event)" value="0.00"
                                                        onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();"
                                                        name="haber[]">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger delete btn-xs">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="history.back()">
                                    <i class="fa fa-long-arrow-left" aria-hidden="true"></i>{{trans('v3/new_tabla.regresar')}}
                                </button>
                                <button type="button" class="btn btn-success" id="guardarDepositoBancario">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>{{trans('v3/new_tabla.guardar')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
