@extends('layouts.adminlte')

@section('title', 'Nueva solicitud de compra')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Nueva solicitud de compra', 'subtitulo' => $presupuesto['centro'].' · '.$presupuesto['anio'], 'icono' => 'bi-clipboard-plus'])
    <div class="app-content">
        <div class="container-fluid">
            @include('comercial::errores')
            <form method="POST" action="{{ route('compras.solicitudes.store') }}" class="card" data-comercial-documento>
                @csrf
                <input type="hidden" name="id_presupuesto" value="{{ $presupuesto['id'] }}">
                <div class="card-body">
                    <p>Disponible en este presupuesto USD {{ $presupuesto['disponible'] }}. La reserva se realiza al aprobar la solicitud.</p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="solicitud-fecha">Fecha de solicitud</label>
                            <input class="form-control" id="solicitud-fecha" name="fecha" type="date"
                                value="{{ old('fecha', (int) $presupuesto['anio'] === now('America/Guayaquil')->year ? now('America/Guayaquil')->toDateString() : $presupuesto['anio'].'-01-01') }}"
                                min="{{ $presupuesto['anio'] }}-01-01" max="{{ $presupuesto['anio'] }}-12-31" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="solicitud-motivo">Motivo de compra</label>
                            <textarea class="form-control" id="solicitud-motivo" name="motivo" rows="2" maxlength="500" required>{{ old('motivo') }}</textarea>
                        </div>
                    </div>
                    <h2 class="h5">Productos y servicios</h2>
                    <div class="d-grid gap-2" data-comercial-lineas>
                        @foreach ($lineas->isEmpty() ? [[]] : $lineas as $indice => $linea)
                            @include('comercial::linea', ['ruta' => 'compras.ordenes-compra', 'vinculado' => false, 'retencion' => false,
                                'indice' => $indice, 'linea' => $linea, 'productoSeleccionado' => $productos->get((int) ($linea['id_producto'] ?? 0))])
                        @endforeach
                    </div>
                    <template data-comercial-plantilla>
                        @include('comercial::linea', ['ruta' => 'compras.ordenes-compra', 'vinculado' => false, 'retencion' => false,
                            'indice' => '__INDICE__', 'linea' => [], 'productoSeleccionado' => null])
                    </template>
                    <button type="button" class="btn btn-outline-secondary mt-3" data-comercial-agregar>Agregar línea</button>
                </div>
                <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Enviar a revisión</button></div>
            </form>
        </div>
    </div>
@endsection
