<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .dataTable > tbody> tr:hover{
     background-color: #99ffe6;
  }
</style>


@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<section class="content">
  
  <div class="row">
    <h4>@if($seguro->id=='1')Cotización Particular @else Cotización para el seguro {{$seguro->nombre}}@endif No. {{$orden->id}}</h4>
    <div class="col-md-10" >
      <a target="_blank" href="{{route('cotizador.imprimir',['id' => $orden->id])}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Cotización</a>
      <a target="_blank" href="{{route('pdf_cotizacion',['id' => $orden->id])}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Recibo de Cobro</a>
      @if($orden->estado=='-1')
      <!--a href="{{route('cotizador.generar',['id' => $orden->id])}}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-ok"></span> Emitir Orden</a-->
      <a href="javascript:emitir()" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-ok"></span> Emitir Orden</a>
      @endif

      @if(is_null($agenda))
        <a align="right" type="button" class="btn btn-primary btn-sm" href="javascript:div_agendar()">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendar
          </a>
        @else
          @if($agenda->agenda->estado=='0')
          <a align="right" type="button" class="btn btn-primary btn-sm" href="javascript:div_agendar()">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendar
          </a>
          @else
          <a align="right" type="button" class="btn btn-success btn-sm" href="{{ route('agenda.edit2', ['id' => $agenda->id_agenda, 'doctor' => '4444444444'])}}" target="_blank">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendado
          </a>
          @endif
        @endif
       
        @if($orden->estado=='-1' || $rolUsuario=='1')
          
          @if($orden->cedula_factura=='')
          <button class="btn btn-danger btn-sm" onclick="alert('Ingrese la informacion de factura');"> Forma de Pago EndoscopyNet</button>
          @else
          <a id="boton_forma_pago_gas" class="btn btn-success btn-sm confirmar" href="{{route('orden.facturacion_gastroclinica',['id' =>$orden->id])}}" data-toggle="modal" data-target="#forma_pago_gas"> Forma de Pago EndoscopyNet</a>
          @endif
        @endif
      <a id="boton_info_factura" class="btn @if($orden->cedula_factura=='') btn-danger @else btn-success @endif btn-sm"  href="{{route('facturalabs.informacion_factura',['id' =>$orden->id])}}" data-toggle="modal" data-target="#informacion_factura"> Informacion de Factura</a>
      <a id="boton_forma_pago" class="btn btn-info btn-sm" href="{{route('facturalabs.forma_pago',['id_orden' =>$orden->id])}}" data-toggle="modal" data-target="#forma_pago"> Forma de Pago HumanLabs</a>
    </div>
    <div class="col-md-2" >
      <div class="row" >
        <div class="col-md-6" style="text-align: right;">        
          <b>@lang('laboratorio.cantidad'): </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          <span id="scantidad">{{ $orden->cantidad }}</span>
        </div>
        <div class="col-md-6" style="text-align: right;">        
          <b>SubTotal: </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          $ <span id='svalor'>@if($orden->cobrar_pac_pct < 100) {{ $orden->valor_con_oda }} @else {{$orden->valor}} @endif</span>
        </div>
        <div class="col-md-6" style="text-align: right;">        
          <b>Descuento: </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          (-)$ <span id="sdescuento_valor"> {{$orden->descuento_valor}} </span>
        </div>
        <div class="col-md-6" style="text-align: right;">        
          <b>F.Admin: </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          $ <span id="srecargo_valor">{{$orden->recargo_valor}}</span>
        </div>
        <div class="col-md-6" style="text-align: right;">        
          <b>Total: </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          $ <span id="stotal_valor">@if($orden->cobrar_pac_pct < 100) {{ round( $orden->total_con_oda,2 ) }} @else {{ round( $orden->total_valor,2 )}} @endif</span>
        </div>
        @if($orden->cobrar_pac_pct < 100)
        <div class="col-md-6" style="text-align: right;">        
          <b>Oda: </b>
        </div>
        <div class="col-md-6" style="text-align: right;">
          $ <span id="stotal_valor"> {{ number_format($orden->valor - $orden->valor_con_oda, 2, '.', '') }}</span>
        </div>
        @endif
      </div>
    </div>    
<!--table class="col-md-12">
  <tr>
    <td></td>
    <td><b>Cantidad: </b></td>
    <td><span id="scantidad">{{$orden->cantidad}}</span></td>
    <td><b>SubTotal: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id='svalor'>@if($orden->cobrar_pac_pct < 100) {{ $orden->valor_con_oda }} @else {{$orden->valor}} @endif</span></td>
  </tr>
  <tr>
    <td>

        

    </td>

    <td></td>
    <td></td>
    <td><b>Descuento: </b></td>
    <td style="font-size: 20px;text-align: right;">(-)$ <span id="sdescuento_valor"> {{$orden->descuento_valor}} </span></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Fee Administrativo: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="srecargo_valor">{{$orden->recargo_valor}}</span></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Total: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="stotal_valor">@if($orden->cobrar_pac_pct < 100) {{ round( $orden->total_con_oda,2 ) }} @else {{ round( $orden->total_valor,2 )}} @endif</span></td>
  </tr>
</table-->

<input type="hidden" name="tiene_domicilio" id="tiene_domicilio" value="{{$tiene_domicilio}}">
<input type="hidden" name="tiene_covid" id="tiene_covid" value="{{$tiene_covid}}">
<input type="hidden" name="valor_covid" id="valor_covid" value="{{$valor_covid}}">
<input type="hidden" name="stotal" id="stotal" value="{{$orden->total_valor}}">


<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <tbody>

          @foreach($agrupador_labs as $agrupador)
            @php
              $examenes_labs2 = $examenes_labs->where('id_examen_agrupador_labs',$agrupador->id);
            @endphp
            @if($examenes_labs2->count()>0)

              <tr>
                <td style="background-color: #ff6600;color: white;margin: 0px;">{{$agrupador->nombre}}</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">Valor</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">CP</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">Convenio</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">No Cubre</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">Sel.</td>
                <td style="background-color: #ff6600;color: white;margin: 0px;">Fee</td>
              </tr>
            @endif
            @foreach($examenes_labs2 as $examen)
              <!-- si estuvo antes seleccionado se muestra el estado 0 es en agrupador sabana -->
              @if($examen->estado=='0' && !in_array($examen->ex_id,$detalles_ch))
              @else
                @if($examen->id_examen_agrupador_labs==$agrupador->id)
                @php
                 $nombre_examen = "";
                if($examen->tiempo_examen ==""|| is_null($examen->tiempo_examen)  ){
                  $nombre_examen = "";
                }else{
                   $nombre_examen = ' - TIEMPO DE ENTREGA: ' . $examen->tiempo_examen;
                }
                  @endphp
                  <tr @if(in_array($examen->ex_id,$detalles_ch)) style="background-color: #b3e0ff;" @endif>
                    <td> <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="{{$examen->sugerencia}}">{{$examen->nombre}}</button> <span style="font-weight:bold;font-size:small">{{$examen->nombre_largo}}</span></td>
                    @php
                      $e_valor = $examen->valor;
                      $cubre = true;
                      $examen_valor = null;
                      if($id_nivel!=null){

                        $examen_valor = $examen_valor_o->where('id_examen',$examen->ex_id)->first();
                        if(!is_null($examen_valor)){
                          if($seguro->id!='1' && ($examen_valor->valor1=='0' || $examen_valor->valor1==null)){
                            $cubre = false;
                          }else{
                            $e_valor = $examen_valor->valor1;
                            $cubre = true;
                          }
                        }else{
                          $cubre = false;
                        }
                      }
                      /*
                      if(($orden->total_valor - $valor_covid) >= 80){

                        if($examen->ex_id=='1191' || $examen->ex_id=='1195' || $examen->ex_id=='1196'){
                          $e_valor = 0;
                        }
                      }
                      */

                    @endphp
                    <td>$ {{number_format($e_valor,2)}}</td>
                    @php $valor_con_oda = ''; @endphp
                    @if(in_array($examen->ex_id,$detalles_ch))
                      @php
                        $item = $orden->detalles->where('id_examen',$examen->ex_id)->first();
                        if(!is_null($item)){
                          $valor_con_oda = $item->valor_con_oda;
                        }
                      @endphp 
                    @endif
                    <td>$ {{ $valor_con_oda }} </td>
                    <td>@if(!$cubre) No @else Si @endif</td>
                    <td style="text-align: center;"> @if($cubre) @if(in_array($examen->ex_id,$detalles_ch) && !is_null($item)) <input type="checkbox" name="sin_cobertura" id="sin_cobertura{{$item->id}}" onchange="actualizarSinCobertura('{{$item->id}}', this.checked)"  @if($item->sin_cobertura == 1) checked @endif> @endif @endif</td>
                    <td ><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange {{$agrupador->nombre}}" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif ></td>
                    <td >
                      @if(in_array($examen->ex_id,$detalles_ch) )
                      <input id="fee{{$examen->ex_id}}" name="fee{{$examen->ex_id}}" type="checkbox" @if(!is_null($item)) @if($item->tiene_fee == '1') checked @endif  @endif onclick="carga_fee('{{$examen->ex_id}}')">
                      @endif    
                    </td>  
                  </tr>
                @endif
              @endif
            @endforeach

          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">
  $('#forma_pago').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    });

  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  })

  deshabilitar_membresias();

  function deshabilitar_membresias(){

    var mem = $('#id_plan').val();
    if(mem != ''){
      
      $('input[type="checkbox"].MEMBRESIAS').iCheck('disable');
    }
    
  }

  domicilio();//promo_covid();
  function domicilio(){
    //console.log("domicilio");
    var domicilio = $('#pres_dom').val();
    var tiene_dom = $('#tiene_domicilio').val();//console.log(tiene_dom);

    if(domicilio=='1'){
      if(tiene_dom=='0'){
        var confirmar = confirm("¿Desea agregar el valor de la toma a domicilio?");
        if(confirmar){
          
          cotizador_crear_id('1203');

              
        }
        
        //cargar_buscador(); 

      }
    }  
      
  }
  //EMITIR SIN FACTURACION ELECTRONICA
  function emitir(){
    @php $puntos = intdiv($orden->total_valor , 25); @endphp
    var mem    = $('#id_plan').val();
    var puntos = '{{ $puntos }}';
    var total  = '{{ $orden->total_valor }}';


    if(mem != '' && total > 50){
    
      var flag = false;
      swal.fire({
        title: 'Paciente con Membresia acumula los siguientes puntos: '+ puntos,
        //text: "You won't be able to revert this!",
        icon: "success",
        type: 'success',
        buttons: true,
      
      }).then((result) => {
        $.ajax({
          type: 'get',
          url:"{{route('facturalabs.cuadrar',['id' => $orden->id])}}",
          datatype: 'json',
          success: function(data){
            //alert(data);
            if (data == 'ok') {
              validar_correo()
              //MOVER POR VALIDACION DE CORREO
              /*swal.fire({
                title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}}',
                //text: "You won't be able to revert this!",
                icon: "warning",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                buttons: true,
              
              }).then((result) => {
                if (result.value) {
                  cierre_caja()
                  location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";                
                }
              });*/
              //MOVER POR VALIDACION DE CORREO  
            }
            if(data == 'no'){
              alert("El valor de las formas de pago no coincide con la cotización");
            }
          },
          error: function(data){
            //console.log(data);
          }
        }); 
      })

    }else{
      $.ajax({
          type: 'get',
          url:"{{route('facturalabs.cuadrar',['id' => $orden->id])}}",
          datatype: 'json',
          success: function(data){
            //alert(data);
            if (data == 'ok') {
              validar_correo()
              //MOVER POR VALIDACION DE CORREO
              /*swal.fire({
                title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}}',
                //text: "You won't be able to revert this!",
                icon: "warning",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                buttons: true,
              
              }).then((result) => {
                if (result.value) {
                  cierre_caja()
                  location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";                
                }
              });*/
              //MOVER POR VALIDACION DE CORREO  
            }
            if(data == 'no'){
              alert("El valor de las formas de pago no coincide con la cotización");
            }
          },
          error: function(data){
            //console.log(data);
          }
        });
    }      
  
  }

  function validar_correo(){
    $.ajax({
      type: 'get',
      url:"{{route('orden.labs_validar_mail',['id' => $orden->id])}}",
      datatype: 'json',
      success: function(data){
        $('#ax_mail').html(data);
        $('#mail').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });

  }

  function cuadrar(){


  }
  function cierre_caja(){
    /*$.ajax({
      type: 'post',
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      url:"{{route('c_caja.storeLabs')}}",
      data:{'id_orden': '{{$orden->id}}','id_paciente':'{{$orden->id_paciente}}','total':'{{$orden->total_valor}}'},
      datatype: 'json',
      success: function(data){
          console.log(data);
          //alert(data)
      },
      error: function(data){
        //console.log(data);
        //alert(data)
      }
    }); */
  }
  //CON FACTURACION ELECTRONICA
  function emitir_sri(){

    var seguro = $('#id_seguro').val();
    if(seguro=='1'){//PARTICULARES DEBE FACTURAR
      solicita_datos_facturas();
    }else{
      swal.fire({
        title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}}',
        //text: "You won't be able to revert this!",
        icon: "warning",
        type: 'warning',
        buttons: true,

      }).then((result) => {
        if (result.value) {
          location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";
        }
      })
    }


  }

  function solicita_datos_facturas(){
    $.ajax({
      type: 'get',
      url:"{{route('facturalabs.datos_factura',['id' => $orden->id])}}",
      datatype: 'json',
      success: function(data){
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });
  }

  function promo_covid(){
    /*var promo = 80; //VARIABLE TOPE DE PROMOCION
    var habilitado = 1;//VARIABLE PARA HABLITAR EN PRODUCCION
    var ctotal = $('#stotal').val();
    var tiene_covid = $('#tiene_covid').val();
    var valor_covid = $('#valor_covid').val();
    var valor_reconfirmar = 0;

    if(habilitado){
      if(ctotal >= promo){
        if(tiene_covid=='1'){
          if(valor_covid>0){
            valor_reconfirmar = ctotal - valor_covid;
            if(valor_reconfirmar>=promo){
              alert("El valor de la orden supera los $"+promo+", El valor del Covid se actualizará a 0.00");
              cotizador_recalcular();
            }
          }
        }else{
          var confirmar2 = confirm("El valor de la orden supera los $"+promo+", Desea agregar Examen Covid");
          if(confirmar2){
            cotizador_crear_id('1191');
            //$('#seleccionados').iCheck('check');
            //cargar_buscador();
          }
        }
      }else{
        if(tiene_covid=='1'){
          if(valor_covid<1){
              alert("El valor de la orden no supera los $"+promo+", El valor del Covid se actualizará");
              cotizador_recalcular();
          }
        }
      }
    } */
    /*$.ajax({
      type: 'post',
      url:"#",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#fecha_enviar").serialize(),
      success: function(data){

        $('#div_agendar').empty().html(data);
      },
      error: function(data){
        //console.log(data);
      }
    });*/


  }



  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

    //console.log(this.name.substring(2));
    //alert("crea");
    @if($orden->puntos_aplicados == null)
    cotizador_crear_id(this.name.substring(2));
    @else
    nosepuedeeditar();
    
    @endif
    

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){

    //cotizador_crear();
    //alert("borra");
    @if($orden->puntos_aplicados == null)
    cotizador_delete_id(this.name.substring(2));
    @else
    nosepuedeeditar();
    @endif


  });



  function div_agendar(){

    $.ajax({
      type: 'get',
      url:"{{ url('seguros_privados/agenda_labs/agendar') }}",
      success: function(data){
        $('#agenda_privados').empty().html(data);
        $('#modal_privados').modal();
      },
      error: function(data){
        
      }
    }); 
  } 
  function aplicar_puntos(puntos){

    @if($orden->puntos_aplicados == null)
    $.ajax({
      type: 'get',
      url: "{{ route('orden.validar_puntos',['id' => $orden->id]) }}", 
               
      success: function(data){
            swal.fire({
              title: 'Puede Aplicar ' + data.msn + ' Puntos en la orden',
              text: "No podra revertir esta operacion, ni modificar la cotización, Desea Continuar?",
              icon: "danger",
              type: 'danger',
              buttons: true,
            
            }).then((result) => {
              if (result.value) {
                aplicar_final();
              }
            })       
      },
      error: function(data){
          
      }    
    }); 
    @else
    alert("Ya estan aplicados los puntos");
    @endif

  }

  function aplicar_final(){

    $.ajax({
      type: 'get',
      url: "{{ route('orden.aplicar_puntos',['id' => $orden->id]) }}", 
               
      success: function(data){
        if(data.estado == 'ok'){
          
          $('#descuento_p').val(data.descuento_p);
          recalcularsinpreguntar();
          $('#descuento_p').attr("readonly","readonly");
          $('#motivo_descuento').attr("readonly","readonly");
          location.reload();
        }             
      },
      error: function(data){
          
      }    
    }); 

  }
  
    $('.confirmar').on('click', function() {
        return confirm('¿Esta seguro de querer generar la forma de pago?');
    });

    function carga_fee(id_ex){

      var fee = document.getElementById('fee'+id_ex);
      
    
        if (fee.checked) {
            cargar_fee(id_ex,'1');
        } else {
            cargar_fee(id_ex,'0');
        }
      

    }

    function cargar_fee(id_ex, tipo){

      $.ajax({
        type: 'get',
        url: "{{ url('fee_salud/laboratorio') }}/{{ $orden->id }}/"+ id_ex +"/"+ tipo, 
                
        success: function(data){
          
            recalcularsinpreguntar();
            
                  
        },
        error: function(data){
          console.log("errro");    
        }    
      }); 

    }

    function actualizarSinCobertura(id, isChecked) {
        let valor = isChecked ? 1 : 0;

        $.ajax({
            type: 'POST',
            url: "{{ url('cotizador/actualizar_sin_cobertura') }}/" + id,
            data: {
            _token: '{{ csrf_token() }}', // Es necesario enviar el token CSRF para proteger contra ataques CSRF
            sin_cobertura: valor
            },
            success: function(response) {
            console.log("Sin cobertura actualizado correctamente.");
            recalcularsinpreguntar();
            //location.reload();
            // Puedes actualizar la UI o hacer otra acción aquí si es necesario
            },
            error: function(error) {
            console.log("Error al actualizar sin cobertura:", error);
            }
        });
    }
</script>    