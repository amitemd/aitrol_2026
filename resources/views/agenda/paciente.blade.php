@extends('agenda.base')

@section('action-content')
<style type="text/css">
    .ro{
        color: orange;
    }
    .ro_2{
        color: blue;
    }
    .radio_origen_2.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .radio_origen_3.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .box-title,.form-group,.box{
        margin: 0;
    }
    h3{
        margin: 0;
    }


</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

    <form class="form-vertical" role="form" method="POST" action="{{ route('agenda.guardar') }}" id="form">
        {{ csrf_field() }}
        <input id="doctor"  type="hidden"  name="doctor" value="{{$id}}" " >
        <input id="fecha"  type="hidden"  name="fecha" value="{{$fecha}}" " >
        <input type="hidden" name="sala" id="sala" value="{{$sala}}">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h4 class="box-title">@lang('agenda.datosPaciente_')</h4>
                    </div>
                    <div class="box-body no-padding">
                        <div class="col-md-6">
                            <div class="alert alert-warning m1 oculto">
                                <strong>@lang('agenda.atencion')!</strong> <span id="alertms"></span>
                            </div>
                            <div class="alert alert-warning m2 oculto">
                                <strong>@lang('agenda.atencion')!</strong> <span id="alertms2"></span>
                            </div>
                            <!--cedula-->
                            <div class="form-group col-md-6 {{ $errors->has('id2') ? ' has-error' : '' }}">
                                <label for="id2" class="col-md-12 control-label">@lang('agenda.cedula')</label>
                                <div class="col-md-12">
                                    <input id="id2" maxlength="10" type="text" class="form-control input-sm" name="id2" value="@if($i != '' && $i != '0'){{$i}}@else{{old('id2')}}@endif" required autofocus onkeyup="validarCedula(this.value);">
                                    @if ($errors->has('id2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--PRIMER NOMBRE-->
                            <div class="form-group col-md-6 {{ $errors->has('nombre12') ? ' has-error' : '' }}">
                                <label for="nombre12" class="col-md-12 control-label">@lang('agenda.primerNombre')</label>
                                <div class="col-md-12">
                                    <input id="nombre12" type="text" class="form-control input-sm" name="nombre12" value="{{ old('nombre12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();" maxlength="50">
                                    @if ($errors->has('nombre12'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('nombre12') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--//segundo nombre-->
                            <div class="form-group col-md-6 {{ $errors->has('nombre22') ? ' has-error' : '' }}">
                                <label for="nombre22" class="col-md-12 control-label">@lang('agenda.segundoNombre')</label>
                                <div class="col-md-12">
                                    <div class="input-group dropdown">
                                        <input id="nombre22" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre22" value="{{ old('nombre22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus onchange="busca_usuario_nombre1();" maxlength="50">
                                        <ul class="dropdown-menu usuario3" id="dnombre22">
                                            <li><a data-value="N/A">N/A</a></li>
                                        </ul>
                                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                    </div>
                                    @if ($errors->has('nombre22'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre22') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                            </div>
                            <!--primer apellido-->
                            <div class="form-group col-md-6{{ $errors->has('apellido12') ? ' has-error' : '' }}">
                                <label for="apellido12" class="col-md-12 control-label">@lang('agenda.primerApellido')</label>
                                <div class="col-md-12">
                                    <input id="apellido12" type="text" class="form-control input-sm" name="apellido12" value="{{ old('apellido12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();" maxlength="50">
                                    @if ($errors->has('apellido12'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido12') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--Segundo apellido-->
                            <div class="form-group col-md-6 {{ $errors->has('apellido22') ? ' has-error' : '' }}">
                                <label for="apellido22" class="col-md-12 control-label">@lang('agenda.segundoApellido')</label>
                                <div class="col-md-12">
                                    <div class="input-group dropdown">
                                        <input id="apellido22" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido22" value="{{ old('apellido22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus onchange="busca_usuario_nombre1();" maxlength="50">
                                        <ul class="dropdown-menu usuario3">
                                            <li><a data-value="N/A">N/A</a></li>
                                        </ul>
                                        <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                    </div>
                                    @if ($errors->has('apellido22'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido22') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                            </div>
                            <!--telefono1-->
                            <div class="form-group col-md-6{{ $errors->has('telefono12') ? ' has-error' : '' }}">
                                <label for="telefono12" class="col-md-12 control-label">@lang('agenda.telefonoDomicilio')</label>
                                <div class="col-md-12">
                                    <input id="telefono12" type="text" class="form-control input-sm" name="telefono12" value="{{ old('telefono12') }}" required autofocus maxlength="50">
                                    @if ($errors->has('telefono12'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('telefono12') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--telefono2-->
                            <div class="form-group col-md-6{{ $errors->has('telefono22') ? ' has-error' : '' }}">
                                <label for="telefono22" class="col-md-12 control-label">@lang('agenda.telefonoCelular')</label>
                                <div class="col-md-12">
                                    <input id="telefono22" type="text" class="form-control input-sm" name="telefono22" value="{{ old('telefono22') }}" required autofocus maxlength="50">
                                    @if ($errors->has('telefono22'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('telefono22') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--Pais-->
                            <div class="form-group col-md-6{{ $errors->has('id_pais2') ? ' has-error' : '' }}">
                                <label for="id_pais2" class="col-md-12 control-label">@lang('agenda.pais')</label>
                                <div class="col-md-12">
                                    <select id="id_pais2" name="id_pais2" class="form-control input-sm" onchange="cambia_pais()">
                                        @foreach($pais as $pvalor)
                                        <option @if(old('id_pais2') == $pvalor->id) selected @endif value="{{$pvalor->id}}">{{$pvalor->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('id_pais2'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('id_pais2') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--fecha_nacimiento-->
                            <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento2') ? ' has-error' : '' }}{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                                <label for="fecha_nacimiento2" class="col-md-12 control-label">@lang('agenda.fechaNacimiento')</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input onchange="edad();" id="fecha_nacimiento2" type="text" class="form-control input-sm" name="fecha_nacimiento2" value="{{ old('fecha_nacimiento2') }}" required autofocus placeholder="1980/01/01">
                                        @if ($errors->has('fecha_nacimiento2'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('fecha_nacimiento2') }}</strong>
                                        </span>
                                        @endif
                                        @if ($errors->has('menoredad'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('menoredad') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Div de edad -->
                            <div class="form-group col-md-6{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-12 control-label">@lang('agenda.edad')</label>
                                <div class="col-md-12">
                                    <input id="Xedad" type="text" class="form-control input-sm" name="Xedad" readonly="readonly">

                                    @if ($errors->has('Xedad'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('Xedad') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <input id="menoredad" type="hidden" class="form-control input-sm" name="menoredad" >
                            <!--menoredad
                            <div class="form-group col-md-6{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                                <label for="menoredad" class="col-md-12 control-label input-sm">Menor de Edad</label>
                                <div class="col-md-12">
                                    <input id="tmenoredad" type="text" class="form-control input-sm" name="tmenoredad" required autofocus  readonly="readonly">
                                    <input id="menoredad" type="hidden" class="form-control input-sm" name="menoredad" >

                                    @if ($errors->has('menoredad'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('menoredad') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            terceraedad
                            <div class="form-group col-md-6" >
                                <label for="terceraedad" class="col-md-4 control-label">Tercera Edad</label>
                                <div class="col-md-7">
                                    <input id="terceraedad" type="text" class="form-control input-sm" name="terceraedad" required autofocus  readonly="readonly">
                                 </div>
                            </div> -->

                        <!--Fecha de Validación-->
                        <div id="fecha_validacion1" class="form-group col-xs-6{{ $errors->has('fecha_val') ? ' has-error' : '' }} oculto">
                               <label for="fecha_val" class="col-md-12 control-label">@lang('agenda.fechavalidacion')</label>
                                <div class="col-md-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right input-sm" name="fecha_val" onchange="valida_fval(event);" id="fecha_val" value="{{ old('fecha_val') }}">
                                        @if ($errors->has('fecha_val'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('fecha_val') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                           </div>

                           <!--Código de validación-->
                          <div id="cod_validacion1" class="form-group col-xs-6{{ $errors->has('cod_val') ? ' has-error' : '' }} oculto">
                              <label for="cod_val" class="col-md-12 control-label">@lang('agenda.codigovalidacion')</label>

                             <div class="col-md-12">
                                <input id="cod_val" type="cod_val" class="form-control" name="cod_val" value="{{ old('cod_val') }}" >

                                @if ($errors->has('cod_val'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cod_val') }}</strong>
                                    </span>
                                @endif
                              </div>

                            </div>
                           <div id="cod_validacion2" class="form-group col-xs-6{{ $errors->has('validacion_cv_msp') ? ' has-error' : '' }} oculto">
                             <label for="validacion_cv_msp" class="col-md-12 control-label">@lang('agenda.codigovalidacion')</label>

                             <div class="col-md-4">
                                  <input id="validacion_cv_msp" type="validacion_cv_msp" class="form-control" name="validacion_cv_msp" value="{{ old('validacion_cv_msp') }}" >

                                 @if ($errors->has('validacion_cv_msp'))
                                     <span class="help-block">
                                        <strong>{{ $errors->first('validacion_cv_msp') }}</strong>
                                     </span>
                                  @endif
                               </div>
                             <div class="col-md-4">
                                  <input id="validacion_nc_msp" type="validacion_nc_msp" class="form-control" name="validacion_nc_msp" value="{{ old('validacion_nc_msp') }}" >

                                 @if ($errors->has('validacion_nc_msp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('validacion_nc_msp') }}</strong>
                                    </span>
                                  @endif
                              </div>
                             <div class="col-md-4">
                                 <input id="validacion_sec_msp" type="validacion_sec_msp" class="form-control" name="validacion_sec_msp" value="{{ old('validacion_sec_msp') }}" >

                                  @if ($errors->has('validacion_sec_msp'))
                                     <span class="help-block">
                                        <strong>{{ $errors->first('validacion_sec_msp') }}</strong>
                                    </span>
                                  @endif
                              </div>
                         </div>

                    </div>

                        <div class="col-md-6">
                            <!--parentesco-->
                            <div class="form-group col-md-6{{ $errors->has('parentesco') ? ' has-error' : '' }}">
                                <label for="parentesco" class="col-md-12 control-label">@lang('agenda.parentesco')</label>
                                <div class="col-md-12">
                                    <select id="parentesco" name="parentesco" class="form-control input-sm" required >
                                        <option @if(old('parentesco')== 'Principal') selected @endif value="Principal">@lang('agenda.principal')</option>
                                        <option @if(old('parentesco')== 'Padre/Madre') selected @endif value="Padre/Madre">@lang('agenda.padre')/@lang('agenda.madre')</option>
                                        <option @if(old('parentesco')== 'Conyuge') selected @endif value="Conyugue">@lang('agenda.conyuge')</option>
                                        <option @if(old('parentesco')== 'Hijo(a)') selected @endif value="Hijo(a)">@lang('agenda.hijo')</option>
                                        <option @if(old('parentesco')== 'Hermano(a)') selected @endif value="Hermano(a)">@lang('agenda.hermano')</option>
                                        <option @if(old('parentesco')== 'Sobrino(a)') selected @endif value="Sobrino(a)">@lang('agenda.sobrino')</option>
                                        <option @if(old('parentesco')== 'Nieto(a)') selected @endif value="Nieto(a)">@lang('agenda.nieto')</option>
                                        <option @if(old('parentesco')== 'Primo(a)') selected @endif value="Primo(a)">@lang('agenda.primo')</option>
                                        <option @if(old('parentesco')== 'Familiar') selected @endif value="Familiar">@lang('agenda.familiar')</option>
                                    </select>
                                    @if ($errors->has('parentesco'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('parentesco') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!--id_tipo_seguro-->
                            <div class="form-group col-md-6{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                                <label for="id_seguro" class="col-md-12 control-label">@lang('agenda.tipoSeguro')</label>
                                <div class="col-md-12">
                                    <select id="id_tipo_seguro" name="id_seguro" class="form-control input-sm" required>
                                            <option value="">@lang('agenda.seleccione') ..</option>
                                        @foreach($seguros as $seguro)
                                            <option @if(old('id_seguro')== $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('id_seguro'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('id_seguro') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!--origen-->
                            <div class="form-group col-md-12" >
                                <label for="origen" class="col-md-12 control-label">@lang('agenda.origen')</label>
                                <div class="col-md-12">
                                    <input class="radio_origen" type="radio" name="origen" required id="medio_impreso" value="MEDIO IMPRESO" @if(old('origen')=='MEDIO IMPRESO') checked @endif>
                                        <span class="ro"> @lang('agenda.medioImpreso-')</span><br>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_2" type="radio" name="origen_impreso" value="REVISTA" @if(old('origen_impreso')=='REVISTA') checked @endif>
                                        <span class="ro_2"> @lang('agenda.revista-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_2" type="radio" name="origen_impreso" value="FLYERS" @if(old('origen_impreso')=='FLYERS') checked @endif>
                                        <span class="ro_2"> @lang('agenda.flyers-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_2" type="radio" name="origen_impreso" value="PERIODICO" @if(old('origen_impreso')=='PERIODICO') checked @endif>
                                        <span class="ro_2"> @lang('agenda.periodico-')</span><br>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_2" type="radio" name="origen_impreso" id="origen_otros" value="OTROS" @if(old('origen_impreso')=='OTROS') checked @endif>
                                        <span class="ro_2"> @lang('agenda.otros-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="input-sm" type="text" name="impreso_otros" id="impreso_otros" maxlength="100" value="{{old('impreso_otros')}}"><br>
                                    <input class="radio_origen" type="radio" name="origen" id="medio_digital" required value="MEDIO DIGITAL" @if(old('origen')=='MEDIO DIGITAL') checked @endif>
                                        <span class="ro">@lang('agenda.medioDigital-')</span><br>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="FACEBOOK" @if(old('origen_digital')=='FACEBOOK') checked @endif>
                                        <span class="ro_2"> @lang('agenda.facebook-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="INSTAGRAM" @if(old('origen_digital')=='INSTAGRAM') checked @endif>
                                        <span class="ro_2"> @lang('agenda.instagram-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="EMAIL" @if(old('origen_digital')=='EMAIL') checked @endif>
                                        <span class="ro_2"> @lang('agenda.email-')</span><br>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="GOOGLE" @if(old('origen_digital')=='GOOGLE') checked @endif>
                                        <span class="ro_2"> @lang('agenda.google-')</span> <br>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input class="radio_origen_3" type="radio" name="origen_digital" value="OTROS" id="origen_otros2" @if(old('origen_digital')=='OTROS') checked @endif>
                                        <span class="ro_2"> @lang('agenda.otros-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input type="text" class="input-sm" name="digital_otros" id="digital_otros" maxlength="100" value="{{old('digital_otros')}}"><br>
                                    <input class="radio_origen" type="radio" name="origen" required value="REFERIDO" id="ireferido" @if(old('origen')=='REFERIDO') checked @endif>
                                        <span class="ro"> @lang('agenda.referido-')</span>
                                        <span>&nbsp;&nbsp;&nbsp;</span>
                                        <input type="text" class="input-sm" name="referido" id="referido" value="{{old('referido')}}"><br>
                                 </div>
                            </div>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 oculto" id="div_repre">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h4 class="box-title">@lang('agenda.datosRepresentante')</h4>
                    </div>

                    <div class="box-body no-padding">
                        <span class="help-block" id="usuario_existe">@if($user != Array()){{'**Usuario ya registrado en el sistema'}}@endif</span>
                        <!--cedula-->
                        <div class="form-group col-md-3 {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-12 control-label">@lang('agenda.cedula')</label>
                            <div class="col-md-12">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="@if($user != Array()) {{$user[0]->id}} @else{{old('id')}}@endif" required autofocus onchange="usuario();" @if($user != Array()){{'readonly'}}@endif >
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--papa mama-->
                        <div class="form-group col-md-3 oculto{{ $errors->has('papa_mama') ? ' has-error' : '' }}" id="div_papa_mama">
                            <label for="papa_mama" class="col-md-12 control-label">@lang('agenda.padre')/@lang('agenda.madre')</label>
                            <div class="col-md-12">
                                <select id="papa_mama" name="papa_mama" class="form-control input-sm">
                                    <option value="">@lang('agenda.seleccione') ...</option>
                                    <option value="Padre">@lang('agenda.padre')</option>
                                    <option value="Madre">@lang('agenda.madre')</option>
                                </select>
                                @if ($errors->has('papa_mama'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('papa_mama') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Primer nombre-->
                        <div class="form-group col-md-3 {{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-12 control-label">@lang('agenda.primerNombre')</label>
                            <div class="col-md-12">
                                <input id="nombre1" type="text" class="form-control input-sm" name="nombre1" value="@if($user != Array()){{$user[0]->nombre1}}@else{{ old('nombre1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--//segundo nombre-->
                        <div class="form-group col-md-3 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-12 control-label">@lang('agenda.segundoNombre')</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="@if($user != Array()){{$user[0]->nombre2}}@else{{ old('nombre2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-3{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-12 control-label">@lang('agenda.primerApellido')</label>
                            <div class="col-md-12">
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="@if($user != Array()){{$user[0]->apellido1}}@else{{ old('apellido1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Segundo apellido-->
                        <div class="form-group col-md-3 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-12 control-label">@lang('agenda.segundoApellido')</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="@if($user != Array()){{$user[0]->apellido2}}@else{{ old('apellido2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <!--telefono1-->
                        <div class="form-group col-md-3{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-12 control-label">@lang('agenda.telefonoDomicilio')</label>

                            <div class="col-md-12">
                                <input id="telefono1" type="text" class="form-control input-sm" name="telefono1" value="@if($user != Array()){{$user[0]->telefono1}}@else{{ old('telefono1') }}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-md-3{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-12 control-label">@lang('agenda.telefonoCelular')</label>

                            <div class="col-md-12">
                                <input id="telefono2" type="text" class="form-control input-sm" name="telefono2" value="@if($user != Array()){{$user[0]->telefono2}}@else{{ old('telefono2')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Pais-->
                        <div class="form-group col-md-3{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais2" class="col-md-12 control-label">@lang('agenda.pais')</label>
                            <div class="col-md-12">
                            @if($user != Array())
                                <input id="id_pais" type="hidden" class="form-control" name="id_pais" value="@if($user != Array()){{$user[0]->id_pais}}@else{{old('id_pais')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>
                                <input id="npais" type="text" class="form-control input-sm" name="npais" value=@if($user!=Array())@foreach($pais as $pais2)@if($user[0]->id_pais==$pais2->id)"{{$pais2->nombre}}"@endif
                                @endforeach
                                @else"{{old('id_pais')}}"
                                @endif" required autofocus readonly="readonly">
                            @else
                                <select onchange="pais();" id="id_pais" name="id_pais" class="form-control input-sm" >
                                    @foreach($pais as $pais2)
                                    <option id="op_pais" name="op_pais" @if(old('id_pais') == $pais2->id) selected @elseif ($user != Array()) @if ($user[0]->id_pais == $pais2->id) selected  @endif @endif value="{{$pais2->id}}">{{$pais2->nombre}}</option>
                                    @endforeach
                                </select>

                            @endif
                                @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-3">
                            <label for="fecha_nacimiento" class="col-md-12 control-label">@lang('agenda.fechaNacimiento')</label>
                            <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right input-sm"  name="fecha_nacimiento" id="fecha_nacimiento" value="@if($user != Array()) {{$user[0]->fecha_nacimiento}} @elseif(old('fecha_nacimiento')!='') {{ old('fecha_nacimiento') }} @endif" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="copiafecha(event);"  placeholder="1980/01/01">
                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-12">&nbsp;</div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 oculto" id="div_repre_2">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h4 class="box-title">@lang('agenda.datosRepresentante_o')</h4>
                    </div>
                    <div class="box-body no-padding">
                        <!--cedula-->
                        <div class="form-group col-md-3 {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-12 control-label">@lang('agenda.cedula')</label>
                            <div class="col-md-12">
                                <input id="id3" maxlength="10" type="text" class="form-control input-sm" name="id3" value="" autofocus onchange="usuario3();" >
                                @if ($errors->has('id3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id3') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--papa mama-->
                        <div class="form-group col-md-3 {{ $errors->has('papa_mama2') ? ' has-error' : '' }}">
                            <label for="papa_mama2" class="col-md-12 control-label">@lang('agenda.padre')/@lang('agenda.madre')</label>
                            <div class="col-md-12">
                                <select id="papa_mama2" name="papa_mama2" class="form-control input-sm">
                                    <option value="">@lang('agenda.seleccione') ...</option>
                                    <option value="Padre">@lang('agenda.padre')</option>
                                    <option value="Madre">@lang('agenda.madre')</option>
                                </select>
                                @if ($errors->has('papa_mama2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('papa_mama2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Primer nombre-->
                        <div class="form-group col-md-3 {{ $errors->has('nombre1_3') ? ' has-error' : '' }}">
                            <label for="nombre1_3" class="col-md-12 control-label">@lang('agenda.primerNombre')</label>
                            <div class="col-md-12">
                                <input id="nombre1_3" type="text" class="form-control input-sm" name="nombre1_3" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus onchange="busca_usuario_nombre3();">
                                @if ($errors->has('nombre1_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--//segundo nombre-->
                        <div class="form-group col-md-3 {{ $errors->has('nombre2_3') ? ' has-error' : '' }}">
                            <label for="nombre2_3" class="col-md-12 control-label">@lang('agenda.segundoNombre')</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="nombre2_3" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2_3" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2_3'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2_3') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-3{{ $errors->has('apellido1_3') ? ' has-error' : '' }}">
                            <label for="apellido1_3" class="col-md-12 control-label">@lang('agenda.primerApellido')</label>
                            <div class="col-md-12">
                                <input id="apellido1_3" type="text" class="form-control input-sm" name="apellido1_3" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus onchange="busca_usuario_nombre3();">
                                @if ($errors->has('apellido1_3'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1_3') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Segundo apellido-->
                        <div class="form-group col-md-3 {{ $errors->has('apellido2_3') ? ' has-error' : '' }}">
                            <label for="apellido2_3" class="col-md-12 control-label">@lang('agenda.segundoApellido')</label>
                            <div class="col-md-12">
                                <div class="input-group dropdown">
                                    <input id="apellido2_3" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2_3" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus  onchange="busca_usuario_nombre3();">
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2_3'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2_3') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <!--telefono1-->
                        <div class="form-group col-md-3{{ $errors->has('telefono1_3') ? ' has-error' : '' }}">
                            <label for="telefono1_3" class="col-md-12 control-label">@lang('agenda.telefonoDomicilio')</label>
                            <div class="col-md-12">
                                <input id="telefono1_3" type="text" class="form-control input-sm" name="telefono1_3" value="" autofocus>
                                @if ($errors->has('telefono1_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-md-3{{ $errors->has('telefono2_3') ? ' has-error' : '' }}">
                            <label for="telefono2_3" class="col-md-12 control-label">@lang('agenda.telefonoCelular')</label>
                            <div class="col-md-12">
                                <input id="telefono2_3" type="text" class="form-control input-sm" name="telefono2_3" value="" autofocus >

                                @if ($errors->has('telefono2_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Pais-->
                        <div class="form-group col-md-3{{ $errors->has('id_pais_3') ? ' has-error' : '' }}">
                            <label for="id_pais_3" class="col-md-12 control-label">@lang('agenda.pais')</label>
                            <div class="col-md-12">
                                <select onchange="pais();" id="id_pais_3" name="id_pais_3" class="form-control input-sm" >
                                    @foreach($pais as $pais2)
                                    <option id="op_pais" name="op_pais" @if(old('id_pais_3') == $pais2->id) selected @endif value="{{$pais2->id}}">{{$pais2->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_pais_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--fecha_nacimiento
                        <div class="form-group col-md-3{{ $errors->has('fecha_nacimiento_3') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento_3" class="col-md-12 control-label">Fecha Nacimiento</label>
                            <div class="col-md-12">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right input-sm"  name="fecha_nacimiento_3" id="fecha_nacimiento_3" value="@if(old('fecha_nacimiento_3')!='') {{ old('fecha_nacimiento_3') }} @endif" autofocus  onchange="copiafecha(event);"  placeholder="1980/01/01">
                                </div>
                                @if ($errors->has('fecha_nacimiento_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>-->
                        <div class="form-group col-md-12">&nbsp;</div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body no-padding">
                        <!--email-->
                        <div class="form-group col-md-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-12 control-label">@lang('agenda.mailAcceso')</label>
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control input-sm" name="email" value="@if($user != Array()){{$user[0]->email}}@elseif(old('email')!=''){{ old('email') }}@else{{'@'}}@endif" required @if($user != Array()){{'readonly'}}@endif>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--email-->
                        <div class="form-group col-md-6{{ $errors->has('email2') ? ' has-error' : '' }}">
                            <label for="email2" class="col-md-12 control-label">@lang('agenda.mailAlternativo')</label>
                            <div class="col-md-12">
                                <input id="email2" type="email2" class="form-control input-sm" name="email2" value="@if(old('email2')!=''){{ old('email') }}@else{{'@'}}@endif">

                                @if ($errors->has('email2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12"> &nbsp;
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    @lang('agenda.agregar')
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"> &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</section>



<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">


    $(function () {
        $('input[type="radio"].radio_origen').iCheck({

            radioClass: 'iradio_square-orange',
        });
        $('input[type="radio"].radio_origen_2').iCheck({

            radioClass: 'iradio_square-blue',
        });
        $('input[type="radio"].radio_origen_3').iCheck({

            radioClass: 'iradio_square-green',
        });
        $("#impreso_otros").removeAttr('required');
        $("#digital_otros").removeAttr('required');
        $("#referido").removeAttr('required');
        //$(".radio_origen_2").iCheck('disable');
        //$(".radio_origen_3").iCheck('disable');
        $('#medio_impreso').on('ifChecked', function(event){
            //habilito los campos
            $(".radio_origen_2").iCheck('enable');
            $("#impreso_otros").removeAttr('disabled');
            //campos requeridos
            $(".radio_origen_2").attr("required", "true");
            //deshabilito otros campos
            $(".radio_origen_3").iCheck('disable');
            $("#digital_otros").attr("disabled", "disabled");
            $("#referido").attr("disabled", "disabled");
            //quito requeridos otros campos
            $("#impreso_otros").removeAttr('required');
            $(".radio_origen_3").removeAttr('required');
            $("#digital_otros").removeAttr('required');
            $("#referido").removeAttr('required');
        });
        $('#medio_digital').on('ifChecked', function(event){
            //habilito los campos
            $(".radio_origen_3").iCheck('enable');
            $("#digital_otros").removeAttr('disabled');
            //campos requeridos
            $(".radio_origen_3").attr("required", "true");
            //deshabilito otros campos
            $(".radio_origen_2").iCheck('disable');
            $("#impreso_otros").attr("disabled", "disabled");
            //quito requeridos otros campos
            $(".radio_origen_2").removeAttr('required');
            $("#impreso_otros").removeAttr('required');
            $("#referido").removeAttr('required');
        });
        $('#ireferido').on('ifChecked', function(event){
            //habilito los campos
            $("#referido").removeAttr('disabled');
            //campos requeridos
            $("#referido").attr("required", "true");
            //deshabilito otros campos
            $(".radio_origen_2").iCheck('disable');
            $(".radio_origen_3").iCheck('disable');
            $("#impreso_otros").attr("disabled", "disabled");
            $("#digital_otros").attr("disabled", "disabled");
            //quito requeridos otros campos
            $(".radio_origen_2").removeAttr('required');
            $(".radio_origen_3").removeAttr('required');
            $("#impreso_otros").removeAttr('required');
            $("#digital_otros").removeAttr('required');
        });
        $('#origen_otros').on('ifChecked', function(event){
            $("#impreso_otros").attr("required", "true");
        });
        $('#origen_otros2').on('ifChecked', function(event){
            $("#digital_otros").attr("required", "true");
        });
        $('#origen_otros').on('ifUnchecked', function(event){
            $("#impreso_otros").removeAttr("required");
        });
        $('#origen_otros2').on('ifUnchecked', function(event){
            $("#digital_otros").removeAttr("required");
        });
        $('#fecha_nacimiento2').datetimepicker({
            format: 'YYYY/MM/DD'
        });
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

    });

    $("#fecha_nacimiento2").on("dp.change", function(e) {
         copiafecha(e);

    });

    $("#fecha_val").on("dp.change", function(e) {
        valida_fval(e);

    });


    $(document).ready(function () {

        edad();

        var valor2 = document.getElementById("id2").value;
        var nombre1 = document.getElementById("nombre12").value;
        var nombre2 = document.getElementById("nombre22").value;
        var apellido1 = document.getElementById("apellido12").value;
        var apellido2 = document.getElementById("apellido22").value;
        var telefono1 = document.getElementById("telefono12").value;
        var telefono2 = document.getElementById("telefono22").value;
        var fecha = document.getElementById("fecha_nacimiento2").value;
        var pais = document.getElementById("id_pais2").value;
        var relacion = document.getElementById("parentesco").value;

        if (relacion == "Principal") {
            $("#id").val(valor2);
            $("#nombre1").val(nombre1);
            $("#nombre2").val(nombre2);
            $("#apellido1").val(apellido1);
            $("#apellido2").val(apellido2);
            $("#telefono1").val(telefono1);
            $("#telefono2").val(telefono2);
            $("#fecha_nacimiento").val(fecha);
            $("#id_pais").val(pais);
            $("#id").attr("readonly","readonly");
            $("#nombre1").attr("readonly","readonly");
            $("#nombre2").attr("readonly","readonly");
            $("#apellido1").attr("readonly","readonly");
            $("#apellido2").attr("readonly","readonly");
            $("#telefono1").attr("readonly","readonly");
            $("#telefono2").attr("readonly","readonly");
            $("#fecha_nacimiento").attr("readonly","readonly");
            $("#id_pais").attr("readonly","readonly");
            $("#div_repre").addClass("oculto");
        }else{
            $("#id").removeAttr("readonly");
            $("#nombre1").removeAttr("readonly");
            $("#nombre2").removeAttr("readonly");
            $("#apellido1").removeAttr("readonly");
            $("#apellido2").removeAttr("readonly");
            $("#telefono1").removeAttr("readonly");
            $("#telefono2").removeAttr("readonly");
            $("#fecha_nacimiento").removeAttr("readonly");
            $("#id_pais").removeAttr("readonly");
            $("#div_repre").removeClass("oculto");
        }
        if(relacion == "Hijo(a)"){
            $("#div_papa_mama").removeClass("oculto");
            $("#div_repre_2").removeClass("oculto");
            $("#papa_mama").attr("required","required");
        }else{
            $("#div_papa_mama").addClass("oculto");
            $("#div_repre_2").addClass("oculto");
            $("#papa_mama").removeAttr("required");
        }

        $("#parentesco").click(function () {

            var valor2 = document.getElementById("id2").value;
            var nombre1 = document.getElementById("nombre12").value;
            var nombre2 = document.getElementById("nombre22").value;
            var apellido1 = document.getElementById("apellido12").value;
            var apellido2 = document.getElementById("apellido22").value;
            var telefono1 = document.getElementById("telefono12").value;
            var telefono2 = document.getElementById("telefono22").value;
            var fecha = document.getElementById("fecha_nacimiento2").value;
            var pais = document.getElementById("id_pais2").value;
            var relacion = document.getElementById("parentesco").value;
            if (relacion == "Principal") {
                $("#id").val(valor2);
                $("#nombre1").val(nombre1);
                $("#nombre2").val(nombre2);
                $("#apellido1").val(apellido1);
                $("#apellido2").val(apellido2);
                $("#telefono1").val(telefono1);
                $("#telefono2").val(telefono2);
                $("#fecha_nacimiento").val(fecha);
                $("#id_pais").val(pais);
                edad();
                $("#id").attr("readonly","readonly");
                $("#nombre1").attr("readonly","readonly");
                $("#nombre2").attr("readonly","readonly");
                $("#apellido1").attr("readonly","readonly");
                $("#apellido2").attr("readonly","readonly");
                $("#telefono1").attr("readonly","readonly");
                $("#telefono2").attr("readonly","readonly");
                $("#fecha_nacimiento").attr("readonly","readonly");
                $("#id_pais").attr("readonly","readonly");
                $("#div_repre").addClass("oculto");
            }else{

                $("#id").val('');
                $("#nombre1").val('');
                $("#nombre2").val('');
                $("#apellido1").val('');
                $("#apellido2").val('');
                $("#fecha_nacimiento").val('');
                $("#id").removeAttr("readonly");
                $("#nombre1").removeAttr("readonly");
                $("#nombre2").removeAttr("readonly");
                $("#apellido1").removeAttr("readonly");
                $("#apellido2").removeAttr("readonly");
                $("#telefono1").removeAttr("readonly");
                $("#telefono2").removeAttr("readonly");
                $("#fecha_nacimiento").removeAttr("readonly");
                $("#id_pais").removeAttr("readonly");
                $("#div_repre").removeClass("oculto");
            }
            if(relacion == "Hijo(a)"){
                $("#div_papa_mama").removeClass("oculto");
                $("#div_repre_2").removeClass("oculto");
                $("#papa_mama").attr("required","required");
            }else{
                $("#div_papa_mama").addClass("oculto");
                $("#div_repre_2").addClass("oculto");
                $("#papa_mama").removeAttr("required");
            }
        });

        <?php if ($user == array()) {?>

        $("#id2").keyup(function () {
            var relacion = document.getElementById("parentesco").value;
            var value = $(this).val();
            validarCedula(value);

            if (relacion == "Principal") {
               $("#id").val(value);
            }
        });

        $("#nombre12").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#nombre1").val(value);
          }
        });

        $("#nombre22").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#nombre2").val(value);}
        });

        $("#apellido12").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#apellido1").val(value);}
        });

        $("#apellido22").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#apellido2").val(value);}
        });

        $("#telefono12").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#telefono1").val(value); }
        });

        $("#telefono22").keyup(function () {
            var value = $(this).val();
            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#telefono2").val(value);}
        });



        $(function() {
        $('.usuario1 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
          .val('(' + $(this).attr('data-value') + ')');

            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#nombre22").val('(' + $(this).attr('data-value') + ')');

            }

            busca_usuario_nombre();
        });})

        $(function() {
        $('.usuario2 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
          .val('(' + $(this).attr('data-value') + ')');

            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#apellido22").val('(' + $(this).attr('data-value') + ')');}

            busca_usuario_nombre();
        });})


        <?php }?>

        $(function() {
        $('.usuario3 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode').val('(' + $(this).attr('data-value') + ')');
            busca_usuario_nombre1();

        });})








    });

    function copiafecha(e)
    {
        //alert("Ingreso");
        var relacion = document.getElementById("parentesco").value;
        if (relacion == "Principal") {
            var valor = document.getElementById("fecha_nacimiento2").value;
            $("#fecha_nacimiento").val(valor)
            edad();
        }

     }

    function valida_fval(e)
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



    function usuario()
    {
        vcedula = document.getElementById("id").value;
        vcedula =  vcedula.trim();
        console.log(vcedula);
        /*if (vcedula != ""){
            location.href =" {{route('agenda.paciente2', ['id' => $id])}}/"+vcedula+"/{{$fecha}}/{{$sala}}";
        }*/
        $.ajax({
        type: 'get',
        url:'{{ url("nuevo_agenda/paciente")}}/'+vcedula,
        datatype: 'json',
        success: function(data){
            console.log(data);
                if(data!='no'){
                    $('#usuario_existe').text('**Usuario ya registrado en el sistema');
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#telefono1').val(data.telefono1);
                    $('#telefono2').val(data.telefono2);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    $('#email').val(data.email);
                    $('#id_pais option[value="'+data.id_pais+'"]').attr("selected", true);
                }
            },
        })
    }

    var busca_usuario_nombre = function ()
    {

        var jnombre1 = document.getElementById('nombre1').value;
        var jnombre2 = document.getElementById('nombre2').value;
        var japellido1 = document.getElementById('apellido1').value;
        var japellido2 = document.getElementById('apellido2').value;

        if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
        {

            $('#alertms').empty().html('');
            $(".m1").addClass("oculto");
            $.ajax({
            type: 'get',
            url:'{{ route('paciente.pacientexnombre')}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

            datatype: 'json',
            data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
            success: function(data){

                    if(data!='0'){
                        $('#alertms').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                        $(".m1").removeClass("oculto");
                    }

                },

            })

        }

    }

    var busca_usuario_nombre1 = function ()
    {

        var jnombre1 = document.getElementById('nombre12').value;
        var jnombre2 = document.getElementById('nombre22').value;
        var japellido1 = document.getElementById('apellido12').value;
        var japellido2 = document.getElementById('apellido22').value;

        if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
        {
            console.log(jnombre1,jnombre2,japellido1,japellido2);
            $('#alertms2').empty().html('');
            $(".m2").addClass("oculto");
            $.ajax({
            type: 'get',
            url:'{{ route('paciente.pacientexnombre')}}',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

            datatype: 'json',
            data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
            success: function(data){
                console.log(data);
                    if(data!='0'){
                        $('#alertms2').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                        $(".m2").removeClass("oculto");
                    }

                },

            })

        }

    }

    function cambia_pais(){

        var relacion = document.getElementById("parentesco").value;
        if (relacion == "Principal") {
            var pais = document.getElementById("id_pais2").value;
            $("#id_pais").val(pais);
        }

    }

</script>
<script type="text/javascript">

    $(document).ready(function() {
      //alert("Ingreso");

    $("#id_tipo_seguro").click(function () {

        var id_seguro = document.getElementById("id_tipo_seguro").value;

        if (id_seguro == '3') {//issfa
          
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
        }else if(id_seguro == '5'){//Msp
            $("#fecha_validacion1").removeClass("oculto");
            //$('#cod_validacion1').removeClass("oculto");
            $("#cod_validacion2").removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion2').prop("required", true);
        }else if(id_seguro == '6'){//isspol
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
        }else if(id_seguro == '2'){//iess
            $("#fecha_validacion1").removeClass("oculto");
            $('#cod_validacion1').removeClass("oculto");
            $('#fecha_validacion1').prop("required", true);
            $('#cod_validacion1').prop("required", true);
            $("#cod_validacion2").addClass("oculto");
        }else{
            $("#fecha_validacion1").addClass("oculto");
            $("#cod_validacion1").addClass("oculto");
            $('#fecha_validacion1').removeAttr("required");
            $('#cod_validacion1').removeAttr("required");
            $('#fecha_val').val(" ");
            $('#cod_val').val(" ");
          
        }
    });
    


    });
</script>

@endsection

