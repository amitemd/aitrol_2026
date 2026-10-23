@extends('actividad_comercial_estado.base_actividad')
@section('action-content')
<br>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actividad comercial</title>

  <style>
    .buttonEnviar {
      background-color: #1E90FF !important;
      border: none;
      color: white;
      padding: 10px 30px;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      margin: 20px 0px;
      font-weight: 600;
    }
  </style>

</head>

<body>
<div class="box-body" style="background-color: white;margin-left: 50px;margin-right: 50px;">
  <br>
  <br>
  <form id="editar_plantilla" method="post" action="{{route('ticketpermisos.actividad_comercial_actualizar')}}">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="id" value="{{$actividad->id}}">
    <div class="text-center"><b>Ingrese tipo de Actividad</b></div>
    <br>
    <div class="text-center">
      <input style="width: 30%; height: 40px;  border-radius: 10px;" class="rounded-md" type="text" name="actividad" id="actividad" value="{{$actividad->actividad_comercial}}">
    </div>
    <br>
    <div class="text-center">
      <button type="submit" class="btn btn-primary" style="border-radius: 10px;">
        {{trans('nuevos.actualizar')}}
      </button>
    </div>
  </form>
</div>

</body>

</html>

<script>


</script>






































@endsection