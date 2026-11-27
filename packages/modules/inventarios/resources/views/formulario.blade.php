@extends('layouts.adminlte')

@php
    $catalogo = in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true);
    $titulo = match ($seccion) {
        'productos' => $registro ? 'Editar producto' : 'Nuevo producto',
        'categorias' => $registro ? 'Editar categoría' : 'Nueva categoría',
        'plantillas-contables' => $registro ? 'Editar plantilla contable' : 'Nueva plantilla contable',
        'bodegas' => $registro ? 'Editar bodega' : 'Nueva bodega',
        'ajustes' => 'Nuevo ajuste',
        default => 'Nuevo movimiento',
    };
    $ruta = 'inventarios.'.$seccion;
@endphp
@section('title', $titulo)

@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form @class(['card', 'inventario-operacion' => !$catalogo]) method="POST" data-ajax-form
                @if ($seccion === 'productos') data-usuario-form data-ficha-producto @endif
                action="{{ $registro ? route($ruta.'.update', $registro['id']) : route($ruta.'.store') }}">
                @csrf
                @if ($registro)
                    @method('PUT')
                @endif
                <h1 class="card-header fs-5">{{ $titulo }}</h1>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @if ($seccion === 'productos')
                    @include('inventarios::ficha-producto')
                @else
                <div class="card-body row g-3">
                    @if ($catalogo)
                        <div class="col-sm-4">
                            <label class="form-label" for="inv-codigo">Código</label>
                            <input class="form-control" name="codigo" id="inv-codigo" maxlength="60" value="{{ old('codigo', $registro['codigo'] ?? '') }}" required autofocus>
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label" for="inv-nombre">Nombre</label>
                            <input class="form-control" name="nombre" id="inv-nombre" maxlength="{{ $seccion === 'categorias' ? 100 : 160 }}" value="{{ old('nombre', $registro['nombre'] ?? '') }}" required>
                        </div>
                        @if ($seccion === 'categorias')
                            <div class="col-12">
                                <label class="form-label" for="inv-plantilla">Plantilla contable</label>
                                <select name="id_plantilla_contable" id="inv-plantilla" class="form-select">
                                    <option value="">Sin plantilla</option>
                                    @foreach ($ficha['plantillas'] as $plantilla)
                                        @continue(!$plantilla->activo && (int) ($registro['id_plantilla_contable'] ?? 0) !== (int) $plantilla->id)
                                        <option value="{{ $plantilla->id }}" @selected((int) old('id_plantilla_contable', $registro['id_plantilla_contable'] ?? 0) === (int) $plantilla->id)>{{ $plantilla->nombre }}</option>
                                    @endforeach
                                </select>
                                <p class="form-text">Los productos de esta categoría usarán estas cuentas.</p>
                            </div>
                        @elseif ($seccion === 'plantillas-contables')
                            @include('inventarios::campos-cuentas')
                        @endif
                        <div class="col-12">
                            <label class="form-label" for="inv-descripcion">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="inv-descripcion" maxlength="500" rows="2">{{ old('descripcion', $registro['descripcion'] ?? '') }}</textarea>
                        </div>
                    @else
                        <input type="hidden" name="clave_operacion" value="{{ old('clave_operacion', (string) Str::uuid()) }}">
                        <div class="col-sm-6">
                            <label class="form-label" for="inv-tipo">Tipo</label>
                            <select class="form-select" id="inv-tipo" name="tipo" required>
                                @foreach ($tipos as $tipo => $etiqueta)
                                    <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        @foreach (['id_producto' => ['Producto', 'productos'], 'id_bodega' => ['Bodega (origen en transferencias)', 'bodegas'], 'id_bodega_destino' => ['Bodega de destino', 'bodegas']] as $campo => [$etiqueta, $origen])
                            <div @class(['col-sm-6', 'inventario-bodega-destino' => $campo === 'id_bodega_destino'])>
                                <label class="form-label" for="inv-{{ $campo }}">{{ $etiqueta }}</label>
                                <select class="form-select" name="{{ $campo }}" id="inv-{{ $campo }}" data-select2-min="0"
                                    data-select2-url="{{ route($ruta.'.opciones', ['catalogo' => $origen, 'activo' => 1]) }}" @required($campo !== 'id_bodega_destino')>
                                    <option value="">Seleccionar</option>
                                    @isset($selecciones[$campo])
                                        <option value="{{ $selecciones[$campo]->id }}" selected>{{ $selecciones[$campo]->codigo }} · {{ $selecciones[$campo]->nombre }}</option>
                                    @endisset
                                </select>
                            </div>
                        @endforeach
                        <div class="col-sm-6">
                            <label class="form-label" for="inv-cantidad">Cantidad</label>
                            <input class="form-control" type="number" id="inv-cantidad" name="cantidad" min="0.001" max="1000000" step="0.001" value="{{ old('cantidad') }}" required>
                        </div>
                        <div class="col-sm-6 inventario-costo">
                            <label class="form-label" for="inv-costo">Costo unitario</label>
                            <input class="form-control" type="number" id="inv-costo" name="costo_unitario" min="0" max="100000" step="0.0001" value="{{ old('costo_unitario') }}" aria-describedby="inv-costo-ayuda">
                            <div class="form-text" id="inv-costo-ayuda">Obligatorio en entradas. En ajustes de entrada puede conservarse el costo promedio actual.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="inv-referencia">Referencia</label>
                            <input class="form-control" name="referencia" id="inv-referencia" maxlength="120" value="{{ old('referencia') }}" placeholder="Número de documento o referencia interna">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="inv-motivo">Motivo</label>
                            <textarea class="form-control" name="motivo" id="inv-motivo" maxlength="250" rows="2" required>{{ old('motivo') }}</textarea>
                            <p class="form-text mb-0">Al guardar, se actualizará la existencia y quedará el registro en Kardex.</p>
                        </div>
                    @endif
                </div>
                @endif
                <div class="card-footer ficha-acciones d-flex flex-wrap align-items-center gap-2">
                    @if ($catalogo)
                        @include('partials.interruptor-activo', ['activo' => $registro['activo'] ?? true])
                    @endif
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route($ruta.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                        <button class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>{{ $catalogo ? 'Guardar' : 'Registrar' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
