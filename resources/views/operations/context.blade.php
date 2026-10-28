<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <link rel="stylesheet" href="{{ asset('css/operations-workbench.css') }}?v={{ filemtime(public_path('css/operations-workbench.css')) }}">

    <div class="ops-workbench">
        <section class="ops-context-hero">
            <a href="{{ route('new-system.operations.index') }}" class="ops-back"><i class="fa fa-arrow-left"></i> Atlas completo</a>
            <div class="ops-context-hero__body">
                <div>
                    <span class="ops-kicker">Contexto delimitado · {{ $context->key }}</span>
                    <h2>{{ $context->name }}</h2>
                    <p>Frontera de negocio que reúne {{ count($context->legacyModules) }} módulos del proyecto base sin duplicar sus tablas ni identificadores.</p>
                </div>
                <div class="ops-context-score">
                    <strong>{{ number_format($context->routeCount()) }}</strong>
                    <span>rutas históricas inventariadas</span>
                </div>
            </div>
            <div class="ops-context-hero__dependencies">
                <strong>Comunicación permitida</strong>
                @if ($context->dependencies === [])
                    <span>Núcleo independiente</span>
                @else
                    @foreach ($context->dependencies as $dependency)
                        <span><i class="fa fa-plug"></i> {{ $dependency }}</span>
                    @endforeach
                @endif
            </div>
        </section>

        <section class="ops-section">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">Mapa funcional</span>
                    <h3>Módulos contenidos</h3>
                </div>
                <p>Cada bloque conserva sus tablas actuales. La modernización se realiza detrás de puertos y adaptadores, operación por operación.</p>
            </div>

            <div class="ops-domain-grid">
                @foreach ($context->legacyModules as $position => $legacyModule)
                    <article class="ops-domain-module">
                        <div class="ops-domain-module__index">{{ str_pad((string) ($position + 1), 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="ops-domain-module__content">
                            <span>{{ $legacyModule->category }}</span>
                            <h3>{{ $legacyModule->name }}</h3>
                            <p>{{ $legacyModule->description }}</p>
                            <div>
                                @foreach ($legacyModule->capabilities as $capability)
                                    <span>{{ $capability }}</span>
                                @endforeach
                            </div>
                            <footer>
                                <code>{{ $legacyModule->source }}</code>
                                <strong>{{ number_format($legacyModule->routeCount) }} rutas</strong>
                            </footer>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="Contrato de transición del contexto">
            <div><span>01</span><strong>Ruta existente</strong><small>Compatibilidad pública</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>Caso de uso</strong><small>Orquestación nueva</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>Dominio</strong><small>Reglas aisladas</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>Puerto</strong><small>Contrato entre capas</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>Base actual</strong><small>Sin migrar datos</small></div>
        </section>
    </div>
</x-new-system.layout>
