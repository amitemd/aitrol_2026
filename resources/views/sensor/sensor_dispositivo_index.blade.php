@extends('sensor.encabezado')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{ route('sensor.dispositivo_index')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title">SENSOR</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('sensor.dispositivo_crear')}}" class="btn btn-primary">{{trans('nuevos.crear')}}</a>
                    <a href="{{ route('sensor.estadistico_sensor')}}" class="btn btn-primary"> Estadistico </a>
                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th class="text-center"> SERIE </th>
                                        <th class="text-center"> ESTADO </th>
                                        <th class="text-center"> ESTABLECIMIENTO </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dispositivo as $es)
                                    <tr>
                                        <td class="text-center">{{$es->serie}}</td>
                                        <td class="text-center">@if($es->estado == '1') AUTORIZADA @else NO AUTORIZADA @endif</td>
                                        <td class="text-center">@if(!is_null($es->establecimiento)){{$es->establecimiento->nombre}} @endif</td>
                                        <td>
                                            <a href="{{route('sensor.dispositivo_edit',['id'=>$es->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a onclick="eliminar({{$es->id}})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
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
                    url: "{{url('sensor/dispositivo/delete/')}}/" + id,
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