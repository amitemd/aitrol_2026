<!DOCTYPE html>
<html>
<head>
   <title>Formulario10B</title>
  <style>
    @page { margin: 30px 20px; }
    #header { position: fixed; left: 0px; right: 0px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -250px; right: 0px; height: 150px; }
    #footer1 { position: fixed; left: 0px; bottom: -220px; right: 0px; height: 225px; }

    .text-center {
        text-align: center;
    }

    .color1 {
      color: black;
    }
    .align-left {
        text-align: left;
    }
  </style>
</head>
<body>
    <!-- HEADER -->
    <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
      <div class="row box-body card" style="text-align: center;">
        @if(!is_null($empresa))
          <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width: 300px; height: 150px">
        @endif
      </div>
      <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
        <table style="font-size: 16px;color: #009a98; width: 100%;">
          <thead>
              <tr role="row" class="align: center;">
                  <td colspan="6"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO</b></td>
              </tr>
          </thead>
          <tbody>
            <tr role="row" >
              <td style="background-color: #e5f5f5;text-align: center;" width="25%"><b>INSTITUCION DEL SISTEMA</b></td>
              <td style="background-color: #e5f5f5;text-align: center;" width="10%"><b>UNICODIGO</b></td>
              <td style="background-color: #e5f5f5;text-align: center;" width="25%"><b>ESTABLECIMIENTO DE SALUD</b></td>
              <td style="background-color: #e5f5f5;text-align: center;" width="15%"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
              <td style="background-color: #e5f5f5;text-align: center;" width="15%"><b>NÚMERO DE ARCHIVO</b></td>
              <td style="background-color: #e5f5f5;text-align: center;" width="10%"><b>No.HOJA</b></td>
            </tr>
            <tr role="row" >
              <td style="color: black;text-align: center;" width="25%">{{$empresa->nombrecomercial}}<</td>
              <td style="color: black;text-align: center;" width="10%">{{$empresa->unicodigo}}</td>
              <td style="color: black;text-align: center;" width="25%">{{$empresa->nombrecomercial}}</td>
              <td style="color: black;text-align: center;" width="15%">{{$orden->id_paciente}}</td>
              <td style="color: black;text-align: center;" width="15%"></td>
              <td style="color: black;text-align: center;" width="10%">1</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
        <table style="font-size: 16px;color: #009a98; width: 100%;">
          <thead>
            <tr>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>PRIMER APELLIDO</b></td>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>SEGUNDO APELLIDO</b></td>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>PRIMER NOMBRE</b></td>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>SEGUNDO NOMBRE</b></td>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>SEXO</b></td>
                <td rowspan="2" style="background-color: #e5f5f5;text-align: center;"><b>EDAD</b></td>
                <td colspan="4" style="background-color: #e5f5f5;text-align: center;"><b>CONDICIÓN EDAD (MARCAR)</b></td>
            </tr>
            <tr>
                <td style="background-color: #e5f5f5;text-align: center;">D</td>
                <td style="background-color: #e5f5f5;text-align: center;">H</td>
                <td style="background-color: #e5f5f5;text-align: center;">M</td>
                <td style="background-color: #e5f5f5;text-align: center;">A</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td style="color: black;text-align: center;" style="height: 18px;">{{$orden->paciente->apellido1}}</td>
              <td style="color: black;text-align: center;">{{$orden->paciente->apellido2}}</td>
              <td style="color: black;text-align: center;">{{$orden->paciente->nombre1}}</td>
              <td style="color: black;text-align: center;">{{$orden->paciente->nombre2}}</td>
              <td style="color: black;text-align: center;">@if($orden->paciente->sexo == 1) Masculino @endif @if($orden->paciente->sexo == 2) Femenino @endif</td>
              <td style="color: black;text-align: center;">{{$edad}}</td>
              <td style="color: black;text-align: center;">{{$diaNacimiento}}</td>
              <td style="color: black;text-align: center;"></td>
              <td style="color: black;text-align: center;">{{$mesNacimiento}}</td>
              <td style="color: black;text-align: center;">{{ date('Y', strtotime($orden->paciente->fecha_nacimiento))}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
        <table style="font-size: 16px;color: #009a98; width: 100%;">
          <thead>
              <tr role="row" class="align: center;">
                  <td colspan="6"><b>B. SERVICIO Y PRIORIDAD DE ATENCIÓN</b></td>
              </tr>
          </thead>
          <tbody>
                <tr>
                    <td colspan="2" class="text-center">DIAGNOSTICO</td>
                    <td class="text-center" >CIE</td>
                    <td rowspan="3" class="text-center" >SERVICIO</td>
                    <td class="text-center" >EMERGENCIA</td>
                    <td class="text-center" >&nbsp;</td>
                    <td class="text-center" >ESPECIALIDAD</td>
                    <td class="text-center" >&nbsp;</td>
                    <td colspan="2" class="text-center">PRIORIDAD</td>
                    <td class="text-center" >&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center color1">1</td>
                    <td class="align-left color1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['descripcion']}} @endif</td>
                    <td class="text-center color1">@if(isset($diagnosticos[0])) {{$diagnosticos[0]['cie10']}} @endif</td>
                    <td class="text-center" >CONSULTA EXTERNA</td>
                    <td class="text-center color1"> X </td>
                    <td class="text-center" >SALA</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td colspan="2" class="text-center">URGENTE</td>
                    <td colspan="2"class="text-center color1">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center color1">2</td>
                    <td class="align-left color1" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['descripcion']}} @endif</td>
                    <td class="text-center color1" >@if(isset($diagnosticos[1])) {{$diagnosticos[1]['cie10']}} @endif</td>
                    <td class="text-center" >HOSPITALIZACION</td>
                    <td class="text-center color1">&nbsp; </td>
                    <td class="text-center" >CAMA</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td colspan="2" class="text-center">RUTINA</td>
                    <td colspan="2" class="text-center color1"> X </td>
                </tr>
                <tr>
                    <td class="text-center color1">3</td>
                    <td class="align-left color1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['descripcion']}} @endif</td>
                    <td class="text-center color1">@if(isset($diagnosticos[2])) {{$diagnosticos[2]['cie10']}} @endif</td>
                    <td  class="text-center">&nbsp;</td>
                    <td class="text-center" ></td>
                    <td class="text-center color1">&nbsp; </td>
                    <td class="text-center color1">&nbsp;</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td colspan="2" class="text-center"> &nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td class="align-left color1">&nbsp;</td>
                    <td class="align-left color1">&nbsp;</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td class="text-center color1"></td>
                    <td class="text-center color1">&nbsp; </td>
                    <td class="text-center color1">&nbsp;</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td class="text-center color1">&nbsp;</td>
                    <td colspan="2" class="text-center color1"> &nbsp; </td>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
    <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
        @php
          if($orden->completo == '0'){
            if($pct<'100'){
              echo "{{trans('laboratorio.informe_parcial')}}";
            }
          }
          $cantidad_detalle=0;
        @endphp
    </div>
    <!-- CONTENT -->
    <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
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
                        {{$value->nombre}}</b>
                        @php $i_agrupador=1; @endphp
                      </div>
                      <div style="width: 40%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >NOMBRE</b></div>
                      <div style="width: 20%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >RESULTADO</b></div>
                      <div style="width: 15%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >UNIDADES</b></div>
                      <div style="width: 25%;float:left;font-size: 19px;padding-bottom: 0;padding-top: 0;" >REFERENCIA</b></div>
                    @endif
                    @if($i == 0)
                      @php $xvhfecha = null; @endphp
                      @if($parametro_nuevo->count() > 1 && $xfmostrar )
                      @if(substr($value_detalle->examen->nombre,0,12) == "trans('laboratorio.testalimento')" || substr($value_detalle->examen->nombre,0,15) == "trans('laboratorio.alergiasaalim')")
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
                          <b style="font-size: 20px;">{{$value_detalle->examen->descripcion}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  @if($xvhfecha!=null){{trans('laboratorio.fecha_muestra')}}: {{$xvhfecha}} @endif</b>
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
                      <div></div>
                      @endif
                      <section style="float:left;width: 68%;">
                        <!--br-->
                        <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px; width: 1122px;">
                                  <b>{{trans('laboratorio.fecha_muestra')}}</b>@if($xvhfecha!=null) {{trans('laboratorio.fecha_muestra')}}:  {{$xvhfecha}} @endif
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
                            <p style=" font-size: 20px;">{{trans('laboratorio.a19')}}</p>
                            <p style=" font-size: 20px;">{{trans('laboratorio.a20')}}</p>
                            <p style=" font-size: 20px;">{{trans('laboratorio.a22')}}</p>
                            <p style=" font-size: 20px;">{{trans('laboratorio.a30')}}</p>
                            <p style=" font-size: 20px;">{{trans('laboratorio.a31')}}</p>
                            <p style=" font-size: 20px;">{{trans('laboratorio.a33')}}</p>
                            <p align="justify" style=" font-size: 20px; "> {{trans('laboratorio.afinal')}}</p>
                            <p align="justify" style=" font-size: 20px;"> {{trans('laboratorio.afinal2')}} </p>
                            </td>
                          </tr>
                          <tr>
                            <table align="right"  cellpadding="0" cellspacing="0" width="100%" >
                            <tr>
                              <td BGCOLOR="eaf2fb" width="15%" border="1" > &nbsp;</td>
                            <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.negativo')}}</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="ACCBEE" width="15%" border="1" > &nbsp;</td>
                            <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_leve')}}</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp;</td>
                            <td width="50%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_moderada')}}</td>
                            </tr>
                            <tr>
                              <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp;</td>
                            <td width="30%" style=" font-size: 20px;">{{trans('laboratorio.reaccion_fuerte')}}</td>
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
                              <td style="width: 30%;"><b>{{trans('laboratorio.grado-')}}</b></td>
                              <td style="width: 20%;"><b>% {{trans('laboratorio.celulas-')}}</b></td>
                              <td style="width: 15%;"><b>L.A.P. {{trans('laboratorio.puntos-')}}</b></td>
                              <td style="width: 35%;"><b>{{trans('laboratorio.referencia-')}}</b></td>
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
                                    <td style="width: 30%;padding-left: 3px;padding-top: 5px;@if($parametro_nuevo->count() == 1) font-size: 20px;font-weight: 800;padding-left: -3px;border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px;@else padding-left: 10px; @endif">{{$value_agrupador->nombre}}</td>
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
                                    <td style="width: 35%;padding-top: 5px;@if($parametro_nuevo->count() == 1) border-top: solid 1px #009a98;padding-top: 5px;padding-bottom: 5px; @endif">
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
                @endif                         

              @endif

            @endif



          @endforeach

      @endforeach

    </div>
    <!-- FOOTER -->
    <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;height: 300px;">
      <table style="font-size: 16px;color: #009a98; width: 100%;">
        <thead>
            <tr role="row" class="align: center;">
                <td colspan="6"><b>D. DATOS DEL PROFESIONAL RESPONSABLE</b></td>
            </tr>
        </thead>
        <tbody>
          <tr>
            <td style="background-color: #e5f5f5;text-align: center;width: 5%;"><b>FECHA<br>(aaaa-mm-dd)</b></td>
            <td style="background-color: #e5f5f5;text-align: center;width: 5%;"><b>HORA<br>(hh:mm)</b></td>
            <td style="background-color: #e5f5f5;text-align: center;width: 30%;"><b>PRIMER NOMBRE</b></td>
            <td style="background-color: #e5f5f5;text-align: center;width: 30%;"><b>PRIMER APELLIDO</b></td>
            <td style="background-color: #e5f5f5;text-align: center;width: 30%;"><b>SEGUNDO APELLIDO</b></td>
          </tr>
          <tr>
            @php
            $firma = Sis_medico\Firma_Usuario::where('nombre', 'labs_responsable.jpg')->first();
            @endphp
            <td style="color: black;text-align: center;width: 5%;">@if($orden->fecha_convenios == null) {{ substr($orden->fecha_orden,0,10) }} @else{{ substr($orden->fecha_convenios,0,10) }} @endif</td>
            <td style="color: black;text-align: center;width: 5%;">@if($orden->fecha_convenios == null) {{ substr($orden->fecha_orden,11,8) }}@else {{ substr($orden->fecha_convenios,11,8) }}@endif</td>
            @if(!is_null($firma))
              @php
              $id_laboratorista = $firma->id_usuario;
              $laboratorista = Sis_medico\User::where('id',$id_laboratorista)->first();
              @endphp
            <td style="color: black;text-align: center;width: 30%;">{{$laboratorista->nombre1}} </td>
            <td style="color: black;text-align: center;width: 30%;">{{$laboratorista->apellido1}} </td>
            <td style="color: black;text-align: center;width: 30%;">{{$laboratorista->apellido2}} </td>
            @else
            <td style="color: black;text-align: center;width: 30%;"> {{$ucreador->nombre1}} </td>
            <td style="color: black;text-align: center;width: 30%;"> {{$ucreador->apellido1}} </td>
            <td style="color: black;text-align: center;width: 30%;">{{$ucreador->apellido2}} </td>
            @endif
          </tr>
          <tr>
            <td colspan="2" style="background-color: #e5f5f5;text-align: center;width: 25%;"><b>NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</b></td>
            <td colspan="2" style="background-color: #e5f5f5;text-align: center;width: 40%;"><b>FIRMA</b></td>
            <td style="background-color: #e5f5f5;text-align: center;width: 35%;"><b>SELLO</b></td>
          </tr>
          <tr>
            <td colspan="2" style="color: black;text-align: center;width: 25%;">{{$laboratorista->id}}</td>
            <td colspan="2" style="color: black;text-align: center;width: 40%;">
              @if( $orden->fecha_orden < '2021-06-01  0:00:00') 
                <img style="position: absolute;margin-left: 150px" width=200 height=94 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12>
              @else 
                <img style='position: absolute;margin-left: 150px' width=200 height=94 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12> 
              @endif
            </td>
            <td style="color: black;text-align: center;width: 35%;">LABORATORISTA RESPONSABLE:<br>{{$laboratorista->apellido1}} {{$laboratorista->apellido2}} {{$laboratorista->nombre1}}  {{$laboratorista->nombre2}}<br> Ci: {{$laboratorista->id}}   </td>
          </tr>
          
        </tbody>
      </table>
    </div>
    
    <div class="row" style="border-bottom: 1px solid #009a98;padding: 0px;width: 100%;">
      <table style="font-size: 14px;color: #009a98; width: 100%;">
        <tr>
          <td style="color: black;width: 50%;text-align: left;font-size: 16px;"><b>SNS-MSP/HCU-form.010B/2021</b></td>
          <td style="color: black;width: 50%;text-align: right;font-size: 16px;"><b>LABORATORIO CLÍNICO - INFORME (1)</b></td>
        </tr>
      </table>
    </div>
    <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(550, 780, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
    </script>
</body>
</html>


