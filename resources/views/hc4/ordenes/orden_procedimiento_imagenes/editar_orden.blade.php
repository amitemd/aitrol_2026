<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
    
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
     
    .boton-proce{
      font-size: 15px ;
      width: 20%;
      background-color: #004AC1; 
      color: white;
      text-align: center;
      height: 35px;
      padding-left: 5px;
      padding-right: 5px;
      padding-bottom: 0px;
      padding-top: 7px; 
      margin-bottom: 5px; 
    } 
     
    .parent{
     height: 462px;
    }

    .alerta_correcto{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;
      
    }


    .alerta_guardado{
      position: fixed;
      z-index: 9999;
      bottom:  500px;
      left: 200px;
      
    }

</style>
@php
  $aleatorio = rand();
@endphp
<div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
       Guardado Correctamente
</div>
<div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Seleccione un Procedimiento Imagenes
</div>

<div class="col-md-12" style="padding-left: 15px;padding-right: 30px;">
  @php
      if(!is_null($paciente)){
            
        $seguro = Sis_medico\Seguro::find($paciente->id_seguro); 
           
      }

      $fecha = substr($data_orden_imag->fecha_orden,0,10);
      $invert = explode( '-',$fecha);
      $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
  @endphp

  <form method="POST" id="edit_orden_imag">
      {{ csrf_field() }}
      <input type="hidden" name="id_ordenimag" value="{{$data_orden_imag->id}}">
      <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
      <div class="row">
        <div class="col-md-8">
          @if(!is_null($fecha_invert))
            <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
            <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
            </label>
          @endif 
        </div>
        @if($ndoctor->id != 1307189140)
          <div class="col-md-4">
            <span style="font-family: 'Helvetica general';font-size: 16px;color: red;">Agregar Firma CRM</span>
            <input style="width:17px;height:17px;" type="checkbox" id="firma_doctor_rob" class="flat-red" name="firma_doctor_rob" value="1" 
              @if(old('firma_doctor_rob')=="1")  
                checked 
              @elseif($data_orden_imag->check_doctor =='1') 
                checked 
              @endif>
          </div>
        @endif
      </div>
      <div class="row">
        <div class="col-md-8">
          <div>
            @if(!is_null($ndoctor)) 
            <span style="font-family: 'Helvetica general'; font-size: 12px">DOCTOR SOLICITANTE:</span>
            <label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$ndoctor->apellido1}} {{$ndoctor->apellido2}} {{$ndoctor->nombre1}} {{$ndoctor->nombre2}}</b>
            </label>
            @endif 
          </div>  
        </div>
        <div class="col-md-4">
          @if(!is_null($seguro)) 
            <span style="font-family: 'Helvetica general';font-size: 12px">SEGURO:</span>
            <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
              <b> 
                {{$seguro->nombre}}
              </b>
            </label>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">MOTIVO:</span>
          <textarea id="xmotivo_orden" name="xmotivo_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden_imag)){{$data_orden_imag->motivo_consulta}}@endif 
          </textarea>
        </div>
      </div>
      <div class="col-12">&nbsp;</div>
      <div class="row">
            <div class="col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
              <div id="imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                @if(!is_null($data_orden_imag))<?php echo $data_orden_imag->resumen_clinico ?>@endif
              </div>
              <input type="hidden" name="imag_historia_clinica" id="imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}">
            </div>
      </div>
      <div class="row">
              <div class="col-md-12">
                <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                <div id="imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                  @if(!is_null($data_orden_imag))<?php echo $data_orden_imag->diagnosticos ?>@endif
                </div>
                <input type="hidden" name="imag_des_diagnostico" id="imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}">
              </div>
      </div>
      <div class="col-12">&nbsp;</div>
      @php      
          if(!is_null($data_orden_imag->id)){ 
           $x_orden_tipo_imagenes = Sis_medico\Orden_Tipo::where('id_orden',$data_orden_imag->id)
                                          ->where('id_grupo_procedimiento','20')
                                          ->first();
          }  
      @endphp
      <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS IMAGENES 
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_imag" class="form-control input-sm select2_proc" name="x_procedimiento_imag[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
              @php
               $im_aceptacion = 0;
               $im_validacion = null;
              @endphp
              @if(!is_null($x_orden_tipo_imagenes))
                @php
                  $proc_imag = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_imagenes->id)->get();  
                @endphp
                  @foreach($proc_imag as $value)
                    @php
                      
                      $clase = 'im'; 
                      if(!is_null($value->procedimiento->grupo_procedimiento)){
                        $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                      }
                      
                      $im_aceptacion++;
                        
                     
                    @endphp 
                    <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                  @endforeach
              @endif  

              @foreach($px as $value)
                @php
                  
                  $clase = 'im'; 
                  if(!is_null($value->grupo_procedimiento)){
                    $clase = $clase.$value->id_grupo_procedimiento;
                  }

                  if(!is_null($x_orden_tipo_imagenes)){
                    
                    $im_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_imagenes->id)->first();
                    
                    if(!is_null($im_validacion)){
                      $im_aceptacion++;
                    }
                  
                  }
                  
                 
                @endphp 
                @if(is_null($im_validacion))
                  <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
                @endif  
              @endforeach
            </select>
          </div>
        </div>
      <div class="row">
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
          <textarea id="xobservacion_orden" name="xobservacion_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_imagenes({{$data_orden_imag->id}});">@if(!is_null($data_orden_imag))<?php echo $data_orden_imag->observacion_medica ?>@endif</textarea>
        </div>
      </div>
      <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span><textarea id="xobservacion_recepcion" name="xobservacion_recepcion" style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_imagenes({{$data_orden_imag->id}});">@if(!is_null($data_orden_imag))<?php echo $data_orden_imag->observacion_recepcion?>@endif</textarea>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OTROS PROCEDIMIENTOS:</span><textarea id="xotros" name="xotros" style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_imagenes({{$data_orden_imag->id}});">@if(!is_null($data_orden_imag))<?php echo $data_orden_imag->otros?>@endif</textarea>
          </div>
      </div>
      <br>
      <div class="row">
          <div class="col-4">
            <center>
              <button style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="actualiza_orden_imagenes({{$data_orden_imag->id}})"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
              </button>
            </center>
          </div>
          <div class="col-4">
            <center>
              <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="descargar_orden_imagenes({{$data_orden_imag->id}});"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;Descargar Orden
              </button>
            </center>
          </div>
          @if($data_orden_imag->id_evolucion!=null)
          <div class="col-4">
            <center>
              <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="formato_012({{$data_orden_imag->id_evolucion}}, {{$data_orden_imag->id}}, 'area_trabajo_formato012_pi{{$data_orden_imag->id}}{{$aleatorio}}')"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Formato 012
              </button>
            </center>
          </div>
          @endif
      </div>
  </form>
  <div class="row" style="border-radius: 8px;" id="area_trabajo_formato012_pi{{$data_orden_imag->id}}{{$aleatorio}}"></div>
</div>

<script type="text/javascript">

  tinymce.init({
      selector: '#imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}');
            $('#imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('imag_resumen_orden{{$data_orden_imag->id}}{{$aleatorio}}');
              $('#imag_historia_clinica{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
              
          });
      }
    });


    tinymce.init({
      selector: '#imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}');
            $('#imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('imag_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}');
              $('#imag_des_diagnostico{{$data_orden_imag->id}}{{$aleatorio}}').val(ed.getContent());
              
          });
      }
    });

</script>

<script type="text/javascript">
    
    $('.im20').removeAttr('disabled');

    $('.select2_proc').select2({
            tags: false,  
    });

    function im_actualiza_select(){
      
      var seleccionados =  $('#id_procedimiento_imag').find(':selected');   
      var longitud =  seleccionados.length; 
      //alert(longitud);     
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.im20').attr('disabled','disabled');
        $('.im').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.im20').removeAttr('disabled'); 
        $('.im').attr('disabled','disabled');
        //$('.c').prop('disabled','false');    
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('.select2_proc').select2({
        tags: false,  
      });
      
     
    }

    function im_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_imag').find(':selected');   
      var longitud =  seleccionados.length; 
      //alert("q"+longitud);  
      if(longitud>1){
        
        $('.im20').attr('disabled','disabled');
        $('.im').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.im20').removeAttr('disabled');
        $('.im').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
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
        im_actualiza_select();
        actualiza_orden_imagenes({{$data_orden_imag->id}});
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {
       im_quita_select();    
       actualiza_orden_imagenes({{$data_orden_imag->id}});     
    }); 

    @if($im_aceptacion > 0 )
      //alert("base");
      im_actualiza_select();
    @endif

</script>

<script type="text/javascript">
  
    //Funcion para actualizar Orden de Imagenes
    function actualiza_orden_imagenes(id_orden){

        var jprocedimientos = $('#id_procedimiento_imag').val();  
        if (jprocedimientos != ""){      
          $.ajax({
              type: "POST",
              url: "{{route('actualiza.ordenhc4_procedimagenes')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: "html", 
              data: $("#edit_orden_imag").serialize(),
              success: function(datahtml){
                //console.log(datahtml);
                $("#alerta_guardado").fadeIn(1000);
                $("#alerta_guardado").fadeOut(3000);

              },
              error:  function(){
                
               //console.log();

              }
            });
        }else{
          $("#select_proc").fadeIn(1000);
          $("#select_proc").fadeOut(3000);
        }   

    };

    function descargar_orden_imagenes(id_or){
       window.open('{{url('imprimir/orden_hc4/imagenes')}}/'+id_or,'_blank');  
    }
</script>

