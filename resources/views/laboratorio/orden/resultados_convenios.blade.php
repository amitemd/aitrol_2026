@extends('laboratorio.orden.base')

@section('action-content')

<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .table-hover>tbody>tr:hover{
      background-color: #b3ffe6;
      cursor:pointer;
    }
    .table>tbody>tr>td{
      padding-top: 2px !important; 
      padding-bottom: 2px !important;  
    }
   
</style>
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
  <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
  <!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                            <div class="row">
                                <div class="col-md-9">
                                  <div class="col-md-6"><h4>Paciente : <span style="color: red;">{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif </span></div>
                                  <div class="col-md-2"><h4>Edad : <span id="edad" name="edad" style="color: red;"></span></h4></div>
                              </div>
                              <div class="col-md-3">
                                <label>Exámenes Certificados (%)</label>
                                <div class="progress progress">
                                  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$orden->id}}">
                                    <span id="sp{{$orden->id}}" style="color: black;"></span>
                                  </div>
                                </div>  
                              </div>
                              <div class="col-md-9">
                                <form id="modificar_fecha" action="{{route('orden.fecha_convenios')}}">
                                  <div class="col-md-4">
                                    <input type="hidden" name="id" value="{{$orden->id}}">
                                    <span><b>Fecha imprimible:</b></span><input type="text" name="fecha_convenios" id="fecha_convenios" value="@if($orden->fecha_convenios != null){{$orden->fecha_convenios}}@else{{substr($orden->created_at, 0, -9)}}@endif" class="form-control pull-right input-sm" required onchange="cambio_fecha()">
                                    <br><br>
                                  </div>
                                </form>
                              </div> 
                               
                              <div class="col-md-3">
                                  <a class="btn btn-success btn-sm" onclick="goBack()">Regresar</a>
                                  <!--a class="btn btn-primary" href="{{ route('resultados.imprimir2',['id' => $orden->id]) }}">Imprimir</a-->
                                  <button class="btn btn-primary btn-sm" id="btn_val">Imprimir</button>
                              </div>
                            </div>
                        </div>
                    </div>
                    
                    @php $resultados = $orden->resultados; @endphp
                    @foreach($agrupador as $value)
                      @php $i_agrupador=0; @endphp

                      @foreach($detalle as $value_detalle)
                        @php

                          $i=0;
                          if($orden->seguro->tipo == '0' ){
                            $id_agrupador = $value_detalle->examen->id_agrupador;
                          }else{
                            $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                            if(!is_null($agrupador_part)){
                              $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                            }

                          }
                        @endphp



                          @if($id_agrupador == $value->id )

                            @php


                              if($value_detalle->examen->sexo_n_s=='0'){
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                              }else{
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                              }
                            @endphp
                            <div class="col-md-12" style="padding: 5px;">
                              @if($i_agrupador == 0)
                                <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;">
                                  <h2 class="box-title"><b>{{$value->nombre}}</b></h2>
                                  @php $i_agrupador=1; @endphp
                                </div>


                                <div style="width: 30%;float:left;" ><b>NOMBRE</b></div>
                                <div style="width: 15%;float:left;" ><b>RESULTADO</b></div>
                                <div style="width: 10%;float:left;" ><b>UNIDADES</b></div>
                                <div style="width: 45%;float:left;" ><b>REFERENCIA</b></div>


                                <div style="clear:both;"></div>
                              @endif
                              @if($i == 0)
                                @if($parametro_nuevo->count() > 1)
                                  @if($value_detalle->id_examen=='639')

                                    <!-- ALEXANDRA AQUI VA EL FORMATO DE LA INTOLERANCIA DE ALIMENTOS-->

                                      <br>
                                      <b>ALERGIAS A ALIM. POR IGG - 59</b>
                                      <br>
                                      <br>
                                      <div class="col-md-10" style="padding: 0px;">
                                        @php
                                          $cuenta=1;
                                        @endphp
                                        @foreach($parametro_nuevo as $value_agrupador)
                                          @php



                                            $rvalor=0;


                                            $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();


                                            if(!is_null($resultado)){
                                              $rvalor=$resultado->valor;

                                            }


                                          @endphp
                                            @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                              <div class="col-md-6" style="padding: 0px;">
                                                <div class="col-md-1" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px">{{$value_agrupador->sec}}</p></div>
                                                <div class="col-md-4" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px">{{$value_agrupador->nombre}}</p></div>
                                                 <div class="col-md-1" style="padding: 0px; border: 1px solid #FFF;">&nbsp;
                                                </div>
                                                <div class="col-md-1" style="border: 1px solid #FFF;text-align: center; padding: 0px;">&nbsp;
                                                </div>
                                                <div class="col-md-1" style="border: 1px solid #FFF;text-align: center; padding: 0px;">&nbsp;
                                                </div>
                                                <div class="col-md-1"  style="background-color:#376EAC; padding: 0px ; border: 1px solid #FFF;text-align: center;">@if($value_agrupador->orden=='48')  <span style="color: white; font-size: 15px;">x</span> @else &nbsp; @endif
                                                </div>
                                              </div>
                                            @else
                                              <div class="col-md-6" style="padding: 0px;">
                                                <div class="col-md-1" style="padding-left: 5px;padding-right: 5px;"><p style=" font-size: 12px; padding: 0px;">{{$value_agrupador->sec}}</p></div>
                                                <div class="col-md-4" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px;">{{$value_agrupador->nombre}}</p></div>
                                                <div class="col-md-1" style="padding: 0px; border: 1px solid #FFF;"><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="0">
                                                </div>
                                                <div class="col-md-1" style="background-color:#ACCBEE ; padding: 0px; border: 1px solid #FFF;text-align: center;"><input  type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="1" @if($rvalor==1) checked  @endif>
                                                </div>
                                                <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center; padding: 0px;"><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="2" @if($rvalor==2) checked  @endif >
                                                </div>
                                                <div class="col-md-1"  style="background-color:#376EAC; border: 1px solid #FFF;text-align: center; padding: 0px;" ><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="3" @if($rvalor==3) checked  @endif>
                                                </div>


                                              </div>
                                            @endif
                                          @php
                                            $cuenta++;
                                          @endphp
                                        @endforeach
                                      </div>
                                      <div class="col-md-2" style="padding: 0px;">
                                        <p>*19 Pescado Blanco Mix: Bacalao y Lenguado.</p>
                                        <p>*20 Pescado de Agua Dulce Mix: Salmón y Trucha.</p>
                                        <p>*22 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</p>
                                        <p>*30 Mezclas de pimientos: rojo, verde y amarillo</p>
                                        <p>*31 Leguminosas: Arverjas, lentejas, fréjol, habas.</p>
                                        <p>*33 Melón Mix: Melón y Sandía</p>
                                        <p align="justify"> Si sus resultados indican una reacción elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</p>
                                        <br> <p>Algunas personas con intolerancia al gluten son sensibles también a la avena. </p></br>
                                        <table scope="col" align="right" border="1" cellpadding="0" cellspacing="0" width="100%" >
                                                <tr>
                                                  <td BGCOLOR="ACCBEE" width="15%" border="1"> &nbsp</td>
                                                  <td width="30%">Reacción Leve</td>
                                                </tr>
                                                <tr>
                                                  <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp</td>
                                                  <td width="50%">Reacción Moderada</td>
                                                </tr>
                                                <tr>
                                                  <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp</td>
                                                  <td width="30%"> Reacción Fuerte</td>
                                                </tr>
                                          </table>
                                      </div>

                                    <!-- ALEXANDRA AQUI VA EL FORMATO DE LA INTOLERANCIA DE ALIMENTOS-->
                                  @else
                                    <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                                      <b>{{$value_detalle->examen->nombre}}</b>
                                      @if($value_detalle->examen->tiene_detalle=='1')
                                        <!--b>{{$value_detalle->examen->nombre}}</b-->
                                        
                                      @endif
                                    </div>
                                    @if($value_detalle->examen->texto!=null)
                                    <div style="border-bottom: 1px solid #009a98;padding: 0px;">
                                      <?php echo $value_detalle->examen->texto; ?>
                                    </div>
                                    @endif
                                  @endif
                                @elseif($parametro_nuevo->count() == 0)
                                  <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                                    <span style="background-color: red;color: white;"> <b>{{$value_detalle->examen->nombre}} @if($value_detalle->examen->no_resultado=='1') NO SE INGRESA INFORMACION POR SISTEMA @else SIN PARAMETRO @endif</b></span>
                                  </div>
                                @endif

                                @php $i = 1; @endphp
                              @endif

                              <div style="border-bottom: 1px solid white;padding: 0px;clear:both;"></div>
                              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="table-responsive">
                                  <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;;table-layout: fixed;width: 100%;">
                                    <tbody>
                                      @if($value_detalle->id_examen=='661')
                                        <tr>
                                          <td width="25%"><b>GRADO</b></td>
                                          <td width="15%"><b>% CELULAS</b></td>
                                          <td width="5%"><b>CER</b></td>
                                          <td width="10%"><b>L.A.P. PUNTOS</b></td>
                                          <td width="5%"><b>CER</b></td>
                                          <td width="40%"><b>REFERENCIA</b></td>
                                        </tr>
                                      @endif


                                      @if($value_detalle->id_examen=='661')
                                        @foreach($parametro_nuevo as $value_agrupador)
                                          @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                            @php
                                              $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp
                                            <tr role="row" >
                                              <td width="25%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                              @php
                                                $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                $resultado_2 = null;
                                                if(!is_null($hermano)){
                                                  $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                }
                                              @endphp
                                              <td width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                                                @if(!is_null($hermano))
                                                <span id="{{$hermano->id}}">@if(!is_null($resultado_2)){{$resultado_2->valor}}@else{{"0"}}@endif</span>
                                                @endif
                                              </td>
                                              
                                              <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">
                                                <span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span>
                                              </td>
                                               
                                              <td width="40%" style="padding-top: 2px;padding-bottom: 2px;"> @if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                            </tr>
                                          @endif
                                        @endforeach
                                      @elseif($value_detalle->id_examen=='639')
                                      @else
                                        @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                          @foreach($parametro_nuevo as $value_agrupador)
                                            @php
                                              $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp
                                            <tr role="row" >
                                              @if($parametro_nuevo->count()=='1')
                                              <td width="30%" style="padding-top: 2px;padding-bottom: 2px;"><b>{{$value_agrupador->nombre}}</b></td>

                                                @if($value_detalle->examen->tiene_detalle=='1')
                                                  <b>{{$value_detalle->examen->nombre}}</b>
                                                  <span class="glyphicon glyphicon-plus"></span> Detalle
                                                @endif
                                              @else
                                              <td width="30%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                              @endif
                                              @php
                                                $fl_ref = 0;
                                                $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                                if(!is_null($ref_ant)){
                                                  $fl_ref = 1;
                                                }
                                              @endphp
                                              <td width="15%" style="padding-top: 2px;padding-bottom: 2px;"><span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span>
                                              <!-- NUEVA VALIDACION DE RANGOS -->
                                              @if(!is_null($resultado))
                                                @if($fl_ref)
                                                  @if($ref_ant->texto_referencia == "")
                                                    @php $xvar = str_replace('*', '', $resultado->valor); @endphp
                                                    @if(is_numeric($ref_ant->valor1g))
                                                      @if($xvar >$ref_ant->valor1g)
                                                        <span id="rg2{{$value_agrupador->id}}" style="color: red">SUPERIOR AL RANGO</span>
                                                      @endif
                                                    @endif   
                                                    @if(is_numeric($ref_ant->valor1))
                                                      @if($xvar <$ref_ant->valor1)
                                                        <span id="rg2{{$value_agrupador->id}}" style="color: red">INFERIOR AL RANGO</span>
                                                      @endif  
                                                    @endif
                                                  @endif
                                                @else
                                                  @if($value_agrupador->texto_referencia == "")
                                                    @php $xvar = str_replace('*', '', $resultado->valor); @endphp
                                                    @if(is_numeric($value_agrupador->valor1g))
                                                      @if($xvar >$value_agrupador->valor1g)
                                                        <span id="rg2{{$value_agrupador->id}}" style="color: red">SUPERIOR AL RANGO</span>
                                                      @endif
                                                    @endif   
                                                    @if(is_numeric($value_agrupador->valor1))
                                                      @if($xvar <$value_agrupador->valor1)
                                                        <span id="rg2{{$value_agrupador->id}}" style="color: red">INFERIOR AL RANGO</span>
                                                      @endif  
                                                    @endif
                                                  @endif
                                                @endif
                                              @endif 
                                              <span id="rg{{$value_agrupador->id}}" style="color: red"></span>     
                                              <!-- NUEVA VALIDACION DE RANGOS -->  
                                              </td>
                                              <td width="10%" style="padding-top: 2px;padding-bottom: 2px;"> @if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif</td>
                                              <td width="35%" style="padding-top: 2px;padding-bottom: 2px;"> 
                                              @if($fl_ref)
                                                @if($ref_ant->texto_referencia == "")
                                                 {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}  
                                                @else
                                                  <?php echo $ref_ant->texto_referencia; ?> 
                                                @endif 
                                              @else 
                                                @if($value_agrupador->texto_referencia == "")
                                                 {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}} 
                                                @else
                                                  <?php echo $value_agrupador->texto_referencia; ?> 
                                                @endif
                                              @endif
                                              </td>
                                            
                                            </tr>
                                          @endforeach
                                          @if($value_detalle->examen->tiene_detalle=='1')
                                            @php
                                              $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$value_detalle->id_examen)->get();
                                            @endphp
                                            <tr >
                                              <td colspan="5" id="sub_tabla{{$value_detalle->id_examen}}" style="padding: 0px;" >
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
                                        @endif
                                      @endif
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          @endif

                      @endforeach
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

  $(document).ready(function() {
    var edad = calcularEdad('<?php echo $orden->paciente->fecha_nacimiento; ?>');
    $('#edad').text( edad );
        
        $('#fecha_convenios').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
        });

  });
        $("#fecha_convenios").on("dp.change", function (e) {
            cambio_fecha();
        });
  
  $(".clickable-row").click(function() {
      //console.log(this);
      //$("#edit_crea").modal();
      //window.location = $(this).data("href");
      var url = $(this).data("href");
      //  alert(url); 
      $.get(url,function(data) {
        $(".modal-content").html(data);
        $("#edit_crea").modal();
      });
  });

  $('#edit_crea').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  function goBack() {
      window.history.back();
  }

  function cambio_fecha(){
      $.ajax({
          type: 'post',
          url:'{{route("orden.fecha_convenios")}}',
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
  
          datatype: 'json',
          data: $("#modificar_fecha").serialize(),
          success: function(data){
              //alert('valio');

          },
          error: function(data){
            console.log(data);
          }
      })
    }

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

  $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $orden->id]) }}", 
        
        success: function(data){
            
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            
            //console.log(data);
            $('#td{{$orden->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$orden->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$orden->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$orden->id}}').addClass("progress-bar-warning");  
            }else{
              $('#td{{$orden->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      }); 

</script>
@endsection
