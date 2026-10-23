<div class="modal-header">
<center> <h3 style="margin:0;">ACTUALIZACION DE DATOS DEL CLIENTE</h3> </center>
</div>
<div class="modal-body">
  <form id="frm_datos_cliente_rc">
    <input type="hidden" name="id_orden" id="id_orden" value="{{$id_orden}}">
    <div class="box-body">
      
      <!--cedula factura-->
      @php
        $tipo = '';
        $cedula = '';
        $nombre = '';
        $direccion = '';
        $ciudad = '';
        $email = '';
        $telefono = '';

        if(!is_null($cliente)){
          $tipo = $cliente->tipo;
          $cedula = $cliente->identificacion;
          $nombre = $cliente->nombre;
          $direccion = $cliente->direccion_representante;
          $ciudad = $cliente->ciudad_representante;
          $email = $cliente->email_representante;
          $telefono = $cliente->telefono1_representante;
        }

      @endphp
      <center><h4 style="color: red">CLIENTE:@if(!is_null($cedula)) {{$cedula}} @endif: @if(!is_null($nombre)) {{$nombre}} @endif </h4></center>
      <div class="form-group col-md-3">
          <label for="cedula" class="control-label">Tipo de Identificacion</label>
          <select id="tipo_identificacion" name="tipo_identificacion" class="form-control" onchange="busca_clientes()">
            <option @if($tipo == '4') selected='selected' @endif value="4">Ruc</option>
            <option @if($tipo == '5') selected='selected' @endif value="5">Cedula</option>
            <option @if($tipo == '6') selected='selected' @endif value="6">Pasaporte</option>
            <option @if($tipo == '8') selected='selected' @endif value="8">Cedula Extranjera</option>
          </select>
      </div>
      <div class="form-group col-md-3">
          <label for="cedula" class="control-label">Cédula / RUC</label>
          <input id="cedula" maxlength="13" type="text" class="form-control" name="cedula" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  required autofocus autocomplete="off" onchange="busca_clientes(this.value);" value="@if($cedula != null){{$cedula}}@endif">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
          <label for="razon_social" class="control-label">Razon Social</label>
          <input id="razon_social" class="form-control" maxlength="100" type="text" name="razon_social" value="@if($nombre != null){{$nombre}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
      </div>
      <!--Direccion-->
        <div class="form-group col-md-3">
          <label for="direccion" class="control-label">Dirección</label>
          <input id="direccion" type="text" class="form-control" name="direccion" maxlength="70" value="@if($direccion != null){{$direccion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--ciudad-->
        <div class="form-group col-md-3">
            <label for="ciudad" class="control-label">Ciudad</label>
            <input id="ciudad" type="text" class="form-control" name="ciudad" maxlength="70" value="@if($ciudad != null){{$ciudad}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--email-->
        <div class="form-group col-md-6">
            <label for="email" class="control-label">E-mail</label>
            <input id="email" type="email" class="form-control" name="email" maxlength="70" value="@if($email != null){{$email}}@endif" required autofocus onchange="" >
        </div>
        <!--telefono-->
        <div class="form-group col-md-3">
            <label for="telefono" class="control-label">Teléfono</label>
            <input id="telefono" type="text" class="form-control" maxlength="10" name="telefono" value="@if($telefono != null){{$telefono}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        
        <div class="form-group col-md-3">
        <br>
          <button type="button" class="btn btn-info" id="boton_save" onclick="guardar_cliente()">Guardar</button>
        </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">

  var cedula = $('#cedula').val();
  cedula = cedula.trim();
  busca_clientes(cedula);

  function busca_clientes(cedula){
    $.ajax({
      type: 'get',
      url:"{{ url('laboratorio/externo/web/buscar_clientes') }}/"+cedula,
      datatype: 'json',
      success: function(data){
        console.log(data);
        if(data!='no'){
          $('#razon_social').val(data.nombre);
          $('#direccion').val(data.direccion);
          $('#ciudad').val(data.ciudad);
          $('#email').val(data.email);
          $('#telefono').val(data.telefono);
        }else{
          $('#razon_social').val('');
          $('#direccion').val('');
          $('#ciudad').val('');
          $('#email').val('');
          $('#telefono').val('');
        }
        console.log(data);
      },
      error: function(data){
        alert("error");
      }
    });
  }

  function guardar_cliente(){
    var cedula    = $('#cedula').val();
    var nombre    = $('#razon_social').val();
    var direccion = $('#direccion').val();
    var ciudad    = $('#ciudad').val();
    var email     = $('#email').val();
    var telefono  = $('#telefono').val();
    var error = '';
    if(cedula==''){
      error = error + "Ingrese la cedula\n";
    }
    if(nombre==''){
      error = error + "Ingrese el nombre\n";
    }
    if(direccion==''){
      error = error + "Ingrese la direccion\n";
    }
    if(ciudad==''){
      error = error + "Ingrese la ciudad\n";
    }
    if(email==''){
      error = error + "Ingrese el email\n";
    }
    if(telefono==''){
      error = error + "Ingrese el telefono\n";
    }
    if(error==''){
      $.ajax({
        type: 'post',
        url:"{{ route('nuevorecibocobro.actualizar_cliente') }}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#frm_datos_cliente_rc").serialize(),
        success: function(data){
          location.reload();
          //$('#modal_datosfacturas').modal('hide');;
          //cuadrar(data.id_orden);
        },
        error: function(data){
          if(data.responseJSON.valor!=null){
              alert(data.responseJSON.valor[0]);
          }
        }
      });
    }else{
      swal({
        title: "Error!",
        type: "error",
        html: error
      });
    }
  }
</script>
