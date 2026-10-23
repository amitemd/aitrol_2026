@extends('layouts.app-template-h')
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
				   {{trans('emergencia.INGRESOPOREMERGENCIANUEVOINGRESO')}}
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick ="location.href='{{route('hospital.emergencia')}}'" class="btn btn-primary btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i> {{trans('emergencia.Regresar')}}</button>
            </div>
        </div>
    </section>

	<div class="row">
		<!-- 1.- Registro de Primera Admisión -->
        <div class="col-md-12">
          	<div class="card card-primary">
				<div class="card-header with-border">
					<h3 class="card-title">{{trans('emergencia.2RegistrodePrimeraAdmision')}}</h3>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.card-header -->
				<form method="post" id="form_emergencia">
					{{ csrf_field() }}
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
									@if(session('message'))
									<div class="alert alert-warning alert-dismissible fade show" role="alert">
										{{session('message')}}
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									@endif
								
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.CeduladeCiudadania')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="cedula" name="cedula" @if(!is_null($ho_solicitud)) value="{{$ho_solicitud->id_paciente}}" @endif required onchange="buscapaciente();">
											<input type="hidden" name="idsolicitud" id="idsolicitud" value="{{$id_solicitud}}" class="form-control" >
										</div>
										<div class="form-group col-md-8">
											<label class="col-form-label-sm"><b>{{trans('emergencia.ApellidosyNombres')}}</b></label>
											<input type="text" class="form-control form-control-sm nombre" name="apellidos_nombres" id="apellidos_nombres" placeholder="Buscar Por Apellidos y Nombres" style="z-index:999999 !important">
										</div>
										<!--div class="form-group-2">
											<br>
											<button class="btn btn-primary btn-sm">Buscar</button>
										</div-->
										
										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Apellido1')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="apellido1" id="apellido1" autocomplete="off" style="z-index:999999 !important">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Apellido2')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="apellido2" id="apellido2" autocomplete="off" style="z-index:999999 !important">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Nombre1')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="nombre1" id="nombre1" autocomplete="off" style="z-index:999999 !important">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Nombre2')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="nombre2" id="nombre2" autocomplete="off" style="z-index:999999 !important">
										</div>
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Ciudad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad" autocomplete="off">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Telefono')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono1" name="telefono1" autocomplete="off">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Celular')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono2" name="telefono2" autocomplete="off">
										</div>
										<div class="form-group col-md-6">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Direccionderesidencia')}}</b></label>
											<input type="text" class="form-control form-control-sm" name="direccion" id="direccion" autocomplete="off">
										</div>
									</div>
									<div class="form-row">
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Barrio')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="barrio" name="barrio" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Parroquia')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="parroquia" name="parroquia" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Canton')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="canton" name="canton" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Provincia')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="provincia" name="provincia" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.ZonaUR')}}</b></label>
											<select class="form-control form-control-sm" id="zona" name="zona">
												<option value="Urbana">{{trans('emergencia.Urbana')}}</option>
												<option value="Rural">{{trans('emergencia.Rural')}}</option>
											</select>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Fechadenacimiento')}}</b></label>
											<!--input type="text" class="form-control form-control-sm" id="f_nacimiento" name="f_nacimiento"-->	
											<div class="input-group date">
								              <input type="text"  data-input="true" class="form-control input-xs flatpickr-basic active" name="f_nacimiento" id="f_nacimiento" value="{{$fecha}}" autocomplete="off">
								              <div class="input-group-addon">
								                <i class="glyphicon glyphicon-remove-circle"></i>
								              </div>   
								            </div>
										</div>

									</div>
									<div class="form-row">
										
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Nacionalidad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="nacionalidad" name="nacionalidad">	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm"><b>{{trans('emergencia.GrCultural')}}</b></label>
											<select class="form-control form-control-sm" id="grupo_cultural" name="grupo_cultural">
												<option value="Mestizo">{{trans('emergencia.Mestizo')}}</option>
												<option value="Morisco">{{trans('emergencia.Morisco')}}</option>
												<option value="Cholo">{{trans('emergencia.Cholocoyote')}}</option>
												<option value="Mulatos">{{trans('emergencia.Mulatos')}}</option>
												<option value="Zambo">{{trans('emergencia.Zambo')}}</option>
												<option value="Castizo">{{trans('emergencia.Castizo')}}</option>
												<option value="Criollo">{{trans('emergencia.Criollo')}}</option>
												<option value="Chino">{{trans('emergencia.Chino')}}</option>
											</select>
										</div>
										<!--div class="form-group col-md-1">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Edad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="edad" name="edad">	
										</div-->
										<div class="form-group col-md-1">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Sexo')}}</b></label>
											
											<select class="form-control form-control-sm" id="sexo" name="sexo">
												<option value="1">{{trans('emergencia.Hombre')}}</option>
												<option value="2">{{trans('emergencia.Mujer')}}</option>
											</select>
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm"><b>{{trans('emergencia.EstadoCivil')}}</b></label>
											<select class="form-control form-control-sm" id="estado" name="estado">
												<option value="1"> {{trans('emergencia.Soltero')}}</option>
												<option value="2"> {{trans('emergencia.Casado')}}</option>
												<option value="3"> {{trans('emergencia.Viudo')}}</option>
												<option value="4"> {{trans('emergencia.Divorciado')}}</option>
												<option value="5"> {{trans('emergencia.UnionLibre')}}</option>
												<option value="6"> {{trans('emergencia.UniondeHecho')}}</option>
											</select>	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Instruccion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="instruccion" name="instruccion">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Ocupacion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="ocupacion" name="ocupacion">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Empresadondetrabaja')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="empresa" name="empresa">	
										</div>
									</div>
									<div class="form-row">
										<!--div class="form-group col-md-2">
											<label class="col-form-label-sm">Fecha de Admisión</label>
											<input type="date" class="form-control form-control-sm" id="f_admision" name="f_admision" value="<?php echo date("Y-m-d");?>">	
										</div-->
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.TipodeSeguro')}}</b></label>
											<select class="form-control form-control-sm" id="id_seguro" name="id_seguro">
												@foreach($seguros as $seg)
												<option value="{{$seg->id}}">{{$seg->nombre}}</option>
												@endforeach
											</select>
										</div>
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Referidode')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="referido" name="referido">	
										</div>
										<div class="form-group col-md-4">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Encasonecesariollamara')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="llamar_a" name="llamar_a">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.ParentescoAfinidad')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="parentesco" name="parentesco">
										</div>
										
									</div>
									
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.NTelefono')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="telefono_inst_per_paci" name="telefono_inst_per_paci">
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.DireccionFamiliar')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="direccion_familiar" name="direccion_familiar">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.FormadeLlegada')}}</b></label>
											<select name="forma_llegada" id="forma_llegada" class="form-control form-control-sm">
												<option value="Ambulatorio">{{trans('emergencia.Ambulatorio')}}</option>
												<option value="Ambulancia">{{trans('emergencia.Ambulancia')}}</option>
												<option value="Otro">{{trans('emergencia.OtroTransporte')}}</option>
											</select>
										</div>

										<div class="form-group col-md-3">
											<label class="col-form-label-sm"><b>{{trans('emergencia.FuentedeInformacion')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="fuente_informacion" name="fuente_informacion">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.NTelefono')}}</b></label>
											<input type="number" class="form-control form-control-sm" id="telefono_llamar" name="telefono_llamar">
										</div>

										<!--div class="form-group col-md-3">
											<label class="col-form-label-sm">Admisionista</label>
											<input type="text" class="form-control form-control-sm" id="admisionista" name="admisionista">
										</div-->
									</div>
									
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- ./card-body -->
					<div class="card-footer">
						<div class="row">
							<button type="button" onclick="guardar();" class="btn btn-primary ml-3 mr-2"><i class="far fa-save"></i>{{trans('emergencia.Guardar')}}</button>
						</div>
						<!-- /.row -->
					</div>
					<!-- /.card-footer -->
				</form>
			</div>
			<!-- /.card -->
        </div>
		<!-- /.col -->
		
 	</div>

</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">

	window.onload = ()=>{
		var cedula = document.getElementById('cedula').value;
		if(!isNaN(cedula)){
			buscapaciente();
		}
	}

	

	function guardar(){
		var msnerror = '';
		if( $('#cedula').val() == ''){
			msnerror = msnerror + 'Ingrese la cedula\n';
		};
		if( $('#apellido1').val() == ''){
			msnerror = msnerror + 'Ingrese el apellido\n';
		};
		if( $('#apellido2').val() == ''){
			msnerror = msnerror + 'Ingrese el segundo apellido\n';
		};
		if( $('#nombre1').val() == ''){
			msnerror = msnerror + 'Ingrese el primer nombre\n';
		};
		if( $('#nombre2').val() == ''){
			msnerror = msnerror + 'Ingrese el segundo nombre\n';
		};
		if( $('#ciudad').val() == ''){
			msnerror = msnerror + 'Ingrese la ciudad\n';
		};
		if( $('#telefono1').val() == ''){
			msnerror = msnerror + 'Ingrese el telefono\n';
		};
		if( $('#telefono2').val() == ''){
			msnerror = msnerror + 'Ingrese el telefono\n';
		};
		if( $('#direccion').val() == ''){
			msnerror = msnerror + 'Ingrese la direccion\n';
		};
		if( $('#barrio').val() == ''){
			msnerror = msnerror + 'Ingrese el barrio\n';
		};
		if( $('#parroquia').val() == ''){
			msnerror = msnerror + 'Ingrese la parroquia\n';
		};
		if( $('#canton').val() == ''){
			msnerror = msnerror + 'Ingrese el canton\n';
		};
		if( $('#provincia').val() == ''){
			msnerror = msnerror + 'Ingrese la provincia\n';
		};
		if( $('#zona').val() == ''){
			msnerror = msnerror + 'Ingrese la zona\n';
		};
		if( $('#f_nacimiento').val() == ''){
			msnerror = msnerror + 'Ingrese la fecha de nacimiento\n';
		};
		if( $('#nacionalidad').val() == ''){
			msnerror = msnerror + 'Ingrese la nacionalidad\n';
		};
		if( $('#grupo_cultural').val() == ''){
			msnerror = msnerror + 'Ingrese el grupo cultural\n';
		};
		if( $('#sexo').val() == ''){
			msnerror = msnerror + 'Ingrese el sexo\n';
		};
		if( $('#estado').val() == ''){
			msnerror = msnerror + 'Ingrese el estado\n';
		};
		if( $('#instruccion').val() == ''){
			msnerror = msnerror + 'Ingrese la instruccion\n';
		};
		if( $('#ocupacion').val() == ''){
			msnerror = msnerror + 'Ingrese la ocupación\n';
		};
		if( $('#empresa').val() == ''){
			msnerror = msnerror + 'Ingrese la empresa\n';
		};
		if( $('#id_seguro').val() == ''){
			msnerror = msnerror + 'Ingrese el seguro\n';
		};
		if( $('#referido').val() == ''){
			msnerror = msnerror + 'Ingrese el referido\n';
		};
		if( $('#llamar_a').val() == ''){
			msnerror = msnerror + 'Ingrese a quien llamar\n';
		};
		if( $('#parentesco').val() == ''){
			msnerror = msnerror + 'Ingrese el parentesco\n';
		};
		if( $('#telefono_inst_per_paci').val() == ''){
			msnerror = msnerror + 'Ingrese el telefono Familiar\n';
		};
		if( $('#direccion_familiar').val() == ''){
			msnerror = msnerror + 'Ingrese la dirección familiar\n';
		};
		if( $('#fuente_informacion').val() == ''){
			msnerror = msnerror + 'Ingrese la fuente de información\n';
		};
		if( $('#telefono_llamar').val() == ''){
			msnerror = msnerror + 'Ingrese el telefono a llamar\n';
		};

		if(msnerror != ''){
			alert(msnerror);
		}else{	
			$.ajax({
		      type: 'post',
		      url:"{{ route('emergencia.guardar') }}",
		      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		      datatype: 'json',
		      data: $("#form_emergencia").serialize(),
		      success: function(data){
		          console.log(data);
		          location.href="{{route('hospital.emergencia')}}";
		      },
		      error: function(data){
		          console.log(data);
		      }
			});
		}	
	}

        $("#apellidos_nombres").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                  type: 'get',
                  url: "{{route('buscar_paciente')}}",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response(data);
                    console.log("hola");
                  }
                } );
            },
            change:function(event, data){
                
               
				console.log(data.item);
                $('#nombre1').val(data.item.nombre1);
                $('#nombre2').val(data.item.nombre2);
                $('#apellido1').val(data.item.apellido1);
                $('#apellido2').val(data.item.apellido2);
                $('#sexo').val(data.item.sexo);
                $('#f_nacimiento').val(data.item.fecha_nacimiento);
                $('#ciudad').val(data.item.ciudad);
                $('#telefono1').val(data.item.telefono1);
                $('#telefono2').val(data.item.telefono2);
                $('#direccion').val(data.item.direccion);
                $('#estado').val(data.item.estadocivil);
                $('#ocupacion').val(data.item.ocupacion);
                $('#barrio').val(data.item.barrio);
                $('#parroquia').val(data.item.parroquia);
                $('#canton').val(data.item.canton);
                $('#provincia').val(data.item.provincia);
                $('#nacionalidad').val(data.item.nacionalidad);
                $('#edad').val(data.item.edad);
                $('#direccion_familiar').val(data.item.direccion_familiar);
                $('#forma_llegada').val(data.item.forma_llegada);
                $('#fuente_informacion').val(data.item.fuente_informacion);
                $('#empresa').val(data.item.empresa_trabajo);
                $('#instruccion').val(data.item.instruccion);
                $('#referido').val(data.item.referido);
                $('#telefono_inst_per_paci').val(data.item.telefono_inst_per_paci);
                $('#telefono_llamar').val(data.item.telefono3);
                $('#parentesco').val(data.item.parentesco);
                $('#llamar_a').val(data.item.llamar_a);
                $('#cedula').val(data.item.id);
            },
            selectFirst: true,
            minLength: 3,
        } );




	function buscapaciente(){
        var id_paciente = document.getElementById('cedula').value;
        $.ajax({
            type: 'get',
            url: "{{ url('hospital/emergencia/buscar')}}/"+id_paciente, //hospitalizados.buscapaciente

            success: function(data){
                if(data=='no'){
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                    $('#sexo').val('');
                    $('#f_nacimiento').val('1980/01/01');
                    $('#ciudad').val('');
                    $('#telefono1').val('');
                    $('#telefono2').val('');
                    $('#direccion').val('');
                    $('#estado').val('');
                    $('#ocupacion').val('');
                    $('#barrio').val('');
                    $('#parroquia').val('');
                    $('#canton').val('');
                    $('#provincia').val('');
                    $('#nacionalidad').val('');
                    $('#edad').val(data.edad);
                    $('#direccion_familiar').val('');
                    $('#forma_llegada').val('');
                    $('#fuente_informacion').val('');
                    $('#empresa').val('');
                    $('#instruccion').val('');
                    $('#referido').val('');
                    $('#telefono_inst_per_paci').val('');
                    $('#telefono_llamar').val('');
                    $('#parentesco').val('');
                    $('#apellidos_nombres').val('');
                    $('#llamar_a').val('');

                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);

                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#f_nacimiento').val(data.fecha_nacimiento);
                    $('#ciudad').val(data.ciudad);
                    $('#telefono1').val(data.telefono1);
                    $('#telefono2').val(data.telefono2);
                    $('#direccion').val(data.direccion);
                    $('#estado').val(data.estadocivil);
                    $('#ocupacion').val(data.ocupacion);
                    $('#barrio').val(data.barrio);
                    $('#parroquia').val(data.parroquia);
                    $('#canton').val(data.canton);
                    $('#provincia').val(data.provincia);
                    $('#nacionalidad').val(data.nacionalidad);
                    $('#edad').val(data.edad);
                    $('#direccion_familiar').val(data.direccion_familiar);
                    $('#forma_llegada').val(data.forma_llegada);
                    $('#fuente_informacion').val(data.fuente_informacion);
                    $('#empresa').val(data.empresa_trabajo);
                    $('#instruccion').val(data.instruccion);
                    $('#referido').val(data.referido);
                    $('#telefono_inst_per_paci').val(data.telefono_inst_per_paci);
                    $('#telefono_llamar').val(data.telefono3);
                    $('#parentesco').val(data.parentesco_afinidad);
                    $('#apellidos_nombres').val(data.apellido1+' '+data.apellido2+' '+data.nombre1+' '+data.nombre2);
                    if(data.apellido1familiar!=null){
                    	 $('#llamar_a').val(data.apellido1familiar+' '+data.apellido2familiar+' '+data.nombre1familiar+' '+data.nombre2familiar);
                    }
                   

                }
            }
        });
    }

</script>
@endsection