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

</style>

<section style=" margin-left: 4px;margin-right: 4px;">  
<div style="border: 2px solid #004AC1;border-radius:8px;margin-left: 4px;margin-right: 4px;background-color: #56ABE3" class="container-fluid">
  <div class="row">
    <div class="col-md-12"  style="padding-left: 0px; padding-right: 0px">
      <div class="box-header with-border" style="padding-right: 0px; padding-left: 0px">
        <span id="msn" style="color: white;"></span>
        <form id="form">
            <input type="hidden" name="paciente" value="{{$paciente->id}}">
            <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
            <div id="cambio3" style="padding-right: 0px; padding-left: 0px" class="form-group col-md-12 {{ $errors->has('procedimiento') ? ' has-error' : '' }}">
              <center>
                <div style="background-color: #004AC1; color: white">
                  <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">Procedimientos Funcionales 
                  </label>
                </div>
              </center>
              <center>
                <div class="col-md-10" style="margin-left: -5px; margin-top: 10px">
                      <select id="id_procedimiento" class="form-control input-sm select2_proc_0" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione"
                      style="width: 95.5%;" autocomplete="off">
                          @foreach($px as $procedimiento)
                              @php 
                                  $clase = 'c'; 
                                  if(!is_null($procedimiento->grupo_procedimiento)){
                                      $clase = $clase.$procedimiento->grupo_procedimiento->tipo_procedimiento;
                                  }
                              @endphp    
                              <option disabled="disabled" class="{{$clase}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                          @endforeach
                      </select>
                </div>
              </center>
            </div>
        </form> 
        <center>              
          <button class="btn btn-primary boton-proce" style="width: 200px" id="boton" onclick="guardar_procedimiento_fun();">Crear Procedimiento
          </button> 
        </center>
      </div>
    </div>  
  </div>
</div>
</section>

<script type="text/javascript">
    
    $('.c{{$tipo}}').removeAttr('disabled');

    $('.select2_proc_0').select2({
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
      
      $('.select2_proc_0').select2({
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

    function guardar_procedimiento_fun(){
        $.ajax({
          type: 'post',
          url:"{{route('proc_fun.crear')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            //console.log(data);
            //alert("ok");
            if(data=='ok'){
              cargar_procedimiento_funcional("Procedimiento creado correctamente");  
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