@extends('layouts.adminlte')

@section('title', 'Ubicaciones')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Ubicaciones', 'subtitulo' => 'Configuración', 'icono' => 'bi-geo-alt'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado')) <p class="alert alert-success" role="status">{{ session('estado') }}</p> @endif
            @if ($errors->any())
                <ul class="alert alert-danger ps-4" role="alert">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @endif
            <div class="row g-3">
                <div class="col-lg-6">
                    <section class="card">
                        <div class="card-header"><h2 class="card-title">Explorar catálogo</h2></div>
                        <div class="card-body">
                            @include('partials.ubicacion-selects')
                            <a class="small" href="https://www.geonames.org/" rel="noopener noreferrer" target="_blank">Datos geográficos de GeoNames</a>
                        </div>
                    </section>
                </div>
                <div class="col-lg-6">
                    <form method="POST" class="card" action="{{ route('configuracion.ubicaciones.store') }}">
                        @csrf
                        <div class="card-header"><h2 class="card-title">Agregar ubicación</h2></div>
                        <div class="card-body" data-ubicacion data-url="{{ route('configuracion.ubicaciones.opciones') }}">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select mb-3" name="tipo" id="tipo" data-ubicacion-tipo>
                                @foreach (['pais' => 'País', 'provincia' => 'Provincia o estado', 'ciudad' => 'Ciudad'] as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('tipo') === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            <label for="nombre" class="form-label">Nombre</label>
                            <input class="form-control mb-3" id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="120">
                            <div data-ubicacion-campo="pais">
                                <label for="codigo" class="form-label">Código del país</label>
                                <input class="form-control" id="codigo" name="codigo" placeholder="EC" value="{{ old('codigo') }}" maxlength="2">
                            </div>
                            <div data-ubicacion-campo="provincia" class="mb-3">
                                <label for="id_pais" class="form-label">País</label>
                                <select class="form-select" id="id_pais" name="id_pais" data-ubicacion-pais data-selected="{{ old('id_pais') }}"><option value="">Seleccionar</option></select>
                            </div>
                            <div data-ubicacion-campo="ciudad">
                                <label for="id_provincia" class="form-label">Provincia o estado</label>
                                <select class="form-select" id="id_provincia" name="id_provincia" data-ubicacion-provincia><option value="">Seleccionar</option></select>
                            </div>
                            <p class="form-text" data-ubicacion-estado role="status"></p>
                        </div>
                        <div class="card-footer text-end"><button class="btn btn-primary">Guardar ubicación</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
