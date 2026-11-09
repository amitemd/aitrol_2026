@extends('layouts.app-template-h')
@section('content')
@php
    $habitacion = $cama->habitacion;
    $tipoHabitacion = !is_null($habitacion) ? $habitacion->tipo : null;
    $piso = !is_null($habitacion) ? $habitacion->piso : null;
    $nombrePaciente = trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2);
    $nombreHabitacion = !is_null($habitacion) && !empty($habitacion->codigo) ? $habitacion->codigo : 'No especificada';
    $nombreCama = !empty($cama->codigo) ? $cama->codigo : (!empty($cama->nombre) ? $cama->nombre : 'Cama #'.$cama->id);
    $nombrePiso = !is_null($piso) && !empty($piso->nombre_piso) ? $piso->nombre_piso : 'No especificado';
    $nombreTipo = !is_null($tipoHabitacion) && !empty($tipoHabitacion->nombre) ? $tipoHabitacion->nombre : 'Hospitalización';
    $nombreSeguro = !is_null($paciente->seguro) && !empty($paciente->seguro->nombre) ? $paciente->seguro->nombre : 'Sin seguro registrado';
    $correoPaciente = !empty($paciente->mail_opcional) ? $paciente->mail_opcional : (!is_null($paciente->usuario) && !empty($paciente->usuario->email) ? $paciente->usuario->email : 'No registrado');
    $sexoValor = strtoupper(trim((string) $paciente->sexo));
    $sexoPaciente = in_array($sexoValor, array('1', 'M', 'MASCULINO', 'HOMBRE')) ? 'Masculino' : (in_array($sexoValor, array('2', 'F', 'FEMENINO', 'MUJER')) ? 'Femenino' : 'No registrado');
    $estadosCiviles = array(1 => 'Soltero/a', 2 => 'Casado/a', 3 => 'Viudo/a', 4 => 'Divorciado/a', 5 => 'Unión libre', 6 => 'Unión de hecho');
    $estadoCivilPaciente = isset($estadosCiviles[(int) $paciente->estadocivil]) ? $estadosCiviles[(int) $paciente->estadocivil] : (!empty($paciente->estadocivil) ? $paciente->estadocivil : 'No registrado');
    $fechaIngreso = !empty($hospitalizacion->fecha) ? date('d/m/Y H:i', strtotime($hospitalizacion->fecha)) : 'No registrada';
    $fechaNacimientoTexto = !is_null($fechaNacimiento) ? $fechaNacimiento->format('d/m/Y') : 'No registrada';
    $puedeEditarPaciente = in_array((int) Auth::user()->id_tipo_usuario, array(1, 4, 5, 11, 20));
    $alergias = array();
    foreach ($paciente->a_alergias as $alergia) {
        if (!is_null($alergia->principio_activo) && !empty($alergia->principio_activo->nombre)) {
            $alergias[] = $alergia->principio_activo->nombre;
        }
    }
@endphp

<style>
    .patient-stay-page {
        --ps-navy: #07527d;
        --ps-blue: #087fba;
        --ps-teal: #12aaa4;
        --ps-ink: #07365a;
        --ps-muted: #667f92;
        --ps-line: #c9dfeb;
        --ps-soft: #eef8fc;
        padding: 18px 22px 36px;
        min-height: calc(100vh - 74px);
        background: #f2f9fc;
        color: var(--ps-ink);
    }
    .patient-stay-shell { max-width: 1540px; margin: 0 auto; }
    .patient-stay-breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; color: #6c879b; font-size: 12px; font-weight: 600; }
    .patient-stay-breadcrumb a { color: var(--ps-blue); text-decoration: none; }
    .patient-stay-hero {
        position: relative; display: flex; align-items: center; justify-content: space-between; gap: 22px;
        margin-bottom: 16px; padding: 22px 24px; overflow: hidden; border-radius: 18px;
        color: #fff; background: linear-gradient(110deg, #074f79 0%, #087fb7 54%, #13aaa4 100%);
        box-shadow: 0 14px 30px rgba(7, 82, 125, .14);
    }
    .patient-stay-hero:after { content: ''; position: absolute; width: 280px; height: 280px; right: -105px; top: -155px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; }
    .patient-stay-hero-main { position: relative; z-index: 1; display: flex; align-items: center; gap: 16px; }
    .patient-stay-hero-icon { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; flex: 0 0 60px; border: 1px solid rgba(255,255,255,.35); border-radius: 16px; background: rgba(255,255,255,.12); font-size: 25px; }
    .patient-stay-kicker { margin-bottom: 3px; color: #bdf7f2; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .patient-stay-hero h1 { margin: 0 0 4px; color: #fff; font-size: 27px; font-weight: 800; }
    .patient-stay-hero p { margin: 0; color: rgba(255,255,255,.9); font-size: 13px; }
    .patient-stay-actions { position: relative; z-index: 1; display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; }
    .patient-stay-btn { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 15px; border: 1px solid transparent; border-radius: 10px; font-size: 12px; font-weight: 800; text-decoration: none; cursor: pointer; }
    .patient-stay-btn:hover, .patient-stay-btn:focus { text-decoration: none; }
    .patient-stay-btn-light { color: #fff; border-color: rgba(255,255,255,.48); background: rgba(255,255,255,.11); }
    .patient-stay-btn-light:hover { color: #fff; background: rgba(255,255,255,.2); }
    .patient-stay-btn-white { color: #086c9e; border-color: #fff; background: #fff; }
    .patient-stay-btn-white:hover { color: #07547c; background: #effaff; }
    .patient-stay-btn-danger { color: #fff; border-color: rgba(255,255,255,.22); background: #e95e62; }
    .patient-stay-btn-danger:hover { color: #fff; background: #d94d52; }
    .patient-stay-summary {
        display: grid; grid-template-columns: minmax(330px, 1.1fr) minmax(560px, 1.9fr);
        margin-bottom: 16px; overflow: hidden; border: 1px solid var(--ps-line); border-radius: 16px; background: #fff;
        box-shadow: 0 10px 28px rgba(7,54,90,.07);
    }
    .patient-stay-person { display: flex; align-items: center; gap: 16px; padding: 20px 22px; border-right: 1px solid var(--ps-line); }
    .patient-stay-avatar { position: relative; width: 76px; height: 76px; flex: 0 0 76px; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; border-radius: 50%; color: #0785c1; background: #e0f4fb; box-shadow: 0 0 0 2px #b7ddea, 0 8px 18px rgba(8,126,185,.14); font-size: 36px; }
    .patient-stay-avatar:after { content: ''; position: absolute; right: 2px; bottom: 5px; width: 13px; height: 13px; border: 2px solid #fff; border-radius: 50%; background: #25ba72; }
    .patient-stay-label { color: var(--ps-muted); font-size: 10px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
    .patient-stay-name { margin: 3px 0 8px; color: var(--ps-ink); font-size: 19px; line-height: 1.2; font-weight: 800; }
    .patient-stay-tags { display: flex; flex-wrap: wrap; gap: 7px; }
    .patient-stay-tag { display: inline-flex; align-items: center; gap: 5px; padding: 5px 9px; border-radius: 999px; color: #41677f; background: #edf7fb; font-size: 11px; font-weight: 700; }
    .patient-stay-location { padding: 18px 22px; }
    .patient-stay-location-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .patient-stay-location-head h2 { margin: 0; font-size: 15px; font-weight: 800; }
    .patient-stay-status { padding: 6px 10px; border-radius: 999px; color: #08774d; background: #e7f8f2; font-size: 11px; font-weight: 800; }
    .patient-stay-location-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; }
    .patient-stay-stat { min-width: 0; padding: 11px 12px; border: 1px solid #d7e7ef; border-radius: 11px; background: #f8fcfe; }
    .patient-stay-stat small { display: block; margin-bottom: 3px; color: #7890a2; font-size: 9px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .patient-stay-stat strong { display: block; overflow: hidden; color: var(--ps-ink); font-size: 12px; white-space: nowrap; text-overflow: ellipsis; }
    .patient-journey { margin-bottom:16px; overflow:hidden; border:1px solid var(--ps-line); border-radius:16px; background:#fff; box-shadow:0 8px 24px rgba(7,54,90,.06); }
    .patient-journey-head { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 18px; border-bottom:1px solid var(--ps-line); background:linear-gradient(90deg,#f0f9fc,#f8fcfd); }
    .patient-journey-title { display:flex; align-items:center; gap:11px; }
    .patient-journey-title-icon { width:38px; height:38px; display:flex; align-items:center; justify-content:center; flex:0 0 38px; border-radius:11px; color:#fff; background:linear-gradient(135deg,var(--ps-blue),var(--ps-teal)); box-shadow:0 7px 16px rgba(8,127,186,.18); }
    .patient-journey h2 { margin:0; color:var(--ps-ink); font-size:16px; font-weight:800; }
    .patient-journey-head p { margin:2px 0 0; color:#718a9e; font-size:11px; }
    .patient-journey-current { max-width:370px; padding:7px 11px; overflow:hidden; border-radius:999px; color:#08775d; background:#e4f8f3; font-size:10px; font-weight:800; white-space:nowrap; text-overflow:ellipsis; }
    .patient-journey-scroll { overflow-x:auto; padding:20px 18px 17px; scrollbar-width:thin; scrollbar-color:#b7d8e5 transparent; }
    .patient-journey-track { position:relative; display:flex; align-items:flex-start; min-width:max-content; padding:0 5px; }
    .patient-journey-track:before { content:''; position:absolute; left:50px; right:50px; top:22px; height:3px; border-radius:3px; background:linear-gradient(90deg,#9bd2df,#5fb8cf,#35b9b0); }
    .patient-journey-step { position:relative; z-index:1; width:176px; flex:0 0 176px; padding:0 14px; text-align:center; }
    .patient-journey-node { width:46px; height:46px; display:flex; align-items:center; justify-content:center; margin:0 auto 9px; border:4px solid #fff; border-radius:50%; color:#0a739f; background:#daf1f7; box-shadow:0 0 0 2px #acd7e4; font-size:16px; }
    .patient-journey-step.completed .patient-journey-node { color:#087c74; background:#dcf5f0; box-shadow:0 0 0 2px #8fd8ca; }
    .patient-journey-step.attention .patient-journey-node { color:#b56d08; background:#fff2d9; box-shadow:0 0 0 2px #f2cd83; }
    .patient-journey-step.current .patient-journey-node { color:#fff; background:linear-gradient(135deg,#087fba,#12aaa4); box-shadow:0 0 0 3px #bde9e6,0 8px 18px rgba(8,127,186,.24); }
    .patient-journey-step.current .patient-journey-node:after { content:''; position:absolute; width:58px; height:58px; border:1px solid rgba(18,170,164,.35); border-radius:50%; animation:journeyPulse 2s ease-out infinite; }
    @keyframes journeyPulse { 0% { transform:scale(.78); opacity:.8; } 100% { transform:scale(1.25); opacity:0; } }
    .patient-journey-label { display:block; color:var(--ps-ink); font-size:12px; font-weight:900; line-height:1.2; }
    .patient-journey-detail { display:-webkit-box; min-height:30px; margin-top:4px; overflow:hidden; color:#5f7a8e; font-size:10px; line-height:1.35; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .patient-journey-time { display:block; margin-top:5px; color:#8aa0ae; font-size:9px; font-weight:700; }
    .patient-journey-badge { display:inline-block; margin-top:5px; padding:3px 7px; border-radius:999px; color:#08775d; background:#e4f8f3; font-size:8px; font-weight:900; letter-spacing:.05em; text-transform:uppercase; }
    .patient-stay-card { margin-bottom: 16px; overflow: hidden; border: 1px solid var(--ps-line); border-radius: 15px; background: #fff; box-shadow: 0 8px 24px rgba(7,54,90,.05); }
    .patient-stay-card-head { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 14px 18px; border-bottom: 1px solid var(--ps-line); background: #f0f9fc; }
    .patient-stay-card-title { display: flex; align-items: center; gap: 11px; }
    .patient-stay-card-icon { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 10px; color: #0684bd; background: #daf2f8; font-size: 16px; }
    .patient-stay-card h2 { margin: 0; color: var(--ps-ink); font-size: 16px; font-weight: 800; }
    .patient-stay-card-head p { margin: 2px 0 0; color: #718a9e; font-size: 11px; }
    .patient-stay-card-body { padding: 18px; }
    .patient-stay-section-title { display: flex; align-items: center; gap: 8px; margin: 2px 0 13px; color: #0a557e; font-size: 10px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .patient-stay-section-title:after { content: ''; height: 1px; flex: 1; background: #d9e8ef; }
    .patient-stay-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 14px; }
    .patient-stay-field { min-width: 0; grid-column: span 3; }
    .patient-stay-field.span-4 { grid-column: span 4; }
    .patient-stay-field.span-6 { grid-column: span 6; }
    .patient-stay-field.span-12 { grid-column: span 12; }
    .patient-stay-field label { display: block; margin: 0 0 6px; color: #244d69; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .patient-stay-value, .patient-stay-field .form-control { min-height: 42px; display: flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid #c8deea; border-radius: 9px; color: #173e59; background: #f8fcfe; font-size: 12px; box-shadow: none; }
    .patient-stay-value i { width: 14px; color: #68a2bf; text-align: center; }
    .patient-stay-field textarea.form-control { min-height: 90px; display: block; resize: vertical; line-height: 1.45; }
    .patient-stay-empty { color: #8aa0af; font-style: italic; }
    .patient-stay-note { margin-top: 14px; padding: 12px 14px; border-left: 4px solid #16a78a; border-radius: 9px; color: #315e5b; background: #effaf7; font-size: 11px; line-height: 1.45; }
    .modal-backdrop { opacity: .45 !important; }
    @media (max-width: 1150px) {
        .patient-stay-summary { grid-template-columns: 1fr; }
        .patient-stay-person { border-right: 0; border-bottom: 1px solid var(--ps-line); }
        .patient-stay-location-grid { grid-template-columns: repeat(3, 1fr); }
        .patient-stay-field { grid-column: span 6; }
    }
    @media (max-width: 760px) {
        .patient-stay-page { padding: 12px 10px 26px; }
        .patient-stay-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
        .patient-stay-hero h1 { font-size: 22px; }
        .patient-stay-actions, .patient-stay-btn { width: 100%; }
        .patient-stay-location-grid { grid-template-columns: 1fr 1fr; }
        .patient-stay-field, .patient-stay-field.span-4, .patient-stay-field.span-6 { grid-column: span 12; }
        .patient-journey-head { align-items:flex-start; flex-direction:column; }
        .patient-journey-current { max-width:100%; }
        .patient-journey-step { width:152px; flex-basis:152px; }
    }
</style>

<div class="modal fade" id="modalprescripcion" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"></div></div></div>
<div class="modal fade" id="modalenfermeria" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"></div></div></div>
<div class="modal fade" id="modalnew" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content" id="date"></div></div></div>
<div class="modal fade" id="modalimagenes" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content" id="enferm"></div></div></div>

<div class="content patient-stay-page" id="area_cambiar">
    <div class="patient-stay-shell">
        <nav class="patient-stay-breadcrumb" aria-label="Navegación">
            <a href="{{route('hospital.index')}}"><i class="fa fa-arrow-left"></i> Área médica</a>
            <span>›</span><a href="{{route('hospital.gcuartos')}}">Gestión de camas</a><span>›</span><span>Paciente hospitalizado</span>
        </nav>

        <form action="{{route('hospital.eliminar')}}" id="form1" method="POST" onsubmit="return confirmarLiberacion();">
            {{ csrf_field() }}
            <input id="id_tipo" name="id_tipo" type="hidden" value="{{!is_null($tipoHabitacion) ? $tipoHabitacion->id : ''}}">
            <input id="id_cama" name="id_cama" type="hidden" value="{{$cama->id}}">
            <input id="id_hospitalizacion" name="id_hospitalizacion" type="hidden" value="{{$hospitalizacion->id}}">
            <input id="indetificacion" name="indetificacion" type="hidden" value="{{$paciente->id}}">

            <section class="patient-stay-hero">
                <div class="patient-stay-hero-main">
                    <div class="patient-stay-hero-icon"><i class="fa fa-hospital-o"></i></div>
                    <div>
                        <div class="patient-stay-kicker">Seguimiento hospitalario</div>
                        <h1>Paciente hospitalizado</h1>
                        <p>Consulta la información clínica, la ubicación actual y los datos de contacto del paciente.</p>
                    </div>
                </div>
                <div class="patient-stay-actions">
                    @if($puedeEditarPaciente)
                        <a href="{{route('paciente.edit', ['paciente' => $paciente->id])}}" class="patient-stay-btn patient-stay-btn-white"><i class="fa fa-pencil"></i> Editar datos</a>
                    @endif
                    <button type="button" class="patient-stay-btn patient-stay-btn-light" onclick="modalinfermeria()"><i class="fa fa-picture-o"></i> Imágenes</button>
                    <button type="button" class="patient-stay-btn patient-stay-btn-light" onclick="modalshow()"><i class="fa fa-medkit"></i> Cirugía</button>
                    <a href="{{route('hospital.documents.index', ['id_solicitud' => $workflowSolicitudId])}}" class="patient-stay-btn patient-stay-btn-light"><i class="fa fa-folder-open"></i> Documentos</a>
                    <a href="{{route('epicrisis.mostrar_final', ['id_solicitud' => $workflowSolicitudId])}}" class="patient-stay-btn patient-stay-btn-danger"><i class="fa fa-sign-out"></i> Alta y salida</a>
                    <a href="{{route('hospital.gcuartos')}}" class="patient-stay-btn patient-stay-btn-light"><i class="fa fa-arrow-left"></i> Regresar</a>
                </div>
            </section>

            <section class="patient-stay-summary">
                <div class="patient-stay-person">
                    <div class="patient-stay-avatar" aria-hidden="true"><i class="fa fa-user"></i></div>
                    <div>
                        <div class="patient-stay-label">Paciente ingresado</div>
                        <div class="patient-stay-name">{{$nombrePaciente}}</div>
                        <div class="patient-stay-tags">
                            <span class="patient-stay-tag"><i class="fa fa-id-card-o"></i> {{$paciente->id}}</span>
                            <span class="patient-stay-tag"><i class="fa fa-birthday-cake"></i> {{is_null($edad) ? 'Edad no disponible' : $edad.' años'}}</span>
                            <span class="patient-stay-tag"><i class="fa fa-venus-mars"></i> {{$sexoPaciente}}</span>
                        </div>
                    </div>
                </div>
                <div class="patient-stay-location">
                    <div class="patient-stay-location-head">
                        <h2><i class="fa fa-bed"></i> Ubicación y estancia</h2>
                        <span class="patient-stay-status"><i class="fa fa-circle"></i> Hospitalización activa</span>
                    </div>
                    <div class="patient-stay-location-grid">
                        <div class="patient-stay-stat"><small>Ingreso</small><strong>{{$fechaIngreso}}</strong></div>
                        <div class="patient-stay-stat"><small>Estancia</small><strong>{{$diasIngreso == 0 ? 'Ingreso de hoy' : $diasIngreso.' día'.($diasIngreso == 1 ? '' : 's')}}</strong></div>
                        <div class="patient-stay-stat"><small>Piso</small><strong>{{$nombrePiso}}</strong></div>
                        <div class="patient-stay-stat"><small>Habitación</small><strong>{{$nombreHabitacion}}</strong></div>
                        <div class="patient-stay-stat"><small>Cama / tipo</small><strong>{{$nombreCama}} · {{$nombreTipo}}</strong></div>
                    </div>
                </div>
            </section>

            <section class="patient-journey" aria-labelledby="patientJourneyTitle">
                <header class="patient-journey-head">
                    <div class="patient-journey-title">
                        <span class="patient-journey-title-icon"><i class="fa fa-map-o"></i></span>
                        <div><h2 id="patientJourneyTitle">Ruta asistencial del episodio</h2><p>Secuencia cronológica de servicios, estudios y cambios de ubicación registrados.</p></div>
                    </div>
                    <span class="patient-journey-current"><i class="fa fa-map-marker"></i> {{$journey['current_location']}}</span>
                </header>
                <div class="patient-journey-scroll" role="region" aria-label="Trazabilidad horizontal del paciente" tabindex="0">
                    <div class="patient-journey-track">
                        @foreach($journey['events'] as $journeyEvent)
                            <article class="patient-journey-step {{$journeyEvent['status']}}">
                                <span class="patient-journey-node"><i class="fa {{$journeyEvent['icon']}}"></i></span>
                                <strong class="patient-journey-label">{{$journeyEvent['label']}}</strong>
                                <span class="patient-journey-detail" title="{{$journeyEvent['detail']}}">{{$journeyEvent['detail']}}</span>
                                <time class="patient-journey-time">{{$journeyEvent['at']}}</time>
                                @if($journeyEvent['status'] === 'current')<span class="patient-journey-badge">Actual</span>@endif
                                @if($journeyEvent['status'] === 'attention')<span class="patient-journey-badge" style="color:#9a620d;background:#fff2d9;">En proceso</span>@endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="patient-stay-card" aria-labelledby="episodeOrdersTitle">
                <header class="patient-stay-card-head">
                    <div class="patient-stay-card-title">
                        <div class="patient-stay-card-icon"><i class="fa fa-flask"></i></div>
                        <div>
                            <h2 id="episodeOrdersTitle">Laboratorio e imagenología</h2>
                            <p>Últimas órdenes del episodio, incluidas las iniciadas en Emergencia.</p>
                        </div>
                    </div>
                    @if($journey['encounter_id'] && $ordersAvailable)
                        <a href="{{route('clinical_orders.index', ['encounter_id' => $journey['encounter_id']])}}" class="patient-stay-btn patient-stay-btn-white" style="border-color:#b9d7e5;">
                            Ver órdenes y resultados
                        </a>
                    @endif
                </header>
                <div class="patient-stay-card-body">
                    @forelse($episodeOrders as $order)
                        @php
                            $orderStatus = [
                                'DRAFT' => 'Borrador', 'ORDERED' => 'Solicitada',
                                'ACKNOWLEDGED' => 'Recibida', 'IN_PROGRESS' => 'En proceso',
                                'COMPLETED' => 'Completada', 'CANCELLED' => 'Cancelada',
                            ];
                        @endphp
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #e1edf3;">
                            <div>
                                <strong>{{$order->category === 'LABORATORY' ? 'Laboratorio' : 'Imagenología'}} #{{$order->source_id ?: $order->id}}</strong>
                                <div style="color:#667f92;font-size:12px;">{{$order->description}}</div>
                            </div>
                            <span class="patient-stay-tag">{{$orderStatus[$order->status] ?? $order->status}}</span>
                        </div>
                    @empty
                        <p style="margin:0;color:#667f92;">No hay órdenes de laboratorio o imagenología registradas en este episodio.</p>
                    @endforelse
                </div>
            </section>

            <section class="patient-stay-card">
                <header class="patient-stay-card-head">
                    <div class="patient-stay-card-title">
                        <div class="patient-stay-card-icon"><i class="fa fa-user-md"></i></div>
                        <div><h2>Información clínica y personal</h2><p>Datos de identificación calculados desde el registro vigente del paciente.</p></div>
                    </div>
                    @if($puedeEditarPaciente)
                        <a href="{{route('paciente.edit', ['paciente' => $paciente->id])}}" class="patient-stay-btn patient-stay-btn-white" style="border-color:#b9d7e5;"><i class="fa fa-pencil"></i> Actualizar paciente</a>
                    @endif
                </header>
                <div class="patient-stay-card-body">
                    <div class="patient-stay-section-title"><i class="fa fa-id-card-o"></i> Identificación</div>
                    <div class="patient-stay-grid">
                        <div class="patient-stay-field span-4"><label>Paciente</label><div class="patient-stay-value"><i class="fa fa-user"></i>{{$nombrePaciente}}</div></div>
                        <div class="patient-stay-field"><label>Identificación</label><div class="patient-stay-value"><i class="fa fa-id-card"></i>{{$paciente->id}}</div></div>
                        <div class="patient-stay-field"><label>Fecha de nacimiento</label><div class="patient-stay-value"><i class="fa fa-calendar"></i>{{$fechaNacimientoTexto}}</div></div>
                        <div class="patient-stay-field"><label>Edad actual</label><div class="patient-stay-value"><i class="fa fa-hourglass-half"></i>{{is_null($edad) ? 'No calculable' : $edad.' años'}}</div></div>
                        <div class="patient-stay-field"><label>Sexo</label><div class="patient-stay-value"><i class="fa fa-venus-mars"></i>{{$sexoPaciente}}</div></div>
                    </div>

                    <div class="patient-stay-section-title" style="margin-top:18px;"><i class="fa fa-heartbeat"></i> Antecedentes y alertas</div>
                    <div class="patient-stay-grid">
                        <div class="patient-stay-field span-6"><label>Alergias registradas</label><textarea class="form-control" id="alergia" readonly>{{count($alergias) ? implode(', ', $alergias) : 'Sin alergias registradas'}}</textarea></div>
                        <div class="patient-stay-field span-6"><label>Observaciones del ingreso</label><textarea class="form-control" id="observacion" name="observacion" readonly>{{!empty($hospitalizacion->detalle) ? $hospitalizacion->detalle : 'Sin observaciones registradas'}}</textarea></div>
                        <div class="patient-stay-field span-4"><label>Antecedentes patológicos</label><textarea class="form-control" id="antpato" readonly>{{!empty($paciente->antecedentes_pat) ? $paciente->antecedentes_pat : 'Sin información registrada'}}</textarea></div>
                        <div class="patient-stay-field span-4"><label>Antecedentes familiares</label><textarea class="form-control" id="antfami" readonly>{{!empty($paciente->antecedentes_fam) ? $paciente->antecedentes_fam : 'Sin información registrada'}}</textarea></div>
                        <div class="patient-stay-field span-4"><label>Antecedentes quirúrgicos</label><textarea class="form-control" id="antquiru" readonly>{{!empty($paciente->antecedentes_quir) ? $paciente->antecedentes_quir : 'Sin información registrada'}}</textarea></div>
                    </div>
                </div>
            </section>

            <section class="patient-stay-card">
                <header class="patient-stay-card-head">
                    <div class="patient-stay-card-title">
                        <div class="patient-stay-card-icon"><i class="fa fa-address-book-o"></i></div>
                        <div><h2>Contacto y filiación</h2><p>Información utilizada para comunicación, cobertura y localización.</p></div>
                    </div>
                </header>
                <div class="patient-stay-card-body">
                    <div class="patient-stay-grid">
                        <div class="patient-stay-field span-4"><label>Correo electrónico</label><div class="patient-stay-value"><i class="fa fa-envelope-o"></i>{{$correoPaciente}}</div></div>
                        <div class="patient-stay-field span-4"><label>Teléfono convencional</label><div class="patient-stay-value"><i class="fa fa-phone"></i>{{!empty($paciente->telefono1) ? $paciente->telefono1 : 'No registrado'}}</div></div>
                        <div class="patient-stay-field span-4"><label>Teléfono celular</label><div class="patient-stay-value"><i class="fa fa-mobile"></i>{{!empty($paciente->telefono2) ? $paciente->telefono2 : 'No registrado'}}</div></div>
                        <div class="patient-stay-field span-4"><label>Ciudad</label><div class="patient-stay-value"><i class="fa fa-map-marker"></i>{{!empty($paciente->ciudad) ? $paciente->ciudad : 'No registrada'}}</div></div>
                        <div class="patient-stay-field span-4"><label>Dirección</label><div class="patient-stay-value"><i class="fa fa-home"></i>{{!empty($paciente->direccion) ? $paciente->direccion : 'No registrada'}}</div></div>
                        <div class="patient-stay-field span-4"><label>Seguro</label><div class="patient-stay-value"><i class="fa fa-shield"></i>{{$nombreSeguro}}</div></div>
                        <div class="patient-stay-field span-4"><label>Estado civil</label><div class="patient-stay-value"><i class="fa fa-users"></i>{{$estadoCivilPaciente}}</div></div>
                        <div class="patient-stay-field span-4"><label>Ocupación</label><div class="patient-stay-value"><i class="fa fa-briefcase"></i>{{!empty($paciente->ocupacion) ? $paciente->ocupacion : 'No registrada'}}</div></div>
                        <div class="patient-stay-field span-4"><label>Grupo sanguíneo</label><div class="patient-stay-value"><i class="fa fa-tint"></i>{{!empty($paciente->gruposanguineo) ? $paciente->gruposanguineo : 'No registrado'}}</div></div>
                    </div>
                    <div class="patient-stay-note"><i class="fa fa-info-circle"></i> La edad se calcula automáticamente con la fecha de nacimiento y no se guarda como un número fijo. Al corregir la fecha en <strong>Editar datos</strong>, la edad se actualizará en esta ficha.</div>
                </div>
            </section>
        </form>
    </div>
</div>

<script type="text/javascript">
    function confirmarLiberacion() {
        return window.confirm('¿Confirma que desea liberar la cama {{$nombreCama}}? Esta acción cambiará su disponibilidad.');
    }

    jQuery('body').on('click', '[data-toggle="modal"]', function() {
        if (typeof remoto_href === 'undefined' || remoto_href != jQuery(this).data('remote')) {
            window.remoto_href = jQuery(this).data('remote');
            jQuery(jQuery(this).data('target')).removeData('bs.modal');
            jQuery(jQuery(this).data('target')).find('.modal-body').empty();
            jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
        }
    });

    function modalshow() {
        $.ajax({
            type: 'get',
            url: "{{route('hospitalizacion.modal_quirofano')}}",
            data: {
                id_tipo: $('#id_tipo').val(),
                id_cama: $('#id_cama').val(),
                id_paciente: $('#indetificacion').val(),
                id_hospitalizacion: $('#id_hospitalizacion').val()
            },
            datatype: 'json',
            success: function(data) { $('#date').empty().html(data); $('#modalnew').modal(); }
        });
    }

    function modalinfermeria() {
        $.ajax({
            type: 'get',
            url: "{{route('cuartos.imagenes')}}",
            data: $('#form1').serialize(),
            datatype: 'json',
            success: function(data) { $('#enferm').empty().html(data); $('#modalimagenes').modal(); }
        });
    }
</script>
@endsection
