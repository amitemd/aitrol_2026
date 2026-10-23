@extends('layouts.app-template')
@section('content')
<style type="text/css">

</style>
<div class="content-wrapper cnt_wrapper">
    <!-- Content Header (Page header) -->
   
    <div >
    @if($empresa->logo!=null)
       <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
    @endif
    <span class="color_rojo">
        @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      @yield('action-content')
    </div>
    <!-- /.content -->
  </div>
    <!-- /.content -->
  </div>
@endsection