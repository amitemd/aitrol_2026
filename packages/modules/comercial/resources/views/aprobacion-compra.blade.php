@extends('layouts.adminlte')

@php $estados = \Aitrol\Comercial\Application\AprobacionesOrdenCompra::ESTADOS; @endphp
@section('title', 'Aprobación · '.$orden['numero'])
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Orden '.$orden['numero'], 'subtitulo' => $orden['proveedor'], 'icono' => 'bi-clipboard-check'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            @include('comercial::errores')
            <section class="card mb-3">
                <h2 class="card-header h5">{{ $estados[$orden['estado']] }} · {{ $orden['fecha'] }}</h2>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th scope="col">Concepto</th><th scope="col" class="text-end">Cantidad</th><th scope="col" class="text-end">Precio (USD)</th>
                            <th scope="col" class="text-end">IVA (USD)</th><th scope="col" class="text-end">Total (USD)</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($lineas as $linea)
                                <tr>
                                    <th scope="row">{{ $linea['descripcion'] }}</th>
                                    @foreach (['cantidad', 'precio', 'impuesto', 'total'] as $campo)
                                        <td class="text-end">{{ $linea[$campo] }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><th colspan="4" class="text-end" scope="row">Total de la orden</th><td class="text-end fw-bold">USD {{ $orden['total'] }}</td></tr></tfoot>
                    </table>
                </div>
            </section>
            @if ($orden['estado'] === 'pendiente')
                @if ((int) $orden['id_usuario_creacion'] === (int) auth()->id())
                    <p class="alert alert-info">Otro usuario con permiso de aprobación debe revisar esta orden.</p>
                @elseif (Gate::allows('permiso', ['compras.aprobaciones.index', 'actualizar']))
                    <form method="POST" action="{{ route('compras.aprobaciones.decidir', $orden['id']) }}" class="card mb-3">
                        @csrf
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label for="decision-compra" class="form-label">Decisión</label>
                                <select id="decision-compra" name="decision" class="form-select" required>
                                    <option value="">Seleccionar</option>
                                    <option value="aprobada" @selected(old('decision') === 'aprobada')>Aprobar</option>
                                    <option value="rechazada" @selected(old('decision') === 'rechazada')>Rechazar</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="motivo-compra" class="form-label">Motivo</label>
                                <textarea id="motivo-compra" name="motivo" class="form-control" rows="2" maxlength="500" aria-describedby="motivo-compra-ayuda">{{ old('motivo') }}</textarea>
                                <div id="motivo-compra-ayuda" class="form-text">Obligatorio al rechazar.</div>
                            </div>
                        </div>
                        <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Registrar decisión</button></div>
                    </form>
                @endif
            @endif
            <section class="card">
                <h2 class="card-header h5">Historial de aprobación</h2>
                <ul class="list-group list-group-flush">
                    @forelse ($historial as $evento)
                        <li class="list-group-item">
                            <strong>{{ $estados[$evento['estado']] }}</strong> · {{ $evento['responsable'] }}
                            · {{ \Illuminate\Support\Carbon::parse($evento['fecha'])->setTimezone('America/Guayaquil')->format('d/m/Y H:i') }}
                            @if ($evento['monto_minimo'] !== null)
                                <p class="mb-0 text-body-secondary">Umbral al registrar la orden USD {{ $evento['monto_minimo'] }}</p>
                            @endif
                            @if ($evento['motivo'] !== null)
                                <p class="mb-0">{{ $evento['motivo'] }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">Esta orden no requirió aprobación.</li>
                    @endforelse
                </ul>
                <div class="card-footer d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('compras.ordenes-compra.recepciones.index', $orden['id']) }}" class="btn btn-outline-primary">Recepciones de la orden</a>
                    <a href="{{ route('compras.aprobaciones.index') }}" class="btn btn-outline-secondary">Volver a aprobaciones</a>
                </div>
            </section>
        </div>
    </div>
@endsection
