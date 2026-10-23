<style>
.tooltips {
    background-color: var(--color-dark);
    color: var(--color-blue);
    font-family: sans-serif;
    border-radius: 10px;
    padding: 40px;
    font-size: 16px;
    line-height: 32px;
    box-shadow: 5px 5px 50px rgba(0,0,0,.10);
}

.tooltip {
    font-weight: normal;
    color: var(--tooltip);
    text-decoration: none;
    text-align: center;
    position: relative;
}

.tooltip:hover {
    text-decoration: underline;
}

.tooltip:hover .tooltip__box {
    display: inline-block;
}

.tooltip__box {
    display: none;
    position: absolute;
    background-color: var(--tooltip);
    line-height: 20px;
    z-index: 500;
    color: var(--color-dark);
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 5px;
    left: -80px;
    right: 0;
    bottom: 30px;
    width: 200px;
}

.tooltip__box-v {
    left: -3px;
}

.tooltip__box:after {
    content: '';
    display: block;
    border-top: 8px solid var(--tooltip);
    border-left: 7px solid transparent;
    border-right: 7px solid transparent;
    position: absolute;
    bottom: -7px;
    left: calc(71% - 7px);
}
</style>
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
                                <a href="javascript:;" class="btn btn-primary btn-sm hero-agregar" onclick="agregarCruceValores();"><i class="fas fa-plus-circle"></i> <span class="botones">{{ trans('v3/new_tabla.agregar') }}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body hero-contenido">
                        <table id="tableCruceValores" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.identificacion')}}</th>
                                    <th>{{trans('v3/new_tabla.cliente')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
                                    <th>{{trans('v3/new_tabla.creadopor:')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('v3/new_tabla.id')}}</th>
                                    <th>{{trans('v3/new_tabla.fecha')}}</th>
                                    <th>{{trans('v3/new_tabla.asiento')}}</th>
                                    <th>{{trans('v3/new_tabla.identificacion')}}</th>
                                    <th>{{trans('v3/new_tabla.cliente')}}</th>
                                    <th>{{trans('v3/new_tabla.concepto')}}</th>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"></h4>
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
                        <div class="col-md-3">
                            <label class="font-normal">Número</label>
                            <input type="text" class="form-control text-right" id="numero" name="numero" readonly />
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Tipo</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" value="CLI-CR-AF" readonly/>
                        </div>
                        <div class="col-md-2">
                            <label class="font-normal">Asiento</label>
                            <input type="text" class="form-control text-right" id="asiento" name="asiento" readonly/>
                        </div>
                        <div class="col-md-3">
                            <label class="font-normal">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" readonly/>
                        </div>
                    </div>
                    <div class="row hero-cliente"> 
                        <div class="col-md-6">
                            <label class="font-normal">Cliente</label>
                            <input type="text" class="form-control" id="id_cliente1" name="id_cliente1" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label class="font-normal">Concepto</label>
                            <input type="text" class="form-control" id="concepto" name="concepto" readonly/>
                        </div>
                    </div> 
                    <br>
                    <div class="row">
                        <div class="col-md-12" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered table-hover dataTables-example " width="100%">
                                <thead>
                                    <tr style="background-color: #F1F1F1">  
                                        <th colspan="10" style="text-align: center; width: 30%;">Detalles de valores a favor</th>
                                    </tr>
                                    <tr style="background-color: #F1F1F1">
                                    <th style="text-align: center; width: 3%;">Fecha</th>
                                    <th style="text-align: center; width: 2%;">Tipo</th>
                                    <th style="text-align: center; width: 3%;">Número</th>
                                    <th style="text-align: center; width: 20%;">Concepto</th>
                                    <th style="text-align: center; width: 1%;">Div</th>
                                    <th style="text-align: center; width: 4%;">Saldo</th>
                                    <th style="text-align: center; width: 4%;">Abono</th>
                                    <th style="text-align: center; width: 4%;">Nuevo saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleValoresaFavor"></tbody>
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
                                        <th colspan="9" style="text-align: center; width: 30%;">Detalle de deudas del cliente</th>
                                    </tr>
                                    <tr style="background-color: #F1F1F1">
                                        <th style="text-align: center; width: 3%;">Fecha</th>
                                        <th style="text-align: center; width: 2%;">Tipo</th>
                                        <th style="text-align: center; width: 3%;">Número</th>
                                        <th style="text-align: center; width: 20%;">Concepto</th>
                                        <th style="text-align: center; width: 1%;">DIV</th>
                                        <th style="text-align: center; width: 4%;">Saldo</th>
                                        <th style="text-align: center; width: 4%;">Abono</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleDeudaClientecvalores">
                                </tbody>
                            </table>
                        </div>
                    </div><br><br>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('v3.contable.contabilidad.view_visualizar_asiento')
