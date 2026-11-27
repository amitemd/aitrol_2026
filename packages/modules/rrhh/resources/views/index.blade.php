@extends('layouts.adminlte')

@php
    use Aitrol\Rrhh\Application\DatosRrhh;
    $titulo = config('rrhh.secciones.'.$seccion.'.0');
    $ruta = 'rrhh.'.$seccion;
    $permiso = $ruta.'.index';
    $editable = ! in_array($seccion, ['nomina', 'roles'], true);
    $procesable = $flujo !== [] && $seccion !== 'organizacion';
    $estados = ($flujo['estados'] ?? []) + ['pendiente' => 'Pendiente', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado', 'borrador' => 'Borrador', 'cerrada' => 'Cerrada', 'pagada' => 'Pagada'];
    $alta = match ($seccion) {
        'empleados' => 'Nuevo empleado',
        'contratos' => 'Nuevo contrato',
        'nomina' => 'Preparar nómina',
        default => 'Nuevo registro',
    };
@endphp

@section('title', $titulo.' | Recursos humanos')
@section('content')
    @include('rrhh::cabecera', [
        'crearUrl' => $seccion !== 'roles' && Gate::allows('permiso', [$permiso, 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $alta,
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => config('rrhh.secciones.'.$seccion.'.1')])
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <section class="card clientes-directorio" data-ajax-list>
                <form class="card-body d-flex flex-wrap align-items-end gap-2 border-bottom" action="{{ route($permiso) }}" method="GET" data-ajax-search>
                    <div class="flex-grow-1">
                        <label for="rrhh-busqueda" class="form-label">Buscar en {{ mb_strtolower($titulo) }}</label>
                        <input id="rrhh-busqueda" name="q" class="form-control" type="search" maxlength="100" value="{{ $filtros['q'] ?? '' }}">
                    </div>
                    <button class="btn btn-outline-primary" type="submit">{{ __('Buscar') }}</button>
                    @if (! empty($filtros['q']))
                        <a class="btn btn-outline-secondary" href="{{ route($permiso) }}">{{ __('Limpiar') }}</a>
                    @endif
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">{{ $seccion === 'nomina' ? 'Período' : ($seccion === 'contratos' ? 'Empleado' : 'Nombre o concepto') }}</th>
                                <th scope="col">{{ __('Información') }}</th>
                                <th scope="col">{{ __('Estado') }}</th>
                                <th scope="col" class="text-end">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $registro)
                                <tr>
                                    <th scope="row">
                                        {{ $registro->titulo ?? $registro->nombre ?? $registro->empleado ?? substr($registro->periodo ?? '', 0, 7) }}
                                        @if ($registro->detalle ?? null)
                                            <div class="small fw-normal text-body-secondary text-break">{{ Str::limit($registro->detalle, 90) }}</div>
                                        @endif
                                    </th>
                                    <td>
                                        @switch($seccion)
                                            @case('empleados')
                                                {{ $registro->codigo }} · Ingreso {{ $registro->ingreso }}
                                                @break
                                            @case('contratos')
                                                {{ $registro->tipo }} · {{ $registro->inicio }}{{ $registro->fin ? ' a '.$registro->fin : '' }}
                                                <div class="small text-body-secondary">USD {{ DatosRrhh::importe($registro->sueldo) }} mensuales</div>
                                                @break
                                            @case('roles')
                                                {{ substr($registro->periodo, 0, 7) }} · Neto USD {{ DatosRrhh::importe($registro->neto) }}
                                                @break
                                            @case('nomina')
                                                {{ $registro->referencia_pago ? 'Pago '.$registro->referencia_pago : 'Nómina mensual' }}
                                                @break
                                            @default
                                                {{ $flujo['tipos'][$registro->tipo] }}{{ $registro->empleado ? ' · '.$registro->empleado : '' }}
                                                @if ($registro->inicio)
                                                    <div class="text-body-secondary small">{{ $registro->inicio }}{{ $registro->fin ? ' a '.$registro->fin : '' }}</div>
                                                @endif
                                                @if ($registro->importe !== null)
                                                    <div class="small">USD {{ DatosRrhh::importe($registro->importe) }}</div>
                                                @endif
                                                @if ($registro->cantidad !== null)
                                                    <div class="small">{{ $flujo['cantidad'] }} {{ $registro->cantidad }}</div>
                                                @endif
                                                @if ($seccion === 'prestamos' && $registro->estado === 'aprobado')
                                                    <div class="small">Saldo USD {{ DatosRrhh::importe($registro->importe - $registro->descontado) }}</div>
                                                @endif
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($seccion === 'empleados')
                                            {{ $registro->salida && $registro->salida < today()->toDateString() ? 'Desvinculado' : ($registro->ingreso > today()->toDateString() ? 'Por ingresar' : 'Activo') }}
                                        @elseif ($seccion === 'contratos')
                                            {{ $registro->fin && $registro->fin < today()->toDateString() ? 'Vencido' : ($registro->inicio > today()->toDateString() ? 'Programado' : 'Vigente') }}
                                        @elseif ($seccion === 'organizacion')
                                            <span class="badge {{ $registro->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $registro->activo ? 'Activo' : 'Inactivo' }}</span>
                                        @else
                                            <span class="badge {{ in_array($registro->estado, ['aprobado', 'pagada'], true) ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $estados[$registro->estado] }}</span>
                                            @if ($seccion === 'conceptos')
                                                <span class="badge {{ $registro->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $registro->activo ? 'Activo' : 'Inactivo' }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (! $editable)
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route($ruta.'.show', $registro->id) }}" data-ajax-open="{{ $titulo }}">{{ __('Ver') }}</a>
                                        @else
                                            <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route($ruta.'.show', $registro->id) }}" data-ajax-open="Detalle de {{ mb_strtolower($titulo) }}">{{ __('Ver') }}</a>
                                                @if (Gate::allows('permiso', [$permiso, 'actualizar']) && (! $procesable || $registro->estado === 'pendiente' || ($seccion === 'conceptos' && $registro->estado === 'aprobado')))
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route($ruta.'.edit', $registro->id) }}" data-ajax-open="{{ __('Editar registro') }}">{{ $seccion === 'conceptos' && $registro->estado === 'aprobado' ? 'Cambiar estado' : 'Editar' }}</a>
                                                @endif
                                                @if ($procesable && $registro->estado === 'pendiente' && Gate::allows('permiso', [$permiso, 'actualizar']))
                                                    <form method="POST" action="{{ route($ruta.'.estado', $registro->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-outline-success" name="estado" value="aprobado">{{ isset($flujo['estados']) ? $estados['aprobado'] : 'Aprobar' }}</button>
                                                        <button class="btn btn-sm btn-outline-danger" name="estado" value="rechazado">{{ isset($flujo['estados']) ? $estados['rechazado'] : 'Rechazar' }}</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="py-5 text-center text-body-secondary" colspan="4">Aún no hay registros en esta vista.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($registros->hasPages())
                    <div class="card-footer">{{ $registros->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
