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
<div id="alerta_guardado{{$data_orden_fun->id}}" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
       Guardado Correctamente
</div>
<div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Seleccione un Procedimiento Funcional
</div>

<div class="col-md-12" style="padding-left: 15px;padding-right: 30px;">
  @php
    if(!is_null($paciente)){

      $seguro = Sis_medico\Seguro::find($paciente->id_seguro);

    }

      $fecha = substr($data_orden_fun->fecha_orden,0,10);
      $invert = explode( '-',$fecha);
      $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];

  @endphp

  <form method="POST" id="edit_orden_fun{{$data_orden_fun->id}}">
      {{ csrf_field() }}
      <input type="hidden" name="id_ordenfun" value="{{$data_orden_fun->id}}">
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
            <input style="width:17px;height:17px;" type="checkbox" id="firma_doctor_rob_fun" class="flat-red" name="firma_doctor_rob_fun" value="1"
              @if(old('firma_doctor_rob_fun')=="1")
                checked
              @elseif($data_orden_fun->check_doctor =='1')
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
          <textarea id="xmotivo_orden" name="xmotivo_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden_fun)){{$data_orden_fun->motivo_consulta}}@endif
          </textarea>
        </div>
      </div>
      <div class="col-12">&nbsp;</div>
      <div class="row">
            <div class="col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
              <div id="func_resumen_orden{{$data_orden_fun->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                @if(!is_null($data_orden_fun))<?php echo $data_orden_fun->resumen_clinico ?>@endif
              </div>
              <input type="hidden" name="func_historia_clinica" id="func_historia_clinica{{$data_orden_fun->id}}{{$aleatorio}}">
            </div>
      </div>
      <div class="row">
            <div class="col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
              <div id="func_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                @if(!is_null($data_orden_fun))<?php echo $data_orden_fun->diagnosticos ?>@endif
              </div>
              <input type="hidden" name="func_des_diagnostico" id="func_des_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}">
            </div>
      </div>
      <div class="col-12">&nbsp;</div>
      @php
        $x_orden_tipo_funcional= null;
        if(!is_null($data_orden_fun->id)){
          $x_orden_tipo_funcional = Sis_medico\Orden_Tipo::where('id_orden', $data_orden_fun->id)
                                                            ->where('id_grupo_procedimiento','18')
                                                            ->first();
        }
      @endphp
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS FUNCIONALES
            </label>
          </div>
        </div>
        <div class="col-md-12">
          <select id="id_procedimiento_func" class="form-control input-sm select2_proc" name="x_procedimiento_func[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" autocomplete="off">
            @php
             $fu_aceptacion = 0;
             $fu_validacion = null;
            @endphp
            @if(!is_null($x_orden_tipo_funcional))
              @php
                $proc_func = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo',$x_orden_tipo_funcional->id)->get();
              @endphp
                @foreach($proc_func as $value)
                  @php

                    $clase = 'fu';
                    if(!is_null($value->procedimiento->grupo_procedimiento)){
                      $clase = $clase.$value->procedimiento->id_grupo_procedimiento;
                    }

                    $fu_aceptacion++;


                  @endphp
                  <option disabled="disabled" class="{{$clase}}" selected value="{{$value->procedimiento->id}}">{{$value->procedimiento->nombre}}</option>
                @endforeach
            @endif

            @foreach($px as $value)
              @php

                $clase = 'fu';
                if(!is_null($value->grupo_procedimiento)){
                  $clase = $clase.$value->id_grupo_procedimiento;
                }

                if(!is_null($x_orden_tipo_funcional)){

                  $fu_validacion = \Sis_medico\Orden_Procedimiento::where('id_procedimiento', $value->id) ->where('id_orden_tipo',$x_orden_tipo_funcional->id)->first();

                  if(!is_null($fu_validacion)){
                    $fu_aceptacion++;
                  }

                }


              @endphp
              @if(is_null($fu_validacion))
                <option disabled="disabled" class="{{$clase}}" value="{{$value->id}}">{{$value->nombre}}</option>
              @endif
            @endforeach
          </select>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-12">
          <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
          <textarea id="xobservacion_orden" name="xobservacion_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden_fun))<?php echo $data_orden_fun->observacion_medica ?>@endif</textarea>
        </div>
      </div>
      <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span><textarea id="xobservacion_recepcion" name="xobservacion_recepcion" style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden_fun))<?php echo $data_orden_fun->observacion_recepcion ?>@endif</textarea>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OTROS PROCEDIMIENTOS:</span><textarea id="xotros" name="xotros" style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_funcional({{$data_orden_fun->id}});">@if(!is_null($data_orden_fun))<?php echo $data_orden_fun->otros?>@endif</textarea>
          </div>
      </div>
      <br>
      <div class="row">
          <div class="col-4">
            <center>
              <button style="font-size: 15px; margin-bottom: 15px; height: 80%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="actualiza_orden_funcional({{$data_orden_fun->id}})"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
              </button>
            </center>
          </div>
          <div class="col-4">
            <center>
              <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="descargar_orden_funcional({{$data_orden_fun->id}});"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;Descargar Orden
              </button>
            </center>
          </div>
          @if($data_orden_fun->id_evolucion!=null)
          <div class="col-4">
            <center>
              <button style="font-size: 14px; margin-bottom: 15px; height: 80%; width: 70%"  type="button" class="btn btn-info btn_ordenes" onclick="formato_012({{$data_orden_fun->id_evolucion}},{{$data_orden_fun->id}}, 'area_trabajo_formato012_pf{{$data_orden_fun->id}}{{$aleatorio}}')"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Formato 012
              </button>
            </center>
          </div>
          @endif
      </div>
  </form>
  <div class="row" style="border-radius: 8px;" id="area_trabajo_formato012_pf{{$data_orden_fun->id}}{{$aleatorio}}"></div>
</div>

<script type="text/javascript">

  tinymce.init({
      selector: '#func_resumen_orden{{$data_orden_fun->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('func_resumen_orden{{$data_orden_fun->id}}{{$aleatorio}}');
            $('#func_historia_clinica{{$data_orden_fun->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('func_resumen_orden{{$data_orden_fun->id}}{{$aleatorio}}');
              $('#func_historia_clinica{{$data_orden_fun->id}}{{$aleatorio}}').val(ed.getContent());

          });
      }
    });


  tinymce.init({
      selector: '#func_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
      invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('func_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}');
            $('#func_des_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('func_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}');
              $('#func_des_diagnostico{{$data_orden_fun->id}}{{$aleatorio}}').val(ed.getContent());

          });
      }
  });

</script>

<script type="text/javascript">

    $('.fu18').removeAttr('disabled');

    $('.select2_proc').select2({
            tags: false,
    });

    function fu_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_func').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.fu18').attr('disabled','disabled');
        $('.fu').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.fu18').removeAttr('disabled');
        $('.fu').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('.select2_proc').select2({
        tags: false,
      });


    }

    function fu_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_func').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.fu18').attr('disabled','disabled');
        $('.fu').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.fu18').removeAttr('disabled');
        $('.fu').attr('disabled','disabled');

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
        fu_actualiza_select();
        actualiza_orden_funcional({{$data_orden_fun->id}});

    });

    $(".select2_proc").on("select2:unselecting", function (evt) {
       fu_quita_select();

    });

    @if($fu_aceptacion > 0 )
      //alert("base");
      fu_actualiza_select();
      actualiza_orden_funcional({{$data_orden_fun->id}});
    @endif

</script>

<script type="text/javascript">

    //Funcion para actualizar Orden de Procedimiento Funcional
    function actualiza_orden_funcional(id_orden){

      var jprocedimientos = $('#id_procedimiento_func').val();

      if (jprocedimientos != ""){
        $.ajax({
            type: "POST",
            url: "{{route('actualiza.ordenhc4_profuncional')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "html",
            data: $("#edit_orden_fun"+id_orden).serialize(),
            success: function(datahtml){

              $("#alerta_guardado{{$data_orden_fun->id}}").fadeIn(1000);
              $("#alerta_guardado{{$data_orden_fun->id}}").fadeOut(3000);

            },
            error:  function(){

               //console.log();
            }
        });

      }else {
        $("#select_proc").fadeIn(1000);
        $("#select_proc").fadeOut(3000);
      }

      function descargar_orden_funcional(id_or){
        window.open('{{url('imprimir/orden_hc4/funcional')}}/'+id_or,'_blank');  
      }


    };



</script>


