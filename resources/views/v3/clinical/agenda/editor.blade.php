@include('v3.clinical.operations._styles')
@include('v3.clinical.agenda._doctor_styles')
@php
    $editing = $appointment !== null;
    $admitted = $editing && ((int) $appointment->estado_cita === 4 || $historyCount > 0);
    $confirmed = $editing && (int) $appointment->estado_cita === 1;
    $origin = $origin ?? '';
    $surgeryBooking = !$editing && $origin === 'hospital-cirugia';
    $type = $surgeryBooking ? 5 : (int) old('tipo', $editing ? $appointment->proc_consul : $defaultType);
    $automaticAppointmentType = (int) old('tipo_cita', $editing ? $appointment->tipo_cita : ($defaultAppointmentType ?? 0));
    $admissionTypeValue = (string) old('est_amb_hos', $editing ? $appointment->est_amb_hos : '0');
    $insuranceValue = (string) old('id_seguro', $editing ? $appointment->id_seguro : '');
    $surgeryStart = old('hora_quirurgica', $surgeryDetail ? date('Y-m-d\TH:i', strtotime($surgeryDetail->hora_quirurgica)) : $defaultStart);
    $backUrl = $surgeryBooking ? route('hospital.index_modulos', ['id_paso' => 5]) : route('v3.doctor-agenda.calendar', ['doctor' => $doctor->id]);
@endphp
<div class="co-page da-page">
  @if(empty($embeddedSurgery))
  <section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-calendar-plus"></i></div><div><div class="co-eyebrow">AGENDA MÉDICA · V3</div><h1>{{$editing?'Editar agenda':($origin==='hospital-cirugia'?'Agendar cirugía':'Nueva agenda')}}</h1><p>Dr(a). {{$doctor->display_name}} · Validación de horario y sala en tiempo real al guardar.</p></div></div><a class="btn btn-light" href="{{$backUrl}}"><i class="fas fa-arrow-left mr-1"></i> {{$origin==='hospital-cirugia'?'Volver a Cirugía':'Volver a la agenda'}}</a></section>
  @endif
  @if(session('agenda_success'))<div class="alert alert-success da-alert">{{session('agenda_success')}}</div>@endif
  @if($errors->any())<div class="alert alert-danger da-alert"><strong>No se pudo guardar:</strong> {{$errors->first()}}</div>@endif
  <section class="co-card"><div class="da-form-wrap"><div class="da-panel"><header class="da-panel__head">
    <h3><i class="fas fa-notes-medical mr-1" aria-hidden="true"></i> {{ !empty($embeddedSurgery) ? 'Datos de la cirugía' : 'Datos de la agenda' }}</h3>
    @if(empty($embeddedSurgery))
        <p>Los campos visibles se adaptan al tipo seleccionado.</p>
    @else
        <p>{{ $doctor->display_name }} · {{ $doctor->id }}</p>
    @endif
  </header><div class="da-panel__body">
    <form class="js-activity-form" method="post" data-availability-url="{{route('v3.doctor-agenda.availability',array('doctor'=>$doctor->id))}}" data-appointment-id="{{$editing?$appointment->id:''}}" data-confirmed="{{$confirmed?'1':'0'}}" action="{{$editing?route('v3.doctor-agenda.update',array('doctor'=>$doctor->id,'appointment'=>$appointment->id)):route('v3.doctor-agenda.store',array('doctor'=>$doctor->id))}}">
        @csrf
        @if($editing)
            @method('PATCH')
        @endif
        @if($origin !== '')
            <input type="hidden" name="origen" value="{{ $origin }}">
        @endif
@if($admitted)
<div class="alert alert-info">Atención admitida: horario, médicos, sala, seguro, especialidad y primera vez/consecutiva bloqueados. Puedes actualizar Referido por, CIE-10 y observaciones.</div>
@if(in_array((int)Auth::user()->id_tipo_usuario,array(1,4,5,20,22),true))<a class="btn btn-primary mb-3" href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'recibo-cobro'))}}" target="_blank" rel="noopener"><i class="fas fa-receipt mr-1"></i> Recibo de cobro</a>@endif
@foreach(array('tipo'=>'proc_consul','id_paciente'=>'id_paciente','id_sala'=>'id_sala','id_doctor2'=>'id_doctor2','id_doctor3'=>'id_doctor3','estado_cita'=>'estado_cita','id_seguro'=>'id_seguro','tipo_cita'=>'tipo_cita') as $input=>$column)
<input type="hidden" name="{{$input}}" value="{{$appointment->$column}}">
@endforeach
@endif
@if($confirmed && !$admitted)
<div class="alert alert-info">Agenda confirmada: cambie el estado a <strong>Reagendar</strong> para habilitar fecha, horario, sala y médicos asignados. Al guardar, la agenda continuará en estado <strong>Confirmar</strong>. El paciente, tipo y especialidad permanecen bloqueados.</div>
@foreach(array('tipo'=>'proc_consul','id_paciente'=>'id_paciente','id_sala'=>'id_sala','id_doctor2'=>'id_doctor2','id_doctor3'=>'id_doctor3') as $input=>$column)
<input type="hidden" class="js-confirmed-lock-value" data-confirmed-field="{{$input}}" name="{{$input}}" value="{{$appointment->$column}}">
@endforeach
@endif
      <div class="da-fields">
      @if($surgeryBooking && !empty($embeddedSurgery))
          <input type="hidden" id="daType" name="tipo" value="5">
          <div class="da-field da-field--wide">
              <label for="surgeryAgendaType">Tipo de agenda</label>
              <input id="surgeryAgendaType" class="form-control" value="Cirugía" readonly>
          </div>
      @else
      <div class="da-field da-field--wide"><label for="daType">Tipo de agenda</label>
        @if($surgeryBooking)
            <input type="hidden" name="tipo" value="5">
            <select class="form-control" id="daType" disabled><option value="5" selected>Cirugía</option></select>
        @else
            <select class="form-control" id="daType" name="tipo" data-order="preserve" autocomplete="off" @if($admitted || $confirmed) disabled @endif><option value="0" {{$type===0?'selected':''}}>Consulta</option><option value="1" {{$type===1?'selected':''}}>Procedimiento</option><option value="5" {{$type===5?'selected':''}}>Cirugía</option><option value="2" {{$type===2?'selected':''}}>Reunión / bloqueo interno</option></select>
        @endif
      </div>
      @endif
        <div class="da-field"><label>Inicio</label><input class="form-control js-reagendar-field" type="datetime-local" name="inicio" data-confirmed-value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechaini)):$defaultStart}}" @if($admitted || $confirmed) readonly @endif value="{{old('inicio',$editing?date('Y-m-d\TH:i',strtotime($appointment->fechaini)):$defaultStart)}}" @if(!$editing) min="{{date('Y-m-d\TH:i')}}" @endif required></div><div class="da-field"><label>Fin</label><input class="form-control js-reagendar-field" type="datetime-local" name="fin" data-confirmed-value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechafin)):''}}" @if($admitted || $confirmed) readonly @endif value="{{old('fin',$editing?date('Y-m-d\TH:i',strtotime($appointment->fechafin)):'')}}" required></div>
        @if(!$admitted)
        <div class="da-field da-field--wide da-availability js-reagendar-availability {{$confirmed?'is-agenda-hidden':''}}">
            <div class="da-availability__head">
                <div>
                    <label>Horarios disponibles del médico</label>
                    <small>{{ !empty($embeddedSurgery) ? 'Selecciona un bloque que incluya preparación, cirugía y limpieza.' : 'Selecciona un intervalo libre para completar automáticamente las horas.' }}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary js-refresh-availability"><i class="fas fa-sync-alt mr-1"></i> Actualizar</button>
            </div>
            <div class="da-availability__slots" role="status" @if(!empty($embeddedSurgery)) data-surgery-blocks @endif>
                <span class="da-availability__hint">Consultando horarios…</span>
            </div>
        </div>
        @endif
        <div class="da-field da-patient-fields da-field--wide"><label>Paciente</label><div class="js-patient-results" data-patient-locked="{{$admitted || $confirmed?'1':'0'}}" aria-live="polite">@if($patients->count())<div class="da-patient-list">@foreach($patients as $patient)<label class="da-patient"><input type="radio" name="id_paciente" @if($admitted || $confirmed) disabled @endif value="{{$patient->id}}" data-appointment-type="{{!empty($patient->has_previous_agenda)?1:0}}" {{old('id_paciente',$editing?$appointment->id_paciente:($defaultPatientId??''))==$patient->id?'checked':''}}><span>{{$patient->display_name}}<small>{{$patient->id}} · {{isset($patient->fecha_nacimiento)?$patient->fecha_nacimiento:'Sin fecha de nacimiento'}}</small></span></label>@endforeach</div>@else<div class="da-help">Busca al paciente en el panel lateral y selecciónalo antes de guardar.</div>@endif</div>@if(!empty($embeddedSurgery))
            @include('v3.clinical.agenda._inline_patient')
        @elseif(!$editing)
            <div class="da-patient-create-row"><span>¿El paciente no está registrado?</span><a class="btn btn-outline-primary js-create-patient" data-patient-create href="{{route('v3.patients.create',array('agenda_doctor'=>$doctor->id,'agenda_inicio'=>$defaultStart,'agenda_fin'=>$defaultEnd,'agenda_tipo'=>$type,'agenda_origen'=>$origin))}}"><i class="fas fa-user-plus mr-1"></i> Crear nuevo paciente</a></div>@endif</div>
        <div class="da-field"><label>Sala / consultorio</label><select class="form-control js-reagendar-field" name="id_sala" data-confirmed-value="{{$editing?$appointment->id_sala:''}}" @if($admitted || $confirmed) disabled @endif><option value="">{{$rooms->isEmpty()?'No hay salas disponibles':'Sin sala asignada'}}</option>@foreach($rooms as $room)<option value="{{$room->id}}" {{old('id_sala',$editing?$appointment->id_sala:'')==$room->id?'selected':''}}>{{$room->display_name}}</option>@endforeach</select>@if($rooms->isEmpty())<div class="da-room-empty"><i class="fas fa-door-open"></i><div><strong>No existen salas o consultorios activos.</strong><small>{{!empty($canManageRooms)?'Crea uno y regresa a esta agenda para seleccionarlo.':'Solicita a Administración que configure una sala.'}}</small></div>@if(!empty($canManageRooms))<a class="btn btn-outline-primary btn-sm js-room-create-link" href="{{$roomCreateUrl}}" target="_blank" rel="noopener"><i class="fas fa-plus-circle mr-1"></i> Crear sala</a>@endif</div>@endif</div>
        <div class="da-field"><label>Especialidad del médico</label><input type="hidden" name="espid" value="{{$doctorSpecialty ? $doctorSpecialty->id : ''}}"><input class="form-control" value="{{$doctorSpecialty ? $doctorSpecialty->nombre : 'Sin especialidad asignada'}}" readonly aria-label="Especialidad del médico"></div>
        <div class="da-field da-patient-fields"><label>Seguro / convenio</label><select class="form-control" name="id_seguro" data-order="preserve" autocomplete="off" @if($admitted) disabled @endif><option value="" {{$insuranceValue===''?'selected':''}}>Seleccione</option>@foreach($insurers as $insurer)<option value="{{$insurer->id}}" {{$insuranceValue===(string)$insurer->id?'selected':''}}>{{$insurer->nombre}}</option>@endforeach</select></div>
        <div class="da-field da-patient-fields"><label>Tipo de ingreso</label><select class="form-control" name="est_amb_hos" data-order="preserve" autocomplete="off"><option value="0" {{$admissionTypeValue==='0'?'selected':''}}>Ambulatorio</option><option value="1" {{$admissionTypeValue==='1'?'selected':''}}>Hospitalizado</option></select></div>
        <div class="da-field da-patient-fields"><label>Primera vez / consecutiva</label><select class="form-control js-appointment-type-display" @if($editing) name="tipo_cita" @else disabled aria-readonly="true" @endif @if($admitted) disabled @endif><option value="0" {{$automaticAppointmentType===0?'selected':''}}>Primera vez</option><option value="1" {{$automaticAppointmentType===1?'selected':''}}>Consecutiva</option></select>@if(!$editing)<input class="js-appointment-type-value" type="hidden" name="tipo_cita" value="{{$automaticAppointmentType}}"><div class="da-help js-appointment-type-help">Se calcula automáticamente según las agendas anteriores del paciente.</div>@endif</div>
        <input type="hidden" name="cortesia" value="NO">
        <div class="da-field da-procedure-fields {{$type===1?'':'is-agenda-hidden'}}"><label>Procedimiento *</label><select class="form-control js-standard-procedure" name="id_procedimiento" data-agenda-search="1" {{$type===1?'required':''}}><option value="">Seleccione el procedimiento</option>@foreach($procedures as $procedure)<option value="{{$procedure->id}}" {{old('id_procedimiento',$editing?$appointment->id_procedimiento:'')==$procedure->id?'selected':''}}>{{$procedure->nombre}}</option>@endforeach</select></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Procedimiento quirúrgico *</label><select class="form-control js-surgery-procedure" name="id_procedimiento_cirugia"><option value="">Seleccione la cirugía</option>@foreach($surgicalProcedures as $procedure)<option value="{{$procedure->id}}" {{old('id_procedimiento',$editing?$appointment->id_procedimiento:'')==$procedure->id?'selected':''}}>{{$procedure->nombre}}</option>@endforeach</select></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Modalidad *</label><select class="form-control" name="modalidad_cirugia">@foreach(array('HOSPITALIZACION_PROGRAMADA'=>'Hospitalización programada','AMBULATORIA'=>'Cirugía ambulatoria','EMERGENCIA'=>'Urgencia programada') as $value=>$label)<option value="{{$value}}" {{old('modalidad_cirugia',$surgeryDetail?$surgeryDetail->modalidad:'HOSPITALIZACION_PROGRAMADA')===$value?'selected':''}}>{{$label}}</option>@endforeach</select></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Fecha y hora de cirugía *</label><input class="form-control" type="datetime-local" name="hora_quirurgica" value="{{$surgeryStart}}"></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Duración estimada (min) *</label><input class="form-control" type="number" name="duracion_cirugia" min="30" max="720" step="15" @if($surgeryBooking) required @endif value="{{old('duracion_cirugia',$surgeryDetail?$surgeryDetail->duracion_minutos:120)}}"></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Preparación (min){{ $surgeryBooking ? ' *' : '' }}</label><input class="form-control" type="number" name="preparacion_minutos" min="0" max="180" @if($surgeryBooking) required @endif value="{{old('preparacion_minutos',$surgeryDetail?$surgeryDetail->preparacion_minutos:30)}}"></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Limpieza / recuperación (min){{ $surgeryBooking ? ' *' : '' }}</label><input class="form-control" type="number" name="limpieza_minutos" min="0" max="180" @if($surgeryBooking) required @endif value="{{old('limpieza_minutos',$surgeryDetail?$surgeryDetail->limpieza_minutos:30)}}"></div>
        <div class="da-field da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Anestesiólogo</label><select class="form-control" name="id_anestesiologo"><option value="">Por asignar</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{old('id_anestesiologo',$surgeryDetail?$surgeryDetail->id_anestesiologo:'')==$assistant->id?'selected':''}}>{{$assistant->display_name}}</option>@endforeach</select></div>
        <div class="da-field da-field--wide da-surgery-fields {{$type===5?'':'is-agenda-hidden'}}"><label>Indicación quirúrgica *</label><textarea class="form-control" name="indicacion_quirurgica" rows="2" maxlength="2000">{{old('indicacion_quirurgica',$surgeryDetail?$surgeryDetail->indicacion_quirurgica:'')}}</textarea></div>
        <div class="da-field da-meeting-fields"><label>Clase de reunión</label><select class="form-control" name="clase"><option value="0">Reunión</option><option value="1" {{old('clase',$editing?$appointment->procedencia:'')==1?'selected':''}}>Capacitación</option><option value="2" {{old('clase',$editing?$appointment->procedencia:'')==2?'selected':''}}>Bloqueo administrativo</option></select></div>
        <div class="da-field da-patient-fields"><label>Estado / acción a realizar</label>@if(!$editing)<input type="hidden" name="estado_cita" value="0"><input class="form-control" value="Por Confirmar" readonly aria-label="Estado / acción a realizar">@else<select class="form-control js-agenda-status" name="estado_cita" data-order="preserve" autocomplete="off" @if($admitted) disabled @endif>@foreach(array(0=>'Por Confirmar',1=>'Confirmar',2=>'Reagendar',3=>'Suspender',4=>'ADMISIÓN',-1=>'NO ASISTE') as $value=>$label)<option value="{{$value}}" {{(int)old('estado_cita',$editing?$appointment->estado_cita:0)===$value?'selected':''}}>{{$label}}</option>@endforeach</select>@endif</div>
        <div class="da-field da-patient-fields"><label>Referido por</label><input class="form-control" name="referido" value="{{old('referido',$selectedPatient?$selectedPatient->referido:'')}}" maxlength="255"></div>
        <div class="da-field da-field--wide"><label>Observaciones</label><textarea class="form-control" rows="3" name="observaciones" maxlength="1000">{{old('observaciones',$editing?$appointment->observaciones:'')}}</textarea></div>
      </div>
      <div class="da-actions-bottom">
          <a class="btn btn-light" href="{{ $backUrl }}">Cancelar</a>
          <button class="btn btn-primary" @if($surgeryBooking) type="button" data-book-surgery @else type="submit" @endif>
              <i class="fas fa-save mr-1"></i> {{ $editing ? 'Guardar cambios' : ($surgeryBooking ? 'Agendar cirugía' : 'Crear agenda') }}
          </button>
      </div>
    </form>
    @if(!empty($embeddedSurgery))
        <form id="surgery-inline-patient-form" method="post" action="{{ route('v3.patients.store') }}" hidden>
            @csrf
            <input type="hidden" name="parentesco" value="Principal">
        </form>
    @endif
    @if($editing)<div class="da-danger"><h4>Cancelar agenda</h4><p>No elimina el registro: lo marca como cancelado y conserva la auditoría. Si ya existe historia clínica, el sistema bloqueará la acción.</p><form method="post" action="{{route('v3.doctor-agenda.destroy',array('doctor'=>$doctor->id,'appointment'=>$appointment->id))}}" onsubmit="return confirm('¿Confirma cancelar esta agenda?');">{{csrf_field()}}{{method_field('DELETE')}}<label for="agendaCancellationReason">Motivo de cancelación</label><textarea class="form-control mb-2" id="agendaCancellationReason" name="observacion_cancelacion" rows="3" maxlength="1000" required placeholder="Explique por qué se cancela la agenda…"></textarea><button class="btn btn-outline-danger btn-sm" type="submit"><i class="fas fa-ban mr-1"></i> Cancelar agenda</button></form></div>@endif
  </div></div>
  <aside class="da-stack"><div class="da-panel"><header class="da-panel__head"><h3><i class="fas fa-search mr-1"></i> Buscar paciente</h3><p>Por identificación, nombre o apellido.</p></header><div class="da-panel__body"><form class="da-patient-search js-patient-search" method="get" data-search-url="{{route('v3.doctor-agenda.patients',array('doctor'=>$doctor->id))}}" action="{{$editing?route('v3.doctor-agenda.edit',array('doctor'=>$doctor->id,'appointment'=>$appointment->id)):route('v3.doctor-agenda.create',array('doctor'=>$doctor->id))}}">@if(!$editing)<input type="hidden" name="tipo" value="{{$type}}">@if($origin!=='')<input type="hidden" name="origen" value="{{$origin}}">@endif @endif<input class="form-control" name="paciente" value="{{$patientSearch}}" minlength="2" required placeholder="Paciente"><button class="btn btn-primary" type="submit" aria-label="Buscar paciente"><i class="fas fa-search"></i></button></form></div></div><div class="da-help"><strong><i class="fas fa-shield-alt mr-1"></i> Controles automáticos</strong><br>Se valida pertenencia del médico, cruce con otras agendas, disponibilidad de sala y horario laboral antes de escribir en la base.</div></aside>
  </div></section>
</div>
@if($surgeryBooking)
<script>
(function () {
    const button = document.querySelector('[data-book-surgery]');
    const form = button.form;
    let booking = false;

    button.addEventListener('click', function () {
        booking = true;
        try {
            form.requestSubmit();
        } finally {
            booking = false;
        }
    });

    form.addEventListener('submit', function (event) {
        if (!booking) {
            event.preventDefault();
            return;
        }
        button.disabled = true;
    });

    form.addEventListener('formdata', function (event) {
        if (booking) event.formData.set('confirmar_cirugia', '1');
    });

    window.addEventListener('pageshow', function () {
        button.disabled = false;
    });
})();
</script>
@endif
<script>(function(){var type=document.getElementById('daType'),form=document.querySelector('.js-activity-form');function show(elements,visible){elements.forEach(function(el){el.classList.toggle('is-agenda-hidden',!visible);});}function sync(){var value=parseInt(type.value,10);show(document.querySelectorAll('.da-patient-fields'),value!==2);show(document.querySelectorAll('.da-procedure-fields'),value===1);show(document.querySelectorAll('.da-surgery-fields'),value===5);show(document.querySelectorAll('.da-meeting-fields'),value===2);var standard=form.querySelector('.js-standard-procedure'),surgery=form.querySelector('.js-surgery-procedure'),admission=form.querySelector('[name="est_amb_hos"]'),modality=form.querySelector('[name="modalidad_cirugia"]');if(standard){standard.required=value===1;standard.name=value===5?'id_procedimiento_estandar':'id_procedimiento';}if(surgery){surgery.required=value===5;surgery.name=value===5?'id_procedimiento':'id_procedimiento_cirugia';}if(value===5&&admission&&modality)admission.value=modality.value==='AMBULATORIA'?'0':'1';}type.addEventListener('change',sync);if(window.jQuery)window.jQuery(type).off('change.agendaActivityFields').on('change.agendaActivityFields',sync);var modality=form.querySelector('[name="modalidad_cirugia"]');if(modality)modality.addEventListener('change',sync);sync();})();</script>
<script>
(function () {
    const form = document.querySelector('.js-activity-form');
    const type = document.getElementById('daType');
    const box = form.querySelector('.da-availability__slots');
    if (!type || !box) return;
    const surgeryBlocks = box.hasAttribute('data-surgery-blocks');

    const field = (name) => form.elements.namedItem(name);
    const start = field('inicio');
    const end = field('fin');
    const surgeryTime = field('hora_quirurgica');
    const timingFields = ['preparacion_minutos', 'duracion_cirugia', 'limpieza_minutos'];
    const surgeryOptions = ['id_sala', 'id_anestesiologo', ...timingFields];
    const isSurgery = () => type.value === '5';
    let dateField = surgeryTime;
    let pendingRequest;
    let selectedBlock = false;

    function showMessage(message, empty = false) {
        const hint = document.createElement('span');
        hint.className = empty ? 'da-availability__hint da-availability__hint--empty' : 'da-availability__hint';
        hint.textContent = message;
        box.replaceChildren(hint);
    }

    function unavailableMessage(data, day) {
        const messages = {
            past_date: 'El ' + day + ' ya pasó. Selecciona otra fecha.',
            no_schedule: 'El médico seleccionado no tiene horario configurado para cirugía el ' + day + '.',
            restricted_schedule: 'El médico tiene horario el ' + day + ', pero los tramos configurados no admiten reservas de cirugía.',
            no_full_block: 'No se encontró un bloque de ' + data.block_minutes + ' minutos para cirugía, preparación y limpieza el ' + day + '.',
            doctor_busy: 'Las citas del médico impiden reservar un bloque de ' + data.block_minutes + ' minutos el ' + day + '.',
            room_busy: 'El quirófano seleccionado está ocupado en los bloques libres del médico. Puedes elegir otro quirófano o fecha.',
            anesthesiologist_busy: 'El anestesiólogo seleccionado está ocupado en los bloques libres del médico y el quirófano.'
        };
        return messages[data.reason] || 'No hay bloques quirúrgicos libres el ' + day + '.';
    }

    function showIntervals(items, surgical, roomSelected) {
        box.replaceChildren();
        if (surgical && !roomSelected) {
            showMessage('Selecciona un quirófano para comprobar también su disponibilidad.');
        }

        for (const item of items) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'da-availability__slot';
            button.dataset.start = item.start;
            button.dataset.end = item.end;
            button.dataset.surgeryStart = item.surgery_start || '';
            if (surgeryBlocks && selectedBlock && start.value === item.start && end.value === item.end) {
                button.classList.add('is-selected');
            }
            if (surgeryBlocks) {
                const clock = document.createElement('i');
                clock.className = 'far fa-clock mr-1';
                clock.setAttribute('aria-hidden', 'true');
                button.append(clock, item.block_label);
            } else {
                button.textContent = surgical ? 'Cirugía ' + item.surgery_label : item.label;
            }
            const detail = document.createElement('small');
            detail.textContent = surgeryBlocks ? 'Bloque de ' + item.minutes + ' minutos' : surgical
                ? (roomSelected ? 'Bloque de sala ' : 'Bloque del médico ') + item.block_label
                : 'Turno de ' + item.minutes + ' minutos';
            button.append(detail);
            box.append(button);
        }
    }

    async function load(invalidateSelection = false) {
        if (pendingRequest) pendingRequest.abort();
        if (surgeryBlocks && selectedBlock && invalidateSelection) {
            end.value = '';
            selectedBlock = false;
        }

        const surgical = isSurgery();
        const date = (surgical && dateField.value ? dateField.value : start.value).slice(0, 10);
        if (!date) {
            showMessage('Selecciona una fecha para consultar los horarios.', true);
            return;
        }

        if (surgeryBlocks && timingFields.some(name => !field(name).validity.valid)) {
            showMessage('Completa la duración estimada, la preparación y la limpieza para consultar los bloques.', true);
            return;
        }
        const request = new AbortController();
        pendingRequest = request;
        const url = new URL(form.dataset.availabilityUrl, window.location.href);
        url.searchParams.set('fecha', date);
        url.searchParams.set('tipo', type.value);
        if (form.dataset.appointmentId) url.searchParams.set('excluir', form.dataset.appointmentId);
        if (surgeryBlocks) url.searchParams.set('bloques_cirugia', '1');
        if (surgical) {
            for (const name of surgeryOptions) {
                const input = field(name);
                if (input.value !== '') url.searchParams.set(name, input.value);
            }
        }

        showMessage('Consultando horarios…');
        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: request.signal
            });
            const data = await response.json();
            if (request.signal.aborted) return;
            if (!response.ok) throw new Error(data.errors ? Object.values(data.errors)[0][0] : data.message);

            if (data.items.length) {
                showIntervals(data.items, surgical, url.searchParams.has('id_sala'));
                return;
            }

            const day = date.split('-').reverse().join('/');
            showMessage(surgical
                ? unavailableMessage(data, day)
                : 'No hay horarios libres el ' + day + ' para este tipo de agenda.', true);
        } catch (error) {
            if (!request.signal.aborted) {
                showMessage(error.message || 'No se pudo consultar la disponibilidad.', true);
            }
        }
    }

    function onChange(input, handler) {
        if (input.tagName === 'SELECT' && window.jQuery) {
            window.jQuery(input).on('change.agendaAvailability', handler);
        } else {
            input.addEventListener('change', handler);
        }
    }

    onChange(type, () => load(true));
    onChange(start, function () {
        dateField = start;
        load(true);
    });
    onChange(surgeryTime, function () {
        if (!isSurgery()) return;
        dateField = surgeryTime;
        load(true);
    });
    for (const name of surgeryOptions) {
        onChange(field(name), function () {
            if (isSurgery()) load(true);
        });
    }
    form.querySelector('.js-refresh-availability').addEventListener('click', () => load());
    box.addEventListener('click', function (event) {
        const slot = event.target.closest('.da-availability__slot');
        if (!slot || slot.disabled) return;

        start.value = slot.dataset.start;
        end.value = slot.dataset.end;
        if (isSurgery()) surgeryTime.value = slot.dataset.surgeryStart;
        selectedBlock = true;
        box.querySelectorAll('.da-availability__slot').forEach(function (item) {
            item.classList.toggle('is-selected', item === slot);
        });
    });

    load();
})();
</script>
<script>(function(){var form=document.querySelector('.js-activity-form'),link=document.querySelector('[data-patient-create]');if(!form||!link)return;link.addEventListener('click',function(){var url=new URL(link.href,window.location.href),start=form.querySelector('[name="inicio"]'),end=form.querySelector('[name="fin"]'),type=form.querySelector('[name="tipo"]');if(start)url.searchParams.set('agenda_inicio',start.value);if(end)url.searchParams.set('agenda_fin',end.value);if(type)url.searchParams.set('agenda_tipo',type.value);link.href=url.toString();});})();</script>
<script>(function(){var searchForm=document.querySelector('.js-patient-search'),results=document.querySelector('.js-patient-results'),agendaForm=document.querySelector('.js-activity-form');if(!searchForm||!results||!agendaForm)return;var input=searchForm.querySelector('[name="paciente"]'),button=searchForm.querySelector('button[type="submit"]'),locked=results.getAttribute('data-patient-locked')==='1';function message(text){results.innerHTML='';var help=document.createElement('div');help.className='da-help';help.textContent=text;results.appendChild(help);}function render(items){var selected=agendaForm.querySelector('[name="id_paciente"]:checked'),selectedId=selected?String(selected.value):'';results.innerHTML='';if(!items.length){message('No se encontraron pacientes con ese criterio.');return;}var list=document.createElement('div');list.className='da-patient-list';items.forEach(function(item){var label=document.createElement('label');label.className='da-patient';var radio=document.createElement('input');radio.type='radio';radio.name='id_paciente';radio.value=item.id;radio.disabled=locked;radio.checked=String(item.id)===selectedId;radio.setAttribute('data-appointment-type',item.appointment_type||0);var text=document.createElement('span');text.textContent=item.name||item.text||item.id;var detail=document.createElement('small');detail.textContent=String(item.id)+(item.birth?' · '+item.birth:' · Sin fecha de nacimiento');text.appendChild(detail);label.appendChild(radio);label.appendChild(text);list.appendChild(label);});results.appendChild(list);}searchForm.addEventListener('submit',function(event){event.preventDefault();var query=(input.value||'').trim();if(query.length<2){message('Escribe al menos 2 caracteres para buscar.');input.focus();return;}button.disabled=true;message('Buscando pacientes…');fetch(searchForm.getAttribute('data-search-url')+'?q='+encodeURIComponent(query),{credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}}).then(function(response){return response.json().then(function(data){if(!response.ok)throw new Error(data.message||'No se pudo buscar al paciente.');return data;});}).then(function(data){render(data.items||data.results||[]);}).catch(function(error){message(error.message);}).then(function(){button.disabled=false;});});results.addEventListener('agenda:patient-selected',function(event){render([event.detail]);var patient=results.querySelector('[name="id_paciente"]');patient.checked=true;patient.dispatchEvent(new Event('change',{bubbles:true}));});results.addEventListener('change',function(event){if(event.target.name!=='id_paciente')return;var appointmentType=event.target.getAttribute('data-appointment-type')||'0',appointmentTypeSelect=agendaForm.querySelector('.js-appointment-type-display'),appointmentTypeValue=agendaForm.querySelector('.js-appointment-type-value');if(appointmentTypeSelect)appointmentTypeSelect.value=appointmentType;if(appointmentTypeValue)appointmentTypeValue.value=appointmentType;});})();</script>
