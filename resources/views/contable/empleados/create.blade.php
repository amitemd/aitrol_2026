@extends('contable.empleados.base')
@section('action-content')

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
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('empleados.index')}}">Vendedor - Recaudador</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <form class="form-vertical" role="form" method="POST" action="{{route('empleados_store')}}">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                <!--<h3 class="box-title">Agregar Nuevo Vendedor / Recaudador</h3>-->
                <h5><b>CREAR VENDEDOR - RECAUDADOR</b></h5>
                </div>
                <div class="col-md-3" style="text-align: right;">
                    <button onclick="goBack()"  class="btn btn-primary btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--Cedula Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                    <label for="cedula" class="col-md-4 control-label">Cédula</label>
                    <div class="col-md-7">
                        <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ old('cedula') }}" autocomplete="off" required autofocus onkeyup="validarCedula(this.value);">
                        @if ($errors->has('id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Primer Nombre Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                    <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                    <div class="col-md-7">
                    <input id="nombre1" type="text" class="form-control"  name="nombre1" value="{{old('nombre1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                    @if ($errors->has('nombre1'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre1') }}</strong>
                        </span>
                    @endif
                    </div>
                </div>
                <!--Segundo Nombre Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                    <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                    <div class="col-md-7">
                        <input id="nombre2" type="text" class="form-control"  name="nombre2" value="{{old('nombre2')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off">
                        @if ($errors->has('nombre2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre2') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Primer Apellido Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                    <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                    <div class="col-md-7">
                        <input id="apellido1" type="text" class="form-control"  name="apellido1" value="{{old('apellido1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('apellido1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellido1') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Segundo Apellido Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                    <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                    <div class="col-md-7">
                        <input id="apellido2" type="text" class="form-control"  name="apellido2" value="{{old('apellido2')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('apellido2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellido2') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Pais-->
                <div class="form-group col-xs-6 {{ $errors->has('id_pais') ? ' has-error' : '' }}">
                    <label for="id_pais" class="col-md-4 control-label">Pais</label>
                    <div class="col-md-7">
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
                        <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
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
                        <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>

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
                        <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" autocomplete="off" required autofocus>

                        @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--telefono celular-->
                <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                    <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>
                    <div class="col-md-7">
                        <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ old('telefono2') }}" autocomplete="off" required autofocus>

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
                        <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="{{ old('ocupacion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
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
                                        @if (($tipousuarios[$i]->id == 17)||($tipousuarios[$i]->id == 18)) 
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
                <!--email-->                        
                <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail</label>

                    <div class="col-md-7">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Campo a Ingresar Otra Tabla-->
                <!--Grupo-->
                <div class="form-group col-xs-6{{ $errors->has('grupo') ? ' has-error' : '' }}">
                    <label for="grupo" class="col-md-4 control-label">Grupo</label>
                    <div class="col-md-7">
                        <select class="form-control id="grupo" name="grupo">
                            <option  value="1">General</option>
                        </select>
                        @if ($errors->has('grupo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('grupo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <!--Cta.Comision-->
                <div class="form-group col-xs-6{{ $errors->has('cta_comision') ? ' has-error' : '' }}">
                    <label for="cta_comision" class="col-md-4 control-label" >Cta. Comisión</label>
                    <div class="col-md-7">
                        <select class="form-control select2_find_cta_comision"  name="cta_comision" id="cta_comision" style="width: 100%">
                               
                        </select>
                        
                    </div>
                </div>
                <!--Profesion Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('profesion') ? ' has-error' : '' }}">
                    <label for="profesion" class="col-md-4 control-label">Profesion</label>
                    <div class="col-md-7">
                    <input id="profesion" type="text" class="form-control"  name="profesion" value="{{old('profesion')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="60" autocomplete="off">
                    @if ($errors->has('profesion'))
                        <span class="help-block">
                            <strong>{{ $errors->first('profesion') }}</strong>
                        </span>
                    @endif
                    </div>
                </div>
                <!--Sexo Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('sexo') ? ' has-error' : '' }}">
                    <label for="sexo" class="col-md-4 control-label">Sexo</label>
                    <div class="col-md-7">
                        <select id="sexo" name="sexo" class="form-control" required autofocus>
                            <option  value="1">MASCULINO</option>
                            <option  value="2">FEMENINO</option>
                        </select>
                        @if ($errors->has('sexo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('sexo') }}</strong>
                            </span>
                        @endif 
                    </div>
                </div>
                <!--Estado Civil Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('estado_civil') ? ' has-error' : '' }}">
                    <label for="estado_civil" class="col-md-4 control-label">Estado Civil</label>
                    <div class="col-md-7">
                        <select id="estado_civil" name="estado_civil" class="form-control" required autofocus>
                            <option  value="1">SOLTERO</option>
                            <option  value="2">CASADO</option>
                            <option  value="3">DIVORCIADO</option>
                        </select>
                        @if ($errors->has('estado_civil'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado_civil') }}</strong>
                            </span>
                        @endif 
                    </div>
                </div>
                <!--Comentario Empleado-->
                <div class="form-group col-xs-6{{ $errors->has('comentario') ? ' has-error' : '' }}">
                    <label for="comentario" class="col-md-4 control-label">Comentario</label>
                    <div class="col-md-7">
                        <textarea class="form-control" rows="2" name="comentario" id="comentario"></textarea>
                        @if ($errors->has('comentario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comentario') }}</strong>
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
                <div class="form-group col-xs-10" style="text-align: center;">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary btn-gray">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>    

<script type="text/javascript">
    
    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });

    });

    $("#id").blur(function(){
        buscarIdentificacion();
    });

    $('.select2_find_cta_comision').select2({
        placeholder: "Seleccione la cuenta",
         allowClear: true,
        ajax: {
            url: '{{route("empleados.find_cta_comision")}}',
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });

    function buscarIdentificacion(){

        $.ajax({
            type: 'post',
            url:"{{route('nomina.identificacion')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'identificacion': $("#id").val(),
             'id_empresa': $("#id_empresa").val()},
            success: function(data){
                console.log(data);
                if(data.existe==null){
                    if(data.usuario!=null){
                        //$('#crear_rol_pago').removeAttr('disabled');
                        $("#btn_add").prop( "disabled", false );
                        $("#nombre1").val(data.usuario.nombre1);
                        $("#nombre2").val(data.usuario.nombre1);
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
                        
                        $("#nombre1").prop( "disabled", true );
                        $("#nombre2").prop( "disabled", true );
                        $("#apellido1").prop( "disabled", true );
                        $("#apellido2").prop( "disabled", true );
                        $("#ciudad").prop( "disabled", true );
                        $("#direccion").prop( "disabled", true );
                        $("#telefono1").prop( "disabled", true );
                        $("#telefono2").prop( "disabled", true );
                        $("#ocupacion").prop( "disabled", true );
                        $("#fecha_nacimiento").prop( "disabled", true );
                        $("#email").prop( "disabled", true );
                        
                        $(".divpass").hide();
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
                    swal("Error!","El Empleado ya se encuentra registrado en esta empresa");
                    $('#btn_add').attr('disabled','disabled');
                }
                
              
               
            },
            error: function(data){
                console.log(data);
            }
        });

    }

</script>

</section>
@endsection
