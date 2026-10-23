<style type="text/css">
    .centered{
      text-align: center;
    }

    .boton-edit{
      font-size: 15px ;
      width: 100%;
      height: 100%;
      background-color: #004AC1;
      color: white;
      border-radius: 5px;
    }

</style>
<section class="content" style="padding-left: 0px; padding-right: 0px;padding-top: 0px;">
  <div class="modal-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;">
      <h4 class="modal-title" id="myModalLabel">Editar Gen&eacuterico</h4>
  </div>
  <div class="box-body" style="border: 2px solid #004AC1;border-radius: 3px;">
    <form method="POST" id="actualiza_generico">
      {{ csrf_field() }}
      <input type="hidden" name="idgenerico" value="{{$generico->id}}">
      <div class="row">
         <!--Nombre-->
        <div class="form-group col-md-6 col-sm-12 col-12 cl_nombre">
          <label for="nombre" class="control-label">Nombre</label>
            <input id="nombre" class="form-control input-sm" type="text" name="nombre" maxlength="100" value=@if(old('nombre')!='')"{{old('nombre')}}" @else"{{$generico->nombre}}" @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
            <span class="help-block">
              <strong id="str_nombre"></strong>
            </span>
        </div>
         <!--Descripcion-->
        <div class="form-group col-md-6 col-sm-12 col-12 cl_descripcion">
          <label for="descripcion" class="control-label">Descripci&oacuten</label>
            <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" maxlength="255" value=@if(old('descripcion')!='')"{{old('descripcion')}}"@else"{{$generico->descripcion}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
            <span class="help-block">
              <strong id="str_descripcion"></strong>
            </span>
        </div>
        <!--Estado-->
        <div class="form-group col-md-6 col-sm-12 col-12 cl_estado">
          <label for="estado" class="control-label">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
              <option @if(old('estado')!='') @if(old('estado')== '0') selected 
                      @endif @else @if($generico->estado == '0') selected @endif @endif value="0">INACTIVO</option>
              <option @if(old('estado')!='') @if(old('estado')== '1') selected 
                      @endif @else @if($generico->estado == '1') selected @endif @endif value="1">ACTIVO</option>
            </select>  
            <span class="help-block">
              <strong id="str_estado"></strong>
            </span>
        </div>
      </div>
      <br>
      <center>
        <div class="col-md-2 col-sm-5 col-5">
          <a id="crea" class="btn btn-info boton-edit"  style="color: white;" onclick="actualiza_generico();">  
            <span class="glyphicon glyphicon-floppy-disk"></span>
            Actualizar
          </a>
        </div>
      </center>
    </form> 
  </div> 
</section>

<script type="text/javascript">
    function actualiza_generico(){
         $.ajax({
            type: 'post',
            url:"{{route('actualiza_generico_hc4')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#actualiza_generico").serialize(),
            success: function(data){
              $("#area_trabajo").html(data);
            },
            error:  function(data){
                if(data.responseJSON.nombre!=null){
                  $('.cl_nombre').addClass('has-error');
                  $('#str_nombre').text(data.responseJSON.nombre);
                }
                if(data.responseJSON.descripcion!=null){
                  $('.cl_descripcion').addClass('has-error');
                  $('#str_descripcion').text(data.responseJSON.descripcion);
                }
                if(data.responseJSON.estado!=null){
                  $('.cl_estado').addClass('has-error');
                  $('#str_estado').text(data.responseJSON.estado);
                }
            }
        }); 
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
    
    });
</script>








