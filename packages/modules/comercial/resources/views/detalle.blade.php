@extends('layouts.adminlte')

@section('title', 'Documento '.$documento['numero'])
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <section class="card" data-ajax-form>
                <h1 class="card-header fs-5">Documento {{ $documento['numero'] }}</h1>
                <div class="card-body">
                    @include('comercial::contenido-documento')
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route($modulo.'.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a>
                </div>
            </section>
        </div>
    </div>
@endsection
