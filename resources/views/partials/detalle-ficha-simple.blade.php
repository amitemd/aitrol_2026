@extends('layouts.adminlte')

@section('title', $titulo)
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">{{ $titulo }}</h1>
            <div class="card-body">
                <div class="row gx-3 flex-nowrap align-items-start">
                    <div class="col">
                        <dl class="row mb-0">
                            @foreach ($campos as $etiqueta => $valor)
                                @continue($valor === null || $valor === '')
                                <dt class="col-sm-5">{{ __($etiqueta) }}</dt><dd class="col-sm-7 text-break">{{ $valor }}</dd>
                            @endforeach
                        </dl>
                    </div>
                    @if (!empty($fotoUrl))
                        <div class="col-auto">
                            <img class="d-block ficha-detalle-foto" src="{{ $fotoUrl }}" alt="Imagen de {{ $campos['Nombre'] ?? $titulo }}"
                                width="144" height="144" decoding="async">
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                @isset($accionUrl)
                    <a href="{{ $accionUrl }}" class="btn btn-outline-primary" data-workspace-link="{{ $accionWorkspace }}" data-workspace-label="{{ $accionTexto }}">{{ $accionTexto }}</a>
                @endisset
                <a href="{{ $volver }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a>
            </div>
        </section>
    </div></div>
@endsection
