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
      position: absolute;
      z-index: 100;
      top: 340px;
    }

</style>

<div class="box " style="border: 2px solid #004AC1; background-color: white;">
    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
      <div class="row">
        <div class="col-md-12">
          <center>
            <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <b>ORDEN DE PROCEDIMIENTO IMAGENES</b>
            </h1> 
          </center>
        </div>
      </div>
    </div>
    <div class="box-body" style="background-color: #56ABE3;">
      <div class="box-body" style="padding: 5px;">
        <div class="box-header with-border" style="background-color: white; color: black;font-family: 'Helvetica general3';border-bottom: #004AC1;border: 2px solid #004AC1;">
          <form method="POST" id="guard_orden_imagenes">
            <input type="hidden" name="fecha" value="{{$fecha_orden}}">
            <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
            <input type="hidden" name="tipo_procedimiento" value="{{$tipo_imagenes}}">
            <div class="col-md-12" style="padding-left: 15px;padding-right: 30px;">
              <div class="row">
                <div class="col-md-8">
                    @if(!is_null($fecha_orden))
                      @php
                        $fecha = substr($fecha_orden,0,10);
                        $invert = explode( '-',$fecha);
                        $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
                      @endphp
                      <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
                      <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>
                      {{$fecha_invert}}</b>
                      </label>
                    @endif 
                </div>
                <div class="col-md-2" style="color: white"> 
                  @if(!is_null($evoluciones)) 
                    {{$evoluciones->id}}
                  @endif
                </div>
                <div class="col-md-2">
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  @if(!is_null($paciente)) 
                    <span style="font-family: 'Helvetica general';font-size: 12px">PACIENTE:</span>
                    <label for="paciente" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$paciente->apellido1}} {{$paciente->apellido2}}
                          {{$paciente->nombre1}} {{$paciente->nombre2}}</b>
                    </label>
                  @endif 
                </div>
                <div class="col-md-4">
                  @if(!is_null($edad)) 
                    <span style="font-family: 'Helvetica general';font-size: 12px">EDAD:</span>
                    <label for="edad" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$edad}}</b>
                    </label>
                  @endif 
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                    @if(!is_null($doctor_solicitante)) 
                      <span style="font-family: 'Helvetica general';font-size: 12px">DOCTOR (a) SOLICITANTE:</span>
                      <label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$doctor_solicitante->apellido1}} {{$doctor_solicitante->apellido2}} {{$doctor_solicitante->nombre1}} {{$doctor_solicitante->nombre2}}
                            </b>
                      </label>
                    @endif 
                </div>
                <div class="col-md-4">
                    @if(!is_null($data)) 
                    <span style="font-family: 'Helvetica general';font-size: 12px">CONVENIO:</span>
                    <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
                      <b> 
                        {{$data->nombre}}-{{$data->nombre_corto}}
                      </b>
                    </label>
                    @endif
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12">
                  <span style="font-family: 'Helvetica general';font-size: 12px">MOTIVO:</span>
                  <textarea id="motivo_orden" name="motivo_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($evoluciones)){{$evoluciones->motivo}}@endif 
                  </textarea>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12">
                  <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
                  <div id="resumen_orden" name="resumen_orden" style="width: 100%; border: 2px solid #004AC1;">
                    @if(!is_null($evoluciones))
                      <?php echo $evoluciones->cuadro_clinico ?>
                    @endif
                  </div>
                  <input type="hidden" name="historia_clinica" id="historia_clinica">
                </div>
              </div>
              <br>
              @php
                  $x_diagnosticos = null;
                  $texto = ""; 
                  
                  if(!is_null($evoluciones)){
                    $x_diagnosticos = \Sis_medico\Hc_Cie10::where('hc_id_procedimiento', $evoluciones->hc_id_procedimiento)->groupBy('cie10')
                    ->get();
                  } 

                  if(!is_null($x_diagnosticos)){ 
                    
                    $mas = true;
                    foreach($x_diagnosticos as $value)
                    {
                       
                      $c3 = \Sis_medico\Cie_10_3::find($value->cie10);
                      if(!is_null($c3)){
                        $descripcion = $c3->descripcion;
                      }

                      $c4 = \Sis_medico\Cie_10_4::find($value->cie10);
                      if(!is_null($c4)){
                        $descripcion = $c4->descripcion;
                      }    

                      if($mas == true){
                        $texto = $value->cie10. ':' . $descripcion. '-' . $value->presuntivo_definitivo;
                        $mas = false;
                         
                      }
                      else{

                        $texto = $texto .'<br>'.$value->cie10. ':' . $descripcion. '-' . $value->presuntivo_definitivo;
                      }
                    }
                  
                  }
              @endphp
              <div class="row">
                <div class="col-md-12">
                  <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                  <div id="diagnostico" name="diagnostico" style="width: 100%; border: 2px solid #004AC1;">
                    <?php echo $texto; ?>
                  </div>
                  <input type="hidden" name="x_diagnostico" id="x_diagnostico">
                </div>
              </div>
              <br>
              <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-6" role="alert" style="display:none;font-size: 15px">
                  <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
                Seleccione un Procedimiento Imagenes
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS IMAGENES 
                    </label>
                  </div>
                  <select id="id_procedimiento" class="form-control input-sm select2_proc_0" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione"
                      style="width: 100%;" autocomplete="off">
                          @foreach($px as $procedimiento)
                              @php 
                                  $clase = 'i'; 
                                  if(!is_null($procedimiento->grupo_procedimiento)){
                                      $clase = $clase.$procedimiento->id_grupo_procedimiento;
                                  }
                              @endphp    
                              <option disabled="disabled" class="{{$clase}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                          @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12">
                  <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
                  <textarea id="observacion_orden" name="observacion_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">{{old('observacion')}}</textarea>
                </div>
              </div>
              <div class="col-md_12" >
                <center>
                <div class="col-md-5" style="padding-top: 15px;text-align: center;">
                    <button id="guarda_orden_imag" style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_orden_procedImagenes();"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
                    </button>
                </div>
                </center>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">
    
    $('.i{{$tipo_imagenes}}').removeAttr('disabled');

    $('.select2_proc_0').select2({
        tags: false,  
    });

    function actualiza_select(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.i{{$tipo_imagenes}}').attr('disabled','disabled');
        $('.i').removeAttr('disabled');

      }else{
        $('.i{{$tipo_imagenes}}').removeAttr('disabled'); 
        $('.i').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc_0').select2({
            tags: false,  
        });
    }

    function quita_select(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.i{{$tipo_imagenes}}').attr('disabled','disabled');
        $('.i').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.i{{$tipo_imagenes}}').removeAttr('disabled');
        $('.i').attr('disabled','disabled');  
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

      $('.select2_proc_0').select2({
            tags: false,  
        });
      
      
    }
     
    $("select").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        actualiza_select();
    }); 

    $("select").on("select2:unselecting", function (evt) {

        quita_select();    
    }); 


  function guardar_orden_procedImagenes(){

    var jprocedimientos = $('#id_procedimiento').val();
      
      if (jprocedimientos != ""){
          $.ajax({
            type: 'post',
            url:"{{route('guarda.ordenhc4_proimagenes')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#guard_orden_imagenes").serialize(),
            success: function(data){
              //console.log(data);
              if(data=='false'){
                //console.log(data);
              }else{
                $("#area_trabajo").html(data);
              }
              
            },

            error: function(data){
              //console.log(data);  
            }

          });

        $('#guarda_orden_imag').attr("disabled", true);

      }else {
        $("#select_proc").fadeIn(1000);
        $("#select_proc").fadeOut(3000);
      }  
  
  }

</script>

<script type="text/javascript">
  tinymce.init({
      selector: '#resumen_orden',
      inline: true,
      menubar: false,
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('resumen_orden');
            $('#historia_clinica').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('resumen_orden');
              $('#historia_clinica').val(ed.getContent());
              
          });
      }
  });


  tinymce.init({
      selector: '#diagnostico',
      inline: true,
      menubar: false,
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('diagnostico');
            $('#x_diagnostico').val(ed.getContent());
            //alert(ed.getContent());
          
          });
      },
    
      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('diagnostico');
              $('#x_diagnostico').val(ed.getContent());
              
          });
      }
  });


</script>








