<div class="modal-content" style="overflow-y: scroll;">
    <div class="modal-header">
        <input type="hidden">
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">{{ trans('contableM.registrar_proveedor')}} </h3>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-md-12 col-xs-24">
                <div class="box ">
                    <div class="box-header">
                        <div class="col-md-9">
                            <h3 class="box-title">{{trans('contableM.agregarnuevoproveedor')}}</h3>
                        </div>
                    </div>
                    <form class="form-vertical" role="form" method="POST" id="formularioNuevo">
                        {{ csrf_field() }}
                        <div class="box-body dobra col-xs-24">
                            <!--RUC-->
                            <div class="form-group col-xs-8 {{ $errors->has('id') ? ' has-error' : '' }}">
                                <label for="tipo_identificacion" class="col-md-2 texto" style="color: blue">{{trans('contableM.identificacion')}}</label>
                                <div class="col-md-3">
                                    <select id="tipo_identificacion" name="tipo_identificacion" class="form-control" required autofocus>
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        <option value="4">{{trans('contableM.ruc')}}</option>
                                        <option value="5">{{trans('contableM.cedula')}}</option>
                                        <option value="6">{{trans('contableM.pasaporte')}}</option>
                                        <option value="8">CEDULA/RUC EXTRANJERO</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input id="id" type="text" maxlength="13" class="form-control" name="id" value="{{$ruc}}" onchange="return tipo_id(this);" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                                    @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--Razon Social-->
                            <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                                <label for="razonsocial" class="col-md-4 control-label">{{trans('contableM.razonsocial')}}</label>
                                <div class="col-md-7">
                                    <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{$razonSocial}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                    @if ($errors->has('razonsocial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('razonsocial') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--Nombre Comercial-->
                            <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                                <label for="nombrecomercial" class="col-md-4 control-label">{{trans('contableM.nombrecomercial')}}</label>
                                <div class="col-md-7">
                                    <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                    @if ($errors->has('nombrecomercial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--Ciudad-->
                            <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                                <label for="ciudad" class="col-md-4 control-label">{{trans('contableM.ciudad')}}</label>
                                <div class="col-md-7">
                                    <input id="ciudad" type="text" class="form-control" name="ciudad" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off">
                                    @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--Direccion-->
                            <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <label for="direccion" class="col-md-4 control-label">{{trans('contableM.direccion')}}</label>
                                <div class="col-md-7">
                                    <input id="direccion" type="text" class="form-control" name="direccion" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off">
                                    @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--email-->
                            <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{trans('contableM.email')}}</label>
                                <div class="col-md-7">
                                    <input id="email" type="email" class="form-control" name="email" value="" required autocomplete="off">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6{{ $errors->has('email2') ? ' has-error' : '' }}">
                                <label for="email2" class="col-md-4 control-label">{{trans('contableM.email')}} 2</label>
                                <div class="col-md-7">
                                    <input id="email2" type="email" class="form-control" name="email2" value="" autocomplete="off">
                                    @if ($errors->has('email2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--telefono1-->
                            <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                                <label for="telefono1" class="col-md-4 control-label">{{trans('contableM.telefonodomicilio')}}</label>
                                <div class="col-md-7">
                                    <input id="telefono1" type="number" class="form-control" name="telefono1" value="" required autofocus>
                                    @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--telefono2-->
                            <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                                <label for="telefono2" class="col-md-4 control-label">{{trans('contableM.telefonocelular')}}</label>
                                <div class="col-md-7">
                                    <input id="telefono2" type="number" class="form-control" name="telefono2" value="" required autofocus>
                                    @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!--id_tipo_usuario-->
                            <div class="form-group col-xs-6{{ $errors->has('id_tipo_proveedor') ? ' has-error' : '' }}">
                                <label for="id_tipo_proveedor" class="col-md-4 control-label">{{trans('contableM.tipoproveedor')}}</label>
                                <div class="col-md-7">
                                    <select id="id_tipo_proveedor" name="id_tipo_proveedor" class="form-control" required="required">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        <?php
                                        foreach ($tipos as $value) {
                                            if ($value->estado != 0) {
                                                $select = '';
                                                if (isset($id)) {
                                                    if ($value->id == $proveedor->id_tipoproveedor)
                                                        $select = 'selected';
                                                }
                                                echo '<option value="' . $value->id . '" ' . $select . '>' . $value->nombre . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    @if ($errors->has('id_tipo_proveedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipo_proveedor') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label for="id_grupo" class="col-md-4 control-label">{{trans('contableM.grupo')}}:</label>
                                <div class="col-md-7">
                                    <select id="id_grupo" onchange="grupos_acreedores()" name="id_grupo" class="form-control col-md-8 " required>
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($id_padre as $value)
                                        <option value="{{$value->id}}">{{$value->plan}} - {{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6{{ $errors->has('lista_contable') ? ' has-error' : '' }}">
                                <label for="lista_contable" class="col-md-4 control-label">{{trans('contableM.cuentacontable')}}</label>
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
                                <label class="control-label col-md-4" for="retencion_iva"> {{trans('contableM.retiva')}}</label>
                                <div class="col-md-7">
                                    <select class="form-control col-md-4" name="retencion_iva" id="retencion_iva">
                                        <option value="0">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($retenciones as $value)
                                        <option {{ isset($id)?($proveedor->id_porcentaje_iva == $value->id ? 'selected' : ''):'' }} value="{{ $value->id }}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="control-label col-md-4" for="retencion_ft"> {{trans('contableM.retrenta')}}</label>
                                <div class="col-md-7">
                                    <select class="form-control" name="retencion_ft" id="retencion_ft">
                                        <option value="0">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($retencioner as $value)
                                        <option {{ isset($id)?($proveedor->id_porcentaje_ft == $value->id ? 'selected' : ''):'' }} value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-12">
                                <label class="col-md-4 control-label">{{trans('contableM.informaciontransferencia')}}</label>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="col-md-4 control-label"> {{trans('contableM.banco')}}: </label>
                                <div class="col-md-7">
                                    <select class="bcn" style="width:100%" name="banco" id="banco_c">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($id_configuracion as $value)
                                        <option {{ isset($id)?($proveedor->id_configuracion == $value->id ? 'selected' : ''):''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="col-md-4 control-label"> {{trans('contableM.nuncuenta')}}: </label>
                                <div class="col-md-7">
                                    <input id="cuenta" type="text" name="cuenta_banco" class="form-control" name="cuenta">
                                    @if ($errors->has('cuenta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cuenta') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="col-md-4 control-label"> {{trans('contableM.tipocuenta')}}: </label>
                                <div class="col-md-7">
                                    <select class="bcn" style="width:100%" name="tipo_cuenta" id="tipo_cuenta">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($tipo_cuenta as $value)
                                        <option {{ isset($id)?($proveedor->tipo_cuenta == $value->id ? 'selected' : ''):''}} value="{{$value->id}}">{{$value->tipo_cuenta}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="col-md-4 control-label"> {{trans('contableM.identificacion')}}: </label>
                                <div class="col-md-7">
                                    <input id="identificacion" type="identificacion" class="form-control" name="identificacion" value="{{ isset($proveedorTemp)?$proveedorTemp['id']:old('identificacion') }}">
                                    @if ($errors->has('identificacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('identificacion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label class="col-md-4 control-label">{{trans('contableM.beneficiario')}}: </label>
                                <div class="col-md-7">
                                    <input id="beneficiario" type="beneficiario" class="form-control" name="beneficiario" value="">
                                    @if ($errors->has('beneficiario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('beneficiario') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-6">
                                <label style="text-align: center;" class="col-md-12">{{trans('contableM.agregardetalles')}}</label>
                                <div class="table table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{trans('contableM.serie')}}</th>
                                                <th>{{trans('contableM.autorizacion')}}</th>
                                                <th>{{trans('contableM.secuenciaini')}}</th>
                                                <th>{{trans('contableM.secuenciafin')}}</th>
                                                <th>{{trans('contableM.fechacaducidad')}}</th>
                                                <th> <button onclick="crea_td()" type="button" class="btn btn-success btn-gray">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button></th>
                                            </tr>
                                        </thead>
                                        <tbody id="agregar">
                                            @if(isset($id))

                                            @else
                                            <tr>
                                                <td><input type="text" class="form-control" name="seried[]" onkeyup="obtenerserie(this)" onchange="series(this)" placeholder="Serie..."></td>
                                                <td><input type="text" class="form-control" name="autod[]" placeholder="Autorizacion..." onchange="autorizaciones(this)"></td>
                                                <td><input type="text" class="form-control" name="secuenciaini[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Inicial"></td>
                                                <td><input type="text" class="form-control" name="secuenciafin[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Final"></td>
                                                <td><input type="date" class="form-control" name="f_caducidad[]" placeholder="Fecha Caducidad"></td>
                                                <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                            </tr>
                                            @endif
                                            <tr id="mifila" style="display: none;">
                                                <td><input type="text" class="form-control" name="seried[]" onkeyup="obtenerserie(this)" onchange="series(this)" placeholder="Serie..."></td>
                                                <td><input type="text" class="form-control" name="autod[]" placeholder="Autorizacion..." onchange="autorizaciones(this)"></td>
                                                <td><input type="text" class="form-control" name="secuenciaini[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Inicial"></td>
                                                <td><input type="text" class="form-control" name="secuenciafin[]" onchange="obtenersecuencia(this)" placeholder="Secuencia Final"></td>
                                                <td><input type="date" class="form-control" name="f_caducidad[]" placeholder="Fecha Caducidad"></td>
                                                <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="button" onclick="guardar()" class="btn btn-gray btn-primary">{{trans('contableM.agregar')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.bcn').select2();
        <?php
        if (isset($proveedores)) {
        ?>
            var valor = $("#id_grupo").val();
            $.ajax({
                type: 'post',
                url: "{{route('proveedor.query_cuentas')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'opcion': valor
                },
                success: function(data) {
                    if (data.value != 'no') {
                        if (valor != 0) {
                            $("#lista_contable").empty();
                            $.each(data, function(key, registro) {
                                if (registro.id == '<?= $proveedor->id ;?>')
                                    $("#lista_contable").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                            });
                        } else {
                            $("#lista_contable").empty();
                        }
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        <?php
        }
        ?>
    });
    $('.select2_cuentas').select2({
        tags: false
    });

    function validarRuc(val) {
        ruc = document.getElementById('id').value;
        /* $proveedor_id = \Sis_medico\Proveedor::where('id', $request['id'])->first();
         dd($proveedor_id);*/
        /* Verifico que el campo no contenga letras */
        var ok = 1;
        for (i = 0; i < ruc.length && ok == 1; i++) {
            var n = parseInt(ruc.charAt(i));
            if (isNaN(n)) ok = 0;
        }
        if (ok == 0) {
            alert("No puede ingresar caracteres en el número");
            return false;
        }
        if (ruc.length < 10 || ruc.length >= 14) {
            alert('El número ingresado no es válido');
            document.getElementById('id').value = "";
            return true;
        }
    }

    function autorizaciones(e) {
        var x = $(e).val();
        $("#autorizacion").val(x);
    }

    function tipo_id(elemento) {
        var tipo = $('#tipo_identificacion').val();
        var numero = parseInt(elemento.value, 10);
        if (tipo == '4') {
            if (numero < 111111111111 || numero > 9999999999999) {
                alert("Número de RUC Incorrecto");
                $('#id').val('');
            }
        }
        if (tipo == '5') {
            if (numero < 111111111 || numero > 9999999999) {
                alert("Número de cedula Incorrecto");
                $('#id').val('');
            }
        }
    }

    function borrar() {
        $('#id').val('');
    }

    function grupos_acreedores() {
        var valor = $("#id_grupo").val();
        $.ajax({
            type: 'post',
            url: "{{route('proveedor.query_cuentas')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': valor
            },
            success: function(data) {
                if (data.value != 'no') {
                    if (valor != 0) {
                        $("#lista_contable").empty();
                        $.each(data, function(key, registro) {
                            $("#lista_contable").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                        });
                    } else {
                        $("#lista_contable").empty();
                    }
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function validar_ruc(id) {
        $.ajax({
            type: 'post',
            url: "{{route('proveedor.validar_ruc')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                data = JSON.parse(data);
                switch (data.rs) {
                    case 1:
                        $("#id").next().remove();
                        break;
                    case 0:
                        $("#id").val('');
                        $("#id").next().remove();
                        $("#id").after('<span class="validationMessage" style="color:red;">Ruc no válido</span>');
                        break;
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

    function series(e) {
        var s = $(e).val();
        $("#serie").val(s);
    }

    function obtenerserie(e) {
        serie = $(e).val();
        if ((serie.length) == 3) {
            $(e).val(serie + '-');
            //$("#serie").val(serie+'-');
        } else if ((serie.length) > 7) {
            $(e).val('');
            swal("Error!", "Ingrese la serie de la factura correctamente", "error");
        }
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

    function guardar() {

        if ($('#banco_c').val() == '') {
            Swal.fire(
                'Error en el guardado',
                'Selecione un banco para el proveedor',
                'error'
            );
            return;
        }
        $.ajax({
            type: 'post',
            url: "{{route('acreedores_store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'formulario': $("#formularioNuevo").serialize(),
            },
            datatype: 'html',
            success: function(data) {
                if (data.status == 'success') {
                    Swal.fire(
                        'Guardado con Exito',
                        'Proceso de guardado Exitoso',
                        'success'
                    )
                } else {
                    Swal.fire(
                        'Error en el guardado',
                        'Ocurrio un error en el proceso de guardado',
                        'error'
                    )
                }
            },
            error: function(data) {
                console.log(data);
            }
        })
    }

    function abrirModal(clave, tipo, comprobante) {
        $('#titulo').html('');
        $('#titulo').html(tipo + ' | ' + comprobante);
        $.ajax({
            url: '{{route("docelectronicos.traedocumento")}}',
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'html',
            success: function(html) {
                $('#body').html(html);
                $('#myModal').modal('toggle');
            }
        });

    }
</script>
