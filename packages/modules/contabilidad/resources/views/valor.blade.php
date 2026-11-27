@switch($campo)
    @case('clase'){{ config('contabilidad.clases.'.$valor, $valor) }}@break
    @case('flujo'){{ config('contabilidad.flujos.'.$valor, '—') }}@break
    @case('activo')<span @class(['badge rounded-pill', 'text-bg-success' => $valor, 'text-bg-secondary' => !$valor])>{{ $valor ? 'Activo' : 'Inactivo' }}</span>@break
    @case('auxiliar'){{ $valor ? 'Auxiliar' : 'Grupo' }}@break
    @case('efectivo'){{ $valor ? 'Sí' : '—' }}@break
    @case('id_reversion')<span @class(['badge rounded-pill', 'text-bg-secondary' => $valor, 'text-bg-success' => !$valor])>{{ $valor ? 'Revertido' : 'Registrado' }}</span>@break
    @case('cerrado_at')<span @class(['badge rounded-pill', 'text-bg-secondary' => $valor, 'text-bg-success' => !$valor])>{{ $valor ? 'Cerrado' : 'Abierto' }}</span>@break
    @case('origen')
        {{ $valor ? match (explode('.', $valor)[0]) {
            'comercial' => 'Ventas / Compras', 'tesoreria' => 'Caja y bancos', 'inventario' => 'Inventarios', 'activos' => 'Activos fijos', default => $valor,
        } : 'Manual' }}
        @break
    @default{{ $valor ?? '—' }}
@endswitch
