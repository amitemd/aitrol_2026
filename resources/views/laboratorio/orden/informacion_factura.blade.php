
    
<div class="modal-header">
  <h5 class="modal-title" id="exampleModalLabel">Información de Factura</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="row">
  <form id="frm_datos_facturas">
    
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="id_orden" value="{{ $orden->id }}">
      <!--cedula factura-->
      @php
        $cedula_factura = '';
        $nombre_factura = '';
        $direccion_factura = '';
        $ciudad_factura = '';
        $email_factura = '';
        $telefono_factura = '';

        
          $cedula_factura = $orden->id_paciente;
          $nombre_factura = $orden->paciente->nombre1;
          if($orden->paciente->nombre2!='(N/A)' && $orden->paciente->nombre2!='N/A'){
            $nombre_factura = $nombre_factura.' '.$orden->paciente->nombre2;
          }
          $nombre_factura = $nombre_factura.' '.$orden->paciente->apellido1;
          if($orden->paciente->apellido2!='(N/A)' && $orden->paciente->apellido2!='N/A'){
            $nombre_factura = $nombre_factura.' '.$orden->paciente->apellido2;
          }
          $direccion_factura = $orden->paciente->direccion;
          $ciudad_factura = $orden->paciente->ciudad;
          $email_factura = $orden->paciente->usuario->email;
          $telefono_factura = $orden->paciente->telefono1;


      @endphp
      <div class="form-group col-md-3" style="margin-bottom: 1px;">
          <label for="cedula_factura" class="control-label">Documento</label>
          <select id="documento" name="documento" class="form-control" onchange="busca_clientes()">
            <option value="5">Cedula</option>
            <option value="4">Ruc</option>
            <option value="6">Pasaporte</option>
            <option value="8">Identificación Extranjera</option>
          </select>
      </div>
      <div class="form-group col-md-3" style="margin-bottom: 1px;">
          <label for="cedula_factura" class="control-label">Cédula / RUC</label>
          <input id="cedula_factura" maxlength="13" type="text" class="form-control" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  required autofocus autocomplete="off" onchange="busca_clientes(this.value);" value="@if($orden->cedula_factura != null){{$orden->cedula_factura}}@else{{$cedula_factura}}@endif">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6" style="margin-bottom: 1px;">
          <label for="nombre_factura" class="control-label">Nombre</label>
          <input id="nombre_factura" class="form-control" maxlength="100" type="text" name="nombre_factura" value="@if($orden->nombre_factura != null){{$orden->nombre_factura}}@else{{$nombre_factura}} @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
      </div>
      <!--Direccion-->
        <div class="form-group col-md-3" style="margin-bottom: 1px;">
          <label for="direccion_factura" class="control-label">Dirección</label>
          <input id="direccion_factura" type="text" class="form-control" name="direccion_factura" maxlength="70" value="@if($orden->direccion_factura != null){{$orden->direccion_factura}}@else{{$direccion_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--ciudad-->
        <div class="form-group col-md-3" style="margin-bottom: 1px;">
            <label for="ciudad_factura" class="control-label">Ciudad</label>
            <input id="ciudad_factura" type="text" class="form-control" name="ciudad_factura" maxlength="70" value="@if($orden->ciudad_factura != null){{$orden->ciudad_factura}}@else{{$ciudad_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>
        <!--email-->
        <div class="form-group col-md-6" style="margin-bottom: 1px;">
            <label for="email_factura" class="control-label">E-mail</label>
            <input id="email_factura" type="text" class="form-control" name="email_factura" maxlength="70" value="@if($orden->email_factura != null){{$orden->email_factura}}@else{{$email_factura}}@endif" required autofocus onchange="" >
        </div>
        <!--telefono-->
        <div class="form-group col-md-3">
            <label for="telefono_factura" class="control-label">Teléfono</label>
            <input id="telefono_factura" type="text" class="form-control" maxlength="10" name="telefono_factura" value="@if($orden->telefono_factura != null){{$orden->telefono_factura}}@else{{$telefono_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" >
        </div>

        <div class="form-group col-md-3">
          <br>
          <button type="button" class="btn btn-info" id="boton_sri" onclick="guardar_info_factura()">Guardar</button>
        </div>
    
  </form>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  <!--button type="button" class="btn btn-primary">Save changes</button-->
</div>
<script type="text/javascript">
  var cedula_factura = $('#cedula_factura').val();
  cedula_factura = cedula_factura.trim();
  busca_clientes(cedula_factura);

  function busca_clientes(cedula){
    //console.log(cedula);
    var documento = $('#documento').val();
      if (documento == 4 || documento == 5) {
        if(!validarCedula(cedula)){
            alert("Error en la cedula/Ruc");
            $('#boton_sri').attr("disabled", true);
          }else{
          $('#boton_sri').removeAttr("disabled");
        }
      }else{
        $('#boton_sri').removeAttr("disabled");
      }


    $.ajax({
      type: 'get',
      url:"{{ url('laboratorio/externo/web/buscar_clientes') }}/"+cedula,
      datatype: 'json',
      success: function(data){
        console.log(data);
        if(data!='no'){
          $('#nombre_factura').val(data.nombre);
          $('#direccion_factura').val(data.direccion);
          $('#ciudad_factura').val(data.ciudad);
          $('#email_factura').val(data.email);
          $('#telefono_factura').val(data.telefono);
        }
        console.log(data);
      },


      error: function(data){

        if(data.responseJSON.valor!=null){
            $('#dvalor').addClass('has-error');
            alert(data.responseJSON.valor[0]);
        }


      }
    });
  }

  function guardar_info_factura(){

    var cedula_factura    = $('#cedula_factura').val();
    var nombre_factura    = $('#nombre_factura').val();
    var direccion_factura = $('#direccion_factura').val();
    var ciudad_factura    = $('#ciudad_factura').val();
    var email_factura     = $('#email_factura').val();
    var telefono_factura  = $('#telefono_factura').val();
    var error = '';
    if(cedula_factura==''){
      error = error + 'Ingrese la cedula';
    }
    if(nombre_factura==''){
      error = error + 'Ingrese el nombre de la factura - ';
    }
    if(direccion_factura==''){
      error = error + 'Ingrese la direccion de la factura - ';
    }
    if(ciudad_factura==''){
      error = error + 'Ingrese la ciudad de la factura - ';
    }
    if(email_factura==''){
      error = error + 'Ingrese el email de la factura - ';
    }
    if(telefono_factura==''){
      error = error + 'Ingrese el telefono de la factura - ';
    }
    if(error==''){
      $.ajax({
        type: 'post',
        url:"{{ route('facturalabs.guardar_info_factura') }}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#frm_datos_facturas").serialize(),
        success: function(data){
          alert("Información de Factura Actualizada");
          location.reload();
          //crear_orden_venta('id'); PASAR AL BOTON CREAR FORMA DE PAGO GASTROCLINICA

        },

        error: function(data){

          if(data.responseJSON.valor!=null){

              alert(data.responseJSON.valor[0]);
          }


        }
      });
    }else{
      alert(error);
    }

  }  
</script>
   
    
    