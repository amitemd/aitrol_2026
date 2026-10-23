@extends('layouts.app-template-h')
@section('content')
<div class="content">
	<section class="content-header">
      <div class="row">
          <div class="col-md-9 col-sm-9">
              <h3>
                  {{trans('hospitalizacion.MasterUci')}}
              </h3>
          </div>
          
      </div>
    </section>
	<div class="card card-primary">
		<div class="card-header with-border">
			<h3 class="card-title">{{trans('hospitalizacion.PacientesHospitalizados')}}</h3>
	        <div class="card-tools pull-right">
	            <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	        </div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<form id="form_buscar" method="POST" action="{{route('hospitalizacion.buscar_hospitalizado')}}">
						{{ csrf_field() }}
						<div class="form-row">
							<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
								<label class="col-md-3 control-label">{{trans('hospitalizacion.Cedula')}}</label>
								<div class="col-md-9">
									<input type="text" class="form-control input-sm"  name="cedula" id="cedula" autocomplete="off" value="">
								</div>
							</div>
							<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
								<label class="col-md-3 control-label">{{trans('hospitalizacion.Pacientes')}}</label>
							
									<input type="text" class="form-control input-sm"  name="paciente" id="paciente" autocomplete="off" value="">
							
							</div>
							<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
								<label class="col-md-4 control-label">{{trans('hospitalizacion.FechaDesde')}}</label>
								<div class="col-md-9">
									<input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="">
								</div>
							</div>
							<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
								<label class="col-md-4 control-label">{{trans('hospitalizacion.FechaHasta')}}</label>
								<div class="col-md-9">
									<input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="">
								</div>
							</div>
							
							<div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
								<label class="col-md-3 control-label">{{trans('hospitalizacion.Salas')}}</label>
								<div class="col-md-9">
									<select class="form-control" id="sala" name="sala">
										<option value=" ">{{trans('hospitalizacion.Seleccione...')}}</option>
										@foreach($salas as $s)
											<option  value="{{$s->id}}">{{$s->nombre_sala}}</option>
										@endforeach
									</select>
								</div>
							</div>
							
							<div class="form-group col-md-2">
								<button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
							</div>
                            <div class="form-group col-md-2">
								<a class="btn btn-info" type="button"><i class="fa fa-file"></i> Ingreso</a>
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
									<th>{{trans('hospitalizacion.Causa')}}</th>
									<th>{{trans('hospitalizacion.Doctor')}}</th>
									<th>{{trans('hospitalizacion.Sala')}}</th>
									<th>{{trans('hospitalizacion.Estado')}}</th>
									<th>{{trans('hospitalizacion.Accion')}}</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				
				
			</div>
		</div>
	</div>
</div>
@endsection