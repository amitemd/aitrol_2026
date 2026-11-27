<div class="usuario-tabs nav nav-pills ficha-tabs" role="tablist" aria-label="Secciones de la ficha" hidden>
    @foreach ($pestanas as $panel => [$nombre, $icono])
        <button type="button" @class(['nav-link', 'active' => $loop->first]) id="{{ $contexto }}-tab-{{ $panel }}"
            role="tab" aria-controls="{{ $contexto }}-{{ $panel }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
            tabindex="{{ $loop->first ? 0 : -1 }}" data-usuario-tab="{{ $panel }}">
            <i class="bi {{ $icono }}" aria-hidden="true"></i>{{ $nombre }}
        </button>
    @endforeach
</div>
