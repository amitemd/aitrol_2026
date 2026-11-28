@extends('layouts.adminlte')

@section('title', __('Panel general'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('panel-administrativo.css') }}?v={{ filemtime(public_path('panel-administrativo.css')) }}">
@endpush

@section('content')
@php
    $importe = static fn (int|string $centavos) => extension_loaded('intl')
        ? \Illuminate\Support\Number::format($centavos / 100, precision: 2, locale: app()->getLocale())
        : number_format($centavos / 100, 2);
@endphp
<div class="app-content dashboard-admin" data-admin-dashboard
    data-preferences="{{ json_encode($preferencias, JSON_THROW_ON_ERROR) }}"
    data-default-order="{{ json_encode($ordenInicial, JSON_THROW_ON_ERROR) }}"
    data-chart="{{ json_encode(compact('meses', 'series'), JSON_THROW_ON_ERROR) }}"
    data-save-url="{{ $personalizable ? route('dashboard.preferencias') : '' }}"
    data-csrf="{{ csrf_token() }}"
    data-saved="{{ __('Preferencias guardadas.') }}"
    data-save-error="{{ __('No se pudieron guardar las preferencias. Inténtalo de nuevo.') }}"
    data-chart-error="{{ __('No se pudo cargar el gráfico.') }}"
    data-moved="{{ __('Orden actualizado.') }}">
    <div class="container-fluid">
        <header class="dashboard-heading">
            <div>
                <h1>{{ __('Panel general') }}</h1>
                <p>{{ $sucursal->nombre }} <span aria-hidden="true">·</span> {{ __('Período') }} {{ $periodo }}</p>
            </div>
            @if ($personalizable)
                <div class="dashboard-tools">
                    <button type="button" class="btn btn-outline-primary" data-dashboard-edit aria-expanded="false">
                        <i class="bi bi-sliders2" aria-hidden="true"></i> {{ __('Personalizar') }}
                    </button>
                    <div class="dashboard-tools" data-dashboard-edit-tools hidden>
                        <button type="button" class="btn btn-outline-secondary" data-dashboard-reset>{{ __('Restablecer') }}</button>
                        <button type="button" class="btn btn-outline-secondary" data-dashboard-cancel>{{ __('Cancelar') }}</button>
                        <button type="button" class="btn btn-primary" data-dashboard-save><i class="bi bi-check-lg" aria-hidden="true"></i> {{ __('Guardar') }}</button>
                    </div>
                </div>
            @endif
        </header>
        <p class="dashboard-status text-body-secondary small" data-dashboard-status role="status"></p>

        @if ($indicadores !== [])
            <section class="dashboard-metrics" data-dashboard-lane="indicadores" aria-label="{{ __('Indicadores') }}">
                @foreach ($preferencias['distribucion']['indicadores'] as $id)
                    @continue(! isset($indicadores[$id]))
                    @php([$titulo, $icono, $valores, $nota, $enlace] = $indicadores[$id])
                    <article class="dashboard-card dashboard-metric" data-dashboard-card="{{ $id }}">
                        <div class="dashboard-metric-heading">
                            <i class="bi {{ $icono }}" aria-hidden="true"></i>
                            <h2>@if ($enlace)<a href="{{ $enlace }}">{{ __($titulo) }}</a>@else{{ __($titulo) }}@endif</h2>
                        </div>
                        <div class="dashboard-value">
                            @forelse ($valores as $moneda => $centavos)
                                <div><small>{{ $moneda }}</small> {{ $importe($centavos) }}</div>
                            @empty
                                <span class="dashboard-empty">{{ __('Sin datos') }}</span>
                            @endforelse
                        </div>
                        <div class="dashboard-metric-footer">
                            <span>{{ __($nota) }}</span>
                            @if ($personalizable) @include('partials.dashboard-mover') @endif
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        <section class="dashboard-panels" data-dashboard-lane="paneles" aria-label="{{ __('Cuadros del panel') }}">
            @foreach ($preferencias['distribucion']['paneles'] as $id)
                @continue($id === 'productos' && $productosRecientes === null)
                <article class="dashboard-card dashboard-panel" data-dashboard-card="{{ $id }}">
                    <header class="dashboard-panel-heading">
                        <h2>
                            @switch($id)
                                @case('tendencia') <i class="bi bi-graph-up" aria-hidden="true"></i> {{ __('Evolución mensual') }} @break
                                @case('alertas') <i class="bi bi-bell" aria-hidden="true"></i> {{ __('Requiere atención') }} @break
                                @case('accesos') <i class="bi bi-grid" aria-hidden="true"></i> {{ __('Accesos rápidos') }} @break
                                @case('productos') <i class="bi bi-box-seam" aria-hidden="true"></i> {{ __('Últimos productos registrados') }} @break
                            @endswitch
                        </h2>
                        <div class="dashboard-tools">
                            @if ($id === 'tendencia')
                                <select class="form-select form-select-sm" data-dashboard-chart-type data-select2-nativo aria-label="{{ __('Tipo de gráfico') }}">
                                    @foreach ($tiposGrafico as $tipo => $nombre)
                                        <option value="{{ $tipo }}" @selected($preferencias['grafico'] === $tipo)>{{ __($nombre) }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if ($personalizable) @include('partials.dashboard-mover') @endif
                        </div>
                    </header>

                    @switch($id)
                        @case('tendencia')
                            <div class="dashboard-panel-body">
                                <div class="dashboard-chart">
                                    @if ($series !== [])
                                        <canvas id="sales-chart" role="img" aria-label="{{ __('Evolución mensual de ventas y compras en USD') }}"></canvas>
                                        <p class="dashboard-empty dashboard-chart-empty" data-dashboard-chart-error hidden></p>
                                    @else
                                        <p class="dashboard-empty dashboard-chart-empty">{{ __('Sin datos comerciales para este período.') }}</p>
                                    @endif
                                </div>
                            </div>
                            @break
                        @case('alertas')
                            <div class="dashboard-panel-body">
                                <ul class="dashboard-list">
                                    @forelse ($alertas as [$titulo, $cantidad, $enlace])
                                        <li><a href="{{ $enlace }}"><span>{{ __($titulo) }}</span><strong @class(['dashboard-alert-count', 'has-items' => $cantidad > 0])>{{ $cantidad }}</strong></a></li>
                                    @empty
                                        <li class="dashboard-empty">{{ __('No hay alertas disponibles.') }}</li>
                                    @endforelse
                                </ul>
                            </div>
                            @break
                        @case('accesos')
                            <div class="dashboard-panel-body">
                                <ul class="dashboard-links">
                                    @foreach ($acciones as $ruta => [$nombre, $icono])
                                        <li><a href="{{ route($ruta) }}"
                                            @if ($ruta === 'ventas.documentos.create') data-workspace-link="{{ $ruta }}" data-workspace-label="{{ __($nombre) }}"
                                            @else data-ajax-open="{{ __($nombre) }}" @endif>
                                            <i class="bi {{ $icono }}" aria-hidden="true"></i>{{ __($nombre) }}
                                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                        </a></li>
                                    @endforeach
                                    @foreach ($accesos as $ruta => [$nombre, $icono])
                                        <li><a href="{{ route($ruta) }}" data-workspace-link="{{ $ruta }}" data-workspace-label="{{ __($nombre) }}">
                                            <i class="bi {{ $icono }}" aria-hidden="true"></i>{{ __($nombre) }}
                                            <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                                        </a></li>
                                    @endforeach
                                    @if ($acciones === [] && $accesos === [])
                                        <li class="dashboard-empty">{{ __('No hay accesos disponibles.') }}</li>
                                    @endif
                                </ul>
                            </div>
                            @break
                        @case('productos')
                            <div class="dashboard-panel-body">
                                <ul class="dashboard-list dashboard-products">
                                    @forelse ($productosRecientes as $producto)
                                        <li>
                                            @if ($producto->id_imagen ?? null)
                                                <img width="40" height="40" class="rounded object-fit-contain" loading="lazy" alt=""
                                                     src="{{ route('inventarios.productos.imagen', [$producto->id, $producto->id_imagen]) }}">
                                            @else
                                                <i class="bi bi-box-seam" aria-hidden="true"></i>
                                            @endif
                                            <a href="{{ route('inventarios.productos.show', $producto->id) }}" data-ajax-open="{{ $producto->nombre }}">
                                                <span>{{ $producto->nombre }}</span>
                                                <small class="text-body-secondary">{{ $producto->codigo }}</small>
                                            </a>
                                            @if ($producto->precio_venta !== null)
                                                <small>USD {{ $importe($producto->precio_venta) }}</small>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="dashboard-empty">{{ __('Todavía no hay productos registrados.') }}</li>
                                    @endforelse
                                </ul>
                                <a class="dashboard-list-more" href="{{ route('inventarios.productos.index') }}" data-workspace-link="inventarios.productos.index" data-workspace-label="{{ __('Productos') }}">{{ __('Ver todos los productos') }}</a>
                            </div>
                            @break
                    @endswitch
                </article>
            @endforeach
        </section>
        @if ($productosRecientes !== null || $acciones !== [])
            @include('partials.listado-modal', ['iconoModal' => 'bi-grid'])
        @endif
    </div>
</div>
@endsection

@push('scripts')
    @if ($series !== [])
        <script nonce="{{ request()->attributes->get('csp_nonce') }}"
                src="https://cdn.jsdelivr.net/npm/chart.js@4.6.1/dist/chart.umd.min.js"
                integrity="sha256-hzZMwrKnamaEkp59a4Cxiol3lNs4QAyv8fzxeRxpJdI=" crossorigin="anonymous" defer></script>
    @endif
    <script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('panel-administrativo.js') }}?v={{ filemtime(public_path('panel-administrativo.js')) }}" defer></script>
@endpush
