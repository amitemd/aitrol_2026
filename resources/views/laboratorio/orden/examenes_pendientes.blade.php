<div class="modal-header">
    <div class="col-md-10"><h3>Examenes Pendientes: {{$orden->paciente->apellido1}} {{$orden->paciente->nombre1}}</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
	<div class="row">
		@foreach($orden->detalles as $detalle)
			@php
				if($detalle->examen->sexo_n_s=='0'){
					$parametros = Sis_medico\Examen_Parametro::where('id_examen',$detalle->id_examen)->where('sexo','3')->where('estado','1')->get(); 
	            }else{
	                $parametros = Sis_medico\Examen_Parametro::where('id_examen',$detalle->id_examen)->where('sexo',$orden->paciente->sexo)->where('estado','1')->get();
	            }
				
				$mostrar_nombre = '1';
			@endphp 
			@if($parametros->count()==0)
				@if($detalle->examen->no_resultado==0)
				@if($mostrar_nombre)
					<div class="col-md-6">{{$detalle->examen->nombre}}</div>
					@php $mostrar_nombre = '0'; @endphp
				@endif
				<div class="col-md-6">Examen pendiente de Ingresar Parametros</div>
				@endif
			@else
				@foreach($parametros as $parametro)
					@php
						$resultado = $orden->resultados->where('id_parametro',$parametro->id)->first();
					@endphp
					@if(is_null($resultado))
						@if($mostrar_nombre)
							<div class="col-md-12"><b>{{$detalle->examen->nombre}}</b></div>
							@php $mostrar_nombre = '0'; @endphp
						@endif
						<div class="col-md-6">{{$parametro->nombre}}</div>
						<div class="col-md-6">Resultado No ingresado</div>
					@else
						@if(!$resultado->certificado)
							@if($mostrar_nombre)
								<div class="col-md-12"><b>{{$detalle->examen->nombre}}</b></div>
								@php $mostrar_nombre = '0'; @endphp
							@endif
							<div class="col-md-6">{{$parametro->nombre}}</div>
							<div class="col-md-6">Resultado No Validado</div>
						@endif		
					@endif
				@endforeach			
			@endif
		@endforeach
	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>	