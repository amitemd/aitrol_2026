<form class="card shadow-sm" method="POST" data-ajax-form action="{{ $seleccionada ? route('configuracion.empresas.update', $seleccionada) : route('configuracion.empresas.store') }}">
        @csrf
    @if ($seleccionada) @method('PUT') @endif
        <div class="card-header"><h2 class="card-title mb-0">{{ $seleccionada ? 'Editar empresa' : 'Crear empresa' }}</h2></div>
        <div class="card-body row g-3">
            @foreach ([
                'razon_social' => ['Razón social', 'text', 160, true],
                'ruc' => ['RUC', 'text', 13, true],
                'nombre_comercial' => ['Nombre comercial', 'text', 160, false],
                'email' => ['Correo electrónico', 'email', 254, false],
                'telefono' => ['Teléfono', 'tel', 30, false],
                'direccion' => ['Dirección', 'text', 255, false],
            ] as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
                <div @class(['col-12', 'col-md-6' => in_array($campo, ['email', 'telefono'], true)])>
                    <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
                    <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" maxlength="{{ $limite }}"
                        class="form-control @error($campo) is-invalid @enderror" value="{{ old($campo, $seleccionada?->{$campo}) }}"
                        @required($obligatorio) @if ($campo === 'ruc') inputmode="numeric" pattern="[0-9]{13}" @endif>
                    @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('configuracion.empresas.index') }}" data-ajax-close>Cancelar</a>
            <button type="submit" class="btn btn-primary">{{ $seleccionada ? 'Guardar cambios' : 'Registrar empresa' }}</button>
        </div>
    </form>