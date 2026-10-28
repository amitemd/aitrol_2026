@extends('agenda.base')

@section('action-content')
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('/css/agenda-directory.css') }}?v={{ filemtime(public_path('css/agenda-directory.css')) }}">

<div class="modal fade" id="editMaxPacientes" tabindex="-1" role="dialog" aria-labelledby="doctorLimitsModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content"></div>
  </div>
</div>

<section class="content agenda-directory">
  <div class="agenda-directory__panel">
    <header class="agenda-directory__header">
      <div>
        <span class="agenda-directory__eyebrow">@lang('agenda.medicalDirectory')</span>
        <h2>@lang('agenda.doctorDirectoryTitle')</h2>
        <p>@lang('agenda.doctorDirectorySubtitle')</p>
      </div>

      <div class="agenda-directory__header-actions">
        <a href="{{ route('preagenda.pentax') }}" class="agenda-directory__shortcut">
          <span class="fa fa-stethoscope" aria-hidden="true"></span>
          <span>@lang('agenda.procedureSchedule')</span>
        </a>
        <a href="{{ route('hospitalizados.index') }}" class="agenda-directory__shortcut agenda-directory__shortcut--secondary">
          <span class="fa fa-bed" aria-hidden="true"></span>
          <span>@lang('agenda.hospitalControl')</span>
        </a>
      </div>
    </header>

    @php
      $doctorSections = [
        ['title' => __('agenda.featuredDoctors'), 'icon' => 'fa-star', 'modifier' => 'featured', 'doctors' => $featuredDoctors],
        ['title' => __('agenda.staffDoctors'), 'icon' => 'fa-user-md', 'modifier' => 'staff', 'doctors' => $staffDoctors],
        ['title' => __('agenda.externalDoctors'), 'icon' => 'fa-users', 'modifier' => 'external', 'doctors' => $externalDoctors],
      ];
    @endphp

    <div class="agenda-directory__sections">
      @foreach($doctorSections as $section)
        @if($section['doctors']->isNotEmpty())
          <section class="doctor-section doctor-section--{{ $section['modifier'] }}" aria-labelledby="doctor-section-{{ $section['modifier'] }}">
            <header class="doctor-section__header">
              <div class="doctor-section__heading">
                <span class="doctor-section__icon fa {{ $section['icon'] }}" aria-hidden="true"></span>
                <div>
                  <h3 id="doctor-section-{{ $section['modifier'] }}">{{ $section['title'] }}</h3>
                  <p>{{ trans_choice('agenda.professionalsCount', $section['doctors']->count(), ['count' => $section['doctors']->count()]) }}</p>
                </div>
              </div>
              <span class="doctor-section__count" aria-hidden="true">{{ $section['doctors']->count() }}</span>
            </header>

            <div class="doctor-grid">
              @foreach($section['doctors'] as $doctor)
                @include('agenda.partials.doctor-card', [
                  'doctor' => $doctor,
                  'specialties' => $specialtiesByDoctor->get($doctor->id, collect()),
                  'showDoctorPrefix' => $doctor->id !== '4444444444',
                ])
              @endforeach
            </div>
          </section>
        @endif
      @endforeach
    </div>
  </div>
</section>

<script>
  $(function () {
    $('.breadcrumb').append('<li class="active">{{ __('agenda.agenda') }}</li>');

    $('#editMaxPacientes').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal');
      $(this).find('.modal-content').empty();
    });
  });
</script>
@endsection
