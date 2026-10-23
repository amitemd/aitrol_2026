 

<div class="modal-header">
    <div class="col-md-10"><h3>MANOMETRÍA ESOFAGICA</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-body">   
  <div class="box-body">
       
    <form class="form-horizontal" id="form">
      {{ csrf_field() }}

      <input  name="id_paciente" id="id_paciente" type="text" class="hidden" value="{{$id_paciente}}">      
      <!--Fecha de Creacion -->
      <div class="form-group col-xs-10">
          <label for="fecha_creacion" class="col-md-5 texto">Fecha de Creación:</label>
          <div class="col-md-7">
              <input id="fecha_creacion" type="date" class="form-control" name="fecha_creacion" value="{{ old('fecha_creacion') }}" required autofocus>
          </div>
      </div>
    </form>
    <br><br><br><br>
    <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
      <center>
        <div class="col-md-6 col-md-offset-2">
          <div class="col-md-7">
            <button type="button" class="btn btn-primary" onclick="guardar_procedimiento_func_mano_eso();"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
          </div>
        </div>
      </center>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
  </div>
</div>

<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

  

  function guardar_procedimiento_func_mano_eso(){

    $.ajax({
      type: 'post',
      url:"{{route('proc_fun.mano_esofagica')}}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#form").serialize(),
      success: function(data){
          
        if(data=='ok'){
          
          swal("Correcto!","Se creo el Correctamente el Procedimiento");

          $("#mano_esof").modal('hide');
        
        }else{
             
          $('#msn').text("Ingrese el Procedimiento");
        
        }
            
      },
      error: function(data){
        //console.log(data);
      }
    });
  
  }




</script>


