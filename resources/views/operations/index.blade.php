<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @php
        $moduleRoutes = [
            'medical-scheduling' => 'new-system.operations.medical',
            'patient-biopsy' => 'new-system.operations.patients',
            'laboratory' => 'new-system.operations.laboratory',
            'administration' => 'new-system.operations.administration',
            'cash-management' => 'new-system.operations.cash',
        ];
        $contextIcons = [
            'institutional' => 'fa-building',
            'clinical-care' => 'fa-heartbeat',
            'diagnostics' => 'fa-flask',
            'hospitalization' => 'fa-hospital-o',
            'inventory-operations' => 'fa-cubes',
            'billing' => 'fa-file-text-o',
            'accounting-finance' => 'fa-line-chart',
            'human-resources' => 'fa-users',
            'support-integrations' => 'fa-plug',
        ];
    @endphp

    <link rel="stylesheet" href="{{ asset('css/operations-workbench.css') }}?v={{ filemtime(public_path('css/operations-workbench.css')) }}">

    <div class="ops-workbench">
        <section class="ops-hero">
            <div class="ops-hero__signal" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
            <div class="ops-hero__copy">
                <span class="ops-kicker">AITROL · plano de transición</span>
                <h2>El legado completo, organizado para evolucionar sin perder operación.</h2>
                <p>Cada capacidad histórica pertenece a un único contexto delimitado. Los accesos prioritarios ya se comprueban contra rutas y controladores reales.</p>
            </div>
            <div class="ops-hero__metrics" aria-label="Resumen de arquitectura">
                <div><strong>{{ $summary['legacyModules'] }}</strong><span>módulos heredados</span></div>
                <div><strong>{{ $summary['contexts'] }}</strong><span>contextos DDD</span></div>
                <div><strong>{{ number_format($summary['legacyRoutes']) }}</strong><span>rutas inventariadas</span></div>
                <div><strong>{{ $summary['readyWorkflows'] }}/{{ $summary['totalWorkflows'] }}</strong><span>entradas verificadas</span></div>
            </div>
        </section>

        <section class="ops-section" aria-labelledby="priority-title">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">Primera batería funcional</span>
                    <h3 id="priority-title">Puertas operativas</h3>
                </div>
                <p>Estas áreas tienen un controlador de entrada nuevo y continúan utilizando los datos y procesos del proyecto base.</p>
            </div>

            <div class="ops-priority-grid">
                @foreach ($modules as $moduleStatus)
                    <article class="ops-module ops-module--{{ $moduleStatus->module->accent }}">
                        <div class="ops-module__topline">
                            <span>{{ $moduleStatus->module->eyebrow }}</span>
                            <span class="ops-status {{ $moduleStatus->isReady() ? 'is-ready' : 'is-review' }}">
                                <i class="fa {{ $moduleStatus->isReady() ? 'fa-check' : 'fa-wrench' }}"></i>
                                {{ $moduleStatus->readinessPercentage() }}%
                            </span>
                        </div>
                        <h4>{{ $moduleStatus->module->name }}</h4>
                        <p>{{ $moduleStatus->module->summary }}</p>
                        <div class="ops-module__capabilities">
                            @foreach ($moduleStatus->module->capabilities as $capability)
                                <span>{{ $capability }}</span>
                            @endforeach
                        </div>
                        <div class="ops-module__footer">
                            <span><strong>{{ $moduleStatus->readyCount() }}</strong> de {{ $moduleStatus->totalCount() }} accesos listos</span>
                            <a href="{{ route($moduleRoutes[$moduleStatus->module->key]) }}">
                                Probar módulo <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-section ops-section--atlas" aria-labelledby="atlas-title">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">Cobertura total</span>
                    <h3 id="atlas-title">Atlas de los 34 módulos del legado</h3>
                </div>
                <p>La agrupación evita duplicar reglas de negocio y deja explícitas las dependencias entre dominios.</p>
            </div>

            <div class="ops-atlas">
                @foreach ($contexts as $position => $context)
                    <article class="ops-context" style="--context-order: {{ $position }}">
                        <header class="ops-context__header">
                            <span class="ops-context__number">{{ str_pad((string) ($position + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="ops-context__icon"><i class="fa {{ $contextIcons[$context->key] ?? 'fa-circle-o' }}"></i></span>
                            <div>
                                <h4>{{ $context->name }}</h4>
                                <p>{{ count($context->legacyModules) }} módulos · {{ number_format($context->routeCount()) }} rutas</p>
                            </div>
                        </header>

                        <div class="ops-context__dependencies">
                            @if ($context->dependencies === [])
                                <span>Núcleo autónomo</span>
                            @else
                                <span>Depende de</span>
                                @foreach ($context->dependencies as $dependency)
                                    <code>{{ $dependency }}</code>
                                @endforeach
                            @endif
                        </div>

                        <div class="ops-context__modules">
                            @foreach ($context->legacyModules as $legacyModule)
                                <details class="ops-legacy-module">
                                    <summary>
                                        <span>
                                            <strong>{{ $legacyModule->name }}</strong>
                                            <small>{{ $legacyModule->category }} · {{ $legacyModule->routeCount }} rutas</small>
                                        </span>
                                        <i class="fa fa-angle-down"></i>
                                    </summary>
                                    <p>{{ $legacyModule->description }}</p>
                                    <div>
                                        @foreach ($legacyModule->capabilities as $capability)
                                            <span>{{ $capability }}</span>
                                        @endforeach
                                    </div>
                                    <code>{{ $legacyModule->source }}</code>
                                </details>
                            @endforeach
                        </div>
                        <a class="ops-context__link" href="{{ route('new-system.operations.contexts.show', ['contextKey' => $context->key]) }}">
                            Revisar contexto <i class="fa fa-arrow-right"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="Flujo de arquitectura hexagonal">
            <div><span>01</span><strong>Interfaz</strong><small>Rutas y controladores</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>Aplicación</strong><small>Casos de uso</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>Dominio</strong><small>Reglas y lenguaje</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>Puertos</strong><small>Contratos estables</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>Adaptadores</strong><small>Laravel + base actual</small></div>
        </section>
    </div>
</x-new-system.layout>
