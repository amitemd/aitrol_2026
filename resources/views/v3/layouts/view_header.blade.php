<style>
    .v3-user-menu{margin-left:4px}.v3-user-trigger{display:flex!important;align-items:center;gap:9px;min-height:38px;margin-top:1px;padding:4px 10px 4px 6px!important;border:1px solid transparent;border-radius:12px;transition:.18s ease}.v3-user-trigger:hover,.v3-user-menu.show .v3-user-trigger{border-color:#c9e0ea;background:#edf7fa!important}.v3-user-trigger-avatar{width:30px!important;height:30px!important;margin:0!important;border:2px solid #fff;object-fit:cover;box-shadow:0 2px 7px rgba(20,68,92,.2)}.v3-user-trigger-copy{display:flex;min-width:0;flex-direction:column;line-height:1.1}.v3-user-trigger-copy strong{max-width:145px;overflow:hidden;color:#214a61;font-size:11px;font-weight:800;text-overflow:ellipsis;white-space:nowrap}.v3-user-trigger-copy small{margin-top:3px;color:#78909c;font-size:8px;font-weight:700;letter-spacing:.35px;text-transform:uppercase}.v3-user-chevron{margin-left:1px;color:#78909c;font-size:9px}.navbar-nav>.v3-user-menu>.v3-user-dropdown{width:320px!important;min-width:320px;margin-top:9px;padding:0;overflow:hidden;border:1px solid #d5e4eb;border-radius:16px;background:#fff;box-shadow:0 18px 45px rgba(17,61,82,.2)}.v3-user-dropdown:before{position:absolute;top:-7px;right:23px;width:13px;height:13px;border-top:1px solid #d5e4eb;border-left:1px solid #d5e4eb;background:#0a6d8f;content:"";transform:rotate(45deg)}.v3-profile-head{position:relative;padding:24px 22px 20px;background:linear-gradient(125deg,#073f66,#087ea3 64%,#12a39e);color:#fff}.v3-profile-identity{position:relative;display:flex;align-items:center;gap:14px}.v3-profile-avatar-wrap{position:relative;flex:0 0 64px}.v3-profile-avatar{width:64px;height:64px;border:3px solid rgba(255,255,255,.85);border-radius:18px;object-fit:cover;box-shadow:0 8px 20px rgba(1,38,58,.22)}.v3-profile-presence{position:absolute;right:-2px;bottom:-2px;width:15px;height:15px;border:3px solid #087b9e;border-radius:50%;background:#55d7a7}.v3-profile-copy{min-width:0}.v3-profile-copy small{display:block;margin-bottom:4px;color:#bde9ef;font-size:9px;font-weight:800;letter-spacing:1px}.v3-profile-copy h3{margin:0;overflow:hidden;color:#fff;font-size:17px;font-weight:800;text-overflow:ellipsis;white-space:nowrap}.v3-profile-copy p{margin:5px 0 0;color:rgba(255,255,255,.82);font-size:11px;line-height:1.35}.v3-profile-actions{display:grid;gap:8px;padding:13px;background:#f7fafc}.v3-profile-action{display:flex;align-items:center;gap:11px;padding:11px 12px;border:1px solid #dce8ed;border-radius:10px;color:#214d65!important;background:#fff;text-decoration:none!important;transition:.16s ease}.v3-profile-action:hover{border-color:#9fcbd9;background:#eef8fa;transform:translateY(-1px)}.v3-profile-action-icon{display:grid;flex:0 0 34px;place-items:center;width:34px;height:34px;border-radius:9px;color:#087ea3;background:#e5f4f8}.v3-profile-action span{display:flex;min-width:0;flex-direction:column}.v3-profile-action strong{font-size:12px}.v3-profile-action small{margin-top:2px;color:#78909c;font-size:9px}.v3-profile-action--logout{color:#96384a!important}.v3-profile-action--logout .v3-profile-action-icon{color:#bd3e53;background:#fff0f2}.v3-theme-item{display:flex;align-items:center;margin-left:3px}.v3-theme-toggle{display:grid!important;width:36px;height:36px;margin:0 3px;padding:0!important;place-items:center;border:1px solid #d4e3ea;border-radius:11px!important;color:#315b71!important;background:#f5f9fb;transition:.18s ease}.v3-theme-toggle:hover{border-color:#9fcbd9;color:#087ea3!important;background:#eaf6f9}.v3-theme-toggle i{font-size:14px}.dark-mode .v3-user-trigger:hover,.dark-mode .v3-user-menu.show .v3-user-trigger{border-color:#385869;background:#213d4b!important}.dark-mode .v3-user-trigger-copy strong{color:#ddecf2}.dark-mode .v3-user-trigger-copy small,.dark-mode .v3-user-chevron{color:#9db4bf}.dark-mode .navbar-nav>.v3-user-menu>.v3-user-dropdown{border-color:#385462;background:#172b36}.dark-mode .v3-profile-actions{background:#172b36}.dark-mode .v3-profile-action{border-color:#35515f;color:#d8e8ee!important;background:#203946}.dark-mode .v3-profile-action:hover{border-color:#4b788a;background:#274654}.dark-mode .v3-profile-action small{color:#9db2bd}.dark-mode .v3-profile-action--logout{color:#ffb7c2!important}.dark-mode .v3-theme-toggle{border-color:#3c5867;color:#ffd36b!important;background:#203946}@media(max-width:767px){.navbar-nav>.v3-user-menu>.v3-user-dropdown{position:fixed!important;top:53px;right:8px;left:auto;width:min(320px,calc(100vw - 16px))!important;min-width:0}.v3-user-trigger-copy,.v3-user-chevron{display:none}}
    .v3-user-trigger.dropdown-toggle:after{display:none}.v3-user-trigger-avatar{float:none!important}
    .v3-profile-action>.v3-profile-action-icon{display:flex!important;align-items:center;justify-content:center;flex-direction:row;min-width:34px;padding:0;line-height:1}
    .v3-profile-action>.v3-profile-action-icon i{display:block;width:auto;margin:0;line-height:1;text-align:center}
    .v3-profile-action--medical{border-color:#b8e3e6!important;color:#075a72!important;background:linear-gradient(120deg,#eefbfd,#e7f8f6)!important}.v3-profile-action--medical .v3-profile-action-icon{color:#fff;background:linear-gradient(135deg,#087ca4,#13a99f);box-shadow:0 5px 12px rgba(8,124,164,.2)}.v3-profile-action--medical:hover{border-color:#69c7cf!important;background:linear-gradient(120deg,#e2f7fa,#daf4ef)!important}.v3-profile-action--medical .v3-profile-action-arrow{margin-left:auto;color:#1594a2;font-size:10px}.dark-mode .v3-profile-action--medical{border-color:#326b72!important;color:#dffbfc!important;background:linear-gradient(120deg,#173f4c,#174844)!important}.dark-mode .v3-profile-action--medical:hover{border-color:#4c9da3!important;background:linear-gradient(120deg,#1b4b59,#1c554f)!important}
    .v3-language-menu{margin:0 2px}.v3-language-toggle{display:grid!important;width:38px;height:38px;margin-top:1px;padding:0!important;place-items:center;border:1px solid transparent;border-radius:11px;transition:.18s ease}.v3-language-toggle:hover,.v3-language-menu.show .v3-language-toggle{border-color:#c9e0ea;background:#edf7fa!important}.v3-language-flag{display:block;width:23px;height:17px;border:1px solid rgba(20,68,92,.18);border-radius:3px;object-fit:cover;box-shadow:0 1px 3px rgba(20,68,92,.18)}.navbar-nav>.v3-language-menu>.v3-language-dropdown{width:205px;min-width:205px;margin-top:9px;padding:7px;border:1px solid #d5e4eb;border-radius:13px;box-shadow:0 14px 34px rgba(17,61,82,.18)}.v3-language-option{display:flex!important;align-items:center;gap:11px;margin:1px 0;padding:9px 10px!important;border-radius:8px;color:#31566a!important;font-size:12px;font-weight:600;transition:.16s ease}.v3-language-option:hover{background:#edf7fa!important}.v3-language-option.active{color:#fff!important;background:linear-gradient(120deg,#087ca4,#109caa)!important}.v3-language-option .v3-language-check{margin-left:auto;font-size:10px}.dark-mode .v3-language-toggle:hover,.dark-mode .v3-language-menu.show .v3-language-toggle{border-color:#385869;background:#213d4b!important}.dark-mode .navbar-nav>.v3-language-menu>.v3-language-dropdown{border-color:#385462;background:#172b36}.dark-mode .v3-language-option{color:#d8e8ee!important}.dark-mode .v3-language-option:hover{background:#203946!important}
    .v3-company-toggle{display:flex!important;align-items:center;max-width:245px;gap:2px;border-radius:11px;transition:.18s ease}.v3-company-toggle[href]:hover{background:#edf7fa!important}.v3-company-label{display:block;max-width:205px;overflow:hidden;color:#526b79;font-size:12px;font-weight:500;text-overflow:ellipsis;white-space:nowrap}.dark-mode .v3-company-toggle[href]:hover{background:#213d4b!important}.dark-mode .v3-company-label{color:#b8cbd4}@media(max-width:575px){.v3-company-toggle{max-width:180px}.v3-company-label{max-width:140px}}
    .v3-period-toggle{display:flex!important;align-items:center;gap:7px;margin:1px 2px;padding:7px 10px!important;border:1px solid transparent;border-radius:11px;transition:.18s ease}.v3-period-toggle:hover,.v3-period-menu.show .v3-period-toggle{border-color:#c9e0ea;background:#edf7fa!important}.v3-period-toggle-year{color:#526b79;font-size:12px;font-weight:700}.navbar-nav>.v3-period-menu>.v3-period-dropdown{width:245px;min-width:245px;max-height:min(520px,calc(100vh - 75px));margin-top:8px;padding:0;overflow:hidden;border:1px solid #d5e4eb;border-radius:14px;box-shadow:0 16px 38px rgba(17,61,82,.2)}.v3-period-head{padding:13px 14px 11px;background:linear-gradient(120deg,#086d96,#0a98a5);color:#fff}.v3-period-head strong,.v3-period-head small{display:block}.v3-period-head strong{font-size:12px}.v3-period-head small{margin-top:3px;color:rgba(255,255,255,.8);font-size:9px}.v3-period-list{max-height:430px;padding:6px;overflow-y:auto}.v3-period-option{display:flex!important;align-items:center;gap:10px;margin:1px 0;padding:8px 10px!important;border-radius:8px;color:#31566a!important}.v3-period-option:hover{background:#edf7fa!important}.v3-period-option.active{color:#fff!important;background:linear-gradient(120deg,#087ca4,#109caa)!important}.v3-period-option-year{margin-left:auto;font-weight:700}.v3-period-check{width:12px;margin-left:2px;font-size:9px}.dark-mode .v3-period-toggle-year{color:#b8cbd4}.dark-mode .navbar-nav>.v3-period-menu>.v3-period-dropdown{border-color:#385462;background:#172b36}.dark-mode .v3-period-option{color:#d8e8ee!important}.dark-mode .v3-period-option:hover{background:#203946!important}@media(max-width:575px){.v3-period-label{display:none}.navbar-nav>.v3-period-menu>.v3-period-dropdown{position:fixed!important;top:53px;right:8px;left:auto}}
</style>
<style>
    .aitrol-v3 .hero-cabecera {
        position: relative;
        color: #fff;
        background: linear-gradient(105deg, #167dc4, #0866a8) !important;
        border-bottom: 0;
        box-shadow: 0 4px 16px rgba(9, 67, 111, .2);
    }
    .aitrol-v3 .hero-cabecera .nav-link,
    .aitrol-v3 .hero-cabecera .v3-user-trigger-copy strong,
    .aitrol-v3 .hero-cabecera .v3-user-trigger-copy small,
    .aitrol-v3 .hero-cabecera .v3-user-chevron,
    .aitrol-v3 .hero-cabecera .v3-company-label,
    .aitrol-v3 .hero-cabecera .v3-period-toggle-year {
        color: #fff;
    }
    .aitrol-v3 .hero-cabecera .nav-link:hover,
    .aitrol-v3 .hero-cabecera .nav-link:focus,
    .aitrol-v3 .hero-cabecera .nav-link.active,
    .aitrol-v3 .hero-cabecera .v3-user-menu.show .v3-user-trigger,
    .aitrol-v3 .hero-cabecera .v3-language-menu.show .v3-language-toggle,
    .aitrol-v3 .hero-cabecera .v3-period-menu.show .v3-period-toggle {
        color: #fff;
        background: rgba(255, 255, 255, .14) !important;
        border-color: rgba(255, 255, 255, .2);
    }
    .aitrol-v3 .hero-cabecera .v3-theme-toggle {
        color: #fff !important;
        background: rgba(255, 255, 255, .1);
        border-color: rgba(255, 255, 255, .2);
    }
    .aitrol-v3 .v3-institution-brand {
        position: absolute;
        top: 50%;
        left: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #fff;
        line-height: 1;
        text-decoration: none;
        transform: translate(-50%, -50%);
    }
    .aitrol-v3 .v3-institution-brand strong {
        font-size: 17px;
        font-weight: 900;
        letter-spacing: 2.5px;
    }
    .aitrol-v3 .v3-institution-brand small {
        margin-top: 3px;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: 2px;
    }
    .aitrol-v3 .main-sidebar .brand-link {
        color: #fff;
        background: linear-gradient(105deg, #167dc4, #0866a8);
        border-bottom-color: rgba(255, 255, 255, .16);
    }
    @media (max-width: 991px) {
        .aitrol-v3 .v3-institution-brand {
            display: none;
        }
    }
</style>
<nav class="main-header navbar navbar-expand navbar-white navbar-light hero-cabecera">
    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link botonMenu" data-widget="pushmenu" href="#" role="button" aria-label="Abrir o cerrar menú"><i class="fas fa-bars" aria-hidden="true"></i></a></li>
        <li class="nav-item d-none d-sm-inline-block"><a href="{{ url('v3/admin') }}" class="nav-link">{{ trans('v3/new_menu.inicio') }}</a></li>
        @php
            $activeControllerV3 = config('data.active_controller') ?: config('data.controlador');
            $activeLabelV3 = $activeControllerV3 ? ucwords(strtolower(trans('v3/new_menu.' . $activeControllerV3))) : 'Módulo clínico';
        @endphp
        <li class="nav-item d-none d-sm-inline-block"><a href="javascript:;" class="nav-link active">{{ $activeLabelV3 }}</a></li>
    </ul>
    <a class="v3-institution-brand" href="{{ url('/') }}" aria-label="AITROL Sistema Médico">
        <strong>AITROL</strong>
        <small>SISTEMA MÉDICO</small>
    </a>
    <ul class="navbar-nav ml-auto">
        @if (isset(config('data.permisos')->id_submenu_permiso) && config('data.permisos')->id_submenu_permiso != '')
        {{-- Reservado para el futuro centro de notificaciones de V3. --}}
        @if(false)
        <li class="nav-item dropdown" id="planesnoti"><a id="planes" class="nav-link" data-toggle="dropdown" href="javascript:;"><i class="fas fa-info-circle" style="color:#2980b9;"></i><span class="badge badge-warning navbar-badge"></span></a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                if (isset(config('data.planes')->nombre)) {
                ?>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="dropdown-item dropdown-header" style="text-align: left;"><b>Tipo de plan</b> </span>
                                </td>
                                <td>
                                    <span class="dropdown-item dropdown-header">{{ isset(config('data.planes')->nombre)?config('data.planes')->nombre:'Sin plan' }}</span>
                                </td>
                                </td>
                            </tr>
                            <?php
                            $planpago = config('data.planes');
                            if ($planpago != '') {
                                $tiempo = date('Y-m-d');
                                $created_at = $planpago->created_at;

                                $fecha_actual = new DateTime($tiempo);
                                $fecha_creacion = new DateTime($created_at);
                                $duracion_plan = $planpago->tiempo;
                                $regalias = $planpago->regalias;
                                $fecha_fin_plan = clone $fecha_creacion;
                                if ($regalias > 0) {
                                    $duracion_plan += $regalias;
                                }
                                $fecha_fin_plan->modify("-$duracion_plan days");
                                $dias_restantes = $fecha_actual->diff($fecha_fin_plan)->days;

                                if ($fecha_actual < $fecha_fin_plan) {
                                    $dias_restantes = 0;
                                }
                            } else {
                                $dias_restantes = 0;
                            }
                            ?>
                            <tr>
                                <td><span class="dropdown-item dropdown-header" style="text-align: left;"><b>Dias restantes</b></span></td>
                                <td>
                                    <span class='dropdown-item dropdown-header'>
                                        <?php echo "Quedan $dias_restantes días"; ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php
                }
                ?>
                <?php
                $datos = Sis_medico\v3\contable\DeEmpresas::getDatosFirma(session('id_empresa'));
                if ($datos['Nombre'] != 'Sin Firma' && $datos['Nombre'] != '') {
                ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Nombre:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ ucwords(strtolower($datos['Nombre'])) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>E-mail:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ strtolower($datos['Email']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Emisión:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ $datos['Desde'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Vence:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ $datos['Hasta'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Estado:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ $datos['Vigente'] == 1 ? 'Vigente' : 'Caducado' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Restantes:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ $datos['Expiracion'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; vertical-align: top;">
                                <span class="dropdown-item dropdown-header" style="text-align: left; white-space: nowrap;"><b>Emisor:</b></span>
                            </td>
                            <td style="font-size: small; padding: 4px; vertical-align: top;">
                                {{ $datos['Emisor'] }}
                            </td>
                        </tr>
                    </table>
                <?php
                }
                ?>
            </div>
        </li>
        @endif
        <li class="nav-item"><a class="nav-link" data-widget="navbar-search" href="#" role="button" aria-label="Abrir búsqueda"><i class="fas fa-search" aria-hidden="true"></i></a>
            <div class="navbar-search-block">
                <form class="form-inline" id="frmbuscador" method="post" action="{{ url('v3/utilidades/buscarTodo') }}">{{ csrf_field() }}
                    <div class="input-group input-group-sm"><input class="form-control form-control-navbar" id="textBuscador" name="textBuscador" type="search" placeholder="Buscar..." aria-label="Buscar en el sistema">
                        <div class="input-group-append"><button class="btn btn-navbar" type="submit" aria-label="{{ trans('v3/accessibility.search_system') }}"><i class="fas fa-search" aria-hidden="true"></i></button><button class="btn btn-navbar" type="button" data-widget="navbar-search" aria-label="{{ trans('v3/accessibility.close_search') }}"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                    </div>
                </form>
            </div>
        </li>
        {{-- Búsqueda por voz oculta: no renderizar el botón de micrófono en la barra superior. --}}
        @endif
        <?php
        $idiomaActual = Illuminate\Support\Facades\App::getLocale();
        $banderasIdioma = array('es' => 'es', 'en' => 'gb', 'pt' => 'pt');
        $banderaActual = isset($banderasIdioma[$idiomaActual]) ? $banderasIdioma[$idiomaActual] : 'es';
        ?>
        <li class="nav-item dropdown v3-language-menu">
            <a class="nav-link v3-language-toggle" data-toggle="dropdown" href="#" aria-label="{{ trans('v3/accessibility.language') }}" aria-haspopup="true" aria-expanded="false">
                <img class="v3-language-flag" src="{{ url('v3/plugins/flag-icon-css/flags/4x3/'.$banderaActual.'.svg') }}" alt="" aria-hidden="true">
            </a>
            <div class="dropdown-menu dropdown-menu-right v3-language-dropdown">
                <a href="{{ route('change_lang', ['lang' => 'es']) }}" class="dropdown-item v3-language-option {{ $idiomaActual === 'es' ? 'active' : '' }}" lang="es">
                    <img class="v3-language-flag" src="{{ url('v3/plugins/flag-icon-css/flags/4x3/es.svg') }}" alt="" aria-hidden="true">
                    <span>{{ trans('v3/new_menu.idioma_es') }}</span>
                    @if ($idiomaActual === 'es')<i class="fas fa-check v3-language-check" aria-hidden="true"></i>@endif
                </a>
                <a href="{{ route('change_lang', ['lang' => 'en']) }}" class="dropdown-item v3-language-option {{ $idiomaActual === 'en' ? 'active' : '' }}" lang="en">
                    <img class="v3-language-flag" src="{{ url('v3/plugins/flag-icon-css/flags/4x3/gb.svg') }}" alt="" aria-hidden="true">
                    <span>{{ trans('v3/new_menu.idioma_en') }}</span>
                    @if ($idiomaActual === 'en')<i class="fas fa-check v3-language-check" aria-hidden="true"></i>@endif
                </a>
                <a href="{{ route('change_lang', ['lang' => 'pt']) }}" class="dropdown-item v3-language-option {{ $idiomaActual === 'pt' ? 'active' : '' }}" lang="pt">
                    <img class="v3-language-flag" src="{{ url('v3/plugins/flag-icon-css/flags/4x3/pt.svg') }}" alt="" aria-hidden="true">
                    <span>{{ trans('v3/new_menu.idioma_pt') }}</span>
                    @if ($idiomaActual === 'pt')<i class="fas fa-check v3-language-check" aria-hidden="true"></i>@endif
                </a>
            </div>
        </li>
        @if (isset(config('data.permisos')->id_submenu_permiso) && config('data.permisos')->id_submenu_permiso != '')
        @php
            $empresaActiva = config('data.empresa');
            $empresaRuc = trim((string) data_get($empresaActiva, 'id', session('id_empresa')));
            $empresaNombre = trim((string) data_get($empresaActiva, 'nombre_corto', ''));
            if ($empresaNombre === '') {
                $empresaNombre = trim((string) data_get($empresaActiva, 'nombrecomercial', ''));
            }
            if ($empresaNombre === '') {
                $empresaNombre = trim((string) data_get($empresaActiva, 'razonsocial', ''));
            }
            $empresaEtiqueta = $empresaNombre !== '' ? $empresaNombre . ($empresaRuc !== '' ? ' | ' . $empresaRuc : '') : $empresaRuc;
            $puedeCambiarEmpresa = Route::has('tenant.select')
                && isset($tenantsDisponibles)
                && $tenantsDisponibles->count() > 1;
        @endphp
        <li class="nav-item v3-company-menu">
            <a class="nav-link v3-company-toggle" href="{{ $puedeCambiarEmpresa ? route('tenant.select') : 'javascript:void(0)' }}" title="{{ $puedeCambiarEmpresa ? 'Cambiar empresa: ' . $empresaEtiqueta : $empresaEtiqueta }}" aria-label="{{ $puedeCambiarEmpresa ? 'Cambiar empresa: ' . $empresaEtiqueta : $empresaEtiqueta }}">
                <i class="fas fa-building mr-2" aria-hidden="true"></i>
                <span class="v3-company-label">{{ $empresaEtiqueta }}</span>
            </a>
        </li>
        <li class="nav-item dropdown v3-period-menu">
            <a class="nav-link v3-period-toggle" data-toggle="dropdown" href="#" title="{{ trans('v3/accessibility.active_period') }}: {{ session('periodo') }}" aria-label="{{ trans('v3/accessibility.active_period') }}: {{ session('periodo') }}" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-stopwatch" aria-hidden="true"></i>
                <span class="v3-period-label">{{ trans('v3/new_varios.periodo') }}</span>
                <span class="v3-period-toggle-year">{{ session('periodo') }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right v3-period-dropdown">
                <div class="v3-period-head">
                    <strong>{{ trans('v3/accessibility.accounting_period') }}</strong>
                    <small>{{ trans('v3/accessibility.select_period') }}</small>
                </div>
                <div class="v3-period-list">
                    @for ($anio = (int) date('Y'); $anio >= 2010; $anio--)
                        <a href="javascript:;" onclick="seleccionarPeriodo({{ $anio }})" class="dropdown-item v3-period-option {{ $anio === (int) session('periodo') ? 'active' : '' }}" aria-current="{{ $anio === (int) session('periodo') ? 'true' : 'false' }}">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            <span>{{ trans('v3/new_varios.periodo') }}</span>
                            <span class="v3-period-option-year">{{ $anio }}</span>
                            <i class="fas fa-check v3-period-check {{ $anio === (int) session('periodo') ? '' : 'invisible' }}" aria-hidden="true"></i>
                        </a>
                    @endfor
                </div>
            </div>
        </li>
        @endif
        <li class="nav-item dropdown user-menu v3-user-menu">
            <a href="#" class="nav-link dropdown-toggle v3-user-trigger" data-toggle="dropdown" aria-label="{{ trans('v3/accessibility.user_menu') }}" aria-haspopup="true">
                <?php
                $usuarioV3 = config('data.usuario');
                if (!is_object($usuarioV3)) {
                    $usuarioV3 = Illuminate\Support\Facades\Auth::user();
                }
                $existe = url('v3/') . '/dist/img/sinfoto.png';
                $imagenUsuario = is_object($usuarioV3) && isset($usuarioV3->imagen_url) ? trim($usuarioV3->imagen_url) : '';
                $url = url('/') . '/avatars/' . $imagenUsuario;
                if ($imagenUsuario !== '') {
                    $existe = $url;
                }
                $nombreUsuario = is_object($usuarioV3)
                    ? trim((isset($usuarioV3->nombre1) ? $usuarioV3->nombre1 : '') . ' ' . (isset($usuarioV3->apellido1) ? $usuarioV3->apellido1 : ''))
                    : '';
                if ($nombreUsuario === '' && is_object($usuarioV3) && isset($usuarioV3->name)) {
                    $nombreUsuario = $usuarioV3->name;
                }
                $nombreUsuario = $nombreUsuario !== '' ? ucwords(strtolower($nombreUsuario)) : 'Usuario';
                $cargoUsuario = is_object($usuarioV3) && isset($usuarioV3->cargo) ? trim((string) $usuarioV3->cargo, " \t\n\r\0\x0B-") : '';
                if ($cargoUsuario === '' && is_object($usuarioV3) && method_exists($usuarioV3, 'isSystemUser') && $usuarioV3->isSystemUser()) {
                    $cargoUsuario = 'Administrador del sistema';
                }
                $cargoUsuario = $cargoUsuario !== '' ? $cargoUsuario : 'Cuenta institucional';
                ?>
                <img src="<?= $existe; ?>" class="user-image v3-user-trigger-avatar" alt="Foto de {{ $nombreUsuario }}">
                <span class="v3-user-trigger-copy"><strong>{{ $nombreUsuario }}</strong><small>Mi cuenta</small></span>
                <i class="fas fa-chevron-down v3-user-chevron" aria-hidden="true"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right v3-user-dropdown">
                <div class="v3-profile-head">
                    <div class="v3-profile-identity">
                        <div class="v3-profile-avatar-wrap"><img src="<?= $existe; ?>" class="v3-profile-avatar" alt=""><span class="v3-profile-presence" aria-hidden="true"></span></div>
                        <div class="v3-profile-copy"><small>CUENTA ACTIVA</small><h3>{{ $nombreUsuario }}</h3><p>{{ $cargoUsuario }}</p></div>
                    </div>
                </div>
                <div class="v3-profile-actions">
                    <a href="{{ url('v3/') }}/configuraciones/perfil" class="v3-profile-action"><span class="v3-profile-action-icon"><i class="fas fa-user-cog" aria-hidden="true"></i></span><span><strong>Mi perfil</strong><small>Datos personales y configuración</small></span></a>
                    <a href="{{ url('profile') }}" class="v3-profile-action"><span class="v3-profile-action-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></span><span><strong>Seguridad de mi cuenta</strong><small>Activar o desactivar el doble factor</small></span></a>
                    @if(Auth::user() && Auth::user()->canSwitchToMedicalWorkspace())
                    <a href="{{ url('inicio') }}" class="v3-profile-action v3-profile-action--medical"><span class="v3-profile-action-icon"><i class="fas fa-stethoscope" aria-hidden="true"></i></span><span><strong>{{ trans('v3/new_menu.abrir_area_medica') }}</strong><small>{{ trans('v3/new_menu.area_medica_descripcion') }}</small></span><i class="fas fa-arrow-right v3-profile-action-arrow" aria-hidden="true"></i></a>
                    @endif
                    @if(Auth::user() && method_exists(Auth::user(), 'isSystemUser') && Auth::user()->isSystemUser() && Route::has('v3.system-config.show'))
                    <a href="{{ route('v3.system-config.show') }}" class="v3-profile-action"><span class="v3-profile-action-icon"><i class="fas fa-server" aria-hidden="true"></i></span><span><strong>Configuración del servidor</strong><small>Consulta protegida y de solo lectura</small></span></a>
                    @endif
                    <a href="{{ route('logout') }}" class="v3-profile-action v3-profile-action--logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><span class="v3-profile-action-icon"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></span><span><strong>Cerrar sesión</strong><small>Salir de Aitrol de forma segura</small></span></a>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </div>
        </li>
        <li class="nav-item v3-theme-item"><a href="javascript:;" class="nav-link v3-theme-toggle" id="modo" role="button" aria-label="Cambiar a modo oscuro" aria-pressed="false" title="Cambiar a modo oscuro"><i class="fas fa-moon" aria-hidden="true"></i></a></li>
    </ul>
</nav>
