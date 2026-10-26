@php
    $isEditing = isset($usuario);
    $companySelection = array_map('strval', old('company_ids', $selectedCompanies));
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <h4><i class="icon fa fa-ban"></i> Revisa la información ingresada</h4>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-user"></i> Información personal y acceso</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="id">Identificación</label>
                        <input id="id" type="text" name="id" value="{{ old('id', $usuario->id ?? '') }}" maxlength="13" required @disabled($isEditing) class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Correo electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" maxlength="191" required autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre1">Primer nombre</label>
                        <input id="nombre1" type="text" name="nombre1" value="{{ old('nombre1', $usuario->nombre1 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre2">Segundo nombre</label>
                        <input id="nombre2" type="text" name="nombre2" value="{{ old('nombre2', $usuario->nombre2 ?? '') }}" maxlength="60" class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido1">Primer apellido</label>
                        <input id="apellido1" type="text" name="apellido1" value="{{ old('apellido1', $usuario->apellido1 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido2">Segundo apellido</label>
                        <input id="apellido2" type="text" name="apellido2" value="{{ old('apellido2', $usuario->apellido2 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Contraseña {{ $isEditing ? '(opcional)' : '' }}</label>
                        <input id="password" type="password" name="password" @required(! $isEditing) minlength="12" autocomplete="new-password" class="form-control">
                        <span class="help-block">Mínimo 12 caracteres.</span>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" @required(! $isEditing) minlength="12" autocomplete="new-password" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-lock"></i> Permisos</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label for="id_tipo_usuario">Rol</label>
                    <select id="id_tipo_usuario" name="id_tipo_usuario" required class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('id_tipo_usuario', $usuario->id_tipo_usuario ?? '') === (string) $role->id)>{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required class="form-control">
                        <option value="1" @selected((string) old('estado', $usuario->estado ?? 1) === '1')>Activo</option>
                        <option value="0" @selected((string) old('estado', $usuario->estado ?? 1) === '0')>Inactivo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-building"></i> Empresas asignadas</h3></div>
            <div class="box-body" style="max-height: 285px; overflow-y: auto;">
                @foreach ($newSystemCompanies as $company)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" @checked(in_array((string) $company->id, $companySelection, true))>
                            <strong>{{ $company->nombrecomercial }}</strong><br><small class="text-muted">{{ $company->id }}</small>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="box box-default">
    <div class="box-footer text-right">
        <a href="{{ route('new-system.users.index') }}" class="btn btn-default btn-flat">Cancelar</a>
        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> {{ $isEditing ? 'Guardar cambios' : 'Crear usuario' }}</button>
    </div>
</div>
