<div @class(['ficha-adjunto', 'ficha-adjunto-foto' => $vistaPrevia ?? false]) data-ficha-adjunto>
    @if ($vistaPrevia ?? false)
        <div class="ficha-adjunto-encabezado">
            <strong id="{{ $campo }}-titulo">{{ $titulo }}</strong>
        </div>
        <div class="ficha-foto" aria-hidden="true">
            <i class="bi {{ $iconoAdjunto }}"></i>
            <img data-adjunto-vista @if (!empty($urlImagen)) src="{{ $urlImagen }}" @else hidden @endif alt="">
        </div>
    @endif
    <label class="ficha-adjunto-selector">
        <input id="{{ $campo }}" name="{{ $nombre }}" type="file" class="visually-hidden @error($campo) is-invalid @enderror"
            accept="{{ $formatos }}" @if ($multiple ?? false) multiple @endif @required($requerido ?? false)
            aria-labelledby="{{ $campo }}-titulo {{ $campo }}-accion" aria-describedby="{{ $campo }}-ayuda">
        @unless ($vistaPrevia ?? false)
            <span class="clientes-icono" aria-hidden="true"><i class="bi {{ $iconoAdjunto }}"></i></span>
            <span class="ficha-adjunto-texto">
                <strong id="{{ $campo }}-titulo">{{ $titulo }}</strong>
                <small id="{{ $campo }}-ayuda">{{ $ayuda }}</small>
            </span>
        @endunless
        <span class="ficha-adjunto-accion" id="{{ $campo }}-accion">
            <i class="bi {{ ($vistaPrevia ?? false) ? 'bi-camera' : 'bi-upload' }}" aria-hidden="true"></i>{{ $accion }}
        </span>
    </label>
    @if ($vistaPrevia ?? false)
        <small id="{{ $campo }}-ayuda" class="ficha-adjunto-ayuda">{{ $ayuda }}</small>
    @endif
    <div class="ficha-adjunto-seleccion" data-adjunto-seleccion hidden>
        <ul class="list-unstyled mb-0" data-adjunto-lista aria-live="polite"></ul>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-adjunto-limpiar>{{ __('Quitar selección') }}</button>
    </div>
    @error($campo) <p class="text-danger small mt-2 mb-0">{{ $message }}</p> @enderror
    @foreach ($errors->get($campo.'.*') as $mensajes)
        @foreach ($mensajes as $mensaje)
            <p class="text-danger small mt-2 mb-0">{{ $mensaje }}</p>
        @endforeach
    @endforeach
</div>
