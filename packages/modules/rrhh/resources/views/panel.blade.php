@extends('layouts.adminlte')

@section('title', config('rrhh.secciones.'.$seccion.'.0').' | Recursos humanos')
@section('content')
    @include('rrhh::cabecera')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row g-3">
                @forelse ($indicadores as $destino => [$titulo, $valor])
                    <div class="col-12 col-sm-6 col-xl-4">
                        <section class="card h-100">
                            <div class="card-body">
                                <h2 class="fs-6 text-body-secondary">{{ $titulo }}</h2>
                                <p class="fs-2 fw-semibold">{{ $valor }}</p>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('rrhh.'.$destino.'.index') }}">{{ config('rrhh.secciones.'.$destino.'.0') }}</a>
                            </div>
                        </section>
                    </div>
                @empty
                    <div class="col-12"><p class="alert alert-info">Los indicadores se muestran según tus permisos de acceso.</p></div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
