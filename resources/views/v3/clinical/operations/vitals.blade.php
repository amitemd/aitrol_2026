@include('v3.clinical.operations._styles')

@php
    $patientName = trim(implode(' ', array_filter(array(
        $agenda->pnombre1,
        $agenda->pnombre2 !== '(N/A)' ? $agenda->pnombre2 : null,
        $agenda->papellido1,
        $agenda->papellido2 !== '(N/A)' ? $agenda->papellido2 : null,
    ))));
    $birthDate = $agenda->fecha_nacimiento ? date('d/m/Y', strtotime($agenda->fecha_nacimiento)) : null;
    $age = null;
    if ($agenda->fecha_nacimiento) {
        try {
            $age = (new DateTime($agenda->fecha_nacimiento))->diff(new DateTime('today'))->y;
        } catch (Exception $exception) {
            $age = null;
        }
    }
    $fieldValue = function ($name, $storedValue, $fallback = 0) {
        return old($name, ($storedValue !== null && $storedValue !== '') ? $storedValue : $fallback);
    };
@endphp

<style>
    .vt-page{padding:13px}.vt-hero{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:15px 18px;margin-bottom:11px;border-radius:12px;color:#fff;background:linear-gradient(115deg,#0b5e89,#138fb4 62%,#16aaa5);box-shadow:0 7px 18px rgba(20,91,122,.11)}.vt-hero__main{display:flex;align-items:center;gap:12px}.vt-hero__icon{display:grid;place-items:center;width:46px;height:46px;border:1px solid rgba(255,255,255,.3);border-radius:11px;background:rgba(255,255,255,.11);font-size:20px}.vt-eyebrow{font-size:9.5px;font-weight:800;letter-spacing:.9px}.vt-hero h1{margin:1px 0 2px;font-size:21px;font-weight:800}.vt-hero p{margin:0;font-size:11.5px;opacity:.94}.vt-hero .btn{min-height:34px;border-radius:8px;font-size:12px;font-weight:800}
    .vt-card{overflow:hidden;margin-bottom:11px;border:1px solid #d4e5ed;border-radius:11px;background:#fff;box-shadow:0 5px 16px rgba(29,76,99,.055)}.vt-card__head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;border-bottom:1px solid #d4e5ed;background:#eef9fc}.vt-card__title{display:flex;align-items:center;gap:9px}.vt-card__title>i{display:grid;place-items:center;width:31px;height:31px;border-radius:8px;color:#0788b7;background:#d9f3f8}.vt-card__title h2{margin:0;color:#0b3554;font-size:14px;font-weight:800}.vt-card__title p{margin:1px 0 0;color:#65839a;font-size:10.5px}
    .vt-patient{display:grid;grid-template-columns:minmax(250px,2fr) repeat(3,minmax(135px,1fr));gap:9px;padding:12px 14px}.vt-patient__item{display:flex;align-items:center;gap:10px;min-height:58px;padding:9px 11px;border:1px solid #dbe8ed;border-radius:9px;background:#fbfdfe}.vt-patient__item>i{display:grid;flex:0 0 34px;place-items:center;width:34px;height:34px;border-radius:9px;color:#087ea3;background:#e4f5f8}.vt-patient__item span{display:block;color:#748d9d;font-size:9px;font-weight:800;letter-spacing:.3px;text-transform:uppercase}.vt-patient__item strong{display:block;margin-top:2px;color:#153f5c;font-size:12px;line-height:1.3}.vt-patient__meta{color:#71899a;font-size:10px;font-weight:600}
    .vt-form{padding:14px}.vt-section{margin-bottom:15px}.vt-section:last-of-type{margin-bottom:4px}.vt-section__title{display:flex;align-items:center;gap:8px;margin:0 0 9px;color:#0d4769;font-size:12px;font-weight:800;text-transform:uppercase}.vt-section__title:after{height:1px;flex:1;background:#dce8ed;content:""}.vt-grid{display:grid;grid-template-columns:repeat(4,minmax(150px,1fr));gap:10px}.vt-field label{display:block;margin:0 0 4px;color:#244a63;font-size:10px;font-weight:800;text-transform:uppercase}.vt-field .form-control{height:37px;border-color:#c8dce5;border-radius:8px;color:#183f58;background:#fff;font-size:12px}.vt-field .form-control:focus{border-color:#1b9bb6;box-shadow:0 0 0 3px rgba(27,155,182,.12)}.vt-field .form-control[readonly]{color:#436477;background:#f1f6f8}.vt-field__hint{display:block;margin-top:3px;color:#78909c;font-size:9px}.vt-field.has-error .form-control{border-color:#dc5969}.vt-error{display:block;margin-top:4px;color:#c33d50;font-size:10px}.vt-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding-top:13px;border-top:1px solid #dce8ed}.vt-actions .btn{min-width:120px;border-radius:8px;font-size:12px;font-weight:800}.vt-alert{display:none;margin:0 14px 12px;padding:9px 11px;border:1px solid #f1d195;border-radius:8px;color:#7b5513;background:#fff8e8;font-size:11px}.vt-alert.is-visible{display:flex;align-items:center;gap:8px}
    .dark-mode .vt-card{border-color:#34505e;background:#152832}.dark-mode .vt-card__head{border-color:#34505e;background:#1b323e}.dark-mode .vt-card__title h2,.dark-mode .vt-patient__item strong,.dark-mode .vt-section__title,.dark-mode .vt-field label{color:#dbe8ee}.dark-mode .vt-card__title p,.dark-mode .vt-patient__item span,.dark-mode .vt-patient__meta,.dark-mode .vt-field__hint{color:#9db4bf}.dark-mode .vt-patient__item{border-color:#34505e;background:#1a303b}.dark-mode .vt-patient__item>i,.dark-mode .vt-card__title>i{color:#71d4df;background:#234653}.dark-mode .vt-section__title:after{background:#34505e}.dark-mode .vt-actions{border-color:#34505e}.dark-mode .vt-field .form-control{border-color:#42606f;color:#e0edf2;background:#1b323e}.dark-mode .vt-field .form-control[readonly]{color:#b7ccd5;background:#203b48}
    @media(max-width:1100px){.vt-patient,.vt-grid{grid-template-columns:repeat(2,minmax(150px,1fr))}.vt-patient__item:first-child{grid-column:1/-1}}@media(max-width:650px){.vt-page{padding:9px}.vt-hero{align-items:flex-start;flex-direction:column}.vt-hero h1{font-size:19px}.vt-patient,.vt-grid{grid-template-columns:1fr}.vt-patient__item:first-child{grid-column:auto}.vt-actions{align-items:stretch;flex-direction:column-reverse}.vt-actions .btn{width:100%}}
</style>

<div class="vt-page">
    <section class="vt-hero">
        <div class="vt-hero__main">
            <div class="vt-hero__icon"><i class="fas fa-heartbeat" aria-hidden="true"></i></div>
            <div>
                <div class="vt-eyebrow">ENFERMERÍA · V3</div>
                <h1>Toma de signos vitales</h1>
                <p>Registre la valoración clínica inicial de la atención #{{ $agenda->id }}.</p>
            </div>
        </div>
        <a class="btn btn-light" href="{{ route('v3.clinical-operations.nursing') }}"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Volver al listado</a>
    </section>

    <section class="vt-card" aria-labelledby="vt-patient-title">
        <header class="vt-card__head">
            <div class="vt-card__title">
                <i class="fas fa-user-injured" aria-hidden="true"></i>
                <div><h2 id="vt-patient-title">Paciente y atención</h2><p>Confirme la identidad antes de registrar los valores.</p></div>
            </div>
        </header>
        <div class="vt-patient">
            <div class="vt-patient__item"><i class="fas fa-id-card" aria-hidden="true"></i><div><span>Paciente</span><strong>{{ $patientName ?: 'Paciente no localizado' }}</strong><div class="vt-patient__meta">{{ $agenda->id_paciente }}</div></div></div>
            <div class="vt-patient__item"><i class="fas fa-birthday-cake" aria-hidden="true"></i><div><span>Edad</span><strong>{{ $age !== null ? $age.' años' : 'No disponible' }}</strong><div class="vt-patient__meta">{{ $birthDate ?: 'Nacimiento no registrado' }}</div></div></div>
            <div class="vt-patient__item"><i class="fas fa-shield-alt" aria-hidden="true"></i><div><span>Seguro</span><strong>{{ $agenda->hsnombre ?: 'No registrado' }}</strong></div></div>
            <div class="vt-patient__item"><i class="fas fa-notes-medical" aria-hidden="true"></i><div><span>Historia clínica</span><strong>{{ $agenda->hcid }}</strong><div class="vt-patient__meta">Atención #{{ $agenda->id }}</div></div></div>
        </div>
    </section>

    <section class="vt-card" aria-labelledby="vt-form-title">
        <header class="vt-card__head">
            <div class="vt-card__title">
                <i class="fas fa-stethoscope" aria-hidden="true"></i>
                <div><h2 id="vt-form-title">Valoración clínica</h2><p>Complete los valores disponibles; las medidas derivadas se calculan automáticamente.</p></div>
            </div>
        </header>

        <form class="vt-form" id="vitals-form" method="POST" action="{{ route('enfermeria.guardar') }}">
            {{ csrf_field() }}
            <input type="hidden" name="id_paciente" value="{{ $agenda->id_paciente }}">
            <input type="hidden" name="hcid" value="{{ $agenda->hcid }}">

            <div class="vt-section">
                <h3 class="vt-section__title"><i class="fas fa-heartbeat" aria-hidden="true"></i> Signos vitales</h3>
                <div class="vt-grid">
                    <div class="vt-field{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                        <label for="gruposanguineo">Grupo sanguíneo</label>
                        <select id="gruposanguineo" class="form-control" name="gruposanguineo" required>
                            <option value="">Seleccionar...</option>
                            @foreach (array('AB-', 'AB+', 'A-', 'A+', 'B-', 'B+', 'O-', 'O+') as $bloodType)
                                <option value="{{ $bloodType }}" {{ old('gruposanguineo', $agenda->gruposanguineo) === $bloodType ? 'selected' : '' }}>{{ $bloodType }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('gruposanguineo'))<span class="vt-error">{{ $errors->first('gruposanguineo') }}</span>@endif
                    </div>
                    <div class="vt-field{{ $errors->has('presion') ? ' has-error' : '' }}"><label for="presion">Presión arterial</label><input id="presion" class="form-control" type="text" name="presion" value="{{ $fieldValue('presion', $agenda->presion) }}" inputmode="text" placeholder="120/80"><span class="vt-field__hint">Ejemplo: 120/80 mmHg</span></div>
                    <div class="vt-field{{ $errors->has('pulso') ? ' has-error' : '' }}"><label for="pulso">Pulso</label><input id="pulso" class="form-control" type="number" min="0" step="any" name="pulso" value="{{ $fieldValue('pulso', $agenda->pulso) }}"><span class="vt-field__hint">Latidos por minuto</span></div>
                    <div class="vt-field{{ $errors->has('o2') ? ' has-error' : '' }}"><label for="o2">Saturación de oxígeno</label><input id="o2" class="form-control" type="number" min="0" max="100" step="any" name="o2" value="{{ $fieldValue('o2', $agenda->o2) }}"><span class="vt-field__hint">Porcentaje SpO₂</span></div>
                    <div class="vt-field{{ $errors->has('temperatura') ? ' has-error' : '' }}"><label for="temperatura">Temperatura (°C)</label><input id="temperatura" class="form-control" type="number" min="0" step="0.1" name="temperatura" value="{{ $fieldValue('temperatura', $agenda->temperatura) }}"></div>
                    <div class="vt-field{{ $errors->has('fr') ? ' has-error' : '' }}"><label for="fr">Frecuencia respiratoria</label><input id="fr" class="form-control" type="number" min="0" step="any" name="fr" value="{{ $fieldValue('fr', $agenda->frecuencia_respiratoria) }}"><span class="vt-field__hint">Respiraciones por minuto</span></div>
                    <div class="vt-field{{ $errors->has('glicemia_capilar') ? ' has-error' : '' }}"><label for="glicemia_capilar">Glicemia capilar</label><input id="glicemia_capilar" class="form-control" type="number" min="0" step="any" name="glicemia_capilar" value="{{ $fieldValue('glicemia_capilar', $agenda->glicemia_capilar) }}"><span class="vt-field__hint">mg/dL</span></div>
                </div>
            </div>

            <div class="vt-section">
                <h3 class="vt-section__title"><i class="fas fa-ruler-combined" aria-hidden="true"></i> Antropometría</h3>
                <div class="vt-grid">
                    <div class="vt-field{{ $errors->has('estatura') ? ' has-error' : '' }}"><label for="estatura">Estatura (cm)</label><input id="estatura" class="form-control" type="number" min="0" step="any" name="estatura" value="{{ $fieldValue('estatura', $agenda->altura) }}"></div>
                    <div class="vt-field{{ $errors->has('peso') ? ' has-error' : '' }}"><label for="peso">Peso (kg)</label><input id="peso" class="form-control" type="number" min="0" step="any" name="peso" value="{{ $fieldValue('peso', $agenda->peso) }}"></div>
                    <div class="vt-field"><label for="peso_ideal">Peso ideal (kg)</label><input id="peso_ideal" class="form-control" type="text" readonly aria-readonly="true" value="—"></div>
                    <div class="vt-field"><label for="gct">% GCT recomendado</label><input id="gct" class="form-control" type="text" readonly aria-readonly="true" value="—"></div>
                    <div class="vt-field"><label for="imc">IMC</label><input id="imc" class="form-control" type="text" readonly aria-readonly="true" value="—"></div>
                    <div class="vt-field"><label for="cimc">Categoría IMC</label><input id="cimc" class="form-control" type="text" readonly aria-readonly="true" value="Sin calcular"></div>
                </div>
            </div>

            <div class="vt-section">
                <h3 class="vt-section__title"><i class="fas fa-brain" aria-hidden="true"></i> Evaluación neurológica y perfusión</h3>
                <div class="vt-grid">
                    <div class="vt-field{{ $errors->has('glasgow') ? ' has-error' : '' }}"><label for="glasgow">Escala de Glasgow</label><input id="glasgow" class="form-control" type="number" min="0" step="any" name="glasgow" value="{{ $fieldValue('glasgow', $agenda->glasgow) }}"></div>
                    <div class="vt-field{{ $errors->has('ocular') ? ' has-error' : '' }}"><label for="ocular">Respuesta ocular</label><input id="ocular" class="form-control" type="number" min="0" step="any" name="ocular" value="{{ $fieldValue('ocular', $agenda->ocular) }}"></div>
                    <div class="vt-field{{ $errors->has('verbal') ? ' has-error' : '' }}"><label for="verbal">Respuesta verbal</label><input id="verbal" class="form-control" type="number" min="0" step="any" name="verbal" value="{{ $fieldValue('verbal', $agenda->verbal) }}"></div>
                    <div class="vt-field{{ $errors->has('motora') ? ' has-error' : '' }}"><label for="motora">Respuesta motora</label><input id="motora" class="form-control" type="number" min="0" step="any" name="motora" value="{{ $fieldValue('motora', $agenda->motora) }}"></div>
                    <div class="vt-field{{ $errors->has('reaccion_pupila_izq') ? ' has-error' : '' }}"><label for="reaccion_pupila_izq">Reacción pupilar izquierda</label><input id="reaccion_pupila_izq" class="form-control" type="number" min="0" step="any" name="reaccion_pupila_izq" value="{{ $fieldValue('reaccion_pupila_izq', $agenda->reaccion_pupila_izq) }}"></div>
                    <div class="vt-field{{ $errors->has('reaccion_pupila_der') ? ' has-error' : '' }}"><label for="reaccion_pupila_der">Reacción pupilar derecha</label><input id="reaccion_pupila_der" class="form-control" type="number" min="0" step="any" name="reaccion_pupila_der" value="{{ $fieldValue('reaccion_pupila_der', $agenda->reaccion_pupila_der) }}"></div>
                    <div class="vt-field{{ $errors->has('t_llenado_capilar') ? ' has-error' : '' }}"><label for="t_llenado_capilar">Tiempo de llenado capilar</label><input id="t_llenado_capilar" class="form-control" type="number" min="0" step="any" name="t_llenado_capilar" value="{{ $fieldValue('t_llenado_capilar', $agenda->t_llenado_capilar) }}"><span class="vt-field__hint">Segundos</span></div>
                </div>
            </div>

            <div class="vt-alert" id="vt-measurement-alert" role="status"><i class="fas fa-info-circle" aria-hidden="true"></i><span>Ingrese una estatura y un peso mayores a cero para calcular los indicadores antropométricos.</span></div>
            <div class="vt-actions">
                <a class="btn btn-outline-secondary" href="{{ route('v3.clinical-operations.nursing') }}">Cancelar</a>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1" aria-hidden="true"></i> Guardar valoración</button>
            </div>
        </form>
    </section>
</div>

<script>
    (function () {
        'use strict';

        var weight = document.getElementById('peso');
        var height = document.getElementById('estatura');
        var idealWeight = document.getElementById('peso_ideal');
        var bodyFat = document.getElementById('gct');
        var bmi = document.getElementById('imc');
        var bmiCategory = document.getElementById('cimc');
        var alert = document.getElementById('vt-measurement-alert');
        var age = @json($age);
        var sex = {{ (int) $agenda->sexo === 1 ? 1 : 0 }};

        function category(value) {
            if (value < 16) return 'Desnutrición';
            if (value < 18) return 'Bajo peso';
            if (value < 25) return 'Normal';
            if (value < 27) return 'Sobrepeso';
            if (value < 30) return 'Obesidad tipo I';
            if (value < 40) return 'Obesidad clínica';
            return 'Obesidad mórbida';
        }

        function clearCalculatedValues(showAlert) {
            idealWeight.value = '—';
            bodyFat.value = '—';
            bmi.value = '—';
            bmiCategory.value = 'Sin calcular';
            alert.classList.toggle('is-visible', showAlert);
        }

        function calculateMeasurements(showAlert) {
            var kilograms = parseFloat(weight.value);
            var centimeters = parseFloat(height.value);
            if (!Number.isFinite(kilograms) || !Number.isFinite(centimeters) || kilograms <= 0 || centimeters <= 0) {
                clearCalculatedValues(showAlert);
                return;
            }

            var metersSquared = Math.pow(centimeters / 100, 2);
            var bmiValue = kilograms / metersSquared;
            idealWeight.value = (21.45 * metersSquared).toFixed(2);
            bmi.value = bmiValue.toFixed(2);
            bmiCategory.value = category(bmiValue);

            if (typeof age === 'number' && Number.isFinite(age)) {
                bodyFat.value = ((1.2 * bmiValue) + (0.23 * age) - (10.8 * sex) - 5.4).toFixed(2);
            } else {
                bodyFat.value = '—';
            }
            alert.classList.remove('is-visible');
        }

        weight.addEventListener('input', function () { calculateMeasurements(true); });
        height.addEventListener('input', function () { calculateMeasurements(true); });
        calculateMeasurements(false);
    })();
</script>
