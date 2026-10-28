<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <link rel="stylesheet" href="{{ asset('css/operations-workbench.css') }}?v={{ filemtime(public_path('css/operations-workbench.css')) }}">

    <div class="ops-workbench">
        <section class="ops-module-hero ops-module-hero--{{ $moduleStatus->module->accent }}">
            <a href="{{ route('new-system.operations.index') }}" class="ops-back"><i class="fa fa-arrow-left"></i> {{ __('new_system.operations.atlas_back') }}</a>
            <div class="ops-module-hero__body">
                <div>
                    <span class="ops-kicker">{{ $moduleStatus->module->eyebrow }}</span>
                    <h2>{{ $moduleStatus->module->name }}</h2>
                    <p>{{ $moduleStatus->module->mission }}</p>
                </div>
                <div class="ops-module-score">
                    <strong>{{ $moduleStatus->readinessPercentage() }}%</strong>
                    <span>{{ __('new_system.operations.verified_accesses', ['ready' => $moduleStatus->readyCount(), 'total' => $moduleStatus->totalCount()]) }}</span>
                </div>
            </div>
            <div class="ops-module-hero__capabilities">
                @foreach ($moduleStatus->module->capabilities as $capability)
                    <span><i class="fa fa-circle"></i> {{ $capability }}</span>
                @endforeach
            </div>
        </section>

        <section class="ops-section">
            <div class="ops-section__heading">
                <div>
                    <span class="ops-kicker">{{ __('new_system.operations.live_verification') }}</span>
                    <h3>{{ __('new_system.operations.routes_controllers') }}</h3>
                </div>
                <p>{{ __('new_system.operations.verification_description') }}</p>
            </div>

            <div class="ops-workflow-grid">
                @foreach ($moduleStatus->workflows as $workflowStatus)
                    <article class="ops-workflow {{ $workflowStatus->isReady() ? 'is-ready' : 'is-pending' }}">
                        <div class="ops-workflow__rail">
                            <span></span>
                            <i class="fa {{ $workflowStatus->isReady() ? 'fa-check' : 'fa-exclamation' }}"></i>
                        </div>
                        <div class="ops-workflow__content">
                            <div class="ops-workflow__meta">
                                <span>{{ $workflowStatus->workflow->stage }}</span>
                                <strong>{{ $workflowStatus->isReady() ? __('new_system.operations.ready') : __('new_system.operations.review_required') }}</strong>
                            </div>
                            <h4>{{ $workflowStatus->workflow->name }}</h4>
                            <p>{{ $workflowStatus->workflow->summary }}</p>

                            <dl>
                                <div><dt>{{ __('new_system.operations.route') }}</dt><dd>{{ $workflowStatus->workflow->routeName }}</dd></div>
                                <div><dt>{{ __('new_system.operations.method') }}</dt><dd>{{ $workflowStatus->methods === [] ? '—' : implode(' · ', $workflowStatus->methods) }}</dd></div>
                                <div><dt>URI</dt><dd>{{ $workflowStatus->uri ?? __('new_system.operations.not_registered') }}</dd></div>
                            </dl>

                            @if ($workflowStatus->isReady() && $workflowStatus->url !== null)
                                <a href="{{ $workflowStatus->url }}" class="ops-open-workflow">
                                    {{ __('new_system.operations.open_original') }} <i class="fa fa-external-link"></i>
                                </a>
                            @else
                                <span class="ops-open-workflow is-disabled">{{ __('new_system.operations.pending_entry') }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="{{ __('new_system.operations.module_layers') }}">
            <div><span>01</span><strong>{{ __('new_system.operations.layers.controller.title') }}</strong><small>{{ __('new_system.operations.layers.controller.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>{{ __('new_system.operations.layers.query.title') }}</strong><small>{{ __('new_system.operations.layers.query.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>{{ __('new_system.operations.layers.definition.title') }}</strong><small>{{ __('new_system.operations.layers.definition.description') }}</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>{{ __('new_system.operations.layers.gateway.title') }}</strong><small>{{ __('new_system.operations.layers.gateway.description') }}</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>{{ __('new_system.operations.layers.laravel.title') }}</strong><small>{{ __('new_system.operations.layers.laravel.description') }}</small></div>
        </section>
    </div>
</x-new-system.layout>
