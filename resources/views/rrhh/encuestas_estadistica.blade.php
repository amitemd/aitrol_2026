@extends('rrhh.resultados.base')
@section('action-content')
<style type="text/css">
	.elemento{
  	white-space: normal !important;
  	text-align: center;
  }
</style>
<section class="content">
	<div class="box">
		<div class="box-header">
		 	<h2>Estadisticas de las Encuestas</h2>
		</div>
		<div class="box-body">
			<div class="form-group">
		 		<div class="row">
					<div class="col-md-4">&nbsp;</div>	
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
				</div>
		 		<div class="col-md-12">
				@foreach($encuestas as $value)
				<div class="row">
					<div class="col-md-3" >&nbsp;</div>	
					<div class="col-md-6 " style="word-wrap: break-word !important;" ><a  href="{{route('rrhh.estadisticas_2',['id'=>$value->id])}}"class="elemento btn btn-primary btn-lg form-control">{{$value->descripcion}}</a></div>
					<div class="col-md-3" >&nbsp;</div>
				</div>	
				<div class="row">
					<div class="col-md-4">&nbsp;</div>	
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
				</div>	
				@endforeach
				<!-- relleno -->
				<div class="row">
					<div class="col-md-4">&nbsp;</div>	
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
				</div>
				<div class="row">
					<div class="col-md-4">&nbsp;</div>	
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
				</div>
				</div>
				
			</div>

		</div>
	</div>
</section>	
@endsection