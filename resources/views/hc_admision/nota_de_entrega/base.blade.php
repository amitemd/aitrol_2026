@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      {{ trans('nota_entrega_ui.title') }}
      </h1>

    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
