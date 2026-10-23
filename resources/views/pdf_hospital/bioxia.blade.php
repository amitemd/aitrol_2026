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