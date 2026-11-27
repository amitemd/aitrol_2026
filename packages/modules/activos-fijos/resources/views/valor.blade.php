@if ($campo === 'estado')
    <span @class(['badge rounded-pill', 'text-bg-success' => in_array($valor, ['activo', 'completada'], true), 'text-bg-warning' => $valor === 'pendiente', 'text-bg-secondary' => in_array($valor, ['baja', 'cancelada'], true), 'text-bg-info' => $valor === 'en_proceso'])>
        {{ config('activos-fijos.estados_orden.'.$valor, $valor === 'baja' ? 'Dado de baja' : 'Activo') }}
    </span>
@elseif ($campo === 'activo' && is_bool($valor))
    <span @class(['badge rounded-pill', 'text-bg-success' => $valor, 'text-bg-secondary' => !$valor])>{{ $valor ? 'Activo' : 'Inactivo' }}</span>
@elseif ($campo === 'tipo' && $seccion === 'movimientos')
    {{ config('activos-fijos.movimientos.'.$valor) }}
@elseif ($campo === 'tipo' && $seccion === 'ordenes')
    {{ ucfirst($valor) }}
@elseif ($campo === 'periodo')
    {{ substr($valor, 0, 7) }}
@elseif ($campo === 'vida_util_meses')
    {{ $valor === null ? '—' : ((int) $valor === 0 ? 'No depreciable' : $valor.' meses') }}
@else
    {{ $valor ?? '—' }}
@endif
