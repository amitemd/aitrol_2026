@extends('layouts.adminlte')

@section('title', 'Parámetros fiscales')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Parámetros fiscales', 'subtitulo' => 'Configuración de empresa', 'icono' => 'bi-percent',
        'crearUrl' => Gate::allows('permiso', ['empresas', 'actualizar']) ? route('configuracion.empresas.parametros.create') : null,
        'crearTexto' => 'Nueva tarifa',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => 'bi-percent'])
            <section class="card clientes-directorio" data-ajax-list>
                <div class="card-body">
                    <p class="mb-0">Registra una nueva vigencia para cada cambio de tarifa. Un mismo impuesto puede tener varios códigos con tarifas distintas.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th scope="col">Concepto</th><th scope="col">Código y descripción</th><th scope="col">Tarifa</th><th scope="col">{{ __('Vigente desde') }}</th></tr></thead>
                        <tbody>
                            @forelse ($parametros as $parametro)
                                <tr>
                                    <th scope="row">{{ $conceptos[$parametro->concepto] }}</th>
                                    <td>{{ $parametro->codigo }} · {{ $parametro->nombre }}</td>
                                    <td>{{ rtrim(rtrim(number_format((float) $parametro->valor, 6, '.', ''), '0'), '.') }} {{ $parametro->unidad === 'porcentaje' ? '%' : 'USD por unidad' }}</td>
                                    <td><time datetime="{{ $parametro->vigente_desde }}">{{ $parametro->vigente_desde }}</time></td>
                                </tr>
                            @empty
                                <tr><td class="text-center text-body-secondary py-5" colspan="4">Aún no hay tarifas parametrizadas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($parametros->hasPages())<div class="card-footer">{{ $parametros->links() }}</div>@endif
            </section>
        </div>
    </div>
@endsection
