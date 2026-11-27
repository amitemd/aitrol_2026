@extends('layouts.adminlte')

@section('title', 'Mantenimiento del servidor')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Mantenimiento del servidor', 'subtitulo' => 'Configuración', 'icono' => 'bi-tools',
    ])
    @php
        $estilos = [
            'pendiente' => ['Pendiente', 'secondary'],
            'ejecutando' => ['En ejecución', 'primary'],
            'correcto' => ['Completado', 'success'],
            'fallido' => ['Fallido', 'danger'],
        ];
    @endphp
    <div class="app-content">
        <div class="container-fluid" data-mantenimiento data-estado-url="{{ route('configuracion.mantenimiento.estado') }}">
            @if (! $habilitado)
                <p class="alert alert-secondary">La ejecución está deshabilitada. Se habilita en el entorno del servidor mediante <code>AITROL_MANTENIMIENTO_WEB=true</code>.</p>
            @endif
            <div class="alert alert-danger" role="alert" data-mantenimiento-error hidden></div>
            <div class="row g-3">
                @foreach ($acciones as $accion => [$titulo, $descripcion, $icono])
                    @php
                        $registro = $estados[$accion] ?? null;
                        [$estado, $color] = $estilos[$registro?->estado] ?? ['Sin ejecutar', 'secondary'];
                        $disponible = in_array($accion, $disponibles, true);
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <section class="card h-100" data-mantenimiento-accion="{{ $accion }}" data-disponible="{{ (int) $disponible }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h2 class="h6 mb-0"><i class="bi {{ $icono }} me-2 text-primary" aria-hidden="true"></i>{{ $titulo }}</h2>
                                    <span class="badge text-bg-{{ $color }}" data-mantenimiento-estado>{{ $estado }}</span>
                                </div>
                                <p class="text-body-secondary mb-2">{{ $descripcion }}</p>
                                @if ($accion === 'git' && $habilitado && ! $disponible)
                                    <p class="small text-body-secondary mb-2">Git se habilita mediante <code>AITROL_MANTENIMIENTO_GIT=true</code>.</p>
                                @endif
                                <div class="small" role="status">
                                    <div data-mantenimiento-detalle>{{ $registro?->detalle ?? '' }}</div>
                                    <time class="text-body-secondary" data-mantenimiento-fecha>{{ $registro?->finalizado_at ?? $registro?->iniciado_at ?? '' }}</time>
                                </div>
                            </div>
                            <div class="card-footer">
                                <form method="POST" action="{{ route('configuracion.mantenimiento.ejecutar', $accion) }}" data-mantenimiento-form
                                    @if (in_array($accion, ['git', 'migrar', 'cache'], true)) data-accion-confirmar="{{ $descripcion }} Se aplicará al servidor que estás usando." @endif>
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" aria-label="{{ $titulo }}" @disabled(! $disponible || $ocupado)>Ejecutar</button>
                                </form>
                            </div>
                        </section>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('mantenimiento.js') }}" defer></script>
@endpush
