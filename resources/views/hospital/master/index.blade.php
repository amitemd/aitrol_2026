@extends('consultam.base')
@section('action-content')
<div class="container-fluid">
  <div class="row">
  	<div class="box box-primary">
  			<div class="box-header">
          <div class="col-md-9 col-sm-9">
              <h3>
                  Agenda de : {{$sala->hospital->nombre_hospital}} - {{$sala->nombre_sala}}
              </h3>
          </div>
        </div>
        <div class="box-body">  
    
					<div class="row">
						<div class="col-md-12">
							<form id="form_buscar" method="POST" action="{{route('cuarto.agenda_hospital',['id_sala' => $id_sala])}}">
								{{ csrf_field() }}
								<div class="form-row">
								
									<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
										<label class="col-md-4 control-label">{{trans('hospitalizacion.FechaDesde')}}</label>
										<div class="col-md-9">
											<input type="date"  data-input="true" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{$fecha_desde}}">
										</div>
									</div>
									<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
										<label class="col-md-4 control-label">{{trans('hospitalizacion.FechaHasta')}}</label>
										<div class="col-md-9">
											<input type="date"  data-input="true" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
										</div>
									</div>
									
									
									</div>
									<!--div class="form-group col-md-2">
										<button class="btn btn-primary" type="button"  onclick="return location.href='{{route("hospital.gcuartos")}}'"> <i class="fa fa-procedures"></i> </button>
									</div-->
									<div class="form-group col-md-2">
										<button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
									</div>
								</div>
							</form>
						</div>
			
						<div class="card-body table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>{{trans('hospitalizacion.Fecha')}}</th>
										<th>{{trans('hospitalizacion.Cedula')}}</th>
										<th>{{trans('hospitalizacion.Paciente')}}</th>
										<th>{{trans('hospitalizacion.Estado')}}</th>
										<th>{{trans('hospitalizacion.Accion')}}</th>
									</tr>
								</thead>
								<tbody>
								@foreach($agendas as $agenda)
								<tr>
									<td>{{substr($agenda->fechaini,0,10)}}</td>
									<td>{{$agenda->id_paciente}}</td>
									<td>{{$agenda->paciente->apellido1}} {{$agenda->paciente->apellido2}} {{$agenda->paciente->nombre1}} {{$agenda->paciente->nombre2}}</td>
									<td>
	                  @if($agenda->estado_cita=='0')
	                    {{'PorConfirmar'}}
	                  @elseif($agenda->estado_cita=='1')
	                    {{'Confirmado'}}
	                  @elseif($agenda->estado_cita=='-1')
	                    {{'No Asiste'}}
	                  @elseif($agenda->estado_cita=='3')
	                    {{'Suspendido'}}
	                  @elseif($agenda->estado_cita=='4')
	                    {{'Admisionado'}}
	                  @endif
									</td>
									<td>
										<a href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}" class="btn btn-warning btn-xs">Editar</a>
									</td>
								</tr>
								@endforeach	
								</tbody>
							</table>
						</div>
				
				
					</div>
				</div>
	</div>
</div>
@endsection