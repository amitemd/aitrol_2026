@php
    $linksImagenesVista = collect($value->links_imagenes ?? [])
        ->map(function ($url) { return trim((string) $url); })
        ->filter()
        ->unique()
        ->values();
    $esEcografia = ($variant ?? 'procedure') === 'ecography';
    $prefijoClase = $esEcografia ? 'hc4-ecography' : 'hc4-procedure';
@endphp

<div class="{{$prefijoClase}}-link-card">
    <div class="{{$prefijoClase}}-link-label">Links de imágenes</div>
    <div class="{{$prefijoClase}}-link-controls" style="display:flex;flex-direction:column;gap:7px;">
        @forelse($linksImagenesVista as $linkImagen)
            <div class="{{$prefijoClase}}-link-row" style="display:flex;align-items:center;gap:7px;min-width:0;">
                <a class="{{$prefijoClase}}-link-view" href="{{$linkImagen}}" target="_blank" rel="noopener noreferrer" title="Abrir enlace de imágenes en una nueva pestaña">{{$linkImagen}}</a>
                <button type="button" class="{{$esEcografia ? 'hc4-ecography-link-copy' : 'hc4-procedure-link-action'}}" title="Copiar enlace" aria-label="Copiar enlace" onclick="{{$copyHandler}}(this)"><i class="fa fa-copy" aria-hidden="true"></i></button>
            </div>
        @empty
            <span class="{{$prefijoClase}}-link-empty">No hay links de imágenes registrados.</span>
        @endforelse
    </div>
</div>
