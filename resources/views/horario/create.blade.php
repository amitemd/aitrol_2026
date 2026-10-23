@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nuevo Usuario</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('user-management.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--primer nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre1" type="text" class="form-control" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
               
                    <!--//segundo nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                        <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre2" type="text" class="form-control" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
           
                        <!--primer apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido1" type="text" class="form-control" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido2" type="text" class="form-control" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                      
                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Cédula</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ old('id') }}" required autofocus onkeyup="validarCedula(this.value);">
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                      
                       
                        <!--Pais-->
                        <div class="form-group col-xs-6 {{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais" class="col-md-4 control-label">Pais</label>
                            <div class="col-md-7">
                                <!--<input id="id_pais" type="text" class="form-control" name="id_pais" value="{{ old('id_pais') }}" required autofocus>-->
                                <select id="id_pais" name="id_pais" class="form-control">
                                    @for($i=0;$i<=count($pais)-1;$i++)
                                    <option value="{{$pais[$i]->id}}">{{$pais[$i]->nombre}}</option>
                                    @endfor
                                </select>  
                                @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-md-4 control-label">Ciudad</label>

                            <div class="col-md-7">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>

                                @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Direccion</label>

                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>

                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ old('telefono2') }}" required autofocus>

                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--ocupacion-->
                        <div class="form-group col-xs-6{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                            <label for="ocupacion" class="col-md-4 control-label">Ocupación</label>

                            <div class="col-md-7">
                                <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="{{ old('ocupacion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('ocupacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ocupacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--fecha_nacimiento-->
                        <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento</label>

                            <div class="col-md-7">
                                <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required autofocus>

                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}">
                            <label for="id_tipo_usuario" class="col-md-4 control-label">Tipo usuario</label>

                            <div class="col-md-7">
                                <select id="id_tipo_usuario" name="id_tipo_usuario" class="form-control">
                                    @for($i=0;$i<=count($tipousuarios)-1;$i++)
                                        @if ($tipousuarios[$i]->estado != 0)
                                             @if ($tipousuarios[$i]->id != 2) 
                                                <option value="{{$tipousuarios[$i]->id}}">{{$tipousuarios[$i]->nombre}}</option>
                                             @endif   
                                        @endif    
                                    @endfor
                                </select>  
                                @if ($errors->has('id_tipo_usuario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipo_usuario') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Color-->
                        <div id="cdoctores" class="form-group col-xs-6{{ $errors->has('color') ? ' has-error' : '' }} oculto" >
                            <label for="color" class="col-md-4 control-label">Color en la Agenda</label>
                            <div class="col-md-7 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ old('color') }}" >
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></sp
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        </div>
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Password-->
                        <div class="form-group col-xs-6{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-7">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Confirmar Password-->
                        <div class="form-group col-xs-6">
                            <label for="password-confirm" class="col-md-4 control-label">Confirma Password</label>

                            <div class="col-md-7">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <!--especialidades-->
                        <div id="especialidades" class="col-md-12 oculto">
                        <div class="form-group col-xs-8">
                            <label  class="col-md-4 control-label">Especialidades:</label>
                        </div>
                        @foreach($especialidad as $value)
                        <div class="form-group col-xs-8">
                            <label for="password-confirm" class="col-md-4 control-label">{{$value->nombre}}</label>
                            <div class="col-md-7">
                                <input name="lista[]" type="checkbox" value="{{$value->id}}">
                            </div>
                        </div>
                        @endforeach
                         </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function() {
        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            /*if(fecha_cita <= fecha_actual)
            {
                $("#enviar").attr("disabled","disabled");
            }*/ 
            @foreach ($tipousuarios as $value)
                @if($value->nombre != 'DOCTORES')
                    if(estado=={{$value->id}}){
                        $("#especialidades").addClass("oculto");
                        $("#cdoctores").addClass("oculto");
                    }
                @endif
                @if($value->nombre == 'DOCTORES')
                    if(estado=={{$value->id}}){
                        $("#especialidades").removeClass("oculto");
                        $("#cdoctores").removeClass("oculto");
                    }
                @endif    
            @endforeach
             
        });
           

    });

</script>
@endsection
