<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">


<div class="card">
  <div class="card-header bg bg-primary">
      <div class="col-md-2">
          <i style="color: white;">{{trans('emergencia.OrdenNo.')}} {{$orden->id}}</i>
      </div>  
      <div class="col-md-10" style="text-align: center;color: white;">
          {{trans('emergencia.DetalledeOrden')}} {{$orden->seguro->nombre}}
      </div>
  </div>
  <div class="card-body" style="margin: 10px 10px;padding: 0;">
    <form id="form_buscador">
    
    <div class="row">
      <div class="form-group col-3" style="margin-bottom: 0;padding-right: 0;">
        <div class="input-group mb-3" style="margin-bottom: 0;">
            <input id="buscador" name="buscador" type="text" class="form-control" placeholder="Buscar Examen" onchange="quitar_todos();cargar_buscador();">
            <div class="input-group-append">
              <span class="input-group-text"><i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador').value = '';cargar_buscador()"></i></span>
            </div>
         </div>
      </div>
      <div class="form-group col-3" style="margin-bottom: 0;padding-right: 0;">
        <div class="input-group mb-3" style="margin-bottom: 0;">
            <input id="buscador2" name="buscador2" type="text" class="form-control" placeholder="Buscar Agrupador" onchange="quitar_todos();cargar_buscador();">
            <div class="input-group-append">
              <span class="input-group-text"><i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador2').value = '';cargar_buscador()"></i></span>
            </div>
         </div>
      </div>
      <div class="form-group col-3" style="margin-bottom: 0;padding-right: 0;">
        <div class="input-group mb-3" style="margin-bottom: 0;">
            <select id="id_protocolo" name="id_protocolo" type="text" class="form-control" onchange="cambia_perfil()">
              <option value="">{{trans('emergencia.SeleccionePerfil')}}</option>
              @foreach($protocolos as $protocolo)
                <option @if($orden->id_protocolo == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
              @endforeach
            </select>  
         </div>
      </div>
      <div class="form-group col-1" style="margin-bottom: 0;">
        <button class="btn btn-success" onclick="" type="button">{{trans('emergencia.Buscar')}}</button>
      </div>  
      
      <div class="form-group col-3" style="margin-bottom: 0;">
        <input id="seleccionados" name="seleccionados" type="checkbox" class="flat-red" onchange="quitar_buscador();cargar_buscador();" value="1" @if(count($detalles_ch) > 0) checked @endif><label style="color: red;font-size: 14px;">{{trans('emergencia.VerSeleccionados')}}</label>
      </div>
      <!--div class="form-group col-4" style="margin-bottom: 0;">
        <input id="firma_dr" name="firma_dr" type="checkbox" class="flat-green" onchange="cargar_buscador();" value="1" @if($orden->doctor_firma=='1307189140') checked @endif><label style="color: green;font-size: 14px;"> Firma del Dr. Robles</label>
      </div-->
      <div class="form-group col-4" style="margin-bottom: 0;">
        <button type="button" class="btn btn-warning btn-sm" onclick="deseleccionar();"><span style="color: white;">{{trans('emergencia.Deseleccionar')}}</span></button>
      </div>  

    </div>

  </form> 


  <div id="listado_final"> 

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-hover" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <tbody>
              @php  $cambia = 0; $contador = 0; @endphp 
              @foreach($examenes_labs as $examen)
                @if($examen->estado=='0' && !in_array($examen->ex_id,$detalles_ch))
                @else
                  @if($cambia != $examen->id_examen_agrupador_labs)
                    @php $contador = 0; @endphp
                    <tr>
                        <td colspan="4" style="background-color: #ff6600;color: white;margin: 0px;padding: 0;">{{$agrupador_labs->where('id',$examen->id_examen_agrupador_labs)->first()->nombre}}</td>
                      </tr>
                      @php $cambia = $examen->id_examen_agrupador_labs; @endphp 
                  @endif
                  @if($contador == 0)
                  <tr >
                  @endif  
                        <td style="padding: 5px;@if(in_array($examen->ex_id,$detalles_ch)) background-color: #b3e0ff; @endif"><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif> </td> 
                        <td style="padding: 5px;@if(in_array($examen->ex_id,$detalles_ch)) background-color: #b3e0ff; @endif" >{{$examen->descripcion}}</td>  
                        @php $contador ++; @endphp
                        @if($contador == 2) @php $contador = 0; @endphp @endif
                      @if($contador == 0)   
                      </tr>
                      @endif
                @endif      
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
    </div>

  </div>  
  </div>
</div>  
    


  
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  });
  $('input[type="checkbox"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  });
  $('input[type="checkbox"].flat-green').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  });

  function quitar_todos(){
    $('#seleccionados').iCheck('uncheck');
  }

  function quitar_buscador(){
    $('#buscador').val('');
    $('#buscador2').val('');
  }

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
    //console.log(this.name.substring(2));
    cotizador_crear_id(this.name.substring(2));
  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
    //cotizador_crear();
    cotizador_delete_id(this.name.substring(2));
  });

  $('input[type="checkbox"].flat-red').on('ifChecked', function(event){
    quitar_buscador();
    cargar_buscador();
  });

  $('input[type="checkbox"].flat-red').on('ifUnchecked', function(event){
    cargar_buscador();
  });

  $('input[type="checkbox"].flat-green').on('ifChecked', function(event){
    cargar_buscador();
  });

  $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
    cargar_buscador();
  });

  function cargar_buscador(){
  	$.ajax({
        type: 'post',
        url:"{{route('decimopaso.buscar_examenes',['id_orden' => $orden->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_buscador").serialize(),
        success: function(data){
            //console.log(data);
            $('#listado_final').empty().html(data);
        },
        error: function(data){
                
            }
    })
  }

  function cambia_perfil(){
    var confirmar = confirm("Al cambiar el perfil, perderá los exámenes previamente seleccionados");
    if(confirmar){
      $.ajax({
        type: 'post',
        url:"{{route('hc4_orden_lab.cambia_perfil',['id_orden' => $orden->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_buscador").serialize(),
        success: function(data){
            //console.log(data);
            $('#listado_final').empty().html(data);
        },
        error: function(data){
                
            }
      })

    }
  }

  function cotizador_crear_id(id){//ES LA MISMA FUNCION QUE RECEPCION PILAS
        //alert(id);
        $.ajax({
            type: 'get',
            url:"{{url('cotizador/update')}}/{{$orden->id}}/"+id,
            
            datatype: 'json',
            
            success: function(data){
                //alert(data);
                //cargar_buscador();
                //cotizador_recalcular();
                $('#scantidad').empty().html(data.cantidad);
                $('#svalor').empty().html(data.valor);
                $('#sdescuento_valor').empty().html(data.descuento_valor);
                $('#srecargo_valor').empty().html(data.recargo_valor);
                $('#stotal_valor').empty().html(data.total_valor);

                
                
            },
            error: function(data){
                    
                }
        })
    }

    function cotizador_delete_id(id){
        //alert(id);
        $.ajax({
            type: 'get',
            url:"{{url('cotizador/delete')}}/{{$orden->id}}/"+id,
            datatype: 'json',
            success: function(data){
                //alert(data);
                //cargar_buscador();
                //cotizador_recalcular();
                $('#scantidad').empty().html(data.cantidad);
                $('#svalor').empty().html(data.valor);
                $('#sdescuento_valor').empty().html(data.descuento_valor);
                $('#srecargo_valor').empty().html(data.recargo_valor);
                $('#stotal_valor').empty().html(data.total_valor); 
            },
            error: function(data){
                    
            }
        })
    }

    function deseleccionar(){
      var confirmar = confirm("Perderá los exámenes previamente seleccionados");
      if(confirmar){
        $.ajax({
          type: 'get',
          url:"{{route('hc4_orden_lab.deseleccionar_perfil',['id_orden' => $orden->id])}}",
          datatype: 'json',
          success: function(data){
              //console.log(data);
              $('#listado_final').empty().html(data);
          },
          error: function(data){
                  
              }
        })

      }  
    }


  
</script>    