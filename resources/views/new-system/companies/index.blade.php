<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-building"></i> {{ __('new_system.companies.heading') }}</h3>
        </div>
        <div class="box-body">
            <p>{{ __('new_system.companies.description') }}</p>
        </div>
    </div>

    @if ($newSystemCompanies->isEmpty())
        <div class="callout callout-warning">
            <h4>{{ __('new_system.companies.empty_title') }}</h4>
            <p>{{ __('new_system.companies.empty_description') }}</p>
        </div>
    @else
        <div class="row">
            @foreach ($newSystemCompanies as $company)
                @php($isCurrent = $newSystemCurrentCompany?->id === $company->id)
                <div class="col-md-6">
                    <div class="box box-primary new-system-company-card {{ $isCurrent ? 'active' : '' }}">
                        <div class="box-body text-center">
                            <span class="company-letter">{{ mb_strtoupper(mb_substr($company->nombrecomercial, 0, 1)) }}</span>
                            <h3>{{ $company->nombrecomercial }}</h3>
                            <p class="text-muted">{{ $company->razonsocial }}</p>
                            <p>
                                <span class="label label-default">RUC {{ $company->id }}</span>
                                @if ($company->ciudad)
                                    <span class="label label-default">{{ $company->ciudad }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="box-footer">
                            <form method="POST" action="{{ route('new-system.companies.store') }}">
                                @csrf
                                <input type="hidden" name="company_id" value="{{ $company->id }}">
                                <button type="submit" class="btn {{ $isCurrent ? 'btn-success' : 'btn-primary' }} btn-flat btn-block" @disabled($isCurrent)>
                                    <i class="fa {{ $isCurrent ? 'fa-check' : 'fa-sign-in' }}"></i>
                                    {{ $isCurrent ? __('new_system.companies.current') : __('new_system.companies.use') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-new-system.layout>
