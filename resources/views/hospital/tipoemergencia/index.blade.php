@extends('hospital/tipoemergencia/base')
@section('action-content')

<section class="content">
<div class="box">
<div class="box-header">
         <div class="col-md-2" style="float: right;"> 
        <a class="btn btn-primary"   href="{{route('tipoemergencia.crear')}}">Crear</a>
          </div>
          <form method="POST" action="">
                {{ csrf_field() }}
                        
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($tipos as $tipoe)
                        <tr>
                            <td>{{$tipoe->nombre}}</td>
                            <td>@if ($tipoe->estado == 1)
                                Activo
                                @else
                                Inactivo
                                @endif</td>
                            <td><a  class="btn btn-primary btn-success waves-effect waves-float waves-light" href="{{route('tipoemergencia.editar',['id'=>$tipoe->id])}}"><i class="fa fa-edit" aria-hidden="true"></i> Editar</a></td>
                            <td><a class="btn btn-primary btn-danger waves-effect waves-float waves-light" href="{{route('tipoemergencia.eliminar_tipoe',['id_tipo' => $tipoe->id])}}"><i class="fa fa-trash" aria-hidden="true"></i></i>Eliminar</a></td>

                        </tr>
                        @endforeach
                        
                        </tbody>
                    
                    </table>    
                   </div>
                   
            </div>
         </div>
        </div>
        </div>
        </form>
        </section>
@endsection    