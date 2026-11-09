<div class="modal fade" id="registro-paciente-emergencia" tabindex="-1" role="dialog" aria-labelledby="registro-paciente-titulo">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="form-paciente-emergencia" class="modal-content" action="{{ route('emergencia.pacientes.registrar') }}" method="post">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title" id="registro-paciente-titulo"><i class="fas fa-user-plus mr-2"></i> Nuevo paciente · Emergencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Guarde lo que conozca. Nombres, sexo y demás datos se pueden completar durante la atención.</p>
                <input type="hidden" name="registration_token" id="rapido-token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                <label class="d-block mb-3"><input type="checkbox" name="without_document" id="rapido-sin-documento" value="1"> No se conoce la identificación. Generar código provisional.</label>
                <div id="registro-paciente-error" class="alert alert-danger" role="alert" hidden></div>
                <div id="registro-persona-info" class="alert alert-info" role="status" hidden></div>
                <div class="form-row">
                    <div class="form-group col-sm-5">
                        <label for="rapido-tipo-documento">Tipo de documento *</label>
                        <select id="rapido-tipo-documento" name="tipo_documento" class="form-control" required>
                            <option value="1">Cédula</option><option value="2">Pasaporte</option><option value="3">Otro documento</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-7">
                        <label for="rapido-id">Identificación *</label>
                        <input id="rapido-id" name="id_paciente" class="form-control" minlength="3" maxlength="13" pattern="[A-Za-z0-9\-]+" autocomplete="off" required aria-describedby="rapido-id-ayuda">
                        <small id="rapido-id-ayuda" class="form-text text-muted">Al salir de este campo se consultan los datos existentes.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="rapido-apellido1">Apellido 1, si se conoce</label>
                        <input id="rapido-apellido1" name="apellido1" class="form-control" maxlength="60">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="rapido-apellido2">Apellido 2, si se conoce</label>
                        <input id="rapido-apellido2" name="apellido2" class="form-control" maxlength="60">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="rapido-nombre1">Nombre 1, si se conoce</label>
                        <input id="rapido-nombre1" name="nombre1" class="form-control" maxlength="60">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="rapido-nombre2">Nombre 2, si se conoce</label>
                        <input id="rapido-nombre2" name="nombre2" class="form-control" maxlength="60">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label for="rapido-sexo">Sexo, si se conoce</label>
                    <select id="rapido-sexo" name="sexo" class="form-control" data-select2-off>
                        <option value="">No identificado</option><option value="1">Hombre</option><option value="2">Mujer</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button id="guardar-paciente-emergencia" type="submit" class="btn btn-primary"><i class="far fa-save mr-1"></i> Guardar y seleccionar</button>
            </div>
        </form>
    </div>
</div>
