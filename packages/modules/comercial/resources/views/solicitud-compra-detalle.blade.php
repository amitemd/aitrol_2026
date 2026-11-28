@extends('layouts.adminlte')

@php
    $estados = \Aitrol\Comercial\Application\SolicitudesCompra::ESTADOS;
    $creador = (int) $solicitud['id_usuario_creacion'] === (int) auth()->id();
    $revisor = Gate::allows('permiso', ['compras.aprobaciones.index', 'actualizar']);
    $hoy = now('America/Guayaquil');
    $fechaOrden = $hoy->year === (int) $presupuesto['anio'] ? max($solicitud['fecha'], $hoy->toDateString()) : $solicitud['fecha'];
@endphp
@section('title', 'Solicitud '.$solicitud['numero'])
@section('content')
    @include('partials.listado-cabecera', ['titulo' => $solicitud['numero'], 'subtitulo' => $presupuesto['centro'].' · '.$presupuesto['anio'], 'icono' => 'bi-clipboard-plus'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
            @include('comercial::errores')
            <section class="card mb-3">
                <h2 class="card-header h5">{{ $estados[$solicitud['estado']] }} · {{ $solicitud['fecha'] }}</h2>
                <div class="card-body">
                    <p>{{ $solicitud['motivo'] }}</p>
                    <p class="mb-0">Disponible en el presupuesto <strong>USD {{ $presupuesto['disponible'] }}</strong></p>
                    @if ($orden)
                        <p class="mb-0 mt-2">
                            <a href="{{ route('compras.ordenes-compra.recepciones.index', $orden->id) }}">Orden {{ $orden->numero }}</a>
                            @if ($orden->anulado_at) · Anulada
                            @elseif ($orden->estado_aprobacion === 'pendiente')
                                · <a href="{{ route('compras.aprobaciones.show', $orden->id) }}">Pendiente de aprobación de orden</a>
                            @elseif ($orden->estado_aprobacion === 'rechazada') · Rechazada
                            @endif
                        </p>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th scope="col">Concepto</th><th scope="col" class="text-end">Cantidad</th>
                            <th scope="col" class="text-end">Precio unitario</th><th scope="col" class="text-end">IVA</th>
                            <th scope="col" class="text-end">Total (USD)</th></tr></thead>
                        <tbody>
                            @foreach ($lineas as $linea)
                                <tr><th scope="row">{{ $linea['descripcion'] }}</th>
                                    @foreach (['cantidad', 'precio', 'impuesto', 'total'] as $campo)
                                        <td class="text-end">{{ $linea[$campo] }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><th scope="row" colspan="4" class="text-end">Total de la solicitud</th><td class="text-end fw-bold">{{ $solicitud['total'] }}</td></tr></tfoot>
                    </table>
                </div>
            </section>
            @if ($solicitud['estado'] === 'pendiente' && $revisor && !$creador)
                <form method="POST" action="{{ route('compras.solicitudes.decidir', $solicitud['id']) }}" class="card mb-3">
                    @csrf
                    <h2 class="card-header h5">Revisar solicitud</h2>
                    <div class="card-body row g-2">
                        <div class="col-md-4">
                            <label class="form-label" for="solicitud-decision">Decisión</label>
                            <select class="form-select" name="decision" id="solicitud-decision" required>
                                <option value="">Seleccionar</option>
                                <option value="reservada" @selected(old('decision') === 'reservada')>Aprobar y reservar</option>
                                <option value="rechazada" @selected(old('decision') === 'rechazada')>Rechazar</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="solicitud-razon-decision">Motivo</label>
                            <input class="form-control" name="motivo" id="solicitud-razon-decision" value="{{ old('decision') ? old('motivo') : '' }}"
                                maxlength="500" aria-describedby="solicitud-decision-ayuda">
                            <div id="solicitud-decision-ayuda" class="form-text">Obligatorio al rechazar.</div>
                        </div>
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Registrar decisión</button></div>
                </form>
            @endif
            @if ($solicitud['estado'] === 'reservada' && Gate::allows('permiso', ['compras.ordenes-compra.index', 'escribir']))
                <form method="POST" action="{{ route('compras.solicitudes.convertir', $solicitud['id']) }}" class="card mb-3">
                    @csrf
                    <h2 class="card-header h5">Generar orden de compra</h2>
                    <div class="card-body row g-2">
                        <div class="col-md-5">
                            <label class="form-label" for="solicitud-proveedor">Proveedor</label>
                            <select class="form-select" name="id_tercero" id="solicitud-proveedor"
                                data-select2-url="{{ route('compras.ordenes-compra.opciones', 'terceros') }}" data-select2-min="0"
                                data-select2-placeholder="Buscar proveedor" required><option value="">Seleccionar</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="solicitud-orden-numero">Número de orden</label>
                            <input class="form-control" name="numero" id="solicitud-orden-numero" value="{{ old('numero') }}" maxlength="60" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="solicitud-orden-fecha">Fecha de orden</label>
                            <input class="form-control" name="fecha" id="solicitud-orden-fecha" type="date" value="{{ old('fecha', $fechaOrden) }}"
                                min="{{ $solicitud['fecha'] }}" max="{{ $presupuesto['anio'] }}-12-31" required>
                        </div>
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Generar orden</button></div>
                </form>
            @endif
            <section class="card">
                <h2 class="card-header h5">Historial</h2>
                <ul class="list-group list-group-flush">
                    @foreach ($historial as $evento)
                        <li class="list-group-item">
                            <strong>{{ $estados[$evento->estado] }}</strong> · {{ $evento->responsable }}
                            · {{ \Illuminate\Support\Carbon::parse($evento->created_at)->setTimezone('America/Guayaquil')->format('d/m/Y H:i') }}
                            @if ($evento->motivo)<p class="mb-0">{{ $evento->motivo }}</p>@endif
                        </li>
                    @endforeach
                </ul>
                <div class="card-footer">
                    @if (in_array($solicitud['estado'], ['pendiente', 'reservada'], true) && ($creador || $revisor)
                        && Gate::allows('permiso', ['compras.ordenes-compra.index', 'actualizar']))
                        <details>
                            <summary>Cancelar solicitud</summary>
                            <form class="row g-2 align-items-end mt-1" method="POST" action="{{ route('compras.solicitudes.cancelar', $solicitud['id']) }}">
                                @csrf
                                <div class="col-md">
                                    <label class="form-label" for="solicitud-motivo-cancelar">Motivo de cancelación</label>
                                    <input class="form-control" id="solicitud-motivo-cancelar" name="motivo" maxlength="500" required>
                                </div>
                                <div class="col-auto"><button class="btn btn-outline-danger" type="submit">Confirmar cancelación</button></div>
                            </form>
                        </details>
                    @endif
                    <div class="text-end mt-2"><a class="btn btn-outline-secondary" href="{{ route('compras.solicitudes.index') }}">Volver a solicitudes</a></div>
                </div>
            </section>
        </div>
    </div>
@endsection
