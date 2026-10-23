@extends('layouts.app-template')
@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1 class="font-semibold text-3xl text-gray-600">
      Actividad Comercial
    </h1>
    <ol class="breadcrumb">
      <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
    </ol>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection