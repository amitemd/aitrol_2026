<div class="row">
	<div class="col-12">
		<span style="font-family: 'Helvetica general';">@lang('hc4.motivo')</span>
	</div>
	<div class="col-12">
		<span>{{strip_tags($evolucion->motivo)}}</span>
	</div>
	<div class="col-12">&nbsp;</div>
	<div class="col-12">
		<span style="font-family: 'Helvetica general';">@lang('hc4.detalle')</span>
	</div>
	<div class="col-12">
		<span><?php echo strip_tags($evolucion->cuadro_clinico); ?></span>
	</div>
</div>