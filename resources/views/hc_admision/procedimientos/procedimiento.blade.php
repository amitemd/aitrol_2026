@extends('hc_admision.visita.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">

    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }

    .mce-edit-focus,
        .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
        }


    .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }

        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222;
        }
</style>
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>
<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>

<div class="modal fade" id="mprotocolo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>
</div>

<div class="modal fade" id="mprotocolo_cpre_eco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>
</div>
<div class="modal fade" id="moxigeno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>
<div class="modal fade" id="mproductos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>

<div class="modal fade" id="formulario013" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>
</div>

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-10" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$agenda->id_paciente}}</td>
                                    <td style="text-align:right;"><b>Cortesias en el día</b></td><td style="text-align:right; @if($cant_cortesias>1) color:red; @endif">{{$cant_cortesias}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2" style="padding-left: 0px;padding-right: 0px;">
            <a type="button" href="{{route('agenda.detalle', ['id' => $id_agenda ])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Historia Clínica</span>
            </a>

            <!--a type="button" href="{{route('estudio.lista', ['id' => $agenda->id ])}}" class="btn btn-success btn-sm">
                <span> Procedimientos</span>
            </a-->
        </div>
        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">

                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#evol').click());">Evoluciones</a></b></h3></div>

                    <div class="col-md-6" >
                        <span style="color: orange;">
                            @if($evoluciones_proc->count()=='0')
                                Procedimiento sin Evoluciones
                            @else
                                Tiene {{$evoluciones_proc->count()}} evoluciones para el procedimiento
                            @endif
                        </span>
                    </div>

                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="evol">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="col-md-12" style="overflow:scroll; height: 500px;">

                        @if($evoluciones_proc->count()>0)
                        @foreach($evoluciones_proc as $value)
                        <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: @if($agenda->proc_consul=='1') @if($value->hc_id_procedimiento == $protocolo->id_hc_procedimientos) orange 1.5px @else black 1px @endif @else black 1px @endif solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})">
                            <table class="table table-striped table-bordered table-hover">

                                <tr>
                                    <td><b>Fecha: </b></td>
                                    @php $dia =  Date('N',strtotime($value->fechaini)); $mes =  Date('n',strtotime($value->fechaini)); @endphp
                                    <td style="color: blue;"><b>
                                        @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($value->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($value->fechaini,0,4)}}</b>
                                    </td>
                                    <td><b>Hora: </b></td>
                                    <td style="color: blue;"><b>{{substr($agenda->fechaini,10,10)}}</b></td>

                                </tr>
                                <tr>

                                    <td><b>Motivo: </b></td>
                                    <td>{{$value->motivo}}</td>
                                    <td style="color: Cornsilk;">{{$value->id}}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Evolución: </b></td>
                                    <td colspan="2"><b>@if($value->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($value->espid)->nombre}} @elseif($value->proc_consul=='1')PROCEDIMIENTO: @php
                                    $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($value->hc_id_procedimiento);
                                    if($procedimiento_evolucion != null){
                                        if($procedimiento_evolucion->id_procedimiento_completo != null){
                                            echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
                                        }
                                    }
                                    @endphp
                                    @endif</b></td>
                                </tr>
                                <tr>
                                    <td colspan="4"><?php echo $value->cuadro_clinico ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"><b>Diagnóstico: </b></td>
                                </tr>
                                <tr>
                                    @php $hc_cie10 = DB::table('hc_cie10')->where('hc_id_procedimiento',$value->hc_id_procedimiento)->get(); @endphp
                                        @foreach($hc_cie10 as $cie10)
                                        @php $c10 = DB::table('cie_10_3')->where('id',$cie10->cie10)->first(); @endphp
                                        @if(!is_null($c10))
                                        <tr><td colspan="4">
                                        {{$c10->descripcion}}
                                        </td></tr>
                                        @endif
                                        @php $c10 = DB::table('cie_10_4')->where('id',$cie10->cie10)->first(); @endphp
                                        @if(!is_null($c10))
                                        <tr><td colspan="4">
                                        {{$c10->descripcion}}
                                        </td></tr>
                                        @endif
                                        @endforeach
                                </tr>
                                <tr>
                                    <td colspan="4"><b>Receta: </b></td>
                                </tr>
                                @php
                                    $receta_evolucion  =  Sis_medico\hc_receta::where('id_hc',$value->hcid)->first();
                                @endphp
                                @if($receta_evolucion != null)
                                    @php
                                        $receta_detalle=Sis_medico\hc_receta_detalle::where('id_hc_receta',$receta_evolucion->id)->get();
                                    @endphp
                                    @if($receta_detalle != null)
                                        @foreach($receta_detalle as $value_receta_detalle)
                                        <tr>
                                            <td colspan="2"> {{$value_receta_detalle->medicina->nombre}} </td>
                                            <td colspan="2"><b>Cantidad:</b> {{$value_receta_detalle->cantidad}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="4"><?php echo $receta_evolucion->prescripcion; ?></td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </div>

                        @endforeach
                            @if($agenda->historia_clinica!=null)
                                <div class="table-responsive col-md-12 col-xs-12" style="font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" >
                                    <b>HISTORIA CLÍNICA SCI</b><br>
                                <textarea style="width: 100%;height: 400px;">
                                    {{$agenda->historia_clinica}}
                                </textarea>
                                </div>
                            @endif
                        @else
                            @if($agenda->historia_clinica==null)
                            <h4 align="center" style="background: #e6fff7">SIN INFORMACIÓN PREVIA REGISTRADA EN EL SISTEMA</h4>
                            @else
                            <div class="table-responsive col-md-12 col-xs-12" style="font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" >
                                <b>HISTORIA CLÍNICA SCI</b><br>
                                <textarea style="width: 100%;height: 400px;">
                                    {{$agenda->historia_clinica}}
                                </textarea>
                            </div>
                            @endif
                        @endif
                    </div>
                    @php $dia =  Date('N',strtotime($agenda->fechaini));
                         $mes =  Date('n',strtotime($agenda->fechaini));
                    @endphp

                </div>
            </div>
        </div>

        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#examenes_externos').click());"><b>Examenes Externos</b></a></h3>
                    </div>

                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="examenes_externos">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered  dataTable" >
                            <tbody style="font-size: 12px;">
                                @php $count=0; @endphp
                                @foreach($laboratorio_externo as $imagen)
                                <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        //$d2 = $variable[4];
                                        //$d3 = $variable[5];

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @endif
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#biopsias_h').click());"><b>Historial de Biopsias</b></a></h3>
                    </div>

                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="biopsias_h">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered  dataTable" >
                            <tbody style="font-size: 12px;">
                                @php $count=0; @endphp
                                @foreach($biopsias_1 as $imagen)
                                <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        //$d2 = $variable[4];
                                        //$d3 = $variable[5];

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @endif
                                </div>
                                @endforeach

                                @php $count=0; @endphp
                                @foreach($biopsias_2 as $imagen)
                                <div class="col-md-3" style='margin: 10px 0;text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->nombre);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        //$d2 = $variable[4];
                                        //$d3 = $variable[5];

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @endif
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($agenda->estado_cita!='4')
        <div class="col-md-12" style="padding-right: 6px;">
            @php $dia =  Date('N',strtotime($agenda->fechaini)); $mes =  Date('n',strtotime($agenda->fechaini)); @endphp
            <div class="callout callout-warning col-md-12" style="margin-bottom: 5px;padding: 5px;">
             Paciente Aun No Admisionado Para @if($agenda->proc_consul=='0') La Consulta @elseif($agenda->proc_consul=='1') El Procedimiento @endif
             Del @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}}, se encuentra en estado @if($agenda->estado_cita=='0') POR CONFIRMAR @elseif($agenda->estado_cita=='1') CONFIRMADO @elseif($agenda->estado_cita=='2') REAGENDAR @elseif($agenda->estado_cita=='3') SUSPENDIDA: {{$agenda->observaciones}} @endif
            </div>
        </div>
        @endif

        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary" >
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title"><b>Procedimiento</b></h3>
                    </div>
                    <!--div class="col-md-3" style="color: #f2f2f2;">
                        <a type="button" href="{{route('hc_reporte.seleccion', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '1'  ])}}"  class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-download ">  Reporte</span>
                        </a>
                    </div-->
                    <div class="col-md-6" style="color: #f2f2f2;">{{$protocolo->id}}</div>
                    <div class="col-md-12" style="padding-left: 0px;">
                         @if($agenda->estado_cita=='4')

                        <!--button id="nuevo_proc" type="button" onclick="Agregar_procedimiento();" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-plus"> Agregar Procedimiento</span>
                        </button-->

                        @if($agenda->estado_cita =='4' && $agenda->proc_consul == '1')
                        <a id="nueva_evolucion" onclick="agregar_evolucion();" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                            <span>Agregar Evolucion</span>
                        </a>
                        @endif

                        <a href="{{route('epicrisis.mostrar',['hcid' => $protocolo->hcid, 'proc' => $protocolo->id_hc_procedimientos])}}">
                            <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file"> Epicrisis</span>
                            </button>
                        </a>

                        <a href="{{route('hc_reporte.seleccion', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '0'  ])}}">
                            <button type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file">  Estudio</span>
                            </button>
                        </a>
                        <a target="_blank" href="{{route('hc_reporte.descargar', ['id'=> $protocolo->id, 'tipo' => '1' ])}}">
                            <button type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file">  @lang('admision.estudio_formato')</span>
                            </button>
                        </a>

                        <a href="{{ route('oxigeno.oxigeno_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#moxigeno">
                                    <span class="glyphicon glyphicon-download-alt"> Oxigeno</span>
                        </a>
                        <a href="{{ route('descargoProducto.productos_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mproductos">
                                    <span class="glyphicon glyphicon-download-alt"> Productos</span>
                        </a>
                        @php $anestesiologia = Sis_medico\Hc_Anestesiologia::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->first(); @endphp
                        @if(!is_null($anestesiologia))
                        <a href="{{ route('anestesiologia.imprime', ['id' => $protocolo->id_hc_procedimientos]) }}" type="button" class="btn btn-success btn-sm" target="_blank">
                            <span class="glyphicon glyphicon-download-alt"> Record Anestesiologico</span>
                        </a>
                        @else
                        <a href="#" type="button" class="btn btn-danger btn-sm" >
                            <span class="glyphicon glyphicon-download-alt"> Sin record</span>
                        </a>
                        @endif
                        @php 
                                $hc_egreso = DB::table('hc_egreso')->where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->first();                                
                                if(is_null($hc_egreso)){
                                    $historia = \Sis_medico\Historiaclinica::where('hcid', $protocolo->hcid)->first();
                                    $procedimiento = $historia->procedimiento()->first();
                                    if(!is_null($procedimiento->id_doctor_examinador)){
                                        $id_doctor_egreso = $procedimiento->id_doctor_examinador;
                                    }elseif(!is_null($historia->id_doctor1)){
                                        $id_doctor_egreso = $historia->id_doctor1;
                                    }else{
                                        $id_doctor_egreso = $agenda->id_doctor1;
                                    }
                                    $id_egreso = \Sis_medico\HcEgreso::insertGetId([
                                        'id_hc_procedimientos'     => $protocolo->id_hc_procedimientos,
                                        'ta'                       => '110/70',
                                        'fc'                       => '80',
                                        'fr'                       => "18",
                                        'sato2'                    => '98',
                                        't'                        => '37',
                                        'condiciones'              => '1',
                                        'conducido'                => '1',
                                        'id_doctor_egreso'         => $id_doctor_egreso,
                                        'ip_creacion'              => $protocolo->ip_creacion,
                                        'ip_modificacion'          => $protocolo->ip_creacion,
                                        'id_usuariocrea'           => $protocolo->id_usuariocrea,
                                        'id_usuariomod'            => $protocolo->id_usuariocrea,
                                    ]);
                                    $hc_egreso = \Sis_medico\HcEgreso::find($id_egreso);
                                  }                                    
                        @endphp
                        @if(!is_null($hc_egreso)) 
                            <a target="_blank" href="{{route('formularioPreparacion.excelPreparacion', ['id'=>$protocolo->id_hc_procedimientos,'id_protocolo' => $protocolo->id])}} " class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Formulario Recuperacion </a>
                        @endif
                        @php
                                $rolUsuario = Auth::user()->id_tipo_usuario;
                            @endphp
                            @if(in_array($rolUsuario, array(1, 11)) == true )
                                <a href="{{ route('evolucion.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                                    <span class="glyphicon glyphicon-file"> Evolución</span>
                                </a>
                                <a href="{{ route('protocolo.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                                    <span class="glyphicon glyphicon-download-alt"> Protocolo</span>
                                </a>
                            @endif

                            <button id="b_cpre_eco" class="btn btn-success btn-sm" onclick="carga_cpre_eco();">Agregar CPRE+ECO</button>

                        @endif
                        <a target="_blank" href="{{route('formulario013.edit', ['id'=>$protocolo->id_hc_procedimientos])}} " class="btn btn-success btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Formulario013</a>
                    </div>
                    @if($agenda->estado_cita=='4')
                        @php
                            $dr_training = Auth::user();
                            $proc_training = Sis_medico\Hc_protocolo_training::where('id_hc_protocolo',$protocolo->id)->where('id_training',$dr_training->id)->first();
                        @endphp

                        @if($dr_training->training)
                        <div class="col-md-3" ><b>TRAINING: </b><input id="ch" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> Participe de este Procedimiento</span></div>
                        @endif
                    @endif

                        <!-- tools box
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse">
                                <i class="fa fa-plus"></i></button>
                        </div>
                        /. tools -->
                </div>

                <div class="box-body" style="padding: 5px;">


                    <div class="col-md-6" >

                        @php $dia =  Date('N',strtotime($agenda->fechaini));
                            $mes =  Date('n',strtotime($agenda->fechaini));
                        @endphp
                        <form id="frm2">
                            <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
                            <input type="hidden" name="id_hc_procedimientos" value="{{$protocolo->id_hc_procedimientos}}">
                            <input type="hidden" name="hcid" value="{{$protocolo->hcid}}">
                            <input type="hidden" name="protocolo" value="{{$protocolo->id}}">

                            <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                                <b>Fecha Procedimiento: </b>@if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: </b>{{substr($agenda->fechaini,10,10)}}
                            </div>
                            @if($agenda->estado_cita=='4')
                            <div class="col-md-6" style="padding: 1px;">
                                <label for="estado_pentax" class="control-label">Estado Paciente: </label>
                                <span>@if(!is_null($agenda->hcid))@if($protocolo->estado_pentax=='0') EN ESPERA @elseif($protocolo->estado_pentax=='1') PREPARACIÓN @elseif($protocolo->estado_pentax=='2') EN PROCEDIMIENTO @elseif($protocolo->estado_pentax=='3') RECUPERACIÓN @elseif($protocolo->estado_pentax=='4') ALTA @elseif($protocolo->estado_pentax=='5') SUSPENDIDO @endif @else "NO ASISTE" @endif</span>
                            </div>
                            @endif

                            <div class="col-md-6" style="padding: 1px;">
                                <label for="est_amb_hos" class="control-label">Ingreso: </label>
                                <span>@if(!is_null($agenda->est_amb_hos))@if($agenda->est_amb_hos=='0') AMBULATORIO @elseif($agenda->est_amb_hos=='1') HOSPITALIZADO @endif @endif</span>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label class="control-label">Procedimientos Agendados</label>
                                @foreach($procedimientos_pentax as $value)
                                <span class="bg-blue" style="border-radius: 4px;padding: 3px;margin-right: 5px;">{{$value->procedimiento->observacion}}</span>
                                @endforeach
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="proc_com" class="control-label">Procedimiento</label>
                                @php
                                    $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->get();
                                    $mas = true;
                                    $texto = "";
                                    foreach($adicionales as $value2)
                                    {
                                        if($mas == true){
                                         $texto = $texto.$value2->procedimiento->nombre  ;
                                         $mas = false;
                                         }
                                        else{
                                         $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                         }
                                    }
                                    if($texto == ""){
                                        $procedimiento_nombre_2 = \Sis_medico\procedimiento_completo::find($protocolo->id_procedimiento_completo);
                                        if(!is_null($procedimiento_nombre_2)){
                                            $texto = $procedimiento_nombre_2->nombre_general;
                                        }
                                    }
                                @endphp
                                <br>
                                <span>{{$texto}}</span>

                            </div>

                            <div class="col-md-3" style="padding: 1px;display: none " >
                                <label for="id_doctor1" class="control-label">Doctor</label>
                                <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" required onchange="guardar_procedimiento();">
                                    @foreach($doctores as $value)
                                    <option @if($protocolo->id_doctor1==$value->id)selected @endif value={{$value->id}}>Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="id_anestesiologo" class="control-label">Anestesiólogo</label>
                                <select id="id_anestesiologo" name="id_anestesiologo" class="form-control input-sm" required onchange="guardar_procedimiento();">
                                    <option value="">Seleccione ...</option>
                                    @foreach($anestesiologos as $value)
                                    <option @if($protocolo->id_anestesiologo==$value->id)selected @endif value="{{$value->id}}">Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="id_doctor2" class="control-label">Asistente 1</label>
                                <select id="id_doctor2" name="id_doctor2" class="form-control input-sm" onchange="guardar_procedimiento();">
                                    <option value="">Seleccione ...</option>
                                    @foreach($doctores as $value)
                                    <option @if($protocolo->id_doctor2==$value->id)selected @endif value="{{$value->id}}">Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                    @foreach($enfermeros as $value)
                                    <option @if($protocolo->id_doctor2==$value->id)selected @endif value="{{$value->id}}">Enf. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="id_doctor3" class="control-label">Asistente 2</label>
                                <select id="id_doctor3" name="id_doctor3" class="form-control input-sm" onchange="guardar_procedimiento();">
                                    <option value="">Seleccione ...</option>
                                    @foreach($doctores as $value)
                                    <option @if($protocolo->id_doctor3==$value->id)selected @endif value="{{$value->id}}">Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                    @foreach($enfermeros as $value)
                                    <option @if($protocolo->id_doctor3==$value->id)selected @endif value="{{$value->id}}">Enf. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1" style="padding: 1px;">
                                <br>
                                <button type="button" class="btn btn-warning" onclick="guardar_procedimiento2()"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </div>    

                            <?php /*
<!--div class="col-md-3" style="padding: 1px;">
<label for="cortesia" class="control-label">Seguro</label>
<select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">
<option value="">Seleccione ...</option>
@foreach($seguros as $value)
<option @if($protocolo->id_seguro==$value->id)selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
@endforeach
</select>
</div>

<div class="col-md-3" style="padding: 1px;">
<label for="cortesia" class="control-label">Subseguro</label>
<select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">

</select>
</div--> */?>

                            <div class="col-md-12" style="padding: 1px;"></div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="motivo" class="control-label">Motivo</label>
                                <textarea name="motivo" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->motivo}}@endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="thallazgos" class="control-label">Hallazgos</label>
                                <div id="thallazgos" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->hallazgos ?>@endif</div>
                                <input type="hidden" name="hallazgos" id="hallazgos">
                            </div>

                            <!--div class="col-md-12" style="padding: 1px;">
                                <label for="conclusion" class="control-label">Conclusión</label>
                                <textarea name="conclusion" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->conclusion}} @endif</textarea>
                            </div-->

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="tconclusion" class="control-label">Conclusión</label>
                                <div id="tconclusion" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->conclusion ?>@endif</div>
                                <input type="hidden" name="conclusion" id="conclusion">
                            </div>


                            <div class="col-md-12" style="padding: 1px;">
                                <label for="complicacion" class="control-label">Complicaciones</label>
                                <textarea name="complicacion" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->complicacion}} @endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="estado_paciente" class="control-label">Estado del Paciente al Terminar</label>
                                <textarea name="estado_paciente" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->estado_paciente}} @endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="plan" class="control-label">Plan Terapeutico</label>
                                <textarea name="plan" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->plan}} @endif</textarea>
                            </div>
                            <div class="col-md-12" style="padding: 1px;">
                                <button type="button" class="btn btn-warning" onclick="guardar_procedimiento2()">Guardar</button>
                            </div>    

                            <!--div class="col-md-12" style="padding: 1px;">
                                <label for="estudio_patologico" class="control-label">Estudio Anátomo Patológico</label>
                                <textarea name="estudio_patologico" style="width: 100%;" rows="1" onchange="guardar_procedimiento();">{{$protocolo->estudio_patologico}}</textarea>
                            </div-->

                        </form>
                    </div>
                    <div class="col-md-6">
                        <div id="div_cpre_eco" class="col-md-12"></div>
                        <form id="hc_protocolo">
                            <input type="hidden" name="id_hc_procedimiento" value="{{$hc_procedimiento->id}}">
                            <div class="col-md-12" style="padding: 1px;">

                                <label for="id_doctor_examinador" class="control-label">Medico Examinador</label>
                                <select onchange="hc_protocolo();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                    @foreach($doctores as $value)
                                        <option @if($hc_procedimiento->id_doctor_examinador == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" style="padding: 1px;">
                                <label for="id_seguro" class="control-label">Seguro</label>
                                <select onchange="hc_protocolo();cargar_empresa();"  class="form-control input-sm" style="width: 100%;" name="id_seguro" id="id_seguro">
                                    @foreach($seguros as $value)
                                        <option @if($hc_procedimiento->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" style="padding: 1px;" id="div_empresa">
                                
                            </div>


                            <br>



                            <div class="col-md-12" style="padding: 1px;">
                                <label for="observaciones" class="control-label">Observaciones</label>
                                <textarea class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;" rows="7" onchange="hc_protocolo();">{{$hc_procedimiento->observaciones}}</textarea>
                            </div>
                            <div class="col-md-12" style="padding: 1px;">
                                <button type="button" class="btn btn-warning" onclick="hc_protocolo2()">Guardar</button>
                            </div>    
                        </form>

                        <form id="frm_cie">
                            <input type="hidden" name="codigo" id="codigo">


                            <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px;"><b>Diagnóstico</b></label>
                            <div class="form-group col-md-12" style="padding: 1px;">
                                <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>
                            </div>
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <select id="ing_egr" name="pre_def" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                    <option value="INGRESO">INGRESO</option>
                                    <option value="EGRESO">EGRESO</option>
                                </select>
                            </div>
                            @if(!is_null($protocolo) && $agenda->estado_cita=='4')
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <button id="bagregar" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                            </div>
                            @endif


                            <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                                </table>
                            </div>
                        </form>

                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">


                                <div class="box-header">
                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#ima_historico').click());">Imagenes</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                        <!-- tools box -->
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="ima_historico">
                                                <i class="fa fa-plus"></i></button>
                                        </div>
                                        <!-- /. tools -->
                                </div>
                                <div class="box-body" style="padding: 5px;">
                                    @if(!is_null($protocolo) && $agenda->estado_cita=='4')
                                    <div class="form-group col-md-3" style="padding: 1px;">
                                        <a type="button" href="{{route('hc_video.mostrar', ['id_protocolo' => $protocolo->id, 'agenda' => $id_agenda, 'ruta' => 1 ])}}" class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-camera"> Imágenes</span>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="table-responsive col-md-12">
                                        <table class="table table-bordered  dataTable" >
                                            <tbody style="font-size: 12px;">
                                                @php $count=0; @endphp
                                                @foreach($imagenes as $imagen)
                                                <div class="col-md-6" style='margin: 10px 0; text-align: center;' >
                                                @php
                                                    $explotar = explode( '.', $imagen->nombre);
                                                    $extension = end($explotar);
                                                @endphp
                                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                    </a><br>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @elseif(($extension == 'mp4'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/video.png')}}" width="90%">
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        //$d2 = $variable[4];
                                                        //$d3 = $variable[5];

                                                    @endphp
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @endif
                                                </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                                <div class="box-header">
                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_historico').click());">Documentos &amp; Anexos</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                        <!-- tools box -->
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_historico">
                                                <i class="fa fa-plus"></i></button>
                                        </div>
                                        <!-- /. tools -->
                                </div>
                                <div class="box-body" style="padding: 5px;">
                                    @if(!is_null($protocolo) && $agenda->estado_cita=='4')
                                    <div class="form-group col-md-3" style="padding: 1px;">
                                        <a type="button" href="{{route('hc_video.mostrar_documento', ['id_protocolo' => $protocolo->id, 'agenda' => $id_agenda, 'ruta' => 1 ])}}" class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-open-file"> Documentos &amp; Anexos</span>
                                        </a>
                                    </div>
                                    @endif

                                    <div class="table-responsive col-md-12">
                                        <table class="table table-bordered  dataTable" >
                                            <tbody style="font-size: 12px; ">
                                                @php $count=0; @endphp
                                                @foreach($documentos as $imagen)
                                                <div class="col-md-6" style='margin: 10px 0;text-align: center;' >
                                                @php
                                                    $explotar = explode( '.', $imagen->nombre);
                                                    $extension = end($explotar);
                                                @endphp
                                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @elseif(($extension == 'pdf'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        //$d2 = $variable[4];
                                                        //$d3 = $variable[5];

                                                    @endphp
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @endif
                                                </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                                <div class="box-header">
                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_estudios').click());">Estudios</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                        <!-- tools box -->
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_estudios">
                                                <i class="fa fa-plus"></i></button>
                                        </div>
                                        <!-- /. tools -->
                                </div>
                                <div class="box-body" style="padding: 5px;">
                                    @if(!is_null($protocolo) && $agenda->estado_cita=='4')
                                    <div class="form-group col-md-3" style="padding: 1px;">
                                        <a type="button" href="{{route('hc_video.mostrar_estudios', ['id_protocolo' => $protocolo->id, 'agenda' => $id_agenda, 'ruta' => 1 ])}}" class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-open-file"> Estudios</span>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="table-responsive col-md-12">
                                        <table class="table table-bordered  dataTable" >
                                            <tbody style="font-size: 12px;">
                                                @php $count=0; @endphp
                                                @foreach($estudios as $imagen)
                                                <div class="col-md-6" style='margin: 10px 0;' >
                                                @php
                                                    $explotar = explode( '.', $imagen->nombre);
                                                    $extension = end($explotar);
                                                @endphp
                                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">

                                                    </a>
                                                @elseif(($extension == 'pdf'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        //$d2 = $variable[4];
                                                        //$d3 = $variable[5];

                                                    @endphp
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                @endif
                                                </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                                <div class="box-header">

                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_bipsia').click());">Biopsias</a></b></h3></div>
                                    <div class="col-md-6" style="color: #f2f2f2;"></div>
                                        <!-- tools box -->
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_bipsia">
                                                <i class="fa fa-plus"></i></button>
                                        </div>
                                        <!-- /. tools -->
                                </div>
                                <div class="box-body" style="padding: 5px;">
                                    @if(!is_null($protocolo) && $agenda->estado_cita=='4')
                                    <div class="form-group col-md-3" style="padding: 1px;">
                                        <a type="button" href="{{route('hc_video.mostrar_biopsias', ['id_protocolo' => $protocolo->id, 'agenda' => $id_agenda, 'ruta' => 1 ])}}" class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-open-file"> Biopsias</span>
                                        </a>
                                    </div>
                                    @endif

                                    <div class="table-responsive col-md-12">
                                        <table class="table table-bordered  dataTable" >
                                            <tbody style="font-size: 12px;">
                                                @php $count=0; @endphp
                                                @foreach($biopsias as $imagen)
                                                <div class="col-md-6" style='margin: 10px 0;' >
                                                @php
                                                    $explotar = explode( '.', $imagen->nombre);
                                                    $extension = end($explotar);
                                                @endphp
                                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                @elseif(($extension == 'pdf'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        //$d2 = $variable[4];
                                                        //$d3 = $variable[5];

                                                    @endphp
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                    </a>
                                                @endif
                                                </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>Receta Del Paciente</b></a></h3>
                    </div>
                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="receta">
                            <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="col-md-12">
                        <!--div class="col-md-7">
                            <h4>RECETA DEL PACIENTE</h4>
                        </div-->
                        <div class="col-md-5">
                        @if($hc_receta != "")
                            <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '1']) }}" target="_blank" type="button" class="btn btn-primary btn-sm">
                                <span class="glyphicon glyphicon-download-alt"> Imprimir</span>
                            </a>
                            <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-primary btn-sm" target="_blank">
                                <span class="glyphicon glyphicon-download-alt"> Imprimir Membretada</span>
                            </a>
                            <!--a href="{{url('medicina')}}/{{$agenda->id}}" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-list-alt"> Medicinas</span>
                            </a-->
                            <a href="{{route('medicina2.create2',['agenda' => $agenda->id, 'ruta' => '1'])}}" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-list-alt"> Crear Medicina</span>
                            </a>
                            <a href="{{url('medicina')}}/{{$agenda->id}}" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-list-alt"> Editar Medicinas</span>
                            </a>
                        @endif
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <input type="hidden" name="id_paciente" id="id_paciente" value="{{$agenda->id_paciente}}">
                        <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                  <label for="inputid" class="col-md-2 control-label">Medicina</label>
                                  <div class="col-md-6">
                                    <input value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre"  >
                                  </div>
                                  <div class="col-md-4">
                                        <button  type="button" id="limpiar" class="btn btn-primary">
                                                Agregar
                                        </button>
                                    </div>
                              </div>
                            </div>
                        </div>

                        <br>
                        <div class="col-md-2"><b>Alergias: </b></div><div class="col-md-10">@if($alergiasxpac->count()==0) <b>NO TIENE </b>@else @foreach($alergiasxpac as $ale)<span class="bg-red" style="padding: 3px;border-radius: 10px;">{{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;@endforeach @endif</div>

                        <div id="index">

                        </div>
                        <form id="final_receta" method="POST">
                            <input type="hidden" name="id_receta" value="{{$hc_receta->id}}">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <span><b>Rp</b></span>
                                    <div id="trp" style="border: solid 1px;min-height: 200px;"><?php echo $hc_receta->rp ?></div>
                                    <input type="hidden" name="rp" id="rp">
                                </div>
                                <div class="col-md-6" >
                                    <span><b>Prescripcion</b></span>
                                    <div id="tprescripcion" style="border: solid 1px;min-height: 200px;"><?php echo $hc_receta->prescripcion ?></div>
                                    <input type="hidden" name="prescripcion" id="prescripcion">
                                </div>
                                <div class="col-md-2 col-md-offset-10">
                                    <button type="button" class="btn btn-primary">
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if($agenda->estado_cita == 4)
        <div class="col-md-12" style="padding-right: 6px;">
            <div  class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b> Historial Recetas del Paciente</b></a></h3>
                    </div>
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="receta">
                            <i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="container-fluid">
                        <div class="row ">
                            <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
                                <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
                                    <div>
                                        <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
                                            <div class="parent" style="background-color: #ffffff">
                                                <div style=" margin-right: 30px;">
                                                  @if($hist_recetas != null)
                                                    @foreach($hist_recetas as $re_hist)
                                                        <div class="box collapsed-box " style="border: 1px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 0px;margin-top: 0px;">
                                                            <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                                                @if(!is_null($re_hist->fechaini))
                                                                    @php
                                                                      $dia =  Date('N',strtotime($re_hist->fechaini));
                                                                      $mes =  Date('n',strtotime($re_hist->fechaini));
                                                                    @endphp
                                                                    <b>
                                                                    <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title" >
                                                                    @if($dia == '1') Lunes
                                                                       @elseif($dia == '2') Martes
                                                                       @elseif($dia == '3') Miércoles
                                                                       @elseif($dia == '4') Jueves
                                                                       @elseif($dia == '5') Viernes
                                                                       @elseif($dia == '6') Sábado
                                                                       @elseif($dia == '7') Domingo
                                                                    @endif
                                                                      {{substr($re_hist->fechaini,8,2)}} de
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
                                                                      del {{substr($re_hist->fechaini,0,4)}}</span></b>
                                                                    @endif
                                                                    <div style="color: white">
                                                                      @if(!is_null($re_hist->id))
                                                                        {{$re_hist->id}}
                                                                      @endif
                                                                    </div>
                                                                    <div class="pull-right box-tools ">
                                                                      <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                                                                        <i class="fa fa-plus"></i>
                                                                      </button>
                                                                    </div>
                                                            </div>
                                                            <!--box-body-->
                                                            <div class="box-body">
                                                                <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                                                                    <div class="row">
                                                                        <div class="col-md-6" style="text-align: right;">
                                                                            <label style="font-family: 'Helvetica general';">Seguro:</label>
                                                                              <span>
                                                                                @if(!is_null($re_hist->nombre))
                                                                                  {{$re_hist->nombre}}
                                                                                @endif
                                                                              </span>
                                                                        </div>
                                                                        <br><br>
                                                                        <div class="col-md-9 col-sm-11 col-11" style="border: 2px solid #3c8dbc;margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;background-color:#ffffff;">
                                                                           <!--Contenedor Historial de Recetas-->
                                                                            <div  style="background-color: #3c8dbc; color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                                                                              <div class="box-title" style="background-color: #3c8dbc; margin-left: 10px">
                                                                                <div class="row">
                                                                                  <div class="col-md-8 col-sm-8 col-8" style="padding-top: 4px">
                                                                                    <span>Historial de Recetas</span>
                                                                                  </div>
                                                                                </div>
                                                                              </div>
                                                                            </div>
                                                                            <div class="contenedor2" id="receta{{$re_hist->id}}" style="padding-bottom: 20px; padding-right: 15px">
                                                                              <div class="col-md-12" style="padding-bottom: 15px;">
                                                                                <div class="row">
                                                                                  <div class="col-md-6">
                                                                                    <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                                                                    <div id="xtrp" style="border: solid 1px;min-height: 200px;border-radius:3px;margin-bottom: 20 px;border: 2px solid #3c8dbc; ">
                                                                                      @if(!is_null($re_hist->rp))
                                                                                        <p><?php echo $re_hist->rp ?>
                                                                                        </p>
                                                                                      @endif
                                                                                    </div>
                                                                                  </div>
                                                                                  <div class="col-md-6" >
                                                                                    <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                                                                    <div id="xtprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                                                                                      @if(!is_null($re_hist->prescripcion))
                                                                                        <p><?php echo $re_hist->prescripcion ?></p>
                                                                                      @endif
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                              </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="row">
                                                                              <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">              <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime', ['id' => $re_hist->id, 'tipo' => '2']) }}">
                                                                                <div class="col-md-12" style="text-align: center; ">
                                                                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                                                      <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                                                    </div>
                                                                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                                                                      <label style="font-size: 14px;color: white">Imprimir Membretada</label>
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                                </a>
                                                                              </div>
                                                                              <div class="col-md-12 col-sm-11 col-11" style="margin: 10px; padding: 0px;background-color: #3c8dbc;">
                                                                                <a target="_blank" class="btn btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime', ['id' => $re_hist->id, 'tipo' => '1']) }}">
                                                                                <div class="col-md-12" style="text-align: center;">
                                                                                  <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                                                    <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                                                      <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                                                    </div>
                                                                                    <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px;">
                                                                                      <label style="font-size: 14px;color: white">Imprimir</label>
                                                                                    </div>
                                                                                  </div>
                                                                                </div>
                                                                                </a>
                                                                              </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
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
            </div>
        </div>
        @endif



    </div>
</div>

<input type="hidden" id="contador_de procedimiento" value="0" >
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    $('#mprotocolo').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);
    });

    $('#mprotocolo_cpre_eco').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);
    });

    $('#eprotocolo').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

</script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
      })

    $('.select2').select2({
        tags: false
    });
    cargar_empresa();

    function cargar_empresa(){

        $.ajax({
          type: 'get',
          url:"{{ url('empresa/historiaclinica/cargar') }}/{{$hc_procedimiento->id}}/{{$agenda->id}}/"+$('#id_seguro').val(), 
          
          success: function(data){
                
            $('#div_empresa').empty().html(data);

          },

          error: function(data){
            
             
          }
        });  
    };
    tinymce.init({
        selector: '#trp',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",


        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
            });
        },
        @endif


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('trp');
                $("#rp").val(ed.getContent());
                cambiar_receta_2();

            });
          }
    });

    tinymce.init({
        selector: '#tprescripcion',
        inline: true,
        menubar: false,

        content_style: ".mce-content-body {font-size:14px;}",


        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
            });
        },
        @endif


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tprescripcion');
                $("#prescripcion").val(ed.getContent());
                cambiar_receta_2();

            });
          }
    });

    tinymce.init({
        selector: '#thallazgos',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        @if(is_null($protocolo))
        readonly: 1,
        @elseif($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thallazgos');
                $("#hallazgos").val(ed.getContent());
            });
        },
        @endif


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thallazgos');
                $("#hallazgos").val(ed.getContent());
                guardar_procedimiento();

            });
          }
      });

    tinymce.init({
        selector: '#tconclusion',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if(is_null($protocolo))
        readonly: 1,
        @elseif($agenda->estado_cita!='4')
        readonly: 1,
        @else
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tconclusion');
                $("#conclusion").val(ed.getContent());
            });
        },
        @endif


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tconclusion');
                $("#conclusion").val(ed.getContent());
                guardar_procedimiento();

            });
          }
      });



    $(document).ready(function() {

        index();
        Carga_proc('0');
        cargar_tabla();

        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');


    });

    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

    $('#formulario013').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    function editar_consulta(id,agenda){
        //alert(agenda);

        location.href ="{{url('historialclinico/visitas_ingreso')}}/"+id+"/"+agenda;//visita.crea_actualiza

    }

    function hc_protocolo(){
        /*$.ajax({
          type: 'post',
          url:"{{route('hc_procedimientos.actualizar_doctor_seguro')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_protocolo").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });*/
    }
    function hc_protocolo2(){
        $.ajax({
          type: 'post',
          url:"{{route('hc_procedimientos.actualizar_doctor_seguro')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_protocolo").serialize(),
          success: function(data){
            console.log(data);
            alert("Guardado Correctamente");
          },
          error: function(data){
             console.log(data);
             alert("Error en Sistema");
          }
        });
    }

    function guardar_procedimiento(){//FUNCION DAÑADA AHORA VA A GUARDAR DE UN SOLO BOTON
        /*$.ajax({
          type: 'post',
          url:"{{route('procedimiento.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm2").serialize(),
          success: function(data){
            //console.log(data);
            //var edad;
            //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            //edad = calcularEdad(fecha_nacimiento);
            //$('#edad').text( edad );
          },
          error: function(data){
             //console.log(data);
          }
        });*/
    }

    function guardar_procedimiento2(){

        var hallazgos  = $('#hallazgos').val();
        var conclusion = $('#conclusion').val();
        
        var err       = '';

        if(hallazgos==''){
            err = err + 'Ingrese hallazgos - ';
        }
        if(conclusion==''){
            err = err + 'Ingrese conclusion - ';
        }
        
        if(err==''){

            $.ajax({
              type: 'post',
              url:"{{route('procedimiento.paciente')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

              datatype: 'json',
              data: $("#frm2").serialize(),
              success: function(data){
                //console.log(data);
                //var edad;
                //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
                //edad = calcularEdad(fecha_nacimiento);
                //$('#edad').text( edad );
                alert("Guardado Correctamente");
              },
              error: function(data){
                //console.log(data);
                alert("Error en el Sistema");
              }
            });

        }else{
            alert(err);
        }
        
            
    }

    function Carga_proc(actualiza){
        var alerta = document.getElementById("contador_de procedimiento").value;
        s_variable = null;

        @if(!is_null($protocolo))
            s_variable = '{{$protocolo->id_procedimiento_completo}}';
        @endif
        /*if(alerta != "" && s_variable != alerta){
            var r = confirm("Esta seguro de cambiar el procedimiento?");
            if (r == false) {
                $('#proc_com').val(s_variable).trigger('change.select2');
            }
        }*/
        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm2").serialize(),
          success: function(data){
            //console.log(data.tecnica_quirurgica);
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                var tecnica = "";
            }
            //var edad;
            //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            //edad = calcularEdad(fecha_nacimiento);
            if(actualiza=='1'){
                anterior1=  $('#hallazgos').val();
                anterior2=  tinyMCE.get('thallazgos').getContent();
                $('#hallazgos').val(anterior1+tecnica);
                tinyMCE.get('thallazgos').setContent(anterior2+tecnica);

            }
            if(data.estado_anestesia=='0'){
                $('#id_anestesiologo').prop( "disabled", true );
            }else{
                $('#id_anestesiologo').prop( "disabled", false );
            }

            if(actualiza=='1'){
                guardar_procedimiento();
            }


            //tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, data )
          },
          error: function(data){
             //console.log(data);
          }
        });

        //guardar();
    }

    $("#cie10").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);

                }
            })
        },
        minLength: 2,
    } );

    $("#cie10").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);

                }

            },
            error: function(data){

                }
        })
    });

    $('#bagregar').click( function(){
        @if(!is_null($protocolo))
        if($('#pre_def').val()!=''){
             if($('#ing_egr').val()!=''){
                guardar_cie10_PRO();
                $('#pre_def').val('');
                $('#ing_egr').val('');
            }else{
                alert("Seleccione Ingreso o Egreso");
            }
        }else{
            alert("Seleccione Presuntivo o Definitivo");
        }
        @endif
        $('#codigo').val('');
        $('#cie10').val('');
    });

    function guardar_cie10_PRO(){
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$protocolo->hcid}}, 'hc_id_procedimiento': {{$protocolo->id_hc_procedimientos}}, 'in_eg': $("#ing_egr").val() },
            success: function(data){
                console.log(data);


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell5 = row.insertCell(3);
                cell5.innerHTML = data.ingreso_egreso;
                var cell4 = row.insertCell(4);
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                //aqui va para la receta
                anterior = tinyMCE.get('trp').getContent();
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                tinyMCE.get('trp').setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                $('#rp').val(tinyMCE.get('trp').getContent());
                cambiar_receta_2();



            },
            error: function(data){

                }
        })
    }

    function eliminar(id_h){


        var i = document.getElementById('tdiag'+id_h).rowIndex;

        document.getElementById("tdiagnostico").deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });
    }

    function cargar_tabla(){
        $.ajax({
                url:"{{route('epicrisis.cargar',['id' => $protocolo->id_hc_procedimientos])}}",
                dataType: "json",
                type: 'get',
                success: function(data){

                    var table = document.getElementById("tdiagnostico");

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;

                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.descripcion;
                        var cell5 = row.insertCell(3);
                        cell5.innerHTML = value.ingreso_egreso;
                        var cell4 = row.insertCell(4);
                        cell4.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });

                }
            })
    }
</script>

<script>
    /*$("#limpiar").click( function(){
        $('#nombre_generico').val('');
    });*/
    $('#moxigeno').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);

    });
    $('#mproductos').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);

    });

    $("#nombre_generico").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('receta.buscar_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);
                }
            })
        },
        minLength: 2,
    } );

    $("#nombre_generico").change( function(){
        var variable1;
        var variable2;
        $.ajax({
            type: 'post',
            url:"{{route('receta.buscar_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre_generico"),
            success: function(data){
                if(data!='0'){
                    //console.log(data);
                    if(data.dieta == 1 ){
                        //anterior = $('#prescripcion').val();
                        anterior = tinyMCE.get('tprescripcion').getContent();
                        //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                        tinyMCE.get('tprescripcion').setContent(anterior+ data.value +': \n' +data.dosis);
                        $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                        cambiar_receta_2();
                    }
                    if(data.dieta == 0){
                        Crear_detalle(data);
                    }
                    $('#nombre_generico').val('');
                }

            },
            error: function(data){
                    //console.log(data);
                }
        })
    });
    $("#prescripcion").change( function(){
       cambiar_receta_2();
    });
    $("#rp").change( function(){
       cambiar_receta_2();
    });


    function cambiar_receta_2(){

        $.ajax({
            type: 'post',
            url:"{{route('receta.update_receta_2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#final_receta").serialize(),
            success: function(data){

            },
            error: function(data){
                    //console.log(data);
                }
        })
    }

    @if(!is_null($hc_receta))

    function Crear_detalle(med){
        var js_cedula = document.getElementById("id_paciente").value;
        //alert(js_cedula);
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/crear_detalle')}}"+"/"+{{$hc_receta->id}}+"/"+med.id+"/"+js_cedula, //receta.crear_detalle
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
            if(data == 1){
                console.log(med);
                if(med.genericos == null){
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp').getContent();
                    //$('#rp').empty().html(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                    //para receta
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;

                    keywords.forEach(function(element) {
                        //En caso de existir se asigna la posición en pos
                        pos = anterior2.search(element.toString());
                        //Si existe
                        if(pos!=-1){
                            resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                        }

                    });

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp').setContent(anterior2.substr(0, pos) +'\n'+ med.value +':  ' +med.cantidad +anterior2.substr(pos));
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }
                    //fin de receta
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion').getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                    tinyMCE.get('tprescripcion').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                    $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                    cambiar_receta_2();
                }else{
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp').getContent();
                    //codigo cie10 de posicion de receta
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;

                    keywords.forEach(function(element) {
                        //En caso de existir se asigna la posición en pos
                        pos = anterior2.search(element.toString());
                        //Si existe
                        if(pos!=-1){
                            resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                        }

                    });

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        tinyMCE.get('trp').setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp').setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
                        $('#rp').val(tinyMCE.get('trp').getContent());
                    }
                    //fin de receta cie10
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion').getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
                    tinyMCE.get('tprescripcion').setContent(anterior +'\n'+ med.value +':  ' +med.dosis);
                    $('#prescripcion').val(tinyMCE.get('tprescripcion').getContent());
                    cambiar_receta_2();
                }

            }else{
                $('#index').empty().html(data);
            }
            console.log(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function index(){

        $.ajax({
          type: 'get',
          url:"{{route('receta.index_detalle',['receta' => $hc_receta->id])}}",
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function det_delete(id) {
        $.ajax({
          type: 'get',
          url:"{{url('receta_detalle/eliminar_detalle')}}/"+{{$hc_receta->id}}+"/"+id,
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          //data: $("#frm").serialize(),
          success: function(data){
            $('#index').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    @endif


    function guardar3(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    function guardar2(){
        $.ajax({
          type: 'post',
          url:"{{route('receta.guardar2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#form2").serialize(),
          success: function(data){
            //console.log(data);
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

    //var vartiempo = setInterval(function(){ location.reload(); }, 7201000);

    @if($agenda->estado_cita =='4' && $agenda->proc_consul == '1')
        function agregar_evolucion(){
            var condicion = $('#proc_com').val();
            if(condicion != ""){
                window.location.href = "{{route('evolucion.crear_evolucion_procedimiento', ['id_agenda' => $agenda->id, 'hc_id_procedimiento' => $protocolo->id_hc_procedimientos])}}";
            }else{
                window.location.href = "{{route('evolucion.crear_evolucion_procedimiento', ['id_agenda' => $agenda->id, 'hc_id_procedimiento' => $protocolo->id_hc_procedimientos])}}";
            }

        }
    @endif


    function carga_cpre_eco(){
        $.ajax({
          type: 'get',
          url:"{{ route('protocolo_cpre_eco.modal_cpre_eco', ['hcid' => $protocolo->hcid]) }}",


          datatype: 'json',

          success: function(data_cpre){
            $('#div_cpre_eco').empty();
            $('#div_cpre_eco').html(data_cpre);
            $('#b_cpre_eco').attr('disabled', 'disabled');
          },
          error: function(data_cpre){
             //console.log(data);
          }
        });
    }

    @php $dr_training = Auth::user(); @endphp
        @if($dr_training->training)

            $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

                $.ajax({
                  type: 'get',
                  url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '1']) }}",


                  datatype: 'json',

                  success: function(data_cpre){
                    $('#train').removeClass('text-muted');
                    $('#train').addClass('text-red');

                  },
                  error: function(data_cpre){
                     //console.log(data);
                  }
                });

            });

            $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){

                $.ajax({
                  type: 'get',
                  url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '0']) }}",


                  datatype: 'json',

                  success: function(data_cpre){
                    $('#train').removeClass('text-red');
                    $('#train').addClass('text-muted');

                  },
                  error: function(data_cpre){
                     //console.log(data);
                  }
                });

            });

        @endif

</script>
</section>

@include('sweet::alert')
@endsection
