@extends('layouts.adminlte')

@section('title', __('Contabilización automática'))
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Contabilización automática', 'subtitulo' => 'Contabilidad', 'icono' => 'bi-diagram-2'])
    <div class="app-content"><div class="container-fluid">
        @if (session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
        <form method="POST" action="{{ route('contabilidad.integraciones.update') }}" class="card">
            @csrf @method('PUT')
            @include('contabilidad::errores')
            @php $puedeEditar = Gate::allows('permiso', ['contabilidad.integraciones.index', 'actualizar']); @endphp
            <fieldset @disabled(!$puedeEditar)>
                <div class="card-body">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-sm-6">
                            <label for="co-activar" class="form-label">Contabilización</label>
                            <select id="co-activar" name="activa" class="form-select" required>
                                <option value="0">Manual</option><option value="1" @selected(old('activa', $config['activa']))>Automática</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="co-inicio" class="form-label">Fecha de inicio de la integración</label>
                            <input id="co-inicio" name="fecha_inicio" type="date" class="form-control" value="{{ old('fecha_inicio', $config['fecha_inicio']) }}">
                        </div>
                    </div>
                    <p class="text-body-secondary">Configura las cuentas antes de activar cada flujo. Una operación automática requiere un período abierto y las cuentas correspondientes.</p>
                    <h2 class="h6 mb-3">Reglas contables</h2>
                    <div class="row g-3 mb-4">
                        @foreach (config('contabilidad.reglas') as $clave => [$etiqueta, $clase])
                            @php $cuenta = $cuentas->get(old('reglas.'.$clave, $config['reglas'][$clave] ?? null)); @endphp
                            <div class="col-sm-6 col-xl-4">
                                <label class="form-label" for="co-regla-{{ $clave }}">{{ __($etiqueta) }}</label>
                                <select id="co-regla-{{ $clave }}" name="reglas[{{ $clave }}]" class="form-select" data-select2-min="0" data-select2-placeholder="Seleccionar cuenta"
                                    data-select2-url="{{ route('contabilidad.integraciones.opciones', array_filter(['clase' => $clase]) + ['efectivo' => 0]) }}">
                                    <option value="">Seleccionar cuenta</option>
                                    @if ($cuenta)<option value="{{ $cuenta->id }}" selected>{{ $cuenta->codigo }} · {{ $cuenta->nombre }}</option>@endif
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <h2 class="h6 mb-3">Cajas y bancos en USD</h2>
                    <div class="row g-3">
                        @forelse ($cajas as $caja)
                            @php $cuenta = $cuentas->get(old('tesoreria.'.$caja->id, $config['tesoreria'][$caja->id] ?? null)); @endphp
                            <div class="col-sm-6 col-xl-4">
                                <label class="form-label" for="co-caja-{{ $caja->id }}">{{ $caja->codigo }} · {{ $caja->nombre }}</label>
                                <select id="co-caja-{{ $caja->id }}" name="tesoreria[{{ $caja->id }}]" class="form-select" data-select2-min="0" data-select2-placeholder="Cuenta de efectivo"
                                    data-select2-url="{{ route('contabilidad.integraciones.opciones', ['efectivo' => 1, 'clase' => 'activo']) }}">
                                    <option value="">Cuenta de efectivo</option>
                                    @if ($cuenta)<option value="{{ $cuenta->id }}" selected>{{ $cuenta->codigo }} · {{ $cuenta->nombre }}</option>@endif
                                </select>
                            </div>
                        @empty
                            <p class="text-body-secondary mb-0">Todavía no hay cajas o bancos en USD.</p>
                        @endforelse
                    </div>
                </div>
                @if ($puedeEditar)<div class="card-footer text-end"><button class="btn btn-primary">Guardar reglas</button></div>@endif
            </fieldset>
        </form>
    </div></div>
@endsection
