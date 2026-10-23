<div class="modal-header">
  <h3 style="margin:0;">Ingrese Datos de Factura.</h3>
</div>
<div class="modal-body">
  <form id="frm_datos_facturas">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="id_orden" value="{{ $orden->id }}">
      <input type="hidden" name="valor_orden" value="{{$orden->total_valor}}">
      <input type="hidden" name="valor_forma" value="{{$total_forma}}">
      <!--cedula factura-->
      @php
      $cedula_factura = '';
      $nombre_factura = '';
      $direccion_factura = '';
      $ciudad_factura = '';
      $email_factura = '';
      $telefono_factura = '';

      if(!is_null($cliente)){
      $cedula_factura = $cliente->cedula_factura;
      $nombre_factura = $cliente->nombre_factura;
      $direccion_factura = $cliente->direccion_factura;
      $ciudad_factura = $cliente->ciudad_factura;
      $email_factura = $cliente->email_factura;
      $telefono_factura = $cliente->telefono_factura;
      $tipo_doc= $cliente->tipo;
      }else{
      $tipo_doc=$orden->tipo_documento;
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
      }

      @endphp
      <h4 style="color: red">Paciente: {{$orden->id_paciente}}: {{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}} - Seguro: {{$orden->seguro->nombre}}</h4>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">Documento</label>
        <select id="documento" name="documento" class="form-control" onchange="busca_clientes()">
          <option @if($tipo_doc=="5" )selected @endif value="5">Cedula</option>
          <option @if($tipo_doc=="4" )selected @endif value="4">Ruc</option>
          <option @if($tipo_doc=="6" )selected @endif value="6">Pasaporte</option>
          <option @if($tipo_doc=="8" )selected @endif value="8">Identificación Extranjera</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">Cédula / RUC</label>
        <input id="cedula_factura" maxlength="13" type="text" class="form-control" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off" onchange="busca_clientes(this.value);" value="@if($orden->cedula_factura != null){{$orden->cedula_factura}}@else{{$cedula_factura}}@endif">
      </div>
      <!--primer nombre--> 
      <div class="form-group col-md-6">
        <label for="nombre_factura" class="control-label">Nombre</label>
        <input id="nombre_factura" class="form-control" maxlength="100" type="text" name="nombre_factura" value="@if($orden->nombre_factura != null){{$orden->nombre_factura}}@else{{$nombre_factura}} @endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Direccion-->
      <div class="form-group col-md-3">
        <label for="direccion_factura" class="control-label">Dirección</label>
        <input id="direccion_factura" type="text" class="form-control" name="direccion_factura" maxlength="70" value="@if($orden->direccion_factura != null){{$orden->direccion_factura}}@else{{$direccion_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--ciudad-->
      <div class="form-group col-md-3">
        <label for="ciudad_factura" class="control-label">Ciudad</label>
        <input id="ciudad_factura" type="text" class="form-control" name="ciudad_factura" maxlength="70" value="@if($orden->ciudad_factura != null){{$orden->ciudad_factura}}@else{{$ciudad_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--email-->
      <div class="form-group col-md-6">
        <label for="email_factura" class="control-label">E-mail</label>
        <input id="email_factura" type="text" class="form-control" name="email_factura" maxlength="70" value="@if($orden->email_factura != null){{$orden->email_factura}}@else{{$email_factura}}@endif" required autofocus onchange="">
      </div>
      <!--telefono-->
      <div class="form-group col-md-3">
        <label for="telefono_factura" class="control-label">Teléfono</label>
        <input id="telefono_factura" type="text" class="form-control" maxlength="10" name="telefono_factura" value="@if($orden->telefono_factura != null){{$orden->telefono_factura}}@else{{$telefono_factura}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Sucursal-->
      <div class="form-group col-md-4">
        <label for="sucursal_factura" class="control-label">Sucursal</label>
        <select id="sucursal_factura" name="sucursal_factura" onchange="obtener_caja();" class="form-control select2">
          <option value="">--Seleccionar--</option>
          <?php
          if (isset($sucursales) && count($sucursales) > 0) {
            foreach ($sucursales as $row) {
              echo '<option value="' . $row->id . '|' . $row->codigo_sucursal . '">' . $row->codigo_sucursal .'-'. $row->nombre_sucursal . '</option>';
            }
          }
          ?>
        </select> 
      </div>
      <!--Punto de emision-->
      <div class="form-group col-md-5">
        <label for="emision_factura" class="control-label">Punto de emisión</label>
        <select id="emision_factura" name="emision_factura" class="form-control select2">
          <option value="">--Seleccionar--</option>
        </select>
      </div>
      <div class="form-group col-md-12">
        <label for="emision_factura" class="control-label">Empresa</label><br>
        <label for="emision_factura" class="control-label" style="color: red;">{{ $sucursales->first()->empresa->razonsocial }}</label>
      </div>

      <div class="form-group col-md-12">
        <label for="emision_factura" class="control-label">Observación</label>
        <textarea class="form-control" id="observacion_factura" name="observacion_factura" rows="3"><?=$orden->observacion_app != ''?$orden->observacion_app:'';?></textarea>
      </div>

      <div class="form-group col-md-6">
        <br>
        <p style="color: red;font-size: 18px;"><b>Confirme El valor a Facturar es: @if($orden->cobrar_pac_pct < 100 ) {{ $orden->total_con_oda }} @else {{$orden->total_valor}} @endif</b>
        </p>
      </div>
      <div class="form-group col-md-3">
        <br>
        <button type="button" class="btn btn-info" id="boton_sri" onclick="guardar_info_factura()">Guardar Y Enviar al SRI</button>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

<script type="text/javascript">
  var cedula_factura = $('#cedula_factura').val();
  cedula_factura = cedula_factura.trim();
  busca_clientes(cedula_factura);

  function obtener_caja() {
    var id_sucursal = $("#sucursal_factura").val().split('|');
    $.ajax({
      type: 'post',
      url: "{{ route('caja.sucursal') }}",
      datatype: 'json',
      data: {
        _token: $('input[name=_token]').val(),
        'id_sucur': id_sucursal[0]
      },
      success: function(data) {
        if (data.value != 'no') {
          if (id_sucursal != 0) {
            $("#emision_factura").empty();
            $.each(data, function(key, registro) {
              var selected = '';
              if (registro.codigo_caja == 100)
                selected = 'selected';
              $("#emision_factura").append('<option value=' + registro.id + '|' + registro.codigo_caja + ' ' + selected + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '-' + registro.nombre_caja + '</option>');
            });
          } else {
            $("#emision_factura").empty();
          }
        }
      },
      error: function(data) {
        console.log(data);
      }
    })
  }

  function busca_clientes(cedula) {
    //console.log(cedula);
    var documento = $('#documento').val();
    if (documento == 4 || documento == 5) {
      if (!validarCedula(cedula)) {
        alert("Error en la cedula/Ruc");
        $('#boton_sri').attr("disabled", true);
      } else {
        $('#boton_sri').removeAttr("disabled");
      }
    } else {
      $('#boton_sri').removeAttr("disabled");
    }
    $.ajax({
      type: 'get',
      url: "{{ url('laboratorio/externo/web/buscar_clientes') }}/" + cedula,
      datatype: 'json',
      success: function(data) {
        //console.log(data);
        if (data != 'no') {
          $('#nombre_factura').val(data.nombre);
          $('#direccion_factura').val(data.direccion);
          $('#ciudad_factura').val(data.ciudad);
          $('#email_factura').val(data.email);
          $('#telefono_factura').val(data.telefono);
        }
        console.log(data);
      },
      error: function(data) {
        if (data.responseJSON.valor != null) {
          $('#dvalor').addClass('has-error');
          alert(data.responseJSON.valor[0]);
        }
      }
    });
  }

  function enviar_sri() {
    <?php
    if ($orden->cobrar_pac_pct < 100) {
    ?>
      //alert("aqui");
      //console.log("enviar sri");
      $.ajax({
        type: 'get',
        url: "{{route('facturalabs.enviar_sri2',['id' => $orden->id])}}",
        datatype: 'json',
        success: function(data) {
          //alert("succes");
          //window.open("{{asset('/contable/ventas/ride_pdf')}}/"+"{{$orden->comprobante}}"+"/"+"0993075000001"+"/"+"pdf", '_blank ');
          window.open("{{route('pdf_tributario',['id' => $orden->id])}}", '_blank ');
          //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";

        },
        error: function(data) {
          //alert("error");
          //console.log(data);
          //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";
        }
      });
    <?php
    } else {
    ?>
      $.ajax({
        type: 'get',
        url: "{{route('facturalabs.humanlabs_enviar_sri',['id' => $orden->id])}}",
        datatype: 'json',
        success: function(data) {
          //alert("succes");
          //window.open("{{asset('/contable/ventas/ride_pdf')}}/"+"{{$orden->comprobante}}"+"/"+"0993075000001"+"/"+"pdf", '_blank ');
          window.open("{{route('pdf_tributario',['id' => $orden->id])}}", '_blank ');
          //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";

        },
        error: function(data) {
          //alert("error");
          //console.log(data);
          //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";
        }
      });
    <?php
    }
    ?>
  }

  function cuadrar() {
    //alert("cuadrar");
    var id_orden = $('#id_orden').val();

    $.ajax({
      type: 'get',
      url: "{{route('facturalabs.cuadrar',['id' => $orden->id])}}",
      datatype: 'json',
      success: function(data) {
        //console.log("cuadra: " + data);
        if (data == 'ok') {
          swal.fire({
            title: 'Al emitir la orden, se enviará la factura electronica al Sri y al laboratorio con la fecha de emisión <?= date('Y-m-d'); ?>',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            buttons: true,

          }).then((result) => {
            if (result.value) {
              enviar_sri()
              $("#modal_datosfacturas").modal('hide');
              $('#examples2').DataTable().clear().destroy();
              bindTable();
              //location.reload();
            }
          })
        }
        if (data == 'no') {
          alert("No se puede enviar al SRI sin forma de pago");
        }
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function guardar_info_factura() {
    if ($('#sucursal_factura').val() == '' || $('#emision_factura').val() == '') {
      alert('Seleccione la sucursal y el punto de emisión');
      return;
    }
    var cedula_factura = $('#cedula_factura').val();
    var nombre_factura = $('#nombre_factura').val();
    var direccion_factura = $('#direccion_factura').val();
    var ciudad_factura = $('#ciudad_factura').val();
    var email_factura = $('#email_factura').val();
    var telefono_factura = $('#telefono_factura').val();
    var error = '';
    if (cedula_factura == '') {
      error = error + 'Ingrese la cedula';
    }
    if (nombre_factura == '') {
      error = error + 'Ingrese el nombre de la factura - ';
    }
    if (direccion_factura == '') {
      error = error + 'Ingrese la direccion de la factura - ';
    }
    if (ciudad_factura == '') {
      error = error + 'Ingrese la ciudad de la factura - ';
    }
    if (email_factura == '') {
      error = error + 'Ingrese el email de la factura - ';
    }
    if (telefono_factura == '') {
      error = error + 'Ingrese el telefono de la factura - ';
    }
    if (error == '') {
      $.ajax({
        type: 'post',
        url: "{{ route('facturalabs.guardar_info_factura') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#frm_datos_facturas").serialize(),
        success: function(data) {
          //$('#modal_datosfacturas').modal('hide');;
          cuadrar(data.id_orden);
        },
        error: function(data) {
          if (data.responseJSON.valor != null) {
            alert(data.responseJSON.valor[0]);
          }
        }
      });
    } else {
      alert(error);
    }
  }
</script>