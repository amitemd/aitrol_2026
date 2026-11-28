@extends('layouts.adminlte')

@php($titulo = $parametro ? 'Editar tarifa' : 'Nueva tarifa')
@section('title', $titulo)
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form class="card" method="POST" action="{{ $parametro ? route('configuracion.empresas.parametros.update', $parametro->id) : route('configuracion.empresas.parametros.store') }}" data-ajax-form>
                @csrf
                @if ($parametro) @method('PUT') @endif
                <h1 class="card-header fs-5">{{ $titulo }}</h1>
                @if ($errors->any())
                    <ul class="alert alert-danger mb-0" role="alert">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                @endif
                <div class="card-body">
                    <section class="ficha-bloque">
                        @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Tarifa y vigencia', 'iconoBloque' => 'bi-percent'])
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fiscal-concepto" class="form-label">Concepto</label>
                                <select id="fiscal-concepto" class="form-select" name="concepto" required>
                                    @foreach ($conceptos as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected(old('concepto', $parametro?->concepto) === $codigo)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fiscal-codigo" class="form-label">Código de tarifa</label>
                                <input id="fiscal-codigo" class="form-control" name="codigo" maxlength="30" value="{{ old('codigo', $parametro?->codigo) }}" required>
                            </div>
                            <div class="col-12">
                                <label for="fiscal-nombre" class="form-label">{{ __('Descripción') }}</label>
                                <input id="fiscal-nombre" class="form-control" name="nombre" maxlength="100" value="{{ old('nombre', $parametro?->nombre) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fiscal-unidad" class="form-label">Unidad de cálculo</label>
                                <select id="fiscal-unidad" class="form-select" name="unidad" required>
                                    <option value="porcentaje">Porcentaje</option>
                                    <option value="usd-unidad" @selected(old('unidad', $parametro?->unidad) === 'usd-unidad')>USD por unidad</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fiscal-valor" class="form-label">Tarifa</label>
                                <input id="fiscal-valor" class="form-control" name="valor" type="number" min="0" step="0.000001" value="{{ old('valor', $parametro?->valor) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="fiscal-vigencia" class="form-label">{{ __('Vigente desde') }}</label>
                                <input id="fiscal-vigencia" class="form-control" name="vigente_desde" type="date" value="{{ old('vigente_desde', $parametro?->vigente_desde) }}" required>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="card-footer ficha-acciones d-flex flex-wrap align-items-center gap-2">
                    @include('partials.interruptor-activo', ['activo' => $parametro?->activo ?? true])
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('configuracion.empresas.parametros') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                        <button class="btn btn-primary" type="submit">Guardar tarifa</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
