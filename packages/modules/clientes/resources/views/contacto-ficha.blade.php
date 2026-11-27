<div class="ficha-contacto p-3 mb-2 rounded border" data-ficha-contacto data-indice="{{ $indice }}">
    <div class="row g-2 align-items-end">
        @foreach ([
            'nombre' => ['Nombre', 'text', 120],
            'cargo' => ['Cargo', 'text', 80],
            'email' => ['Correo', 'email', 254],
            'telefono' => ['Teléfono', 'tel', 30],
        ] as $clave => [$etiqueta, $tipo, $limite])
            <div class="col-sm">
                <label class="form-label" for="contacto-{{ $indice }}-{{ $clave }}">{{ __($etiqueta) }}</label>
                <input class="form-control" id="contacto-{{ $indice }}-{{ $clave }}"
                    name="contactos[{{ $indice }}][{{ $clave }}]" type="{{ $tipo }}" maxlength="{{ $limite }}" value="{{ $contacto[$clave] ?? '' }}">
            </div>
        @endforeach
        <div class="col-sm-auto">
            <button type="button" class="btn btn-outline-danger" data-ficha-contacto-quitar aria-label="Quitar contacto"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </div>
    </div>
</div>