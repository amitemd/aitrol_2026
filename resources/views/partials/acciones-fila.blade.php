<div class="d-inline-flex gap-1 align-items-center">
    @isset($ver)
        <a href="{{ $ver }}" class="btn btn-sm btn-outline-secondary accion-fila-icono" data-ajax-open="{{ $tituloVer ?? 'Ver detalle' }}"
            title="Ver detalle" aria-label="{{ $tituloVer ?? 'Ver detalle' }}"><i class="bi bi-eye" aria-hidden="true"></i></a>
    @endisset
    @if (isset($editar) || !$slot->isEmpty())
        @php $menuId = 'acciones-'.Str::uuid(); @endphp
        <button type="button" class="btn btn-sm btn-outline-secondary accion-fila-icono" popovertarget="{{ $menuId }}"
            aria-label="Opciones" title="Opciones" data-acciones-boton><i class="bi bi-list-ul" aria-hidden="true"></i></button>
        <div id="{{ $menuId }}" popover="auto" data-acciones-menu class="acciones-fila-menu text-start" role="group" aria-label="Acciones del registro">
            @isset($editar)
                @include('partials.accion-fila-enlace', ['href' => $editar, 'etiqueta' => 'Editar', 'titulo' => $tituloEditar ?? 'Editar', 'icono' => 'bi-pencil'])
                @unless ($slot->isEmpty())<hr class="dropdown-divider my-1">@endunless
            @endisset
            {{ $slot }}
        </div>
    @endif
</div>
