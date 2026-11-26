@php($perfil = $cliente?->perfil ?? [])
<nav class="nav nav-pills ficha-tabs gap-1 mb-3" aria-label="Secciones de la ficha">
    @foreach (['generales' => 'Datos generales', 'distribucion' => 'Distribución', 'contactos' => 'Contactos', 'documentos' => 'Documentos'] as $panel => $etiqueta)
        <button type="button" class="nav-link @if($loop->first) active @endif" data-ficha-tab="{{ $panel }}"
            aria-controls="ficha-{{ $panel }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $etiqueta }}</button>
    @endforeach
</nav>

<section id="ficha-generales" data-ficha-panel="generales" aria-label="Datos generales">
    <div class="ficha-identidad mb-3">
        <div class="ficha-foto" aria-hidden="true">
            <i class="bi bi-person fs-3"></i>
            <img data-ficha-foto @if ($tieneFoto ?? false) src="{{ route('clientes.foto', $cliente->id) }}" @else hidden @endif alt="">
        </div>
        <div class="flex-grow-1">
            <label for="foto" class="form-label fw-semibold">Foto del cliente</label>
            <input id="foto" name="foto" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG o WebP · hasta 3 MB</div>
            @error('foto') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="tipo_identificacion" class="form-label">Tipo de identificación</label>
            <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" required>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->value }}" @selected(old('tipo_identificacion', $cliente?->identificacion->tipo->value) === $tipo->value)>{{ $tipo->etiqueta() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="identificacion" class="form-label">Identificación</label>
            <input class="form-control" id="identificacion" name="identificacion" maxlength="30"
                value="{{ old('identificacion', $cliente?->identificacion->numero) }}" autofocus required>
        </div>
        <div class="col-md-4">
            <label for="activo" class="form-label">Estado</label>
            <select class="form-select" id="activo" name="activo" required>
                <option value="1" @selected((bool) old('activo', $cliente?->activo ?? true))>Activo</option>
                <option value="0" @selected(!(bool) old('activo', $cliente?->activo ?? true))>Inactivo</option>
            </select>
        </div>
        @include('clientes::campo-ficha', ['campo' => 'nombres', 'etiqueta' => 'Nombres o razón social', 'valor' => $perfil['nombres'] ?? old('nombre'), 'limite' => 160, 'obligatorio' => true])
        @include('clientes::campo-ficha', ['campo' => 'apellidos', 'etiqueta' => 'Apellidos', 'valor' => $perfil['apellidos'] ?? null, 'limite' => 100])
        <div class="col-md-6">
            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
            <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" class="form-control"
                value="{{ old('fecha_nacimiento', $perfil['fecha_nacimiento'] ?? null) }}">
        </div>
        <div class="col-12">
            <label for="observacion" class="form-label">Observación</label>
            <textarea id="observacion" name="observacion" class="form-control" rows="2" maxlength="3000">{{ old('observacion', $perfil['observacion'] ?? null) }}</textarea>
        </div>
    </div>
</section>

<section id="ficha-distribucion" data-ficha-panel="distribucion" aria-label="Distribución">
    <div class="row g-3">
        @include('clientes::campo-ficha', ['campo' => 'direccion', 'etiqueta' => 'Dirección', 'valor' => $cliente?->direccion, 'limite' => 255, 'ancho' => 'col-12'])
        @foreach (['provincia' => 'Provincia', 'ciudad' => 'Ciudad', 'zona' => 'Zona de distribución'] as $campo => $etiqueta)
            @include('clientes::campo-ficha', ['campo' => $campo, 'etiqueta' => $etiqueta, 'valor' => $perfil[$campo] ?? null, 'limite' => 100, 'ancho' => 'col-md-4'])
        @endforeach
        @foreach (['urbanizacion' => 'Urbanización', 'etapa' => 'Etapa', 'manzana' => 'Manzana', 'villa' => 'Villa'] as $campo => $etiqueta)
            @include('clientes::campo-ficha', ['campo' => $campo, 'etiqueta' => $etiqueta, 'valor' => $perfil[$campo] ?? null, 'limite' => 100, 'ancho' => 'col-md-3'])
        @endforeach
        <div class="col-12">
            <label for="referencia" class="form-label">Referencia de entrega</label>
            <textarea id="referencia" name="referencia" class="form-control" rows="2" maxlength="1000">{{ old('referencia', $perfil['referencia'] ?? null) }}</textarea>
        </div>
        @foreach (['latitud' => ['Latitud', 90], 'longitud' => ['Longitud', 180]] as $campo => [$etiqueta, $rango])
            <div class="col-md-6">
                <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                <input id="{{ $campo }}" class="form-control" name="{{ $campo }}" type="number" step="any"
                    min="{{ -$rango }}" max="{{ $rango }}" value="{{ old($campo, $perfil[$campo] ?? null) }}">
            </div>
        @endforeach
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
        <button type="button" class="btn btn-outline-primary btn-sm" data-ficha-mapa-abrir><i class="bi bi-geo-alt me-1"></i> Abrir mapa</button>
        <span class="small text-body-secondary" data-ficha-mapa-estado role="status">Selecciona un punto en el mapa o ingresa las coordenadas.</span>
    </div>
    <div class="ficha-mapa mt-3" data-ficha-mapa hidden aria-label="Mapa de ubicación del cliente"></div>
</section>

<section id="ficha-contactos" data-ficha-panel="contactos" aria-label="Contactos">
    <div class="row g-3">
        @include('clientes::campo-ficha', ['campo' => 'email', 'etiqueta' => 'Correo electrónico', 'valor' => $cliente?->email, 'limite' => 254, 'tipo' => 'email'])
        @include('clientes::campo-ficha', ['campo' => 'telefono', 'etiqueta' => 'Teléfono', 'valor' => $cliente?->telefono, 'limite' => 30, 'tipo' => 'tel'])
        @include('clientes::campo-ficha', ['campo' => 'celular', 'etiqueta' => 'Celular', 'valor' => $perfil['celular'] ?? null, 'limite' => 100, 'tipo' => 'tel'])
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h3 class="fs-6 fw-semibold mb-0">Contactos adicionales</h3>
        <button type="button" class="btn btn-sm btn-outline-primary" data-ficha-contacto-agregar>Agregar contacto</button>
    </div>
    <input type="hidden" name="contactos_presentes" value="1">
    <div data-ficha-contactos>
        @foreach (old('contactos', $perfil['contactos'] ?? []) ?? [] as $indice => $contacto)
            @include('clientes::contacto-ficha', ['indice' => $indice, 'contacto' => $contacto])
        @endforeach
    </div>
    <template data-ficha-contacto-template>
        @include('clientes::contacto-ficha', ['indice' => '__indice__', 'contacto' => []])
    </template>
</section>

<section id="ficha-documentos" data-ficha-panel="documentos" aria-label="Documentos">
    <label for="adjuntos" class="form-label">Adjuntar documentos</label>
    <input id="adjuntos" type="file" name="adjuntos[]" class="form-control" multiple accept="application/pdf,image/jpeg,image/png">
    <div class="form-text">PDF, JPG o PNG · hasta 5 archivos por carga, de 8 MB cada uno.</div>
    <h3 class="fs-6 fw-semibold mt-4">Archivos del cliente</h3>
    <ul class="list-group list-group-flush">
        @forelse ($documentos ?? [] as $documento)
            <li class="list-group-item px-0">
                <a href="{{ route('clientes.documento', [$cliente->id, $documento['id']]) }}">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>{{ $documento['nombre'] }}
                </a>
            </li>
        @empty
            <li class="list-group-item px-0 text-body-secondary">Todavía no hay archivos adjuntos.</li>
        @endforelse
    </ul>
</section>
