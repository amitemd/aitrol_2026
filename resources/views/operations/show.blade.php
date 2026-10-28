<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <link rel="stylesheet" href="{{ asset('css/operations-workbench.css') }}?v={{ filemtime(public_path('css/operations-workbench.css')) }}">

    <div class="ops-workbench">
        <section class="ops-module-hero ops-module-hero--{{ $moduleStatus->module->accent }}">
            <a href="{{ route('new-system.operations.index') }}" class="ops-back"><i class="fa fa-arrow-left"></i> Atlas completo</a>
            <div class="ops-module-hero__body">
                <div>
                    <span class="ops-kicker">{{ $moduleStatus->module->eyebrow }}</span>
                    <h2>{{ $moduleStatus->module->name }}</h2>
                    <p>{{ $moduleStatus->module->mission }}</p>
                </div>
                <div class="ops-module-score">
                    <strong>{{ $moduleStatus->readinessPercentage() }}%</strong>
                    <span>{{ $moduleStatus->readyCount() }} de {{ $moduleStatus->totalCount() }} accesos verificados</span>
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
                    <span class="ops-kicker">Verificación viva</span>
                    <h3>Rutas y controladores</h3>
                </div>
                <p>Cada tarjeta inspecciona el enrutador real. “Listo” significa que existe la ruta y que su acción es invocable.</p>
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
                                <strong>{{ $workflowStatus->isReady() ? 'Listo' : 'Revisión requerida' }}</strong>
                            </div>
                            <h4>{{ $workflowStatus->workflow->name }}</h4>
                            <p>{{ $workflowStatus->workflow->summary }}</p>

                            <dl>
                                <div><dt>Ruta</dt><dd>{{ $workflowStatus->workflow->routeName }}</dd></div>
                                <div><dt>Método</dt><dd>{{ $workflowStatus->methods === [] ? '—' : implode(' · ', $workflowStatus->methods) }}</dd></div>
                                <div><dt>URI</dt><dd>{{ $workflowStatus->uri ?? 'No registrada' }}</dd></div>
                            </dl>

                            @if ($workflowStatus->isReady() && $workflowStatus->url !== null)
                                <a href="{{ $workflowStatus->url }}" class="ops-open-workflow">
                                    Abrir flujo original <i class="fa fa-external-link"></i>
                                </a>
                            @else
                                <span class="ops-open-workflow is-disabled">Punto de entrada pendiente</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-architecture" aria-label="Capas utilizadas por el módulo">
            <div><span>01</span><strong>Controlador</strong><small>Entrada HTTP nueva</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>02</span><strong>Consulta</strong><small>Caso de uso</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>03</span><strong>Definición</strong><small>Dominio del módulo</small></div>
            <i class="fa fa-exchange"></i>
            <div><span>04</span><strong>Gateway</strong><small>Puerto de rutas</small></div>
            <i class="fa fa-long-arrow-right"></i>
            <div><span>05</span><strong>Laravel</strong><small>Adaptador actual</small></div>
        </section>
    </div>
</x-new-system.layout>
