<div class="card card-default">
    <div class="card-body">
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Primer Nombre</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                    </div>
                                    <input type="text" id="nombre1" name="nombre1" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ingrese el primer nombre">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Segundo Nombre</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                    </div>
                                    <input type="text" id="nombre2" name="nombre2" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ingrese el segundo nombre">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Primer Apellido</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                    </div>
                                    <input type="text" id="apellido1" name="apellido1" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Ingrese el primer apellido">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Segundo Apellido</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                    </div>
                                    <input type="text" id="apellido2" name="apellido2" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Ingrese el segundo apellido">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Cedula</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                                    </div>
                                    <input type="text" maxlength="13" id="id" name="id" class="form-control text-right" required placeholder="Ingrese el número de cedula">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Telefono Celular</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                    </div>
                                    <input type="text" id="telefono2" name="telefono2" class="form-control text-right" required placeholder="Ingrese el telefono">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Telefono Domicilio</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                    </div>
                                    <input type="text" id="telefono1" name="telefono1" class="form-control text-right" required placeholder="Ingrese el telefono">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Ocupación</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="ocupacion" name="ocupacion" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ingrese la ocupación">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Fecha Nacimiento</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" placeholder="dd/mm/aaaa">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Correo</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="mail" id="email" name="email" class="form-control" placeholder="Ingrese el correo" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Nueva contraseña</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese la contraseña">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="password" id="conf_password" name="conf_password" class="form-control" placeholder="Confirme la contraseña">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Tipo usuario</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2 id_tipo_usuario" id="id_tipo_usuario" name="id_tipo_usuario" style="width: 100%;" required>
                                        <option value="">--Seleccione--</option>
                                        <?php
                                        if (config('data.tipousuario') != null && count(config('data.tipousuario')) > 0) {
                                            foreach (config('data.tipousuario') as $row) {
                                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6" id="div_especialidad">
                            <div class="form-group">
                                <label>Especialidades</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2 especialidad" id="especialidad" name="especialidad" style="width: 100%;">
                                        <option value="">--Seleccione--</option>
                                        <?php
                                        if (config('data.especialidad') != null && count(config('data.especialidad')) > 0) {
                                            foreach (config('data.especialidad') as $row) {
                                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6" id="div_color">
                            <div class="form-group">
                                <label>Color</label>
                                <div class="input-group mb-3">
                                    <input type="color" id="color" name="color" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Rol usuario</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="id_tipo_rol" name="id_tipo_rol" style="width: 100%;" required>
                                        <option value="">--Seleccione--</option>
                                        <?php
                                        if (config('data.rolesUsuario') != null && count(config('data.rolesUsuario')) > 0) {
                                            foreach (config('data.rolesUsuario') as $row) {
                                                echo '<option value="' . $row->id_rol . '">' . $row->nombre_rol . '</option>';
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
        </div>
    </div>
</div>