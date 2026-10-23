<div class="card card-default">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Tipo de identificación</label>
                    <div class="input-group mb-3 hero-tipo_identificacion">
                        <select class="form-control select2" id="tipo_identificacion" name="tipo_identificacion" style="width: 100%;">
                            <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                            <?php
                            if (config('data.tipoIdentificacion') != null && count(config('data.tipoIdentificacion')) > 0) {
                                foreach (config('data.tipoIdentificacion') as $row) {
                                    echo '<option value="' . $row->cod_sri_tipo_identificacion . '">' . $row->tipo_identificacion . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Nombre</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del Cliente">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Identificación</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="identificacion" name="identificacion" onblur="verificarCliente(this)" class="form-control text-right" placeholder="Número de identificación">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label>Clase</label>
                    <div class="input-group mb-3">
                        <select class="form-control select2" id="clase" name="clase" style="width: 100%;">
                            <option value="">--<?= trans('v3/new_varios.seleccionar'); ?>--</option>
                            <?php
                            if (config('data.clase') != null && count(config('data.clase')) > 0) {
                                foreach (config('data.clase') as $row) {
                                    echo '<option value="' . $row->id_catalogo . '">' . $row->catalogo . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>E-mail</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="email_representante" name="email_representante" class="form-control text-right" placeholder="usuaro@dominio.com">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Teléfono 1</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="telefono1_representante" name="telefono1_representante" class="form-control text-right" placeholder="Teléfono 1">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group mb-3">
                    <label>Teléfono 2</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                        </div>
                        <input type="text" id="telefono2_representante" name="telefono2_representante" class="form-control text-right" placeholder="Teléfono 2">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label>&nbsp;</label>
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="cheestado">
                        <label class="custom-control-label" for="cheestado">Estado</label>
                    </div>
                </div>
            </div>
        </div>
        <div id="datosRepresentante" style="display: none">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <h3 class="bd-title">
                            Datos del Representante
                        </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label>Nombre</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                            <input type="text" id="nombre_representante" name="nombre_representante" class="form-control" placeholder="Nombre del Representante">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group mb-3">
                        <label>C&eacute;dula</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-edit"></i></span>
                            </div>
                            <input type="text" id="cedula" name="cedula" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Número de cédula">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>