<?php 
  $imagen= Auth::user()->imagen_url;
  if($imagen==' '){
    $imagen='avatar.jpg';
  }
?>

<style type="text/css">
.navbar, .logo {
    /*background: url({{asset('/imagenes')}}/cat-nav-content.png) repeat-x scroll 0 0 transparent;*/
  }
 
  .example-8 .navbar-brand {
    background: url({{asset('/imagenes')}}/logoiecedquito.png) center / contain no-repeat;
    width: 300px;
    transform: translateX(-60%);
    left: 50%;
    position: absolute;
  }
  .main-header {
    background: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    box-shadow: 0px 3px 6px #00000040!important;
  }
  .navbar {
    background-color: transparent!important;
  }
  .logo{
    background-color: transparent!important;
  }
</style>
@include('layouts.header_account_styles')

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="{{route('dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>S</b>M</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">{{ config('app.name', 'Sistema Médico') }}</span>
    </a> 

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top example-8" role="navigation" >
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <a class="navbar-brand text-hide" href="#">Brand Text
        </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu" >
        <ul class="nav navbar-nav">
          <li class="dropdown ieced-language-dropdown">
            <a href="#" class="dropdown-toggle ieced-language-trigger" data-toggle="dropdown" role="button" aria-expanded="false">
              <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
              @lang('main.idioma') <span class="caret"></span>
            </a>
            <ul class="dropdown-menu ieced-language-menu" role="menu">
              <li class="{{ App::getLocale() == 'es' ? 'active' : '' }}">
                <a href="{{ route('change_lang', ['lang' => 'es']) }}">
                  <span class="ieced-language-option"><span class="ieced-language-code">ES</span>@lang('main.espanol')</span>
                  @if(App::getLocale() == 'es')<span class="glyphicon glyphicon-ok ieced-language-check" aria-hidden="true"></span>@endif
                </a>
              </li>
              <li class="{{ App::getLocale() == 'en' ? 'active' : '' }}">
                <a href="{{ route('change_lang', ['lang' => 'en']) }}">
                  <span class="ieced-language-option"><span class="ieced-language-code">EN</span>@lang('main.ingles')</span>
                  @if(App::getLocale() == 'en')<span class="glyphicon glyphicon-ok ieced-language-check" aria-hidden="true"></span>@endif
                </a>
              </li>
              <li class="{{ App::getLocale() == 'pt' ? 'active' : '' }}">
                <a href="{{ route('change_lang', ['lang' => 'pt']) }}">
                  <span class="ieced-language-option"><span class="ieced-language-code">PT</span>@lang('main.portugues')</span>
                  @if(App::getLocale() == 'pt')<span class="glyphicon glyphicon-ok ieced-language-check" aria-hidden="true"></span>@endif
                </a>
              </li>
            </ul>
          </li>

          <!-- User Account Menu -->
          <li class="dropdown user user-menu ieced-user-dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle ieced-user-trigger" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{asset('/avatars').'/'.$imagen}}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span>
            </a>
            <ul class="dropdown-menu ieced-user-menu">
              <!-- The user image in the menu -->
              <li class="user-header ieced-user-card">
                <img src="{{asset('/avatars').'/'.$imagen}}" class="img-circle ieced-user-avatar" alt="User Image">

                <p class="ieced-user-name">
                  Hola {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer ieced-user-footer">
               @if (Auth::guest())
                    <a href="{{ route('login') }}" class="btn btn-default btn-flat ieced-user-action">Login</a>
               @else
                    @if (Auth::user()->id_tipo_usuario != 2)
                      <a href="{{ route('gateway_tickets.create') }}" class="btn btn-default btn-flat ieced-user-action ieced-user-ticket">
                        <span class="fa fa-ticket" aria-hidden="true"></span> Enviar ticket
                      </a>
                    @endif
                    <a href="{{ route('perfil.editar') }}" class="btn btn-default btn-flat ieced-user-action">
                      <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Perfil
                    </a>
                    <a class="btn btn-default btn-flat ieced-user-action ieced-user-signout" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                      <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Cerrar sesión
                    </a>
                @endif
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      {{ csrf_field() }}
   </form>
