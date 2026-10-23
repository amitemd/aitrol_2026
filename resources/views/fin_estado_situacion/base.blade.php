
@extends('layouts.app-template')
@section('content')
<style type="text/css">
  .color_rojo{
    font-size: 15pt;
    font-weight: bold;
    color: red;
  }
</style>
<div class="content-wrapper" style="border: 2px solid black;background-color:#9E9E9E; ">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <!-- <h1>
        Contabilidad - Estado de Resultados
      </h1> -->
      <span class="color_rojo">
        EMPRESA - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
        <!--<li class="active"><i class="fa fa-list"></i>Productos</li>-->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
