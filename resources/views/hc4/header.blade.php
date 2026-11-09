<?php
$imagen = trim((string) Auth::user()->imagen_url);
if ($imagen === '') {
    $imagen = 'avatar.jpg';
}
$defaultAvatarSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="50" fill="#e7f4f8"/><circle cx="50" cy="37" r="18" fill="#0b78bd"/><path d="M17 92c3-24 17-36 33-36s30 12 33 36" fill="#0b78bd"/></svg>';
$defaultAvatarUrl = 'data:image/svg+xml;base64,' . base64_encode($defaultAvatarSvg);
?>

<style type="text/css">
.main-header {
    position: relative;
    z-index: 100100 !important;
    height: 58px;
    border-bottom: 1px solid #e5e0ef;
    background: rgba(255,255,255,.98);
    box-shadow: 0 4px 14px rgba(61, 45, 112, .06);
}

.main-header .logo,
.main-header .navbar {
    height: 58px;
    min-height: 58px;
}

.main-header .logo {
    display: flex;
    align-items: center;
}

.main-header .logo img {
    max-width: 178px;
    max-height: 38px;
    object-fit: contain;
}

.main-header .navbar,
.hc4-navbar-actions,
.hc4-header-list,
.hc4-user-dropdown.open,
.hc4-language-dropdown.open {
    overflow: visible !important;
}

.navbar, .logo {
    /*background: url({{asset('/imagenes')}}/cat-nav-content.png) repeat-x scroll 0 0 transparent;*/
  }

  .example-8 .navbar-brand {
    display: none !important;
    background: url('{{asset('/imagenes')}}/logo1.png') center / contain no-repeat;
    width: 200px;
    height: 50px;
    transform: translateX(-60%);
    left: 43%;
    position: absolute;
  }

  @media screen and (max-width:640px) {
      /* reglas CSS */
      .example-8 .navbar-brand {
        background: none;
        width: 200px;
        height: 50px;
        transform: translateX(-60%);
        left: 43%;
        position: absolute;
      }
  }
  @media (max-width: 768px) {
    .example-8 .navbar-brand {
        left: 30%;
    }
    #area_trabajo_2{
      font-size: 9px;
    }
    #area_trabajo{
      font-style: 9px;
    }

    .parent{
      font-size: 9px;
    }

    .cuerpo{
      font-size: 9px;
    }
    .area_trabajo_2 {
      font-size: 9px;
    }
  }

  .hc4-navbar-actions {
    float: right;
    height: 58px;
  }

  .hc4-navbar-actions > .hc4-header-list {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center;
    height: 58px;
    margin: 0 !important;
  }

.hc4-navbar-actions > .hc4-header-list > li {
    display: flex !important;
    float: none !important;
    align-items: center;
    height: 58px;
    white-space: nowrap;
}

  .hc4-context-menu {
    right: 0 !important;
    left: auto !important;
    width: 260px !important;
    padding: 8px !important;
    border: 1px solid #cfe0e8 !important;
    border-radius: 12px !important;
    background: #fff !important;
    box-shadow: 0 14px 34px rgba(0,55,91,.2) !important;
    z-index: 100200 !important;
  }

  .hc4-context-user {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
    padding: 9px 10px 11px;
    border-bottom: 1px solid #e1ebf0;
  }

  .hc4-context-user img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
  .hc4-context-user strong,
  .hc4-context-user small { display: block; }
  .hc4-context-user strong { color: #173f57; font-size: 12px; line-height: 1.25; }
  .hc4-context-user small { margin-top: 2px; color: #718897; font-size: 10px; }

  .hc4-context-title {
    padding: 7px 10px 4px;
    color: #7a8e9a;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .hc4-context-menu > li > a {
    display: flex !important;
    align-items: center;
    gap: 9px;
    min-height: 36px;
    padding: 8px 10px !important;
    color: #294e62 !important;
    border-radius: 8px;
    font-size: 12px;
  }

  .hc4-context-menu > li > a:hover,
  .hc4-context-menu > li > a:focus { color: #000000 !important; background: #edf7fa !important; }
  .hc4-context-menu > li > a > .glyphicon,
  .hc4-context-menu > li > a > .fa { width: 18px; color: #087eae; text-align: center; }
  .hc4-context-menu .hc4-context-check { margin-left: auto; color: #16a09b; }
  .hc4-context-arrow { margin-left: auto; color: #7b929f; font-size: 10px; }
  .hc4-context-menu .divider { margin: 6px 4px !important; background-color: #e1ebf0 !important; }
  .hc4-context-menu .hc4-context-signout { color: #b53b3b !important; }
  .hc4-context-menu .hc4-context-signout > .glyphicon { color: #c84b4b; }

  html.dark-layout .hc4-context-menu { border-color: #3a5362 !important; background: #182c39 !important; }
  html.dark-layout .hc4-context-user { border-color: #3a5362; }
  html.dark-layout .hc4-context-user strong,
  html.dark-layout .hc4-context-menu > li > a { color: #dce8f1 !important; }
  html.dark-layout .hc4-context-user small,
  html.dark-layout .hc4-context-title { color: #9fb4c2; }
  html.dark-layout .hc4-context-menu > li > a:hover { background: #203b49 !important; }

  .hc4-context-language { position: relative; }
  .hc4-language-submenu {
    position: absolute;
    top: -8px;
    right: calc(100% + 8px);
    left: auto;
    display: none;
    width: 190px;
    margin: 0;
    padding: 8px;
    list-style: none;
    border: 1px solid #cfe0e8;
    border-radius: 11px;
    background: #fff;
    box-shadow: 0 12px 28px rgba(0,55,91,.18);
  }
  .hc4-context-language.open > .hc4-language-submenu { display: block; }
  .hc4-context-language.open .hc4-context-arrow { transform: rotate(-90deg); }
  .hc4-language-submenu > li > a {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 36px;
    padding: 7px 9px;
    color: #294e62;
    border-radius: 7px;
    font-size: 12px;
  }
  .hc4-language-submenu > li > a:hover { color: #000000; background: #edf7fa; }
  html.dark-layout .hc4-language-submenu { border-color: #3a5362; background: #182c39; }
  html.dark-layout .hc4-language-submenu > li > a { color: #dce8f1; }
  html.dark-layout .hc4-language-submenu > li > a:hover { background: #203b49; }

  @media (max-width: 560px) {
    .hc4-language-submenu { right: 0; top: 100%; margin-top: 5px; }
  }

  .hc4-header-action {
    display: flex !important;
    align-items: center;
    gap: 7px;
    min-height: 46px;
    padding: 0 12px !important;
    color: #243746 !important;
    border-radius: 8px;
  }

  .hc4-navbar-module {
    position: absolute;
    top: 50%;
    left: 50%;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 13px;
    color: #51418d;
    border: 1px solid #ddd6ee;
    border-radius: 10px;
    background: linear-gradient(135deg, #faf8fe, #f1edfa);
    box-shadow: 0 3px 10px rgba(74, 52, 139, .08);
    font-size: 12px;
    font-weight: 600;
    transform: translate(-50%, -50%);
    white-space: nowrap;
  }

  .hc4-navbar-module i {
    color: #7259bf;
    font-size: 14px;
  }

  .hc4-header-action:hover,
  .hc4-header-action:focus,
  .hc4-language-dropdown.open > .hc4-header-action,
  .hc4-user-dropdown.open > .hc4-header-action {
    color: #0a58a8 !important;
    background-color: #eef5fb !important;
  }

  .hc4-workspace-shortcut-link {
    display: inline-flex !important;
    min-height: 38px;
    align-items: center;
    gap: 7px;
    margin: 0 2px;
    padding: 8px 11px !important;
    border: 1px solid #d4e4ec;
    border-radius: 9px;
    color: #174d69 !important;
    background: #f5fafc;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
    transition: .18s ease;
  }

  .hc4-workspace-shortcut-link .glyphicon { color: #0d94a3; }
  .hc4-workspace-shortcut-link--admin .glyphicon { color: #4265c5; }
  .hc4-workspace-shortcut-link:hover,
  .hc4-workspace-shortcut-link:focus {
    border-color: #83c8d5;
    color: #075f82 !important;
    background: #eaf7f9;
    transform: translateY(-1px);
  }

  html.dark-layout .hc4-workspace-shortcut-link {
    border-color: #365668;
    color: #dcebf2 !important;
    background: #1b3442;
  }

  @media (max-width: 767px) {
    .hc4-workspace-shortcut-link { width: 38px; justify-content: center; padding: 8px !important; }
    .hc4-workspace-shortcut-label { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
  }

  .hc4-language-menu {
    right: 0 !important;
    left: auto !important;
    min-width: 215px !important;
    padding: 8px !important;
    border: 1px solid #dce5ed !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 28px rgba(18, 69, 116, .18) !important;
  }

  .hc4-language-menu > li > a {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    min-height: 40px;
    padding: 7px 9px !important;
    color: #34495e !important;
    border-radius: 7px;
  }

  .hc4-language-menu > li > a:hover,
  .hc4-language-menu > li > a:focus,
  .hc4-language-menu > li.active > a {
    color: #124574 !important;
    background-color: #e8f2f9 !important;
  }

  .hc4-language-option {
    display: flex;
    align-items: center;
    gap: 9px;
  }

  .hc4-language-code {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 24px;
    color: #124574;
    background-color: #edf3f7;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
  }

  .hc4-user-trigger > .user-image {
    float: none !important;
    margin: 0 !important;
  }

  .hc4-user-menu {
    right: 0 !important;
    left: auto !important;
    width: 300px !important;
    padding: 0 !important;
    overflow: hidden;
    border: 1px solid #dce5ed !important;
    border-radius: 12px !important;
    box-shadow: 0 12px 32px rgba(18, 69, 116, .2) !important;
    z-index: 100200 !important;
  }

  .hc4-user-menu > .hc4-user-card {
    height: auto !important;
    padding: 22px 20px 18px !important;
    color: #243746 !important;
    background: linear-gradient(180deg, #f8fbfd 0%, #eef5fa 100%) !important;
    text-align: center;
  }

  .hc4-user-avatar {
    width: 76px !important;
    height: 76px !important;
    margin: 0 auto 12px !important;
    object-fit: cover;
    border: 3px solid #fff !important;
    box-shadow: 0 4px 14px rgba(18, 69, 116, .2);
  }

  .hc4-user-name {
    margin: 0 !important;
    color: #243746 !important;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.35;
  }

  .hc4-user-menu > .hc4-user-footer {
      display: flex !important;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px !important;
    background-color: #fff !important;
    border-top: 1px solid #e5edf3;
  }

  .hc4-user-action {
    display: inline-flex !important;
    flex: 1 1 0;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 38px;
    margin: 0 !important;
    padding: 8px 9px !important;
    color: #124574 !important;
    background-color: #f3f7fa !important;
    border: 1px solid #dce5ed !important;
    border-radius: 7px !important;
    font-size: 13px;
  }

  .hc4-v3-action {
    flex: 1 0 100%;
    justify-content: flex-start;
    min-height: 46px;
    padding: 9px 12px !important;
    color: #000000 !important;
    border-color: #b9dce9 !important;
    background: linear-gradient(90deg, #edf9fb 0%, #f2f7ff 100%) !important;
    font-weight: 600;
  }

  .hc4-v3-action .glyphicon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 27px;
    height: 27px;
    color: #fff;
    background: linear-gradient(135deg, #0877be, #0d9488);
    border-radius: 7px;
  }

  .hc4-v3-action::after {
    content: '\e080';
    margin-left: auto;
    font-family: 'Glyphicons Halflings';
    font-size: 11px;
  }

  .hc4-v3-action:hover,
  .hc4-v3-action:focus {
    color: #064b79 !important;
    border-color: #82c4d7 !important;
    background: linear-gradient(90deg, #e3f5f8 0%, #eaf2fc 100%) !important;
  }

  .hc4-user-signout {
    color: #b53b3b !important;
    background-color: #fff7f7 !important;
    border-color: #f0d2d2 !important;
  }

  @media (max-width: 640px) {
    .hc4-navbar-module {
      display: none;
    }

    .hc4-language-label {
      display: none;
    }

    .hc4-user-menu {
      width: 280px !important;
    }
  }

  .hc4-theme-toggle {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: 38px;
    min-height: 36px !important;
    padding: 0 !important;
    border: 1px solid rgba(120,100,190,.22);
    border-radius: 9px !important;
  }

  html.dark-layout .hc4-theme-toggle { color: #ffd36b !important; }
</style>

<div class="modal fade" id="perfil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%; max-width: 100% !important;">
      <div class="modal-content" style="">

      </div>
    </div>
</div>

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a  class="logo">
      <img src="{{asset('/')}}hc4/img/logo_nv.png">
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top example-8" role="navigation" >
      <!-- Sidebar toggle button-->
      <a data-toggle="offcanvas" role="button">

      </a>
      <a class="navbar-brand text-hide" href="#">Brand Text
        </a>
      <span class="hc4-navbar-module"><i class="fa fa-stethoscope" aria-hidden="true"></i> Atención ambulatoria</span>
      <!-- Navbar Right Menu -->

      <div class="navbar-custom-menu hc4-navbar-actions">
        <ul class="nav navbar-nav hc4-header-list">
          @if(Auth::user() && method_exists(Auth::user(), 'canSwitchToHospitalWorkspace') && Auth::user()->canSwitchToHospitalWorkspace())
          <li class="hc4-workspace-shortcut">
            <a href="{{ route('hospital.index') }}" class="hc4-workspace-shortcut-link" title="Ir al área hospitalaria">
              <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
              <span class="hc4-workspace-shortcut-label">Hospitalización</span>
            </a>
          </li>
          @endif
          @if(Auth::user() && method_exists(Auth::user(), 'canSwitchToMedicalWorkspace') && Auth::user()->canSwitchToMedicalWorkspace())
          <li class="hc4-workspace-shortcut">
            <a href="{{ url('v3/admin') }}" class="hc4-workspace-shortcut-link hc4-workspace-shortcut-link--admin" title="Ir al área administrativa">
              <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>
              <span class="hc4-workspace-shortcut-label">Administrativa</span>
            </a>
          </li>
          @endif
          <li class="dropdown hc4-context-dropdown">
            <a href="#" class="dropdown-toggle hc4-header-action" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="Opciones">
              <span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>
              <span class="hidden-xs">Opciones</span>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu hc4-context-menu" role="menu">
              <li class="hc4-context-user">
                <img src="{{asset('/avatars').'/'.$imagen}}" alt="Fotografía del usuario" onerror="this.onerror=null;this.src='{{ $defaultAvatarUrl }}';">
                <span><strong>{{ Auth::user()->nombre1 }} {{ Auth::user()->apellido1 }}</strong><small>{{ Auth::user()->id_tipo }}</small></span>
              </li>
              <li class="hc4-context-title">Apariencia</li>
              <li><a href="#" id="hc4-theme-toggle"><i class="fa fa-moon-o" aria-hidden="true"></i><span class="hc4-theme-label">Modo oscuro</span></a></li>
              <li class="hc4-context-title">Preferencias</li>
              <li class="hc4-context-language">
                <a href="#" class="hc4-language-submenu-toggle"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span><span>Idioma</span><span class="glyphicon glyphicon-chevron-left hc4-context-arrow" aria-hidden="true"></span></a>
                <ul class="hc4-language-submenu">
                  <li><a href="{{ route('change_lang', ['lang' => 'es']) }}"><span class="hc4-language-code">ES</span>{{ trans('main.espanol') }}@if(App::getLocale() == 'es')<span class="glyphicon glyphicon-ok hc4-context-check" aria-hidden="true"></span>@endif</a></li>
                  <li><a href="{{ route('change_lang', ['lang' => 'en']) }}"><span class="hc4-language-code">EN</span>{{ trans('main.ingles') }}@if(App::getLocale() == 'en')<span class="glyphicon glyphicon-ok hc4-context-check" aria-hidden="true"></span>@endif</a></li>
                  <li><a href="{{ route('change_lang', ['lang' => 'pt']) }}"><span class="hc4-language-code">PT</span>{{ trans('main.portugues') }}@if(App::getLocale() == 'pt')<span class="glyphicon glyphicon-ok hc4-context-check" aria-hidden="true"></span>@endif</a></li>
                </ul>
              </li>
              <li class="divider"></li>
              @if(Auth::user() && method_exists(Auth::user(), 'canSwitchToMedicalWorkspace') && Auth::user()->canSwitchToMedicalWorkspace())
              <li><a href="{{ url('v3/admin') }}"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>Área administrativa</a></li>
              @endif
              @if(Auth::user() && method_exists(Auth::user(), 'canSwitchToHospitalWorkspace') && Auth::user()->canSwitchToHospitalWorkspace())
              <li><a href="{{ route('hospital.index') }}"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>Área hospitalaria</a></li>
              @endif
              <li><a data-toggle="modal" data-target="#perfil" data-remote="{{ route('perfil.editar_nuevo') }}" href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>{{ trans('main.perfil') }}</a></li>
              <li><a class="hc4-context-signout" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>{{ trans('main.cerrarSesion') }}</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      {{ csrf_field() }}
   </form>

   <script type="text/javascript">
     (function () {
       var $botonTema = $('#hc4-theme-toggle');
       var $idiomaContextual = $('.hc4-context-language');

       function actualizarIconoTema() {
         var oscuro = document.documentElement.classList.contains('dark-layout');
         $botonTema.find('i').attr('class', oscuro ? 'fa fa-sun-o' : 'fa fa-moon-o');
         $botonTema.find('.hc4-theme-label').text(oscuro ? 'Modo claro' : 'Modo oscuro');
       }

       $botonTema.on('click', function (event) {
         event.preventDefault();
         event.stopPropagation();
         var oscuro = !document.documentElement.classList.contains('dark-layout');
         document.documentElement.classList.toggle('dark-layout', oscuro);
         try {
           window.localStorage.setItem('hospital-theme', oscuro ? 'dark-layout' : 'light-layout');
         } catch (error) {}
         actualizarIconoTema();
       });

       $('.hc4-language-submenu-toggle').on('click', function (event) {
         event.preventDefault();
         event.stopPropagation();
         $idiomaContextual.toggleClass('open');
       });

       $('.hc4-context-dropdown').on('hide.bs.dropdown', function () {
         $idiomaContextual.removeClass('open');
       });

       actualizarIconoTema();
     })();

     $('#perfil').on('hidden.bs.modal', function(){
          $(this).removeData('bs.modal');
          //$(this).find('#imagen_solita').empty().html('');
      });

     var remoto_href = '';
      jQuery('body').on('click', '[data-toggle="modal"]', function() {
          if(remoto_href != jQuery(this).data('remote')) {
              remoto_href = jQuery(this).data('remote');
              jQuery(jQuery(this).data('target')).removeData('bs.modal');

              jQuery(jQuery(this).data('target')).find('.modal-body').empty();
              console.log(remoto_href);
            console.log($(this).data('target'));

            console.log(jQuery(this).data('target') + ' .modal-content');
              jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
          }
      });
   </script>
