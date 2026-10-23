<!DOCTYPE html>
<!--
  This is a starter template page. Use this page to start your new project from
  scratch. This page gets rid of all links and provides the needed markup only.
  -->
<html>
  <head>
    @php
      $empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--  <title>SM | Sistema Medico</title> -->
    <title>
      @if($empresa->compras == 0) Aitrol @else  {{$empresa->nombrecomercial}} @endif
    </title>

    <!-- Tell the browser to be responsive to screen width -->

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->

    <link rel="stylesheet" href="{{asset('css/w3.css')}}">
    <link rel="shortcut icon" href="{{ asset("/favicon.ico")}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset("/plugins/colorpicker/bootstrap-colorpicker.css")}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap time Picker -->
    <link href="{{ asset("/plugins/timepicker/bootstrap-timepicker.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/plugins/datetimepicker/bootstrap-material-datetimepicker.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset ("/plugins/sweet_alert/sweetalert.css") }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->

    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <style type="text/css">
/*
Full screen Modal
*/
.fullscreen-modal .modal-dialog {
  margin: 0;
  margin-right: auto;
  margin-left: auto;
  width: 100%;
}
@media (min-width: 768px) {
  .fullscreen-modal .modal-dialog {
    width: 750px;
  }
}
@media (min-width: 992px) {
  .fullscreen-modal .modal-dialog {
    width: 970px;
  }
}
@media (min-width: 1200px) {
  .fullscreen-modal .modal-dialog {
     width: 1170px;
  }
}
    #mceu_30 {
  display: none;
}


.navbar-brand {
  padding: 0px;
}
.navbar-brand>img {
  height: 100%;
  padding: 15px;
  width: auto;
}

:root{
  --fuentePrincipal : 'Poppins', sans-serif;
}
</style>

    <script src="{{ asset ("/bower_components/jquery/dist/jquery.min.js")}}"></script>

    <script src="{{ asset ("/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js") }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset ("/plugins/timepicker/bootstrap-timepicker.min.js") }}"></script>



    <script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
    <script src="{{ asset ("/plugins/daterangepicker/moment.js") }}"></script>
    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/sitio.js") }}"></script>
    <script src="{{ asset ("/js/paciente.js") }}"></script>
    <script src="{{ asset ("/js/tinymce/tinymce.min.js") }}"></script>
    <script src="{{ asset ("/js/dropzone.js") }}"></script>
    <script src="{{ asset ("/js/lupa.js") }}"></script>
    <script src="{{ asset ("/plugins/sweet_alert/sweetalert.min.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <!-- Select2 -->





    <script type="text/javascript" ></script>
<!--
<script language="JavaScript">
//(c) 1999-2001 Zone Web
function click() {
if (event.button==2) {

alert ('Este boton esta deshabilitado.')
}
}
document.onmousedown=click

</script>
-->

    <style type="text/css">
    .oculto {
        display: none;
      }
      .size_text{
        font-size: 12px !important;
      }
      .content-wrapper{
	min-height: 2304px !important;	
      }
      .sidebar-mini.sidebar-collapse .sidebar-menu>li:hover>a>span:not(.pull-right),.sidebar-mini.sidebar-collapse .sidebar-menu>li:hover>.treeview-menu{
        width: 310px;
      }
      .sidebar-mini.sidebar-collapse .sidebar-menu>li:hover>a>.pull-right-container{
        left:310px !important;
      }
    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <!--
    Creado y Diseñado por:
    -Victor Touris
    -Eduardo Faustos
    -Andres Abad
    para IECED
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
  <body class="hold-transition skin-blue  sidebar-mini sidebar-collapse" id="body2" >
    <div class="wrapper">
    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')
    @yield('content')
    <!-- /.content-wrapper -->
    <!-- Footer -->
    <input type="hidden" id="asset_" value="<?=asset('/');?>"/>
	</div>
</div>
    @include('layouts.footer')

    <!-- ./wrapper -->
    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.3 -->

    <!-- Bootstrap 3.3.2 JS -->

    <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
  <?php
    if (config('data.table') == null) {
      ?>
      <script src="{{ asset('/bower_components/AdminLTE/plugins/datatables/dataTables.responsive.min.js') }}"></script>
      <?php
    }
    ?>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/fastclick/fastclick.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.date.extensions.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/input-mask/jquery.inputmask.extensions.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js") }}" type="text/javascript" ></script>

  <!--Sistema-->
  <?php
  if (config('data.controlador') != null) {
    $njs = config('data.controlador');
  ?>
    <link rel="stylesheet" type="text/css" href="{{ asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/bower_components/AdminLTE/plugins/datatables/responsive.dataTables.min.css') }}">
    <script src="{{asset('/bower_components/AdminLTE/sistema/function_'.$njs.'.js')}}"></script>
  <?php
  }
  ?>

    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/demo.js") }}" type="text/javascript"></script>
    <!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->
      <script>

      $(document).ready(function() {

        //Date picker
        $('#birthDate').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#hiredDate').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#from').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
        $('#to').datepicker({
          autoclose: true,
          format: 'yyyy/mm/dd'
        });
    });
      $(document).ready(function($){
          var ventana_ancho = $(window).width();
          if(ventana_ancho <= "1201" ){
              $("#body2").addClass('sidebar-collapse');
          }
      });

      function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
      }

      function validarDatos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }
</script>

<script type="text/javascript">

  $('.colorpicker').colorpicker();

</script>
  </body>

</html>
