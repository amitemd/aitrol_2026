<label class="form-label" for="af-{{ $campo }}">{{ __($etiqueta) }}</label>
<select id="af-{{ $campo }}" class="form-select" name="{{ $campo }}" data-select2-url="{{ route($ruta.'.opciones', $origen) }}"
    data-select2-min="0" @if (!$requerido) data-select2-placeholder="{{ __('Seleccionar') }}" @endif @required($requerido)>
    <option value="">{{ __('Seleccionar') }}</option>
</select>
