@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        PentaxTV
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('pentax') }}"><i class="fa fa-television"></i> Control Pentax</a></li>
        <li class="active">Pentax TV</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection