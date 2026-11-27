@extends('layouts.adminlte')

@php
    $ruta = 'contabilidad.'.$seccion;
    $titulo = match ($accion) {
        'editar' => 'Editar cuenta', 'cerrar' => 'Cerrar período', 'revertir' => 'Revertir asiento',
        default => config('contabilidad.secciones.'.$seccion.'.2'),
    };
@endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <form class="card" method="POST" action="{{ $accion ? route($ruta.'.'.$accion, $registro['id']) : route($ruta.'.store') }}" data-ajax-form
            @if ($seccion === 'asientos' && !$accion) data-comercial-documento data-min-lineas="2" @endif>
            @csrf
            @if ($accion === 'editar') @method('PUT') @endif
            <h1 class="card-header fs-5">{{ $titulo }}</h1>
            @include('contabilidad::errores')
            <div class="card-body row g-3">
                @if ($seccion === 'cuentas')
                    <div class="col-sm-4">
                        <label class="form-label" for="co-codigo">{{ __('Código') }}</label>
                        <input id="co-codigo" name="codigo" class="form-control" maxlength="40" value="{{ old('codigo', $registro['codigo'] ?? '') }}" required autofocus>
                    </div>
                    <div class="col-sm-8">
                        <label class="form-label" for="co-nombre">{{ __('Nombre') }}</label>
                        <input id="co-nombre" name="nombre" class="form-control" maxlength="160" value="{{ old('nombre', $registro['nombre'] ?? '') }}" required>
                    </div>
                    @unless ($accion)
                        <div class="col-sm-6">
                            <label class="form-label" for="co-clase">Clase</label>
                            <select id="co-clase" name="clase" class="form-select" required>
                                @foreach (config('contabilidad.clases') as $valor => $etiqueta)<option value="{{ $valor }}" @selected(old('clase') === $valor)>{{ __($etiqueta) }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="co-padre">Cuenta superior (opcional)</label>
                            <select id="co-padre" name="id_padre" class="form-select" data-select2-url="{{ route($ruta.'.opciones') }}" data-select2-min="0" data-select2-placeholder="Sin cuenta superior">
                                <option value="">Sin cuenta superior</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-3">
                            <label class="form-check"><input type="hidden" name="auxiliar" value="0"><input type="checkbox" name="auxiliar" value="1" class="form-check-input" @checked(old('auxiliar', true))> Permite movimientos (auxiliar)</label>
                            <label class="form-check"><input type="checkbox" name="efectivo" value="1" class="form-check-input" @checked(old('efectivo'))> Efectivo o equivalente</label>
                        </div>
                    @endunless
                @elseif ($accion)
                    <div class="col-12">
                        <p class="mb-0">{{ $seccion === 'asientos' ? 'Asiento '.$registro['numero'].' · '.$registro['concepto'] : $registro['nombre'].' · '.$registro['desde'].' a '.$registro['hasta'] }}</p>
                    </div>
                    @if ($accion === 'revertir')
                        <div class="col-sm-6"><label class="form-label" for="co-fecha">Fecha de reversión</label><input id="co-fecha" type="date" name="fecha" class="form-control" value="{{ old('fecha', now()->toDateString()) }}" required></div>
                    @endif
                    <div class="col-12"><label class="form-label" for="co-motivo">Motivo</label><textarea id="co-motivo" class="form-control" name="motivo" rows="3" maxlength="250" required>{{ old('motivo') }}</textarea></div>
                @elseif ($seccion === 'periodos')
                    <div class="col-12"><label class="form-label" for="co-nombre">{{ __('Nombre') }}</label><input id="co-nombre" class="form-control" name="nombre" maxlength="120" value="{{ old('nombre') }}" required autofocus></div>
                    @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                        <div class="col-sm-6"><label class="form-label" for="co-{{ $campo }}">{{ __($etiqueta) }}</label><input type="date" id="co-{{ $campo }}" name="{{ $campo }}" class="form-control" value="{{ old($campo) }}" required></div>
                    @endforeach
                @else
                    <input type="hidden" name="clave_operacion" value="{{ old('clave_operacion', (string) Str::uuid()) }}">
                    <div class="col-sm-6"><label class="form-label" for="co-fecha">{{ __('Fecha') }}</label><input id="co-fecha" type="date" name="fecha" class="form-control" value="{{ old('fecha', now()->toDateString()) }}" required></div>
                    <div class="col-sm-6">
                        <label class="form-label" for="co-flujo">Actividad de efectivo</label>
                        <select id="co-flujo" name="flujo" class="form-select">
                            <option value="">Sin movimiento de efectivo</option>
                            @foreach (config('contabilidad.flujos') as $valor => $etiqueta)<option value="{{ $valor }}" @selected(old('flujo') === $valor)>{{ __($etiqueta) }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-sm-7"><label class="form-label" for="co-concepto">Concepto</label><input id="co-concepto" class="form-control" name="concepto" maxlength="250" value="{{ old('concepto') }}" required autofocus></div>
                    <div class="col-sm-5"><label class="form-label" for="co-referencia">Referencia</label><input id="co-referencia" class="form-control" name="referencia" maxlength="120" value="{{ old('referencia') }}"></div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <h2 class="h6 mb-0">Partidas en USD</h2>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-comercial-agregar><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Agregar partida</button>
                        </div>
                        <div class="d-grid gap-2" data-comercial-lineas>
                            @foreach (array_values((array) old('lineas', [[], []])) as $indice => $fila)
                                @include('contabilidad::linea', ['indice' => $indice, 'fila' => is_array($fila) ? $fila : []])
                            @endforeach
                        </div>
                        <template data-comercial-plantilla>@include('contabilidad::linea', ['indice' => '__INDICE__', 'fila' => []])</template>
                    </div>
                @endif
            </div>
            <div class="card-footer d-flex align-items-center flex-wrap gap-2">
                @if ($seccion === 'cuentas')@include('partials.interruptor-activo', ['activo' => $registro['activo'] ?? true])@endif
                <div class="ms-auto d-flex gap-2"><a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button class="btn btn-primary">{{ $accion ? 'Confirmar' : 'Guardar' }}</button>
                </div>
            </div>
        </form>
    </div></div>
@endsection
