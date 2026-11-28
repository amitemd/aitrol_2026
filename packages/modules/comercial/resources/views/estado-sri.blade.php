@if (!empty($documentoSri['clave_acceso']))
    @php
        $estadoSri = $documentoSri['estado_sri'] ?? 'PENDIENTE';
        $autorizadoSri = $estadoSri === 'AUTORIZADO';
    @endphp
    <div class="d-flex flex-wrap align-items-center gap-2 my-2">
        <span class="badge {{ $autorizadoSri ? 'text-bg-success' : ($estadoSri === 'NO_AUTORIZADO' ? 'text-bg-danger' : 'text-bg-secondary') }}">
            SRI · {{ \App\Services\AutorizacionSri::ESTADOS[$estadoSri] }}
        </span>
        @if ($autorizadoSri)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('ventas.documentos.sri.descargar', [$documentoSri['id'], 'pdf']) }}">Descargar PDF</a>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('ventas.documentos.sri.descargar', [$documentoSri['id'], 'xml']) }}">Descargar XML</a>
        @endif
        @unless ($enLogSri ?? false)
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('ventas.documentos.sri.log', $documentoSri['id']) }}">Ver log</a>
        @endunless
    </div>
@endif
