@extends('layouts.app-template-hospital')
@section('content')

<style>
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box; 
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }

</style>

 <?php 
	date_default_timezone_set('America/Guayaquil');
	$fecha_actual=date("Y-m-d H:i:s");
 ?>


<div class="content">

	<section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                    FORMULARIO 008
                    <small>/Emergencia</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick ="location.href='{{route('hospital.emergencia')}}'" class="btn btn-primary btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i> Regresar</button>
            </div>
        </div>
    </section>

	<div class="row">
		<!-- 1.- Registro de Primera Admisión -->
        <div class="col-md-12">
          	<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">1.- Registro de Primera Admisión</h3>

					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<form>
					{{ csrf_field() }}
					<div class="box-body">
						<div class="row">
							@foreach($datos_paciente as $items)
							<div class="col-md-12">
								<fieldset disabled>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label class="col-form-label-sm">Apellidos Nombres</label>
											<input type="text" class="form-control form-control-sm nombre" name="nombre" id="nombre" value="{{$items->nombre1}} {{$items->nombre2}} {{$items->apellido1}} {{$items->apellido2}}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Cédula de Ciudadanía</label>
											<input type="text" class="form-control form-control-sm" id="cedula" name="cedula" value="{{ $items->id }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Ciudad</label>
											<input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad" value="{{ $items->ciudad }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Teléfono</label>
											<input type="text" class="form-control form-control-sm" id="telefono1" name="telefono1" value="{{ $items->telefono1 }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Celular</label>
											<input type="text" class="form-control form-control-sm" id="telefono2" name="telefono2" value="{{ $items->telefono2 }}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Dirección de residencia</label>
											<input type="text" class="form-control form-control-sm" name="direccion" id="direccion" value="{{ $items->direccion }}">
											<small id="emailHelp" class="form-text text-muted">(Calle Y N° - Manzana Y CASA)</small>
										</div>
										@foreach($adicional as $datos)
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">Barrio</label>
											<input type="text" class="form-control form-control-sm" id="barrio" name="barrio" value="{{ $datos->barrio }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Parroquia</label>
											<input type="text" class="form-control form-control-sm" id="parroquia" name="parroquia" value="{{ $datos->parroquia}}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Cantón</label>
											<input type="text" class="form-control form-control-sm" id="canto" name="canto" value="{{ $datos->canto }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Provincia</label>
											<input type="text" class="form-control form-control-sm" id="provincia" name="provincia" value="{{ $datos->provincia }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Zona (U/R)</label>
											<input type="text" class="form-control form-control-sm" id="zona_ur" name="zona_ur" value="{{ $datos->zona_ur }}">
										</div>
										@endforeach
									</div>
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Fecha de nacimiento</label>
											<input type="text" class="form-control form-control-sm" id="f_nacimiento" name="f_nacimiento" value="{{ $items->fecha_nacimiento }}">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Lugar de nacimiento</label>
											<input type="text" class="form-control form-control-sm" id="lugar_nacimento" name="lugar_nacimento" value="{{ $items->lugar_nacimiento }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Nacionalidad</label>
											<input type="text" class="form-control form-control-sm" id="nacionalidad" name="nacionalidad" value="{{ $items->id_pais }}">	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">Gr.Cultural</label>
											<input type="text" class="form-control form-control-sm" id="grupo_cultural" name="grupo_cultural"
											value="@if(($datos->grupo_cultural)==1) Mestizo @elseif(($datos->grupo_cultural)==2) Morisco @elseif(($datos->grupo_cultural)==3) Cholo o coyote @elseif(($datos->grupo_cultural)==4) Mulatos @elseif(($datos->grupo_cultural)==5) Zambo @elseif(($datos->grupo_cultural)==6) Castizo @elseif(($datos->grupo_cultural)==7)Criollo @elseif(($datos->grupo_cultural)==8) Chino @endif">
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">Edad</label>
											<input type="text" class="form-control form-control-sm" id="edad" name="edad">	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">Sexo</label>
											<input type="text" class="form-control form-control-sm" id="sexo" name="sexo" value="@if(($items->sexo) == 1) Masculino @else(($items->sexo) == 2) Femenino @endif">	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">Estado Civil</label>
											<input type="text" class="form-control form-control-sm" id="estado" name="estado" value="@if(($items->estadocivil) == 1) soltero @elseif(($items->estadocivil) == 2) casado @elseif(($items->estadocivil) == 3) viduo @elseif(($items->estadocivil) == 4) divorciado @elseif(($items->estadocivil) == 5) union libre @elseif(($items->estadocivil) == 6) union de hecho @endif">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Instrucción</label>
											<input type="text" class="form-control form-control-sm" id="instruccion" name="instruccion">	
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Fecha de Admisión</label>
											<input type="text" class="form-control form-control-sm" id="f_admision" name="f_admision" value="{{ $items->created_at}}">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Ocupación</label>
											<input type="text" class="form-control form-control-sm" id="ocupacion" name="ocupacion" value="{{ $items->ocupacion }}">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Empresa donde trabaja</label>
											<input type="text" class="form-control form-control-sm" id="empresa" name="empresa" value="{{ $datos->trabajo }}">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Tipo de seguro social</label>
											<input type="text" class="form-control form-control-sm" id="seguro" name="seguro" value="{{ $items->id_seguro }}">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Referido de:</label>
											<input type="text" class="form-control form-control-sm" id="referido" name="referido" value="{{ $items->referido }}">	
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label class="col-form-label-sm">En caso necesario llamar a:</label>
											<input type="text" class="form-control form-control-sm" id="nombrefamiliar" name="nombrefamiliar" value="@if($items->nombre1familiar!=null){{ $items->nombre1familiar }}@endif @if($items->nombre2familiar!=null){{ $items->nombre2familiar }}@endif @if($items->apellido1familiar!= null){{ $items->apellido1familiar }}@endif @if($items->apellido2familiar != null){{ $items->apellido2familiar }}@endif">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Parentesco - Afinidad</label>
											<input type="text" class="form-control form-control-sm" id="parentesco" name="parentesco" value="{{ $items->parentesco }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Nº Telefono</label>
											<input type="text" class="form-control form-control-sm" id="telefono_llamar" name="telefono_llamar" value="{{ $items->telefono_llamar }}">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Direcciòn Familiar</label>
											<input type="text" class="form-control form-control-sm" id="direccion_familiar" name="direccion_familiar" value="{{ $datos->direccion_familiar }}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4">
											<label class="col-form-label-sm">Forma de Llegada</label>
											<input type="text" class="form-control form-control-sm" id="forma_llegada" name="forma_llegada" value="{{ $datos->forma_llegada }}">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Fuente de Información</label>
											<input type="text" class="form-control form-control-sm" id="fuente_informacion" name="fuente_informacion" value="{{ $datos->fuente_informacion }}">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">Nº Telefono</label>
											<input type="text" class="form-control form-control-sm" id="telefono_inst_per_paci" name="telefono_inst_per_paci" value="{{ $datos->elefono_inst_per_paci }}">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">Admisionista</label>
											<input type="text" class="form-control form-control-sm" id="admisionista" name="admisionista" value="{{ $datos->admisionista }}">
										</div>
									</div>
								</fieldset>
							</div>
							<!-- /.col -->
							@endforeach
						</div>
						<!-- /.row -->
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<!-- <button type="submit" class="btn btn-sm btn-primary ml-3 mr-2">Guardar</button> -->
							<!-- <a href="#" class="btn btn-sm btn-warning">Historial</a> -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
			<!-- /.box -->
        </div>
		<!-- /.col -->
		
		<!-- 2.- Inicio de Atención y motivo -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">2.- Inicio de Atención y motivo</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emergencia.atencio_motivo')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_atencio'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_atencio')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								@endif
								
								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<div class="form-group col-md-3 row">
										<label class="col-sm-4 col-form-label">Hora:</label>
										<div class="col-sm-8">
											<input type="datetime" class="form-control form-control-sm" value="<?=$fecha_actual ?>" name="hora" id="hora">
										</div>
									</div>
									<div class="form-group col-md-5 row">
										<label class="col-sm-2 col-form-label">Causa:</label>
										<div class="col-sm-10">
											<textarea class="form-control" name="causa" id="causa" rows="3"></textarea required>
										</div>
									</div>
									<div class="form-group col-md-4 row">
										<label class="col-sm-4 col-form-label">Grupo Sanguineo y Factor RH:</label>
										<div class="col-sm-8">
											<select class="form-control" name="sanguineo_factor" id="sanguineo_factor">
												<option value="1">O+</option>
												<option value="2">O-</option>
												<option value="3">A+</option>
												<option value="4">A-</option>
												<option value="5">B+</option>
												<option value="6">B-</option>
												<option value="7">AB+</option>
												<option value="8">AB-</option>
											</select>
										</div>
									</div>
								</div>
								

								<div class="form-row">
									<div class="form-group col-md-12 row">
										<label class="col-sm-1 col-form-label">Notificación a la policia</label>
										<div class="col-sm-11">
											<textarea class="form-control" name="notificacion_policial" id="notificacion_policial" rows="3" ></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.atencion', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

  		<!-- 3.- Enfermedades actuales y revision de sistema -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">3.- Enfermedad Actual y Revisión de Sistemas</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emergerncia.enfer_actual')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_enfer_actu_revi'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_enfer_actu_revi')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								@endif
								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<div class="form-group col-md-6 row">
										<label class="col-sm-2 col-form-label">Vía Area:</label>
										<div class="col-sm-10">
											<textarea class="form-control" name="via_area" id="via_area" rows="3"></textarea>
										</div>
									</div>

									<div class="form-group col-md-6 row">
										<label class="col-sm-2 col-form-label">Condición Sistemas:</label>
										<div class="col-sm-10">
											<textarea class="form-control" name="condicion_sistemas" id="condicion_sistemas" rows="3"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.revision', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

  		<!-- 4.- Accidente, violencia, intoxicacion envenenamiento o quemadura -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">4.- Accidente, Violencia, Intoxicación Envenenamiento o Quemadura</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emergencia.accd_viol_intx')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_viol_intox'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_viol_intox')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								@endif
								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<!-- <div class="form-group col-md-3">
										<label>Fecha y Hora del Evento</label>
										<input type="text" class="form-control" name="fecha_hora" id="fecha_hora">
									</div> -->
									<div class="form-group col-md-4">
										<label>Lugar del Evento</label>
										<input type="text" class="form-control" id="lugar_evento" name="lugar_evento" required>
									</div>
									<div class="form-group col-md-4">
										<label>Dirección del Evento</label>
										<input type="text" class="form-control" id="direccion_evento" name="direccion_evento" required>
									</div>
									<div class="form-group col-md-4">
										<label>Custodia Policial</label>
										<input type="text" class="form-control" id="custodia_policial" name="custodia_policial" required>
									</div>
								</div>
								<div class="box-header with-border">
									<h3 class="box-title">Tipo de Evento:</h3>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-row">

												<div class="form-group col-md-8 row">
													<label class="col-sm-2 col-form-label">Observaciones:</label>
													<div class="col-sm-10">
														<textarea class="form-control" name="observacion" id="observacion" rows="3"></textarea>
													</div>
												</div>
												
												<div class="form-group col-md-2">
													<input type="text" class="form-control" name="aliento_etilico" id="aliento_etilico" required>
													<small class="form-text">ALIENTO ETÍLICO</small>
												</div>

												<div class="form-group col-md-2">
													<input type="text" class="form-control" name="valor_alcocheck" id="valor_alcocheck" required>
													<small class="form-text">VALOR ALCOCHECK</small>
												</div>

											</div>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.accidente', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

		<!-- 5.- Antecendentes Personales y Familiares -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">5.- Antecendentes Personales y Familiares</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emergencia.ante_pers_familiar')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_antecedentes'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_antecedentes')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								@endif
								<div class="form-group row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<label class="col-sm-2 col-form-label">Clinico</label>
									<div class="col-sm-10">
										<textarea class="form-control" name="clinico" id="clinico" rows="3"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.antecendentes', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

		<!-- 6.- Signos Vitales, Mediciones y Valores -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">6.- Signos Vitales, Mediciones y Valores</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emegerncia.signos_vitales')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							@if(session('message_signos_vitales'))
							<div class="alert alert-warning alert-dismissible fade show" role="alert">
								{{session('message_signos_vitales')}}
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							@endif
							<div class="col-md-12">

								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<div class="form-group col-md-2">
										<label>Presión Arterial</label>
										<input type="text" class="form-control form-control-sm" name="presion_arterial" id="presion_arterial" required>
									</div>
									<div class="form-group col-md-1">
										<label>F.Cardiaca</label>
										<input type="number" class="form-control form-control-sm" name="cardiaca" id="cardiaca" required>
										<small class="form-text">min</small>
									</div>
									<div class="form-group col-md-2">
										<label>F.Respiratoria</label>
										<input type="number" class="form-control form-control-sm" name="respiratoria" id="respiratoria" required>
										<small class="form-text">min</small>
									</div>
									<div class="form-group col-md-2">
										<label>Temp Bucal °C</label>
										<input type="number" class="form-control form-control-sm" name="temp_bucal" id="temp_bucal" required>
									</div>
									<div class="form-group col-md-2">
										<label>Temp Axilar °C</label>
										<input type="number" class="form-control form-control-sm" name="temp_axilar" id="temp_axilar">
									</div>
									<div class="form-group col-md-1">
										<label>Peso Kg</label>
										<input type="number" class="form-control form-control-sm" name="peso_kg" id="peso_kg" required>
									</div>
									<div class="form-group col-md-1">
										<label>Talla</label>
										<input type="number" class="form-control form-control-sm" name="talla" id="talla" required>
										<small class="form-text">m</small>
									</div>

								</div>
								<hr>
								<div class="form-row">
									<div class="form-group col-md-1">
										<label>Glasgow</label>
										<!-- <input type="text" class="form-control" name="glasgow" id="glasgow"> -->
									</div>
									<div class="form-group col-md-1">
										<label>Ocular 0</label>
										<select class="form-control form-control-sm" name="ocultar" id="ocultar">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-1">
										<label>Verbal 0</label>
										<select class="form-control form-control-sm" name="verbal" id="verbal">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-1">
										<label>Motora</label>
										<select class="form-control form-control-sm" name="motora" id="motora">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-1">
										<label>Total</label>
										<p id="total"></p>
									</div>

									<div class="form-group col-md-2">
										<label>Reacción Pupilar Der.</label>
										<input type="text" class="form-control form-control-sm" name="reaccion_pupilar_d" id="reaccion_pupilar_d" required >
									</div>

									<div class="form-group col-md-2">
										<label>Reacción Puppilar Izq.</label>
										<input type="text" class="form-control form-control-sm" name="reaccion_pupilar_i" id="reaccion_pupilar_i" required >
									</div>

									<div class="form-group col-md-2">
										<label>T. LLenado Capilar</label>
										<input type="number" class="form-control form-control-sm" name="llenado_capilar" id="llenado_capilar" required>
									</div>

									<div class="form-group col-md-1">
										<label>S.Oxigeno</label>
										<input type="number" class="form-control form-control-sm" name="satura_oxigeno" id="satura_oxigeno" required >
									</div>

								</div>
									
							</div>
						</div>
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.signos_vitales', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

		<!--7.- Exámen Físico y Diagnóstico -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">7.- Exámen Físico y Diagnóstico</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div style = "display: none;" class="box-body">
					<div class="row">
						<div class="col-md-12">

						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 8.- Localización Del Lesiones -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">8.- Localización Del Lesiones</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
  				<form>
					<div style="display: none;" class="box-body">
						<div class="form-row">
							<div class="col-md-6">
  								<div class="row">
  									<div class="col-md-8">
										<canvas width="500px" height="450px" id="micanvas"></canvas>
									</div>
									<div class="col-md-4">
										<!-- <h6>Herramienta de colores</h6> -->
										<input type="color" id="defcolor" oninput="color(this.value);">
										<!-- <input type="range" id="defgrosor" oninput="grosor(this.value);" min="1" max="5"> -->
										<a href="#" class="btn btn-sm btn-warning">Guardar</a>
									</div>
								</div>
								
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<label>Descripción</label>
									<textarea class="form-control" name="description" id="description" rows="3"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

		<!--9.- Emergencia Obstétrica -->
		<div class="col-md-12">
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">9.- Emergencia Obstétrica</h3>
					<div class="box-tools pull-rigth">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<form action="{{ route('emergencias.obstetrica')}}" method="post">
				{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_obstetrica'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_obstetrica')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								@endif
								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<div class="form-group col-md-3">
										<label>Gestas</label>
										<input type="text" class="form-control" name="gestas" id="gestas" required >
									</div>
									<div class="form-group col-md-3">
										<label>Partos</label>
										<select class="form-control form-control-sm" name="partos" id="partos">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Abortos</label>
										<select class="form-control form-control-sm" name="abortos" id="abortos" required >
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Cesáreas</label>
										<select class="form-control form-control-sm" name="cesareas" id="cesareas">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-3">
										<label>Fecha Última <br> Mentruación</label>
										<input type="date" class="form-control form-control-sm" name="fec_mestruacion" id="fec_mestruacion" value="<?php echo date("Y-m-d"); ?>">
									</div>
									<div class="form-group col-md-3">
										<label>Fecha Probable <br> de Parto</label>
										<input type="date" class="form-control form-control-sm" name="fec_parto" id="fec_parto" value="<?php echo date("Y-m-d"); ?>">
									</div>

									<div class="form-group col-md-1">
										<label>Nivel de <br> Riesgo</label>
										<select class="form-control form-control-sm" name="nivel_riesgo" id="nivel_riesgo">
											<option value="0">0</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
										</select>
									</div>
									<div class="form-group col-md-2">
										<label>Semanas <br> Gestación</label>
										<select class="form-control form-control-sm" name="semana_gestacion" id="semana_gestacion">
											<option value="0">0 Semanas</option>
											<option value="1 ">1 Semanas</option>
											<option value="2 ">2 Semanas</option>
											<option value="3 ">3 Semanas</option>
											<option value="4 ">4 Semanas</option>
											<option value="5 ">5 Semanas</option>
											<option value="6 ">6 Semanas</option>
											<option value="7 ">7 Semanas</option>
											<option value="8 ">8 Semanas</option>
											<option value="9 ">9 Semanas</option>
											<option value="10">10 Semanas</option>
											<option value="11">11 Semanas</option>
											<option value="12">12 Semanas</option>
											<option value="13">13 Semanas</option>
											<option value="14">14 Semanas</option>
											<option value="15">15 Semanas</option>
											<option value="16">16 Semanas</option>
											<option value="17">17 Semanas</option>
											<option value="18">18 Semanas</option>
											<option value="19">19 Semanas</option>
											<option value="20">20 Semanas</option>
											<option value="21">21 Semanas</option>
											<option value="22">22 Semanas</option>
											<option value="23">23 Semanas</option>
											<option value="24">24 Semanas</option>
											<option value="25">25 Semanas</option>
											<option value="26">26 Semanas</option>
											<option value="27">27 Semanas</option>
											<option value="28">28 Semanas</option>
											<option value="29">29 Semanas</option>
											<option value="30">30 Semanas</option>
											<option value="31">31 Semanas</option>
											<option value="32">32 Semanas</option>
											<option value="33">33 Semanas</option>
											<option value="34">34 Semanas</option>
											<option value="35">35 Semanas</option>
											<option value="36">36 Semanas</option>
											<option value="37">37 Semanas</option>
											<option value="38">38 Semanas</option>
											<option value="39">39 Semanas</option>
											<option value="40">40 Semanas</option>
											<option value="41">41 Semanas</option>
											<option value="42">42 Semanas</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Movimiento <br> Fetal</label>
										<select class="form-control form-control-sm" name="movimiento_fetal" id="movimiento_fetal">
											<option value="1">Si</option>
											<option value="2">No</option>
										</select>
									</div>

								</div>

								<div class="form-row">
									<div class="form-group col-md-3">
										<label>Frecuencia <br> C. Fetal</label>
										<input type="text" class="form-control form-control-sm" name="frec_fetal" id="frec_fetal" required >
									</div>
									<div class="form-group col-md-3">
										<label>Membranas <br> Rotas</label>
										<select class="form-control form-control-sm" name="membranas_rotas" id="membranas_rotas">
											<option value="1">Si</option>
											<option value="2">No</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Tiempo de <br> Ruptura</label>
										<select class="form-control form-control-sm" name="tiempo_ruptura" id="tiempo_ruptura">
											<option value="1">15</option>
											<option value="2">30</option>
											<option value="3">45</option>
											<option value="4">60</option>
											<option value="5">75</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Altura <br> Uterina</label>
										<input type="text" class="form-control form-control-sm" name="altura_uterina" id="altura_uterina" required >
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-3">
										<label>Presentación</label>
										<input type="text" class="form-control form-control-sm" name="presentacion" id="presentacion" required >
									</div>
									<div class="form-group col-md-3">
										<label>Dilatación</label>
										<input type="text" class="form-control form-control-sm" name="dilatacion" id="dilatacion" required >
									</div>
									<div class="form-group col-md-3">
										<label>Borramiento</label>
										<input type="text" class="form-control form-control-sm" name="borramiento" id="borramiento">
									</div>
									<div class="form-group col-md-3">
										<label>Plano</label>
										<input type="text" class="form-control form-control-sm" name="plano" id="plano" required >
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-3">
										<label>Pelvis Útil</label>
										<input type="text" class="form-control form-control-sm" name="pelvis_util" id="pelvis_util" required >
									</div>
									<div class="form-group col-md-3">
										<label>Sangramiento</label>
										<select class="form-control form-control-sm"  name="sangramiento" id="sangramiento">
											<option value="1">Si</option>
											<option value="2">No</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>Contracciones</label>
										<select class="form-control form-control-sm" name="contracciones" id="contracciones">
											<option value="1">Si</option>
											<option value="2">No</option>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.obstetrica', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>

		<!--10.- Solicitud de Exámenes -->
		<div class="col-md-12">
          	<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">10.- Solicitud de Exámenes</h3>

					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<div style="display: none;" class="box-body">
					<div class="row">
						<div class="col-md-12">

						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- ./box-body -->
				<div class="box-footer">
					<div class="row">
						
					</div>
					<!-- /.row -->
				</div>
				<!-- /.box-footer -->
			</div>
			<!-- /.box -->
        </div>

		<!--11.- Diagnostico de Ingreso -->
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-6">
					<div class="box box-primary collapsed-box">
						<div class="box-header with-border">
							<h3 class="box-title">11.- Diagnostico de Ingreso</h3>

							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<!-- /.box-header -->
						<div style = "display: none;" class="box-body">
							<div class="row">
								<div class="col-md-12">

								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->
						</div>
						<!-- ./box-body -->
						<div class="box-footer">
							<div class="row">
								
							</div>
							<!-- /.row -->
						</div>
						<!-- /.box-footer -->
					</div>
				</div>
				<div class="col-md-6">
					<div class="box box-primary collapsed-box">
						<div class="box-header with-border">
							<h3 class="box-title">12.- Diagnostico de Alta</h3>

							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<!-- /.box-header -->
						<div style = "display: none;" class="box-body">
							<div class="row">
								<div class="col-md-12">

								</div>
								<!-- /.col -->
							</div>
							<!-- /.row -->
						</div>
						<!-- ./box-body -->
						<div class="box-footer">
							<div class="row">
								
							</div>
							<!-- /.row -->
						</div>
						<!-- /.box-footer -->
					</div>
				</div>
			</div>
			<!-- /.row -->
        </div>

		<!-- 13.- Plan de Tratamiento -->
		<div class="col-md-12">
          	<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">13.- Plan de Tratamiento</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<form action="{{ route('guardar.tratamiento')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
							@if(session('message_tratamiento'))
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									{{session('message_tratamiento')}}
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							@endif

							<div class="form-row">
								<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
								<div class="form-group col-md-5">
									<label>Nombre del Medicamento</label>
									<input type="text" class="form-control"name="nombre" id="nombre" required >
								</div>
								<div class="form-group col-md-5">
									<label>Presentación</label>
									<input type="text" class="form-control"name="presentacion" id="presentacion" required >
								</div>
								<div class="form-group col-md-2">
									<label>Cantidad</label>
									<input type="number" class="form-control" name="cantidad" id="cantidad" required >
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label>Concetración</label>
									<input type="text" class="form-control"name="concentracion" id="concentracion" required >
								</div>
								<div class="form-group col-md-5">
									<label>Dosis</label>
									<input type="text" class="form-control" name="dosis" id="dosis">
								</div>
								<div class="form-group col-md-2">
									<label>Unidad</label>
									<select class="form-control" name="unidad" id="unidad">
										<option>ml</option>
										<option>g</option>
										<option>mg</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label>Via</label>
									<input type="text" class="form-control" name="via" id="via">
								</div>
								<div class="form-group col-md-6">
									<label>Frencuencia</label>
									<input type="text" class="form-control" name="frecuencia" id="frecuencia">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label>Duracion</label>
									<input type="text" class="form-control" name="duracion" id="duracion" required >
								</div>
								<div class="form-group col-md-6">
									<label>Indicaciones Medicinas</label>
									<input type="text" class="form-control" name="indicaciones_medicinas" id="indicaciones_medicinas">
								</div>
							</div>

							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.tratamiento', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
			<!-- /.box -->
        </div>
		<!-- /.col -->

		<!-- 14.- Alta -->
		<div class="col-md-12">
          	<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">14.- Alta</h3>

					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<form action="{{ route('guardar.alta')}}" method="post">
					{{ csrf_field() }}
					<div style="display: none;" class="box-body">
						<div class="row">
							<div class="col-md-12">
								@if(session('message_formulario008_alta'))
									<div class="alert alert-warning alert-dismissible fade show" role="alert">
										{{session('message_formulario008_alta')}}
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								@endif
								
								<div class="form-row">
									<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
									<div class="form-group col-md-4 row">
										<label class="col-sm-5 col-form-label">Lugar de Alta</label>
										<div class="col-sm-7">
										<input type="text" class="form-control" name="lugar_alta" id="lugar_alta" required >
										</div>
									</div>
									<div class="form-group col-md-4 row">
										<label class="col-sm-5 col-form-label">Condición Alta</label>
										<div class="col-sm-7">
										<input type="text" class="form-control" name="condicion_alta" id="condicion_alta" required >
										</div>
									</div>
									<div class="form-group col-md-4 row">
										<label class="col-sm-6 col-form-label">Días de Reposo</label>
										<div class="col-sm-6">
										<input type="text" class="form-control" name="dia_incapacidad" id="dia_incapacidad" required >
										</div>
									</div>

								</div>
								
								<div class="form-row">

									<div class="form-group col-md-4 row">
										<label class="col-sm-5 col-form-label">Servicio de Referencia</label>
										<div class="col-sm-7">
											<select class="form-control form-control-sm" name="servicio_referencia" id="servicio_referencia">
												<option value="0">Seleccion..</option>
												<option value="1">Hospitalaria</option>
												<option value="2">Cuidados Intensivos</option>
												<option value="3">Quirófano</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-4 row">
										<label class="col-sm-5 col-form-label">Establecimiento</label>
										<div class="col-sm-7">
											<input type="text" class="form-control" name="establecimiento" id="establecimiento" required >
										</div>
									</div>
									<div class="form-group col-md-4 row">
										<label class="col-sm-6 col-form-label">Causa Alta</label>
										<div class="col-sm-6">
											<input type="text" class="form-control" name="causa_alta" id="causa_alta" required >
										</div>
									</div>

								</div>

								<div class="form-row">

									<div class="form-group col-md-6 row">
										<label class="col-sm-4 col-form-label">Observaciones</label>
										<div class="col-sm-8">
											<textarea name="desc_alta" id="desc_alta" rows="3"></textarea>
										</div>
									</div>
									
									<div class="form-group col-md-6 row">
										<label class="col-sm-4 col-form-label">Fecha</label>
										<div class="col-sm-8">
											<input type="date" name="fecha" id="fecha" value="<?php echo date("Y-m-d");?>">
										</div>
									</div>
									

								</div>
								<hr>
								<div class="form-row">
									<div class="form-group col-md-4">
										<label>Fecha y hora de Emision</label>
										<input type="datetime" class="form-control" name="fecha_hora_emision" id="fecha_hora_emision" value="<?php echo date("Y-m-d H:i:s");?>">
									</div>
									<div class="form-group col-md-4">
										<label>Nombre Profesional </label>
										<input type="text" class="form-control" name="nombre_profesional" id="nombre_profesional" required >
									</div>
									<div class="form-group col-md-4">
										<label>Firma</label>
										<input type="text" class="form-control" name="firma" id="firma">
									</div>
									
								</div>
								
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- ./box-body -->
					<div class="box-footer">
						<div class="row">
							<button type="submit" class="btn btn-sm btn-primary ml-3 mr-2"><i class="far fa-save"></i> Guardar</button>
							<a href="{{ route('emergencia.alta', $id_paciente) }}" class="btn btn-sm btn-warning"><i class="fas fa-history"></i> Historial</a>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
			<!-- /.box -->
        </div>
		<!-- /.col -->
		
 	</div>

</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('hospital/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://unpkg.com/konva@4.2.2/konva.min.js"></script>

<script>
	CKEDITOR.config.height = 100;
	CKEDITOR.config.width = 'auto';
    CKEDITOR.replace('causa');
	CKEDITOR.replace('notificacion_policial');
	CKEDITOR.replace('via_area');
	CKEDITOR.replace('condicion_sistemas');
	CKEDITOR.replace('observacion');
	CKEDITOR.replace('clinico');
	CKEDITOR.replace('description');
	CKEDITOR.replace('desc_alta');
</script>

<script type="text/javascript">

	(function () {
		const ocultarO = document.getElementById('ocultar');
		const verbalV = document.getElementById('verbal');
		const motoraM = document.getElementById('motora');
		const onChange = (e) => {
			let total = document.getElementById('total');
			total.innerHTML = '';
			if (ocultarO.value && verbalV.value && motoraM.value)
			total.innerHTML = parseInt(ocultarO.value) + parseInt(verbalV.value) + parseInt(motoraM.value);
			
		};

		ocultarO.addEventListener('change', onChange);
		verbalV.addEventListener('change', onChange);
		motoraM.addEventListener('change', onChange);
	})();


	//DESDE AQUI COMIENZA LO DE CANVAS
	var micanvas = document.getElementById("micanvas");
	var ctx = micanvas.getContext("2d");

	var miimagen = new Image();
	miimagen.src = "{{asset('/')}}hc4/img/Anatomia.jpg";

	miimagen.onload = function() {
		ctx.drawImage(miimagen,0,0);
	}


</script>


<!-- <script type="text/javascript">
	$(".nombre").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url: "{{route('buscar_paciente')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
            }
            } );
        },
        minLength: 2,
        } );
		function completar(){
				$.ajax({
				type: 'post',
				url:"{{route('obtener_informacion')}}",
				headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
				datatype: 'json',
				data: {'nombre':$("#nombre").val()},
				success: function(data){
				// console.log(data);
				var sexo;
				var estadoc;
				var user = data[0];
				if((user.sexo)==1){
					sexo= 'HOMBRE';
				}
				else if((user.sexo)==2){
					sexo= 'MUJER';
				}
				if((user.estadoc)==1){
					estadoc='SOLTERO(A)';
				}
				else if((user.estadoc)==2){
					estadoc='CASADO(A)';
				}
				else if((user.estadoc)==3){
					estadoc='VIUDO(A)';
				}
				else if((user.estadoc)==4){
					estadoc='DIVORCIADO(A)';
				}
				else if((user.estadoc)==5){
					estadoc='UNIÓN LIBRE';
				}
				else if((user.estadoc)==6){
					estadoc='UNIÓN DE HECHO';
				}
				var hoy = new Date();
				var nacimiento= user.fecha;
				var y = hoy.getFullYear();
				//console.log(y);
				var res2= nacimiento.substr(0,4);
				//console.log(res2);
				var fe1= parseInt(y);
				var fe2= parseInt(res2);
				var edad= fe1-fe2;

				$("#cedula").val(data[0].id);
				$("#telefono1").val(data[0].telefono1);
				$("#telefono2").val(data[0].telefono2);
				$("#ciudad").val(data[0].ciudad);
				$("#nacionalidad").val(data[0].id_pais);
				$("#telefono_llamar").val(data[0].telefono_llamar);
				$("#direccion").val(data[0].direccion);
				$("#f_nacimiento").val(data[0].fecha);
				$("#lugar_nacimento").val(data[0].lugar_nacimiento);
				// $("#grupo_cultural").val(data[0].);
				$("#edad").val(edad);
				$("#sexo").val(sexo);
				$("#estado").val(estadoc);
				// $("#instruccion").val(data[0].);
				$("#ocupacion").val(data[0].ocupacion);
				// $("#empresa").val(data[0].);
				$("#seguro").val(data[0].tipo_seguro);
				
				if(data.value != 'no resultados'){
					console.log(data);
				}else{
				
				}
				},
				error: function(data){
					console.log(data);
				}
        	});
		}

</script> -->
@endsection