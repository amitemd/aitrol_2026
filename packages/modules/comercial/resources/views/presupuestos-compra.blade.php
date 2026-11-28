@extends('layouts.adminlte')

@section('title', 'Presupuestos de compra')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Presupuestos de compra', 'subtitulo' => 'Sucursal activa', 'icono' => 'bi-wallet2'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
            @include('comercial::errores')
            <section class="card mb-3">
                <div class="card-header">
                    <form method="GET" action="{{ route('compras.presupuestos.index') }}" class="d-flex flex-wrap align-items-end gap-2">
                        <div>
                            <label class="form-label" for="presupuestos-anio">Año</label>
                            <input class="form-control" type="number" name="anio" id="presupuestos-anio" value="{{ $anio }}" min="1900" max="{{ now('America/Guayaquil')->year + 1 }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Consultar</button>
                        <a class="btn btn-outline-secondary ms-auto" href="{{ route('compras.solicitudes.index') }}">Solicitudes de compra</a>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th scope="col">Centro de costo</th><th scope="col" class="text-end">Asignado (USD)</th>
                            <th scope="col" class="text-end">Reservado</th><th scope="col" class="text-end">Comprometido</th>
                            <th scope="col" class="text-end">Disponible</th><th scope="col" class="text-end">Acción</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($presupuestos as $presupuesto)
                                <tr>
                                    <th scope="row">{{ $presupuesto['codigo_centro'] }} · {{ $presupuesto['centro'] }}</th>
                                    @foreach (['monto', 'reservado', 'comprometido', 'disponible'] as $campo)
                                        <td class="text-end">{{ $presupuesto[$campo] }}</td>
                                    @endforeach
                                    <td class="text-end">
                                        @if (!$presupuesto['centro_activo'])
                                            Centro inactivo
                                        @elseif (Gate::allows('permiso', ['compras.ordenes-compra.index', 'escribir']))
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('compras.solicitudes.create', $presupuesto['id']) }}">Nueva solicitud</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-body-secondary">No hay presupuestos para este año y sucursal.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($presupuestos->hasPages()) <div class="card-footer">{{ $presupuestos->links('pagination::bootstrap-5') }}</div> @endif
            </section>
            @can('administrar-configuracion')
                <section class="card">
                    <h2 class="card-header h5">Asignar o actualizar presupuesto</h2>
                    <div class="card-body">
                        @if ($centros->isEmpty())
                            <p class="mb-0">Crea un centro de costo activo en Contabilidad para asignar presupuesto.</p>
                        @else
                            <form method="POST" action="{{ route('compras.presupuestos.update') }}" class="row g-2 align-items-end">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="anio" value="{{ $anio }}">
                                <div class="col-md-6">
                                    <label class="form-label" for="presupuesto-centro">Centro de costo</label>
                                    <select class="form-select" id="presupuesto-centro" name="id_centro_costo" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($centros as $centro)
                                            <option value="{{ $centro->id }}" @selected((int) old('id_centro_costo') === $centro->id)>{{ $centro->codigo }} · {{ $centro->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="presupuesto-monto">Monto anual con IVA (USD)</label>
                                    <input class="form-control" id="presupuesto-monto" name="monto" type="number" min="0" step="0.01" value="{{ old('monto') }}" required>
                                </div>
                                <div class="col-auto"><button type="submit" class="btn btn-primary">Guardar</button></div>
                            </form>
                        @endif
                    </div>
                </section>
            @endcan
        </div>
    </div>
@endsection
