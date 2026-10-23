@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            INFORME TÉCNICO
        </h1>
        <ol class="breadcrumb">
            <li class="active">Informe Técnico</li>
        </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
</div>
@endsection