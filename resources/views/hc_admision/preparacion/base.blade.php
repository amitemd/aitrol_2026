@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Signos Vitales
      </h1>
      
      <ol class="breadcrumb">
        
        <!--li><a href="{{asset('/')}}"><i class="fa fa-home"></i> Inicio</a></li-->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
