@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Historia Clínica
      </h1>
      
      <ol class="breadcrumb">
        
        <li><a href="{{asset('/')}}" style="color: blue;"><i class="fa fa-home"></i> Inicio</a></li>
        <!--li><a href="{{route('agenda.agenda2')}}" style="color: blue;"></i> Agenda</a></li-->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection