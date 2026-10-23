@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper cnt_wrapper">
    <style type="text/css">
      .color_rojo{
        font-size: 15pt;
        font-weight: bold;
        color: #FE2E2E;
        background: #FFF;
        padding: 5px;
        border-radius: 5px;
      }
    </style>
    <section class="content-header">
      <span class="color_rojo">
        MANTENIMIENTO TIPO EMISIÓN
      </span>
    </section>
    @yield('action-content')
  
  </div>
@endsection