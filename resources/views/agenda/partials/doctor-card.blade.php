@php
  $rawImage = trim((string) $doctor->imagen_url);
  $placeholderUrl = asset('/images/avatars/doctor-placeholder.svg');
  $doctorImageUrl = $rawImage !== '' ? asset('/avatars/'.$rawImage) : $placeholderUrl;
  $doctorName = collect([
    $doctor->apellido1,
    $doctor->apellido2 !== '(N/A)' ? $doctor->apellido2 : null,
    $doctor->nombre1,
    $doctor->nombre2 !== '(N/A)' ? $doctor->nombre2 : null,
  ])->filter()->implode(' ');
@endphp

<article class="doctor-card">
  <div class="doctor-card__body">
    <a href="{{ route('agenda.agenda', ['id' => $doctor->id]) }}" class="doctor-card__photo-link" aria-label="{{ __('agenda.openDoctorSchedule', ['doctor' => $doctorName]) }}">
      <img
        src="{{ $doctorImageUrl }}"
        class="doctor-card__photo"
        alt="{{ __('agenda.doctorPhoto', ['doctor' => $doctorName]) }}"
        loading="lazy"
        onerror="this.onerror=null;this.src='{{ $placeholderUrl }}';"
      >
      <span class="doctor-card__availability" title="@lang('agenda.activeProfessional')">
        <span aria-hidden="true"></span>
        @lang('agenda.active')
      </span>
    </a>

    <div class="doctor-card__identity">
      <span class="doctor-card__role">@lang('agenda.medicalProfessional')</span>
      <h4>@if($showDoctorPrefix)Dr(a). @endif{{ $doctorName }}</h4>

      <div class="doctor-card__specialties">
        @forelse($specialties as $specialty)
          <span>{{ data_get($specialty, 'nombre') }}</span>
        @empty
          <span class="doctor-card__specialty-empty">@lang('agenda.noSpecialtyRegistered')</span>
        @endforelse
      </div>
    </div>
  </div>

  <div class="doctor-card__metrics">
    <div>
      <span class="fa fa-commenting-o" aria-hidden="true"></span>
      <strong>{{ $doctor->max_consulta ?? 0 }}</strong>
      <small>@lang('agenda.consultations')</small>
    </div>
    <div>
      <span class="fa fa-medkit" aria-hidden="true"></span>
      <strong>{{ $doctor->max_procedimiento ?? 0 }}</strong>
      <small>@lang('agenda.procedures')</small>
    </div>
  </div>

  <div class="doctor-card__actions">
    <a href="{{ route('agenda.agenda', ['id' => $doctor->id]) }}" class="doctor-card__action doctor-card__action--primary">
      <span class="fa fa-calendar-plus-o" aria-hidden="true"></span>
      @lang('agenda.agendar')
    </a>
    <a href="{{ route('doctor.max', ['id' => $doctor->id]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="doctor-card__action">
      <span class="fa fa-sliders" aria-hidden="true"></span>
      @lang('agenda.limits')
    </a>
    <a href="{{ route('horario.doctor', ['id' => $doctor->id]) }}" class="doctor-card__action">
      <span class="fa fa-clock-o" aria-hidden="true"></span>
      @lang('agenda.horariolaboral')
    </a>
  </div>
</article>
