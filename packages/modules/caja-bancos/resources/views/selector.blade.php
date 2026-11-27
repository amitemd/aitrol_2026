<label for="cb-{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
<select id="cb-{{ $campo }}" name="{{ $campo }}" class="form-select" required data-select2-url="{{ $selectorUrl }}" data-select2-min="0">
    <option value="">{{ __('Seleccionar') }}</option>
</select>
