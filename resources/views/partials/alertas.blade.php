@php
    $avisosAitrol = [
        'error' => [session('error'), ...$errors->all()],
        'warning' => [session('warning')],
        'info' => [session('info')],
        'success' => [session('success'), session('status'), session('estado')],
    ];
@endphp
@foreach ($avisosAitrol as $tipoAviso => $mensajesAviso)
    @foreach ($mensajesAviso as $mensajeAviso)
        @if (is_string($mensajeAviso) && filled($mensajeAviso))
            <span hidden data-aitrol-mensaje="{{ $tipoAviso }}">{{ $mensajeAviso }}</span>
        @endif
    @endforeach
@endforeach
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.min.js" defer></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('alertas.js') }}" defer></script>
