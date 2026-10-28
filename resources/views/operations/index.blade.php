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
                <span class="ops-kicker">{{ __('new_system.operations.transition_plan') }}</span>
                <h2>{{ __('new_system.operations.hero_title') }}</h2>
                <p>{{ __('new_system.operations.hero_description') }}</p>
            </div>
            <div class="ops-hero__metrics" aria-label="{{ __('new_system.operations.architecture_summary') }}">
                <div><strong>{{ $summary['legacyModules'] }}</strong><span>{{ __('new_system.operations.legacy_modules') }}</span></div>
                <div><strong>{{ $summary['contexts'] }}</strong><span>{{ __('new_system.operations.ddd_contexts') }}</span></div>
                <div><strong>{{ number_format($summary['legacyRoutes']) }}</strong><span>{{ __('new_system.operations.inventoried_routes') }}</span></div>
                <div><strong>{{ $summary['readyWorkflows'] }}/{{ $summary['totalWorkflows'] }}</strong><span>{{ __('new_system.operations.verified_entries') }}</span></div>
            </div>
        </section>

        <section class="ops-section" aria-labelledby="priority-title">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">{{ __('new_system.operations.first_batch') }}</span>
                    <h3 id="priority-title">{{ __('new_system.operations.operational_gates') }}</h3>
                </div>
                <p>{{ __('new_system.operations.operational_description') }}</p>
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
                            <span>{{ __('new_system.operations.ready_accesses', ['ready' => $moduleStatus->readyCount(), 'total' => $moduleStatus->totalCount()]) }}</span>
                            <a href="{{ route($moduleRoutes[$moduleStatus->module->key]) }}">
                                {{ __('new_system.operations.test_module') }} <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-section ops-section--atlas" aria-labelledby="atlas-title">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">{{ __('new_system.operations.total_coverage') }}</span>
                    <h3 id="atlas-title">{{ __('new_system.operations.legacy_atlas') }}</h3>
                </div>
                <p>{{ __('new_system.operations.atlas_description') }}</p>
            </div>

            <div class="ops-atlas">
                @foreach ($contexts as $position => $context)
                    <article class="ops-context" style="--context-order: {{ $position }}">
                        <header class="ops-context__header">
                            <span class="ops-context__number">{{ str_pad((string) ($position + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="ops-context__icon"><i class="fa {{ $contextIcons[$context->key] ?? 'fa-circle-o' }}"></i></span>
                            <div>
                                <h4>{{ $context->name }}</h4>
                                <p>{{ __('new_system.operations.modules_routes', ['modules' => count($context->legacyModules), 'routes' => number_format($context->routeCount())]) }}</p>
                            </div>
                        </header>

                        <div class="ops-context__dependencies">
                            @if ($context->dependencies === [])
                                <span>{{ __('new_system.operations.autonomous_core') }}</span>
                            @else
                                <span>{{ __('new_system.operations.depends_on') }}</span>
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
                                            <small>{{ $legacyModule->category }} · {{ __('new_system.operations.route_count', ['count' => $legacyModule->routeCount]) }}</small>
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
                            {{ __('new_system.operations.review_context') }} <i class="fa fa-arrow-right"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="{{ __('new_system.operations.architecture_flow') }}">
            <div><span>01</span><strong>{{ __('new_system.operations.layers.interface.title') }}</strong><small>{{ __('new_system.operations.layers.interface.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>{{ __('new_system.operations.layers.application.title') }}</strong><small>{{ __('new_system.operations.layers.application.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>{{ __('new_system.operations.layers.domain.title') }}</strong><small>{{ __('new_system.operations.layers.domain.description') }}</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>{{ __('new_system.operations.layers.ports.title') }}</strong><small>{{ __('new_system.operations.layers.ports.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>{{ __('new_system.operations.layers.adapters.title') }}</strong><small>{{ __('new_system.operations.layers.adapters.description') }}</small></div>
        </section>
    </div>
</x-new-system.layout>
