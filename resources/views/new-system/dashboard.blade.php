<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    @php
        $dashboardUserName = trim(implode(' ', array_filter([$newSystemUser->nombre1, $newSystemUser->apellido1])));
        $dashboardCompanyName = $newSystemCurrentCompany?->nombrecomercial ?? $newSystemCompanies->first()?->nombrecomercial ?? 'AITROL';
    @endphp

    <section class="aitrol-welcome">
        <div class="aitrol-welcome__content">
            <p class="aitrol-welcome__eyebrow">{{ __('new_system.dashboard.platform') }}</p>
            <h2>{{ __('new_system.dashboard.welcome', ['name' => $dashboardUserName]) }}</h2>
            <p>{{ __('new_system.dashboard.description', ['company' => $dashboardCompanyName]) }}</p>
        </div>
        <a href="#modulos-principales" class="aitrol-welcome__action">{{ __('new_system.dashboard.view_main_access') }} <i class="fa fa-arrow-down"></i></a>
    </section>

    @if ($newSystemCurrentCompany === null && $newSystemCompanies->count() > 1)
        <div class="alert alert-warning">
            <h4><i class="icon fa fa-warning"></i> {{ __('new_system.dashboard.select_work_company') }}</h4>
            {{ __('new_system.dashboard.company_filter_notice') }}
            <a href="{{ route('new-system.companies.index') }}" class="btn btn-warning btn-sm pull-right">{{ __('new_system.dashboard.select_company') }}</a>
            <div class="clearfix"></div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['activeUsers']) }}</h3>
                    <p>{{ __('new_system.dashboard.active_users') }}</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <span class="small-box-footer">{{ __('new_system.dashboard.shared_system') }} <i class="fa fa-check-circle"></i></span>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['companies']) }}</h3>
                    <p>{{ __('new_system.dashboard.active_companies') }}</p>
                </div>
                <div class="icon"><i class="fa fa-building"></i></div>
                <a href="{{ route('new-system.companies.index') }}" class="small-box-footer">{{ __('new_system.dashboard.select_company') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['companyUsers']) }}</h3>
                    <p>{{ __('new_system.dashboard.company_users') }}</p>
                </div>
                <div class="icon"><i class="fa fa-user-md"></i></div>
                @can('manage-new-system-users')
                    <a href="{{ route('new-system.users.index') }}" class="small-box-footer">{{ __('new_system.dashboard.manage_users') }} <i class="fa fa-arrow-circle-right"></i></a>
                @else
                    <span class="small-box-footer">{{ __('new_system.dashboard.selected_company') }} <i class="fa fa-check-circle"></i></span>
                @endcan
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red new-system-stat">
                <div class="inner">
                    <h3>{{ number_format($stats['legacyRoutes']) }}</h3>
                    <p>{{ __('new_system.dashboard.integrated_routes') }}</p>
                </div>
                <div class="icon"><i class="fa fa-exchange"></i></div>
                <span class="small-box-footer">{{ __('new_system.dashboard.sidebar_available') }} <i class="fa fa-check-circle"></i></span>
            </div>
        </div>
    </div>

    <div class="row" id="modulos-principales">
        <div class="col-md-4">
            <div class="box box-info new-system-module-box">
                <div class="box-header with-border"><h3 class="box-title">{{ __('new_system.common.companies') }}</h3></div>
                <div class="box-body">
                    <p>{{ __('new_system.dashboard.companies_description') }}</p>
                    <span class="icon"><i class="fa fa-building"></i></span>
                </div>
                <div class="box-footer"><a href="{{ route('new-system.companies.index') }}" class="btn btn-info btn-flat">{{ __('new_system.dashboard.open_module') }}</a></div>
            </div>
        </div>
        @can('manage-new-system-users')
            <div class="col-md-4">
                <div class="box box-success new-system-module-box">
                    <div class="box-header with-border"><h3 class="box-title">{{ __('new_system.common.users') }}</h3></div>
                    <div class="box-body">
                        <p>{{ __('new_system.dashboard.users_description') }}</p>
                        <span class="icon"><i class="fa fa-users"></i></span>
                    </div>
                    <div class="box-footer"><a href="{{ route('new-system.users.index') }}" class="btn btn-success btn-flat">{{ __('new_system.dashboard.open_module') }}</a></div>
                </div>
            </div>
        @endcan
        <div class="col-md-4">
            <div class="box box-warning new-system-module-box">
                <div class="box-header with-border"><h3 class="box-title">{{ __('new_system.dashboard.complete_operation') }}</h3></div>
                <div class="box-body">
                    <p>{{ __('new_system.dashboard.complete_operation_description') }}</p>
                    <span class="icon"><i class="fa fa-external-link"></i></span>
                </div>
                <div class="box-footer"><span class="text-muted"><i class="fa fa-bars"></i> {{ __('new_system.dashboard.select_sidebar_option') }}</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-primary new-system-module-box">
                <div class="box-header with-border"><h3 class="box-title">{{ __('new_system.common.module_architecture') }}</h3></div>
                <div class="box-body">
                    <p>{{ __('new_system.dashboard.architecture_description') }}</p>
                    <span class="icon"><i class="fa fa-sitemap"></i></span>
                </div>
                <div class="box-footer"><a href="{{ route('new-system.operations.index') }}" class="btn btn-primary btn-flat">{{ __('new_system.dashboard.open_atlas') }}</a></div>
            </div>
        </div>
    </div>
</x-new-system.layout>
