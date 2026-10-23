@extends('layouts.app-template')
@section('content')
<style>
  .w3-bar.w3-blue-light {
    background-color: #E0E5E8;

  }

  .w3-bar.w3-blue-light .w3-button {
    color: #000;

  }
</style>
<div class="content-wrapper">

  <div class="w3-bar w3-blue-light">

    <a class="btn btn-success" href="{{ route('formato.rc_preanestesico_crear', ['id' => $transanestesico->id]) }}" class="w3-bar-item w3-button" onclick="openCity('London')">Preanestésico</a>

    <a class="btn btn-primary" href="" class="w3-bar-item w3-button" onclick="openCity('Paris')">Transanestésico</a>

    <a class="btn btn-success" href="{{ route('formato.rc_postanestesico_crear', ['id' => $transanestesico->id]) }}" class="w3-bar-item w3-button" onclick="openCity('Paris')">Post Anestésico</a>
    
  </div>
  <br>

  <div class="text-right" style="margin-right: 20px;">
    <a id="formulario" href="{{ route('formulario.018A', ['id' => $transanestesico->id]) }}" target="_blank" type="button" class="btn btn-danger">
      <i class="fa fa-file-pdf-o" aria-hidder="true">&nbsp;&nbsp; PDF</i>
    </a>
  </div>

  @yield('action-content')
  <!-- /.content -->
</div>
<script>
  function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    document.getElementById(cityName).style.display = "block";
  }
</script>


@endsection