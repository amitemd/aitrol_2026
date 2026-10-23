@extends('rrhh.resultados.base')
@section('action-content')
<style type="text/css">
	.elemento {
		white-space: normal !important;
		text-align: center;
	}
</style>
<section class="content">
	<div class="box">
		<div class="box-header">
			<h2>{{trans('encuestas.estadisticasdelasencuestas')}}</h2>
		</div>
		<div class="box-body">
			<div class="form-group">
				
				<div class="col-md-12">
					@foreach($encuestas as $value)
					<div class="row">
						<div class="col-md-2">&nbsp;</div>
						<div class="col-md-8 " style="word-wrap: break-word !important;"><a href="{{route('tipo_sugerencia.estadisticas_tipo',['id'=>$value->id])}}" class="elemento btn btn-primary btn-lg form-control">{{$value->descripcion}}</a></div>
						<div class="col-md-2">&nbsp;</div>
					</div>
					@endforeach
				</div>
                

			</div>

		</div>
	</div>
</section>
@endsection