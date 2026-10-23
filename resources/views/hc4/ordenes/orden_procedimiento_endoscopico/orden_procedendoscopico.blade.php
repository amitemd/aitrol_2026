<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
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
      top: 520px;
    }

</style>

<div class="box " style="border: 2px solid #004AC1; background-color: white;">
    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
      <div class="row">
        <div class="col-md-12">
          <center>
            <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <b>ORDEN DE PROCEDIMIENTO ENDOSC&OacutePICO</b>
            </h1> 
          </center>
        </div>
      </div>
    </div>
    
    <div class="box-body" style="background-color: #56ABE3;">
      <div class="box-body" style="padding: 5px;">
        <div class="box-header with-border" style="background-color: white; color: black;font-family: 'Helvetica general3';border-bottom: #004AC1;border: 2px solid #004AC1;">
          <!--<div class="alert alert-warning m1 oculto">
            <strong>Atencion!</strong> <span id="alertms"></span>
          </div>-->
          <form method="POST" id="guard_orden">
            {{ csrf_field() }}
              <input type="hidden" name="fecha" value="{{$fecha_orden}}">
              <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
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
                  <span style="font-family: 'Helvetica general';font-size: 12px">CONSULTA</span>
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
                <div class="col-md-12">
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
                Seleccione un Procedimiento
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">ENDOSCOPIAS DIGESTIVAS 
                    </label>
                  </div>
                  <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $clase = 'c'; 
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
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">COLONOSCOPIA 
                    </label>
                  </div>
                  <select id="id_procedimiento_col" class="form-control input-sm select2_proc" name="procedimiento_colono[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $clasep = 'p'; 
                            if(!is_null($procedimiento->grupo_procedimiento)){
                              $clasep = $clasep.$procedimiento->id_grupo_procedimiento;
                            }
                        @endphp    
                        <option disabled="disabled" class="{{$clasep}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">
                     INTESTINO DELGADO 
                    </label>
                  </div>
                  <select id="id_procedimiento_enter" class="form-control input-sm select2_proc" name="procedimiento_enter[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $claset = 't'; 
                            if(!is_null($procedimiento->grupo_procedimiento)){
                              $claset = $claset.$procedimiento->id_grupo_procedimiento;
                            }
                        @endphp    
                        <option disabled="disabled" class="{{$claset}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">
                     ECOENDOSCOPIAS 
                    </label>
                  </div>
                  <select id="id_procedimiento_ecoend" class="form-control input-sm select2_proc" name="procedimiento_ecoend[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $clased = 'd'; 
                            if(!is_null($procedimiento->grupo_procedimiento)){
                              $clased = $clased.$procedimiento->id_grupo_procedimiento;
                            }
                        @endphp    
                        <option disabled="disabled" class="{{$clased}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">
                     CPRE 
                    </label>
                  </div>
                  <select id="id_procedimiento_cpre" class="form-control input-sm select2_proc" name="procedimiento_cpre[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $claser = 'r'; 
                            if(!is_null($procedimiento->grupo_procedimiento)){
                              $claser = $claser.$procedimiento->id_grupo_procedimiento;
                            }
                        @endphp    
                        <option disabled="disabled" class="{{$claser}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div style="background-color: #004AC1; color: white">
                    <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">
                     BRONCOSCOPIA 
                    </label>
                  </div>
                  <select id="id_procedimiento_bron" class="form-control input-sm select2_proc" name="procedimiento_bron[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%; " autocomplete="off">
                    @foreach($px as $procedimiento)
                        @php 
                            $claseb = 'b'; 
                            if(!is_null($procedimiento->grupo_procedimiento)){
                              $claseb = $claseb.$procedimiento->id_grupo_procedimiento;
                            }
                        @endphp    
                        <option disabled="disabled" class="{{$claseb}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
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
                    <button  id="guarda_orden_endos"  style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_orden_procedendoscopico();"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
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

</script>


<script type="text/javascript">

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

<script type="text/javascript">

function guardar_orden_procedendoscopico(){

    //$('#alertms').empty().html('');
    //$(".m1").addClass("oculto");

    var jprocedimiento_endos = $('#id_procedimiento').val();
    var jprocedimiento_colono = $('#id_procedimiento_col').val();
    var jprocedimiento_int = $('#id_procedimiento_enter').val();
    var jprocedimiento_ecoend = $('#id_procedimiento_ecoend').val();
    var jprocedimiento_cpre = $('#id_procedimiento_cpre').val();
    var jprocedimiento_bron = $('#id_procedimiento_bron').val();

        
    if ((jprocedimiento_endos != "")||(jprocedimiento_colono != "")||(jprocedimiento_int != "")||(jprocedimiento_ecoend != "")||(jprocedimiento_cpre != "")||(jprocedimiento_bron != "")){
        
        $.ajax({
              type: 'post',
              url:"{{route('guarda.ordenhc4_proendoscopica')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: 'json',
              data: $("#guard_orden").serialize(),
              success: function(data){
                console.log(data);
                if(data=='false'){
                  //console.log(data);
                  //$('#alertms').empty().html('Ingrese un Procedimiento: Endoscopias Digestivas, Colonoscopia, Intestino Delgado,Ecoendoscopias,Cpre.');
                  //$(".m1").removeClass("oculto");

                }else {
                  
                  $("#area_trabajo").html(data);
                }
                
              },

              error: function(data){
                 //console.log(data);
              }

        });

      $('#guarda_orden_endos').attr("disabled", true);

    }else {
      $("#select_proc").fadeIn(1000);
      $("#select_proc").fadeOut(3000);
    }  

}

</script>

<script type="text/javascript">
    
    $('.c{{$tipo_eda_diagnostica}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo_eda_diagnostica}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo_eda_diagnostica}}').removeAttr('disabled'); 
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
        
        $('.c{{$tipo_eda_diagnostica}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c{{$tipo_eda_diagnostica}}').removeAttr('disabled');
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

</script>

<script type="text/javascript">
    
    $('.p{{$tipo_colonoscopia_diagnostica}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select_col(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento_col').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.p{{$tipo_colonoscopia_diagnostica}}').attr('disabled','disabled');
        $('.p').removeAttr('disabled');

      }else{
        $('.p{{$tipo_colonoscopia_diagnostica}}').removeAttr('disabled'); 
        $('.p').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select_col(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento_col').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.p{{$tipo_colonoscopia_diagnostica}}').attr('disabled','disabled');
        $('.p').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.p{{$tipo_colonoscopia_diagnostica}}').removeAttr('disabled');
        $('.p').attr('disabled','disabled');  
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
        actualiza_select_col();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select_col();    
    }); 

</script>

<script type="text/javascript">
    
    $('.b{{$tipo_broncoscopia}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select_bronc(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento_bron').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.b{{$tipo_broncoscopia}}').attr('disabled','disabled');
        $('.b').removeAttr('disabled');

      }else{
        $('.b{{$tipo_broncoscopia}}').removeAttr('disabled'); 
        $('.b').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select_bronc(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento_bron').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.b{{$tipo_broncoscopia}}').attr('disabled','disabled');
        $('.b').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.b{{$tipo_broncoscopia}}').removeAttr('disabled');
        $('.b').attr('disabled','disabled');  
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
        actualiza_select_bronc();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select_bronc();    
    }); 

</script>



<script type="text/javascript">
    
    $('.t{{$tipo_enteroscopia}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select_enter(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento_enter').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.t{{$tipo_enteroscopia}}').attr('disabled','disabled');
        $('.t').removeAttr('disabled');

      }else{
        $('.t{{$tipo_enteroscopia}}').removeAttr('disabled'); 
        $('.t').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select_enter(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento_enter').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.t{{$tipo_enteroscopia}}').attr('disabled','disabled');
        $('.t').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.t{{$tipo_enteroscopia}}').removeAttr('disabled');
        $('.t').attr('disabled','disabled');  
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
        actualiza_select_enter();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select_enter();    
    }); 

</script>
<script type="text/javascript">
    
    $('.d{{$tipo_ecoendoscopia}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select_ecoend(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.d{{$tipo_ecoendoscopia}}').attr('disabled','disabled');
        $('.d').removeAttr('disabled');

      }else{
        $('.d{{$tipo_ecoendoscopia}}').removeAttr('disabled'); 
        $('.d').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select_ecoend(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.d{{$tipo_ecoendoscopia}}').attr('disabled','disabled');
        $('.d').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.d{{$tipo_ecoendoscopia}}').removeAttr('disabled');
        $('.d').attr('disabled','disabled');  
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
        actualiza_select_ecoend();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select_ecoend();    
    }); 

</script>

<script type="text/javascript">
    
    $('.r{{$tipo_cpre}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,  
    });

    function actualiza_select_cpre(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento_cpre').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.r{{$tipo_cpre}}').attr('disabled','disabled');
        $('.r').removeAttr('disabled');

      }else{
        $('.r{{$tipo_cpre}}').removeAttr('disabled'); 
        $('.r').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2_proc').select2({
            tags: false,  
        });
    }

    function quita_select_cpre(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento_cpre').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.r{{$tipo_cpre}}').attr('disabled','disabled');
        $('.r').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.r{{$tipo_cpre}}').removeAttr('disabled');
        $('.r').attr('disabled','disabled');  
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
        actualiza_select_cpre();
    }); 

    $(".select2_proc").on("select2:unselecting", function (evt) {

        quita_select_cpre();    
    }); 

</script>










