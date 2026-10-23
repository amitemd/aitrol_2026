<nav class="main-header navbar navbar-expand navbar-white navbar-light hero-cabecera">
    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link botonMenu" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        <li class="nav-item d-none d-sm-inline-block"><a href="{{ url('v3/admin') }}" class="nav-link">{{ trans('v3/new_menu.inicio') }}</a></li>
        <li class="nav-item d-none d-sm-inline-block"><a href="javascript:;" class="nav-link active">{{ ucwords(strtolower(trans('v3/new_menu.' . config('data.controlador')))) }}</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
        @if (isset(config('data.permisos')->id_submenu_permiso) && config('data.permisos')->id_submenu_permiso != '')
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
        <li class="nav-item dropdown" id="alertNoti" style="display:<?= config("data.countNotificacion.numCotizacion") == 0 ? 'none' : 'block'; ?>"><a id="traerNotificacion" class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span class="badge badge-warning navbar-badge">{{config("data.countNotificacion.numCotizacion")}}</span></a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"><span class="dropdown-item dropdown-header">{{config('data.countNotificacion.eventos')}} Notificaciones</span>
                <div class="dropdown-divider"></div>
                <div id="contenedorNotificaciones"></div>
                <div class="dropdown-divider"></div><a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item"><a class="nav-link" data-widget="navbar-search" href="#" role="button"><i class="fas fa-search"></i></a>
            <div class="navbar-search-block">
                <form class="form-inline" id="frmbuscador" method="post" action="{{ url('v3/utilidades/buscarTodo') }}">{{ csrf_field() }}
                    <div class="input-group input-group-sm"><input class="form-control form-control-navbar" id="textBuscador" name="textBuscador" type="search" placeholder="Buscar..." aria-label="Search">
                        <div class="input-group-append"><button class="btn btn-navbar" type="submit"><i class="fas fa-search"></i></button><button class="btn btn-navbar" type="button" data-widget="navbar-search"><i class="fas fa-times"></i></button></div>
                    </div>
                </form>
            </div>
        </li>
        <li class="nav-item" id="botonhablar"><a class="nav-link" href="javascript:;" onclick="procesar()" id="procesar" role="button"><i class="fa fa-microphone"></i></a></li>
        @endif
        <li class="nav-item dropdown"><a class="nav-link" data-toggle="dropdown" href="#"><i class="flag-icon flag-icon-{{ App::getLocale() }}"></i></a>
            <div class="dropdown-menu dropdown-menu-right p-0">
                <?php
                $leng = '';
                $activoEs = '';
                $activoEn = '';
                $activoPt = '';
                if (Illuminate\Support\Facades\App::getLocale() == 'es') {
                    $activoEs = 'active';
                } elseif (Illuminate\Support\Facades\App::getLocale() == 'en') {
                    $activoEn = 'active';
                } elseif (Illuminate\Support\Facades\App::getLocale() == 'pt') {
                    $activoPt = 'active';
                }
                echo '<a href="' . route('change_lang', ['lang' => 'es']) . '" class="dropdown-item ' . $activoEs . '"><i class="flag-icon flag-icon-es mr-2"></i> Español</a><a href="' . route('change_lang', ['lang' => 'en']) . '" class="dropdown-item ' . $activoEn . '"><i class="flag-icon flag-icon-en mr-2"></i> Ingles</a><a href="' . route('change_lang', ['lang' => 'pt']) . '" class="dropdown-item ' . $activoPt . '"><i class="flag-icon flag-icon-pt mr-2"></i> Portugues</a>';
                ?>
            </div>
        </li>
        @if (isset(config('data.permisos')->id_submenu_permiso) && config('data.permisos')->id_submenu_permiso != '')
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#"><i class="fas fa-building mr-2"></i>
                <span class="float-right text-muted text-sm"><span class="botones"><?= isset(config('data.empresa')->nombre_corto) ? config('data.empresa')->nombre_corto . ' | ' . config('data.empresa')->id : ''; ?></span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                if (config('data.empresas_') != null && count(config('data.empresas_')) > 0) {
                    foreach (config('data.empresas_') as $row) {
                        $class_ = '';
                        $color_ = '';
                        if ($row->id == session('id_empresa')) {
                            $class_ = 'active';
                            $color_ = 'style="color:white;"';
                        }
                        echo '<div class="dropdown-divider"></div><a href="javascript:;" onclick="seleccionarEmpresa(\'' . $row->id . '\')" class="dropdown-item ' . $class_ . '"><span class="text-sm">' . $row->id . ' | ' . $row->nombre_corto . '</span></a>';
                    }
                }
                ?>
            </div>
        </li>
        <li class="nav-item dropdown"><a class="nav-link" data-toggle="dropdown" href="#"><i class="fas fa-stopwatch mr-2"></i>
                <?php
                if (session('periodo') == '') {
                    session(['periodo' => date('Y')]);
                }
                ?>
                <span class="float-right text-muted text-sm"><span class="botones">{{ session('periodo') }}</span></span></a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                for ($anio = date('Y'); 2010 <= $anio; $anio--) {
                    $class = '';
                    $color = '';
                    if ($anio == session('periodo')) {
                        $class = 'active';
                        $color = 'style="color:white;"';
                    }
                    echo '<div class="dropdown-divider"></div><a href="javascript:;" onclick="seleccionarPeriodo(\'' . $anio . '\')" class="dropdown-item ' . $class . '" ><i class="fas fa-stopwatch mr-2"></i> Periodo<span class="float-right text-sm" ' . $color . '>' . $anio . '</span></a>';
                }
                ?>
            </div>
        </li>
        @endif
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <?php
                $existe = url('v3/') . '/dist/img/sinfoto.png';
                $url = url('/') . '/avatars/' . config('data.usuario')->imagen_url;
                if (trim(config('data.usuario')->imagen_url) != null && trim(config('data.usuario')->imagen_url) != '') {
                    $existe = $url;
                }
                ?>
                <img src="<?= $existe; ?>" class="user-image img-circle elevation-2" alt="User Image"><span class="d-none d-md-inline">{{ ucwords(strtolower(config('data.usuario')->nombre1 . ' ' . config('data.usuario')->apellido1)) }}</span></a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-primary"><img src="<?= $existe; ?>" class="img-circle elevation-2" alt="User Image">
                    <p>{{ ucwords(strtolower(config('data.usuario')->nombre1 . ' ' . config('data.usuario')->apellido1)) }} - {{ config('data.usuario')->cargo }}<!--<small>Member since Nov. 2012</small></p>-->
                </li>
                <!--<li class="user-body">
                    <div class="row">
                        <div class="col-4 text-center"><a href="#">Followers</a></div>
                        <div class="col-4 text-center"><a href="#">Sales</a></div>
                        <div class="col-4 text-center"><a href="#">Friends</a></div>
                    </div>
                </li>-->
                <li class="user-footer">
                    <a href="{{ url('v3/') }}/configuraciones/perfil" class="btn btn-default btn-flat">Perfil</a>
                    <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-right" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Salir</a>
                </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </ul>
        </li>
        <li class="nav-item"><a href="javascript:;" class="nav-link" id="modo"><i class="fa-regular fa-moon" aria-hidden="true"></i></a></li>
    </ul>
</nav>
<div class="modal fade modalTotal" id="notiModal" tabindex="-1" role="dialog" aria-labelledby="titulo_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{ trans('v3/new_varios.cargando') }}...</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body" id="cuerpo_modal"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between"><button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{ trans('v3/new_varios.cerrar') }}</button></div>
        </div>
    </div>
</div>