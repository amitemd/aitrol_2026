@extends('layouts.app-template-h')
@section('content')
<style>
  .emergency-admission-page {
    --emergency-navy: #074d78;
    --emergency-blue: #087db6;
    --emergency-teal: #11a8a3;
    --emergency-ink: #06365a;
    --emergency-muted: #667f96;
    --emergency-border: #cfe2ed;
    --emergency-soft: #eef8fb;
    max-width: 1810px;
    margin: 0 auto;
    padding: 18px 22px 34px;
  }

  .emergency-admission-page .admission-hero {
    align-items: center;
    background: linear-gradient(118deg, var(--emergency-navy) 0%, var(--emergency-blue) 52%, var(--emergency-teal) 100%);
    border-radius: 18px;
    box-shadow: 0 14px 30px rgba(5, 72, 112, .16);
    color: #fff;
    display: flex;
    justify-content: space-between;
    margin: 0 0 18px;
    overflow: hidden;
    padding: 22px 24px;
    position: relative;
  }

  .emergency-admission-page .admission-hero::after {
    border: 1px solid rgba(255, 255, 255, .15);
    border-radius: 50%;
    content: '';
    height: 190px;
    position: absolute;
    right: 62px;
    top: -112px;
    width: 190px;
  }

  .emergency-admission-page .admission-hero__content {
    align-items: center;
    display: flex;
    min-width: 0;
    position: relative;
    z-index: 1;
  }

  .emergency-admission-page .admission-hero__icon {
    align-items: center;
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .35);
    border-radius: 14px;
    display: flex;
    flex: 0 0 58px;
    font-size: 25px;
    height: 58px;
    justify-content: center;
    margin-right: 17px;
  }

  .emergency-admission-page .admission-hero__eyebrow {
    color: #a9f1ec;
    display: block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.25px;
    margin-bottom: 3px;
    text-transform: uppercase;
  }

  .emergency-admission-page .admission-hero h1 {
    color: #fff;
    font-size: 25px;
    font-weight: 800;
    line-height: 1.2;
    margin: 0;
  }

  .emergency-admission-page .admission-hero p {
    color: rgba(255, 255, 255, .88);
    font-size: 13px;
    margin: 5px 0 0;
  }

  .emergency-admission-page .admission-back {
    align-items: center;
    background: rgba(255, 255, 255, .13);
    border: 1px solid rgba(255, 255, 255, .45);
    border-radius: 10px;
    color: #fff;
    display: inline-flex;
    font-size: 13px;
    font-weight: 700;
    gap: 8px;
    min-height: 42px;
    padding: 0 18px;
    position: relative;
    transition: background .2s ease, transform .2s ease;
    z-index: 1;
  }

  .emergency-admission-page .admission-back:hover,
  .emergency-admission-page .admission-back:focus {
    background: rgba(255, 255, 255, .23);
    color: #fff;
    transform: translateY(-1px);
  }

  .emergency-admission-page .admission-card {
    background: #fff;
    border: 1px solid var(--emergency-border);
    border-radius: 16px;
    box-shadow: 0 12px 28px rgba(35, 81, 109, .08);
    margin: 0;
    overflow: visible;
  }

  .emergency-admission-page .admission-card > .card-header {
    align-items: center;
    background: linear-gradient(90deg, #f1fafc 0%, #e8f7fa 100%);
    border-bottom: 1px solid var(--emergency-border);
    border-radius: 16px 16px 0 0;
    display: flex;
    min-height: 76px;
    flex-wrap: nowrap;
    gap: 10px;
    padding: 15px 20px;
  }

  .emergency-admission-page .admission-card__heading {
    align-items: center;
    display: flex;
    min-width: 0;
  }

  .emergency-admission-page .admission-card__heading-icon,
  .emergency-admission-page .admission-section-title i {
    align-items: center;
    background: #dff3f7;
    border-radius: 10px;
    color: var(--emergency-blue);
    display: inline-flex;
    justify-content: center;
  }

  .emergency-admission-page .admission-card__heading-icon {
    flex: 0 0 42px;
    font-size: 17px;
    height: 42px;
    margin-right: 12px;
  }

  .emergency-admission-page .admission-card .card-title {
    color: var(--emergency-ink);
    float: none;
    font-size: 18px;
    font-weight: 800;
    margin: 0;
  }

  .emergency-admission-page .admission-card__subtitle {
    color: var(--emergency-muted);
    font-size: 12px;
    margin: 3px 0 0;
  }

  .emergency-admission-page .admission-card .card-tools {
    margin-left: auto;
    position: static;
    flex-shrink: 0;
  }

  .emergency-admission-page .admission-card .btn-card-tool {
    align-items: center;
    background: #fff;
    border: 1px solid var(--emergency-border);
    border-radius: 9px;
    color: var(--emergency-ink);
    display: inline-flex;
    height: 36px;
    justify-content: center;
    width: 40px;
  }

  .emergency-admission-page .admission-card .card-body {
    padding: 20px 20px 6px;
  }

  .emergency-admission-page .admission-section-title {
    align-items: center;
    background: linear-gradient(90deg, var(--emergency-soft), #f8fcfd);
    border-left: 3px solid #10a9bf;
    border-radius: 0 9px 9px 0;
    color: var(--emergency-ink);
    display: flex;
    font-size: 13px;
    font-weight: 800;
    gap: 9px;
    margin: 2px 0 16px;
    min-height: 40px;
    padding: 8px 12px;
  }

  .emergency-admission-page .admission-section-title:not(:first-of-type) {
    margin-top: 8px;
  }

  .emergency-admission-page .admission-section-title i {
    background: transparent;
    border-radius: 0;
    height: auto;
    width: auto;
  }

  .emergency-admission-page .form-row {
    margin-left: -6px;
    margin-right: -6px;
  }

  .emergency-admission-page .form-group {
    margin-bottom: 16px;
    padding-left: 6px;
    padding-right: 6px;
  }

  .emergency-admission-page .form-group label {
    color: #244760;
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1px;
    margin-bottom: 6px;
    min-height: 16px;
  }

  .emergency-admission-page .form-group label b {
    font-weight: inherit;
  }

  .emergency-admission-page .form-control,
  .emergency-admission-page .input-group .form-control {
    background-color: #fff;
    border: 1px solid #c7dce8;
    border-radius: 8px;
    box-shadow: none;
    color: #173f5c;
    font-size: 13px;
    height: 42px;
    padding: 8px 12px;
    transition: border-color .2s ease, box-shadow .2s ease;
  }

  .emergency-admission-page .form-control:focus {
    border-color: #12a7bf;
    box-shadow: 0 0 0 3px rgba(18, 167, 191, .12);
  }

  .emergency-admission-page .form-control::placeholder {
    color: #9aacb9;
  }

  .emergency-admission-page .input-group-addon {
    display: none;
  }

  .emergency-admission-page .card-footer {
    background: #f7fbfd;
    border-top: 1px solid var(--emergency-border);
    border-radius: 0 0 16px 16px;
    padding: 15px 20px;
  }

  .emergency-admission-page .admission-save {
    align-items: center;
    background: linear-gradient(110deg, var(--emergency-blue), #0b91b2);
    border: 0;
    border-radius: 9px;
    box-shadow: 0 8px 18px rgba(8, 125, 182, .2);
    display: inline-flex;
    font-size: 13px;
    font-weight: 800;
    gap: 8px;
    min-height: 43px;
    padding: 0 22px;
  }

  .emergency-admission-page .alert {
    border: 0;
    border-left: 4px solid #e7a321;
    border-radius: 9px;
    font-size: 13px;
  }

  .emergency-admission-page .form-group { display: flex; flex-direction: column; min-width: 0; }
  .emergency-admission-page .form-group > label { min-height: 30px; margin: 0 0 6px; padding: 0; display: flex; align-items: end; }
  .emergency-admission-page .admission-help { color: var(--emergency-muted); font-size: 13px; margin-bottom: 20px; }
  .emergency-admission-page .form-control[readonly] { background: #f1f6f9; }
  .emergency-admission-page .is-invalid { border-color: #dc3545; }
  .emergency-admission-page [hidden] { display: none !important; }
  .emergency-admission-page .admission-patient-results { border: 1px solid var(--emergency-border); border-radius: 8px; max-height: 240px; overflow-y: auto; }
  .emergency-admission-page .admission-patient-results button { display: block; width: 100%; text-align: left; padding: 10px 12px; border: 0; background: #fff; color: var(--emergency-ink); }
  .emergency-admission-page .admission-patient-results button:hover,
  .emergency-admission-page .admission-patient-results button:focus { background: var(--emergency-soft); }
  .emergency-admission-page #admission-feedback { white-space: pre-line; }
  .emergency-admission-page #admission-patient-status { color: var(--emergency-muted); margin-top: 5px; }

  @media (max-width: 767px) {
    .emergency-admission-page {
      padding: 12px 10px 24px;
    }

    .emergency-admission-page .admission-hero {
      align-items: stretch;
      flex-direction: column;
      padding: 18px;
    }

    .emergency-admission-page .admission-hero h1 {
      font-size: 20px;
    }

    .emergency-admission-page .admission-back {
      justify-content: center;
      margin-top: 16px;
      width: 100%;
    }

    .emergency-admission-page .admission-card > .card-header,
    .emergency-admission-page .admission-card .card-body,
    .emergency-admission-page .card-footer {
      padding-left: 14px;
      padding-right: 14px;
    }
  }
</style>

@php
    $patientData = $patientData ?? [];
    $linkingHospitalized = $ho_solicitud && (int) $ho_solicitud->estado_paso === 3 && !$ho_solicitud->id_agenda;
    $fieldMap = [
        'cedula'=>'id', 'f_nacimiento'=>'fecha_nacimiento', 'estado'=>'estadocivil',
        'zona'=>'zona_ur', 'empresa'=>'empresa_trabajo', 'parentesco'=>'parentesco_afinidad',
        'telefono_llamar'=>'telefono3',
    ];
    $fieldValue = function ($name) use ($patientData, $fieldMap) {
        if ($name === 'apellidos_nombres') {
            return trim(implode(' ', array_filter(array_map(function ($key) use ($patientData) {
                return $patientData[$key] ?? '';
            }, ['apellido1', 'apellido2', 'nombre1', 'nombre2']))));
        }
        return old($name, $patientData[$fieldMap[$name] ?? $name] ?? '');
    };
@endphp

<div class="content emergency-admission-page">

	<section class="content-header admission-hero">
		<div class="admission-hero__content">
			<div class="admission-hero__icon"><i class="fas fa-ambulance"></i></div>
			<div>
				<span class="admission-hero__eyebrow">Gestión de emergencias</span>
				<h1>{{ $linkingHospitalized ? 'Vincular Historia 008 del episodio hospitalario' : trans('emergencia.INGRESOPOREMERGENCIANUEVOINGRESO') }}</h1>
				<p>{{ $linkingHospitalized ? 'Complete los datos del ingreso original de Emergencia. La cama y el servicio actual se conservarán.' : 'Registra y confirma la información necesaria para la atención inmediata del paciente.' }}</p>
			</div>
		</div>
		<button type="button" onclick ="location.href='{{ $linkingHospitalized ? route('hospital.documents.index', ['id_solicitud' => $ho_solicitud->id]) : route('hospital.emergencia') }}'" class="btn admission-back"><i class="fas fa-arrow-left"></i> {{trans('emergencia.Regresar')}}</button>
    </section>

	<div class="row">
		<!-- 1.- Registro de Primera Admisión -->
        <div class="col-md-12">
			<div class="card admission-card">
				<div class="card-header with-border">
					<div class="admission-card__heading">
						<span class="admission-card__heading-icon"><i class="fas fa-notes-medical"></i></span>
						<div>
							<h3 class="card-title">{{ $linkingHospitalized ? 'Datos de admisión de Emergencia' : trans('emergencia.2RegistrodePrimeraAdmision') }}</h3>
							<p class="admission-card__subtitle">Completa los datos de identificación, contacto y cobertura del paciente.</p>
						</div>
					</div>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-intake-toggle aria-controls="form_emergencia" aria-expanded="true" aria-label="Contraer formulario"><i class="fa fa-minus" aria-hidden="true"></i></button>
					</div>
				</div>
				<!-- /.card-header -->
				<form method="post" id="form_emergencia" action="{{ route('emergencia.guardar') }}"
                    data-emergency-intake data-lookup-url="{{ url('hospital/emergencia/buscar') }}"
                    data-search-url="{{ route('buscar_paciente') }}" data-return-url="{{ $linkingHospitalized ? route('hospital.documents.index', ['id_solicitud' => $ho_solicitud->id]) : route('hospital.emergencia') }}"
                    data-locked-patient="{{ $ho_solicitud ? '1' : '0' }}">
					<input type="hidden" name="clinical_override" id="clinical_override" value="0">
					<input type="hidden" name="clinical_override_reason" id="clinical_override_reason" value="">
					{{ csrf_field() }}
					<input type="hidden" name="id_paso" value="2">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
									@if(session('message'))
									<div class="alert alert-warning alert-dismissible fade show" role="alert">
										{{session('message')}}
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									@endif

									<div id="admission-feedback" class="alert alert-danger" role="alert" tabindex="-1" hidden></div>
                                    <p class="admission-help">Seleccione la sala y el médico responsable. Complete los datos conocidos; el segundo nombre, segundo apellido y los datos complementarios son opcionales.</p>
                                    <div class="admission-section-title"><i class="fas fa-hospital"></i> Sala y responsable de la atención</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="id_sala">Sala de Emergencia</label>
							<select name="id_sala" id="id_sala" class="form-control" required>
								<option value="">Seleccione</option>
								@foreach($salas as $sala)
								<option value="{{$sala->id}}" @if(count($salas) === 1) selected @endif>{{$sala->nombre_sala}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-6">
							<label for="id_doctor">Médico responsable</label>
							<select name="id_doctor" id="id_doctor" class="form-control" required>
								<option value="">Seleccione</option>
								@foreach($doctores as $doctor)
								<option value="{{$doctor->id}}" @if((string) $doctor->id === (string) Auth::id()) selected @endif>{{$doctor->apellido1}} {{$doctor->nombre1}}</option>
								@endforeach
							</select>
						</div>
					</div>

                                    @if($salas->isEmpty() || $doctores->isEmpty())
                                    <div class="alert alert-warning" role="alert">{{ $salas->isEmpty() ? 'No hay salas de Emergencia configuradas para esta empresa.' : 'No hay médicos activos disponibles.' }} Solicite su configuración antes de continuar.</div>
                                    @endif
                                    <div class="admission-section-title"><i class="fas fa-id-card"></i> Identificación del paciente</div>
									<div class="form-row">
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="cedula"><b>{{trans('emergencia.CeduladeCiudadania')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="cedula" name="cedula" required @if($ho_solicitud) readonly @endif value="{{ $ho_solicitud ? $ho_solicitud->id_paciente : $fieldValue('cedula') }}">
											<input type="hidden" name="idsolicitud" id="idsolicitud" value="{{$id_solicitud}}" class="form-control" >
										</div>
										<div class="form-group col-md-8 col-xl-10">
											<label class="col-form-label-sm" for="apellidos_nombres"><b>{{trans('emergencia.ApellidosyNombres')}}</b></label>
											<input type="text" class="form-control form-control-sm nombre" name="apellidos_nombres" id="apellidos_nombres" placeholder="Buscar Por Apellidos y Nombres" @if($ho_solicitud) readonly @endif autocomplete="off" aria-controls="admission-patient-results" aria-describedby="admission-patient-status" value="{{ $fieldValue('apellidos_nombres') }}">
                                            <div id="admission-patient-results" class="admission-patient-results" hidden></div>
                                            <small id="admission-patient-status" role="status" aria-live="polite"></small>
										</div>


										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="apellido1"><b>{{trans('emergencia.Apellido1')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="apellido1" id="apellido1" autocomplete="off" required value="{{ $fieldValue('apellido1') }}">
										</div>
										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="apellido2"><b>{{trans('emergencia.Apellido2')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="apellido2" id="apellido2" autocomplete="off" value="{{ $fieldValue('apellido2') }}">
										</div>
										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="nombre1"><b>{{trans('emergencia.Nombre1')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="nombre1" id="nombre1" autocomplete="off" required value="{{ $fieldValue('nombre1') }}">
										</div>
										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="nombre2"><b>{{trans('emergencia.Nombre2')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="nombre2" id="nombre2" autocomplete="off" value="{{ $fieldValue('nombre2') }}">
										</div>

										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="ciudad"><b>{{trans('emergencia.Ciudad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad" autocomplete="off" value="{{ $fieldValue('ciudad') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="telefono1"><b>{{trans('emergencia.Telefono')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono1" name="telefono1" autocomplete="off" value="{{ $fieldValue('telefono1') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="telefono2"><b>{{trans('emergencia.Celular')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono2" name="telefono2" autocomplete="off" value="{{ $fieldValue('telefono2') }}">
										</div>
										<div class="form-group col-md-6">
											<label class="col-form-label-sm" for="direccion"><b>{{trans('emergencia.Direccionderesidencia')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="direccion" id="direccion" autocomplete="off" value="{{ $fieldValue('direccion') }}">
										</div>
									</div>
									<div class="admission-section-title"><i class="fas fa-map-marker-alt"></i> Domicilio y ubicación</div>
									<div class="form-row">

										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="barrio"><b>{{trans('emergencia.Barrio')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="barrio" name="barrio" value="{{ $fieldValue('barrio') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="parroquia"><b>{{trans('emergencia.Parroquia')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="parroquia" name="parroquia" value="{{ $fieldValue('parroquia') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="canton"><b>{{trans('emergencia.Canton')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="canton" name="canton" value="{{ $fieldValue('canton') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="provincia"><b>{{trans('emergencia.Provincia')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="provincia" name="provincia" value="{{ $fieldValue('provincia') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="zona"><b>{{trans('emergencia.ZonaUR')}}</b></label>
											<select class="form-control form-control-sm" id="zona" name="zona"><option value="">Seleccione</option>
												<option value="Urbana" @selected((string) $fieldValue('zona') === (string) 'Urbana')>{{trans('emergencia.Urbana')}}</option>
												<option value="Rural" @selected((string) $fieldValue('zona') === (string) 'Rural')>{{trans('emergencia.Rural')}}</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="f_nacimiento"><b>{{trans('emergencia.Fechadenacimiento')}}</b></label>

											<div class="input-group date">
								              <input type="date"  class="form-control form-control-sm" name="f_nacimiento" id="f_nacimiento" autocomplete="off" value="{{ $fieldValue('f_nacimiento') }}">
								              <div class="input-group-addon">
								                <i class="glyphicon glyphicon-remove-circle"></i>
								              </div>
								            </div>
										</div>

									</div>
									<div class="admission-section-title"><i class="fas fa-user-circle"></i> Información demográfica y social</div>
									<div class="form-row">


										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="nacionalidad"><b>{{trans('emergencia.Nacionalidad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="nacionalidad" name="nacionalidad" value="{{ $fieldValue('nacionalidad') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="grupo_cultural"><b>{{trans('emergencia.GrCultural')}}</b></label>
											<select class="form-control form-control-sm" id="grupo_cultural" name="grupo_cultural"><option value="">Seleccione</option>
												<option value="Mestizo" @selected((string) $fieldValue('grupo_cultural') === (string) 'Mestizo')>{{trans('emergencia.Mestizo')}}</option>
												<option value="Morisco" @selected((string) $fieldValue('grupo_cultural') === (string) 'Morisco')>{{trans('emergencia.Morisco')}}</option>
												<option value="Cholo" @selected((string) $fieldValue('grupo_cultural') === (string) 'Cholo')>{{trans('emergencia.Cholocoyote')}}</option>
												<option value="Mulatos" @selected((string) $fieldValue('grupo_cultural') === (string) 'Mulatos')>{{trans('emergencia.Mulatos')}}</option>
												<option value="Zambo" @selected((string) $fieldValue('grupo_cultural') === (string) 'Zambo')>{{trans('emergencia.Zambo')}}</option>
												<option value="Castizo" @selected((string) $fieldValue('grupo_cultural') === (string) 'Castizo')>{{trans('emergencia.Castizo')}}</option>
												<option value="Criollo" @selected((string) $fieldValue('grupo_cultural') === (string) 'Criollo')>{{trans('emergencia.Criollo')}}</option>
												<option value="Chino" @selected((string) $fieldValue('grupo_cultural') === (string) 'Chino')>{{trans('emergencia.Chino')}}</option>
											</select>
										</div>

										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="sexo"><b>{{trans('emergencia.Sexo')}}</b></label>

											<select class="form-control form-control-sm" id="sexo" name="sexo"><option value="">Seleccione</option>
												<option value="1" @selected((string) $fieldValue('sexo') === (string) '1')>{{trans('emergencia.Hombre')}}</option>
												<option value="2" @selected((string) $fieldValue('sexo') === (string) '2')>{{trans('emergencia.Mujer')}}</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="estado"><b>{{trans('emergencia.EstadoCivil')}}</b></label>
											<select class="form-control form-control-sm" id="estado" name="estado"><option value="">Seleccione</option>
												<option value="1" @selected((string) $fieldValue('estado') === (string) '1')> {{trans('emergencia.Soltero')}}</option>
												<option value="2" @selected((string) $fieldValue('estado') === (string) '2')> {{trans('emergencia.Casado')}}</option>
												<option value="3" @selected((string) $fieldValue('estado') === (string) '3')> {{trans('emergencia.Viudo')}}</option>
												<option value="4" @selected((string) $fieldValue('estado') === (string) '4')> {{trans('emergencia.Divorciado')}}</option>
												<option value="5" @selected((string) $fieldValue('estado') === (string) '5')> {{trans('emergencia.UnionLibre')}}</option>
												<option value="6" @selected((string) $fieldValue('estado') === (string) '6')> {{trans('emergencia.UniondeHecho')}}</option>
											</select>
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="instruccion"><b>{{trans('emergencia.Instruccion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="instruccion" name="instruccion" value="{{ $fieldValue('instruccion') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="ocupacion"><b>{{trans('emergencia.Ocupacion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="ocupacion" name="ocupacion" value="{{ $fieldValue('ocupacion') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="empresa"><b>{{trans('emergencia.Empresadondetrabaja')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="empresa" name="empresa" value="{{ $fieldValue('empresa') }}">
										</div>
									</div>
									<div class="admission-section-title"><i class="fas fa-shield-alt"></i> Cobertura y contacto de emergencia</div>
									<div class="form-row">


										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="id_seguro"><b>{{trans('emergencia.TipodeSeguro')}}</b></label>
											<select class="form-control form-control-sm" id="id_seguro" name="id_seguro" required><option value="">Seleccione</option>
												@foreach($seguros as $seg)
												<option value="{{$seg->id}}" @selected((string) $fieldValue('id_seguro') === (string) $seg->id)>{{$seg->nombre}}</option>
												@endforeach
											</select>
										</div>

										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="referido"><b>{{trans('emergencia.Referidode')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="referido" name="referido" value="{{ $fieldValue('referido') }}">
										</div>
										<div class="form-group col-md-4">
											<label class="col-form-label-sm" for="llamar_a"><b>{{trans('emergencia.Encasonecesariollamara')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="llamar_a" name="llamar_a" value="{{ $fieldValue('llamar_a') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="parentesco"><b>{{trans('emergencia.ParentescoAfinidad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="parentesco" name="parentesco" value="{{ $fieldValue('parentesco') }}">
										</div>

									</div>

									<div class="form-row">
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="telefono_inst_per_paci"><b>{{trans('emergencia.NTelefono')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono_inst_per_paci" name="telefono_inst_per_paci" value="{{ $fieldValue('telefono_inst_per_paci') }}">
										</div>
										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="direccion_familiar"><b>{{trans('emergencia.DireccionFamiliar')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="direccion_familiar" name="direccion_familiar" value="{{ $fieldValue('direccion_familiar') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="forma_llegada"><b>{{trans('emergencia.FormadeLlegada')}}</b></label>
											<select name="forma_llegada" id="forma_llegada" class="form-control form-control-sm"><option value="">Seleccione</option>
												<option value="Ambulatorio" @selected((string) $fieldValue('forma_llegada') === (string) 'Ambulatorio')>{{trans('emergencia.Ambulatorio')}}</option>
												<option value="Ambulancia" @selected((string) $fieldValue('forma_llegada') === (string) 'Ambulancia')>{{trans('emergencia.Ambulancia')}}</option>
												<option value="Otro" @selected((string) $fieldValue('forma_llegada') === (string) 'Otro')>{{trans('emergencia.OtroTransporte')}}</option>
											</select>
										</div>

										<div class="form-group col-md-6 col-xl-3">
											<label class="col-form-label-sm" for="fuente_informacion"><b>{{trans('emergencia.FuentedeInformacion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="fuente_informacion" name="fuente_informacion" value="{{ $fieldValue('fuente_informacion') }}">
										</div>
										<div class="form-group col-md-4 col-xl-2">
											<label class="col-form-label-sm" for="telefono_llamar"><b>{{trans('emergencia.NTelefono')}}</b></label>
											<input type="tel" class="form-control form-control-sm" id="telefono_llamar" name="telefono_llamar" value="{{ $fieldValue('telefono_llamar') }}">
										</div>


									</div>

							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- ./card-body -->
					<div class="card-footer">
                        <div data-clinical-conflict class="alert alert-warning" hidden>
                            <strong>Confirmar excepción clínica</strong>
                            <p data-conflict-message></p>
                            <label for="admission-override-reason">Motivo clínico de la excepción (mínimo 10 caracteres). La autorización quedará auditada.</label>
                            <textarea id="admission-override-reason" class="form-control mb-2" rows="3"></textarea>
                            <button type="button" class="btn btn-warning" data-authorize>Autorizar y continuar</button>
                            <button type="button" class="btn btn-outline-secondary" data-cancel-override>Cancelar</button>
                        </div>
						<div class="row">
							<button type="submit" class="btn btn-primary admission-save ml-3 mr-2" @disabled($salas->isEmpty() || $doctores->isEmpty())><i class="far fa-save"></i> {{trans('emergencia.Guardar')}}</button>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.card-footer -->
				</form>
			</div>
			<!-- /.card -->
        </div>
		<!-- /.col -->

	</div>

</div>

<script src="{{ asset('js/emergency-intake.js') }}?v=1"></script>
@endsection
