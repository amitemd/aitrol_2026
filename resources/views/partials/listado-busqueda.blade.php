<div class="card-header bg-body p-3">
    <form method="GET" action="{{ $listadoUrl }}" class="d-flex flex-wrap gap-2" role="search" data-ajax-search>
        <label class="visually-hidden" for="q">Buscar en el listado</label>
        <div class="input-group clientes-busqueda">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input class="form-control" id="q" name="q" type="search" maxlength="160"
                value="{{ request('q') }}" placeholder="{{ $placeholder }}">
            <button class="btn btn-outline-primary" type="submit">{{ __('Buscar') }}</button>
        </div>
        @if (request()->filled('q'))
            <a href="{{ $listadoUrl }}" class="btn btn-outline-secondary" data-ajax-list-link>{{ __('Limpiar') }}</a>
        @endif
    </form>
    @error('q') <p class="text-danger mb-0 mt-2">{{ $message }}</p> @enderror
</div>