@push('scripts')
    <script src="{{ asset('listados.js') }}" defer></script>
@endpush

<p class="alert" role="status" data-ajax-status hidden></p>
<dialog class="cliente-dialog cliente-dialog-redisenado" aria-labelledby="listado-modal-titulo"
    data-ajax-dialog @if (request()->routeIs('clientes.index')) data-cliente-dialog @endif>
    <div class="d-flex align-items-center justify-content-between gap-3 cliente-dialog-cabecera">
        <div class="d-flex align-items-center gap-3">
            <span class="clientes-icono" aria-hidden="true"><i class="bi {{ $iconoModal ?? 'bi-pencil-square' }}"></i></span>
            <h2 id="listado-modal-titulo" data-ajax-title class="fs-5 fw-semibold mb-0"></h2>
        </div>
        <button type="button" class="cliente-dialog-cerrar" aria-label="Cerrar" data-ajax-close>
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
    <div class="cliente-dialog-cuerpo">
        <div class="alert alert-danger" role="alert" data-ajax-errors hidden></div>
        <div data-ajax-body></div>
    </div>
</dialog>
