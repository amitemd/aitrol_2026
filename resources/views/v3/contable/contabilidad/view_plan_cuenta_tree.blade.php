<style>
    .contenedor-arbol,
    .contenedor-arbol ul {
        position: relative;
        margin: 0px;
        padding: 0px 0px 0px 10px;
    }

    .contenedor-arbol li {
        position: relative;
        margin: 0px;
        padding: 0px 0px 0px 0px;
    }

    .contenedor-arbol ul {
        list-style: none;
    }

    .contenedor-arbol li::before,
    .contenedor-arbol li::after {
        content: "";
        position: absolute;
        left: -12px;
    }

    .contenedor-arbol li::before {
        border-top: 2px solid green;
        top: 9px;
        width: 8px;
        height: 0;
    }

    .contenedor-arbol li::after {
        border-left: 2px solid brown;
        height: 100%;
        width: 0px;
        top: 2px;
    }

    .contenedor-arbol ul>li:last-child::after {
        height: 8px;
    }

    /* Cambiamos el cursor al hacer hover en los elementos con hijos */
    .contenedor-arbol li>span {
        cursor: pointer;
    }

    /* Ocultamos los elementos hijos por defecto */
    .contenedor-arbol ul.collapsed {
        display: none;
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('v3/admin')}}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</li>
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
                        <h3 class="card-title">{{config('data.titulo')}}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-success btn-sm" onclick="exportarPlan();"><i class="fas fa-file-excel"></i> <span class="botones">{{trans('v3/new_tabla.exportar')}}</span></a>
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarPlanCuenta();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <?php

                                use Sis_medico\v3\contable\PlanCuentas;

                                $rows = array();
                                foreach (config('data.plan') as $row) {
                                    $rows[] = $row;
                                }
                                $tree = PlanCuentas::buildTree($rows);
                                PlanCuentas::printTree($tree);
                                ?>
                            </div>
                            <div class="col-12 col-sm-8">
                                <table class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{trans('v3/new_varios.codigo')}}</th>
                                            <th>{{trans('v3/new_varios.nombre')}}</th>
                                            <th>{{trans('v3/new_varios.modificado')}}</th>
                                            <th>{{trans('v3/new_varios.creado')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoCuentas">
                                    </tbody>
                                </table>
                                <br />
                                <form id="frm_editar_plan">
                                    {{csrf_field()}}
                                    <input type="hidden" id="id_plan" name="id_plan" value="" />
                                    <input type="hidden" id="d" name="d" value="|">
                                    <input type="hidden" id="id" name="id">
                                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/modificarPlanCuenta">
                                    <div id="infoCuenta" class="card card-outline card-primary collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">{{trans('v3/new_varios.informacionDeLaCuenta')}}</h3>
                                            <div class="card-tools">
                                                <button type="button" id="btnExpandir" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered table-striped" width="100%">
                                                <tr>
                                                    <td>{{trans('v3/new_varios.codigo')}}</td>
                                                    <td><input type="text" id="plan" name="plan" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('v3/new_varios.nombre')}}</td>
                                                    <td><input type="text" id="nombre" name="nombre" class="form-control" style="text-transform: uppercase;" /></td>
                                                </tr>
                                                <tr>
                                                    <td>Tipo</td>
                                                    <td>
                                                        <select class="form-control" id="tipo" name="tipo">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="1">{{trans('v3/new_varios.grupo')}}</option>
                                                            <option value="2">{{trans('v3/new_varios.detalle')}}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('v3/new_varios.estado')}}</td>
                                                    <td>
                                                        <select class="form-control" id="estado" name="estado">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="3">{{trans('v3/new_varios.activo')}}</option>
                                                            <option value="0">{{trans('v3/new_varios.inactivo')}}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('v3/new_varios.naturaleza')}}</td>
                                                    <td>
                                                        <select class="form-control" id="naturaleza" name="naturaleza">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="1">{{trans('v3/new_varios.positivo')}}</option>
                                                            <option value="0">{{trans('v3/new_varios.negativo')}}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('v3/new_varios.calculaen')}}</td>
                                                    <td>
                                                        <select class="form-control" id="naturaleza_2" name="naturaleza_2">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="0">{{trans('v3/new_varios.cuentadebe')}}</option>
                                                            <option value="1">{{trans('v3/new_varios.cuentahaber')}}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('v3/new_varios.cierredeaño')}}</td>
                                                    <td>
                                                        <select class="form-control" id="estado_cierre_ano" name="estado_cierre_ano">
                                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                            <option value="1">{{trans('v3/new_varios.si')}}</option>
                                                            <option value="0">{{trans('v3/new_varios.no')}}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i>{{trans('v3/new_varios.cerrar')}}</button>&nbsp;&nbsp;
                                                        <button type="button" class="btn btn-primary" id="btnModificarPlan"><i class="fas fa-save"></i>{{trans('v3/new_varios.guardar')}}</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i>{{trans('v3/new_varios.editar')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_agregar_plan">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="action_" value="{{url('v3/')}}/{{config('data.controlador')}}/savePlanCuenta">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_varios.codigo')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="lblcodigo" name="lblcodigo" class="form-control text-right" placeholder="" readonly>
                                            <input type="text" id="txtcodigo" name="txtcodigo" class="form-control" placeholder="XX.XX.XX">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_varios.nombre')}}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                            </div>
                                            <input type="text" id="txtnombre" name="txtnombre" class="form-control" style="text-transform:uppercase" placeholder="Nombre de la cuenta">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_varios.tipo')}}</label>
                                        <div class="input-group mb-3">
                                            <select class="form-control" id="cmbtipo" name="cmbtipo">
                                                <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                                <option value="1">{{trans('v3/new_varios.grupo')}}</option>
                                                <option value="2">{{trans('v3/new_varios.detalle')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_varios.calculaen')}}</label>
                                        <select class="form-control" id="cmbnaturaleza_2" name="cmbnaturaleza_2">
                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                            <option value="0">{{trans('v3/new_varios.cuentadebe')}}</option>
                                            <option value="1">{{trans('v3/new_varios.cuentahaber')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>{{trans('v3/new_varios.cierredeaño')}}</label>
                                        <select class="form-control" id="cmbcierreanio" name="cmbcierreanio">
                                            <option value="">{{trans('v3/new_varios.seleccione')}}</option>
                                            <option value="1">{{trans('v3/new_varios.si')}}</option>
                                            <option value="0" selected>{{trans('v3/new_varios.no')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i>{{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPlan"><i class="fas fa-save"></i>{{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>