@extends('layouts.app-template')
@section('content')
  <style>
    /* La plantilla general fuerza 2304px; soporte debe crecer según su contenido. */
    .content-wrapper.ticket-support-wrapper {
      min-height: 0 !important;
      height: auto !important;
      padding-bottom: 16px;
    }
  </style>
  <div class="content-wrapper ticket-support-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Ticket de Soporte Tecnico
      </h1>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
        <!--li class="active">Archivo Plano</li-->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
