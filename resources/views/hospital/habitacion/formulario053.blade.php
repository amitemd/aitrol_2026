@extends('hospital.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
 @php
    foreach($log as $value){
    $paciente= $value->paciente;
    }
 @endphp
<div class="content">

	<section class="content-header">
        <div class="row">
            <div class="col-md-11 col-sm-10">
                <h3>
				{{trans('hospitalizacion.FORMULARIO053')}}
                    
                </h3>
            </div>
            <div class="col-1">
            	<button type="button" class="btn btn-danger btn-sm btn-block" onclick="location.href='{{ URL::previous() }}'"><i class="far fa-arrow-alt-circle-left"></i>{{trans('hospitalizacion.Regresar')}}</button>
            
            </div>
        </div>
    </section>

	<div class="row">

        <div class="col-md-12">
          	<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">{{trans('hospitalizacion.1.-DatosdelPaciente')}}</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
					<div class="box-body">
						<div class="row">
						@foreach($datos as $value)
							<div class="col-md-12">
								<fieldset disabled>
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Instituci&oacute;ndelSistema')}}</label>
											<input type="text" class="form-control form-control-sm nombre" name="institucion" id="institucion" value="IECED" >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.UnidadOperativa')}}</label>
											<input type="text" class="form-control form-control-sm" id="unidad" name="unidad" >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.CODU.O')}}</label>
											<input type="text" class="form-control form-control-sm" id="cod" name="cod">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Cant&oacute;n')}}</label>
											<input type="text" class="form-control form-control-sm" id="canton" name="canton"  value="{{ $value->canto}}"  >
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Ciudad')}}</label>
											<input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad" value="{{ $value->ciudad}}" >
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Parroquia')}}</label>
											<input type="text" class="form-control form-control-sm" id="parroquia" name="parroquia"  value="{{ $value->parroquia}}" >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Ndehistoriaclinica')}}</label>
											<input type="text" class="form-control form-control-sm" id="nhistoria_clinica" name="nhistoria_clinica" value="{{ $value->historia_clinica}}">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">{{trans('hospitalizacion.ApellidoPaterno')}}</label>
											<input type="text" class="form-control form-control-sm" name="apellido1" id="apellido1" value="{{ $value->apellido1}}" >
											
										</div>
										
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.ApellidoMaterno')}}</label>
											<input type="text" class="form-control form-control-sm" id="apellido2" name="apellido2" value="{{ $value->apellido2}}"  >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.PrimerNombre')}}</label>
											<input type="text" class="form-control form-control-sm" id="nombre1" name="nombre1" value="{{ $value->nombre1}}" >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.SegundoNombre')}}</label>
											<input type="text" class="form-control form-control-sm" id="nombre2" name="nombre2" value="{{ $value->nombre2}}" >
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Ceduladeciudadania')}}</label>
											<input type="text" class="form-control form-control-sm" id="cedula" name="cedula" value="{{ $value->id}}" >
										</div>
										
									</div>
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.FechadeReferencia')}}</label>
											<input type="text" class="form-control form-control-sm" id="fecha_referencia" name="fecha_referencia">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Hora')}}</label>
											<input type="text" class="form-control form-control-sm" id="hora" name="hora"  value="{{ $value->created_at}}">
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Edad')}}</label>
											<input type="text" class="form-control form-control-sm" id="edad" name="edad" value="{{ $value->edad}}" >	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Genero')}}</label>
											<input type="text" class="form-control form-control-sm" id="genero" name="genero"  value="@if(($value->sexo)==1) MASCULINO @elseif(($value->sexo)==2) FEMENINO @endif">
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.EstadoCivil')}}</label>
											<input type="text" class="form-control form-control-sm" id="estado_civil" name="estado_civil" value="@if(($value->estadocivil)==1) SOLTERO/A @elseif(($value->estadocivil)==2) CASADO/A @elseif(($value->estadocivil)==3) VIUDO/A 
											@elseif(($value->estadocivil)==4) DIVORCIADO/A
											@elseif(($value->estadocivil)==4) UNION LIBRE @endif">	
										</div>
										<div class="form-group col-md-1">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Instruci&oacute;n')}}</label>
											<input type="text" class="form-control form-control-sm" id="instruccion" name="instruccion" >	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Empresadondelabora')}}</label>
											<input type="text" class="form-control form-control-sm" id="empresa" name="empresa" value="{{ $value->trabajo }}">	
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Segurodesalud')}}</label>
											<input type="text" class="form-control form-control-sm" id="seguro" name="seguro" value="@if(($value->id_seguro)==1) PARTICULAR @elseif(($value->id_seguro)==2) IESS @elseif(($value->id_seguro)==3) ISSFA 
											@elseif(($value->id_seguro)==4) HUMANA
											@elseif(($value->id_seguro)==5) MSP
											@elseif(($value->id_seguro)==6) ISSPOL
											@elseif(($value->id_seguro)==7) SALUD
											@elseif(($value->id_seguro)==8) BMI
											@elseif(($value->id_seguro)==9) MEDEC
											@elseif(($value->id_seguro)==10) ECUASANITAS
											@elseif(($value->id_seguro)==11) PLAN VITAE
											@elseif(($value->id_seguro)==12) MEDIKEN
											@elseif(($value->id_seguro)==13) EHG
											@elseif(($value->id_seguro)==14) BUPA
											@elseif(($value->id_seguro)==15) BEST DOCTOR
											@elseif(($value->id_seguro)==16) ASIKSEN
											@elseif(($value->id_seguro)==17) METRORED
											@elseif(($value->id_seguro)==18) TRASMEDICAL
											@elseif(($value->id_seguro)==19) VUMI
											@elseif(($value->id_seguro)==20) IESS DCTO PART @endif">	
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-6">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Establecimiento')}}</label>
											<input type="text" class="form-control form-control-sm" id="establecimiento" name="establecimiento" >	
										</div>
										<div class="form-group col-md-6">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Servicio')}}</label>
											<input type="text" class="form-control form-control-sm" id="servicio" name="servicio" >
										</div>
									</div>
									
									
								</fieldset>
							</div>
							
						</div>
						@endforeach
					</div>
					</div>
			</div>
        </div>

	    <div class="col-md-12">
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">{{trans('hospitalizacion.2.Motivodereferencia:')}}</h3>
	            <div class="box-tools pull-right">
	              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	            </div>
	          </div>
	            <div class="col-md-12">
	          @if(Session('referencia'))
	            <div class="alert alert-success">
	              {{session('referencia')}}
	               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	               </button>
	             </div>
	          @endif
	        </div>
	          <div class="box-body">
	            <div class="row">
	              <div class="col-md-12">
	                <form action="{{route('hospital.formulario053_referencia')}}" method="POST" id="formulario">
               			{{ csrf_field() }}
               			<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
	                	<div class="form-row">
	                		<div class="form-group col-md-12 row">
								<div class="col-sm-12">
									<textarea class="form-control" name="cuadro_diagnostico" id="cuadro_diagnostico" rows="3"></textarea>
								</div>
							</div>
	                	</div>
	                	<div class="btn-der">
				     	<button type="submit"  class="btn btn-primary"><i class="far fa-save"></i>{{trans('hospitalizacion.Guardar')}}</button>
				     </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	    </div>


	    <div class="col-md-12">
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">{{trans('hospitalizacion.3.Resumendelcuadroclinico:')}}</h3>
	            <div class="box-tools pull-right">
	              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	            </div>
	          </div>
	           <div class="col-md-12">
	          @if(Session('success'))
	            <div class="alert alert-success">
	              {{session('success')}}
	               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	               </button>
	             </div>
	          @endif
	        </div>
	          <div class="box-body">
	            <div class="row">
	              <div class="col-md-12">
	                <form action="{{route('hospital.guardar_informacion')}}" method="POST" id="formulario">
               			{{ csrf_field() }}
               			<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
	                	<div class="form-row">
	                		<div class="form-group col-md-12 row">
								<div class="col-sm-12">
									<textarea class="form-control" name="cuadro_clinico" id="cuadro_clinico" rows="3"></textarea>
								</div>
							</div>
	                	</div>
	                  <div class="btn-der">
				     	<button type="submit"  class="btn btn-primary"><i class="far fa-save"></i>{{trans('hospitalizacion.Guardar')}}</button>
				     	<button type="button" class="btn btn-info" 
                          onclick="location.href='{{route('hospital.formulario053_resultado',['id_cama'=>$id_cama,'id'=>$paciente->id])}}'"><i class="fas fa-book"></i>{{trans('hospitalizacion.Resultados')}} 
                        </button>
				     </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	    </div>

	    <div class="col-md-12">
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">{{trans('hospitalizacion.4.Hallazgorelevantesdeexamenesyprocedimientosdiagnosticos:')}}</h3>
	            <div class="box-tools pull-right">
	              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	            </div>
	          </div>
	          <div class="col-md-12">
	          @if(Session('dato'))
	            <div class="alert alert-success">
	              {{session('dato')}}
	               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	               </button>
	             </div>
	          @endif
	        </div>
	          <div class="box-body">
	            <div class="row">
	              <div class="col-md-12">
	                <form action="{{route('hospital.formulario053_hallazgos')}}" method="POST" id="formulario">
               			{{ csrf_field() }}
               			<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
	                	<div class="form-row">
	                		<div class="form-group col-md-12 row">
								<div class="col-sm-12">
									<textarea class="form-control" name="cuadro_procedimiento" id="cuadro_procedimiento" rows="3"></textarea>
								</div>
							</div>
	                	</div>
	                	<div class="btn-der">
				     	<button type="submit"  class="btn btn-primary"><i class="far fa-save"></i>{{trans('hospitalizacion.Guardar')}}</button>
				     </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	    </div>

	   <div class="col-md-12">
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">{{trans('hospitalizacion.4.Diagnostico:')}}</h3>
	            <small id="emailHelp" class="form-text text-muted">{{trans('hospitalizacion.(PRE=PRESUNTIVODEF=DEFINITIVO)')}}</small>
	            <div class="box-tools pull-right">
	              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	            </div>
	          </div>
	          <div class="col-md-12">
	          @if(Session('ok'))
	            <div class="alert alert-success">
	              {{session('ok')}}
	               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	               </button>
	             </div>
	          @endif
	        </div>
	          <div class="box-body">
	            <div class="row">
	              <div class="col-md-12">
	                <form action="{{route('hospital.formulario053_diagnostico')}}" method="POST" id="formulario">
               			{{ csrf_field() }}
               			<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
	                	<table id="agregar" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
					  <thead>
					    <tr>
					    <th width="60%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"></th>
					    <th width="10%" class="" tabindex="0"  aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.cie')}}</th>
					    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Pre')}}</th>
					    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Def')}}</th>
					     <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Acci&oacute;n')}}</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr class="fila-fija">
                        <td style="text-align:center;"><input class="form-control" name="cuadro_procedimiento[]"  rows="3"></input></td>
                        <td style="text-align:center;"><input id="buscarnombre"  required  name="cie[]" placeholder="Cie"/></td>
                        <td style="text-align:center;"><input required  name="pre[]" placeholder="Pre"/></td>
                        <td style="text-align:center;"><input required  name="def[]" placeholder="Def"/></td>
                        <td style="text-align:center;" class="quitar"><button type="button" class="btn btn-danger" ><i class="fas fa-minus"></i>{{trans('hospitalizacion.Eliminar')}}</button></td>        
					    </tr>
					  </tbody>
				     </table>
				     <div class="btn-der">
				     	<button type="submit"  class="btn btn-primary"><i class="far fa-save"></i>{{trans('hospitalizacion.Guardar')}}</button>
				     	 <button id="adicional" name="adicional" type="button" class="btn btn-success agregar_td"><i class="fas fa-plus"></i>{{trans('hospitalizacion.Mas')}}</button>
				     </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	   </div>

	   <div class="col-md-12">
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">{{trans('hospitalizacion.5.PlanTratamientoRealizado:')}}</h3>
	            <div class="box-tools pull-right">
	              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	            </div>
	          </div>
	          <div class="col-md-12">
	          @if(Session('guardado'))
	            <div class="alert alert-success">
	              {{session('guardado')}}
	               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	               </button>
	             </div>
	          @endif
	        </div>
	          <div class="box-body">
	            <div class="row">
	              <div class="col-md-12">
	                <form action="{{route('hospital.formulario053_tratamiento')}}" method="POST" id="formulario">
               			{{ csrf_field() }}
               			<input type="hidden" class="form-control" value="{{$id_paciente}}" id="id_paciente" name="id_paciente">
	                	<div class="form-row">
	                		<div class="form-group col-md-12 row">
								<div class="col-sm-12">
									<textarea class="form-control" name="cuadro_tratamiento" id="cuadro_tratamiento" rows="3"></textarea>
								</div>
							</div>
	                	</div>
	                	<div class="btn-der">
				     	<button type="submit"  class="btn btn-primary"><i class="far fa-save"></i>{{trans('hospitalizacion.Guardar')}}</button>
				     </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	   </div>
       
       <div class="col-md-12">
          	<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">{{trans('hospitalizacion.6.-DatosGenerales')}}</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<!-- /.box-header -->
				<form>
					{{ csrf_field() }}
					<div class="box-body">
						<div class="row">
							
							<div class="col-md-12">
								<fieldset disabled>
									<div class="form-row">
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Sala')}}</label>
											<input type="text" class="form-control form-control-sm nombre" name="nombre" id="nombre" >
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Cama')}}</label>
											<input type="text" class="form-control form-control-sm" id="cedula" name="cedula" value="<?php echo $id_cama ?>" >
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Medico')}}</label>
											<input type="text" class="form-control form-control-sm" id="ciudad" name="ciudad">
										</div>
										<div class="form-group col-md-2">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Codigo')}}</label>
											<input type="text" class="form-control form-control-sm" id="telefono1" name="telefono1" >
										</div>
										<div class="form-group col-md-3">
											<label class="col-form-label-sm">{{trans('hospitalizacion.Firma')}}</label>
											<input type="text" class="form-control form-control-sm" id="telefono1" name="telefono1" >
										</div>
									</di>
								</fieldset>
							</div>
						</div>
					</div>
				</form>
			</div>
        </div>
 	</div>

</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
  $('.agregar_td').on('click',function(){
    agregar_td();
  });
  function agregar_td()
  {
    var tr='<tr>'+'<td style="text-align:center;"><input class="form-control" name="cuadro_procedimiento[]" rows="3"></input></td>'+'<td style="text-align:center;"><input  name="cie[]"  required placeholder="Cie"/></td>'+'<td style="text-align:center;"><input  name="pre[]" placeholder="Pre"/></td>'+'<td style="text-align:center;"><input  name="def[]" placeholder="Def"/></td>'+'<td style="text-align:center;" class="quitar"><button type="button" class="btn btn-danger"><i class="fas fa-minus"></i> Eliminar</button></td>'  
      '</tr>'+
    $('#agregar').append(tr);
  };
  $(document).on("click",".quitar",function(){
          var parent = $(this).parents().get(0);
          $(parent).remove();
        });


  $("#buscarnombre").autocomplete({
  source: function( request, response ){
    $.ajax({
      method:'GET',
      url: "{{route('hospital.formulario053_autucompletar')}}",
      dataType: "json",
      data: { term: request.term },
      success: function( data ) {
      response(data);
      }
    });
  },
  minLength: 2,
  change: function( event, ui ){
  }
});
</script>
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
@endsection
