@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h3 style="margin:0;">{{trans('laboratorio.editarDatosFactura')}}</h3>
</div>
<div class="modal-body">
  <form id="form_agrupada">
    <div class="box-body">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <!--cedula factura-->
      <div class="form-group col-md-3">
        <label for="anio" class="control-label">{{trans('laboratorio.Anio')}}</label>
        <select id="anio" name="anio" class="form-control">
          @php $x=2020; $anio_actual=date('Y'); echo  $anio_actual;   @endphp
          @for($x=2020;$x<=$anio_actual;$x++)
          <option @if($x==$cabecera->anio) selected @endif>{{$x}}</option>
          @endfor
        </select>
      </div>
      @php $meses = [trans('laboratorio.enero'),trans('laboratorio.febrero'), trans('laboratorio.marzo'), trans('laboratorio.abril'), trans('laboratorio.mayo'), trans('laboratorio.junio'), trans('laboratorio.julio'), trans('laboratorio.agosto'), trans('laboratorio.septiembre'), trans('laboratorio.octubre'), trans('laboratorio.noviembre'), trans('laboratorio.diciembre')]; @endphp

      <div class="form-group col-md-3">
        <label for="mes" class="control-label">{{trans('laboratorio.mes')}}</label>
        <select id="mes" name="mes" class="form-control">
          @for($ids =0; $ids<sizeof($meses); $ids++)
          @if($ids == ($cabecera->mes)-1){
          <option value="<?php echo $ids+1;?>" selected ><?php echo $meses[$ids];?></option>
          @else
          <option value="<?php echo $ids+1;?>" ><?php echo $meses[$ids];?></option>
          @endif
          @endfor
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="fecha_desde" class="control-label">{{trans('laboratorio.FechaDesde')}}</label>
        <input type="date" class="form-control validar" name="fecha_desde" id="fecha_desde" value="{{$cabecera->fecha_desde}}">
      </div>
      <div class="form-group col-md-3">
        <label for="fecha_hasta" class="control-label">{{trans('laboratorio.FechaHasta')}}</label>
        <input type="date" class="form-control validar" name="fecha_hasta" id="fecha_hasta" value="{{$cabecera->fecha_hasta}}">
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('laboratorio.Documento')}}</label>
        <select id="documento" name="documento" class="form-control validar" onchange="busca_clientes()">
          <option @php if($cabecera->tipo_documento == 5){ echo "selected";} @endphp value="5">{{trans('laboratorio.cedula')}}</option>
          <option @php if($cabecera->tipo_documento == 4){ echo "selected";} @endphp value="4">RUC</option>
          <option @php if($cabecera->tipo_documento == 6){ echo "selected";} @endphp value="6">{{trans('laboratorio.pasaporte')}}</option>
          <option @php if($cabecera->tipo_documento == 8){ echo "selected";} @endphp value="8">{{trans('laboratorio.identificaciónExtranjera')}}</option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="cedula_factura" class="control-label">{{trans('laboratorio.cedula')}} / RUC</label>
        <input value="{{$cabecera->cedula_factura}} " id="cedula_factura" maxlength="13" type="text" class="form-control validar" name="cedula_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off" onchange="busca_clientes(this.value);">
      </div>
      <!--primer nombre-->
      <div class="form-group col-md-6">
        <label for="nombre_factura" class="control-label">{{trans('laboratorio.nombre')}}</label>
        <input value="{{$cabecera->nombre_factura}}" id="nombre_factura" class="form-control validar" maxlength="100" type="text" name="nombre_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--Direccion-->
      <div class="form-group col-md-3">
        <label for="direccion_factura" class="control-label">{{trans('laboratorio.dirección')}}</label>
        <input value="{{$cabecera->direccion_factura}} " id="direccion_factura" type="text" class="form-control validar" name="direccion_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--ciudad-->
      <div class="form-group col-md-3">
        <label for="ciudad_factura" class="control-label">{{trans('laboratorio.ciudad')}}</label>
        <input value="{{$cabecera->ciudad_factura}}" id="ciudad_factura" type="text" class="form-control validar" name="ciudad_factura" maxlength="70" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <!--email-->
      <div class="form-group col-md-6">
        <label for="email_factura" class="control-label">{{trans('laboratorio.email')}}</label>
        <input value="{{$cabecera->email_factura}} " id="email_factura" type="text" class="form-control validar" name="email_factura" maxlength="70" required autofocus onchange="">
      </div>
      <!--telefono-->
      <div class="form-group col-md-3">
        <label for="telefono_factura" class="control-label">{{trans('laboratorio.telefono')}}</label>
        <input value="{{$cabecera->telefono_factura}} " id="telefono_factura" type="text" class="form-control validar" maxlength="10" name="telefono_factura" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
      </div>
      <input type="hidden" name="id_cambio" value="{{$cabecera->id}} ">
      <div class="form-group col-md-3">
        <button type="button" class="btn btn-info" id="boton_sri" onclick="validar()">{{trans('laboratorio.editar-')}}</button>
      </div>
    </div>
  </form>
</div>


<script type="text/javascript">
  function validar() {
    
    if (!validarDatos()) {
      $.ajax({
        type: 'post',
        url: "{{ route('factura_agrupada.editar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data) {
          $('#modal_editar').modal('hide');;
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

  function busca_clientes(cedula){
    console.log(cedula);
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

  function guardar_info_cab(){

    if (!validarDatos()) {
      $.ajax({
        type: 'post',
        url: "{{ route('factura_agrupada.editar_datos_agrupada_cab') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form_agrupada").serialize(),
        success: function(data){
          $('#modal_editar').modal('hide');;
        },

        error: function(data){

          if(data.responseJSON.valor!=null){

            alert(data.responseJSON.valor[0]);
          }


        }
      });
    } else {
      alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
    }

  }

</script>
