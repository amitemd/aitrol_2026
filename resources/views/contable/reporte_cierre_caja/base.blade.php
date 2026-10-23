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
      
    </section>
    @yield('action-content')
  
  </div>
@endsection