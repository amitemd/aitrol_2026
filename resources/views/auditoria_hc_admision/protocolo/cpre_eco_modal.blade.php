<div class="col-md-12" style="background-color: #ffe6e6;">
  <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

  <span id="cpre_mensaje" style="color: red; ">  </span>


  <form id="cpre_form">

    <input  type="hidden"  name="hcid" value="{{ $hcid }}" >

    <h4>Procedimientos CPRE + ECO para Pacientes MSP Auditoria</h4>
    <div class="col-md-12" style="padding: 1px;">
      <label for="tcphallazgos" class="control-label">Hallazgo (CPRE + ECO)</label>
      <div id="tcphallazgos" style="border: solid 1px;" class="mce-content-body mce-edit-focus" contenteditable="true" spellcheck="false">@if(!is_null($cpre_eco))<?php echo $cpre_eco->hallazgos ?> @else <?php echo $texto ?>  @endif</div>
      <input type="hidden" name="cphallazgos" id="cphallazgos" required @if(!is_null($cpre_eco)) value="{{$cpre_eco->hallazgos}}" @endif >
    </div>

    <div class="col-md-12" style="padding: 1px;">
      <label for="tcpconclusion" class="control-label">Conclusión (CPRE + ECO)</label>
      <div id="tcpconclusion" style="border: solid 1px;">@if(!is_null($cpre_eco))<?php echo $cpre_eco->conclusion ?>@endif</div>
      <input type="hidden" name="cpconclusion" id="cpconclusion" required>
    </div>



  </form>    
  <div class="form-group col-md-12 col-md-offset-5">
    <div class="col-md-6 col-md-offset-4">
        <button id="cpre_guardar" class="btn btn-primary" onclick="guardar_cpre();" >
            Guardar
        </button>
    </div>
  </div>
</div>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
	//$(function () {

    $(document).ready(function() {
      
        $('#cpre_guardar').attr('disabled', 'disabled');
       
        tinymce.init({
              selector: '#tcphallazgos',
              inline: true,
              menubar: false,
              content_style: ".mce-content-body {font-size:14px;}",
              toolbar: [
                'undo redo | bold italic underline | styleselect fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
              ],
             
              setup: function (editor) {
                  editor.on('init', function (e) {
                    //alert("inicia tiny");
                     var ed = tinyMCE.get('tcphallazgos');
                      $("#cphallazgos").val(ed.getContent());
                      
                  });
                  },
      
        

                 init_instance_callback: function (editor) {
                  editor.on('Change', function (e) {
                  var ed = tinyMCE.get('tcphallazgos');
                  $("#cphallazgos").val(ed.getContent());

                  var cp_conclusion = $("#cpconclusion").val();
                  var cp_hallazgo = $("#cphallazgos").val();

                      if (cp_conclusion != "" && cp_hallazgo != ""){
                            //console.log ( cp_conclusion +'/'+ cp_hallazgo);
                            $('#cpre_guardar').removeAttr('disabled');
                      }
                   
                
                 });
             }
        });

      

        tinymce.init({
            selector: '#tcpconclusion',
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:14px;}",
            toolbar: [
              'undo redo | bold italic underline | styleselect fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
            ],
               
            setup: function (editor) {
                editor.on('init', function (e) {
                   var ed = tinyMCE.get('tcpconclusion');
                    $("#cpconclusion").val(ed.getContent());
                });
            },
          
            

            init_instance_callback: function (editor) {
                editor.on('Change', function (e) {
                    var ed = tinyMCE.get('tcpconclusion');
                    $("#cpconclusion").val(ed.getContent());
                    
                    var cp_conclusion = $("#cpconclusion").val();
                    var cp_hallazgo = $("#cphallazgos").val();

                        if (cp_conclusion != null && cp_hallazgo != null){
                              console.log ( cp_conclusion +'/'+ cp_hallazgo);
                              $('#cpre_guardar').removeAttr('disabled');
                        }
                  
                });
            }

        });

    });    



      function guardar_cpre(){

        //alert("ok");
        $.ajax({
          type: 'post',
          url:"{{route('auditoria_protocolo_cpre_eco.modal_crear_editar')}}", //CombinadoController->ingreso
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