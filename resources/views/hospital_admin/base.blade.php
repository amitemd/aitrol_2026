@extends('hospital_admin.app-template')
@section('content')

<!-- Page Heading -->
<div class="content">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">GESTI&Oacute;N HOSP&Iacute;TALARIA-ADMINISTRACI&Oacute;N</h1>
  </div>
  <div class = "container-fluid">
    @yield('action-content')
  </div>
</div>


@endsection