@extends('contable.empleados.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{route('empleados.index')}}">Vendedor - Recaudador</a></li>
            <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
        </ol>
    </nav>
    <form class="form-vertical" role="form" method="POST" action="{{route('empleados_update', ['id' => $user->id])}}">
        <div class="box">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-header color_cab">
                <div class="col-md-9">
                <h5><b>DETALLE VENDEDOR - RECAUDADOR</b></h5>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <button onclick="goBack()" class="btn btn-danger btn-gray" >
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--primer nombre-->
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                    <label for="nombre1" class="control-label">Primer Nombre</label>
                    <input id="nombre1" type="text" class="form-control" name="nombre1" value="{{ $user->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                    @if ($errors->has('nombre1'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre1') }}</strong>
                    </span>
                    @endif
                </div>
                <!--segundo nombre-->
                <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                    <label for="nombre2" class="control-label">Segundo Nombre</label>
                    <input id="nombre2" type="text" class="form-control" name="nombre2" value="{{ $user->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off">
                    @if ($errors->has('nombre2'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre2') }}</strong>
                    </span>
                    @endif
                </div>
                <!--primer apellido-->
                <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                    <label for="apellido1" class="control-label">Primer Apellido</label>
                    <input id="apellido1" type="text" class="form-control" name="apellido1" value="{{ $user->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                    @if ($errors->has('apellido1'))
                    <span class="help-block">
                        <strong>{{ $errors->first('apellido1') }}</strong>
                    </span>
                    @endif
                </div>
                <!--segundo apellido-->
                <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                    <label for="apellido2" class="control-label">Segundo Apellido</label>
                    <input id="apellido2" type="text" class="form-control" name="apellido2" value="{{ $user->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                    @if ($errors->has('apellido2'))
                    <span class="help-block">
                        <strong>{{ $errors->first('apellido2') }}</strong>
                    </span>
                    @endif
                </div>
                <!--cedula-->
                <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                    <label for="id" class="control-label">Cedula</label>
                    <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ $user->id }}" required autofocus onkeyup="validarCedula(this.value);" @if ($rolusuario !=1) readonly="readonly" ; @endif autocomplete="off">
                    @if ($errors->has('id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('id') }}</strong>
                    </span>
                    @endif
                </div>
                <!--pais-->
                <div class="form-group col-xs-6{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                    <label for="id_pais" class="control-label">Pais</label>
                    <!--<input id="id_pais" type="text" class="form-control" name="id_pais" value="{{ $user->id_pais }}" required>-->
                    <select id="id_pais" name="id_pais" class="form-control">
                        @foreach ($paises as $pais)
                        <option {{$user->id_pais == $pais->id ? 'selected' : ''}} value="{{$pais->id}}">{{$pais->nombre}}</option>
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
                    <label for="ciudad" class="control-label">Ciudad</label>
                    <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $user->ciudad }}" autocomplete="off" required>
                    @if ($errors->has('ciudad'))
                    <span class="help-block">
                        <strong>{{ $errors->first('ciudad') }}</strong>
                    </span>
                    @endif
                </div>
                <!--direccion-->
                <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                    <label for="direccion" class="control-label">Direccion</label>
                    <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $user->direccion }}" autocomplete="off" required>
                    @if ($errors->has('direccion'))
                    <span class="help-block">
                        <strong>{{ $errors->first('direccion') }}</strong>
                    </span>
                    @endif
                </div>
                <!--telefono1-->
                <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                    <label for="telefono1" class="control-label">Telefono Domicilio</label>
                    <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $user->telefono1 }}"  autocomplete="off" required>
                    @if ($errors->has('telefono1'))
                    <span class="help-block">
                        <strong>{{ $errors->first('telefono1') }}</strong>
                    </span>
                    @endif
                </div>
                <!--telefono2-->
                <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                    <label for="telefono2" class="control-label">Telefono Celular</label>
                    <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $user->telefono2 }}" autocomplete="off" required>
                    @if ($errors->has('telefono2'))
                    <span class="help-block">
                        <strong>{{ $errors->first('telefono2') }}</strong>
                    </span>
                    @endif
                </div>
                <!--ocupacion-->
                <div class="form-group col-xs-6{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                    <label for="ocupacion" class="control-label">Ocupacion</label>
                    <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="{{ $user->ocupacion }}" autocomplete="off" required>
                    @if ($errors->has('ocupacion'))
                    <span class="help-block">
                        <strong>{{ $errors->first('ocupacion') }}</strong>
                    </span>
                    @endif
                </div>
                <!--fecha_nacimiento-->
                <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                    <label for="fecha_nacimiento" class="control-label">Fecha Nacimiento</label>
                    <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
                    @if ($errors->has('fecha_nacimiento'))
                    <span class="help-block">
                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                    </span>
                    @endif
                </div>
                <!--id_tipo_usuario-->
                <div class="form-group col-xs-6{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}" @if ($rolusuario !=1) style="display:none" @endif>
                    <label for="id_tipo_usuario" class="control-label">Tipo Usuario</label>
                    <select id="id_tipo_usuario" name="id_tipo_usuario" class="form-control">
                        @foreach ($tipousuarios as $tipousuario)
                        @if ($tipousuario->estado != 0)
                        <option {{$user->id_tipo_usuario == $tipousuario->id ? 'selected' : ''}} value="{{$tipousuario->id}}">{{$tipousuario->nombre}}</option>
                        @endif
                        @endforeach
                    </select>
                    @if ($errors->has('id_tipo_usuario'))
                    <span class="help-block">
                        <strong>{{ $errors->first('id_tipo_usuario') }}</strong>
                    </span>
                    @endif
                </div>
                <!--email-->
                <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="control-label">E-mail</label>
                    <input id="email" type="text" class="form-control" name="email" value="{{ $user->email }}" autocomplete="off" required>
                    @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group col-xs-6{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="control-label">Nuevo Password</label>
                    <input id="password" type="password" class="form-control" name="password">
                    @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group col-xs-6">
                    <label for="password-confirm" class="control-label">Confirmar Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                </div>
                <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}" @if ($rolusuario !=1) style="display:none" @endif>
                    <label for="estado" class="col-xs-8 control-label">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option {{$user->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                        <option {{$user->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>

                    </select>
                    @if ($errors->has('estado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('estado') }}</strong>
                    </span>
                    @endif
                </div>
                <!--Button Actualizar-->
                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary btn-gray">
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
       
    

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });

        });

        function goBack() {
           location.href="{{route('empleados.index')}}";
        }
    </script>

</section>
@endsection