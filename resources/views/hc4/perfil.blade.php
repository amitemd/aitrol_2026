
<style type="text/css">
    .oculto{
        display: none;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-sm-7">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">@lang('hc4.editar')</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('user-management.update', ['id' => $user->id]) }}">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!--primer nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-xs-8 control-label">@lang('hc4.primerNombre')</label>
                            <input id="nombre1" type="text" class="form-control" name="nombre1" value="{{ $user->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--segundo nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-xs-8 control-label">@lang('hc4.segundoNombre')</label>
                            <input id="nombre2" type="text" class="form-control" name="nombre2" value="{{ $user->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--primer apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-xs-8 control-label">@lang('hc4.primerApellido')</label>
                            <input id="apellido1" type="text" class="form-control" name="apellido1" value="{{ $user->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--segundo apellido-->
                        <div class="form-group col-xs-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-xs-10 control-label">@lang('hc4.segundoApellido')</label>
                            <input id="apellido2" type="text" class="form-control" name="apellido2" value="{{ $user->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--cedula-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-xs-8 control-label">@lang('hc4.cedula')</label>
                            <input id="id" maxlength="10" type="text" class="form-control" name="id" value="{{ $user->id }}" required autofocus onkeyup="validarCedula(this.value);" @if ($rolusuario != 1) readonly="readonly"; @endif >
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--pais-->
                        <div class="form-group col-xs-6{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais" class="col-xs-8 control-label">@lang('hc4.pais')</label>
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
                            <label for="ciudad" class="col-xs-8 control-label">@lang('hc4.ciudad')</label>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $user->ciudad }}" required>
                                @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-xs-8 control-label">@lang('hc4.direccion')</label>
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $user->direccion }}" required>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-xs-10 control-label">@lang('hc4.telefonoDomicilio')</label>
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $user->telefono1 }}" required>
                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-xs-10 control-label">@lang('hc4.telefonoCelular')</label>
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $user->telefono2 }}" required>
                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--ocupacion-->
                        <div class="form-group col-xs-6{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                            <label for="ocupacion" class="col-xs-8 control-label">@lang('hc4.ocupacion')</label>
                            <input id="ocupacion" type="text" class="form-control" name="ocupacion" value="{{ $user->ocupacion }}" required>
                                @if ($errors->has('ocupacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ocupacion') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--fecha_nacimiento-->
                        <div class="form-group col-xs-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-xs-10 control-label">@lang('hc4.fechaNacimiento')</label>
                            <input id="fecha_nacimiento" type="date" class="form-control" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}" @if ($rolusuario != 1) style="display:none" @endif>
                            <label for="id_tipo_usuario" class="col-xs-8 control-label">@lang('hc4.tipoUsuario')</label>
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


                        <!--Color-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }} @if ($rolusuario != 1 || $user->id == $id) oculto @endif" id="cdoctor" style="display: none;">
                            <label for="color" class="col-xs-8 control-label">@lang('hc4.colorAgenda')</label>
                            <div class="col-md-7 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $user->color }}" >
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
                            <label for="email" class="col-xs-8 control-label">@lang('hc4.email')</label>
                            <input id="email" type="text" class="form-control" name="email" value="{{ $user->email }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>


                        <div class="form-group col-xs-6{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-xs-8 control-label">@lang('hc4.nuevoPassword')</label>
                            <input id="password" type="password" class="form-control" name="password">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                        </div>


                        <div class="form-group col-xs-6">
                            <label for="password-confirm" class="col-xs-10 control-label">@lang('hc4.confirmaPassword')</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}" @if ($rolusuario != 1) style="display:none" @endif>
                            <label for="estado" class="col-xs-8 control-label">@lang('hc4.estado')</label>
                            <select id="estado" name="estado" class="form-control">
                            <option {{$user->estado == 0 ? 'selected' : ''}} value="0">@lang('hc4.inactivo')</option>
                            <option {{$user->estado == 1 ? 'selected' : ''}} value="1">@lang('hc4.activo')</option>

                                </select>
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <!--especialidades-->
                        <div id="especialidades" class="col-md-12 oculto" style="display: none;">
                            <div class="form-group col-xs-8">
                                <label  class="col-md-4 control-label">@lang('hc4.especialidades'):</label>
                            </div>
                            @foreach($especialidades as $value)
                            <div class="form-group col-xs-8">
                                <label for="password-confirm" class="col-md-4 control-label">{{$value->nombre}}</label>
                                <div class="col-md-7">
                                    <input name="lista[]" type="checkbox"
                                    @foreach($especialidad as $value2)
                                        @if($value->id == $value2->espid)
                                            checked
                                        @endif
                                    @endforeach
                                     value="{{$value->id}}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                @lang('hc4.actualizar')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--right-->
        <div class="col-sm-5">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">@lang('hc4.cambiarFotografia')</h3></div>
                <form  id="subir_imagen" name="subir_imagen" method="post"  action="{{ route('user-management.subir_imagen_usuario', ['id' => $user->id]) }}" class="formarchivo" enctype="multipart/form-data" >
                    <input type="hidden" name="id_usuario_foto" value="{{$user->id}}">
                    <!--<input type="hidden" name="_method" value="PATCH">-->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group col-xs-12" >
                            <input type="hidden" name="carga" value="@if($user->imagen_url==' ')
                               {{$user->imagen_url='../avatars/avatar.jpg'}}
                            @endif">
                            <img src="{{asset('/avatars').'/'.$user->imagen_url}}"  alt="User Image"  style="width:160px;height:160px;" id="fotografia_usuario" >
                            <!-- User image -->
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label>@lang('hc4.agregarImagen')</label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control"  required/><br /><br />
                            @if ($errors->has('archivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">@lang('hc4.actualizarImagen')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-sm-5">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">@lang('hc4.rolesPago')</h3></div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <th style="text-align: center;">@lang('hc4.nombres')</th>
                      <th style="text-align: center;">@lang('hc4.cedula')</th>
                      <th style="text-align: center;">@lang('hc4.anio')</th>
                      <th style="text-align: center;">@lang('hc4.mes')</th>
                      <th style="text-align: center;">@lang('hc4.sueldo')</th>
                      <th style="text-align: center;">@lang('hc4.totalingresos')</th>
                      <th style="text-align: center;">@lang('hc4.totalegresos')</th>
                      <th style="text-align: center;">@lang('hc4.netorecibido')</th>
                      <th style="text-align: center;">@lang('hc4.accion')</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($rol_pag as $rol)
                      <tr>
                        @php

                        $empresa =Sis_medico\Empresa::find($rol->id_empresa);
                        $detalle_rol = Sis_medico\Ct_Detalle_Rol::where('id_rol',$rol->id)->first();
                        @endphp
                        <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                        <td>{{$rol->id_user}}</td>
                        <td>{{$rol->anio}}</td>
                        <td>
                          @if($rol->mes == '1') @lang('hc4.enero')
                            @elseif($rol->mes == '2') @lang('hc4.febrero')
                            @elseif($rol->mes == '3') @lang('hc4.marzo')
                            @elseif($rol->mes == '4') @lang('hc4.abril')
                            @elseif($rol->mes == '5') @lang('hc4.mayo')
                            @elseif($rol->mes == '6') @lang('hc4.junio')
                            @elseif($rol->mes == '7') @lang('hc4.julio')
                            @elseif($rol->mes == '8') @lang('hc4.agosto')
                            @elseif($rol->mes == '9') @lang('hc4.septiembre')
                            @elseif($rol->mes == '10') @lang('hc4.octubre')
                            @elseif($rol->mes == '11') @lang('hc4.noviembre')
                            @elseif($rol->mes == '12') @lang('hc4.diciembre')
                            @endif
                        </td>
                        <td>{{$detalle_rol->sueldo_mensual}}</td>
                        <td>{{$detalle_rol->total_ingresos}}</td>
                        <td>{{$detalle_rol->total_egresos}}</td>
                        <td>{{$detalle_rol->neto_recibido}}</td>
                        <td>@if($rol->estado == '1') Activo @elseif($rol->estado =='0') Anulada @else Activo @endif</td>
                        <td>
                          @if($rol->estado == '1')
                          <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function() {

        var js_tipo_usu = document.getElementById("id_tipo_usuario").value;

            /*if(fecha_cita <= fecha_actual)
            {
                $("#enviar").attr("disabled","disabled");
            }*/
            @foreach ($tipousuarios as $value)
                @if($value->nombre != 'DOCTORES')
                    if(js_tipo_usu=={{$value->id}}){
                        $("#especialidades").addClass("oculto");
                        $("#cdoctor").addClass("oculto");
                    }
                @endif
                @if($value->nombre == 'DOCTORES')
                    if(js_tipo_usu=={{$value->id}}){
                        $("#especialidades").removeClass("oculto");
                        $("#cdoctor").removeClass("oculto");
                    }
                @endif
            @endforeach

        $("#id_tipo_usuario").change(function () {

            //var valor = 0;
            var js_tipo_usu = document.getElementById("id_tipo_usuario").value;

            /*if(fecha_cita <= fecha_actual)
            {
                $("#enviar").attr("disabled","disabled");
            }*/
            @foreach ($tipousuarios as $value)
                @if($value->nombre != 'DOCTORES')
                    if(js_tipo_usu=={{$value->id}}){
                        $("#especialidades").addClass("oculto");
                        $("#cdoctor").addClass("oculto");
                    }
                @endif
                @if($value->nombre == 'DOCTORES')
                    if(js_tipo_usu=={{$value->id}}){
                        $("#especialidades").removeClass("oculto");
                        $("#cdoctor").removeClass("oculto");
                    }
                @endif
            @endforeach

        });


    });

</script>
