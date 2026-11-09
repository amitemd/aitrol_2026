@php
    $raw020 = [];
    foreach (['fecha_hora', 'constantes', 'ingresos', 'egresos', 'balance'] as $key) {
        $raw020[$key] = old('data.'.$key, $entry ? ($entry->payload[$key] ?? '') : '');
    }
    $document020 = \Sis_medico\Services\HospitalEpisodeForm020::forDisplay($raw020);
    $vitalRows020 = count($document020['constantes']['registros']) ? $document020['constantes']['registros'] : [array_fill_keys(['fecha', 'dia_internacion', 'dia_postquirurgico', 'turno', 'hora', 'pulso', 'temperatura', 'frecuencia_respiratoria', 'saturacion', 'presion_sistolica', 'presion_diastolica', 'responsable'], '')];
    $intakeRows020 = count($document020['ingresos']['registros']) ? $document020['ingresos']['registros'] : [array_fill_keys(['fecha', 'hora', 'turno', 'categoria', 'tipo', 'cantidad', 'responsable'], '')];
    $outputRows020 = count($document020['egresos']['registros']) ? $document020['egresos']['registros'] : [array_fill_keys(['fecha', 'hora', 'turno', 'categoria', 'tipo', 'cantidad', 'responsable'], '')];
    $disabled020 = $canWrite ? '' : 'disabled';
@endphp

<div class="form020-sheet" id="form020-editor">
    <p class="form-muted">Las cantidades del balance se registran en mililitros (ml). Cada fila conserva fecha, turno, hora y responsable.</p>
    <div class="form020-grid" style="border-top:1px solid #b6d3df">
        <div class="form-field span-2">
            <label for="field_fecha_hora">Fecha y hora de inicio del registro</label>
            <input id="field_fecha_hora" type="datetime-local" name="data[fecha_hora]" value="{{ $raw020['fecha_hora'] }}" {{ $disabled020 }}>
        </div>
    </div>

    @foreach(['constantes', 'ingresos', 'egresos', 'balance'] as $jsonSection020)
        <textarea hidden data-f020-json="{{ $jsonSection020 }}" name="data[{{ $jsonSection020 }}]" maxlength="60000" {{ $disabled020 }}>{{ json_encode($document020[$jsonSection020], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</textarea>
    @endforeach

    <div class="form020-band">B. CONSTANTES VITALES</div>
    <div class="form020-table-wrap">
        <table class="form020-table" data-f020-rows="constantes">
            <thead><tr><th>Fecha</th><th>Día<br>internación</th><th>Día post<br>quirúrgico</th><th>Turno</th><th>Hora</th><th>Pulso<br>lpm</th><th>Temp.<br>°C</th><th>F. resp.<br>rpm</th><th>SpO₂<br>%</th><th>PA sist.<br>mmHg</th><th>PA diast.<br>mmHg</th><th>Responsable</th><th></th></tr></thead>
            <tbody>
            @foreach($vitalRows020 as $row020)
                <tr>
                    @foreach(['fecha' => 'date', 'dia_internacion' => 'text', 'dia_postquirurgico' => 'text'] as $key020 => $type020)<td><input type="{{ $type020 }}" data-row-field="{{ $key020 }}" value="{{ $row020[$key020] ?? '' }}" {{ $disabled020 }}></td>@endforeach
                    <td><select data-row-field="turno" {{ $disabled020 }}><option value=""></option>@foreach(['AM', 'PM', 'HS'] as $turno020)<option value="{{ $turno020 }}" {{ ($row020['turno'] ?? '') === $turno020 ? 'selected' : '' }}>{{ $turno020 }}</option>@endforeach</select></td>
                    <td><input type="time" data-row-field="hora" value="{{ $row020['hora'] ?? '' }}" {{ $disabled020 }}></td>
                    @foreach(['pulso', 'temperatura', 'frecuencia_respiratoria', 'saturacion', 'presion_sistolica', 'presion_diastolica', 'responsable'] as $key020)<td><input type="text" data-row-field="{{ $key020 }}" value="{{ $row020[$key020] ?? '' }}" {{ $disabled020 }}></td>@endforeach
                    <td><button class="form020-remove" type="button" title="Quitar fila" {{ $disabled020 }}>×</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="form020-toolbar"><span>Hasta 21 controles por registro.</span>@if($canWrite)<button class="btn" type="button" data-add-row="constantes">Agregar control</button>@endif</div>

    <div class="form020-band alt">C. MEDIDAS ANTROPOMÉTRICAS</div>
    <div class="form020-grid">
        @foreach(['peso' => 'Peso (kg)', 'talla' => 'Talla (cm)', 'perimetro_cefalico' => 'Perímetro cefálico (cm)', 'perimetro_abdominal' => 'Perímetro abdominal (cm)', 'otro' => 'Otra medida'] as $key020 => $label020)
            <div class="form-field"><label>{{ $label020 }}</label><input type="text" data-f020-section="constantes" data-f020-group="antropometria" data-f020-field="{{ $key020 }}" value="{{ $document020['constantes']['antropometria'][$key020] }}" {{ $disabled020 }}></div>
        @endforeach
    </div>

    <div class="form020-band">D. INGESTA - ELIMINACIÓN / BALANCE HÍDRICO</div>
    @foreach([
        'ingresos' => ['title' => 'INGRESOS EN ML', 'categories' => ['Enteral', 'Parenteral', 'Vía oral', 'Otros']],
        'egresos' => ['title' => 'ELIMINACIONES EN ML', 'categories' => ['Orina', 'Drenaje', 'Vómito', 'Diarrea', 'Heces', 'Otros']],
    ] as $fluidSection020 => $fluidConfig020)
        @php $fluidRows020 = $fluidSection020 === 'ingresos' ? $intakeRows020 : $outputRows020; @endphp
        <div class="form020-table-wrap" style="border-top:0">
            <table class="form020-table" data-f020-rows="{{ $fluidSection020 }}">
                <thead><tr><th colspan="8" style="background:#e8f7f8;text-align:left">{{ $fluidConfig020['title'] }}</th></tr><tr><th>Fecha</th><th>Hora</th><th>Turno</th><th>Categoría</th><th>Tipo / detalle</th><th>Cantidad<br>ml</th><th>Responsable</th><th></th></tr></thead>
                <tbody>
                @foreach($fluidRows020 as $row020)
                    <tr>
                        <td><input type="date" data-row-field="fecha" value="{{ $row020['fecha'] ?? '' }}" {{ $disabled020 }}></td>
                        <td><input type="time" data-row-field="hora" value="{{ $row020['hora'] ?? '' }}" {{ $disabled020 }}></td>
                        <td><select data-row-field="turno" {{ $disabled020 }}><option value=""></option>@foreach(['AM', 'PM', 'HS'] as $turno020)<option value="{{ $turno020 }}" {{ ($row020['turno'] ?? '') === $turno020 ? 'selected' : '' }}>{{ $turno020 }}</option>@endforeach</select></td>
                        <td><select data-row-field="categoria" {{ $disabled020 }}><option value=""></option>@foreach($fluidConfig020['categories'] as $category020)<option value="{{ $category020 }}" {{ ($row020['categoria'] ?? '') === $category020 ? 'selected' : '' }}>{{ $category020 }}</option>@endforeach</select></td>
                        <td><input type="text" data-row-field="tipo" value="{{ $row020['tipo'] ?? '' }}" {{ $disabled020 }}></td>
                        <td><input type="number" min="0" step="0.01" inputmode="decimal" data-row-field="cantidad" value="{{ $row020['cantidad'] ?? '' }}" {{ $disabled020 }}></td>
                        <td><input type="text" data-row-field="responsable" value="{{ $row020['responsable'] ?? '' }}" {{ $disabled020 }}></td>
                        <td><button class="form020-remove" type="button" title="Quitar fila" {{ $disabled020 }}>×</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="form020-toolbar"><span class="form020-total">Total {{ mb_strtolower($fluidConfig020['title'], 'UTF-8') }}: <strong data-total="{{ $fluidSection020 }}">0.00</strong> ml</span>@if($canWrite)<button class="btn" type="button" data-add-row="{{ $fluidSection020 }}">Agregar fila</button>@endif</div>
    @endforeach
    <div class="form020-toolbar" style="border-top:1px solid #b6d3df"><span class="form020-total">Balance calculado: <strong data-balance-total>0.00</strong> ml</span><span>Ingresos − eliminaciones</span></div>

    <div class="form020-grid">
        @foreach([
            'primer_ingresos' => '1.er turno · ingresos', 'primer_egresos' => '1.er turno · egresos', 'primer_total' => '1.er turno · balance',
            'segundo_ingresos' => '2.º turno · ingresos', 'segundo_egresos' => '2.º turno · egresos', 'segundo_total' => '2.º turno · balance',
            'total_ingresos' => 'Total ingresos 24 h', 'total_egresos' => 'Total egresos 24 h', 'balance_24h' => 'Balance 24 h',
            'diuresis_horaria' => 'Diuresis horaria (ml/h)', 'gasto_urinario' => 'Gasto urinario (ml/kg/h)', 'gasto_fecal' => 'Gasto fecal', 'perdidas_insensibles' => 'Pérdidas insensibles',
        ] as $key020 => $label020)
            <div class="form-field"><label>{{ $label020 }}</label><input type="text" data-f020-section="balance" data-f020-group="resumen" data-f020-field="{{ $key020 }}" value="{{ $document020['balance']['resumen'][$key020] }}" {{ $disabled020 }}></div>
        @endforeach
    </div>

    <div class="form020-grid">
        @foreach(['dieta' => 'Dieta prescrita', 'comidas' => 'Número de comidas', 'micciones' => 'Número de micciones', 'deposiciones' => 'Número de deposiciones'] as $key020 => $label020)
            <div class="form-field"><label>{{ $label020 }}</label><input type="text" data-f020-section="balance" data-f020-group="alimentacion" data-f020-field="{{ $key020 }}" value="{{ $document020['balance']['alimentacion'][$key020] }}" {{ $disabled020 }}></div>
        @endforeach
    </div>

    <div class="form020-band alt">E. CUIDADOS GENERALES</div>
    <div class="form020-grid">
        @foreach(['aseo' => 'Aseo', 'bano' => 'Baño', 'reposo' => 'Reposo · especifique', 'posicion' => 'Posición · especifique', 'otros' => 'Otros cuidados'] as $key020 => $label020)
            <div class="form-field"><label>{{ $label020 }}</label><input type="text" data-f020-section="balance" data-f020-group="cuidados" data-f020-field="{{ $key020 }}" value="{{ $document020['balance']['cuidados'][$key020] }}" {{ $disabled020 }}></div>
        @endforeach
    </div>

    <div class="form020-band">F. FECHA DE COLOCACIÓN DE DISPOSITIVOS MÉDICOS</div>
    <div class="form020-grid">
        @foreach(['via_central' => 'Vía central', 'via_periferica' => 'Vía periférica', 'sonda_nasogastrica' => 'Sonda nasogástrica', 'sonda_vesical' => 'Sonda vesical', 'otros' => 'Otros', 'responsable' => 'Responsable'] as $key020 => $label020)
            <div class="form-field"><label>{{ $label020 }}</label><input type="text" data-f020-section="balance" data-f020-group="dispositivos" data-f020-field="{{ $key020 }}" value="{{ $document020['balance']['dispositivos'][$key020] }}" {{ $disabled020 }}></div>
        @endforeach
        <div class="form-field span-4"><label>Observaciones</label><textarea data-f020-section="balance" data-f020-field="observaciones" {{ $disabled020 }}>{{ $document020['balance']['observaciones'] }}</textarea></div>
    </div>

    @foreach(['constantes' => 'Constantes vitales', 'ingresos' => 'Ingresos', 'egresos' => 'Egresos', 'balance' => 'Balance'] as $section020 => $label020)
        @if($document020[$section020]['nota_legacy'] !== '')<div class="form020-legacy"><strong>{{ $label020 }} · texto de un borrador anterior:</strong><br>{{ $document020[$section020]['nota_legacy'] }}</div>@endif
    @endforeach
</div>

@if($canWrite)
<script>
(function () {
    var root = document.getElementById('form020-editor');
    if (!root) return;
    var limits = {constantes: 21, ingresos: 30, egresos: 30};
    function rows(section) {
        var table = root.querySelector('[data-f020-rows="' + section + '"]');
        return table ? Array.prototype.slice.call(table.tBodies[0].rows) : [];
    }
    function rowValue(row) {
        var value = {};
        row.querySelectorAll('[data-row-field]').forEach(function (field) { value[field.dataset.rowField] = field.value.trim(); });
        return value;
    }
    function nonEmpty(value) { return Object.keys(value).some(function (key) { return value[key] !== ''; }); }
    function readJson(section) {
        var field = root.querySelector('[data-f020-json="' + section + '"]');
        try { return JSON.parse(field.value || '{}'); } catch (error) { return {}; }
    }
    function writeJson() {
        ['constantes', 'ingresos', 'egresos'].forEach(function (section) {
            var data = readJson(section);
            data.registros = rows(section).map(rowValue).filter(nonEmpty);
            root.querySelector('[data-f020-json="' + section + '"]').value = JSON.stringify(data);
        });
        root.querySelectorAll('[data-f020-section][data-f020-field]').forEach(function (field) {
            var section = field.dataset.f020Section;
            var data = readJson(section);
            var group = field.dataset.f020Group;
            if (group) { data[group] = data[group] || {}; data[group][field.dataset.f020Field] = field.value.trim(); }
            else data[field.dataset.f020Field] = field.value.trim();
            root.querySelector('[data-f020-json="' + section + '"]').value = JSON.stringify(data);
        });
    }
    function updateTotals() {
        var totals = {};
        ['ingresos', 'egresos'].forEach(function (section) {
            totals[section] = rows(section).reduce(function (sum, row) {
                var field = row.querySelector('[data-row-field="cantidad"]');
                var value = field ? parseFloat(String(field.value).replace(',', '.')) : 0;
                return sum + (isFinite(value) ? value : 0);
            }, 0);
            root.querySelector('[data-total="' + section + '"]').textContent = totals[section].toFixed(2);
        });
        root.querySelector('[data-balance-total]').textContent = (totals.ingresos - totals.egresos).toFixed(2);
    }
    root.querySelectorAll('[data-add-row]').forEach(function (button) {
        button.addEventListener('click', function () {
            var section = button.dataset.addRow;
            var current = rows(section);
            if (current.length >= limits[section]) return;
            var row = current[0].cloneNode(true);
            row.querySelectorAll('input, select').forEach(function (field) { field.value = ''; });
            row.querySelector('.form020-remove').disabled = false;
            current[0].parentNode.appendChild(row);
            row.querySelector('input, select').focus();
            writeJson(); updateTotals();
        });
    });
    root.addEventListener('click', function (event) {
        if (!event.target.classList.contains('form020-remove')) return;
        var row = event.target.closest('tr');
        var body = row.parentNode;
        if (body.rows.length === 1) row.querySelectorAll('input, select').forEach(function (field) { field.value = ''; });
        else row.remove();
        writeJson(); updateTotals();
    });
    root.addEventListener('input', function () { writeJson(); updateTotals(); });
    root.closest('form').addEventListener('submit', writeJson);
    writeJson(); updateTotals();
})();
</script>
@endif
