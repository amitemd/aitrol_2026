<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border hc4-ecography-evolution-item-header">
		<button type="button" class="hc4-ecography-edit-button hc4-ecography-evolution-edit" onclick="editar_evolucion({{$evolucion->id}});">
			<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
		</button>
		<div class="hc4-ecography-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
		<div class="hc4-ecography-evolution-item-tools">
			<button type="button" class="hc4-ecography-evolution-delete" title="Eliminar evolución" onclick="eliminar_evolucion_imagenes(this, {{$evolucion->id}});">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v5M14 11v5"></path></svg><span>Eliminar</span>
			</button>
			<button type="button" class="hc4-ecography-evolution-item-toggle" aria-expanded="true" title="Colapsar detalle" onclick="alternar_detalle_evolucion(this);">
				<span aria-hidden="true">&minus;</span>
			</button>
		</div>
	</div>
	<div class="box-body hc4-ecography-evolution-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
