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
                        Charlas
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                        <a href="{{route('charlasapps.create')}}" class="btn btn-primary">Crear</a>
                    </div>
                </div>
            </div>


        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripcion</th>
                        <th>Dr.</th>
                        <th>Url</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charlas as $charlas)
                    @php
                    $user=DB::table('users')->where('id',$charlas->id_doctor)->first(); 
                    @endphp
                        <tr>
                            <td>{{$charlas->descripcion}}</td>
                            <td>@if(isset($user)) {{$user->apellido1}} {{$user->nombre1}} @endif</td>
                            <td>{{$charlas->url}}</td>
                            <td>@if($charlas->estado==1) Activo @else Inactivo @endif</td>
                            <td><a class="btn btn-warning" href="{{route('charlasapps.edit',['id'=>$charlas->id])}}">Editar</a></td>
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