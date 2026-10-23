<div class="modal-header">
  <h3 style="margin:0;">Factura Agrupada</h3>
</div>
<div class="modal-body">
  @php
  $rolUsuario = Auth::user()->id_tipo_usuario;
  @endphp
  <div class="row">
    <div class="col-md-3">
      <a onclick="eliminar();" id="btn_elimina_sesion" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
    </div>
    <div class="col-md-3">
      <a onclick="datos_factura_agrupada();" class="btn btn-success">Datos Factura</a>
    </div>
  </div>
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example4" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr>
              <th>Del</th>
              <th>nro</th>
              <th>Id</th>
              <th>Fecha</th>
              <th>Nombres</th>
              <th>Seguros</th>
              <th>Cantidad</th>
              <th>Valor</th>
              <th>Descuento</th>
              @if($rolUsuario=='20' || $rolUsuario=='1')
              <th>Valor Publico/forma de Pago</th>
              @endif
            </tr>
          </thead>
          <tbody>@php $n = 0; $totalf = 0; $descf =0; @endphp
            @if(!is_null($agrup2) )
            @foreach($agrup2 as $value)
            @php
            $orden = Sis_medico\Examen_Orden::find($value);
            $n ++;
            $totalf = $totalf + $orden->total_valor;
            $descf = $descf + $orden->descuento_valor;
            @endphp
            @if($orden->fecha_envio == null)
            @if($orden->estado = '1')
            <tr>
              <td><button class="btn btn-danger btn-xs" onclick="eliminar_orden_sesion('{{$value}}');"><span class="glyphicon glyphicon-trash"></span></button></td>
              <td>{{$n}}</td>
              <td>{{$orden->id}}</td>
              <td>{{substr($orden->fecha_orden,0,10)}}</td>
              <td>{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2 != null) {{$orden->paciente->apellido2}} @endif {{$orden->paciente->nombre1}} @if($orden->paciente->nombre2 != null) {{$orden->paciente->nombre2}} @endif</td>
              <td>{{$orden->seguro->nombre}}</td>
              <td>{{$orden->cantidad}}</td>
              <td>{{$orden->total_valor}}</td>
              <td>{{number_format($orden->descuento_valor,2,'.',',')}}</td>
              @if($rolUsuario=='20' || $rolUsuario=='1')
              @if($orden->seguro->tipo=='0')
              <td @if($orden->total_nivel2=='0' || $orden->total_nivel2==null) style="background-color: red;" @endif>{{$orden->total_nivel2}}</td>
              @else
              <td>
                @foreach( $orden->detalle_forma_pago as $fp)
                {{ $fp->tipo_pago->nombre }}
                @endforeach
              </td>
              @endif
              @else
              <td></td>
              @endif
            </tr>
            @endif
            @endif
            @endforeach
            <tr>
              <td colspan="6"></td>
              <td><b>Total:</b></td>
              <td>{{number_format($totalf,2,'.',',')}}</td>
              <td>{{number_format($descf,2,'.',',')}}</td>
              <td></td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    @if($cuenta > '1')
    <div class="col-md-12" id="datos_factura_agrup"></div>
    @else
    <div class="col-md-12">
      <span style="color: red;">Seleccione más de 1 orden</span>
    </div>
    @endif
  </div>
  <!--div class="row">
    <div class="col-md-3">
      <a onclick="guardar();" class="btn btn-success">Crear Factura</a>
    </div>
  </div-->
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<script type="text/javascript">
  $('#example4').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
  })

  function guardar_agrupada() {
    if ($('#sucursal_factura').val() == '') {
      alert('seleccione una sucursal, para continuar');
      return;
    }
    $('#imagen_espera').css("display", "block");
    $('#boton_sri').css("display", "none");
    $('#boton_sri_contabilidad').css("display", "none");
    $('#boton_sri').attr("disabled", true);
    $('#boton_sri_contabilidad').attr("disabled", true);
    var cedula_factura = $('#cedula_factura').val();
    var nombre_factura = $('#nombre_factura').val();
    var direccion_factura = $('#direccion_factura').val();
    var ciudad_factura = $('#ciudad_factura').val();
    var email_factura = $('#email_factura').val();
    var telefono_factura = $('#telefono_factura').val();
    var txt = '';
    if (cedula_factura == '') {
      txt = txt + 'Ingrese la Cedula -';
    }
    if (nombre_factura == '') {
      txt = txt + 'Ingrese el Nombre -';
    }
    if (direccion_factura == '') {
      txt = txt + 'Ingrese la Direccion -';
    }
    if (ciudad_factura == '') {
      txt = txt + 'Ingrese la Ciudad -';
    }
    if (email_factura == '') {
      txt = txt + 'Ingrese el email -';
    }
    if (telefono_factura == '') {
      txt = txt + 'Ingrese el telefono -';
    }
    if (txt != '') {
      alert("Ingrese los campos: " + txt);
      $('#imagen_espera').css("display", "none");
      $('#boton_sri').css("display", "block");
      $('#boton_sri_contabilidad').css("display", "block");
      $('#boton_sri').attr("disabled", false);
      $('#boton_sri_contabilidad').attr("disabled", false);
      return;
    } else {
      //alert("hola");
      $.ajax({
        type: 'get',
        url: "{{ url('facturacion_labs/factura_agrup/guardar')}}",
        datatype: 'json',
        data: $("#frm_datos_agrupada").serialize(),
        success: function(data) {
          //alert("sucess");
          if (data == 'ok') {
            $('#imagen_espera').css("display", "none");
            $('#boton_sri').css("display", "block");
            $('#modal_agrupada').modal('hide');
            $('#btn_elimina_sesion').click();
            //eliminar();
            location.reload();
          };
        },
        error: function() {
          alert('error al cargar');
          $('#imagen_espera').css("display", "none");
          $('#boton_sri').css("display", "block");
        }
      });
    }
  }

  function guardar_agrupada_contabilidad() {
    $('#imagen_espera').css("display", "block");
    $('#boton_sri').css("display", "none");
    $('#boton_sri_contabilidad').css("display", "none");
    $('#boton_sri').attr("disabled", true);
    $('#boton_sri_contabilidad').attr("disabled", true);
    var cedula_factura = $('#cedula_factura').val();
    var nombre_factura = $('#nombre_factura').val();
    var direccion_factura = $('#direccion_factura').val();
    var ciudad_factura = $('#ciudad_factura').val();
    var email_factura = $('#email_factura').val();
    var telefono_factura = $('#telefono_factura').val();
    var txt = '';
    if (cedula_factura == '') {
      txt = txt + 'Ingrese la Cedula -';
    }
    if (nombre_factura == '') {
      txt = txt + 'Ingrese el Nombre -';
    }
    if (direccion_factura == '') {
      txt = txt + 'Ingrese la Direccion -';
    }
    if (ciudad_factura == '') {
      txt = txt + 'Ingrese la Ciudad -';
    }
    if (email_factura == '') {
      txt = txt + 'Ingrese el email -';
    }
    if (telefono_factura == '') {
      txt = txt + 'Ingrese el telefono -';
    }
    if (txt != '') {
      alert("Ingrese los campos: " + txt);
    } else {
      //alert("hola");
      $.ajax({
        type: 'get',
        url: "{{ url('facturacion_labs/factura_agrup/guardar/contabilidad')}}",
        datatype: 'json',
        data: $("#frm_datos_agrupada").serialize(),
        success: function(data) {
          //alert("sucess");
          if (data == 'ok') {
            $('#imagen_espera').css("display", "none");
            $('#boton_sri').css("display", "block");
            $('#modal_agrupada').modal('hide');
            $('#btn_elimina_sesion').click();
            //eliminar();
            location.reload();
          };
        },
        error: function() {
          alert('error al cargar');
          $('#imagen_espera').css("display", "none");
          $('#boton_sri').css("display", "block");
        }
      });
    }
  }

  function eliminar_orden_sesion(id_orden) {
    $.ajax({
      type: 'get',
      url: "{{ url('facturacion/labs/eliminar/orden/sesion')}}/" + id_orden,
      datatype: 'json',
      success: function(data) {
        alert('Eliminado');
      },
      error: function() {}
    });
  }
</script>