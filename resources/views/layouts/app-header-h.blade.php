<?php
$imagen = trim((string) Auth::user()->imagen_url);
if ($imagen === '') {
    $imagen = 'avatar.jpg';
}
$defaultAvatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="50" fill="#e7f4f8"/><circle cx="50" cy="37" r="18" fill="#0b78bd"/><path d="M17 92c3-24 17-36 33-36s30 12 33 36" fill="#0b78bd"/></svg>';
$defaultAvatarUrl = 'data:image/svg+xml;base64,' . base64_encode($defaultAvatarSvg);
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$hospitalCompany = isset($tenantActual) && $tenantActual ? $tenantActual : null;
$hospitalCompanyName = $hospitalCompany
    ? ($hospitalCompany->nombrecomercial ?: ($hospitalCompany->razonsocial ?: $hospitalCompany->id))
    : 'Empresa activa';
$hospitalCompanyLogo = null;
$hospitalCompanyHasLogo = false;
if ($hospitalCompany && trim((string) $hospitalCompany->logo) !== '') {
    $hospitalCompanyLogoFile = basename(trim((string) $hospitalCompany->logo));
    if (file_exists(storage_path('app/logo/' . $hospitalCompanyLogoFile))) {
        $hospitalCompanyLogo = rtrim(request()->getBaseUrl(), '/') . '/logo/' . rawurlencode($hospitalCompanyLogoFile);
        $hospitalCompanyHasLogo = true;
    }
}
?>

<div class="modal fade" id="perfil" tabindex="-1" role="dialog" aria-labelledby="hospital-profile-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>

<nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
    <div class="navbar-header d-xl-block d-none">
        <ul class="nav navbar-nav">
            <li class="nav-item">
                <a class="navbar-brand hospital-company-brand" href="{{ url('hospital/inicio') }}"
                   aria-label="Inicio hospitalario de {{ $hospitalCompanyName }}"
                   title="{{ $hospitalCompanyName }}">
                    @if($hospitalCompanyHasLogo)
                        <img class="hospital-company-logo"
                             src="{{ $hospitalCompanyLogo }}"
                             alt="Logo de {{ $hospitalCompanyName }}"
                             onerror="this.hidden=true;this.nextElementSibling.hidden=false;">
                    @endif
                    <span class="hospital-company-logo-fallback" aria-hidden="true" @if($hospitalCompanyHasLogo) hidden @endif>
                        <svg viewBox="0 0 48 48" focusable="false" aria-hidden="true">
                            <path d="M9 40V15.5c0-2 1.5-3.5 3.5-3.5h23c2 0 3.5 1.5 3.5 3.5V40"/>
                            <path d="M6 40h36M19 40V29h10v11M17 20h4M27 20h4M17 26h4M27 26h4"/>
                            <path class="hospital-company-logo-cross" d="M24 5v8M20 9h8"/>
                        </svg>
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
            </ul>

        </div>

        <ul class="nav navbar-nav navbar-left align-items-center ">
            <!-- Authentication Links -->
            <li class="nav-item dropdown">
                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="user-name font-weight-bolder">@lang('main.idioma')</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{ route('change_lang', ['lang' => 'es']) }}">
                        @lang('main.espanol')
                    </a>
                    <a class="dropdown-item" href="{{ route('change_lang', ['lang' => 'en']) }}">
                        @lang('main.ingles')
                    </a>

                </div>

            </li>

            @if(Auth::user() && method_exists(Auth::user(), 'canSwitchToAmbulatoryWorkspace') && Auth::user()->canSwitchToAmbulatoryWorkspace())
            <li class="nav-item d-flex align-items-center">
                <a class="hospital-ambulatory-link"
                   href="{{ rtrim(request()->getBaseUrl(), '/') . '/inicio' }}"
                   title="{{ trans('main.atencionAmbulatoria') }}">
                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                        <path d="M3 12h4l2-7 4 14 2-7h6"/>
                    </svg>
                    <span>{{ trans('main.atencionAmbulatoria') }}</span>
                </a>
            </li>
            @endif

            @if(Auth::user() && Auth::user()->canSwitchToMedicalWorkspace())
            <li class="nav-item d-flex align-items-center">
                <a class="hospital-ambulatory-link hospital-admin-visible-link"
                   href="{{ url('v3/admin') }}"
                   title="Área administrativa">
                    <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Área administrativa</span>
                </a>
            </li>
            @endif

        </ul>

        <ul class="nav navbar-nav align-items-center ml-auto">

            @include('layouts.tenant-selector')

            <li class="nav-item d-none d-lg-block">
                <button id="hospital-theme-toggle" class="nav-link hospital-theme-toggle" type="button" aria-label="Cambiar modo de visualización" title="Cambiar modo de visualización">
                    <i class="ficon" data-feather="{{$classicon}}"></i>
                </button>
            </li>


            <li class="nav-item dropdown dropdown-user">
                <a class=" dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name font-weight-bolder">{{ Auth::user()->nombre1}} {{Auth::user()->apellido1}}</span>
                        <span class="user-status">{{ Auth::user()->id_tipo}}</span>
                    </div>
                    <span class="avatar"><img class="round" src="{{asset('/avatars').'/'.$imagen}}" alt="Fotografía del usuario" height="40" width="40" onerror="this.onerror=null;this.src='{{ $defaultAvatarUrl }}';">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right hospital-user-menu" aria-labelledby="dropdown-user">
                    <div class="hospital-user-card">
                        <div class="hospital-user-card-avatar">
                            <img src="{{asset('/avatars').'/'.$imagen}}" alt="Fotografía del usuario" onerror="this.onerror=null;this.src='{{ $defaultAvatarUrl }}';">
                            <span aria-hidden="true"></span>
                        </div>
                        <div class="hospital-user-card-info">
                            <p><span aria-hidden="true"></span> Sesión activa</p>
                            <strong>{{ Auth::user()->nombre1 }} {{ Auth::user()->apellido1 }}</strong>
                            <small>{{ Auth::user()->id_tipo }}</small>
                        </div>
                    </div>
                    <div class="hospital-user-actions">
                        @if(Auth::user() && Auth::user()->canSwitchToMedicalWorkspace())
                        <a href="{{ url('v3/admin') }}" class="hospital-user-action hospital-v3-workspace-link">
                            <i data-feather="grid" aria-hidden="true"></i>
                            <span><strong>Ir a Aitrol Hospitalario</strong><small>Administración y gestión integral</small></span>
                            <i class="hospital-v3-workspace-arrow" data-feather="arrow-right" aria-hidden="true"></i>
                        </a>
                        @endif
                        <a href="javascript:void(0);" class="hospital-user-action hospital-profile-open" data-toggle="modal" data-target="#perfil" data-remote="{{ route('perfil.editar_nuevo') }}">
                            <i data-feather="user" aria-hidden="true"></i>
                            <span>{{ trans('main.perfil') }}</span>
                        </a>
                        <a class="hospital-user-action hospital-user-signout" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i data-feather="log-out" aria-hidden="true"></i>
                            <span>{{ trans('main.cerrarSesion') }}</span>
                        </a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<script>
    (function ($) {
        var profileModal = $('#perfil');

        profileModal.on('hidden.bs.modal', function () {
            $(this).find('.modal-content').empty();
        });

        $(document).off('click.hospitalProfile', '.hospital-profile-open').on('click.hospitalProfile', '.hospital-profile-open', function (event) {
            event.preventDefault();
            var remoteUrl = $(this).data('remote');
            var modalContent = profileModal.find('.modal-content');

            modalContent.html('<div class="hospital-profile-loading"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><strong>Cargando perfil...</strong></div>');
            modalContent.load(remoteUrl, function (response, status) {
                if (status === 'error') {
                    modalContent.html('<div class="hospital-profile-error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><strong>No se pudo abrir el perfil</strong><span>Inténtalo nuevamente en unos momentos.</span></div>');
                }
            });
        });
    })(jQuery);
</script>
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><i class="fa fa-file-excel-o" aria-hidden="true"></i></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><i class="fa fa-file-image-o" aria-hidden="true"></i></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><i class="fa fa-file-word-o" aria-hidden="true"></i></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
        </a></li>
    <li class="d-flex align-items-center"><a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Members</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="{{ $defaultAvatarUrl }}" alt="Avatar" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="{{ $defaultAvatarUrl }}" alt="Avatar" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="{{ $defaultAvatarUrl }}" alt="Avatar" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="{{ $defaultAvatarUrl }}" alt="Avatar" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion justify-content-between"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75" data-feather="alert-circle"></span><span>No results found.</span></div>
        </a></li>
</ul>
<!-- END: Header-->
<!-- BEGIN: Main Menu-->
@if(isset($data))
    <div class="horizontal-menu-wrapper hospital-patient-summary-wrapper">
        <div id="hospi_nav" class="hospital-patient-summary">
            <section class="hospital-patient-summary__card" aria-labelledby="hospitalPatientName">
                <header class="hospital-patient-summary__header">
                    <span class="hospital-patient-summary__icon" aria-hidden="true"><i class="fa fa-user"></i></span>
                    <div>
                        <span class="hospital-patient-summary__eyebrow">{{trans('hospital.datos_paciente')}}</span>
                        <h2 id="hospitalPatientName">{{$data->nombre_paciente ?? 'Paciente'}}</h2>
                    </div>
                </header>
                <div class="hospital-patient-summary__grid">
                    <div class="hospital-patient-summary__item hospital-patient-summary__item--accent">
                        <span>{{trans('hospital.dias_hospitalizados')}}</span>
                        <strong>{{$data->dias_hospitalizados ?? 0}} {{trans('hospital.dia')}}(s)</strong>
                    </div>
                    <div class="hospital-patient-summary__item hospital-patient-summary__item--doctor">
                        <span>{{trans('hospital.medico_tratante')}}</span>
                        <strong>{{!empty($data->nombre_doctor) ? $data->nombre_doctor : trans('hospital.no_posee')}}</strong>
                    </div>
                    <div class="hospital-patient-summary__item">
                        <span>{{trans('hospital.edad')}}</span>
                        <strong>{{$data->edad ?? 0}}</strong>
                    </div>
                    <div class="hospital-patient-summary__item">
                        <span>{{trans('hospital.seguro')}}</span>
                        <strong>{{$data->seguro ?? trans('hospital.no_posee')}}</strong>
                    </div>
                    <div class="hospital-patient-summary__item">
                        <span>{{trans('hospital.alergias')}}</span>
                        <strong>{{$str_alergias==''?trans('hospital.no_posee'):$str_alergias}}</strong>
                    </div>
                    <div class="hospital-patient-summary__item">
                        <span>{{trans('hospital.religion')}}</span>
                        <strong>{{!empty($data->religion) ? $data->religion : trans('hospital.no_posee')}}</strong>
                    </div>
                    <div class="hospital-patient-summary__item hospital-patient-summary__item--history">
                        <span>{{trans('hospital.antecedentes')}}</span>
                        <strong>{{$data->ante_familiares ?? trans('hospital.no_posee')}}</strong>
                        <strong>{{$data->ante_patologicos ?? trans('hospital.no_posee')}}</strong>
                        <strong>{{$data->ante_quirurgicos ?? trans('hospital.no_posee')}}</strong>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endif
