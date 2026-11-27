@extends('layouts.adminlte')

@php
    use Aitrol\Rrhh\Application\DatosRrhh;
    $seccion = 'roles';
@endphp
@section('title', 'Rol de pago | Recursos humanos')
@section('content')
    @include('rrhh::cabecera')
    <div class="app-content">
        <div class="container-fluid">
            <section class="card" data-ajax-form>
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <h2 class="card-title me-auto">{{ $rol->empleado }} · {{ substr($nomina->periodo, 0, 7) }}</h2>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rrhh.roles.index') }}" data-ajax-close>{{ __('Cerrar') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th scope="col">Concepto</th><th scope="col">{{ __('Tipo') }}</th><th scope="col" class="text-end">{{ __('Importe') }}</th></tr></thead>
                        <tbody>
                            @foreach ($lineas as $linea)
                                <tr><td>{{ $linea['concepto'] }}</td><td>{{ ucfirst($linea['tipo']) }}</td><td class="text-end">USD {{ DatosRrhh::importe($linea['importe']) }}</td></tr>
                            @endforeach
                        </tbody>
                        <tfoot><tr><th scope="row" colspan="2">Neto</th><td class="text-end fw-semibold">USD {{ DatosRrhh::importe($rol->neto) }}</td></tr></tfoot>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
