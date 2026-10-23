@extends('contable.nomina.base')
@section('action-content')

<style type="text/css">

    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    .tab button:hover {
       background-color: #ddd;
    }

    .tab button.active {
       background-color: #ccc;
    }

    .tabcontent {
       display: none;
       padding: 6px 12px;
       border: 1px solid #ccc;
       border-top: none;
    }

    .tabcontent2 {
       display: none;
       padding: 6px 12px;
       border: 1px solid #ccc;
       border-top: none;
    }

    .separator{
       width:100%;
       height:30px;
       clear: both;
    }

    .alerta_guardado{
      position: absolute;
      z-index: 9999;
      bottom: 100px;
      right: 20px;
    }
    input[type="checkbox"] {
      zoom: 1.2;
    }

</style>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>

<div class="modal fade" id="foto_ficha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div id="alerta_guardado" class="alert alert-success alerta_guardado alert-dismissable" role="alert" style="display:none;">
         <button type="button" class="close" data-dismiss="alert">&times;</button>
           Guardado Correctamente
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item"><a href="{{route('nomina.index')}}">Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    @php
        $explotar1 = explode( '.', $registro->archivo_curriculum);
        $extension1 = end($explotar1);

        $explotar2 = explode( '.', $registro->archivo_ficha_tecnica);
        $extension2 = end($explotar2);

        $explotar3 = explode( '.', $registro->archivo_ficha_ocupacional);
        $extension3 = end($explotar3);

        $explotar4 = explode( '.', $registro->foto);
        $extension4 = end($explotar4);
    @endphp
    <div class="box">
        <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>DETALLE EMPLEADO</b></h5>
            </div>
            <!--<div class="col-md-1 text-right">
                <button id="actualiza_rol_pago" onclick="actualiza_empleado()" class="btn btn-primary btn-gray">
                   Guardar
                </button>
            </div>-->
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="tab">
                <button class="tablinks" onclick="openCity(event, 'datos_personal')"><b>Datos Personales</b></button>
                <button class="tablinks" onclick="openCity(event, 'datos_empleado')"><b>Datos Del Empleado</b></button>
                <h5 style="color: red; padding-top: 10px; padding-left: 500px; padding-right: 50px; padding-bottom: 10px;"><b> Empleado: {{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}} </b></h5>
                <!-- <label for="" style="padding-top: 15px; padding-left: 500px; padding-right: 50px; padding-bottom: 15px; color:red;" >Empleado: {{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}}</label> -->
            </div>
            <form class="form-vertical"  id="actua_form" role="form" method="POST"  enctype="multipart/form-data" action="{{route('nomina.actualizar')}}">
                {{ csrf_field() }}
                <input  name="chek_dis" id="chek_dis" type="text" class="hidden" value="@if(!is_null($registro->check_discapacidad)){{$registro->check_discapacidad}}@endif">
                <div class="separator"></div>
                <div class="box-body dobra col-xs-24 tabcontent" id="datos_personal">
                    <!--Empresa-->
                    <!--4<div class="form-group col-xs-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-4 texto">Empresa</label>
                        <div class="col-md-7">
                          <select class="form-control" id="id_empresa" name="id_empresa" onchange="buscarIdentificacion()">
                            @foreach($empresas as $value)
                              <option value="{{$value->id}}" @if($empresa->id ==  $value->id) selected="selected" @endif >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>-->
                    <div class="clearfix"></div>
                    <div class="form-group col-xs-6{{ $errors->has('identificacion') ? ' has-error' : '' }}">
                        <label for="identificacion" class="col-md-4 texto">Identificaci&oacute;n</label>
                        <div class="col-md-7">
                            <input id="id" type="hidden" class="form-control" name="id" value="@if(!is_null($registro)){{$registro->id}}@endif">
                            <input id="identificacion" maxlength="10" type="text" class="form-control"  name="identificacion" value="@if(!is_null($usuario)){{$usuario->id}}@endif" onkeypress="return isNumberKey(event)" autocomplete="off" required autofocus>
                            @if ($errors->has('identificacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('identificacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                
                    @if(($extension4 == 'jpg') || ($extension4 == 'jpeg') || ($extension4 == 'png'))
                        <div class="form-group col-xs-6{{ $errors->has('foto') ? ' has-error' : '' }}">
                            <label for="foto" class="col-md-4 texto">{{trans('nomina.Foto')}}</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->foto))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_foto.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('../storage/app/archivos_nomina')}}/{{$registro->foto}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->foto}}</span><br>
                                <a type="button" href="{{asset('descarga_archivo_foto')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="foto" name="foto" type="file" class="archivo form-control">
                            </div>
                        </div>
                    @elseif(($extension4 == 'pdf'))
                        <div class="form-group col-xs-6{{ $errors->has('foto') ? ' has-error' : '' }}">
                            <label for="foto" class="col-md-4 texto">{{trans('nomina.Foto')}}</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->foto))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_foto.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('imagenes/pdf.png')}}" width="60%" style="height: 80px;">
                                </a>
                                <span>{{$registro->foto}}</span><br>
                                <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->
                                <a type="button" href="{{asset('descarga_archivo_foto')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="foto" name="foto" type="file" class="archivo form-control">
                                <!--<a type="button" onclick="#" class="btn btn-danger btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>-->
                            </div>
                        </div>
                    @else
                        @php
                            $variable = explode('/' , asset('/archivos_nomina/'));
                            $d1 = $variable[3];
                        @endphp
                        <div class="form-group col-xs-6{{ $errors->has('ficha_tecnica') ? ' has-error' : '' }}">
                            <label for="ficha_tecnica" class="col-md-4 texto">{{trans('nomina.Foto')}}</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->foto))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_foto.modal', ['id' => $registro->id]) }}">
                                 <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->foto}}</span><br>
                                <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->
                                <a type="button" href="{{asset('descarga_archivo_foto')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="foto" name="foto" type="file" class="archivo form-control">
                                <!--<a type="button" onclick="eliminar_archivo_curr('{{$registro->id}}');" class="btn btn-danger btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>-->
                            </div>
                        </div>

                    @endif
                    <!--Foto-->
                    <!--primer nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre1" class="col-md-4 texto">Primer Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre1" type="text" class="form-control" name="nombre1" value="@if(!is_null($usuario)){{$usuario->nombre1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--//segundo nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                        <label for="nombre2" class="col-md-4 texto">Segundo Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre2" type="text" class="form-control" name="nombre2" value="@if(!is_null($usuario)){{$usuario->nombre2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                                @if ($errors->has('nombre2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2') }}</strong>
                                </span>
                                @endif
                        </div>
                    </div>
                    <!--primer apellido-->
                    <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                        <label for="apellido1" class="col-md-4 texto">Primer Apellido</label>
                        <div class="col-md-7">
                            <input id="apellido1" type="text" class="form-control" name="apellido1" value="@if(!is_null($usuario)){{$usuario->apellido1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Segundo apellido-->
                    <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                        <label for="apellido2" class="col-md-4 texto">Segundo Apellido</label>
                        <div class="col-md-7">
                            <input id="apellido2" type="text" class="form-control" name="apellido2" value="@if(!is_null($usuario)){{$usuario->apellido2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('apellido2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellido2') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Pais-->
                    <div class="form-group divpass col-xs-6 {{ $errors->has('id_pais') ? ' has-error' : '' }}">
                        <label for="id_pais" class="col-md-4 texto">Pais</label>
                        <div class="col-md-7">
                            <select id="id_pais" name="id_pais" class="form-control">
                                @foreach($pais as $value)
                                   <option {{ $usuario->id_pais == $value->id ? 'selected' : ''}} value="$value->id">{{$value->nombre}}</option>
                                @endforeach
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
                        <label for="ciudad" class="col-md-4 texto">Ciudad</label>
                        <div class="col-md-7">
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="@if(!is_null($usuario)){{$usuario->ciudad}}@endif"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Direccion-->
                    <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                        <label for="direccion" class="col-md-4 texto">Direccion</label>
                        <div class="col-md-7">
                            <input id="direccion" type="text" class="form-control" name="direccion" value="@if(!is_null($usuario)){{$usuario->direccion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" required autofocus>
                            @if ($errors->has('direccion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('direccion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--telefono1-->
                    <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                        <label for="telefono1" class="col-md-4 texto">Telefono Domicilio</label>
                        <div class="col-md-7">
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="@if(!is_null($usuario)){{$usuario->telefono1}}@endif" onkeypress="return isNumberKey(event)" autocomplete="off" required autofocus>
                            @if ($errors->has('telefono1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono1') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--telefono2-->
                    <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                        <label for="telefono2" class="col-md-4 texto">Telefono Celular</label>
                        <div class="col-md-7">
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="@if(!is_null($usuario)){{$usuario->telefono2}}@endif" onkeypress="return isNumberKey(event)" autocomplete="off" required autofocus>
                            @if ($errors->has('telefono2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--ocupacion-->
                    <div class="form-group col-xs-6{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                        <label for="ocupacion" class="col-md-4 texto">Ocupación</label>
                        <div class="col-md-7">
                            <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="@if(!is_null($registro)){{$registro->ocupacion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('ocupacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ocupacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--fecha_nacimiento-->
                    <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                        <label for="fecha_nacimiento" class="col-md-4 texto">Fecha Nacimiento</label>
                        <div class="col-md-7">
                            <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="@if(!is_null($usuario)){{$usuario->fecha_nacimiento}}@endif" required autofocus>
                            @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--GENERO-->
                    <div class="form-group col-xs-6">
                        <label  class="col-md-4 texto">Genero</label>
                        <div class="col-md-7">
                            <select id="genero" name="genero" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option {{ $registro->sexo == 'M' ? 'selected' : ''}} value="M">MASCULINO</option>
                                <option {{ $registro->sexo == 'F' ? 'selected' : ''}} value="F">FEMENINO</option>
                            </select>
                            @if ($errors->has('genero'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('genero') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--ETNIA-->
                    <div class="form-group col-xs-6">
                        <label for="etnia" class="col-md-4 texto">Etnia</label>
                        <div class="col-md-7">
                            <input id="etnia" type="text" class="form-control" name="etnia" value="@if(!is_null($registro)){{$registro->etnia}}@endif" autocomplete="off" required autofocus>
                        </div>
                    </div>
                    <!--DISCAPACIDAD-->
                    <div class="form-group col-xs-6">
                        <label for="discapacidad" class="col-md-4 texto">Discapacidad</label>
                        <div class="col-md-7">
                            <input style="width:17px;height:17px;" type="checkbox" id="discapacidad" class="checkVal" name="discapacidad" value="1"
                            @if(old('discapacidad')=="1")
                              checked
                            @elseif($registro->check_discapacidad == "1")
                              checked
                            @endif>
                        </div>
                    </div>


                    <!--% Discapacidad-->
                    <div class="form-group col-xs-6"  id="datos_porcentaje">
                        <label for="porcent_discapacidad" class="col-md-4 texto">Porcentaje</label>
                        <div class="col-md-7">
                            <input id="porcent_discapacidad" type="text" class="form-control" name="porcent_discapacidad" value="@if(!is_null($registro)){{$registro->porcentaje_discapacidad}}@endif" onkeypress="return isNumberKey(event)">
                        </div>
                    </div>
                    <!--NUMERO DE CARGA-->
                    <div class="form-group col-xs-6">
                        <label for="numero_carga" class="col-md-4 texto">Numero de Cargas</label>
                        <div class="col-md-7">
                            <input id="numero_carga" type="number" maxlength="4" class="form-control" name="numero_carga" value="@if(!is_null($registro)){{$registro->numero_cargas}}@endif" onkeypress="return isNumberKey(event)">
                        </div>
                    </div>
                    <!--NIVEL ACADEMICO-->
                    <div class="form-group col-xs-6">
                        <label for="nivel_academico" class="col-md-4 texto">Nivel Acadèmico</label>
                        <div class="col-md-7">
                            <select id="nivel_academico" name="nivel_academico" class="form-control" required>
                                @foreach($nivel_academico as $value)
                                <option value="{{$value->id}}" {{ $registro->nivel_academico == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--ESTADO CIVIL-->
                    <div class="form-group col-xs-6">
                        <label for="estado_civil" class="col-md-4 texto">Estado Civil</label>
                        <div class="col-md-7">
                          <select id="estado_civil" name="estado_civil" class="form-control" required>
                                @foreach($estado_civil as $value)
                                <option value="{{$value->id}}" {{ $registro->estado_civil == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                                @endforeach
                          </select>
                        </div>
                    </div>
                    <!--id_tipo_usuario-->
                    <div class="form-group divpass col-xs-6{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}">
                        <label for="id_tipo_usuario" class="col-md-4 texto">Tipo usuario</label>
                        <div class="col-md-7">
                            <select disabled id="id_tipo_usuario" name="id_tipo_usuario" value="{{$nominaTipo->id}}" class="form-control">
                                @for($i=0;$i<=count($tipousuarios)-1;$i++)
                                    @if ($tipousuarios[$i]->estado != 0)
                                         @if ($tipousuarios[$i]->id != 2)
                                            <option {{$nominaTipo->id == $tipousuarios[$i]->id ? 'selected' : ''}}  value="{{$tipousuarios[$i]->id}}">{{$tipousuarios[$i]->nombre}}</option>
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
                    <!--email-->
                    <div class="form-group  col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 texto">E-Mail</label>
                        <div class="col-md-7">
                            <input id="email" type="email" class="form-control" name="email" value="@if(!is_null($usuario)){{$usuario->email}}@endif" autocomplete="off" readonly>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--email opcional-->
                    <div class="form-group  col-xs-6{{ $errors->has('mail_opcional') ? ' has-error' : '' }}">
                        <label for="mail_opcional" class="col-md-4 texto">E-Mail alternativo</label>
                        <div class="col-md-7">
                            <input id="mail_opcional" type="mail_opcional" class="form-control" name="mail_opcional" value="@if(!is_null($registro)){{$registro->mail_opcional}}@endif" autocomplete="off" >
                            @if ($errors->has('mail_opcional'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mail_opcional') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Password-->
                    <!--<div class="form-group divpass col-xs-6{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 texto">Password</label>
                        <div class="col-md-7">
                            <input id="password" type="password" class="form-control" name="password">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>-->
                    <!--Confirmar Password-->
                    <!--<div class="form-group divpass col-xs-6">
                        <label for="password-confirm" class="col-md-4 texto">Confirma Password</label>
                        <div class="col-md-7">
                            <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                        </div>
                    </div>-->
                </div>
                <div class="box-body dobra col-xs-24 tabcontent" id="datos_empleado">
                    <div class="separator"></div>
                    <!--AREA-->
                    <div class="form-group col-xs-6">
                        <label for="area" class="col-md-4 texto">Area</label>
                        <div class="col-md-7">
                          <select id="area" name="area" class="form-control" required>
                            @foreach($area as $value)
                              <option value="{{$value->id}}" {{ $registro->area == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!--Cargo-->
                    <div class="form-group col-xs-6{{ $errors->has('cargo') ? ' has-error' : '' }}">
                        <label for="cargo" class="col-md-4 texto">Cargo que Ocupa</label>
                        <div class="col-md-7">
                            <input id="cargo" type="text" class="form-control" name="cargo" value="@if(!is_null($registro)){{$registro->cargo}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('cargo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cargo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Fecha_Ingreso -->
                    <div class="form-group col-xs-6{{ $errors->has('fecha_actividad') ? ' has-error' : '' }}">
                        <label for="fecha_actividad" class="col-md-4 texto">Fecha de Ingreso</label>
                        <div class="col-md-7">
                            <input id="fecha_actividad" type="date" class="form-control" name="fecha_actividad" value="@if(!is_null($registro)){{$registro->fecha_ingreso}}@endif" required autofocus>
                            @if ($errors->has('fecha_actividad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_actividad') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Fondo de Reserva-->
                    <div class="form-group col-xs-6">
                        <label for="fondo_reserva" class="col-md-4 texto">Fondo Reserva</label>
                        <div class="col-md-7">
                          <select id="fondo_reserva" name="fondo_reserva" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($pago_beneficio as $value)
                              <option value="{{$value->id}}" {{ $registro->pago_fondo_reserva == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!--Beneficios Sociales-->
                    <!--<div class="form-group col-xs-6">
                        <label for="beneficio_social" class="col-md-4 texto">Beneficios Sociales</label>
                        <div class="col-md-7">
                          <select id="beneficio_social" name="beneficio_social" class="form-control" required>
                            @foreach($pago_beneficio as $value)
                              <option value="{{$value->id}}" {{ $registro->pago_beneficios_sociales == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>-->
                    <!--Decimo Tercero-->
                    <div class="form-group col-xs-6">
                        <label for="decimo_tercero" class="col-md-4 texto">Decimo Tercero</label>
                        <div class="col-md-7">
                          <select id="decimo_tercero" name="decimo_tercero" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($pago_beneficio as $value)
                              <option value="{{$value->id}}" {{ $registro->decimo_tercero == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!--Decimo Cuarto-->
                    <div class="form-group col-xs-6">
                        <label for="decimo_cuarto" class="col-md-4 texto">Decimo Cuarto</label>
                        <div class="col-md-7">
                          <select id="decimo_cuarto" name="decimo_cuarto" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($pago_beneficio as $value)
                              <option value="{{$value->id}}" {{ $registro->decimo_cuarto == $value->id ? 'selected' : ''}}>{{$value->descripcion}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!--Seguro Privado-->
                    <div class="form-group col-xs-6{{ $errors->has('seguro_privado') ? ' has-error' : '' }}">
                        <label for="seguro_privado" class="col-md-4 texto">Seguro Privado</label>
                        <div class="col-md-7">
                            <input id="seguro_privado" type="text" class="form-control" name="seguro_privado" value="@if(!is_null($registro)){{$registro->seguro_privado}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('seguro_privado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('seguro_privado') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Horario Labores-->
                    <div class="form-group col-xs-6">
                        <label for="horario" class="col-md-4 texto">Horario</label>
                        <div class="col-md-7">
                            <select id="horario" name="horario" class="form-control" required>
                                @foreach($horario as $value)
                                <option value="{{$value->id}}" {{ $registro->horario == $value->id ? 'selected' : ''}}>{{$value->horario}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--Bonificacion-->
                    <div class="form-group col-xs-6{{ $errors->has('bono') ? ' has-error' : '' }}">
                        <label for="bono" class="col-md-4 texto">Bono</label>
                        <div class="col-md-7">
                            <input id="bono" type="text" class="form-control" name="bono" value="@if(!is_null($registro)){{$registro->bono}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('bono'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('bono') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--bono imputable-->
                    <div class="form-group col-xs-6{{ $errors->has('bono_imputable') ? ' has-error' : '' }}">
                        <label for="bono_imputable" class="col-md-4 texto">Bono Imputable</label>
                        <div class="col-md-7">
                            <input id="bono_imputable" type="text" class="form-control" name="bono_imputable" value="@if(!is_null($registro)){{$registro->bono_imputable}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('bono_imputable'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('bono_imputable') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Impuesto a la Renta-->
                    <div class="form-group col-xs-6{{ $errors->has('bono') ? ' has-error' : '' }}">
                        <label for="imp_renta" class="col-md-4 texto">Impuesto a la Renta</label>
                        <div class="col-md-7">
                            <input id="imp_renta" type="text" class="form-control" name="imp_renta" value="@if(!is_null($registro)){{$registro->impuesto_renta}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('imp_renta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('imp_renta') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Alimentacion-->
                    <div class="form-group col-xs-6{{ $errors->has('alimentacion') ? ' has-error' : '' }}">
                        <label for="alimentacion" class="col-md-4 texto">Alimentacion</label>
                        <div class="col-md-7">
                            <input id="alimentacion" type="text" class="form-control" name="alimentacion" value="@if(!is_null($registro)){{$registro->alimentacion}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('alimentacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('alimentacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Listado Banco-->
                    <div class="form-group col-xs-6">
                        <label for="id_banco" class="col-md-4 texto">Banco</label>
                        <div class="col-md-7">
                          <select id="id_banco" name="id_banco" class="form-control select2" style="width: 100%;" required>
                            @foreach($lista_banco as $value)
                            <option value="{{$value->id}}" {{ $registro->banco == $value->id ? 'selected' : ''}}>{{$value->nombre}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!--Numero de Cuenta para Hacer Pago-->
                    <div class="form-group col-xs-6{{ $errors->has('numero_cuenta') ? ' has-error' : '' }}">
                        <label for="numero_cuenta" class="col-md-4 texto">N# Cuenta</label>
                        <div class="col-md-7">
                            <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="@if(!is_null($registro)){{$registro->numero_cuenta}}@endif" onchange="return verifica_caracteres(this);" onkeypress="return isNumberKey(event)" required autofocus>
                            @if ($errors->has('numero_cuenta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('numero_cuenta') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!--Sueldo-->
                    <div class="form-group col-xs-6{{ $errors->has('sueldo') ? ' has-error' : '' }}">
                        <label for="sueldo" class="col-md-4 texto">Sueldo Neto</label>
                        <div class="col-md-7">
                            <input id="sueldo" type="text" class="form-control" name="sueldo" value="@if(!is_null($registro)){{$registro->sueldo_neto}}@endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" required autofocus>
                            @if ($errors->has('sueldo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sueldo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--estado-->
                   <!-- <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 texto">Estado</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$registro->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$registro->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> -->
                    <!--fondos de reserva-->
                    <div class="form-group col-xs-6{{ $errors->has('fondos_reserva') ? ' has-error' : '' }} oculto">
                        <label  class="col-md-4 texto">Fondos de Reserva</label>
                        <div class="col-md-7">
                            <select id="fondos_reserva" name="fondos_reserva" class="form-control" required>
                                <option {{$registro->fondos_reserva == 1 ? : ''}} value="1">Si</option>
                                <option {{$registro->fondos_reserva == 0 ? 'selected' : ''}} value="0">No</option>
                            </select>
                            @if ($errors->has('fondos_reserva'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fondos_reserva') }}</strong>
                                </span>
                            @endif
                        </div>
                     </div>
                      <!--parqueo-->
                    <div class="form-group col-xs-6{{ $errors->has('parqueo') ? ' has-error' : '' }}">
                        <label for="parqueo" class="col-md-4 texto">Parqueo</label>
                        <div class="col-md-7">
                            <input id="parqueo" type="text" class="form-control" name="parqueo" value="@if(!is_null($registro))@if($registro->parqueo==null){{'0.00'}}@else{{$registro->parqueo}}@endif @endif" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                            @if ($errors->has('parqueo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parqueo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(($extension1 == 'jpg') || ($extension1 == 'jpeg') || ($extension1 == 'png'))
                    <div class="form-group col-xs-6{{ $errors->has('curriculum_vitae') ? ' has-error' : '' }}">
                        <label for="curriculum_vitae" class="col-md-4 texto">Curriculum</label>
                        <div class="col-md-7">
                        @if(!is_null($registro->archivo_curriculum))
                            <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_curr.modal', ['id' => $registro->id]) }}">
                                <img  src="{{asset('../storage/app/archivos_nomina')}}/{{$registro->archivo_curriculum}}" width="90%" style="height: 140px;">
                            </a>
                            <span>{{$registro->archivo_curriculum}}</span><br>
                            <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->

                            <a type="button" href="{{asset('descarga_archivo_curriculum')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                <span class="glyphicon glyphicon-download-alt"> </span>
                            </a>
                        @endif
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input id="curriculum" name="curriculum" type="file" class="archivo form-control">
                            <!--<a type="button" onclick="eliminar_archivo_curr('{{$registro->id}}');" class="btn btn-danger btn-sm" target="_blank">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>-->
                        </div>
                    </div>
                    @elseif(($extension1 == 'pdf'))
                    <div class="form-group col-xs-6{{ $errors->has('curriculum_vitae') ? ' has-error' : '' }}">
                        <label for="curriculum_vitae" class="col-md-4 texto">Curriculum</label>
                        <div class="col-md-7">
                        @if(!is_null($registro->archivo_curriculum))
                            <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_curr.modal', ['id' => $registro->id]) }}">
                                <img  src="{{asset('imagenes/pdf.png')}}" width="60%" style="height: 80px;">
                            </a>
                            <span>{{$registro->archivo_curriculum}}</span><br>
                            <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->

                            <a type="button" href="{{asset('descarga_archivo_curriculum')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                <span class="glyphicon glyphicon-download-alt"> </span>
                            </a>
                        @endif
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input id="curriculum" name="curriculum" type="file" class="archivo form-control">
                            <!--<a type="button" onclick="eliminar_archivo_curr('{{$registro->id}}');" class="btn btn-danger btn-sm" target="_blank">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>-->
                        </div>
                    </div>
                    @else
                        @php
                            $variable = explode('/' , asset('/archivos_nomina/'));
                            $d1 = $variable[3];
                            
                        @endphp

                        <div class="form-group col-xs-6{{ $errors->has('curriculum_vitae') ? ' has-error' : '' }}">
                            <label for="curriculum_vitae" class="col-md-4 texto">Curriculum</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_curriculum))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_curr.modal', ['id' => $registro->id]) }}">
                                 <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->archivo_curriculum}}</span><br>
                                <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->

                                <a type="button" href="{{asset('descarga_archivo_curriculum')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="curriculum" name="curriculum" type="file" class="archivo form-control">
                                <!--<a type="button" onclick="eliminar_archivo_curr('{{$registro->id}}');" class="btn btn-danger btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>-->
                            </div>
                        </div>

                    @endif

                    @if(($extension2 == 'jpg') || ($extension2 == 'jpeg') || ($extension2 == 'png'))
                        <div class="form-group col-xs-6{{ $errors->has('ficha_tecnica') ? ' has-error' : '' }}">
                            <label for="ficha_tecnica" class="col-md-4 texto">Ficha Tecnica</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_tecnica))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ficha.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('../storage/app/archivos_nomina')}}/{{$registro->archivo_ficha_tecnica}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->archivo_ficha_tecnica}}</span><br>
                                <a type="button" href="{{asset('descarga_archivo_ficha')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                           
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="ficha" name="ficha" type="file" class="archivo form-control">

                            </div>
                        </div>
                    @elseif(($extension2 == 'pdf'))
                        <div class="form-group col-xs-6{{ $errors->has('ficha_tecnica') ? ' has-error' : '' }}">
                            <label for="ficha_tecnica" class="col-md-4 texto">Ficha Tecnica</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_tecnica))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ficha.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('imagenes/pdf.png')}}" width="60%" style="height: 80px;">
                                </a>
                                <span>{{$registro->archivo_ficha_tecnica}}</span><br>
                                <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->
                                <a type="button" href="{{asset('descarga_archivo_ficha')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="ficha" name="ficha" type="file" class="archivo form-control">
                                <!--<a type="button" onclick="#" class="btn btn-danger btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>-->
                            </div>
                        </div>
                    @else

                        @php
                            $variable = explode('/' , asset('/archivos_nomina/'));
                            $d1 = $variable[3];
                           
                            
                        @endphp

                        <div class="form-group col-xs-6{{ $errors->has('ficha_tecnica') ? ' has-error' : '' }}">
                            <label for="ficha_tecnica" class="col-md-4 texto">Ficha Tecnica</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_tecnica))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ficha.modal', ['id' => $registro->id]) }}">
                                 <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->archivo_ficha_tecnica}}</span><br>
                                <!--<span>{{substr($registro->created_at, 0, 10)}}</span>-->

                                <a type="button" href="{{asset('descarga_archivo_ficha')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="ficha" name="ficha" type="file" class="archivo form-control">
                                <!--<a type="button" onclick="eliminar_archivo_curr('{{$registro->id}}');" class="btn btn-danger btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>-->
                            </div>
                        </div>

                    @endif

                    @if(($extension3 == 'jpg') || ($extension3 == 'jpeg') || ($extension3 == 'png'))
                        <div class="form-group col-xs-6{{ $errors->has('ficha_ocupacional') ? ' has-error' : '' }}">
                            <label for="ficha_ocupacional" class="col-md-4 texto">Ficha Ocupacional</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_ocupacional))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ocupacional.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('../storage/app/archivos_nomina')}}/{{$registro->archivo_ficha_ocupacional}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->archivo_ficha_ocupacional}}</span><br>
                                <a type="button" href="{{asset('descarga_archivo_ocupacional')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="fich_ocup" name="fich_ocup" type="file" class="archivo form-control">

                            </div>
                        </div>
                    @elseif(($extension3 == 'pdf'))
                        <div class="form-group col-xs-6{{ $errors->has('ficha_ocupacional') ? ' has-error' : '' }}">
                            <label for="ficha_ocupacional" class="col-md-4 texto">Ficha Ocupacional</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_ocupacional))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ocupacional.modal', ['id' => $registro->id]) }}">
                                    <img  src="{{asset('imagenes/pdf.png')}}" width="60%" style="height: 80px;">
                                </a>
                                <span>{{$registro->archivo_ficha_ocupacional}}</span><br>

                                <a type="button" href="{{asset('descarga_archivo_ocupacional')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="fich_ocup" name="fich_ocup" type="file" class="archivo form-control">

                            </div>
                        </div>
                    @else

                        @php
                            $variable = explode('/' , asset('/archivos_nomina/'));
                            $d1 = $variable[3];
                            
                            
                        @endphp

                        <div class="form-group col-xs-6{{ $errors->has('ficha_ocupacional') ? ' has-error' : '' }}">
                            <label for="ficha_ocupacional" class="col-md-4 texto">Ficha Ocupacional</label>
                            <div class="col-md-7">
                            @if(!is_null($registro->archivo_ficha_ocupacional))
                                <a data-toggle="modal" data-target="#foto" href="{{ route('nomina_imagen_ocupacional.modal', ['id' => $registro->id]) }}">
                                 <img  src="{{asset('imagenes/office.png')}}" width="90%" style="height: 140px;">
                                </a>
                                <span>{{$registro->archivo_ficha_ocupacional}}</span><br>
                                <a type="button" href="{{asset('descarga_archivo_ocupacional')}}/{{$registro->id}}" class="btn btn-primary btn-sm" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"> </span>
                                </a>
                            @endif
                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input id="fich_ocup" name="fich_ocup" type="file" class="archivo form-control">

                            </div>
                        </div>

                    @endif


                     <div class="form-group col-xs-6">
                        <label for="aporte_personal" class="col-md-4 texto">Aporte Personal</label>
                        <div class="col-md-7">
                            

                            <select id="aporte_personal" name="aporte_personal" class="form-control" required>
                                @foreach($aporte_personal as $ap)
                                    <option @if($registro->aporte_personal == $ap->id) selected @endif value="{{$ap->id}}">{{$ap->valor}}%</option>
                                @endforeach
                            </select>
                        </div>
                   </div>
                    <!--Cajas-->
                    {{--<div class="form-group col-xs-6">
                        <label for="id_caja" class="col-md-4 texto">{{trans('contableM.cajas')}}</label>
                        <div class="col-md-7">
                          <select id="id_caja" name="id_caja" class="form-control" required>
                            @foreach($uso_sucursales as $caja)
                              <option @if($registro->id_caja == $caja->id) selected @endif value="{{$caja->id}}"> {{$caja->codigo_sucursal}}:{{$caja->nombre_sucursal}} => {{$caja->cajas[0]->codigo_sucursal}}:{{$caja->cajas[0]->nombre_caja}} </option>
                            @endforeach
                          </select>
                        </div>
                    </div>--}}


                    
                    <div class="form-group col-xs-6">
                        <label for="conyuge" class="col-md-4 texto">{{trans('nomina.extension_de_conyuge')}}</label>
                        <div class="col-md-7">
                          <input  style="width:17px;height:17px;" type="checkbox" name="verificar_conyuge" id="verificar_conyuge" @if($registro->extension_conyuge == 1) checked @endif >
                        </div>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('sucursal') ? ' has-error' : '' }}">
                        <label for="sucursal" class="col-md-4 texto">{{trans('tsidebar.sucursales')}}</label>
                        <div class="col-md-7">
                            <select name="sucursal" id="sucursal" class="form-control">
                                <!--option value="">Seleccione</option-->
                                @foreach($ct_sucursales as $value)
                                    <option  value="{{$value->id}}" @if($value->id == $registro->id_sucursal) selected @endif>{{$value->nombre_sucursal}}</option>
                                @endforeach
                            </select>    
                        </div>
                        @if ($errors->has('sucursal'))
                        <span class="help-block text-center">
                            <strong>{{ $errors->first('sucursal') }}</strong>
                        </span>
                        @endif
                    </div>
                    
                    <div class="form-group col-xs-6">
                        <label for="movilizacion" class="col-md-4 texto">{{trans('nomina.movilizacion')}}</label>
                        <div class="col-md-7">
                            <input type="number" name="movilizacion" id="movilizacion" class="form-control" value="{{number_format($registro->movilizacion, 2, '.', '')}}">
                        </div>
                    </div>
                    
                    <div class="form-group col-xs-6">
                        <label for="fecha_salida" class="col-md-4 texto">{{trans('nomina.fecha_primer_ingreso')}}</label>
                        <div class="col-md-7">
                            <input type="date" name="fecha_ant" id="fecha_ant" class="form-control" value="{{$registro->fecha_ant}}">
                        </div>
                    </div>
                <div>

                    <!-- <div class="form-group col-xs-6">
                        <label for="fecha_salida" class="col-md-4 texto">Fecha Primer Ingreso</label>
                        <div class="col-md-7">
                            <input type="date" name="fecha_ant" id="fecha_ant" class="form-control" value="{{$registro->fecha_ant}}">
                        </div>
                    </div> -->


                    
                <!--Fecha_Ingreso -->
                <div class="form-group col-xs-6{{ $errors->has('fecha_salida') ? ' has-error' : '' }}">
                        <label for="fecha_salida" class="col-md-4 texto" style="color: red;"> {{trans('nomina.fecha_salida')}} </label>
                        <div class="col-md-7">
                            <input id="fecha_salida" type="date" class="form-control" name="fecha_salida" value="@if(!is_null($registro)){{$registro->fecha_salida}}@endif" autofocus>
                            @if ($errors->has('fecha_salida'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_salida') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>
                    <div class="form-group col-xs-6">
                        <label for="pasante" class="col-md-4 texto">Pasante</label>
                        <div class="col-md-7">
                            <input style="width:17px;height:17px;" type="checkbox" @if($registro->pasante == 1) checked @endif  name="pasante" id="pasante" onchange="tipo_pasante(this)">
                        </div>
                    </div>

                    </div>

                <div class="form-group col-xs-6{{ $errors->has('jornada') ? ' has-error' : '' }}">
                    <label for="jornada" class="col-md-4 texto">{{trans('nomina.jornada_laboral')}}</label>
                        <div class="col-md-7">
                            <select name="jornada" id="jornada" class="form-control" onchange="tipo_jornada(this)">
                                <option  value="0" @if($registro->jornada==0) selected @endif>{{trans('nomina.jornada_completa')}}</option>
                                <option  value="1" @if($registro->jornada==1) selected @endif>{{trans('nomina.media_jornada')}}</option>
                                <option  value="2" @if($registro->jornada==2) selected @endif>{{trans('nomina.jornada_parcial')}}</option>
                            </select>    
                        </div>
                        @if ($errors->has('jornada'))
                        <span class="help-block text-center">
                            <strong>{{ $errors->first('jornada') }}</strong>
                        </span>
                        @endif
                </div>

                <div class="form-group col-xs-6{{ $errors->has('sueldo_pasante') ? ' has-error' : '' }}">
                        <label for="sueldo_pasante" class="col-md-4 texto"> {{trans('nomina.sueldo_pasante')}}</label>
                        <div class="col-md-7">
                            <input id="sueldo_pasante" type="text" class="form-control" name="sueldo_pasante" value="{{$registro->sueldo_pasante}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" autofocus>
                            @if ($errors->has('sueldo_pasante'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sueldo_pasante') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>

                


                <div class="form-group col-xs-6{{ $errors->has('dias_laborables') ? ' has-error' : '' }}">
                        <label for="dias_laborables" class="col-md-4 texto"> {{trans('nomina.dias_laborables')}} </label>
                        <div class="col-md-7">
                            <input id="dias_laborables" type="text" class="form-control" name="dias_laborables" value="{{$registro->dias_laborables}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" autofocus>
                            @if ($errors->has('dias_laborables'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('dias_laborables') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>


                <div class="form-group col-xs-6{{ $errors->has('funciones') ? ' has-error' : '' }}">
                        <label for="cargo" class="col-md-4 texto">{{trans('nomina.funciones')}}</label>
                        <div class="col-md-7">
                            <textarea id="funciones" type="text" class="form-control" name="funciones" value="{{$registro->funciones}}" style="text-transform: uppercase; height: 105px; width: 404px;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" autofocus>@if(!is_null($registro->funciones)){{$registro->funciones}} @else  @endif</textarea>
                            @if ($errors->has('funciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('funciones') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>

                
                <div class="form-group col-xs-6{{ $errors->has('horas_mes') ? ' has-error' : '' }}">
                        <label for="horas_mes" class="col-md-4 texto"> {{trans('nomina.horas_al_mes')}} </label>
                        <div class="col-md-7">
                            <input id="horas_mes" type="text" class="form-control" name="horas_mes" value="{{$registro->horas_mes}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" autofocus>
                            @if ($errors->has('horas_mes'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('horas_mes') }}</strong>
                                </span>
                            @endif
                        </div>
                </div>


                <div class="separator"></div>
                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="button" id="btn_add" class="btn btn-primary">
                            Actualizar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

    $( document ).ready(function() {
        let condicion = $("#jornada").val();
        if(condicion == "0"){
            $("#horas_mes").attr("disabled", true);
            $("#dias_laborables").attr("disabled", true);
        }
    });

    $( document ).ready(function() {
        let condicion = $("#check_pa").val();
        if(condicion == "0"){
            $("#sueldo_pasante").attr("disabled", true);
        }
    });

    document.getElementById('btn_add').addEventListener('click', validarCampos);
    document.getElementById('identificacion').addEventListener('keyup', buscarIdentificacion)
      function validarCampos(e){
        e.preventDefault();

        let selectores = document.querySelectorAll('input');
        for(let i = 0; i<selectores.length ; i++){
            if(selectores[i].type == 'text' && selectores[i].type == 'date' && selectores[i].name != 'chek_dis' ){
                console.log(selectores[i].name + " :" +selectores[i].value);
            }
        }
        

        let inputs = [
            document.getElementById('identificacion'),
            document.getElementById('nombre1'),          
            document.getElementById('nombre2'),          
            document.getElementById('apellido1'),        
            document.getElementById('apellido2'),      
            document.getElementById( 'id_pais'),    
            document.getElementById('ciudad'),  
            document.getElementById('direccion'),  
            document.getElementById( 'telefono1'),  
            document.getElementById('telefono2'),      
            document.getElementById( 'ocupacion'),      
            document.getElementById( 'fecha_nacimiento'),
            document.getElementById( 'genero'),
            document.getElementById( 'etnia'),     
            document.getElementById( 'nivel_academico'),  
            document.getElementById( 'estado_civil'),     
            document.getElementById( 'email'),           
            document.getElementById( 'area'),             
            document.getElementById( 'cargo'),
            document.getElementById( 'funciones'),
            document.getElementById( 'fecha_actividad'), 
            document.getElementById( 'fondo_reserva'),    
            document.getElementById( 'decimo_tercero'),  
            document.getElementById('decimo_cuarto'),    
            document.getElementById('seguro_privado'),   
            document.getElementById('horario'),          
            document.getElementById('id_banco'),         
            document.getElementById('numero_cuenta'),    
            document.getElementById( 'sueldo'),   
            document.getElementById( 'aporte_personal')  
        ];
        let validado = true;

        for(let i=0;i<inputs.length;i++){

            if(inputs[i].value == '' || inputs[i].value == null){
                let res ="";
                let variable = inputs[i].name;
                if(res=='fecha_actividad'){
                    alertasPersonalizadas('error', 'Error', 'FALTA SELECCIONAR FECHA DE INGRESO')
                }else{
                    res = res.replace("id", "");
                    res = variable.replace("_", " ");
                    res = res.replace("1", "");
                    res = res.replace("2", "");
                    

                    res = res.toUpperCase();
                    alertasPersonalizadas('error', 'Error', 'CAMPO VACIO '+res)
                }
                validado= false;
                break;
            }else{
                validado = true;
            }

        }

        var jornada = $('#jornada').val();
        var dias = $('#dias_laborables').val();
        var horas = $('#horas_mes').val();

        if( jornada != 0){

            if( dias == ''){
                validado= false;  
                alertasPersonalizadas('error', 'Error', 'Ingrese los días laborables');  
            }

            if( horas == ''){
                validado= false;  
                alertasPersonalizadas('error', 'Error', 'Ingrese las horas mes');  
            }

            if( dias == 0 ){
                validado= false;  
                alertasPersonalizadas('error', 'Error', 'los días laborables no pueden ser 0');  
            }

            if( horas == 0){
                validado= false;  
                alertasPersonalizadas('error', 'Error', 'Las horas mes no pueden ser 0');  
            }

        }

        if(validado == true){
            $('#actua_form').submit();
        }
    }
    function alertasPersonalizadas(icon, title, text){
        Swal.fire({
          icon: `${icon}`,
          title: `${title}`,
          text: `${text}`
        })
    }
    $(document).ready(function(){
         //Verifica Check

        openCity(1,'datos_personal');

        $('.select2_cuentas').select2({
            tags: false
        });

         //Verifica si viene activado el Check
         //chek_dis



    });

    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{date("Y-m-d")}}',
            });
    });


    //buscarIdentificacion();
    /*$("#identificacion").blur(function() {
        buscarIdentificacion();
    });*/

    function buscarIdentificacion(){
        $.ajax({
            type: 'post',
            url:"{{route('nomina.identificacion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#identificacion").val(),
             'id_empresa': $("#id_empresa").val()},
            success: function(data){
                console.log(data);
                if(data.existe!=null){
                    if(data.usuario!=null){
                        $("#btn_add").prop( "disabled", false );
                        $("#nombre1").val(data.usuario.nombre1);
                        $("#nombre2").val(data.usuario.nombre2);
                        $("#apellido1").val(data.usuario.apellido1);
                        $("#apellido2").val(data.usuario.apellido2);
                        $("#ciudad").val(data.usuario.ciudad);
                        $("#direccion").val(data.usuario.direccion);
                        $("#telefono1").val(data.usuario.telefono1);
                        $("#telefono2").val(data.usuario.telefono2);
                        $("#ocupacion").val(data.usuario.ocupacion);
                        $("#fecha_nacimiento").val(data.usuario.fecha_nacimiento);
                        $("#email").val(data.usuario.email);
                        $("#password").prop('required',false);
                        $("#password_confirm").prop('required',false);

                        //$(".divpass").hide();
                    }else{
                        $("#nombre1").val("");
                        $("#nombre2").val("");
                        $("#apellido1").val("");
                        $("#apellido2").val("");
                        $("#ciudad").val("");
                        $("#direccion").val("");
                        $("#telefono1").val("");
                        $("#telefono2").val("");
                        $("#ocupacion").val("");
                        $("#fecha_nacimiento").val("");
                        $("#email").val("");
                        $("#password").prop('required',true);
                        $("#password_confirm").prop('required',true);

                        $("#nombre1").prop( "disabled", false );
                        $("#nombre2").prop( "disabled", false );
                        $("#apellido1").prop( "disabled", false );
                        $("#apellido2").prop( "disabled", false );
                        $("#ciudad").prop( "disabled", false );
                        $("#direccion").prop( "disabled", false );
                        $("#telefono1").prop( "disabled", false );
                        $("#telefono2").prop( "disabled", false );
                        $("#ocupacion").prop( "disabled", false );
                        $("#fecha_nacimiento").prop( "disabled", false );
                        $("#email").prop( "disabled", false );
                        $("#btn_add").prop( "disabled", false );
                        $(".divpass").show();
                    }
                }else{
                    //swal("Error!","El Empleado ya se encuentra registrado en esta empresa");
                    //$('#btn_add').attr('disabled','disabled');
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }



    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function verifica_caracteres(elemento){

        // var Max_Length = 10;
        // var num_cuenta = parseInt(elemento.value);
        // var longitud = num_cuenta.toString().length;

        // if(longitud > Max_Length){
        //     swal("Error!","El Numero de Cuenta no debe superar los 10 caracteres");
        //     //$('#numero_cuenta').val('');
        // } else if(longitud < Max_Length){
        //     swal("Error!","El Numero de Cuenta no debe ser menor a los 10 caracteres");
        //     //$('#numero_cuenta').val('');
        // }

    }

    $(".checkVal").click(function(){

        if ($(this).is(':checked')){

           document.getElementById("datos_porcentaje").style.visibility = "visible";

        }else{

           document.getElementById("datos_porcentaje").style.visibility = "hidden";

           $('#porcent_discapacidad').val(" ");

        }

    });


    function openCity(evt, id) {

        ///
      var discap = $('#chek_dis').val();

      if (discap == 1){

        document.getElementById("datos_porcentaje").style.visibility = "visible";

      }else{

        document.getElementById("datos_porcentaje").style.visibility = "hidden";

        $('#porcent_discapacidad').val(" ");

      }

      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");

      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }

      tablinks = document.getElementsByClassName("tablinks");

      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }

      document.getElementById(id).style.display = "block";
      evt.currentTarget.className += " active";
    }

    function soloNumeros(e)
    {
        // capturamos la tecla pulsada
        var teclaPulsada=window.event ? window.event.keyCode:e.which;

        // capturamos el contenido del input
        var valor=e.value;

        // 45 = tecla simbolo menos (-)
        // Si el usuario pulsa la tecla menos, y no se ha pulsado anteriormente
        // Modificamos el contenido del mismo añadiendo el simbolo menos al
        // inicio
        if(teclaPulsada==45 && valor.indexOf("-")==-1)
        {
            document.getElementById("inputNumero").value="-"+valor;
        }

        // 13 = tecla enter
        // 46 = tecla punto (.)
        // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
        // punto
        if(teclaPulsada==13 || (teclaPulsada==46 && valor.indexOf(".")==-1))
        {
            return true;
        }

        // devolvemos true o false dependiendo de si es numerico o no
        return /\d/.test(String.fromCharCode(teclaPulsada));
    }


    $(".checkVal").click(function(){

        if ($(this).is(':checked')){

        document.getElementById("datos_porcentaje").style.visibility = "visible";

        }else{

        document.getElementById("datos_porcentaje").style.visibility = "hidden";
        $("#porcent_discapacidad").val("");


        }

    });


    /*function actualiza_empleado(){

        var formulario = document.forms["actua_form"];

        //Datos Personales

        var id_emp = formulario.id_empresa.value;
        var cedula = formulario.identificacion.value;
        var nombre1 = formulario.nombre1.value;
        var nombre2 = formulario.nombre2.value;
        var apellido1 = formulario.apellido1.value;
        var apellido2 = formulario.apellido2.value;
        var id_pais = formulario.id_pais.value;
        var ciudad = formulario.ciudad.value;
        var direccion = formulario.direccion.value;
        var telefono1 = formulario.telefono1.value;
        var telefono2 = formulario.telefono2.value;
        var ocupacion = formulario.ocupacion.value;
        var fecha_nacimiento = formulario.fecha_nacimiento.value;
        var id_genero = formulario.genero.value;
        var etnia = formulario.etnia.value;
        var porcent_discapacidad = formulario.porcent_discapacidad.value;

        var nivel_academico = formulario.nivel_academico.value;
        var estado_civil = formulario.estado_civil.value;

        var id_tipo_usuario = formulario.id_tipo_usuario.value;
        //var color = formulario.color.value;
        var email = formulario.email.value;

        //Datos Empleado
        var cargo = formulario.cargo.value;
        var area = formulario.area.value;
        var fecha_actividad = formulario.fecha_actividad.value;
        var fondo_reserva = formulario.fondo_reserva.value;
        var horario = formulario.horario.value;
        var sueldo = formulario.sueldo.value;

        //Mensaje
        var msj = "";

        if(id_emp == ""){

           msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(cedula == ""){

           msj = msj + "Por favor, Ingrese la Identificacion<br/>";
        }

        if(nombre1 == ""){

           msj = msj + "Por favor, Ingrese el primer nombre<br/>";
        }

        if(nombre2 == ""){

           msj = msj + "Por favor, Ingrese el segundo nombre<br/>";
        }

        if(apellido1 == ""){

           msj = msj + "Por favor, Ingrese el primer apellido<br/>";
        }

        if(apellido2 == ""){

           msj = msj + "Por favor, Ingrese el segundo apellido<br/>";
        }


        if(id_pais == ""){

           msj = msj + "Por favor, Seleccione un pais<br/>";
        }

        if(ciudad == ""){

           msj = msj + "Por favor, Ingrese la ciudad<br/>";
        }

        if(direccion == ""){

           msj = msj + "Por favor, Ingrese la direccion<br/>";
        }

        if(telefono1 == ""){

           msj = msj + "Por favor, Ingrese el telefono de domicilio<br/>";
        }

        if(telefono2 == ""){

           msj = msj + "Por favor, Ingrese el telefono celular<br/>";
        }

        if(ocupacion == ""){

           msj = msj + "Por favor, Ingrese la ocupacion<br/>";
        }

        if(fecha_nacimiento == ""){

           msj = msj + "Por favor, Seleccione la fecha de nacimiento<br/>";
        }

        if(id_genero == ""){

           msj = msj + "Por favor, Seleccione el genero<br/>";
        }

        if(etnia == ""){

           msj = msj + "Por favor, ingrese la etnia<br/>";
        }

        if(nivel_academico == ""){

           msj = msj + "Por favor, Seleccione el nivel academico<br/>";
        }

        if(estado_civil == ""){

           msj = msj + "Por favor, Seleccione el estado civil<br/>";
        }

        if(email == ""){

           msj = msj + "Por favor, ingrese el correo electronico<br/>";
        }

        if(cargo == ""){

           msj = msj + "Por favor, ingrese el cargo que ocupa<br/>";
        }

        if(area == ""){

           msj = msj + "Por favor, Seleccione el Area donde labora<br/>";
        }

        if(fecha_actividad == ""){

           msj = msj + "Por favor, Seleccione la fecha de ingreso de Labores<br/>";
        }

        if(fondo_reserva == ""){

           msj = msj + "Por favor, Seleccione si acumula fondos de reservas Iees<br/>";
        }

        if(horario == ""){

           msj = msj + "Por favor, Seleccione el Horario de Labores<br/>";
        }

        if(sueldo == ""){

           msj = msj + "Por favor, Ingrese el sueldo del empleado<br/>";
        }

        if(msj != ""){

            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
            type: 'post',
            url:"{{route('nomina.actualizar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#actua_form").serialize(),
            success: function(data){

                location.href ="{{route('nomina.index')}}";

            },
            error: function(data){
                console.log(data);

            }
        });



    }*/

    function checkformat(entry) {

        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){
            entry.value='0.00';
        }
        if (test < 0) {

            entry.value = '0.00';
        }

    }

    function eliminar_archivo_curr(id){
        var opcion = confirm("¿Estas Seguro que deseas eliminar el Archivo?");
        if (opcion == true) {
            $.ajax({
                type: 'get',
                url:'{{ url("nomina/eliminar/archivo")}}/'+id,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                success: function(data){
                    //console.log(data);
                    alert(data);
                    location.reload();
                }
            })

        }
    }


    $(document).ready(function() {
        $('.select2').select2();
    });

    function tipo_jornada(item){
       // alert(item.value);
        if(item.value == '1' || item.value == '2'){
            $('#horas_mes').removeAttr("disabled");
            $('#dias_laborables').removeAttr("disabled");
        }else{
            $('#horas_mes').attr("disabled","disabled");  
            $('#dias_laborables').attr("disabled","disabled");
        }
        console.log(item);
    }

    function tipo_pasante(item){
        //alert(item.value);
        let condicion = $("#pasante").is(":checked");
        if(condicion){
            $('#sueldo_pasante').removeAttr("disabled");
        }else{
            $('#sueldo_pasante').attr("disabled","disabled");  
        }
        console.log(item);
    }



</script>

</section>
@endsection
