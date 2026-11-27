<form class="card shadow-sm" method="POST" data-ajax-form data-usuario-form action="{{ route('configuracion.empresas.update', $seleccionada) }}">
        @csrf
    @method('PUT')
        <div class="card-header"><h2 class="card-title mb-0">Editar empresa</h2></div>
        <div class="card-body">
            @include('partials.pestanas-ficha', ['pestanas' => ['generales' => ['General', 'bi-buildings'], 'ubicacion' => ['Ubicación', 'bi-geo-alt']], 'contexto' => 'empresa'])
            <section id="empresa-generales" role="tabpanel" aria-labelledby="empresa-tab-generales" class="row g-3" data-usuario-panel="generales">
            @foreach ([
                'razon_social' => ['Razón social', 'text', 160, true],
                'ruc' => ['RUC', 'text', 13, true],
                'nombre_comercial' => ['Nombre comercial', 'text', 160, false],
                'email' => ['Correo electrónico', 'email', 254, false],
                'telefono' => ['Teléfono', 'tel', 30, false],
            ] as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
                <div @class(['col-12', 'col-md-6' => in_array($campo, ['email', 'telefono'], true)])>
                    <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
                    <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" maxlength="{{ $limite }}"
                        class="form-control @error($campo) is-invalid @enderror" value="{{ old($campo, $seleccionada?->{$campo}) }}"
                        @required($obligatorio) @if ($campo === 'ruc') inputmode="numeric" pattern="[0-9]{13}" @endif>
                    @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach
            </section>
            <section id="empresa-ubicacion" role="tabpanel" aria-labelledby="empresa-tab-ubicacion" class="row g-3" data-usuario-panel="ubicacion">
                <div class="col-12">
                    <label for="direccion" class="form-label fw-semibold">Dirección</label>
                    <input id="direccion" name="direccion" maxlength="255" autocomplete="street-address"
                        class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $seleccionada->direccion) }}">
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </section>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('configuracion.empresas.index') }}" data-ajax-close>Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>Guardar cambios</button>
        </div>
    </form>
