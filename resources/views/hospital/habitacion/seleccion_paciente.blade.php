<div class="row" style="padding: 25px;"> 
	<div class="col-md-9"> 
		<h2> <b> {{trans('hospitalizacion.PacienteSeleccionado:')}} {{$paciente}}</b> </h2> &nbsp; 
		
	</div> 
	<div class="col-md-3"> 
		<button type="button" onclick="reset(this)" class="btn btn-danger btn-sm"> 
			<i class="fa fa-remove"> </i> 
		</button> 
	</div> 
	<div class="col-md-12"> <b>{{trans('hospitalizacion.FechayHora:')}}</b> </div> 
	<div class="col-md-12"> {{$fecha}} 
	</div> 
	<div class="col-md-12"> <b> {{trans('hospitalizacion.Causa')}} </b> </div> 
	<div class="col-md-12"> {{$traspaso->causa}}
	</div> 
		<div class="col-md-12"> <b> {{trans('hospitalizacion.Observación')}}  </b> </div> 
	<div class="col-md-12">{{$traspaso->observaciones}} 
	</div> 
</div> 
