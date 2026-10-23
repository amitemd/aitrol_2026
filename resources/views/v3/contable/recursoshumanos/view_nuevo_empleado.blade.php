<div class="modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modalEmpleado"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4>
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
                    <input type="hidden" id="edit_nomina" name="edit_nomina" value="">

                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveTransportista">
                    <div class="card card-default">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-content-above-general-tab" data-toggle="pill" href="#custom-content-above-general" role="tab" aria-controls="custom-content-above-general" aria-selected="true"><i class="fas fa-user"></i> <span class="botones">Datos Personales</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-direccion-tab" data-toggle="pill" href="#custom-content-above-direccion" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="fas fa-user-check"></i> <span class="botones">Datos de Nomina</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-rol-tab" data-toggle="pill" href="#custom-content-above-rol" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="fa fa-user-circle"></i> <span class="botones">Datos de Rol</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-above-documentos-tab" data-toggle="pill" href="#custom-content-above-documentos" role="tab" aria-controls="custom-content-above-direccion" aria-selected="false"><i class="far fa-list-alt"></i> <span class="botones">Documentos</span></a>
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
                                                        <input type="text" id="id_user" name="id_user" maxlength="13" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Número de Identificación" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Primer Apellido</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="apellido1" name="apellido1" class="form-control" placeholder="Ingrese el primer apellido del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Segundo Apellido</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="apellido2" name="apellido2" class="form-control" placeholder="Ingrese el segundo apellido del empleado">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Primer Nombre</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombre1" name="nombre1" class="form-control" placeholder="Ingrese el primer nombre del empleado">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Segundo Nombre</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombre2" name="nombre2" class="form-control" placeholder="Ingrese el segundo nombre del empleado">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>País</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2 pais" id="pais" name="pais" style="width: 100%;">
                                                            <option value="">--Seleccione--</option>
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

                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Provincia</label>
                                                    <div class="input-group mb-3">
                                                        <select class="form-control select2 provincia" name="provincia" id="provincia" style="width: 100%;">
                                                            <option value="">--Seleccione--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Ciudad</label>
                                                    <div class="input-group mb-3">
                                                        <input type="hidden" id="ciudad" name="ciudad" />
                                                        <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad" style="width: 100%;">
                                                            <option value="">--Seleccione--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Etapa</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                                                        </div>
                                                        <input type="text" id="etapa" name="etapa" class="form-control" placeholder="Ingrese la etapa del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Manzana</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-location-arrow"></i></span>
                                                        </div>
                                                        <input type="text" id="manzana" name="manzana" class="form-control" placeholder="Ingrese la manzana del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Villa/Solar</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                        </div>
                                                        <input type="text" id="villa_solar" name="villa_solar" class="form-control" placeholder="Ingrese la villa del empleado">
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
                                                        <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Ingrese la dirección del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono Domicilio</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono1" name="telefono1" maxlength="10" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Ingrese el teléfono del domicilio del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Teléfono Celular</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                        </div>
                                                        <input type="text" id="telefono2" name="telefono2" maxlength="10" class="form-control text-right" onkeypress="return soloNumeros(event);" placeholder="Ingrese el teléfono celular del empleado">
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
                                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                        </div>
                                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
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
                                                        <input type="text" id="ocupacion" name="ocupacion" class="form-control" placeholder="Ingrese la ocupación del empleado">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Genero</label>
                                                    <div class="input-group mb-3">
                                                        <select name="genero" id="genero" class="form-control select2" style="width: 100%;">
                                                            <option value="">-- Seleccione --</option>
                                                            <?php
                                                            if (config('data.genero') != null && count(config('data.genero')) > 0) {
                                                                foreach (config('data.genero') as $row) {
                                                                    echo '<option value="' . $row->valor . '">' . $row->catalogo . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Etnia</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-male"></i></span>
                                                        </div>
                                                        <input type="text" id="etnia" name="etnia" class="form-control" placeholder="Ingrese la etnia del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="discapacidad" name="discapacidad">
                                                        <br>
                                                        <label class="custom-control-label" for="discapacidad">Discapacidad</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nivel Académico</label>
                                                    <div class="input-group mb-3">
                                                        <select name="nivel_academico" id="nivel_academico" class="form-control select2" style="width: 100%;">
                                                            <option value="">-- Seleccione --</option>
                                                            <?php
                                                            if (config('data.nivel_academico') != null && count(config('data.nivel_academico')) > 0) {
                                                                foreach (config('data.nivel_academico') as $row) {
                                                                    echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
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
                                                    <label>Estado Civil</label>
                                                    <div class="input-group mb-3">
                                                        <select name="estado_civil" id="estado_civil" class="form-control select2" style="width: 100%;">
                                                            <option value="">-- Seleccione --</option>
                                                            <?php
                                                            if (config('data.estado_civil') != null && count(config('data.estado_civil')) > 0) {
                                                                foreach (config('data.estado_civil') as $row) {
                                                                    echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Número de Cargas</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-child"></i></span>
                                                        </div>
                                                        <input type="text" id="numero_cargas" name="numero_cargas" class="form-control" placeholder="Ingrese el número de cargas">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                                        </div>
                                                        <input type="text" id="email" name="email" class="form-control" placeholder="Ingrese el mail del empleado">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Email Alternativo</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                                        </div>
                                                        <input type="text" id="mail_opcional" name="mail_opcional" class="form-control" placeholder="Ingrese el mail opcional del empleado">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row">


                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label> Password </label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                        </div>
                                                        <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese la contraseña">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label> Confirmar Password </label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                        </div>
                                                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Ingrese la confirmación de la contraseña">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="cheestado">
                                                        <label class="custom-control-label" for="cheestado">Estado</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div-->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-content-above-direccion" role="tabpanel" aria-labelledby="custom-content-above-direccion-tab">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Area</label>
                                                <div class="input-group mb-3">
                                                    <select name="area" id="area" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.area') != null && count(config('data.area')) > 0) {
                                                            foreach (config('data.area') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Cargo que ocupa</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                    </div>
                                                    <input type="text" id="cargo" name="cargo" class="form-control" placeholder="Ingrese el cargo del empleado">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Fecha de ingreso</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" placeholder="dd/mm/aaaa" value="<?= date('Y-m-d') ?>" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Fecha de Primer ingreso</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" id="fecha_ant" name="fecha_ant" placeholder="dd/mm/aaaa" />
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Fondo de Reserva</label>
                                                <div class="input-group mb-3">
                                                    <select name="pago_beneficio" id="pago_beneficio" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.pago_beneficio') != null && count(config('data.pago_beneficio')) > 0) {
                                                            foreach (config('data.pago_beneficio') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Decimo Tercero</label>
                                                <div class="input-group mb-3">
                                                    <select name="decimo_tercero" id="decimo_tercero" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.pago_beneficio') != null && count(config('data.pago_beneficio')) > 0) {
                                                            foreach (config('data.pago_beneficio') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
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
                                                <label>Decimo Cuarto</label>
                                                <div class="input-group mb-3">
                                                    <select name="decimo_cuarto" id="decimo_cuarto" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.pago_beneficio') != null && count(config('data.pago_beneficio')) > 0) {
                                                            foreach (config('data.pago_beneficio') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Horario</label>
                                                <div class="input-group mb-3">
                                                    <select name="horario" id="horario" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.horario') != null && count(config('data.horario')) > 0) {
                                                            foreach (config('data.horario') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->horario . '</option>';
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
                                                <label>Sucursal</label>
                                                <div class="input-group mb-3">
                                                    <select name="codigo_sucursal" id="codigo_sucursal" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        //dd(config('data.sucursal'));
                                                        if (config('data.sucursal') != null && count(config('data.sucursal')) > 0) {
                                                            foreach (config('data.sucursal') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->codigo_sucursal . '-' . $row->nombre_sucursal . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Aporte Personal</label>
                                                <div class="input-group mb-3">
                                                    <select name="aporte_personal" id="aporte_personal" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.aporte_personal') != null && count(config('data.aporte_personal')) > 0) {
                                                            foreach (config('data.aporte_personal') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->valor . '</option>';
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
                                                <label>Banco</label>
                                                <div class="input-group mb-3">
                                                    <select name="banco" id="banco" class="form-control select2" style="width: 100%;">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        if (config('data.banco') != null && count(config('data.banco')) > 0) {
                                                            foreach (config('data.banco') as $row) {
                                                                echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Número de Cuenta</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-bank"></i></span>
                                                    </div>
                                                    <input type="text" id="numero_cuenta" name="numero_cuenta" class="form-control" placeholder="Ingrese el número de cuenta del empleado">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Sueldo Neto</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="text" id="sueldo_neto" name="sueldo_neto" class="form-control" placeholder="Ingrese el sueldo neto del empleado">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox" class="custom-control-input" id="extension_conyuge">
                                                    <br>
                                                    <label class="custom-control-label" for="extension_conyuge">Extensión de Conyuge</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox" class="custom-control-input" id="pasante" name="pasante" onchange="tipo_pasante(this)">
                                                    <br>
                                                    <label class="custom-control-label" for="pasante">Pasante</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Sueldo Pasante</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="number" id="sueldo_pasante" name="sueldo_pasante" class="form-control" placeholder="Ingrese el sueldo del pasante">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Jornada Laboral</label>
                                                <div class="input-group mb-3">
                                                    <select name="jornada" id="jornada" class="form-control select2" style="width: 100%;" onchange="tipo_jornada(this)">
                                                        <?php
                                                        if (config('data.jornada_laboral') != null && count(config('data.jornada_laboral')) > 0) {
                                                            foreach (config('data.jornada_laboral') as $row) {
                                                                echo '<option value="' . $row->valor . '">' . $row->catalogo . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Dias Laborables</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar-times-o"></i></span>
                                                    </div>
                                                    <input type="number" id="dias_laborables" name="dias_laborables" class="form-control" placeholder="Ingrese los dias laborables del empleado">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Horas al mes</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar-times-o"></i></span>
                                                    </div>
                                                    <input type="number" id="horas_mes" name="horas_mes" class="form-control" placeholder="Ingrese las horas laborables al mes del empleado">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Funciones</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-map"></i></span>
                                                    </div>
                                                    <textarea rows="2" id="funciones" name="funciones" class="form-control" placeholder="Ingrese las funciones del empleado"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-content-above-rol" role="tabpanel" aria-labelledby="custom-content-above-rol-tab">
                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Bono</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="number" id="bono" name="bono" class="form-control" placeholder="Ingrese el valor del bono del empleado">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Bono imputable</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="number" id="bono_imputable" name="bono_imputable" class="form-control" placeholder="Ingrese el valor del bono imputable del empleado">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Parqueo</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-car"></i></span>
                                                    </div>
                                                    <input type="number" id="parqueo" name="parqueo" class="form-control" placeholder="Ingrese el valor del parqueo del empleado">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Impuesto a la Renta</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="number" id="impuesto_renta" name="impuesto_renta" class="form-control" placeholder="Ingrese el impuesto a la renta del empleado">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Alimentación</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-cutlery"></i></span>
                                                    </div>
                                                    <input type="number" id="alimentacion" name="alimentacion" class="form-control" placeholder="Ingrese el valor de alimentación del empleado">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Movilización </label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-car"></i></span>
                                                    </div>
                                                    <input type="number" id="movilizacion" name="movilizacion" class="form-control" placeholder="Ingrese el impuesto a la renta del empleado">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Seguro Privado</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                    </div>
                                                    <input type="number" id="seguro_privado" name="seguro_privado" class="form-control" placeholder="Ingrese el valor del parqueo del empleado">
                                                </div>
                                            </div>
                                        </div>

                                    </div>




                                </div>

                                <div class="tab-pane fade" id="custom-content-above-documentos" role="tabpanel" aria-labelledby="custom-content-above-documentos-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label id="lblempleadoCedula" class="font-normal">Cédula </label>
                                                <div>
                                                    <label title="Archivo pdf o imagen de la cédula del empleado" for="empleadoCedula" class="ladda-button ladda-button-demo-ruc btn btn-primary" data-style="zoom-in">
                                                        <input type="file" name="empleadoCedula" id="empleadoCedula" style="display:none">
                                                        <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label id="lblFichaOcupacional" class="font-normal">Ficha Ocupacional </label>
                                                <div>
                                                    <label title="Archivo pdf o imagen de la ficha ocupacional del empleado" for="empleadoFichaOcupacional" class="ladda-button ladda-button-demo-ced btn btn-primary" data-style="zoom-in">
                                                        <input type="file" name="empleadoFichaOcupacional" id="empleadoFichaOcupacional" style="display:none">
                                                        <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label id="lblFichaTecnica" class="font-normal">Ficha Técnica </label>
                                                <div>
                                                    <label title="Archivo pdf o imagen de la ficha tecnica" for="empleadoFichaTecnica" class="ladda-button ladda-button-demo-cer btn btn-primary" data-style="zoom-in">
                                                        <input type="file" name="empleadoFichaTecnica" id="empleadoFichaTecnica" style="display:none">
                                                        <i class="fa fa-paperclip"></i> {{trans('v3/new_varios.aniadirArchivo')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <div style="width: 70%;">
                                                    <div>
                                                        <div class="card-body">
                                                            <div class="card card-primary card-outline">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Logo</h5>
                                                                    <div class="card-tools">
                                                                        <a href="javascript:;" class="btn btn-tool">
                                                                            <label title="Archivo imágen del proveedor" for="proveedorLogo" class="ladda-button ladda-button-demo-provlogo btn btn-success" data-style="zoom-in">
                                                                                <input type="file" name="proveedorLogo" id="proveedorLogo" style="display:none">
                                                                                <i class="fas fa-pen"></i>
                                                                            </label>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body" style="position: flex;text-align: center;">
                                                                    <center>
                                                                        <div style="width: 204px;height: 204px;border:1px solid gray;" id="logoProveedor">
                                                                            <img src="../storage/app/logo/0401182274_Mifoto.jpg" />
                                                                        </div>
                                                                    </center>
                                                                </div>
                                                            </div>
                                                        </div>
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