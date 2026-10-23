<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{ trans('v3/new_menu.inicio') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('v3/new_submodulo.' . config('data.submodulo')) }}</li>
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
                        <h3 class="card-title">{{  config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarCruceCuenta();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-contenido">
                        <table id="tableCruceCuentas" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.identificacion')}}</th>
                                    <th>{{trans('v3/new_tabla.cliente')}}</th>
                                    <th>{{trans('v3/new_tabla.No. Factura')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.valor')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor:')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.identificacion')}}</th>
                                    <th>{{trans('v3/new_tabla.cliente')}}</th>
                                    <th>{{trans('v3/new_tabla.No. Factura')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.valor')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor:')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveCruceCuentas" />
                    <input type="hidden" id="d" name="d" />
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
                            <input type="text" class="form-control" id="tipo" name="tipo" value="CR-NEW" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Asiento</label>
                            <input type="text" class="form-control text-right" id="asiento" name="asiento" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Fecha de Cruce</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" readonly />
                        </div>
                    </div>

                    <div class="row hero-cliente"> 
                        <div class="col-md-6">
                            <label class="font-normal">Factura</label>
                            <input type="text" class="form-control" id="comprobanteCrucec" name="comprobanteCrucec" readonly/>
                        </div>
                        <div class="col-md-3">
                            <label class="font-normal">Valor</label>
                            <input type="text" class="form-control text-right" id="info" name="info" readonly/>
                        </div>
                        <div class="col-md-3">
                            <label class="font-normal">Fecha de Factura</label>
                            <input type="date" class="form-control" id="fecha_fact" name="fecha_fact" readonly/>
                        </div>
                    </div>
                    <div class="row hero-cliente"> 
                        <div class="col-md-6">
                            <label class="font-normal">Concepto</label>
                            <input type="text" class="form-control" id="concepto" name="concepto" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label class="font-normal">Cliente</label>
                            <input type="text" class="form-control" id="clientecc" name="clientecc" readonly/>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-md-12" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered table-hover dataTables-example " width="100%">
                                <thead>
                                    <tr style="background-color: #F1F1F1">
                                        <th colspan="5" style="text-align: center;">Detalle de cuentas</th>
                                    </tr>
                                    <tr style="background-color: #F1F1F1">
                                        <th style="text-align: center; width: 30%;">Codigo</th>
                                        <th style="text-align: center; width: 40%;">Detalle</th>
                                        <th style="text-align: center; width: 10%;">Divisa</th>
                                        <th style="text-align: center; width: 10%;">Valor</th>
                                        <th style="text-align: center; width: 10%;">Total Base</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaDetalles">
                                </tbody>
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
                                                <td><input type="text" class="form-control text-right" value="0.00" id="impuesto" name="impuesto" readonly/></td> -->
                                                <td><input type="text" class="form-control text-right" value="0.00" readonly id="total" name="total" readonly/></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div><br><br>
                    <div style="margin-top: 2%">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')
