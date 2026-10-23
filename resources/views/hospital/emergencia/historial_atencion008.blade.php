@extends('hospital.base')
@section('action-content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css"/>

<div class="content">

	<section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                {{trans('transformularios.Formulario008')}}
                    <small> {{trans('transformularios.AtenciónyMotivo')}}</small>
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
					<h3 class="box-title">{{trans('transformularios.HistorialdeAtenciónymotivo')}}</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form>
					{{ csrf_field() }}
					<div class="box-body">
                        @foreach($nombre as $items)
                            <input type="text" class="col-sm-4 form-control form-control-sm my-3" value="{{$items->nombre1}} {{$items->nombre2}} {{$items->apellido1}} {{$items->apellido2}}" readonly>
                        @endforeach
                        <table id="atencion" class="table">
                            <thead>
                                <tr>
                                    <th>{{trans('transformularios.Hora')}}</th>
                                    <th>{{trans('transformularios.Cedula')}}</th>
                                    <th>{{trans('transformularios.Causa')}}</th>
                                    <th>{{trans('transformularios.GrupoSanguineo')}}</th>
                                    <th>{{trans('transformularios.Notificaciónalapolicia')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($dato_paciente as $item)
                                <tr>
                                    <td>{{ $item->hora }}</td>
                                    <td>{{ $item->id_emer }}</td>
                                    <td>{{ $item->causa }}</td>
                                    <td> @if(($item->grupo_sanguineo)==1) O+
                                            @elseif(($item->grupo_sanguineo)==2) O-
                                            @elseif(($item->grupo_sanguineo)==3) A+
                                            @elseif(($item->grupo_sanguineo)==4) A-
                                            @elseif(($item->grupo_sanguineo)==5) B+
                                            @elseif(($item->grupo_sanguineo)==6) B-
                                            @elseif(($item->grupo_sanguineo)==7) AB+
                                            @elseif(($item->grupo_sanguineo)==8) AB- @endif
                                    </td>
                                    <td>{{ $item->notificacion_policia }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
        $('#atencion').DataTable({
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