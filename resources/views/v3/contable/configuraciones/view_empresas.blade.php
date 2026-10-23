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
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <div>
                            <div style="position: absolute; right: 5px;top:7px">
                                <a href="javascript:;" class="btn btn-primary btn-sm" onclick="agregarEmpresa();"><i class="fas fa-plus-circle"></i> <span class="botones">{{trans('v3/new_tabla.agregar')}}</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tableEmpresas" class="table table-bordered table-striped tablaDatos" width="100%">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>RUC</th>
                                    <th>Razon social</th>
                                    <th>Nombre comercial</th>
                                    <th>E-mail</th>
                                    <th>Estado</th>
                                    <th>...</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th>Logo</th>
                                    <th>RUC</th>
                                    <th>Razon social</th>
                                    <th>Nombre comercial</th>
                                    <th>E-mail</th>
                                    <th>Estado</th>
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
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Cargando...</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{csrf_field()}}
                    <input type="hidden" id="d" name="d" value="|">
                    <input type="hidden" id="estado" name="estado">
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveEmpresa">
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
                                    <a class="nav-link" id="custom-content-above-contable-tab" data-toggle="pill" href="#custom-content-above-contable" role="tab" aria-controls="custom-content-above-contable" aria-selected="false"><i class="fas fa-address-book"></i> <span class="botones">Info contable</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-bank-tab" data-toggle="pill" href="#custom-content-above-bank" role="tab" aria-controls="custom-content-above-bank" aria-selected="false"><i class="fas fa-receipt"></i> <span class="botones">Info tributaria</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-documentacion-tab" data-toggle="pill" href="#custom-content-above-documentacion" role="tab" aria-controls="custom-content-above-documentacion" aria-selected="false"><i class="fas fa-copy"></i> <span class="botones">Documentación</span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="custom-content-above-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                                    <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Identificación</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                                        </div>
                                                        <input type="text" id="id" name="id" maxlength="13" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Número de Identificación" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Razón Social</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="razonsocial" name="razonsocial" class="form-control" placeholder="Razón Social de la empresa">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nombre Comercial</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombrecomercial" name="nombrecomercial" class="form-control" placeholder="Nombre Comercial de la empresa">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                        </div>
                                                        <input type="email" id="email" name="email" class="form-control" placeholder="Email de la empresa">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono convencional</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono1" name="telefono1" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Telefono Domicilio del Proveedor">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono Celular</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono2" name="telefono2" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Telefono Celular del Proveedor">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nombre corto</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombre_corto" name="nombre_corto" class="form-control" placeholder="Nombre corto de la empresa">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="cheestado">
                                                        <label class="custom-control-label" for="cheestado">Estado</label>
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
                                                <label>País</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2 pais" id="pais" style="width: 88%;">
                                                        <option value="">--Seleccione--</option>
                                                        <?php
                                                        if (config('data.paises') != null && count(config('data.paises')) > 0) {
                                                            foreach (config('data.paises') as $row) {
                                                                echo '<option value="' . $row->id . '">' .  ucwords(strtolower(Sis_medico\v3\contable\Utilidades::sanear_string_tildes($row->nombre))) . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Provincia</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2 provincia" id="provincia" style="width: 88%;">
                                                        <option value="">--Seleccione--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Ciudad</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 88%;">
                                                        <option value="">--Seleccione--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Dirección</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección del Proveedor">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Urbanización</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="urbanizacion" name="urbanizacion" class="form-control" placeholder="Urbanización del Proveedor">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Etapa</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                    </div>
                                                    <input type="text" id="etapa" name="etapa" class="form-control" placeholder="Etapa del Proveedor">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Manzana</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map"></i></span>
                                                    </div>
                                                    <input type="text" id="manzana" name="manzana" class="form-control" placeholder="Manzana del Proveedor">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Villa/Solar</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                    </div>
                                                    <input type="text" id="villa" name="villa" class="form-control" placeholder="Villa del Proveedor">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-12">
                                            <div class="form-group">
                                                <label>Referencia</label>
                                                <div class="input-group mb-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map"></i></span>
                                                    </div>
                                                    <textarea rows="3" id="referencia" name="referencia" class="form-control" placeholder="Referencia del Proveedor"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-contable" role="tabpanel" aria-labelledby="custom-content-above-contable-tab">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Representante</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2" id="id_representante" name="id_representante" style="width: 88%;">
                                                        <option value="">--Seleccione--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Contador</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2" id="id_contador" name="id_contador" style="width: 88%;">
                                                        <option value="">--Seleccione--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>No Registro contador</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                    </div>
                                                    <input type="text" id="num_registro_contador" name="num_registro_contador" class="form-control" placeholder="No. de registro del contador">
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
                                                    <label>Tipo contribuyente</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="tipo_contribuyente" name="tipo_contribuyente" style="width: 88%;">
                                                            <option value="">--Seleccione--</option>
                                                            <option value="0">Persona natural</option>
                                                            <option value="1">Persona juridica</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Agente de retención</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="agente_retencion" name="agente_retencion" style="width: 88%;">
                                                            <option value="">--Seleccione--</option>
                                                            <option value="1">Si</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Contribuyente especial</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="contribuyente_especial" name="contribuyente_especial" class="form-control" placeholder="No. del contribuyente">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Ambiente</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="ambiente" name="ambiente" style="width: 88%;">
                                                            <option value="">--Seleccione--</option>
                                                            <option value="1">Pruebas</option>
                                                            <option value="2">Producción</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Contabilidad</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="contabilidad" name="contabilidad" style="width: 88%;">
                                                            <option value="">--Seleccione--</option>
                                                            <option value="1">Si</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Tipo rimpe</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2" id="tipo_rimpe" name="tipo_rimpe" style="width: 88%;">
                                                            <option value="">--Seleccione--</option>
                                                            <option value="0">Rimpe popular</option>
                                                            <option value="1">Rimpe emprendedor</option>
                                                            <option value="2">Regimen general</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Firma</label>
                                                    <div class="input-group mb-3">
                                                        <input type="hidden" id="ruta_firma" name="ruta_firma" value="" />
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadFirma" id="lblEntidadFirma" class="ladda-button ladda-button-demo-firma btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadFirma" id="entidadFirma" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Clave de firma</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend" onclick="validarFirma();">
                                                            <span class="input-group-text" style="cursor: pointer;"><i class="fas fa-key"></i></span>
                                                        </div>
                                                        <input type="password" id="clave_firma" name="clave_firma" class="form-control" placeholder="Clave de la firma electrónica" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <table id="tableInfoTributaria" class="table table-bordered table-striped" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <th>Ag. Retención</th>
                                                            <th>Cont. Especial</th>
                                                            <th>Ambiente</th>
                                                            <th>Contabilidad</th>
                                                            <th>Regimen</th>
                                                            <th>Año</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <th>Ag. Retención</th>
                                                            <th>Cont. Especial</th>
                                                            <th>Ambiente</th>
                                                            <th>Contabilidad</th>
                                                            <th>Regimen</th>
                                                            <th>Año</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-documentacion" role="tabpanel" aria-labelledby="custom-content-above-documentacion-tab">
                                    <div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Ruc: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadRuc" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadRuc" id="entidadRuc" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div style="text-align: center;">
                                                        <div style="border: solid gray 1px;">
                                                            Logo
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Registro Cias: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadRegistroCias" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadRegistroCias" id="entidadRegistroCias" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Acta de accionistas: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadActaAccionista" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadActaAccionista" id="entidadActaAccionista" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Cédula representante: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadCedulaRepresentante" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadCedulaRepresentante" id="entidadCedulaRepresentante" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Certificado de votación: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadCertificadoRepresentante" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadCertificadoRepresentante" id="entidadCertificadoRepresentante" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Estatutos: </label>
                                                    <div class="input-group mb-3">
                                                        <label title="Archivo pdf o imagen del RUC de la compañia" for="entidadEstatutos" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                            <input type="file" name="entidadEstatutos" id="entidadEstatutos" style="display:none">
                                                            <i class="fa fa-paperclip"></i> Añadir archivo
                                                        </label>
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
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEmpresa"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>