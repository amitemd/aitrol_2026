

<div class="card">
    <div class="card-body" style="padding: 0;">
    	@foreach($ptv_biopsias as $orden)
			<!--div id="orden_lab_div{{$orden->id}}" class="col-md-12" style="padding: 0;" -->
			<div class="box box-primary collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px;padding: 0; ">
				<div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
				<!--div class="card-header bg bg-primary colorbasic" -->
					<div class="row">
						<div class="col-md-1">
		        			<button id="edit{{$orden->id}}" type="button" class="btn btn-warning btn-xs" onclick="editar_orden_ptv('{{$orden->id}}')">
	                        	<i class="fa fa-edit"></i>
	                        </button>
		        		</div>
		        		<div class="col-md-6">
		        			{{$orden->tipo->descripcion}}
		        		</div>
		        		<div class="col-md-2">
		        			<!--button id="imprimir{{$orden->id}}" type="button" class="btn btn-success btn-xs" onclick="imprimir_orden_ptv('{{$orden->id}}')">
	                        	Imprimir
	                        </button-->
	                        <a href="{{route('ordenbiopsiasptv.imprimir',['id'=>$orden->id])}}" target="_blank" class="btn btn-success btn-xs">Imprimir</a>
		        		</div>
		        		<div class="col-md-1">
		        			<button id="eliminar{{$orden->id}}" type="button" class="btn btn-danger btn-xs" onclick="eliminar_orden_ptv('{{$orden->id}}','{{$orden->hc_id_procedimientos}}')">
	                        	<i class="fa fa-trash"></i>
	                        </button>
		        		</div>
		        		
						<!--div class="col-md-1">
							<button id="min{{$orden->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden_ptv('{{$orden->id}}')" style="display: none">
	                        	<i class="fa fa-minus"></i>
	                        </button>
							<button id="plus{{$orden->id}}" type="button" class="btn btn-primary" onclick="ver_orden_ptv('{{$orden->id}}')">
	                        	<i class="fa fa-plus"></i>
	                        </button>
						</div-->
					</div>
					<div class="pull-right box-tools">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="open_biopsias_ptv_2{{$orden->id}}" >
                            <i class="fa fa-plus"></i></button>
                    </div>	
		        </div>
		        <!--div class="card-body" id="labs_detalle{{$orden->id}}" style="padding: 0px;"-->
		        <div class="box-body" id="detalle_biopsia_ptv{{$orden->id}}" style="background: white;">
		        	<div class="row">
		        		<div class="col-md-6"><b>Otras Localizaciones</b></div>
		        		<div class="col-md-6">{{$orden->otras_localizaciones}}</div>
		        		<div class="col-md-6"><b>Otros Organos</b></div>
		        		<div class="col-md-6">{{$orden->otros_organos}}</div>
		        		<div class="col-md-12"><b>Datos Clínicos</b></div>
		        		<div class="col-md-12"><?php echo $orden->datos_clinicos; ?></div>
		        		<div class="col-md-12"><b>Diagnóstico</b></div>
		        		<div class="col-md-12"><?php echo $orden->diagnostico; ?></div>
		        	@foreach($orden->detalles as $detalle)
		        		<div class="col-md-6">
        				<b>{{$detalle->descripcion}}:</b> {{$detalle->detalle}}
        				</div>
        			@endforeach
        			</div>
		        </div>
		    </div>

		@endforeach
	</div>
</div>


<script type="text/javascript">
	

</script>

