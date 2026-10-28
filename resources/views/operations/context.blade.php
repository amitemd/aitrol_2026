<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <link rel="stylesheet" href="{{ asset('css/operations-workbench.css') }}?v={{ filemtime(public_path('css/operations-workbench.css')) }}">

    <div class="ops-workbench">
        <section class="ops-context-hero">
            <a href="{{ route('new-system.operations.index') }}" class="ops-back"><i class="fa fa-arrow-left"></i> {{ __('new_system.operations.atlas_back') }}</a>
            <div class="ops-context-hero__body">
                <div>
                    <span class="ops-kicker">{{ __('new_system.operations.bounded_context', ['key' => $context->key]) }}</span>
                    <h2>{{ $context->name }}</h2>
                    <p>{{ __('new_system.operations.context_description', ['count' => count($context->legacyModules)]) }}</p>
                </div>
                <div class="ops-context-score">
                    <strong>{{ number_format($context->routeCount()) }}</strong>
                    <span>{{ __('new_system.operations.historical_routes') }}</span>
                </div>
            </div>
            <div class="ops-context-hero__dependencies">
                <strong>{{ __('new_system.operations.allowed_communication') }}</strong>
                @if ($context->dependencies === [])
                    <span>{{ __('new_system.operations.independent_core') }}</span>
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
                    <span class="ops-kicker">{{ __('new_system.operations.functional_map') }}</span>
                    <h3>{{ __('new_system.operations.contained_modules') }}</h3>
                </div>
                <p>{{ __('new_system.operations.contained_description') }}</p>
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
                                <strong>{{ __('new_system.operations.route_count', ['count' => number_format($legacyModule->routeCount)]) }}</strong>
                            </footer>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="{{ __('new_system.operations.transition_contract') }}">
            <div><span>01</span><strong>{{ __('new_system.operations.layers.existing_route.title') }}</strong><small>{{ __('new_system.operations.layers.existing_route.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>{{ __('new_system.operations.layers.use_case.title') }}</strong><small>{{ __('new_system.operations.layers.use_case.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>{{ __('new_system.operations.layers.isolated_domain.title') }}</strong><small>{{ __('new_system.operations.layers.isolated_domain.description') }}</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>{{ __('new_system.operations.layers.port.title') }}</strong><small>{{ __('new_system.operations.layers.port.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>{{ __('new_system.operations.layers.current_database.title') }}</strong><small>{{ __('new_system.operations.layers.current_database.description') }}</small></div>
        </section>
    </div>
</x-new-system.layout>
