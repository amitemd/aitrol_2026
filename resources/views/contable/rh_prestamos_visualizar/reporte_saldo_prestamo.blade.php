@extends('contable.rh_prestamos_visualizar.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<section  class="content">
	<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reporte Prestamo</li>
      </ol>
    </nav>
    <div class="box">
    	<div class="row head-title">
	        <div class="col-md-12 cabecera">
	            <label class="color_texto" for="title">REPORTE DATOS PRESTAMOS</label>
	        </div>
      	</div>
        <div class="box-body dobra">
        	<form method="POST" id="reporte_prestamo">
        		{{ csrf_field() }}

        		

        			<div class="form-group col-md-2 col-xs-2">
            			<label class="texto" for="id_empresa">Fecha Hasta:</label>
          			</div>

          			<div class="form-group col-md-3 col-xs-2 container-2">
			          <div class="col-md-12">
			            <div class="input-group date">
			              <div class="input-group-addon">
			                <i class="fa fa-calendar"></i>
			              </div>
			              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
			              <div class="input-group-addon">
			                <i class="glyphicon glyphicon-remove-circle"></i>
			              </div>   
			            </div>
			          </div>  
        			</div>
          			<div class="form-group col-md-2 col-xs-2


          			 pull-right"> 
			            <button type="submit" class="btn btn-primary" formaction="{{route('prestamos_empleados.excel_reporte_prestamo')}}">
			              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
			            </button> 
			        </div> 
			        		
        	</form>
        </div>

    </div>
	
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
	$('#fecha').datetimepicker({
        format: 'YYYY/MM/DD',            
        defaultDate: '{{$fecha}}',  
    });

    $('#fecha_hasta').datetimepicker({
        format: 'YYYY/MM/DD',            
        defaultDate: '{{$fecha}}',  
    });

    
</script>
@endsection