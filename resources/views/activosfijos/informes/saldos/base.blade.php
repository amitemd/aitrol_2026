@extends('layouts.app-template')
@section('content')
<style type="text/css">
  .color_rojo{
    font-size: 15pt;
    font-weight: bold;
    color: red;

  }
 
</style>
  <div class="content-wrapper cnt_wrapper">
    <!-- Content Header (Page header) -->
  
    <section class="content-header" >
     
    </section>

    <span class="color_rojo">
        EMPRESA - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      @yield('action-content')

    <!-- /.content -->
  </div>
@endsection