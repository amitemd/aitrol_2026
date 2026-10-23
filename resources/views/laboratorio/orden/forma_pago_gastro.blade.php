<div class="modal-header">
  <h5 class="modal-title" id="exampleModalLabel">Forma de Pago EndoscopyNet</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="row">
    <form id="form_oda_gc" class="form-horizontal" name="form_oda_gc">
      {{ csrf_field() }}
      <div id="div_calcula_oda">
        @php
          $total_det = $orden_venta_detalle->cantidad * $orden_venta_detalle->precio;
        @endphp
        <div class="col-md-4">
          <div class="col-md-8">
            <label>%Cobrar Paciente</label>
          </div>
          <div class="col-md-4">
              <input type="hidden" name="id_orden_venta" value="{{$orden_venta->id}}">
              <input type="hidden" id="total_orden_venta" value="{{$total_det}}">
              <input type="text" id="oda_gc" name="oda_gc" class="form-control input-sm" onchange="valor_oda();guardar_oda();" value="{{$orden_venta_detalle->p_oda}}">
          </div>
        </div>
        <div class="col-md-8">
          <div class="col-md-6">
            <div class="col-md-7">
              <label>Total a Pagar:</label>
            </div>
            <div class="col-md-5">
              <input type="text" name="total_ov" class="form-control input-sm" value="{{$orden_venta->total}}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="col-md-7">            
              <label>Valor Oda:</label>
            </div>
            <div class="col-md-5">
              <input type="text" name="total_oda_ov" class="form-control input-sm" value="{{$orden_venta->valor_oda}}" readonly>
            </div>
          </div>
        </div>
      </div>
    </form>
    <div class="col-md-3">
      <a type="button" class="btn btn-success btn-sm" onclick="agregar_forma_pago_gastro();" > Crear </a>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div id="forma_pago_gastro_tabla">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="table-responsive">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr>
                    <th >Tipo Pago</th>
                    <th >Nro Transaccion</th>
                    <th >Tarjeta</th>
                    <th >Banco</th>
                    <th >Valor</th>
                    <th >Fi Administrativo</th>
                    <th >Valor Neto</th>
                    <th >Acción</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $total_pago=0; $forma_pago = $orden_venta->pagos;
                  @endphp
                  @foreach($forma_pago as $value)
                    @php
                    //dd($value);
                      $fi = 0;
                      $valor_neto =0;
                      if ($value->tipo == '4') {
                        /*$fi= 0.07;*/

                      }
                      if ($value->tipo == '6') {
                        /*$fi= 0.02;*/
                      }
                      $valor_neto= round($value->valor+($value->valor*$fi),2);
                      $total_pago += round($valor_neto,2);
                    @endphp
                  <tr>
                    <td>{{$value->metodo->nombre}}</td>
                    <td>{{$value->numero}}</td>
                    <td>@if($value->tipo_tarjeta != null) {{$value->tarjeta->nombre}} @endif</td>
                    <td>@if($value->banco != null) {{$value->ct_banco->nombre}} @endif</td>
                    <td>{{$value->valor}}</td>
                    <td>{{$fi}}</td>
                    <td>{{round($valor_neto,2)}}</td>
                    <td><a type="button" onclick="eliminar_forma_gastro('{{$orden_venta->id}}','{{$value->id}}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="row">
                <div class="col-md-4">
                    <label>Total Pagado</label>

                    <span>{{round($total_pago,2)}}</span>
                </div>
                <div class="col-md-4">
                    <label>Pendiente</label>

                    <span>{{round($orden_venta->total - $total_pago,2)}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>    

    <div id="crear_registro_pago_gastroclinica" style="display: none;">
      <form id="form_datos_gc" class="form-horizontal" name="form_datos_gc">
        {{ csrf_field() }}
        <div class="form-group col-md-4 "  id="div_id_pago">
          <div class="col-md-12">
            <label> Metodo</label>
          </div>
          <div class="col-md-12">
            <input type="hidden" name="id_orden_venta" value="{{$orden_venta->id}}">
            <select id="id_pago_gc" name="id_pago_gc" class="form-control input-sm" onchange="validar_gc();valor_neto_gc();">

              @foreach($pagos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-4 " style="display:none;" id="div_id_tarjeta_gc">
          <div class="col-md-12">
            <label> Tipo tarjeta</label>
          </div>
            <div class="col-md-12">
              <select id="id_tipo_tarjeta_gc" name="id_tipo_tarjeta_gc" class="form-control input-sm">
                <option value="">Seleccione</option>
                @foreach($tarjetas as $tarjeta)
                  <option value="{{$tarjeta->id}}">{{$tarjeta->nombre}}</option>
                @endforeach
              </select>
            </div>
        </div>

        <div class="form-group col-md-4" style="display:none;" id="div_transaccion_gc">
          <div class="col-md-12">
            <label> Número Transaccion</label>
          </div>
          <div class="col-md-12">
            <input type="text" id="transaccion_gc" name="transaccion_gc" class="form-control input-sm">
          </div>
        </div>

        <div class="form-group col-md-4 " style="display:none;" id="div_banco_gc">
          <div class="col-md-12">
            <label> Banco</label>
          </div>
          <div class="col-md-12">
            <select id="id_banco_gc" name="id_banco_gc" class="form-control input-sm">
              <option value="">Seleccione</option>
              @foreach($bancos as $banco)
                <option value="{{$banco->id}}">{{$banco->nombre}}</option>
              @endforeach
            </select>

          </div>
        </div>


        <div class="form-group col-md-4 "  id="div_valor">
          <div class="col-md-12">
            <label> Valor Base</label>
          </div>
          <div class="col-md-10">
            <input type="number" id="valor_gc" name="valor_gc" class="form-control input-sm" onchange="valor_neto_gc();">
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-info btn-xs"><i class="fa fa-calculator"></i></button>
          </div>
        </div>

        <!--div class="form-group col-md-4 "  id="div_valor">
          <div class="col-md-12">
            <label> % Cobrar Paciente</label>
          </div>
          <div class="col-md-10">
            <input type="text" id="p_oda_gc" name="p_oda_gc" class="form-control input-sm" onchange="valor_neto_gc();" value="100">
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-info btn-xs"><i class="fa fa-calculator"></i></button>
          </div>
        </div-->

        <!--div class="form-group col-md-4 "  id="div_valor">
          <div class="col-md-12">
            <label> &nbsp;</label>
          </div>
          <div class="col-md-12">
            <button type="button" class="btn btn-info btn-xs"><i class="fa fa-calculator"></i></button>
          </div>
        </div-->


        <div class="form-group col-md-4 "  id="div_valor">
          <div class="col-md-12">
            <label> Valor Neto</label>
          </div>
          <div class="col-md-12">
            <span id="valor_neto_g" name="valor_neto_g"></span>
          </div>
        </div>

        <!--div class="form-group col-md-4 "  id="div_valor">
          <div class="col-md-12">
            <label> Valor Oda</label>
          </div>
          <div class="col-md-12">
            <span id="valor_oda_gc" name="valor_oda_gc"></span>
          </div>
        </div-->


        <div class="form-group col-md-6 ">
          <div class="col-md-6">
              <button id="crear" type="button" class="btn btn-info" onclick="guardar_gc();">Guardar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  <!--button type="button" class="btn btn-primary">Save changes</button-->
</div>    

<!--script src="{{ asset ("/js/jquery.validate.js") }}"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script-->
  <script type="text/javascript">
    $( document ).ready(function() {
      @if($forma_pago->count() > 0) 
        $('#oda_gc').prop('readonly', true);
      @endif
    });
    

    function validar_gc(){
      $('#div_transaccion_gc').hide();
      $('#div_banco_gc').hide();
      $('#div_id_tarjeta_gc').hide();
      $('#div_fi').hide();

      var forma_pago= $('#id_pago_gc').val();
      if(forma_pago == '2' || forma_pago == '3' || forma_pago == '5'){
        $('#div_transaccion_gc').show();
        $('#div_banco_gc').show();
      }

      if (forma_pago == '4' || forma_pago == '6') {
        $('#div_transaccion_gc').show();
        $('#div_banco_gc').show();
        $('#div_id_tarjeta_gc').show();
        $('#div_fi').show();

      }
    }

    function valor_oda(){
      var valor_base = $('#total_orden_venta').val();
      //alert(valor_base);
      valor_base     = parseFloat(valor_base);
      var p_oda      = $('#oda_gc').val();
      p_oda          = parseFloat(p_oda);

      var valor_cobrar_paciente = valor_base * p_oda/100;
      valor_cobrar_paciente     =  Math.round(valor_cobrar_paciente*100)/100;
      var valor_oda = valor_base - valor_cobrar_paciente;
      valor_oda =  Math.round(valor_oda*100)/100;
      var valor_neto = valor_base - valor_oda;
      valor_neto =  Math.round(valor_neto*100)/100;


      //console.log(valor_base, p_oda, valor_cobrar_paciente, valor_oda);
      //$('#total_gc').text(valor_neto);
      //$('#valor_odagc').text(valor_oda );

    }

    function valor_neto_gc(){
      var valor_base = $('#valor_gc').val();
      valor_base     = parseFloat(valor_base);
      var forma_pago = $('#id_pago_gc').val();
      

      var fi         = 0;
      var valor_neto = 0;
      if (forma_pago == '4') {
        //fi= 0.07;

      }

      if (forma_pago == '6') {
        //fi= 0.02;
      }
      //console.log(forma_pago, fi, valor_base);
      valor_neto= valor_base+(valor_base*fi);
      valor_neto =  Math.round(valor_neto*100)/100;

      //console.log(forma_pago, fi, valor_base, valor_neto, valor_cobrar_paciente);

      $('#valor_neto_g').text(valor_neto);
      //$('#valor_oda_gc').text(valor_oda);


    }

    function guardar_oda(){
      $.ajax({
        type: 'post',
        url:"{{ route('facturalabs.guardar_oda') }}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_oda_gc").serialize(),
        success: function(data){
            //console.log(data);
            if(data.estado =='ok'){
                //cargar_forma_pago_tabla(data.id_orden);
                //$("#crear_registro").hide();
                //alert("oda guardada");
                cargar_valor_oda(data.id_orden);
                cargar_forma_pago_gastro_tabla(data.id_orden);

            };
        },
        error: function(data){
            console.log(data);
        }
      });
    }

    function guardar_gc(){

      var forma_pago= $('#id_pago_gc').val();
      var transaccion= $('#transaccion_gc').val();
      var banco =$('#id_banco_gc').val();
      var tarjeta =$('#id_tipo_tarjeta_gc').val();
      var error='';
      if(forma_pago == '2' || forma_pago == '3' || forma_pago == '5'){
        if (transaccion == '') {
          error='Debe ingresar transaccion <br>';
        }
        if(banco == ''){
          error='Debe seleccionar el banco <br>';
        }

      }

      if (forma_pago == '4' || forma_pago == '6') {
        if(banco == ''){
          error='Debe seleccionar el banco <br>';
        }
        if (tarjeta== '') {
          error='Debe seleccionar el tarjeta <br>';
        }
      }
      //alert(error);
      if (error=='') {
        $.ajax({
            type: 'post',
            url:"{{ route('facturalabs.guardar_forma_gastro') }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_datos_gc").serialize(),
            success: function(data){
                //console.log(data);
                if(data.estado =='ok'){
                  cargar_forma_pago_gastro_tabla(data.id_orden);
                  $("#crear_registro_pago_gastroclinica").hide();
                  cargar_valor_oda(data.id_orden);

                };
            },
            error: function(data){
                console.log(data);
            }
          });
      }

    }

    function eliminar_forma_gastro(id_orden, id_forma){
      //alert("hola");
      $.ajax({
            type: 'get',
            url: "{{ url('cotizador/eliminar_forma_gastro')}}/"+id_orden+"/"+id_forma,
            datatype: 'json',
      success: function(data){
          //alert("sucess");
          if(data.estado=='ok'){
            cargar_forma_pago_gastro_tabla(data.id_orden);
            cargar_valor_oda(data.id_orden);

          };
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function agregar_forma_pago_gastro(){
      $('#valor_gc').val('');
      $('#transaccion_gc').val('');
      $('#valor_gc').val('');
      $('#id_tipo_tarjeta_gc').val('');
      $('#id_banco_gc').val('');
      $('#crear_registro_pago_gastroclinica').show();  
  }



  </script>
