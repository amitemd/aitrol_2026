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
    background: url('{{asset('/')}}hc4/img/logo.png')center / contain no-repeat;
    width: 200px;
    height: 50px;
    transform: translateX(-60%); 
    left: 44%;
    position: absolute;
  }

  @media screen and (max-width:640px) {
      /* reglas CSS */
      .example-8 .navbar-brand {
        background: none;
        width: 200px;
        height: 50px;
        transform: translateX(-60%);
        left: 50%;
        position: absolute;
      }
  }
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
      <img src="{{asset('/')}}hc4/img/siams.png">
    </a> 

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top example-8" role="navigation" >
      <!-- Sidebar toggle button-->
      <a data-toggle="offcanvas" role="button">
        
      </a>
      <a class="navbar-brand text-hide" href="#">Brand Text
        </a>
      <!-- Navbar Right Menu -->

      <div class="navbar-custom-menu" style="">
        <ul class="nav navbar-nav" style="z-index: 9999">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu" style="color:black;">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{asset('/avatars').'/'.$imagen}}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs" style="color:black;">{{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span>
            
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{asset('/avatars').'/'.$imagen}}" class="img-circle" alt="User Image">

                <p style="color:black;">
                  Hola {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
               @if (Auth::guest())
                  <div class="pull-left">
                    <a href="{{ route('login') }}" class="btn btn-default btn-flat">Login</a>
                  </div>
               @else
                <div class="pull-left">
                    <a data-toggle="modal" data-target="#perfil" data-remote="{{ route('perfil.editar_nuevo') }}"  class="btn btn-default btn-flat">Perfil</a>
                  </div>
                 <div class="pull-right">
                    <a class="btn btn-default btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Cerrar Sesion
                    </a>
                 </div>
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


     <script type="text/javascript">
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