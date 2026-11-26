<style>
    .surgery-booking {
        --booking-ink: #123d57;
        --booking-surface: #fff;
        --booking-border: #cfe2eb;
        max-width: 1440px;
        margin: 18px auto 32px;
        padding-inline: 12px;
    }
    .surgery-booking__header {
        display: flex; align-items: center; justify-content: space-between; gap: 14px;
        padding: 14px 18px; border-radius: 12px 12px 0 0;
        background: linear-gradient(100deg, #104968, #087594 72%, #087e81);
        color: #fff;
    }
    .surgery-booking__header h1 { margin: 0; color: inherit; font-size: clamp(1.2rem, 2vw, 1.55rem); font-weight: 700; }
    .surgery-booking__back {
        display: inline-flex; align-items: center; gap: 7px; flex-shrink: 0;
        padding: 7px 11px; border: 1px solid rgba(255,255,255,.55);
        border-radius: 7px; color: #fff; font-weight: 600;
    }
    .surgery-booking__back:hover, .surgery-booking__back:focus { background: rgba(255,255,255,.16); color: #fff; }
    .surgery-booking__selector {
        padding: 13px 18px 15px; border: 1px solid var(--booking-border); border-top: 0;
        border-radius: 0 0 12px 12px; background: var(--booking-surface); color: var(--booking-ink);
    }
    .surgery-booking__selector .alert { margin-bottom: 10px; }
    .surgery-booking__selector p:last-child { margin-bottom: 0; }
    .surgery-booking__doctor-form { display: flex; align-items: flex-end; flex-wrap: wrap; gap: 10px; }
    .surgery-booking__doctor-field { flex: 1 1 280px; min-width: 0; max-width: 640px; }
    .surgery-booking__doctor-field label { display: block; margin: 0 0 5px; color: inherit; font-weight: 700; }
    .surgery-booking__doctor-field .form-control { min-height: 39px; }
    .surgery-booking__continue { min-height: 39px; white-space: nowrap; }
    .surgery-booking__hours { margin-top: 12px; padding: 10px 12px; border: 1px solid var(--booking-border); border-radius: 8px; }
    .surgery-booking__hours ul { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; padding: 0; margin: 6px 0 0; }
    .surgery-booking__hours li { padding: 3px 8px; border: 1px solid var(--booking-border); border-radius: 5px; }
    .surgery-booking__hours p { margin: 6px 0 0; }
    .surgery-booking__details { margin-top: 12px; scroll-margin-top: 90px; }
    .surgery-booking .surgery-booking__details .co-page { padding: 0; }
    .surgery-booking .surgery-booking__details .da-form-wrap { padding: 12px; gap: 12px; }
    .surgery-booking .surgery-booking__details .da-panel__head p { font-size: 12px; line-height: 1.4; }
    .dark-layout .surgery-booking { --booking-ink: #e0eef5; --booking-surface: #202e3e; --booking-border: #3a5163; }
    @media (max-width: 575px) {
        .surgery-booking { margin-top: 10px; padding-inline: 4px; }
        .surgery-booking__header, .surgery-booking__selector { padding-inline: 12px; }
        .surgery-booking__header { flex-wrap: wrap; gap: 8px; }
        .surgery-booking__doctor-field, .surgery-booking__continue { width: 100%; max-width: none; }
        .surgery-booking .surgery-booking__details .da-form-wrap { padding: 7px; }
    }
</style>
<div class="surgery-booking">
    @if(empty($bookingModal))
    <header class="surgery-booking__header">
        <h1>Agendar cirugía</h1>
        <a class="surgery-booking__back" href="{{ route('hospital.index_modulos', ['id_paso' => 5]) }}">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver a Cirugía
        </a>
    </header>
    @endif
    <section class="surgery-booking__selector" aria-label="Selección del médico principal">
            @if($invalidDoctor)
                <div class="alert alert-warning" role="alert">El médico seleccionado no está disponible en tu agenda. Selecciona uno de la lista.</div>
            @endif
            @if($bookingError)
                <div class="alert alert-warning" role="alert">{{ $bookingError }}</div>
            @endif
            @if($doctors->isEmpty())
                <p role="status">No hay médicos disponibles para agendar con tu usuario en la empresa activa.</p>
            @else
                <form class="surgery-booking__doctor-form" method="GET" action="{{ empty($bookingModal) ? route('hospital.admision', ['id_paso' => 5]).'#datos-agenda-cirugia' : route('hospital.index_modulos', ['id_paso' => 5]) }}">
                    @if(!empty($bookingModal))
                        <input type="hidden" name="agendar" value="1">
                        @foreach($bookingFilters as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                    @endif
                    <div class="surgery-booking__doctor-field">
                        <label for="surgeryDoctor">Médico principal</label>
                        <select id="surgeryDoctor" name="doctor" class="form-control" autocomplete="off"
                            data-schedule-url="{{ route('hospital.admision', ['id_paso' => 5]) }}" required>
                            <option value="">Seleccione</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @if((string) $doctor->id === (string) $selectedDoctorId) selected @endif>{{ $doctor->display_name }} · {{ $doctor->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary surgery-booking__continue" aria-controls="datos-agenda-cirugia" aria-expanded="{{ $agenda ? 'true' : 'false' }}">
                        Continuar <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
                <section id="surgeryDoctorHours" class="surgery-booking__hours" aria-label="Horario del médico principal" hidden>
                    <strong>Horario del médico principal</strong>
                    <div role="status" aria-live="polite"></div>
                </section>
            @endif
    </section>
    <section id="datos-agenda-cirugia" class="surgery-booking__details" aria-label="Datos de la cirugía" @if($agenda) data-doctor="{{ $agenda['doctor']->id }}" @else hidden @endif>
        @if($agenda)
            @include('v3.clinical.agenda.editor', $agenda + ['embeddedSurgery' => true])
        @endif
    </section>
</div>
@if($doctors->isNotEmpty())
<script>
(function () {
    const doctor = document.getElementById('surgeryDoctor');
    const details = document.getElementById('datos-agenda-cirugia');
    const button = doctor.form.querySelector('[aria-controls="datos-agenda-cirugia"]');
    const hours = document.getElementById('surgeryDoctorHours');
    const result = hours.querySelector('[role="status"]');
    let request;

    async function syncDoctor() {
        const doctorId = doctor.value;
        const matches = Boolean(doctorId) && doctorId === details.dataset.doctor;
        details.hidden = !matches;
        button.setAttribute('aria-expanded', String(matches));
        if (request) request.abort();

        hours.hidden = !doctorId;
        result.replaceChildren();
        if (!doctorId) return;

        const current = new AbortController();
        request = current;
        const url = new URL(doctor.dataset.scheduleUrl, window.location.href);
        url.hash = '';
        url.searchParams.set('doctor', doctorId);
        url.searchParams.set('horario', '1');
        result.textContent = 'Consultando horario…';
        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: current.signal
            });
            if (!response.ok) throw new Error();
            const schedule = await response.json();
            if (current.signal.aborted || doctor.value !== doctorId) return;

            const slots = [...schedule.recurring, ...schedule.exceptions];
            if (!slots.length) {
                result.textContent = 'El médico seleccionado no tiene horarios configurados.';
                return;
            }

            const list = document.createElement('ul');
            for (const slot of slots) {
                const item = document.createElement('li');
                item.textContent = slot;
                list.append(item);
            }
            result.replaceChildren(list);
        } catch (error) {
            if (!current.signal.aborted) result.textContent = 'No se pudo consultar el horario del médico.';
        }
    }

    if (window.jQuery) {
        window.jQuery(doctor).on('change.surgeryBooking', syncDoctor);
    } else {
        doctor.addEventListener('change', syncDoctor);
    }
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) syncDoctor();
    });
    syncDoctor();
})();
</script>
@endif
