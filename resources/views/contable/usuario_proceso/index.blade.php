@extends('contable.usuario_proceso.base')
@section('action-content')
<section class="content">
	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Usuario Proceso</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mantenimiento Usuario Proceso</li>
        </ol>
    </nav>
    <div class="box">
    	<div class="box-header header_new">
            <div class="col-md-9">
                <!--<h8 class="box-title size_text">Empleados</h8>-->
                <!--<label class="size_text" for="title">EMPLEADOS</label>-->
                <h3 class="box-title">Usuario Proceso</h3>
            </div>

            <div class="col-md-1 text-right">
                <button type="button" onclick="location.href='{{route('compraspedidos.create')}} '"class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Crear
                </button>
            </div>
        </div>

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO </label>
            </div>
        </div>

        <div class="box-body dobra">
        	<div class="form-group col-md-12">
        		<div class="form-row">
        			<div id="contenedor">
        				<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        					<div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well-dark'>
                                            	<th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cedula</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Paso</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($usuarios as $usuario)
                                        @php
                                        $nombres = Sis_medico\User::where('id',$usuario->id_usuario)->first();
                                        $passo = Sis_medico\Ct_Usuario_Proceso::where('id_usuario',$usuario->id_usuario)->get();
                              
                                        $nombre_paso="";
                                        foreach ($passo as $datos ){
                                            $paso = Sis_medico\Ct_Paso_Proceso::where('id',$datos->id_paso)->first();
                                            $nombre_paso = $paso->nombre. "-". $nombre_paso;
                                        }
                                        
                                        @endphp
                                           	<tr>
                                           		<td>{{$usuario->id}}</td>
                                                <td>{{$nombres->nombre1}} {{$nombres->nombre2}} {{$nombres->apellido1}} {{$nombres->apellido2}} </td>
                                                <td>{{$usuario->id_usuario}}</td>
                                                <td>{{$nombre_paso}}</td>
                                                <td style="text-align: center;">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <a href="{{ route('compraspedidos.editar', ['id' => $usuario->id])}}" class="btn btn-success btn-gray">
                                                    <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                           	</tr>
                                
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                    <div class="col-md-12">
                                        <div class="text-right">
                                            {{ $usuarios->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
        				</div>
        			</div>
        		</div>
        	</div>
        </div>
    </div>

</section>

@endsection