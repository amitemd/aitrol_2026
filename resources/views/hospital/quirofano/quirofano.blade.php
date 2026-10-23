@extends('layouts.app-template-h')
@section('content')
<style>
	
</style>

<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" id="content">

		</div>
	</div>
</div>

<div class="content">
	<div class="col-md-12 col-sm-6">
		<h3>QUIRÓFANO</h3>
	</div>
	<section class="content">

		<div class="row">
			<div class="col-md-12">
				<div class="row">
					
					<div class="col-md-12">
						<div class="card card-solid">
							<div class="card-header d-flex justify-content-between align-items-center">
								<h4> @if($tipo == 1) OPERACIONES AGENDADAS @else IMAGENES @endif</h4>
							</div>
							<div class="card-header with-border text-center">

							</div>
							<div class="card card-success">
                                <div class="col-md-12">
                                
                                    <form id="form_buscar" method="POST" action="{{route('quirofano.buscar_quirofano',['tipo' => $tipo])}}">
                                        {{ csrf_field() }}
                                        <div class="form-row">
                                            <input type="hidden" name="tipo" id="tipo" value="{{$tipo}}" class="form-control input-sm">
                                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Fecha Desde')}}</label>
                                                <div class="col-md-9">
                                                    <input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{$fecha_desde}}">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Fecha Hasta')}}</label>
                                                <div class="col-md-9">
                                                    <input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Apellidos')}}</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="apellidos" id="apellidos" placeholder="Apellidos">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Nombres')}}</label>
                                                <div class="col-md-9">
                                                    <input class="form-control" name="nombres" id="nombres" placeholder="Nombres">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Doctor')}}</label>
                                                <div class="col-md-9">
                                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm">
                                                        <option value="">Seleccione</option>
                                                        @foreach($doctores as $doctor)
                                                            <option value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group col-md-2" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-5 control-label">{{trans('quirofano.Especialidad')}}</label>
                                                <div class="col-md-9">
                                                    <select id="espid" name="espid" class="form-control input-sm">
                                                        <option value="">Todos</option>
                                                        @foreach($especialidades as $especialidad)
                                                            <option value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-md-2" style="padding-left: 0px;padding-right: 0px;">
                                                <label class="col-md-4 control-label">{{trans('Seguro')}}</label>
                                                <div class="col-md-9">
                                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm">
                                                        <option value="">Seleccione</option>
                                                        @foreach($seguros as $seguro)
                                                            <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            

                                            <div class="form-group col-md-1">
                                                <br>
                                                <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>

                                    
                                </div>
								<div class="card-body no-padding">
									<div class="card-body table-responsive">
										<table  class="table table-hover">
											<thead class="table-primary">
												<tr>
                                                    <th scope="col">FECHA</th>
                                                    <th scope="col">HORA</th>
													<th scope="col">CÉDULA</th>
													<th scope="col">APELLIDOS</th>
													<th scope="col">NOMBRES</th>
													<th scope="col">SEGURO</th>
													<th scope="col">DOCTOR</th>
                                                    <th scope="col">TIPO</th>
                                                    <th scope="col">ESTADO</th>
                                                    <th scope="col">ACCIÓN</th>
												</tr>
											</thead>
											<tbody>
												@foreach($agendas_pac as $value)
                                                    @php
                                                        $solicitud = Sis_medico\Ho_Solicitud::where('id_agenda',$value->id_agenda)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{substr($value->fechaini,0,10)}}</td>
                                                        <td>{{substr($value->fechaini,10,10)}}</td>
                                                        <td>{{$value->id}}</td>
                                                        <td>{{$value->apellido1}} {{$value->apellido2}}</td>
                                                        <td>{{$value->nombre1}} {{$value->nombre2}}</td>
                                                        <td>@if(!is_null($value->seguro_nombre))
                                                              {{$value->seguro_nombre}}
                                                            @endif
                                                        </td>
                                                        <td>{{$value->dnombre1}} {{$value->dapellido1}}</td>
                                                        <td>
                                                            @if($value->proc_consul=='0')
                                                                CONSULTA
                                                            @elseif($value->proc_consul=='1')
                                                              @if(isset($agendas_proc[$value->id_agenda])) {{$agendas_proc[$value->id_agenda]['0']}}
                                                              @else
                                                                PROCEDIMIENTO
                                                              @endif
                                                            @endif
                                                        </td>
                                                        
                                                        @php

                                                            $contador_cie10 = 0;

                                                            $verificar = \Sis_medico\Historiaclinica::where('id_agenda', $value->id_agenda)->first();
                                                            $nueva_agenda = \Sis_medico\Agenda::find($value->id_agenda)->first();

                                                            $contador_cie10 = DB::table('hc_cie10 as c')->where('hcid',$value->hcid)->get()->count();
                                                        @endphp
                                                        <td>
                                                            @if($value->omni=='OM')
                                                                @if($value->estado_cita==4)
                                                                  Ingresado
                                                                @elseif($value->estado_cita==5)
                                                                  Alta
                                                                @elseif($value->estado_cita==6)
                                                                  Emergencia
                                                                @endif
                                                              @elseif($contador_cie10 >'1')
                                                                ATENDIDO
                                                              @elseif(!is_null($verificar))
                                                                ADMISIONADO
                                                              @else
                                                                @if($nueva_agenda->estado_cita == 0)
                                                                  Por Confirmar
                                                                @elseif($nueva_agenda->estado_cita == 1)
                                                                  Confirmada
                                                                @elseif($nueva_agenda->estado_cita == 2)
                                                                  Reagendado
                                                                @endif
                                                              @endif
                                                        </td>
                                                        <td>
                                                            @if(!is_null($solicitud))
                                                                <a type="button" class="btn btn-info" id="btn_detalle" href="{{route('quirofano.quirofano_paciente',['tipo' => $tipo,'id_solicitud' => $solicitud->id])}}">Ver Detalle</a>
                                                                <a href="{{route('hospital.formulario008_pdf', ['id_solicitud' => $solicitud->id])}}" target="_blank"class="btn btn-info btn-xs" ><i class="fa fa-download"></i> {{trans('emergencia.Formulario008')}}</a>
                                                            @endif
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
				</div>
			</div>
		</div>
	</section>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>

<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script>
	$(document).ready(function() {
		$('#example2').DataTable({
			'paging': false,
			'lengthChange': false,
			'searching': false,
			'ordering': true,
			'info': false,
			'autoWidth': false
		})
	});
</script>

@endsection