@extends('admisiones.base')
@section('action-content')
<style type="text/css">
    div.form-group{
        margin-bottom: 0;
        padding-left: 5px;padding-right: 5px;
    }
    legend{
        margin-bottom: 0;
    }

</style>
<!-- Ventana modal editar -->
<div class="modal fade " id="Editar_Principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Ventana modal editar -->
<div class="modal fade" id="Crear_Principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<style type="text/css">

    .control-label{
        padding-left: 5px;
    }


</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<?php /*
<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
<div class="modal-dialog" role="document" id="frame_ventana2">
<div class="modal-content"  id="imprimir3">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

</div>
<!--<iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="@if($segurod->url_validacion != "" ){{$segurod->url_validacion}}@endif" ></iframe> -->
<iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="@if($segurod->tipo == 0 )https://aplicaciones.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif @else{{$segurod->url_validacion}}@endif" ></iframe>
</div>
</div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
<div class="modal-dialog" role="document" id="frame_ventana" >
<div class="modal-content"  id="imprimir3">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

</div>
<iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://aplicaciones.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" ></iframe>
</div>
</div>
</div> */?>



<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h3 class="box-title">Admisión de Pacientes </h3>
                        </div>
                        @if(!is_null($historia))
                        @php
                            $alergia = $paciente->alergias;

                            if($alergia == null || $alergia == "No" || $alergia == "" || $alergia == "NO" || $alergia == "no" || $alergia == "nO" || $alergia == "NO REFIERE"){
                                $dato_alergia =  1;
                            }
                            else
                            {
                                $dato_alergia =  2;
                            }
                        @endphp
                        <div class="col-md-4">
                            <a class="btn btn-primary btn-xs agbtn"  id="imprimir_etiquetas2" target="_blank" href="{{ route('admision.etiqueta2', ['id' => $historia->id_agenda, 'seguro' => $historia->id_seguro, 'alergia' => $dato_alergia]) }}" ><span class="glyphicon glyphicon-print"></span> Etiqueta</a>
                        </div>
                        @else
                        <div class="col-md-4">
                            <a class="btn btn-primary btn-xs"  id="imprimir_etiquetas"  ><span class="glyphicon glyphicon-user"></span> Imprimir Etiqueta</a>
                        </div>
                        @endif
                        <?php /*@if(!is_null($historia))
@if($cita->proc_consul=='0')
<div class="col-md-4">
<a href="{{route('preparacion.mostrar',['id' => $cita->id, 'url_doctor' => $url_doctor])}}" class="btn btn-primary" ></span> Preparación</a>
</div>
@endif
@endif */?>
                    </div>
                </div>
                <form class="form-vertical" id="form_adm" role="form" method="POST" action="{{ route('admisiones.update', ['id' => $paciente->id]) }}" enctype="multipart/form-data" autocomplete="nope">
                    <div class="box-body">
                        <div class="col-md-12" style="color: blue;">@if(!is_null($historia))** Paciente ya fue Admisionado el {{$historia->created_at}} @endif</div>
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="cita" value="{{$cita->id}}">
                        <input type="hidden" name="ruta" value="{{$ruta}}">
                        <input type="hidden" name="url_doctor" value="{{$ruta}}">
                        <input type="hidden" name="unix" value="{{$unix}}">
                        @if(!is_null($historia))
                        <input type="hidden" name="hcid" value="{{$historia->hcid}}">
                        @endif
                         <!--seguro-->
                         @php $tipo=""; $tienecodigo=""; @endphp
                        <div class="form-group col-md-2{{ $errors->has('id_seguro') ? ' has-error' : '' }}" style="margin-bottom: 0px;">
                            <label for="id_seguro" class="col-md-8 control-label">Seguro</label>
                            <select id="id_seguro" name="id_seguro" class="form-control input-sm" onchange="subseguro();" required autofocus autocomplete="nope">
                                @foreach ($seguros as $seguro)
                                    <option @if(old('id_seguro')==$seguro->id) {{"selected"}}@php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @elseif($i == $seguro->id) {{"selected"}} @php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--subseguro-->
                        <div class="form-group col-md-2 oculto {{ $errors->has('id_subseguro') ? ' has-error' : '' }}" id="dsub" style="margin-bottom: 0px;">
                            <div id="div_subseguro" class="col-md-12" style="padding: 0;"></div>
                            @if ($errors->has('id_subseguro'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_subseguro') }}</strong>
                            </span>
                            @endif

                        </div>
                        @if($tipo==1)
                        <input id="copago" type="hidden" class="form-control input-sm" name="copago" value=@if(old('copago')!='') "{{old('copago')}}" @else "0" @endif required autofocus min="0" max="100" >
                        <!--copago
                        <div class="form-group col-md-3{{ $errors->has('copago') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Co-Pago(%) : </b>{{$historia->copago}} </p>
                            @else
                            <label for="copago" class="col-md-8 control-label">Co-Pago(%)</label>
                            <input id="copago" type="number" class="form-control input-sm" name="copago" value=@if(old('copago')!='') "{{old('copago')}}" @else "0" @endif required autofocus min="0" max="100">


                            @if ($errors->has('copago'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('copago') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div> -->
                        @if($segurod->url_validacion != "" )
                        <!--div class="form-group col-md-3{{ $errors->has('copago') ? ' has-error' : '' }}" style="margin-bottom: 0px;">
                            @if($paciente != Array())
                            <br>
                                <a  data-toggle="modal" data-target="#favoritesModal2">
                                    <button type="button" class="btn btn-primary" >
                                        Consulta Cobertura
                                    </button>
                                </a>
                            @endif
                        </div-->
                        @endif
                        @endif

                        @if($cita->proc_consul=='1')
                        <!--procedencia-->
                        <div class="form-group col-md-2 {{ $errors->has('procedencia') ? ' has-error' : '' }} " style="margin-bottom: 0px;">
                            <label for="procedencia" class="col-md-12 control-label">Procedencia</label>
                            <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="@if(old('procedencia')!=''){{old('procedencia')}}@else{{$cita->procedencia}}@endif" required autocomplete="nope">
                            @if ($errors->has('procedencia'))
                            <span class="help-block">
                                <strong>{{ $errors->first('procedencia') }}</strong>
                            </span>
                            @endif
                        </div>
                        @endif
                        <!--NUEVA VALIDACIÓN BRONCOSCOPIA-->
                        <div id="div_empresa" class="form-group col-md-3 {{ $errors->has('empresa') ? ' has-error' : '' }} {{ $errors->has('id_empresa') ? ' has-error' : '' }}" >
                        </div>
                        <!--div class="form-group col-md-3 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                            <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                            <div class="col-md-12">
                                <select id="id_empresa" name="id_empresa" class="form-control input-sm" required>
                                    <option value=""  >Seleccione..</option>
                                    @php $eban='0'; @endphp
                                    @foreach($empresas as $empresa)
                                    <option @if(old('id_empresa')==$empresa->id){{"selected"}} @php $eban='1'; @endphp @elseif($cita->id_empresa==$empresa->id){{"selected"}} @php $eban='1'; @endphp @endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $cita->proc_consul == "1" && $eban==false) {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_empresa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_empresa') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div-->
                        @if($cita->proc_consul=='1')
                        <!--id_cie_10-->
                        <div class="form-group col-md-12 {{ $errors->has('id_cie_10') ? ' has-error' : '' }} oculto">
                            <label for="id_cie_10" class="col-md-12 control-label">CIE_10</label>
                            <div class="col-md-2">
                                <input id="id_cie_10" type="text" class="form-control input-sm" name="id_cie_10" value=@if(old('id_cie_10')!='')"{{old('id_cie_10')}}"@elseif(!is_null($historia))"{{$historia->id_cie_10}}"@else "" @endif onchange="busca_cie_10();" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="nope">
                                @if ($errors->has('id_cie_10'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_cie_10') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-10">
                                <input id="cie_nombre" type="text" class="form-control input-sm" name="cie_nombre" readonly>

                            </div>
                        </div>
                        @endif
                        <!--consultorio-->
                        <div class="form-group col-md-3 {{ $errors->has('consultorio') ? ' has-error' : '' }} " id="dcon">
                            <label for="consultorio" class="col-md-12 control-label">IESS/Consultorio</label>
                            <div class="col-md-12">
                                <input type="checkbox" id="paciente_dr" class="flat-green" name="consultorio" value="1"  @if($cita->consultorio=="1") checked @endif autocomplete="nope">
                                @if ($errors->has('consultorio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('consultorio') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        @if($errors->has('id_empresa'))
                        <div class="form-group col-md-offset-9 {{ $errors->has('id_empresa') ? ' has-error' : '' }}" >
                            <span class="help-block">
                                <strong>{{ $errors->first('id_empresa') }}</strong>
                            </span>
                        </div>
                        @endif
                        <!--@if($tipo==0)
                        verificar
                        @if(is_null($historia))
                        <div class="checkbox col-md-2{{ $errors->has('verificar') ? ' has-error' : '' }}">

                            <label><input type="checkbox" name="verificar" value="1"  autofocus @if(old('verificar')=='1') checked @endif> Consulta Cobertura Salud</label>
                            @if ($errors->has('verificar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('verificar') }}</strong>
                                </span>
                            @endif
                        </div>
                        @endif
                        -->
                        <!--archivo-->
                        <!--div class="form-group col-md-4{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                                <?php // @if( $archivo_vrf==null ) ?>
                                    <a data-toggle="modal" href="#favoritesModal2">Consulta Cobertura Salud</a>
                                    <label for="archivo" class="col-md-8 control-label">Agregar Archivo</label>
                                    <input name="archivo" id="archivo" type="file"   class="archivo form-control input-sm"   autofocus/><br /><br />
                                    @if ($errors->has('archivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </span>
                                    @endif
                                <?php // @else  ?>
                                <?php // @php $archivopdf=$archivo_vrf->ruta.$archivo_vrf->archivo @endphp ?>

                                <?php // @if($archivo_vrf->archivo!="") ?>
                                <a target="_blank" <?php // href="{{asset($archivopdf)}}" ?> alt="pdf"  style="width:120px;height:120px;" id="pdf" > Consulta Cobertura Salud</a>
                                <?php // @endif ?>
                                <?php // @endif ?>
                            @else
                            <label for="archivo" class="col-md-8 control-label">Agregar Archivo</label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control input-sm"   autofocus/><br /><br />
                            @if ($errors->has('archivo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('archivo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div
                        @endif-->

                        <input type="hidden" name="codigo_validacion" value="{{$tienecodigo}}">
                        <input type="hidden" name="xtipo" value="{{$tipo}}">
                        @if($tienecodigo=="SI")
                        <!--codigo-->
                        <div class="form-group col-md-4{{ $errors->has('codigo') || $errors->has('fecha_codigo')? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Codigo : </b>{{$historia->codigo}} </p>
                            @else
                            <label for="codigo" class="col-md-8 control-label">Código Validación</label>
                            <input id="codigo" type="text" class="form-control input-sm" name="codigo" value=@if(old('codigo')!='') "{{old('codigo')}}" @else "{{$paciente->codigo}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!is_null($historia)){{"readonly"}}@endif autocomplete="nope">
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('fecha_codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_codigo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div>
                        <!--fecha_codigo-->
                        <div class="form-group col-md-4{{ $errors->has('fecha_codigo') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Caducidad Código : </b> {{substr($historia->fecha_codigo,0,11)}} </p>
                            @else
                            <label for="fecha_codigo" class="col-md-8 control-label">Caducidad Código</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_codigo" type="text" class="form-control input-sm" name="fecha_codigo" value=@if(old('fecha_codigo')!='') "{{old('fecha_codigo')}}" @else "{{$paciente->fecha_codigo}}" @endif required autofocus autocomplete="nope">
                            </div>
                            @if ($errors->has('fecha_codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_codigo') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>
                        @endif
                        <fieldset class="col-md-12"><legend style="margin-bottom: 0;">Datos Básicos del Paciente: </legend>
                            @if(!is_null($historia) && $cita->estado_cita=='4')
                                <p class="col-md-2"><b>Parentesco:</b></p>
                                <p class="col-md-1" >@if($historia->parentesco==null){{$paciente->parentesco}}@else{{$historia->parentesco}}@endif</p>
                                <input type="hidden" name="parentesco" value="{{$paciente->parentesco}}" id="parentesco">
                                <p class="col-md-1"><b>Cédula:</b></p>
                                <p class="col-md-1">{{$paciente->id}}</p>
                                <p class="col-md-2" ><b style="padding-left: 30px">Nombres:</b></p>
                                <p class="col-md-5">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</p>
                                <p class="col-md-2"><b>Fecha Nacimiento:</b></p>
                                <p class="col-md-1" style="padding: 0;">{{$paciente->fecha_nacimiento}}</p>
                                <input id="sexo" type="hidden" class="form-control input-sm" name="sexo" value={{$paciente->sexo}}>
                                <p class="col-md-1"><b>Sexo :</b></p>
                                <p class="col-md-1">@if($paciente->sexo==1) {{"HOMBRE"}} @elseif($paciente->sexo==2) {{"MUJER"}} @endif </p>
                                <input id="estadocivil" type="hidden" class="form-control input-sm" name="estadocivil" value={{$paciente->estadocivil}}>
                                <p class="col-md-2"><b style="padding-left: 30px">Estado Civil:</b></p>
                                <p class="col-md-2">@if($paciente->estadocivil==1) {{"SOLTERO(A)"}} @elseif($paciente->estadocivil==2) {{"CASADO(A)"}} @elseif($paciente->estadocivil==3) {{"VIUDO(A)"}} @elseif($paciente->estadocivil==4) {{"DIVORCIADO(A)"}} @elseif($paciente->estadocivil==5) {{"UNION LIBRE"}} @elseif($paciente->estadocivil==6) {{"UNION DE HECHO"}} @endif </p>
                                <p class="col-md-1"><b>Ocupación:</b></p>
                                <p class="col-md-2">{{$paciente->ocupacion}}</p>
                                <p class="col-md-1"><b>Religi&oacuten:</b></p>
                                <p class="col-md-2">{{$paciente->religion}}</p>
                                <p class="col-md-1"><b>Alergias:</b></p>
                                <p class="col-md-2">{{$paciente->alergias}}</p>
                                <input id="alergias" type="hidden" name="alergias" value={{ $paciente->alergias }} >
                                <input id="fecha_nacimiento" type="hidden" name="fecha_nacimiento" value="{{$paciente->fecha_nacimiento}}" >
                            @else
                            <!--parentesco-->
                            <div class="form-group col-md-2{{ $errors->has('parentesco') ? ' has-error' : '' }}" >
                                <label for="parentesco" class="col-md-8 control-label">Parentesco</label>
                                <select id="parentesco" name="parentesco" class="form-control input-sm" onchange="cambia_parentesco(this.value)">
                                    <option @if(old('parentesco')=="Principal"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Principal"){{"selected"}}@endif value="Principal">Principal</option>
                                    <option @if(old('parentesco')=="Padre/Madre"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Padre/Madre"){{"selected"}}@endif value="Padre/Madre">Padre/Madre</option>
                                    <option @if(old('parentesco')=="Conyuge"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Conyuge"){{"selected"}}@endif value="Conyuge">Conyuge</option>
                                    <option @if(old('parentesco')=="Hijo(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Hijo(a)"){{"selected"}}@endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentesco')=="Hermano(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Hermano(a)"){{"selected"}}@endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentesco')=="Sobrino(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Sobrino(a)"){{"selected"}}@endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentesco')=="Nieto(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Nieto(a)"){{"selected"}}@endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentesco')=="Primo(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Primo(a)"){{"selected"}}@endif value="Primo(a)">Primo(a)</option>
                                    <option @if(old('parentesco')=="Familiar"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Familiar"){{"selected"}}@endif value="Familiar">Familiar</option>
                                </select>
                                @if ($errors->has('parentesco'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentesco') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--cedula-->
                            <div class="form-group col-md-2{{ $errors->has('id') || $errors->has('id_prin')? ' has-error' : '' }}">
                                <label for="id" class="col-md-8 control-label">Cédula</label>
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value=@if(old('id')!='')"{{old('id')}}"@else"{{$paciente->id}}"@endif required autofocus onkeyup="validarCedula(this.value);" autocomplete="nope">
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('id_prin'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_prin') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--primer nombre-->
                            <div class="form-group col-md-2{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                                <label for="nombre1" class="col-md-12 control-label">Primer Nombre</label>
                                <input id="nombre1" type="text" class="form-control input-sm" name="nombre1" value=@if(old('nombre1')!='')"{{old('nombre1')}}"@else"{{$paciente->nombre1}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope" >
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--segundo nombre-->
                            <div class="form-group col-md-2{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                                <label for="nombre2" class="col-md-12 control-label" style="padding-right: 0px;font-size: 14px;">Segundo Nombre</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value=@if(old('nombre2')!='')"{{old('nombre2')}}"@else"{{ $paciente->nombre2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope" >
                                    @if(is_null($historia))
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--primer apellido-->
                            <div class="form-group col-md-2{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                                <label for="apellido1" class="col-md-12 control-label">Primer Apellido</label>
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value=@if(old('apellido1')!='')"{{old('apellido1')}}"@else"{{ $paciente->apellido1 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope" >
                                @if ($errors->has('apellido1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--segundo apellido-->
                            <div class="form-group col-md-2{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                                <label for="apellido2" class="col-md-12 control-label" style="padding: 0px;">Segundo Apellido</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value=@if(old('apellido2')!='')"{{old('apellido2')}}"@else"{{ $paciente->apellido2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope" >
                                  @if(is_null($historia))
                                 <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                  @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--fecha_nacimiento-->
                            <div class="form-group col-md-2{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                                <label for="fecha_nacimiento" class="col-md-12 control-label" style="padding: 0px;">Fecha Nacimiento</label>
                                <div class="input-group date col-md-12">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input id="fecha_nacimiento" onchange="edad2();" type="text" class="form-control input-sm" name="fecha_nacimiento" value=@if(old('fecha_nacimiento')!='')"{{old('fecha_nacimiento')}}"@else"{{ $paciente->fecha_nacimiento }}" @endif required autofocus @if(!is_null($historia)){{"readonly"}}@endif autocomplete="nope">
                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!-- Div de edad -->
                            <div class="form-group col-md-2{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-8 control-label">Edad</label>
                                <input id="Xedad" type="text" class="form-control input-sm" name="Xedad" readonly="readonly">
                                @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--menor edad-->
                            <div class="form-group col-md-2{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                                <label for="menoredad" class="col-md-12 control-label">Menor Edad</label>
                                <input id="tmenoredad" type="text" class="form-control input-sm" name="tmenoredad" value="" required autofocus readonly="readonly">
                                <input id="menoredad" type="hidden" class="form-control input-sm" name="menoredad" value="" >
                                @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--sexo 1=MASCULINO 2=FEMENINO-->
                            <div class="form-group col-md-2{{ $errors->has('sexo') ? ' has-error' : '' }}" >
                                <label for="sexo" class="col-md-8 control-label">Sexo</label>
                                <select id="sexo" name="sexo" class="form-control input-sm" required autofocus autocomplete="nope">
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('sexo')==1){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 1){{"selected"}}@endif value="1">HOMBRE</option>
                                    <option @if(old('sexo')==2){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 2){{"selected"}}@endif value="2">MUJER</option>
                                </select>
                                @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--estado civil 1=SOLTERO(A) 2=CASADO(A) 3=VIUDO(A) 4=DIVORCIADO(A) 5=UNION LIBRE 6=UNION DE HECHO-->
                            <div class="form-group col-md-2{{ $errors->has('estadocivil') ? ' has-error' : '' }}" >
                                <label for="estadocivil" class="col-md-12 control-label">Estado Civil</label>
                                <select id="estadocivil" name="estadocivil" class="form-control input-sm" required autofocus autocomplete="nope">
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('estadocivil')==1){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 1){{"selected"}}@endif value="1">SOLTERO(A)</option>
                                    <option @if(old('estadocivil')==2){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 2){{"selected"}}@endif value="2">CASADO(A)</option>
                                    <option @if(old('estadocivil')==3){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 3){{"selected"}}@endif value="3">VIUDO(A)</option>
                                    <option @if(old('estadocivil')==4){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 4){{"selected"}}@endif value="4">DIVORCIADO(A)</option>
                                    <option @if(old('estadocivil')==5){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 5){{"selected"}}@endif value="5">UNION LIBRE</option>
                                    <option @if(old('estadocivil')==6){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 6){{"selected"}}@endif value="6">UNION DE HECHO</option>
                                </select>
                                @if ($errors->has('estadocivil'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estadocivil') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--ocupacion-->
                            <div class="form-group col-md-2{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                                <label for="ocupacion" class="col-md-12 control-label">Ocupación</label>
                                <input id="ocupacion" type="text" class="form-control input-sm" name="ocupacion" value=@if(old('ocupacion')!='')"{{old('ocupacion')}}"@else"{{ $paciente->ocupacion }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50" autocomplete="nope">
                                @if ($errors->has('ocupacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ocupacion') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--alergias>
                            <div class="form-group col-md-2{{ $errors->has('alergias') ? ' has-error' : '' }}">
                                <label for="alergias" class="col-md-12 control-label">Alergias</label>
                                <input id="alergias" type="text" class="form-control input-sm" name="alergias" value=@if(old('alergias')!='')"{{old('alergias')}}"@else"{{ $paciente->alergias }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope">
                                    @if ($errors->has('alergias'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('alergias') }}</strong>
                                    </span>
                                    @endif
                                SE CAMBIO POR  MEDICINAS - ALERGIAS
                            </div-->
                            <input id="alergias" type="hidden" name="alergias" value={{ $paciente->alergias }} >
                            <div class="form-group col-md-2{{ $errors->has('trabajo') ? ' has-error' : '' }}">
                                <label for="trabajo" class="col-md-12 control-label">Trabajo</label>
                                <input id="trabajo" type="text" class="form-control input-sm" name="trabajo" value=@if(old('trabajo')!='')"{{old('trabajo')}}"@else"{{ $paciente->trabajo }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="100" autocomplete="nope">
                                @if ($errors->has('trabajo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('trabajo') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--Religion-->
                            <div class="form-group col-md-2{{ $errors->has('religion') ? ' has-error' : '' }}">
                                <label for="religion" class="col-md-12 control-label">Religi&oacuten</label>
                                <input id="religion" type="text" class="form-control input-sm" name="religion" value=@if(old('religion')!='')"{{old('religion')}}"@else"{{ $paciente->religion }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="60" autocomplete="nope">
                                    @if ($errors->has('religion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('religion') }}</strong>
                                    </span>
                                    @endif
                            </div>
                            <!--Fin Religion-->
                            <!--div class="form-group col-md-5">
                                <label for="alergias" class="col-md-12 control-label">Buscar Alergia</label>
                                <div class="col-md-10" style="padding-right: 0px;">
                                    <input id="alergias_new" type="text" class="form-control input-sm"  name="alergias_new" value="{{old('alergias_new')}}" required placeholder="Ingrese Alergia" >
                                </div>
                                <div class="col-md-1">
                                    <button id="bagregar" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button>
                                </div>
                            </div>
                            <!--alergias-->
                            <!--div class="form-group col-md-4{{ $errors->has('alergias') ? ' has-error' : '' }}">
                                <label for="alergias" class="col-md-12 control-label">Alergias</label>
                                <input id="alergias" type="text" class="form-control input-sm" name="alergias" value=@if(old('alergias')!='')"{{old('alergias')}}"@else"{{ $paciente->alergias }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope">
                                @if ($errors->has('alergias'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('alergias') }}</strong>
                                </span>
                                @endif
                            </div-->
                            <div class="form-group col-md-4{{ $errors->has('ale_list') ? ' has-error' : '' }}">
                                <label for="ale_list">Alergias</label>
                                <select id="ale_list" name="ale_list[]" class="form-control input-sm" multiple >
                                    @foreach($alergiasxpac as $ale_pac)
                                    <option selected value="{{$ale_pac->id_principio_activo}}">{{$ale_pac->principio_activo->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4{{ $errors->has('email_opc') ? ' has-error' : '' }}">
                                <label for="email_opc" class="col-xs-8 control-label">EMail(Opcional)</label>
                                   <input id="email_opc" type="email_opc" class="form-control input-sm" name="email_opc" value=@if($paciente->mail_opcional!='')"{{ $paciente->mail_opcional }}"@else @if(old('email_opc')!='')"{{old('email_opc')}}"@else"{{'@'}}"@endif @endif required maxlength="100" >

                                    @if ($errors->has('email_opc'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email_opc') }}</strong>
                                        </span>
                                    @endif
                            </div>
                            @endif
                        </fieldset>
                        <!--fieldset class="col-md-12 "><legend>Principal: </legend>
                            <div class="form-horizontal col-md-6{{ $errors->has('id_prin') ? ' has-error' : '' }}">
                                <label for="id_buscar" class="col-md-2 control-label form-horizontal" style="text-align: left;">Cédula</label>
                                <input id="id_buscar" maxlength="10" type="text" class="input-sm col-md-4" name="id_buscar" onchange="busca_principal()" value=@if(old('id_buscar')!='')"{{old('id_buscar')}}"@else"{{$paciente->id_usuario}}"@endif required autofocus onkeyup="validarCedula(this.value);" >
                                <div class="col-md-6" id="id_nombre"></div>
                            </div>
                            <div class="col-md-12">&nbsp;</div>
                        </fieldset-->
                        
                        <fieldset class="col-md-12 oculto" id="principal" style="background-color: #e6eeff;"><legend style="background-color: white;">Datos del Representante Principal: </legend>
                            <div id="div_principal" >
                                <div class="form-group col-md-2{{ $errors->has('cedula_principal') ? ' has-error' : '' }}">
                                    <label for="cedula_principal" class="col-md-12 control-label">Cédula</label>
                                    <input id="cedula_principal" type="text" class="form-control input-sm" name="cedula_principal" value=@if(old('cedula_principal')!='')"{{old('cedula_principal')}}"@else"{{$user_aso->id}}"@endif required autofocus onkeyup="validarCedula(this.value);" autocomplete="nope">
                                    @if ($errors->has('cedula_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cedula_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <!--papa mama-->
                                <div class="form-group col-md-2 oculto{{ $errors->has('papa_mama') ? ' has-error' : '' }}" id="div_papa_mama">
                                    <label for="papa_mama" class="col-md-12 control-label">Padre/Madre</label>
                                    <select id="papa_mama" name="papa_mama" class="form-control input-sm">
                                        <option value="">Seleccione ...</option>
                                        <option @if($paciente->papa_mama=='Padre') selected @endif value="Padre">Padre</option>
                                        <option @if($paciente->papa_mama=='Madre') selected @endif value="Madre">Madre</option>
                                    </select>
                                    @if ($errors->has('papa_mama'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('papa_mama') }}</strong>
                                    </span>
                                    @endif
                                </div>
                               
                                <div class="form-group col-md-2{{ $errors->has('apellido1_principal') ? ' has-error' : '' }}">
                                    <label for="apellido1_principal" class="col-md-12 control-label">Primer Apellido</label>
                                    <input id="apellido1_principal" type="text" class="form-control input-sm" name="apellido1_principal" value=@if(old('apellido1_principal')!='')"{{old('apellido1_principal')}}"@else"{{$user_aso->apellido1}}"@endif required autofocus autocomplete="nope">
                                    @if ($errors->has('apellido1_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('apellido2_principal') ? ' has-error' : '' }}">
                                    <label for="apellido2_principal" class="col-md-12 control-label">Segundo Apellido</label>
                                    <input id="apellido2_principal" type="text" class="form-control input-sm" name="apellido2_principal" value=@if(old('apellido2_principal')!='')"{{old('apellido2_principal')}}"@else"{{$user_aso->apellido2}}"@endif required autofocus autocomplete="nope">
                                    @if ($errors->has('apellido2_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('nombre1_principal') ? ' has-error' : '' }}">
                                    <label for="nombre1_principal" class="col-md-12 control-label">Primer Nombre</label>
                                    <input id="nombre1_principal" type="text" class="form-control input-sm" name="nombre1_principal" value=@if(old('nombre1_principal')!='')"{{old('nombre1_principal')}}"@else"{{$user_aso->nombre1}}"@endif required autofocus autocomplete="nope">
                                    @if ($errors->has('nombre1_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('nombre2_principal') ? ' has-error' : '' }}">
                                    <label for="nombre2_principal" class="col-md-12 control-label">Segundo Nombre</label>
                                    <input id="nombre2_principal" type="text" class="form-control input-sm" name="nombre2_principal" value=@if(old('nombre2_principal')!='')"{{old('nombre2_principal')}}"@else"{{$user_aso->nombre2}}"@endif required autofocus autocomplete="nope">
                                    @if ($errors->has('nombre2_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('fecha_nacimiento_principal') ? ' has-error' : '' }}">
                                    <label for="fecha_nacimiento_principal" class="col-md-12 control-label">Fecha Nacimiento</label>
                                    <div class="input-group date col-md-12">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input id="fecha_nacimiento_principal" type="text" class="form-control input-sm" name="fecha_nacimiento_principal" value=@if(old('fecha_nacimiento_principal')!='')"{{old('fecha_nacimiento_principal')}}"@else"{{ $user_aso->fecha_nacimiento }}" @endif required autofocus >
                                    </div>
                                    @if ($errors->has('fecha_nacimiento_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-4{{ $errors->has('email_principal') ? ' has-error' : '' }}">
                                    <label for="email_principal" class="col-md-8 control-label">E-Mail</label>
                                    <input id="email_principal" type="email" class="form-control input-sm" name="email_principal" value=@if(old('email_principal')!='')"{{old('email_principal')}}"@else"{{ $user_aso->email }}" @endif required autofocus  >
                                    @if ($errors->has('email_principal'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email_principal') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="col-md-12 oculto" id="opcional" style="background-color: #e6ffff;"><legend style="background-color: white">Datos del Representante(Opcional): </legend>
                            <div >
                                @php
                                    $opc_cedula=''; $opc_apellido1=''; $opc_apellido2=''; $opc_nombre1=''; $opc_nombre2=''; $opc_pm='';
                                    if(!is_null($repre_opc)){
                                        $opc_cedula=$repre_opc->cedula_fam;     $opc_apellido1=$repre_opc->apellido1;
                                        $opc_apellido2=$repre_opc->apellido2;   $opc_nombre1=$repre_opc->nombre1;
                                        $opc_nombre2=$repre_opc->nombre2;       $opc_pm=$repre_opc->papa_mama;
                                    }
                                @endphp
                                <div class="form-group col-md-2{{ $errors->has('cedula_opcional') ? ' has-error' : '' }}">
                                    <label for="cedula_opcional" class="col-md-12 control-label">Cédula</label>
                                    <input id="cedula_opcional" type="text" class="form-control input-sm" name="cedula_opcional" value=@if(old('cedula_opcional')!='')"{{old('cedula_opcional')}}"@else"{{$opc_cedula}}"@endif required autofocus onkeyup="validarCedula(this.value);" autocomplete="nope">
                                    @if ($errors->has('cedula_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cedula_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <!--papa mama-->
                                <div class="form-group col-md-2 {{ $errors->has('papa_mama_opcional') ? ' has-error' : '' }}" >
                                    <label for="papa_mama_opcional" class="col-md-12 control-label">Padre/Madre</label>
                                    <select id="papa_mama_opcional" name="papa_mama_opcional" class="form-control input-sm">
                                        <option value="">Seleccione ...</option>
                                        <option @if($opc_pm=='Padre') selected @endif value="Padre">Padre</option>
                                        <option @if($opc_pm=='Madre') selected @endif value="Madre">Madre</option>
                                    </select>
                                    @if ($errors->has('papa_mama_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('papa_mama_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('apellido1_opcional') ? ' has-error' : '' }}">
                                    <label for="apellido1_opcional" class="col-md-12 control-label">Primer Apellido</label>
                                    <input id="apellido1_opcional" type="text" class="form-control input-sm" name="apellido1_opcional" value=@if(old('apellido1_opcional')!='')"{{old('apellido1_opcional')}}"@else"{{$opc_apellido1}}"@endif required autofocus autocomplete="nope" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    @if ($errors->has('apellido1_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('apellido2_opcional') ? ' has-error' : '' }}">
                                    <label for="apellido2_opcional" class="col-md-12 control-label">Segundo Apellido</label>
                                    <input id="apellido2_opcional" type="text" class="form-control input-sm" name="apellido2_opcional" value=@if(old('apellido2_opcional')!='')"{{old('apellido2_opcional')}}"@else"{{$opc_apellido2}}"@endif required autofocus autocomplete="nope" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    @if ($errors->has('apellido2_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('nombre1_opcional') ? ' has-error' : '' }}">
                                    <label for="nombre1_opcional" class="col-md-12 control-label">Primer Nombre</label>
                                    <input id="nombre1_opcional" type="text" class="form-control input-sm" name="nombre1_opcional" value=@if(old('nombre1_opcional')!='')"{{old('nombre1_opcional')}}"@else"{{$opc_nombre1}}"@endif required autofocus autocomplete="nope" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    @if ($errors->has('nombre1_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-2{{ $errors->has('nombre2_opcional') ? ' has-error' : '' }}">
                                    <label for="nombre2_opcional" class="col-md-12 control-label">Segundo Nombre</label>
                                    <input id="nombre2_opcional" type="text" class="form-control input-sm" name="nombre2_opcional" value=@if(old('nombre2_opcional')!='')"{{old('nombre2_opcional')}}"@else"{{$opc_nombre2}}"@endif required autofocus autocomplete="nope" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    @if ($errors->has('nombre2_opcional'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2_opcional') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="col-md-12" style="background-color: #f0f5f5;"><legend style="background-color: white;">Datos de ubicación: </legend>

                            @if(!is_null($historia) && $cita->estado_cita=='4')
                                <input id="id_pais" type="hidden" class="form-control input-sm" name="id_pais" value={{$paciente->id_pais}}>
                                <p class="col-md-1"><b>Ciudad:</b></p>
                                <p class="col-md-3">{{$paciente->ciudad}} - @foreach($paises as $pais) @if($paciente->id_pais==$pais->id) {{$pais->nombre}} @endif @endforeach</p>

                                <p class="col-md-1"><b>E-Mail:</b></p>
                                <p class="col-md-3">@if(!is_null($user_aso)){{$user_aso->email}}@endif</p>

                                <p class="col-md-1"><b>Teléfonos:</b></p>
                                <p class="col-md-3">{{$paciente->telefono1}} - {{$paciente->telefono2}}</p>

                                <p class="col-md-1"><b>Dirección:</b></p>
                                <p class="col-md-5">{{$paciente->direccion}}</p>

                                <p class="col-md-3"><b>Lugar de Nacimiento:</b></p>
                                <p class="col-md-3">{{$paciente->lugar_nacimiento}}</p>

                                <p class="col-md-1"><b>Referido:</b></p>
                                <p class="col-md-3">{{$paciente->referido}} </p>
                            @else

                            <!--pais-->
                            <div class="form-group col-md-2{{ $errors->has('id_pais') ? ' has-error' : '' }}">


                                <label for="id_pais" class="col-md-8 control-label">Pais</label>
                                <select id="id_pais" name="id_pais" class="form-control input-sm" required autofocus >
                                    @foreach ($paises as $pais)
                                        <option @if(old('id_pais')==$pais->id){{"selected"}}@elseif(old('id_pais')=="" && $paciente->id_pais == $pais->id){{"selected"}}@endif value="{{$pais->id}}">{{$pais->nombre}}</option>
                                    @endforeach
                                </select>
                                    @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--ciudad-->
                            <div class="form-group col-md-2{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                                <label for="ciudad" class="col-md-12 control-label" style="padding-right: 0px;font-size: 14px;">Ciudad Procedencia</label>
                                <input id="ciudad" type="text" class="form-control input-sm" name="ciudad" value=@if(old('ciudad')!='')"{{old('ciudad')}}"@else"{{$paciente->ciudad}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50" autocomplete="nope">
                                    @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif

                            </div>

                            <!--direccion-->
                            <div class="form-group col-md-4{{ $errors->has('direccion') ? ' has-error' : '' }}">



                                <label for="direccion" class="col-md-8 control-label">Direccion</label>
                                <input id="direccion" type="text" class="form-control input-sm" name="direccion" value=@if(old('direccion')!='')"{{old('direccion')}}"@else"{{ $paciente->direccion }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="200" autocomplete="nope">
                                    @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--email-->
                            <div id="dv_email" class="form-group col-md-4{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-8 control-label">E-Mail</label>
                                <input id="email" type="email" class="form-control input-sm" name="email" value=@if(old('email')!='')"{{old('email')}}"@else @if(!is_null($user_aso)) "{{ $user_aso->email }}" @endif @endif required autofocus autocomplete="nope" >
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--telefono1-->
                            <div class="form-group col-md-2{{ $errors->has('telefono1') ? ' has-error' : '' }}">



                                <label for="telefono1" class="col-md-12 control-label">Teléfono</label>
                                <input id="telefono1" type="text" class="form-control input-sm" name="telefono1" value=@if(old('telefono1')!='')"{{old('telefono1')}}"@else"{{ $paciente->telefono1 }}" @endif required autofocus maxlength="30" autocomplete="nope">
                                    @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--telefono2-->
                            <div class="form-group col-md-2{{ $errors->has('telefono2') ? ' has-error' : '' }}">



                                <label for="telefono2" class="col-md-10 control-label">Celular</label>
                                <input id="telefono2" type="text" class="form-control input-sm" name="telefono2" value=@if(old('telefono2')!='')"{{old('telefono2')}}"@else"{{ $paciente->telefono2 }}" @endif required autofocus maxlength="30" autocomplete="nope">
                                    @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--LUGAR NACIMIENTO-->
                            <div class="form-group col-md-4{{ $errors->has('lugar_nacimiento') ? ' has-error' : '' }}">



                                <label for="lugar_nacimiento" class="col-md-8 control-label">Lugar de Nacimiento</label>
                                <input id="lugar_nacimiento" type="text" class="form-control input-sm" name="lugar_nacimiento" value=@if(old('lugar_nacimiento')!='')"{{old('lugar_nacimiento')}}"@else"{{ $paciente->lugar_nacimiento }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50" autocomplete="nope">
                                    @if ($errors->has('lugar_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lugar_nacimiento') }}</strong>
                                    </span>
                                    @endif

                            </div>

                            <!--REFERIDO-->
                            <div class="form-group col-md-4{{ $errors->has('referido') ? ' has-error' : '' }}">



                                <label for="referido" class="col-md-8 control-label">Referencia</label>
                                <input id="referido" type="text" class="form-control input-sm" name="referido" value=@if(old('referido')!='')"{{old('referido')}}"@else"{{ $paciente->referido }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="nope">
                                    @if ($errors->has('referido'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('referido') }}</strong>
                                    </span>
                                    @endif

                            </div>
                            @endif

                        </fieldset>

                        <fieldset class="col-md-12"><legend>@if($cita->proc_consul=='0')Familiar de Contacto: @else Representante/Acompañante (Quien firma)@endif</legend>

                            @if(!is_null($historia) && $cita->estado_cita=='4')
                                <p class="col-md-2"><b>Cédula :</b></p>
                                <p class="col-md-3">{{$paciente->cedulafamiliar}}</p>
                                <p class="col-md-2"><b>Nombres :</b></p>
                                <p class="col-md-5">{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}} </p>
                                <p class="col-md-2"><b>Parentesco :</b></p>
                                <p class="col-md-4">{{$paciente->parentescofamiliar}}</p>
                                <p class="col-md-2"><b>Teléfono Familiar :</b></p>
                                <p class="col-md-4">{{$paciente->telefono3}}</p>
                            @else

                            <!--parentescofamiliar-->
                            <div class="form-group col-md-2{{ $errors->has('parentescofamiliar') ? ' has-error' : '' }}" >



                                <label for="parentescofamiliar" class="col-md-12 control-label">Parentesco</label>
                                <select id="parentescofamiliar" name="parentescofamiliar" class="form-control input-sm" required onchange="Escoger_pr();" autocomplete="nope">
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Principal") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Principal" ? 'selected' : ''}}@endif @endif value="Principal">Principal</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Padre/Madre") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Padre/Madre" ? 'selected' : ''}}@endif @endif value="Padre/Madre">Padre/Madre</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Conyuge") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Conyuge" ? 'selected' : ''}}@endif @endif value="Conyuge">Conyuge</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Hijo(a)") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Hijo(a)" ? 'selected' : ''}}@endif @endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Hermano(a)") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Hermano(a)" ? 'selected' : ''}}@endif @endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Sobrino(a)") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Sobrino(a)" ? 'selected' : ''}}@endif @endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Nieto(a)") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Nieto(a)" ? 'selected' : ''}}@endif @endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Primo(a)") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Primo(a)" ? 'selected' : ''}}@endif @endif value="Primo(a)">Primo(a)</option>
                                    <option @if(old('parentescofamiliar')!='') @if(old('parentescofamiliar')=="Familiar") selected @endif @else @if($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal' ) @else {{$paciente->parentescofamiliar == "Familiar" ? 'selected' : ''}}@endif @endif value="Familiar">Familiar</option>
                                </select>
                                @if ($errors->has('parentescofamiliar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentescofamiliar') }}</strong>
                                </span>
                                @endif

                            </div>

                            <div class="form-group col-md-2{{ $errors->has('cedulafamiliar') ? ' has-error' : '' }}">


                                <label for="cedulafamiliar" class="col-md-12 control-label">Cédula</label>
                                <input id="cedulafamiliar" type="text" class="form-control input-sm" name="cedulafamiliar" value=@if(old('cedulafamiliar')!='')"{{old('cedulafamiliar')}}" @elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal') "" @elseif($cita->proc_consul=='0' && $paciente->parentescofamiliar == 'Principal') "{{$paciente->id}}" @elseif(!is_null($paciente->cedulafamiliar)) "{{ $paciente->cedulafamiliar }}" @else "" @endif required autofocus onkeyup="validarCedula(this.value);" autocomplete="nope">
                                @if ($errors->has('cedulafamiliar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cedulafamiliar') }}</strong>
                                </span>
                                @endif

                            </div>

                            <!--nombre1familiar-->
                            <div class="form-group col-md-2{{ $errors->has('nombre1familiar') ? ' has-error' : '' }}">


                                <label for="nombre1familiar" class="col-md-12 control-label">Primer Nombre</label>
                                <input id="nombre1familiar" type="text" class="form-control input-sm" name="nombre1familiar" value=@if(old('nombre1familiar')!='')"{{old('nombre1familiar')}}" @elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal') "" @else "{{ $paciente->nombre1familiar }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="nope">
                                @if ($errors->has('nombre1familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1familiar') }}</strong>
                                </span>
                                @endif

                            </div>

                            <!--nombre2familiar-->
                            <div class="form-group col-md-2{{ $errors->has('nombre2familiar') ? ' has-error' : '' }}">
                                <label for="nombre2familiar" class="col-md-12 control-label" style="padding-right: 0px;font-size: 14px;">Segundo Nombre</label>
                                <div class="input-group dropdown col-md-12">
                                    <input id="nombre2familiar" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2familiar" value=@if(old('nombre2familiar')!='')"{{old('nombre2familiar')}}"@elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal') "" @else "{{ $paciente->nombre2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope">
                                    @if(is_null($historia))
                                    <ul class="dropdown-menu usuario3">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                @if ($errors->has('nombre2familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2familiar') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--apellido1familiar-->
                            <div class="form-group col-md-2{{ $errors->has('apellido1familiar') ? ' has-error' : '' }}">
                                <label for="apellido1familiar" class="col-md-12 control-label">Primer Apellido</label>
                                <input id="apellido1familiar" type="text" class="form-control input-sm" name="apellido1familiar" value=@if(old('apellido1familiar')!='')"{{old('apellido1familiar')}}" @elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal') "" @else "{{ $paciente->apellido1familiar }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="nope">
                                @if ($errors->has('apellido1familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1familiar') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--apellido2familiar-->
                            <div class="form-group col-md-2{{ $errors->has('apellido2familiar') ? ' has-error' : '' }}">
                                <label for="apellido2familiar" class="col-md-12 control-label" style="padding: 0px;">Segundo Apellido</label>
                                <div class="input-group dropdown col-md-12">
                                    <input id="apellido2familiar" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2familiar" value=@if(old('apellido2familiar')!='')"{{old('apellido2familiar')}}"@elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal')"" @else "{{ $paciente->apellido2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="nope">
                                    @if(is_null($historia))
                                    <ul class="dropdown-menu usuario4">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                @if ($errors->has('apellido2familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2familiar') }}</strong>
                                </span>
                                @endif
                            </div>


                            <!--telefono3-->
                                <div class="form-group col-md-2{{ $errors->has('telefono3') ? ' has-error' : '' }}">



                                    <label for="telefono3" class="col-md-12 control-label" style="padding: 0px;">Teléfono Familiar</label>
                                    <input id="telefono3" type="text" class="form-control input-sm" name="telefono3" value=@if(old('telefono3')!='')"{{old('telefono3')}}"@elseif($cita->proc_consul=='1' && $paciente->parentescofamiliar == 'Principal')"" @else "{{$paciente->telefono3}}" @endif required autofocus autocomplete="nope">
                                    @if ($errors->has('telefono3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono3') }}</strong>
                                    </span>
                                    @endif

                                </div>
                            @endif
                        </fieldset>

                        <div class="form-group col-xs-6">
                            <div class="col-md-12 col-md-offset-8" style="padding: 5px;">

                                <!--button type="submit" class="btn btn-primary">
                                @if(!is_null($historia)) <span class="glyphicon glyphicon-chevron-right"></span> Continuar @else <span class="glyphicon glyphicon-floppy-disk"></span> ADMISION @endif
                                </button-->

                                <button class="btn btn-primary" type="button" id="boton_admision" onclick="Confirmar_empresa();" >@if(!is_null($historia) && $cita->estado_cita=='4') <span class="glyphicon glyphicon-floppy-disk"></span> Actualizar @else <span class="glyphicon glyphicon-floppy-disk"></span> ADMISION @endif</button>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



    </div>
</div>



<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>



<script type="text/javascript">
@if(is_null($historia))



$('#fecha_codigo').bootstrapMaterialDatePicker({
    date: true,
    shortTime: false,
    time: false,
    format : 'YYYY-MM-DD',
    lang : 'es',
});
@endif

function Escoger_pr(){
    var par_fam = document.getElementById('parentescofamiliar').value;
    if(par_fam=='Principal'){
        document.getElementById('cedulafamiliar').value='{{$paciente->id}}';
        /*document.getElementById('nombre1familiar').value='{{$paciente->nombre1familiar}}';
        document.getElementById('apellido1familiar').value='{{$paciente->apellido1familiar}}';
        document.getElementById('nombre2familiar').value='{{$paciente->nombre2familiar}}';
        document.getElementById('apellido2familiar').value='{{$paciente->apellido2familiar}}';
        document.getElementById('telefono3').value='{{$paciente->telefono1}}';*/
        document.getElementById('nombre1familiar').value='{{$paciente->nombre1}}';
        document.getElementById('apellido1familiar').value='{{$paciente->apellido1}}';
        document.getElementById('nombre2familiar').value='{{$paciente->nombre2}}';
        document.getElementById('apellido2familiar').value='{{$paciente->apellido2}}';
        document.getElementById('telefono3').value='{{$paciente->telefono1}}';
    }
}



$(document).ready(function () {


    edad2();
    //edad2_prin();
    cambia_parentesco();
    //busca_principal();
    subseguro();
    //busca_cie_10();


    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    /*$('.Antecedentes').boxWidget('toggle');*/

    $('input[type="checkbox"]').on('change', function(e){
        if(e.target.checked){
            $('#favoritesModal2').modal();
        }
    });

    $("#div_papa_mama").addClass("oculto");
    $("#opcional").addClass("oculto");
    var parentesco = $("#parentesco").val();
    if(parentesco=='Hijo(a)'){
        $("#div_papa_mama").removeClass("oculto");
        $("#opcional").removeClass("oculto");

    }

    var sexo = document.getElementById("sexo").value;

        if(sexo==2)
        {

           $("#primera_mens").show();
           $('label[for="primera_mens"]').show();

           $("#menopausia").show();
           $('label[for="menopausia"]').show();

           $("#parto_normal").show();
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show();
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show();
           $('label[for="aborto"]').show();




        }else{

           $("#primera_mens").hide();
           $('label[for="primera_mens"]').hide();

           $("#menopausia").hide();
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide();
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide();
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide();
           $('label[for="aborto"]').hide();

        }

    <?php if (is_null($historia)) {?>
    datos_edad();
    <?php }?>

    $(function() {
    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');


    });})



    $(function() {
    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');


    });})

    $(function() {
    $('.usuario3 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');


    });})

    $(function() {
    $('.usuario4 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    });

    $('#fecha_nacimiento').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
         //Important! See issue #1075
    });

    $('#fecha_nacimiento_principal').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
         //Important! See issue #1075
    });

    $('#fecha_nacimiento_prin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075

        });

    $("#fecha_nacimiento").on("dp.change", function (e) {
            edad2();
        });

    $("#fecha_nacimiento_prin").on("dp.change", function (e) {
            edad2_prin();
        });



})

    $("#sexo").change(function () {
        var sexo = document.getElementById("sexo").value;

        if(sexo==2)
        {

           $("#primera_mens").show();
           $('label[for="primera_mens"]').show();

           $("#menopausia").show();
           $('label[for="menopausia"]').show();

           $("#parto_normal").show();
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show();
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show();
           $('label[for="aborto"]').show();




        }else{

           $("#primera_mens").hide();
           $('label[for="primera_mens"]').hide();

           $("#menopausia").hide();
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide();
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide();
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide();
           $('label[for="aborto"]').hide();

        }




    });



});

/*function subseguro()
{
        vseguro = document.getElementById("id_seguro").value;

        vseguro =  vseguro.trim();

        if (vseguro != ""){
            location.href =" {{route('admisiones.admision2', ['id' => $paciente->id, 'cita' => $cita, 'ruta' => $ruta, 'unix' => $unix ])}}/"+vseguro;
        }
}*/

function subseguro()
{
        $('#dcon').hide();


        empresas();
        vseguro = document.getElementById("id_seguro").value;

        vseguro =  vseguro.trim();

        if(vseguro=='2'){
            $('#dcon').show();
        }


        jsparentesco = document.getElementById("parentesco").value;
        if(jsparentesco=="Padre/Madre"){
            jsparentesco="PadreMadre";
        }

        if (vseguro != ""){

            $.ajax({
                type: 'get',
                url: "{{url('select_sseguro')}}/"+vseguro+"/"+jsparentesco+"/{{$cita->id}}/@if(old('id_subseguro')!=''){{old('id_subseguro')}}@else{{'0'}}@endif",
                success: function(data){
                    if(data!=0){
                        $('#div_subseguro').empty().html(data);
                        $('#dsub').removeClass("oculto");
                    }else{
                        $('#dsub').addClass("oculto");
                    }
                }
            })
        }
}

function empresas()
{
    vseguro = document.getElementById("id_seguro").value;

    vseguro =  vseguro.trim();

    if (vseguro != ""){

            $.ajax({
                type: 'get',
                url: "{{url('convenios/admision')}}/"+vseguro+"/{{$cita->id}}/@if(old('id_subseguro')!=''){{old('id_empresa')}}@else{{'0'}}@endif",
                success: function(data){
                    if(data!="null"){
                        $('#div_empresa').empty().html(data);
                        $('#div_empresa').removeClass("oculto");
                    }
                }
            })
        }
}

function cambia_parentesco(){

    var parentesco = document.getElementById("parentesco").value;
    if(parentesco!="Principal"){
       $("#principal").removeClass("oculto");
       $("#dv_email").addClass("oculto");
    }else{
       $("#dv_email").removeClass("oculto");
       $("#principal").addClass("oculto");
       @if( old('email')=='')
           @if(!is_null($eprincipal))
            $("#email").val('{{$eprincipal->email}}');
           @else
            $("#email").val("");
           @endif
        @endif
    }

    subseguro();
}
$(document).ready(function($){
    var ventana_ancho = $(window).width();


    if(ventana_ancho > "962" ){
        var nuevovalor = ventana_ancho * 0.8;
    }
    else
    {
        var nuevovalor = ventana_ancho * 0.9;
    }
    $("#frame_ventana").width(nuevovalor);
    $("#frame_ventana2").width(nuevovalor);

});

/*function edad2_prin(fechana)
{

    var jsnacimiento = document.getElementById("fecha_nacimiento_prin").value;
    var jsedad = calcularEdad(jsnacimiento);

    if(isNaN(jsedad))
    {
        $("#Xedadp").val('0');
    }
    else
    {

        $("#Xedadp").val(jsedad);
    }


    if (jsedad>=18){

       $("#tmenoredadp").val("NO");
       $("#menoredadp").val("0");
    }
    else{
        $("#tmenoredadp").val("SI");
        $("#menoredadp").val("1");
    }


}*/

var busca_principal = function ()
{

    @php
        $mid_usuario = '0';
        $historia_id = '0';

        if(!is_null($historia)){
            $historia_id = $historia->hcid;
            if($historia->id_usuario!=null){
                $mid_usuario = $historia->id_usuario;
            }
        }
    @endphp

    $.ajax({
        type: 'get',
        url: "{{ route('admisiones.busca_principal',['id' => $paciente->id, 'id_usuario' => $mid_usuario, 'historia_id' => $historia_id])}}",
        success: function(data){

            $('#div_principal').empty().html(data);
        }
    })

}

$('#Editar_Principal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

 $('#Crear_Principal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });


$('#imprimir_etiquetas').click(function(){

    url = "{{ route('admision.etiqueta', ['id' => $cita->id])}}/";
    seguro = document.getElementById('id_seguro').value;
    url = url+seguro+"/";
    alergias = document.getElementById('ale_list').value;
    console.log(document.getElementById('ale_list').value);
    if((alergias == "") || (alergias == "No") || (alergias == "NO") || (alergias == "no") || (alergias == "nO") || (alergias == "NO REFIERE")){
        dato_alergia = 1;
    } else{
        dato_alergia = 2;
    }
    url = url+dato_alergia;
    window.open(url, '_blank');
    return false;
});

var busca_cie_10 = function ()
{

    var cie=document.getElementById('id_cie_10').value;
    if(cie.length>0){
        $.ajax({
            type: 'get',
            url: "{{ url('admisiones/cie_10') }}/"+cie,
            success: function(data){
            //alert(data);
            $('#cie_nombre').val(data);
            }
        })
    }else{
        $('#cie_nombre').val('');
    }


}

function Confirmar_empresa(){

    var empresa_ok = '';
    @foreach($empresas as $empresa)
        //var empresa = document.getElementById({{$empresa->id}})
        var empresa = document.getElementById('{{$empresa->id}}');
        //alert({{$empresa->id}});
        //alert("empresa");
        //console.log(empresa);
        if(empresa!=null){
            //alert("ingreso");
            if(empresa.checked){
                var empresa_ok = '{{$empresa->nombrecomercial}}';
            }

        }
    @endforeach
    //alert(empresa_ok);

    if(empresa_ok!=''){
        var mensaje = confirm("CONFIRME QUE LA EMPRESA ES : "+empresa_ok);
        if(mensaje){
            //alert("disabled");
            $('#boton_admision').attr("disabled", "disabled");
            //alert("paso");
            document.getElementById("form_adm").submit();
        }
    }else{
        alert("SELECCIONE LA EMPRESA");
    }


}

$('#ale_list').select2({
    placeholder: "Seleccione Medicamento...",
    minimumInputLength: 2,
    ajax: {
        url: '{{route('generico.find')}}',
        dataType: 'json',
        data: function (params) {
            console.log(params);
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            return {
                results: data

            };
            alert(data);
        },
        cache: true
    }
});


</script>
@endsection
