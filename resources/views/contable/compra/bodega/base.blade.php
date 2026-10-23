@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div >
    @php
      $id_empresa = session('id_empresa');
      $empresa    = \Sis_medico\Empresa::where('id', $id_empresa)->first();
    @endphp
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
@endsection
