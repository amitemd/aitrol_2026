@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Procedimientos Pentax
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i>Consultas y Procedimientos</a></li>
        <li><a href="{{ route('consultam.detalle',['id' => $historia->id_agenda]) }}">Detalle</a></li>
        <li class="active">Control Documentos</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection