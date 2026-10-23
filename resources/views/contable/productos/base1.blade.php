@extends('layouts.app-template')
@section('content')
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
<div class="content-wrapper cnt_wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <!--Productos y Servicios-->    
    <!--EMPRESA - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @endif-->
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection