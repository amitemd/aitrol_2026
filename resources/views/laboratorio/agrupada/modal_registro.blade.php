@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h3 style="margin:0;">{{trans('laboratorio.ingreseDatosFactura')}}</h3>
</div>
<div class="modal-body">
  <form id="form_agrupada">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <!--cedula factura-->
      <div class="form-group col-md-3">
        <label for="anio" class="control-label">{{trans('laboratorio.anio')}}</label>
        <select id="anio" name="anio" class="form-control">
          @php $x=2020; $anio_actual=date('Y'); @endphp
          @for($x=2020;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
            @endfor
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="mes" class="control-label">{{trans('laboratorio.mes')}}</label>
        <select id="mes" name="mes" class="form-control">
          <option value="1" @if($mes==1) selected @endif>{{trans('laboratorio.enero')}}</option>
          <option value="2" @if($mes==2) selected @endif>{{trans('laboratorio.febrero')}}</option>
          <option value="3" @if($mes==3) selected @endif>{{trans('laboratorio.marzo')}}</option>
          <option value="4" @if($mes==4) selected @endif>{{trans('laboratorio.abril')}}</option>
          <option value="5" @if($mes==5) selected @endif>{{trans('laboratorio.mayo')}}</option>
          <option value="6" @if($mes==6) selected @endif>{{trans('laboratorio.junio')}}</option>
          <option value="7" @if($mes==7) selected @endif>{{trans('laboratorio.julio')}}</option>
          <option value="8" @if($mes==8) selected @endif>{{trans('laboratorio.agosto')}}</option>
          <option value="9" @if($mes==9) selected @endif>{{trans('laboratorio.septiembre')}}</option>
          <option value="10" @if($mes==10) selected @endif>{{trans('laboratorio.octubre')}}</option>
          <option value="11" @if($mes==11) selected @endif>{{trans('laboratorio.noviembre')}}</option>
          <option value="12" @if($mes==12) selected @endif>{{trans('laboratorio.diciembre')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="fecha_desde" class="control-label">{{trans('laboratorio.FechaDesde')}}</label>
        <input type="date" class="form-control validar" name="fecha_desde" id="fecha_desde" value="">
      </div>
      <div class="form-group col-md-3">
        <label for="fecha_hasta" class="control-label">{{trans('laboratorio.FechaHasta')}}</label>
        <input type="date" class="form-control validar" name="fecha_hasta" id="fecha_hasta" value="">
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('laboratorio.documento')}}</label>
        <select id="documento" name="documento" class="form-control validar" onchange="busca_clientes()">
          <option value="5">{{trans('laboratorio.cedula')}}</option>
          <option value="4">RUC</option>
          <option value="6">{{trans('laboratorio.pasaporte')}}</option>
          <option value="8">{{trans('laboratorio.identificacionExtranjera')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('laboratorio.cedula')}} / RUC</label>
        <input id="cedula_factura" maxlength="13" type="text" class="form-control validar" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off" onchange="busca_clientes(this.value);">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
        <label for="nombre_factura" class="control-label">{{trans('laboratorio.nombre')}}</label>
        <input id="nombre_factura" class="form-control validar" maxlength="100" type="text" name="nombre_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Direccion-->
      <div class="form-group col-md-3">
        <label for="direccion_factura" class="control-label">{{trans('laboratorio.direccion')}}</label>
        <input id="direccion_factura" type="text" class="form-control validar" name="direccion_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--ciudad-->
      <div class="form-group col-md-3">
        <label for="ciudad_factura" class="control-label">{{trans('laboratorio.ciudad')}}</label>
        <input id="ciudad_factura" type="text" class="form-control validar" name="ciudad_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--email-->
      <div class="form-group col-md-6">
        <label for="email_factura" class="control-label">{{trans('laboratorio.email')}}</label>
        <input id="email_factura" type="text" class="form-control validar" name="email_factura" maxlength="70" required autofocus onchange="">
      </div>
      <!--telefono-->
      <div class="form-group col-md-3">
        <label for="telefono_factura" class="control-label">{{trans('laboratorio.telefono')}}</label>
        <input id="telefono_factura" type="text" class="form-control validar" maxlength="10" name="telefono_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <div class="form-group col-md-3">
        <button type="button" class="btn btn-info" id="boton_sri" onclick="guardar_info_cab()">{{trans('laboratorio.guardar')}}</button>
      </div>
      <!--Sucursal-->
      <div class="form-group col-md-3">
        <label for="sucursal_factura" class="control-label">Sucursal</label>
        <select id="sucursal_factura" name="sucursal_factura" onchange="obtener_caja();" class="form-control select2">
          <option value="">--Seleccionar--</option>
          <?php
          if (isset($sucursales) && count($sucursales) > 0) {
            foreach ($sucursales as $row) {
              echo '<option value="' . $row->id . '">' . $row->codigo_sucursal . '</option>';
            }
          }
          ?>
        </select>
      </div>
      <!--Punto de emision-->
      <div class="form-group col-md-3">
        <label for="emision_factura" class="control-label">Punto de emisión</label>
        <select id="emision_factura" name="emision_factura" class="form-control select2">
          <option value="">--Seleccionar--</option>
        </select>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript">
  /*var cedula_factura = $('#cedula_factura').val();
  cedula_factura = cedula_factura.trim();
  busca_clientes(cedula_factura);*/
  function obtener_caja() {
    var id_sucursal = $("#sucursal_factura").val();
    $.ajax({
      type: 'post',
      url: "{{ route('caja.sucursal') }}",
      datatype: 'json',
      data: {
        _token: $('input[name=_token]').val(),
        'id_sucur': id_sucursal
      },
      success: function(data) {
        if (data.value != 'no') {
          if (id_sucursal != 0) {
            $("#emision_factura").empty();
            $.each(data, function(key, registro) {
              var selected = '';
              if (registro.codigo_caja == 100)
                selected = 'selected';
              $("#emision_factura").append('<option value=' + registro.id + ' ' + selected + '>' + registro.codigo_sucursal + '-' + registro.codigo_caja + '</option>');
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
    console.log(cedula);
    var documento = $('#documento').val();
    if (documento == 4 || documento == 5) {
      if (!validarCedula(cedula)) {
        alert("{{trans('laboratorio.errorcedula_ruc')}}");
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
        console.log(data);
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

  function guardar_info_cab() {


    if (!validarDatos()) {
      $.ajax({
        type: 'post',
        url: "{{ route('factura_agrupada.guardar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data) {
          $('#modal_registro').modal('hide');
          location.reload();
        },
        error: function(data) {
          if (data.responseJSON.valor != null) {
            alert(data.responseJSON.valor[0]);
          }
        }
      });
    } else {
      alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
    }
  }
</script>