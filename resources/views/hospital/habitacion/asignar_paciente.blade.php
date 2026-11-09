@extends('layouts.app-template-h')
@section('content')
@php
    $nombrePaciente = trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2);
    $habitacion = $cama->habitacion;
    $piso = !is_null($habitacion) ? $habitacion->piso : null;
    $tipoHabitacion = !is_null($habitacion) ? $habitacion->tipo : null;
    $nombrePiso = !is_null($piso) && !empty($piso->nombre_piso) ? $piso->nombre_piso : 'Piso no especificado';
    $nombreHabitacion = !is_null($habitacion) ? 'Habitación '.$habitacion->codigo : 'Habitación no disponible';
    $nombreTipo = !is_null($tipoHabitacion) && !empty($tipoHabitacion->nombre) ? $tipoHabitacion->nombre : (!is_null($tipoHabitacion) ? $tipoHabitacion->descripcion : 'Hospitalización');
    $nombreSeguro = !is_null($paciente->seguro) ? $paciente->seguro->nombre : 'Sin seguro registrado';
    $sexoPaciente = $paciente->sexo == 1 ? 'Hombre' : 'Mujer';
    $estadoCama = $cama->estado == 1 ? 'Libre' : ($cama->estado == 2 ? 'Preparación' : ($cama->estado == 3 ? 'Ocupada' : 'No disponible'));
    $flujoIngreso = isset($traspaso->id_tipo) && (int) $traspaso->id_tipo === 2 ? 'Emergencia' : 'Hospitalización';
@endphp

<style>
    .bed-assignment-page {
        --ba-navy: #07527d;
        --ba-blue: #087eb9;
        --ba-teal: #12aaa4;
        --ba-ink: #07365a;
        --ba-muted: #637d92;
        --ba-line: #c9dfeb;
        --ba-soft: #edf8fc;
        padding: 18px 22px 34px;
        background: #f2f9fc;
        min-height: calc(100vh - 74px);
        color: var(--ba-ink);
    }
    .bed-assignment-shell { max-width: 1500px; margin: 0 auto; }
    .bed-assignment-breadcrumb {
        display: flex; align-items: center; gap: 8px; margin: 0 0 14px;
        color: #6c879b; font-size: 12px; font-weight: 600;
    }
    .bed-assignment-breadcrumb a { color: #087db8; text-decoration: none; }
    .bed-assignment-hero {
        display: flex; align-items: center; justify-content: space-between; gap: 24px;
        padding: 22px 24px; margin-bottom: 16px; border-radius: 18px;
        color: #fff; background: linear-gradient(110deg, #074f79 0%, #087fb7 54%, #13aaa4 100%);
        box-shadow: 0 14px 30px rgba(7, 82, 125, .14); overflow: hidden; position: relative;
    }
    .bed-assignment-hero:after {
        content: ''; position: absolute; width: 260px; height: 260px; right: -95px; top: -150px;
        border: 1px solid rgba(255,255,255,.16); border-radius: 50%;
    }
    .bed-assignment-hero-main { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; }
    .bed-assignment-hero-icon {
        width: 60px; height: 60px; border: 1px solid rgba(255,255,255,.35); border-radius: 16px;
        background: rgba(255,255,255,.12); display: flex; align-items: center; justify-content: center; font-size: 26px;
    }
    .bed-assignment-kicker { margin-bottom: 3px; color: #bdf7f2; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .bed-assignment-hero h1 { margin: 0 0 4px; font-size: 27px; font-weight: 800; color: #fff; }
    .bed-assignment-hero p { margin: 0; font-size: 13px; color: rgba(255,255,255,.9); }
    .bed-assignment-back {
        position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 16px; border: 1px solid rgba(255,255,255,.5); border-radius: 10px;
        color: #fff; background: rgba(255,255,255,.1); font-weight: 700; text-decoration: none;
    }
    .bed-assignment-back:hover { color: #fff; background: rgba(255,255,255,.2); text-decoration: none; }
    .bed-assignment-summary {
        display: grid; grid-template-columns: minmax(320px, 1.15fr) minmax(480px, 1.85fr);
        background: #fff; border: 1px solid var(--ba-line); border-radius: 16px; overflow: hidden;
        box-shadow: 0 10px 28px rgba(7, 54, 90, .07); margin-bottom: 16px;
    }
    .bed-patient-summary { padding: 20px 22px; display: flex; align-items: center; gap: 16px; border-right: 1px solid var(--ba-line); }
    .bed-patient-avatar {
        flex: 0 0 74px; width: 74px; height: 74px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; background: #e0f4fb;
        border: 4px solid #fff; box-shadow: 0 0 0 2px #b7ddea, 0 8px 18px rgba(8,126,185,.14);
        color: #0785c1; font-size: 36px; position: relative;
    }
    .bed-patient-avatar:after { content: ''; position: absolute; right: 2px; bottom: 5px; width: 13px; height: 13px; background: #25ba72; border: 2px solid #fff; border-radius: 50%; }
    .bed-patient-label { color: var(--ba-muted); font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .bed-patient-name { margin: 3px 0 7px; color: var(--ba-ink); font-size: 19px; line-height: 1.2; font-weight: 800; }
    .bed-patient-meta { display: flex; flex-wrap: wrap; gap: 7px; }
    .bed-patient-meta span, .bed-assignment-badge {
        display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; border-radius: 999px;
        background: #edf7fb; color: #41677f; font-size: 11px; font-weight: 700;
    }
    .bed-destination { padding: 18px 22px; }
    .bed-destination-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .bed-destination-heading h2 { margin: 0; font-size: 15px; font-weight: 800; }
    .bed-assignment-badge { background: #e7f8f2; color: #08774d; }
    .bed-destination-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .bed-destination-item { padding: 11px 12px; border: 1px solid #d7e7ef; border-radius: 11px; background: #f8fcfe; min-width: 0; }
    .bed-destination-item small { display: block; margin-bottom: 3px; color: #7890a2; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .bed-destination-item strong { display: block; color: var(--ba-ink); font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bed-assignment-notice {
        display: flex; align-items: flex-start; gap: 12px; padding: 13px 15px; margin-bottom: 16px;
        border: 1px solid #bfe5dc; border-left: 4px solid #19ad84; border-radius: 11px; background: #effaf7;
        color: #245d54; font-size: 12px; line-height: 1.45;
    }
    .bed-assignment-notice i { color: #0b9b74; margin-top: 2px; }
    .bed-assignment-card {
        margin-bottom: 16px; background: #fff; border: 1px solid var(--ba-line); border-radius: 15px;
        overflow: hidden; box-shadow: 0 8px 24px rgba(7,54,90,.05);
    }
    .bed-assignment-card-head {
        display: flex; align-items: center; justify-content: space-between; gap: 14px;
        padding: 14px 18px; border-bottom: 1px solid var(--ba-line); background: #f0f9fc;
    }
    .bed-assignment-card-title { display: flex; align-items: center; gap: 11px; }
    .bed-assignment-card-icon {
        width: 38px; height: 38px; border-radius: 10px; background: #daf2f8; color: #0684bd;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .bed-assignment-card h2 { margin: 0; font-size: 16px; font-weight: 800; }
    .bed-assignment-card-head p { margin: 2px 0 0; color: #718a9e; font-size: 11px; }
    .bed-assignment-card-body { padding: 18px; }
    .bed-assignment-section-title {
        display: flex; align-items: center; gap: 8px; margin: 4px 0 13px; color: #0a557e;
        font-size: 11px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase;
    }
    .bed-assignment-section-title:after { content: ''; height: 1px; background: #d9e8ef; flex: 1; }
    .bed-assignment-grid { display: grid; gap: 14px; grid-template-columns: repeat(12, minmax(0, 1fr)); }
    .bed-field { grid-column: span 3; min-width: 0; }
    .bed-field.span-4 { grid-column: span 4; }
    .bed-field.span-6 { grid-column: span 6; }
    .bed-field.span-12 { grid-column: span 12; }
    .bed-field label { display: block; margin: 0 0 6px; color: #244d69; font-size: 11px; font-weight: 800; }
    .bed-field .form-control {
        height: 42px; border: 1px solid #c8deea; border-radius: 9px; background: #fff; color: #173e59;
        font-size: 12px; box-shadow: none;
    }
    .bed-field textarea.form-control { height: auto; min-height: 92px; padding: 11px 12px; resize: vertical; line-height: 1.45; }
    .bed-field .form-control[readonly] { background: #f7fbfd; color: #47657a; }
    .bed-field .form-control:focus { border-color: #13a8bd; box-shadow: 0 0 0 3px rgba(19,168,189,.1); }
    .bed-field-alert textarea { border-color: #f0d5a0 !important; background: #fffaf0 !important; }
    .bed-assignment-actions {
        position: sticky; bottom: 0; z-index: 4; display: flex; align-items: center; justify-content: space-between; gap: 18px;
        padding: 15px 18px; border: 1px solid var(--ba-line); border-radius: 14px; background: rgba(255,255,255,.96);
        box-shadow: 0 -8px 24px rgba(7,54,90,.08); backdrop-filter: blur(8px);
    }
    .bed-assignment-actions-copy strong { display: block; font-size: 13px; }
    .bed-assignment-actions-copy span { color: #6c8598; font-size: 11px; }
    .bed-assignment-action-buttons { display: flex; gap: 9px; }
    .bed-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 17px; border-radius: 9px; font-size: 12px; font-weight: 800; }
    .bed-btn-cancel { border: 1px solid #bcd3df; color: #356078; background: #fff; }
    .bed-btn-primary { border: 0; color: #fff; background: linear-gradient(100deg, #087db8, #10a6a3); box-shadow: 0 7px 16px rgba(8,125,184,.2); }
    .bed-btn-primary:hover, .bed-btn-primary:focus { color: #fff; filter: brightness(.97); }
    .bed-btn[disabled] { opacity: .7; cursor: wait; }
    @media (max-width: 1100px) {
        .bed-assignment-summary { grid-template-columns: 1fr; }
        .bed-patient-summary { border-right: 0; border-bottom: 1px solid var(--ba-line); }
        .bed-destination-grid { grid-template-columns: repeat(2, 1fr); }
        .bed-field, .bed-field.span-4 { grid-column: span 6; }
    }
    @media (max-width: 700px) {
        .bed-assignment-page { padding: 12px 10px 24px; }
        .bed-assignment-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
        .bed-assignment-hero h1 { font-size: 22px; }
        .bed-assignment-back { width: 100%; justify-content: center; }
        .bed-patient-summary { align-items: flex-start; }
        .bed-destination-grid { grid-template-columns: 1fr; }
        .bed-field, .bed-field.span-4, .bed-field.span-6 { grid-column: span 12; }
        .bed-assignment-actions { position: static; align-items: stretch; flex-direction: column; }
        .bed-assignment-action-buttons, .bed-btn { width: 100%; }
    }
</style>

<div class="content bed-assignment-page">
    <div class="bed-assignment-shell">
        <div class="bed-assignment-breadcrumb">
            <a href="{{route('hospital.index')}}"><i class="fa fa-arrow-left"></i> Área médica</a>
            <span>›</span><a href="{{route('hospital.gcuartos')}}">Gestión de camas</a><span>›</span><span>Asignación</span>
        </div>

        <section class="bed-assignment-hero">
            <div class="bed-assignment-hero-main">
                <div class="bed-assignment-hero-icon"><i class="fa fa-bed"></i></div>
                <div>
                    <div class="bed-assignment-kicker">Gestión hospitalaria</div>
                    <h1>Asignar cama al paciente</h1>
                    <p>Verifica la información clínica y confirma el destino antes de realizar la asignación.</p>
                </div>
            </div>
            <a href="{{route('hospital.gcuartos')}}" class="bed-assignment-back"><i class="fa fa-arrow-left"></i> Regresar a gestión de camas</a>
        </section>

        <form action="{{route('hospitalizacion.store')}}" id="formstore" method="post">
            {{ csrf_field() }}
            <input id="id_paciente" type="hidden" name="id_paciente" value="{{$paciente->id}}">
            <input type="hidden" name="id_habitacion" value="{{$habitacion->id}}">
            <input type="hidden" name="id_cama" value="{{$cama->id}}">
            <input type="hidden" name="traspaso" value="{{$traspaso->id}}">

            <section class="bed-assignment-summary">
                <div class="bed-patient-summary">
                    <div class="bed-patient-avatar" aria-hidden="true"><i class="fa fa-user"></i></div>
                    <div>
                        <div class="bed-patient-label">Paciente seleccionado</div>
                        <div class="bed-patient-name">{{$nombrePaciente}}</div>
                        <div class="bed-patient-meta">
                            <span><i class="fa fa-id-card"></i> {{$paciente->id}}</span>
                            <span><i class="fa fa-venus-mars"></i> {{$sexoPaciente}}</span>
                            <span><i class="fa fa-calendar"></i> <b id="edadResumen">--</b> años</span>
                        </div>
                    </div>
                </div>
                <div class="bed-destination">
                    <div class="bed-destination-heading">
                        <h2><i class="fa fa-map-marker"></i> Destino de la asignación</h2>
                        <span class="bed-assignment-badge"><i class="fa fa-check-circle"></i> Cama {{$estadoCama}}</span>
                    </div>
                    <div class="bed-destination-grid">
                        <div class="bed-destination-item"><small>Servicio</small><strong>{{$flujoIngreso}}</strong></div>
                        <div class="bed-destination-item"><small>Piso</small><strong>{{$nombrePiso}}</strong></div>
                        <div class="bed-destination-item"><small>Habitación</small><strong>{{$nombreHabitacion}} · {{$nombreTipo}}</strong></div>
                        <div class="bed-destination-item"><small>Cama</small><strong>{{$cama->codigo}}</strong></div>
                    </div>
                </div>
            </section>

            <div class="bed-assignment-notice">
                <i class="fa fa-info-circle"></i>
                <div><strong>Confirmación clínica:</strong> al guardar, la cama <strong>{{$cama->codigo}}</strong> quedará ocupada por <strong>{{$nombrePaciente}}</strong>. Revisa alergias y observaciones antes de continuar.</div>
            </div>

            <section class="bed-assignment-card">
                <div class="bed-assignment-card-head">
                    <div class="bed-assignment-card-title">
                        <div class="bed-assignment-card-icon"><i class="fa fa-user-md"></i></div>
                        <div><h2>Información clínica</h2><p>Datos principales que deben revisarse antes de asignar la cama.</p></div>
                    </div>
                </div>
                <div class="bed-assignment-card-body">
                    <div class="bed-assignment-section-title"><i class="fa fa-id-card"></i> Identificación del paciente</div>
                    <div class="bed-assignment-grid">
                        <div class="bed-field span-6"><label for="nombre">Paciente</label><input readonly type="text" value="{{$nombrePaciente}}" id="nombre" name="nombre" class="form-control"></div>
                        <div class="bed-field"><label for="identificacion">Identificación</label><input readonly type="text" id="identificacion" name="identificacion" class="form-control" value="{{$paciente->id}}"></div>
                        <div class="bed-field"><label for="edad">Edad</label><input readonly type="number" id="edad" name="edad" class="form-control" placeholder="Edad"></div>
                        <div class="bed-field"><label for="sexo">Sexo</label><input readonly type="text" id="sexo" class="form-control" value="{{$sexoPaciente}}"></div>
                        <div class="bed-field"><label for="cortesia">Cortesía</label><input readonly type="text" id="cortesia" class="form-control" placeholder="Sin cortesía registrada"></div>
                    </div>

                    <div class="bed-assignment-section-title" style="margin-top:20px"><i class="fa fa-heartbeat"></i> Alertas y observaciones</div>
                    <div class="bed-assignment-grid">
                        <div class="bed-field span-6 bed-field-alert">
                            <label for="alergia"><i class="fa fa-exclamation-triangle"></i> Alergias registradas</label>
                            <textarea class="form-control" id="alergia" name="alergia" rows="3" readonly>@forelse($paciente->a_alergias as $x){{!is_null($x->principio_activo) ? $x->principio_activo->nombre : 'Alergia registrada'}}&#10;@empty Sin alergias registradas @endforelse</textarea>
                        </div>
                        <div class="bed-field span-6"><label for="observacion">Observaciones para la hospitalización</label><textarea class="form-control" id="observacion" name="observacion" rows="3" placeholder="Agrega una indicación relevante para la atención...">{{$traspaso->observaciones}}</textarea></div>
                        <div class="bed-field span-4"><label for="antpato">Antecedentes patológicos</label><textarea class="form-control" id="antpato" name="antpato" rows="3" readonly>{{$paciente->antecedentes_pat}}</textarea></div>
                        <div class="bed-field span-4"><label for="antfami">Antecedentes familiares</label><textarea class="form-control" name="antfami" id="antfami" rows="3" readonly>{{$paciente->antecedentes_fam}}</textarea></div>
                        <div class="bed-field span-4"><label for="antquiru">Antecedentes quirúrgicos</label><textarea class="form-control" name="antquiru" id="antquiru" rows="3" readonly>{{$paciente->antecedentes_quir}}</textarea></div>
                    </div>
                </div>
            </section>

            <section class="bed-assignment-card">
                <div class="bed-assignment-card-head">
                    <div class="bed-assignment-card-title">
                        <div class="bed-assignment-card-icon"><i class="fa fa-address-book"></i></div>
                        <div><h2>Afiliación y contacto</h2><p>Información complementaria para identificar y contactar al paciente.</p></div>
                    </div>
                </div>
                <div class="bed-assignment-card-body">
                    <div class="bed-assignment-grid">
                        <div class="bed-field"><label for="seguro">Seguro</label><input readonly type="text" id="seguro" name="seguro" value="{{$nombreSeguro}}" class="form-control"></div>
                        <div class="bed-field"><label for="estcivil">Estado civil</label><input readonly type="text" id="estcivil" class="form-control" value="{{$paciente->estado_civil ? 'Casado' : 'Soltero'}}"></div>
                        <div class="bed-field"><label for="telefono">Teléfono</label><input readonly type="text" id="telefono" value="{{$paciente->telefono1}}" class="form-control"></div>
                        <div class="bed-field"><label for="celular">Celular</label><input readonly type="text" id="celular" value="{{$paciente->telefono2}}" class="form-control"></div>
                        <div class="bed-field"><label for="ciudad">Ciudad</label><input readonly type="text" id="ciudad" class="form-control" value="{{$paciente->ciudad}}"></div>
                        <div class="bed-field"><label for="fechadenacimi">Fecha de nacimiento</label><input readonly type="text" id="fechadenacimi" class="form-control" value="{{$paciente->fecha_nacimiento}}"></div>
                        <div class="bed-field"><label for="ciudadpro">Ciudad de nacimiento</label><input readonly type="text" id="ciudadpro" class="form-control" value="{{$paciente->lugar_nacimiento}}"></div>
                        <div class="bed-field"><label for="religion">Religión</label><input readonly type="text" id="religion" class="form-control" value="{{$paciente->religion}}" placeholder="Sin registrar"></div>
                        <div class="bed-field"><label for="ocupacion">Ocupación</label><input readonly type="text" id="ocupacion" class="form-control" value="{{$paciente->ocupacion}}"></div>
                        <div class="bed-field"><label for="trabajo">Trabajo</label><input readonly type="text" id="trabajo" class="form-control" value="{{$paciente->ocupacion}}"></div>
                        <div class="bed-field"><label for="gruposa">Grupo sanguíneo</label><input readonly type="text" id="gruposa" class="form-control" value="{{$paciente->grupo_sanguineo}}" placeholder="Sin registrar"></div>
                        <div class="bed-field span-6"><label for="email">Correo electrónico</label><input readonly type="email" id="email" class="form-control" value="{{$paciente->mail_primera_vez}}"></div>
                        <div class="bed-field span-6"><label for="direccion">Dirección</label><input readonly type="text" id="direccion" class="form-control" value="{{$paciente->direccion}}"></div>
                    </div>
                </div>
            </section>

            <div class="bed-assignment-actions">
                <div class="bed-assignment-actions-copy"><strong>¿La información y la cama son correctas?</strong><span>La asignación ocupará la cama y enviará al paciente al mapa hospitalario.</span></div>
                <div class="bed-assignment-action-buttons">
                    <a href="{{route('hospital.gcuartos')}}" class="bed-btn bed-btn-cancel"><i class="fa fa-times"></i> Cancelar</a>
                    <button class="bed-btn bed-btn-primary" type="button" onclick="guardar(this)" id="btnFetch"><i class="fa fa-bed"></i> Confirmar asignación</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        calcular_edad('{{$paciente->fecha_nacimiento}}');
    });

    function calcular_edad(fecha) {
        if (!fecha) { return; }
        var nacimiento = new Date(String(fecha).replace(/-/g, '/'));
        if (isNaN(nacimiento.getTime())) { return; }
        var hoy = new Date();
        var edad = hoy.getFullYear() - nacimiento.getFullYear();
        var mes = hoy.getMonth() - nacimiento.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) { edad--; }
        $('#edad').val(edad);
        $('#edadResumen').text(edad);
    }

    function mostrarMensajeAsignacion(titulo, mensaje, tipo) {
        if (window.Swal && typeof Swal.fire === 'function') {
            return Swal.fire({ title: titulo, text: mensaje, icon: tipo, type: tipo, confirmButtonText: 'Entendido' });
        }
        if (window.swal) {
            return swal({ title: titulo, text: mensaje, type: tipo, confirmButtonText: 'Entendido' });
        }
        alert(titulo + (mensaje ? '\n' + mensaje : ''));
        return null;
    }

    function guardar(boton) {
        var $boton = $(boton);
        if ($boton.prop('disabled')) { return; }
        var textoOriginal = $boton.html();
        $boton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Asignando cama...');

        $.ajax({
            type: 'POST',
            url: "{{route('hospitalizacion.store')}}",
            headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() },
            dataType: 'json',
            cache: false,
            data: $('#formstore').serialize(),
            success: function(data) {
                if (data && data.state === 'Guardado Correctamente') {
                    var mensaje = 'El paciente fue asignado correctamente a la cama {{$cama->codigo}}.';
                    if (data.warning) { mensaje += ' Aviso: ' + data.warning; }
                    var aviso = mostrarMensajeAsignacion('Cama asignada', mensaje, 'success');
                    if (aviso && typeof aviso.then === 'function') {
                        aviso.then(function() { window.location.href = "{{route('hospital.gcuartos')}}"; });
                    } else {
                        window.location.href = "{{route('hospital.gcuartos')}}";
                    }
                    return;
                }
                mostrarMensajeAsignacion('No se pudo asignar la cama', data && data.state ? data.state : 'Revisa la información e inténtalo nuevamente.', 'warning');
                $boton.prop('disabled', false).html(textoOriginal);
            },
            error: function(xhr) {
                var respuesta = xhr && xhr.responseJSON ? xhr.responseJSON : null;
                if (respuesta && respuesta.state) {
                    mostrarMensajeAsignacion('No se pudo asignar la cama', respuesta.state, 'warning');
                    $boton.prop('disabled', false).html(textoOriginal);
                    return;
                }
                mostrarMensajeAsignacion('No se pudo completar la asignación', 'Ocurrió un inconveniente al guardar. Inténtalo nuevamente o comunícate con Sistemas.', 'error');
                $boton.prop('disabled', false).html(textoOriginal);
            }
        });
    }
</script>
@endsection
