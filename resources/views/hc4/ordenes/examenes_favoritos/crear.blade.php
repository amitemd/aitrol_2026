<style type="text/css">  
  .alerta_correcto{
      position: absolute;
      z-index: 9999;
      top: 12%;
      right: 10%;
  }
</style>  
<div id="alerta_datos" class="alert alert-danger alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<input type="hidden" name="xid" id="xid" value="0">
<div class="content">
  <div class="box-header">
    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
      <div class="col-12" style="background-color: #004AC1; padding: 10px">
         <label class="box-title" style="color: white; font-size: 20px">Crear Lista de Exámenes Favoritos</label>
      </div> 
    </div>
  </div>
  <div class="box-body"> 
    <div class="row">
      <div class="col-2"><label >Ingresar Nombre del Perfil: </label></div>
      <div class="col-4"><input class="form-control" type="text" name="nombre" id="nombre" style="width: 100%" onchange="guardar_favorito()" maxlength="100"></div>
      <div class="col-1"><button type="button" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-floppy-disk"></span></button></div>
    </div>
    <br>
    <div id="xlistado">
      
    </div>  
  </div>
</div>    
<script type="text/javascript">

  function guardar_favorito(){
    var xid = $('#xid').val();
    if(xid=='0'){
      //alert("guarda");
      $.ajax({
        type: 'post',
        url:"{{route('hc4_examenes.guardar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: { nombre: $("#nombre").val() },
        success: function(data){
          if(data.estado=='err'){
            $('#alerta_datos').empty().html(data.respuesta);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          }
          if(data.estado=='ok'){
            $('#xid').val(data.respuesta);
            ingresar_examenes(data.respuesta);  
          }
          //$("#area_trabajo").html(data);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }else{
      //alert("actualiza");
      $.ajax({
        type: 'post',
        url:"{{route('hc4_examenes.actualizar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: { xid: xid, nombre: $("#nombre").val() },
        success: function(data){
          if(data.estado=='err'){
            $('#alerta_datos').empty().html(data.respuesta);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          }
          if(data.estado=='ok'){
            $('#xid').val(data.respuesta);
            ingresar_examenes(data.respuesta);  
          }
          //$("#area_trabajo").html(data);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
  }

  function ingresar_examenes(id){
    $.ajax({
      type: 'get',
      url:"{{url('hc4/laboratorio/examenes/favoritos/crear/guardar/listado')}}/"+id,
      datatype: 'json',
      success: function(data){
        $("#xlistado").empty().html(data);
        $("#alerta_ok").fadeIn(1000);
        $("#alerta_ok").fadeOut(3000);
      },
      error:  function(){
        alert('error al cargar');
      }
    });

  }

  

</script>