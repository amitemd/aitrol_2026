@extends('layouts.adminlte')

@section('title', 'Sucursales')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Sucursales', 'subtitulo' => 'Configuración', 'icono' => 'bi-diagram-3'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <p class="alert alert-success" role="status">{{ session('estado') }}</p>
            @endif
            @if ($errors->any())
                <ul class="alert alert-danger ps-4" role="alert">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            @endif
            <div class="row g-3">
                <div class="col-lg-7">
                    <section class="card">
                        <div class="card-header"><h2 class="card-title">Sucursales de la empresa</h2></div>
                        <div class="list-group list-group-flush">
                            @foreach ($sucursales as $sucursal)
                                <details class="list-group-item">
                                    <summary>
                                        <strong>{{ $sucursal->nombre }}</strong>
                                        <span class="text-body-secondary ms-2">{{ $sucursal->codigo }}</span>
                                        @if ($sucursal->direccion)
                                            <span class="small text-body-secondary d-block">{{ $sucursal->direccion }}</span>
                                        @endif
                                    </summary>
                                    <form method="POST" class="row g-2 mt-2" action="{{ route('configuracion.sucursales.update', $sucursal->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="registro" value="{{ $sucursal->id }}">
                                        <label class="col-sm-4">Código
                                            <input class="form-control" name="codigo" maxlength="30" required
                                                value="{{ (int) old('registro') === $sucursal->id ? old('codigo') : $sucursal->codigo }}"
                                                @readonly($sucursal->codigo === 'PRINCIPAL')>
                                        </label>
                                        <label class="col-sm-8">Nombre
                                            <input class="form-control" name="nombre" maxlength="120" required
                                                value="{{ (int) old('registro') === $sucursal->id ? old('nombre') : $sucursal->nombre }}">
                                        </label>
                                        <label class="col-12">Dirección
                                            <input class="form-control" name="direccion" maxlength="255"
                                                value="{{ (int) old('registro') === $sucursal->id ? old('direccion') : $sucursal->direccion }}">
                                        </label>
                                        <div class="col-12 text-end"><button class="btn btn-outline-primary" type="submit">Guardar cambios</button></div>
                                    </form>
                                </details>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="col-lg-5">
                    <form method="POST" class="card" action="{{ route('configuracion.sucursales.store') }}">
                        @csrf
                        <div class="card-header"><h2 class="card-title">Nueva sucursal</h2></div>
                        <div class="card-body">
                            <label for="sucursal-codigo" class="form-label">Código</label>
                            <input class="form-control mb-3" id="sucursal-codigo" name="codigo" maxlength="30" value="{{ old('registro') ? '' : old('codigo') }}" required>
                            <label for="sucursal-nombre" class="form-label">Nombre</label>
                            <input class="form-control mb-3" id="sucursal-nombre" name="nombre" maxlength="120" value="{{ old('registro') ? '' : old('nombre') }}" required>
                            <label for="sucursal-direccion" class="form-label">Dirección</label>
                            <input class="form-control" id="sucursal-direccion" name="direccion" maxlength="255" value="{{ old('registro') ? '' : old('direccion') }}">
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg" aria-hidden="true"></i> Crear sucursal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
