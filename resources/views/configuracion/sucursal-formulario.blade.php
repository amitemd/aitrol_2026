@extends('layouts.adminlte')

@section('title', $sucursal ? 'Editar sucursal' : 'Nueva sucursal')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form class="card" method="POST" data-ajax-form action="{{ $sucursal ? route('configuracion.sucursales.update', $sucursal->id) : route('configuracion.sucursales.store') }}">
                @csrf
                @if ($sucursal) @method('PUT') @endif
                <h1 class="card-header fs-5">{{ $sucursal ? 'Editar sucursal' : 'Nueva sucursal' }}</h1>
                <div class="card-body">
                    <section class="ficha-bloque">
                        @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identificación de la sucursal', 'iconoBloque' => 'bi-diagram-3'])
                        <div class="row g-3">
                            @foreach (['codigo' => ['Código', 30], 'nombre' => ['Nombre', 120], 'direccion' => ['Dirección', 255]] as $campo => [$etiqueta, $limite])
                                <div class="{{ $campo === 'direccion' ? 'col-12' : 'col-md-6' }}">
                                    <label for="sucursal-{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                                    <input id="sucursal-{{ $campo }}" class="form-control @error($campo) is-invalid @enderror" name="{{ $campo }}" maxlength="{{ $limite }}"
                                        value="{{ old($campo, $sucursal?->{$campo}) }}" @required($campo !== 'direccion') @readonly($campo === 'codigo' && $sucursal?->codigo === 'PRINCIPAL')>
                                    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('configuracion.sucursales.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                    <button class="btn btn-primary">Guardar sucursal</button>
                </div>
            </form>
        </div>
    </div>
@endsection
