
@extends('hc_admision.historia.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">

    @media screen and (max-width: 1500px) {

        label#peri.control-label {
            font-size: 13px;
        }

    }

    .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
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

        .mce-edit-focus,
        .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
        }

        .select2-selection--multiple{
            background-color: #ffe0cc !important;
        }

        /*Nuevos css historial de recetas*/
        .parent{
           overflow-y:scroll;
           height: 600px;
        }

        .parent::-webkit-scrollbar {
            width: 8px;
        } /* this targets the default scrollbar (compulsory) */

        .parent::-webkit-scrollbar-thumb {
            background: #3c8dbc;
            border-radius: 10px;
        }

        .parent::-webkit-scrollbar-track {
            width: 10px;
            background-color: #3c8dbc;
            box-shadow: inset 0px 0px 0px 3px #3c8dbc;
        }

        .contenedor2{
            padding-left: 15px;
            padding-right: 60px;
            padding-top: 20px;
            padding-bottom: 0px;
            background-color: #FFFFFF;
            margin-left: 0px;
        }
        /* Fin Nuevos css historial de recetas*/

</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<div class="modal fade" id="foto2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

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

<div class="modal fade" id="mprotocolo_cpre_eco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

<div class="modal fade" id="formulario013" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>

<div class="modal fade" id="archivos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document" style="width: 80%;">
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
                                <tr >
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$agenda->id_paciente}}</td>
                                    <td style="text-align:right;"><b>Cortesias en el día</b></td><td style="text-align:right; @if($cant_cortesias>1) color:red; @endif">{{$cant_cortesias}}</td>
                                    <td style="text-align: right;background: #e6ffff;"><b>@if($agenda->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($agenda->espid)->nombre}} @elseif($agenda->proc_consul=='1')PROCEDIMIENTO @endif</b></td>
                                </tr>
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
        <!--div class="col-md-4">
            <a type="button" href="{{route('visitas.index', ['id_paciente' => $agenda->id_paciente, 'id_agenda' => $agenda->id ])}}" class="btn btn-success btn-sm">
                Visitas
            </a>
            <a type="button" href="{{route('estudio.lista', ['id' => $agenda->id ])}}" class="btn btn-success btn-sm">
                <span> Procedimientos</span>
            </a>
            <a type="button" href="{{ route('agenda.agenda2') }}" class="btn btn-primary btn-sm">
                <span class="glyphicon glyphicon-calendar"> Agenda</span>
            </a>
        </div-->
        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary" style="margin-bottom: 5px;" >
                <div class="box-header" style="display: flex; gap: 10px;">
                    <div>
                        <h3 class="box-title"><a href="javascript:void($('#fili').click());"><b>Filiación</b></a></h3>
                    </div>

                    @php
                        $cant_labs = DB::table('examen_orden')->where('id_paciente',$agenda->id_paciente)->where('estado','1')->count();
                    @endphp

                    <div class="col-md-2">
                        @if($cant_labs > 0)
                        <a id="nuevo_proc" href="{{route('orden.index_doctor', ['id' => $agenda->id_paciente, 'agenda' => $agenda->id])}}" class="btn btn-success btn-sm">
                            <i class="ionicons ion-ios-flask"></i>
                            <span>Laboratorio</span>
                        </a>
                        @else
                        <a id="nuevo_proc" class="btn btn-warning btn-sm">
                            <i class="ionicons ion-ios-flask"></i>
                            <span>No Tiene Exámenes</span>
                        </a>
                        @endif
                    </div>

                    <div class="col-md-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}" class="btn btn-success btn-sm" >
                        <span></span> Datos Agenda
                        </a>
                    </div>
                    <div class="col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{route('paciente.historial_ordenes', ['id' => $agenda->id_paciente])}}" class="btn btn-success btn-sm" >
                        <span></span> Ordenes de Procedimiento
                        </a>
                    </div>
		    @if($agenda->proc_consul =='0')
                    <div class="col-md-2">
                        <a target="_blank" href="{{ route('formato.anamnesis_crear', ['hcid' => $agenda->hcid]) }}">
                            <button class="btn btn-success btn-sm">ANAMNESIS</button>
                        </a>
                    </div>

                    @endif
                    @if($agenda->estado_cita=='4')
                    <div class="col-md-2">
                        <a href="{{route('orden_012.carga_012',['hcid' => $agenda->hcid])}}">
                            <button class="btn btn-success btn-sm">Orden 012</button>
                        </a>
                    </div>
		    

                    <!--div class="col-md-2">
                        <a href="{{route('orden_proc.crear_editar',['hcid' => $agenda->hcid])}}">
                            <button class="btn btn-success btn-sm">Orden De Procedimiento</button>
                        </a>
                    </div-->
                    @endif
                    <!--div class="col-md-2">
                        <a href="{{url('tecnicas')}}/{{$agenda->id}}">
                            <button class="btn btn-success btn-sm">Maestro de Procedimientos</button>
                        </a>
                    </div-->
                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                            <i class="fa fa-minus"></i></button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <form id="frm">

                        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">

                            <input type="hidden" name="snombre" id="snombre" value="{{$agenda->snombre}}">

                            <div class="col-md-2 {{ $errors->has('cortesia') ? ' has-error' : '' }}" style="padding: 1px;">
                                <label for="cortesia" class="control-label" style="color: #00e6e6;">Cortesia</label>
                                <select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);" style="background-color: #ccffcc;">
                                    <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                    <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                </select>
                            </div>

                            <input id ="id_paciente_clinica" type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="sexo" class="control-label">Sexo</label>
                                <select name="sexo" id="sexo" onchange="guardar();" class="form-control input-sm" >
                                   <option @if($agenda->sexo == 1) selected @endif value="1">Masculino</option>
                                   <option @if($agenda->sexo == 2) selected @endif value="2">Femenino</option>
                                </select>
                            </div>

                            <div class="col-md-4" style="padding: 1px;">
                                <label for="fecha_nacimiento" class="control-label">F.nacimiento</label>
                                <input id="fecha_nacimiento" type="date" onchange="guardar();" name="fecha_nacimiento" value='{{$agenda->fecha_nacimiento}}' class="form-control input-sm" >
                            </div>

                            <div class="col-md-2" style="padding: 1px;">
                                <label for="edad" class="control-label">Edad</label>
                                <input id="edad" type="text" name="edad" value='' class="form-control input-sm" readonly>
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="estadocivil" class="control-label">Estado Civil</label>
                                <select class="form-control input-sm"  name="estadocivil" onchange="guardar();">
                                   <option @if($agenda->estadocivil == 1) selected @endif value="1">Soltero</option>
                                   <option @if($agenda->estadocivil == 2) selected @endif value="2">Casado</option>
                                   <option @if($agenda->estadocivil == 3) selected @endif value="3">Viudo</option>
                                   <option @if($agenda->estadocivil == 4) selected @endif value="4">Divorciado</option>
                                   <option @if($agenda->estadocivil == 5) selected @endif value="5">Unión Libre</option>
                                   <option @if($agenda->estadocivil == 6) selected @endif value="6">Unión de Hecho</option>
                                </select>
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="ocupacion" class="control-label">Ocupación</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="ocupacion" value="{{$agenda->ocupacion}}" maxlength="50">
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="trabajo" class="control-label">Trabajo</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="trabajo" value="{{$agenda->trabajo}}" maxlength="100">
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="id_seguro" class="control-label">Seguro</label>
                                <?php
/* <!--select id="id_seguro" name="id_seguro" class="form-control input-sm" onchange="crear_select();guardar();">
@foreach($seguros as $seguro)
<option @if(is_null($protocolo))@if($agenda->id_seguro==$seguro->id){{'selected '}}@endif @else @if($protocolo->id_seguro==$seguro->id){{'selected '}}@endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
@endforeach
</select--> */

?>
                                <input class="form-control input-sm" type="text" name="seguro" value=@if(is_null($agenda->hcid))"{{$agenda->snombre}}"@else"{{$agenda->hsnombre}}"@endif readonly>
                            </div>

                            <!--div class="col-md-3 sub_dv" style="padding: 1px;">
                                <label for="id_sub-seguro" class="control-label">Sub-Seguro</label>
                                <select id="id_sub-seguro" name="sub-seguro" class="form-control input-sm" onchange="guardar();  ">
                                </select>
                            </div-->

                            <div class="col-md-12 has-warning" style="padding: 1px;">
                                <label for="observacion" class="control-label">Observación</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="observacion" value="{{$agenda->observacion}}" autocomplete="off" placeholder="INGRESE OBSERVACIÓN MÉDICA DEL PACIENTE" style="background-color: #ffffb3;">
                            </div>
                            <div class="col-md-12 has-warning" style="padding: 1px;">
                                <label for="observacion_admin" class="control-label">Observación Administrativa</label>
                                <textarea id="observacion_admin" class="form-control input-sm" onblur="guardar_admin();" name="observacion_admin" placeholder="INGRESE OBSERVACIÓN ADMINISTRATIVA DEL PACIENTE" rows="4">@if($paciente_observacion!=null) {{ $paciente_observacion->observacion }} @endif</textarea>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-left: 1px;padding-right: 1px;">

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="ciudad" class="control-label">Ciudad Procedencia</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="ciudad" value="{{$agenda->ciudad}}" maxlength="50">
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="cortesia" class="control-label">Ciudad Nacimiento</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="lugar_nacimiento" value="{{$agenda->lugar_nacimiento}}" maxlength="50">
                            </div>

                            <div class="col-md-6" style="padding: 1px;">
                                <label for="direccion" class="control-label">Dirección Domicilio</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="direccion" value="{{$agenda->direccion}}" maxlength="200" autocomplete="off">
                            </div>

                            <div class="col-md-3 div_tel" style="padding: 1px;">
                                <label for="telefono1" class="control-label">Teléfono</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="telefono1" value="{{$agenda->telefono1}}" maxlength="30" autocomplete="off">
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="" class="control-label">Celular</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="telefono2" value="{{$agenda->telefono2}}" maxlength="30" autocomplete="off">
                            </div>

                            <div class="col-md-6 div_ema" style="padding: 1px;">
                                <label for="mail" class="control-label">Mail</label>
                                <input class="form-control input-sm" onchange="guardar();" type="email" name="mail" value="{{$mail}}" autocomplete="off">
                            </div>

                            <div class="col-md-6" style="padding: 1px;">
                                <label for="referido" class="control-label">Refererencia</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="referido" value="{{$agenda->referido}}">
                            </div>

                        </div>

                        <div class="col-md-6" style="padding: 1px;">

                            <div class="col-md-6" style="padding: 1px;">
                                <label for="gruposanguineo" class="control-label" >Grupo Sanguineo</label>
                                <select id="gruposanguineo" class="form-control" name="gruposanguineo" onchange="guardar();">
                                    <option value="">Seleccionar ..</option>
                                    <option @if($agenda->gruposanguineo == "AB-") selected @endif value="AB-">AB-</option>
                                    <option @if($agenda->gruposanguineo == "AB+") selected @endif value="AB+">AB+</option>
                                    <option @if($agenda->gruposanguineo == "A-") selected @endif value="A-">A-</option>
                                    <option @if($agenda->gruposanguineo == "A+") selected @endif value="A+">A+</option>
                                    <option @if($agenda->gruposanguineo == "B-") selected @endif value="B-">B-</option>
                                    <option @if($agenda->gruposanguineo == "B+") selected @endif value="B+">B+</option>
                                    <option @if($agenda->gruposanguineo == "O-") selected @endif value="O-">O-</option>
                                    <option @if($agenda->gruposanguineo == "O+") selected @endif value="O+">O+</option>
                                </select>
                            </div>

                            <div class="col-md-6" style="padding: 1px;">
                                <label for="cortesia" class="control-label" >Transfusiones</label>
                                <select id="transfusion" name="transfusion" class="form-control" onchange="guardar();  ">
                                    <option @if($agenda->transfusion=='NO'){{'selected '}}@endif value="NO">NO</option>
                                    <option @if($agenda->transfusion=='SI'){{'selected '}}@endif value="SI">SI</option>
                                </select>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="alcohol" class="control-label" >Hábitos</label>
                                <input class="form-control input-sm" onchange="guardar();" type="text" name="alcohol" value="{{$agenda->alcohol}}">
                            </div>

                        </div>

                        <div class="col-md-6" style="padding: 1px;">

                            <!--div class="col-md-12" style="padding: 1px;">
                                <label for="cortesia" class="control-label">Alergias</label>
                                <textarea class="form-control input-sm" name="alergias" id="alergias" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->alergias}}</textarea>
                            </div-->

                            <div class="col-md-12 has-error" style="padding: 1px;">
                                <div class="col-md-12" style="padding: 0px;">
                                    <label for="ale_list" class="control-label">Alergias</label>
                                </div>
                                <div class="col-md-12 has-error" style="padding: 0px;">
                                    <select id="ale_list" name="ale_list[]" class="form-control" multiple style="width: 100%;">
                                        @foreach($alergiasxpac as $ale_pac)
                                        <option selected value="{{$ale_pac->id_principio_activo}}">{{$ale_pac->principio_activo->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12" style="padding: 1px;">
                                <label for="cortesia" class="control-label">Vacunas</label>
                                <textarea class="form-control input-sm" name="vacuna" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->vacuna}}</textarea>
                            </div>

                        </div>

                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">Antecedentes Patologicos</label>
                            <textarea name="antecedentes_pat" id="antecedentes_pat" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->antecedentes_pat}}</textarea>
                        </div>

                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">Antecedentes Familiares</label>
                            <textarea name="antecedentes_fam" id="antecedentes_fam" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->antecedentes_fam}}</textarea>
                        </div>

                        <div class="col-md-6" style="padding: 1px;">
                            <label for="cortesia" class="control-label">Antecedentes Quirurgicos</label>
                            <textarea name="antecedentes_quir" id="antecedentes_quir" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->antecedentes_quir}}</textarea>
                        </div>

                        <!--a type="button" href="#" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-calendar"> Guardar</span>
                        </a-->

                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#examenes_externos').click());"><b>Documentos de Agenda</b></a></h3>
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
                                @php
                                    $agenda_archivos_2 = DB::table('agenda as a')
                                        ->join('agenda_archivo as ah', 'ah.id_agenda', '=', 'a.id')
                                        ->where('a.id_paciente', $agenda->id_paciente)
                                        ->select('a.*', 'ah.ruta', 'ah.archivo', 'ah.tipo_documento', 'ah.texto', 'ah.id as ahid')
                                        ->where('ah.archivo', '<>', null)
                                        ->orderBy('a.created_at', 'desc')
                                        ->get();
                                @endphp
                                @foreach($agenda_archivos_2 as $imagen)
                                <div class="col-md-3" style='margin: 10px 0; text-align: center;' >
                                @php
                                    $explotar = explode( '.', $imagen->archivo);
                                    $extension = end($explotar);
                                @endphp
                                @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                        <img  src="{{asset('hc_agenda')}}/{{$imagen->archivo}}" width="90%" style="max-height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('agenda/paciente/descarga/'.$imagen->archivo)}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('agenda/paciente/descarga/'.$imagen->archivo)}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    @php
                                        $variable = explode('/' , asset('/hc_ima/'));
                                        $d1 = $variable[3];
                                        // $d2 = $variable[4];
                                        

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('agenda.imagen567', ['id' => $imagen->ahid])}}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('agenda/paciente/descarga/'.$imagen->archivo)}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
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
                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
                                        <span>{{$imagen->nombre_anterior}}</span>
                                    </a>
                                    <a type="button" href="{{asset('laboratorio_externo_descarga')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @else
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
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
                                        // $d2 = $variable[4];
                                        

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
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
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%" style="max-height: 140px;">
                                    </a>
                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                    </a>
                                @elseif(($extension == 'pdf'))
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
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
                                        // $d2 = $variable[4];
                                        

                                    @endphp
                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_lab_externo', ['id' => $imagen->id]) }}">
                                        <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
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
 
        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary @if(is_null($evolucion)) collapsed-box  @endif" style="margin-bottom: 5px;" >
                <div class="box-header">

                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title">
                            <b><a href="javascript:void($('#evol').click());">Evoluciones</a></b>
                        </h3>
                    </div>
                    <!--div class="col-md-3" style="padding-left: 0px;">
                        <a href="{{route('sin_agenda.crear_evolucion',['id' => $agenda->id_paciente, 'ag' => $agenda->id])}}"><button class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-plus"></span> Agregar Visita</button></a>
                    </div-->

                    @if($agenda->proc_consul=='1')
                        @if($evoluciones_proc!=[])
                            @if($evoluciones_proc->count()=='0')
                            <div class="col-md-3" >
                                <span style="color: orange;">Procedimiento sin Evoluciones</span>
                            </div>
                            @else
                            <div class="col-md-3" >
                                <span style="color: orange;">Tiene {{$evoluciones_proc->count()}} evoluciones para el procedimiento</span>
                            </div>
                            @endif
                        @endif
                    @endif
                    @if(!is_null($evolucion))
                    <div class="col-md-3" >
                        <span style="color: #f2f2f2;">{{$evolucion->id}}</span>
                    </div>
                    @endif


                        <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="evol">
                            @if(is_null($evolucion))<i class="fa fa-plus"></i> @else <i class="fa fa-minus"></i>@endif</button>
                    </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">
                    <h4 style="text-align: center;background-color: cyan;"><b>Historial de Evoluciones </b></h4>
                    <div @if(!is_null($evolucion)) id="div1" class="col-md-12" @else class="col-md-12" @endif style="overflow:scroll; height: 400px !important; ">

                        @if($evoluciones->count()>0)

                        @foreach($evoluciones as $value)
                        
                        @php
                        $procedimiento =  Sis_medico\hc_procedimientos::find($value->hc_id_procedimiento);
                        
                        @endphp


                        <!--div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: @if($agenda->proc_consul=='1') @if(!is_null($protocolo)) @if($value->hc_id_procedimiento == $protocolo->id_hc_procedimientos) orange 1.5px @else black 1px @endif @endif @else black 1px @endif solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})"-->

                     
                        @if($agenda->proc_consul=='1')
                            @if(!is_null($protocolo))
                                @if($value->hc_id_procedimiento == $protocolo->id_hc_procedimientos)
                                    <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: orange 1.5px solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})">
                                @else
                                    <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})">
                                @endif
                            @else
                                <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})">
                            @endif
                        @else
                            <div class="table-responsive col-md-12 col-xs-12" style="max-height: 500px;font-size: 14px;padding: 0px;border: black 1px solid; margin: 5px 0;padding-left: 0px;" ondblclick="editar_consulta({{$value->id}},{{$agenda->id}})">
                        @endif
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td><b>Fecha:</b></td>
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
                                    @php
                                        $procedimiento_3333 = Sis_medico\hc_procedimientos::find($value->hc_id_procedimiento);
                                    @endphp

                                    <td><b>Medico Examinador:</b></td>
                                    <td style="background-color: #ffffe6;"><b>@if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_doctor_examinador))Dr. {{$procedimiento_3333->doctor->nombre1}} {{$procedimiento_3333->doctor->apellido1}}@else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif @else Dr. {{$agenda->udnombre}} {{$agenda->udapellido}}@endif</b></td>


                                    <td><b>Seguro:</b></td>
                                    <td>@if(!is_null($procedimiento_3333)) @if(!is_null($procedimiento_3333->id_seguro)){{$procedimiento_3333->seguro->nombre}}@else{{$agenda->snombre}}@endif @else{{$agenda->snombre}}@endif</td>
                                </tr>
                                <tr>
                                    <td ><b>Observación</b></td>
                                    <td colspan="3" style="background-color: #ffffb3;">@if(!is_null($procedimiento_3333)){{$procedimiento_3333->observaciones}}@endif</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Evolución: </b></td>

                                    @php
                                        $esp = DB::table('especialidad')->find($value->espid);
                                    @endphp
                                    <td colspan="2"><b>@if($value->proc_consul=='0' || $value->proc_consul=='4')CONSULTA @if(!is_null($esp)) {{$esp->nombre}} @endif @elseif($value->proc_consul=='1')PROCEDIMIENTO: @php
                                    if($procedimiento_3333 != null){
                                        if($procedimiento_3333->id_procedimiento_completo != null){
                                            echo $procedimiento_3333->procedimiento_completo->nombre_general;
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
                                    <tr>
                                        <td colspan="4"><?php echo $receta_evolucion->prescripcion; ?></td>
                                    </tr>
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
                    @if(!is_null($evolucion))
                    <div class="col-md-12" style="padding: 1px;" id="div2">

                        <h4 style="text-align: center;background-color: cyan;"><b>Ingresar Evolución</b></h3>
                        <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                            <b>Fecha Visita: </b>@if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: </b>{{substr($agenda->fechaini,10,10)}}
                        </div>

                        @if($agenda->espid=='8')
                            @php
                                $cardiologia = DB::table('hc_cardio')->where('hcid',$evolucion->hcid)->first();
                            @endphp
                            @if(!is_null($cardiologia))
                                <a href="{{route('cardio.formato',['id' => $evolucion->id])}}">
                                    <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                                        <span class="glyphicon glyphicon-file"> Cardiología</span>
                                    </button>
                                </a>
                            @endif
                    
                        @if(!is_null($cardiologia))
                            <a href="{{route('auditoria_cardio.reporte_cardiologia',['id' => $evolucion->id,"agenda"=>$agenda->id])}}" class="btn btn-success btn-sm"> 
                                <span class="glyphicon glyphicon-file"> @lang('laboratorio.reporte_cardiologia')</span>
                            </a>
                        @endif
                    @endif


                        <form id="frm_evol">
                            <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                            <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
                            <div class="col-md-6">
                                <h4><b>Preparación</b></h4>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="presion" class="control-label">P. Arterial</label>
                                    <input class="form-control input-sm" name="presion" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->presion}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="pulso" class="control-label">Pulso</label>
                                    <input class="form-control input-sm" name="pulso" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->pulso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="temperatura" class="control-label">Temperatura (ºC)</label>
                                    <input class="form-control input-sm" name="temperatura" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->temperatura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="o2" class="control-label">SaO2:</label>
                                    <input class="form-control input-sm" name="o2" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->o2}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="estatura" class="control-label">Estatura (cm)</label>
                                    <input class="form-control input-sm" id="estatura" name="estatura" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->altura}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="peso" class="control-label">Peso (kg)</label>
                                    <input class="form-control input-sm" id="peso" name="peso" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->peso}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="perimetro" class="control-label" id="peri">Perimetro Abdominal</label>
                                    <input class="form-control input-sm" id="perimetro" name="perimetro" style="width: 100%;" rows="4" onchange="guardar_protocolo();" value="{{$agenda->perimetro}}" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-3" style="padding: 1px;" >
                                    <label for="peso_ideal" class="control-label">Peso Ideal (kg)</label>
                                    <input class="form-control input-sm" id="peso_ideal" name="peso_ideal" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="gct" class="control-label">% GCT RECOMENDADO</label>
                                    <input class="form-control input-sm" id="gct" name="gct" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="imc" class="control-label">IMC</label>
                                    <input class="form-control input-sm" id="imc" name="imc" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                <div class="col-md-4" style="padding: 1px;">
                                    <label for="cimc" class="control-label">Categoria IMC</label>
                                    <input class="form-control input-sm" id="cimc" name="cimc" disabled style="width: 100%;" rows="4" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>
                                @if(!is_null($child_pugh))
                                <h4><b>Clasificación Child Pugh</b></h4>
                                <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id}}">
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="ascitis" class="control-label">Ascitis</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="ascitis" id="ascitis">
                                        <option @if($child_pugh->ascitis == 1) selected @endif value="1" >Ausente</option>
                                        <option @if($child_pugh->ascitis == 2) selected @endif value="2" >Leve</option>
                                        <option @if($child_pugh->ascitis == 3) selected @endif value="3" >Moderada</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="encefalopatia" class="control-label">Encefalopatia</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="encefalopatia" id="encefalopatia">
                                        <option @if($child_pugh->encefalopatia == 1) selected @endif value="1" >No</option>
                                        <option @if($child_pugh->encefalopatia == 2) selected @endif value="2" >Grado 1 a 2</option>
                                        <option @if($child_pugh->encefalopatia == 3) selected @endif value="3" >Grado 3 a 4</option>
                                    </select>
                                </div>
                                <div class="col-md-2" style="padding: 1px;">
                                    <label for="albumina" class="control-label">Albúmina(g/l)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="albumina" id="albumina">
                                        <option @if($child_pugh->albumina == 1) selected @endif value="1" >&gt; 3.5</option>
                                        <option @if($child_pugh->albumina == 2) selected @endif value="2" >2.8 - 3.5</option>
                                        <option @if($child_pugh->albumina == 3) selected @endif value="3" >&lt; 2.8</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="bilirrubina" class="control-label">Bilirrubina(mg/dl)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="bilirrubina" id="bilirrubina">
                                        <option @if($child_pugh->bilirrubina == 1) selected @endif value="1" >&lt; 2</option>
                                        <option @if($child_pugh->bilirrubina == 2) selected @endif value="2" >2 - 3</option>
                                        <option @if($child_pugh->bilirrubina == 3) selected @endif value="3" >&gt; 3</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="inr" class="control-label">Protrombina% (INR)</label>
                                    <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="inr" id="inr">
                                        <option @if($child_pugh->inr == 1) selected @endif value="1" >&gt; 50 (&lt; 1.7)</option>
                                        <option @if($child_pugh->inr == 2) selected @endif value="2" >30 - 50 (1.8 - 2.3)</option>
                                        <option @if($child_pugh->inr == 3) selected @endif value="3" >&lt; 30 (&gt; 2.3)</option>
                                    </select>
                                </div>

                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="puntaje" class="control-label">Puntaje</label>
                                    <input class="form-control input-sm" id="puntaje" name="puntaje" disabled style="width: 100%;" readonly="yes" >
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="clase" class="control-label">Clase</label>
                                    <input class="form-control input-sm" id="clase" disabled style="width: 100%;"  readonly="yes">
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="sv1" class="control-label">SV1 Año:</label>
                                    <input class="form-control input-sm" id="sv1" disabled style="width: 100%;"  readonly="yes">
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="sv2" class="control-label">SV2 años:</label>
                                    <input class="form-control input-sm" id="sv2" disabled style="width: 100%;" readonly="yes">
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">

                                    <input type="hidden" name="id_hc_procedimiento" value="{{$hc_proc->id}}">
                                <div class="col-md-12" style="padding: 1px;">
                                   <h4><b>Datos Generales</b></h4>
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="id_doctor_examinador" class="control-label">Medico Examinador</label>
                                        <select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                            @foreach($doctores as $value)
                                                <option @if($hc_proc->id_doctor_examinador == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- CONVENIO-->
                                    <div class="col-md-6" style="padding: 1px;">
                                        <label for="id_seguro" class="control-label">Seguro</label>
                                        <select onchange="guardar_protocolo();cargar_empresa2();"  class="form-control input-sm" style="width: 100%;" name="id_seguro" id="id_seguro">
                                            @foreach($seguros as $value)
                                                <option @if($hc_proc->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="div_empresa"></div>
                                    <div class="col-md-12 has-error" style="padding: 1px;">
                                        <label for="observaciones" class="control-label">Observaciones</label>
                                        <textarea class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;background-color: #ffffb3;" rows="8" onchange="guardar_protocolo();">{{$hc_proc->observaciones}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="motivo" class="control-label">Motivo</label>
                                    <textarea name="motivo" style="width: 100%;" rows="1" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($evolucion)){{$evolucion->motivo}}@endif</textarea>
                                </div>

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="thistoria_clinica" class="control-label">Evolución</label>
                                    <div id="thistoria_clinica" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $evolucion->cuadro_clinico ?>@endif</div>
                                    <input type="hidden" name="historia_clinica" id="historia_clinica">
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="tresultado_ev" class="control-label">Resultados de Exámenes y Procedimientos Diagnósticos</label>
                                    <div id="tresultado_ev" style="border: solid 1px;">@if(!is_null($evolucion))<?php echo $evolucion->resultado ?>@endif</div>
                                    <input type="hidden" name="resultado_ev" id="resultado_ev">
                                </div>
                                @if(!is_null($child_pugh))
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examen_fisico" class="control-label">Examen Fisico</label>
                                    <textarea name="examen_fisico" style="width: 100%;" rows="7" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                                </div>
                                @endif

                                @if($agenda->espid=='8')

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="resumen" class="control-label">Resumen</label>
                                    <textarea name="resumen" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_diagnostico" class="control-label">Plan Diagnóstico</label>
                                    <textarea name="plan_diagnostico" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="plan_tratamiento" class="control-label">Plan Tratamiento</label>
                                    <textarea name="plan_tratamiento" style="width: 100%;" rows="1" onchange="guardar_cardio();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
                                </div>
                                @endif

                                <input type="hidden" name="codigo" id="codigo">

                                <label for="cie10" class="col-md-8 control-label" style="padding-left: 0px;"><b>Diagnóstico </b></label>
                                <div class="form-group col-md-8" style="padding: 1px;">
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
                                </div>

                                <div class="form-group col-md-2" style="padding: 1px;">
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>
                                    </select>
                                </div>

                                @if($agenda->estado_cita=='4')
                                <button id="bagregar" class="btn btn-success btn-sm col-md-2"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                @endif

                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                    <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                                    </table>
                                </div>

                                <?php /*
                @if(!is_null($evolucion))
                <div class="col-md-12" style="padding: 1px;">
                <label for="rp" class="control-label col-md-6">RP</label>
                @if($agenda->estado_cita=='4')
                <a href="{{route('receta.receta', ['hcid' => $evolucion->hcid])}}" class="col-md-offset-4 btn btn-success btn-sm col-md-2"><span> Receta</span></a>
                @endif
                </div>

                @php
                $rec_ev = Sis_medico\hc_receta::where('id_hc',$evolucion->hcid)->first();
                $medicinas = Sis_medico\Medicina::where('estado',1)->get();
                if(!is_null($rec_ev)){
                $det_ev = Sis_medico\hc_receta_detalle::where('id_hc_receta',$rec_ev->id)->get();
                }
                @endphp
                @if(!is_null($rec_ev))
                <div class="col-md-12" style="padding: 1px;border: 1px solid black;">
                @foreach($det_ev as $dvalue)
                @php $genericos = $medicinas->where('id',$dvalue->id_medicina)->first()->genericos; @endphp
                {{$dvalue->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$dvalue->cantidad}}<br>

                @endforeach
                </div>
                @endif

                <!--textarea name="rp" style="width: 100%;" readonly="readonly" rows="2" onchange="guardar_protocolo();">{{$evolucion->rp}}</textarea-->

                @endif
                 */?>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examenes_realizar" class="control-label">Examenes a Realizar</label>
                                    <textarea name="examenes_realizar" style="width: 100%;" rows="2" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($evolucion)){{$agenda->examenes_realizar}}@endif</textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    @endif
                </div>
            </div>
        </div>

        @if($protocolos->count() > '0')

        <div class="col-md-12" style="padding-right: 6px;">
            <!--div class="box box-primary @if($protocolos_dia!=[]) @if($protocolos_dia->count()<=1) collapsed-box @endif @endif" style="margin-bottom: 5px;"-->
            @if($protocolos_dia!=[])
                @if($protocolos_dia->count()<=1)
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                @else
                    <div class="box box-primary" style="margin-bottom: 5px;">
                @endif
            @else
                <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
            @endif
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#hist').click());">Historial de Procedimientos </a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;">@if($protocolos_dia!=[]) @if($protocolos_dia->count()>1)<span style="color: orange;"> Procedimientos de Hoy: {{$protocolos_dia->count()}} </span>@endif @endif</div>
                        <!-- tools box -->
                        <div class="pull-right box-tools">

                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="hist">
                                @if($protocolos_dia!=[])
                                    @if($protocolos_dia->count()<=1)
                                        <i class="fa fa-plus"></i>
                                    @else
                                        <i class="fa fa-minus"></i>
                                    @endif
                                @else
                                    <i class="fa fa-plus"></i>
                                @endif
                            </button>
                        </div>
                        <!-- /. tools -->
                </div>
                <div class="box-body" style="padding: 5px;">


                    <div col-md-12" style="overflow:scroll; height:400px;">

                        @foreach($protocolos as $value)
                        @php
                            $cant_anexo = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id)->where('estado','2')->get()->count();
                            $cant_estud = DB::table('hc_imagenes_protocolo')->where('id_hc_protocolo',$value->id)->where('estado','3')->get()->count();
                            $cant_total = $cant_anexo + $cant_estud;
                            $vh_procedimiento = \Sis_medico\hc_procedimientos::find($value->id_hc_procedimientos);

                            $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_hc_procedimientos)->get();
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

                        @endphp

                        <div class="col-md-12" ondblclick="editar({{$value->id}},{{$agenda->id}})" style="border: @if($value->hcid == $agenda->hcid) orange 1.5px @else black 1px @endif solid; margin: 5px 0;padding-left: 0px;padding-right: 0px">
                            <div class="table-responsive">
                                <table class="table table-striped tok" style="margin-bottom: 0px;">
                                    <tbody>
                                        <tr>
                                            <td><b>Fecha: </b></td>
                                            @php $dia =  Date('N',strtotime($value->fechaini)); $mes =  Date('n',strtotime($value->fechaini)); @endphp
                                            <td style="color: blue;"><b>
                                                @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($value->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($value->fechaini,0,4)}}</b>
                                            </td>
                                            <td><b>Hora: </b></td>
                                            <td style="color: blue;"><b>{{substr($value->fechaini,10,10)}}</b></td>
                                            <td style="color: Cornsilk;">{{$value->id}}@if($vh_procedimiento->estado==0)<span style="color: red"><b>Eliminado</b></span>@endif</td>

                                        </tr>
                                        @php
                                            $seguro_nom='';$empresa_nom='';
                                            $procedimiento_222 = Sis_medico\hc_procedimientos::find($value->id_hc_procedimientos);
                                            if($procedimiento_222->id_seguro!=null){
                                                $seguro_nom = $procedimiento_222->seguro->nombre;
                                            }
                                            if($procedimiento_222->id_empresa!=null){
                                                $empresa = Sis_medico\Empresa::find($procedimiento_222->id_empresa);
                                                $empresa_nom = $empresa->nombrecomercial;
                                            }
                                            if($empresa_nom==''){
                                                if($procedimiento_222->historia->agenda->id_empresa!=null){
                                                    $empresa_nom = $procedimiento_222->historia->agenda->empresa->nombrecomercial;
                                                }
                                            }

                                        @endphp

                                        <tr>
                                            <td><b>Procedimiento</b></td>
                                            <td colspan="2"><span style="color: blue;"><b>@if($value->nombre_general!='') {{$value->nombre_general}} @else {{$texto}} @endif</b></span></td>
                                            <td >@if($cant_total > 0)
                                                    <span style="font-size: 12px;color: #339966;"><b>ARCHIVOS CARGADOS: {{$cant_anexo}} ANEXO(S) - {{$cant_estud}} ESTUDIO(S)<b></span>
                                                @else
                                                @endif
                                            </td>
                                            <td colspan="2"><b>SEGURO:</b> {{$seguro_nom}}/{{$empresa_nom}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><b>Hallazgos</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><?php echo $value->hallazgos; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><b>Conclusiones</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"><?php echo $value->conclusion; ?></td>
                                        </tr>
                                        <tr>

                                            <td><b>Medico Examinador</b></td>
                                            <td><b>@if(!is_null($procedimiento_222)) @if(!is_null($procedimiento_222->id_doctor_examinador))Dr. {{$procedimiento_222->doctor->nombre1}} {{$procedimiento_222->doctor->apellido1}}@else Dr. {{$value->d1nombre1}} {{$value->d1apellido1}}@endif @else Dr. {{$value->d1nombre1}} {{$value->d1apellido1}}@endif</b></td>

                                            <td><b>Asistente: </b></td>
                                            <td>{{$value->d2apellido1}}</td>
                                            <td><b>Asistente: </b>
                                            <td>{{$value->d3apellido1}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Medico que firma</b></td>
                                            <td><b>@if(!is_null($procedimiento_222)) @if(!is_null($procedimiento_222->id_doctor_examinador2)) Dr. {{$procedimiento_222->doctor_firma->nombre1}} {{$procedimiento_222->doctor_firma->apellido1}} @else Dr. {{$value->d1nombre1}} {{$value->d1apellido1}}@endif @else Dr. {{$value->d1nombre1}} {{$value->d1apellido1}}@endif</b></td>

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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach


                    </div>

                </div>
            </div>
        </div>
        @endif

        @if(!is_null($protocolo))

        <div class="col-md-12" style="padding-right: 6px;">

            <div class="box box-primary ">
                <div class="box-header">
                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title"><b><a href="javascript:void($('#proc').click());">Procedimiento</a></b>
                        </h3>
                    </div>
                    <div class="col-md-6" style="color: #f2f2f2;">{{$protocolo->id}}</div>
                    <div class="col-md-12" style="padding-left: 0px;">
                        @if($agenda->estado_cita=='4')

                        <!--button id="nuevo_proc" type="button" onclick="Agregar_procedimiento();" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-plus"> Agregar Procedimiento</span>
                        </button-->

                        @if($agenda->estado_cita =='4' && $agenda->proc_consul == '1')
                        <!--a id="nueva_evolucion" onclick="agregar_evolucion();" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i>
                            <span>Agregar Evolucion</span>
                        </a-->
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
                                <span class="glyphicon glyphicon-file"> @lang('admision.estudio_formato') </span>
                            </button>
                        </a>
                            @php
                                $rolUsuario = Auth::user()->id_tipo_usuario;
                            @endphp
                            @if(in_array($rolUsuario, array(1, 11, 5)) == true )
                                <a href="{{ route('evolucion.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                                    <span class="glyphicon glyphicon-file"> Evolución</span>
                                </a>
                                <a href="{{ route('protocolo.pr_modal', ['id_protocolo' => $protocolo->id]) }}" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#mprotocolo">
                                    <span class="glyphicon glyphicon-download-alt"> Protocolo</span>
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
                            <a target="_blank" href="{{route('formularioPreparacion.excelPreparacion', ['id'=>$protocolo->id_hc_procedimientos,'id_protocolo' => $protocolo->id])}} " class="btn btn-success btn-sm"><span class="fa fa-file-excel-o"> Formulario Recuperacion</span></a>
                            @endif
                            <a target="_blank" href="{{ route('formato.anamnesis_crear', ['hcid' =>  $protocolo->hcid]) }}" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Anamnesis</a>
                            <button id="b_cpre_eco" class="btn btn-success btn-sm" onclick="carga_cpre_eco();">@lang('laboratorio.agregar') CPRE+ECO</button>
                            @php
                                $hc_egreso = DB::table('hc_egreso')->where('id_hc_procedimientos', $protocolo->id_hc_procedimientos)->first();
                            @endphp
                            <!-- cambiar a diferente de null -->
                            <a href="javascript:void(0);" onclick="sumit_documentos()" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-level-up"></span> Documentos</a>
                            <a target="_blank" href="{{route('formulario013.edit', ['id'=>$protocolo->id_hc_procedimientos])}} " class="btn btn-success btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true" ></i> Formulario013</a>
                            <a data-toggle="modal" data-target="#archivos" href="{{route('formulario007.modal_formatos_24', ['id_protocolo' => $protocolo->id]) }}" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-level-up"></span> Formatos</a>
                            </a>
			                    <form role="form" method="POST" action="{{route('controldoc.control_doc')}}" style="">
                                @if(in_array($rolUsuario, array(1, 11)) == true )
                                
                                    <input type="hidden" name="hcid" value="{{$agenda->hcid}}">
                                    <input type="hidden" name="url_doctor" value="{{$agenda->id_doctor1}}">
                                    <input type="hidden" id="unix2" name="unix" value="">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="protocolo" value="{{$protocolo->id}}">

                                    <button type="submit" id="enviar2" class="btn btn-success btn-sm" style="display: none;" ><span class="glyphicon glyphicon-level-up"></span> Documentos</button>
                                
                                @endif
                                </form>
                                  
                            
                            <!--
                            @if($hc_proc->id_seguro=='2')
                            <a id="planilla_iees" type="button" href="{{route('archivo_plano.planilla_hcid',['hcid' => $protocolo->hcid, 'id_seguro' => '2'])}}" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-file"> Planilla Iees</span>
                            </a>
                            @endif
                            @if($hc_proc->id_seguro=='5')
                            <a id="planilla_msp" type="button" href="{{route('archivo_plano.planilla_msp',['hcid' => $protocolo->hcid, 'id_seguro' => '5'])}}" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-file"> Planilla MSP</span>
                            </a>
                            @endif
                            -->

                        @endif

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

                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="proc">
                                <i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /. tools -->
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

                            <div class="col-md-3" style="padding: 1px; display: none;">
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

                            <div class="col-md-4" style="padding: 1px;">
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

                            <!--div class="col-md-12" style="padding: 1px;">
                                <label for="estudio_patologico" class="control-label">Estudio Anátomo Patológico</label>
                                <textarea name="estudio_patologico" style="width: 100%;" rows="1" onchange="guardar_procedimiento();">{{$protocolo->estudio_patologico}}</textarea>
                            </div-->

                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12">


                        <div id="div_cpre_eco" class="col-md-12"></div>
                        <form id="hc_protocolo">
                        <input type="hidden" name="id_hc_procedimiento" value="{{$hc_proc->id}}">
                        <div class="col-md-12" style="padding: 1px;">
                            <label for="id_doctor_examinador" class="control-label">Medico Examinador</label>
                            <select onchange="hc_protocolo();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                @foreach($doctores as $value)
                                    <option @if($hc_proc->id_doctor_examinador == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" style="padding: 1px;">
                            <label for="id_seguro" class="control-label">Seguro</label>
                            <select onchange="hc_protocolo();cargar_empresa();"  class="form-control input-sm" style="width: 100%;" name="id_seguro" id="id_seguro">
                                @foreach($seguros as $value)
                                    <option @if($hc_proc->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" style="padding: 1px;" id="div_empresa">

                        </div>




                        <div class="col-md-12" style="padding: 1px;">
                            <label for="observaciones" class="control-label">Observaciones</label>
                            <textarea class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;" rows="7" onchange="hc_protocolo();">{{$hc_proc->observaciones}}</textarea>
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
                        </div>
                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                                <div class="box-header">
                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#ima_historico').click());">Imágenes</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
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
                                        <a type="button" href="{{route('hc_video.mostrar', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '0' ])}}" class="btn btn-primary btn-sm"><!-- ruta 0 desde la historia clinica -->
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
                                                    <a data-toggle="modal" data-target="#foto2" href="{{route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
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
                                                    <a data-toggle="modal" data-target="#video" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/video.png')}}" width="90%">
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        // $d2 = $variable[4];
                                                        

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
                                        <a type="button" href="{{route('hc_video.mostrar_documento', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '0' ])}}" class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-open-file"> Documentos &amp; Anexos</span>
                                        </a>
                                    </div>
                                    @endif

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
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                    <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                        <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                                    </a>
                                                @elseif(($extension == 'pdf'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
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
                                                        // $d2 = $variable[4];
                                                        

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
                                        <a type="button" href="{{route('hc_video.mostrar_estudios', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '0' ])}}" class="btn btn-primary btn-sm">
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
                                                        <span>{{$imagen->nombre_anterior}}</span>
                                                    </a>
                                                @elseif(($extension == 'pdf'))
                                                    <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                        <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                    </a>
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        // $d2 = $variable[4];
                                                        

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
                        <div class="col-md-12" >
                            <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                                <div class="box-header">
                                    <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_bipsia').click());">Biopsias</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
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
                                        <a type="button" href="{{route('hc_video.mostrar_biopsias', ['id_protocolo' => $protocolo->id, 'agenda' => $agenda->id, 'ruta' => '0' ])}}" class="btn btn-primary btn-sm">
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
                                                        // $d2 = $variable[4];
                                                        

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
                    </div>



                </div>
            </div>
        </div>
        @endif
        @if($agenda->estado_cita == 4)
            @if($evolucion == null)
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
                                                        @php 
                                                            $receta = Sis_medico\hc_receta::find($re_hist->id);
                                                         @endphp
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
                                                                                    @if(!is_null($receta))
                                                                                        @php $rdetalles = $receta->detalles; @endphp
                                                                                        @foreach($rdetalles as $detalle)
                                                                                            @php $genericos = $detalle->medicina->genericos; @endphp                       
                                                                                            <p>{{$detalle->nombre}} @foreach($genericos as $gen) ( {{$gen->generico->nombre}} ) @endforeach : {{$detalle->cantidad}} {{ $detalle->detalle_cantidad}}</p>                                  
                                                                                        @endforeach  
                                                                                      @if(!is_null($re_hist->rp))
                                                                                        <p><?php echo $re_hist->rp ?>
                                                                                        </p>
                                                                                      @endif
                                                                                    @endif    
                                                                                    </div>
                                                                                  </div>
                                                                                  <div class="col-md-6" >
                                                                                    <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                                                                    <div id="xtprescripcion" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #3c8dbc;">
                                                                                    @if(!is_null($receta))
                                                                                        @php $rdetalles = $receta->detalles; @endphp
                                                                                        @foreach($rdetalles as $detalle)
                                                                                            @php $genericos = $detalle->medicina->genericos; @endphp                       
                                                                                            <p>{{$detalle->nombre}}: {{$detalle->dosis}}</p>                                 
                                                                                        @endforeach  
                                                                                      @if(!is_null($re_hist->prescripcion))
                                                                                        <p><?php echo $re_hist->prescripcion ?></p>
                                                                                        @endif
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
        @endif
        <div class="col-md-12" style="padding-right: 6px;">
            <div  class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#ordenes_biopsia').click());"><b>Historial Ordenes de Biopsias</b></a></h3>
                    </div>
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="ordenes_biopsia">
                            <i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" style="padding: 5px;">
                    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
                        <div class="parent" style="background-color: #ffffff">
                            <div style=" margin-right: 30px;">
                                @if($orden_biopsias != null)
                                   @foreach($orden_biopsias as $bp)
                                        @php
                                          $biop_detalle = Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento',$bp->hc_id_procedimiento)->get();
                                          $biop_hcid = Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento',$bp->hc_id_procedimiento)->first();
                                          $cuadclinico_diagnostico = Sis_medico\hc_procedimientos::where('id',$bp->hc_id_procedimiento)->first();
                                        @endphp
                                    <div class="box collapsed-box" style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                                        <div class="box-header with-border" style="font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        @if(!is_null($biop_hcid->created_at))
                                                          @php
                                                            $dia =  Date('N',strtotime($biop_hcid->created_at));
                                                            $mes =  Date('n',strtotime($biop_hcid->created_at));
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
                                                            {{substr($biop_hcid->created_at,8,2)}} de
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
                                                            del {{substr($biop_hcid->created_at,0,4)}}</span></b>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-5">
                                                    </div>
                                                    <div class="col-md-2">
                                                      <a target="_blank" class="btn btn-danger" style="color:white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" href="{{ route('imprimir.orden_biopsias_recepcion', ['id' => $bp->hc_id_procedimiento,'id_hcid' => $bp->hcid,'id_doct' => $bp->id_doctor]) }}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Imprimir Orden Biopsia</a>
                                                    </div>
                                                    <div class="pull-right box-tools ">
                                                      <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                                                        <i class="fa fa-minus"></i>
                                                      </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general';" id="d">
                                            <br>
                                            <div class="col-12">
                                                <table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;border-color: #4682B4;">
                                                  <tr>
                                                    <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;background-color: #4682B4;color: white;border-color: currentColor;text-align: center;"><b>&nbsp;Detalle Frascos</b></td>
                                                  </tr>
                                                  @foreach($biop_detalle as $value)
                                                  <tr>
                                                   <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                                                   <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                                                  </tr>
                                                  @endforeach
                                                </table>
                                            </div>
                                            <div class="col-md-12" style="padding-top: 10px"></div>
                                            <div class="col-md-12" style="padding: 1px;">
                                              <div class="row">
                                                <div class="col-md-12">
                                                  <label style="font-family: 'Helvetica general';">CUADRO CLINICO:</label>
                                                </div>
                                                <div class="col-12" style="padding: 15px;">
                                                  <?php echo $cuadclinico_diagnostico->cuadro_clinico_bp ?>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col-md-12" style="padding: 1px;">
                                              <div class="row">
                                                <div class="col-md-12">
                                                  <label style="font-family: 'Helvetica general';">DIAGNOSTICO:</label>
                                                </div>
                                                <div class="col-12" style="padding: 15px;">
                                                  <?php echo $cuadclinico_diagnostico->diagnosticos_bp ?>
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
<input type="hidden" id="contador_de procedimiento" value="0" >


<div id="medicina_div"></div>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
      })

    function hc_protocolo(){
        $.ajax({
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
        });
    }
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

    function datos_child_pugh(){
        dato1 = parseInt($('#ascitis').val());
        dato2 = parseInt($('#albumina').val());
        dato3 = parseInt($('#encefalopatia').val());
        dato4 = parseInt($('#bilirrubina').val());
        dato5 = parseInt($('#inr').val());
        cantidad = dato1+ dato2+dato3+dato4+dato5;
        $('#puntaje').val(cantidad);
        if(cantidad >= 5 && cantidad<=6){
            $('#clase').val('A');
            $('#sv1').val('100%');
            $('#sv2').val('85%');
        }else if(cantidad >= 7 && cantidad<=9){
            $('#clase').val('B');
            $('#sv1').val('80%');
            $('#sv2').val('60%');
        }else if(cantidad >= 10 && cantidad<=15){
            $('#clase').val('C');
            $('#sv1').val('45%');
            $('#sv2').val('35%');
        }
    }
    $(document).ready(function() {

        datos_child_pugh();
        var edad;
        edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>');
        $('#edad').val( edad );

        @if(!is_null($evolucion))
        calcular_indice();
        @endif
        //cargar_historia();
        @if(!is_null($evolucion))
            cargar_tabla();
        @endif
        @if(!is_null($protocolo))
            cargar_tabla();
            Carga_proc(0);
        @endif
        index();

        //crear_select();//subseguro
        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');

        /*$("#div1").height(400);
        var hdiv1 = $("#div1").height();
        //alert(hdiv1);
        var hdiv2 = $("#div2").height();
        alert(hdiv2);
        $("#div1").height(hdiv2);
        var hdiv1 = $("#div1").height();
        //alert(hdiv1);*/



    });



    @if(!is_null($evolucion))
        cargar_empresa2();
        function cargar_tabla(){
            $.ajax({
                    url:"{{route('epicrisis.cargar',['id' => $evolucion->hc_id_procedimiento])}}",
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
                            cell2.innerHTML = value.descripcion;

                            var vpre_def = '';
                            if(value.pre_def!=null){
                                vpre_def = value.pre_def;
                            }
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = vpre_def;

                            var cell4 = row.insertCell(3);
                            cell4.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                        });

                    }
                })
        }
    @endif

    @if(!is_null($protocolo))
        cargar_empresa();
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
                            var cell4 = row.insertCell(3);
                            cell4.innerHTML = value.ingreso_egreso;
                            var cell5 = row.insertCell(4);
                            cell5.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                        });

                    }
                })
        }

    @endif


    @if(!is_null($hc_proc))
        function cargar_empresa(){

            $.ajax({
              type: 'get',
              url:"{{ url('empresa/historiaclinica/cargar') }}/{{$hc_proc->id}}/{{$agenda->id}}/"+$('#id_seguro').val(),

              success: function(data){

                $('#div_empresa').empty().html(data);

              },

              error: function(data){


              }
            });
        };



        function cargar_empresa2(){

            $.ajax({
              type: 'get',
              url:"{{ url('empresa2/historiaclinica/cargar') }}/{{$hc_proc->id}}/{{$agenda->id}}/"+$('#id_seguro').val(),

              success: function(data){

                $('#div_empresa').empty().html(data);

              },

              error: function(data){


              }
            });
        }
    @endif

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


    $('.select2').select2({
        tags: false
    });



    tinymce.init({
        selector: '#thistoria_clinica',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else


        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica');
               //alert(ed.getContent());
                $("#historia_clinica").val(ed.getContent());

            });
        },

        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_protocolo();

            });
          }
    });

    tinymce.init({
        selector: '#tresultado_ev',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        @if($agenda->estado_cita!='4')
        readonly: 1,
        @else


        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tresultado_ev');
               //alert(ed.getContent());
                $("#resultado_ev").val(ed.getContent());

            });
        },

        @endif

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tresultado_ev');
                $("#resultado_ev").val(ed.getContent());
                guardar_protocolo();

            });
          }
    });

    tinymce.init({
        selector: '#thallazgos',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | styleselect fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
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




    function guardar(){
        $.ajax({
          type: 'post',
          url:"{{route('admision_datos.doctor')}}", //CombinadoController->ingreso
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
            //alert(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);

            $('#edad').val( edad );
          },
          error: function(data){

            if(data.responseJSON.telefono1!=null){
                $('.div_tel').addClass('has-error');
                alert(data.responseJSON.telefono1[0]);
            }
            if(data.responseJSON.mail!=null){
                $('.div_ema').addClass('has-error');
                alert(data.responseJSON.mail[0]);
            }
            //console.log(data.responseJSON);

          }
        });
    }









function alertaPersonalizada(icon, title){
    Swal.fire({
      position: `center` ,
      icon: `${icon}`,
      title: `${title}` ,
      showConfirmButton: false,
      timer: 1500
    })
}


    function guardar_admin(){
        let observacion = document.getElementById('observacion_admin').value;
        let id_paciente = document.getElementById('id_paciente_clinica').value;
        //console.log(observacion + id_paciente)


         $.ajax({
          type: 'post',
          url:"{{route('paciente.guardarObservacionAdministrativa')}}", //CombinadoController->ingreso
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            console.log(data);
          
          },
          error: function(data){

         

          }
        });
    }

































    function guardar_protocolo(){

        calcular_indice();
        datos_child_pugh();
        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualizar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){

          }
        });
    }

    function guardar_cardio(){

        calcular_indice();

        $.ajax({
          type: 'post',
          url:"{{route('cardiologia.crea_actualiza')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){

          }
        });
    }

    function calcular_indice(){
        var peso =  document.getElementById('peso').value;
        var estatura = document.getElementById('estatura').value;
        var sexo = @if($agenda->sexo == 1){{$agenda->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$agenda->fecha_nacimiento}}');
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        }
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc').val(texto);
        $('#gct').val(gct.toFixed(2));
        $('#imc').val(imc.toFixed(2));
        $('#peso_ideal').val(peso_ideal.toFixed(2));
    }

    function guardar_evolucion(){

        $.ajax({
          type: 'post',
          url:"{{route('consulta.actualiza_historia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){

            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad').val( edad );
          },
          error: function(data){

          }
        });

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
                    console.log(data);

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
                console.log(data);
                if(data!='0'){
                    $('#codigo').val(data.id);

                    @if(!is_null($evolucion))

                    @endif

                }

            },
            error: function(data){

                }
        })
    });





    $('#bagregar').click( function(e){
        e.preventDefault();
        //alert("ok");
        @if(!is_null($protocolo))
        if($('#pre_def').val()!='' ){
            if($('#ing_egr').val()!='' ){
                guardar_cie10_PRO();
                $('#ing_egr').val('');
            }else{
                alert("Seleccione Ingreso o Egreso");
            }
        }else{
            alert("Seleccione Presuntivo o Definitivo");
        }
        @endif

        @if(!is_null($evolucion))
            if($('#cie10').val()!='' ){
                if($('#pre_def').val()!='' ){
                    //alert("guardar");
                    guardar_cie10();
                }else{
                    alert("Seleccione Presuntivo o Definitivo");
                }
            }else{
                alert("Seleccione CIE10");
            }

        @endif
        $('#codigo').val('');
        $('#cie10').val('');
        $('#pre_def').val('');

    });



    @if(!is_null($evolucion))
    //alert("guardar_cie10");
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$evolucion->hcid}}, 'hc_id_procedimiento': {{$evolucion->hc_id_procedimiento}}, 'in_eg': null },
            success: function(data){

                console.log(data);
                if(data.count>0){

                    var indexr = data.count-1
                    var table = document.getElementById("tdiagnostico");
                    var row = table.insertRow(indexr);
                    row.id = 'tdiag'+data.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>'+data.cie10+'</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = data.descripcion;

                    var vpre_def = '';
                    if(data.pre_def!=null){
                        vpre_def = data.pre_def;
                    }
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';


                    //codigo para acceso a cie10 en la receta
                    //aqui va para la receta
                    anterior = tinyMCE.get('trp').getContent();
                    //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                    tinyMCE.get('trp').setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                    $('#rp').val(tinyMCE.get('trp').getContent());
                    cambiar_receta_2();

                }



            },
            error: function(data){

                }
        })
    }
    @endif

    @if(!is_null($protocolo))
    function guardar_cie10_PRO(){
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(),'hcid': {{$protocolo->hcid}}, 'hc_id_procedimiento': {{$protocolo->id_hc_procedimientos}}, 'in_eg': $("#ing_egr").val() },
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
                var cell4 = row.insertCell(3);
                cell4.innerHTML = data.in_eg;
                var cell5 = row.insertCell(4);
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

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
    @endif

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
                //alert(alerta);
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
    function actualiza(e){
        cortesia = document.getElementById("cortesia").value;

        if (cortesia == "SI"){

            location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 1])}}";

        }
        else if(cortesia == "NO"){
            location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 0])}}";
        }

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

    function editar(id,agenda){
        //alert("{{url('estudio/editar')}}/"+id);
        location.href ="{{url('estudio/editar')}}/"+id+"/"+agenda;

    }

    function editar_consulta(id,agenda){
        //alert(agenda);

        location.href ="{{url('historialclinico/visitas_ingreso')}}/"+id+"/"+agenda;//visita.crea_actualiza

    }

    function Agregar_procedimiento(){

        $('#nuevo_proc').attr('disabled','disabled');
        //alert("ok");
        location.href ="{{route('estudio.nuevo', ['id' => $agenda->id ])}}";
    }

    $('#ale_list').select2({
        placeholder: "Seleccione Medicamento...",
        minimumInputLength: 2,
        ajax: {
            url: '{{route('generico.find')}}',
            dataType: 'json',
            data: function (params) {
                //console.log(params);
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                //console.log(data);
                return {
                    results: data
                };
            },
            cache: true
        },
        tags: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            return {
                id: term.toUpperCase()+'xnose',
                text: term.toUpperCase(),
                newTag: true, // add additional parameters
            }
        }
    });
    $('#ale_list').on('change', function (e) {
      //alert("hola");
      guardar();
    });
</script>
<script>
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
//alert("cambiar receta");
        $.ajax({
            type: 'post',
            url:"{{route('receta.update_receta_2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#final_receta").serialize(),
            success: function(data){
                //console.log("ok");
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
          success: function(data_receta){
            $('#index').empty();
            $('#index').html(data_receta);
          },
          error: function(data_receta){
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

<script type="text/javascript">
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
        //$(this).find('#imagen_solita').empty().html('');
    });
    $('#foto2').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);
    });
    $('#video').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
    $('#mprotocolo').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);
        $(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);

    });
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

    $('#mprotocolo_cpre_eco').on('hidden.bs.modal', function(){
        //alert('hola');
        //console.log(this);

        console.log("cerro");
        var editor = tinymce.get('tcphallazgos'); //the id of your textarea
        console.log(editor);
        tinymce.remove("#tcphallazgos");
        var editor = tinymce.get('tcphallazgos'); //the id of your textarea
        console.log(editor);
        //$(this).removeData('bs.modal');
        //$(this).find('.modal-content').empty().html('');
        //console.log(this);

    });

    $('#eprotocolo').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');

    });
    $('#formulario013').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');

    });

    @if(!is_null($protocolo))

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



    @endif

    function sumit_documentos(){

        $('#enviar2').click();    

    }

</script>

</section>

@include('sweet::alert')
@endsection
