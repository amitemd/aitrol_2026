@php
    $creadorAuditoria = data_get($filaAuditoria, 'id_usuario_creacion') ?? data_get($filaAuditoria, 'id_usuario');
    $editorAuditoria = data_get($filaAuditoria, 'id_usuario_modificacion') ?? data_get($filaAuditoria, 'id_usuario_anulacion');
    $creacionAuditoria = data_get($filaAuditoria, 'created_at');
    $edicionAuditoria = data_get($filaAuditoria, 'updated_at');
    if (! $editorAuditoria && $edicionAuditoria == $creacionAuditoria) {
        $edicionAuditoria = null;
    }
@endphp
@foreach ([[$creadorAuditoria, $creacionAuditoria], [$editorAuditoria, $edicionAuditoria]] as [$autorAuditoria, $fechaAuditoria])
    <td class="text-nowrap">{{ $autoresAuditoria->get($autorAuditoria) ?? '—' }}</td>
    <td class="text-nowrap">
        @if ($fechaAuditoria)
            @php($localAuditoria = \Illuminate\Support\Carbon::parse($fechaAuditoria)->tz('America/Guayaquil'))
            <time datetime="{{ $localAuditoria->toDateString() }}">{{ $localAuditoria->format('d/m/Y') }}</time>
        @else
            —
        @endif
    </td>
@endforeach
