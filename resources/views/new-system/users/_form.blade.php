@php
    $isEditing = isset($usuario);
    $companySelection = array_map('strval', old('company_ids', $selectedCompanies));
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <h4><i class="icon fa fa-ban"></i> {{ __('new_system.users.review_information') }}</h4>
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
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-user"></i> {{ __('new_system.users.personal_access') }}</h3></div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="id">{{ __('new_system.users.identification') }}</label>
                        <input id="id" type="text" name="id" value="{{ old('id', $usuario->id ?? '') }}" maxlength="13" required @disabled($isEditing) class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">{{ __('new_system.auth.email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" maxlength="191" required autocomplete="off" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre1">{{ __('new_system.users.first_name') }}</label>
                        <input id="nombre1" type="text" name="nombre1" value="{{ old('nombre1', $usuario->nombre1 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombre2">{{ __('new_system.users.second_name') }}</label>
                        <input id="nombre2" type="text" name="nombre2" value="{{ old('nombre2', $usuario->nombre2 ?? '') }}" maxlength="60" class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido1">{{ __('new_system.users.first_surname') }}</label>
                        <input id="apellido1" type="text" name="apellido1" value="{{ old('apellido1', $usuario->apellido1 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido2">{{ __('new_system.users.second_surname') }}</label>
                        <input id="apellido2" type="text" name="apellido2" value="{{ old('apellido2', $usuario->apellido2 ?? '') }}" maxlength="60" required class="form-control text-uppercase">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">{{ $isEditing ? __('new_system.users.password_optional') : __('new_system.users.password_required') }}</label>
                        <input id="password" type="password" name="password" @required(! $isEditing) minlength="12" autocomplete="new-password" class="form-control">
                        <span class="help-block">{{ __('new_system.users.minimum_twelve') }}</span>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password_confirmation">{{ __('new_system.users.confirm_password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" @required(! $isEditing) minlength="12" autocomplete="new-password" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-lock"></i> {{ __('new_system.users.permissions') }}</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label for="id_tipo_usuario">{{ __('new_system.users.role') }}</label>
                    <select id="id_tipo_usuario" name="id_tipo_usuario" required class="form-control">
                        <option value="">{{ __('new_system.common.select') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('id_tipo_usuario', $usuario->id_tipo_usuario ?? '') === (string) $role->id)>{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="estado">{{ __('new_system.users.status') }}</label>
                    <select id="estado" name="estado" required class="form-control">
                        <option value="1" @selected((string) old('estado', $usuario->estado ?? 1) === '1')>{{ __('new_system.common.active') }}</option>
                        <option value="0" @selected((string) old('estado', $usuario->estado ?? 1) === '0')>{{ __('new_system.common.inactive') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-building"></i> {{ __('new_system.users.assigned_companies') }}</h3></div>
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
        <a href="{{ route('new-system.users.index') }}" class="btn btn-default btn-flat">{{ __('new_system.common.cancel') }}</a>
        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> {{ $isEditing ? __('new_system.users.save_changes') : __('new_system.users.create') }}</button>
    </div>
</div>
