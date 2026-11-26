<div class="app-content-header clientes-cabecera">
    <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="clientes-icono" aria-hidden="true"><i class="bi {{ $icono }}"></i></span>
            <div>
                <h1 class="mb-0 fs-3 fw-semibold">{{ $titulo }}</h1>
                <p class="mb-0 text-body-secondary">{{ $subtitulo }}</p>
            </div>
        </div>
        @isset($crearUrl)
            <a href="{{ $crearUrl }}" class="btn btn-primary" data-ajax-open="{{ $crearTexto }}">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>{{ $crearTexto }}
            </a>
        @endisset
    </div>
</div>