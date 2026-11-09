@extends('hc4.app-template')
@section('content')

<style type="text/css">
    #navbar {
      overflow: hidden;
      z-index: auto !important;
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
    }

    .sticky + .content {
      padding-top: 60px;
    }

    hr{
	    height: 3px;
      width: 100%;
	    border: 0;
	    background-color: #6B6B6F;
      margin-top:2px;
    }

    .calendario{
      background-image: url("{{asset('/')}}hc4/img/btn-ca.png"),linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);
      border-radius: 10px;
      width: 100%;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }

    .hc4-medical-header-row {
      display: flex;
      align-items: center;
    }

    .content-wrapper {
      background:
        radial-gradient(circle at 8% 5%, rgba(12, 139, 236, .09), transparent 25rem),
        linear-gradient(180deg, #f5faff 0%, #edf5f9 100%);
    }

    .hc4-medical-hero {
      position: relative;
      overflow: visible;
      z-index: 20;
      margin: 8px 18px 16px !important;
      min-height: 124px;
      padding: 14px 24px !important;
      border: 1px solid rgba(255, 255, 255, .2);
      border-radius: 18px !important;
      background: linear-gradient(118deg, #053d68 0%, #006fb9 55%, #12a4b4 100%) !important;
      box-shadow: 0 14px 34px rgba(0, 74, 130, .2);
    }

    /* En la pantalla ambulatoria la navegación se concentra en el panel principal. */
    .main-header {
      display: none !important;
    }

    .hc4-medical-hero > .hc4-navbar-actions.hc4-hero-actions {
      position: absolute;
      z-index: 10020;
      top: 12px;
      right: 18px;
      left: auto;
      float: none;
      height: 42px;
      transform: none;
    }

    .hc4-medical-hero .hc4-hero-actions > .hc4-header-list {
      height: 42px;
      gap: 12px;
      padding: 3px 0;
      border: 0;
      background: transparent;
    }

    .hc4-medical-hero .hc4-hero-actions > .hc4-header-list > li {
      height: 36px;
    }

    .hc4-medical-hero .hc4-hero-actions > .hc4-header-list > li > .hc4-header-action {
      border: 1px solid rgba(255,255,255,.25);
      border-radius: 10px !important;
      background: rgba(0,55,104,.22) !important;
      box-shadow: 0 4px 10px rgba(0,38,75,.14);
      backdrop-filter: blur(8px);
    }

    .hc4-medical-hero .hc4-hero-actions > .hc4-header-list > li + li {
      position: relative;
    }

    .hc4-medical-hero .hc4-hero-actions > .hc4-header-list > li + li::before { display: none; }

    .hc4-medical-hero .hc4-hero-actions .hc4-header-action {
      min-height: 34px;
      color: #fff !important;
    }

    .hc4-medical-hero .hc4-hero-actions .hc4-header-action:hover,
    .hc4-medical-hero .hc4-hero-actions .hc4-header-action:focus,
    .hc4-medical-hero .hc4-hero-actions .open > .hc4-header-action {
      color: #fff !important;
      background: rgba(255,255,255,.14) !important;
    }

    .hc4-medical-hero .hc4-workspace-shortcut-link {
      min-height: 34px;
      border-color: rgba(255,255,255,.34);
      color: #fff !important;
      background: rgba(0,55,104,.22);
      box-shadow: 0 4px 10px rgba(0,38,75,.14);
      backdrop-filter: blur(8px);
    }

    .hc4-medical-hero .hc4-workspace-shortcut-link .glyphicon { color: #bff8f3; }
    .hc4-medical-hero .hc4-workspace-shortcut-link--admin .glyphicon { color: #dbe4ff; }
    .hc4-medical-hero .hc4-workspace-shortcut-link:hover,
    .hc4-medical-hero .hc4-workspace-shortcut-link:focus { color: #fff !important; background: rgba(255,255,255,.17); }

    .hc4-medical-hero .hc4-hero-actions .hidden-xs,
    .hc4-medical-hero .hc4-hero-actions .caret {
      color: #fff !important;
    }

    .hc4-medical-hero .hc4-theme-toggle {
      border-color: rgba(255,255,255,.28) !important;
      color: #fff !important;
      background: rgba(255,255,255,.12) !important;
      box-shadow: 0 4px 10px rgba(0,38,75,.16);
    }

    html.dark-layout .hc4-medical-hero .hc4-theme-toggle {
      border-color: rgba(255,211,107,.42) !important;
      color: #ffd36b !important;
      background: rgba(255,211,107,.12) !important;
    }

    .hc4-medical-hero .hc4-hero-actions .dropdown-menu {
      z-index: 10030 !important;
    }

    body > .hc4-floating-menu {
      position: fixed !important;
      z-index: 2147483000 !important;
      right: auto !important;
      display: block !important;
    }

    .hc4-medical-hero::after {
      content: '';
      position: absolute;
      width: 190px;
      height: 190px;
      top: -105px;
      right: 19%;
      border: 32px solid rgba(255, 255, 255, .08);
      border-radius: 50%;
      pointer-events: none;
    }

    .hc4-aitrol-mark {
      position: absolute;
      z-index: 2;
      top: 25px;
      left: 50%;
      width: 190px;
      height: 59px;
      overflow: hidden;
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
      transform: translateX(-50%);
      backdrop-filter: none;
    }

    .hc4-aitrol-mark img {
      width: 186px !important;
      max-width: 186px !important;
      height: 57px !important;
      margin: 1px auto;
      object-fit: contain;
      filter: brightness(0) invert(1) drop-shadow(0 2px 4px rgba(0,45,78,.24));
      opacity: .96;
    }

    .hc4-medical-title h1 {
      display: inline-flex;
      align-items: center;
      gap: 13px;
      margin: 0;
      font-size: 27px;
      letter-spacing: -.4px;
    }

    .hc4-hero-eyebrow {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 5px;
      color: #d8f3ff;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .13em;
      text-transform: uppercase;
    }

    .hc4-hero-eyebrow::before {
      content: '';
      width: 27px;
      height: 2px;
      border-radius: 9px;
      background: #72d8e8;
    }

    .hc4-hero-copy {
      margin: 5px 0 0;
      color: rgba(255,255,255,.82);
      font-size: 14px;
      line-height: 1.5;
    }

    .hc4-hero-welcome {
      position: absolute;
      top: 58px;
      right: 24px;
      margin-top: 0;
      color: #fff;
      font-size: 13px;
      font-weight: 600;
      text-align: right;
    }

    .hc4-hero-welcome i {
      margin-right: 6px;
      color: #9ce8ef;
    }

    .hc4-hero-date {
      padding: 0;
      color: rgba(255,255,255,.9);
      font-size: 12px;
      text-align: left;
    }

    .hc4-hero-summary {
      width: 100%;
      max-width: 430px;
      padding: 14px;
      border: 1px solid rgba(255,255,255,.24);
      border-radius: 14px;
      background: rgba(255,255,255,.11);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.12);
      backdrop-filter: blur(8px);
      text-align: left;
    }

    .hc4-hero-summary-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(255,255,255,.2);
    }

    .hc4-hero-summary-title strong {
      color: #fff;
      font-size: 12px;
      letter-spacing: .07em;
      text-transform: uppercase;
    }

    .hc4-hero-metrics {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin: 10px 0;
    }

    .hc4-hero-metric {
      padding: 9px 10px;
      border-radius: 10px;
      background: rgba(0,47,91,.24);
    }

    .hc4-hero-metric strong,
    .hc4-hero-metric span {
      display: block;
    }

    .hc4-hero-metric strong {
      color: #fff;
      font-size: 18px;
      line-height: 1;
    }

    .hc4-hero-metric span {
      margin-top: 4px;
      color: rgba(255,255,255,.76);
      font-size: 10px;
      text-transform: uppercase;
    }

    .hc4-hero-summary .hc4-hospital-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      box-shadow: none;
    }

    .hc4-medical-title h1 img {
      width: 40px !important;
      filter: drop-shadow(0 4px 6px rgba(0, 0, 0, .12));
    }

    .hc4-hospital-action .btn {
      padding: 9px 18px;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: .2px;
      box-shadow: 0 5px 12px rgba(0, 31, 92, .18);
      transition: transform .18s ease, background-color .18s ease, box-shadow .18s ease;
      background: rgba(0, 56, 103, .38) !important;
      border-color: rgba(255, 255, 255, .72) !important;
    }

    .hc4-hospital-action .btn:hover,
    .hc4-hospital-action .btn:focus {
      transform: translateY(-2px);
      background: #004a82 !important;
      box-shadow: 0 8px 16px rgba(0, 48, 87, .3);
    }

    .hc4-hospital-action {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media screen and (max-width:640px) {
      /* reglas CSS */
      .example-8 .navbar-brand {
        background: none;
        width: 200px;
        height: 50px;
        transform: translateX(-60%);
        left: 43%;
        color: ;
        position: absolute;
      }

      .cambiar{
        display: none !important;
      }
      .row_datos{
        padding-left: 5%;
        padding-right: 5%;
      }
      .boton{
        width: 100%;
      }

      .hc4-medical-header-row {
        flex-direction: column;
      }

      .hc4-medical-title,
      .hc4-hospital-action {
        width: 100%;
      }

      .hc4-hospital-action {
        padding-bottom: 8px;
      }

      .hc4-medical-hero {
        margin: 6px 10px 12px !important;
        padding: 15px 12px !important;
      }

      .hc4-hero-welcome {
        position: static;
        margin-top: 9px;
        text-align: left;
      }

      .hc4-aitrol-mark { display: none; }

      .hc4-medical-hero > .hc4-navbar-actions.hc4-hero-actions {
        position: relative;
        top: auto;
        right: auto;
        left: auto;
        width: 100%;
        margin-bottom: 12px;
        transform: none;
      }

      .hc4-medical-hero .hc4-hero-actions > .hc4-header-list {
        justify-content: flex-end;
      }

      .hc4-medical-title h1 {
        margin-bottom: 12px;
        font-size: 23px;
      }

      .hc4-hero-copy { display: none; }

      .hc4-hero-summary {
        max-width: none;
      }
    }

    /* Responsive safety net for the ambulatory workspace and AJAX-loaded views. */
    .content-wrapper,
    .content-wrapper .container-fluid,
    .content-wrapper .box,
    .content-wrapper .box-body,
    .content-wrapper .row > [class*="col-"] { min-width: 0; }

    .content-wrapper img,
    .content-wrapper svg,
    .content-wrapper canvas,
    .content-wrapper video { max-width: 100%; }

    .content-wrapper .table-responsive,
    .content-wrapper .dataTables_wrapper { width: 100%; max-width: 100%; }

    .content-wrapper .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .content-wrapper .select2-container { max-width: 100% !important; }

    @media screen and (max-width: 991px) {
      .hc4-medical-hero { margin-right: 12px !important; margin-left: 12px !important; }
      .hc4-medical-header-row { display: flex; flex-wrap: wrap; }
      .hc4-medical-title,
      .hc4-hospital-action { width: 100%; }
      .hc4-hospital-action { margin-top: 16px; }
      .hc4-hero-summary { max-width: none; }
    }

    @media screen and (max-width: 767px) {
      html, body { max-width: 100%; overflow-x: hidden; }
      .content-wrapper > .content { padding: 8px 10px 16px; }
      .content-wrapper .box-header { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
      .content-wrapper .box-header > .box-tools,
      .content-wrapper .box-header > .pull-right { position: static; width: 100%; margin: 0 !important; }
      .content-wrapper .dataTables_wrapper .row { margin-right: 0; margin-left: 0; }
      .content-wrapper .dataTables_wrapper [class*="col-"] { width: 100%; padding-right: 0; padding-left: 0; }
      .content-wrapper .dataTables_filter,
      .content-wrapper .dataTables_length,
      .content-wrapper .dataTables_info,
      .content-wrapper .dataTables_paginate { float: none !important; width: 100%; margin: 6px 0; text-align: left !important; }
      .content-wrapper .dataTables_filter label { display: flex; width: 100%; align-items: center; gap: 6px; }
      .content-wrapper .dataTables_filter input { min-width: 0; flex: 1; margin-left: 0; }
      .content-wrapper table.table:not(.dataTable) {
        display: block; width: 100%; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;
      }
      .content-wrapper .pagination { display: flex; flex-wrap: wrap; }
      .modal-dialog { width: auto !important; max-width: calc(100vw - 20px); margin: 10px auto; }
      .modal-body { max-height: calc(100vh - 130px); overflow-y: auto; }
      .modal-footer { display: flex; flex-wrap: wrap; gap: 6px; }
      .modal-footer::before,
      .modal-footer::after { display: none; }
      .modal-footer .btn { flex: 1 1 auto; margin: 0 !important; white-space: normal; }
    }

    @media screen and (max-width: 479px) {
      .hc4-medical-hero { margin-right: 6px !important; margin-left: 6px !important; padding: 13px 10px !important; }
      .hc4-hero-metrics { grid-template-columns: 1fr; }
      .hc4-medical-title h1 { gap: 8px; font-size: 20px; line-height: 1.2; }
      .hc4-medical-title h1 img { width: 32px !important; }
      .hc4-hero-summary .hc4-hospital-link { gap: 8px; white-space: normal; }
      .content-wrapper > .content { padding-right: 6px; padding-left: 6px; }
      .content-wrapper .box-body { padding: 8px; }
      .content-wrapper .btn-group { display: inline-flex; max-width: 100%; flex-wrap: wrap; }
    }

  </style>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header hc4-medical-hero" style="font-family: Helvetica;margin: 2px;color: white; text-align: center; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
      <span class="hc4-aitrol-mark" title="Aitrol" aria-label="Aitrol"><img src="{{ asset('assets/images/aitrol-logo.png') }}" alt="Aitrol"></span>
      <div class="row hc4-medical-header-row">
        <div class="col-md-12 hc4-medical-title" style="text-align:left;">
          <span class="hc4-hero-eyebrow">Gestión clínica ambulatoria</span>
          <h1><img src="{{asset('/')}}hc4/img/art_doc.png" style="width: 36px;"><b>@lang('hc4.medical_area')</b></h1>
          <p class="hc4-hero-copy">Agenda, pacientes y herramientas clínicas para la atención médica diaria.</p>
          <div class="hc4-hero-welcome"><i class="fa fa-user-md" aria-hidden="true"></i>Bienvenido, {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</div>
        </div>
      </div>
      

    </section>

    <script type="text/javascript">
      $(function () {
        var $accionesCabecera = $('.main-header .hc4-navbar-actions').first();
        var $cabeceraAmbulatoria = $('.hc4-medical-hero').first();

        if ($accionesCabecera.length && $cabeceraAmbulatoria.length) {
          $accionesCabecera.addClass('hc4-hero-actions').prependTo($cabeceraAmbulatoria);

          $accionesCabecera.find('.dropdown').each(function () {
            var $contenedor = $(this);
            var $menu = $contenedor.children('.dropdown-menu');

            $contenedor.on('shown.bs.dropdown', function () {
              var $boton = $contenedor.children('.dropdown-toggle');
              var boton = $boton.get(0);
              var posicion = boton.getBoundingClientRect();

              $menu
                .data('hc4-dropdown-owner', $contenedor)
                .addClass('hc4-floating-menu')
                .appendTo(document.body);

              var anchoMenu = $menu.outerWidth() || 300;
              var izquierda = posicion.right - anchoMenu;
              izquierda = Math.max(10, Math.min(izquierda, $(window).width() - anchoMenu - 10));

              $menu.get(0).style.setProperty('top', (posicion.bottom + 5) + 'px', 'important');
              $menu.get(0).style.setProperty('right', 'auto', 'important');
              $menu.get(0).style.setProperty('left', izquierda + 'px', 'important');
            });

            $contenedor.on('hide.bs.dropdown', function () {
              if ($menu.hasClass('hc4-floating-menu')) {
                $menu.removeClass('hc4-floating-menu').removeAttr('style').appendTo($contenedor);
              }
            });
          });
        }
      });
    </script>

    @yield('action-content')
    <!-- /.content -->
  </div>

 

@endsection
