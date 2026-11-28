@extends('layouts.adminlte')

@section('title', 'Documento '.$documento['numero'])
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            @if ($visorFactura)
                <h1 class="visually-hidden">{{ $modulo === 'compras' ? 'Factura de compra' : 'Factura' }} {{ $documento['numero'] }}</h1>
                @include('comercial::visor-factura', ['logoEnFicha' => $logoFactura !== null, 'detalleModal' => true])
                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route($modulo.'.'.$seccion.'.index') }}" class="btn btn-outline-secondary">{{ __('Cerrar') }}</a>
                </div>
            @else
                <section class="card" data-ajax-form>
                    <h1 class="card-header fs-5">Documento {{ $documento['numero'] }}</h1>
                    <div class="card-body">
                        @include('comercial::contenido-documento')
                    </div>
                    <div class="card-footer text-end">
                        @if ($modulo === 'compras' && $seccion === 'ordenes-compra')
                            @if (($documento['id_solicitud_compra'] ?? null) !== null)
                                <a href="{{ route('compras.solicitudes.show', $documento['id_solicitud_compra']) }}"
                                    class="btn btn-outline-primary">Solicitud de origen</a>
                            @endif
                            @if (($documento['estado_aprobacion'] ?? null) !== null)
                                <a href="{{ route('compras.aprobaciones.show', $documento['id']) }}" class="btn btn-outline-primary"
                                    data-workspace-link="compras.aprobaciones.{{ $documento['id'] }}" data-workspace-label="Aprobación de la orden">Revisar aprobación</a>
                            @endif
                            <a href="{{ route('compras.ordenes-compra.recepciones.index', $documento['id']) }}"
                                class="btn btn-outline-primary" data-workspace-link="compras.recepciones.{{ $documento['id'] }}"
                                data-workspace-label="Recepciones de la orden">Recepciones de la orden</a>
                        @endif
                        <a href="{{ route($modulo.'.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
