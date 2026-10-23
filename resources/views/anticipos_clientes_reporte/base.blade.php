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
    <section class="content-header">
      <h1>
        Anticipos Clientes
      </h1>
      <span class="color_rojo">
        Empresa - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      <ol class="breadcrumb">
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection