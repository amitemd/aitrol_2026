(function () {
    'use strict';
    const form = document.querySelector('[data-emergency-intake]');
    if (!form) return;
    const field = name => form.elements.namedItem(name);
    const feedback = form.querySelector('#admission-feedback');
    const results = form.querySelector('#admission-patient-results');
    const patientStatus = form.querySelector('#admission-patient-status');
    const submit = form.querySelector('[type="submit"]');
    const originalButton = submit.innerHTML;
    const conflict = form.querySelector('[data-clinical-conflict]');
    const toggle = form.parentElement?.querySelector('[data-intake-toggle]');
    if (toggle) toggle.addEventListener('click', () => {
        form.hidden = !form.hidden;
        toggle.setAttribute('aria-expanded', String(!form.hidden));
        toggle.setAttribute('aria-label', form.hidden ? 'Expandir formulario' : 'Contraer formulario');
        toggle.querySelector('i').className = form.hidden ? 'fa fa-plus' : 'fa fa-minus';
    });
    const mapping = {
        nombre1: 'nombre1', nombre2: 'nombre2', apellido1: 'apellido1', apellido2: 'apellido2',
        f_nacimiento: 'fecha_nacimiento', sexo: 'sexo', ciudad: 'ciudad', telefono1: 'telefono1',
        telefono2: 'telefono2', direccion: 'direccion', estado: 'estadocivil', ocupacion: 'ocupacion',
        barrio: 'barrio', parroquia: 'parroquia', canton: 'canton', provincia: 'provincia',
        nacionalidad: 'nacionalidad', direccion_familiar: 'direccion_familiar', forma_llegada: 'forma_llegada',
        fuente_informacion: 'fuente_informacion', empresa: 'empresa_trabajo', instruccion: 'instruccion',
        referido: 'referido', telefono_inst_per_paci: 'telefono_inst_per_paci', telefono_llamar: 'telefono3',
        parentesco: 'parentesco_afinidad', llamar_a: 'llamar_a', id_seguro: 'id_seguro',
        zona: 'zona_ur', grupo_cultural: 'grupo_cultural',
    };
    let saving = false, loadingPatient = false, lookupVersion = 0, searchVersion = 0, searchTimer;

    function showError(message) {
        feedback.textContent = message;
        feedback.hidden = false;
        feedback.focus();
    }

    function fillPatient(data, unchanged) {
        Object.entries(mapping).forEach(([name, key]) => {
            const input = field(name);
            if (unchanged && input.value !== unchanged[name]) return;
            input.value = data[key] == null ? '' : String(data[key]);
        });
        field('apellidos_nombres').value = ['apellido1', 'apellido2', 'nombre1', 'nombre2']
            .map(key => data[key]).filter(Boolean).join(' ');
    }

    async function readResponse(response) {
        const data = await response.json().catch(() => null);
        if (!response.ok) {
            let message = data?.msj || data?.message || 'No se pudo completar la solicitud. Intente nuevamente.';
            if (response.status === 419 || response.status === 401) message = 'La sesión venció. Conserve los datos escritos e inicie sesión de nuevo.';
            if (response.status === 403) message = data?.message || 'No tiene permiso para completar este ingreso.';
            throw Object.assign(new Error(message), {data, status: response.status});
        }
        if (!data || typeof data !== 'object') throw new Error('El servidor no confirmó la operación. Los datos permanecen en el formulario.');
        return data;
    }

    async function lookupPatient(id) {
        const version = ++lookupVersion;
        ++searchVersion;
        results.hidden = true;
        field('clinical_override').value = '0';
        field('clinical_override_reason').value = '';
        conflict.hidden = true;
        fillPatient({});
        const unchanged = Object.fromEntries(Object.keys(mapping).map(name => [name, field(name).value]));
        loadingPatient = Boolean(id);
        patientStatus.textContent = id ? 'Buscando paciente…' : '';
        if (!id) return;
        try {
            const response = await fetch(form.dataset.lookupUrl + '/' + encodeURIComponent(id), {
                credentials: 'same-origin', headers: {'Accept': 'application/json'},
            });
            // The legacy lookup returns plain "no" for a new patient.
            const text = await response.text();
            if (version !== lookupVersion) return;
            if (!response.ok) throw new Error('No se pudo consultar al paciente. Intente buscarlo nuevamente.');
            if (text.trim() === 'no' || text.trim() === '"no"') {
                patientStatus.textContent = 'Paciente no encontrado. Complete sus datos para registrar el ingreso.';
                return;
            }
            const data = JSON.parse(text);
            if (!data || String(data.id) !== id) throw new Error('No se pudo verificar la identidad del paciente. Repita la búsqueda.');
            fillPatient(data, unchanged);
            patientStatus.textContent = 'Datos del paciente cargados.';
        } catch (error) {
            if (version === lookupVersion) showError(error instanceof SyntaxError ? 'No se pudo consultar al paciente. Repita la búsqueda.' : error.message);
        } finally {
            if (version === lookupVersion) loadingPatient = false;
        }
    }

    if (form.dataset.lockedPatient !== '1') {
        field('cedula').addEventListener('change', () => lookupPatient(field('cedula').value.trim()));
        field('cedula').addEventListener('input', () => { ++lookupVersion; loadingPatient = false; });
        field('apellidos_nombres').addEventListener('input', () => {
            clearTimeout(searchTimer);
            const version = ++searchVersion;
            const term = field('apellidos_nombres').value.trim();
            results.hidden = true;
            if (term.length < 3) { patientStatus.textContent = 'Escriba al menos tres caracteres para buscar.'; return; }
            searchTimer = setTimeout(async () => {
                patientStatus.textContent = 'Buscando coincidencias…';
                try {
                    const response = await fetch(form.dataset.searchUrl + '?term=' + encodeURIComponent(term), {
                        credentials: 'same-origin', headers: {'Accept': 'application/json'},
                    });
                    const data = await readResponse(response);
                    if (version !== searchVersion) return;
                    const patients = Array.isArray(data) ? data.filter(patient => patient.id) : [];
                    results.replaceChildren();
                    patients.forEach(patient => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.textContent = patient.value + ' · ' + patient.id;
                        button.addEventListener('click', () => {
                            field('cedula').value = patient.id;
                            lookupPatient(String(patient.id));
                        });
                        results.append(button);
                    });
                    results.hidden = !patients.length;
                    patientStatus.textContent = patients.length ? 'Seleccione un paciente de la lista.' : 'Sin coincidencias. Puede ingresar su identificación y registrar sus datos.';
                } catch (error) {
                    if (version === searchVersion) patientStatus.textContent = 'No se pudo realizar la búsqueda. Intente nuevamente.';
                }
            }, 300);
        });
    }

    form.addEventListener('input', event => {
        event.target.classList.remove('is-invalid');
        event.target.removeAttribute('aria-invalid');
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (saving) return;
        if (loadingPatient) { showError('Espere a que termine la búsqueda del paciente.'); return; }
        if (!form.reportValidity()) return;
        feedback.hidden = true;
        conflict.hidden = true;
        const body = new FormData(form);
        saving = true;
        const controls = Array.from(form.elements).filter(input => !input.disabled);
        controls.forEach(input => { input.disabled = true; });
        submit.innerHTML = 'Guardando ingreso…';
        try {
            const response = await fetch(form.action, {
                method: 'POST', credentials: 'same-origin', body,
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': field('_token').value},
            });
            const data = await readResponse(response);
            if (data.respuesta !== 'success') throw Object.assign(new Error(data.msj || 'No se pudo completar el ingreso.'), {data});
            window.location.assign(data.url || form.dataset.returnUrl);
        } catch (error) {
            const data = error.data || {};
            if (data.respuesta === 'conflicto_clinico' && data.puede_autorizar) {
                conflict.querySelector('[data-conflict-message]').textContent = data.msj;
                conflict.hidden = false;
            } else {
                const messages = Object.values(data.errors || {}).flat();
                Object.keys(data.errors || {}).forEach(name => {
                    const input = field(name);
                    if (input) { input.classList.add('is-invalid'); input.setAttribute('aria-invalid', 'true'); }
                });
                showError(messages.length ? messages.join('\n') : error instanceof TypeError
                    ? 'No se pudo conectar. Los datos permanecen en el formulario; intente guardar nuevamente.' : error.message);
            }
        } finally {
            controls.forEach(input => { input.disabled = false; });
            submit.innerHTML = originalButton;
            saving = false;
            if (!conflict.hidden) conflict.querySelector('textarea').focus();
        }
    });
    conflict.querySelector('[data-authorize]').addEventListener('click', () => {
        const reason = conflict.querySelector('textarea').value.trim();
        if (reason.length < 10) { showError('Escriba un motivo clínico de al menos 10 caracteres.'); return; }
        field('clinical_override').value = '1';
        field('clinical_override_reason').value = reason;
        form.requestSubmit();
    });
    conflict.querySelector('[data-cancel-override]').addEventListener('click', () => {
        field('clinical_override').value = '0';
        field('clinical_override_reason').value = '';
        conflict.hidden = true;
    });
}());
