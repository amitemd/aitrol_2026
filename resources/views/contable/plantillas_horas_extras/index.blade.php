@extends('contable.plantillas_horas_extras.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
    	<ol class="breadcrumb">
	        <li class="breadcrumb-item"><a href="#">Contable</a></li>
	        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
	        <li class="breadcrumb-item active" aria-current="page">Plantillas Horas Extras</li>
    	</ol>
    </nav>
    <div class="box">
      	<div class="box-header">
	        <div class="col-md-7">
	            <h5><b>Plantillas Horas Extras</b></h5>
	        </div>
          <div class="col-md-4">
            <a type="button" class="btn btn-primary" href="{{route('plantillas_nomina.excel_plantilla_horas')}}"> <span class="glyphicon glyphicon-download-alt"></span> Plantilla</a>
          </div>
      	</div>
      	<div class="row head-title">
	        <div class="col-md-12 cabecera">
	          <label class="color_texto" for="title"></label>
	        </div>
      	</div>

      	<div class="box-body dobra">
      		<form method="POST" id="form_horas">
      			{{ csrf_field() }}
      			<div class="form-group col-md-1 col-xs-2">
            		<label class="texto" for="year">Año</label>
          		</div>

          		<div class="form-group col-md-2 col-xs-10 container-4">
          			<select class="form-control" name="anio" value="{{$anio}}">
                    	@php $x=2019; $anio_actual=date('Y'); @endphp
                    	@for($x=2019;$x<=$anio_actual;$x++)
                      		<option @if($x==$anio) selected @endif>{{$x}}</option>
                      	@endfor
                    </select>
          		</div>
          		<div class="form-group col-md-1 col-xs-2">
		            <label class="texto" for="mes">Mes</label>
		        </div>
		        <div class="form-group col-md-2 col-xs-10 container-4">
		        	<select class="form-control" name="mes">
                      <option value="1" @if($mes==1) selected @endif>Enero</option>
                      <option value="2" @if($mes==2) selected @endif >Febrero</option>
                      <option value="3" @if($mes==3) selected @endif >Marzo</option>
                      <option value="4" @if($mes==4) selected @endif >Abril</option>
                      <option value="5" @if($mes==5) selected @endif >Mayo</option>
                      <option value="6" @if($mes==6) selected @endif >Junio</option>
                      <option value="7" @if($mes==7) selected @endif >Julio</option>
                      <option value="8" @if($mes==8) selected @endif >Agosto</option>
                      <option value="9" @if($mes==9) selected @endif >Septiembre</option>
                      <option value="10" @if($mes==10) selected @endif >Octubre</option>
                      <option value="11" @if($mes==11) selected @endif>Noviembre</option>
                      <option value="12" @if($mes==12) selected @endif>Diciembre</option>
                    </select>
		        </div>

          		<div class="form-group col-md-2 col-xs-2">
          			<label class="texto" for="prestamos">Agregar Archivo</label>
          		</div>

          		<div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
          			<input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/>
          		</div>

          		<div class="col-xs-2">
		            <button type="button" class="btn btn-primary" onclick="guardar_horas();">
		                 Guardar
		            </button>
          		</div>
      		</form>
      	</div>
    </div>
</section>
<script type="text/javascript">
  function guardar_horas(){
    event.preventDefault();
    var form = $('#form_horas')[0];
    var data = new FormData(form);

    $.ajax({
      enctype: 'multipart/form-data',
          type: "post",
            url: "{{ route('plantillas_nomina.subir_horas_extras')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "html",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(datahtml){
                //console.log(datahtml);
                if(datahtml=='ok'){
                   alert("Datos Guardados");
                   location.reload(); 
                }  
            },
            error: function(datahtml){
            //console.log(datahtml);
            }
      });
  }

</script>
@endsection
