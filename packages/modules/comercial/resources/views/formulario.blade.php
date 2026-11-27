@extends('layouts.adminlte')

@php
    $config = config('comercial.'.$modulo);
    $ruta = $modulo.'.'.$seccion;
    $titulo = $config['secciones'][$seccion][2];
    $vinculado = in_array($seccion, \Aitrol\Comercial\Application\DocumentosComerciales::VINCULADOS, true);
    $retencion = $seccion === 'retenciones';
@endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form method="POST" action="{{ route($ruta.'.store') }}" class="card" data-ajax-form data-comercial-documento>
                @csrf
                <h1 class="card-header fs-5">{{ $titulo }}</h1>
                @include('comercial::errores')
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="com-numero">Número de documento</label>
                            <input name="numero" class="form-control" id="com-numero" maxlength="60" value="{{ old('numero') }}" required autofocus>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="com-fecha">Fecha</label>
                            <input type="date" name="fecha" class="form-control" id="com-fecha" value="{{ old('fecha', now()->toDateString()) }}" required>
                        </div>
                        <div @class(['col-sm-6' => $seccion === 'documentos', 'col-12' => $seccion !== 'documentos'])>
                            <label class="form-label" for="com-tercero">{{ $vinculado ? 'Documento de origen' : $config['tercero'] }}</label>
                            <select id="com-tercero" class="form-select" name="{{ $vinculado ? 'id_documento_origen' : 'id_tercero' }}"
                                data-select2-url="{{ route($ruta.'.opciones', $vinculado ? 'origenes' : 'terceros') }}" data-select2-min="0" required>
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        @if ($seccion === 'documentos')
                            <div class="col-sm-6">
                                <label class="form-label" for="com-bodega">Bodega</label>
                                <select id="com-bodega" class="form-select" name="id_bodega" data-select2-url="{{ route($ruta.'.opciones', 'bodegas') }}" data-select2-min="0" data-select2-placeholder="Sin movimiento de inventario">
                                    <option value="">Sin movimiento de inventario</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label" for="com-observacion">Observación</label>
                            <textarea id="com-observacion" name="observacion" class="form-control" rows="2" maxlength="500">{{ old('observacion') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4 mb-2">
                        <h2 class="h6 mb-0">{{ $retencion ? 'Detalle de retenciones' : 'Detalle del documento' }}</h2>
                        <button type="button" data-comercial-agregar class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Agregar línea</button>
                    </div>
                    @if ($seccion === 'documentos')
                        <p class="form-text">Selecciona una bodega si incluyes productos de inventario. Para servicios, escribe el concepto y deja el producto vacío.</p>
                    @endif
                    <div class="d-grid gap-2" data-comercial-lineas>
                        @foreach (array_values((array) old('lineas', [[]])) as $indice => $linea)
                            @include('comercial::linea', ['indice' => $indice, 'linea' => is_array($linea) ? $linea : []])
                        @endforeach
                    </div>
                    <template data-comercial-plantilla>@include('comercial::linea', ['indice' => '__INDICE__', 'linea' => []])</template>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-close>Cancelar</a>
                    <button class="btn btn-primary">Registrar documento</button>
                </div>
            </form>
        </div>
    </div>
@endsection
