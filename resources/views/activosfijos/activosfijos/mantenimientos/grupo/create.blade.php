<h4><b id="subnombre">Crear Nuevo</b></h4>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" onclick="guardar();">Guardar</button>
</div>
<br><br>
<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style=" font-size: 12px;">
  <input type="hidden" id="id_subt" name="id_subt" value="">
  <tbody>
    <tr>
      <td width="20%"><b>ID:</b></td>
      <td width="80%"></td>
    </tr>
    <tr>
      <td><b>Codigo:</b></td>
      <td><input  style="width: 80%;font-size: 12px;" type="text" id="codigo" name="codigo" value="" ></td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input  style="width: 80%;font-size: 12px;" type="text" id="nombre" name="nombre" value="" ></td>
    </tr>
    <tr>
      <td><b>Tipo:</b></td>
      <td>
        <select id="grupo" name="grupo"  class="" style="width: 60%;" disabled required>
            <option></option>
            <option value="1" >GRUPO</option>
            <option value="2" selected >SUBGRUPO</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Padre:</b></td>
      <td>
        <select id="tipo_id" name="tipo_id"  class="" style="width: 60%;" required>
            <option></option>
            @foreach(@$tipos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
        </select>
      </td>
    </tr>
    {{-- <tr>
      <td><b>Estado:</b></td>
      <td><select name="estado" id="estado" >
            <option value="1" >Activo</option>
            <option value="0" >Inactivo</option>
          </select>
      </td>
    </tr> --}}
  </tbody>
</table>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" id="guardar" onclick="guardar()" style="display: none;">Guardar</button>
</div>
<script type="text/javascript">

  function modificar() {
    // body...
    $("#nombre").removeAttr("disabled").focus();
    $("#grupo").removeAttr("disabled").focus();
    $("#tipo_id").removeAttr("disabled").focus();
    $("#estado").removeAttr("disabled").focus();
    $('#guardar').show();
  }

  function guardar(){  
    codigo  = $('#codigo').val();
    nombre  = $('#nombre').val();
    tipo_id = $('#tipo_id').val(); 
    $.ajax({
        type: 'post',
        url:"{{route('afGrupo.store')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: {'nombre':nombre, 'tipo_id':tipo_id},
        success: function(data){ 
            console.log(data);
            $("#nombre").prop('disabled', true);
            $("#codigo").prop('disabled', true); 
            $("#tipo_id").prop('disabled', true); 
            $('#guardar').hide();
            $("#id_subt").val(data.id);
            $("#nombre").val(data.nombre);
            $("#codigo").val(data.codigo); 
            $("#tipo_id").val(data.tipo_id); 
            recargar();
            Swal.fire(
                'Alerta!',
                'Elemento guardado con éxito',
                'success'
            ); 
        },
        error: function(data){
          //alert('Error revise su conexion de red');
          console.log(data);
        }
      });
  }

 
</script>
