<br>
<div class="card">
	<div class="card-header bg bg-primary colorbasic">
		
		<div class="col-md-2" >
			<a class="btn btn-warning btn-xs waves-effect waves-float waves-light" type="button"  onclick="editar_evolucion({{$evolucion->id}});"><span class="fa fa-pencil-square-o"></span>
            </a>
    	</div>
    	<div class="col-md-9">
    		<span style="padding-top: 5px;">Detalles de la Evolucion</span>
    	</div>
		
	</div>
		
	</div>
	<div class="card-body" id="evolucion{{$evolucion->id}}">
		<div class="row">
			<div class="col-12">
				<span>Motivo</span>
			</div>
			<div class="col-12">
				<span>{{strip_tags($evolucion->motivo)}}</span>
			</div>
			<div class="col-12">&nbsp;</div>
			<div class="col-12">
				<span>Detalle</span>
			</div>
			<div class="col-12">
				<span><?php echo strip_tags($evolucion->cuadro_clinico); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" style="height: 5px;"></div>