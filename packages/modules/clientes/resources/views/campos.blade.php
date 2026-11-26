    <div class="col-md-4">
        <label for="tipo_identificacion" class="form-label">Tipo de identificación <span aria-hidden="true">*</span></label>
        <select class="form-select @error('tipo_identificacion') is-invalid @enderror" id="tipo_identificacion" name="tipo_identificacion" required>
            @foreach ($tipos as $tipo)
                <option value="{{ $tipo->value }}" @selected(old('tipo_identificacion', $cliente?->identificacion->tipo->value) === $tipo->value)>{{ $tipo->etiqueta() }}</option>
            @endforeach
        </select>
        @error('tipo_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="identificacion" class="form-label">Identificación <span aria-hidden="true">*</span></label>
        <input class="form-control @error('identificacion') is-invalid @enderror" id="identificacion" name="identificacion"
            maxlength="30" value="{{ old('identificacion', $cliente?->identificacion->numero) }}" @if (! $cliente) autofocus @endif required>
        @error('identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="activo" class="form-label">Estado <span aria-hidden="true">*</span></label>
        <select class="form-select @error('activo') is-invalid @enderror" id="activo" name="activo" required>
            <option value="1" @selected((bool) old('activo', $cliente?->activo ?? true))>Activo</option>
            <option value="0" @selected(!(bool) old('activo', $cliente?->activo ?? true))>Inactivo</option>
        </select>
        @error('activo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    @foreach ([
        'nombre' => ['Nombre o razón social', 'text', 160, $cliente?->nombre],
        'email' => ['Correo electrónico', 'email', 254, $cliente?->email],
        'telefono' => ['Teléfono', 'tel', 30, $cliente?->telefono],
        'direccion' => ['Dirección', 'text', 255, $cliente?->direccion],
    ] as $campo => [$etiqueta, $tipo, $longitud, $valor])
        <div @class(['col-12' => in_array($campo, ['nombre', 'direccion'], true), 'col-md-6' => in_array($campo, ['email', 'telefono'], true)])>
            <label for="{{ $campo }}" class="form-label">
                {{ $etiqueta }} @if ($campo === 'nombre') <span aria-hidden="true">*</span> @endif
            </label>
            <input class="form-control @error($campo) is-invalid @enderror" id="{{ $campo }}" name="{{ $campo }}"
                type="{{ $tipo }}" maxlength="{{ $longitud }}" value="{{ old($campo, $valor) }}" @if ($campo === 'nombre') required @endif>
            @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endforeach
