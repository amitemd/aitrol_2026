
@php
    $variable_tiempo = rand(1, 9999999);
@endphp
<div class="row">
	<form id="hc_evolucion{{$variable_tiempo}}" style="width: 100%;">
		<input type="hidden" name="id_paciente" value="{{$id_paciente}}">
		<input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
		<div class="col-12">&nbsp;</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Motivo</span>
		</div>
		<div class="col-12">
			<input type="text" name="motivo" id="evolucion" value="{{$evolucion->motivo}}" style="width: 100%">
		</div>
		<div class="col-12">&nbsp;</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Detalle</span>
		</div>
		<div class="col-12">
			<div id="tcuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}" style="border: solid 1px;"><?php if (!is_null($evolucion)): ?><?php echo $evolucion->cuadro_clinico ?><?php endif;?></div>
            <input type="hidden" name="cuadro_clinico" id="cuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}">
		</div>
		<div class="col-12">
		<center>
		<div class="col-5" style="padding-top: 15px">
             <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_evolucion(<?php echo e($evolucion->id); ?>, '{{$variable_tiempo}}')"  ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
        </center>
        </div>
	</form>
	@if(!is_null($solicitud) and $evolucion->secuencia == 0)
	
		<div class="col-12">
			<b>{{trans('pasos.ExamenFisicoyDiagnostico')}}</b>
		</div>
	
		<form id="form_paso7{{$solicitud->id}}" style="width: 100%;">
			@php
				$form008 = $solicitud->form008->first();
			@endphp
			<input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">

			<div class="col-12">
				<b> 1.{{trans('pasos.PielFaneras')}}</b>
				<input class="form-control" type="text" name="piel_faneras" id="piel_faneras" value="{{$form008->piel_faneras}}">
			</div>
			<div class="col-12">
				<b> 2.{{trans('pasos.Cabeza')}}</b>
				<input class="form-control" type="text" name="cabeza" id="cabeza" value="{{$form008->cabeza}}">
			</div>
			<div class="col-12">
				<b> 3.{{trans('pasos.Ojos')}}</b>
				<input class="form-control" type="text" name="ojos" id="ojos" value="{{$form008->ojos}}">
			</div>
			<div class="col-12">
				<b> 4.{{trans('pasos.Oidos')}}</b>
				<input class="form-control" type="text" name="oidos" id="oidos" value="{{$form008->oidos}}">
			</div>
			<div class="col-12">
				<b> 5.{{trans('pasos.Nariz')}}</b>
				<input class="form-control" type="text" name="nariz" id="nariz" value="{{$form008->nariz}}">
			</div>
			<div class="col-12">
				<b> 6.{{trans('pasos.Boca')}}</b>
				<input class="form-control" type="text" name="boca" id="boca" value="{{$form008->boca}}">
			</div>
			<div class="col-12">
				<b> 7.{{trans('pasos.OroFaringe')}}</b>
				<input class="form-control" type="text" name="oro_faringe" id="oro_faringe" value="{{$form008->oro_faringe}}">
			</div>
			<div class="col-12">
				<b> 8.{{trans('pasos.Cuello')}}</b>
				<input class="form-control" type="text" name="cuello" id="cuello" value="{{$form008->cuello}}">
			</div>
			<div class="col-12">
				<b> 9.{{trans('pasos.AxilasMamas')}}</b>
				<input class="form-control" type="text" name="axilas_mamas" id="axilas_mamas" value="{{$form008->axilas_mamas}}">
			</div>
			<div class="col-12">
				<b> 10.{{trans('pasos.Torax')}}</b>
				<input class="form-control" type="text" name="torax" id="torax" value="{{$form008->torax}}">
			</div>

			<div class="col-12">
				<b> 11.{{trans('pasos.Abdomen')}}</b>
				<input class="form-control" type="text" name="abdomen" id="abdomen" value="{{$form008->abdomen}}">
			</div>
			<div class="col-12">
				<b> 12.{{trans('pasos.Columna')}}</b>
				<input class="form-control" type="text" name="columna" id="columna" value="{{$form008->columna}}">
			</div>
			<div class="col-12">
				<b> 13.{{trans('pasos.InglePerine')}}</b>
				<input class="form-control" type="text" name="ingle_perine" id="ingle_perine" value="{{$form008->ingle_perine}}">
			</div>
			<div class="col-12">
				<b> 14.{{trans('pasos.MiembrosSuperiores')}}</b>
				<input class="form-control" type="text" name="miembros_superiores" id="miembros_superiores" value="{{$form008->miembros_superiores}}">
			</div>
			<div class="col-12">
				<b> 15.{{trans('pasos.MiembrosInferiores')}}</b>
				<input class="form-control" type="text" name="miembros_inferiores" id="miembros_inferiores" value="{{$form008->miembros_inferiores}}">
			</div>
			<div class="col-12" style="text-align: center; margin-top: 10px;">
				<button class="btn btn-primary" onclick="guardar_paso7('{{$solicitud->id}}');" type="button"> <i class="fa fa-save"></i> </button>
			</div>
				
		</form>

	
	@endif
	
</div>

<script type="text/javascript">


    tinymce.init({
        selector: '#tcuadro_clinico{{($evolucion->id)}}{{$variable_tiempo}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if (is_null($evolucion)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tcuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}');
                $("#cuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}").val(ed.getContent());
            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tcuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}');
                $("#cuadro_clinico<?php echo e($evolucion->id); ?>{{$variable_tiempo}}").val(ed.getContent());

            });
          }
    });
    function guardar_evolucion(id, variable){
    	var entra = id;
			$.ajax({
			type: "POST",
			url: "{{route('guardar.procedimiento_evolucion')}}",
          	data: $("#hc_evolucion"+variable).serialize(),
          	headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
			datatype: "html",
			success: function(datahtml, entra){
				$("#evolucion"+id).html(datahtml);
				console.log(id);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	};
	function guardar_paso7(id) {

		$.ajax({
			type: "post",
			url: "{{route('hospital.septimopaso_store')}}",
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: 'json',
			data: $("#form_paso7"+id).serialize(),

			success: function(datahtml, data) {
				//$("#content").html(datahtml);
				//return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);
			},
			error: function() {
				alert('error al cargar');
			}
		});

	}

	
</script>