@extends('empresa.base')

@section('action-content')
<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-sm-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Editar Empresa</h3>
                </div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('empresa.update', ['id' => $empresa->id]) }}">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-xs-6 control-label">RUC</label>
                            <input id="id" type="text" class="form-control" name="id" value="{{ $empresa->id }}" required autofocus>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--Razón Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-xs-6 control-label">Razón Social</label>
                            <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ $empresa->razonsocial }}" required autofocus>
                            @if ($errors->has('razonsocial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('razonsocial') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-xs-8 control-label">Nombre Comercial</label>
                            <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ $empresa->nombrecomercial }}" required autofocus>
                            @if ($errors->has('nombrecomercial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombrecomercial') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-xs-6 control-label">Ciudad</label>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $empresa->ciudad }}" required>
                            @if ($errors->has('ciudad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('ciudad') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-xs-6 control-label">Dirección</label>
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $empresa->direccion }}" required>
                            @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--email-->
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-xs-6 control-label">E-mail</label>
                            <input id="email" type="text" class="form-control" name="email" value="{{ $empresa->email }}" required>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-xs-8 control-label">Telefono Domicilio</label>
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $empresa->telefono1 }}" required>
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-xs-6 control-label">Telefono Celular</label>
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $empresa->telefono2 }}" required>
                            @if ($errors->has('telefono2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono2') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-xs-6 control-label">Estado</label>
                            <select id="estado" name="estado" class="form-control">
                                <option {{$empresa->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$empresa->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>

                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('admision') ? ' has-error' : '' }}">
                            <label for="admision" class="col-xs-6 control-label">Admision</label>
                            <select id="admision" name="admision" class="form-control">
                                <option {{$empresa->admision == 0 ? 'selected' : ''}} value="0">NO</option>
                                <option {{$empresa->admision == 1 ? 'selected' : ''}} value="1">SI</option>

                            </select>
                            @if ($errors->has('admision'))
                            <span class="help-block">
                                <strong>{{ $errors->first('admision') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('prioridad') ? ' has-error' : '' }}">
                            <label for="prioridad" class="col-xs-6 control-label">Prioridad</label>
                            <select id="prioridad" name="prioridad" class="form-control">
                                <option {{$empresa->prioridad == 0 ? 'selected' : ''}} value="0">NO</option>
                                <option {{$empresa->prioridad == 1 ? 'selected' : ''}} value="1">SI</option>

                            </select>
                            @if ($errors->has('prioridad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('prioridad') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('electronica') ? ' has-error' : '' }}">
                            <label for="electronica" class="col-xs-6 control-label">Electronica</label>
                            <select id="electronica" name="electronica" class="form-control">
                                <option {{$empresa->electronica == 0 ?  'selected' : ''}} value="0">NO</option>
                                <option {{$empresa->electronica == 1 ?  'selected' : ''}} value="1">SI</option>


                            </select>
                            @if ($errors->has('electronica'))
                            <span class="help-block">
                                <strong>{{ $errors->first('electronica') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div id="appid1" class="form-group col-xs-6{{ $errors->has('appid') ? ' has-error' : '' }}">
                            <label for="appid" class="col-xs-6 control-label">App ID</label>
                            <input id="appid" type="text" class="form-control" name="appid" value="{{ $empresa->appid }} " requiered>
                            @if ($errors->has('appid'))
                            <span class="help-block">
                                <strong>{{ $errors->first('appid') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="appsecret1" class="form-group col-xs-6{{ $errors->has('appsecret') ? ' has-error' : '' }}">
                            <label for="appsecret" class="col-xs-6 control-label">App Secret</label>
                            <input id="appsecret" type="text" class="form-control" name="appsecret" value="{{ $empresa->appsecret }}" requiered>
                            @if ($errors->has('appsecret'))
                            <span class="help-block">
                                <strong>{{ $errors->first('appsecret') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="url1" class="form-group col-xs-6{{ $errors->has('url') ? ' has-error' : '' }}">
                            <label for="url" class="col-xs-6 control-label">URL</label>
                            <input id="url" type="text" class="form-control" name="url" value="{{ $empresa->url }}" requiered>
                            @if ($errors->has('url'))
                            <span class="help-block">
                                <strong>{{ $errors->first('url') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div id="establecimiento1" class="form-group col-xs-6{{ $errors->has('establecimiento') ? ' has-error' : '' }}">
                            <label for="establecimiento" class="col-xs-6 control-label">Establecimiento</label>
                            <input id="establecimiento" type="text" class="form-control" name="establecimiento" value="{{ $empresa->establecimiento }}" requiered>
                            @if ($errors->has('establecimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('establecimiento') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="punto_emision1" class="form-group col-xs-6{{ $errors->has('punto_emision') ? ' has-error' : '' }}">
                            <label for="punto_emision" class="col-xs-6 control-label">Punto de emision</label>
                            <input id="punto_emision" type="text" class="form-control" name="punto_emision" value="{{ $empresa->punto_emision }}" requiered>
                            @if ($errors->has('punto_emision'))
                            <span class="help-block">
                                <strong>{{ $errors->first('punto_emision') }}</strong>
                            </span>
                            @endif
                        </div>



                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--right-->
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Cambiar Logo</h3>
                </div>
                <form id="subir_imagen" name="subir_imagen" method="post" action="{{ route('empresa.subir_logo', ['id' => $empresa->id]) }}" class="formarchivo" enctype="multipart/form-data">
                    <input type="hidden" name="logo" value="{{$empresa->id}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group col-xs-12">
                            <input type="hidden" name="carga" value="@if($empresa->logo=='') {{$empresa->logo='../logo/avatar.jpg'}} @endif">
                            <img src="../../logo/{{$empresa->logo}}" alt="Logo Image" style="width:160px;height:160px;" id="logo_empresa">
                            <!-- User image -->
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label for="archivo">Agregar Logo </label>
                            <input name="archivo" id="archivo" type="file" class="archivo form-control" required /><br /><br />
                            @if ($errors->has('archivo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('archivo') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Actualizar Logo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var estado = $("#electronica").val();
        if (estado == 0) {

            $('#appid1').addClass("oculto");
            $('#appsecret1').addClass("oculto");
            $('#url1').addClass("oculto");
            $('#establecimiento1').addClass("oculto");
            $('#punto_emision1').addClass("oculto");

            $('#appid').removeAttr("required");
            $('#appsecret').removeAttr("required");
            $('#url').removeAttr("required");
            $('#establecimiento').removeAttr("required");
            $('#punto_emision').removeAttr("required");
        }

        $("#electronica").change(function() {

            var estado = $("#electronica").val();

            if (estado == 0) {
                $('#appid1').addClass("oculto");
                $('#appsecret1').addClass("oculto");
                $('#url1').addClass("oculto");
                $('#establecimiento1').addClass("oculto");
                $('#punto_emision1').addClass("oculto");

                $('#appid').removeAttr("required");
                $('#appsecret').removeAttr("required");
                $('#url').removeAttr("required");
                $('#establecimiento').removeAttr("required");
                $('#punto_emision').removeAttr("required");
            }

            if (estado == 1) {
                $('#appid1').removeClass("oculto");
                $('#appsecret1').removeClass("oculto");
                $('#url1').removeClass("oculto");
                $('#establecimiento1').removeClass("oculto");
                $('#punto_emision1').removeClass("oculto");

                $('#appid').attr("required", true);
                $('#appsecret').attr("required", true);
                $('#url').attr("required", true);
                $('#establecimiento').attr("required", true);
                $('#punto_emision').attr("required", true);


            }

        });
    });
</script>

@endsection