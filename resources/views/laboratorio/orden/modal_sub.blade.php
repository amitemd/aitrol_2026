<div class="modal-header">
    <div class="col-md-10"><h3>Crear detalle {{$examen->descripcion}}:</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
    
        
    
            
    <form id="frm_sub">
        
          
        <input type="hidden" name="id_orden" value="{{ $orden->id }}">
        <input type="hidden" name="id_examen" value="{{ $examen->id }}">
        <!--participantes-->
        <div id="dvalor1" class="form-group col-xs-12">
            <label for="valor1" class="col-md-4 control-label">Valor 1</label>
            <div class="col-md-7">
               
                <input id="valor1" type="text" class="form-control" name="valor1" value="" required autofocus">
                
                
            </div>
        </div>

        <div id="dvalor2" class="form-group col-xs-12">
            <label for="valor2" class="col-md-4 control-label">Valor 2</label>
            <div class="col-md-7">
               
                <input id="valor2" type="text" class="form-control" name="valor2" value="" required autofocus">
                
                
            </div>
        </div>

        <div id="dvalor3" class="form-group col-xs-12">
            <label for="valor3" class="col-md-4 control-label">Valor 3</label>
            <div class="col-md-7">
               
                <input id="valor3" type="text" class="form-control" name="valor3" value="" required autofocus">
                
                
            </div>
        </div>
             
          
    </form>

   <button class="btn btn-submit btn-sm btn-success">Guardar</button>
     
        
     
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">

   
    
    $(".btn-submit").click(function(e){

        $("#dvalor1").removeClass("has-error");
        $("#dvalor2").removeClass("has-error");
        $("#dvalor3").removeClass("has-error");
        $.ajax({
          type: 'post',
          url: '{{route('subresultado.store')}}', 
          headers: {'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_sub").serialize(),
          success: function(data){
            
            console.log(data);
            
            $('#sub_tabla{{$examen->id}}').empty().html(data);
            $('.close').click();
          },


          error: function(data){

            //alert("error");
            if(data.responseJSON.valor1!=null){
                $('#dvalor1').addClass("has-error");
                alert("Ingrese Valor1");
            }
            if(data.responseJSON.valor2!=null){
               $('#dvalor2').addClass("has-error");
               alert("Ingrese Valor2");
            }
            if(data.responseJSON.valor3!=null){
                $('#dvalor3').addClass("has-error");
                alert("Ingrese Valor3"); 
            }
            
             
          }
        });
    });

</script>