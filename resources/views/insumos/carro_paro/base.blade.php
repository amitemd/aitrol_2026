@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Carro paro
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('producto.index') }}" ><i class="fa fa-list"></i>Carro paro</a></li>
        <li class="active">Insumos</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection