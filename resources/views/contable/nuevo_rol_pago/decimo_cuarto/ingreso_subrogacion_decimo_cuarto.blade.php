

<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<section class="content" style="position: fixed;">

  <div class="box-header">
        <div class="col-md-12">
            <h5><b> Ingreso de Subrogación del Empleado:</b></h5>
        </div>

    </div>
    <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{ $nomina->nombres }}</label>
        </div>
    </div>

    <p style="text-align: center;"> <b> Dias Laborados: </b> {{$ct_rh_acumulado->dias_laborados}} <b> Valor: </b> {{$ct_rh_acumulado->valor}} </p>

    <!--div>
        <div>
          @php $valor_por_cruzar=0;
          $valor_por_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
          $valor_por_cruzar = number_format($valor_por_cruzar,4);
          @endphp
            <p style="text-align: center;"> <b>Valor Pendiente a Cruzar: $ {{ $valor_por_cruzar }} </b> </p>
        </div>
    </div-->


    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="form-row">
            <form class="form-horizontal" role="form" id="form_pago_manual_dtecer">
                <div id="contenedor">
                    <div id="pago_manual_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="ingreso_subrogacion" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="ingreso_subrogacion_info" style="width: 100%">
                                <thead>
                                  <tr class='well-dark'>
                                    <th width="20%" style="text-align: center;"> Dias Laborados Adicionales</th>
                                    <th width="20%" style="text-align: center;"> Valor a Aplicar</th>
                                    <th width="50%" style="text-align: center;"> Observacion</th>
                                    
                                  </tr>
                                </thead>

                                <tbody>
                                <tr>
                                <td style="text-align: center;"> <input size="10" id="dias_subrogacion" type="text"  name="dias_subrogacion" value="" autocomplete="off"></td>
                                  <td style="text-align: center;"> <input size="10" id="valor_subrogacion" type="text"  name="valor_subrogacion" value="" autocomplete="off"></td>
                                  <td style="text-align: center;"> <input size="40" id="observacion_pago" type="text"  name="observacion_pago" value="" autocomplete="off" maxlength="100"></td>
                                </tr>
                               

                              </tbody>

                           
                                    <tfoot>
                                    </tfoot>

                              </table>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="col-md-6">
                      <input type="hidden" name="id_ct_rh_acum" value="{{$ct_rh_acumulado->id}}">
                      <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_ingreso_subrogacion_empleado(event);">Generar Ingreso Subrogacion</a>
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




  function guardar_ingreso_subrogacion_empleado(e) {
    e.preventDefault();
    let msj = "";
    if (msj != "") {
      alertas('error', 'Error!..', msj)
    } else {
      var confirmar = confirm('Desea Realizar el ingreso de valores de subrogacion');
      if (confirmar) {
        $.ajax({
          type: 'post',
          url: "{{ route('nuevo_rol.guarda_subrogacion_empleado') }}",

          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: $("#form_pago_manual_dtecer").serialize(),
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

