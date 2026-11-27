@extends('layouts.adminlte')

@section('title', 'Papel de trabajo tributario')
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">{{ config('tributario.secciones.'.$registro['formato'].'.0') }}</h1>
            <div class="card-body">
                <p><strong>{{ $registro['desde'] }} a {{ $registro['hasta'] }}</strong> · {{ config('tributario.estados.'.$registro['estado']) }}</p>
                @if ($registro['referencia_externa'])
                    <p class="text-body-secondary">Constancia {{ $registro['referencia_externa'] }} · {{ $registro['fecha_externa'] }}</p>
                @endif
                <dl class="row g-2">
                    @foreach ($registro['resumen'] as $clave => $valor)
                        <div class="col-6 col-lg-4"><div class="border rounded p-2 bg-body-tertiary h-100">
                            <dt class="small text-body-secondary">{{ Str::ucfirst(str_replace('_', ' ', $clave)) }}</dt><dd class="fw-semibold mb-0">{{ $valor }}</dd>
                        </div></div>
                    @endforeach
                </dl>
                @if ($registro['observaciones'])
                    <ul class="small text-body-secondary">@foreach ($registro['observaciones'] as $texto)<li>{{ $texto }}</li>@endforeach</ul>
                @endif
                <div class="table-responsive">
                    <table class="table align-middle">
                        <caption class="visually-hidden">Detalle del papel de trabajo</caption>
                        <thead><tr>@foreach ($columnas as $etiqueta)<th scope="col">{{ __($etiqueta) }}</th>@endforeach</tr></thead>
                        <tbody>
                            @forelse ($registro['detalle'] as $fila)
                                <tr>@foreach ($columnas as $campo => $etiqueta)<td>{{ $fila[$campo] ?? '—' }}</td>@endforeach</tr>
                            @empty
                                <tr><td colspan="{{ count($columnas) }}" class="text-center">Sin datos en el período seleccionado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a class="btn btn-outline-secondary" href="{{ route('tributario.'.$seccion.'.index') }}" data-ajax-close>{{ __('Cerrar') }}</a>
                <a class="btn btn-outline-primary" href="{{ route('tributario.'.$seccion.'.exportar', $registro['id']) }}"><i class="bi bi-download me-1" aria-hidden="true"></i>CSV de trabajo</a>
            </div>
        </section>
    </div></div>
@endsection
