<h4>Catalogo de Plan de Cuentas: <b id="subnombre">{{$cuenta->nombre}}</b></h4>
<h6>Información General</h6>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" onclick="modificar();">Modificar</button>
</div>
<br><br>
<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style=" font-size: 12px;">
  <input type="hidden" id="id_cuenta" name="id_cuenta" value="{{$cuenta->id}}">
  <tbody>
    <tr>
      <td width="20%"><b>Codigo:</b></td>
      <td width="80%">{{$cuenta->pempresa->plan}}</td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input  style="width: 80%;font-size: 12px;" type="text" id="nombre" name="nombre" value="{{$cuenta->pempresa->nombre}}" disabled=""></td>
    </tr>
    <tr>
      <td><b>Tipo:</b></td>
      <td><select name="tipo" id="tipo" disabled="">
            <option value="1" @if($cuenta->pempresa->estado == 1) selected @endif >Grupo</option>
            <option value="2" @if($cuenta->pempresa->estado == 2) selected @endif >Detalle</option>
          </select>
      </td>
    </tr>
    <tr>
      <td><b>Estado:</b></td>
      <td><select name="tipo2" id="tipo2" disabled="">
            <option value="3" @if($cuenta->pempresa->estado != 0) selected @endif >Activo</option>
            <option value="0" @if($cuenta->pempresa->estado == 0) selected @endif >Inactivo</option>
          </select>
      </td>
    </tr>
    <tr>
      <td><b>Naturaleza:</b></td>
      <td><select name="naturaleza" id="naturaleza" disabled="">
            <option value="1" @if($cuenta->naturaleza == 1) selected @endif >Positivo</option>
            <option value="0" @if($cuenta->naturaleza == 0) selected @endif >Negativo</option>
          </select>
      </td>
    </tr>
    <tr>
      <td><b>Calcula en:</b></td>
      <td><select name="naturaleza_2" id="naturaleza_2" disabled="">
            <option value="1" @if($cuenta->pempresa->naturaleza_2 == 1) selected @endif >Haber</option>
            <option value="0" @if($cuenta->pempresa->naturaleza_2 == 0) selected @endif >Debe</option>
          </select>
      </td>
      <tr>
      <td><b>Cierre de año:</b></td>
      <td><select name="cierre_ano" id="cierre_ano" disabled="">
            <option value="1" @if($cuenta->pempresa->estado_cierre_ano == 1) selected @endif>Si</option>
            <option value="0" @if($cuenta->pempresa->estado_cierre_ano == 0) selected @endif >No</option>
          </select>
      </td>
    </tr>
  </tbody>
</table>
<div class="col-md-3 col-md-offset-9">
  <button type="button" class="btn btn-xs btn-primary" id="guardar" onclick="guardar()" style="display: none;">Guardar</button>
</div>
<script type="text/javascript">
  function modificar() {
    // body...
    $("#nombre").removeAttr("disabled").focus();
    $("#tipo").removeAttr("disabled").focus();
    $("#tipo2").removeAttr("disabled").focus();
    $("#naturaleza").removeAttr("disabled").focus();
    $("#naturaleza_2").removeAttr("disabled").focus();
    $("#cierre_ano").removeAttr("disabled").focus();
    $('#guardar').show();
  }
  function guardar(){

    codigo = $('#id_cuenta').val();
    nombre = $('#nombre').val();
    tipo = $('#tipo').val();
    tipo2 = $('#tipo2').val();
    naturaleza = $('#naturaleza').val();
    naturaleza_2 = $('#naturaleza_2').val();
    cierre = $('#cierre_ano').val();

    console.log(cierre);

    $.ajax({
        type: 'post',
        url:"{{route('plan_cuentas.guardar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: {'codigo':codigo,'nombre':nombre, 'tipo':tipo, 'tipo2':tipo2, 'naturaleza':naturaleza, 'naturaleza_2':naturaleza_2, 'cierre_ano': cierre},
        success: function(data){
          if(data == 'ok'){
            $("#nombre").prop('disabled', true);
            $("#subnombre").html(nombre);
            $("#tipo2").prop('disabled', true);
            $("#tipo").prop('disabled', true);
            $("#naturaleza").prop('disabled', true);
            $("#naturaleza_2").prop('disabled', true);
            $('#guardar').hide();
          }else{
            alert(data);
          }

        },
        error: function(data){
          alert('Error revise su conexion de red');
          console.log(data);
        }
      });
  }
</script>
