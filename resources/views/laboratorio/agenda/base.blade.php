@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Agenda LABS
      </h1>
      
      <ol class="breadcrumb">
        
        <li><a href="{{asset('/')}}" style="color: blue;"><i class="fa fa-home"></i> Inicio</a></li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
