@extends('hospital/prioridademergencia/base')
@section('action-content')

<section class="content">
<div class="box">
<div class="box-header">
         <div class="col-md-2" style="float: right;"> 
        <a class="btn btn-primary"   href="{{route('prioridademergencia.crear')}}">Crear</a>
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
                                <th>Prioridad</th>
                                <th>Color</th>
                                <th>Estado</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($tipos as $tipop)
                        <tr>
                            <td>{{$tipop->nombre}}</td>
                            <td>{{$tipop->prioridad}}</td>
                            <td style="background-color: {{$tipop->color}};">{{$tipop->color}}</td>
                            <td>@if ($tipop->estado == 1)
                                Activo
                                @else
                                Inactivo
                                @endif</td>
                            <td><a class="btn btn-warning" href="{{route('prioridademergencia.editar',['id_tipo' => $tipop->id])}}"><i class="fa fa-edit" aria-hidden="true"></i> Editar
                                
                            </a> 

                            </td>

                            <td><a class="btn btn-primary btn-danger waves-effect waves-float waves-light" href="{{route('prioridademergencia.eliminar_prioridad',['id_tipo' => $tipop->id])}}"><i class="fa fa-trash" aria-hidden="true"></i></i>Eliminar</a></td>

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