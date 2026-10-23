

<div class="modal-header" style="padding: 2px;">
  <div class="col-md-10" style="padding: 2px;"><h4>Formas de Pago HumanLabs</h4></div>
  <div class="col-md-2" style="padding: 2px;">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
  </div>
</div>

<div class="modal-body"> 
  <div class="box-body">
    <div class="form-group col-md-3">
        <a type="button" class="btn btn-info btn-sm" onclick="crear_forma_pago('{{$id_orden}}');" > Crear</a>  
    </div>
  
    <div id="forma_pago_tabla">
      <div class="col-md-6">
        <label>Total a Pagar:</label>
        <span><b>$ @if($orden->cobrar_pac_pct < 100) {{ round( $orden->total_con_oda,2 ) }}  @else {{ round( $orden->total_valor,2 ) }} @endif</b></span> 
        @if($orden->cobrar_pac_pct < 100)
          <label>Oda:</label>@php $dif = $orden->total_valor - $orden->total_con_oda; @endphp
          <span><b>$ {{ $dif }}</b></span>   
        @endif
      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Tipo Pago</th>
                  <th>Numero Transaccion</th>
                  <th>Tarjeta</th>
                  <th>Banco</th>
                  <th>Valor</th>
                  <th>Fi Administrativo</th>
                  <th>Valor Neto</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @php
                $total_pago=0;
                @endphp
                @foreach($forma_pago as $value)
                @php
                  $fi = 0;
                  $valor_neto =0;
                  if ($value->id_tipo_pago == '4') {
                    /*$fi= 0.07;*/

                  }
                  if ($value->id_tipo_pago == '6') {
                    /*$fi= 0.045;*/
                  }
                  $valor_neto= round($value->valor+($value->valor*$fi),2);
                  $total_pago += round($valor_neto,2);
                @endphp
                <tr>
                  <td>{{$value->tipo_pago->nombre}}</td>
                  <td>{{$value->numero}}</td>
                  <td>@if($value->tipo_tarjeta != null) {{$value->tarjetas->nombre}} @endif</td>
                  <td>@if($value->banco != null) {{$value->bancos->nombre}} @endif</td>
                  <td>{{$value->valor}}</td>
                  <td>{{$fi}}</td>
                  <td>{{round($valor_neto,2)}}</td>
                  <td><a type="button" onclick="eliminar('{{$id_orden}}','{{$value->id}}')" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
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
                  @php  
                    $total_con_oda = round($orden->total_con_oda, 2);  
                    $total_con_valor = round($orden->total_valor, 2);
                  @endphp
                  <span>@if($orden->cobrar_pac_pct < 100) {{ $total_con_oda - $total_pago }} @else {{ $total_con_valor - $total_pago }} @endif</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12" > 
      <div id="crear_registro"></div>
    </div>
    
    @if(is_null($orden->venta_gastro))
    <!--div class="col-md-12" id="div_boton_gastro">
      <a type="button" class="btn btn-info btn-sm" onclick="crear_forma_pago_gastroclinica();" > Forma pago Gastro Clinica </a>
    </div-->
    @endif  
    
      
    
  </div>  
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>


<script type="text/javascript">

  //crear_orden_venta('{{$id_orden}}');

  
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
        //console.log(data);
      },


      error: function(data){

        if(data.responseJSON.valor!=null){
            $('#dvalor').addClass('has-error');
            alert(data.responseJSON.valor[0]);
        }


      }
    });
  }

  

  
  function eliminar(id_orden, id_forma){
      //alert("hola");
      $.ajax({
            type: 'get',
            url: "{{ url('cotizador/eliminar_forma')}}/"+id_orden+"/"+id_forma,
            datatype: 'json',
      success: function(datahtml){
          //alert("sucess");
          if(datahtml.estado=='ok'){
            cargar_forma_pago_tabla(datahtml.id_or);
          };
            
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  
</script>