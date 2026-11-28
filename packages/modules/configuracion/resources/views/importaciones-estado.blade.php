<section class="card" @if ($tareas['activas']->isNotEmpty()) data-importaciones-activas @endif>
    <div class="card-header">
        <h2 class="h5 mb-0">Historial de importaciones</h2>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col">Archivo</th>
                    <th scope="col">Estado y avance</th>
                    <th scope="col">Tiempo restante</th>
                    <th scope="col">Filas nuevas / existentes / omitidas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tareas['activas']->concat($tareas['historial']) as $tarea)
                    @php
                        $porcentaje = $tarea->total > 0 ? min(100, (int) floor($tarea->procesadas * 100 / $tarea->total)) : 0;
                        $etiqueta = match ($tarea->estado) {
                            'pendiente' => 'En cola',
                            'preparando' => 'Leyendo el Excel',
                            'procesando' => 'Importando',
                            'completada' => 'Completada',
                            'fallida' => 'Interrumpida',
                        };
                        $filasEnIntento = $tarea->procesadas - $tarea->procesadas_inicio;
                        $segundos = $tarea->estado === 'procesando' && $filasEnIntento > 0
                            ? (int) ceil(max(1, strtotime($tarea->updated_at) - strtotime($tarea->inicio_progreso))
                                * max(0, $tarea->total - $tarea->procesadas) / $filasEnIntento)
                            : null;
                    @endphp
                    <tr>
                        <th scope="row">
                            {{ $tarea->archivo }}
                            <small class="d-block fw-normal text-body-secondary">{{ $tarea->created_at }}</small>
                        </th>
                        <td class="importacion-avance">
                            <div class="d-flex justify-content-between gap-2">
                                <span>{{ $etiqueta }}</span>
                                @if ($tarea->total > 0)
                                    <span class="text-nowrap fw-semibold">{{ $porcentaje }} %</span>
                                @endif
                            </div>
                            @if ($tarea->total > 0 || in_array($tarea->estado, ['pendiente', 'preparando', 'procesando'], true))
                                <progress @class([
                                    'importacion-barra w-100 d-block my-1',
                                    'text-success' => $tarea->estado === 'completada',
                                    'text-danger' => $tarea->estado === 'fallida',
                                ])
                                    @if ($tarea->total > 0) value="{{ $tarea->procesadas }}" max="{{ $tarea->total }}" @endif
                                    aria-label="{{ $etiqueta }}: {{ $tarea->archivo }}"></progress>
                            @endif
                            @if ($tarea->total > 0)
                                <small class="text-body-secondary">{{ number_format($tarea->procesadas, 0, ',', '.') }} de {{ number_format($tarea->total, 0, ',', '.') }} filas procesadas</small>
                            @endif
                            @if ($tarea->error)
                                <small class="d-block text-danger">{{ $tarea->error }}</small>
                            @endif
                        </td>
                        <td>
                            @if ($segundos !== null)
                                ~{{ $segundos >= 60 ? ceil($segundos / 60).' min' : $segundos.' s' }}
                            @elseif ($tarea->estado === 'pendiente')
                                Esperando a que se procese
                            @elseif (in_array($tarea->estado, ['preparando', 'procesando'], true))
                                Calculando
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $tarea->importadas }} / {{ $tarea->existentes }} / {{ $tarea->omitidas }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-body-secondary">Todavía no hay importaciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
