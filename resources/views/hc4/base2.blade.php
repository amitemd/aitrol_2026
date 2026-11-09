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

    /* Conserva el comportamiento original de la cabecera al desplazarse. */
    .sticky {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000 !important;
    }

    .sticky + .content {
      padding-top: 60px;
    }
    .boton{
        height: 93px;
    }

    .hc4-patient-summary-actions .boton {
        display: block;
        width: 100% !important;
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

    .hc4-patient-shell {
      padding-top: 1px;
      background:
        radial-gradient(circle at 8% 3%, rgba(12,139,236,.09), transparent 25rem),
        linear-gradient(180deg,#f5faff 0%,#edf5f9 100%) !important;
    }

    .main-header { display:none !important; }

    .hc4-patient-hero {
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      min-height: 112px;
      margin: 8px 18px 12px !important;
      padding: 20px 28px !important;
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 18px !important;
      background: linear-gradient(118deg,#053d68 0%,#006fb9 55%,#12a4b4 100%) !important;
      box-shadow: 0 14px 34px rgba(0,74,130,.2);
      text-align: left !important;
    }

    .hc4-patient-hero > .hc4-navbar-actions.hc4-hero-actions {
      position:absolute;
      z-index:10020;
      top:12px;
      right:18px;
      left:auto;
      height:42px;
    }
    .hc4-patient-hero .hc4-hero-actions > .hc4-header-list {
      display:flex !important;
      height:42px;
      gap:10px;
      padding:3px 0;
      border:0;
      background:transparent;
    }
    .hc4-patient-hero .hc4-hero-actions > .hc4-header-list > li { height:36px; }
    .hc4-patient-hero .hc4-hero-actions .hc4-header-action {
      min-height:34px;
      color:#fff !important;
      border:1px solid rgba(255,255,255,.25);
      background:rgba(0,55,104,.22) !important;
      box-shadow:0 4px 10px rgba(0,38,75,.14);
      backdrop-filter:blur(8px);
    }
    .hc4-patient-hero .hc4-hero-actions .hc4-header-action:hover,
    .hc4-patient-hero .hc4-hero-actions .open > .hc4-header-action {
      color:#fff !important;
      background:rgba(255,255,255,.14) !important;
    }
    body > .hc4-floating-menu { position:fixed !important; z-index:2147483000 !important; right:auto !important; display:block !important; }

    .hc4-patient-aitrol {
      position:absolute;
      z-index:2;
      top:50%;
      left:50%;
      width:190px;
      height:58px;
      transform:translate(-50%,-50%);
    }
    .hc4-patient-aitrol img {
      width:186px !important;
      height:57px !important;
      object-fit:contain;
      filter:brightness(0) invert(1) drop-shadow(0 2px 4px rgba(0,45,78,.24));
    }
    .hc4-patient-welcome {
      position:absolute;
      z-index:2;
      top:65px;
      right:24px;
      color:#fff;
      font-size:12px;
      font-weight:700;
    }
    .hc4-patient-welcome i { margin-right:6px; color:#9ce8ef; }

    .hc4-patient-hero::before {
      content: 'GESTIÓN CLÍNICA DEL PACIENTE';
      position: absolute;
      top: 18px;
      left: 28px;
      color: #d8f3ff;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .hc4-patient-hero::after {
      content: '';
      position: absolute;
      top: -86px;
      right: 12%;
      width: 180px;
      height: 180px;
      border: 30px solid rgba(255,255,255,.07);
      border-radius: 50%;
    }

    .hc4-patient-area-title {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      margin-top: 22px;
      font-size: 24px !important;
      text-align:left;
      color:#fff;
      text-decoration:none;
      cursor:pointer;
    }

    .hc4-patient-area-title:hover,
    .hc4-patient-area-title:focus {
      color:#fff;
      text-decoration:none;
      opacity:.9;
    }

    .hc4-patient-nav {
      min-height: 118px !important;
      margin-right: 0 !important;
      margin-left: 0 !important;
      padding: 0 18px !important;
    }

    .hc4-patient-nav .row_datos,
    .hc4-patient-nav .row_datos > .col-md-12 {
      margin-right: 0 !important;
      margin-left: 0 !important;
      padding-right: 0 !important;
      padding-left: 0 !important;
    }

    .hc4-patient-nav > .hc4-patient-nav-inner {
      width: 100%;
      max-width: none;
      flex: 0 0 100%;
      padding: 0 !important;
      background: transparent !important;
    }

    .hc4-patient-nav > .col-xl-2 { display: none !important; }

    .hc4-patient-summary-actions {
      display: grid;
      grid-template-columns: repeat(4,minmax(0,1fr));
      gap: 10px;
      margin: 0 !important;
      padding: 0 !important;
    }

    .hc4-patient-summary-actions > [class*="col-"] {
      width: auto;
      max-width: none;
      padding: 0 !important;
    }

    .hc4-patient-summary-actions .boton {
      display: flex;
      align-items: center;
      min-height: 104px;
      padding: 14px 16px !important;
      border: 1px solid var(--patient-action-border,#c6ddeb);
      border-top: 3px solid var(--patient-action-color,#087eae);
      border-radius: 14px !important;
      color: var(--patient-action-color,#087eae) !important;
      background: linear-gradient(135deg,#fff 15%,var(--patient-action-soft,#e9f7fb) 100%) !important;
      box-shadow: 0 6px 17px rgba(0,74,117,.09);
      transition: transform .18s ease, box-shadow .18s ease;
    }

    .hc4-patient-summary-actions > div .boton { --patient-action-color:#3f63c9; --patient-action-soft:#eaf0ff; --patient-action-border:#cbd8f2; }

    .hc4-patient-summary-actions .boton:hover {
      box-shadow: 0 11px 24px rgba(0,74,117,.16);
      transform: translateY(-3px);
      text-decoration: none;
    }

    .hc4-patient-summary-actions .boton img {
      max-width: 54px !important;
      max-height: 54px !important;
      filter: drop-shadow(0 4px 5px rgba(50,34,110,.14));
    }

    .hc4-patient-summary-actions .boton .row {
      width: 100%;
      margin: 0;
      align-items: center;
    }

    .hc4-patient-summary-actions .boton .row > [class*="col-"] {
      display: flex;
      align-items: center;
      justify-content: center;
      float: none;
      min-width: 0;
      padding: 4px 8px !important;
    }

    .hc4-patient-summary-actions .boton .row > .cambiar {
      flex: 1 1 auto;
      justify-content: flex-start;
      text-align: left;
      line-height: 1.25 !important;
    }

    .hc4-patient-summary-actions .boton .row > .cambiar > div {
      width: 100%;
      color: var(--patient-action-color,#6953bd);
      font-size: 12px !important;
      line-height: 1.25 !important;
    }

    .hc4-patient-summary-actions .boton .row > div:empty { display: none; }

    #agenda_del_dia .row > div:first-child { flex: 0 0 34%; }
    #agenda_del_dia .row > .cambiar { flex: 1 1 66%; }
    #agenda_del_dia p { width: 100%; color: var(--patient-action-color); }
    #agenda_del_dia p span { display: inline-block; margin: 1px 2px; }
    #agenda_del_dia p span b { font-size: 20px; }

    .hc4-patient-summary-actions .boton br { display: none; }

    html.dark-layout .hc4-patient-shell { background: linear-gradient(180deg,#101e2a,#0f1b26) !important; }
    html.dark-layout .hc4-patient-summary-actions .boton {
      border-right-color:#394f60;
      border-bottom-color:#394f60;
      border-left-color:#394f60;
      color:#dce8f1 !important;
      background:linear-gradient(135deg,#1b2f3c,#203541) !important;
    }

    html.dark-layout .hc4-patient-summary-actions .boton .row > .cambiar > div,
    html.dark-layout #agenda_del_dia p { color:#dce8f1 !important; }

    @media (max-width: 767px) {
      .hc4-patient-hero { margin-right:10px !important; margin-left:10px !important; }
      .hc4-patient-nav { padding:0 10px !important; }
      .hc4-patient-summary-actions { grid-template-columns:repeat(2,minmax(0,1fr)); }
      .hc4-patient-aitrol { display:none; }
      .hc4-patient-welcome { display:none; }
      .hc4-patient-hero > .hc4-navbar-actions.hc4-hero-actions { position:relative; top:auto; right:auto; width:100%; margin-bottom:8px; }
    }


  </style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <div class="content-wrapper hc4-patient-shell">
    <!-- Content Header (Page header) -->
    <section class="content-header hc4-patient-hero" style="font-family: Helvetica;margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
      <span class="hc4-patient-aitrol"><img src="{{ asset('assets/images/aitrol-logo.png') }}" alt="Aitrol"></span>
      <span class="hc4-patient-welcome"><i class="fa fa-user-md" aria-hidden="true"></i>Bienvenido, {{ Auth::user()->nombre1 }} {{ Auth::user()->apellido1 }}</span>
      <a class="hc4-patient-area-title" href="{{ route('nuevo.diseño') }}" title="Volver al inicio">
        <img src="{{asset('/')}}hc4/img/art_doc.png" style="width: 36px;" alt="">
        <b>@lang('hc4.medical_area')</b>
      </a>
    </section>
    <script type="text/javascript">
      $(function () {
        var $accionesCabecera = $('.main-header .hc4-navbar-actions').first();
        var $cabeceraPaciente = $('.hc4-patient-hero').first();
        if (!$accionesCabecera.length || !$cabeceraPaciente.length) return;

        $accionesCabecera.addClass('hc4-hero-actions').prependTo($cabeceraPaciente);
        $accionesCabecera.find('.dropdown').each(function () {
          var $contenedor = $(this);
          var $menu = $contenedor.children('.dropdown-menu');
          $contenedor.on('shown.bs.dropdown', function () {
            var posicion = $contenedor.children('.dropdown-toggle').get(0).getBoundingClientRect();
            $menu.data('hc4-dropdown-owner',$contenedor).addClass('hc4-floating-menu').appendTo(document.body);
            var ancho = $menu.outerWidth() || 260;
            var izquierda = Math.max(10,Math.min(posicion.right-ancho,$(window).width()-ancho-10));
            $menu.get(0).style.setProperty('top',(posicion.bottom+5)+'px','important');
            $menu.get(0).style.setProperty('left',izquierda+'px','important');
          });
          $contenedor.on('hide.bs.dropdown', function () {
            if ($menu.hasClass('hc4-floating-menu')) $menu.removeClass('hc4-floating-menu').removeAttr('style').appendTo($contenedor);
          });
        });
      });
    </script>
    <div class="content" style="min-height: 103px !important; padding: 0;">
      <div id="navbar" class="row cabecera_responsive hc4-patient-nav" style="z-index: 998">
        <div class="col-xl-2 ">     </div>
        <div class="col-xl-8 hc4-patient-nav-inner" style="background: #004AC1;">
          <div class="row row_datos">
            <div class="col-md-12">
              <div class="row hc4-patient-summary-actions">
                <div class="col-3" style="padding: 5px 2px; padding-left: 5px;  ">
                  <a id="agenda_del_dia" href="#" class="boton"  style="color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1); border-radius: 10px; width: 100%;">
                    <div class="row" >
                      <div class="col-md-4 col-12" style="padding: 5px;">
                        <p class="align-middle" style='text-align: center;margin:0;line-height: 1;'><span style="line-height: 1; font-size: 9px; text-align: center"> @if(date('N') == 1) @lang('hc4.monday') @elseif(date('N') == 2) @lang('hc4.tuesday') @elseif(date('N') == 3) @lang('hc4.wednesday') @elseif(date('N') == 4) @lang('hc4.thursday') @elseif(date('N') == 5) @lang('hc4.friday') @elseif(date('N') == 6) @lang('hc4.saturday') @elseif(date('N') == 7) @lang('hc4.sunday') @endif</span><br><span><b>{{date('d')}}</b></span><br><span style="font-size:7px;">@if(date('m') == 1) @lang('hc4.january') @elseif(date('m') == 2) @lang('hc4.february') @elseif(date('m') == 3) @lang('hc4.march') @elseif(date('m') == 4) @lang('hc4.april') @elseif(date('m') == 5) @lang('hc4.may') @elseif(date('m') == 6) @lang('hc4.june') @elseif(date('m') == 7) @lang('hc4.july') @elseif(date('m') == 8) @lang('hc4.august') @elseif(date('m') == 9) @lang('hc4.september') @elseif(date('m') == 10) @lang('hc4.october') @elseif(date('m') == 11) @lang('hc4.november') @elseif(date('m') == 12) @lang('hc4.december') @endif {{date('Y')}}</span></p>
                      </div>
                      <div class="col-8 cambiar" style="line-height: 1;" >
                        <br>
                        <div class="align-middle" style="font-size: 12px;line-height: 1; margin:0" ><b>@lang('hc4.todays_schedule')</b> <br><span class="align-middle" style="font-size: 9px; color: #e9550e">Dr. {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span></div>

                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                  <a id="laboratorio_boton" href="#" class="boton"  style="width: 100%;color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px">
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-lg-3 col-12" style="padding:0;">
                        <img src="{{asset('/')}}hc4/img/lab.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-6 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 11.5px;line-height: 1; margin:0"><b>@lang('hc4.laboratory')</b> </div>
                      </div>
                      <div class="col-sm-1 col-md-1"></div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                  <a id="listado_paciente_boton" href="#" class="boton" style="color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px" >
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-12 col-lg-3" style="padding:0;">
                        <img src="{{asset('/')}}hc4/img/lista.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-6 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 9.5px;line-height: 1; margin:0; font-weight: 800;"><b style="">@lang('hc4.todays_patient_list')</b></div>
                      </div>
                      <div class="col-md-1"></div>
                    </div>
                  </a>
                </div>
                <div class="col-3" style="padding: 5px 2px; ">
                  <a id="crear_editar_boton" href="#" class="boton"  style="color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px; padding-top: 2px">
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col-lg-3 col-12" style="padding:0; margin-top: 10px">
                        <img src="{{asset('/')}}hc4/img/med.png" style="width: 100%; max-height: 61px; max-width: 46px;">
                      </div>
                      <div class="col-lg-7 col-12 cambiar" style="line-height: 0.2;">
                        <br style="line-height: 0.7;"><br>
                        <div class="align-middle" style="font-size: 12px;line-height: 1; margin:0"><b>@lang('hc4.create_edit_medicines')</b> </div>
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
