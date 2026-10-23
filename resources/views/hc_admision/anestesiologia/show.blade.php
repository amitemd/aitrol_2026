@extends('hc_admision.anestesiologia.base')

@section('action-content')
<style type="text/css">
    .table>tbody>tr>td,
    .table>tbody>tr>th {
        padding: 0.4%;
    }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">
    .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
</style>

<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog" role="document" style="width:1350px; ">
        <div class="modal-content" id="imprimir3">
            <p>Hola Mundo</p>
        </div>
    </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog" role="document" style="width:70%;">
        <div class="modal-content">
        </div>
    </div>
</div>
<style>
    .right {
        text-align: right;
    }
</style>
<input type="hidden" id="record_anestesiologico" value="{{$record_anestesilogico[0]->id}}">
<input type="hidden" value="@if($image != null){{$image->id}}  @endif" id="imagen">
<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <!--   <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-triangle-left"></span> Procedimientos</button>
                    </a> -->
                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><b>Paciente:</b></td>
                                <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                <td><b>Edad:</b></td>
                                <td><span id="edad"></span></td>
                                <td><b>Seguro:</b></td>
                                <td>{{$seguro->nombre}}</td>
                            </tr>
                            <tr>

                                <td><b>Procedimientos:</b></td>

                              @php
                              $proc_fin = $hc_procedimientos->hc_procedimiento_f;
                              $nombre_proc= "";
                              foreach($proc_fin as $pf){

                                $procedimiento = \Sis_medico\Procedimiento::where('id', $pf->id_procedimiento)->first();
                                //dd($procedimiento);
                               $nombre_proc= "{$procedimiento->nombre} + {$nombre_proc}";

                              }
                                    $alergiasxpac = \Sis_medico\Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
                              @endphp

                                <td colspan="3">{{substr($nombre_proc, 0, -2)}}</td>
                                <td><b>Alergias:</b></td>
                                <td>
                                    @if($alergiasxpac->count()==0)
                                    <b>NO TIENE </b>
                                    @else
                                    @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
                                    @endforeach
                                    @endif 
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="box ">
                        <div class="box-header with-border">
                            <div class="col-md-5">
                                <h4>RECORD ANESTÉSICO DEL PACIENTE</h4>

                            </div>
                            @php
                                $user_mod = null;
                                if($record_anestesilogico !='[]' ){
                                    $user_mod = \Sis_medico\User::find( $record_anestesilogico[0]->id_usuariomod );
                                }
                            @endphp
                            <div class="col-md-4">
                                @if(!is_null($user_mod))
                                    <span><b>Actualizado Por:</b> {{$user_mod->apellido1}} - {{$record_anestesilogico[0]->updated_at}}</span>
                                @endif
                            </div>
			    
                            
                            
                            @if($record_anestesilogico != '[]')
                            <div class="col-md-3">
                                <a style="margin: 10px" class="btn btn-info btn_accion" href="{{ route('formato.rc_preanestesico_crear', ['id' => $record_anestesilogico[0]->id]) }}"> <i class="fa fa-medkit"></i>&nbsp;Pre Anestésico</a>
                            </div>
                            
                            <div class="col-md-3">
                                <!--a id="previsualizar" onclick="save2()" href="{{ route('anestesiologia.imprime', ['id' => $id]) }}" target="_blank" type="button" class="btn btn-primary">
                                    Previsualizar
                                </a-->
                                <a id="previsualizar" href="{{ route('anestesiologia.imprime', ['id' => $id]) }}" target="_blank" type="button" class="btn btn-primary">
                                    Previsualizar
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="box-body">

                            <form class="form-vertical" id="formx" role="form" method="POST" action="{{ route('anestesiologia.crea_actualiza', ['hc_id' => $hca[0]->hcid ]) }}">

                                <input type="hidden" name="id_hc_procedimientos" value="{{ $id }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <!--doctor-->
                                <div class="form-group col-md-4{{ $errors->has('id_doctor') ? ' has-error' : '' }}">
                                    <label for="id_doctor" class="col-md-12 control-label">Doctor</label>
                                    <select id="id_doctor" class="form-control input-sm" name="id_doctor" required>
                                        <option  value="">Seleccione ...</option>
                                        @foreach($usuarios as $doctor)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_doctor == $doctor->id) selected @endif @endif value="{{$doctor->id}}">Dr. {{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                                        @if($record_anestesilogico !='[]' )
                                            @if($record_anestesilogico[0]->id_doctor == null)
                                                <option @if($doctor->id == '1307189140') selected @endif value="{{$doctor->id}}">Dr. {{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>    
                                            @endif
                                        @else
                                            <option @if($doctor->id == '1307189140') selected @endif value="{{$doctor->id}}">Dr. {{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--doctor ayudante-->
                                <div class="form-group col-md-4{{ $errors->has('id_doctor_ayudante') ? ' has-error' : '' }}">
                                    <label for="id_doctor_ayudante" class="col-md-12 control-label">Doctor Ayudante</label>
                                    <select id="id_doctor_ayudante" class="form-control input-sm" name="id_doctor_ayudante" required>
                                        <option  value="">Seleccione ...</option>
                                        @foreach($usuarios as $doctor_ayudante)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_doctor_ayudante == $doctor_ayudante->id) selected @endif @endif value="{{$doctor_ayudante->id}}">Dr. {{$doctor_ayudante->apellido1}} {{$doctor_ayudante->apellido2}} {{$doctor_ayudante->nombre1}}</option>
                                        @if($record_anestesilogico !='[]' )
                                            @if($record_anestesilogico[0]->id_doctor_ayudante == null)
                                                <option @if($hca[0]->id_doctor1 == $doctor_ayudante->id) selected @endif value="{{$doctor_ayudante->id}}">Dr. {{$doctor_ayudante->apellido1}} {{$doctor_ayudante->apellido2}} {{$doctor_ayudante->nombre1}}</option>    
                                            @endif
                                        @else
                                            <!-- <option @if($hca[0]->id_doctor1 == $doctor_ayudante->id) selected @endif value="{{$doctor_ayudante->id}}">Dr. {{$doctor_ayudante->apellido1}} {{$doctor_ayudante->apellido2}} {{$doctor_ayudante->nombre1}}</option> -->
                                        @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor_ayudante'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor_ayudante') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('fecha') ? ' has-error' : '' }}">
                                    <label for="fecha" class="col-md-12 control-label">Fecha</label>
                                    <input type="datetime-local" id="fecha" required class="form-control input-sm" name="fecha" value="@if($record_anestesilogico !='[]' ){{ substr($record_anestesilogico[0]->fecha,0,10) }}T{{substr($record_anestesilogico[0]->fecha,11,5) }}@endif">
                                    @if ($errors->has('fecha'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--anestesiologo-->

                                <div class="form-group col-md-4{{ $errors->has('id_anestesiologo') ? ' has-error' : '' }}">
                                    <label for="id_anestesiologo" class="col-md-12 control-label">Anestesiólogo</label>
                                    <select id="id_anestesiologo" class="form-control input-sm" name="id_anestesiologo" required>
                                        <option value="">Seleccione..</option>
                                        @foreach($anestesiologos as $anestesiologo)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_anestesiologo == $anestesiologo->id) selected @endif @endif value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_anestesiologo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_anestesiologo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('id_ayudante') ? ' has-error' : '' }}">
                                    <label for="id_ayudante" class="col-md-12 control-label">Ayudante de Anestesiologia</label>
                                    <select id="id_ayudante" class="form-control input-sm" name="id_ayudante" required>
                                        <option value="">Seleccione..</option>
                                        @foreach($anestesiologos as $anestesiologo)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_ayudante == $anestesiologo->id) selected="selected" @endif @endif value="{{$anestesiologo->id}}">@if($anestesiologo->id == "1203240658") Lcda. @else Dr. @endif  {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_ayudante'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_ayudante') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('id_instrumentista') ? ' has-error' : '' }}">
                                    <label for="id_instrumentista" class="col-md-12 control-label">Instrumentistas</label>
                                    <select id="id_instrumentista" class="form-control input-sm" name="id_instrumentista">
                                        <option value="">Seleccione..</option>
                                        @foreach($enfermeros as $value)
                                        <option value="{{$value->id}}" @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_instrumentista == $value->id) selected @endif @endif >Enf. {{$value->nombre1}} {{$value->apellido1}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_instrumentista'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_instrumentista') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <!--record-->

                                <div class="form-group col-md-4{{ $errors->has('id_tipoanesteciologia') ? ' has-error' : '' }}">
                                    <label for="id_tipoanesteciologia" class="col-md-12 control-label">Tipo Anestesiologia</label>
                                    <select id="id_tipoanesteciologia" class="form-control input-sm" name="id_tipoanesteciologia" required>
                                        <option value="">Seleccionar ..</option>
                                        @foreach($tipo_anesteciologia as $value)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_tipoanestesiologia == $value->id) selected="selected" @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_tipoanesteciologia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipoanesteciologia') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('operacion_propuesta') ? ' has-error' : '' }}">
                                    <label for="operacion_propuesta" class="col-md-12 control-label">Operacion Propuesta</label>
                                    <input id="operacion_propuesta" required class="form-control input-sm" name="operacion_propuesta" value="@if(old('operacion_propuesta')!='')" {{old('operacion_propuesta')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->operacion_propuesta }}@endif">
                                    @if ($errors->has('operacion_propuesta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('operacion_propuesta') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('operacion_realizada') ? ' has-error' : '' }}">
                                    <label for="operacion_realizada" class="col-md-12 control-label">Operacion Realizada</label>
                                    <input id="operacion_realizada" required class="form-control input-sm" name="operacion_realizada" value="@if(old('operacion_realizada')!='')" {{old('operacion_realizada')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->operacion_realizada }}@endif">
                                    @if ($errors->has('operacion_realizada'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('operacion_realizada') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('diagnostico_preoperatorio') ? ' has-error' : '' }}">
                                    <label for="diagnostico_preoperatorio" class="col-md-12 control-label">Diagnostico Preoperatorio</label>
                                    <input id="diagnostico_preoperatorio" required class="form-control input-sm" name="diagnostico_preoperatorio" value="@if(old('diagnostico_preoperatorio')!='')" {{old('diagnostico_preoperatorio')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->diagnostico_preoperatorio }}@endif">
                                    @if ($errors->has('diagnostico_preoperatorio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('diagnostico_preoperatorio') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('diagnostico_postoperatorio') ? ' has-error' : '' }}">
                                    <label for="diagnostico_postoperatorio" required class="col-md-12 control-label">Diagnostico Postoperatorio</label>
                                    <input id="diagnostico_postoperatorio" required class="form-control input-sm" name="diagnostico_postoperatorio" value="@if(old('diagnostico_postoperatorio')!='')" {{old('diagnostico_postoperatorio')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->diagnostico_postoperatorio }}@endif">
                                    @if ($errors->has('diagnostico_postoperatorio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('diagnostico_postoperatorio') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--dextrosa-->
                                <div class="col-md-2 {{ $errors->has('dextrosa') ? ' has-error' : '' }}">
                                    <label for="dextrosa" class="col-md-12 control-label">Dextrosa (cc)</label>
                                    <input id="dextrosa" min=0 type="number" step="any" class="form-control input-sm" name="dextrosa" value=@if(old('dextrosa')!='' )"{{old('dextrosa')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->dextrosa }}" @else "{{0}}" @endif>
                                    @if ($errors->has('dextrosa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dextrosa') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--cloruro_sodio-->
                                <div class="col-md-2 {{ $errors->has('cloruro_sodio') ? ' has-error' : '' }}">
                                    <label for="cloruro_sodio" class="col-md-12 control-label">Cloruro de Sodio (cc)</label>
                                    <input id="cloruro_sodio" min=0 type="number" step="any" class="form-control input-sm" name="cloruro_sodio" value=@if(old('cloruro_sodio')!='' )"{{old('cloruro_sodio')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->cloruro_sodio }}" @else "{{0}}" @endif>
                                    @if ($errors->has('cloruro_sodio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cloruro_sodio') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-12">&nbsp;</div>
                                <!--lactato_ringer-->
                                <div class="col-md-2 {{ $errors->has('lactato_ringer') ? ' has-error' : '' }}">
                                    <label for="lactato_ringer" class="col-md-12 control-label" style="font-size: 14px;">Lactato de Ringer (cc)</label>
                                    <input id="lactato_ringer" min=0 type="number" step="any" class="form-control input-sm" name="lactato_ringer" value=@if(old('lactato_ringer')!='' )"{{old('lactato_ringer')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->lactato_ringer }}" @else "{{0}}" @endif>
                                    @if ($errors->has('lactato_ringer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lactato_ringer') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--sangre_derivados-->
                                <div class="col-md-2 {{ $errors->has('sangre_derivados') ? ' has-error' : '' }}">
                                    <label for="sangre_derivados" class="col-md-12 control-label" style="font-size: 13px;">Sangre o Derivados (cc)</label>
                                    <input id="sangre_derivados" min=0 type="number" step="any" class="form-control input-sm" name="sangre_derivados" value=@if(old('sangre_derivados')!='' )"{{old('sangre_derivados')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->sangre_derivados }}" @else "{{0}}" @endif>
                                    @if ($errors->has('sangre_derivados'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sangre_derivados') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--expansores-->
                                <div class="col-md-2{{ $errors->has('expansores') ? ' has-error' : '' }}">
                                    <label for="expansores" class="col-md-12 control-label"">Expansores (cc)</label>
                                        <input id=" expansores" min=0 type="number" step="any" class="form-control input-sm" name="expansores" value=@if(old('expansores')!='' )"{{old('expansores')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->expansores }}" @else "{{0}}" @endif>
                                        @if ($errors->has('expansores'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('expansores') }}</strong>
                                        </span>
                                        @endif
                                </div>
                                <div class="col-md-2{{ $errors->has('total') ? ' has-error' : '' }}">
                                    <label for="total" class="col-md-12 control-label"">Total</label>
                                        <input id=" total" min=0 type="number" step="any" class="form-control input-sm" name="total" value=@if(old('total')!='' )"{{old('total')}}"@elseif($record_anestesilogico !='[]' )"{{ $record_anestesilogico[0]->total }}" @else "{{0}}" @endif>
                                        @if ($errors->has('total'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('total') }}</strong>
                                        </span>
                                        @endif
                                </div>
                                <!--tecnicas_especiales-->
                                <div class="form-group col-md-4{{ $errors->has('tecnicas_especiales') ? ' has-error' : '' }}">
                                    <label for="tecnicas_especiales" class="col-md-12 control-label">Tecnicas Especiales</label>
                                    <input id="tecnicas_especiales" class="form-control input-sm" name="tecnicas_especiales" value="@if(old('tecnicas_especiales')!='')" {{old('tecnicas_especiales')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->tecnicas_especiales }}@endif">
                                    @if ($errors->has('tecnicas_especiales'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tecnicas_especiales') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--id_sala-->
                                <div class="form-group col-md-4{{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                    <label for="id_sala" class="col-md-12 control-label">Conducido A</label>
                                    <select id="id_sala" class="form-control input-sm" name="id_sala" required>
                                        <option value="">Seleccione..</option>
                                        @foreach($salas as $value)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_sala == $value->id) selected="selected" @endif @endif value="{{$value->id}}">{{$value->nombre_sala}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--id_guiado-->
                                <div class="form-group col-md-4{{ $errors->has('id_guiado') ? ' has-error' : '' }}">
                                    <label for="id_guiado" class="col-md-12 control-label">Guiado por</label>
                                    <select id="id_guiado" class="form-control input-sm" name="id_guiado" required>
                                        @foreach($anestesiologos as $anestesiologo)
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->id_guiado == $anestesiologo->id) selected="selected" @endif @endif  value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_guiado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_guiado') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--hora-->
                                <div class="form-group col-md-2{{ $errors->has('hora') ? ' has-error' : '' }}">
                                    <label for="hora" class="col-md-12 control-label">Hora de Traslado</label>
                                    <input id="hora" required type="text" class="form-control input-sm" name="hora" value=@if(old('hora')!='' )"{{old('hora')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->hora }}@endif>
                                    @if ($errors->has('hora'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hora') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3{{ $errors->has('servicio') ? ' has-error' : '' }}">
                                    <label for="servicio" class="col-md-12 control-label" >Servicio</label>
                                    <input id="servicio" class="form-control input-sm" name="servicio" value="@if(old('servicio')!='')"{{old('servicio')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->servicio }}@endif" >
                                    @if ($errors->has('servicio'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('servicio') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3{{ $errors->has('sala') ? ' has-error' : '' }}">
                                    <label for="sala" class="col-md-12 control-label" >Sala</label>
                                    <input id="sala" class="form-control input-sm" name="sala" value="@if(old('sala')!='')"{{old('sala')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->sala }}@endif" >
                                    @if ($errors->has('sala'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('sala') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-3{{ $errors->has('cama') ? ' has-error' : '' }}">
                                    <label for="cama" class="col-md-12 control-label" >Cama</label>
                                    <input id="cama" class="form-control input-sm" name="cama" value="@if(old('cama')!='')"{{old('cama')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->cama }}@endif" >
                                    @if ($errors->has('cama'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('cama') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--comentarios-->
                                <div class="form-group col-md-10{{ $errors->has('comentarios') ? ' has-error' : '' }}">
                                    <label for="comentarios" class="col-md-12 control-label">Comentarios</label>
                                    <input required id="comentarios" class="form-control input-sm" name="comentarios" value="@if(old('comentarios')!='')" {{old('comentarios')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->comentarios }}@endif">
                                    @if ($errors->has('comentarios'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comentarios') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group col-md-12">
                                    <h2 class="box-title"></h2>
                                </div>
                                <div class="form-group col-md-12">
                                    <h2 class="box-title">TECNICAS</h2>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                                    <thead>
                                                        <tr role="row" style="background-color: #00bfff;">
                                                            <th colspan="4" width="90%"><b>General</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($general as $value)
                                                        <tr role="row">
                                                            <td>{{$value->nombre}}</td>
                                                            <td><input name="lista[]" @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}">
                                                            @if($value->nombre == 'Tubo n') <input type="text" name="tubo" @if($record_anestesilogico != '[]') value="{{ $record_anestesilogico[0]->tubo }}" @endif> @endif
                                                            </td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                                    <thead>
                                                        <tr role="row" style="background-color: #00bfff;">
                                                            <th colspan="4" width="90%"><b>Conductiva</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($conductiva as $value)
                                                        <tr role="row">
                                                            <td>{{$value->nombre}}</td>
                                                            <td><input name="lista[]" type="checkbox" class="flat-green" @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}"></td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                            <div class="table-responsive">
                                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                                    <thead>
                                                        <tr role="row" style="background-color: #00bfff;">
                                                            <th colspan="4" width="90%"><b>Complicaciones Operatorias</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($complicaciones as $value)
                                                        <tr role="row">
                                                            <td>{{$value->nombre}}</td>
                                                            <td><input name="lista[]" type="checkbox" class="flat-green" @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}"></td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <h2 class="box-title"></h2>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="row">

                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-12">
                                                    <label>HR.</label>
                                                </div>
                                                <div class="col-md-12">
                                                    <label>Ingrese Texto</label>
                                                </div>
                                                <div class="col-md-12">
                                                    <input class="form-control" type="text" id="text" placeholder="Ingresar texto" />
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label>MAX</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                <button type="button" class="btn btn-default btn-xs" id="max"><img height="20" src="{{asset('max_a.ico')}}" /></button>
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label>MIN</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                <button type="button" class="btn btn-default btn-xs" id="min"><img height="20" src="{{asset('min_a.ico')}}" /></button>
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label> PULSO</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                    <button type="button" class="btn btn-default btn-xs" id="circ"><img height="20" src="{{asset('circle_a.ico')}}" /></button>
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label> INDUCCION</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                    <button type="button" class="btn btn-default btn-xs" id="circsmall"><img height="20" src="{{asset('circle_a.ico')}}" /></button>
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label> INCISION</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                    <button type="button"  class="btn btn-default btn-xs" id="rect"><img height="20" src="https://img.icons8.com/material/24/000000/square.png" /></button>
                                                </div>
                                                <div class="col-md-12">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-6">
                                                    <label>F.N ANESTESIA</label>
                                                </div>
                                                <div class="col-md-6 right">
                                                    <button type="button"  class="btn btn-default btn-xs" id="circlex"><img height="20" src="{{asset('circlex.ico')}}" /></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <!--  <button id="rect">Rect</button>

                                            <button id="save">Save</button> -->
                                            <div class="row">
                                                <div class="col-md-1">

                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" id="drawing-mode" class="btn btn-primary"><i class="fa fa-pencil"></i></button>
                                                    <button type="button" id="delete_select" class="btn btn-primary"> <i class="fa fa-trash"></i> Borrar Seleccionado </button>
                                                    <button type="button" id="clear-canvas" class="btn btn-warning"> <i class="fa fa-remove"></i> Borrar</button><br>
                                                </div>
                                                <div class="col-md-8">
                                                    &nbsp;
                                                </div>
                                                <div class="col-md-12">
                                                    <canvas id="c" width="1450" height="500"></canvas>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <br>
                                    <br>

                                <div class="form-group col-md-12">
                                    <label class="col-md-12 control-label">Drogas Administradas</label>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d1') ? ' has-error' : '' }}">
                                    <label for="d1"  class="col-md-2 control-label">1</label>
                                    <div class="col-md-10">
                                        <input id="d1"  class="form-control input-sm" name="d1" value="@if(old('d1')!='')" {{old('d1')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d1 }}@endif">
                                        @if ($errors->has('d1'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d1') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d2') ? ' has-error' : '' }}">
                                    <label for="d2"  class="col-md-2 control-label">2</label>
                                    <div class="col-md-10">
                                        <input id="d2"  class="form-control input-sm" name="d2" value="@if(old('d2')!='')" {{old('d2')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d2 }}@endif">
                                        @if ($errors->has('d2'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d2') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d3') ? ' has-error' : '' }}">
                                    <label for="d3"  class="col-md-2 control-label">3</label>
                                    <div class="col-md-10">
                                        <input id="d3"  class="form-control input-sm" name="d3" value="@if(old('d3')!='')" {{old('d3')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d3 }}@endif">
                                        @if ($errors->has('d3'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d3') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d4') ? ' has-error' : '' }}">
                                    <label for="d4"  class="col-md-2 control-label">4</label>
                                    <div class="col-md-10">
                                        <input id="d4"  class="form-control input-sm" name="d4" value="@if(old('d4')!='')" {{old('d4')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d4 }}@endif">
                                        @if ($errors->has('d4'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d4') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d5') ? ' has-error' : '' }}">
                                    <label for="d5"  class="col-md-2 control-label">5</label>
                                    <div class="col-md-10">
                                        <input id="d5"  class="form-control input-sm" name="d5" value="@if(old('d5')!='')" {{old('d5')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d5 }}@endif">
                                        @if ($errors->has('d5'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d5') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d6') ? ' has-error' : '' }}">
                                    <label for="d6"  class="col-md-2 control-label">6</label>
                                    <div class="col-md-10">
                                        <input id="d6"  class="form-control input-sm" name="d6" value="@if(old('d6')!='')" {{old('d6')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d6 }}@endif">
                                        @if ($errors->has('d6'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d6') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d7') ? ' has-error' : '' }}">
                                    <label for="d7"  class="col-md-2 control-label">7</label>
                                    <div class="col-md-10">
                                        <input id="d7"  class="form-control input-sm" name="d7" value="@if(old('d7')!='')" {{old('d7')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d7 }}@endif">
                                        @if ($errors->has('d7'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d7') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d8') ? ' has-error' : '' }}">
                                    <label for="d8"  class="col-md-2 control-label">8</label>
                                    <div class="col-md-10">
                                        <input id="d8"  class="form-control input-sm" name="d8" value="@if(old('d8')!='')" {{old('d8')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d8 }}@endif">
                                        @if ($errors->has('d8'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d8') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d9') ? ' has-error' : '' }}">
                                    <label for="d9"  class="col-md-2 control-label">9</label>
                                    <div class="col-md-10">
                                        <input id="d9"  class="form-control input-sm" name="d9" value="@if(old('d9')!='')" {{old('d9')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d9 }}@endif">
                                        @if ($errors->has('d9'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d9') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d10') ? ' has-error' : '' }}">
                                    <label for="d10"  class="col-md-2 control-label">10</label>
                                    <div class="col-md-10">
                                        <input id="d10"  class="form-control input-sm" name="d10" value="@if(old('d10')!='')" {{old('d10')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d10 }}@endif">
                                        @if ($errors->has('d10'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d10') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-3 {{ $errors->has('d11') ? ' has-error' : '' }}">
                                    <label for="d11"  class="col-md-2 control-label">11</label>
                                    <div class="col-md-10">
                                        <input id="d11"  class="form-control input-sm" name="d11" value="@if(old('d11')!='')" {{old('d11')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->d11 }}@endif">
                                        @if ($errors->has('d11'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('d11') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-12 ">
                                    <label for="d1"  class="col-md-2 control-label">&nbsp;</label>

                                </div>


                                    <div class="form-group col-md-2{{ $errors->has('duracion_anestesia') ? ' has-error' : '' }}">
                                        <label for="duracion_anestesia" class="col-md-12 control-label">Duracion Anestesia</label>
                                        <input id="duracion_anestesia" required type="text" class="form-control input-sm" name="duracion_anestesia" value=@if(old('duracion_anestesia')!='' )"{{old('duracion_anestesia')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->duracion_anestesia }}@endif>
                                        @if ($errors->has('duracion_anestesia'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('duracion_anestesia') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-2{{ $errors->has('duracion_operacion') ? ' has-error' : '' }}">
                                        <label for="duracion_operacion" class="col-md-12 control-label">Duracion Operacion</label>
                                        <input id="duracion_operacion" required type="text" class="form-control input-sm" name="duracion_operacion" value=@if(old('duracion_operacion')!='' )"{{old('duracion_operacion')}}"@elseif($record_anestesilogico !='[]' ){{ $record_anestesilogico[0]->duracion_operacion }}@endif>
                                        @if ($errors->has('duracion_operacion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('duracion_operacion') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                </div>
                                <div class="form-group col-md-12">
                                    <h2 class="box-title">VALORACION POST-ANESTESICA</h2>
                                </div>
                                <!--sistema_circulatorio-->
                                <div class="form-group col-md-4{{ $errors->has('sistema_circulatorio') ? ' has-error' : '' }}">
                                    <label for="sistema_circulatorio" class="col-md-12 control-label">Sistema Circulatorio</label>
                                    <select id="sistema_circulatorio" class="form-control input-sm" name="sistema_circulatorio" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->sistema_circulatorio == 2) selected="selected" @endif @endif value="2">PA 20% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->sistema_circulatorio == 1) selected="selected" @endif @endif value="1">PA 20 - 49% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->sistema_circulatorio == 0) selected="selected" @endif @endif value="0">PA 50% NIVEL PRE-ANEST&Eacute;SICO</option>
                                    </select>
                                    @if ($errors->has('sistema_circulatorio'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sistema_circulatorio') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--conciencia-->
                                <div class="form-group col-md-4{{ $errors->has('conciencia') ? ' has-error' : '' }}">
                                    <label for="conciencia" class="col-md-12 control-label">Conciencia</label>
                                    <select id="conciencia" class="form-control input-sm" name="conciencia" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->conciencia == 2) selected="selected" @endif @endif value="2">DESPIERTO</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->conciencia == 1) selected="selected" @endif @endif value="1">DESPIERTO AL LLAMADO</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->conciencia == 0) selected="selected" @endif @endif value="0">NO RESPONDE +</option>
                                    </select>
                                    @if ($errors->has('conciencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('conciencia') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--saturacion-->
                                <div class="form-group col-md-4{{ $errors->has('saturacion') ? ' has-error' : '' }}">
                                    <label for="saturacion" class="col-md-12 control-label">Saturacion</label>
                                    <select id="saturacion" class="form-control input-sm" name="saturacion" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->saturacion == 2) selected="selected" @endif @endif value="2">SAT 02 + 92% AIRE AMBIENTE</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->saturacion == 1) selected="selected" @endif @endif value="1">NECESITA 02 SAT&gt;90%</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->saturacion == 0) selected="selected" @endif @endif value="0">SAT 02&lt;90% con 02</option>
                                    </select>
                                    @if ($errors->has('saturacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('saturacion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--actividades-->
                                <div class="form-group col-md-6{{ $errors->has('actividades') ? ' has-error' : '' }}">
                                    <label for="actividades" class="col-md-12 control-label">Actividades</label>
                                    <select id="actividades" class="form-control input-sm" name="actividades" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->actividades == 2) selected="selected" @endif @endif value="2">CAPAZ DE MOVER LAS CUATRO EXTREMIDADES VOLUNTARIO O BAJO ORDENES</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->actividades == 1) selected="selected" @endif @endif value="1">CAPAZ DE MOVER LAS DOS EXTREMIDADES VOLUNTARIOS O BAJO ORDENES</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->actividades == 0) selected="selected" @endif @endif value="0">CAPAZ DE MOVER UNA EXTREMIDAD VOLUNTARIO O BAJO ORDENES</option>
                                    </select>
                                    @if ($errors->has('actividades'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('actividades') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--respiracion-->
                                <div class="form-group col-md-6{{ $errors->has('respiracion') ? ' has-error' : '' }}">
                                    <label for="respiracion" class="col-md-12 control-label">Respiracion</label>
                                    <select id="respiracion" class="form-control input-sm" name="respiracion" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->respiracion == 2) selected="selected" @endif @endif value="2">CAPAZ DE RESPIRAR PROFUNDAMENTE Y TOSER</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->respiracion == 1) selected="selected" @endif @endif value="1">APNEA RESPIRACION LIMITADA O TAQUIPMEA</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->respiracion == 0) selected="selected" @endif @endif value="0">APNEICO O CON RESPIRADOR ARTIFICIAL</option>
                                    </select>
                                    @if ($errors->has('respiracion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('respiracion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--tipo_analgesia-->
                                <div class="form-group col-md-6{{ $errors->has('tipo_analgesia') ? ' has-error' : '' }}">
                                    <label for="tipo_analgesia" class="col-md-12 control-label">Tipo de Analgesia</label>
                                    <select id="tipo_analgesia" class="form-control input-sm" name="tipo_analgesia" required>
                                        <option value="">Seleccione..</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->tipo_analgesia == 2) selected="selected" @endif @endif value="2">ANALGESIA INTRAVENOSA</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->tipo_analgesia == 1) selected="selected" @endif @endif value="1">ANALGESIA PERIDURAL</option>
                                        <option @if($record_anestesilogico !='[]' ) @if($record_anestesilogico[0]->tipo_analgesia == 0) selected="selected" @endif @endif value="0">ANALGESIA POR PRN</option>
                                    </select>
                                    @if ($errors->has('tipo_analgesia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo_analgesia') }}</strong>
                                    </span>
                                    @endif
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12" style="text-align: center;">
                                        <button onclick="save()" type="button" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Guardar
                                        </button>
                                    </div>
                                </div>

                            </form>
                            @if($record_anestesilogico != '[]')
                            <div class="form-group col-md-12">
                                <h2 class="box-title"></h2>
                            </div>
                            <div class="form-group col-md-12">
                                <h2 class="box-title">CSV</h2>
                                <div class="col-md-6 col-md-offset-9">
                                    <a class="btn btn-primary" data-toggle="modal" data-target="#favoritesModal" href="{{ route('anestesiologia.mostrarcsv', ['id' => $record_anestesilogico[0]->id, 'agenda' => $id]) }}">Añadir Nuevo CSV</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr>
                                                <th>Hora</th>
                                                <th>Presion Arterial</th>
                                                <th>Pulso</th>
                                                <th>Respiracion</th>
                                                <th>Oxigeno</th>
                                                <th>Orina cc</th>
                                                <th>Temperatura</th>
                                                <th>Anotaciones</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($csv as $value)
                                            <tr>
                                              <td>
                                                  <div style="display:flex">
                                                     <input type="text" onblur="EditarT('{{$value->id}}')" id="hora{{$value->id}}" value="{{$value->hora}}" class="form-control" style="width:60%">
                                                     <i style="cursor: pointer;margin:8px;font-size:21px" class="fa fa-floppy-o" aria-hidden="true"></i>
                                                  </div>
                                              </td>
                                                <td>{{ $value->presion_arterial }}</td>
                                                <td>{{ $value->pulso }}</td>
                                                <td>{{ $value->respiracion }}</td>
                                                <td>{{ $value->o2 }}</td>
                                                <td>{{ $value->orina }}</td>
                                                <td>{{ $value->Temperatura }}</td>
                                                <td>{{ $value->anotaciones }}</td>
                                                <td><button class="btn btn-danger" onclick="Eliminar_csv('{{$value->id}}')">Eliminar</button></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>



                    <script>

                    </script>
                </div>
            </div>
        </div>




    </div>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>
<script>
    $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });

    $(document).ready(function() {



        $('#favoritesModal2').on('hidden.bs.modal', function() {
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function() {
            $(this).removeData('bs.modal');
        });

        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>') + "años";

        $('#edad').text(edad);


        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false
        });


    });

    var drogasadministradas = function() {

        var id_record = document.getElementById('id_record').value;

        //console.log(unix);
        $.ajax({
            type: 'get',
            url: '{{ url('historia/drogasadministradas')}}/'+id_record, //historia.drogasadministradas
            success: function(data) {
                console.log(data);
                $('#drogas').empty().html(data);
            }
        })

    };

    var Eliminar_csv = function(id) {
        var confirmar = confirm("Seguro, Desea eliminar Csv");
        if(confirmar){
            $.ajax({
                type: 'get',
                url: '{{ url('nuevo_record/csv/eliminar')}}/'+id, //historia.drogasadministradas
                success: function(data) {
                    
                    location.reload();
                }
            })
        }    
    };
        
    function save(){
        $.ajax({
            type: 'post',
            url: "{{ route('anestesiologia.crea_actualiza', ['hc_id' => $hca[0]->hcid ]) }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formx").serialize(),
            success: function(data) {
                //console.log(data);
                tosaveCanvas(data);


            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function save2(){
        $.ajax({
            type: 'post',
            url: "{{ route('anestesiologia.crea_actualiza', ['hc_id' => $hca[0]->hcid ]) }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formx").serialize(),
            success: function(data) {
                //console.log(data);
                tosaveCanvas2(data);


            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function tosaveCanvas2(id){
        var canvas= document.getElementById('c');
        var blob = canvas.toDataURL();

        //console.log(blob);
        // PREPARE FORM DATA TO SEND VIA POST
        //ar formData = new FormData();
        //formData.append('croppedImage', blob, 'sampleimage.png');

        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('my-file', blob, 'filename.png');

            // Post via axios or other transport method
            $.ajax({
                url: "{{route('anestesiologia.saveCanvas')}}?id=" + id, // upload url
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function(data) {

                    console.log(data)
                    //location.reload();

                },
                error: function(xhr, status, error) {
                    alert('Error, contactase con el programador code:'+status);
                }
            });
        });
    }
    var canvas = new fabric.Canvas('c', {
        isDrawingMode: false
    });
    @if($image!=null)
    fabric.Image.fromURL('{{asset("hc_ima")}}/{{$image->url_imagen}}', function (img) {
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
            scaleX: canvas.width / img.width,
            scaleY: canvas.height / img.height
        });
    });
    @else
    fabric.Image.fromURL("{{asset('record_a.png')}}", function (img) {
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
            scaleX: canvas.width / img.width,
            scaleY: canvas.height / img.height
        });
    });
    @endif
    fabric.Object.prototype.transparentCorners = false;
    var a = function(id) {
        return document.getElementById(id);
    };
    var drawingModeEl = a('drawing-mode'),
        drawingOptionsEl = a('drawing-mode-options'),
        drawingColorEl = a('drawing-color'),
        drawingShadowColorEl = a('drawing-shadow-color'),
        drawingLineWidthEl = a('drawing-line-width'),
        drawingShadowWidth = a('drawing-shadow-width'),
        drawingShadowOffset = a('drawing-shadow-offset'),
        clearEl = a('clear-canvas');

    clearEl.onclick = function() {
        canvas.clear();
        fabric.Image.fromURL("{{asset('record_a.png')}}", function (img) {
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
            scaleX: canvas.width / img.width,
            scaleY: canvas.height / img.height
        });
        });
    };
    var clxz= a('delete_select');
    clxz.onclick= function(){
        deleteSelectedObjectsFromCanvas()
    }
    drawingModeEl.onclick = function() {
        canvas.isDrawingMode = !canvas.isDrawingMode;
        if (canvas.isDrawingMode) {
            drawingModeEl.innerHTML = '<i class="fa fa-remove"> </i>';
            //drawingOptionsEl.style.display = '';
        } else {
            drawingModeEl.innerHTML = '<i class="fa fa-pencil"> </i>';
            //drawingOptionsEl.style.display = 'none';
        }
    };
    canvas.freeDrawingBrush.width = 2;
    /* var hLinePatternBrush = new fabric.PatternBrush(canvas);
        hLinePatternBrush.getPatternSrc = function() {

            var patternCanvas = fabric.document.createElement('canvas');
            patternCanvas.width = patternCanvas.height = 20;
            var ctx = patternCanvas.getContext('2d');

            ctx.strokeStyle = '#000';
            ctx.lineWidth = 10;
            ctx.beginPath();
            ctx.moveTo(5, 0);
            ctx.lineTo(5, 10);
            ctx.closePath();
            ctx.stroke();

            return patternCanvas;
        }; */



    /*     if (fabric.PatternBrush) {
        var vLinePatternBrush = new fabric.PatternBrush(canvas);
        vLinePatternBrush.getPatternSrc = function() {

            var patternCanvas = fabric.document.createElement('canvas');
            patternCanvas.width = patternCanvas.height = 10;
            var ctx = patternCanvas.getContext('2d');

            ctx.strokeStyle = this.color;
            ctx.lineWidth = 5;
            ctx.beginPath();
            ctx.moveTo(0, 5);
            ctx.lineTo(10, 5);
            ctx.closePath();
            ctx.stroke();

            return patternCanvas;
        };

        var hLinePatternBrush = new fabric.PatternBrush(canvas);
        hLinePatternBrush.getPatternSrc = function() {

            var patternCanvas = fabric.document.createElement('canvas');
            patternCanvas.width = patternCanvas.height = 10;
            var ctx = patternCanvas.getContext('2d');

            ctx.strokeStyle = this.color;
            ctx.lineWidth = 20;
            ctx.beginPath();
            ctx.moveTo(5, 0);
            ctx.lineTo(5, 10);
            ctx.closePath();
            ctx.stroke();

            return patternCanvas;
        };

        var squarePatternBrush = new fabric.PatternBrush(canvas);
        squarePatternBrush.getPatternSrc = function() {

            var squareWidth = 10,
                squareDistance = 2;

            var patternCanvas = fabric.document.createElement('canvas');
            patternCanvas.width = patternCanvas.height = squareWidth + squareDistance;
            var ctx = patternCanvas.getContext('2d');

            ctx.fillStyle = this.color;
            ctx.fillRect(0, 0, squareWidth, squareWidth);

            return patternCanvas;
        };

        var diamondPatternBrush = new fabric.PatternBrush(canvas);
        diamondPatternBrush.getPatternSrc = function() {

            var squareWidth = 10,
                squareDistance = 5;
            var patternCanvas = fabric.document.createElement('canvas');
            var rect = new fabric.Rect({
                width: squareWidth,
                height: squareWidth,
                angle: 45,
                fill: this.color
            });

            var canvasWidth = rect.getBoundingRect().width;

            patternCanvas.width = patternCanvas.height = canvasWidth + squareDistance;
            rect.set({
                left: canvasWidth / 2,
                top: canvasWidth / 2
            });

            var ctx = patternCanvas.getContext('2d');
            rect.render(ctx);

            return patternCanvas;
        };

        var img = new Image();
        img.src = '../assets/honey_im_subtle.png';

        var texturePatternBrush = new fabric.PatternBrush(canvas);
        texturePatternBrush.source = img;
    } */
    //canvas.freeDrawingBrush = hLinePatternBrush;
    $("#text").on("change", function(e) {
        text = new fabric.Text($("#text").val(), {
            left: 100,
            top: 100
        });
        canvas.add(text);
    });
    $("#rect").on("click", function(e) {
       rect = new fabric.Rect({
            left: 40,
            top: 40,
            width: 50,
            height: 50,
            fill: 'transparent',
            stroke: 'black',
            strokeWidth: 2,
        });
        canvas.add(rect);
      /*   fabric.Image.fromURL('{{asset("circle_a.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.5,
                scaleY: 0.5,
                selectable: true
            })
            canvas.add(oImg).renderAll();
           }); */
    });
    $("#max").on("click", function(e) {
        fabric.Image.fromURL('{{asset("max_a.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.5,
                scaleY: 0.5,
                selectable: true
            })
            canvas.add(oImg).renderAll();
           });
    });
    $("#min").on("click", function(e) {
        fabric.Image.fromURL('{{asset("min_a.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.5,
                scaleY: 0.5,
                selectable: true
            })
            canvas.add(oImg).renderAll();
           });
    });
    $("#circlex").on("click", function(e) {
        fabric.Image.fromURL('{{asset("circlex.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.5,
                scaleY: 0.5,
                selectable: true
            })
            canvas.add(oImg).renderAll();
           });
    });


    $("#circ").on("click", function(e) {
        /* rect = new fabric.Circle({
            left: 40,
            top: 40,
            radius: 50,
            fill: 'transparent',
            stroke: 'red',
            strokeWidth: 5,
        });
        canvas.add(rect); */
        fabric.Image.fromURL('{{asset("circle_a.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.3,
                scaleY: 0.3,
                selectable: true
            })
            canvas.add(oImg).renderAll();
        });
    });
    $("#circsmall").on("click", function(e) {
       /*  rect = new fabric.Circle({
            left: 40,
            top: 40,
            radius: 80,
            fill: 'transparent',
            stroke: 'blue',
            strokeWidth: 5,
        });
        canvas.add(rect); */
        /* fabric.Image.fromURL('{{asset("circlex.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.5,
                scaleY: 0.5,
                selectable: true
            })
            canvas.add(oImg).renderAll();
           }); */
           fabric.Image.fromURL('{{asset("circle_a.ico")}}', function (img) {
            var oImg = img.set({
                scaleX: 0.3,
                scaleY: 0.3,
                selectable: true
            })
            canvas.add(oImg).renderAll();
        });
    });

    $("#save").on("click", function(e) {
        $(".save").html(canvas.toSVG());
    });

    $('html').keyup(function(e) {
        if (e.keyCode == 46 || e.keyCode== 8) {
            deleteSelectedObjectsFromCanvas();
        }
    });
    function deleteSelectedObjectsFromCanvas() {
        var selection = canvas.getActiveObject();
        if (selection.type === 'activeSelection') {
            selection.forEachObject(function(element) {
                console.log(element);
                canvas.remove(element);
            });
        } else {
            canvas.remove(selection);
        }
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }
    function tosaveCanvas(id){
        var canvas= document.getElementById('c');
        var blob = canvas.toDataURL();

        //console.log(blob);
        // PREPARE FORM DATA TO SEND VIA POST
        //ar formData = new FormData();
        //formData.append('croppedImage', blob, 'sampleimage.png');

        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('my-file', blob, 'filename.png');

            // Post via axios or other transport method
            $.ajax({
                url: "{{route('anestesiologia.saveCanvas')}}?id=" + id, // upload url
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function(data) {
                    swal.fire({
                        title: "Success!",
                        text: "Guardado correctamente!",
                        icon: "success"
                    }).then(function() {
                        window.open("{{ route('anestesiologia.imprime', ['id' => $id]) }}", '_blank');
                    });
                    //console.log(data)
                    //location.reload();

                },
                error: function(xhr, status, error) {
                    alert('Error, contactase con el programador code:'+status);
                }
            });
        });
    }
    function EditarT(id){
       let editar = confirm('Esta seguro de queres editar el registro');
        if (editar) {
            $.ajax({
                type: 'get',
                url: "{{ route('anestesiologia.editar_csv') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    id: id,
                    hora: document.getElementById("hora"+id).value,
                },
                success: function(data) {
                    if(data == 'ok'){
                        console.log("perfecto");
                    }else{
                        console.log("error");
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        }
    }
</script>

@include('sweet::alert')
@endsection