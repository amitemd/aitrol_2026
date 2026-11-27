@php
    $perfil = $cliente?->perfil ?? [];
    $modulo = $modulo ?? 'clientes';
    $esProveedor = $modulo === 'proveedores';
    $entidad = $esProveedor ? 'proveedor' : 'cliente';
    $panelUbicacion = $esProveedor ? 'ubicacion' : 'distribucion';
    $tituloUbicacion = $esProveedor ? 'Ubicación' : 'Distribución';
    $pestanas = [
        'generales' => ['Datos generales', 'bi-person-vcard'],
        $panelUbicacion => [$tituloUbicacion, $esProveedor ? 'bi-geo-alt' : 'bi-signpost-2'],
        'contactos' => ['Contactos', 'bi-person-lines-fill'],
        'documentos' => ['Documentos', 'bi-folder2-open'],
    ];
@endphp
@include('partials.pestanas-ficha', ['pestanas' => $pestanas, 'contexto' => 'ficha', 'navegacionFicha' => true])

<section id="ficha-generales" data-ficha-panel="generales" aria-label="Datos generales">
    @include('partials.ficha-panel-intro', [
        'tituloPanel' => 'Datos generales', 'descripcionPanel' => 'Registra la identificación, el nombre y la imagen de la ficha.',
    ])
    <div class="ficha-perfil">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="tipo_identificacion" class="form-label">Tipo de identificación</label>
                <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" required>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo->value }}" @selected(old('tipo_identificacion', $cliente?->identificacion->tipo->value) === $tipo->value)>{{ $tipo->etiqueta() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="identificacion" class="form-label">Identificación</label>
                <input class="form-control" id="identificacion" name="identificacion" maxlength="30"
                    value="{{ old('identificacion', $cliente?->identificacion->numero) }}" autofocus required>
            </div>
            @include('clientes::campo-ficha', ['campo' => 'nombres', 'etiqueta' => 'Nombres o razón social', 'valor' => $perfil['nombres'] ?? old('nombre'), 'limite' => 160, 'obligatorio' => true])
            @include('clientes::campo-ficha', ['campo' => 'apellidos', 'etiqueta' => 'Apellidos', 'valor' => $perfil['apellidos'] ?? null, 'limite' => 100])
            @unless ($esProveedor)
                <div class="col-md-6">
                    <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                    <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" class="form-control"
                        value="{{ old('fecha_nacimiento', $perfil['fecha_nacimiento'] ?? null) }}">
                </div>
            @endunless
            <div class="col-12">
                <label for="observacion" class="form-label">Observación</label>
                <textarea id="observacion" name="observacion" class="form-control" rows="2" maxlength="3000">{{ old('observacion', $perfil['observacion'] ?? null) }}</textarea>
            </div>
        </div>
        <aside class="ficha-perfil-foto">
            @include('partials.campo-adjunto', [
                'campo' => 'foto', 'nombre' => 'foto', 'titulo' => $esProveedor ? 'Foto o logotipo del proveedor' : 'Foto del cliente',
                'iconoAdjunto' => $esProveedor ? 'bi-truck' : 'bi-person', 'formatos' => 'image/jpeg,image/png,image/webp',
                'ayuda' => 'JPG, PNG o WebP · máximo 3 MB', 'accion' => ($tieneFoto ?? false) ? 'Cambiar fotografía' : 'Seleccionar imagen',
                'vistaPrevia' => true, 'urlImagen' => ($tieneFoto ?? false) ? route($modulo.'.foto', $cliente->id) : null,
            ])
        </aside>
    </div>
</section>

<section id="ficha-{{ $panelUbicacion }}" data-ficha-panel="{{ $panelUbicacion }}" aria-label="{{ $tituloUbicacion }}">
    @include('partials.ficha-panel-intro', [
        'tituloPanel' => $tituloUbicacion,
        'descripcionPanel' => $esProveedor ? 'Registra la dirección y la ubicación del proveedor.' : 'Define la dirección, la zona y la ubicación en el mapa.',
    ])
    <div class="row g-3">
        @include('clientes::campo-ficha', ['campo' => 'direccion', 'etiqueta' => 'Dirección', 'valor' => $cliente?->direccion, 'limite' => 255, 'ancho' => 'col-12'])
        @include('partials.ubicacion-selects', ['idCiudad' => old('id_ciudad', $perfil['id_ciudad'] ?? null)])
        @unless ($esProveedor)
            @include('clientes::campo-ficha', ['campo' => 'zona', 'etiqueta' => 'Zona de distribución', 'valor' => $perfil['zona'] ?? null, 'limite' => 100, 'ancho' => 'col-md-4'])
        @endunless
        @foreach (['urbanizacion' => 'Urbanización', 'etapa' => 'Etapa', 'manzana' => 'Manzana', 'villa' => 'Villa'] as $campo => $etiqueta)
            @include('clientes::campo-ficha', ['campo' => $campo, 'etiqueta' => $etiqueta, 'valor' => $perfil[$campo] ?? null, 'limite' => 100, 'ancho' => 'col-md-3'])
        @endforeach
        <div class="col-12">
            <label for="referencia" class="form-label">{{ $esProveedor ? 'Referencia de la dirección' : 'Referencia de entrega' }}</label>
            <textarea id="referencia" name="referencia" class="form-control" rows="2" maxlength="1000">{{ old('referencia', $perfil['referencia'] ?? null) }}</textarea>
        </div>
        @unless ($esProveedor)
            @foreach (['latitud' => ['Latitud', 90], 'longitud' => ['Longitud', 180]] as $campo => [$etiqueta, $rango])
            <div class="col-md-6">
                <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                <input id="{{ $campo }}" class="form-control" name="{{ $campo }}" type="number" step="any"
                    min="{{ -$rango }}" max="{{ $rango }}" value="{{ old($campo, $perfil[$campo] ?? null) }}">
            </div>
            @endforeach
        @endunless
    </div>
    @unless ($esProveedor)
        <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
            <button type="button" class="btn btn-outline-primary btn-sm" data-ficha-mapa-abrir>
                <i class="bi bi-map me-1" aria-hidden="true"></i> Abrir mapa
            </button>
            <span class="small text-body-secondary" data-ficha-mapa-estado role="status">Selecciona un punto en el mapa o ingresa las coordenadas.</span>
        </div>
        <div class="ficha-mapa mt-3" data-ficha-mapa hidden aria-label="Mapa de ubicación del cliente"></div>
    @endunless
</section>

<section id="ficha-contactos" data-ficha-panel="contactos" aria-label="Contactos">
    @include('partials.ficha-panel-intro', [
        'tituloPanel' => 'Contactos', 'descripcionPanel' => 'Guarda los datos de contacto y las personas de referencia.',
    ])
    <div class="row g-3">
        @include('clientes::campo-ficha', ['campo' => 'email', 'etiqueta' => 'Correo electrónico', 'valor' => $cliente?->email, 'limite' => 254, 'tipo' => 'email'])
        @include('clientes::campo-ficha', ['campo' => 'telefono', 'etiqueta' => 'Teléfono', 'valor' => $cliente?->telefono, 'limite' => 30, 'tipo' => 'tel'])
        @include('clientes::campo-ficha', ['campo' => 'celular', 'etiqueta' => 'Celular', 'valor' => $perfil['celular'] ?? null, 'limite' => 100, 'tipo' => 'tel'])
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h3 class="fs-6 fw-semibold mb-0">Contactos adicionales</h3>
        <button type="button" class="btn btn-sm btn-outline-primary" data-ficha-contacto-agregar>
            <i class="bi bi-person-plus me-1" aria-hidden="true"></i> Agregar contacto
        </button>
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
    @include('partials.ficha-panel-intro', [
        'tituloPanel' => 'Documentos', 'descripcionPanel' => 'Adjunta los archivos que respaldan este registro.',
    ])
    @include('partials.campo-adjunto', [
        'campo' => 'adjuntos', 'nombre' => 'adjuntos[]', 'titulo' => 'Adjuntar documentos', 'iconoAdjunto' => 'bi-paperclip',
        'formatos' => 'application/pdf,image/jpeg,image/png', 'ayuda' => 'PDF, JPG o PNG · hasta 5 archivos de 8 MB cada uno',
        'accion' => 'Seleccionar archivos', 'multiple' => true,
    ])
    <h3 class="fs-6 fw-semibold mt-4">Archivos del {{ $entidad }}</h3>
    <ul class="list-group list-group-flush">
        @forelse ($documentos ?? [] as $documento)
            <li class="list-group-item px-0">
                <a class="ficha-archivo-guardado" href="{{ route($modulo.'.documento', [$cliente->id, $documento['id']]) }}">
                    <i class="bi {{ Str::endsWith(Str::lower($documento['nombre']), '.pdf') ? 'bi-file-earmark-pdf' : 'bi-file-earmark-image' }}" aria-hidden="true"></i>
                    <span>{{ $documento['nombre'] }}</span>
                    <i class="bi bi-download ms-auto" aria-hidden="true"></i>
                </a>
            </li>
        @empty
            <li class="list-group-item px-0 text-body-secondary">Todavía no hay archivos adjuntos.</li>
        @endforelse
    </ul>
</section>
