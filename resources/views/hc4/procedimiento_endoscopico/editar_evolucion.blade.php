
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
			<div id="tcuadro_clinico{{ $evolucion->id }}{{ $variable_tiempo }}" style="border: solid 1px;">
				@php
					$texto = trim(str_replace('&nbsp;', '', strip_tags($evolucion->cuadro_clinico ?? '')));
			
					$id_grupo_procedimiento = null;
			
					if (
						!is_null($evolucion->historiaclinica) &&
						!is_null($evolucion->historiaclinica->agenda) &&
						!is_null($evolucion->historiaclinica->agenda->procedimiento)
					) {
						$id_grupo_procedimiento = $evolucion->historiaclinica->agenda->procedimiento->id_grupo_procedimiento;
					}
			
					$texto_egreso_quirofano = 'PACIENTE EGRESA DE QUIROFANO ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS ENDOSCOPICOS. PACIENTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. SE DA DE ALTA, SE EXPLICAN LOS RESULTADOS A PACIENTE Y ACOMPAÑANTE. SE ENTREGAN INDICACIONES AL ALTA.';
			
					if ($id_grupo_procedimiento == 18) {
						$contenido_cuadro_clinico = !empty($texto) ? $evolucion->cuadro_clinico : 'PACIENTE EGRESA ESTABLE HEMODINAMICAMENTE LUEGO DE LA REALIZACION DE ESTUDIOS FUNCIONALES. PACIENTE EVOLUCIONA FAVORABLEMENTE DESPUES DEL PROCEDIMIENTO. SE MANTIENE ESTABLE HEMODINAMICAMENTE. CLINICAMENTE BIEN. SE DA DE ALTA, SE EXPLICAN LOS RESULTADOS A PACIENTE Y ACOMPAÑANTE.';
					} else {
						$contenido_cuadro_clinico = !empty($texto) ? $evolucion->cuadro_clinico : $texto_egreso_quirofano;
					}
				@endphp
			
				{!! $contenido_cuadro_clinico !!}
			</div>
		
			<input type="hidden" 
				   name="cuadro_clinico" 
				   id="cuadro_clinico{{ $evolucion->id }}{{ $variable_tiempo }}">
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
</script>