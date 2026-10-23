@extends('contable.acreedores.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24">
            <div class="box ">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">Editar Proveedor</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm btn-gray">
                            <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>
                </div>
              
                <form class="form-vertical" role="form" method="GET" action="{{ route('acreedores.update', ['id' => $proveedor->id]) }}">
                  
                    {{ csrf_field() }}
                    <div class="box-body dobra col-xs-24">
                        <!--RUC-->
                        <div class="form-group col-xs-8{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="tipo_identificacion" class="col-md-2 texto" style="color: blue">Identificacion</label>
                            <div class="col-md-3">
                                <select id="tipo_identificacion" name="tipo_identificacion" onchange="borrar()" class="form-control" required autofocus>
                                    <option value="">Seleccione...</option>
                                    <option {{$proveedor->tipo == 4 ? 'selected' : ''}} value="4">{{trans('contableM.ruc')}}</option>
                                    <option {{$proveedor->tipo == 5 ? 'selected' : ''}} value="5">{{trans('contableM.cedula')}}</option>
                                    <option {{$proveedor->tipo == 6 ? 'selected' : ''}} value="6">{{trans('contableM.pasaporte')}}</option>
                                    <option {{$proveedor->tipo == 8 ? 'selected' : ''}} value="8">CEDULA/RUC EXTRANJERO</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input id="id" type="text" maxlength="13" class="form-control" name="id" value="{{ $proveedor->id }}" onchange="return tipo_id(this);" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autocomplete="off" autofocus>
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Razón Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-md-4 control-label">Razón Social</label>
                            <div class="col-md-7">
                                <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ $proveedor->razonsocial }}" required autofocus>
                                @if ($errors->has('razonsocial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('razonsocial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-md-4 control-label">Nombre Comercial</label>
                            <div class="col-md-7">
                                <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ $proveedor->nombrecomercial }}" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-md-4 control-label">Ciudad</label>
                            <div class="col-md-7">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $proveedor->ciudad }}" required>
                                @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Dirección</label>
                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $proveedor->direccion }}" required>
                                @if ($errors->has('direccion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('direccion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--email-->
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-mail</label>
                            <div class="col-md-7">
                                <input id="email" type="text" class="form-control" name="email" value="{{ $proveedor->email }}" required>
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('email2') ? ' has-error' : '' }}">
                            <label for="email2" class="col-md-4 control-label">E-Mail 2</label>
                            <div class="col-md-7">
                                <input id="email2" type="email" class="form-control" name="email2" value="{{ $proveedor->email2 }}" autocomplete="off">
                                @if ($errors->has('email2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email2') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>
                            <div class="col-md-7">
                                <input id="telefono1" type="number" class="form-control" name="telefono1" value="{{ $proveedor->telefono1 }}" required autofocus>
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
                                <input id="telefono2" type="number" class="form-control" name="telefono2" value="{{ $proveedor->telefono2 }}" required autofocus>
                                @if ($errors->has('telefono2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--estado-->
                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-7">
                                <select id="estado" name="estado" class="form-control">
                                    <option {{$proveedor->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                    <option {{$proveedor->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                                </select>
                                @if ($errors->has('estado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_tipo_proveedor') ? ' has-error' : '' }}">
                            <label for="id_tipo_proveedor" class="col-md-4 control-label">Tipo Proveedor</label>
                            <div class="col-md-7">
                                <select id="id_tipo_proveedor" name="id_tipo_proveedor" class="form-control" required="required">
                                    <option value="">Seleccione..</option>
                                    @foreach($tipos as $value)
                                    @if ($value->estado != 0)
                                    <option {{$proveedor->id_tipoproveedor == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('id_tipo_proveedor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_tipo_proveedor') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="acreedores" class="col-md-4 control-label">Grupo:</label>
                            <div class="col-md-7">
                                <select id="acreedores" style="width:100%" onchange="grupos_acreedores()" name="acreedores" class="bcn" required>
                                    <option value=""></option>
                                    @foreach($id_padre as $value)
                                    <option value="{{$value->id_plan}}"  @if($value->id_plan == $proveedor->id_grupo) selected @endif >{{$value->plan}} | {{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="id_cuentas" id="id_cuentas" value="{{$proveedor->id_cuentas}}">
                        <div class="form-group col-xs-6{{ $errors->has('lista_contable') ? ' has-error' : '' }}">
                            <label for="lista_contable" class="col-md-4 control-label">Cuenta Contable</label>
                            <div class="col-md-7">
                                <select id="lista_contable" name="lista_contable" class="form-control" required>
                                   
                                </select>
                                @if ($errors->has('lista_contable'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('lista_contable') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group col-xs-6">
                            <label class="control-label col-md-4" for="retencion_iva"> % RET IVA</label>
                            <div class="col-md-7">
                                <select class="form-control col-md-4" name="retencion_iva" id="retencion_iva" required>
                                    <option value="0">Seleccione...</option>
                                    @foreach($retenciones as $value)
                                    <option {{$proveedor->id_porcentaje_iva == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="form-group col-xs-6">
                            <label class="control-label col-md-4" for="retencion_iva"> % RET FUENTE</label>
                            <div class="col-md-7">
                                <select class="form-control col-md-4" name="retencion_ft" id="retencion_ft" required>
                                    <option value="0">Seleccione...</option>
                                    @foreach($retencioner as $value)
                                    <option {{$proveedor->id_porcentaje_ft == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('serie') ? ' has-error' : '' }}">
                            <label for="serie" class="col-md-4 control-label">Serie</label>
                            <div class="col-md-7">
                                <input id="serie" type="text" class="form-control" style="width: 100%;" name="serie" maxlength="7" value="{{ $proveedor->serie }}" autofocus>
                                @if ($errors->has('serie'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('serie') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('autorizacion') ? ' has-error' : '' }}">
                            <label for="autorizacion" class="col-md-4 control-label">Autorización</label>
                            <div class="col-md-7">
                                <input id="autorizacion" type="text" class="form-control" style="width: 100%;" name="autorizacion" value="{{ $proveedor->autorizacion }}" autofocus>
                                @if ($errors->has('autorizacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('autorizacion') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group col-xs-12">
                            <label class="col-md-4 control-label">Informacion de transferencia</label>
                        </div>
                        <div class="form-group col-xs-6">
                            <label class="col-md-4 control-label" for="banco_c"> Banco</label>
                            <div class="col-md-7">
                                <select style="width:100%" class="bcn" name="banco_c" id="banco_c">
                                    <option value=" ">Seleccione....</option>
                                    @foreach($id_configuracion as $value)
                                    <option {{$proveedor->id_configuracion == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6">

                            <label class="col-md-4 control-label" for="tipo_cuenta"> Tipo Cuenta:</label>
                            <div class="col-md-7">
                                <select style="width:100%" class="bcn" name="tipo_cuenta" id="tipo_cuenta">
                                    <option value=" ">Seleccione...</option>
                                    @foreach($tipo_cuenta as $value)
                                    <option {{$proveedor->tipo_cuenta == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->tipo_cuenta}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                            <label class="col-md-4 control-label" for="cuenta"> N° Cuenta:</label>
                            <div class="col-md-7">
                                <input id="cuenta" type="text" style="width: 100%;" class="form-control" name="cuenta" value="{{ $proveedor->cuenta}}" autofocus>
                                @if ($errors->has('cuenta'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cuenta') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6">

                            <label class="col-md-4 control-label" for="identificacion"> Identificacion:</label>
                            <div class="col-md-7">
                                <input id="identificacion" type="text" style="width: 100%;" class="form-control" name="identificacion" value="{{ $proveedor->identificacion }}" autofocus>
                                @if ($errors->has('identificacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('identificacion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                            <label class="col-md-4 control-label" for="beneficiario">Beneficiario: </label>
                            <div class="col-md-7">
                                <input id="beneficiario" type="text" style="width: 100%;" class="form-control" name="beneficiario" value="{{ $proveedor->beneficiario }}">
                                @if ($errors->has('beneficiario'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('beneficiario') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group col-xs-6">
                            <label style="text-align: center;" class="col-md-12">Agregar Detalle</label>
                            <div class="table table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Serie</th>
                                            <th>Autorizacion</th>
                                            <th>Secuencia Ini</th>
                                            <th>Secuencia Fin</th>
                                            <th>Fecha de Caducidad</th>
                                            <th> <button onclick="crea_td()" type="button" class="btn btn-success btn-gray">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button></th>
                                        </tr>

                                    </thead>
                                    <tbody id="agregar">
                                        @foreach($detalles as $zx)
                                        <tr>
                                            <td> <input type="hidden" name="validate[]" value="{{$zx->id}}"><input type="text" class="form-control" name="seried[]" onkeyup="obtenerserie(this)" value="{{$zx->serie}}" onchange="series(this)" placeholder="Serie..."></td>
                                            <td><input type="text" class="form-control" name="autod[]" value="{{$zx->autorizacion}}" onchange="autorizaciones(this)" placeholder="Autorizacion..."></td>
                                            <td><input type="text" class="form-control" name="secuenciaini[]" onchange="obtenersecuencia(this)" value="{{$zx->sinicia}}" placeholder="Secuencia Inicial"></td>
                                            <td><input type="text" class="form-control" name="secuenciafin[]" onchange="obtenersecuencia(this)" value="{{$zx->sfin}}" placeholder="Secuencia Final"></td>
                                            <td><input type="date" class="form-control" name="f_caducidad[]" value="{{$zx->f_caducidad}}" placeholder="Fecha Caducidad"></td>
                                            <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                        </tr>
                                        @endforeach
                                        <tr id="mifila" style="display: none;">
                                            <td> <input type="hidden" name="validate[]" value="-1"> <input type="text" onchange="series(this)" class="form-control" name="seried[]" onkeyup="obtenerserie(this)" placeholder="Serie..."></td>
                                            <td><input type="text" class="form-control" name="autod[]" onchange="autorizaciones(this)" placeholder="Autorizacion..."></td>
                                            <td><input type="text" class="form-control" name="secuenciaini[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Inicial"></td>
                                            <td><input type="text" class="form-control" name="secuenciafin[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Final"></td>
                                            <td><input type="date" class="form-control" name="f_caducidad[]" placeholder="Fecha Caducidad"></td>
                                            <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box dobra">
                                <div class="box-header">
                                    <h3 class="box-title">Subir Logo</h3>
                                </div>
                                <form id="subir_imagen" name="subir_imagen" method="post" action="{{ route('acreedores_subir_logo', ['id' => $proveedor->id]) }}" class="formarchivo" enctype="multipart/form-data">
                                    <input type="hidden" name="logo" value="{{$proveedor->id}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="box-body">
                                        <div class="form-group col-xs-12">
                                            <input type="hidden" name="carga" value="@if($proveedor->logo=='') {{$proveedor->logo='../logo/avatar.jpg'}} @endif">
                                            <img src="{{asset('/logo').'/'.$proveedor->logo}}" alt="Logo Image" style="width:160px;height:160px;margin-top:15px;box-shadow:3px 3px 2px grey" id="logo_empresa">
                                            <!-- User image -->
                                        </div>
                                        <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                                            <label for="archivo">Agregar Logo </label>
                                            <input name="archivo" id="archivo" type="file" class="archivo form-control"  /><br /><br />
                                            @if ($errors->has('archivo'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('archivo') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-primary btn-gray">Actualizar Logo</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-gray">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

    $(document).ready(function() {
        grupos_acreedores()
        //grupos_acreedores()
    });
    $(document).ready(function() {
        $('.bcn').select2();
    });

    function tipo_id(elemento) {
        var tipo = $('#tipo_identificacion').val();
        var numero = parseInt(elemento.value, 10);
        if (tipo == '4') {

        }
        if (tipo == '5') {
            if (numero < 111111111 || numero > 9999999999) {
                alert("Número de cedula Incorrecto");
                $('#id').val('');
            }
        }
    }

    function borrar() {

    }

    function autorizaciones(e) {
        var x = $(e).val();
        //alert(x);
        $("#autorizacion").val(x);
    }

    function grupos_acreedores() {
        //if (isNaN(valor)) {
            let valor = $("#acreedores").val();
      //  }
      let id_cuentas     = document.getElementById("id_cuentas").value;
        console.log(valor);
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('proveedor.query_cuentas')}}",
            headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val()},
            datatype: 'json',
            data: { 
                'opcion': valor
            },
            success: function(data) {
                console.log(data);
                //alert(data[0].nombre);
                if (data.value != 'no') {
                    if (valor != 0) {
                        $("#lista_contable").empty();
                        $.each(data, function(key, registro) {
                            $("#lista_contable").append(`<option value='${registro.id}' ${registro.id  == id_cuentas ? 'selected' : ''}> ${registro.nombre} </option>`);
                        });
                    } else {
                        $("#lista_contable").empty();
                    }

                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    var fila = $("#mifila").html();

    function crea_td() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }

    function eliminar(a) {
        //alert("dada");
        $(a).parent().parent().remove();
    }

    function obtenerserie(e) {
        serie = $(e).val();
        if ((serie.length) == 3) {
            $(e).val(serie + '-');

        } else if ((serie.length) > 7) {
            $(e).val('');
            swal("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
    }

    function series(e) {
        var s = $(e).val();
        $("#serie").val(s);
    }

    function obtenersecuencia(e) {
        var digitos = 9;
        var ceros = 0;
        var varos = '0';
        var secuencia = 0;
        if ($(e).val() > 0) {
            var longitud = parseInt($(e).val().length);
            if (longitud > 10) {
                swal("Error!", "Valor no permitido", "error");
                $(e).val('');

            } else {
                var concadenate = parseInt(digitos - longitud);
                switch (longitud) {
                    case 1:
                        secuencia = '000000000';
                        break;
                    case 2:
                        secuencia = '00000000';
                        break;
                    case 3:
                        secuencia = '0000000';
                        break;
                    case 4:
                        secuencia = '000000';
                        break;
                    case 5:
                        secuencia = '00000';
                        break;
                    case 6:
                        secuencia = '0000';
                        break;
                    case 7:
                        secuencia = '000';
                        break;
                    case 8:
                        secuencia = '00';
                        break;
                    case 9:
                        secuencia = '0';
                }
                $(e).val(secuencia + $(e).val());
            }


        } else {
            swal("Error!", "Valor no permitido", "error");
            $(e).val('');
        }
    }
</script>
@endsection