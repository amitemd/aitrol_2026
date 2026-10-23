<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border" style="background-color: #004AC1; color: white;  font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
		<div class="row">
			<div class="col-md-5" >
			<div class="btn" style="color: white">
				<a class="fa fa-pencil-square-o "  onclick="editar_evolucion({{$evolucion->id}});" ><span style="font-size: 13px">&nbsp;Editar</span>
				</a>
			</div>
		</div>
			<span style="padding-top: 5px;">Detalles de la Evolucion</span>
		</div>
		
	</div>
	<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
		<div class="row">
			<div class="col-12">
				<span style="font-family: 'Helvetica general';">Motivo</span>
			</div>
			<div class="col-12">
				<span>{{strip_tags($evolucion->motivo)}}</span>
			</div>
			<div class="col-12">&nbsp;</div>
			<div class="col-12">
				<span style="font-family: 'Helvetica general';">Detalle</span>
			</div>
			<div class="col-12">
				<span><?php echo strip_tags($evolucion->cuadro_clinico); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12" style="height: 5px;"></div>