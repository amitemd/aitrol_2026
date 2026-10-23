@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Administración de Marcas
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('producto.index') }}" ><i class="fa fa-list"></i>Productos</a></li>
        <li class="active">Marcas</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection