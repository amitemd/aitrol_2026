@php
    $areas = [
        'empresas' => 'Empresas',
        'medica' => 'Administración médica',
        'contabilidad' => 'Contabilidad',
        'inventario' => 'Inventario',
        'tributacion' => 'Tributación',
        'importaciones' => 'Importaciones',
        'logistica' => 'Logística',
        'servicios' => 'Servicios',
    ];
@endphp
<canvas class="access-network" aria-hidden="true"></canvas>
<section class="access-showcase" aria-label="Áreas de gestión" data-auth-showcase>
    <div class="access-showcase-frame" id="access-showcase-photos">
        @foreach ($areas as $foto => $actividad)
            <figure @class(['access-slide', 'is-active' => $loop->first]) data-showcase-slide
                aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                <picture>
                    <source media="(min-width: 768px)" srcset="{{ asset('auth-erp/'.$foto.'.jpg') }}">
                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        alt="" draggable="false" decoding="async" fetchpriority="{{ $loop->first ? 'high' : 'low' }}">
                </picture>
                <figcaption>{{ $actividad }}</figcaption>
            </figure>
        @endforeach
    </div>
    <div class="access-showcase-controls" data-showcase-controls hidden>
        <span class="access-showcase-label" data-showcase-label>{{ reset($areas) }}</span>
        <div class="access-showcase-dots" role="group" aria-label="Elegir imagen">
            @foreach ($areas as $actividad)
                <button type="button" data-showcase-select aria-label="{{ $actividad }}"
                    aria-controls="access-showcase-photos" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
        <button type="button" class="access-showcase-playback" data-showcase-playback>Pausar</button>
    </div>
</section>
