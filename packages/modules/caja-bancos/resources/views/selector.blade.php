<label for="cb-{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
<select id="cb-{{ $campo }}" name="{{ $campo }}" class="form-select" required data-select2-url="{{ $selectorUrl }}" data-select2-min="0">
    <option value="">Seleccionar</option>
</select>
