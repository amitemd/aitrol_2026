@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{trans('winsumos.adm_tipos_proveedores')}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('proveedor.index') }}"><i class="fa fa-users"></i> {{trans('winsumos.proveedores')}}</a></li>
        <li class="active">{{trans('winsumos.tipos_proveedores')}}</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection