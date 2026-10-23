<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">

  <div class="card-body" style="padding: 1px,">
    <div class="row">
      <input type="hidden" name="contador" id="contador" value="1">
      <div class="col-12">
        <div class="row">
          <div class="col-md-12" id="tabla_detalle">
            <table role="table" aria-busy="false" aria-colcount="4" class="table b-table">
              <thead role="rowgroup" class="">
                <tr role="row" class="">
                  <th width="20%" role="columnheader" scope="col" aria-colindex="1" class="">
                    <div>{{trans('paso2.MEDICAMENTO')}}</div>
                  </th>
                  <th width="10%" role="columnheader" scope="col" aria-colindex="2" class="">
                    <div>{{trans('paso2.Cantidad')}}</div>
                  </th>
                  <th width="40%" role="columnheader" scope="col" aria-colindex="3" class="">
                    <div>{{trans('paso2.POSOLOGIA')}}</div>
                  </th>
                  <th width="30%" role="columnheader" scope="col" aria-colindex="4" class="">
                    <div>{{trans('paso2.ACCION')}}</div>
                  </th>
                </tr>
              </thead>
              <tbody role="rowgroup">


                @foreach($detalles as $detalle)
                @php $producto = Sis_medico\Producto_Medicina::where('id_medicina',$detalle->id_medicina)->first(); @endphp
                <tr role="row" class="">
                  <input type="hidden" name="d{{$detalle->id_medicina}}" value="{{$detalle->id_medicina}}">
                  @if($producto!=null)
                  <input type="hidden" name="p{{$producto->id}}" value="{{$producto->id}}">
                  @endif
                  <td aria-colindex="1" role="cell" class="">{{$detalle->nombre}}</td>
                  <td aria-colindex="2" role="cell" class="">{{$detalle->cantidad}}</td>
                  <td aria-colindex="3" role="cell" class="">
                    {{$detalle->dosis}}
                  </td>
                  <td aria-colindex="4" role="cell" class="">
                    @if($detalle->descargo)
                    <input id="serie{{$detalle->id}}" name="serie{{$detalle->id}}" type="text" class="form-control is-valid" onchange="descargar_xserie('{{$detalle->id}}', this);" value="{{$detalle->serie}}" disabled><br>
                    <span class="font-weight-bolder text-success">{{trans('paso2.Entregado')}}</span>
                    @else
                    @if(!is_null($producto)) <!-- BODEGA 13 ES HOSPITAL -->
                    @php $inventario = Sis_medico\InvInventario::where('id_producto',$producto->id_producto)->where('id_bodega',19)->first(); @endphp
                    @if(!is_null($inventario))
                    @if($inventario->existencia > 0)
                    <input id="serie{{$detalle->id}}" name="serie{{$detalle->id}}" type="text" class="form-control form-control-sm" onchange="descargar_xserie('{{$detalle->id}}', this);"><br>
                    <span class="font-weight-bolder text-success" id="sp_ex{{$detalle->id}}">{{$inventario->existencia}} {{trans('paso2.EnStock')}} </span>
                    @else
                    <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock.')}} </span>
                    @endif
                    @else
                    <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock...')}}</span>
                    @endif
                    @else
                    <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock...')}}</span>
                    @endif
                    @endif
                  </td>
                </tr>
                @endforeach


              </tbody>
            </table>
          </div>
        </div>
        <br><br>
        <div class="row">

          <div class="col-12 alert alert-info" role="alert">
            Plantilla Descargo

          </div>
          <div class="col-md-5">
            <input id="nombre" type="text" class="form-control" name="nombre" value="{{old('nombre')}}" style="text-transform:uppercase;">
            <input type="hidden" id="codigo" name="codigo">
          </div>
          <div class="col-md-4">

            <select class="form-control" name="id_bodega" id="id_bodega" onchange="cambio_bodega(this, '2')">
              <option value="">Seleccione</option>
              @foreach($bodegas as $b)
              <option @if(!is_null($modulo_bodega)) @if($modulo_bodega->id_bodega == $b->id) selected @endif @endif value="{{$b->id}}">{{$b->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <button class="btn btn-success btn-sm col-md-10" onClick="enviar();"><span class="glyphicon glyphicon-plus"> {{trans('ecamilla.Agregar')}}</span></button>
          </div>
          <div class="col-12" id="listado"></div>
        </div>


        <!-- <div class="row">
          <div class="col-md-12">
            <h3><b>{{trans('eenfermeria.MedicamentosProcedimiento')}}: </b></h3>

          </div>
          <div class="col-md-4">
            <label>{{trans('eenfermeria.SeleccioneProcedimiento')}}</label>
          </div>
          <div class="col-md-6">
            <label>{{trans('eenfermeria.SeleccioneMedicamento')}}</label>
          </div>
          <div class="col-md-4">
            <select name="hc_procedimientos" id="hc_procedimientos" class="form-control select2">
              <option value="">{{trans('hospital.Seleccione')}} ...</option>

            </select>
          </div>

          <div class="input-group input-group col-md-4">
            <input type="hidden" id="id_plantilla" name="id_plantilla">
            <input type="hidden" id="id_plantilla_2" name="id_plantilla_2">
            <input type="text" class="form-control" id="nom_plantilla" name="nom_plantilla" required>
            <span class="input-group-btn">
              <button type="button" class="btn btn-info btn-flat" onclick="mostrar_planilla();">{{trans('erol.Buscar')}}</button>
            </span>
          </div>
          <br>
          <div class="input-group input-group col-md-12">
            <center>
              <button type="button" onclick="guardar_plantilla_basica()" class="btn btn-success">{{trans('econtrolsintomas.Guardar')}}</button>
            </center>
          </div>
          <div class="col-md-12" id="detalle"></div>
        </div> -->
      </div>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    listado_prod();
  });

  $("#nom_plantilla").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('enfermeria.nombre_plantilla')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          response(data);

        }
      })
    },
    minLength: 2,
    select: function(data, ui) {
      //console.log("select",ui.item.id);
      $('#id_plantilla').val(ui.item.id);
      $('#id_plantilla_2').val(ui.item.id);

    }
  });

  $("#nom_plantilla").change(function() {
    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.nombre_plantilla2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#nom_plantilla"),
      success: function(data) {
        if (data != '0') {
          $('#id_plantilla').val(data.id);
          $('#id_plantilla_2').val(data.id);

        }
      },
      error: function(data) {

      }
    })
  });

  function mostrar_planilla() {
    var plantilla = document.getElementById("id_plantilla").value;
    if (plantilla == "") {
      Swal.fire({
        icon: 'error',
        title: 'Mensaje!!',
        text: 'Digite la planilla que desea agregar',
      });
      return;
    }
    $.ajax({
      type: 'get',
      url: "{{ url('insumos/plantillas/item_lista2')}}/" + plantilla + "/{{$hcid}}/1",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      success: function(data) {
        $('#detalle').empty().html(data);
        $(".oculto").show();
      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function descargar_xserie(id, element) {
    console.log(id, element.value);
    $.ajax({
      type: "post",
      url: "{{route('hospitalizacion.descargo_enfermeria_detalle_store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        id: id,
        serie: element.value
      },

      success: function(datahtml, data) {
        console.log(datahtml);
        alert(datahtml.msn);
        if (datahtml.estado == 'E') {
          $('#serie' + id).val('');
        } else {
          $('#serie' + id).attr('disabled', 'disabled');
          $('#sp_ex' + id).text('Entregado');
        }
      },
      error: function() {
        alert('error al cargar');
      }
    });

  }

  $("#nombre").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('enfermeria.nombre')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          //console.log(data);
          response(data);
        }
      })
    },
    minLength: 2,
    select: function(data, ui) {
      //console.log("++"+ui.item.codigo);
      $('#codigo').val(ui.item.codigo);
      //enfermeria_nombre_2(ui.item.codigo);
    }
  });
</script>