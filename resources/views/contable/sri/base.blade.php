@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Servicios de Rentas Internas
      </h1>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
        <!--<li class="active"><i class="fa fa-list"></i>Productos</li>-->
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection