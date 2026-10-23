<div class="col-md-12" style="background-color: #ffffff;">
    <!--<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">-->
    <span id="cpre_mensaje" style="color: red; ">  </span>
     
    <form id="cpre_form">
      <input  type="hidden"  name="hcid" value="{{$hcid}}">
        <center><h4>Procedimientos CPRE + ECO para Pacientes MSP</h4></center>
        <div class="col-md-12" style="padding: 1px;">

          <label for="tcphallazgos" class="control-label">Hallazgo (CPRE + ECO)</label>
          <div id="tcphallazgos<?php echo e(date('his')); ?>" style="border: solid 1px;" class="mce-content-body mce-edit-focus" contenteditable="true" spellcheck="false">
            @if(!is_null($cpre_eco))
              <?php echo $cpre_eco->hallazgos ?>
            @else 
              <?php echo $texto ?>   
            @endif
          </div>
          <input type="hidden" name="cphallazgos" id="cphallazgos<?php echo e(date('his')); ?>" required @if(!is_null($cpre_eco)) value="{{$cpre_eco->hallazgos}}" @endif >
        </div>
        <div class="col-md-12" style="padding: 1px;">
          <label for="tcpconclusion" class="control-label">Conclusi&oacuten (CPRE + ECO)</label>
          <div id="tcpconclusion<?php echo e(date('his')); ?>" style="border: solid 1px;">
              @if(!is_null($cpre_eco))
                <?php echo $cpre_eco->conclusion ?>
              @endif
          </div>
          <input type="hidden" name="cpconclusion" id="cpconclusion<?php echo e(date('his')); ?>" required>
        </div>
    </form>  
    <center>  
    <div style="padding-top: 10px" class="form-group col-md-12 col-md-offset-5" class="close" data-dismiss="modal">
        <div class="col-md-6 col-md-offset-4">
            <button id="cpre_guardar<?php echo e(date('his')); ?>" class="btn btn-primary" onclick="guardar_cpre();" >
                Guardar
            </button>
        </div>
    </div>
    </center>
    
</div>


<!--<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>-->
<script type="text/javascript">
    //$(function () {
    $(document).ready(function() {
        $('#cpre_guardar<?php echo e(date('his')); ?>').attr('disabled', false);
       
        tinymce.init({
              selector: '#tcphallazgos<?php echo e(date('his')); ?>',
              inline: true,
              menubar: false,
              content_style: ".mce-content-body {font-size:14px;}",
              toolbar: [
                'undo redo | bold italic underline | styleselect fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
              ],
             
                setup: function (editor) {
                  editor.on('init', function (e) {
                    //alert("inicia tiny");
                     var ed = tinyMCE.get('tcphallazgos<?php echo e(date('his')); ?>');
                      $("#cphallazgos<?php echo e(date('his')); ?>").val(ed.getContent());
                  });
                  },

                 init_instance_callback: function (editor) {
                  editor.on('Change', function (e) {
                  var ed = tinyMCE.get('tcphallazgos<?php echo e(date('his')); ?>');
                  $("#cphallazgos<?php echo e(date('his')); ?>").val(ed.getContent());

                  var cp_conclusion = $("#cpconclusion<?php echo e(date('his')); ?>").val();
                  var cp_hallazgo = $("#cphallazgos<?php echo e(date('his')); ?>").val();

                      if (cp_conclusion != "" && cp_hallazgo != ""){
                        //console.log ( cp_conclusion +'/'+ cp_hallazgo);
                        $('#cpre_guardar<?php echo e(date('his')); ?>').removeAttr('disabled');
                      }
                });
             }
        });

      

        tinymce.init({
            selector: '#tcpconclusion<?php echo e(date('his')); ?>',
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:14px;}",
            toolbar: [
              'undo redo | bold italic underline | styleselect fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
            ],
               
            setup: function (editor) {
                editor.on('init', function (e) {
                   var ed = tinyMCE.get('tcpconclusion<?php echo e(date('his')); ?>');
                    $("#cpconclusion<?php echo e(date('his')); ?>").val(ed.getContent());
                });
            },
          
            init_instance_callback: function (editor) {
                editor.on('Change', function (e) {
                    var ed = tinyMCE.get('tcpconclusion<?php echo e(date('his')); ?>');
                    $("#cpconclusion<?php echo e(date('his')); ?>").val(ed.getContent());
                    
                    var cp_conclusion = $("#cpconclusion<?php echo e(date('his')); ?>").val();
                    var cp_hallazgo = $("#cphallazgos<?php echo e(date('his')); ?>").val();

                        if (cp_conclusion != null && cp_hallazgo != null){
                              console.log ( cp_conclusion +'/'+ cp_hallazgo);
                              $('#cpre_guardar<?php echo e(date('his')); ?>').removeAttr('disabled');
                        }
                });
            }
        });

    });    

      
      function guardar_cpre(){

        $.ajax({
          type: 'post',
          url:"{{route('protocolo_hc4_cpre_eco.modal_crear_editar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#cpre_form").serialize(),
          success: function(data){
            //console.log(data);
            $('#cpre_mensaje').text(data);
            //alert(data);
            
          },
          error: function(data){

           
            
          }
        });
      }


</script>