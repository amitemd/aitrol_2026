
@extends('hc_admision.anestesiologia.base')

@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
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
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:70%;">
    <div class="modal-content" >
    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-triangle-left"></span> Procedimientos</button>
                    </a>
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
                            <td colspan="3">{{$hc_procedimientos->procedimiento_completo->nombre_general}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        <button class="w3-bar-item w3-button tablink">DIAGNOSTICO</button>
                        <button class="w3-bar-item w3-button tablink w3-red">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink" >PROTOCOLO</button>

                        <button class="w3-bar-item w3-button tablink">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink " >VIDEO</button>
                    </div>

                    <div id="tab1" class="w3-container w3-border city" >
                        <div class="box ">
                            <div class="box-header with-border">
                                <div class="col-md-9">
                                    <h4>RECORD ANESTÉSICO DEL PACIENTE</h4>
                                </div>
                                @if($record_anestesilogico != '[]')
                                    <div class="col-md-3">
                                        <a href="{{ route('anestesiologia.imprime', ['id' => $id]) }}" type="button" class="btn btn-primary">
                                                Descargar
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="box-body">

                                <form class="form-vertical" role="form" method="POST" action="{{ route('anestesiologia.crea_actualiza', ['hc_id' => $hca[0]->hcid ]) }}" >

                                    <input type="hidden" name="id_hc_procedimientos" value="{{ $id }}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                    <!--anestesiologo-->

                                    <div class="form-group col-md-4{{ $errors->has('id_anestesiologo') ? ' has-error' : '' }}">
                                        <label for="id_anestesiologo" class="col-md-12 control-label" >Anestesiólogo</label>
                                        <select id="id_anestesiologo" class="form-control input-sm" name="id_anestesiologo"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->id_anestesiologo  == $anestesiologo->id) selected  @endif @endif value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('id_anestesiologo'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_anestesiologo') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('id_ayudante') ? ' has-error' : '' }}">
                                        <label for="id_ayudante" class="col-md-12 control-label" >Ayudante de Anestesiologia</label>
                                        <select id="id_ayudante" class="form-control input-sm" name="id_ayudante"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->id_ayudante  == $anestesiologo->id) selected="selected"  @endif @endif value="{{$anestesiologo->id}}">@if($anestesiologo->id = "1203240658") Lcda. @else Dr. @endif {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('id_ayudante'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_ayudante') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('id_instrumentista') ? ' has-error' : '' }}">
                                        <label for="id_instrumentista" class="col-md-12 control-label" >Instrumentistas</label>
                                        <select id="id_instrumentista" class="form-control input-sm" name="id_instrumentista"   >
                                            <option value="">Seleccione..</option>
                                            @foreach($enfermeros as $value)
                                            <option value="{{$value->id}}">Enf. {{$value->nombre1}} {{$value->apellido1}}</option>
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
                                        <label for="id_tipoanesteciologia" class="col-md-12 control-label" >Tipo Anestesiologia</label>
                                        <select id="id_tipoanesteciologia" class="form-control input-sm" name="id_tipoanesteciologia"  required >
                                            <option value="">Seleccionar ..</option>
                                            @foreach($tipo_anesteciologia as $value)
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->id_tipoanestesiologia  == $value->id) selected="selected"  @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('id_tipoanesteciologia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('id_tipoanesteciologia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('diagnostico_preoperatorio') ? ' has-error' : '' }}">
                                        <label for="diagnostico_preoperatorio" class="col-md-12 control-label" >Diagnostico Preoperatorio</label>
                                        <input id="diagnostico_preoperatorio" required class="form-control input-sm" name="diagnostico_preoperatorio" value="@if(old('diagnostico_preoperatorio')!='')"{{old('diagnostico_preoperatorio')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->diagnostico_preoperatorio }}@endif" >
                                        @if ($errors->has('diagnostico_preoperatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('diagnostico_preoperatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-4{{ $errors->has('diagnostico_postoperatorio') ? ' has-error' : '' }}">
                                        <label for="diagnostico_postoperatorio" required class="col-md-12 control-label" >Diagnostico Postoperatorio</label>
                                        <input id="diagnostico_postoperatorio" required class="form-control input-sm" name="diagnostico_postoperatorio" value="@if(old('diagnostico_postoperatorio')!='')"{{old('diagnostico_postoperatorio')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->diagnostico_postoperatorio }}@endif" >
                                        @if ($errors->has('diagnostico_postoperatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('diagnostico_postoperatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--dextrosa-->
                                    <div class="col-md-2{{ $errors->has('dextrosa') ? ' has-error' : '' }}">
                                        <label for="dextrosa" class="col-md-12 control-label">Dextrosa (cc)</label>
                                        <input id="dextrosa" min=0 type="number" step="any" class="form-control input-sm" name="dextrosa" value=@if(old('dextrosa')!='')"{{old('dextrosa')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->dextrosa }}" @else "{{0}}" @endif >
                                        @if ($errors->has('dextrosa'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('dextrosa') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--cloruro_sodio-->
                                    <div class="col-md-2{{ $errors->has('cloruro_sodio') ? ' has-error' : '' }}">
                                        <label for="cloruro_sodio" class="col-md-12 control-label">Cloruro de Sodio (cc)</label>
                                        <input id="cloruro_sodio" min=0 type="number" step="any" class="form-control input-sm" name="cloruro_sodio" value=@if(old('cloruro_sodio')!='')"{{old('cloruro_sodio')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->cloruro_sodio }}" @else "{{0}}" @endif >
                                        @if ($errors->has('cloruro_sodio'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cloruro_sodio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--lactato_ringer-->
                                    <div class="col-md-2{{ $errors->has('lactato_ringer') ? ' has-error' : '' }}">
                                        <label for="lactato_ringer" class="col-md-12 control-label" style="font-size: 14px;">Lactato de Ringer (cc)</label>
                                        <input id="lactato_ringer" min=0 type="number" step="any" class="form-control input-sm" name="lactato_ringer" value=@if(old('lactato_ringer')!='')"{{old('lactato_ringer')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->lactato_ringer }}" @else "{{0}}" @endif >
                                        @if ($errors->has('lactato_ringer'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lactato_ringer') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--sangre_derivados-->
                                    <div class="col-md-2{{ $errors->has('sangre_derivados') ? ' has-error' : '' }}">
                                        <label for="sangre_derivados" class="col-md-12 control-label" style="font-size: 13px;">Sangre o Derivados (cc)</label>
                                        <input id="sangre_derivados" min=0 type="number" step="any" class="form-control input-sm" name="sangre_derivados" value=@if(old('sangre_derivados')!='')"{{old('sangre_derivados')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->sangre_derivados }}" @else "{{0}}" @endif >
                                        @if ($errors->has('sangre_derivados'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sangre_derivados') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--expansores-->
                                    <div class="col-md-2{{ $errors->has('expansores') ? ' has-error' : '' }}">
                                        <label for="expansores" class="col-md-12 control-label"">Expansores (cc)</label>
                                        <input id="expansores" min=0 type="number" step="any" class="form-control input-sm" name="expansores" value=@if(old('expansores')!='')"{{old('expansores')}}"@elseif($record_anestesilogico != '[]')"{{ $record_anestesilogico[0]->expansores }}" @else "{{0}}" @endif >
                                        @if ($errors->has('expansores'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('expansores') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--tecnicas_especiales-->
                                    <div class="form-group col-md-4{{ $errors->has('tecnicas_especiales') ? ' has-error' : '' }}">
                                        <label for="tecnicas_especiales" class="col-md-12 control-label" >Tecnicas Especiales</label>
                                        <input id="tecnicas_especiales" class="form-control input-sm" name="tecnicas_especiales" value="@if(old('tecnicas_especiales')!='')"{{old('tecnicas_especiales')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->tecnicas_especiales }}@endif" >
                                        @if ($errors->has('tecnicas_especiales'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('tecnicas_especiales') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--id_sala-->
                                    <div class="form-group col-md-4{{ $errors->has('id_sala') ? ' has-error' : '' }}">
                                        <label for="id_sala" class="col-md-12 control-label" >Traslado a Sala</label>
                                        <select id="id_sala" class="form-control input-sm" name="id_sala"  required >
                                            <option value="">Seleccione..</option>
                                            @foreach($salas as $value)
                                            <option  @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->id_sala  == $value->id) selected="selected"  @endif @endif value="{{$value->id}}">{{$value->nombre_sala}}</option>
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
                                        <label for="id_guiado" class="col-md-12 control-label" >Guiado por</label>
                                        <select id="id_guiado" class="form-control input-sm" name="id_guiado"  required >
                                            @foreach($anestesiologos as $anestesiologo)
                                            <option value="{{$anestesiologo->id}}">Dr. {{$anestesiologo->nombre1}} {{$anestesiologo->apellido1}}</option>
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
                                        <label for="hora" class="col-md-12 control-label" >Hora de Traslado</label>
                                        <input id="hora" required type="text" class="form-control input-sm" name="hora" value=@if(old('hora')!='')"{{old('hora')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->hora }}@endif >
                                        @if ($errors->has('hora'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('hora') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--comentarios-->
                                    <div class="form-group col-md-10{{ $errors->has('comentarios') ? ' has-error' : '' }}">
                                        <label for="comentarios" class="col-md-12 control-label" >Comentarios</label>
                                        <input required id="comentarios"  class="form-control input-sm" name="comentarios" value="@if(old('comentarios')!='')"{{old('comentarios')}}"@elseif($record_anestesilogico != '[]'){{ $record_anestesilogico[0]->comentarios }}@endif" >
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
                                                                <td><input name="lista[]"
                                                                    @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked  @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}"></td>

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
                                                                <td><input name="lista[]" type="checkbox" class="flat-green" @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked  @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}"></td>

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
                                                                <td><input name="lista[]" type="checkbox" class="flat-green" @foreach($datos as $valor) @if($value->id == $valor->id_tecnicas_anestesicas) checked  @endif @endforeach type="checkbox" class="flat-green" value="{{$value->id}}"></td>

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
                                    </div>
                                    <div class="form-group col-md-12">
                                        <h2 class="box-title">VALORACION POST-ANESTESICA</h2>
                                    </div>
                                    <!--sistema_circulatorio-->
                                    <div class="form-group col-md-4{{ $errors->has('sistema_circulatorio') ? ' has-error' : '' }}">
                                        <label for="sistema_circulatorio" class="col-md-12 control-label" >Sistema Circulatorio</label>
                                        <select id="sistema_circulatorio" class="form-control input-sm" name="sistema_circulatorio"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->sistema_circulatorio  == 2) selected="selected"  @endif @endif value="2">PA 20% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->sistema_circulatorio  == 1) selected="selected"  @endif @endif value="1">PA 20 - 49% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->sistema_circulatorio  == 0) selected="selected"  @endif @endif value="0">PA 50% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        </select>
                                        @if ($errors->has('sistema_circulatorio'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('sistema_circulatorio') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--conciencia-->
                                    <div class="form-group col-md-4{{ $errors->has('conciencia') ? ' has-error' : '' }}">
                                        <label for="conciencia" class="col-md-12 control-label" >Conciencia</label>
                                        <select id="conciencia" class="form-control input-sm" name="conciencia"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->conciencia  == 2) selected="selected"  @endif @endif value="2">PA 20% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->conciencia  == 1) selected="selected"  @endif @endif value="1">PA 20 - 49% NIVEL PRE-ANEST&Eacute;SICO</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->conciencia  == 0) selected="selected"  @endif @endif value="0">PA 50% NIVEL PRE-ANEST&Eacute;SICO</option>
                                        </select>
                                        @if ($errors->has('conciencia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('conciencia') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--saturacion-->
                                    <div class="form-group col-md-4{{ $errors->has('saturacion') ? ' has-error' : '' }}">
                                        <label for="saturacion" class="col-md-12 control-label" >Saturacion</label>
                                        <select id="saturacion" class="form-control input-sm" name="saturacion"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->saturacion  == 2) selected="selected"  @endif @endif value="2">SAT 02 + 92% AIRE AMBIENTE</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->saturacion  == 1) selected="selected"  @endif @endif value="1">NECESITA 02 SAT&gt;90%</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->saturacion  == 0) selected="selected"  @endif @endif value="0">SAT 02&lt;90% con 02</option>
                                        </select>
                                        @if ($errors->has('saturacion'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('saturacion') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--actividades-->
                                    <div class="form-group col-md-6{{ $errors->has('actividades') ? ' has-error' : '' }}">
                                        <label for="actividades" class="col-md-12 control-label" >Actividades</label>
                                        <select id="actividades" class="form-control input-sm" name="actividades"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->actividades  == 2) selected="selected"  @endif @endif value="2">CAPAZ DE MOVER LAS CUATRO EXTREMIDADES VOLUNTARIO O BAJO ORDENES</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->actividades  == 1) selected="selected"  @endif @endif value="1">CAPAZ DE MOVER LAS DOS EXTREMIDADES VOLUNTARIOS O BAJO ORDENES</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->actividades  == 0) selected="selected"  @endif @endif value="0">CAPAZ DE MOVER UNA EXTREMIDAD VOLUNTARIO O BAJO ORDENES</option>
                                        </select>
                                        @if ($errors->has('actividades'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('actividades') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--respiracion-->
                                    <div class="form-group col-md-6{{ $errors->has('respiracion') ? ' has-error' : '' }}">
                                        <label for="respiracion" class="col-md-12 control-label" >Respiracion</label>
                                        <select id="respiracion" class="form-control input-sm" name="respiracion"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->respiracion  == 2) selected="selected"  @endif @endif value="2">CAPAZ DE RESPIRAR PROFUNDAMENTE Y TOSER</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->respiracion  == 1) selected="selected"  @endif @endif value="1">APNEA RESPIRACION LIMITADA O TAQUIPMEA</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->respiracion  == 0) selected="selected"  @endif @endif value="0">APNEICO O CON RESPIRADOR ARTIFICIAL</option>
                                        </select>
                                        @if ($errors->has('respiracion'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('respiracion') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <!--tipo_analgesia-->
                                    <div class="form-group col-md-6{{ $errors->has('tipo_analgesia') ? ' has-error' : '' }}">
                                        <label for="tipo_analgesia" class="col-md-12 control-label" >Tipo de Analgesia</label>
                                        <select id="tipo_analgesia" class="form-control input-sm" name="tipo_analgesia"  required >
                                            <option value="">Seleccione..</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->tipo_analgesia  == 2) selected="selected"  @endif @endif value="2">ANALGESIA INTRAVENOSA</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->tipo_analgesia  == 1) selected="selected"  @endif @endif value="1">ANALGESIA PERIDURAL</option>
                                            <option @if($record_anestesilogico != '[]') @if($record_anestesilogico[0]->tipo_analgesia  == 0) selected="selected"  @endif @endif value="0">ANALGESIA POR PRN</option>
                                        </select>
                                        @if ($errors->has('tipo_analgesia'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('tipo_analgesia') }}</strong>
                                        </span>
                                        @endif
                                    </div>


                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-9">
                                            <button type="submit" class="btn btn-primary">
                                                    Guardar
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
                                            <a  class="btn btn-primary" data-toggle="modal" data-target="#favoritesModal" href="{{ route('anestesiologia.mostrarcsv', ['id' => $record_anestesilogico[0]->id, 'agenda' => $id]) }}">Añadir Nuevo CSV</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                            <thead>
                                              <tr >
                                                <th >Hora</th>
                                                <th >Presion Arterial</th>
                                                <th >Pulso</th>
                                                <th >Respiracion</th>
                                                <th >Oxigeno</th>
                                                <th >Orina cc</th>
                                                <th >Temperatura</th>
                                                <th >Anotaciones</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($csv as $value)
                                                <tr >
                                                  <td >{{ $value->hora }}</td>
                                                  <td >{{ $value->presion_arterial }}</td>
                                                  <td >{{ $value->pulso }}</td>
                                                  <td >{{ $value->respiracion }}</td>
                                                  <td >{{ $value->o2 }}</td>
                                                  <td >{{ $value->orina }}</td>
                                                  <td>{{ $value->Temperatura }}</td>
                                                  <td>{{ $value->anotaciones }}</td>
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
                    </div>


<script>

</script>
                </div>
            </div>
        </div>




    </div>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    $(document).ready(function() {

        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');
        $(".breadcrumb").append('<li class="active">Atención</li>');

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });

        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ "años";

        $('#edad').text( edad );


        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });

    function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " w3-red";
    }

    var drogasadministradas = function ()
    {

        var id_record = document.getElementById('id_record').value;

        //console.log(unix);
        $.ajax({
            type: 'get',
            url:'{{ url('historia/drogasadministradas')}}/'+id_record,//historia.drogasadministradas
            success: function(data){
                console.log(data);
                $('#drogas').empty().html(data);
            }
        })

    }

</script>

@include('sweet::alert')
@endsection

