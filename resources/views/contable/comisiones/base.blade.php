@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Comision Contable
    </h1>
    <ol class="breadcrumb">
      <li class="active"> Comision Contable</li>
    </ol>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection