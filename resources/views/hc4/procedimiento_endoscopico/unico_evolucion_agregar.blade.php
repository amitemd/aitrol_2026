<div class="box" id="evolucion_eliminar{{$evolucion->id}}" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border hc4-evolution-item-header">
		<div class="hc4-endoscopy-action-wrap hc4-content-edit">
			<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_evolucion({{$evolucion->id}});">
				<i class="fa fa-pencil-square-o"></i><span>Editar</span>
			</button>
		</div>
		<div class="hc4-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
		<div class="hc4-endoscopy-action-wrap hc4-content-delete hc4-evolution-item-actions">
			<button type="button" class="btn hc4-endoscopy-inline-action hc4-endoscopy-inline-action--danger" onclick="eliminar_evolucion({{$evolucion->id}}, '{{$evolucion->hc_id_procedimiento}}');" title="Eliminar evolución" aria-label="Eliminar evolución">
				<i class="fa fa-trash"></i><span class="sr-only">Eliminar</span>
			</button>
			<button type="button" class="hc4-evolution-item-toggle" aria-expanded="true" title="Minimizar detalle" onclick="alternar_detalle_evolucion_endoscopica(this);">
				<span aria-hidden="true">&minus;</span>
			</button>
		</div>
	</div>
	<div class="box-body hc4-evolution-item-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
