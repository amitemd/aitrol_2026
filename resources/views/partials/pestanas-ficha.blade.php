@php($navegacionFicha = $navegacionFicha ?? false)

<div @class(['nav nav-pills ficha-tabs', 'usuario-tabs' => !$navegacionFicha])
    role="{{ $navegacionFicha ? 'navigation' : 'tablist' }}"
    aria-label="{{ $etiquetaPestanas ?? 'Secciones de la ficha' }}" @unless($navegacionFicha) hidden @endunless>
    @foreach ($pestanas as $panel => [$nombre, $icono])
        <button type="button" @class(['nav-link', 'active' => $loop->first]) id="{{ $contexto }}-tab-{{ $panel }}"
            aria-controls="{{ $contexto }}-{{ $panel }}" aria-label="{{ $nombre }}" title="{{ $nombre }}"
            @if ($navegacionFicha)
                data-ficha-tab="{{ $panel }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
            @else
                role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                tabindex="{{ $loop->first ? 0 : -1 }}" data-usuario-tab="{{ $panel }}"
            @endif>
            <i class="bi {{ $icono }}" aria-hidden="true"></i>
            <span class="ficha-tab-etiqueta">{{ $nombre }}</span>
        </button>
    @endforeach
</div>
