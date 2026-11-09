@php
    $profileImageName = trim((string) $user->imagen_url);
    $profileImageName = $profileImageName !== '' ? $profileImageName : 'avatar.jpg';
    $profileImageUrl = asset('/avatars') . '/' . $profileImageName;
    $defaultProfileAvatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="50" fill="#e7f4f8"/><circle cx="50" cy="37" r="18" fill="#0b78bd"/><path d="M17 92c3-24 17-36 33-36s30 12 33 36" fill="#0b78bd"/></svg>';
    $defaultProfileImageUrl = 'data:image/svg+xml;base64,' . base64_encode($defaultProfileAvatarSvg);
@endphp

<style>
    #perfil .modal-dialog {
        width: calc(100% - 36px) !important;
        max-width: 1180px !important;
        margin: 20px auto !important;
    }

    #perfil .modal-content {
        max-height: calc(100vh - 40px);
        overflow: hidden;
        border: 0;
        border-radius: 18px;
        background: #f4f8fb;
        box-shadow: 0 28px 75px rgba(7, 46, 79, .28);
    }

    .hc4-profile-editor {
        --profile-navy: #073b66;
        --profile-blue: #0b78bd;
        --profile-cyan: #18aabe;
        --profile-ink: #183650;
        --profile-muted: #687f92;
        --profile-line: #d8e6ee;
        max-height: calc(100vh - 40px);
        overflow-x: hidden;
        overflow-y: auto;
        color: var(--profile-ink);
        font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
    }

    .hc4-profile-editor * { box-sizing: border-box; }
    .hc4-profile-editor .oculto { display: none !important; }

    .profile-modal-header {
        position: sticky;
        z-index: 20;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 20px 26px;
        background: linear-gradient(115deg, var(--profile-navy), var(--profile-blue) 62%, #12989b);
        color: #fff;
        box-shadow: 0 8px 24px rgba(7, 59, 102, .15);
    }

    .profile-modal-title {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 14px;
    }

    .profile-modal-title-icon {
        display: inline-flex;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 13px;
        background: rgba(255,255,255,.14);
        font-size: 20px;
    }

    .profile-modal-title h2 {
        margin: 0;
        color: #fff;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.25;
    }

    .profile-modal-title p {
        margin: 4px 0 0;
        color: rgba(255,255,255,.8);
        font-size: 12px;
    }

    .profile-modal-close {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 11px;
        background: rgba(255,255,255,.12);
        color: #fff;
        font-size: 17px;
        transition: background .2s ease, transform .2s ease;
    }

    .profile-modal-close:hover,
    .profile-modal-close:focus {
        background: rgba(255,255,255,.24);
        color: #fff;
        outline: none;
        transform: rotate(4deg);
    }

    .profile-modal-body { padding: 22px; }

    .profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.75fr) minmax(290px, .75fr);
        gap: 20px;
        align-items: start;
    }

    .profile-card {
        overflow: hidden;
        border: 1px solid var(--profile-line);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(16, 70, 109, .07);
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--profile-line);
        background: linear-gradient(100deg, #f8fcfe, #edf8fb);
    }

    .profile-card-header-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #dff1f7;
        color: var(--profile-blue);
        font-size: 15px;
    }

    .profile-card-header h3 {
        margin: 0;
        color: var(--profile-navy);
        font-size: 15px;
        font-weight: 700;
    }

    .profile-card-header p {
        margin: 3px 0 0;
        color: var(--profile-muted);
        font-size: 11px;
    }

    .profile-card-body { padding: 18px; }

    .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px 16px;
    }

    .profile-field { min-width: 0; }
    .profile-span-full { grid-column: 1 / -1; }

    .hc4-profile-editor .profile-field label {
        display: block;
        margin: 0 0 6px;
        padding: 0;
        color: var(--profile-ink);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .hc4-profile-editor .form-control {
        width: 100%;
        height: 42px;
        padding: 9px 12px;
        border: 1px solid #cddde7;
        border-radius: 9px;
        background: #fff;
        color: var(--profile-ink);
        font-size: 13px;
        box-shadow: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .hc4-profile-editor .form-control:focus {
        border-color: var(--profile-cyan);
        outline: none;
        box-shadow: 0 0 0 3px rgba(24,170,190,.13);
    }

    .hc4-profile-editor .form-control[readonly] {
        background: #edf2f5;
        color: #637685;
        cursor: not-allowed;
    }

    .profile-field-note {
        display: block;
        margin-top: 5px;
        color: var(--profile-muted);
        font-size: 10px;
    }

    .hc4-profile-editor .help-block {
        margin: 5px 0 0;
        color: #c33f48;
        font-size: 11px;
    }

    .profile-specialties {
        padding: 14px;
        border: 1px solid var(--profile-line);
        border-radius: 11px;
        background: #f8fbfd;
    }

    .profile-specialties-title {
        display: block;
        margin-bottom: 10px;
        color: var(--profile-navy);
        font-size: 12px;
        font-weight: 700;
    }

    .profile-specialties-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .profile-specialty-option {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        margin: 0;
        padding: 8px 10px;
        border: 1px solid #e0ebf1;
        border-radius: 8px;
        background: #fff;
        color: var(--profile-ink);
        font-size: 11px;
        font-weight: 500;
    }

    .profile-specialty-option input { margin: 0; accent-color: var(--profile-blue); }

    .profile-color-picker {
        display: flex;
        min-height: 42px;
        align-items: center;
        padding: 5px 10px;
        border: 1px solid #cddde7;
        border-radius: 9px;
        background: #fff;
    }

    .profile-color-picker .input-group-addon {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .profile-color-picker .input-group-addon i { width: 24px !important; height: 24px !important; border-radius: 7px; }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid var(--profile-line);
    }

    .profile-primary-btn,
    .profile-secondary-btn {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .profile-primary-btn {
        border: 1px solid var(--profile-blue);
        background: var(--profile-blue);
        color: #fff;
    }

    .profile-primary-btn:hover,
    .profile-primary-btn:focus { border-color: var(--profile-navy); background: var(--profile-navy); color: #fff; }

    .profile-secondary-btn { border: 1px solid #cfdee7; background: #fff; color: var(--profile-ink); }

    .profile-photo-summary {
        padding: 22px 18px 18px;
        text-align: center;
    }

    .profile-avatar-frame {
        position: relative;
        width: 138px;
        height: 138px;
        margin: 0 auto 13px;
        padding: 5px;
        border: 1px solid #cfe0e9;
        border-radius: 50%;
        background: linear-gradient(135deg, #e9f7fb, #dff4ef);
        box-shadow: 0 12px 28px rgba(7,59,102,.13);
    }

    .profile-avatar-frame img {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #eef4f7;
        object-fit: cover;
    }

    .profile-avatar-status {
        position: absolute;
        right: 7px;
        bottom: 9px;
        width: 17px;
        height: 17px;
        border: 3px solid #fff;
        border-radius: 50%;
        background: #23b26d;
    }

    .profile-photo-summary strong {
        display: block;
        color: var(--profile-navy);
        font-size: 15px;
    }

    .profile-photo-summary span {
        display: block;
        margin-top: 4px;
        color: var(--profile-muted);
        font-size: 11px;
    }

    .profile-photo-form { padding: 0 18px 18px; }
    .profile-file-input { position: absolute; width: 1px; height: 1px; overflow: hidden; opacity: 0; }

    .profile-file-picker {
        display: flex;
        min-height: 84px;
        align-items: center;
        justify-content: center;
        padding: 14px;
        border: 1px dashed #a9c8d9;
        border-radius: 11px;
        background: #f6fbfd;
        color: var(--profile-blue);
        cursor: pointer;
        text-align: center;
        transition: border-color .2s ease, background .2s ease;
    }

    .profile-file-picker:hover { border-color: var(--profile-cyan); background: #eef9fb; }
    .profile-file-picker i { display: block; margin-bottom: 6px; font-size: 21px; }
    .profile-file-picker strong { display: block; font-size: 12px; }
    .profile-file-picker small { display: block; margin-top: 4px; color: var(--profile-muted); font-size: 10px; font-weight: 400; }

    .profile-file-name {
        display: block;
        min-height: 16px;
        margin: 8px 2px 0;
        color: var(--profile-muted);
        font-size: 10px;
        overflow-wrap: anywhere;
    }

    .profile-photo-actions { margin-top: 14px; }
    .profile-photo-actions .profile-primary-btn { width: 100%; }

    @media (max-width: 900px) {
        .profile-layout { grid-template-columns: 1fr; }
        .profile-photo-card { order: -1; }
    }

    @media (max-width: 600px) {
        #perfil .modal-dialog { width: calc(100% - 16px) !important; margin: 8px auto !important; }
        #perfil .modal-content, .hc4-profile-editor { max-height: calc(100vh - 16px); }
        .profile-modal-header { padding: 16px; }
        .profile-modal-title p { display: none; }
        .profile-modal-body { padding: 12px; }
        .profile-form-grid, .profile-specialties-grid { grid-template-columns: 1fr; }
        .profile-span-full { grid-column: auto; }
        .profile-actions { flex-direction: column-reverse; }
        .profile-actions .btn { width: 100%; }
    }
</style>

<div class="hc4-profile-editor">
    <header class="profile-modal-header">
        <div class="profile-modal-title">
            <span class="profile-modal-title-icon"><i class="fa fa-user-md" aria-hidden="true"></i></span>
            <div>
                <h2 id="hospital-profile-modal-title">Mi perfil profesional</h2>
                <p>Actualiza tus datos personales, información de contacto y fotografía.</p>
            </div>
        </div>
        <button type="button" class="profile-modal-close" data-dismiss="modal" aria-label="Cerrar">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
    </header>

    <div class="profile-modal-body">
        <div class="profile-layout">
            <section class="profile-card">
                <div class="profile-card-header">
                    <span class="profile-card-header-icon"><i class="fa fa-address-card-o" aria-hidden="true"></i></span>
                    <div>
                        <h3>Información personal</h3>
                        <p>Datos de identificación, contacto y acceso a la plataforma.</p>
                    </div>
                </div>

                <form class="form-vertical" role="form" method="POST" action="{{ route('user-management.update', ['user_management' => $user->id]) }}">
                    <div class="profile-card-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="profile-form-grid">
                            <div class="profile-field{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                                <label for="nombre1">Primer nombre</label>
                                <input id="nombre1" type="text" class="form-control" name="nombre1" value="{{ $user->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre1'))<span class="help-block"><strong>{{ $errors->first('nombre1') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                                <label for="nombre2">Segundo nombre</label>
                                <input id="nombre2" type="text" class="form-control" name="nombre2" value="{{ $user->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                @if ($errors->has('nombre2'))<span class="help-block"><strong>{{ $errors->first('nombre2') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                                <label for="apellido1">Primer apellido</label>
                                <input id="apellido1" type="text" class="form-control" name="apellido1" value="{{ $user->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                                @if ($errors->has('apellido1'))<span class="help-block"><strong>{{ $errors->first('apellido1') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                                <label for="apellido2">Segundo apellido</label>
                                <input id="apellido2" type="text" class="form-control" name="apellido2" value="{{ $user->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                                @if ($errors->has('apellido2'))<span class="help-block"><strong>{{ $errors->first('apellido2') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('id') ? ' has-error' : '' }}">
                                <label for="id">Cédula</label>
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ $user->id }}" required onkeyup="validarCedula(this.value);" @if ($rolusuario != 1) readonly="readonly" @endif>
                                @if ($rolusuario != 1)<span class="profile-field-note">Este dato no puede modificarse desde tu perfil.</span>@endif
                                @if ($errors->has('id'))<span class="help-block"><strong>{{ $errors->first('id') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                                <label for="id_pais">País</label>
                                <select id="id_pais" name="id_pais" class="form-control">
                                    <option value="">Selecciona un país</option>
                                    @foreach ($paises as $pais)
                                        <option {{ $user->id_pais == $pais->id ? 'selected' : '' }} value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_pais'))<span class="help-block"><strong>{{ $errors->first('id_pais') }}</strong></span>@endif
                            </div>

                            <div class="profile-field">
                                <label for="id_provincia_perfil">Provincia o estado</label>
                                <select id="id_provincia_perfil" class="form-control">
                                    <option value="">Selecciona una provincia o estado</option>
                                    @foreach ($provincias as $provincia)
                                        <option {{ (string)$idProvincia === (string)$provincia->id_provincia ? 'selected' : '' }} value="{{ $provincia->id_provincia }}">{{ $provincia->nombre_provincia }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="profile-field{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                                <label for="ciudad">Ciudad</label>
                                <select id="ciudad" class="form-control" name="ciudad" required>
                                    <option value="">Selecciona una ciudad</option>
                                    @if(!is_null($user->ciudad) && is_null($ciudadUsuario))
                                        <option value="{{ $user->ciudad }}" selected>{{ $user->ciudad }}</option>
                                    @endif
                                    @foreach ($ciudades as $ciudad)
                                        <option data-id="{{ $ciudad->id_ciudad }}" {{ !is_null($ciudadUsuario) && $ciudadUsuario->id_ciudad == $ciudad->id_ciudad ? 'selected' : '' }} value="{{ $ciudad->nombre_ciudad }}">{{ $ciudad->nombre_ciudad }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="id_ciudad_usuario" name="id_ciudad_usuario" value="{{ !is_null($ciudadUsuario) ? $ciudadUsuario->id_ciudad : $user->id_ciudad_usuario }}">
                                @if ($errors->has('ciudad'))<span class="help-block"><strong>{{ $errors->first('ciudad') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <label for="direccion">Dirección</label>
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $user->direccion }}" required>
                                @if ($errors->has('direccion'))<span class="help-block"><strong>{{ $errors->first('direccion') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                                <label for="telefono1">Teléfono de domicilio</label>
                                <input id="telefono1" type="text" inputmode="tel" class="form-control" name="telefono1" value="{{ $user->telefono1 }}" required>
                                @if ($errors->has('telefono1'))<span class="help-block"><strong>{{ $errors->first('telefono1') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                                <label for="telefono2">Teléfono celular</label>
                                <input id="telefono2" type="text" inputmode="tel" class="form-control" name="telefono2" value="{{ $user->telefono2 }}" required>
                                @if ($errors->has('telefono2'))<span class="help-block"><strong>{{ $errors->first('telefono2') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                                <label for="ocupacion">Ocupación</label>
                                <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="{{ $user->ocupacion }}" required>
                                @if ($errors->has('ocupacion'))<span class="help-block"><strong>{{ $errors->first('ocupacion') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
                                @if ($errors->has('fecha_nacimiento'))<span class="help-block"><strong>{{ $errors->first('fecha_nacimiento') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}" @if ($rolusuario != 1) style="display:none" @endif>
                                <label for="id_tipo_usuario">Tipo de usuario</label>
                                <select id="id_tipo_usuario" name="id_tipo_usuario" class="form-control">
                                    @foreach ($tipousuarios as $tipousuario)
                                        @if ($tipousuario->estado != 0)
                                            <option {{ $user->id_tipo_usuario == $tipousuario->id ? 'selected' : '' }} value="{{ $tipousuario->id }}">{{ $tipousuario->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_tipo_usuario'))<span class="help-block"><strong>{{ $errors->first('id_tipo_usuario') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email">Correo electrónico</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                @if ($errors->has('email'))<span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>@endif
                            </div>

                            <div class="profile-field{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password">Nueva contraseña</label>
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password" placeholder="Déjala vacía para conservar la actual">
                                @if ($errors->has('password'))<span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>@endif
                            </div>

                            <div class="profile-field">
                                <label for="password-confirm">Confirmar contraseña</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>

                            <div class="profile-field{{ $errors->has('estado') ? ' has-error' : '' }}" @if ($rolusuario != 1) style="display:none" @endif>
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado" class="form-control">
                                    <option {{ $user->estado == 0 ? 'selected' : '' }} value="0">INACTIVO</option>
                                    <option {{ $user->estado == 1 ? 'selected' : '' }} value="1">ACTIVO</option>
                                </select>
                                @if ($errors->has('estado'))<span class="help-block"><strong>{{ $errors->first('estado') }}</strong></span>@endif
                            </div>

                            <div id="cdoctor" class="profile-field profile-span-full colorpicker @if ($rolusuario != 1 || $user->id == $id) oculto @endif">
                                <label for="color">Color en la agenda</label>
                                <div class="profile-color-picker">
                                    <input id="color" type="hidden" name="color" value="{{ $user->color }}">
                                    <span class="input-group-addon colorpicker-2x"><i></i></span>
                                    <span class="profile-field-note" style="margin:0 0 0 9px;">Selecciona el color que identificará tus citas.</span>
                                </div>
                                @if ($errors->has('color'))<span class="help-block"><strong>{{ $errors->first('color') }}</strong></span>@endif
                            </div>

                            <div id="especialidades" class="profile-field profile-span-full oculto">
                                <div class="profile-specialties">
                                    <span class="profile-specialties-title"><i class="fa fa-stethoscope" aria-hidden="true"></i>&nbsp; Especialidades médicas</span>
                                    <div class="profile-specialties-grid">
                                        @foreach($especialidades as $value)
                                            <label class="profile-specialty-option">
                                                <input name="lista[]" type="checkbox"
                                                    @foreach($especialidad as $value2)
                                                        @if($value->id == $value2->espid) checked @endif
                                                    @endforeach
                                                    value="{{ $value->id }}">
                                                <span>{{ $value->nombre }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button type="button" class="btn profile-secondary-btn" data-dismiss="modal">
                                <i class="fa fa-times" aria-hidden="true"></i> Cancelar
                            </button>
                            <button type="submit" class="btn profile-primary-btn">
                                <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <aside class="profile-card profile-photo-card">
                <div class="profile-card-header">
                    <span class="profile-card-header-icon"><i class="fa fa-camera" aria-hidden="true"></i></span>
                    <div>
                        <h3>Fotografía profesional</h3>
                        <p>Será visible en tu sesión y perfil clínico.</p>
                    </div>
                </div>

                <div class="profile-photo-summary">
                    <div class="profile-avatar-frame">
                        <img src="{{ $profileImageUrl }}" alt="Fotografía de {{ $user->nombre1 }} {{ $user->apellido1 }}" id="fotografia_usuario" onerror="this.onerror=null;this.src='{{ $defaultProfileImageUrl }}';">
                        <span class="profile-avatar-status" aria-label="Usuario activo"></span>
                    </div>
                    <strong>{{ $user->nombre1 }} {{ $user->apellido1 }}</strong>
                    <span>{{ $user->ocupacion ?: 'Profesional de salud' }}</span>
                </div>

                <form id="subir_imagen" name="subir_imagen" method="post" action="{{ route('user-management.subir_imagen_usuario', ['id' => $user->id]) }}" class="formarchivo profile-photo-form" enctype="multipart/form-data">
                    <input type="hidden" name="id_usuario_foto" value="{{ $user->id }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="carga" value="{{ $profileImageName }}">

                    <div class="{{ $errors->has('archivo') ? ' has-error' : '' }}">
                        <input name="archivo" id="archivo" type="file" class="archivo profile-file-input" accept="image/jpeg,image/png,image/gif,image/svg+xml" required>
                        <label for="archivo" class="profile-file-picker">
                            <span>
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                <strong>Seleccionar nueva fotografía</strong>
                                <small>Formato JPG, PNG, GIF o SVG</small>
                            </span>
                        </label>
                        <span class="profile-file-name" id="profile_file_name">Ningún archivo seleccionado</span>
                        @if ($errors->has('archivo'))<span class="help-block"><strong>{{ $errors->first('archivo') }}</strong></span>@endif
                    </div>

                    <div class="profile-photo-actions">
                        <button type="submit" class="btn profile-primary-btn">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Actualizar fotografía
                        </button>
                    </div>
                </form>
            </aside>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var tipoUsuario = document.getElementById('id_tipo_usuario');

        function actualizarCamposDoctor() {
            if (!tipoUsuario) return;
            var tipoSeleccionado = tipoUsuario.value;
            var esDoctor = false;

            @foreach ($tipousuarios as $value)
                @if($value->nombre == 'DOCTORES')
                    if (tipoSeleccionado == '{{ $value->id }}') esDoctor = true;
                @endif
            @endforeach

            $('#especialidades').toggleClass('oculto', !esDoctor);
            $('#cdoctor').toggleClass('oculto', !esDoctor);
        }

        actualizarCamposDoctor();
        $(tipoUsuario).on('change', actualizarCamposDoctor);

        var $pais = $('#id_pais');
        var $provincia = $('#id_provincia_perfil');
        var $ciudad = $('#ciudad');
        var $idCiudad = $('#id_ciudad_usuario');
        var provinciasUrl = '{{ url('catalogos-geograficos/paises') }}';
        var ciudadesUrl = '{{ url('catalogos-geograficos/provincias') }}';

        function llenarSelect($select, items, placeholder, usarNombre) {
            $select.empty().append($('<option>', { value: '', text: placeholder }));
            $.each(items || [], function(_, item) {
                var option = $('<option>', {
                    value: usarNombre ? item.nombre : item.id,
                    text: item.nombre
                });
                if (usarNombre) option.attr('data-id', item.id);
                $select.append(option);
            });
        }

        $pais.on('change', function() {
            llenarSelect($provincia, [], 'Cargando provincias...', false);
            llenarSelect($ciudad, [], 'Selecciona una ciudad', true);
            $idCiudad.val('');
            if (!this.value) {
                llenarSelect($provincia, [], 'Selecciona una provincia o estado', false);
                return;
            }
            $.getJSON(provinciasUrl + '/' + this.value + '/provincias', function(data) {
                llenarSelect($provincia, data, 'Selecciona una provincia o estado', false);
            }).fail(function() {
                llenarSelect($provincia, [], 'No se pudieron cargar las provincias', false);
            });
        });

        $provincia.on('change', function() {
            llenarSelect($ciudad, [], 'Cargando ciudades...', true);
            $idCiudad.val('');
            if (!this.value) {
                llenarSelect($ciudad, [], 'Selecciona una ciudad', true);
                return;
            }
            $.getJSON(ciudadesUrl + '/' + this.value + '/ciudades', function(data) {
                llenarSelect($ciudad, data, 'Selecciona una ciudad', true);
            }).fail(function() {
                llenarSelect($ciudad, [], 'No se pudieron cargar las ciudades', true);
            });
        });

        $ciudad.on('change', function() {
            $idCiudad.val($(this).find('option:selected').attr('data-id') || '');
        });

        $('#archivo').on('change', function(event) {
            var archivo = event.target.files && event.target.files[0];
            $('#profile_file_name').text(archivo ? archivo.name : 'Ningún archivo seleccionado');

            if (archivo && archivo.type && archivo.type.indexOf('image/') === 0 && window.FileReader) {
                var reader = new FileReader();
                reader.onload = function(loadEvent) {
                    $('#fotografia_usuario').attr('src', loadEvent.target.result);
                };
                reader.readAsDataURL(archivo);
            }
        });
    });
</script>
