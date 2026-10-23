@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <style type="text/css">
      .color_rojo{
        font-size: 15pt;
        font-weight: bold;
        color: red;
      }
    </style>

    <section class="content-header">
      <h1>
       CONTABLE
      </h1>
      <span class="color_rojo">
         EMPRESA - @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
      </span>
      <ol class="breadcrumb">
      </ol>
    </section>
    @yield('action-content')

  </div>
@endsection