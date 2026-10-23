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
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarCompIngresoVarios();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-contenido">
                        <table id="tableCompIngresoVarios" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.id')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.secuencia')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.asiento')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.fecha')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.banco')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.detalle')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.total')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.creadopor:')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.estado:')}}</th>
                                    <th style="text-align: center;">...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.id')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.secuencia')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.asiento')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.fecha')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.banco')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.detalle')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.total')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.creadopor:')}}</th>
                                    <th style="text-align: center;">{{trans('v3/new_tabla.estado:')}}</th>
                                    <th style="text-align: center;">...</th>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal_comIngVar"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{ csrf_field() }}
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
                            <input type="text" class="form-control" id="tipo" name="tipo" value="BAN-IN" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Asiento</label>
                            <input type="text" class="form-control text-right" id="asiento" name="asiento" readonly/>
                        </div>
                        <div class="col-md-4">
                            <label class="font-normal">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" readonly/>
                        </div>
                        <!-- <div class="col-md-2">
                            <label class="font-normal">Establecimiento</label>
                            <input type="text" class="form-control text-right" id="sucursal" name="sucursal" readonly/>
                        </div> -->
                        <div class="col-md-8">
                            <label class="font-normal">Concepto</label>
                            <input type="text" class="form-control" id="concepto" name="concepto" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Caja/Banco</label>
                            <input type="text" class="form-control text-right" value="0.00" id="id_banco" name="id_banco" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Divisas</label>
                            <input type="text" class="form-control" id="divisas" name="divisas" readonly/>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered table-hover dataTables-example " width="100%">
                                <thead>
                                    <tr style="background-color: #F1F1F1">  
                                        <th colspan="10" style="text-align: center; width: 30%;">Detalles de valores recibidos</th>
                                    </tr>
                                    <tr style="background-color: #F1F1F1">
                                        <th style="text-align: center; width: 11%;">Tipo</th>
                                        <th style="text-align: center; width: 8%;">Fecha</th>
                                        <th style="text-align: center; width: 10%;">Referencia</th>
                                        <th style="text-align: center; width: 15%;">Banco</th>
                                        <th style="text-align: center; width: 14%;">Tarjeta</th>
                                        <th style="text-align: center; width: 10%;">Cuenta</th>
                                        <th style="text-align: center; width: 13%;">Girador</th>
                                        <th style="text-align: center; width: 10%;">Valor</th>
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
                                                <td><input type="text" class="form-control text-right" value="0.00" readonly id="total" name="total" readonly/></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div><br><br>
                    <div class="row">
                        <div class="col-md-12" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered table-hover dataTables-example" width="100%">
                                <thead>
                                    <tr style="background-color: #F1F1F1">
                                        <th colspan="9" style="text-align: center; width: 30%;">Detalle del complemento contable</th>
                                    </tr>
                                    <tr style="background-color: #F1F1F1">
                                        <th style="text-align: center; width: 50%;">Codigo</th>
                                        <th style="text-align: center; width: 15%;">Divisa</th>
                                        <th style="text-align: center; width: 15%;">Debe</th>
                                        <th style="text-align: center; width: 15%;">Haber</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleDeudaCliente">
                                </tbody>
                            </table>
                        </div>
                    </div><br><br>
                    <div class="row">
                        <div class='col-md-12' style='text-align: left'>
                            <input type="text" class="form-control" value="" id="nota" name="nota" readonly/>
                        </div>
                    </div>
                    <br>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')