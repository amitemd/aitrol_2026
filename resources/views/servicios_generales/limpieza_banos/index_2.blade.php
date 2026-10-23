@extends('servicios_generales.limpieza_banos.base')
@section('action-content')

@php
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
@endphp
<style type="text/css">
  th,
  td {
    text-align: center;
  }
</style>



<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="col-md-12">
        <div class="col-md-10">
          <h4 style="text-align: left;">Seleccione el Edificio</h4>
        </div>
      </div>
    </div>
    <div class="box-body">
      <div class="row">
        @foreach($nombre_piso as $value)
        <div class="col-md-10 col-md-offset-1" align="center">
          <a style="width:70%; height: 60px;  font-size: 16px; text-align: center;font-weight:bold;" onclick="viewModal({{$value->id}})" class="btn btn-primary"> {{$value->nombre}} </a>
        </div><br><br>
        @endforeach
      </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title" id="exampleModalLabel">Eligia que limpieza va a realizar</h5>
          </div>
          @if($rolUsuario == 24 || $rolUsuario == 1 || $rolUsuario == 6  )
          <div class="modal-body">
            <form action="{{route('eleccion-tipo')}}" method="post">
              <input type="hidden" id="id" name="id">
              <input type="hidden" id="tipo" name="tipo">
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <div class="row">
                <div class="col-sm-6 text-center">
                  <button type="submit" class="btn btn-warning" onclick="document.getElementById('tipo').value = 1 ;">Limpieza Baños</button>
                </div>
                <div class="col-sm-6 text-center">
                  <button type="submit" class="btn btn-success" onclick="document.getElementById('tipo').value = 2 ;">Limpieza de Áreas</button>
                </div>
              </div>
            </form>
          </div>
          @endif
          <div class="modal-body">
            <form action="{{route('eleccion-tipo2')}}" method="post">
              <input type="hidden" id="id" name="id">
              <input type="hidden" id="tipo" name="tipo">
              <input type="hidden" id="id_2" name="id_2">
              <input type="hidden" id="tipo2" name="tipo2">
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <div class="row">
                <div class="col-sm-6 text-center">
                  <button type="submit" class="btn btn-warning" onclick="document.getElementById('tipo2').value = 1 ;">Reportes Baños</button>
                </div>
                <div class="col-sm-6 text-center">
                  <button type="submit" class="btn btn-success" onclick="document.getElementById('tipo2').value = 2 ;">Reportes de Áreas</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</section>
<script>
  const viewModal = (id) => {
    document.getElementById("id").value = id;
    $('#exampleModal').modal('show');
  }
</script>
@endsection