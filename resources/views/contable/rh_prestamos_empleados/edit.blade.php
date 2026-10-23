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

</style>

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
        <li class="breadcrumb-item"><a href="#">Nòmina</a></li>
        <li class="breadcrumb-item"><a href="../nomina">Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>

    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Editar Empleado</h3>
            </div>
            <div class="col-md-1 text-right">
                <button id="actualiza_rol_pago" onclick="actualiza_rol_pago()" class="btn btn-primary btn-gray">
                   Guardar
                </button>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
            <div class="tab">
                <button class="tablinks" onclick="openCity(event, 'datos_personal')">Datos Personales</button>
                <button class="tablinks" onclick="openCity(event, 'datos_empleado')">Datos Del Empleado</button>
            </div>
            <form class="form-vertical"  id="crear_form" role="form" method="POST" autocomplete="off">
                {{ csrf_field() }}
                <div class="separator"></div>
                <div class="box-body col-xs-24 tabcontent" id="datos_personal">
                    <!--Empresa-->
                    <div class="form-group col-xs-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-4 texto">Empresa</label>
                        <div class="col-md-7">
                          <select class="form-control" id="id_empresa" name="id_empresa" onchange="buscarIdentificacion()">
                            @foreach($empresas as $value)
                              <option value="{{$value->id}}" @if($empresa->id ==  $value->id) selected="selected" @endif >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-xs-6{{ $errors->has('identificacion') ? ' has-error' : '' }}">
                        <label for="identificacion" class="col-md-4 texto">Identificaci&oacute;n</label>
                        <div class="col-md-7">
                            <input id="id" type="hidden" class="form-control" name="id" value="@if(!is_null($registro)){{$registro->id}}@endif">
                            <input id="identificacion" type="text" class="form-control"  name="identificacion" value="@if(!is_null($usuario)){{$usuario->id}}@endif">
                            @if ($errors->has('identificacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('identificacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--primer nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre1" class="col-md-4 texto">Primer Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre1" type="text" class="form-control" name="nombre1" value="@if(!is_null($usuario)){{$usuario->nombre1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
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
                            <input id="nombre2" type="text" class="form-control" name="nombre2" value="@if(!is_null($usuario)){{$usuario->nombre2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
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
                            <input id="apellido1" type="text" class="form-control" name="apellido1" value="@if(!is_null($usuario)){{$usuario->apellido1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
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
                            <input id="apellido2" type="text" class="form-control" name="apellido2" value="@if(!is_null($usuario)){{$usuario->apellido2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
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
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="@if(!is_null($usuario)){{$usuario->ciudad}}@endif"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">

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
                            <input id="direccion" type="text" class="form-control" name="direccion" value="@if(!is_null($usuario)){{$usuario->direccion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">

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
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="@if(!is_null($usuario)){{$usuario->telefono1}}@endif">

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
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="@if(!is_null($usuario)){{$usuario->telefono2}}@endif">

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
                            <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="@if(!is_null($usuario)){{$usuario->ocupacion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
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
                            <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="@if(!is_null($usuario)){{$usuario->fecha_nacimiento}}@endif">

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
                            <select id="genero" name="genero" class="form-control">
                                <option>Seleccione...</option>
                                <option {{ $registro->sexo == 'M' ? 'selected' : ''}} value="M">MASCULINO</option>
                                <option {{ $registro->sexo == 'F' ? 'selected' : ''}} value="F">FEMENINO</option>
                            </select>
                        </div>
                    </div>
                    <!--ETNIA-->
                    <div class="form-group col-xs-6">
                        <label for="etnia" class="col-md-4 texto">Etnia</label>
                        <div class="col-md-7">
                            <input id="etnia" type="text" class="form-control" name="etnia" value="@if(!is_null($registro)){{$registro->etnia}}@endif">
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
                    @if($registro->check_discapacidad == "1")
                       <!--% Discapacidad-->
                        <div class="form-group col-xs-6"  id="datos_porcentaje">
                            <label for="porcent_discapacidad" class="col-md-4 texto">Porcentaje</label>
                            <div class="col-md-7">
                                <input id="porcent_discapacidad" type="text" class="form-control" name="porcent_discapacidad" value="@if(!is_null($registro)){{$registro->porcentaje_discapacidad}}@endif">
                            </div>
                        </div>
                    @endif
                    <!--NUMERO DE CARGA-->
                    <div class="form-group col-xs-6">
                        <label for="numero_carga" class="col-md-4 texto">Nùmero de Cargas</label>
                        <div class="col-md-7">
                            <input id="numero_carga" type="text" class="form-control" name="numero_carga" value="@if(!is_null($registro)){{$registro->numero_cargas}}@endif">
                        </div>
                    </div>
                    <!--NIVEL ACADEMICO-->
                    <div class="form-group col-xs-6">
                        <label for="nivel_academico" class="col-md-4 texto">Nivel Acadèmico</label>
                        <div class="col-md-7">
                            <select id="nivel_academico" name="nivel_academico" class="form-control">
                               <option>Seleccione...</option>
                               <option {{ $registro->nivel_academico == '1' ? 'selected' : ''}} value="1">BACHILLER</option>
                               <option {{ $registro->nivel_academico == '2' ? 'selected' : ''}} value="2">UNIVERSITARIO</option>
                               <option {{ $registro->nivel_academico == '3' ? 'selected' : ''}} value="3">TERCER NIVEL</option>
                               <option {{ $registro->nivel_academico == '4' ? 'selected' : ''}} value="4">CUARTO NiVEL</option>
                            </select>
                        </div>
                    </div>
                    <!--ESTADO CIVIL-->
                    <div class="form-group col-xs-6">
                        <label for="estado_civil" class="col-md-4 texto">Estado Civil</label>
                        <div class="col-md-7">
                          <select id="estado_civil" name="estado_civil" class="form-control">
                            <option>Seleccione...</option>
                            <option {{ $registro->estado_civil == '1' ? 'selected' : ''}} value="1">UNIDO</option>
                            <option {{ $registro->estado_civil == '2' ? 'selected' : ''}} value="2">SOLTERO</option>
                            <option {{ $registro->estado_civil == '3' ? 'selected' : ''}} value="3">CASADO</option>
                            <option {{ $registro->estado_civil == '4' ? 'selected' : ''}} value="4">DIVORCIADO</option>
                            <option {{ $registro->estado_civil == '5' ? 'selected' : ''}} value="5">VIUDO</option>
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
                            <input id="email" type="email" class="form-control" name="email" value="@if(!is_null($usuario)){{$usuario->email}}@endif">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Password-->
                    <div class="form-group divpass col-xs-6{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 texto">Password</label>
                        <div class="col-md-7">
                            <input id="password" type="password" class="form-control" name="password">

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Confirmar Password-->
                    <div class="form-group divpass col-xs-6">
                        <label for="password-confirm" class="col-md-4 texto">Confirma Password</label>
                        <div class="col-md-7">
                            <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                        </div>
                    </div>
                </div> 
                <div class="box-body col-xs-24 tabcontent" id="datos_empleado">
                    <div class="separator"></div>
                    <!--Cargo-->
                    <div class="form-group col-xs-6{{ $errors->has('cargo') ? ' has-error' : '' }}">
                        <label for="cargo" class="col-md-4 texto">Cargo que Ocupa</label>
                        <div class="col-md-7">
                            <input id="cargo" type="text" class="form-control" name="cargo" value="@if(!is_null($registro)){{$registro->cargo}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            @if ($errors->has('cargo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cargo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                     <!--AREA-->
                    <div class="form-group col-xs-6">
                        <label for="area" class="col-md-4 texto">Area</label>
                        <div class="col-md-7">
                          <select id="area" name="area" class="form-control">
                            <option>Seleccione...</option>
                            <option {{ $registro->area == 'ADMINISTRATIVA' ? 'selected' : ''}} value="ADMINISTRATIVA">ADMINISTRATIVA</option>
                            <option {{ $registro->area == 'MEDICA' ? 'selected' : ''}} value="MEDICA">MÈDICA</option>
                          </select>
                        </div>
                    </div>
                    <!--Fecha_Ingreso -->
                    <div class="form-group col-xs-6{{ $errors->has('fecha_actividad') ? ' has-error' : '' }}">
                        <label for="fecha_actividad" class="col-md-4 texto">Fecha de Ingreso</label>
                        <div class="col-md-7">
                            <input id="fecha_actividad" type="date" class="form-control" name="fecha_actividad" value="@if(!is_null($registro)){{$registro->fecha_ingreso}}@endif">
                            @if ($errors->has('fecha_actividad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_actividad') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--DISCAPACIDAD-->
                    <div class="form-group col-xs-6">
                        <label for="fondo_reserva" class="col-md-4 texto">Acumula Fondos de Reservas Iess?</label>
                        <div class="col-md-7">
                            <input style="width:17px;height:17px;" type="checkbox" id="fondo_reserva" class="checkVal" name="fondo_reserva" value="1"
                            @if(old('fondo_reserva')=="1")
                              checked
                            @elseif($registro->acumula_fondo == "1")                         
                              checked
                            @endif>
                        </div>
                    </div>
                    <!--Horario Labores-->
                    <div class="form-group col-xs-6">
                        <label for="horario" class="col-md-4 texto">Horario</label>
                        <div class="col-md-7">
                            <select id="horario" name="horario" class="form-control">
                            <option>Seleccione...</option>
                                <option {{ $registro->horario == '8:00 - 16:40' ? 'selected' : ''}} value="8:00 - 16:40">8:00 - 16:40</option>
                                <option {{ $registro->horario == '7:30 - 16:10' ? 'selected' : ''}} value="7:30 - 16:10">7:30 - 16:10</option>
                                <option {{ $registro->horario == '7:00 - 15:40' ? 'selected' : ''}} value="7:00 - 15:40">7:00 - 15:40</option>
                                <option {{ $registro->horario == '8:30 - 17:10' ? 'selected' : ''}} value="8:30 - 17:10">8:30 - 17:10</option>
                                <option {{ $registro->horario == '9:00 - 17:40' ? 'selected' : ''}} value="9:00 - 17:40">9:00 - 17:40</option>
                                <option {{ $registro->horario == '9:30 - 18:10' ? 'selected' : ''}} value="9:30 - 18:10">9:30 - 18:10</option>
                            </select>
                        </div>
                    </div>
                    <!--Sueldo-->
                    <div class="form-group col-xs-6{{ $errors->has('sueldo') ? ' has-error' : '' }}">
                        <label for="sueldo" class="col-md-4 texto">Salario Neto</label>
                        <div class="col-md-7">
                            <input id="sueldo" type="text" class="form-control" name="sueldo" value="@if(!is_null($registro)){{$registro->sueldo_neto}}@endif" onkeypress="return soloNumeros(event)" onblur="this.value=parseFloat(this.value).toFixed(2);">
                            @if ($errors->has('sueldo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sueldo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
            </form>
        </div>
    </div>
        

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">

    $(document).ready(function(){

        //document.getElementById("datos_porcentaje").style.visibility = "hidden";

        openCity(1,'datos_personal');
        
        $('.select2_cuentas').select2({
            tags: false
        });
    
    });
        
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{date("Y-m-d")}}',
            });
    });


    buscarIdentificacion();
    $("#identificacion").blur(function() {
        buscarIdentificacion();
    });

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
                        $('#actualiza_rol_pago').removeAttr('disabled');
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

                        $(".divpass").show();
                    }
                }else{
                   // alert("la identifiacaion ya esta registrada en esta empresa");
                   // $('#btn_add').attr('disabled','disabled');
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

    $(".checkVal").click(function(){

        if ($(this).is(':checked')){

           document.getElementById("datos_porcentaje").style.visibility = "visible";         

        }else{
         
           document.getElementById("datos_porcentaje").style.visibility = "hidden";

           $('#porcent_discapacidad').val(" ");

        }
    
    });


    function openCity(evt, id) {
      
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


    function actualiza_rol_pago(){

        alert("Prueba");


    }


</script>

</section>
@endsection
