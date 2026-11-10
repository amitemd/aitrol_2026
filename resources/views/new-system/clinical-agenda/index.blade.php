<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/clinical-agenda.css') }}?v={{ filemtime(public_path('css/clinical-agenda.css')) }}">
    @endpush

    @php
        $directoryRoute = $scheduleDirectory
            ? route('new-system.clinical-agenda.schedules')
            : route('new-system.clinical-agenda.index');
    @endphp

    <div class="clinical-agenda-page" data-clinical-agenda>
        <section class="clinical-agenda-hero">
            <div class="clinical-agenda-hero__icon" aria-hidden="true">
                <i class="fa {{ $scheduleDirectory ? 'fa-clock-o' : 'fa-calendar' }}"></i>
            </div>
            <div class="clinical-agenda-hero__content">
                <p class="clinical-agenda-eyebrow">{{ __('new_system.clinical_agenda.eyebrow') }}</p>
                <h2>{{ $scheduleDirectory ? __('new_system.clinical_agenda.schedule_directory_title') : __('new_system.clinical_agenda.title') }}</h2>
                <p>{{ $scheduleDirectory ? __('new_system.clinical_agenda.schedule_directory_subtitle') : __('new_system.clinical_agenda.subtitle') }}</p>
            </div>
            <a
                class="btn clinical-agenda-hero__action"
                href="{{ $scheduleDirectory ? route('new-system.clinical-agenda.index') : route('new-system.clinical-agenda.schedules') }}"
            >
                <i class="fa {{ $scheduleDirectory ? 'fa-arrow-left' : 'fa-clock-o' }}" aria-hidden="true"></i>
                {{ $scheduleDirectory ? __('new_system.clinical_agenda.back_to_directory') : __('new_system.clinical_agenda.view_schedules') }}
            </a>
        </section>

        @if (session('agenda_success'))
            <div class="alert alert-success clinical-agenda-alert" role="alert">
                <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('agenda_success') }}
            </div>
        @endif

        <section class="clinical-agenda-directory">
            <header class="clinical-agenda-directory__header">
                <div class="clinical-agenda-directory__heading">
                    <span class="clinical-agenda-directory__icon" aria-hidden="true"><i class="fa fa-user-md"></i></span>
                    <div>
                        <h3>{{ $scheduleDirectory ? __('new_system.clinical_agenda.schedule_directory') : __('new_system.clinical_agenda.directory') }}</h3>
                        <p>{{ $scheduleDirectory ? __('new_system.clinical_agenda.schedule_directory_help') : __('new_system.clinical_agenda.directory_help') }}</p>
                    </div>
                </div>
                <span class="clinical-agenda-count" data-doctor-count>
                    {{ trans_choice('new_system.clinical_agenda.professionals', $doctors->count(), ['count' => $doctors->count()]) }}
                </span>
            </header>

            <form class="clinical-agenda-filters" method="GET" action="{{ $directoryRoute }}" data-directory-filters>
                <div class="form-group">
                    <label for="clinical-agenda-search">{{ __('new_system.clinical_agenda.doctor_or_id') }}</label>
                    <div class="clinical-agenda-input">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input
                            id="clinical-agenda-search"
                            class="form-control"
                            type="search"
                            name="buscar"
                            value="{{ $filters['search'] }}"
                            placeholder="{{ __('new_system.clinical_agenda.search_professional') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="form-group">
                    <label for="clinical-agenda-specialty">{{ __('new_system.clinical_agenda.specialty') }}</label>
                    <select id="clinical-agenda-specialty" class="form-control" name="especialidad">
                        <option value="">{{ __('new_system.clinical_agenda.all_specialties') }}</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected((string) $filters['specialty'] === (string) $specialty->id)>
                                {{ $specialty->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <noscript>
                    <button type="submit" class="btn btn-primary">{{ __('new_system.clinical_agenda.filter') }}</button>
                </noscript>
            </form>

            <div class="clinical-agenda-grid" data-doctor-grid aria-live="polite">
                @forelse ($doctors as $doctor)
                    @php
                        $doctorPhotoName = basename(trim((string) $doctor->imagen_url));
                        $doctorHasPhoto = $doctorPhotoName !== '' && mb_strtolower($doctorPhotoName) !== 'avatar.jpg' && is_file(public_path('avatars/'.$doctorPhotoName));
                    @endphp
                    <article class="clinical-agenda-doctor">
                        <div class="clinical-agenda-doctor__identity">
                            <div class="clinical-agenda-avatar" aria-hidden="true">
                                @if ($doctorHasPhoto)
                                    <img src="{{ asset('avatars/'.$doctorPhotoName) }}" alt="" loading="lazy">
                                @else
                                    <span>{{ $doctor->initials ?: 'MD' }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="clinical-agenda-doctor__label">{{ __('new_system.clinical_agenda.professional') }}</span>
                                <h4>{{ __('new_system.clinical_agenda.doctor_prefix') }} {{ $doctor->display_name }}</h4>
                                <small><i class="fa fa-id-card-o" aria-hidden="true"></i> {{ $doctor->id }}</small>
                            </div>
                        </div>

                        <div class="clinical-agenda-specialties">
                            <span>{{ __('new_system.clinical_agenda.specialty') }}</span>
                            <div>
                                @forelse ($doctor->specialties as $specialty)
                                    <span class="clinical-agenda-badge">{{ $specialty->nombre }}</span>
                                @empty
                                    <span class="clinical-agenda-badge clinical-agenda-badge--muted">{{ __('new_system.clinical_agenda.no_specialty') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="clinical-agenda-doctor__actions">
                            @unless ($scheduleDirectory)
                                <a class="btn btn-primary" href="{{ route('v3.doctor-agenda.calendar', ['doctor' => $doctor->id]) }}">
                                    <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> {{ __('new_system.clinical_agenda.open_agenda') }}
                                </a>
                            @endunless
                            <a class="btn {{ $scheduleDirectory ? 'btn-primary' : 'btn-default' }}" href="{{ route('v3.doctor-agenda.schedule', ['doctor' => $doctor->id]) }}">
                                <i class="fa fa-clock-o" aria-hidden="true"></i> {{ __('new_system.clinical_agenda.manage_schedule') }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="clinical-agenda-empty">
                        <span class="clinical-agenda-empty__icon" aria-hidden="true"><i class="fa fa-user-md"></i></span>
                        <h4>{{ __('new_system.clinical_agenda.no_doctors') }}</h4>
                        <p>{{ __('new_system.clinical_agenda.empty_help') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('js/clinical-agenda.js') }}?v={{ filemtime(public_path('js/clinical-agenda.js')) }}"></script>
    @endpush
</x-new-system.layout>
