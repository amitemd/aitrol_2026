@extends('layouts.app-template')
@section('content')
@php
$id_empresa= Session::get('id_empresa');
$empresa= Sis_medico\Empresa::find($id_empresa);
$companyLogoExists = isset($empresa)
  && $empresa->logo != null
  && file_exists(public_path('logo/'.$empresa->logo));
@endphp
<div class="content-wrapper cnt_wrapper">
    <!-- Content Header (Page header) -->
    <div class="aitrol-company-context">
      @if($companyLogoExists)
        <img class="aitrol-company-context__logo" src="{{asset('logo/'.$empresa->logo)}}" alt="Logo de {{$empresa->nombrecomercial}}">
      @else
        <span class="aitrol-company-context__fallback" aria-hidden="true">
          <i class="fa fa-building"></i>
        </span>
      @endif
      <div>
        <span class="aitrol-company-context__eyebrow">Empresa activa</span>
        <span class="aitrol-company-context__name">
          @if(isset($empresa)) {{$empresa->id}} · {{$empresa->nombrecomercial}} @else GASTROCLINICA @endif
        </span>
      </div>
    </div>
    <div>
      @yield('action-content')
    </div>
    <!-- /.content -->
  </div>
@endsection
