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
                            @foreach (['codigo' => ['Código interno', 30], 'nombre' => ['Nombre', 120], 'direccion' => ['Dirección', 255]] as $campo => [$etiqueta, $limite])
                                <div class="{{ $campo === 'direccion' ? 'col-12' : 'col-md-6' }}">
                                    <label for="sucursal-{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
                                    <input id="sucursal-{{ $campo }}" class="form-control @error($campo) is-invalid @enderror" name="{{ $campo }}" maxlength="{{ $limite }}"
                                        value="{{ old($campo, $sucursal?->{$campo}) }}" @required($campo !== 'direccion') @readonly($campo === 'codigo' && $sucursal?->codigo === 'PRINCIPAL')>
                                    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                    <section class="ficha-bloque mt-3">
                        @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Facturación electrónica', 'iconoBloque' => 'bi-receipt'])
                        <div class="row g-3">
                            @foreach (['establecimiento_sri' => 'Establecimiento SRI', 'punto_emision_sri' => 'Punto de emisión SRI'] as $campo => $etiqueta)
                                <div class="col-md-6">
                                    <label for="sucursal-{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                                    <input id="sucursal-{{ $campo }}" name="{{ $campo }}" class="form-control @error($campo) is-invalid @enderror"
                                        inputmode="numeric" maxlength="3" pattern="(?!000)[0-9]{3}" autocomplete="off"
                                        value="{{ old($campo, $sucursal?->{$campo}) }}" aria-describedby="sucursal-serie-ayuda">
                                    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                        <p id="sucursal-serie-ayuda" class="form-text mb-0 mt-2">Usa los códigos asignados a esta sucursal. Ambos campos requieren tres dígitos para facturar.</p>
                    </section>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('configuracion.sucursales.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button class="btn btn-primary">Guardar sucursal</button>
                </div>
            </form>
        </div>
    </div>
@endsection
