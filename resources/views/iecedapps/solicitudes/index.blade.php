@extends('layouts.app-template-apps')
@section('content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        Solicitudes
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                        <a href="{{route('charlasapps.create')}}" class="btn btn-primary">Crear</a>
                    </div>
                </div>
            </div>


        </div>
        <div class="card-body">
            <form action="{{route('solicitudes_apps.index')}}" method="POST">
                {{ csrf_field() }}
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-2">
                            <label>Fecha Desde</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="fecha_desde" value="@if($request->fecha_desde!=null){{$request->fecha_desde}}@endif">
                        </div>
                        <div class="col-md-2">
                            <label>Fecha Hasta</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="fecha_hasta" value="@if($request->fecha_hasta!=null){{$request->fecha_hasta}}@endif">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"> Buscar </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-md-12">
                &nbsp;
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripcion</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Procedimiento</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $charlas)
                    @php
                    $user=DB::table('users')->where('id',$charlas->id_usuariocrea)->first();
                    $procedimiento= DB::table('procedimiento')->where('id',$charlas->id_procedimiento)->first();
                    @endphp
                    <tr>
                        <td>{{$charlas->observaciones}}</td>
                        <td>{{date('Y-m-d',strtotime($charlas->created_at))}}</td>
                        <td> {{$user->apellido1}} {{$user->nombre1}}</td>
                        <td>@if(!is_null($procedimiento)){{$procedimiento->nombre}}@endif</td>
                        <td>@if($charlas->estado==1 || $charlas->estado==2) Activo @else Inactivo @endif</td>
                        <td><a class="btn btn-info" href="{{route('solicitudes_apps.edit',['id'=>$charlas->id])}}">VISUALIZAR </a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>
@endsection