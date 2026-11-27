@extends('layouts.adminlte')

@section('title', 'Bitácora | Auditoría')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Bitácora', 'subtitulo' => 'Auditoría', 'icono' => 'bi-shield-check',
    ])

    <div class="app-content">
        <div class="container-fluid">
            <div class="card clientes-directorio" data-ajax-list>
                <form method="GET" action="{{ route('auditoria.logs.index') }}" data-ajax-search
                    class="row g-2 align-items-end m-0 p-3 border-bottom">
                    <div class="col-12 col-sm-4 col-lg-3">
                        <label class="form-label" for="auditoria-nivel">Nivel</label>
                        <select id="auditoria-nivel" name="nivel" class="form-select">
                            <option value="">Todos los niveles</option>
                            @foreach ($niveles as $valor => $texto)
                                <option value="{{ $valor }}" @selected($valor === $nivel)>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->is_admin_global)
                        <div class="col-12 col-sm-4 col-lg-3">
                            <label class="form-label" for="auditoria-ambito">Ámbito</label>
                            <select id="auditoria-ambito" name="ambito" class="form-select">
                                <option value="">Mi empresa</option>
                                <option value="sistema" @selected($global)>Sistema</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-auto">
                        <button class="btn btn-outline-primary"><i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtrar</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-active">
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Nivel</th>
                                <th scope="col">Evento</th>
                                <th scope="col">Ruta</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Solicitud</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($eventos as $evento)
                                @php
                                    [$clase, $icono] = match ($evento->nivel) {
                                        'error' => ['danger', 'bi-exclamation-octagon'],
                                        'warning' => ['warning', 'bi-exclamation-triangle'],
                                        default => ['info', 'bi-info-circle'],
                                    };
                                @endphp
                                <tr>
                                    <td class="text-nowrap">{{ $evento->created_at }}</td>
                                    <td><span class="badge text-{{ $clase }}-emphasis bg-{{ $clase }}-subtle">
                                        <i class="bi {{ $icono }} me-1" aria-hidden="true"></i>{{ $niveles[$evento->nivel] }}
                                    </span></td>
                                    <td class="text-break">
                                        <div class="fw-semibold">{{ $evento->evento }}</div>
                                        <div class="small text-body-secondary">{{ $evento->origen === 'http' ? 'HTTP '.$evento->estado_http : 'Aplicación' }}</div>
                                    </td>
                                    <td class="text-break">
                                        <div>{{ $evento->ruta ?? '—' }}</div>
                                        <div class="small text-body-secondary">{{ $evento->metodo }}</div>
                                    </td>
                                    <td>{{ $evento->id_usuario ? '#'.$evento->id_usuario : '—' }}</td>
                                    <td class="font-monospace small text-break">{{ $evento->solicitud }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-body-secondary py-5">No hay eventos para este filtro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($eventos->hasPages())
                    <div class="px-3 pt-3 border-top">{{ $eventos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
