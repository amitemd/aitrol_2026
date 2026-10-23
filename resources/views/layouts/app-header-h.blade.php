<?php
$imagen = Auth::user()->imagen_url;
if ($imagen == ' ') {
    $imagen = 'avatar.jpg';
}
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$id_empresa = "0992704152001";
?>

<nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
    <div class="navbar-header d-xl-block d-none">
        <ul class="nav navbar-nav">
            <li class="nav-item"><a class="navbar-brand" href="{{url('hospital/inicio')}}">
                    <img src="{{asset('/hc4/img/logo1.png')}}" width="200" srcset="">

                    <!--  <h2 class="brand-text mb-0">Vuexy</h2> -->
                </a></li>
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

        </ul>

        <ul class="nav navbar-nav align-items-center ml-auto">

            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="{{$classicon}}"></i></a></li>


            <li class="nav-item dropdown dropdown-user">
                <a class=" dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name font-weight-bolder">{{ Auth::user()->nombre1}} {{Auth::user()->apellido1}}</span>
                        <span class="user-status">{{ Auth::user()->id_tipo}}</span>
                    </div>
                    <span class="avatar"><img class="round" src="{{asset('/avatars').'/'.$imagen}}" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="page-profile.html">
                        <i class="mr-50" data-feather="user"></i> {{trans('hospital.perfil')}}
                    </a>
                    <a class="dropdown-item" href="app-email.html">
                        <i class="mr-50" data-feather="mail"></i> Inbox
                    </a>
                    <a class="dropdown-item" href="app-todo.html">
                        <i class="mr-50" data-feather="check-square"></i> Task
                    </a>
                    <a class="dropdown-item" href="app-chat.html">
                        <i class="mr-50" data-feather="message-square"></i> Chats
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="page-account-settings.html">
                        <i class="mr-50" data-feather="settings"></i> {{trans('hospital.configuracion')}}
                    </a>
                    <a class="dropdown-item" href="page-pricing.html">
                        <i class="mr-50" data-feather="credit-card"></i> Pricing
                    </a>
                    <a class="dropdown-item" href="page-faq.html">
                        <i class="mr-50" data-feather="help-circle"></i> FAQ
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="mr-50" data-feather="power"></i> {{trans('hospital.cerrar_sesión')}}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/xls.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/jpg.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/pdf.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/doc.png" alt="png" height="32"></div>
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
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
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
<style>
    .remove {
        display: none;
    }

    .por {
        margin-top: -4%;
    }
</style>

<!-- BEGIN: Main Menu-->
<div class="horizontal-menu-wrapper">
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border" id="quitar" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">

            </ul>
        </div>
        <div class="shadow-bottom"></div>

        <div class="navbar-container main-menu-content" data-menu="menu-container">
            <div class="row">
                <div class="col-md-12">
                    <h1 style="text-align: center;"><img src="{{asset('/')}}hc4/img/art_doc.png" style="width: 36px;"> &nbsp;&nbsp;<b>{{trans('hospital.area_medica')}}</b></h1>
                </div>
            </div>
        </div>
    </div>

    @if(isset($data))
        <div id="hospi_nav">
            <div class="navbar-container main-menu-content" data-menu="menu-container" style="margin-top: 3%;margin-left: 5%;z-index:9999">
                <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border">
                    <div align="center" class="col-md-12">
                        <h2><b>{{trans('hospital.datos_paciente')}} {{$data->nombre_paciente}}</b></h2>
                    </div>
                </div>
            </div>
            <div class="navbar-container main-menu-content" data-menu="menu-container" style="margin-top: 1%;margin-left: 5%;z-index:9999">
                <div class="header-navbar navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border">
                    <table class="table">
                        <thead>
                            <tr>
                                <td style="text-align: center;" width="10%">
                                    <h5><b style="text-transform: uppercase;"> {{trans('hospital.dias_hospitalizados')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="20%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.medico_tratante')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="5%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.edad')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="10%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.seguro')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="20%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.alergias')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="15%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.religion')}}</b></h5>
                                </td>
                                <td style="text-align: center;" width="20%">
                                    <h5><b style="text-transform: uppercase;">{{trans('hospital.antecedentes')}}</b></h5>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center" width="10%">{{$data->dias_hospitalizados}} {{trans('hospital.dia')}}(s)</td>
                                <td width="20%">{{$data->nombre_doctor}}</td>
                                <td align="center" width="5%">{{$data->edad}}</td>
                                <td width="10%">{{$data->seguro}}</td>
                                <td width="20%">{{$str_alergias==''?trans('hospital.no_posee'):$str_alergias}}</td>
                                <td width="15%">{{$data->religion}}</td>
                                <td width="20%">
                                    {{$data->ante_familiares}} <br />
                                    {{$data->ante_patologicos}}<br />
                                    {{$data->ante_quirurgicos}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    window.onscroll = function() {
        myFunction()
    };
    var navbar = document.getElementById("quitar");
    var hospi_nav =document.getElementById("hospi_nav");
    var sticky = navbar.offsetTop;

    function myFunction() {
        if (window.pageYOffset >= sticky) {
            navbar.classList.add("remove");
            hospi_nav.classList.add("por");
        } else {
            navbar.classList.remove("remove");
            hospi_nav.classList.remove("por");
        }
    }
</script>