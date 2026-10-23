
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
@php
    $variable_tiempo = rand(1, 9999999);
@endphp
<div class="modal fade" id="cpre_eco{{$variable_tiempo}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
<div class="row">
    @php
        if(!is_null($procedimiento->created_at)){
            $fecha_r =  Date('Y-m-d',strtotime($procedimiento->created_at));
        }else{
            $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\agenda::where('id', $procedimiento->historia->id_agenda)->first()->fechaini));
        }
    @endphp
    <form id="hc_protocolo{{$procedimiento->id}}" style="width: 100%;">
        <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
         <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
        <input type="hidden" name="id_procedimiento" value="{{$procedimiento->id}}">
        <input type="hidden" name="id_protocolo" value="<?php if (!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif;?>">
        <input type="hidden" name="tipo" value="{{$tipo}}">
        <div class="col-12">&nbsp;</div>
        <div class="row">
            <div class="col-6">

                <div class="col-12">
                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                </div>
                @php
                    $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
                @endphp
                @if(!is_null($adicionales->last()))
                    @php
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
                    @endphp
                    <div class="col-12">
                        <span>{{$texto}}
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
                    <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 95.5%; padding-left: 15px " autocomplete="off" onchange="autoguardado({{$procedimiento->id}})">
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
            </div>
            <div class="col-6">
                <div class="col-12" >
                    <!--<center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_proc_endos_plantilla', ['id' => $procedimiento->id.date('his') ])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar Plantilla
                            </a>
                        </div>
                    </center>-->
                </div>
            </div>
        </div>
        <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
          Seleccione un Procedimiento Endoscopico
        </div>
        <br>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Plantilla de Procedimientos</span>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7" style="width: 100%">
                    <select class="form-control select2_plantilla"  name="proc_com" style="width: 100%;">
                            <option value="">Todos</option>
                        @foreach($proc_completo as $value)
                            <option @if($value->id == $procedimiento_completo_plantilla) selected @endif value="{{$value->id}}">{{$value->nombre_general}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5" >
                    <a class="btn btn-info btn_ordenes" style="color: white; height: 100%; " onClick="cargar_plantilla('1',{{$procedimiento->id}}{{$variable_tiempo}}, {{$procedimiento->id}});">
                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                            <label style="font-size: 16px">Insertar Plantilla</label>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <br>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">Seguro</span>
            </div>
        <!-- DOCTOR TRAINING -->
        <!--VH. PREGUNTAR POR EL ESTADO ADMISIONADO -->
            @php
                $dr_training = Auth::user();
                $proc_training = Sis_medico\Hc_protocolo_training::where('id_hc_protocolo',$protocolo->id)->where('id_training',$dr_training->id)->first();
            @endphp
            @if($dr_training->training)
                <div class="col-6" >
                    <span style="font-family: 'Helvetica general';">Training </span>
                </div>
            @endif
        <!-- DOCTOR TRAINING -->
        </div>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">{{$hc_seguro->nombre}}</span>
            </div>
            <!-- DOCTOR TRAINING -->
            @if($dr_training->training)
            <div class="col-6">
                <input id="ch" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> Participe de este Procedimiento</span>
            </div>
            @endif
            <!-- DOCTOR TRAINING -->
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Hallazgos</span>
        </div>
        <div class="col-12">
            <div id="thallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif;?></div>
            <input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}">
        </div>
        <div class="col-12">&nbsp;</div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Conclusiones</span>
        </div>
        <div class="col-12">
            <div id="tconclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif;?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}">
        </div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">&nbsp;</span>
        </div>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
        </div>
        <div class="col-4">
            <select name="id_doctor_examinador" onchange="autoguardado({{$procedimiento->id}})">
                @foreach($doctores as $value)
                    <option @if(!is_null($procedimiento->id_doctor_examinador)) @if($procedimiento->id_doctor_examinador == $value->id) selected @endif @endif  value="{{$value->id}}">{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12" >
        <center>
        <div class="col-5" style="padding-top: 15px;text-align: center;">
            <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento({{$procedimiento->id}})" ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
        </center>
        </div>
    </form>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">


    $(document).ready(function(){

      $('.select2_plantilla').select2({
        tags: false
      });

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });


      $(".breadcrumb").append('<li class="active">Procedimientos</li>');

    });




     tinymce.init({
        selector: '#thallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}").val(ed.getContent());


            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}});
                @endif

            });
          }
    });

    tinymce.init({
        selector: '#tconclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}").val(ed.getContent());

            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$variable_tiempo}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}});
                @endif
            });
          }
    });
    function guardar_procedimiento(id){
        var jprocedimientos = $('#id_procedimiento').val();
        //console.log (jprocedimientos);

        if (jprocedimientos != ""){
            var entra = id;
                $.ajax({
                type: "POST",
                url: "<?php echo e(route('guardar.procedimiento_ecografia')); ?>",
                data: $("#hc_protocolo"+id).serialize(),
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                success: function(datahtml, entra){

                    $("#procedimiento"+id).html(datahtml);
                    //console.log(datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
                });
        }else {
            $("#select_proc").fadeIn(1000);
            $("#select_proc").fadeOut(3000);
            //alert("Seleccione un Procedimiento");
        }
    };

</script>
<script type="text/javascript">

    @php $dr_training = Auth::user(); @endphp

    @if($dr_training->training)

        $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '1']) }}",


              datatype: 'json',

              success: function(data_cpre){
                $('#train').removeClass('text-muted');
                $('#train').addClass('text-red');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

        $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '0']) }}",


              datatype: 'json',

              success: function(data_cpre){
                $('#train').removeClass('text-red');
                $('#train').addClass('text-muted');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

    @endif

    $('.c{{$tipo}}').removeAttr('disabled');

    $('.select2_proc').select2({
        tags: false,
    });

    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
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



    function cargar_plantilla(actualiza, id, id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica_plantilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_protocolo"+id_proc).serialize(),
          success: function(data){
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                alert("No Existe Plantilla Precargada !!");
                var tecnica = "";
            }

            if(actualiza=='1'){
                anterior1=  $('#hallazgos'+id).val();
                anterior2=  tinyMCE.get('thallazgos'+id).getContent();
                $('#hallazgos'+id).val(anterior1+tecnica);
                tinyMCE.get('thallazgos'+id).setContent(anterior2+tecnica);
            }
          },
          error: function(data){
             console.log(data);
          }
    });

    //guardar();
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

    @if($fecha_r == date('Y-m-d') )
        function autoguardado(id){
            var jprocedimientos = $('#id_procedimiento').val();
            //console.log (jprocedimientos);

            if (jprocedimientos != ""){
                var entra = id;
                    $.ajax({
                    type: "POST",
                    url: "{{route('guardar.procedimiento_ecografia_autoguardado')}}",
                    data: $("#hc_protocolo"+id).serialize(),
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: "html",
                    success: function(){
                        //console.log(datahtml);
                        $("#alerta_datos").fadeIn(1000);
                        $("#alerta_datos").fadeOut(3000);
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
        }
    @endif
</script>
