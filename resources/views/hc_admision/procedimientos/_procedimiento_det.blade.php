@extends('hc_admision.historia.base2')

@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
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

<div class="modal fade" id="mprotocolo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>

<div class="modal fade" id="eprotocolo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>
<div class="modal fade" id="asiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">
      </div>
    </div>
</div>
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $procedimiento->historia->paciente->apellido1}} @if($procedimiento->historia->paciente->apellido2 != "(N/A)"){{ $procedimiento->historia->paciente->apellido2}}@endif {{ $procedimiento->historia->paciente->nombre1}} @if($procedimiento->historia->paciente->nombre2 != "(N/A)"){{ $procedimiento->historia->paciente->nombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$procedimiento->historia->paciente->id}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="col-md-12">
                        @if(!is_null($epicrisis))
                        <a target="_blank" href="{{route('epicrisis.imprimir_stream',['epicrisis' => $epicrisis->id])}}">
                            <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file"> Epicrisis</span>
                            </button>
                        </a>
                        @endif

                        <a href="{{route('hc_reporte.seleccion_descargar', ['id_protocolo' => $protocolo->id])}}" data-toggle="modal" data-target="#foto">
                            <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file">Descargar Estudio</span>
                            </button>
                        </a>

                        <a href="{{ route('evolucion.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                            <span class="glyphicon glyphicon-file"> Evolución</span>
                        </a>

                        <!--a target="_blank" href="{{route('evolucion.imprimir_stream', ['id' => $protocolo->id_hc_procedimientos ])}}">
                            <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-file"> Evolución</span>
                            </button>
                        </a-->

                        @if(!is_null($hc_receta))
                        <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-success btn-sm" target="_blank">
                            <span class="glyphicon glyphicon-download-alt"> Receta</span>
                        </a>
                        @endif


                        <a href="{{ route('protocolo.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                            <span class="glyphicon glyphicon-download-alt"> Protocolo</span>
                        </a>
                    </div>
                </div>
            </div>
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
                        <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: @if($value->proc_consul=='1') @if($value->hc_id_procedimiento == $protocolo->id_hc_procedimientos) orange 1.5px @else black 1px @endif @else black 1px @endif solid; margin: 5px 0;padding-left: 0px;" >
                            <table class="table table-striped table-bordered table-hover">

                                <tr>
                                    <td><b>Fecha: </b></td>
                                    @php $dia =  Date('N',strtotime($value->fechaini)); $mes =  Date('n',strtotime($value->fechaini)); @endphp
                                    <td style="color: blue;"><b>
                                        @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($value->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($value->fechaini,0,4)}}</b>
                                    </td>
                                    <td><b>Hora: </b></td>
                                    <td style="color: blue;"><b>{{substr($value->fechaini,10,10)}}</b></td>

                                </tr>
                                <tr>

                                    <td><b>Motivo: </b></td>
                                    <td>{{$value->motivo}}</td>
                                    <td style="color: Cornsilk;">{{$value->id}}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Evolución: </b></td>
                                    <td colspan="2"><b>@if($value->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($value->espid)->nombre}} @elseif($value->proc_consul=='1')PROCEDIMIENTO: @php
                                    $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::where('id_hc',$value->hcid)->first();
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
                                    @endif
                                @endif
                            </table>
                        </div>

                        @endforeach
                            @if($procedimiento->historia->historia_clinica!=null)
                                <div class="table-responsive col-md-12 col-xs-12" style="font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" >
                                    <b>HISTORIA CLÍNICA SCI</b><br>
                                <textarea style="width: 100%;height: 400px;">
                                    {{$procedimiento->historia->historia_clinica}}
                                </textarea>
                                </div>
                            @endif
                        @else
                            @if($procedimiento->historia->historia_clinica==null)
                            <h4 align="center" style="background: #e6fff7">SIN INFORMACIÓN PREVIA REGISTRADA EN EL SISTEMA</h4>
                            @else
                            <div class="table-responsive col-md-12 col-xs-12" style="font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" >
                                <b>HISTORIA CLÍNICA SCI</b><br>
                                <textarea style="width: 100%;height: 400px;">
                                    {{$procedimiento->historia->historia_clinica}}
                                </textarea>
                            </div>
                            @endif
                        @endif
                    </div>
                    @php
                        $agenda = DB::table('agenda')->where('id',$procedimiento->historia->id_agenda)->first();
                        $dia =  Date('N',strtotime($agenda->fechaini));
                        $mes =  Date('n',strtotime($agenda->fechaini));
                    @endphp

                </div>
            </div>
        </div>

        {{--<div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#equipos_usados').click());">Equipos Usados</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="equipos_usados">
                                <i class="fa fa-plus"></i></button>
                        </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div class="col-md-12" style="padding: 1px;">
                    @if($equipos != '[]')
                        <center>
                            <table style="text-align: center;">
                                <tr>
                                    <td style="width: 25%"><b>SERIE</b></td>
                                    <td style="width: 25%"><b>NOMBRE DEL EQUIPO</b></td>
                                    <td style="width: 25%"><b>FECHA</b></td>
                                </tr>
                                    @foreach($equipos as $value)
                                    <tr>
                                    <td><span>@if(!is_null($value)){{$value->serie}}@endif</span></td>
                                    <td><span>@if(!is_null($value)){{$value->nombre}}@endif</span></td>
                                    <td><span>@if(!is_null($value)){{$value->created_at}}@endif</span></td>
                                    </tr>
                                    @endforeach
                            </table>
                        </center>
                    @else
                        <label>NO SE ENCONTRARON EQUIPOS UTILIZADOS</label>
                    @endif
                    </div>
                </div>
            </div>
        </div>--}}

        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title">
                            <b><a href="javascript:void($('#ima_historico').click());">Insumos Utilizados</a></b>
                        </h3>
                    </div>
                    <div class="col-md-6" style="color: #f2f2f2;">
                    </div>
                        <!-- tools box -->
                    <div class="pull-right box-tools">
                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="ima_historico">
                                <i class="fa fa-plus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    
                        <div class="col-md-12" style="font-weight: bolder; font-size: 1.5em;">
                            Equipos Usados
                        </div>
                        <div class="col-md-12" style="padding: 1px; border-bottom: 1px solid black;">
                        @if($equipos != '[]')
                            <center>
                                <table style="text-align: center;">
                                    <tr>
                                        <td style="width: 25%"><b>SERIE</b></td>
                                        <td style="width: 25%"><b>NOMBRE DEL EQUIPO</b></td>
                                        <td style="width: 25%"><b>FECHA</b></td>
                                    </tr>
                                        @foreach($equipos as $value)
                                        <tr>
                                        <td><span>@if(!is_null($value)){{$value->serie}}@endif</span></td>
                                        <td><span>@if(!is_null($value)){{$value->nombre}}@endif</span></td>
                                        <td><span>@if(!is_null($value)){{$value->created_at}}@endif</span></td>
                                        </tr>
                                        @endforeach
                                </table>
                            </center>
                        @else
                            <label>NO SE ENCONTRARON EQUIPOS UTILIZADOS</label>
                        @endif
                        </div>

                    
                    <div style="margin-top: 20px;" class="col-md-12">
                        <span><b>Observaciones:</b></span>
                        <p>{{$procedimiento->historia->observaciones_enfermeria}}</p>
                    </div>
                     @php
                      $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
                      $mas = true;
                      $texto = "";
                      foreach($adicionales as $value2)
                      {
                        if($mas == true){
                         $texto = $texto.$value2->procedimiento->nombre;
                         $mas = false;
                        }
                        else{
                          $texto = $texto.' + '.  $value2->procedimiento->nombre;
                        }
                      }
                      //echo $texto;
                    @endphp
                    <div class="col-md-12">
                    @if($insumos != '[]')
                        <center>
                                @include('hc_admision.procedimientos.details2')
                        </center>

                        <div class="col-md-12" style="margin-top:20px">
                            <center style="margin-bottom: 20px;">
                                @php
                                    $comparativo= Sis_medico\Planilla::where('id_hc_procedimiento',$procedimiento->id)->first();
                                @endphp
                                @if(!is_null($comparativo))
                                    <a class="btn btn-primary" data-remote="{{route('venta.planilla.detalle',['id' => $procedimiento->id])}}" data-toggle="modal" data-target="#md_planilla" >Planilla</a>

                                    <a href="{{route('productos.edit_comparar',['id'=>$comparativo->id])}}" target="_blank" class="btn btn-primary"> Planilla Verificar </a>
                                    <!--  <button type="button" onclick="return alert('Asiento Generado Correctamente');" class="btn btn-warning" > Generar Asiento</button> -->
                                    @if($comparativo->id_asiento_cabecera==null)
                                    <a class="btn btn-primary" data-toggle="modal" data-target="#asiento" href="{{ route('productos.modal.asiento', ['id' => $comparativo->id]) }}">
                                        Generar Asiento Insumos
                                    </a>
                                    @else
                                    <a class="btn btn-success" href="{{route('librodiario.revisar',['id'=>$comparativo->id_asiento_cabecera])}}" target="_blank">
                                        Asiento Insumos No. {{ $comparativo->id_asiento_cabecera }}
                                    </a>
                                    @endif
                                    @if($comparativo->id_asiento_medico==null)
                                    @else
                                    <a class="btn btn-success" >
                                        Asiento H. Médicos No. {{ $comparativo->id_asiento_cabecera }}
                                    </a>
                                    @endif
                                    @if($comparativo->id_asiento_anestesia==null)
                                    @else
                                    <a class="btn btn-success" >
                                        Asiento H. Anestesiologicos No. {{ $comparativo->id_asiento_cabecera }}
                                    </a>
                                    @endif
                                @else
                                    <label style="font-size: 17px;" class="label label-danger">No tiene una Planilla Asociada</label>
                                @endif
                            </center>
                        </div>
                       
                    @else
                        <label>NO SE ENCONTRARON INSUMOS UTILIZADOS</label>
                    @endif
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

        @php

            $pentax = DB::table('pentax')->where('hcid',$procedimiento->historia->hcid)->first();
            $procedimientos_pentax = null;
            if(!is_null($pentax)){
                $procedimientos_pentax = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get();
            }
        @endphp

        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary" >
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title"><b>Procedimiento</b></h3>
                    </div>

                    <div class="col-md-6" style="color: #f2f2f2;">{{$protocolo->id}}</div>


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
                                <span>@if(!is_null($pentax))@if($pentax->estado_pentax=='0') EN ESPERA @elseif($pentax->estado_pentax=='1') PREPARACIÓN @elseif($pentax->estado_pentax=='2') EN PROCEDIMIENTO @elseif($pentax->estado_pentax=='3') RECUPERACIÓN @elseif($pentax->estado_pentax=='4') ALTA @elseif($pentax->estado_pentax=='5') SUSPENDIDO @endif @else "NO ASISTE" @endif</span>
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
                            @php
                              $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
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
                              echo $texto;
                            @endphp
                            <div class="col-md-12" style="padding: 1px;">
                                <label for="proc_com" class="control-label col-md-4" style="padding-left: 0px;">Procedimiento Realizado</label>
                                <input class="col-md-8" type="text" name="procedimiento" readonly value="@if($procedimiento->procedimiento_completo!=null){{$procedimiento->procedimiento_completo->nombre_general}}@elseif(!is_null($texto)) {{$texto}} @endif">
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="id_doctor2" class="control-label">Asistente 1</label>
                                <input type="text" name="asistente1" readonly value="@if($procedimiento->historia->doctor_2!=null){{$procedimiento->historia->doctor_2->apellido1}} {{$procedimiento->historia->doctor_2->nombre1}}@endif">
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="id_doctor3" class="control-label">Asistente 2</label>
                                <input type="text" name="asistemte2" readonly value="@if($procedimiento->historia->doctor_3!=null){{$procedimiento->historia->doctor_3->apellido1}} {{$procedimiento->historia->doctor_3->nombre1}}@endif">
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="motivo" class="control-label">Motivo</label>
                                <textarea name="motivo" style="width: 100%;" rows="1" readonly onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->motivo}}@endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="thallazgos" class="control-label">Hallazgos</label>
                                <div id="thallazgos" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->hallazgos ?>@endif</div>
                                <input type="hidden" name="hallazgos" id="hallazgos">
                            </div>


                        </form>
                    </div>
                    <div class="col-md-6">
                        <form id="hc_protocolo">

                        <div class="col-md-12" style="padding: 1px;">
                            <label for="id_doctor_examinador" class="control-label col-md-4" style="padding-left: 0px;">Medico Examinador</label>
                            <input class="col-md-8" type="text" name="examinador" readonly value="@if($procedimiento->doctor!=null){{$procedimiento->doctor->apellido1}} {{$procedimiento->doctor->nombre1}}@else{{$procedimiento->historia->doctor_1->apellido1}} {{$procedimiento->historia->doctor_1->nombre1}}@endif">
                        </div>
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="id_seguro" class="control-label col-md-4" style="padding-left: 0px;">Seguro</label>
                            <input class="col-md-8" type="text" name="seguro" readonly value="@if($procedimiento->seguro!=null){{$procedimiento->seguro->nombre}}@else{{$procedimiento->historia->seguro->nombre}}@endif">
                        </div>
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="observaciones" class="control-label">Observaciones</label>
                            <textarea readonly class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;" rows="3" onchange="hc_protocolo();">{{$procedimiento->observaciones}}</textarea>
                        </div>
                        </form>

                        <form id="frm_cie">
                            <input type="hidden" name="codigo" id="codigo">


                            <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px;"><b>Diagnóstico</b></label>
                            <!--div class="form-group col-md-12" style="padding: 1px;">
                                <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>
                            </div>
                            <div class="form-group col-md-3" style="padding: 1px;">
                                <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                    <option value="">Seleccione ...</option>
                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                </select>
                            </div-->



                            <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                                </table>
                            </div>

                             <div class="col-md-12" style="padding: 1px;">
                                <label for="tconclusion" class="control-label">Conclusión</label>
                                <div id="tconclusion" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->conclusion ?>@endif</div>
                                <input type="hidden" name="conclusion" id="conclusion">
                            </div>


                            <div class="col-md-12" style="padding: 1px;">
                                <label for="complicacion" class="control-label">Complicaciones</label>
                                <textarea readonly name="complicacion" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->complicacion}} @endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="estado_paciente" class="control-label">Estado del Paciente al Terminar</label>
                                <textarea readonly name="estado_paciente" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->estado_paciente}} @endif</textarea>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="plan" class="control-label">Plan Terapeutico</label>
                                <textarea readonly name="plan" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->plan}} @endif</textarea>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
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
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
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
                                        $d2 = $variable[4];
                                        

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

                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered  dataTable" >
                            <tbody style="font-size: 12px;">
                                @php $count=0; @endphp
                                @foreach($documentos as $imagen)
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
                                        $d2 = $variable[4];
                                        

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
                                        $d2 = $variable[4];
                                        

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
                                        $d2 = $variable[4];
                                        

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

        @if($agenda->estado_cita == 4)
            <div class="col-md-12" style="padding-right: 6px;">
                <div  class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                    <div class="box-header">
                        <div class="col-md-4">
                            <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>Recetas del Paciente</b></a></h3>
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
                                                        <div class="box collapsed-box " style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
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


<div class="modal fade" id="md_planilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">

    $('#asiento').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

    $('.select2').select2({
        tags: false
    });
    var table = $('#examples2').DataTable({
      dom: 'lBrtip',
      paging: false,
      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'Insumos',
          exportOptions: {
                    columns: [ 0, 1, 2, 3,4,5,6,7,8,9,10,11,12,13,14]
          }
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
          title: 'Insumos',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      "order": [
        [1, 'asc']
      ],
    });
    var table2 = $('#examples22').DataTable({
      dom: 'lBrtip',
      paging: false,
      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'Insumos',
          exportOptions: {
                    columns: [ 0, 1, 2, 3,4,5,6,7,8,9,10,11,12,13,14]
          }
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
          title: 'Insumos',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      "order": [
        [1, 'asc']
      ],
    });
    tinymce.init({
        selector: '#thallazgos',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        readonly: 1,



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

        readonly: 1,

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

    function editar_consulta(id,agenda){
        //alert(agenda);

        location.href ="{{url('historialclinico/visitas_ingreso')}}/"+id+"/"+agenda;//visita.crea_actualiza

    }



    function guardar_procedimiento(){
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
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function Carga_proc(actualiza){

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
                $('#hallazgos').val(tecnica);
                tinyMCE.activeEditor.setContent(tecnica);
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
            guardar_cie10_PRO();
            $('#pre_def').val('');
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
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$protocolo->hcid}}, 'hc_id_procedimiento': {{$protocolo->id_hc_procedimientos}}, 'in_eg': null },
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


                    });

                }
            })
    }
</script>

<script>
    $("#limpiar").click( function(){
        $('#nombre_generico').val('');
    });

    $('#forma_pago').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
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
                        anterior = $('#prescripcion').val();
                        $('#prescripcion').empty().html(anterior
                            + data.value +': \n' +data.dosis);
                        cambiar_receta_2();
                    }
                    if(data.dieta == 0){
                        Crear_detalle(data);
                    }
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
                    anterior2 = $('#rp').val();
                    $('#rp').empty().html(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                    anterior = $('#prescripcion').val();
                    $('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                    cambiar_receta_2();
                }else{
                    anterior2 = $('#rp').val();
                    $('#rp').empty().html(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                    anterior = $('#prescripcion').val();
                    $('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
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

    var vartiempo = setInterval(function(){ location.reload(); }, 7201000)
</script>
</section>

@include('sweet::alert')
@endsection
