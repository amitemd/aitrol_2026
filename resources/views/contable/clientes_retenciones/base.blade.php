@extends('layouts.app-template')
@section('content')
<style type="text/css">
  .color_rojo{
    font-size: 13pt;
    font-weight: bold;
    color: red;

  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="background-color:#555555 ">
    </section>
    <div style="border: 2px solid black;background-color:#9E9E9E; ">
    <span class="color_rojo">
        EMPRESA - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      @yield('action-content')
    </div>
    <!-- /.content -->
  </div>
@endsection