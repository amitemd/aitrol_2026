<div class="hc4-agenda-log" aria-live="polite">
  <div class="hc4-agenda-log__summary">
    <div>
      <strong>{{count($logs)}} movimiento{{count($logs)===1?'':'s'}}</strong>
      <span>Historial cronológico de los cambios realizados en esta agenda.</span>
    </div>
    <span class="hc4-agenda-log__secure"><i class="fas fa-shield-alt" aria-hidden="true"></i> Auditoría</span>
  </div>

  <div class="hc4-agenda-log__list">
    @forelse($logs as $log)
      @php
        $descriptions = array_filter(array(
          trim((string)$log->descripcion),
          trim((string)$log->descripcion2),
          trim((string)$log->descripcion3) !== 'CAMBIO:' ? trim((string)$log->descripcion3) : '',
        ));
        $description = count($descriptions) ? implode(' · ', $descriptions) : 'Actualización de agenda';
        $previousObservation = trim((string)$log->observaciones_ant);
        $currentObservation = trim((string)$log->observaciones);
        $creator = isset($log->user_crea) ? $log->user_crea : null;
        $creatorName = $creator ? trim($creator->nombre1.' '.$creator->apellido1) : 'Usuario del sistema';
        $initials = $creator ? mb_strtoupper(mb_substr((string)$creator->nombre1,0,1).mb_substr((string)$creator->apellido1,0,1),'UTF-8') : 'S';
        $doctorBefore = $log->id_doctor1_ant ? Sis_medico\User::find($log->id_doctor1_ant) : null;
        $doctorAfter = $log->id_doctor1 ? Sis_medico\User::find($log->id_doctor1) : null;
        $doctorBeforeName = $doctorBefore ? trim($doctorBefore->nombre1.' '.$doctorBefore->apellido1) : '';
        $doctorAfterName = $doctorAfter ? trim($doctorAfter->nombre1.' '.$doctorAfter->apellido1) : '';
        $scheduleChanged = (string)$log->fechaini_ant !== (string)$log->fechaini || (string)$log->fechafin_ant !== (string)$log->fechafin;
        $doctorChanged = (string)$log->id_doctor1_ant !== (string)$log->id_doctor1;
        $scheduleBefore = $log->fechaini_ant ? date('d/m/Y H:i',strtotime((string)$log->fechaini_ant)) : 'Sin horario previo';
        $scheduleAfter = $log->fechaini ? date('d/m/Y H:i',strtotime((string)$log->fechaini)) : 'Sin horario';
      @endphp
      <article class="hc4-agenda-log__item">
        <div class="hc4-agenda-log__rail" aria-hidden="true"><span></span></div>
        <div class="hc4-agenda-log__card">
          <header>
            <div class="hc4-agenda-log__event">
              <span class="hc4-agenda-log__icon"><i class="fas {{$loop->first?'fa-calendar-check':'fa-history'}}" aria-hidden="true"></i></span>
              <div><strong>{{$description}}</strong><small>Movimiento #{{$loop->iteration}}</small></div>
            </div>
            <time datetime="{{$log->created_at}}"><strong>{{substr((string)$log->created_at,11,5)}}</strong><span>{{date('d/m/Y',strtotime((string)$log->created_at))}}</span></time>
          </header>

          <div class="hc4-agenda-log__user">
            <span class="hc4-agenda-log__avatar">{{$initials}}</span>
            <div><small>Realizado por</small><strong>{{$creatorName}}</strong></div>
          </div>

          @if($scheduleChanged || $doctorChanged || $previousObservation!=='' || $currentObservation!=='')
          <div class="hc4-agenda-log__changes">
            @if($scheduleChanged)
            <div><i class="far fa-clock" aria-hidden="true"></i><span><small>Horario</small><strong>{{$scheduleBefore}} → {{$scheduleAfter}}</strong></span></div>
            @endif
            @if($doctorChanged)
            <div><i class="fas fa-user-md" aria-hidden="true"></i><span><small>Médico</small><strong>{{$doctorBeforeName?:'Sin asignar'}} → {{$doctorAfterName?:'Sin asignar'}}</strong></span></div>
            @endif
            @if($previousObservation!=='' || $currentObservation!=='')
            <div class="hc4-agenda-log__change--wide"><i class="far fa-comment-alt" aria-hidden="true"></i><span><small>Observación</small><strong>@if($previousObservation!=='' && $previousObservation!==$currentObservation)<em>{{$previousObservation}}</em> → @endif{{$currentObservation?:'Sin observación'}}</strong></span></div>
            @endif
          </div>
          @else
          <p class="hc4-agenda-log__no-detail"><i class="fas fa-info-circle" aria-hidden="true"></i> El movimiento no contiene observaciones adicionales.</p>
          @endif
        </div>
      </article>
    @empty
      <div class="hc4-agenda-log__empty"><i class="far fa-clipboard" aria-hidden="true"></i><strong>Sin movimientos registrados</strong><span>Esta agenda todavía no tiene cambios en su historial.</span></div>
    @endforelse
  </div>
</div>
