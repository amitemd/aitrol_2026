<h4><b id="subnombre">{{$subt->nombre}}</b></h4>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" onclick="modificar();">Modificar</button>
</div>
<br><br>
<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style=" font-size: 12px;">
  <input type="hidden" id="id_subt" name="id_subt" value="{{$subt->id}}">
  <tbody>
    <tr>
      <td width="20%"><b>ID:</b></td>
      <td width="80%">{{$subt->id}}</td>
    </tr>
    <tr>
      <td><b>Codigo:</b></td>
      <td><input  style="width: 80%;font-size: 12px;" type="text" id="codigo" name="codigo" value="{{$subt->codigo}}" disabled=""></td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input  style="width: 80%;font-size: 12px;" type="text" id="nombre" name="nombre" value="{{$subt->nombre}}" disabled=""></td>
    </tr>
    <tr>
      <td><b>Tipo:</b></td>
      <td>
        <select id="grupo" name="grupo"  class="" style="width: 60%;" disabled required>
            <option></option>
            <option value="1" @if($subt->estado != 0) selected @endif >GRUPO</option>
            <option value="2" @if($subt->estado == 0) selected @endif >SUBGRUPO</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Padre:</b></td>
      <td>
        <select id="tipo_id" name="tipo_id"  class="" style="width: 60%;" disabled required>
            <option></option>
            @foreach(@$tipos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Estado:</b></td>
      <td><select name="estado" id="estado" disabled="">
            <option value="1" @if($subt->estado != 0) selected @endif >Activo</option>
            <option value="0" @if($subt->estado == 0) selected @endif >Inactivo</option>
          </select>
      </td>
    </tr>
  </tbody>
</table>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" id="guardar" onclick="guardar()" style="display: none;">Guardar</button>
</div>
<script type="text/javascript">

$('#tipo_id').val('{{ $subt->tipo_id }}');
$('#grupo').val('{{ 2 }}');

  function modificar() {
    // body...
    $("#nombre").removeAttr("disabled").focus();
    $("#grupo").removeAttr("disabled").focus();
    $("#tipo_id").removeAttr("disabled").focus();
    $("#estado").removeAttr("disabled").focus();
    $('#guardar').show();
  }

  function guardar(){ 
    id      = $('#id_subt').val();
    codigo  = $('#codigo').val();
    nombre  = $('#nombre').val();
    tipo_id = $('#tipo_id').val();
    estado  = $('#estado').val();
    $.ajax({
        type: 'post',
        url:"{{route('afGrupo.store')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: {'id':id,'codigo':codigo,'nombre':nombre, 'tipo_id':tipo_id, 'estado':estado},
        success: function(data){ 
          $("#nombre").prop('disabled', true);
          $("#codigo").prop('disabled', true);
          $("#tipo_id").prop('disabled', true);
          $("#estado").prop('disabled', true);
          $('#guardar').hide();
          Swal.fire(
                'Alerta!',
                'Elemento actualizado con éxito',
                'success'
            );
          recargar();
          abrir_arbol(); 
        },
        error: function(data){
          alert('Error revise su conexion de red');
          console.log(data);
        }
      });
  }
</script>
