
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

    .endoscopy-order-actions {
      display: flex;
      flex-wrap: nowrap;
      align-items: center;
      gap: 24px;
      margin: 0;
      overflow-x: auto;
      padding: 0 45px 15px;
    }

    .endoscopy-order-actions__item {
      flex: 1 1 0;
      min-width: 180px;
    }

    .endoscopy-order-actions .btn_ordenes {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100% !important;
      height: 46px !important;
      margin: 0 !important;
      padding: 8px 12px;
      font-size: 14px !important;
      line-height: 1.2;
      white-space: nowrap;
    }

    .form053-download-modal .modal-dialog {
      max-width: 440px;
      margin: 12vh auto 0;
    }

    .form053-download-modal .modal-content {
      overflow: hidden;
      border: 0;
      border-radius: 14px;
      box-shadow: 0 18px 46px rgba(8, 53, 88, .28);
    }

    .form053-download-modal .modal-header {
      color: #fff;
      border: 0;
      background: linear-gradient(118deg, #053d68 0%, #007bb8 58%, #12a4b4 100%);
    }

    .form053-download-modal .modal-header .close {
      color: #fff;
      opacity: .9;
    }

    .form053-download-options {
      display: flex;
      gap: 14px;
      padding: 10px 0;
    }

    .form053-download-options .btn {
      flex: 1 1 0;
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 13px 10px;
      border-radius: 9px;
      color: #fff !important;
      font-weight: 600;
      opacity: 1 !important;
      visibility: visible !important;
    }

    .form053-download-options .form053-download-excel {
      border-color: #16884e !important;
      background: #16884e !important;
    }

    .form053-download-options .form053-download-pdf {
      border-color: #cf3f35 !important;
      background: #cf3f35 !important;
    }

</style>
@php
  $aleatorio = rand();$idusuario = Auth::user()->id;
  $aceptacion = $co_aceptacion = $en_aceptacion = $ec_aceptacion = $cp_aceptacion = $br_aceptacion = 0;
@endphp
<div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
       Guardado Correctamente
</div>
<div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Seleccione un Procedimiento
</div>
<div id="req_val" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none;font-size: 14px">
        Indique si requiere valoración cardiológica
</div>
<div class="col-md-12" style="padding-left: 15px;padding-right: 30px;">

    @php
      $fecha = substr($data_orden->fecha_orden,0,10);
      $invert = explode( '-',$fecha);
      $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];

    @endphp
    <form method="POST" id="edit_orden">
        {{ csrf_field() }}
        <input type="hidden" name="id_ordenendos" value="{{$data_orden->id}}">
        <input type="hidden" name="id_pacient" value="{{$paciente->id}}">

      <div class="row">
        <div class="col-md-8">
          @if(!is_null($fecha_invert))
            <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
            <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
            </label>
          @endif
        </div>
        @if($ndoctor->id != 1307189140)
          <!--div class="col-md-4">
            <span style="font-family: 'Helvetica general';font-size: 16px;color: green;">Agregar Firma CRM</span>
            <input style="width:17px;height:17px;" type="checkbox" id="firma_doctor_rob_end" class="flat-green" name="firma_doctor_rob_end" value="1"
              @if(old('firma_doctor_rob_end')=="1")
                checked
              @elseif($data_orden->check_doctor =='1')
                checked
              @endif>
          </div-->
        @endif

        <div class="col-md-12">
          <div class="col-md-12" style="padding-left: 0;">
            <span style="font-family: 'Helvetica general';color: red;">REQUIERE VALORACIÓN CARDIOLÓGICA</span>
          </div>  
          <input style="width:17px;height:17px;" type="radio" id="necesita_valoracion_NO" class="flat-red" name="necesita_valoracion" value="NO"
           
            @if(old('necesita_valoracion')=="NO")
              checked
            @elseif($data_orden->necesita_valoracion =='NO')
              checked
            @endif
            
          <span style="color: red;font-weight: bold;"> NO</span>
          <input style="width:17px;height:17px;" type="radio" id="necesita_valoracion_SI" class="flat-red" name="necesita_valoracion" value="SI"
            @if($ndoctor->id == 1307189140)
              checked
            @else    
              @if(old('necesita_valoracion')=="SI")
                checked
              @elseif($data_orden->necesita_valoracion =='SI')
                checked
              @endif
            @endif  
            > <span style="color: red;font-weight: bold;"> SI</span> 
        </div>
        <div class="col-md-12">&nbsp;</div>
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
                  <textarea id="xmotivo_orden" name="xmotivo_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3">@if(!is_null($data_orden)){{$data_orden->motivo_consulta}}@endif
                  </textarea>
        </div>
      </div>
      <div class="col-12">&nbsp;</div>
        <div class="row">
            <div class="col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
              <div id="endos_resumen_orden{{$data_orden->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                @if(!is_null($data_orden))<?php echo $data_orden->resumen_clinico ?>@endif
              </div>
              <input type="hidden" name="endos_historia_clinica" id="endos_historia_clinica{{$data_orden->id}}{{$aleatorio}}">
            </div>
        </div>
        <br>
        <div class="row">
              <div class="col-md-12">
                <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                <div id="endos_diagnostico{{$data_orden->id}}{{$aleatorio}}"  style="width: 100%; border: 2px solid #004AC1;">
                  @if(!is_null($data_orden))<?php echo $data_orden->diagnosticos ?>@endif
                </div>
                <input type="hidden" name="endos_desc_diagnostico" id="endos_desc_diagnostico{{$data_orden->id}}{{$aleatorio}}">
              </div>
        </div>
        <div class="col-12">&nbsp;</div>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">ENDOSCOPIAS DIGESTIVAS
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_endosco" class="form-control input-sm select2_proc" name="x_procedimiento[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="1" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(1, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">COLONOSCOPIA-PROCTOLOGIA
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_colono" class="form-control input-sm select2_proc" name="x_procedimiento_colono[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="2" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(2, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <br>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">INTESTINO DELGADO
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_entero" class="form-control input-sm select2_proc" name="x_procedimiento_entero[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="3" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(3, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <br>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">ECOENDOSCOPIAS
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_ecoend" class="form-control input-sm select2_proc" name="x_procedimiento_ecoend[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="9" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(9, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <br>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">CPRE
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_cpre" class="form-control input-sm select2_proc" name="x_procedimiento_cpre[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="10" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(10, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
            <div style="background-color: #004AC1; color: white">
              <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">BRONCOSCOPIA
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <select id="id_procedimiento_bronc" class="form-control input-sm select2_proc" name="x_procedimiento_bronc[]" multiple="multiple" data-placeholder="Seleccione" data-grupo="14" style="width: 100%;" autocomplete="off">
              @foreach($procedimientos_seleccionados->get(14, collect()) as $procedimiento)
                <option selected value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <br>
        <div class="row">
            <div class="form-group col-md-12">
              <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
              <textarea id="xobservacion_orden" name="xobservacion_orden"  style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_endoscopica({{$data_orden->id}});">@if(!is_null($data_orden))<?php echo $data_orden->observacion_medica ?>@endif</textarea>
            </div>
        </div>
        <div class="row">
          <div class="col-md-12">
           <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION RECEPCI&OacuteN:</span><textarea id="xobservacion_recepcion" name="xobservacion_recepcion" style="width: 100%; border: 2px solid #004AC1;" rows="3" onchange="actualiza_orden_endoscopica({{$data_orden->id}});">@if(!is_null($data_orden))<?php echo $data_orden->observacion_recepcion ?>@endif</textarea>
          </div>
        </div>
        <br>
        <div class="endoscopy-order-actions">
          <div class="endoscopy-order-actions__item">
            <button type="button" class="btn btn-info btn_ordenes" onclick="actualiza_orden_endoscopica({{$data_orden->id}})"><span class="fa fa-floppy-o"></span><span>Guardar</span>
            </button>
          </div>
          <div class="endoscopy-order-actions__item">
            <button type="button" class="btn btn-info btn_ordenes" onclick="descargar({{$data_orden->id}});"><span class="glyphicon glyphicon-download-alt"></span><span>Descargar Orden</span>
            </button>
          </div>
          @if($data_orden->id_evolucion!=null)
          <div class="endoscopy-order-actions__item">
            <button type="button" class="btn btn-info btn_ordenes" onclick="formato_012({{$data_orden->id_evolucion}},{{$data_orden->id}},'area_trabajo_formato012{{$data_orden->id}}{{$aleatorio}}')"><span class="glyphicon glyphicon-list-alt"></span><span>Formato 012</span>
            </button>
          </div>
          <div class="endoscopy-order-actions__item">
            <button type="button" class="btn btn-info btn_ordenes" onclick="jQuery('#form053Download{{$data_orden->id}}{{$aleatorio}}').modal('show');"><span class="glyphicon glyphicon-list-alt"></span><span>Formulario 053</span>
            </button>
          </div>
          @endif
        </div>

    </form>

    <div class="modal fade form053-download-modal" id="form053Download{{$data_orden->id}}{{$aleatorio}}" tabindex="-1" role="dialog" aria-labelledby="form053DownloadTitle{{$data_orden->id}}{{$aleatorio}}">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="form053DownloadTitle{{$data_orden->id}}{{$aleatorio}}"><i class="glyphicon glyphicon-list-alt"></i>&nbsp; Formulario 053</h4>
          </div>
          <div class="modal-body">
            <p class="text-center">Seleccione el formato que desea descargar.</p>
            <div class="form053-download-options">
              <a class="btn form053-download-excel" style="display:inline-flex !important;align-items:center;justify-content:center;min-height:48px;padding:13px 10px;border:1px solid #16884e !important;border-radius:9px;background:#16884e !important;color:#fff !important;font-weight:700;opacity:1 !important;visibility:visible !important;" href="{{route('formato_053.index', ['id' => $data_orden->id])}}" target="_blank" rel="noopener">
                <i class="fa fa-file-excel-o"></i>&nbsp; Descargar Excel
              </a>
              <a class="btn form053-download-pdf" style="display:inline-flex !important;align-items:center;justify-content:center;min-height:48px;padding:13px 10px;border:1px solid #cf3f35 !important;border-radius:9px;background:#cf3f35 !important;color:#fff !important;font-weight:700;opacity:1 !important;visibility:visible !important;" href="{{route('formato_053.pdf', ['id' => $data_orden->id])}}" target="_blank" rel="noopener">
                <i class="fa fa-file-pdf-o"></i>&nbsp; Descargar PDF
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row" style="border-radius: 8px;" id="area_trabajo_formato012{{$data_orden->id}}{{$aleatorio}}"></div>
    <div class="row" style="border-radius: 8px;" id="area_trabajo_formato053{{$data_orden->id}}{{$aleatorio}}"></div>

</div>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">


    tinymce.init({
      selector: '#endos_resumen_orden{{$data_orden->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
        invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('endos_resumen_orden{{$data_orden->id}}{{$aleatorio}}');
            $('#endos_historia_clinica{{$data_orden->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('endos_resumen_orden{{$data_orden->id}}{{$aleatorio}}');
              $('#endos_historia_clinica{{$data_orden->id}}{{$aleatorio}}').val(ed.getContent());

          });
      }
    });


    tinymce.init({
      selector: '#endos_diagnostico{{$data_orden->id}}{{$aleatorio}}',
      inline: true,
      menubar: false,
        invalid_elements : 'table,tr,td',
      content_style: ".mce-content-body {font-size:14px;}",

      setup: function (editor) {
          editor.on('init', function (e) {

            var ed = tinyMCE.get('endos_diagnostico{{$data_orden->id}}{{$aleatorio}}');
            $('#endos_desc_diagnostico{{$data_orden->id}}{{$aleatorio}}').val(ed.getContent());
            //alert(ed.getContent());

          });
      },

      init_instance_callback: function (editor) {
          editor.on('Change', function (e) {

             var ed = tinyMCE.get('endos_diagnostico{{$data_orden->id}}{{$aleatorio}}');
              $('#endos_desc_diagnostico{{$data_orden->id}}{{$aleatorio}}').val(ed.getContent());

          });
      }
    });

</script>

<script type="text/javascript">

    //Funcion para actualizar Orden de Procedimiento Endoscopico
    function actualiza_orden_endoscopica(id_orden){

       var jprocedimiento_endos = $('#id_procedimiento_endosco').val();
       var jprocedimiento_colono = $('#id_procedimiento_colono').val();
       var jprocedimiento_int = $('#id_procedimiento_entero').val();
       var jprocedimiento_ecoend = $('#id_procedimiento_ecoend').val();
       var jprocedimiento_cpre = $('#id_procedimiento_cpre').val();
       var jprocedimiento_bron = $('#id_procedimiento_bronc').val();

       

        

        if ((jprocedimiento_endos != "")||(jprocedimiento_colono != "")||(jprocedimiento_int != "")||(jprocedimiento_ecoend != "")||(jprocedimiento_cpre != "")||(jprocedimiento_bron != "")){

            

            $.ajax({
                type: "POST",
                url: "{{route('actualiza.ordenhc4_proendoscopica')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                data: $("#edit_orden").serialize(),
                success: function(datahtml){
                  
                  if(datahtml==1){
                    
                    $("#req_val").fadeIn(1000);
                    $("#req_val").fadeOut(3000);
                  }else{
                    $("#alerta_guardado").fadeIn(1000);
                    $("#alerta_guardado").fadeOut(3000);  
                  }
                  

                },
                error: function(){

                  //console.log(data);

                }
            });
            
            

            

        }else {
          $("#select_proc").fadeIn(1000);
          $("#select_proc").fadeOut(3000);
        }

    };

</script>

<script type="text/javascript">

    $('.c1').removeAttr('disabled');

    $('.select2_proc').select2({
            tags: false,
    });

    function actualiza_select(){

      var seleccionados =  $('#id_procedimiento_endosco').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){

        //$('.c1').attr('disabled','disabled');
        $('.c').removeAttr('disabled');
        //$('.c18').removeAttr('disabled');

      }else{
        $('.c1').removeAttr('disabled');
        $('.c').attr('disabled','disabled');

        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_endosco').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_endosco').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.c1').attr('disabled','disabled');
        $('.c').removeAttr('disabled');
        //$('.c18').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c1').removeAttr('disabled');
        $('.c').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_endosco').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_endosco").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

        actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});

    });

    $("#id_procedimiento_endosco").on("select2:unselecting", function (evt) {
       quita_select();
       actualiza_orden_endoscopica({{$data_orden->id}});
    });

    @if($aceptacion > 0 )
      actualiza_select();
    @endif

</script>

<script type="text/javascript">

  $('input[type="checkbox"].flat-green').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  });
  $('input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  }); 

    $('.co2').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function co_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_colono').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.co2').attr('disabled','disabled');
        $('.co').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.co2').removeAttr('disabled');
        $('.co').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_colono').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function co_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_colono').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.co2').attr('disabled','disabled');
        $('.co').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.co2').removeAttr('disabled');
        $('.co').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_colono').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_colono").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        co_actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});
    });

    $("#id_procedimiento_colono").on("select2:unselecting", function (evt) {
       co_quita_select();
       actualiza_orden_endoscopica({{$data_orden->id}});
    });

    @if($co_aceptacion > 0 )
      //alert("base");
      co_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.en3').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function en_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_entero').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.en3').attr('disabled','disabled');
        $('.en').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.en3').removeAttr('disabled');
        $('.en').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_entero').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function en_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_entero').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.en3').attr('disabled','disabled');
        $('.en').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.en3').removeAttr('disabled');
        $('.en').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_entero').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_entero").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        en_actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});
    });

    $("#id_procedimiento_entero").on("select2:unselecting", function (evt) {
       en_quita_select();
       actualiza_orden_endoscopica({{$data_orden->id}});
    });

    @if($en_aceptacion > 0 )
      //alert("base");
      en_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.ec9').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function ec_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.ec9').attr('disabled','disabled');
        $('.ec').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.ec9').removeAttr('disabled');
        $('.ec').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_ecoend').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function ec_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_ecoend').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.ec9').attr('disabled','disabled');
        $('.ec').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.ec9').removeAttr('disabled');
        $('.ec').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_ecoend').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_ecoend").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        ec_actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});
    });

    $("#id_procedimiento_ecoend").on("select2:unselecting", function (evt) {
       ec_quita_select();
       actualiza_orden_endoscopica({{$data_orden->id}});
    });

    @if($ec_aceptacion > 0 )
      //alert("base");
      ec_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.cp10').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function cp_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_cpre').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.cp10').attr('disabled','disabled');
        $('.cp').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.cp10').removeAttr('disabled');
        $('.cp').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_cpre').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function cp_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_cpre').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.cp10').attr('disabled','disabled');
        $('.cp').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.cp10').removeAttr('disabled');
        $('.cp').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_cpre').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_cpre").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        cp_actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});
    });

    $("#id_procedimiento_cpre").on("select2:unselecting", function (evt) {
       cp_quita_select();
       actualiza_orden_endoscopica({{$data_orden->id}});
    });

    @if($cp_aceptacion > 0 )
      //alert("base");
      cp_actualiza_select();
    @endif

</script>

<script type="text/javascript">

    $('.br14').removeAttr('disabled');

    /*$('.select2_proc').select2({
            tags: false,
    });*/

    function br_actualiza_select(){

      var seleccionados =  $('#id_procedimiento_bronc').find(':selected');
      var longitud =  seleccionados.length;
      //alert(longitud);
      if(longitud>=1){
        //alert("mayor igual a 1");
        //$('.br14').attr('disabled','disabled');
        $('.br').removeAttr('disabled');

      }else{
        //alert("cero");
        $('.br14').removeAttr('disabled');
        $('.br').attr('disabled','disabled');
        //$('.c').prop('disabled','false');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_bronc').select2({
        tags: false,
      });

      //actualiza_orden_endoscopica({{$data_orden->id}});


    }

    function br_quita_select(){
      //alert("quita");
      var seleccionados =  $('#id_procedimiento_bronc').find(':selected');
      var longitud =  seleccionados.length;
      //alert("q"+longitud);
      if(longitud>1){

        $('.br14').attr('disabled','disabled');
        $('.br').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.br14').removeAttr('disabled');
        $('.br').attr('disabled','disabled');

      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');
      });

      $('#id_procedimiento_bronc').select2({
        tags: false,
      });


    }

    $("#id_procedimiento_bronc").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        br_actualiza_select();
        actualiza_orden_endoscopica({{$data_orden->id}});
    });

    $("#id_procedimiento_bronc").on("select2:unselecting", function (evt) {
       br_quita_select();
    });

    @if($br_aceptacion > 0 )
      //alert("base");
      br_actualiza_select();
      actualiza_orden_endoscopica({{$data_orden->id}});
    @endif

    $('.flat-red').on('ifChecked', function(event){
      actualiza_orden_endoscopica({{$data_orden->id}});
    });

    function descargar(id_or){
       window.open('{{url('imprimir/orden_hc4/endoscopico')}}/'+id_or,'_blank');  
    }


    // El catálogo se consulta bajo demanda. Las opciones seleccionadas ya
    // vienen precargadas desde el controlador en una sola consulta.
    $('.select2_proc').each(function() {
      var $select = $(this);
      var grupo = $select.data('grupo');

      if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
      }

      $select.off('select2:select select2:unselect select2:unselecting');
      $select.select2({
        width: '100%',
        tags: false,
        ajax: {
          url: "{{route('hc4.ordenes.procedimientos.buscar')}}",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {q: params.term || '', grupo: grupo};
          },
          processResults: function(data) {
            return {results: data.results || []};
          },
          cache: true
        }
      }).on('select2:select select2:unselect', function() {
        actualiza_orden_endoscopica({{$data_orden->id}});
      });
    });
</script>















