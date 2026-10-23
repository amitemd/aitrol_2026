<div class="modal fade" id="modal_clientes" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="bodyhtml"></div>
</div>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.strtolower(config('data.submodulo')))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">{{trans('v3/new_tabla.inicio')}}</a></li>
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
                    <input type="hidden" id="idMes" value="<?= config('data.idMes') != null ? config('data.idMes') : ''; ?>" />
                    <form id="frm_" method="post">
                        <div class="card-header">
                            <h3 class="card-title">{{ config('data.titulo') }}</h3>
                            <div>
                                <div style="position: absolute; right: 5px;top:7px">
                                    <a href="javascript:;" class="btn btn-danger btn-sm" onclick="goBack('conciliacion');"><i class="fas fa-undo"></i> <span class="botones">Volver</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body hero-factura">
                            <div class="ibox-content">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveConciliacion" />
                                <input type="hidden" id="d" name="d" />
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-info">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.buscador')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.institucion')}}</label>
                                                        <input type="hidden" id="id_caja_banco" value="<?= config('data.idMes') != null ? config('data.parametros')->id_caja_banco . '|' . config('data.parametros')->nombre . '|' . config('data.parametros')->numero_cuenta . '|' . config('data.parametros')->anio . '|' . config('data.parametros')->mes : ''; ?>" />
                                                        <select class="form-control select2" style="width: 100%;" id="instituciones" name="instituciones">
                                                            <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="font-normal">{{trans('v3/new_varios.periodo')}}</label>
                                                        <input type="month" class="form-control" id="fecha_periodo" name="fecha_periodo" value="" placeholder="aaaa-mm" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-normal">&nbsp;</label>
                                                        <div class="input-group mb-12">
                                                            <a href="javascript:;" class="btn btn-primary" onclick="buscarDatosConciliacion()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>&nbsp;&nbsp;
                                                            <a href="javascript:;" class="btn btn-danger ocultar" onclick="pdfDatosConciliacion()" style="display: none;"><i class="fas fa-file-pdf"></i> PDF</a>&nbsp;&nbsp;
                                                            <a href="javascript:;" class="btn btn-success ocultar" onclick="excelDatosConciliacion()" style="display: none;"><i class="fas fa-file-excel"></i> Excel</a>&nbsp;&nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosdepositos">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_varios.periodo')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="htmlPendientes"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosresumen">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.saldoLibros')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.saldoanterior')}}</label>
                                                        <input type="text" id="saldoAnteriorResumenLibros" name="saldoAnteriorResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.depositos')}}</label>
                                                        <input type="text" id="depositoResumenLibros" name="depositoResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.valoresacreditados')}}</label>
                                                        <input type="text" id="acreditadosResumenLibros" name="acreditadosResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.chequesgirados')}}</label>
                                                        <input type="text" id="chequesGiradosResumenLibros" name="chequesGiradosResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.valoresdebitados')}}</label>
                                                        <input type="text" id="debitadosResumenLibros" name="debitadosResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.saldoactual')}}</label>
                                                        <input type="text" id="saldoActualResumenLibros" name="saldoActualResumenLibros" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosresumen">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.saldoBancos')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.saldoanterior')}}</label>
                                                        <input type="text" id="saldoAnteriorResumenBancos" name="saldoAnteriorResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.depositos')}}</label>
                                                        <input type="text" id="depositoResumenBancos" name="depositoResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.valoresacreditados')}}</label>
                                                        <input type="text" id="acreditadosResumenBancos" name="acreditadosResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.chequesgirados')}}</label>
                                                        <input type="text" id="chequesGiradosResumenBancos" name="chequesGiradosResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.valoresdebitados')}}</label>
                                                        <input type="text" id="debitadosResumenBancos" name="debitadosResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="font-normal">{{trans('v3/new_titulos.saldoactual')}}</label>
                                                        <input type="text" id="saldoActualResumenBancos" name="saldoActualResumenBancos" class="form-control text-right" value="0.00" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosfactura">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.informaciongeneral')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-9" style="text-align: center;">
                                                        <table style="width: 80%;text-align: center;">
                                                            <tr>
                                                                <td style="text-align: center;" colspan="2">
                                                                    <table style="width: 100%; text-align: center;">
                                                                        <tr>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;display: flex;gap: 10px;justify-content: center;align-items: center;" id="">$ <input type="text" style="width: 150px;font-size: 20px;" id="lblSaldoInicial" onblur="calcularDatosConciliacion();" class="form-control text-right" name="" readonly value="0.00" /></td>
                                                                            <td>&nbsp;</td>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;" id="lblTotalEgresos">$ 0.00</td>
                                                                            <td>&nbsp;</td>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;" id="lblTotalIngresos">$ 0.00</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center;font-size: 18px;font: bold;">{{trans('v3/new_tabla.saldoinicial')}}</td>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;">-</td>
                                                                            <td style="text-align: center;font-size: 18px;font: bold;">{{trans('v3/new_tabla.egresos')}}</td>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;">+</td>
                                                                            <td style="text-align: center;font-size: 18px;font: bold;">{{trans('v3/new_tabla.ingresos')}}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <table style="width: 100%;">
                                                            <tr>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-primary btn-xl dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                            <i class="far fa-save"></i>{{trans('v3/new_tabla.guardarParaMasTarde')}}
                                                                        </button>
                                                                        <div class="dropdown-menu" style="font-size: 20px;">
                                                                            <a class="dropdown-item" href="javascript:;" onclick="finalizarConciliacion()"><i class="fas fa-stopwatch"></i>{{trans('v3/new_tabla.finalizar')}}</a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="text-align: center;">
                                                                    <table style="width: 100%;">
                                                                        <tr>
                                                                            <td style="text-align: center;font-size: 30px;font: bold;"><i class="fas fa-exclamation-triangle fa-2x" style="color: orange;"></i><span id="lblDiferencia"> $ 0.00<span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center;font-size: 18px;text-decoration: underline;text-decoration-style: dotted">{{trans('v3/new_tabla.diferencia')}}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosdepositos">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.depositos')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="depositos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosvaloresacreditados">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-success">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.transferenciasacreditadas')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="transferenciasacreditadas"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosvaloresdebitados">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-warning">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.transferenciasadebitadas')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="transferenciasadebitadas"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosdebitos">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.notasdedebito')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="debitos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datoscredito">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.notasdecredito')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="creditos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosdebitosbancarios">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.debitosbancarios')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="debitoBancario"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosegresos">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.egresos')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="egresos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosegresosvarios">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.egresosvarios')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="egresosVarios"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-datosegrsosmasivos">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.egresosmasivos')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="egresosMasivos"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hero-pendientesagregados">
                                    <div class="col-md-12">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header">
                                                <h3 class="card-title">{{trans('v3/new_titulos.pendientesAgregados')}}</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row" id="htmlPendientesAgregados"></div>
                                            </div>
                                        </div>
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
<div class="modal fade modalTotal" id="myModalPendientes">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="tab-content" id="custom-content-above-tabContent">
                            <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                    <div class="row">
                                        <div class="col-12 col-sm-12" id="contenidoPendientes"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
            </div>
        </div>
    </div>
</div>