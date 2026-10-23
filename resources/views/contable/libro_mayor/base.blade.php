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
    <section class="content-header">
      <!-- <h1>
        Contabilidad - Mayor de cuenta
      </h1> -->
      <div >
    @if($empresa->logo!=null)
       <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
    @endif
    <span class="color_rojo">
        @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      @yield('action-content')
    </div>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
        <!--<li class="active"><i class="fa fa-list"></i>Productos</li>-->
      </ol>
    </section>
    <!-- /.content -->
  </div>
@endsection
