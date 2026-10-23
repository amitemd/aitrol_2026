@extends('hospital.base')
@section('action-content')


<div class="content">

	<section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                {{trans('transformularios.Formulario008')}}
                    <small>{{trans('transformularios.Alta')}}</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick ="location.href='{{route('hospital.formulario08',$id_paciente)}}'" class="btn btn-primary btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i> {{trans('transformularios.Regresar')}}</button>
            </div>
        </div>
    </section>

	<div class="row">
		
		<!-- 4.-  -->
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"> {{trans('transformularios.Formulario008')}}Historial Alta</h3>
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
                                <div class="table-responsive">
                                    <table id="alta_de_paciente" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{trans('transformularios.FechayHora')}}</th>
                                                <th>{{trans('transformularios.Cedula')}}</th>
                                                <th>{{trans('transformularios.LugardeAlta')}}</th>
                                                <th>{{trans('transformularios.CondiciónAlta')}}</th>
                                                <th>{{trans('transformularios.DíasdeReposo')}}</th>
                                                <th>{{trans('transformularios.ServiciodeReferencia')}}</th>
                                                <th>{{trans('transformularios.Establecimiento')}}</th>
                                                <th>{{trans('transformularios.CausaAlta')}}</th>
                                                <th>{{trans('transformularios.OBSERVACIONES')}}</th>
                                                <th>{{trans('transformularios.FECHA')}}</th>
                                                <th>{{trans('transformularios.FechayhoradeEmision')}}</th>
                                                <th>{{trans('transformularios.NombreProfesional')}}</th>
                                                <th>{{trans('transformularios.FIRMA')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dato_paciente as $item)
                                            <tr>
                                                <td>{{ $item->created_at }}</td>
                                                <td>{{ $item->id_emer }}</td>
                                                <td>{{ $item->lugar_alta }}</td>
                                                <td>{{ $item->condicion_alta }}</td>
                                                <td>{{ $item->dia_incapacidad }}</td>
                                                <td>{{ $item->servicio_referencia }}</td>
                                                <td>{{ $item->establecimiento }}</td>
                                                <td>{{ $item->causa_alta }}</td>
                                                <td>{{ $item->desc_alta }}</td>
                                                <td>{{ $item->fecha_hora_emision }}</td>
                                                <td>{{ $item->nombre_profesional }}</td>
                                                <td>{{ $item->firma }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
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


@endsection