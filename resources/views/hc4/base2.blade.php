@extends('hc4.app-template')
@section('content')
  <style type="text/css">
    #navbar {
      overflow: hidden;
    }

    #navbar a {
      float: left;
      display: block;
      color: #f2f2f2;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 17px;
      z-index: 99999;
    }

    #navbar a:hover {
      background-color: #ddd;
      color: black;
    }

    #navbar a.active {
      background-color: #4CAF50;
      color: white;
    }

    .content {
      padding: 16px;
    }

    .sticky {
      position: fixed;
      top: 0;
      width: 100%;
      margin-top:4%
    }

    .sticky + .content {
      padding-top: 60px;
    }
    .boton{
        height: 93px;
    }

    .titulo_pro{
        font-size: 12px;
      }

      .endo_interno{
        padding-left: 10px; padding-right: 5px; margin-bottom: 5px
      }

      .endo_interno2{
        padding-left: 5px;
        padding-right: 10px;
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

      .cambiar{
        display: none !important;
      }
      .boton{
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .cabecera_responsive{
        padding: 0 20px;
        z-index: 999 !important;
      }

      .responsive_filiacion{
        font-size: 10px;
      }
      .responsive_filiacion td{
        font-size: 10px;
      }
      .antecedentes_responsive .col-lg-4{
        text-align: left;
      }
      .antecedentes_responsive .col-lg-4 textarea{
        width: 100%;
      }

      .detalle_tabla td{
        font-size: 12px !important;
      }
      .col-12 .box .box-header .row{
        font-size: 10px !important;
      }

      .cuerpo{
        font-size: 9px !important;
      }
      .titulo_pro{
        font-size: 9px;
      }

      .endo_interno{
        padding-left: 15px;
        padding-right: 15px;
      }

      .endo_interno2{
        padding-left: 15px;
        padding-right: 15px;
      }

    }
    @media (max-width: 812px) {
      .cabecera_responsive{
        padding: 0 20px;
        z-index: 999 !important;
      }
    }  


  </style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="font-family: Helvetica;margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right,#124574,#0C8BEC,#124574);  margin-bottom: 15px;">
      <span style="font-size: 21px;"><img src="{{asset('/')}}hc4/img/art_doc.png" style="width: 36px;"> &nbsp;&nbsp;<b>@lang('hc4.areaMedica')</b></span>
    </section>
    <div class="content" style="min-height: 103px !important; padding: 0;">
      <div id="navbar" class="row cabecera_responsive" style="z-index: 998">
        <div class="col-xl-2 ">     </div>
        <div class="col-xl-8 " style="background: #124574;">
          <div class="row row_datos">
            <div class="col-md-12">
              <div class="row">
                <div class="col-3" style="padding: 5px 2px; padding-left: 5px;  ">
                  <a id="agenda_del_dia" href="#" class="boton"  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1); border-radius: 10px; width: 100%;">
                    <div class="row" >
                      <div class="col-md-4 col-12" style="padding: 5px;">
                        <p class="align-middle" style='text-align: center;margin:0;line-height: 1;'><span style="line-height: 1; font-size: 9px; text-align: center"> @if(date('N') == 1) @lang('hc4.lunes') @elseif(date('N') == 2) @lang('hc4.martes') @elseif(date('N') == 3) @lang('hc4.miercoles') @elseif(date('N') == 4) @lang('hc4.jueves') @elseif(date('N') == 5) @lang('hc4.viernes') @elseif(date('N') == 6) @lang('hc4.sabado') @elseif(date('N') == 7) @lang('hc4.domingo') @endif</span><br><span><b>{{date('d')}}</b></span><br><span style="font-size:7px;">@if(date('m') == 1) @lang('hc4.enero') @elseif(date('m') == 2) @lang('hc4.febrero') @elseif(date('m') == 3) @lang('hc4.marzo') @elseif(date('m') == 4) @lang('hc4.abril') @elseif(date('m') == 5) @lang('hc4.mayo') @elseif(date('m') == 6) @lang('hc4.junio') @elseif(date('m') == 7) @lang('hc4.julio') @elseif(date('m') == 8) @lang('hc4.agosto') @elseif(date('m') == 9) @lang('hc4.septiembre') @elseif(date('m') == 10) @lang('hc4.octubre') @elseif(date('m') == 11) @lang('hc4.noviembre') @elseif(date('m') == 12) @lang('hc4.diciembre') @endif {{date('Y')}}</span></p>
                      </div>
                      <div class="col-8 cambiar" style="line-height: 1;" >
                        <br>
                        <div class="align-middle" style="font-size: 12px;line-height: 1; margin:0" ><b>@lang('hc4.agendaDia')</b> <br><span class="align-middle" style="font-size: 9px; color: #e9550e">@lang('hc4.dr') {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span></div>

                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                  <a id="laboratorio_boton" href="#" class="boton"  style="width: 100%;color: #124574; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px">
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-lg-3 col-12" style="padding:0;">
                        <img src="{{asset('/')}}hc4/img/lab.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-6 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 11.5px;line-height: 1; margin:0"><b>@lang('hc4.laboratorio-')</b> </div>
                      </div>
                      <div class="col-sm-1 col-md-1"></div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                  <a id="listado_paciente_boton" href="#" class="boton" style="color: #124574; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px" >
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-12 col-lg-3" style="padding:0;">
                        <img src="{{asset('/')}}hc4/img/lista.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-6 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 9.5px;line-height: 1; margin:0; font-weight: 800;"><b style="">@lang('hc4.listadoPacientesDia')</b></div>
                      </div>
                      <div class="col-md-1"></div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                    <a id="crear_editar_boton" href="#" class="boton"  style="color: #124574; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px">
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-lg-3 col-12" style="padding:0; margin-top: 10px">
                        <img src="{{asset('/')}}hc4/img/med.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-7 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 12px;line-height: 1; margin:0"><b>@lang('hc4.crear-') / @lang('hc4.editarmedicinas-')</b> </div>
                      </div>
                      <div class="col-sm-1 col-md-1"></div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
   </div>


    @yield('action-content')
    <!-- /.content -->
  </div>

<script>
  $('#agenda_del_dia').click(function(){
    $('#variable4').val('4');
    $('#formulario4').submit();
  });
  window.onscroll = function() {myFunction()};

  var navbar = document.getElementById("navbar");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }
  }
</script>


<script>
  $('#laboratorio_boton').click(function(){
    $('#variable').val('1');
    $('#formulario1').submit();
  });
  window.onscroll = function() {myFunction()};

  var navbar = document.getElementById("navbar");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }
  }
</script>


<script>
  $('#listado_paciente_boton').click(function(){
    $('#variable2').val('2');
    $('#formulario2').submit();
  });
  window.onscroll = function() {myFunction()};

  var navbar = document.getElementById("navbar");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }
  }
</script>

<script>
  $('#crear_editar_boton').click(function(){
    $('#variable3').val('3');
    $('#formulario3').submit();
  });
  window.onscroll = function() {myFunction()};

  var navbar = document.getElementById("navbar");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }
  }
</script>


@endsection
