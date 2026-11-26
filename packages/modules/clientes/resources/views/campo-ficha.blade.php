<div class="{{ $ancho ?? 'col-md-6' }}">
    <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
    <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo ?? 'text' }}" class="form-control @error($campo) is-invalid @enderror"
        maxlength="{{ $limite }}" value="{{ old($campo, $valor) }}" @required($obligatorio ?? false)>
    @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>