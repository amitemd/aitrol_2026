@if(isset($tenantActual) && $tenantActual)
@php
    $tenantLabel = $tenantActual->nombrecomercial ?: ($tenantActual->razonsocial ?: $tenantActual->id);
    $tenantCount = isset($tenantsDisponibles) ? $tenantsDisponibles->count() : 0;
    $tenantTexts = App::getLocale() === 'en'
        ? array('active' => 'Active company', 'change' => 'Change company')
        : array('active' => 'Empresa activa', 'change' => 'Cambiar empresa');
@endphp
<li class="nav-item tenant-switcher-item" title="{{ $tenantTexts['active'] }}">
    @if($tenantCount > 1)
        <a href="{{ route('tenant.select') }}" class="tenant-current-company tenant-change-company" title="{{ $tenantTexts['change'] }}">
            <span class="tenant-switcher-icon" aria-hidden="true"><i class="fa fa-building"></i></span>
            <span class="tenant-current-copy"><small>{{ $tenantTexts['change'] }}</small><strong>{{ $tenantLabel }}</strong></span>
        </a>
    @else
        <span class="tenant-current-company">
            <span class="tenant-switcher-icon" aria-hidden="true"><i class="fa fa-building"></i></span>
            <span class="tenant-current-copy"><small>{{ $tenantTexts['active'] }}</small><strong>{{ $tenantLabel }}</strong></span>
        </span>
    @endif
</li>
<style>
    .tenant-switcher-item{display:flex!important;align-items:center;margin:5px 5px 5px 0;max-width:280px}
    .tenant-current-company{display:flex;align-items:center;gap:8px;min-height:40px;padding:5px 10px;border:1px solid rgba(255,255,255,.28);border-radius:9px;background:rgba(255,255,255,.12)}
    .tenant-change-company{color:inherit!important;text-decoration:none!important;transition:background .18s ease,border-color .18s ease}
    .tenant-change-company:hover,.tenant-change-company:focus{background:rgba(255,255,255,.22);border-color:rgba(255,255,255,.45);outline:0}
    .tenant-switcher-icon{display:inline-flex;align-items:center;justify-content:center;flex:0 0 27px;width:27px;height:27px;border-radius:7px;background:rgba(255,255,255,.18)}
    .tenant-current-copy{display:flex;flex-direction:column;min-width:0;line-height:1.15}
    .tenant-current-copy small{font-size:9px;opacity:.78;text-transform:uppercase;letter-spacing:.35px}
    .tenant-current-copy strong{display:block;max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px}
    @media(max-width:991px){.tenant-switcher-item{max-width:210px}.tenant-current-copy small{display:none}.tenant-current-copy strong{max-width:145px}}
    @media(max-width:767px){.tenant-switcher-item{display:none!important}}
</style>
@endif
