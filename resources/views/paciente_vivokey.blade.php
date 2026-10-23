<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema | Panel Control</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../css/pe-icon-7-stroke.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="../plugins/morris/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="../plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

     <link rel="stylesheet" href="../css/sistemalaravel.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
      .bold_ne{
        font-weight: 800 !important;
      }
    </style>


  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">



      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper" style=" text-align: center; margin-left: 0;">
        <!-- Content Header (Page header) -->
        <br>
        <br>
        <img style="width: 25%;" src="../integra.png">
        <br>
        <br>
        <table style="margin: auto;font-size: 20px; font-family:  sans-serif;">
          <thead>
            <tr>
              <td class="bold_ne" colspan="2" style="text-align: center;">Datos Principales</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
          </thead>
          <tbody style="text-align: left; ">
            <tr>
              <td class="bold_ne">Nombres:</td>
              <td>{{$paciente->nombre1}} {{$paciente->nombre2}}</td>
            </tr>
            <tr>
              <td class="bold_ne">Apellidos:</td>
              <td>{{$paciente->apellido1}} {{$paciente->apellido2}}</td>
            </tr>
            <tr>
              <td class="bold_ne">Seguro:</td>
              <td>{{$paciente->seguro->nombre}}</td>
            </tr>
            <tr>
              <td class="bold_ne">Fecha de Nacimiento:</td>
              <td>{{$paciente->fecha_nacimiento}}</td>
            </tr>
            <tr>
              <td class="bold_ne">Direccion:</td>
              <td>{{$paciente->direccion}}</td>
            </tr>
            <tr>
              <td class="bold_ne">Telefono:</td>
              <td>{{$paciente->telefono1}}</td>
            </tr>
          </tbody>
        </table>
        <br>
        <br>
        <br>
        <div>
          <span style="font-weight: 800 !important; font-size: 20px;">Powered by </span><img style="width: 8%;" src="../vivokey.webp" >
        </div>
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->


    <!-- jQuery 2.1.4 -->
    <script src="../plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    <!-- Bootstrap 3.3.5 -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="../plugins/morris/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="../plugins/knob/jquery.knob.js"></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="../dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>

 <!-- javascript del sistema laravel -->
   <script src="js/sistemalaravel.js"></script>
    <script src="js/highcharts.js"></script>
  <script src="js/graficas.js"></script>





  </body>
</html>
