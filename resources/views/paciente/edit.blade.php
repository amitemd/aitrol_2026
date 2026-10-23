
@extends('paciente.base')

@section('action-content')

<style type="text/css">
    .form-group{
        margin-bottom: 0;
    }  
    .alerta_correcto{
        position: fixed;
        z-index: 9999;
        bottom:  58%;
        left: 41%;
        
    }
    .alerta_ok{
        position: fixed;
        z-index: 9999;
        bottom:  58%;
        left: 41%; 
    }  
</style>

<div class="alert alert-danger alerta_correcto alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;" id="err">
    
</div>
<div class="alert alert-success alerta_ok alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Se actualizó ..<span id="actualiza"></span></b>
</div>

<div class="modal fade" id="mostrar" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<div class="container-fluid">
    <div class="row">
      
        <div class="col-sm-6">
            <div class="box box-primary"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Paciente</h3></div>
                <form class="form-vertical" role="form" method="POST" id="form_paciente">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!--parentesco-->
                        <div class="form-group col-xs-6{{ $errors->has('parentesco') ? ' has-error' : '' }}" >
                            <label for="parentesco" class="col-xs-8 control-label">Parentesco</label>
                            @if($paciente->id!=$paciente->id_usuario)    
                            <select id="parentesco" name="parentesco" class="form-control">
                                <option {{$paciente->parentesco == "Padre/Madre" ? 'selected' : ''}} value="Padre/Madre">Padre/Madre</option>
                                <option {{$paciente->parentesco == "Conyuge" ? 'selected' : ''}} value="Conyuge">Conyuge</option>
                                <option {{$paciente->parentesco == "Hijo(a)" ? 'selected' : ''}} value="Hijo(a)">Hijo(a)</option>
                                <option {{$paciente->parentesco == "Hermano(a)" ? 'selected' : ''}} value="Hermano(a)">Hermano(a)</option>
                                <option {{$paciente->parentesco == "Sobrino(a)" ? 'selected' : ''}} value="Sobrino(a)">Sobrino(a)</option>
                                <option {{$paciente->parentesco == "Nieto(a)" ? 'selected' : ''}} value="Nieto(a)">Nieto(a)</option>
                                <option {{$paciente->parentesco == "Primo(a)" ? 'selected' : ''}} value="Primo(a)">Primo(a)</option>
                                <option {{$paciente->parentesco == "Familiar" ? 'selected' : ''}} value="Familiar">Familiar</option>
                            </select>     
                            @else
                                <input id="parentesco" type="parentesco" class="form-control input-sm" name="parentesco" value="{{ $paciente->parentesco }}" required readonly >
                            @endif
                            @if ($errors->has('parentesco'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentesco') }}</strong>
                                </span>
                            @endif
                        </div> 
                        <!--seguro-->
                        <div class="form-group col-xs-6{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-xs-8 control-label">Seguro</label>
                            <select id="id_seguro"  onchange="ocultar_seguro()" name="id_seguro" class="form-control">
                                @foreach ($seguros as $seguro)
                                    <option {{$paciente->id_seguro == $seguro->id ? 'selected' : ''}} value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                            </select>      
                            @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--primer nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-xs-8 control-label">Primer Nombre</label>
                            <input id="nombre1" type="text" class="form-control input-sm" name="nombre1" value="{{ $paciente->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                            @endif
                        </div>                
                        <!--segundo nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-xs-8 control-label">Segundo Nombre</label>
                            <input id="nombre2" type="text" class="form-control input-sm" name="nombre2" value="{{ $paciente->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('nombre2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-xs-8 control-label">Primer Apellido</label>
                            <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="{{ $paciente->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--segundo apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-xs-10 control-label">Segundo Apellido</label>
                            <input id="apellido2" type="text" class="form-control input-sm" name="apellido2" value="{{ $paciente->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('apellido2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2') }}</strong>
                                </span>
                            @endif
                        </div> 
                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-xs-8 control-label">Cedula</label>
                            <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ $paciente->id }}" required autofocus onkeyup="validarCedula(this.value);" >
                            @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                            @endif
                        </div> 
                        <!--pais-->
                        <div class="form-group col-xs-6{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais" class="col-xs-8 control-label">Pais</label>
                            <select id="id_pais" name="id_pais" class="form-control input-sm">
                                @foreach ($paises as $pais)
                                    <option {{$paciente->id_pais == $pais->id ? 'selected' : ''}} value="{{$pais->id}}">{{$pais->nombre}}</option>
                                @endforeach
                            </select>      
                            @if ($errors->has('id_pais'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_pais') }}</strong>
                                </span>
                            @endif
                        </div>  
                        <!--ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-xs-8 control-label">Ciudad</label>
                            <input id="ciudad" type="text" class="form-control input-sm" name="ciudad" value="{{ $paciente->ciudad }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-xs-8 control-label">Direccion</label>
                            <input id="direccion" type="text" class="form-control input-sm" name="direccion" value="{{ $paciente->direccion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('direccion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('direccion') }}</strong>
                                </span>
                            @endif
                        </div> 
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-xs-8 control-label">EMail Acceso</label>
                           <input id="email" type="email" class="form-control input-sm" name="email" value="{{$user_aso->email}}" required @if($paciente->id!=$paciente->id_usuario){{'readonly'}}@endif >
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div> 
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-xs-10 control-label">Telefono Domicilio</label>
                            <input id="telefono1" type="text" class="form-control input-sm" name="telefono1" value="{{ $paciente->telefono1 }}" required>
                            @if ($errors->has('telefono1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono1') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-xs-10 control-label">Telefono Celular</label>
                            <input id="telefono2" type="text" class="form-control input-sm" name="telefono2" value="{{ $paciente->telefono2 }}" required>
                            @if ($errors->has('telefono2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-md-12 control-label" style="padding: 0px;">Fecha Nacimiento</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_nacimiento" onchange="edad2();" type="text" class="form-control input-sm" name="fecha_nacimiento" value="{{ $paciente->fecha_nacimiento }}" >
                            </div>    
                            @if ($errors->has('fecha_nacimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- Div de edad -->
                        <div class="form-group col-md-6{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                            <label for="Xedad" class="col-md-10 control-label">Edad</label>
                            <input id="Xedad" type="text" class="form-control input-sm" name="Xedad" readonly="readonly">
                            @if ($errors->has('Xedad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Xedad') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--menor edad-->
                        <div class="form-group col-xs-6{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="menoredad" class="col-xs-10 control-label">Menor de Edad</label>
                            <input id="tmenoredad" type="text" class="form-control input-sm" name="tmenoredad" value=@if($paciente->menoredad==0)"{{'NO'}}"@else"{{'SI'}}"@endif required readonly="readonly">
                            <input id="menoredad" type="hidden" class="form-control" name="menoredad" value="{{$paciente->menoredad}}" >
                            @if ($errors->has('menoredad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('menoredad') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-xs-6{{ $errors->has('sexo') ? ' has-error' : '' }}" >
                            <label for="sexo" class="col-xs-8 control-label">Género</label>
                            <select id="sexo" name="sexo" class="form-control input-sm" required>
                                <option value="">Seleccionar ..</option>
                                <option {{$paciente->sexo == 1 ? 'selected' : ''}} value="1">MASCULINO</option>
                                <option {{$paciente->sexo == 2 ? 'selected' : ''}} value="2">FEMENINO</option>     
                            </select>  
                            @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--estado civil 1=SOLTERO(A) 2=CASADO(A) 3=VIUDO(A) 4=DIVORCIADO(A) 5=UNION LIBRE-->
                        <div class="form-group col-xs-6{{ $errors->has('estadocivil') ? ' has-error' : '' }}" >
                            <label for="estadocivil" class="col-xs-8 control-label">Estado Civil</label>
                            <select id="estadocivil" name="estadocivil" class="form-control input-sm" >
                                <option value="">Seleccionar ..</option>
                                <option {{$paciente->estadocivil == 1 ? 'selected' : ''}} value="1">SOLTERO(A)</option>
                                <option {{$paciente->estadocivil == 2 ? 'selected' : ''}} value="2">CASADO(A)</option>
                                <option {{$paciente->estadocivil == 3 ? 'selected' : ''}} value="3">VIUDO(A)</option>
                                <option {{$paciente->estadocivil == 4 ? 'selected' : ''}} value="4">DIVORCIADO(A)</option>
                                <option {{$paciente->estadocivil == 5 ? 'selected' : ''}} value="5">UNION LIBRE</option>         
                            </select>  
                            @if ($errors->has('estadocivil'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estadocivil') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--Grupo Sanguineo
                        <div class="form-group col-xs-6{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                            <label for="gruposanguineo" class="col-xs-8 control-label">Grupo Sanguineo</label>
                            <select id="gruposanguineo" class="form-control" name="gruposanguineo"  required>
                                <option value="">Seleccionar ..</option>
                                <option {{$paciente->gruposanguineo == "AB+" ? 'selected' : ''}} value="AB+">AB+</option>
                                <option {{$paciente->gruposanguineo == "AB-" ? 'selected' : ''}} value="AB">AB-</option>
                                <option {{$paciente->gruposanguineo == "A+" ? 'selected' : ''}} value="A+">A+</option>
                                <option {{$paciente->gruposanguineo == "A-" ? 'selected' : ''}} value="A-">A-</option>
                                <option {{$paciente->gruposanguineo == "B+" ? 'selected' : ''}} value="B+">B+</option>
                                <option {{$paciente->gruposanguineo == "B-" ? 'selected' : ''}} value="B-">B-</option>
                                <option {{$paciente->gruposanguineo == "O+" ? 'selected' : ''}} value="O+">O+</option>
                                <option {{$paciente->gruposanguineo == "O-" ? 'selected' : ''}} value="O-">O-</option>
                            </select>     
                                @if ($errors->has('gruposanguineo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gruposanguineo') }}</strong>
                                    </span>
                                @endif
                        </div>-->
                        <!--LUGAR NACIMIENTO-->
                        <div class="form-group col-xs-6{{ $errors->has('lugar_nacimiento') ? ' has-error' : '' }}">
                            <label for="lugar_nacimiento" class="col-xs-8 control-label">Lugar de Nacimiento</label>
                            <input id="lugar_nacimiento" type="text" class="form-control input-sm" name="lugar_nacimiento" value="{{ $paciente->lugar_nacimiento }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('lugar_nacimiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('lugar_nacimiento') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--ocupacion-->
                        <div class="form-group col-xs-6{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                            <label for="ocupacion" class="col-xs-8 control-label">Ocupación</label>
                            <input id="ocupacion" type="text" class="form-control input-sm" name="ocupacion" value="{{ $paciente->ocupacion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('ocupacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ocupacion') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--alergias
                        <div class="form-group col-xs-6{{ $errors->has('alergias') ? ' has-error' : '' }}">
                            <label for="alergias" class="col-xs-8 control-label">Alergias</label>
                            <input id="alergias" type="text" class="form-control" name="alergias" value="{{ $paciente->alergias }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('alergias'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('alergias') }}</strong>
                                    </span>
                                @endif
                        </div>-->
                        <!--vacunas
                        <div class="form-group col-xs-6{{ $errors->has('vacuna') ? ' has-error' : '' }}">
                            <label for="vacuna" class="col-xs-8 control-label">vacunas</label>
                            <input id="vacuna" type="text" class="form-control" name="vacuna" value="{{ $paciente->vacuna }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('vacuna'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('vacuna') }}</strong>
                                    </span>
                                @endif
                        </div>-->
                        <!--REFERIDO-->
                        <div class="form-group col-xs-6{{ $errors->has('referido') ? ' has-error' : '' }}">
                            <label for="referido" class="col-xs-8 control-label">Referido</label>
                            <input id="referido" type="text" class="form-control input-sm" name="referido" value="{{ $paciente->referido }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('referido'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('referido') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('ale_list') ? ' has-error' : '' }}">
                            <label for="ale_list">Alergias</label>
                            <select id="ale_list" name="ale_list[]" class="form-control input-sm" multiple>
                                @foreach($alergiasxpac as $ale_pac)
                                <option selected value="{{$ale_pac->id_principio_activo}}">{{$ale_pac->principio_activo->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email_opc') ? ' has-error' : '' }}">
                            <label for="email_opc" class="col-xs-8 control-label">EMail(Opcional)</label>
                               <input id="email_opc" type="email_opc" class="form-control input-sm" name="email_opc" value=@if($paciente->mail_opcional!='')"{{ $paciente->mail_opcional }}"@else @if(old('email_opc')!='')"{{old('email_opc')}}"@else"{{'@'}}"@endif @endif required maxlength="100" >

                                @if ($errors->has('email_opc'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email_opc') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <!--Religion-->
                        <div class="form-group col-xs-6{{ $errors->has('religion') ? ' has-error' : '' }}">
                            <label for="religion" class="col-xs-8 control-label">Religión</label>
                            <input id="religion" type="text" class="form-control input-sm" name="religion" value="{{ $paciente->religion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('religion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('religion') }}</strong>
                                </span>
                            @endif
                        </div>
                         <!--Fecha de Validación-->
                         <div id="fecha_validacion1" class="form-group col-xs-6{{ $errors->has('fecha_val') ? ' has-error' : '' }} oculto">
                            <label for="fecha_val" class="col-xs-8 control-label">Fecha de Validación</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_val" type="text" class="form-control input-sm" name="fecha_val" value="{{$paciente->fecha_val}}" required autofocus>
                            </div>    
                            @if ($errors->has('fecha_val'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_val') }}</strong>
                            </span>
                            @endif
                        </div>
                        

                        <!--Código de validación-->
                        <div  id="cod_validacion1" class="form-group col-xs-6{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                            <label for="cod_val" class="col-xs-8 control-label">Código de Validación</label>
                                <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="{{$paciente->cod_val}}" >
                                @if ($errors->has('cod_val'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cod_val') }}</strong>
                                    </span>
                                @endif
                         

                        </div>
                        <div id="cod_validacion2" class="form-group col-xs-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                            <label for="validacion_cv_msp" class="col-md-12 control-label">Código de Validación</label>

                            <div class="col-md-4">
                                <input id="validacion_cv_msp" type="validacion_cv_msp" class="form-control" name="validacion_cv_msp" value="{{$paciente->validacion_cv_msp}}" >

                                @if ($errors->has('validacion_cv_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_cv_msp') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <input id="validacion_nc_msp" type="validacion_nc_msp" class="form-control" name="validacion_nc_msp" value="{{$paciente->validacion_nc_msp}}" >

                                @if ($errors->has('validacion_nc_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_nc_msp') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <input id="validacion_sec_msp" type="validacion_sec_msp" class="form-control" name="validacion_sec_msp" value="{{$paciente->validacion_sec_msp}}" >

                                @if ($errors->has('validacion_sec_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_sec_msp') }}</strong>
                                    </span>
                                @endif
                            </div>

                          </div>

                        <div class="form-group col-xs-12">&nbsp;</div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" onclick="guardar();" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Ingresar Copia de Cédula</h3></div>
                <form  id="copia_cedula" name="copia_cedula" class="formarchivo" enctype="multipart/form-data" >    
                    <input type="hidden" name="id_usuario_foto" value="{{$paciente->id}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                    <div class="box-body">
                        <div class="form-group col-xs-10" >
                            <!--img src="@if(!is_null($copia_cedula)){{asset('/avatars').'/'.$copia_cedula->nombre}}@else{{asset('/avatars').'/../avatars/avatar.jpg'}}@endif"  alt="User Image"  style="width:120px;height:120px;" id="fotografia_usuario"-->  
                            <a href="{{route('paciente.ver_copia_cedula',['cedula' => $paciente->id])}}" target="_blank" class="btn btn-primary" ><span id="bt_ver">@if(!is_null($copia_cedula))Ver {{$copia_cedula->nombre}}@else Sin Imagen @endif</span></a>
                        </div>
                        <div class="form-group col-xs-10{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label>Agregar Imagen </label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/><br /><br />
                            @if ($errors->has('archivo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('archivo') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button type="button" class="btn btn-primary" onclick="guardar_imagen()">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>                       
        </div>     
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Datos Familiar/Acompañante/Representante</h3></div>
                <form class="formfamiliar" id="formfamiliar" role="form">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">    
                    
                        
                        <!--nombre1familiar-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre1familiar') ? ' has-error' : '' }}">
                            <label for="nombre1familiar" class="col-xs-8 control-label">Primer Nombre</label>
                            <input id="nombre1familiar" type="text" class="form-control" name="nombre1familiar" value="{{ $paciente->nombre1familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre1familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1familiar') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--nombre2familiar-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre2familiar') ? ' has-error' : '' }}">
                            <label for="nombre2familiar" class="col-xs-8 control-label">Segundo Nombre</label>
                            <input id="nombre2familiar" type="text" class="form-control" name="nombre2familiar" value="{{ $paciente->nombre2familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre2familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2familiar') }}</strong>
                                    </span>
                                @endif
                        </div>

                         <!--apellido1familiar-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1familiar') ? ' has-error' : '' }}">
                            <label for="apellido1familiar" class="col-xs-8 control-label">Primer Apellido</label>
                            <input id="apellido1familiar" type="text" class="form-control" name="apellido1familiar" value="{{ $paciente->apellido1familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('apellido1familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1familiar') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--apellido2familiar-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2familiar') ? ' has-error' : '' }}">
                            <label for="apellido2familiar" class="col-xs-8 control-label">Segundo Apellido</label>
                            <input id="apellido2familiar" type="text" class="form-control" name="apellido2familiar" value="{{ $paciente->apellido2familiar }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('apellido2familiar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2familiar') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('cedulafamiliar') ? ' has-error' : '' }}">
                            <label for="cedulafamiliar" class="col-xs-8 control-label">Cedula</label>
                            <input id="cedulafamiliar" maxlength="10" type="text" class="form-control" name="cedulafamiliar" value="{{ $paciente->cedulafamiliar }}" required autofocus onkeyup="validarCedula(this.value);" >
                                @if ($errors->has('cedulafamiliar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cedulafamiliar') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--parentescofamiliar-->
                        <div class="form-group col-xs-6{{ $errors->has('parentescofamiliar') ? ' has-error' : '' }}" >
                            <label for="parentescofamiliar" class="col-xs-8 control-label">Parentesco</label>
                            <select id="parentescofamiliar" name="parentescofamiliar" class="form-control">
                            <option {{$paciente->parentescofamiliar == "Principal" ? 'selected' : ''}} value="Principal">Principal</option>
                            <option {{$paciente->parentescofamiliar == "Padre/Madre" ? 'selected' : ''}} value="Padre/Madre">Padre/Madre</option>
                            <option {{$paciente->parentescofamiliar == "Conyuge" ? 'selected' : ''}} value="Conyuge">Conyuge</option>
                            <option {{$paciente->parentescofamiliar == "Hijo(a)" ? 'selected' : ''}} value="Hijo(a)">Hijo(a)</option>
                            <option {{$paciente->parentescofamiliar == "Hermano(a)" ? 'selected' : ''}} value="Hermano(a)">Hermano(a)</option>
                            <option {{$paciente->parentescofamiliar == "Sobrino(a)" ? 'selected' : ''}} value="Sobrino(a)">Sobrino(a)</option>
                            <option {{$paciente->parentescofamiliar == "Nieto(a)" ? 'selected' : ''}} value="Nieto(a)">Nieto(a)</option>
                            <option {{$paciente->parentescofamiliar == "Primo(a)" ? 'selected' : ''}} value="Primo(a)">Primo(a)</option>
                            <option {{$paciente->parentescofamiliar == "Familiar" ? 'selected' : ''}} value="Familiar">Familiar</option>
                                        
                                </select>  
                                @if ($errors->has('parentescofamiliar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parentescofamiliar') }}</strong>
                                    </span>
                                @endif
                        </div>  


                         <!--telefono3-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono3') ? ' has-error' : '' }}">
                            <label for="telefono3" class="col-xs-10 control-label">Telefono Familiar</label>
                            <input id="telefono3" type="text" class="form-control" name="telefono3" value="{{ $paciente->telefono3 }}" required>
                                @if ($errors->has('telefono3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono3') }}</strong>
                                    </span>
                                @endif
                        </div> 

                        <div class="form-group col-xs-12">&nbsp;</div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-7">   
                                <div class="box-footer">
                                    <button type="button" onclick="guardar_familiar()" class="btn btn-primary">Actualizar</button>
                                </div>
                            </div>
                        </div> 
                    </div>
                </form>
            </div>                       
        </div>
    </div>
    <div class="row">    
        @if($paciente->parentesco!='Principal')
        <div class="col-sm-6">
            <div class="box box-primary" style="margin-bottom: 5px;"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Representante Principal</h3></div>
                <form class="form-vertical" role="form" method="POST" id="form_principal">
                    <div class="box-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id_paciente_pr" value="{{$paciente->id}}">
                        <div class="form-group col-xs-12">
                            <span id="usuario_existe"></span>
                        </div>    
                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('id_2') ? ' has-error' : '' }}">
                            <label for="id_2" class="col-xs-8 control-label">Cedula</label>
                            <input id="id_2" maxlength="10" type="text" class="form-control input-sm" name="id_2" value="{{ $paciente->id_usuario }}" required autofocus onkeyup="validarCedula(this.value);" onchange="usuario()">
                            @if ($errors->has('id_2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--papa mama-->
                        <div class="form-group col-xs-6 oculto{{ $errors->has('papa_mama') ? ' has-error' : '' }}" id="div_papa_mama">
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
                        <!--primer nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre1_2') ? ' has-error' : '' }}">
                            <label for="nombre1_2" class="col-xs-8 control-label">Primer Nombre</label>
                            <input id="nombre1_2" type="text" class="form-control input-sm" name="nombre1_2" value="{{ $user_aso->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50">
                            @if ($errors->has('nombre1_2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1_2') }}</strong>
                                </span>
                            @endif
                        </div>                    
                        <!--segundo nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre2_2') ? ' has-error' : '' }}">
                            <label for="nombre2_2" class="col-xs-8 control-label">Segundo Nombre</label>
                            <input id="nombre2_2" type="text" class="form-control input-sm" name="nombre2_2" value="{{ $user_aso->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="50">
                            @if ($errors->has('nombre2_2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2_2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1_2') ? ' has-error' : '' }}">
                            <label for="apellido1_2" class="col-xs-8 control-label">Primer Apellido</label>
                            <input id="apellido1_2" type="text" class="form-control input-sm" name="apellido1_2" value="{{ $user_aso->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50">
                            @if ($errors->has('apellido1_2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1_2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--segundo apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2_2') ? ' has-error' : '' }}">
                            <label for="apellido2_2" class="col-xs-10 control-label">Segundo Apellido</label>
                            <input id="apellido2_2" type="text" class="form-control input-sm" name="apellido2_2" value="{{ $user_aso->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus maxlength="50">
                            @if ($errors->has('apellido2_2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2_2') }}</strong>
                                </span>
                            @endif
                        </div>   
                        <!--pais-->
                        <div class="form-group col-xs-6{{ $errors->has('id_pais_2') ? ' has-error' : '' }}">
                            <label for="id_pais_2" class="col-xs-8 control-label">Pais</label>
                            <select id="id_pais_2" name="id_pais_2" class="form-control input-sm">
                                    @foreach ($paises as $pais)
                                        <option {{$user_aso->id_pais == $pais->id ? 'selected' : ''}} value="{{$pais->id}}">{{$pais->nombre}}</option>
                                    @endforeach
                                </select>      
                                @if ($errors->has('id_pais_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais_2') }}</strong>
                                    </span>
                                @endif
                        </div> 
                        <!--ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad_2') ? ' has-error' : '' }}">
                            <label for="ciudad_2" class="col-xs-8 control-label">Ciudad</label>
                            <input id="ciudad_2" type="text" class="form-control input-sm" name="ciudad_2" value="{{ $user_aso->ciudad }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="50">
                                @if ($errors->has('ciudad_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad_2') }}</strong>
                                    </span>
                                @endif
                        </div> 
                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion_2') ? ' has-error' : '' }}">
                            <label for="direccion_2" class="col-xs-8 control-label">Direccion</label>
                            <input id="direccion_2" type="text" class="form-control input-sm" name="direccion_2" value="{{ $user_aso->direccion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="100">
                                @if ($errors->has('direccion_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion_2') }}</strong>
                                    </span>
                                @endif
                        </div> 
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email_2') ? ' has-error' : '' }}">
                            <label for="email_2" class="col-xs-8 control-label">EMail Acceso</label>
                               <input id="email_2" type="email_2" class="form-control input-sm" name="email_2" value="{{ $user_aso->email }}" required maxlength="100" >

                                @if ($errors->has('email_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email_2') }}</strong>
                                    </span>
                                @endif
                        </div> 
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1_2') ? ' has-error' : '' }}">
                            <label for="telefono1_2" class="col-xs-10 control-label">Telefono Domicilio</label>
                            <input id="telefono1_2" type="text" class="form-control input-sm" name="telefono1_2" value="{{ $user_aso->telefono1 }}" required maxlength="50">
                                @if ($errors->has('telefono1_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1_2') }}</strong>
                                    </span>
                                @endif
                        </div> 
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2_2') ? ' has-error' : '' }}">
                            <label for="telefono2_2" class="col-xs-10 control-label">Telefono Celular</label>
                            <input id="telefono2_2" type="text" class="form-control input-sm" name="telefono2_2" value="{{ $user_aso->telefono2 }}" required maxlength="50">
                                @if ($errors->has('telefono2_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2_2') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento_2') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento_2" class="col-md-12 control-label" style="padding: 0px;">Fecha Nacimiento</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_nacimiento_2" onchange="edad2();" type="text" class="form-control input-sm" name="fecha_nacimiento_2" value="{{ $user_aso->fecha_nacimiento }}" >
                            </div>    
                            @if ($errors->has('fecha_nacimiento_2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_nacimiento_2') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div class="form-group col-xs-12">&nbsp;</div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" onclick="guardar_principal();" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>        
                </form>
            </div>
        </div> 
        @endif
        @if($paciente->parentesco=='Hijo(a)')
        <div class="col-sm-6">
            <div class="box box-primary" style="margin-bottom: 5px;"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Representante (Opcional)</h3></div>
                <form class="form-vertical" role="form" method="POST" id="form_opcional">
                    <div class="box-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id_pac" id="id_pac" value="{{$paciente->id}}">
                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('id_3') ? ' has-error' : '' }}">
                            <label for="id_3" class="col-xs-8 control-label">Cedula</label>
                            <input id="id_3" maxlength="10" type="text" class="form-control input-sm" name="id_3" value="@if(!is_null($repre_opc)){{ $repre_opc->cedula_fam }}@endif" required autofocus onkeyup="validarCedula(this.value);">
                            @if ($errors->has('id_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_3') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--papa mama-->
                        <div class="form-group col-xs-6 {{ $errors->has('papa_mama_3') ? ' has-error' : '' }}">
                            <label for="papa_mama_3" class="col-md-12 control-label">Padre/Madre</label>
                            <select id="papa_mama_3" name="papa_mama_3" class="form-control input-sm">
                                <option value="">Seleccione ...</option>
                                <option @if(!is_null($repre_opc))@if($repre_opc->papa_mama=='Padre') selected @endif @endif value="Padre">Padre</option>
                                <option @if(!is_null($repre_opc))@if($repre_opc->papa_mama=='Madre') selected @endif @endif value="Madre">Madre</option>
                            </select>
                            @if ($errors->has('papa_mama_3'))
                            <span class="help-block">
                                <strong>{{ $errors->first('papa_mama_3') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--primer nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre1_3') ? ' has-error' : '' }}">
                            <label for="nombre1_3" class="col-xs-8 control-label">Primer Nombre</label>
                            <input id="nombre1_3" type="text" class="form-control input-sm" name="nombre1_3" value="@if(!is_null($repre_opc)){{ $repre_opc->nombre1 }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre1_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1_3') }}</strong>
                                </span>
                            @endif
                        </div>                    
                        <!--segundo nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre2_3') ? ' has-error' : '' }}">
                            <label for="nombre2_3" class="col-xs-8 control-label">Segundo Nombre</label>
                            <input id="nombre2_3" type="text" class="form-control input-sm" name="nombre2_3" value="@if(!is_null($repre_opc)){{ $repre_opc->nombre2 }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                            @if ($errors->has('nombre2_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2_3') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1_3') ? ' has-error' : '' }}">
                            <label for="apellido1_3" class="col-xs-8 control-label">Primer Apellido</label>
                            <input id="apellido1_3" type="text" class="form-control input-sm" name="apellido1_3" value="@if(!is_null($repre_opc)){{ $repre_opc->apellido1 }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('apellido1_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1_3') }}</strong>
                                </span>
                            @endif
                        </div>
                        <!--segundo apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2_3') ? ' has-error' : '' }}">
                            <label for="apellido2_3" class="col-xs-10 control-label">Segundo Apellido</label>
                            <input id="apellido2_3" type="text" class="form-control input-sm" name="apellido2_3" value="@if(!is_null($repre_opc)){{ $repre_opc->apellido2 }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('apellido2_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2_3') }}</strong>
                                </span>
                            @endif
                        </div>   
                        <div class="form-group col-xs-12">&nbsp;</div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" onclick="guardar_opcional();" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>        
                </form>
            </div>
        </div> 
        @endif

            
        <!--right
        <div class="col-sm-5">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Asociado Principal</h3></div>
                    <div class="box-body">
                        
                        <div class="form-group col-xs-6">
                            <label for="id_user" class="col-xs-8 control-label">Cedula</label>
                            <input id="id_user" type="text" class="form-control" name="id_user" value="{{ $user_aso->id }}" readonly="readonly" >
                        </div>
                     
                        <div class="form-group col-xs-6">
                            <label for="nombres_user" class="col-xs-8 control-label">Nombre</label>
                            <input id="nombres_user" type="text" class="form-control" name="nombres_user" value="{{$user_aso->nombre1}} {{$user_aso->nombre2}} {{$user_aso->apellido1}} {{$user_aso->apellido2}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly" >
                        </div>                                                
                    </div>
            </div>                       
        </div>-->
       <!--right
        <div class="col-sm-5">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Cambiar Fotografia</h3></div>
                <form  id="subir_imagen" name="subir_imagen" method="post"  action="{{route('paciente.subir_imagen_usuario', ['id' => $paciente->id])}}" class="formarchivo" enctype="multipart/form-data" >    
                    <input type="hidden" name="id_usuario_foto" value="{{$paciente->id}}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                    <div class="box-body">
                        <div class="form-group col-xs-10" >
                            <input type="hidden" name="carga" value="@if($paciente->imagen_url==' ') 
                               {{$paciente->imagen_url='../avatars/avatar.jpg'}} 
                            @endif">
                            <img src="{{asset('/avatars').'/'.$paciente->imagen_url}}"  alt="User Image"  style="width:120px;height:120px;" id="fotografia_usuario" >
                            
                        </div>
                        <div class="form-group col-xs-10{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label>Agregar Imagen </label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/><br /><br />
                            @if ($errors->has('archivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Actualizar Imagen</button>
                        </div>
                    </div>
                </form>
            </div>                       
        </div--> 
    
    </div>
</div>

<script src="{{ asset ("/js/paciente.js") }}"></script>

<script type="text/javascript">
/*$('#fecha_nacimiento').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    time: false,
    format : 'YYYY-MM-DD',
    lang : 'es',
});*/
$('#fecha_nacimiento').datetimepicker({
    useCurrent: false,
    format: 'YYYY/MM/DD',
     //Important! See issue #1075
    
});
$('#fecha_val').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
        });

$("#fecha_nacimiento").on("dp.change", function (e) {
    edad2();
});

$("#fecha_val").on("dp.change", function (e) {
    //valida_fval2(e);
});

$(document).ready(function () {
    edad2();

    $("#div_papa_mama").addClass("oculto");
    var parentesco = $("#parentesco").val();
    if(parentesco=='Hijo(a)'){
        $("#div_papa_mama").removeClass("oculto");
    }
});

$(document).ready(function(){

        var id_seguro = document.getElementById("id_seguro").value;
 
        if (id_seguro == '3') {//issfa
           $("#fecha_validacion1").removeClass("oculto");    
           $('#cod_validacion1').removeClass("oculto");
           $('#fecha_val').prop("required", true);
           $('#cod_val').prop("required", true);
           $("#cod_validacion2").addClass("oculto");
           $('#cod_validacion2').removeAttr("required");
        }else if(id_seguro == '5'){//Msp
            $("#fecha_validacion1").removeClass("oculto");
            $("#cod_validacion2").removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion2').prop("required", true);
            $('#cod_validacion1').addClass("oculto");//Cod val issfa y isspol
            $('#cod_validacion1').removeAttr("required");
        }else if(id_seguro == '6'){//isspol
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_val').prop("required", true);
            $('#cod_val').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
            $('#cod_validacion2').removeAttr("required");
        }else{//Otro tipo de Seguro menos isspol-issfa-msp
            $("#fecha_validacion1").addClass("oculto");
            $("#cod_validacion1").addClass("oculto");
            $("#cod_validacion2").addClass("oculto");//Cod val Msp
            $('#fecha_validacion1').removeAttr("required");
            $('#cod_validacion1').removeAttr("required");
            $('#cod_validacion2').removeAttr("required");
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
        }
});

function ocultar_seguro(){

   var id_seguro = document.getElementById("id_seguro").value;
    
        if (id_seguro == '3') {//ISSFA
          
           $("#fecha_validacion1").removeClass("oculto");
           $('#cod_validacion1').removeClass("oculto");
           $('#fecha_val').prop("required", true);
           $('#cod_validacion1').prop("required", true);
           $("#cod_validacion2").addClass("oculto");
           $('#cod_validacion2').removeAttr("required");
        }else if(id_seguro == '5'){//msp
            $("#fecha_validacion1").removeClass("oculto");
            $("#cod_validacion2").removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion2').prop("required", true);
            $('#cod_validacion1').addClass("oculto");//Cod val issfa y isspol
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
        }else if(id_seguro == '6'){//isspol
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_val').prop("required", true);
            $('#cod_val').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");
        }else{
            $("#fecha_validacion1").addClass("oculto");
            $('#fecha_validacion1').removeAttr("required");
            $("#cod_validacion1").addClass("oculto");
            $('#cod_validacion1').removeAttr("required");
            $("#cod_validacion2").addClass("oculto");//Cod val Msp
            $('#cod_validacion2').removeAttr("required");
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
            $('#validacion_cv_msp').val(" ");
            $('#validacion_nc_msp').val(" ");
            $('#validacion_sec_msp').val(" ");

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
        },
        cache: true
    }
});

function guardar(){
    $.ajax({
        type: "post",
            url: "{{ route('paciente.update', ['id' => $paciente->id]) }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data: $("#form_paciente").serialize(),
            success: function(datahtml){
                console.log(datahtml);
                if(datahtml=='ok'){
                    $(".alerta_ok").fadeIn(1000);
                    $(".alerta_ok").fadeOut(20000); 
                }  
            },
            error: function(datahtml){
                var err = '';
                $.each(datahtml.responseJSON, function (ind, elem) { 
                  err = err + elem+'<br>';
                  $('#'+ind).parent().addClass('has-error');
                  //console.log(err); 
                }); 
                if(err!=''){
                    $("#err").html(err);
                    $("#actualiza").text(" Paciente");
                    $(".alerta_correcto").fadeIn(1000);
                    $(".alerta_correcto").fadeOut(10000);
                }
            }
    });
}
function valida_fval2(e)
    {
       
        var fecha_val = document.getElementById("fecha_val").value;
        var anio = fecha_val.substr(0,4);
        var mes = fecha_val.substr(5,2);
        var dia = fecha_val.substr(8,2);
       
        var mes_2 = parseInt(mes)-1;
      
        var fecha_nueva = new Date(anio,mes_2, dia);

        var fecha = new Date();
     
        if(fecha_nueva < fecha){
            
            alert("La Fecha de Validacion no debe ser menor a la Fecha Actual")   
            $('#fecha_val').val("");
           //console.log("Entra a");
        }
        

    }

function guardar_principal(){
    $.ajax({

        type: "post",
            url: "{{ route('paciente.guardar_principal')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data: $("#form_principal").serialize(),
            success: function(datahtml){
                console.log(datahtml);
                if(datahtml=='ok'){
                    $("#actualiza").text(" Representante Principal");
                    $(".alerta_ok").fadeIn(1000);
                    $(".alerta_ok").fadeOut(20000); 
                }  
            },
            error: function(datahtml){
                console.log(datahtml);
                var err = '';
                $.each(datahtml.responseJSON, function (ind, elem) { 
                  err = err + elem+'<br>';
                  $('#'+ind).parent().addClass('has-error');
                  //console.log(err); 
                }); 
                if(err!=''){
                    $("#err").html(err);
                    $(".alerta_correcto").fadeIn(1000);
                    $(".alerta_correcto").fadeOut(10000);
                }
            }
    });
}

function guardar_opcional(){
    $.ajax({
        type: "post",
            url: "{{ route('paciente.guardar_opcional')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data: $("#form_opcional").serialize(),
            success: function(datahtml){
                console.log(datahtml);
                if(datahtml=='ok'){
                    $("#actualiza").text(" Representante Principal");
                    $(".alerta_ok").fadeIn(1000);
                    $(".alerta_ok").fadeOut(20000); 
                }  
            },
            error: function(datahtml){
                console.log(datahtml);
                var err = '';
                $.each(datahtml.responseJSON, function (ind, elem) { 
                  err = err + elem+'<br>';
                  $('#'+ind).parent().addClass('has-error');
                  //console.log(err); 
                }); 
                if(err!=''){
                    $("#err").html(err);
                    $(".alerta_correcto").fadeIn(1000);
                    $(".alerta_correcto").fadeOut(10000);
                }
            }
    });
}

function guardar_familiar(){
    $.ajax({
        type: "post",
            url: "{{ route('paciente.updatefamiliar', ['id' => $paciente->id], ['id' => '0'] ) }}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data: $("#formfamiliar").serialize(),
            success: function(datahtml){
                console.log(datahtml);
                if(datahtml=='ok'){
                    $("#actualiza").text(" Acompañante");
                    $(".alerta_ok").fadeIn(1000);
                    $(".alerta_ok").fadeOut(20000); 
                }  
            },
            error: function(datahtml){
                console.log(datahtml);
                var err = '';
                $.each(datahtml.responseJSON, function (ind, elem) { 
                  err = err + elem+'<br>';
                  $('#'+ind).parent().addClass('has-error');
                  //console.log(err); 
                }); 
                if(err!=''){
                    $("#err").html(err);
                    $(".alerta_correcto").fadeIn(1000);
                    $(".alerta_correcto").fadeOut(10000);
                }
            }
    });
}

function guardar_imagen(){
    var formu = new FormData(document.getElementById('copia_cedula'));
    //console.log(formu);
    $.ajax({
        type: "post",
        url: "{{route('paciente.subir_copia')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        data: formu,
        processData: false,
        contentType: false,
        dataType: 'html',
        success: function(datahtml){
            console.log(datahtml);
            if(datahtml=="ok"){  
                $("#actualiza").text(" Copia de Cédula"); 
                $(".alerta_ok").fadeIn(1000);
                $(".alerta_ok").fadeOut(20000);
                $("#bt_ver").text('Ver copia_{{$paciente->id}}');
            }  
        },
        error: function(datahtml){
            console.log(datahtml);
            var json = JSON.parse(datahtml.responseText);
            console.log(json);
            var err = '';
            $.each(json, function (ind, elem) { 
              err = err + elem+'<br>';
              $('#'+ind).parent().addClass('has-error');
              //console.log(err); 
            }); 
          
            if(err!=''){
                $("#err").html(err);
                $(".alerta_correcto").fadeIn(1000);
                $(".alerta_correcto").fadeOut(10000);
            }
        }
    });
}

function usuario()
{
    vcedula = document.getElementById("id_2").value;
    vcedula =  vcedula.trim();
    
    $.ajax({
    type: 'get',
    url:'{{ url("nuevo_agenda/paciente")}}/'+vcedula,
    datatype: 'json',
    success: function(data){
        console.log(data);
            if(data!='no'){
                $('#usuario_existe').text('**Usuario ya registrado en el sistema');
                $('#nombre1_2').val(data.nombre1);
                $('#nombre2_2').val(data.nombre2);
                $('#apellido1_2').val(data.apellido1);
                $('#apellido2_2').val(data.apellido2);
                $('#telefono1_2').val(data.telefono1);
                $('#telefono2_2').val(data.telefono2);
                $('#ciudad_2').val(data.ciudad);
                $('#fecha_nacimiento_2').val(data.fecha_nacimiento); 
                $('#email_2').val(data.email);    
                $('#id_pais_2 option[value="'+data.id_pais+'"]').attr("selected", true);
            }    
        },
    })
}



</script> 
@endsection
