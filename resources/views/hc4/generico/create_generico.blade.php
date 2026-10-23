<style type="text/css">
    .centered{
      text-align: center;
    }

    .boton-create{
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
      <h4 class="modal-title" id="myModalLabel">Crear Generico</h4>
  </div>
  <div class="box-body" style="border: 2px solid #004AC1;border-radius: 3px;">
    <form method="POST" id="guard_generico">
      {{ csrf_field() }}
      <div class="row">
        <!--Nombre-->
        <div class="form-group col-md-6 col-12 cl_nombre">
          <label for="nombre" class="control-label col-12">Nombre</label>
            <input id="nombre" class="form-control input-sm" type="text" name="nombre"  value="{{old('nombre')}}" maxlength="100" required autofocus>
            <span class="help-block">
                <strong id="str_nombre"></strong>
            </span>
        </div>
        <!--Estado-->
        <div class="form-group col-md-6 col-sm-12 col-12 cl_estado">
          <label for="estado" class="control-label">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
                <option @if(old('estado')== '1') selected @endif value="1">ACTIVO</option> 
                <option @if(old('estado')== '0') selected @endif value="0">INACTIVO</option>
            </select>  
            <span class="help-block">
              <strong id="str_estado"></strong>
            </span>
        </div>
        <!--Descripcion-->
        <div class="form-group col-md-12 col-12 cl_descripcion">
          <label for="descripcion" class="control-label col-12" >Descripci&oacuten</label>
           <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value="{{old('descripcion')}}" required autofocus>
          <span class="help-block">
              <strong id="str_descripcion"></strong>
          </span>
        </div>
      </div>
      <br>
      <div class="centered">
        <a id="id_crea_generico" class="btn btn-info boton-create col-md-2 col-4" style="color: white;" onclick="store_generico();">
            <span class="glyphicon glyphicon-floppy-disk"></span> 
            Crear Generico
        </a>
      </div>
    </form> 
  </div> 
</section>

<script type="text/javascript">

    function store_generico(){

        $('.cl_nombre').removeClass('has-error');
        $('#str_nombre').text('');

        $('.cl_descripcion').removeClass('has-error');
        $('#str_descripcion').text('');

        $.ajax({
            type: 'post',
            url:"{{route('guarda.generico')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#guard_generico").serialize(),
            success: function(data){
                //alert("!! MEDICINA ACTUALIZADA !!");
                //$("#area_trabajo").html(data);
                $("#area_trabajo").html(data);
                //location.href = '{{url('inicio/')}}'
                //location.href = "{{URL::previous()}}"
            },
            error: function(data){
              //alert('error al cargar');
                if(data.responseJSON.nombre!=null){
                  $('.cl_nombre').addClass('has-error');
                  $('#str_nombre').text(data.responseJSON.nombre);
                }
                if(data.responseJSON.descripcion!=null){
                  $('.cl_descripcion').addClass('has-error');
                  $('#str_descripcion').text(data.responseJSON.descripcion);
                }
            }
        }); 
    }

</script>

 

