<?php 
  $imagen= Auth::user()->imagen_url;
  if($imagen==' '){
    $imagen='avatar.jpg';
  }
?>

<!-- POSIBLES CAMBIOS -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

  <!-- Topbar Search -->
  <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
    <div class="my-2">
      <a href="{{route('hospital_admin.index')}}" class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="{{asset('/')}}hc4/img/icono_omni.png"></span>
      </a>
    </div>
  </div>

  <!-- Topbar Navbar -->
  <ul class="navbar-nav ml-auto">

    <!-- LOGO -->
    <div class="my-2">
      <a href="{{route('hospital_admin.index')}}" class="logo">
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="{{asset('/')}}hc4/img/siams.png"></span>
      </a>
    </div>

    <div class="topbar-divider d-none d-sm-block"></div>

    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span>
        <img class="img-profile rounded-circle" src="{{asset('/avatars').'/'.$imagen}}">
      </a>
      <!-- Dropdown - User Information -->
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        <!--POSIBLES CAMBISO-->
        @if (Auth::guest())
          <div class="pull-left">
            <a href="{{ route('login') }}" class="btn btn-default btn-flat">Login</a>
          </div>
        @else
          <div class="pull-left">
            <a href="" class="btn btn-default btn-flat">Perfil</a>
          </div>
          <div class="pull-right">
            <a class="btn btn-default btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            Cerrar Sesion
            </a>
          </div>
        @endif
      </div>
    </li>

  </ul>

</nav>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
  {{ csrf_field() }}
</form>