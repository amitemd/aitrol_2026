<div class="{{ $clase ?? 'col-12' }}" data-ubicacion data-url="{{ route('configuracion.ubicaciones.opciones') }}" data-ciudad="{{ $idCiudad ?? '' }}">
    <div class="row g-3">
        @foreach (['pais' => 'País', 'provincia' => 'Provincia o estado', 'ciudad' => 'Ciudad'] as $campo => $etiqueta)
            <div class="col-md-4">
                <label for="{{ $campo }}-ubicacion" class="form-label">{{ __($etiqueta) }}</label>
                <select id="{{ $campo }}-ubicacion" class="form-select" data-ubicacion-{{ $campo }} @if ($campo === 'ciudad') name="id_ciudad" @endif disabled>
                    <option value="">{{ __('Seleccionar') }}</option>
                </select>
            </div>
        @endforeach
    </div>
    <p class="form-text mb-0" data-ubicacion-estado role="status"></p>
    @error('id_ciudad') <div class="text-danger small">{{ $message }}</div> @enderror
</div>