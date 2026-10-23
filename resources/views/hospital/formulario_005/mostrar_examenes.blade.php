<style type="text/css">
    .parent {
        overflow-y: scroll;
        height: 462px;
    }

    .parent::-webkit-scrollbar {
        width: 8px;
    }

    /* this targets the default scrollbar (compulsory) */
    .parent::-webkit-scrollbar-thumb {
        background: #004AC1;
        border-radius: 10px;
    }

    .parent::-webkit-scrollbar-track {
        width: 10px;
        background-color: #004AC1;
        box-shadow: inset 0px 0px 0px 3px #56ABE3;
    }

    .boton-lab {
        font-size: 14px;
        width: 90%;
        background-color: #004AC1;
        color: white;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
<div id="lab_index" class="container-fluid " style="margin:4px;">
    <div class="row">
        <div class="col-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;">
            <!--Agrupamos la fila en 12 columnas-->
            <div class="col-12" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;">
                <!--Titulo-->
                <h1 style="font-size: 15px; margin:0;">
                    <img style="width: 35px; margin-left: 15px; margin-bottom: 5px;margin-top:5px;" src="{{asset('/')}}hc4/img/iconos/lab.png">
                    <b>{{trans('transquirofano.laboratorio')}}</b>
                    @if(!is_null($paciente))
                    <center>
                        <div class="col-12" style="padding-bottom: 20px;">
                            <h1 style="font-size: 14px; margin:0;padding-left: 20px">
                                <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                                    {{$paciente->nombre1}} {{$paciente->nombre2}}
                                </b>
                            </h1>
                        </div>
                    </center>
                    @endif
                </h1>
                <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;margin-left: 0px">
                    <!--Hacemos el llamado al estilo parent-->
                    <div>
                        <div style=" margin-right: 30px;">
                            <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                            @foreach($ordenes as $value)
                            <div class="box" style="border: 2px solid #004AC1; border-radius: 10px;font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                <!--Cabecera-->
                                <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;font-size:15px;">
                                    <!--@if(!is_null($value))
                        @php
                          $fecha = substr($value->fecha_orden,0,10);
                          $invert = explode( '-',$fecha);
                          $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                        @endphp
                         <span> <b style="font-family: 'Helvetica';" class="box-title">{{$fecha_invert}}</b></span>
                      @endif-->

                                    @if(!is_null($value))
                                    @php
                                    $fecha_orden = $value->fecha_orden;
                                    $dia = Date('N',strtotime($value->fecha_orden));
                                    $mes = Date('n',strtotime($value->fecha_orden));
                                    @endphp

                                    <span> <b style="font-family: 'Helvetica';" class="box-title">
                                            @if($dia == '1') Lunes
                                            @elseif($dia == '2') Martes
                                            @elseif($dia == '3') Miércoles
                                            @elseif($dia == '4') Jueves
                                            @elseif($dia == '5') Viernes
                                            @elseif($dia == '6') Sábado
                                            @elseif($dia == '7') Domingo
                                            @endif
                                            {{substr($value->fecha_orden,8,2)}} de
                                            @if($mes == '1') Enero
                                            @elseif($mes == '2') Febrero
                                            @elseif($mes == '3') Marzo
                                            @elseif($mes == '4') Abril
                                            @elseif($mes == '5') Mayo
                                            @elseif($mes == '6') Junio
                                            @elseif($mes == '7') Julio
                                            @elseif($mes == '8') Agosto
                                            @elseif($mes == '9') Septiembre
                                            @elseif($mes == '10') Octubre
                                            @elseif($mes == '11') Noviembre
                                            @elseif($mes == '12') Diciembre
                                            @endif
                                            del {{substr($value->fecha_orden,0,4)}}</b></span>
                                    @endif


                                    <div class="pull-right box-tools">
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#demo{{$value->id}}">
                                            <i onclick="move({{$value->id}})" class="fa fa-plus" id="port{{$value->id}}"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--Cuerpo-->
                                <div class="box-body collapse" id="demo{{$value->id}}">
                                    <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                                        <div class="col-12">
                                            <div class="row ">
                                                <div class="col-md-8 col-12" style="border: 2px solid #004AC1;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;height: 100%; margin-bottom: 10px">
                                                    <div class="col-12" style="background-color: #004AC1;font-family: 'Helvetica general3';border-bottom: #004ac1; text-align: center ">
                                                        <label class="box-title" style="background-color: #004AC1;font-size: 16px;color:white">
                                                            {{trans('transquirofano.DetalledelaOrdendeLaboratorio')}}
                                                        </label>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <!--Obtenemos el nombre del Doctor de cada Orden de Laboratorio-->
                                                        <div class="form-group col-12" style="padding-right: 0px">
                                                            <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12">{{trans('transquirofano.Médico')}}</label>
                                                            <div class="col-12">
                                                                @foreach ($usuarios as $usuario)
                                                                @if($value->id_doctor_ieced==$usuario->id)
                                                                {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}
                                                                @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-12" style="padding-right: 0px">
                                                            <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12">{{trans('transquirofano.Seguro')}}</label>
                                                            <div class="col-12">
                                                                @if(!is_null($value->snombre))
                                                                {{$value->snombre}}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-12">
                                                    <span style="font-size: 15px;">{{trans('transquirofano.Progreso')}}</span>
                                                    <br>
                                                    <div class="progress progress" style="margin: 5px;">
                                                        <div class="progress-bar progress-bar-striped active" style="background-color: orange;" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                                                            <span id="sp{{$value->id}}" style="color: white;"></span>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <center>
                                                        <div class="col-md-12 col-8">
                                                            <a target="_blank" onclick="descargar({{$value->id}});" style="width: 100%; height: 100%; color: white" class="btn btn-danger boton-lab">
                                                                <span style="color: white" class="fa fa-download"></span> {{trans('transquirofano.Resultados')}}
                                                            </a>
                                                        </div>
                                                    </center>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!--Contenedor Listado de Examenes de Laboratorio-->
                                    <div class="col-12" style="margin:5px;">
                                        <!--Div que contiene el Contenedor-->
                                        <div style="border: 2px solid #004AC1; border-radius: 10px;" id="{{$value->id}}">
                                            <!--Div que contiene el Titulo-->
                                            <div class="col-12" class="box-header " style="background-color: #004AC1;font-family: 'Helvetica general3';border-bottom: #004AC1; height: 35px; text-align: center;">
                                                <label class="box-title" style="background-color: #004AC1;  font-size: 20px;color:white">
                                                    &nbsp;{{trans('transquirofano.Listadodeexamenesdelaboratorio')}}
                                                </label>
                                            </div>
                                            <div class="col-12" style="padding: 7px;">
                                                <!--Realizamos la Consulta a la tabla Examen_Detalle-->
                                                <div class="row">
                                                    @php
                                                    $orden = Sis_medico\Examen_Orden::find($value->id);
                                                    $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                                                    $resultados = $orden->resultados;
                                                    @endphp
                                                    @if($orden->seguro->tipo=='0')
                                                    @php
                                                    $agrupador = Sis_medico\Examen_Agrupador::all();
                                                    @endphp
                                                    @else
                                                    @php
                                                    $agrupador = Sis_medico\Examen_Agrupador_labs::orderBy('secuencia')->get();
                                                    @endphp
                                                    @endif
                                                    @php
                                                    $parametros = Sis_medico\Examen_Parametro::orderBy('orden')->get();
                                                    @endphp
                                                    <br>
                                                    @foreach($agrupador as $value)
                                                    @php
                                                    $i_agrupador=0;
                                                    $xycont = 0;
                                                    @endphp
                                                    @foreach($detalle as $value_detalle)
                                                    @php
                                                    $i=0;
                                                    $xycont ++;
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
                                                    @php
                                                    if($value_detalle->examen->sexo_n_s=='0'){
                                                    $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                                                    }else{
                                                    $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                                    }
                                                    @endphp
                                                    <div class="col-12">
                                                        @if($i_agrupador == 0)
                                                        <!--Contenedor Nombre Agrupador-->
                                                        <div style="text-align: center;border-bottom: 1px solid #004AC1;padding: 0px;background-color: #004AC1;">
                                                            <!--Nombre Agrupador-->
                                                            <label style="padding-top: 2px;font-size: 12px;color:white"> {{$value->nombre}}</label>
                                                            @php
                                                            $i_agrupador=1;
                                                            @endphp
                                                        </div>
                                                        <div style="width: 28%;float:left;color: white;"><b>{{trans('transquirofano.NOMBRE')}}</b></div>
                                                        <div style="width: 18%;float:left;color: white;"><b>{{trans('transquirofano.RESULTADO')}}</b></div>
                                                        <div style="width: 13%;float:left;color: white;"><b>{{trans('transquirofano.UNIDADES')}}</b></div>
                                                        <div style="width: 40%;float:left;color: white;"><b>{{trans('transquirofano.REFERENCIA')}}</b></div>
                                                        <div style="clear:both;"></div>
                                                        @endif
                                                        @if($i == 0)
                                                        @if($parametro_nuevo->count() > 1)
                                                        @if($value_detalle->id_examen!='639')
                                                        <div class="col-12" style="border-bottom: 1px solid #004AC1;padding: 0px">
                                                            <b>{{$value_detalle->examen->nombre}}</b>
                                                        </div>
                                                        @if($value_detalle->examen->texto!=null)
                                                        <div style="border-bottom: 1px solid #009a98;padding: 0px;">
                                                            <?php echo $value_detalle->examen->texto; ?>
                                                        </div>
                                                        @endif
                                                        @endif
                                                        @endif
                                                        @php
                                                        $i = 1;
                                                        @endphp
                                                        @endif
                                                        <div style="clear:both;"></div>
                                                        <div class="col-12" @if($parametro_nuevo->count() == 1) style="padding-left: 10px;" @endif>
                                                            <table id="example2" style="font-size: 12px;  width: 100%">
                                                                <tbody>
                                                                    @if($value_detalle->id_examen=='661')
                                                                    <tr style="font-size: 12px;">
                                                                        <td style="width: 20%;"><b>{{trans('transquirofano.GRADO')}}</b></td>
                                                                        <td style="width: 10%;"><b>{{trans('transquirofano.%CELULAS')}}</b></td>
                                                                        <td style="width: 8%;"><b>{{trans('transquirofano.LAPPUNTOS')}}</b></td>
                                                                        <td style="width: 40%;"><b>{{trans('transquirofano.REFERENCIA')}}</b></td>
                                                                    </tr>
                                                                    @endif
                                                                    @if($value_detalle->id_examen=='661')
                                                                    @foreach($parametro_nuevo as $value_agrupador)
                                                                    @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                                                    @php
                                                                    $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                                                    @endphp
                                                                    <tr role="row">
                                                                        <td style="width: 30%;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        @php
                                                                        $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                                        $resultado_2 = null;
                                                                        if(!is_null($hermano)){
                                                                        $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                                        }
                                                                        @endphp
                                                                        <td style="width: 20%;">
                                                                            <div style="word-wrap: break-word;">
                                                                                @if(!is_null($hermano))
                                                                                @if(!is_null($resultado_2))
                                                                                @if($resultado_2->certificado=='1')
                                                                                {{$resultado_2->valor}}
                                                                                @else
                                                                                {{"0"}}
                                                                                @endif
                                                                                @endif
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td style="width: 10%;">
                                                                            @if(!is_null($resultado))
                                                                            {{$resultado->valor}}
                                                                            @else
                                                                            {{"0"}}
                                                                            @endif
                                                                        </td>
                                                                        <td style="width: 40%;">
                                                                            @if($value_agrupador->texto_referencia == "")
                                                                            {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                            @else
                                                                            <?php echo
                                                                            $value_agrupador->texto_referencia;
                                                                            ?>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                    @endforeach

                                                                    @elseif($value_detalle->id_examen=='639')
                                                                    <center>
                                                                        <b>{{trans('trans.ALERGIASAALIMPORIGG59')}}</b>
                                                                    </center>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #ACCBEE;font-size: 12px;text-align: center;">{{trans('trans.ReacciónLeve')}}</div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #8AB0DB;font-size: 12px;text-align: center;">{{trans('trans.ReacciónModerada')}}</div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div style="background-color: #376EAC;color: white;font-size: 12px;text-align: center;">{{trans('trans.ReacciónFuerte')}}</div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    @php $cuenta=0;
                                                                    @endphp
                                                                    <tr role="row">
                                                                        @foreach($parametro_nuevo as $value_agrupador)
                                                                        @php

                                                                        $rvalor=0;

                                                                        $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();

                                                                        if(!is_null($resultado)){
                                                                        $rvalor=$resultado->valor;

                                                                        }


                                                                        @endphp

                                                                        @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                                                        @if($cuenta == 0)
                                                                    <tr> @endif
                                                                        <td style="width: 3%; font-size: 10px; padding:0;">
                                                                            {{$value_agrupador->sec}}
                                                                        </td>
                                                                        <td style="width: 12%;  font-size: 10px;padding:0;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        <td style="width: 3%;padding:0;">
                                                                        </td>
                                                                        <td style="width: 3%;padding:0;">
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#A8A8A8; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">@if($value_agrupador->orden=='48') <span style="color: white">x</span> @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            &nbsp;
                                                                        </td>
                                                                        @php $cuenta++; @endphp
                                                                        @if($cuenta == 2)

                                                                    </tr>
                                                                    @endif
                                                                    @else
                                                                    @if($cuenta == 0) <tr> @endif
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            {{$value_agrupador->sec}}
                                                                        </td>
                                                                        <td style="width: 12%;  font-size: 10px;padding:0;">
                                                                            {{$value_agrupador->nombre}}
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 13px;padding:0;">
                                                                            @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#8AB0DB; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                                            @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%; background-color:#376EAC; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                                            @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif</span> @else &nbsp; @endif
                                                                        </td>
                                                                        <td style="width: 3%;  font-size: 10px; padding:0;">
                                                                            &nbsp;
                                                                        </td>
                                                                        @php $cuenta++; @endphp
                                                                        @if($cuenta == 2)
                                                                    </tr>
                                                                    @php
                                                                    $cuenta=0;
                                                                    @endphp
                                                                    @endif  
                                                                    @endif
                                                                    @endforeach

                                                                    </tr>
                                                                    <table>
                                                                        <tr role="row">
                                                                            <br>
                                                                            <br>
                                                                            <div>{{trans('transquirofano.*19PescadoBlanco')}}</div>
                                                                            <div>{{trans('transquirofano.*20PescadodeAguaDulce')}}</div>
                                                                            <div>{{trans('transquirofano.*22MariscoMix')}}</div>
                                                                            <div>{{trans('transquirofano.*30Mezclasdepimientos')}}</div>
                                                                            <div>{{trans('transquirofano.*31Leguminosas')}}</div>
                                                                            <div>{{trans('transquirofano.*33MelónMix:MelónySandía')}}</div>
                                                                            <div style="text-align: justify;">{{trans('transquirofano. Sisusresultadosindican')}}</div>
                                                                            <div>{{trans('transquirofano.Algunaspersonas')}}</div>


                                                                        </tr>
                                                                    </table>

                                                                    @else
                                                                    
                                                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                                                        @foreach($parametro_nuevo as $value_agrupador)
                                                                        @php
                                                                        $resultado = $orden->resultados
                                                                        ->where('id_parametro', $value_agrupador->id)
                                                                        ->first();
                                                                        @endphp
                                                                     
                                                                        @if(!is_null($resultado))
                                                                        @if($resultado->certificado=='1')
                                                                        <tr role="row">

                                                                            <td style="width: 30%;@if($parametro_nuevo->count() == 1) font-weight: 700;padding-left: -3px;font-size: 14px; @endif">
                                                                                {{$value_agrupador->nombre}}
                                                                            </td>

                                                                            <td style="width: 20%;">
                                                                                <div style="word-wrap: break-word;">
                                                                                    @if(!is_null($resultado))
                                                                                    {{$resultado->valor}}
                                                                                    @else
                                                                                    {{"0"}}
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            @php
                                                                            $fl_ref = 0;
                                                                            $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                                                            if(!is_null($ref_ant)){
                                                                            $fl_ref = 1;
                                                                            }
                                                                            @endphp
                                                                            <td style="width: 10%;">
                                                                                @if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif
                                                                            </td>

                                                                            <td style="width: 40%;">
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
                                                                        $sub_resultados = DB::table('examen_sub_resultado')
                                                                        ->where('id_orden',$orden->id)
                                                                        ->where('estado','1')
                                                                        ->where('id_examen',$value_detalle
                                                                        ->id_examen)->get();
                                                                        @endphp
                                                                        <tr>
                                                                            <td colspan="4" id="sub_tabla">
                                                                                <table class="table b-table">
                                                                                    <tbody>
                                                                                        @foreach($sub_resultados as $sub)
                                                                                        <tr>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo1}}</td>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo2}}</td>
                                                                                            <td style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">{{$sub->campo3}}</td>
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
                                                    @endif
                                                    @endforeach
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- procedimientos -->
            <div class="col-12" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;margin-top: 25px;">
                <!--Titulo-->
                <h1 style="font-size: 15px;">
                    <center>
                        <b>{{trans('transquirofano.procedimientos')}}</b>
                    </center>                    
                </h1>
                <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;margin-left: 0px">
                    <!--Hacemos el llamado al estilo parent-->
                    <div>
                        <div style=" margin-right: 30px;">
                            <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                            <div class="box" style="border: 2px solid #004AC1; border-radius: 10px;font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                <!--Cuerpo-->
                                <!--Div que contiene el Titulo-->
                                @if(isset($procedimientos))
                                    @if(count($procedimientos)>0)
                                        <div class="col-12" class="box-header " style="background-color: #004AC1;font-family: 'Helvetica general3';border-bottom: #004AC1; height: 35px; text-align: center;">
                                            <label class="box-title" style="background-color: #004AC1;  font-size: 20px;color:white">
                                                &nbsp;{{trans('transquirofano.ListadodeProcedimientos')}}
                                            </label>
                                        </div>
                                        <div class="col-12" style="padding: 7px;">
                                            <!--Realizamos la Consulta a la tabla Examen_Detalle-->
                                            <div class="row">
                                                <br>
                                                <table border="1" style="width: 95%;margin-left: 2.5%;">
                                                    <thead>
                                                        <tr>
                                                            <td>{{trans('transquirofano.Fecha')}}</td>
                                                            <td>{{trans('transquirofano.NOMBRE')}}</td>
                                                            <td>{{trans('transquirofano.accion')}}</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($procedimientos as $procedimiento)
                                                            <tr>
                                                                <td>{{$procedimiento->fechaini}}</td>
                                                                <td>{{$procedimiento->nombre}}</td>
                                                                <td>
                                                                    <button style="width: 100%;" class="btn btn-primary btn-xs" onclick="ver_estudio('{{$procedimiento->id_protocolo}}')">{{trans('hospitalizacion.ver')}}</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- consultas -->
            <div class="col-12" style="border: 2px solid #004AC1;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;margin-top: 25px;">
                <!--Titulo-->
                <h1 style="font-size: 15px;">
                    <center>
                        <b>{{trans('transquirofano.consultas')}}</b>
                    </center>                    
                </h1>
                <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px;margin-left: 0px">
                    <!--Hacemos el llamado al estilo parent-->
                    <div>
                        <div style=" margin-right: 30px;">
                            <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;margin-left: 0px">
                                <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                                <div class="box" style="border: 2px solid #004AC1; border-radius: 10px;font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                    <!--Cuerpo-->
                                    <!--Div que contiene el Titulo-->
                                    @if(isset($consultas))
                                        @if(count($consultas)>0)
                                        <div class="col-12" class="box-header " style="background-color: #004AC1;font-family: 'Helvetica general3';border-bottom: #004AC1; height: 35px; text-align: center;">
                                            <label class="box-title" style="background-color: #004AC1;  font-size: 20px;color:white">
                                                &nbsp;{{trans('transquirofano.ListadodeConsultas')}}
                                            </label>
                                        </div>
                                        <div class="col-12" style="padding: 7px;">
                                            <!--Realizamos la Consulta a la tabla Examen_Detalle-->
                                            <div class="row">
                                                <br>
                                                <table border="1" style="width: 95%;margin-left: 2.5%;">
                                                    <thead>
                                                        <tr>
                                                            <td>{{trans('transquirofano.Fecha')}}</td>
                                                            <td>{{trans('transquirofano.NOMBRE')}}</td>
                                                            <td>{{trans('transquirofano.accion')}}</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($consultas as $value)
                                                            <tr>
                                                                <td>{{$value->fechaini}}</td>
                                                                <td>{{$value->nombre}}</td>
                                                                <td>
                                                                    <button style="width: 100%;" class="btn btn-primary btn-xs" onclick="ver_consulta('{{$value->id_hc_evolucion}}')">{{trans('hospitalizacion.ver')}}</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var valor = true;

    function move(id) {
        if (valor == true) {
            console.log(valor);
            document.getElementById("port" + id).className = "fa fa-minus";
            valor = false;
        } else {
            document.getElementById("port" + id).className = "fa fa-plus";
            valor = true;
        }
    }

    function descargar(id_or) {
        var cert = $('#sp' + id_or).text();
        if (cert == '0%') {
            alert("Sin Exámenes Ingresados");
        } else {
            window.open("{{url('hospital/mostrar/resultados/imprimir')}}/" + id_or, '_blank');

        }
    }
    $(document).ready(function($) {
        @foreach($ordenes as $value)
        $.ajax({
            type: 'get',
            url: "{{ route('valida.puede_imprimir',['id' => $value->id]) }}",
            success: function(data) {
                console.log(data);
                if (data.cant_par == 0) {
                    var pct = 0;
                    //console.log(pct);  
                } else {
                    var pct = data.certificados / data.cant_par * 100;
                    //console.log(pct); 
                }
                //alert(pct);
                //$('#st').val(pct);
                if (pct <= 0) {
                    document.getElementById("{{$value->id}}").style.display = "none";
                }
                $('#td{{$value->id}}').css("width", Math.round(pct) + "%");
                $('#sp{{$value->id}}').text(Math.round(pct) + "%");
                if (pct < 10) {
                    $('#td{{$value->id}}').addClass("progress-bar-danger");
                } else if (pct >= 10 && pct < 90) {
                    $('#td{{$value->id}}').addClass("progress-bar-warning");
                } else {
                    $('#td{{$value->id}}').addClass("progress-bar-success");
                }
            },

            error: function(data) {}

        });

        @endforeach
    });
    function ver_consulta(id){
$.ajax({
    type: "get",
    url: "{{url('hospital/consultas_listado/modal/')}}/"+id,
    data: {
    // 'ep': id_orden,
    },
    datatype: "html",
    success: function(datahtml, data) {
        $("#consulta_div").html(datahtml);
    },
    error: function() {
        alert('error al cargar');
    }
});
$('#consulta_modal').modal('show');
}
function ver_estudio(id){
$.ajax({
    type: "get",
    url: "{{url('hospital/vtimagen/modal')}}/"+id,
    data: {
    // 'ep': id_orden,
    },
    datatype: "html",
    success: function(datahtml, data) {
        $("#examen_div").html(datahtml);
    },
    error: function() {
        alert('error al cargar');
    }
});
$('#examen_modal').modal('show');
}
</script>