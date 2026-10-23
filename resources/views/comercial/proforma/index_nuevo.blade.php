@extends('comercial.proforma.base')

@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">
    .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td {
        padding: 3px !important;

    }

    div.formgroup.col-md-4 {
        margin-bottom: 0px !important;
    }
</style>
<style type="text/css">
    .ro {
        color: black;
        font-weight: bolder;
    }

    .ro_2 {
        color: black;
    }

    .radio_origen_2.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    .radio_origen_3.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    .box-title,
    .form-group,
    .box {
        margin: 0;
    }

    h3 {
        margin: 0;
    }
</style>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-6">
                        <h3 class="box-title">{{trans('proforma.Paciente')}}</h3>
                    </div>
                </div>
                <div class="box-body">
                    <div class="alert-danger col-md-4 oculto" id="err">

                    </div>
                    <div class="col-md-12"></div>
                    <form class="form-vertical" id="formulario" role="form">
                        <!--cedula-->
                        <div class="alert-danger"><span id="cant_ord"></span></div>
                        <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="control-label">{{trans('proforma.Cedula')}}</label>
                            <input id="id" maxlength="10" type="text" class="form-control input-sm validar" name="id" value="{{ old('id') }}" required autofocus onchange="verificarCedula(this.value);">
                        </div>
                        <!--primer nombre-->
                        <div class="form-group col-md-4{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="control-label">{{trans('proforma.primernombre')}}</label>
                            <input id="nombre1" class="form-control input-sm validar" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
                        </div>
                        <!--//segundo nombre-->
                        <div class="form-group col-md-4 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="control-label">{{trans('proforma.segundonombre')}}</label>
                            <div class="input-group dropdown">
                                <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="">
                                <ul class="dropdown-menu usuario1">
                                    <li><a data-value="N/A">N/A</a></li>
                                </ul>
                                <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                            </div>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-4{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="control-label">{{trans('proforma.primerapellido')}}</label>
                            <input id="apellido1" type="text" class="form-control input-sm validar" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="">
                        </div>
                        <!--Segundo apellido-->
                        <div class="form-group col-md-4 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="control-label">{{trans('proforma.segundoapellido')}}</label>
                            <div class="input-group dropdown">
                                <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="">
                                <ul class="dropdown-menu usuario2">
                                    <li><a data-value="N/A">N/A</a></li>
                                </ul>
                                <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                            </div>
                        </div>
                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-4{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="control-label">{{trans('proforma.sexo')}}</label>
                            <select id="sexo" name="sexo" class="form-control input-sm validar" required onchange="">
                                <option value=""> {{trans('proforma.seleccion')}} ...</option>
                                <option style="text-transform:uppercase" value="1">{{trans('proforma.masculino')}}</option>
                                <option style="text-transform:uppercase" value="2">{{trans('proforma.femenino')}}</option>
                            </select>
                        </div>
                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-4 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                            <label class="control-label">{{trans('proforma.fechanacimiento')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm validar" id="fecha_nacimiento" required onchange="">
                            </div>
                        </div>

                        <div class="form-group col-md-4 {{ $errors->has('id_tipo') ? ' has-error' : '' }}">
                            <label for="id_tipo" class="control-label">Tipo Seguro</label>
                            <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro();">
                                <!--option value="">Seleccione ...</option-->
                                @foreach($tipos_seguros as $ts)
                                <option value="{{$ts->id}}">{{$ts->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="control-label">{{trans('proforma.Seguro')}} Seguro</label>
                            <select id="id_seguro" name="id_seguro" class="form-control input-sm validar" required>
                                <?php /*<option value="">{{trans('proforma.seleccion')}}...</option>
                                @foreach ($seguros1 as $seguro)
                                <option @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach
                                @foreach ($seguros2 as $seguro)
                                <option @if(old('id_seguro')==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                @endforeach */ ?>
                            </select>
                        </div>

                        <!--Telefono-->
                        <div class="form-group col-md-4{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="control-label">{{trans('proforma.Telefono')}}</label>
                            <input id="telefono1" class="form-control input-sm" type="text" name="telefono1" value="{{ old('telefono1') }}" required autofocus maxlength="50" autocomplete="off">
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--direccion-->
                        <div class="form-group col-md-4{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="control-label">{{trans('proforma.Direccion')}}</label>
                            <input id="direccion" class="form-control input-sm" type="text" name="direccion" value="{{ old('direccion') }}" required autofocus maxlength="100" autocomplete="off">
                        </div>
                        <!--email-->
                        <div class="form-group col-md-4{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Email</label>
                            <input id="email" class="form-control input-sm validar" type="text" name="email" value="{{ old('email') }}" required autofocus maxlength="100" onchange="busca_mail()">
                        </div>

                        <!--origen-->

                    </form>
                    <br>
                    <br>


                    <div class="col-md-12" id="enlaces"></div>
                    <br>

                    <div style="margin-top:20px;" class="col-md-12" id="botones"></div>
                    <br>

                </div>

            </div>
        </div>
    </div>
</section>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    cargar_seguro();
    $('#fecha_nacimiento').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
        //Important! See issue #1075
    });

    const verificarCedula = valor => {
        let validar = validarCedula(valor);
        //console.log(cedula)
        // if (validar) {
            buscarPaciente(valor)
        // } else {
        //    // $("#id").val('');
        // }
    }

    const buscarPaciente = cedula => {
        $.ajax({
            type: 'get',
            url: "{{route('proforma.buscarPaciente_nuevo')}}",
            data: {
                id: cedula
            },
            dataType: 'json',
            success: function(data) {
                let enlaces = document.getElementById("enlaces");
                let enlaces_grupo = document.getElementById("enlaces_grupo");
                if (data.status == 'success') {
                    $("#nombre1").val(data.paciente.nombre1)
                    $("#nombre2").val(data.paciente.nombre2)
                    $("#apellido1").val(data.paciente.apellido1)
                    $("#apellido2").val(data.paciente.apellido2)
                    $("#fecha_nacimiento").val(data.paciente.fecha_nacimiento)
                    $("#telefono1").val(data.paciente.telefono1)
                    $('#email').val(data.paciente.pemail)
                    $('#direccion').val(data.paciente.direccion)
                    $("#sexo option[value=" + data.paciente.sexo + "]").attr("selected", true);
                    $("#id_seguro option[value=" + data.paciente.id_seguro + "]").attr("selected", true);



                    if (data.enlaces != "") {
                        if (enlaces_grupo == null) {
                            $('#enlaces').append(data.enlaces);
                        } else {
                            enlaces.removeChild(enlaces_grupo)
                            $('#enlaces').append(data.enlaces);
                        }
                    } else {
                        if (enlaces_grupo != null) {
                            enlaces.removeChild(enlaces_grupo);
                        }
                    }
                    let typeButton = `<div id="crear_cotizacion"><button type="button" class="btn btn-success" onclick="crearCotizacion()">Crear</button></div>`
                    buttons(typeButton);
                } else {
                    //crear el boton de  crear el paciente y la cotizacion
                    if (enlaces_grupo != null) {
                        enlaces.removeChild(enlaces_grupo)
                    }
                    let typeButton = `<div id="crear_cotizacion"><button type="button" class="btn btn-success" onclick="crearPaciente()">{{trans('proforma.crearproformaypaciente')}}</button></div>`
                    buttons(typeButton);
                }
            }
        });

    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '3px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    const buttons = typeButton => {
        let button = typeButton
        let botones = document.getElementById("botones");
        let crear_cotizacion = document.getElementById("crear_cotizacion");
        if (crear_cotizacion == null) {
            $('#botones').append(button);
        } else {
            botones.removeChild(crear_cotizacion)
            $('#botones').append(button);
        }
    }

    const crearCotizacion = () => {
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{ route('comercial.proforma.store_nuevo') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.status == 'success') {
                        storeProformaLista(data.id_proforma);
                        //window.location.href = `{{url('nuevo_recibo_de_cobro/editar/${data.id_proforma}')}}`;
                    } else {
                        alertas(data.status, "Error...", data.msj)
                    }
                },
                error: function(data) {

                }
            })
        } else {
            $('#crear_cotizacion').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }

    const crearPaciente = () => {
        $('#crear_cotizacion').prop("disabled", true);
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{ route('proforma.crearPaciente') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#formulario").serialize(),
                success: function(data) {
                    if (data.status == 'success') {
                        crearCotizacion();
                    } else {
                        $('#crear_cotizacion').prop("disabled", false);
                        alertas(data.status, "Error...", data.msj)
                    }

                },
                error: function(data) {
                    $('#crear_cotizacion').prop("disabled", false);
                }
            })
        } else {
            $('#crear_cotizacion').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }

    }


    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }
    function cargar_seguro() {
        var id_tipo   = $('#id_tipo').val();
        $.ajax({
            type: 'post',
            url: "{{route('facturaagenda.tipifica_seguros')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_tipo'   : id_tipo,
            },
            success: function(data) {
                if (data.value != 'no') {
                    var contador = 0;
                    $("#id_seguro").empty();
                    $.each(data, function(key, registro) {
                        if(contador == 0){
                            $("#id_seguro").append('<option selected value=' + registro.id + '>' + registro.nombre + '</option>');
                            $('#id_seguro').val(registro.id); 
                            contador ++;
                        }else{
                            $("#id_seguro").append("<option value=" + registro.id + ">" + registro.nombre + "</option>");
                        }
                    });
                } else {
                    $("#id_seguro").empty();
                }
            },
            error: function(data) {}
        });
    }

    function storeProformaLista(id) {
        $.ajax({
            type: 'post',
            url: "{{ route('comercial.proforma.proformaLista_nuevo') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_orden': id
            },
            success: function(data) {
                console.log(data);
                if (data.status == 'success') {
                    alertas(data.status, "Exito...", data.msj);
                    setTimeout(function() {
                        window.location.href = `{{url('nuevo_recibo_de_cobro/editar/${data.id_rc}')}}`;
                    })
                } else {
                    alertas(data.status, "Error...", data.msj);
                    $('#crear_cotizacion').prop("disabled", false);
                }
            },
            error: function(data) {

            }
        })
    }
</script>


@endsection