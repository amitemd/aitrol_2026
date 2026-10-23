@extends('layouts.app-template')
@section('content')
<style type="text/css">
  .color_rojo{
    font-size: 15pt;
    font-weight: bold;
    color: red;

  }   
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
  
    <div >
        <span class="color_rojo"> Administración de Productos </span>
        @yield('action-content')
    </div>
    <!-- /.content -->
</div>
@endsection