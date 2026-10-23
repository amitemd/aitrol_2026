


<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .table-hover>tbody>tr:hover{
      background-color: #b3ffe6;
      cursor:pointer;
    }
    p{
      font-size:12px;
    }

</style>

@php
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp



<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea_sub" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                          <div class="col-md-6"><h4>Paciente : <span style="color: red;">{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif</span></div>
                          <div class="col-md-2"><h4>Edad : <span id="edad" name="edad" style="color: red;"></span></h4></div>

                          
                          <button class="btn btn-primary btn-sm" id="btn_val">Imprimir</button>

                          <button class="btn btn-success btn-sm" id="btn_val" onclick="certificar_todo()">Certificar Todo</button>
                          <div id="espera_gif" style="display: none" ><img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 10%;"></div>
                         
                        </div>
                    </div>
                    
                    <div class="form-group col-md-12 col-xs-12" style="padding-left: 0px;padding-right: 0px;">
                      <label for="fecha" class="texto col-md-3 control-label">Fecha Resultado</label>
                      <div class="col-md-9">
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control input-sm" name="fecha_resultado" id="fecha_resultado" autocomplete="off" value="@if($orden->fecha_convenios != null){{date('Y/m/d H:i:s',strtotime($orden->fecha_convenios))}}@else{{date('Y/m/d H:i:s',strtotime($orden->fecha_orden))}}@endif">
                          <div class="input-group-addon">
                            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_resultado').value = ''; buscar();"></i>
                          </div>
                        </div>
                      </div>
                    </div> 
                    
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>GRUPO</th>
                          <th>EXAMEN</th>
                          <th>PARAMETRO</th>
                          <th>RESULTADO</th>
                          <th>UNIDADES</th>
                          <th>REFERENCIA</th>
                          <td></td>
                          <th>CERTIFICAR</th>
                        </tr>
                      </thead>
                      <tbody>@php $exa_ant = '';$cont_par = 0;$tot_par = 0; @endphp
                        @foreach($resultados as $resultado)
                          
                          @if($resultado->texto!=null)
                            <!--div style="border-bottom: 1px solid #009a98;padding: 0px;">
                              <?php echo $resultado->texto; ?>
                            </div-->
                          @endif
                          @php
                            $fl_ref = 0;
                            $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$resultado->id_parametro)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                            if(!is_null($ref_ant)){
                              $fl_ref = 1;
                            }
                          @endphp
                          @if($resultado->resultado_especial == 'CUADRO_POS')
                          @if($resultado->id_parametro_ref == null)
                          <tr>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->grupo}} {{$resultado->grupo2}}<br>
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->examen}}<br>
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->parametro}}<br>
                              @if($resultado->tiene_detalle=='1') 
                                @php
                                if($exa_ant != $resultado->id_examen){
                                  $exa_ant = $resultado->id_examen;
                                  $cont_par = 1;
                                  $xorden = Sis_medico\Examen_Orden::find($resultado->id_orden);
                                  $xresultados = $xorden->resultados;
                                  $tot_par = count($xresultados->where('id_examen',$resultado->id_examen));
                                }else{
                                  $cont_par ++;
                                }
                                @endphp
                                @if($cont_par == '1')
                                <a href="{{route('subresultado.crear',['id_orden' => $orden->id, 'id_examen' => $resultado->id_examen])}}" data-toggle="modal" data-target="#edit_crea_sub" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-plus"></span> Detalle Adicional</a>
                                @endif
                              @endif
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">     
                              <input type="radio" id="par_res_{{$resultado->id_parametro}}_neg" name="par_res_{{$resultado->id_parametro}}" value="NEGATIVO" class="flat-red" @if(!is_null($resultado)) @if($resultado->valor=='NEGATIVO') checked="checked" @endif @endif>
                              <label for="html" style="font-size: 10px">NEGATIVO</label>
                              <input type="radio" id="par_res_{{$resultado->id_parametro}}_pos" name="par_res_{{$resultado->id_parametro}}" value="POSITIVO" class="flat-red" @if(!is_null($resultado)) @if($resultado->valor=='POSITIVO') checked="checked" @endif @endif>
                              <label for="css" style="font-size: 10px">POSITIVO</label>
                            </td>
                            <td  width="10%" style="padding-top: 2px;padding-bottom: 2px;"> @if($fl_ref) {{$ref_ant->unidad1}} @else {{$resultado->unidad1}} @endif</td>
                            <td  width="35%" style="padding-top: 2px;padding-bottom: 2px;"> 
                              @if($fl_ref)
                                @if($ref_ant->texto_referencia == "")
                                  {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}  
                                @else
                                  <?php echo $ref_ant->texto_referencia; ?> 
                                @endif 
                              @else 
                                @if($resultado->texto_referencia == "")
                                  {{$resultado->valor1}} - {{$resultado->valor1g}} 
                                @else
                                  <?php echo $resultado->texto_referencia; ?> 
                                @endif
                              @endif
                            </td>
                            <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">
                              <input id="ch{{$resultado->id_parametro}}" name="ch{{$resultado->id_parametro}}" type="checkbox" class="flat-orangeX" @if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif
                            </td>
                          </tr>
                          @endif
                          @else
                          <tr>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->grupo}} {{$resultado->grupo2}}<br>
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->examen}}<br>
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                              {{$resultado->parametro}}<br>
                              @if($resultado->tiene_detalle=='1') 
                                @php
                                if($exa_ant != $resultado->id_examen){
                                  $exa_ant = $resultado->id_examen;
                                  $cont_par = 1;
                                  $xorden = Sis_medico\Examen_Orden::find($resultado->id_orden);
                                  $xresultados = $xorden->resultados;
                                  $tot_par = count($xresultados->where('id_examen',$resultado->id_examen));
                                }else{
                                  $cont_par ++;
                                }
                                @endphp
                                @if($cont_par == '1')
                                <a href="{{route('subresultado.crear',['id_orden' => $orden->id, 'id_examen' => $resultado->id_examen])}}" data-toggle="modal" data-target="#edit_crea_sub" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-plus"></span> Detalle Adicional</a>
                                @endif
                              @endif
                              
                            </td>
                            <td  width="15%" style="padding-top: 2px;padding-bottom: 2px;">    
                            @if($resultado->id_examen == '639')
                            <div class="row">
                              <div class="col-md-3" style="padding: 0px; border: 1px solid #FFF;"><input type="radio" name="op{{$resultado->id_parametro}}" class="flat-orange2" value="0" @if($resultado->valor==1) checked  @endif>
                              </div>
                              <div class="col-md-3" style="background-color:#ACCBEE ; padding: 0px; border: 1px solid #FFF;text-align: center;"><input  type="radio" name="op{{$resultado->id_parametro}}" class="flat-orange2" value="1" @if($resultado->valor==1) checked  @endif>
                              </div>
                              <div class="col-md-3" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center; padding: 0px;"><input type="radio" name="op{{$resultado->id_parametro}}" class="flat-orange2" value="2" @if($resultado->valor==2) checked  @endif >
                              </div>
                              <div class="col-md-3" style="background-color:#376EAC; border: 1px solid #FFF;text-align: center; padding: 0px;" ><input type="radio" name="op{{$resultado->id_parametro}}" class="flat-orange2" value="3" @if($resultado->valor==3) checked  @endif>
                              </div>
                            </div>
                            @else
                              <input type="text" name="res_{{$resultado->id_parametro}}" id="res_{{$resultado->id_parametro}}" value="{{$resultado->valor}}" placeholder="Ingrese resultado" onchange="guardar_resultado('{{$resultado->id_parametro}}')">    
                            @endif
                            <span id="rg{{$resultado->id_parametro}}" style="color: red"></span>
                              @if($resultado->valor != null )
                                @php $xvar = str_replace('*', '', $resultado->valor); @endphp
                                @if(is_numeric($resultado->valor1g))
                                  @if($xvar > $resultado->valor1g)
                                    <span id="rg2{{$resultado->id_parametro}}" style="color: red">SUPERIOR AL RANGO</span>
                                  @endif
                                @endif   
                                @if(is_numeric($resultado->valor1))
                                  @if($xvar < $resultado->valor1)
                                    <span id="rg2{{$resultado->id_parametro}}" style="color: red">INFERIOR AL RANGO</span>
                                  @endif  
                                @endif 
                              @endif  
                            </td>
                            <td  width="10%" style="padding-top: 2px;padding-bottom: 2px;"> @if($fl_ref) {{$ref_ant->unidad1}} @else {{$resultado->unidad1}} @endif</td>
                            <td  width="35%" style="padding-top: 2px;padding-bottom: 2px;"> 
                              @if($fl_ref)
                                @if($ref_ant->texto_referencia == "")
                                  {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}  
                                @else
                                  <?php echo $ref_ant->texto_referencia; ?> 
                                @endif 
                              @else 
                                @if($resultado->texto_referencia == "")
                                  {{$resultado->valor1}} - {{$resultado->valor1g}} 
                                @else
                                  <?php echo $resultado->texto_referencia; ?> 
                                @endif
                              @endif
                            </td>
                            <td style="width: 200px;"><div style="display: none" id="loading_gif{{$resultado->id_parametro}}" ><img id="imagen_loading" src="{{asset('/images/loading-bar.gif')}}" style="width: 200px;"></div></td>
                            <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">
                              <input id="ch{{$resultado->id_parametro}}" name="ch{{$resultado->id_parametro}}" type="checkbox" class="flat-orange"@if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif
                            </td>
                          </tr>
                          @endif
                          
                          
                          @if($resultado->tiene_detalle=='1' and $cont_par == $tot_par )
                            
                            @php  
                              $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$resultado->id_examen)->get();
                            @endphp
                            <tr><td colspan="4"><b>Detalle Adicional:</b></td></tr>
                            <tr >
                              <td colspan="5" id="sub_tabla{{$resultado->id_examen}}" style="padding: 0px;" >
                                <table class="table table-bordered table-hover dataTable" style="width: 100%;">
                                  <tbody>
                                    @foreach($sub_resultados as $sub)
                                      <tr id="sr{{$sub->id}}">
                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo1}}</td>
                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo2}}</td>
                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo3}}</td>
                                        <td style="width: 10% !important;padding: 0px;"><button class="btn btn-sm btn-danger" onclick="elimina_sub('{{$sub->id}}')" href=""><i class="glyphicon glyphicon-trash"></i></button></td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
$(document).ready(function(){
  $('input[type="radio"].flat-orange2').iCheck({
    checkboxClass: 'flat-orange2',
    radioClass: 'iradio_square-grey',
  });
 
  $('input[type="radio"].flat-red').iCheck({
    checkboxClass: 'flat-red',
    radioClass: 'iradio_square-red',
  });
  $('#fecha_resultado').datetimepicker({
    format: 'YYYY/MM/DD HH:mm:ss',
    defaultDate: '@if($orden->fecha_convenios != null){{$orden->fecha_convenios}}@else{{date($orden->fecha_orden)}}@endif',
    
  });
  $("#fecha_resultado").on("dp.change", function(e) {
      cambiar_fecha_res();
  });
});
</script>
<script type="text/javascript">
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  })

  $('input[type="checkbox"].flat-orangeX').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  })

 

  $('#edit_crea').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#edit_crea_sub').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
      $('#valor3').val(' ');
      $('#valor2').val(' ');
      $('#valor1').val(' ');

  });

  @php

    $usuario = Auth::user()->id;
    $usuario_aut = Sis_medico\Agenda_Permiso::where('id_usuario',$usuario)->first();

  @endphp

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
    //console.log(this.name.substring(2));
    @if(!is_null($usuario_aut))
    $.ajax({
      type: 'get',
      url:"{{ url('labs_nuevo/sin_maq/certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/1",

      success: function(data){

        //alert(data);
        //console.log(data);

      },


      error: function(data){


      }
    });
    @else
      alert("Usuario No autorizado a Certificar");
      location.reload();

    @endif

  });

  $('input[type="radio"].flat-orange2').on('ifChecked', function(event){

    //console.log(this);
    $.ajax({
      type: 'get',
      url: "{{url('guardar/testdealimentos')}}/"+this.name.substring(2)+"/"+this.value+"/{{$orden->id}}",

      success: function(data){

        $('#ch'+data).iCheck('enable');

      },


      error: function(data){


      }
    });

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
    @if(!is_null($usuario_aut))
      $.ajax({
        type: 'get',
        url:"{{ url('labs_nuevo/sin_maq/certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/0",

        success: function(data){

          //alert(data);

        },


        error: function(data){


        }
      });
    @else
      alert("Usuario No autorizado a Certificar");
      location.reload();
    @endif
  });

  $(document).ready(function() {

    var edad = calcularEdad('<?php echo $orden->paciente->fecha_nacimiento; ?>');
    $('#edad').text( edad );

  });

  $( "#btn_val" ).click(function() {

    $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $orden->id]) }}",

      success: function(data){

        if(data.certificados>0){

          window.open('{{ route('resultados.imprimir',['id' => $orden->id]) }}');

        }else{
          alert("No tiene exámenes certificados");
        }

      },


      error: function(data){


      }
    });

  });

  function elimina_sub(id){
    var confirmar = confirm("Desea Eliminar Resultado");
    if(confirmar){
      $.ajax({
        type: 'get',
        url:"{{ url('subresultado/eliminar') }}/"+id,

        success: function(data){
          //alert(data);
          console.log($('#sr'+id));
          $('#sr'+id).hide();


        },


        error: function(data){


        }
      });
    }
  }

  function cambiar_fecha_res(){
    $.ajax({
        url: "{{route('orden.cambio_resultado')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'id_orden': '{{$orden->id}}', 'fecha_resultado':$('#fecha_resultado').val(),
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
           if(data == 'ok'){
            alert("Fecha Resultado Actualizada");  
           }else{

            alert('Ocurrio un Error');
           }
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });  
  }

  function guardar_resultado(id_par){
    $('#loading_gif'+id_par).show();
    $.ajax({
      type: 'post',
      url:"{{ route('resultados.guardar_actualizar_resultados') }}", 
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      
      datatype: 'json',
      data: {'id_parametro': id_par, 'id_orden': '{{$orden->id}}', 'valor': $('#res_'+id_par).val() },
      success: function(data){
        
        //alert(data);
        rango(data[0]);
        $('#'+data[0]).text( data[1] );
        $('.close').click();
        //console.log($('#ch'+data[0]));
        $('#ch'+data[0]).iCheck('enable');
        $('#loading_gif'+id_par).hide();

      },


      error: function(data){

        if(data.responseJSON.valor!=null){
            $('#dvalor').addClass('has-error');
            alert(data.responseJSON.valor[0]);
        }
        
        
      }
    });
  }

  function certificar_todo(){

    $('#espera_gif').show();

    $.ajax({
      type: 'post',
      url:"{{ route('nuevoResultado.nuevo_certificar_grupo') }}", 
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      
      datatype: 'json',
      data: {'id': '{{ $request->id }}', 'nombre_grupo': '{{ $request->nombre_grupo }}', 'nombre_examen': '{{ $request->nombre_examen }}', 'tipo': '{{ $request->tipo }}', 'fecha_desde': '{{ $request->fecha_desde }}',
'fecha_hasta': '{{ $request->fecha_hasta }}' },
      success: function(data){
      
        //location.reload();
        cargar_resultados_no_reload('{{$orden->id}}');

      },


      error: function(data){

        
      }
    });


  }

  function rango(id){
        $.ajax({
          type: 'post',
          url:"{{ route('resultados.validacion_maximos') }}", 
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: {'id_parametro': id, 'id_orden': '{{$orden->id}}', 'valor': $('#res_'+id).val() },
          success: function(data){
            if(data=='mayor'){
                $('#rg'+id).text('SUPERIOR AL RANGO');
            }
            if(data=='menor'){
                $('#rg'+id).text('INFERIOR AL RANGO');
            }
            $('#rg2'+id).hide();
          },


          error: function(data){

            if(data.responseJSON.valor!=null){
                $('#dvalor').addClass('has-error');
                alert(data.responseJSON.valor[0]);
            }
            
             
          }
        });    
    }

    $('input[type="radio"].flat-red').on('ifChecked', function(event){
    //console.log(this.name);
    //console.log(this.value);  
    $.ajax({
        url: "{{route('orden.guardar_resultado_esp')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'nombre': this.name, 'valor': this.value, 'id': '{{$orden->id}}',
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
          //data.forEach(seleccionar_especial);
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });
   

  });

  $('input[type="checkbox"].flat-orangeX').on('ifChecked', function(event){
    //console.log(this.name.substring(2));
    $.ajax({
        url: "{{route('orden.certificar_resultado_esp')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            'nombre': this.name, 'valor': this.value, 'id': '{{$orden->id}}',
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
        console.log(data);
          //data.forEach(seleccionar_especial);
        },
        error: function(xhr, status) {
            alert('Existió un problema');
            //console.log(xhr);
        },
    });

     
  
  });
  







</script>
