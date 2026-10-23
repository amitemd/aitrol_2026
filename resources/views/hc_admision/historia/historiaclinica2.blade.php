
@extends('hc_admision.historia.base')
@section('action-content')
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
</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                <div class="box-header">
                    <div class="col-md-4">
                        <h3 class="box-title"><a href="javascript:void($('#fili').click());"><b>Filiación</b></a></h3>
                    </div>
                        
                    @php 
                        $cant_labs = DB::table('examen_orden')->where('id_paciente',$agenda->id_paciente)->count();
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

                    @if($agenda->estado_cita=='4')
                    <div class="col-md-2">
                        <a href="{{route('orden_proc.crear_editar',['hcid' => $agenda->hcid])}}">
                            <button class="btn btn-success btn-sm">Orden De Procedimiento</button>    
                        </a> 
                    </div>
                    @endif
                    <div class="col-md-2">
                        <a href="{{url('tecnicas')}}/{{$agenda->id}}">
                            <button class="btn btn-success btn-sm">Maestro de Procedimientos</button>    
                        </a> 
                    </div>
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
                                <label for="cortesia" class="control-label">Cortesia</label>
                                <select id="cortesia" name="cortesia" class="form-control input-sm" required onchange="actualiza(event);">
                                    <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                    <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                </select>    
                            </div>

                            <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
                            
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
                                   <option @if($agenda->estadocivil == 5) selected @endif value="3">Unión Libre</option> 
                                   <option @if($agenda->estadocivil == 6) selected @endif value="4">Unión de Hecho</option> 
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
                                <?php /* <!--select id="id_seguro" name="id_seguro" class="form-control input-sm" onchange="crear_select();guardar();">
                                     @foreach($seguros as $seguro)
                                    <option @if(is_null($protocolo))@if($agenda->id_seguro==$seguro->id){{'selected '}}@endif @else @if($protocolo->id_seguro==$seguro->id){{'selected '}}@endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>           
                                    @endforeach 
                                </select--> */ ?>
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
                                <label for="cortesia" class="control-label">Celular</label>
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
       
                        <div class="col-md-4" style="padding: 1px;">
                            <label for="cortesia" class="control-label">Antecedentes Patologicos</label>
                            <textarea name="antecedentes_pat" id="antecedentes_pat" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->antecedentes_pat}}</textarea>
                        </div>

                        <div class="col-md-4" style="padding: 1px;">
                            <label for="cortesia" class="control-label">Antecedentes Familiares</label>
                            <textarea name="antecedentes_fam" id="antecedentes_fam" style="width: 100%;font-size: 13px;" rows="1" onchange="guardar();">{{$agenda->antecedentes_fam}}</textarea>
                        </div>
                        
                        <div class="col-md-4" style="padding: 1px;">
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
            
            <div class="box box-primary @if(is_null($evolucion)) collapsed-box  @endif" style="margin-bottom: 5px;" >
                <div class="box-header">
                    
                    <div class="col-md-6" style="padding-left: 0px;">
                        <h3 class="box-title">
                            <b><a href="javascript:void($('#evol').click());">Evoluciones</a></b>
                        </h3>
                    </div>
                    <div class="col-md-3" style="padding-left: 0px;">
                        <a href="{{route('sin_agenda.crear_evolucion',['id' => $agenda->id_paciente, 'ag' => $agenda->id])}}"><button class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-plus"></span> Agregar Visita</button></a>
                    </div>    

                    
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
                    <h4 style="text-align: center;background-color: cyan;"><b>Historial de Evoluciones</b></h4>
                    <div @if(!is_null($evolucion)) id="div1" class="col-md-12" @else class="col-md-12" @endif style="overflow:scroll; height: 400px !important; ">
                        
                        @if($evoluciones->count()>0) 

                        @foreach($evoluciones as $value) 
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
                                    @php 
                                        $esp = DB::table('especialidad')->find($value->espid);    
                                    @endphp
                                    <td colspan="2"><b>@if($value->proc_consul=='0' || $value->proc_consul=='4')CONSULTA @if(!is_null($esp)) {{$esp->nombre}} @endif @elseif($value->proc_consul=='1')PROCEDIMIENTO: @php
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
                            </div>
                            @if(!is_null($child_pugh))  
                            <div class="col-md-6">
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
                                    <label for="inr" class="control-label">T. Protrombina % (INR)</label>
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
                            </div>
                            @endif
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
                                @if(!is_null($child_pugh))
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examen_fisico" class="control-label">Examen Fisico</label>
                                    <textarea name="examen_fisico" style="width: 100%;" rows="7" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                                </div>
                                @endif
                                
                                
                                @if($agenda->espid=='8')  
                                @php
                                    $cardiologia = DB::table('hc_cardio')->where('hcid',$evolucion->hcid)->first(); 
                                @endphp  
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

                                <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px;"><b>Diagnóstico</b></label>
                                <div class="form-group col-md-10" style="padding: 1px;">
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" @if($agenda->estado_cita!='4') readonly="yes" @endif>
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
                                */ ?>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="examenes_realizar" class="control-label">Examenes a Realizar</label>
                                    <textarea name="examenes_realizar" style="width: 100%;" rows="2" onchange="guardar_protocolo();" @if($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($evolucion)){{$agenda->examenes_realizar}}@endif</textarea>
                                </div>
                            </div>    
                        </form>  
                    </div>
                    <!-- Receta -->
                    <div class="col-md-12" style="padding-right: 6px;">
                        <h4 style="text-align: center;background-color: cyan;"><b>Receta del Paciente</b></h4>
                        <div class="col-md-12">
                            
                            <div class="col-md-5">
                            @if($hc_receta != "")   
                                <a target="_blank" href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '1']) }}" type="button" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-download-alt"> Imprimir</span>
                                </a>
                                <a target="_blank" href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-download-alt"> Imprimir Membretada</span>
                                </a>
                                <a href="{{url('medicina')}}/{{$agenda->id}}" type="button" class="btn btn-success btn-sm">
                                    <span class="glyphicon glyphicon-list-alt"> Medicinas</span>
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
                                        <textarea id="rp" name="rp" style="width: 100%" rows="10" >{{$hc_receta->rp}}</textarea>
                                    </div>
                                    <div class="col-md-6" >
                                        <span><b>Prescripcion</b></span>
                                        <textarea id="prescripcion" name="prescripcion" rows="10" style="width: 100%" >{{$hc_receta->prescripcion}}</textarea>
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
                    @endif
                </div>
            </div>
        </div>


        @if($protocolos->count()>0) 
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
                                            <td style="color: blue;"><b>{{substr($agenda->fechaini,10,10)}}</b></td>
                                            <td style="color: Cornsilk;">{{$value->id}}</td>
                                             
                                        </tr>
                                        <tr>
                                            <td><b>Procedimiento</b></td>
                                            <td colspan="5"><span style="color: blue;"><b>{{$value->nombre_general}}</b></span></td>
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
                                            <td><b>Encargado</b></td>
                                            <td><b>Dr. {{$value->d1apellido1}}</b></td>
                                            <td><b>Asistente: </b></td>
                                            <td>{{$value->d2apellido1}}</td>
                                            <td><b>Asistente: </b>
                                            <td>{{$value->d3apellido1}}</td>    
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
                        
                        <button id="nuevo_proc" type="button" onclick="Agregar_procedimiento();" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-plus"> Agregar Procedimiento</span>
                        </button>
                        
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
                        
                        
                        <a href="{{route('evolucion.imprimir', ['id' => $protocolo->id_hc_procedimientos ])}}">
                            <button type="button" class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-download-alt"> Evolucion</span>
                            </button>
                        </a>
                         
                        @endif
                    
                    </div>    

                    
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
                                
                                <select class="form-control input-sm select2" id="proc_com"  name="proc_com" onchange="Carga_proc('1');" required style="width: 100%;">
                                    <option value="">Seleccione ...</option> 
                                    @foreach($proc_completo as $value)    
                                        <option @if(!is_null($protocolo))@if($value->id == $protocolo->id_procedimiento_completo) selected @endif @endif value="{{$value->id}}">{{$value->nombre_general}}</option>
                                    @endforeach    
                                </select>
                                
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="id_doctor1" class="control-label">Doctor</label>
                                <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" required onchange="guardar_procedimiento();">
                                    @foreach($doctores as $value) 
                                    <option @if($protocolo->id_doctor1==$value->id)selected @endif value={{$value->id}}>Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>    
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
                                <label for="id_anestesiologo" class="control-label">Anestesiólogo</label>
                                <select id="id_anestesiologo" name="id_anestesiologo" class="form-control input-sm" required onchange="guardar_procedimiento();">
                                    <option value="">Seleccione ...</option>
                                    @foreach($anestesiologos as $value) 
                                    <option @if($protocolo->id_anestesiologo==$value->id)selected @endif value="{{$value->id}}">Dr. {{$value->nombre1}} {{$value->apellido1}}</option>
                                    @endforeach
                                </select>    
                            </div>

                            <div class="col-md-3" style="padding: 1px;">
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
                            </div--> */ ?>

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
                        </div><br>
                        <div class="col-md-12">&nbsp;</div>
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
                                                    </a> 
                                                @else
                                                    @php
                                                        $variable = explode('/' , asset('/hc_ima/'));
                                                        $d1 = $variable[3];
                                                        $d2 = $variable[4];
                                                        $d3 = $variable[5];
                                                        
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
                                                        $d3 = $variable[5];
                                                        
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
                                                        $d2 = $variable[4];
                                                        $d3 = $variable[5];
                                                        
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
                                                        $d2 = $variable[4];
                                                        $d3 = $variable[5];
                                                        
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
                <div class="box box-primary collapsed-box" style="margin-bottom: 5px;" >
                    <div class="box-header">
                        <div class="col-md-4">
                            <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>Receta del Paciente</b></a></h3>
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
                            
                            <div class="col-md-5">
                            @if($hc_receta != "")   
                                <a target="_blank" href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '1']) }}" type="button" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-download-alt"> Imprimir</span>
                                </a>
                                <a target="_blank" href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-download-alt"> Imprimir Membretada</span>
                                </a>
                                <a href="{{url('medicina')}}/{{$agenda->id}}" type="button" class="btn btn-success btn-sm">
                                    <span class="glyphicon glyphicon-list-alt"> Medicinas</span>
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
                                        <textarea id="rp" name="rp" style="width: 100%" rows="10" >{{$hc_receta->rp}}</textarea>
                                    </div>
                                    <div class="col-md-6" >
                                        <span><b>Prescripcion</b></span>
                                        <textarea id="prescripcion" name="prescripcion" rows="10" style="width: 100%" >{{$hc_receta->prescripcion}}</textarea>
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
            @endif    
        @endif        
    </div>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });
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
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                                           
                    });

                }
            })    
    }
    @endif

    @if(!is_null($protocolo))
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
                        cell4.innerHTML = '<a href="javascript:eliminar('+value.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                                           
                    });

                }
            })    
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
        selector: '#thallazgos',
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
 



    function guardar(){

        //alert("ok");
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

                    @if(!is_null($evolucion))
                    guardar_cie10();
                    @endif
                    
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

    

    @if(!is_null($evolucion))
    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': null, 'hcid': {{$evolucion->hcid}}, 'hc_id_procedimiento': {{$evolucion->hc_id_procedimiento}}, 'in_eg': null },
            success: function(data){
                

                var indexr = data.count-1 
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                   
               
                
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
                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

                   
               
                
            },
            error: function(data){
                    
                }
        })
    }
    @endif

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
        }
    });
    $('#ale_list').on('change', function (e) {
      //alert("hola");
      guardar();
    });
</script>                     
<script>
    $("#limpiar").click( function(){
        $('#nombre_generico').val(''); 
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
            $('#index').empty().html(data);
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
