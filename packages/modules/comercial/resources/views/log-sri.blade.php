@extends('layouts.adminlte')

@section('title', 'Autorización SRI · '.$factura->numero)

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Autorización SRI', 'subtitulo' => $factura->numero, 'icono' => 'bi-clock-history'])
    <div class="app-content">
        <div class="container-fluid">
            <section class="card">
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-sm-4">Clave de acceso</dt><dd class="col-sm-8 font-monospace text-break">{{ $identidad['clave'] }}</dd>
                        <dt class="col-sm-4">Ambiente</dt><dd class="col-sm-8">{{ $identidad['ambiente'] === '2' ? 'Producción' : 'Pruebas' }}</dd>
                        <dt class="col-sm-4">Registro en Aitrol</dt><dd class="col-sm-8">{{ $factura->created_at ?? 'Sin fecha registrada' }}</dd>
                    </dl>
                    @include('comercial::estado-sri', ['documentoSri' => ['id' => $factura->id, 'clave_acceso' => $identidad['clave'], 'estado_sri' => $estado], 'enLogSri' => true])
                    @if ($estado !== 'AUTORIZADO' && Gate::allows('permiso', ['ventas.documentos.index', 'actualizar']))
                        <form method="POST" action="{{ route('ventas.documentos.sri.consultar', $factura->id) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-primary">Consultar estado en el SRI</button>
                        </form>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <caption class="visually-hidden">Historial de consultas al SRI</caption>
                        <thead><tr><th scope="col">Fecha y hora</th><th scope="col">Resultado</th><th scope="col">Mensajes</th></tr></thead>
                        <tbody>
                            @forelse ($consultas as $consulta)
                                <tr>
                                    <td class="text-nowrap">{{ $consulta->created_at }}</td>
                                    <td>{{ \App\Services\AutorizacionSri::ESTADOS[$consulta->estado] }}</td>
                                    <td>
                                        @forelse (json_decode($consulta->mensajes, true) ?? [] as $mensaje)
                                            <div>{{ $mensaje }}</div>
                                        @empty
                                            <span class="text-body-secondary">Sin mensajes</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-body-secondary">Todavía no se registran consultas al SRI para esta factura.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2 justify-content-end">
                    {{ $consultas->links('pagination::bootstrap-5') }}
                    <a class="btn btn-outline-secondary" href="{{ route(Gate::allows('permiso', ['ventas.documentos.index', 'leer']) ? 'ventas.documentos.index' : 'clientes.documentos.facturas') }}">Volver a las facturas</a>
                </div>
            </section>
        </div>
    </div>
@endsection
