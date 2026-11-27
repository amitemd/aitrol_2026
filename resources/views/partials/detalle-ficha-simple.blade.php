@extends('layouts.adminlte')

@section('title', $titulo)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">{{ $titulo }}</h1>
            <div class="card-body"><dl class="row mb-0">
                @foreach ($campos as $etiqueta => $valor)
                    @continue($valor === null || $valor === '')
                    <dt class="col-sm-5">{{ $etiqueta }}</dt><dd class="col-sm-7">{{ $valor }}</dd>
                @endforeach
            </dl></div>
            <div class="card-footer text-end"><a href="{{ $volver }}" class="btn btn-outline-secondary" data-ajax-close>Cerrar</a></div>
        </section>
    </div></div>
@endsection
