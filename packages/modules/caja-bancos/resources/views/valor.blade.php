@if ($campo === 'activo')
    <span @class(['badge rounded-pill', 'text-bg-success' => $valor, 'text-bg-secondary' => !$valor])>{{ $valor ? 'Activo' : 'Inactivo' }}</span>
@elseif ($campo === 'tipo')
    {{ config('caja-bancos.tipos.'.$valor, $valor === 'banco' ? 'Banco' : 'Caja') }}
@elseif ($campo === 'id_asiento')
    <span @class(['badge rounded-pill', 'text-bg-success' => $valor, 'text-bg-warning' => !$valor])>{{ $valor ? 'Conciliado' : 'Pendiente' }}</span>
@elseif ($campo === 'id_reversion')
    <span @class(['badge rounded-pill', 'text-bg-secondary' => $valor, 'text-bg-success' => !$valor])>{{ $valor ? 'Revertido' : 'Registrado' }}</span>
@elseif ($campo === 'cerrado_at' && $valor === null)
    <span class="badge rounded-pill text-bg-success">Abierta</span>
@else
    {{ $valor ?? '—' }}
@endif
