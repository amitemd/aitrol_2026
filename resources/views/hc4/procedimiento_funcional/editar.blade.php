

<div class="row">
	<form id="hc_protocolo" style="width: 100%;">
		<input type="hidden" name="id_paciente" value="{{$id_paciente}}">
		<input type="hidden" name="id_procedimiento" value="<?php echo e($procedimiento->id); ?>">
		<input type="hidden" name="id_protocolo" value="<?php if(!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif; ?>">
		<input type="hidden" name="tipo" value="{{$tipo}}">
		<div class="col-12">&nbsp;</div> 
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Procedimiento</span>
		</div>
		<?php 
			$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
		?>
		@if(!is_null($adicionales->last()))
		<?php 
			

		$mas = true; 
		$texto = "";

			foreach($adicionales as $value2)
			{
				if($mas == true){
				 $texto = $texto.$value2->procedimiento->nombre  ;
				 $mas = false; 
				 }
				else{
			  	 $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
			  	 }					  						
			}
		?>
		<div class="col-12">
			<span>
				<?php echo e($texto); ?>

			</span>
		</div>
		@else
		<div class="col-12">
			@php  $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
			@endphp
			<span>@if(!is_null($procedimiento_completo)) {{$procedimiento_completo->nombre_general}} @endif</span>
		</div>
		@endif

		<div class="col-12">
      <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple"     data-placeholder="Seleccione" style="width: 95.5%; padding-left: 15px " autocomplete="off">
          @php      
              $aceptacion = 0        
          @endphp
            @foreach($px as $value)
                @php 
                    $validacion = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->where('id_hc_procedimientos', $procedimiento->id)->first();

                    $clase = 'c'; 
                    if(!is_null($value->grupo_procedimiento)){
                      $clase = $clase.$value->grupo_procedimiento->tipo_procedimiento;
                    } 
                    if(!is_null($validacion)){
                      $aceptacion++;
                    }
                @endphp    
                <option  disabled="disabled" class="{{$clase}}" @if(!is_null($validacion)) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
      </select>
    </div>
    <div id="alerta_datos" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
          Seleccione un Procedimiento Funcional
    </div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Seguro</span>
		</div>
		<div class="col-12">
			<span style="">{{$hc_seguro->nombre}}</span>
		</div>
		<br>
		<div class="col-12">&nbsp;</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Hallazgos</span>
		</div>
		<div class="col-12">
			<div id="thallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;"><?php if(!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif; ?></div>
			<input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>">
		</div>
		<div class="col-12">&nbsp;</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Conclusiones</span>
		</div>
		<div class="col-12">
			<div id="tconclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;"><?php if(!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif; ?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>">
		</div>

		<div class="col-12">
			<span style="font-family: 'Helvetica general';">&nbsp;</span>
		</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
		</div>
		   <div class="col-4">
            <select name="id_doctor_examinador">
                @foreach($doctores as $value)
                    <option @if(!is_null($procedimiento->id_doctor_examinador)) @if($procedimiento->id_doctor_examinador == $value->id) selected @endif @endif  value="{{$value->id}}">{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}</option>
                @endforeach
            </select>
        </div>
		<div  style="text-align: center;">
            <button style="font-size: 15px; margin-bottom: 15px; height: 30px; width: 125px;"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento_func(<?php echo e($procedimiento->id); ?>)"  ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
	</form>
</div>
<script type="text/javascript">
	tinymce.init({
        selector: '#thallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if(is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>');
                $("#hallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
        },
        <?php endif; ?>
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>');
                $("#hallazgos<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
              
            });
          }
    });

    tinymce.init({
        selector: '#tconclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if(is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>');
                $("#conclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
        },
        <?php endif; ?>
        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>');
                $("#conclusion<?php echo e($procedimiento->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
              
            });
          }
    });

    function guardar_procedimiento_func(id){
      var jprocedimientos = $('#id_procedimiento').val();
      //console.log (jprocedimientos);
      if (jprocedimientos != ""){
      	var entra = id;
  			$.ajax({
  			type: "POST",
  			url: "<?php echo e(route('guardar.procedimiento_funcional')); ?>", 
            	data: $("#hc_protocolo").serialize(),
            	headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
  			datatype: "html",
  			success: function(datahtml, entra){
  				$("#procedimiento"+id).html(datahtml);
  				console.log(id);
  			},
  			error:  function(){
  				alert('error al cargar');
  			}
  		  });	
      }else {
              $("#alerta_datos").fadeIn(1000);
              $("#alerta_datos").fadeOut(3000);
              //alert("Seleccione un Procedimiento");
             }
	   };

</script>
<script type="text/javascript">
    
    $('.c{{$tipo}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo}}').removeAttr('disabled'); 
        $('.c').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');  
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

      $('.select2_proc').select2({
            tags: false,  
        });
      
      
    }
     
    $(".select2_proc").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        actualiza_select();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select();    
    }); 

    @if($aceptacion > 0 )
     actualiza_select();
    @endif

</script>