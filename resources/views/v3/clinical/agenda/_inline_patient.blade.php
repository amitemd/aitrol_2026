<div class="da-patient-create-row">
    <button type="button" class="btn btn-outline-primary" id="surgery-inline-patient-toggle"
        aria-controls="surgery-inline-patient" aria-expanded="false">
        <i class="fas fa-user-plus mr-1" aria-hidden="true"></i> Crear nuevo paciente
    </button>
</div>
<section id="surgery-inline-patient" class="da-availability mb-2" hidden>
    <div class="row align-items-end">
        <div class="col-sm">
            <label for="surgery-patient-id">Identificación</label>
            <input class="form-control" id="surgery-patient-id" form="surgery-inline-patient-form"
                name="id" type="text" minlength="3" maxlength="13" autocomplete="off" required>
        </div>
        <div class="col-sm-auto mt-2">
            <button class="btn btn-outline-primary" type="button" data-lookup-patient>Consultar identificación</button>
        </div>
    </div>
    <p class="da-help my-2" role="status" hidden></p>
    <fieldset class="mt-2" hidden disabled>
        <legend class="h6" data-patient-heading>Datos básicos del paciente</legend>
        <div class="row">
            @foreach ([
                'nombre1' => 'Primer nombre', 'nombre2' => 'Segundo nombre',
                'apellido1' => 'Primer apellido', 'apellido2' => 'Segundo apellido',
                'fecha_nacimiento' => 'Fecha de nacimiento', 'telefono1' => 'Teléfono',
                'patient_email' => 'Correo electrónico',
            ] as $name => $label)
                <label class="col-sm-6 mb-2">
                    {{ $label }}
                    <input class="form-control" form="surgery-inline-patient-form" name="{{ $name }}"
                        type="{{ $name === 'fecha_nacimiento' ? 'date' : ($name === 'patient_email' ? 'email' : 'text') }}"
                        @if($name === 'fecha_nacimiento') max="{{ date('Y-m-d') }}" @else maxlength="{{ $name === 'patient_email' ? 191 : 60 }}" @endif
                        @if($name !== 'nombre2') required @endif>
                </label>
            @endforeach
            @foreach ([
                'tipo_documento' => ['Tipo de documento', [1 => 'Cédula', 2 => 'Pasaporte', 3 => 'Otro documento']],
                'sexo' => ['Sexo', [1 => 'Masculino', 2 => 'Femenino']],
                'id_pais' => ['País', $patientRegistration['countries']->pluck('nombre', 'id')],
                'id_seguro' => ['Seguro / convenio', $patientRegistration['insurers']->pluck('nombre', 'id')],
                'origen' => ['Cómo conoció la clínica', ['MEDIO IMPRESO' => 'Medio impreso', 'MEDIO DIGITAL' => 'Medio digital', 'REFERIDO' => 'Referido', 'OTRO' => 'Otro']],
            ] as $name => [$label, $options])
                <label class="col-sm-6 mb-2" @if($name === 'origen') data-new-patient-only @endif>
                    {{ $label }}
                    <select class="form-control" form="surgery-inline-patient-form" name="{{ $name }}" data-select2-off required>
                        <option value="">Seleccione</option>
                        @foreach($options as $value => $text)
                            <option value="{{ $value }}">{{ $text }}</option>
                        @endforeach
                    </select>
                </label>
            @endforeach
        </div>
        <details class="mb-2" data-patient-parents hidden>
            <summary>Datos del padre o la madre (para menores de edad)</summary>
            <div class="row mt-2">
                <label class="col-sm-6 mb-2">Cédula del padre
                    <input class="form-control" form="surgery-inline-patient-form" name="father_id" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" disabled>
                </label>
                <label class="col-sm-6 mb-2">Cédula de la madre
                    <input class="form-control" form="surgery-inline-patient-form" name="mother_id" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" disabled>
                </label>
            </div>
        </details>
    </fieldset>
    <div class="d-flex justify-content-end mt-2">
        <button class="btn btn-light mr-2" type="button" data-close-patient>Cerrar</button>
        <button class="btn btn-primary" type="submit" form="surgery-inline-patient-form" hidden data-save-patient>Guardar y seleccionar</button>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('surgery-inline-patient-form');
    const panel = document.getElementById('surgery-inline-patient');
    const toggle = document.getElementById('surgery-inline-patient-toggle');
    const identification = form.elements.namedItem('id');
    const fields = panel.querySelector('fieldset');
    const heading = panel.querySelector('[data-patient-heading]');
    const message = panel.querySelector('[role="status"]');
    const lookupButton = panel.querySelector('[data-lookup-patient]');
    const saveButton = panel.querySelector('[data-save-patient]');
    const results = document.querySelector('.js-patient-results');
    const search = document.querySelector('.js-patient-search');
    const registrationFields = panel.querySelectorAll('[data-new-patient-only]');
    const birthDate = form.elements.namedItem('fecha_nacimiento');
    const parentDetails = panel.querySelector('[data-patient-parents]');
    const parentInputs = parentDetails.querySelectorAll('input');
    let verifiedIdentification;
    let busy = false;

    function setOpen(open) {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        (open ? identification : toggle).focus();
    }

    function showMessage(text) {
        message.textContent = text;
        message.hidden = !text;
    }

    function setBusy(value) {
        busy = value;
        identification.readOnly = value;
        lookupButton.disabled = value;
        saveButton.disabled = value;
    }

    function showFields(existing) {
        fields.hidden = false;
        fields.disabled = existing;
        saveButton.hidden = existing;
        heading.textContent = existing ? 'Datos del paciente registrado' : 'Datos básicos del nuevo paciente';
        registrationFields.forEach(element => { element.hidden = existing; });
        syncParents();
    }

    function syncParents() {
        let isMinor = false;
        if (!fields.disabled && birthDate.value && birthDate.value <= birthDate.max) {
            const birthday = new Date(birthDate.value + 'T00:00:00Z');
            birthday.setUTCFullYear(birthday.getUTCFullYear() + 18);
            isMinor = birthday.toISOString().slice(0, 10) > birthDate.max;
        }
        if (isMinor && parentDetails.hidden) parentDetails.open = true;
        parentDetails.hidden = !isMinor;
        parentInputs.forEach(input => { input.disabled = !isMinor; });
    }

    function selectPatient(patient) {
        results.dispatchEvent(new CustomEvent('agenda:patient-selected', { detail: patient }));
    }

    async function requestJson(url, options) {
        const response = await fetch(url, {
            ...options,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        const data = await response.json();
        if (!response.ok) {
            const errors = Object.values(data.errors || {});
            throw new Error(errors.length ? errors[0][0] : data.message);
        }
        return data;
    }

    async function lookup() {
        if (busy) return;
        identification.value = identification.value.trim();
        if (identification.value.length < 3 || !identification.reportValidity()) {
            showMessage('Ingresa una identificación de al menos 3 caracteres.');
            return;
        }

        const url = new URL(search.dataset.searchUrl, window.location.href);
        url.searchParams.set('q', identification.value);
        url.searchParams.set('exact', '1');
        setBusy(true);
        showMessage('Consultando identificación…');
        try {
            const data = await requestJson(url);
            const patient = data.items[0];
            verifiedIdentification = identification.value;
            showFields(Boolean(patient));
            if (patient) {
                for (const [name, value] of Object.entries(patient.details)) {
                    const input = form.elements.namedItem(name);
                    input.value = name === 'fecha_nacimiento' ? String(value || '').slice(0, 10) : (value ?? '');
                }
                selectPatient(patient);
                showMessage('Paciente encontrado y seleccionado para la cirugía.');
            } else {
                showMessage('No se encontró un paciente con esa identificación. Completa los datos para registrarlo.');
                form.elements.namedItem('nombre1').focus();
            }
        } catch (error) {
            showMessage(error.message || 'No se pudo consultar la identificación.');
        } finally {
            setBusy(false);
        }
    }

    toggle.addEventListener('click', () => setOpen(panel.hidden));
    panel.querySelector('[data-close-patient]').addEventListener('click', () => setOpen(false));
    lookupButton.addEventListener('click', lookup);
    birthDate.addEventListener('input', syncParents);
    birthDate.addEventListener('change', syncParents);
    identification.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            lookup();
        }
    });
    identification.addEventListener('input', () => {
        if (!verifiedIdentification) return;
        const entered = identification.value;
        form.reset();
        identification.value = entered;
        verifiedIdentification = undefined;
        fields.hidden = true;
        fields.disabled = true;
        saveButton.hidden = true;
        syncParents();
        showMessage('');
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (busy || fields.disabled || verifiedIdentification !== identification.value) return;
        const body = new FormData(form);
        setBusy(true);
        showMessage('Registrando paciente…');
        try {
            const {id} = await requestJson(form.action, { method: 'POST', body });
            const name = ['apellido1', 'apellido2', 'nombre1', 'nombre2']
                .map(key => body.get(key)).filter(Boolean).join(' ').toLocaleUpperCase('es');
            selectPatient({
                id, name, birth: body.get('fecha_nacimiento'), appointment_type: 0,
            });
            showFields(true);
            showMessage('Paciente registrado y seleccionado para la cirugía.');
        } catch (error) {
            showMessage(error.message || 'No se pudo registrar al paciente.');
        } finally {
            setBusy(false);
        }
    });
});
</script>
