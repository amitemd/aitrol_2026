@extends('layouts.adminlte')

@php
    use Aitrol\Rrhh\Application\DatosRrhh;
    $seccion = 'nomina';
    $estados = ['borrador' => 'Borrador', 'cerrada' => 'Cerrada', 'pagada' => 'Pagada'];
@endphp
@section('title', 'Nómina '.substr($nomina->periodo, 0, 7))
@section('content')
    @include('rrhh::cabecera')
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <p class="alert alert-danger" role="alert">{{ $errors->first() }}</p>
            @endif
            <section class="card" data-ajax-form>
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <h2 class="card-title me-auto">Nómina {{ substr($nomina->periodo, 0, 7) }} · {{ $estados[$nomina->estado] }}</h2>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rrhh.nomina.index') }}" data-ajax-close>Cerrar</a>
                    @if ($nomina->estado !== 'borrador')
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('rrhh.nomina.exportar', $nomina->id) }}">Exportar CSV</a>
                    @endif
                </div>
                @if ($nomina->estado === 'borrador')
                    <div class="card-body">
                        <p>Verifica los contratos, las deducciones y los beneficios del mes. El cierre calcula los roles y guarda su desglose.</p>
                        @can('permiso', ['rrhh.nomina.index', 'actualizar'])
                            <form method="POST" action="{{ route('rrhh.nomina.estado', $nomina->id) }}" data-ajax-form data-accion-confirmar="El cierre emitirá los roles de pago y bloqueará los cambios en las novedades del período.">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="cerrar">
                                <button type="submit" class="btn btn-primary">Cerrar y emitir roles</button>
                            </form>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th scope="col">Empleado</th><th scope="col">Ingresos</th><th scope="col">Descuentos</th><th scope="col">Neto</th></tr></thead>
                            <tbody>
                                @foreach ($roles as $rol)
                                    <tr>
                                        <th scope="row">{{ $rol->empleado }}</th>
                                        <td>USD {{ DatosRrhh::importe($rol->ingresos) }}</td>
                                        <td>USD {{ DatosRrhh::importe($rol->descuentos) }}</td>
                                        <td>USD {{ DatosRrhh::importe($rol->neto) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($roles->hasPages())
                        <div class="card-footer" data-ajax-modal-page>{{ $roles->links() }}</div>
                    @endif
                    @if ($nomina->estado === 'cerrada')
                        @can('permiso', ['rrhh.nomina.index', 'actualizar'])
                            <form method="POST" action="{{ route('rrhh.nomina.estado', $nomina->id) }}" class="card-footer d-flex flex-wrap gap-2 align-items-end" data-ajax-form>
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="pagar">
                                <div>
                                    <label class="form-label" for="rrhh-pago">Referencia del pago efectuado</label>
                                    <input class="form-control" id="rrhh-pago" name="referencia" maxlength="80" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Registrar pago</button>
                            </form>
                        @endcan
                    @else
                        <p class="card-footer mb-0">Referencia del pago {{ $nomina->referencia_pago }}</p>
                    @endif
                @endif
            </section>
        </div>
    </div>
@endsection
