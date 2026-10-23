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
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarTransportista();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="id_" value="{{config('data.id_proveedor')}}">
                        <table id="tableTransportistas" class="table table-bordered table-striped tablaDatos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th> {{trans('v3/new_tabla.identificacion')}}</th>
                                    <th> {{trans('v3/new_tabla.razonsocial')}}</th>
                                    <th> {{trans('v3/new_tabla.nombrecomercial')}}</th>
                                    <th> {{trans('v3/new_tabla.ciudad')}} </th>
                                    <th> {{trans('v3/new_tabla.tipodocumento')}} </th>
                                    <th> {{trans('v3/new_tabla.rise')}} </th>
                                    <th> {{trans('v3/new_tabla.contabilidad')}} </th>
                                    <th> {{trans('v3/new_tabla.contribuyenteespecial')}}</th>
                                    <th>{{trans('v3/new_tabla.estado')}}</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> {{trans('v3/new_tabla.identificacion')}}</th>
                                    <th> {{trans('v3/new_tabla.razonsocial')}}</th>
                                    <th> {{trans('v3/new_tabla.nombrecomercial')}}</th>
                                    <th> {{trans('v3/new_tabla.ciudad')}} </th>
                                    <th> {{trans('v3/new_tabla.tipodocumento')}} </th>
                                    <th> {{trans('v3/new_tabla.rise')}} </th>
                                    <th> {{trans('v3/new_tabla.contabilidad')}} </th>
                                    <th> {{trans('v3/new_tabla.contribuyenteespecial')}}</th>
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

                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveTransportista">
                    <div class="card card-default">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-content-above-general-tab" data-toggle="pill" href="#custom-content-above-general" role="tab" aria-controls="custom-content-above-general" aria-selected="true"><i class="fas fa-user"></i> <span class="botones">General</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-direccion-tab" data-toggle="pill" href="#custom-content-above-direccion" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="fas fa-map-marked-alt"></i> <span class="botones">Ubicación</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-bank-tab" data-toggle="pill" href="#custom-content-above-bank" role="tab" aria-controls="custom-content-above-bank" aria-selected="false"><i class="fas fa-receipt"></i> <span class="botones">Info tributaria</span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.tipodocumento')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select name="tipo_documento" id="tipo_documento" class="form-control select2" style="width: 100%;">
                                                            <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                            <?php
                                                            foreach (config('data.tipos') as $row) {
                                                                echo '<option value="' . $row->valor . '">' . $row->catalogo . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.identificacion')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                                        </div>
                                                        <input type="text" id="ci_ruc" name="ci_ruc" maxlength="13" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Número de Identificación" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.razonSocial')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="Razón Social de la empresa">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.nombreComercial')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombrecomercial" name="nombrecomercial" class="form-control" placeholder="Nombre Comercial de la empresa">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.email')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                        </div>
                                                        <input type="email" id="email" name="email" class="form-control" placeholder="Email del transportista">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Web</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fab fa-internet-explorer"></i></span>
                                                        </div>
                                                        <input type="text" id="web" name="web" class="form-control" placeholder="Web de la empresa">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.telefonoConvencional')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono1" name="telefono1" maxlength="10" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Telefono Domicilio del Transportista">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.celular')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono2" name="telefono2" maxlength="10" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Telefono Celular del Transportista">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.placa')}}</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="placa" name="placa" class="form-control" placeholder="Placa del Transportista">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="cheestado">
                                                        <label class="custom-control-label" for="cheestado">{{trans('v3/new_tabla.placa')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-direccion" role="tabpanel" aria-labelledby="custom-content-above-direccion-tab">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.pais')}}</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2 pais" id="pais" name="pais" style="width: 100%;">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                        <?php
                                                        use Sis_medico\v3\contable\Utilidades;

                                                        if (config('data.paises') != null && count(config('data.paises')) > 0) {
                                                            foreach (config('data.paises') as $row) {
                                                                echo '<option value="' . $row->id . '">' .  ucwords(strtolower(Utilidades::sanear_string_tildes($row->nombre))) . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.provincia')}}</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2 provincia" name="provincia" id="provincia" style="width: 100%;">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.ciudad')}}</label>
                                                <div class="input-group mb-3">
                                                    <input type="hidden" id="ciudad" name="ciudad" />
                                                    <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 100%;">
                                                        <option value="">{{trans('v3/new_tabla.seleccione')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.urbanizacion')}}</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="urbanizacion" name="urbanizacion" class="form-control" placeholder="Urbanización del Transportista">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.etapa')}}</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="etapa" name="etapa" class="form-control" placeholder="Etapa del Transportista">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.manzana')}}</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map"></i></span>
                                                    </div>
                                                    <input type="text" id="manzana" name="manzana" class="form-control" placeholder="Manzana del Transportista">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.villa')}}</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                    </div>
                                                    <input type="text" id="villa_solar" name="villa_solar" class="form-control" placeholder="Villa del Transportista">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.direccion')}}</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend" style="cursor: pointer;" onclick="verMapa();">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="direccion" name="direccion" class="form-control direccion" placeholder="Dirección del Transportista">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-12">
                                            <div class="form-group">
                                                <label>{{trans('v3/new_tabla.referencia')}}</label>
                                                <div class="input-group mb-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map"></i></span>
                                                    </div>
                                                    <textarea rows="3" id="referencia" name="referencia" class="form-control" placeholder="Referencia del Transportista"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-bank" role="tabpanel" aria-labelledby="custom-content-above-bank-tab">
                                    <div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.contribuyenteespecial')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="contribuyente_especial" name="contribuyente_especial" style="width: 100%;">
                                                            <option value="0">{{trans('v3/new_tabla.no')}}</option>
                                                            <option value="1">{{trans('v3/new_tabla.si')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>{{trans('v3/new_tabla.contabilidad')}}</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="contabilidad" name="contabilidad" style="width: 100%;">
                                                            <option value="0">{{trans('v3/new_tabla.no')}}</option>
                                                            <option value="1">{{trans('v3/new_tabla.si')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Rise</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="rise" name="rise" style="width: 100%;">
                                                            <option value="0">{{trans('v3/new_tabla.no')}}</option>
                                                            <option value="1">{{trans('v3/new_tabla.si')}}</option>
                                                        </select>
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
                <button type="button" class="btn btn-primary" id="btnGuardarTransportista"><i class="fas fa-save"></i> {{trans('v3/new_varios.guardar')}}</button>
            </div>
        </div>
    </div>
</div>