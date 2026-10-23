@extends('hospital.base')
@section('action-content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css"/>

<div class="content">

	<section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                {{trans('transformularios.Formulario008')}}
                    <small>{{trans('transformularios.EnfermedadActualyR')}}</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick ="location.href='{{route('hospital.formulario08',$id_paciente)}}'" class="btn btn-primary btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i> {{trans('transformularios.Regresar')}}</button>
            </div>
        </div>
    </section>

	<div class="row">
		
		<!-- 2.- Inicio de Atención y motivo -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">{{trans('transformularios.HistorialdeEnfermedadActual')}}</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form>
					{{ csrf_field() }}
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
                                @foreach($nombre as $items)
                                <input type="text" class="col-sm-4 form-control form-control-sm my-3" value="{{$items->nombre1}} {{$items->nombre2}} {{$items->apellido1}} {{$items->apellido2}}" readonly>
                                @endforeach
                                <table id="revision" class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{trans('transformularios.FechayHora')}}</th>
                                            <th>{{trans('transformularios.Cedula')}}</th>
                                            <th>{{trans('transformularios.VíaArea')}}</th>
                                            <th>{{trans('transformularios.CondiciónSistemas')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dato_paciente as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->id_emer }}</td>
                                            <td>{{ $item->via_area }}</td>
                                            <td>{{ $item->condicion_sistemas }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>
		
 	</div>

</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $('#revision').DataTable({
            language: {
                processing:     "Tratamiento en curso...",
                search:         "Buscar:",
                lengthMenu:     "Mostrar _MENU_ registros",
                info:           "Mostrar registros del _START_ al _END_ de un toatal de _TOTAL_ registro",
                infoEmpty:      "Mostrado registro del 0 al 0 de un total de  0 registro",
                infoFiltered:   "(Filtrado de un total de _MAX_ registros)",
                infoPostFix:    "",
                loadingRecords: "cargando...",
                zeroRecords:    "No se ha encotrado resultados",
                emptyTable:     "No hay datos disponibles en la tabla.",
                paginate: {
                    first:      "Primero",
                    previous:   "Anterior",
                    next:       "Siguiente",
                    last:       "Ultimo"
                },
            }
        });

    } );

</script>
@endsection