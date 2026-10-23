@extends('layouts.app-template-h')
@section('content')
<style>
    .flip-card {
        background-color: transparent;
        width: 200px;
        height: 200px;
        perspective: 1000px;
        text-align: center;
        top: 20px;
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    }

    .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
    }

    .flip-card-front {
        background-color: transparent;
        color: black;
    }

    .flip-card-back {
        background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
        color: white;
        transform: rotateY(180deg);
    }

    .ui-autocomplete {
        z-index: 2147483647;
        position: absolute;
        top: 100%;
        left: 0;

        float: left;
        display: none;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu-item>a.ui-corner-all {
        display: block;
        padding: 3px 15px;
        clear: both;
        font-weight: normal;
        line-height: 18px;
        color: #555555;
        white-space: nowrap;
        text-decoration: none;
    }

    .ui-state-hover,
    .ui-state-active {
        color: #ffffff;
        text-decoration: none;
        background-color: #0088cc;
        border-radius: 0px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        background-image: none;
    }
</style>
<div class="content">
    <section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                    {{trans('hospitalizacion.GESTI&Oacute;NDECUARTO')}}
                    <small>Asignaci&oacute;n</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick="location.href='{{route('hospital.gcuartos')}}'" class="btn btn-primary btn-sm btn-block">{{trans('hospitalizacion.Regresar')}}</button>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h6>{{trans('hospitalizacion.Datosprincipalesdepaciente')}}</h6>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-row">
                                    <div class="col-12">
                                        <h6>{{trans('hospitalizacion.Descripci&oacute;n')}}</h6>
                                    </div>
                                    <div class="col-12">
                                        <div class="flip-card">
                                            <div class="flip-card-inner">
                                                <div class="flip-card-front">
                                                    <img src="{{asset('/hc4/img'.'/'.$url)}}" alt="Avatar" style="width: 50%; ">
                                                </div>
                                                <div class="flip-card-back">
                                                    <h4>{{trans('hospitalizacion.HABITACIÓN')}} {{$tipo}}</h4>
                                                    <p>{{trans('hospitalizacion.CÓDIGODELACAMA:')}} {{$codigo}}</p>
                                                    <p>{{trans('hospitalizacion.ESTADO:')}} @if(($estado)==1) LIBRE
                                                        @elseif(($estado)==2) {{trans('hospitalizacion.PREPARACION')}}
                                                        @elseif(($estado)==3) {{trans('hospitalizacion.OCUPADA')}}
                                                        @elseif(($estado)==4) {{trans('hospitalizacion.NODISPONIBLE')}} @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <form method="GET" action="{{route('hospital.cuartog')}}" id="enviar2">
                                    {{ csrf_field() }}
                                    <div class="form-row">
                                        <?php /*-- @php
                                            $carbon = carbon\carbon::createFromDate(2018,11,15);
                                            dd($carbon->day);
                                        @endphp */ ?>
                                        <!-- <div class="col-md-3 col-sm-6 col-3">
                                            <label for="fecha" class = "col-form-label-sm">Fecha de Ingreso</label>
                                        </div>
                                        <div class="col-3">
                                            <input type="date" value="{{date('Y-m-d')}}" class="form-control form-control-sm">
                                        </div> -->
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="nombre" class="col-form-label-sm">{{trans('hospitalizacion.Paciente')}}</label>
                                            <input type="text" id="nombre" name="nombre" class="form-control form-control-sm" placeholder="Nombre de Paciente">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="indetificacion" class="col-form-label-sm">{{trans('hospitalizacion.Indetificaci&oacute;n')}}</label>
                                            <input id="id_paciente" type="hidden" name="id_paciente">
                                            <input id="id_tipo" type="hidden" name="id_tipo" value="{{$id_tipo}}">
                                            <input id="estado" type="hidden" name="estado" value="{{$estado}}">
                                            <input id="id_cama" type="hidden" name="id_cama" value="{{$id_cama}}">

                                            <input readonly type="number" id="identificacion" name="identificacion" class="form-control form-control-sm" placeholder="C.I:">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="edad" class="col-form-label-sm">{{trans('hospitalizacion.Edad')}}</label>
                                            <input readonly type="number" id="edad" name="edad" class="form-control form-control-sm" placeholder="Edad">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="sexo" class="col-form-label-sm">{{trans('hospitalizacion.Sexo')}}</label>
                                            <input readonly type="text" id="sexo" class="form-control form-control-sm" placeholder="Sexo">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="cortesia" class="col-form-label-sm">{{trans('hospitalizacion.Cortes&iacute;a')}}</label>
                                            <input readonly type="text" id="cortesia" class="form-control form-control-sm" placeholder="cortesia">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="alergia" class="col-form-label-sm">{{trans('hospitalizacion.ALERGIAS')}}</label>
                                            <textarea class="form-control" id="alergia" name="alergia" rows="3" readonly></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="observacion" class="col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES')}}</label>
                                            <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="antpato" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEPATOLÓGICOS')}}</label>
                                            <textarea class="form-control" id="antpato" name="antpato" rows="3" readonly></textarea>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="antfami" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEFAMILIARES')}}</label>
                                            <textarea class="form-control" name="antfami" id="antfami" rows="3" readonly></textarea>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="antquiru" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEQUIRURGICOS')}}</label>
                                            <textarea class="form-control" name="antquiru" id="antquiru" rows="3" readonly></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h6>{{trans('hospitalizacion.Datosdefiliaci&oacute;n')}}</h6>
                    <div class="card-tools pull-rigth">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-3 col-sm-3">
                            <label for="seguro" class="col-form-label-sm">{{trans('hospitalizacion.SEGURO')}}</label>
                            <input readonly type="text" id="seguro" value="" class="form-control form-control-sm" placeholder="Seguro">
                        </div>
                        <div class="form-group col-md-3 col-sm-3">
                            <label for="estcivil" class="col-form-label-sm">{{trans('hospitalizacion.ESTADOCIVIL')}}</label>
                            <input readonly type="text" id="estcivil" class="form-control form-control-sm" placeholder="Estado Civil">
                        </div>
                        <div class="form-group col-md-3 col-sm-3">
                            <label for="telefono" class="col-form-label-sm">{{trans('hospitalizacion.TELEFONO')}}</label>
                            <input readonly type="number" id="telefono" class="form-control form-control-sm" placeholder="telefono">
                        </div>
                        <div class="form-group col-md-3 col-sm-3">
                            <label for="celular" class="col-form-label-sm">{{trans('hospitalizacion.CELULAR')}}</label>
                            <input readonly type="number" id="celular" class="form-control form-control-sm" placeholder="Celular">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3 col-sm-6">
                            <label for="ciudad" class="col-form-label-sm">{{trans('hospitalizacion.CIUDAD)}}</label>
                            <input readonly type="text" id="ciudad" class="form-control form-control-sm" placeholder="Ciudad de procedencia">
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <label for="fechadenacimi" class="col-form-label-sm">{{trans('hospitalizacion.FECHADENACIMIENTO)}}</label>
                            <input readonly type="text" id="fechadenacimi" class="form-control form-control-sm" placeholder="Fecha de nacimiento">
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <label for="ciudadpro" class="col-form-label-sm">{{trans('hospitalizacion.CIUDADDENACIMIENTO)}}</label>
                            <input readonly type="text" id="ciudadpro" class="form-control form-control-sm" placeholder="Ciudad de nacimiento">
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            <label for="religion" class="col-form-label-sm">{{trans('hospitalizacion.RELIGIÓN)}}</label>
                            <input readonly type="text" id="religion" class="form-control form-control-sm" placeholder="Religi&oacute;n">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="ocupacion" class="col-form-label-sm">{{trans('hospitalizacion.OCUPACIÓN)}}</label>
                            <input readonly type="text" id="ocupacion" class="form-control form-control-sm" placeholder="Ocupacion">
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="trabajo" class="col-form-label-sm">{{trans('hospitalizacion.TRABAJO)}}</label>
                            <input readonly type="text" id="trabajo" class="form-control form-control-sm" placeholder="Trabajo">
                        </div>
                        <div class="form-group col-md-3 col-sm-4">
                            <label for="sanguineo" class="col-form-label-sm">{{trans('hospitalizacion.GRUPOSANGUINEO)}}</label>
                            <input readonly type="text" id="gruposa" class="form-control form-control-sm" placeholder="GRUPO SANGUINEO">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="email" class="col-form-label-sm">{{trans('hospitalizacion.CORREOELECTR&Oacute;NICO)}}</label>
                            <input readonly type="email" id="email" class="form-control form-control-sm" placeholder="Email">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="direccion" class="col-form-label-sm">{{trans('hospitalizacion.DIRECCIÓN)}}</label>
                            <input readonly type="text" id="direccion" class="form-control form-control-sm" placeholder="Direcci&oacute;n">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="observaciones" class="col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES)}}</label>
                            <textarea class="form-control" id="observaciones" name="observacion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="transfusion" class="col-form-label-sm">{{trans('hospitalizacion.TRANSFUSIONES)}}</label>
                            <input type="text" id="transfusion" class="form-control form-control-sm" placeholder="Transfusiones">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="habito" class="col-form-label-sm">{{trans('hospitalizacion.H&Aacute;BITOS)}}</label>
                            <input type="text" id="habito" class="form-control form-control-sm" placeholder="H&aacute;bito">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="referencia" class="col-form-label-sm">{{trans('hospitalizacion.REFERENCIA)}}</label>
                            <input type="text" id="referencia" class="form-control form-control-sm" placeholder="Referencia del paciente">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="vacuna" class="col-form-label-sm">{{trans('hospitalizacion.VACUNA)}}</label>
                            <input type="text" id="vacuna" class="form-control form-control-sm" placeholder="Vacuna">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12" style="text-align: center;">
                            <button class="btn btn-primary" type="button" id="btnFetch">
                            {{trans('hospitalizacion.ASIGNAR)}}  
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        src2 = "{{route('hospital.auto')}}";
        $("#nombre").autocomplete({
            source: function(request, response) {

                $.ajax({
                    url: "{{route('hospital.auto')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: {
                        term: request.term
                    },
                    dataType: "json",
                    type: 'get',
                    success: function(data) {
                        response(data);
                        console.log(data);
                    }
                })
            },
            minLength: 3,
        });

        $("#nombre").change(function() {
            $.ajax({
                type: 'get',
                url: "{{route('hospital.auto2')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#nombre"),
                success: function(data) {
                    // an *array* that contains the user
                    var seguro;
                    var sexo;
                    var estadoc;
                    var user = data[0]; // a simple user
                    if ((user.seguro) == 1) {
                        seguro = 'PARTICULAR';
                    } else if ((user.seguro) == 2) {
                        seguro = 'IESS';
                    } else if ((user.seguro) == 3) {
                        seguro = 'ISSFA';
                    } else if ((user.seguro) == 4) {
                        seguro = 'HUMANA';
                    } else if ((user.seguro) == 5) {
                        seguro = 'MSP';
                    } else if ((user.seguro) == 6) {
                        seguro = 'ISSPOL';
                    }

                    if ((user.sexo) == 1) {
                        sexo = 'HOMBRE';
                    } else if ((user.sexo) == 2) {
                        sexo = 'MUJER';
                    }
                    if ((user.estadoc) == 1) {
                        estadoc = 'SOLTERO(A)';
                    } else if ((user.estadoc) == 2) {
                        estadoc = 'CASADO(A)';
                    } else if ((user.estadoc) == 3) {
                        estadoc = 'VIUDO(A)';
                    } else if ((user.estadoc) == 4) {
                        estadoc = 'DIVORCIADO(A)';
                    } else if ((user.estadoc) == 5) {
                        estadoc = 'UNIÓN LIBRE';
                    } else if ((user.estadoc) == 6) {
                        estadoc = 'UNIÓN DE HECHO';
                    }
                    var hoy = new Date();
                    var nacimiento = user.fecha;
                    var y = hoy.getFullYear();
                    //console.log(y);
                    var res2 = nacimiento.substr(0, 4);
                    //console.log(res2);
                    var fe1 = parseInt(y);
                    var fe2 = parseInt(res2);
                    var edad = fe1 - fe2;

                    $('#id_paciente').val(user.id);
                    $('#edad').val(String(edad));
                    $('#religion').val(user.religion);
                    $('#estcivil').val(estadoc);
                    $('#seguro').val(seguro);
                    $('#identificacion').val(user.cedula);
                    $('#sexo').val(sexo);
                    $('#cortesia').val('NO');
                    $('#alergia').val(user.alergia);
                    $('#ciudad').val(user.lugar_nacimiento);
                    $('#ciudadpro').val(user.lugar_nacimiento);
                    $('#gruposa').val(user.grupos);
                    $('#direccion').val(user.direccion);
                    $('#telefono').val(user.telefono1);
                    $('#celular').val(user.telefono2);
                    $('#antpato').val(user.antp);
                    $('#antfami').val(user.antf);
                },
                error: function(data) {
                    console.log(data);
                }
            })
        });

        $("#btnFetch").click(function() {
            var nombre = document.getElementById("nombre").value;
            var paciente = document.getElementById("id_paciente").value;

            var msj = "";
            if (msj == "") {
                $.ajax({
                    type: 'get',
                    url: "{{route('hospital.cuartog')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#enviar2").serialize(),
                    success: function(data) {

                        $(this).prop("disabled", true);
                        // add spinner to button
                        $(this).html(
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> REGISTRANDO...`
                        );
                        swal({
                                title: data,
                                icon: "success",
                                type: 'success',
                                buttons: true,
                            })
                            .then((value) => {
                                window.onbeforeunload = beforeVoid;
                                location.href = "{{route('hospital.gcuartos')}}";
                            });
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            } else {
                // add spinner to button
            }
        });

        function beforeVoid() {

        }
    });
</script>

@endsection