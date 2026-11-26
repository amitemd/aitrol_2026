@php($editing=!is_null($appointment))
@php($admitted=$editing && ((int)$appointment->estado_cita===4 || $historyCount>0))
@php($confirmed=$editing && (int)$appointment->estado_cita===1)
@php($type=(int)($editing?$appointment->proc_consul:$defaultType))
@php($automaticAppointmentType=(int)($editing?$appointment->tipo_cita:(isset($defaultAppointmentType)?$defaultAppointmentType:0)))
@php($admissionTypeValue=$editing?(string)$appointment->est_amb_hos:'0')
@php($surgeryStart=$surgeryDetail?date('Y-m-d\TH:i',strtotime($surgeryDetail->hora_quirurgica)):$defaultStart)
@php($surgeryDuration=$surgeryDetail?(int)$surgeryDetail->duracion_minutos:120)
@php($surgeryPreparation=$surgeryDetail?(int)$surgeryDetail->preparacion_minutos:30)
@php($surgeryCleaning=$surgeryDetail?(int)$surgeryDetail->limpieza_minutos:30)
@php($surgeryStatus=$surgeryDetail?$surgeryDetail->estado_quirurgico:'TENTATIVA')
<div class="modal-header da-modal-head">
    <div><small>AGENDA MÉDICA · V3</small>
        <h5 class="modal-title"><i class="fas {{$editing?'fa-calendar-check':'fa-calendar-plus'}} mr-2"></i>{{$editing?($type===5?'Editar cirugía':'Editar agenda'):'Nueva agenda'}}</h5><span>Dr(a). {{$doctor->display_name}}</span>
    </div><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>
<div class="modal-body">
    <div class="alert alert-danger da-modal-error" role="alert" hidden></div>
    @if($editing && $selectedPatient)
    <section class="{{$type===5?'da-surgery-context':'da-agenda-tools'}}">
        @if($type===5)
        <div class="da-surgery-context__patient">
            <span class="da-surgery-context__avatar"><i class="fas fa-user-injured"></i></span>
            <div><small>Paciente de la cirugía</small><strong>{{$selectedPatient->display_name}}</strong><span>{{$selectedPatient->id}} · {{$historyCount}} historia(s) · {{$fileCount}} archivo(s)</span></div>
            <span class="da-surgery-context__status da-surgery-context__status--{{strtolower($surgeryStatus)}}">{{str_replace('_',' ',$surgeryStatus)}}</span>
        </div>
        @if($admitted)
        <div class="da-surgery-context__notice"><i class="fas fa-lock"></i><span><strong>Planificación protegida</strong> Horario, quirófano, procedimiento y equipo médico bloqueados después de la admisión.</span></div>
        @elseif($confirmed)
        <div class="da-surgery-context__notice"><i class="fas fa-calendar-check"></i><span><strong>Cirugía confirmada</strong> Selecciona Reagendar para modificar horario, quirófano o equipo.</span></div>
        @endif
        @else
        <div class="da-tools-title">
            <div><strong>{{$selectedPatient->display_name}}</strong><small>{{$selectedPatient->id}} · {{$historyCount}} historia(s) · {{$fileCount}} archivo(s)</small></div><span>Accesos clínicos</span>
        </div>
        <div class="da-tool-groups">
            <details class="da-tool-group da-tool-group--docs" open>
                <summary><i class="fas fa-folder-open"></i><span><strong>Documentación</strong><small>Identificación, certificados y archivos</small></span><i class="fas fa-chevron-down da-group-arrow"></i></summary>
                <div class="da-tools-grid">
                    @if($admitted && !empty($receiptInsuranceAllowed) && in_array((int)Auth::user()->id_tipo_usuario,array(1,4,5,20,22),true))
                    <a class="js-receipt-modal" href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'recibo-cobro'))}}"><i class="fas fa-receipt"></i> Recibo de cobro</a>
                    @endif
                    <a class="js-label-modal" href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'etiqueta'))}}"><i class="fas fa-tags"></i> Etiqueta</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'documentos'))}}" target="_blank"><i class="fas fa-folder-open"></i> Documentos</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'preparaciones'))}}" target="_blank"><i class="fas fa-clipboard-check"></i> Preparaciones</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'consentimientos'))}}" target="_blank"><i class="fas fa-file-signature"></i> Consentimientos</a>
                    {{-- El resumen permanece disponible en su módulo, pero se oculta de este modal. --}}
                    <a class="js-certificate-modal" href="{{route('v3.medical-certificates.create',array('doctor'=>$doctor->id,'appointment'=>$appointment->id))}}"><i class="fas fa-certificate"></i> Certificado médico</a>
                    <a class="js-id-document-modal {{$patientIdDocumentAvailable?'is-available':'is-unavailable'}}" href="{{$patientIdDocumentUrl}}" data-available="{{$patientIdDocumentAvailable?'1':'0'}}"><i class="fas fa-id-card"></i><span>Cédula<small class="da-doc-status"><i class="fas {{$patientIdDocumentAvailable?'fa-check-circle':'fa-exclamation-circle'}}"></i> {{$patientIdDocumentAvailable?'Disponible':'No cargada'}}</small></span></a>
                </div>
            </details>
            <details class="da-tool-group da-tool-group--orders">
                <summary><i class="fas fa-notes-medical"></i><span><strong>Órdenes y resultados</strong><small>Laboratorio, procedimientos y estudios</small></span><i class="fas fa-chevron-down da-group-arrow"></i></summary>
                <div class="da-tools-grid">
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'examenes-externos'))}}" target="_blank"><i class="fas fa-flask"></i> Exámenes externos</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'orden-procedimiento'))}}" target="_blank"><i class="fas fa-file-medical"></i> Orden procedimiento</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'imagenes'))}}" target="_blank"><i class="fas fa-images"></i> Imágenes</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'estudios'))}}" target="_blank"><i class="fas fa-x-ray"></i> Estudios</a>
                    {{-- Biopsias, órdenes de biopsias y accesos de laboratorio permanecen disponibles en sus módulos, pero se ocultan de este modal. --}}
                </div>
            </details>
            <details class="da-tool-group da-tool-group--history">
                <summary><i class="fas fa-user-md"></i><span><strong>Historia y seguimiento</strong><small>Recetas y antecedentes</small></span><i class="fas fa-chevron-down da-group-arrow"></i></summary>
                <div class="da-tools-grid">
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'recetas'))}}" target="_blank"><i class="fas fa-prescription"></i> Recetas</a>
                    <a href="{{route('v3.doctor-agenda.action',array('doctor'=>$doctor->id,'appointment'=>$appointment->id,'action'=>'historial-agendas'))}}" target="_blank"><i class="fas fa-history"></i> Historial de agendas</a>
                </div>
            </details>
        </div>
        @endif
    </section>
    @endif
    @if(!$rooms->count())<div class="alert alert-warning"><strong>Falta configurar la sede:</strong> asocia al menos un hospital y una sala activa con la empresa actual.</div>@endif
    @if(!$insurers->count())<div class="alert alert-warning"><strong>Falta configurar seguros:</strong> registra al menos un seguro o convenio activo.</div>@endif
    @if(!$doctorSpecialty)<div class="alert alert-warning"><strong>Especialidad no configurada:</strong> asigna una especialidad activa al médico desde Usuarios.</div>@endif
    @if(!$hasProcedures)<div class="alert alert-info">No hay procedimientos activos. Para agendar un procedimiento, primero créalo en el catálogo general.</div>@endif
    <form class="js-activity-form" method="post" novalidate data-availability-url="{{route('v3.doctor-agenda.availability',array('doctor'=>$doctor->id))}}" data-appointment-id="{{$editing?$appointment->id:''}}" data-confirmed="{{$confirmed?'1':'0'}}" action="{{$editing?route('v3.doctor-agenda.update',array('doctor'=>$doctor->id,'appointment'=>$appointment->id)):route('v3.doctor-agenda.store',array('doctor'=>$doctor->id))}}">{{csrf_field()}}@if($editing){{method_field('PATCH')}}@endif
@if($admitted)
@if($type===5)
@else
<div class="alert alert-info">Atención admitida: horario, médicos, sala, seguro, especialidad y primera vez/consecutiva bloqueados. Puedes actualizar Referido por y observaciones.</div>
@endif
@foreach(array('tipo'=>'proc_consul','id_paciente'=>'id_paciente','id_sala'=>'id_sala','id_doctor2'=>'id_doctor2','id_doctor3'=>'id_doctor3','estado_cita'=>'estado_cita','id_seguro'=>'id_seguro','tipo_cita'=>'tipo_cita') as $input=>$column)
<input type="hidden" name="{{$input}}" value="{{$appointment->$column}}">
@endforeach
@if($type===5)<input type="hidden" name="id_procedimiento" value="{{$appointment->id_procedimiento}}"><input type="hidden" name="modalidad_cirugia" value="{{$surgeryDetail?$surgeryDetail->modalidad:'HOSPITALIZACION_PROGRAMADA'}}">@endif
@endif
@if($confirmed && !$admitted)
@if($type===5)
@else
<div class="alert alert-info">Agenda confirmada: cambie el estado a <strong>Reagendar</strong> para habilitar fecha, horario, sala y médicos asignados. Al guardar, la agenda continuará en estado <strong>Confirmar</strong>. El paciente, tipo y especialidad permanecen bloqueados.</div>
@endif
@foreach(array('tipo'=>'proc_consul','id_paciente'=>'id_paciente','id_sala'=>'id_sala','id_doctor2'=>'id_doctor2','id_doctor3'=>'id_doctor3') as $input=>$column)
<input type="hidden" class="js-confirmed-lock-value" data-confirmed-field="{{$input}}" name="{{$input}}" value="{{$appointment->$column}}">
@endforeach
@endif
        <div class="da-fields">
            <div class="da-field da-field--wide {{$type===5&&$admitted?'da-surgery-admitted-hidden':''}}"><label>Tipo de agenda</label>
                <select id="agendaActivityType" class="form-control js-activity-type" name="tipo" data-order="preserve" autocomplete="off" @if($admitted || $confirmed) disabled @endif>
                    <option value="0" {{$type===0?'selected':''}}>Consulta</option>
                    <option value="1" {{$type===1?'selected':''}}>Procedimiento</option>
                    <option value="5" {{$type===5?'selected':''}}>Cirugía</option>
                    <option value="2" {{$type===2?'selected':''}}>Reunión / bloqueo interno</option>
                </select>
            </div>
            <div class="da-field da-field--half da-standard-time-fields" data-activity-types="0,1,2">
                <label>Inicio</label><input class="form-control js-reagendar-field" type="datetime-local" name="inicio" data-confirmed-value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechaini)):$defaultStart}}" @if($admitted || $confirmed) readonly @endif value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechaini)):$defaultStart}}" @if(!$editing) min="{{date('Y-m-d\TH:i')}}" @endif required>
            </div>
            <div class="da-field da-field--half da-standard-time-fields" data-activity-types="0,1,2">
                <label>Fin</label><input class="form-control js-reagendar-field" type="datetime-local" name="fin" data-confirmed-value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechafin)):$defaultEnd}}" @if($admitted || $confirmed) readonly @endif value="{{$editing?date('Y-m-d\TH:i',strtotime($appointment->fechafin)):$defaultEnd}}" required>
            </div>
@if(!$admitted)            <div class="js-standard-availability-anchor" hidden></div><div class="da-field da-field--wide da-availability js-reagendar-availability {{$confirmed?'is-agenda-hidden':''}}"><div class="da-availability__head"><div><label class="js-availability-title">Horarios disponibles del médico</label><small class="js-availability-help">Selecciona un intervalo libre para completar automáticamente las horas.</small></div><button type="button" class="btn btn-sm btn-outline-primary js-refresh-availability"><i class="fas fa-sync-alt mr-1"></i> Actualizar</button></div><div class="da-availability__slots" role="status"><span class="da-availability__hint">Consultando disponibilidad…</span></div></div>@endif
            <div class="da-field da-patient-fields da-field--wide {{$type===5&&$admitted?'da-surgery-admitted-hidden':''}}">
                <label>Paciente</label><select class="form-control js-patient-select" name="id_paciente" @if($admitted || $confirmed) disabled @endif data-search-url="{{route('v3.doctor-agenda.patients',array('doctor'=>$doctor->id))}}">
                    <option value="">Seleccione un paciente</option>
                    @foreach($patients as $patient)
                    <option value="{{$patient->id}}" data-appointment-type="{{!empty($patient->has_previous_agenda)?1:0}}" {{(($editing&&$appointment->id_paciente==$patient->id)||(!$editing&&(string)($defaultPatientId??'')===(string)$patient->id))?'selected':''}}>{{$patient->display_name}} · {{$patient->id}}</option>
                    @endforeach
                </select>
                @if(!$editing || (!$admitted && !$confirmed))<div class="da-patient-create-row">
                    <div class="da-help">Escribe al menos 3 caracteres del nombre, apellido o identificación para buscar al paciente.</div>
                    @if(!$editing)<a class="btn btn-outline-primary js-create-patient" data-patient-create href="{{route('v3.patients.create',array('agenda_doctor'=>$doctor->id,'agenda_inicio'=>$defaultStart,'agenda_fin'=>$defaultEnd,'agenda_tipo'=>$type))}}"><i class="fas fa-user-plus mr-1"></i> Crear nuevo paciente</a>@endif
                </div>@endif
            </div>
            <section class="da-field da-field--wide da-surgery-fields da-surgery-card js-surgery-plan {{$type===5?'':'is-agenda-hidden'}}" data-activity-types="5">
                <div class="da-surgery-card__head">
                    <div><i class="fas fa-procedures"></i><span><strong>Plan quirúrgico</strong><small>Información clínica, equipo responsable y reserva del quirófano.</small></span></div>
                    @if(!$editing)<span class="da-surgery-badge">{{str_replace('_',' ',$surgeryStatus)}}</span>@endif
                </div>
                <div class="da-surgery-grid">
                    <div class="da-surgery-section-title da-field--wide"><span>01</span><div><strong>Información de la cirugía</strong><small>Modalidad, procedimiento e indicación clínica</small></div></div>
                    <div class="da-field"><label>Modalidad *</label><select class="form-control js-surgery-modality" name="modalidad_cirugia" @if($admitted) disabled @endif>
                        @foreach(array('HOSPITALIZACION_PROGRAMADA'=>'Hospitalización programada','AMBULATORIA'=>'Cirugía ambulatoria','EMERGENCIA'=>'Urgencia programada') as $value=>$label)<option value="{{$value}}" {{($surgeryDetail?$surgeryDetail->modalidad:'HOSPITALIZACION_PROGRAMADA')===$value?'selected':''}}>{{$label}}</option>@endforeach
                    </select><input class="js-surgery-admission-type" type="hidden" name="est_amb_hos" value="{{($surgeryDetail&&$surgeryDetail->modalidad==='AMBULATORIA')?0:1}}"></div>
                    <div class="da-field"><label>Procedimiento quirúrgico *</label><select class="form-control" name="id_procedimiento" data-search-url="{{route('v3.doctor-agenda.procedures',array('doctor'=>$doctor->id,'quirurgico'=>1))}}" @if($admitted) disabled @endif>
                        <option value="">Seleccione la cirugía</option>@foreach($surgicalProcedures as $procedure)<option value="{{$procedure->id}}" {{($editing&&$appointment->id_procedimiento==$procedure->id)?'selected':''}}>{{$procedure->nombre}}</option>@endforeach
                    </select>@if($surgicalProcedures->isEmpty())<div class="da-help da-help--warning">No hay procedimientos activos marcados como disponibles para quirófano.</div>@endif</div>
                    <div class="da-field da-field--wide da-surgery-editable"><label>Indicación quirúrgica * <small>Editable</small></label><textarea class="form-control" name="indicacion_quirurgica" rows="3" maxlength="2000" placeholder="Diagnóstico o motivo clínico que justifica la cirugía">{{$surgeryDetail?$surgeryDetail->indicacion_quirurgica:''}}</textarea></div>
                    <div class="da-surgery-section-title da-field--wide"><span>02</span><div><strong>Programación de la cirugía</strong><small>Define la fecha y los tiempos estimados de ocupación</small></div></div>
                    <div class="da-field"><label>Fecha y hora de cirugía *</label><input class="form-control js-surgery-time js-reagendar-field" type="datetime-local" name="hora_quirurgica" value="{{$surgeryStart}}" @if(!$editing) min="{{date('Y-m-d\TH:i')}}" @endif @if($admitted) readonly @endif></div>
                    <div class="da-field"><label>Duración estimada *</label><div class="input-group"><input class="form-control js-surgery-duration" type="number" name="duracion_cirugia" min="30" max="720" step="15" value="{{$surgeryDuration}}" @if($admitted) readonly @endif><div class="input-group-append"><span class="input-group-text">min</span></div></div></div>
                    <div class="da-field"><label>Preparación de sala</label><div class="input-group"><input class="form-control js-surgery-preparation" type="number" name="preparacion_minutos" min="0" max="180" step="5" value="{{$surgeryPreparation}}" @if($admitted) readonly @endif><div class="input-group-append"><span class="input-group-text">min</span></div></div></div>
                    <div class="da-field"><label>Limpieza / recuperación</label><div class="input-group"><input class="form-control js-surgery-cleaning" type="number" name="limpieza_minutos" min="0" max="180" step="5" value="{{$surgeryCleaning}}" @if($admitted) readonly @endif><div class="input-group-append"><span class="input-group-text">min</span></div></div></div>
                    <div class="da-surgery-summary da-field--wide"><i class="fas fa-clock"></i><span><strong class="js-surgery-summary">Bloque total: {{$surgeryPreparation+$surgeryDuration+$surgeryCleaning}} minutos</strong><small>Incluye {{$surgeryPreparation}} min de preparación y {{$surgeryCleaning}} min de limpieza/recuperación.</small></span></div>
                    <div class="da-surgery-section-title da-field--wide"><span>03</span><div><strong>Equipo quirúrgico</strong><small>Profesionales que deben estar disponibles durante la intervención</small></div></div>
                    <div class="da-field"><label>Cirujano principal</label><input class="form-control" value="{{$doctor->display_name}}" readonly></div>
                    <div class="da-field"><label>Anestesiólogo</label><select class="form-control js-surgery-resource" name="id_anestesiologo" @if($admitted) disabled @endif><option value="">Por asignar</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{($surgeryDetail&&$surgeryDetail->id_anestesiologo==$assistant->id)?'selected':''}}>{{$assistant->display_name}}</option>@endforeach</select></div>
                    <div class="da-field"><label>Ayudante 1</label><select class="form-control" name="id_doctor2" @if($admitted) disabled @endif><option value="">Sin ayudante</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{($editing&&$appointment->id_doctor2==$assistant->id)?'selected':''}}>{{$assistant->display_name}}</option>@endforeach</select></div>
                    <div class="da-field"><label>Ayudante 2</label><select class="form-control" name="id_doctor3" @if($admitted) disabled @endif><option value="">Sin ayudante</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{($editing&&$appointment->id_doctor3==$assistant->id)?'selected':''}}>{{$assistant->display_name}}</option>@endforeach</select></div>
                    <div class="da-surgery-section-title da-field--wide da-surgery-availability-title"><span>04</span><div><strong>Quirófano y disponibilidad</strong><small>Selecciona la sala para consultar y elegir uno de sus bloques libres</small></div></div>
                    <div class="da-field da-field--wide da-surgery-room-first"><label>Quirófano *</label><select class="form-control js-surgery-resource js-reagendar-field" name="id_sala" @if($admitted) disabled @endif>
                        <option value="">{{$surgeryRooms->isEmpty()?'No hay quirófanos configurados':'Seleccione un quirófano para consultar disponibilidad'}}</option>@foreach($surgeryRooms as $room)<option value="{{$room->id}}" {{($editing&&$appointment->id_sala==$room->id)?'selected':''}}>{{$room->display_name}}</option>@endforeach
                    </select>@if($surgeryRooms->isEmpty())<div class="da-help da-help--warning">Configura una sala activa cuyo nombre o categoría corresponda a cirugía o quirófano.</div>@else<div class="da-help"><i class="fas fa-info-circle mr-1"></i>Al seleccionar el quirófano se actualizarán los horarios que aparecen debajo.</div>@endif</div>
                    <div class="js-surgery-availability-anchor da-field--wide" hidden></div>
                </div>
            </section>
            <div class="da-field da-patient-fields" data-activity-types="0,1">
                <label>Sala / consultorio</label><select class="form-control js-reagendar-field" name="id_sala" data-confirmed-value="{{$editing?$appointment->id_sala:''}}" @if($admitted || $confirmed) disabled @endif>
                    <option value="">{{$rooms->isEmpty()?'No hay salas disponibles':'Seleccione'}}</option>
                    @foreach($rooms as $room)
                    <option value="{{$room->id}}" {{($editing&&$appointment->id_sala==$room->id)?'selected':''}}>{{$room->display_name}}</option>
                    @endforeach
                </select>
                @if($rooms->isEmpty())
                <div class="da-room-empty">
                    <i class="fas fa-door-open"></i><div><strong>No existen salas o consultorios activos.</strong><small>{{!empty($canManageRooms)?'Crea uno y vuelve a abrir esta agenda para seleccionarlo.':'Solicita a Administración que configure una sala.'}}</small></div>
                    @if(!empty($canManageRooms))<a class="btn btn-outline-primary btn-sm js-room-create-link" href="{{$roomCreateUrl}}" target="_blank" rel="noopener"><i class="fas fa-plus-circle mr-1"></i> Crear sala</a>@endif
                </div>
                @endif
            </div>
            <div class="da-field da-patient-fields da-specialty-field"><label>Especialidad del médico</label>
                <input type="hidden" name="espid" value="{{$doctorSpecialty ? $doctorSpecialty->id : ''}}">
                <input class="form-control" value="{{$doctorSpecialty ? $doctorSpecialty->nombre : 'Sin especialidad asignada'}}" readonly aria-label="Especialidad del médico">
            </div>
            <div class="da-field da-patient-fields da-coverage-field"><label>Seguro / convenio</label><select class="form-control" name="id_seguro" data-order="preserve" autocomplete="off" @if($admitted) disabled @endif>
                    <option value="" {{!$editing?'selected':''}}>Seleccione</option>@foreach($insurers as $insurer)<option value="{{$insurer->id}}" {{($editing&&$appointment->id_seguro==$insurer->id)?'selected':''}}>{{$insurer->nombre}}</option>@endforeach
                </select></div>
            <div class="js-surgery-plan-anchor da-field--wide" hidden></div>
            <div class="da-field da-patient-fields" data-activity-types="0,1"><label>Tipo de ingreso</label><select class="form-control" name="est_amb_hos" data-order="preserve" autocomplete="off">
                    <option value="0" {{$admissionTypeValue==='0'?'selected':''}}>Ambulatorio</option>
                    <option value="1" {{$admissionTypeValue==='1'?'selected':''}}>Hospitalizado</option>
                </select></div>
            <div class="da-field da-field--half da-procedure-fields {{$type===1?'':'is-agenda-hidden'}}" data-activity-types="1"><label>Procedimiento *</label><select class="form-control" name="id_procedimiento" data-search-url="{{route('v3.doctor-agenda.procedures',array('doctor'=>$doctor->id))}}" {{$type===1?'required':''}}>
                    <option value="">Seleccione el procedimiento</option>@foreach($procedures as $procedure)<option value="{{$procedure->id}}" {{($editing&&$appointment->id_procedimiento==$procedure->id)?'selected':''}}>{{$procedure->nombre}}</option>@endforeach
                </select></div>
            <div class="da-field da-procedure-fields {{$type===1?'':'is-agenda-hidden'}}" data-activity-types="1"><label>Asistente 1</label><select class="form-control js-reagendar-field" name="id_doctor2" data-confirmed-value="{{$editing?$appointment->id_doctor2:''}}" @if($admitted || $confirmed) disabled @endif>
                    <option value="">Sin asistente</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{($editing&&$appointment->id_doctor2==$assistant->id)?'selected':''}}>{{$assistant->display_name}}</option>@endforeach
                </select></div>
            <div class="da-field da-procedure-fields {{$type===1?'':'is-agenda-hidden'}}" data-activity-types="1"><label>Asistente 2</label><select class="form-control js-reagendar-field" name="id_doctor3" data-confirmed-value="{{$editing?$appointment->id_doctor3:''}}" @if($admitted || $confirmed) disabled @endif>
                    <option value="">Sin asistente</option>@foreach($assistants as $assistant)<option value="{{$assistant->id}}" {{($editing&&$appointment->id_doctor3==$assistant->id)?'selected':''}}>{{$assistant->display_name}}</option>@endforeach
                </select></div>
            <div class="da-field da-field--half da-meeting-fields"><label>Clase de reunión</label><select class="form-control" name="clase">
                    <option value="0">Reunión</option>
                    <option value="1" {{($editing&&$appointment->procedencia==1)?'selected':''}}>Capacitación</option>
                    <option value="2" {{($editing&&$appointment->procedencia==2)?'selected':''}}>Bloqueo administrativo</option>
                </select></div>
            <div class="da-field da-patient-fields" data-activity-types="0,1"><label>Primera vez / consecutiva</label><select class="form-control js-appointment-type-display" @if($editing) name="tipo_cita" @else disabled aria-readonly="true" @endif @if($admitted) disabled @endif>
                    <option value="0" {{$automaticAppointmentType===0?'selected':''}}>Primera vez</option>
                    <option value="1" {{$automaticAppointmentType===1?'selected':''}}>Consecutiva</option>
                </select>@if(!$editing)<input class="js-appointment-type-value" type="hidden" name="tipo_cita" value="{{$automaticAppointmentType}}"><div class="da-help js-appointment-type-help">Se calcula automáticamente según las agendas anteriores del paciente.</div>@endif</div>
            <div class="da-field da-patient-fields" data-activity-types="0,1"><label>Procedencia</label><input class="form-control" name="procedencia" value="{{$editing?$appointment->procedencia:''}}" maxlength="255"></div>
            <div class="da-field da-patient-fields" data-activity-types="0,1"><label>Referido por</label><input class="form-control" name="referido" value="{{$selectedPatient?$selectedPatient->referido:''}}" maxlength="255"></div>
            <input type="hidden" name="cortesia" value="NO">
            <input type="hidden" name="omni" value="{{$editing?$appointment->omni:''}}"><input type="hidden" name="tc" value="{{$editing?(int)$appointment->tc:0}}"><input type="hidden" name="teleconsulta" value="{{$editing?$appointment->teleconsulta:''}}">
            <div class="da-field da-patient-fields" data-activity-types="0,1"><label>Ocupación</label><input class="form-control" name="ocupacion" value="{{$selectedPatient?$selectedPatient->ocupacion:''}}" maxlength="255"></div>
            <input type="hidden" name="vip" value="{{$editing?(int)$appointment->vip:0}}"><input type="hidden" name="paciente_dr" value="{{$editing?(int)$appointment->paciente_dr:0}}"><input type="hidden" name="triaje" value="{{$hasTriage?1:0}}"><input type="hidden" name="adelantado" value="{{$editing?(int)$appointment->adelantado:0}}">
            <div class="da-field da-patient-fields da-field--wide" data-activity-types="0,1"><label>Observación médica/administrativa del paciente</label><textarea class="form-control" rows="2" name="observacion_paciente" maxlength="2000">{{$patientObservation}}</textarea></div>
            {{-- Campo genérico de observaciones oculto en V3. Se conserva el valor existente para no borrarlo al actualizar la agenda. --}}
            <input type="hidden" name="observaciones" value="{{$editing?$appointment->observaciones:''}}">
            <div class="da-field da-field--wide da-patient-fields da-status-field da-status-field--final" data-status="{{$editing?(int)$appointment->estado_cita:0}}">
                <label><i class="fas fa-exchange-alt mr-1"></i> Estado / acción a realizar</label>
                <div class="da-status-control js-agenda-status-card" data-status="{{$editing?(int)$appointment->estado_cita:0}}">
                    <span class="da-status-control__icon"><i class="fas fa-clock js-agenda-status-icon"></i></span>
                    <div class="da-status-control__body">
                        @if(!$editing)
                        <input type="hidden" name="estado_cita" value="0"><input class="form-control" value="Por Confirmar" readonly aria-label="Estado / acción a realizar">
                        @else
                        <select class="form-control js-agenda-status" name="estado_cita" data-order="preserve" autocomplete="off" @if($admitted) disabled @endif>@foreach(array(0=>'Por Confirmar',1=>'Confirmar',2=>'Reagendar',3=>'Suspender',4=>'ADMISIÓN',-1=>'NO ASISTE') as $value=>$label)<option value="{{$value}}" {{($editing?(int)$appointment->estado_cita:0)===$value?'selected':''}}>{{$label}}</option>@endforeach</select>
                        @endif
                        <small class="js-agenda-status-help">La cita está pendiente de confirmación.</small>
                    </div>
                </div>
            </div>
        </div>@if($editing && $selectedPatient)<details class="da-audit">
            <summary><i class="fas fa-address-card mr-1"></i> Información de contacto</summary>
            <div class="da-contact-grid">
                <p><strong>Teléfonos</strong><small>{{trim($selectedPatient->telefono1.' · '.$selectedPatient->telefono2.' · '.$selectedPatient->telefono3,' ·')?:'Sin teléfonos registrados'}}</small></p>
                <p><strong>Dirección</strong><small>{{$selectedPatient->direccion?:'Sin dirección registrada'}}</small></p>
                <p><strong>Ciudad</strong><small>{{$selectedPatient->ciudad?:'Sin ciudad registrada'}}</small></p>
                <p><strong>Fecha de nacimiento</strong><small>{{$selectedPatient->fecha_nacimiento?:'Sin fecha registrada'}}</small></p>
            </div>
        </details>
        <details class="da-audit">
            <summary><i class="fas fa-user-clock mr-1"></i> Auditoría de la agenda ({{$logs->count()}})</summary>
            <div>@forelse($logs as $log)<p><strong>{{trim($log->usuario_nombre.' '.$log->usuario_apellido)?:'Sistema'}}</strong><span>{{$log->created_at}}</span><small>{{$log->descripcion?:'Actualización de agenda'}} @if($log->observaciones)· {{$log->observaciones}}@endif</small></p>@empty<p>Sin movimientos registrados.</p>@endforelse</div>
        </details>
        <section class="da-payment-files js-payment-files"
            data-list-url="{{route('v3.doctor-agenda.files.index',array('doctor'=>$doctor->id,'appointment'=>$appointment->id))}}"
            data-upload-url="{{route('v3.doctor-agenda.files.store',array('doctor'=>$doctor->id,'appointment'=>$appointment->id))}}">
            <div class="da-payment-files__head"><div><strong><i class="fas fa-receipt mr-1"></i> Comprobantes de pago y archivos</strong><small>Se guardan en los mismos archivos de agenda que usa V2.</small></div></div>
            <div class="da-payment-files__upload">
                <input id="agendaPaymentFile" class="da-payment-file-native js-payment-file-input" type="file" accept=".pdf,.jpg,.jpeg,.png">
                <label class="da-payment-file-picker" for="agendaPaymentFile">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span><strong>Seleccionar archivo</strong><small class="js-payment-file-name">PDF, JPG o PNG · máximo 10 MB</small><em class="js-payment-file-status" aria-live="polite">La carga iniciará automáticamente.</em></span>
                    <i class="fas fa-folder-open da-payment-file-picker__end"></i>
                </label>
            </div>
            <div class="da-payment-files__list js-payment-files-list" aria-live="polite"><p class="da-payment-files__empty"><i class="fas fa-spinner fa-spin mr-1"></i> Cargando archivos…</p></div>
        </section>@endif<div class="da-modal-actions">@if($editing)<button class="btn btn-outline-danger js-cancel-activity" type="button" data-activity-noun="{{$type===5?'cirugía':'agenda'}}" data-cancel-url="{{route('v3.doctor-agenda.destroy',array('doctor'=>$doctor->id,'appointment'=>$appointment->id))}}"><i class="fas fa-ban mr-1"></i> {{$type===5?'Cancelar cirugía':'Cancelar agenda'}}</button>@else<span></span>@endif<div><button class="btn btn-light" type="button" data-dismiss="modal">Cerrar</button> <button class="btn btn-primary js-save-activity" type="submit"><i class="fas fa-save mr-1"></i> <span class="js-save-label">{{$editing?($type===5?'Guardar cirugía':'Guardar cambios'):($type===5?'Guardar como tentativa':'Crear agenda')}}</span></button></div>
        </div>
    </form>
</div>
