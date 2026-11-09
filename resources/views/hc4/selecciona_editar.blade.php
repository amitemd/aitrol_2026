
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    
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

</style>

<section style=" margin-left: 4px;margin-right: 4px;">  
<div style="border: 2px solid #004AC1;border-radius:8px;margin-left: 4px;margin-right: 4px; background-color: #56ABE3" class="container-fluid" >
  <div class="row">
    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
      <div class="box-header with-border" style="padding-right: 0px; padding-left: 0px">
        <span id="msn" style="color: white;"></span>
        <form id="form">
          <input type="hidden" name="paciente" value="{{$paciente->id}}">
          <input type="hidden" name="hcid" value="{{$hcid}}">
          <input id="tipo_procedimiento_buscador" type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
          <div id="cambio3" style="padding-right: 0px; padding-left: 0px" class="form-group col-md-12 {{ $errors->has('procedimiento') ? ' has-error' : '' }}">
            <center>
              <div style="background-color: #004AC1; color: white">
                <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">Procedimientos activos</label>
              </div>
            </center>
            <center>
              <div class="col-10" style="margin-left: -5px; margin-top: 10px; ">
                <select id="id_procedimiento" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 95.5%; " autocomplete="off">
                  @foreach($px as $procedimiento)
                      <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </center>
          </div>
        </form>               
        <center>

      
          <button class="btn btn-primary boton-proce" style="width: 200px" id="boton" onclick="guardar_procedimiento_select()">Crear Procedimiento
          </button>
        </center> 
      </div>
    </div>  
  </div>
</div>
</section>

<script type="text/javascript">
    
    $('.select2_proc').select2({
        tags: false,  
    });
     
    $(".select2_proc").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    }); 

    function guardar_procedimiento_select(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_procedimiento.editar_burbuja')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            //console.log(data);
            //alert("ok");
            if(data=='ok'){
              cargar_procedimientos("Procedimiento creado correctamente");
            }else{
              //alert("Ingrese el Procedimiento");
              $('#msn').text("Ingrese el Procedimiento");
            }
            
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

</script>
