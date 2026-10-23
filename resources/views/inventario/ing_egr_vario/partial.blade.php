<script>
let preventUnload = true;

$( document ).ready(function() {
    @if(@$pedido->id!=0)
      $('#id_proveedor').val('{{@$pedido->id_proveedor}}'); 
      $('#id_proveedor').trigger('change');

      $('#bodega_recibe').val('{{@$pedido->id_bodega}}'); 
      $('#bodega_recibe').trigger('change');
    @endif

    @if(@$cab_movimiento->id!=0)
      var t = @if (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==1) '{{"I"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==2) '{{"E"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==4) '{{"R"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==5) '{{"C"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==6) '{{"N"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==7) '{{"U"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==8) '{{"G"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==10) '{{"M"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==11) '{{"B"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==12) '{{"S"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==13) '{{"F"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==14) '{{"V"}}' @elseif (@$cab_movimiento->documento_bodega->id_inv_tipo_movimiento==15) '{{"O"}}' @endif;
      $('#tipo').val(t); 
      $('#tipo').trigger('change');

    @endif

      // Activar el evento beforeunload solo si es necesario
    window.addEventListener('beforeunload', function (e) {
          if (preventUnload) {
              e.preventDefault();
              e.returnValue = ''; // Muestra la alerta predeterminada del navegador
      }
    });
});

  var tipo_mov = 'I';
    $(document).ready(function(){ 
      $('#fecha_vence').datetimepicker({
          format: 'DD/MM/YYYY',
      });
      $( "#tipo" ).change(function() {
        var filas = $('#crear tr').length; 
        if (filas <= 1) {
          tipo_mov = $( "#tipo" ).val(); 
          if($( "#tipo" ).val() == 'E' || $( "#tipo" ).val() == 'C' || $( "#tipo" ).val()=='N' || $( "#tipo" ).val() == 'U'  || $( "#tipo" ).val() == 'M' || $( "#tipo" ).val() == 'B' || $( "#tipo" ).val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V'|| $( "#tipo" ).val()=='O'){ 
            //$('#codigo').prop('readonly', true);
            //$('#nombre').prop('readonly', false); 
            $('#busqueda').hide();
          } else {
            $('#codigo').prop('readonly', false);
            $('#nombre').prop('readonly', false); 
            $( "#codigo" ).val("");
            $( "#nombre" ).val(""); 
            $('#busqueda').show(); 
          }
        } else {
          $( "#tipo" ).val(tipo_mov);  
           Swal.fire("{{trans('winsumos.error')}}","{{trans('winsumos.no_cambiar_tipo_movimiento')}}" ,'error');
        }
      });

    });


    $('#example2').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': true,
            'info': false,
            'autoWidth': true,
            'columnDefs': [
                { "width": "5%", "targets": 0 },
                { "width": "5%", "targets": 2 },
                { "width": "10%", "targets": 6 },
                { "width": "5%", "targets": 8 }
            ],
            language: {
                zeroRecords: " "
            },
            buttons: [{
            extend: 'copyHtml5',
            footer: true
            },

            {
            extend: 'excelHtml5',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}"
            },
            {
            extend: 'csvHtml5',
            footer: true
            },
            {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'LEGAL',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}",
            customize: function(doc) {
                doc.styles.title = {
                color: 'black',
                fontSize: '17',
                alignment: 'center'
                }
            }
            }
        ],
        });

        function total_calculo(id){
          cantidad = parseInt($('#cantidad'+id).val());
          valor = parseFloat($('#precio'+id).val());
          total = cantidad * valor;
          descuento= parseFloat($('#descuento'+id).val());
          if(descuento>0){
            descf=(total*descuento/100);
            console.log("el valor del descuento es"+descf);
                  $("#descuentof"+id).val(descf.toFixed(2,2));
          }else{
            $("#descuentof"+id).val(0);
          }
    
    
          $('#precio_final'+id).text(parseFloat(total).toFixed(2));
          suma_totales();
        }
    
        function suma_totales(){
          console.log("suma_totales");
          contador  =  0;
          subtotal_0 = 0;
          subtotal_12 = 0;
          iva = 0;
          total = 0;
          desc=0;
          $("#crear tr").each(function(){
            $(this).find('td')[0];
            // visibilidad = $(this).find('#visibilidad'+contador).val();
            visibilidad = $('#visibilidad'+contador).val();
            if(visibilidad == 1){
              cantidad = parseInt($('#cantidad'+contador).val());
              valor = parseFloat($('#precio'+contador).val());
              total = cantidad * valor;
              descuento = parseFloat($('#descuentof'+contador).val());
              desc+=descuento;
              console.log(descuento);
              iva = $('#iva'+contador).val();
              if(iva == 1){
                subtotal_12 = subtotal_12 + total;
              }else{
                subtotal_0 = subtotal_0 + total;
              }
    
            }
            contador = contador+1;
          });
          @php
            $conf = \Sis_medico\Ct_Configuraciones::find(3);
          @endphp
          iva = subtotal_12 * {{$conf->iva}};
          if(desc>0){
            total= subtotal_12 + subtotal_0 + iva - desc;
          }else{
            total = subtotal_12 + subtotal_0 + iva;
          }
          $('#subtotal_12_1').val(subtotal_12.toFixed(2,2));
          $('#subtotal_0_1').val(subtotal_0.toFixed(2,2));
          $('#iva_1').val(iva.toFixed(2,2));
          $('#total_1').val(total.toFixed(2,2));
          $("#descuentx").val(desc.toFixed(2,2));
          $('#subtotal_12').val(subtotal_12);
          $('#subtotal_0').val(subtotal_0);
          $('#iva').val(iva);
          $('#total').val(total);
        }
    
        $(document).ready(function()
        {
          $('#fecha').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
                     //Important! See issue #1075
    
                });
          $('#vencimiento').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
    
                     //Important! See issue #1075
    
                });
          src = "{{route('producto.listar')}}";
    
          $("#codigo").autocomplete({
              source: function( request, response ) {
                $.ajax( {
                  url: src,
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response(data);
                  }
                } );
              },
              minLength: 1,
              select: function(data, ui) {
                $('#nombre').val(ui.item.nombre);
                //console.log(ui.item.value);

                if( $('#tipo').val() == 'E' ||  $('#tipo').val() == 'C' || $('#tipo').val() == 'N' || $('#tipo').val() == 'U' || $('#tipo').val() == 'P' || $('#tipo').val() == 'M' || $('#tipo').val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O'){
                  listar_series(ui.item.value);
                }
              }
            } );
    
          $("#codigo").change( function(){
              $.ajax({
                type: 'GET',
                url:"{{route('producto.codigo2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                datatype: 'json',
                data: $("#codigo"),
                success: function(data){
                  console.log(data.trim());
                  if(data.trim() != 'error'){
                    $('#nombre').val(data);
                    codigo = $('#codigo').val();
                    if( $('#tipo').val() == 'E' ||  $('#tipo').val() == 'C' || $('#tipo').val() == 'N' || $('#tipo').val() == 'U' || $('#tipo').val() == 'P' || $('#tipo').val() == 'M' || $('#tipo').val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O'){
                      listar_series(codigo);
                    }
                  }else{
                    $('#nombre').val("{{trans('winsumos.producto_no_encontrado')}}");
                  }
                },
                error: function(data){
                    console.log(data);
                }
              });
          });

          $("#serie").change( function(){
            var test = null;
            if( $('#tipo').val() == 'E' ||  $('#tipo').val() == 'C' || $('#tipo').val() == 'N' || $('#tipo').val() == 'U' || $('#tipo').val() == 'P' || $('#tipo').val() == 'M' || $('#tipo').val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O' ){
              listar_series(test);   
            }   
          });
    
          $('#busqueda').click(function(event){
            if($('#bodega_recibe').val()=="") {
              Swal.fire("{{trans('winsumos.error')}}","{{trans('winsumos.ingrese_bodega')}}","error");
              return;
            }
            if($('#codigo').val()!=""){
              $.ajax({
                  type: 'post',
                  url:"{{route('ingreso.formulario')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  datatype: 'json',
                  data: $("#frm_ingreso").serialize(),
                  success: function(data){
                    id= document.getElementById('contador').value;
                    var midiv = document.createElement("tr");
                      midiv.setAttribute("id","dato"+id);
                      var costo_promedio = 0; 
                      if(data[0].despacho == 0)//Código de producto
                      {
                        var f = new Date();
                        var dia = ('0' + (f.getDate())).slice(-2);
                        var mes = ('0' + (f.getMonth()+1)).slice(-2);
                        var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                        var id2 = id.slice(-1);
    
                        if ($('#serie').val()!="") {
                          var serie = $('#serie').val();
                        } else {
                          var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
                        }
    
                        // midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                        midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >  <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> "
                        +" <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number td_der'  onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;text-align: right;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> "
                        +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "
                        // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' @if(env('BODEGA_PRINCIPAL',13)==$value->id) selected @endif>{{$value->nombre}}</option> @endforeach</select></td>
                        +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"' name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                        +" <td><div class='form-group  lote_error"+id+"'> <input style='width: 90px;' type='text' name='lote"+id+"'  required> </div> </td> "
                        +" <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> "
                        +" <td> <div class='form-group fecha_vencimiento_error"+id+"'> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' > </div></td> "
                        +" <td> <input name='descuento"+id+"' id='descuento"+id+"' class='input-number td_der' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")' readonly='readonly'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td> "
                        +" <td><input class='td_der' onkeyup='total_calculo("+id+")' value='"+costo_promedio+"' id='precio"+id+"' type='text' style='width: 60px;' name='precio"+id+"' ></td> "
                        +" <td id='precio_final"+id+"' ></td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td> ";
                      }
                      if(data[0].despacho == 1)//Código de serie
                      {
                          var f = new Date();
                          var dia = ('0' + (f.getDate())).slice(-2);
                          var mes = ('0' + (f.getMonth()+1)).slice(-2);
                          var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                          var id2 = id.slice(-1);
    
                          var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
    
                          midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> "
                          +" <td  style='text-align: right;'> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> "
                          +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "
                          // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> "
                          +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"'  name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                          +" <td> <div class='form-group  lote_error"+id+"'><input style='width: 90px;' type='text' name='lote"+id+"' id='lote"+id+"' required></div></td> "
                          +" <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> "
                          +" <td> <div class='form-group fecha_vencimiento_error"+id+"'><input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></div></td> "
                          +" <td> <input style='width: 50%;' class='td_der' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00' readonly='readonly'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td> "
                          +" <td><input type='text' name='precio"+id+"' class='td_der' id='precio"+id+"' style='width: 60px;' value='"+costo_promedio+"' onkeyup='total_calculo("+id+")' ></td> "
                          +" <td id='precio_final"+id+"' ></td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td>  ";
                      }
    
                      document.getElementById('crear').appendChild(midiv);
                      //window.onbeforeunload = confirmarSalida;
                      preventUnload = true;
                      id = parseInt(id);
                      id = id+1;
                      document.getElementById('contador').value = id;
                      // $("#codigo").val("");
                      // $("#nombre").val("");
                  },
                  error: function(data){
                      console.log(data);
                  }
              })
            }
    
          });
    
          $('#envio').click(function(event){
            $('#envio').button('loading');
            var formulario = document.forms["frm_ingreso"];  
            // var id_empresa = formulario.id_empresa.value;
            var contador = formulario.contador.value;
            var tipo= formulario.tipo.value;
            var proveedor= formulario.id_proveedor.value;
            var msj = "";
            if(tipo == "")
                msj += "{{trans('winsumos.ingrese_tipo')}} <br/>".tipo;
            if(proveedor == "")
                msj += "{{trans('winsumos.ingrese_nombre_proveedor')}} <br/>".tipo;
            if(contador == 0)
                msj += "{{trans('winsumos.ingrese_producto')}} <br/>"; 
            if(msj == "")
            {
              /////////////////////////
              
              Swal.fire({
                  title: 'Esta seguro de guardar los cambios',
                  text: "No puedes revertir esta acccion!",
                  icon: 'error',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si'
              }).then((result) => {
                  if (result.isConfirmed) {
                    $('#envio').hide();
                    $.ajax({
                      type: 'post',
                      url:"{{route('inventario.ingresos.egresos.guardar')}}",
                      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                      datatype: 'json',
                      data: $("#frm_ingreso").serialize(),
                      beforeSend: function(){
                        @if(Auth::user()->id=='0931563241' ||Auth::user()->id=='0924383631') 
                        Swal.fire({
                          title: "{{trans('winsumos.procesando')}}...",
                          // text: 'Procesando..',
                          imageUrl: "https://c.tenor.com/DHkIdy0a-UkAAAAC/loading-cat.gif",
                          imageWidth: 400,
                          imageHeight: 200,
                          imageAlt: 'loading',
                        })
                        @endif
                      },
                      success: function(data){
                        console.log(data);
                        if(data.msj=='error'){
                          Swal.fire("{{trans('winsumos.error')}}","{{trans('winsumos.error')}} <br> "+data.error ,'error');
                          $('#envio').show();
                        }else{
                          $('#envio').button('reset');
                          var dato_url = "{{route('producto.index')}}";
                          //window.onbeforeunload = beforeVoid;
                          preventUnload = false;
                          Swal.fire("{{trans('winsumos.correcto')}}","{{trans('winsumos.Datos Ingresados Correctamente')}}",'success');
                          setTimeout(function(){ location.href ="{{ route('inventario.ingresos.egresos.varios')}}"; }, 3000);
                        }
        
                      },
                      error: function(data){
                        Swal.close();
                        console.log(data);
                          $('#envio').button('reset');
                          $('#envio').show();
                          var errores = data.responseJSON;
                          var contador = document.getElementById('contador').value;
                          var error ;
                          for(i=0; i<contador; i++) {
                            if(errores['lote'+i] != undefined){
                              error = errores['lote'+i];
                              $('.lote_error'+i).addClass("has-error");
        
                              $('.general').addClass("has-error");
                              $('#lote_errores').empty().html(errores['lote'+i]);
        
                            }
                            console.log(errores['fecha_vencimiento'+i]);
                            if(errores['fecha_vencimiento'+i]!= undefined){
                              //console.log('aqui');
                              $('.general').addClass("has-error");
                              $('.fecha_vencimiento_error'+i).addClass("has-error");
                              $('#fecha_errores').empty().html(errores['fecha_vencimiento'+i]);
                            }
                          }
                          if(data.responseJSON.pedido!=null){
                              $(".cl_pedido").addClass("has-error");
                              $('#str_pedido').empty().html(data.responseJSON.pedido);
                          }
                          if(data.responseJSON.id_proveedor!=null){
                              $(".cl_id_proveedor").addClass("has-error");
                              $('#str_id_proveedor').empty().html(data.responseJSON.pedido);
                          }
                      }
                    })


                  }
              })
              /////////////////////  
                    
            }
            else{
              $('#envio').button('reset');
              $('#envio').show();
              Swal.fire("{{trans('winsumos.error')}}",msj,"error");
            }
            $('#envio').button('reset');
    
    
          });
          src2 = "{{route('producto.nombre')}}";

          // actualizar
          $('#actualizar').click(function(event){
            $('#actualizar').button('loading');
            var formulario = document.forms["frm_ingreso"];  
            // var id_empresa = formulario.id_empresa.value;
            var contador = $('#crear tr').length;
            var tipo= formulario.tipo.value;
            var proveedor= formulario.id_proveedor.value;
            var msj = "";
            if(tipo == "")
                msj += "{{trans('winsumos.ingrese_tipo')}} <br/>".tipo;
            if(proveedor == "")
                msj += "{{trans('winsumos.ingrese_nombre_proveedor')}} <br/>".tipo;
            if(contador == 0)
                msj += "{{trans('winsumos.ingrese_producto')}} <br/>"; 
            if(msj == "")
            {
                $.ajax({
                  type: 'post',
                  url:"{{route('inventario.ingresos.egresos.actualizar')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  datatype: 'json',
                  data: $("#frm_ingreso").serialize(),
                  beforeSend: function(){
                    @if(Auth::user()->id=='0931563241' ||Auth::user()->id=='0924383631') 
                    Swal.fire({
                      title: "{{trans('winsumos.procesando')}}...", 
                      imageUrl: "https://c.tenor.com/DHkIdy0a-UkAAAAC/loading-cat.gif",
                      imageWidth: 400,
                      imageHeight: 200,
                      imageAlt: 'loading',
                    })
                    @endif
                  },
                  success: function(data){
                    console.log(data);
                    if(data.msj=='error'){
                      Swal.fire("{{trans('winsumos.error')}}", "{{trans('winsumos.error')}} <br> "+data.error ,'error');
                    }else{
                      $('#actualizar').button('reset');
                      var dato_url = "{{route('producto.index')}}";
                      window.onbeforeunload = beforeVoid;
                      Swal.fire("{{trans('winsumos.correcto')}}","{{trans('winsumos.guardado_exito')}}",'success');
                      setTimeout(function(){ location.href ="{{ route('inventario.ingresos.egresos.varios')}}"; }, 3000);
                    }
    
                  },
                  error: function(data){
                     Swal.close();
                     console.log(data);
                      $('#actualizar').button('reset');
                      var errores = data.responseJSON;
                      var contador = document.getElementById('contador').value;
                      var error ;
                      for(i=0; i<contador; i++) {
                        if(errores['lote'+i] != undefined){
                          error = errores['lote'+i];
                          $('.lote_error'+i).addClass("has-error");
    
                          $('.general').addClass("has-error");
                          $('#lote_errores').empty().html(errores['lote'+i]);
    
                        }
                        console.log(errores['fecha_vencimiento'+i]);
                        if(errores['fecha_vencimiento'+i]!= undefined){
                          //console.log('aqui');
                          $('.general').addClass("has-error");
                          $('.fecha_vencimiento_error'+i).addClass("has-error");
                          $('#fecha_errores').empty().html(errores['fecha_vencimiento'+i]);
                        }
                      }
                      if(data.responseJSON.pedido!=null){
                          $(".cl_pedido").addClass("has-error");
                          $('#str_pedido').empty().html(data.responseJSON.pedido);
                      }
                      if(data.responseJSON.id_proveedor!=null){
                          $(".cl_id_proveedor").addClass("has-error");
                          $('#str_id_proveedor').empty().html(data.responseJSON.pedido);
                      }
                  }
                })
            }
            else{
              $('#envio').button('reset');
              Swal.fire("Mensaje : ",msj,"error");
            }
            $('#envio').button('reset');
    
    
          });
    
          $("#nombre").autocomplete({
            source: function( request, response ) {
              $.ajax( {
    
                url: src2,
                dataType: "json",
                data: {
                  term: request.term
                },
                success: function( data ) {
                  response(data);
                }
              } );
            },
            minLength: 3,
            select: function(data, ui) {
              $('#codigo').val(ui.item.codigo);
            }
          });
    
          $("#nombre").change( function(){
              $.ajax({
                type: 'post',
                url:"{{route('producto.nombre2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                datatype: 'json',
                data: $("#nombre"),
                success: function(data){
                    $('#codigo').val(data);
                    codigo = $('#codigo').val();
                    if( $('#tipo').val() == 'E' ||  $('#tipo').val() == 'C' || $('#tipo').val() == 'N' || $('#tipo').val() == 'U'|| $('#tipo').val() == 'M' || $('#tipo').val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O'){
                      listar_series(codigo);
                    }
                },
                error: function(data){
                    console.log(data);
                }
            })
          });
        });
    
        $("#bodega_recibe").change( function(){
          var contador = $('#contador').val();
          if(contador > 0){
            Swal.fire({
                  title: 'Esta seguro?',
                  text: "Al cambiar la bodega se procede a reiniciar el listado",
                  icon: 'warning',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si'
              }).then((result) => {
                  if (result.isConfirmed) {
                    preventUnload = false;
                    location.href = '{{route("inventario.ingresos.egresos.crear")}}'   

                  }
              })
            //location.reload();
            
          }
          if( $('#tipo').val() == 'E' ||  $('#tipo').val() == 'C' || $('#tipo').val() == 'N' || $('#tipo').val() == 'U' || $('#tipo').val() == 'M' || $('#tipo').val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O'){
            var codigo = $('#codigo').val();
            listar_series(codigo);   
          } 
        });  
        function confirmarSalida()
        {
            return "{{trans('winsumos.abandonar_pagina')}}";
        }
        function beforeVoid()
        {}
    
        function valida(e){
            tecla = (document.all) ? e.keyCode : e.which;
    
            //Tecla de retroceso para borrar, siempre la permite
            if (tecla==8){
                return true;
            }
    
            // Patron de entrada, en este caso solo acepta numeros
            patron =/[0-9]/;
            tecla_final = String.fromCharCode(tecla);
            return patron.test(tecla_final);
        }
    
        function eliminardato(valor)
        {
          var nombre1 = "dato"+valor;
          var nombre2 = 'visibilidad'+valor;
          document.getElementById(nombre1).style.display='none';
          document.getElementById(nombre2).value = 0;
          // document.getElementById(nombre1).remove();
          suma_totales();
        }
    
        function calcular() {
            // obtenemos todas las filas del tbody
          var filas=document.querySelectorAll("#tbl_detalles tbody tr");
    
          var total=0;
    
          // recorremos cada una de las filas
          filas.forEach(function(e) {
    
              // obtenemos las columnas de cada fila
              var columnas=e.querySelectorAll("td");
    
              // obtenemos los valores de la cantidad y importe
              var cantidad=parseFloat(columnas[1].textContent);
              var importe=parseFloat(columnas[2].textContent);
    
              // mostramos el total por fila
              columnas[3].textContent=(cantidad*importe).toFixed(2);
    
              total+=cantidad*importe;
          });
    
          // mostramos la suma total  
          var filas=document.querySelectorAll("#tbl_detalles tfoot tr td");
          filas[1].textContent=total.toFixed(2);
        }

        function listar_series(codigo){
          $('#codigo').val(codigo);
          $.ajax({
            type: 'post',
            url:"{{route('inv_ingreso_egreso_vario.listar_series')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#frm_ingreso").serialize(),
            success: function(data){
              $('#dv_listado').empty().html(data);    
            },
            error: function(data){
                console.log(data);
            }
          })
        }
        function ingresar_por_serie(serie, id_bodega, vt_id){

          var cantidad = $('#cant_eg'+vt_id).val();
          var bandera = true;

          if(cantidad == ''){
            alert("Ingrese la cantidad a Egresar");
            bandera = false;
          }

          if(cantidad == null){
            alert("Ingrese la cantidad a Egresar");
            bandera = false;
          }

          if(bandera){
            var confirmar = confirm("Desea Egresar La Serie");
            if(confirmar){
              $.ajax({
                type: 'post',
                url:"{{route('ingreso.formulario_serie')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: { 'serie': serie, 'bodega_recibe': id_bodega, 'cantidad_egr': cantidad },
                success: function(data){
                  if( data[3]=='ok'){
                    //console.log(data);
                    id= document.getElementById('contador').value;
                    var midiv = document.createElement("tr");
                    midiv.setAttribute("id","dato"+id);
                    var costo_promedio = data[2].costo_promedio; 
                    /*if($( "#tipo" ).val() == 'E' || $( "#tipo" ).val() == 'C' || $( "#tipo" ).val()=='N' || $( "#tipo" ).val() == 'U'  || $( "#tipo" ).val() == 'M' || $( "#tipo" ).val() == 'B' || $('#tipo').val() == 'S' || $( "#tipo" ).val() == 'F' || $( "#tipo" ).val()=='V' || $( "#tipo" ).val()=='O'){
                      var costo_promedio = data[6];  
                    }*/
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);
          
                    var serie = data[1].serie;
          
                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> "
                    +" <td  style='text-align: right;'> <input type='text' id='cantidad"+id+"' name='cantidad"+ id +"' value='" + cantidad + "' readonly > </td> "
                    +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >" + serie +"</td>  "
                    +" <td> <input type='text' size='10' value='"+ data[5].nombre +"' readonly> <input type='hidden' id='id_bodega"+id+"'  name='id_bodega"+id+"' value='"+ data[5].id +"' >"
                    +" <td> <div class='form-group  lote_error"+id+"'><input style='width: 90px;' type='text' name='lote"+id+"' id='lote"+id+"' value='"+ data[1].lote +"' readonly='readonly'></div></td> "
                    +" <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> "
                    +" <td> <div class='form-group fecha_vencimiento_error"+id+"'><input type='date' class='input-number' value='"+ data[1].fecha_vence +"' name='fecha_vencimiento"+id+"' ></div></td> "
                    +" <td> <input style='width: 50%;' class='td_der' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00' readonly='readonly'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td> "
                    +" <td><input type='text' name='precio"+id+"' class='td_der' id='precio"+id+"' style='width: 60px;' value='"+costo_promedio+"' onkeyup='total_calculo("+id+")' readonly='readonly' ></td> "
                    +" <td id='precio_final"+id+"' ></td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td>  ";
          
                    document.getElementById('crear').appendChild(midiv);
                    //window.onbeforeunload = confirmarSalida;
                    preventUnload = true;
                    total_calculo(id);
                    id = parseInt(id);
                    id = id+1;
                    document.getElementById('contador').value = id;
                    // $("#codigo").val("");
                    // $("#nombre").val("");
                  }else{
                    alert(data[4]);
                  }  
                },
                error: function(data){
                    console.log(data);
                }
              })
            }  
          }  

        }
    
      </script>