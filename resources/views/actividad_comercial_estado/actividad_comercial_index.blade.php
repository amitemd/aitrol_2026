@extends('actividad_comercial_estado.base_actividad')
@section('action-content')

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
    <div class="col-md-12" style="text-align: right">
      <a href="{{route('ticketpermisos.actividad_comercial')}}" class="btn btn-primary">{{trans('nuevos.crear')}}</a>
    </div>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row" id="listado_pro_agrup">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Actividad </th>
                <th class="text-center">usuario crea</th>
                <th>Accion</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($actividad as $value)
              <tr>
                <td class="text-center">{{$value->id}}</td>
                <td class="text-center">{{$value->actividad_comercial}}</td>
                <td class="text-center">{{$value->id_usuariocrea}}</td>
                <td>
                  <a href="{{route('ticketpermisos.actividad_comercial_editar',['id'=>$value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                  <a onclick="eliminar({{$value->id}})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</body>

</html>

<script>
  function eliminar(id) {
    Swal.fire({
      title: `Estas seguro de eliminar`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{url('actividad/comercial/eliminar/')}}/" + id,
          dataType: 'json',
          success: function(data) {
            location.reload();
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    });
  }
</script>






































@endsection