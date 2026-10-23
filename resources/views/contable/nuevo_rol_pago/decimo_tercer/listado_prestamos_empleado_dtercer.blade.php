

<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<section class="content" style="position: fixed;">

  <div class="box-header">
        <div class="col-md-7">
            <h5><b> Valores Correspondientes a Préstamos <br> pendientes del Empleado:</b></h5>
        </div>

    </div>
    <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{ $nomina->nombres }}</label>
        </div>
    </div>

    <div>
        <div>
          @php $valor_por_cruzar=0;
          $valor_por_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
          $valor_por_cruzar = number_format($valor_por_cruzar,4);
          @endphp
            <p style="text-align: center;"> <b>Valor Pendiente a Cruzar: $ {{ $valor_por_cruzar }} </b> </p>
        </div>
    </div>


    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="form-row">
            <form class="form-horizontal" role="form" id="form_cruce_prestamos_dtecer">
                <div id="contenedor">
                    <div id="det_prestamo_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="det_prestamo" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="det_prestamo_info" style="width: 100%">
                                <thead>
                                  <tr class='well-dark'>
                                    <th width="10%">Sel.</th>  
                                    <th width="20%"> Id </th>
                                    <th width="40%">Fecha Creación de Préstamo</th>
                                    <th width="40%" style="text-align: center;"> Valor Pediente de Pago </th>
                                    <th width="40%" style="text-align: center;"> Estado </th>
                                    <th width="40%" style="text-align: center;"> Valor a Aplicar</th>
                                    <th width="10%">{{trans('nomina.acciones')}}</th>
                                  </tr>
                                </thead>

                                <tbody>
                                @php
                                $tvalor=0;
                                $tvalorpago=0;
                                @endphp

                                @foreach ($prestamos as $value)

                                <tr>
                                  <td> <input type="checkbox" name="Prestamos[]" value="{{$value->id}}"> </td>
                                  <td style="text-align: left;">{{$value->id}}</td>
                                  <td>{{$value->fecha_creacion}}</td>
                                  <td style="text-align: center;">{{$value->saldo_total}}</td>
              
                                  <td style="text-align: center;"> @if($value->estado) {{"Activo"}}@endif</td>
                                  <td> <input size="8" id="valor_pago_prest" type="text"  name="valor_pago_prest[{{$value->id}}]" value="{{$value->valor_pago}}" autocomplete="off"></td>
                                  <td>

                                  </td>

                                </tr>
                                @php

                                $tvalor += $value->valor;
                                $tvalorpago += $value->valor_pago;
                                @endphp

                                @endforeach

                              </tbody>

                           
                                    <tfoot>
                                    </tfoot>

                              </table>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                </div>
                <div class="form-group col-md-6 col-xs-3">
                      <label for="fecha_creacion{{$ct_rh_acumulado->id}}" class="col-md-6 texto" >Fecha:</label>
                      <div class="col-md-6">
                        <input id="fecha_creacion{{$ct_rh_acumulado->id}}" type="date" class="form-control validar" name="fecha_creacion{{$ct_rh_acumulado->id}}" value="" >
                      </div>
                </div>
                <div class="form-group col-md-6">
                    <div class="col-md-6">
                      <input type="hidden" name="id_ct_rh_acum" value="{{$ct_rh_acumulado->id}}">
                      <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_cruce_prestamos_dtercer(event);">Generar Cruce</a>
                    </div>
                </div>
             <form>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
  function changeExcel(l) {
    if (l === 'e') {
      document.getElementById("excel").value = 1;
    } else {
      document.getElementById("excel").value = 0;
    }

  }

  $('#example2_').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })

  $('#det_prestamo').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': false
  })



  function sel_todos() {
    // Get the checkbox
    var checkBox = document.getElementById("ctodos");

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true) {
      $(':checkbox').prop('checked', true);
    } else {
      $(':checkbox').prop('checked', false);
    }
  }


  function guardar_cruce_prestamos_dtercer(e) {
    e.preventDefault();
    let fecha_asiento = document.getElementById('fecha_creacion{{$ct_rh_acumulado->id}}').value;

    let msj = "";
    if (fecha_asiento == "") {
      msj += "Seleccione la Fecha de Creacion <br>";
    }
    
    if (msj != "") {
      alertas('error', 'Error!..', msj)
    } else {
      var confirmar = confirm('Desea Realizar el Cruce de Prestamos con Valor Vacaciones');
      if (confirmar) {
        $.ajax({
          type: 'post',
          url: "{{ route('nuevo_rol.cruce_valor_prestamos_decimo_tercer') }}",

          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: $("#form_cruce_prestamos_dtecer").serialize(),
          success: function(data) {
            alertas(data.respuesta, data.titulos, data.msj);
            if (data.respuesta == 'success') {
              setTimeout(function() {
                location.reload();
              }, 1000);
            }
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    }
  }



</script>

