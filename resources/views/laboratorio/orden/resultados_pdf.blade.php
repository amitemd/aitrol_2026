<!DOCTYPE html>
<html>
<head>
  <style>
    @page { margin: 190px 70px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -250px; right: 0px; height: 150px; }
    #footer1 { position: fixed; left: 0px; bottom: -220px; right: 0px; height: 225px; }

  </style>

</head>
<body>

  <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(550, 780, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
  </script>

    <div id="header">
      <div class="col-md-12" style="width: 1122px;border-bottom: 0.5px solid #009a98;border-top: 0.5px solid #ec6c25;">
       <img src="{{base_path().'/public/imagenes/labs_res.jpg'}}" align=center>
      </div>
      <div style="border-bottom: 1px solid #009a98;padding: 0px;width: 1122px;">
        <table style="font-size: 16px;color: #009a98;">
          <tbody>
            <tr role="row" >
              <td style="background-color: #e5f5f5;height: 20px;"><b>PACIENTE</b></td>
              <td width="230" style="color: black;">{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
              <td style="background-color: #e5f5f5;text-align: center;"><b>CEDULA</b></td>
              <td width="60" style="color: black;text-align: center;">{{$orden->id_paciente}}</td>
              <td style="background-color: #e5f5f5;text-align: center;"><b>EDAD</b></td>
              <td width="50" style="color: black;text-align: center;">{{$age}}</td>
              <td rowspan="2" width="80" style="background-color: #e5f5f5;font-size: 12px;border: 1px solid #009a98;text-align: center"><b>F.TOMA DE MUESTRA</b><br><span style="color: black;background-color: white;font-size: 15px !important;">@if($orden->fecha_convenios == null){{ substr($orden->fecha_orden,0,10) }} / {{ substr($orden->fecha_orden,11,8) }}@else{{ substr($orden->fecha_convenios,0,10) }} / {{ substr($orden->fecha_convenios,11,8) }}@endif</span></td>
            </tr>
            <tr role="row" >
              @if($orden->codigo==null || $orden->codigo=='0001')
              <td style="background-color: #e5f5f5;height: 20px;"><b>DOCTOR:</b></td>
              <td >
                @if($orden->doctor->id == '0941416661')
                  {{$orden->doctor->apellido1}} 
                    @if($orden->doctor->apellido2!='(N/A)' && $orden->doctor->apellido2!='.')
                      {{$orden->doctor->apellido2}}
                    @endif 
                    {{$orden->doctor->nombre1}} 
                    @if($orden->doctor->nombre2!='(N/A)' && $orden->doctor->nombre2!='.')
                      {{$orden->doctor->nombre2}}
                    @endif 
                @endif 
                @if($orden->doctor->uso_sistema!='1') 
                  @if($orden->doctor->id!='GASTRO')
                    {{$orden->doctor->apellido1}} 
                    @if($orden->doctor->apellido2!='(N/A)' && $orden->doctor->apellido2!='.')
                      {{$orden->doctor->apellido2}}
                    @endif 
                    {{$orden->doctor->nombre1}} 
                    @if($orden->doctor->nombre2!='(N/A)' && $orden->doctor->nombre2!='.')
                      {{$orden->doctor->nombre2}}
                    @endif 
                  @endif 
                @endif </td>
              @else
                @php $codigo = Sis_medico\Labs_doc_externos::find($orden->codigo); @endphp
              <td style="background-color: #e5f5f5;height: 20px;"><b>DOCTOR</b></td>
              <td style="color: black;">@if(!is_null($codigo)){{$codigo->id}} - {{$codigo->nombre1}} {{$codigo->nombre2}} {{$codigo->apellido1}} {{$codigo->apellido2}}@endif</td>
              @endif
              <td style="background-color: #e5f5f5;font-size: 12px;"><b>F.NACIMIENTO</b></td>
              <td style="color: black;text-align: center;">{{ $orden->paciente->fecha_nacimiento }}</td>
              <td style="background-color: #e5f5f5;text-align: center;"><b>SEXO</b></td>
              <td style="color: black;text-align: center;">@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
              <!--td style="background-color: #e5f5f5;"><b>HORA</b></td>
              <td style="color: black;">@if($orden->seguro->tipo == '0')@if($orden->fecha_convenios == null){{substr($orden->fecha_orden,10,10)}}@else {{substr($orden->fecha_convenios,10,10)}} @endif @else {{substr($orden->fecha_orden,10,10)}} @endif</td-->
             
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div  align="right" class="col-md-12" style="font-size: 18px">
        @php
          if($orden->completo == '0'){
            if($pct<'100'){
              echo "INFORME PARCIAL";
            }
          }
          $cantidad_detalle=0;
        @endphp
    </div>

    <div id="footer">
      <div align="left" style="position: absolute;top: -90px;">
        <img  style="width: 150px;height: 150px;" src="data:image/png;base64, {{ DNS2D::getBarcodePNG(asset('pagina/resultados/externos/imprimir').'/' . $orden->id, 'QRCODE')}}" alt="barcode" />
      </div>
      @if( $orden->fecha_orden < '2021-06-01  0:00:00') 
        <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
      @elseif($orden->fecha_orden < '2024-05-08  0:00:00')
        <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12> 
      @else
        @if($orden->id == '3959')
          <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/firma_resp_labs.png'}}" align=center hspace=12> 
        @else
          <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/firma_richards.jpeg'}}" align=center hspace=12> 
        @endif
      @endif    
      <div align="right"> 
        <p style="font-size: 12px;">{{substr($ucreador->nombre1,0,1)}}{{$ucreador->apellido1}}</p>
      </div>
    </div>


    <!--<div id="footer1">
      <div align="left">
        <p style="font-size: 15px;color: black">Resultado en Línea:</p>
        <p style="font-size: 15px;color: black">Usuario: {{$user->email}}</p>
        <p style="font-size: 15px;color: black">Si usted no ha modificado su contraseña<br>
        la contraseña sera su numero cédula.</p>
        <p style="font-size: 15px;color: black">*Ud.podrá cambiar la clave en el perfil de usuario.</p>
      </div>
    </div> -->


    <div id="content">

      @foreach($agrupador as $value)
        @php $i_agrupador=0; $cantidad_detalle= $detalle->count();@endphp
          @foreach($detalle as $value_detalle)
            @php
              $i=0;

              if($orden->seguro->tipo == '0' ){
                $id_agrupador = $value_detalle->examen->id_agrupador;
              }else{
                $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();

                $id_agrupador = 0;
                if(!is_null($agrupador_part)){
                  $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                }
              }
            @endphp
            @if($value_detalle->examen->no_resultado=='0')
              @if($id_agrupador == $value->id)
                @if($value_detalle->examen->resultado_especial == null)
                  @php

                    if($value_detalle->examen->sexo_n_s=='0'){
                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                    }else{
                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                    }
                    $xfmostrar = false;
                    if($parametro_nuevo->count()>0){
                      foreach($parametro_nuevo as $xpar){
                        $xresultado = $orden->resultados
                          ->where('id_parametro', $xpar->id)
                          ->where('certificado','1')
                          ->first();
                        if(!is_null($xresultado)){
                          $xfmostrar = true; break;
                        }
                      }
                    }
                  @endphp
                  @if($xfmostrar)
                  <div class="col-md-12">
                    @if($i_agrupador == 0)
                      <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;width: 1122px;padding-top: 5px;padding-bottom: 5px;font-size: 18px;">
                        <b>{{$value->nombre}}</b>
                        @php $i_agrupador=1; @endphp
                      </div>
                      <div style="width: 40%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" ><b>NOMBRE</b></div>
                      <div style="width: 20%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" ><b>RESULTADO</b></div>
                      <div style="width: 15%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" ><b>UNIDADES</b></div>
                      <div style="width: 25%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" ><b>REFERENCIA</b></div>

                      <div style="clear:both;padding-bottom: 0;padding-top: 0;"></div>

                    @endif
                    @if($i == 0)
                      @php $xvhfecha = null; @endphp
                      @if($parametro_nuevo->count() > 1 && $xfmostrar )
                        @if(substr($value_detalle->examen->nombre,0,12) == 'TEST ALIENTO' || substr($value_detalle->examen->nombre,0,15) == 'ALERGIAS A ALIM')
                          @foreach($parametro_nuevo as $xvhpar)
                            @php
                              $resultado = $resultados->where('id_parametro', $xvhpar->id)->first();
                            @endphp
                            @if(!is_null($resultado))
                                @php
                                  $xvhfecha = $resultado->created_at;
                                  
                                  break;

                                @endphp  
                            @endif
                          @endforeach
                          @php  @endphp
                        @endif
                        

                        @if($value_detalle->id_examen!='639')
                          <div style="border-bottom: 1px solid #009a98;clear:both;padding-top: 10px;padding-bottom: 10px;">
                            <b style="font-size: 20px;">{{$value_detalle->examen->descripcion}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  @if($xvhfecha!=null)Fecha Muestra: {{$xvhfecha}} @endif</b>
                          </div>
                          @if($value_detalle->examen->texto!=null)
                          <div style="border-bottom: 1px solid #009a98;padding: 0px;font-size: 20px !important;">
                            <?php echo $value_detalle->examen->texto; ?>
                          </div>
                          @endif
                        @endif
                      @endif
                      @php $i = 1; @endphp
                    @endif

                    @if($value_detalle->id_examen=='639' && $xfmostrar)
                      @if($cantidad_detalle>1)
                      <div style="page-break-after:always;"></div>
                      @endif
                      <section style="float:left;width: 68%;">
                        <!--br-->
                        <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px; width: 1122px;">
                                  <b>ALERGIAS A ALIM. POR IGG - 59 </b>@if($xvhfecha!=null) Fecha Muestra:  {{$xvhfecha}} @endif
                        </div>
                        <!--br-->

                        @php $cuenta=0;

                          $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                        @endphp

                          <table style="width: 98%;">
                            @foreach($parametro_nuevo as $value_agrupador)
                              @php
                                $rvalor=0;

                                $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();
                                if(!is_null($resultado)){
                                  $rvalor=$resultado->valor;
                                }
                              @endphp
                              @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                @if($cuenta == 0) <tr> @endif
                                  <td  style=" font-size: 15px; padding: 12px;">{{$value_agrupador->sec}}</td>
                                  <td style=" font-size: 15px;">{{$value_agrupador->nombre}}</td>
                                  <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                  <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                  <td style="background-color:#A8A8A8 ; border: 1px solid #FFF;text-align: center;font-size: 18px;"> @if($value_agrupador->orden=='48')  <span style="color: white;">X</span> @else &nbsp; @endif</td>
                                  @php $cuenta++; @endphp
                                  @if($cuenta == 2)
                                  </tr>

                                @endif
                              @else
                                @if($cuenta == 0) <tr> @endif
                                <td style="font-size: 15px; padding: 12px;width: 5px;">{{$value_agrupador->sec}}</td>
                                <td style=" font-size: 15px; width: 60px;">{{$value_agrupador->nombre}}</td>
                                <td style="background-color:#eaf2fb ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;" > @if($rvalor==null || $rvalor==0) X @endif</td>
                                <td style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;" > @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                <td style="background-color:#8AB0DB ; border: 1px solid #FFF;text-align: center;font-size: 18px;width: 25px;" > @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                <td style="background-color:#376EAC ; border: 1px solid #FFF;text-align: center;font-size: 18px;width: 25px;">  @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif</span> @else &nbsp; @endif</td>
                                @php $cuenta++; @endphp
                                @if($cuenta == 2)
                                </tr>
                                @php
                                  $cuenta=0;
                                @endphp
                                @endif
                              @endif
                            @endforeach
                        </table>
                      </section>
                      <section style="float:right;width: 30%;">
                        <table>
                          <tr>
                            <td>
                              <br>
                              <br>
                              <p style=" font-size: 20px;">*19 Pescado Blanco Mix: Bacalao y Lenguado.</p>
                              <p style=" font-size: 20px;">*20 Pescado de Agua Dulce Mix: Salmón y Trucha.</p>
                              <p style=" font-size: 20px;">*22 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</p>
                              <p style=" font-size: 20px;">*30 Mezclas de pimientos: rojo, verde y amarillo</p>
                              <p style=" font-size: 20px;">*31 Leguminosas: Arverjas, lentejas, fréjol, habas.</p>
                              <p style=" font-size: 20px;">*33 Melón Mix: Melón y Sandía</p>
                              <p align="justify" style=" font-size: 20px; "> Si sus resultados indican una reacción elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</p>
                              <p align="justify" style=" font-size: 20px;">Algunas personas con intolerancia al gluten son sensibles también a la avena. </p>
                            </td>
                          </tr>
                          <tr>
                            <table align="right"  cellpadding="0" cellspacing="0" width="100%" >
                            <tr>
                              <td BGCOLOR="eaf2fb" width="15%" border="1" > &nbsp;</td>
                              <td width="30%" style=" font-size: 20px;">Negativo</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="ACCBEE" width="15%" border="1" > &nbsp;</td>
                              <td width="30%" style=" font-size: 20px;">Reacción Leve</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp;</td>
                              <td width="50%" style=" font-size: 20px;">Reacción Moderada</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp;</td>
                              <td width="30%" style=" font-size: 20px;"> Reacción Fuerte</td>
                            </tr>
                            </table>
                          </tr>
                        </table>
                      </section>
                    @endif
                    <div style="clear:both;"></div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 18px;table-layout: fixed;width: 100%;">
                        <tbody>
                          @if($value_detalle->id_examen=='661')
                            <tr style="font-size: 19px;">
                                <td style="width: 30%;"><b>GRADO</b></td>
                                <td style="width: 20%;"><b>% CELULAS</b></td>
                                <td style="width: 15%;"><b>L.A.P. PUNTOS</b></td>
                                <td style="width: 35%;"><b>REFERENCIA</b></td>
                            </tr>
                          @endif
                          @if($value_detalle->id_examen=='661')
                            @foreach($parametro_nuevo as $value_agrupador)
                              @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                @php
                                $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                @endphp
                                <tr role="row" >
                                  <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                  @php
                                    $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                    $resultado_2 = null;
                                    if(!is_null($hermano)){
                                      $resultado_2 = $resultados->where('id_parametro', $hermano->id)->first();
                                    }

                                  @endphp
                                  <td style="width: 20%;">
                                    <div style="word-wrap: break-word;">
                                      @if(!is_null($hermano))
                                        @if(!is_null($resultado_2)) @if($resultado_2->certificado=='1') {{$resultado_2->valor}}@else{{"0"}} @endif @endif
                                      @endif
                                    </div>
                                  </td>
                                  <td style="width: 15%;">@if(!is_null($resultado))  {{$resultado->valor}}@else{{"0"}} @endif</td>
                                  <td style="width: 35%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>

                                </tr>

                              @endif
                            @endforeach

                          @elseif($value_detalle->id_examen=='639')

                          @else
                          @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                              @foreach($parametro_nuevo as $value_agrupador)
                                @php
                                $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                @endphp

                                @if(!is_null($resultado))
                                  @if($resultado->certificado=='1')
                                    <tr role="row">
                                      <td style="width: 40%;padding-left: 3px;padding-top: 5px;@if($parametro_nuevo->count() == 1) font-size: 20px;font-weight: 800;padding-left: -3px;border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px;@else padding-left: 10px; @endif">{{$value_agrupador->nombre}}</td>
                                      <td style="width: 20%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">
                                        <!--div style="word-wrap: break-word;"-->
                                          @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                        <!--/div-->
                                      </td>
                                      @php
                                        $fl_ref = 0;
                                        $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                        if(!is_null($ref_ant)){
                                          $fl_ref = 1;
                                        }
                                      @endphp
                                      <td style="width: 15%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">@if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif</td>
                                      <td style="width: 25%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">
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
                                  @endif
                                @endif

                              @endforeach
                              @if($value_detalle->examen->tiene_detalle=='1')
                                @php
                                  $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$value_detalle->id_examen)->get();
                                @endphp
                                <tr >
                                  <td colspan="4" id="sub_tabla">
                                    <table class="table table-bordered table-hover dataTable">
                                      <tbody>
                                        @foreach($sub_resultados as $sub)
                                        <tr>
                                          <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                          <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                          <td style="width: 300px !important;">{{$sub->campo3}}</td>
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
                  @endif
                @else
                  <!------------->
                  <div class="col-md-12" style="text-align: center;">{{$value_detalle->examen->nombre}}</div>
                  @php 
                      $parametros_nuevos = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref','<>',null); 
                      $xp_cant = 0;  $limite2 = 0;
                      $ele_cant = count($parametros_nuevos);
                  @endphp
                  
                    
                  <table class="table table-striped" style="width: 100%">
                    <tr>                    
                      <td style="font-size: 18px;width:1%;"></td>
                      <td style="font-size: 18px;width:19%;"></td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                      <td style="font-size: 18px;width:1%;"></td>
                      <td style="font-size: 18px;width:19%;"></td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                    </tr>
                    <tr>                    
                    @foreach($parametros_nuevos as $px_n)
                      @php  
                        $xresultado = $orden->resultados
                          ->where('id_parametro', $px_n->id)
                          ->where('certificado','1')
                          ->first();
                        if(!is_null($xresultado)){
                          $xp_cant ++; $limite2 ++;
                        }    
                          
                        $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                        if($px_n->id_parametro_ref == '1103'){
                          $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                        }
                        if($px_n->id_parametro_ref == '1146'){
                          $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                        }
                        
                      @endphp
                      @if(!is_null($xresultado))
                      <td style="font-size: 18px;width:1%;"> {{$xp_cant}}</td>
                      <td style="font-size: 18px;width:19%;"> {{$xresultado->parametro->nombre}}</td>
                      <td style="font-size: 20px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif</td>
                      <td style="font-size: 20px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif</td>
                      @if($limite2 == 2) @php $limite2 = 0;  @endphp </tr><tr> @endif
                      @endif 
                    @endforeach
                  </table> 

                  @php 
                      $parametros_nuevos2 = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref',null); 
                      
                  @endphp
                   
                    
                  <table class="table table-striped" style="width: 50%">
                    <tr>                    
                      <td style="font-size: 18px;width:1%;"></td>
                      <td style="font-size: 18px;width:19%;"></td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">NEGATIVO</td>
                      <td style="font-size: 20px;border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;">POSITIVO</td>
                    </tr>
                                       
                    @foreach($parametros_nuevos2 as $px_n)
                      @php  
                        $xresultado = $orden->resultados
                          ->where('id_parametro', $px_n->id)
                          ->where('certificado','1')
                          ->first();
                        
                        if(!is_null($xresultado)){
                          $xp_cant ++; 
                        }  
                          
                          
                        $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                        if($px_n->id == '1103'){
                          $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                        }
                        if($px_n->id == '1146'){
                          $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                        }
                      @endphp
                      @if(!is_null($xresultado))
                      <tr> 
                      <td style="font-size: 18px;width:1%;"> {{$xp_cant}} </td>
                      <td style="font-size: 18px;width:19%;"> {{$xresultado->parametro->nombre}} </td>
                      <td style="font-size: 20px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif </td>
                      <td style="font-size: 20px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif </td>
                      </tr>
                      @endif
                    @endforeach
                  </table> 
                  
                    <!------------->
                @endif                         

              @endif

            @endif



          @endforeach

      @endforeach

    </div>








</body>
</html>


