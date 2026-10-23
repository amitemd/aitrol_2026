@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper cnt_wrapper">
    <section class="content-header">
      <ol class="breadcrumb">
      </ol>
    </section>
    @yield('action-content')
  </div>
@endsection