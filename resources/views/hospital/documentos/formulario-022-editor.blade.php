@php
    $raw022 = [];
    foreach (['alergias', 'medicamentos'] as $key022) {
        $raw022[$key022] = old('data.'.$key022, $form022Payload[$key022] ?? ($entry ? ($entry->payload[$key022] ?? '') : ''));
    }
    $document022 = \Sis_medico\Services\HospitalEpisodeForm022::forDisplay($raw022);
    $blankMedication022 = ['prescripcion' => '', 'medicamento' => '', 'dosis' => '', 'via' => '', 'frecuencia' => '', 'prescrita_por' => '', 'fecha_prescripcion' => '', 'administraciones' => [['fecha' => '', 'hora' => '', 'responsable' => '']]];
    $medicationRows022 = count($document022['medicamentos']['registros']) ? $document022['medicamentos']['registros'] : [$blankMedication022];
    $prescribedMedications022 = $prescribedMedications022 ?? [];
    $selectedPrescriptionKeys022 = array_values(array_filter(array_map(function ($record022) { return $record022['prescripcion'] ?? ''; }, $document022['medicamentos']['registros'])));
    $disabled022 = $canWrite ? '' : 'disabled';
@endphp

<div class="form022-sheet" id="form022-editor">
    @foreach(['alergias', 'medicamentos'] as $jsonSection022)
        <textarea hidden data-f022-json="{{ $jsonSection022 }}" name="data[{{ $jsonSection022 }}]" maxlength="60000" {{ $disabled022 }}>{{ json_encode($document022[$jsonSection022], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</textarea>
    @endforeach

    <div class="form022-band">A. DATOS DE ALERGIA A MEDICAMENTOS</div>
    <div class="form022-allergy">
        <label><input type="radio" data-allergy-state value="SI" {{ $document022['alergias']['estado'] === 'SI' ? 'checked' : '' }} {{ $disabled022 }}> Sí</label>
        <label><input type="radio" data-allergy-state value="NO" {{ $document022['alergias']['estado'] === 'NO' ? 'checked' : '' }} {{ $disabled022 }}> No</label>
        <div class="form-field" style="margin:0"><label>Describa la alergia</label><input type="text" data-allergy-description value="{{ $document022['alergias']['descripcion'] }}" maxlength="1000" {{ $disabled022 }}></div>
    </div>

    <div class="form022-band">B. ADMINISTRACIÓN DE MEDICAMENTOS PRESCRITOS</div>
    @if($canWrite)
        <div class="form022-medical-orders">
            <div class="form022-medical-orders__intro"><strong>Prescripciones médicas y administración eMAR</strong><span>Marque hasta seis medicamentos. Las dosis realmente administradas se sincronizan automáticamente con el 022 y no se pueden alterar desde este formulario.</span></div>
            @if($medicationAdministrationUrl ?? null)
                <div style="margin:10px 0"><a class="btn btn-primary" href="{{ $medicationAdministrationUrl }}" target="_blank" rel="noopener"><i class="fa fa-medkit"></i> Abrir mantenimiento de administración por horas</a></div>
            @endif
            @if(count($prescribedMedications022))
                <div class="form022-order-grid" role="group" aria-label="Prescripciones médicas disponibles">
                    @foreach($prescribedMedications022 as $prescription022)
                        <label class="form022-order-option">
                            <input type="checkbox" data-prescription-check value="{{ $prescription022['prescripcion'] }}" {{ in_array($prescription022['prescripcion'], $selectedPrescriptionKeys022, true) ? 'checked' : '' }} {{ count($prescription022['administraciones'] ?? []) ? 'disabled' : '' }}>
                            <span><strong>{{ $prescription022['medicamento'] }}</strong>@if($prescription022['dosis'] !== '') · {{ $prescription022['dosis'] }}@endif@if($prescription022['via'] !== '') · Vía {{ $prescription022['via'] }}@endif<br><small>{{ $prescription022['prescrita_por'] !== '' ? 'Prescribe: '.$prescription022['prescrita_por'] : 'Prescripción del episodio' }}{{ $prescription022['fecha_prescripcion'] !== '' ? ' · '.$prescription022['fecha_prescripcion'] : '' }}@if(count($prescription022['administraciones'] ?? [])) · {{ count($prescription022['administraciones']) }} dosis administrada(s) en eMAR @endif</small></span>
                        </label>
                    @endforeach
                </div>
                <script type="application/json" data-f022-prescriptions>{!! json_encode($prescribedMedications022, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
            @else
                <p class="form-muted" style="margin:0">Todavía no hay medicamentos prescritos por un médico en este episodio. Puede agregarlos manualmente si corresponde.</p>
            @endif
        </div>
    @endif
    <div class="form022-dates">
        @for($dateIndex022 = 0; $dateIndex022 < 4; $dateIndex022++)
            <div class="form-field" style="margin:0"><label>Fecha {{ $dateIndex022 + 1 }}</label><input type="date" data-med-date="{{ $dateIndex022 }}" value="{{ $document022['medicamentos']['fechas'][$dateIndex022] }}" {{ $disabled022 }}></div>
        @endfor
    </div>
    <p class="form-muted">El formato admite seis medicamentos y cuatro fechas. Registre cada administración con su hora y responsable.</p>

    <div data-medication-list>
        @foreach($medicationRows022 as $medication022)
            @php $administrations022 = count($medication022['administraciones'] ?? []) ? $medication022['administraciones'] : [['fecha' => '', 'hora' => '', 'responsable' => '']]; @endphp
            @php $prescriptionKey022 = $medication022['prescripcion'] ?? ''; @endphp
            @php $hasEmar022 = count(array_filter($administrations022, function ($administration022) { return !empty($administration022['emar_id']); })) > 0; @endphp
            <section class="form022-card" data-medication-card data-prescription-key="{{ $prescriptionKey022 }}" data-prescribed-by="{{ $medication022['prescrita_por'] ?? '' }}" data-prescribed-at="{{ $medication022['fecha_prescripcion'] ?? '' }}">
                <div class="form022-card-head"><div><strong>Medicamento <span data-med-number>{{ $loop->iteration }}</span></strong> <span class="form022-order-badge" data-prescription-badge {{ $prescriptionKey022 === '' ? 'hidden' : '' }}>{{ $hasEmar022 ? 'Sincronizado desde eMAR' : 'Prescripción médica' }}</span><small data-prescription-meta>@if($prescriptionKey022 !== ''){{ ($medication022['prescrita_por'] ?? '') !== '' ? 'Prescribe: '.$medication022['prescrita_por'] : 'Prescripción del episodio' }}{{ ($medication022['fecha_prescripcion'] ?? '') !== '' ? ' · '.$medication022['fecha_prescripcion'] : '' }}@endif</small></div>@if($canWrite && !$hasEmar022)<button class="form020-remove" type="button" data-remove-medication>Quitar medicamento</button>@endif</div>
                <div class="form022-prescription">
                    @foreach(['medicamento' => 'Medicamento prescrito', 'dosis' => 'Dosis', 'via' => 'Vía', 'frecuencia' => 'Frecuencia'] as $key022 => $label022)
                        <div class="form-field" style="margin:0"><label>{{ $label022 }}</label><input type="text" data-med-field="{{ $key022 }}" value="{{ $medication022[$key022] ?? '' }}" {{ $disabled022 }} {{ $canWrite && $prescriptionKey022 !== '' ? 'readonly' : '' }}></div>
                    @endforeach
                </div>
                <table class="form022-admin">
                    <thead><tr><th style="width:28%">Fecha</th><th style="width:24%">Hora</th><th>Responsable</th><th style="width:42px"></th></tr></thead>
                    <tbody data-admin-rows>
                    @foreach($administrations022 as $administration022)
                        <tr>
                            <td><select data-admin-field="fecha" {{ $disabled022 }} {{ !empty($administration022['emar_id']) ? 'disabled' : '' }}><option value="">Seleccione</option>@for($dateIndex022 = 0; $dateIndex022 < 4; $dateIndex022++)<option value="{{ $dateIndex022 }}" {{ (string) ($administration022['fecha'] ?? '') === (string) $dateIndex022 ? 'selected' : '' }}>Fecha {{ $dateIndex022 + 1 }}{{ $document022['medicamentos']['fechas'][$dateIndex022] ? ' · '.$document022['medicamentos']['fechas'][$dateIndex022] : '' }}</option>@endfor</select></td>
                            <td><input type="time" data-admin-field="hora" value="{{ $administration022['hora'] ?? '' }}" {{ $disabled022 }} {{ !empty($administration022['emar_id']) ? 'readonly' : '' }}></td>
                            <td><input type="text" data-admin-field="responsable" value="{{ $administration022['responsable'] ?? '' }}" {{ $disabled022 }} {{ !empty($administration022['emar_id']) ? 'readonly' : '' }}></td>
                            <td class="center">@if(empty($administration022['emar_id']))<button class="form020-remove" type="button" data-remove-administration title="Quitar administración" {{ $disabled022 }}>×</button>@else<span title="Administración eMAR #{{ $administration022['emar_id'] }}">✓</span>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if($canWrite && !$hasEmar022)<div class="form022-card-actions"><button class="btn" type="button" data-add-administration>Agregar administración</button></div>@endif
            </section>
        @endforeach
    </div>
    @if($canWrite)<div class="form020-toolbar" style="border-top:1px solid #b6d3df;margin-top:12px"><span>Máximo 6 medicamentos.</span><button class="btn" type="button" data-add-medication>Agregar medicamento</button></div>@endif

    @if($document022['alergias']['nota_legacy'] !== '')<div class="form020-legacy"><strong>Alergias · texto de un borrador anterior:</strong><br>{{ $document022['alergias']['nota_legacy'] }}</div>@endif
    @if($document022['medicamentos']['nota_legacy'] !== '')<div class="form020-legacy"><strong>Medicamentos · texto de un borrador anterior:</strong><br>{{ $document022['medicamentos']['nota_legacy'] }}</div>@endif
</div>

@if($canWrite)
<script>
(function () {
    var root = document.getElementById('form022-editor');
    if (!root) return;
    var list = root.querySelector('[data-medication-list]');
    var prescriptionData = root.querySelector('[data-f022-prescriptions]');
    var prescriptions = {};
    if (prescriptionData) {
        try {
            JSON.parse(prescriptionData.textContent || '[]').forEach(function (item) { prescriptions[item.prescripcion] = item; });
        } catch (error) { prescriptions = {}; }
    }
    function cards() { return Array.prototype.slice.call(list.querySelectorAll('[data-medication-card]')); }
    function rows(card) { return Array.prototype.slice.call(card.querySelectorAll('[data-admin-rows] tr')); }
    function valueMap(container, selector, attribute) {
        var values = {};
        container.querySelectorAll(selector).forEach(function (field) { values[field.dataset[attribute]] = field.value.trim(); });
        return values;
    }
    function nonEmpty(value) { return Object.keys(value).some(function (key) { return value[key] !== ''; }); }
    function readJson(section) {
        try { return JSON.parse(root.querySelector('[data-f022-json="' + section + '"]').value || '{}'); }
        catch (error) { return {}; }
    }
    function cardHasMedicationData(card) {
        return Array.prototype.slice.call(card.querySelectorAll('[data-med-field]')).some(function (field) { return field.value.trim() !== ''; });
    }
    function cardHasAdministrations(card) {
        return rows(card).some(function (row) { return nonEmpty(valueMap(row, '[data-admin-field]', 'adminField')); });
    }
    function prescriptionCheckbox(key) {
        return Array.prototype.slice.call(root.querySelectorAll('[data-prescription-check]')).find(function (field) { return field.value === key; });
    }
    function clearPrescription(card) {
        card.dataset.prescriptionKey = '';
        card.dataset.prescribedBy = '';
        card.dataset.prescribedAt = '';
        card.querySelectorAll('[data-med-field]').forEach(function (field) { field.readOnly = false; });
        var badge = card.querySelector('[data-prescription-badge]');
        if (badge) badge.hidden = true;
        var meta = card.querySelector('[data-prescription-meta]');
        if (meta) meta.textContent = '';
    }
    function resetCard(card) {
        card.querySelectorAll('input, select').forEach(function (field) { field.value = ''; field.checked = false; });
        var adminRows = card.querySelectorAll('[data-admin-rows] tr');
        for (var index = adminRows.length - 1; index > 0; index--) adminRows[index].remove();
        clearPrescription(card);
    }
    function blankCard() {
        var card = cards()[0].cloneNode(true);
        resetCard(card);
        return card;
    }
    function applyPrescription(card, prescription) {
        resetCard(card);
        card.dataset.prescriptionKey = prescription.prescripcion || '';
        card.dataset.prescribedBy = prescription.prescrita_por || '';
        card.dataset.prescribedAt = prescription.fecha_prescripcion || '';
        ['medicamento', 'dosis', 'via', 'frecuencia'].forEach(function (key) {
            var field = card.querySelector('[data-med-field="' + key + '"]');
            field.value = prescription[key] || '';
            field.readOnly = true;
        });
        var badge = card.querySelector('[data-prescription-badge]');
        if (badge) badge.hidden = false;
        var meta = card.querySelector('[data-prescription-meta]');
        if (meta) meta.textContent = (prescription.prescrita_por ? 'Prescribe: ' + prescription.prescrita_por : 'Prescripción del episodio')
            + (prescription.fecha_prescripcion ? ' · ' + prescription.fecha_prescripcion : '');
    }
    function linkedCard(key) {
        return cards().find(function (card) { return card.dataset.prescriptionKey === key; });
    }
    function addPrescription(key) {
        if (linkedCard(key) || !prescriptions[key]) return true;
        var current = cards();
        var card = current.length === 1 && !cardHasMedicationData(current[0]) && !cardHasAdministrations(current[0])
            ? current[0]
            : null;
        if (!card && current.length >= 6) {
            alert('El formulario 022 admite hasta seis medicamentos. Quite uno antes de seleccionar otra prescripción.');
            return false;
        }
        if (!card) {
            card = blankCard();
            list.appendChild(card);
        }
        applyPrescription(card, prescriptions[key]);
        renumber(); refreshDateLabels(); writeJson();
        return true;
    }
    function removeCard(card, confirmAdministration) {
        if (confirmAdministration && cardHasAdministrations(card)
            && !window.confirm('Este medicamento ya tiene administraciones registradas. ¿Desea quitarlo del formulario 022?')) return false;
        var key = card.dataset.prescriptionKey || '';
        if (cards().length === 1) resetCard(card);
        else card.remove();
        if (key) {
            var checkbox = prescriptionCheckbox(key);
            if (checkbox) checkbox.checked = false;
        }
        renumber(); writeJson();
        return true;
    }
    function renumber() { cards().forEach(function (card, index) { card.querySelector('[data-med-number]').textContent = String(index + 1); }); }
    function refreshDateLabels() {
        var dates = Array.prototype.slice.call(root.querySelectorAll('[data-med-date]')).map(function (field) { return field.value; });
        root.querySelectorAll('[data-admin-field="fecha"]').forEach(function (select) {
            Array.prototype.slice.call(select.options).forEach(function (option, index) {
                if (index === 0) return;
                option.textContent = 'Fecha ' + index + (dates[index - 1] ? ' · ' + dates[index - 1] : '');
            });
        });
    }
    function writeJson() {
        var allergy = readJson('alergias');
        var selected = root.querySelector('[data-allergy-state]:checked');
        allergy.estado = selected ? selected.value : '';
        allergy.descripcion = root.querySelector('[data-allergy-description]').value.trim();
        root.querySelector('[data-f022-json="alergias"]').value = JSON.stringify(allergy);

        var medications = readJson('medicamentos');
        medications.fechas = Array.prototype.slice.call(root.querySelectorAll('[data-med-date]')).map(function (field) { return field.value.trim(); });
        medications.registros = cards().map(function (card) {
            var record = valueMap(card, '[data-med-field]', 'medField');
            record.prescripcion = card.dataset.prescriptionKey || '';
            record.prescrita_por = card.dataset.prescribedBy || '';
            record.fecha_prescripcion = card.dataset.prescribedAt || '';
            record.administraciones = rows(card).map(function (row) { return valueMap(row, '[data-admin-field]', 'adminField'); }).filter(nonEmpty);
            return record;
        }).filter(function (record) { return nonEmpty({medicamento: record.medicamento, dosis: record.dosis, via: record.via, frecuencia: record.frecuencia}) || record.administraciones.length; });
        root.querySelector('[data-f022-json="medicamentos"]').value = JSON.stringify(medications);
    }
    function capacityIsValid() {
        var valid = true;
        cards().forEach(function (card) {
            var counts = [0, 0, 0, 0];
            rows(card).forEach(function (row) {
                var selected = row.querySelector('[data-admin-field="fecha"]').value;
                if (selected !== '') counts[Number(selected)]++;
            });
            if (counts.some(function (count) { return count > 12; })) valid = false;
        });
        return valid;
    }
    root.addEventListener('click', function (event) {
        if (event.target.hasAttribute('data-add-administration')) {
            var card = event.target.closest('[data-medication-card]');
            if (rows(card).length >= 48) return;
            var row = rows(card)[0].cloneNode(true);
            row.querySelectorAll('input, select').forEach(function (field) { field.value = ''; });
            card.querySelector('[data-admin-rows]').appendChild(row);
            row.querySelector('select').focus(); refreshDateLabels(); writeJson();
        }
        if (event.target.hasAttribute('data-remove-administration')) {
            var targetRow = event.target.closest('tr');
            var body = targetRow.parentNode;
            if (body.rows.length === 1) targetRow.querySelectorAll('input, select').forEach(function (field) { field.value = ''; });
            else targetRow.remove();
            writeJson();
        }
        if (event.target.hasAttribute('data-add-medication')) {
            var current = cards();
            if (current.length >= 6) {
                alert('El formulario 022 admite hasta seis medicamentos.');
                return;
            }
            var card = blankCard();
            list.appendChild(card); renumber(); refreshDateLabels(); writeJson();
            card.querySelector('[data-med-field="medicamento"]').focus();
        }
        if (event.target.hasAttribute('data-remove-medication')) {
            var targetCard = event.target.closest('[data-medication-card]');
            removeCard(targetCard, true);
        }
    });
    root.addEventListener('change', function (event) {
        if (!event.target.hasAttribute('data-prescription-check')) return;
        var key = event.target.value;
        if (event.target.checked) {
            if (!addPrescription(key)) event.target.checked = false;
            return;
        }
        var card = linkedCard(key);
        if (card && !removeCard(card, true)) event.target.checked = true;
    });
    root.addEventListener('input', function () { refreshDateLabels(); writeJson(); });
    root.closest('form').addEventListener('submit', function (event) {
        writeJson();
        if (!capacityIsValid()) {
            event.preventDefault();
            alert('Cada medicamento admite hasta 12 administraciones por fecha, según el formato 022.');
        }
    });
    renumber(); refreshDateLabels(); writeJson();
})();
</script>
@endif
