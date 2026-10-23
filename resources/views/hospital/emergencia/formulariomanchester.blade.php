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
	
	<div class="row">
		<!-- 1.- Registro de Primera Admisión -->
        <div class="col-md-12">
          	<div class="card card-primary">
				<div class="card-header with-border">
					<h4>{{trans('emergencia.Registrodeemergencia')}}</h4>
					<div class="card-tools pull-right col-md-1">
				        <button type="button" onclick ="location.href='{{route('hospital.emergencia')}}'" class="btn btn-danger btn-sm btn-block">{{trans('emergencia.Regresar')}}</button>
				    </div>
				</div>
				<!-- /.card-header -->
				<form id="form_manchester" method="post">
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
										<div class="form-group col-md-4">
											<label class="col-form-label-sm"><b>{{trans('emergencia.ApellidosNombres')}}</b> </label>
											<input type="text" class="form-control form-control-sm nombre" name="nombre" id="nombre" placeholder="Buscar el nombre del paciente" style="z-index:999999 !important">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.CI')}}</b> </label>
											<input type="text" class="form-control form-control-sm" id="id_paciente" name="id_paciente" onchange="buscar_cedula_paciente();" maxlength="13">
										</div>
									</div>

									<div class="form-row">
										<!--div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('Admisión')}} I.D: *</label>
											<input type="text" class="form-control form-control-sm" id="id_admision" name="id_admision">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('Nº revisión')}}: *</label>
											<input type="text" class="form-control form-control-sm" id="num_rev" name="num_rev" required>
										</div-->

									<div class="form-group col-md-3">
										<label class="col-form-label-sm"><b>{{trans('emergencia.Apellido1')}}</b> </label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm" id="apellido1" name="apellido1" required>
										</div>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>{{trans('emergencia.Apellido2')}}</b> </label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm" id="apellido2" name="apellido2" required>
										</div>
									</div>	
									<div class="form-group col-md-3">
										<label class="col-form-label-sm"><b>{{trans('emergencia.Nombre1')}}</b> </label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm" id="nombre1" name="nombre1" required>
										</div>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>{{trans('emergencia.Nombre2')}}</b> </label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm" id="nombre2" name="nombre2" required>
										</div>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>@lang('emergencia.Sexo')</b> </label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="sexo" id="sexo" required>
												<option value="1">@lang('emergencia.Hombre')</option>
												<option value="2">@lang('emergencia.Mujer')</option>
											</select>
										</div>
									</div>
									<div class="form-group col-md-8">
										<label class="col-form-label-sm"><b>{{trans('emergencia.MotivodeConsulta')}}</b> </label>
										<div class="col-sm-12">
											<input type="text" class="form-control form-control-sm" id="mot_consulta" name="mot_consulta" required>
										</div>
									</div>
								</div>
								

								<div class="form-row">
									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>{{trans('emergencia.TipodeEmergencia')}}</b> </label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="tipos_emergencia" id="tipos_emergencia" required>
												 <option value="">{{trans('emergencia.Seleccione')}}</option>
												    @foreach($tipos_emergencia as $value)
												        <option value="{{$value->id}}">{{$value->nombre}}</option>
												    @endforeach
											</select>
										</div>
									</div>
									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>{{trans('emergencia.EmbarazoPuerperio:')}}</b> </label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="embarazo_p" id="embarazo_p">
												<option>{{trans('emergencia.Noaplica')}}</option>
												<option value="0">No</option>
												<option value="1">Si</option>
											</select>
										</div>
								
									</div>

										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.PresionArterialSistolica')}}</b> </label>
											<input type="text" class="form-control form-control-sm" id="presion_art_sis" name="presion_art_sis" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.PresionArterialDiastolica')}}</b> </label>
											<input type="text" class="form-control form-control-sm" id="presion_art_dias" name="presion_art_dias" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.FrecuenciaCardiaca')}}</b> </label>
											<input type="text" class="form-control form-control-sm" name="frec_cardiaca" id="frec_cardiaca" required>
											
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.FrecuenciaRespiratoria')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="frec_resp" name="frec_resp" required>
										</div>

										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.TemperaturaC')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="temperatura" name="temperatura" required>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.Tallacm')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="talla" name="talla">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.PesoKg')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="peso" name="peso">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.RespuestaOcular')}}</b></label>
											<select class="form-control form-control-sm" id="resp_ocular" name="resp_ocular">
												<option value="">{{trans('emergencia.Seleccione')}}</option>
												@foreach($ocular as $oc)
												<option value="{{$oc->prioridad}}">{{$oc->nombre}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.RespuestaVerbal')}}</b></label>
											<select class="form-control form-control-sm" id="resp_verbal" name="resp_verbal">
												<option value="">{{trans('emergencia.Seleccione')}}</option>
												@foreach($verbal as $verb)
												<option value="{{$verb->prioridad}}">{{$verb->nombre}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.RespuestaMotora')}}</b></label>
											<select class="form-control form-control-sm" id="resp_motora" name="resp_motora">
												<option value="">{{trans('emergencia.Seleccione')}}</option>
												@foreach($motora as $mot)
												<option value="{{$mot->prioridad}}">{{$mot->nombre}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.ReaccionPupilar')}}</b></label>
										<div class="col-sm-12">
											<!--textarea type="text" class="form-control" id="reac_pupilar" name="reac_pupilar" rows="1"></textarea-->
											<!--input type="text" class="form-control form-control-sm" id="reac_pupilar" name="reac_pupilar"-->
											<select class="form-control form-control-sm" id="reac_pupilar" name="reac_pupilar">
												<option value="Si">Si</option>
												<option value="No">No</option>
											</select>
										</div>
										</div>
										<div class="form-group col-md-2">
										 <label class="col-form-label-sm"><b>{{trans('emergencia.TotalLlenadoCapilar')}}</b> </label>
											<input type="text" class="form-control form-control-sm" id="total_capilar" name="total_capilar">	
										</div>
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm"><b>{{trans('emergencia.SaturaciondeOxigeno')}}</b></label>
											<input type="text" class="form-control form-control-sm" id="satura_oxigeno" name="satura_oxigeno">	
										</div>
										<div class="form-group col-md-2">
										    <label class="col-form-label-sm"><b>{{trans('emergencia.EstadodeConciencia')}}</b></label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="est_conciencia" id="est_conciencia">
												<option>{{trans('emergencia.Seleccione')}}</option>
												<option  value="1">{{trans('Consciente')}}</option>
												<option  value="0">{{trans('Inconsciente')}}</option>
											
											</select>
										</div>
									</div>

									<div class="form-group col-md-2">
										<label class="col-form-label-sm"><b>{{trans('emergencia.Prioridad')}}</b></label>
										<div class="col-sm-12">
											<select class="form-control form-control-sm" name="prioridad" id="prioridad" required>
												<option>{{trans('emergencia.Seleccione')}}</option>
												@foreach($prioridad as $p)
													<option style="background-color: {{$p->color}}; color: black; font-weight:bold" value="{{$p->id}}">{{$p->nombre}}</option>
												@endforeach
											
											</select>
										</div>
									</div>	
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- ./card-body -->
					<div class="card-footer" style="text-align: center">
						<button type="button" onclick="guardar_manchester();" class="btn btn-sm btn-primary ml-3 mr-2"> <i class="far fa-save"></i> {{trans('Guardar')}}</button>
						<!--div class="row">
							<label class="col-sm-4 col-form-label">ACTUALIZAR LOS SIGNOS VITALES DEL PACIENTE</label> 
						</div-->
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

	
	$("#nombre").autocomplete({

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
              //console.log("hola");
            }
          } );
      },
      change:function(event, data){
          $('#id_paciente').val(data.item.id);
          if(data.item.sexo == 2){
        		$('#div_embarazo').show();
        	}else{
        		$('#div_embarazo').hide();
        	}
      },
      select: function( event, data ) {
      	$('#id_paciente').val(data.item.id);
          if(data.item.sexo == 2){
        		$('#div_embarazo').show();
        	}else{
        		$('#div_embarazo').hide();
        	}
        	buscar_cedula_paciente();
      },
      selectFirst: true,
      minLength: 3,
  } );	

	function buscar_cedula_paciente(){
		var id_paciente = document.getElementById('id_paciente').value;
      $.ajax({
        type: 'get',
        url: "{{ url('hospital/emergencia/buscar')}}/"+id_paciente, 

        success: function(data){
        	if(data=='no'){
        		$('#nombre').val('');
        	}else{
        		//console.log(data);
        		$('#nombre').val(data.apellido1+' '+data.apellido2+' '+data.nombre1+' '+data.nombre2);
        		$('#apellido1').val(data.apellido1);
        		$('#apellido2').val(data.apellido2);
        		$('#nombre1').val(data.nombre1);
        		$('#nombre2').val(data.nombre2);
        		$('#sexo option[value='+data.sexo+']').attr('selected','selected');

        		if(data.sexo == 2){
        			$('#div_embarazo').show();
        		}else{
        			$('#div_embarazo').hide();
        		}
        	}
        }
      });
	}


	function guardar_manchester(){
		var msnerror = '';
		if( $('#id_paciente').val() == ''){
			msnerror = msnerror + 'Ingrese la cedula del paciente\n';
		};
		if( $('#apellido1').val() == ''){
			msnerror = msnerror + 'Ingrese el primer apellido\n';
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
		if( $('#mot_consulta').val() == ''){
			msnerror = msnerror + 'Ingrese el motivo de la consulta\n';
		};
		if( $('#tipos_emergencia').val() == ''){
			msnerror = msnerror + 'Ingrese el tipo de emergencia\n';
		};
		if( $('#embarazo_p').val() == ''){
			msnerror = msnerror + 'Ingrese estado embarazo\n';
		};
		if( $('#presion_art_sis').val() == ''){
			msnerror = msnerror + 'Ingrese la presion\n';
		};
		if( $('#presion_art_dias').val() == ''){
			msnerror = msnerror + 'Ingrese la presion\n';
		};
		if( $('#frec_cardiaca').val() == ''){
			msnerror = msnerror + 'Ingrese la frecuencia\n';
		};
		if( $('#frec_resp').val() == ''){
			msnerror = msnerror + 'Ingrese la frecuencia\n';
		};
		if( $('#temperatura').val() == ''){
			msnerror = msnerror + 'Ingrese la temperatura\n';
		};
		if( $('#peso').val() == ''){
			msnerror = msnerror + 'Ingrese el peso\n';
		};
		if( $('#resp_ocular').val() == ''){
			msnerror = msnerror + 'Ingrese la respuesta ocular\n';
		};
		if( $('#resp_verbal').val() == ''){
			msnerror = msnerror + 'Ingrese la respuesta verbal\n';
		};
		if( $('#resp_motora').val() == ''){
			msnerror = msnerror + 'Ingrese la respuesta motora\n';
		};
		if( $('#reac_pupilar').val() == ''){
			msnerror = msnerror + 'Ingrese la reaccion pupilar\n';
		};
		if( $('#total_capilar').val() == ''){
			msnerror = msnerror + 'Ingrese el total capilar\n';
		};
		if( $('#satura_oxigeno').val() == ''){
			msnerror = msnerror + 'Ingrese la saturacion de oxigeno\n';
		};
		if( $('#est_conciencia').val() == ''){
			msnerror = msnerror + 'Ingrese el estado de conciencia\n';
		};
		if( $('#prioridad').val() == ''){
			msnerror = msnerror + 'Ingrese la prioridad<br>';
		};

		if(msnerror != ''){
			alert(msnerror);
		}else{

			$.ajax({
	      type: 'post',
	      url:"{{ route('manchester.guardar') }}",
	      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	      datatype: 'json',
	      data: $("#form_manchester").serialize(),
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
</script>

@endsection