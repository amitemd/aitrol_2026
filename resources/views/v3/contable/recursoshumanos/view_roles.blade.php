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
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{trans('v3/new_tabla.buscador')}}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.desde')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="desde" name="desde" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>{{trans('v3/new_tabla.hasta')}}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="date" id="hasta" name="hasta" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 col-sm-6">
                                <label>Año</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="anio" name="anio">
                                        <option value="">--Seleccione--</option>
                                        <?php
                                        $inicio = date('Y') - 10;
                                        for ($i = date('Y'); $i > $inicio; $i--) {
                                            $select = '';
                                            echo '<option value="' . $i . '" ' . $select . '>' . $i . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 col-sm-6">
                                <label>Mes</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="mes" name="mes">
                                        <option value="">--Seleccione--</option>
                                        <?php
                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                        for ($j = 0; $j < sizeof($meses); $j++) {
                                            $numMes = $j + 1;
                                            $select = '';
                                            if (isset(config('data.input')['mes']) && config('data.input')['mes'] == $numMes) {
                                                $select = 'selected';
                                            }
                                            echo '<option value=' . $numMes . ' ' . $select . '>' . $meses[$j] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label>{{trans('v3/new_tabla.estado')}}</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="estado_" name="estado">
                                        <option value="">--Seleccione--</option>
                                        <option value="1">{{trans('v3/new_tabla.activos')}}</option>
                                        <option value="0">{{trans('v3/new_tabla.inactivos')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label>&nbsp;</label>
                                <div class="input-group mb-3">
                                    <a href="javascript:;" class="btn btn-primary" onclick="buscarVentasNetas()"><i class="fas fa-search"></i> <span class="botones">{{trans('v3/new_tabla.buscar')}}</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{config('data.titulo')}}</h3>
                    <div>
                        <div style="position: absolute; right: 5px;top:7px">
                            <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarRol();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                    <table id="tableRoles" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Año/Mes</th>
                                <th>Empleado</th>
                                <th>FR</th>
                                <th>Sueldo</th>
                                <th>Extra 50%</th>
                                <th>Extra 100%</th>
                                <th>T. ingreso</th>
                                <th>Prestamos</th>
                                <th>Saldo</th>
                                <th>Quirografario</th>
                                <th>Hipotecario</th>
                                <th>T. egreso</th>
                                <th>Neto</th>
                                <th>Cert</th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Empresa</th>
                                <th>Año/Mes</th>
                                <th>Empleado</th>
                                <th>FR</th>
                                <th>Sueldo</th>
                                <th>Extra 50%</th>
                                <th>Extra 100%</th>
                                <th>T. ingreso</th>
                                <th>Prestamos</th>
                                <th>Saldo</th>
                                <th>Quirografario</th>
                                <th>Hipotecario</th>
                                <th>T. egreso</th>
                                <th>Neto</th>
                                <th>Cert</th>
                                <th>...</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="id_empresa" name="id_empresa" value="{{session('id_empresa')}}">
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveConfiguracion">
                    <div class="card card-default">
                        <div class="card-body">
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Tipo</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                                        </div>
                                                        <select name="tipo" id="tipo" class="form-control select2" style="width: 98%;">
                                                            <option value="">-- Seleccione --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Valor</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                                        </div>
                                                        <input type="text" id="valor" name="valor" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="cheestado">
                                                        <label class="custom-control-label" for="cheestado">{{trans('v3/new_tabla.estado')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
                <button type="button" class="btn btn-primary" id="btnGuardarConfiguracion"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>