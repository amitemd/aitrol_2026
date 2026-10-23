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
                    <small>{{trans('hospitalizacion.Asignaci&oacute;n')}}</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick="location.href='{{route('hospital.gcuartos')}}'" class="btn btn-primary btn-sm btn-block">{{trans('hospitalizacion.Regresar')}}</button>
            </div>
        </div>
    </section>
    <section class="content">
        <form action="{{route('hospitalizacion.store')}}" id="formstore" method="post">
            {{ csrf_field() }}
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header with-border">
                        <h6>{{trans('hospitalizacion.Datosprincipalesdepaciente')}}</h6>
                        <a data-action="collapse" class=""><svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg></a>
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
                                                        <img src="{{asset('/hc4/img'.'/')}}" alt="Avatar" style="width: 50%; ">
                                                    </div>
                                                    <div class="flip-card-back">
                                                        <p>{{trans('hospitalizacion.HABITACIÓN')}}  </p>
                                                        <p>{{trans('hospitalizacion.CÓDIGODELACAMA:')}} {{$cama->codigo}}</p>
                                                        <p>{{trans('hospitalizacion.ESTADO:')}} @if(($cama->estado)==1) LIBRE
                                                            @elseif(($cama->estado)==2) {{trans('hospitalizacion.PREPARACION')}}
                                                            @elseif(($cama->estado)==3) {{trans('hospitalizacion.OCUPADA')}}
                                                            @elseif(($cama->estado)==4) {{trans('hospitalizacion.NODISPONIBLE')}} @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">

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
                                            <input type="text" value="{{$paciente->apellido2}} {{$paciente->apellido1}} {{$paciente->nombre1}}" id="nombre" name="nombre" class="form-control form-control-sm" placeholder="Nombre de Paciente">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="indetificacion" class="col-form-label-sm">{{trans('hospitalizacion.Indetificaci&oacute;n')}}</label>
                                            <input id="id_paciente" type="hidden" name="id_paciente" value="{{$paciente->id}}">
                                            <input type="hidden" name="id_habitacion" value="{{$cama->habitacion->id}}">
                                            <input type="hidden" name="id_cama" value="{{$cama->id}}">
                                            <input type="hidden" name="traspaso" value="{{$traspaso->id}}">

                                            <input readonly type="number" id="identificacion" name="identificacion" class="form-control form-control-sm" placeholder="C.I:" value="{{$paciente->id}}">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="edad" class="col-form-label-sm">{{trans('hospitalizacion.Edad')}}</label>
                                            <input readonly type="number" id="edad" name="edad" class="form-control form-control-sm" placeholder="Edad">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="sexo" class="col-form-label-sm">{{trans('hospitalizacion.Sexo')}}</label>
                                            <input readonly type="text" id="sexo" class="form-control form-control-sm" value="@if($paciente->sexo==1) HOMBRE @else MUJER @endif" placeholder="Sexo">
                                        </div>
                                        <div class="form-group col-md-2 col-sm-3">
                                            <label for="cortesia" class="col-form-label-sm">{{trans('hospitalizacion.Cortes&iacute;a')}}</label>
                                            <input readonly type="text" id="cortesia" class="form-control form-control-sm" placeholder="cortesia">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="alergia" class="col-form-label-sm">{{trans('hospitalizacion.ALERGIAS')}}</label>
                                            <textarea class="form-control" id="alergia" name="alergia" rows="3" readonly>@foreach($paciente->a_alergias as $x){{$x->principio_activo->nombre."\n"}}@endforeach</textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="observacion" class="col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES')}}</label>
                                            <textarea class="form-control" id="observacion" name="observacion" rows="3">{{$traspaso->observaciones}}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="antpato" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEPATOLÓGICOS')}}</label>
                                            <textarea class="form-control" value="{{$paciente->antecedentes_pat}}" id="antpato" name="antpato" rows="3" readonly></textarea>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="antfami" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEFAMILIARES')}}</label>
                                            <textarea class="form-control" value="{{$paciente->antecedentes_fam}}" name="antfami" id="antfami" rows="3" readonly></textarea>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="antquiru" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEQUIRURGICOS')}}</label>
                                            <textarea class="form-control" name="antquiru" id="antquiru" value="{{$paciente->antecedentes_quir}}" rows="3" readonly></textarea>
                                        </div>
                                    </div>

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
                        <a data-action="collapse" class=""><svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg></a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3 col-sm-3">
                                    <label for="seguro" class="col-form-label-sm">{{trans('hospitalizacion.SEGURO')}}</label>
                                    <input readonly type="text" id="seguro" value="{{$paciente->seguro->nombre}}" class="form-control form-control-sm" placeholder="Seguro">
                                </div>
                                <div class="form-group col-md-3 col-sm-3">
                                    <label for="estcivil" class="col-form-label-sm">{{trans('hospitalizacion.ESTADOCIVIL')}}</label>
                                    <input readonly type="text" id="estcivil" class="form-control form-control-sm" @if($paciente->estado_civil) value="CASADO" @else value="SOLTERO" @endif placeholder="Estado Civil">
                                </div>
                                <div class="form-group col-md-3 col-sm-3">
                                    <label for="telefono" class="col-form-label-sm">{{trans('hospitalizacion.TELEFONO')}}</label>
                                    <input readonly type="number" id="telefono" value="{{$paciente->telefono1}}" class="form-control form-control-sm" placeholder="telefono">
                                </div>
                                <div class="form-group col-md-3 col-sm-3">
                                    <label for="celular" class="col-form-label-sm">{{trans('hospitalizacion.CELULAR')}}</label>
                                    <input readonly type="number" id="celular" value="{{$paciente->telefono2}}" class="form-control form-control-sm" placeholder="Celular">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3 col-sm-6">
                                    <label for="ciudad" class="col-form-label-sm">{{trans('hospitalizacion.CIUDAD')}}</label>
                                    <input readonly type="text" id="ciudad" class="form-control form-control-sm" value="{{$paciente->ciudad}}" placeholder="Ciudad de procedencia">
                                </div>
                                <div class="form-group col-md-3 col-sm-6">
                                    <label for="fechadenacimi" class="col-form-label-sm">{{trans('hospitalizacion.FECHADENACIMIENTO')}}</label>
                                    <input readonly type="text" id="fechadenacimi" class="form-control form-control-sm" value="{{$paciente->fecha_nacimiento}}" placeholder="Fecha de nacimiento">
                                </div>
                                <div class="form-group col-md-3 col-sm-6">
                                    <label for="ciudadpro" class="col-form-label-sm">{{trans('hospitalizacion.CIUDADDENACIMIENTO')}}</label>
                                    <input readonly type="text" id="ciudadpro" class="form-control form-control-sm" value="{{$paciente->lugar_nacimiento}}" placeholder="Ciudad de nacimiento">
                                </div>
                                <div class="form-group col-md-2 col-sm-6">
                                    <label for="religion" class="col-form-label-sm">{{trans('hospitalizacion.RELIGIÓN')}}</label>
                                    <input readonly type="text" id="religion" class="form-control form-control-sm" value="{{$paciente->religion}}" placeholder="Religi&oacute;n">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3 col-sm-4">
                                    <label for="ocupacion" class="col-form-label-sm">{{trans('hospitalizacion.OCUPACIÓN')}}</label>
                                    <input readonly type="text" id="ocupacion" class="form-control form-control-sm" value="{{$paciente->ocupacion}}" placeholder="Ocupacion">
                                </div>
                                <div class="form-group col-md-3 col-sm-4">
                                    <label for="trabajo" class="col-form-label-sm">{{trans('hospitalizacion.TRABAJO')}}</label>
                                    <input readonly type="text" id="trabajo" class="form-control form-control-sm" value="{{$paciente->ocupacion}}" placeholder="Trabajo">
                                </div>
                                <div class="form-group col-md-3 col-sm-4">
                                    <label for="sanguineo" class="col-form-label-sm">{{trans('hospitalizacion.GRUPOSANGUINEO')}}</label>
                                    <input readonly type="text" id="gruposa" class="form-control form-control-sm" value="{{$paciente->grupo_sanguineo}}" placeholder="GRUPO SANGUINEO">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email" class="col-form-label-sm">{{trans('hospitalizacion.CORREOELECTR&Oacute;NICO')}}</label>
                                    <input readonly type="email" id="email" class="form-control form-control-sm" value="{{$paciente->mail_primera_vez}}" placeholder="Email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="direccion" class="col-form-label-sm">{{trans('hospitalizacion.DIRECCIÓN')}}</label>
                                    <input readonly type="text" id="direccion" class="form-control form-control-sm" value="{{$paciente->direccion}}" placeholder="Direcci&oacute;n">
                                </div>
                            </div>
                            <!-- <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="transfusion" class="col-form-label-sm">TRANSFUSIONES</label>
                                    <input type="text" id="transfusion" class="form-control form-control-sm" placeholder="Transfusiones">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="habito" class="col-form-label-sm">H&Aacute;BITOS</label>
                                    <input type="text" id="habito" class="form-control form-control-sm" placeholder="H&aacute;bito">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="referencia" class="col-form-label-sm">REFERENCIA</label>
                                    <input type="text" id="referencia" class="form-control form-control-sm" placeholder="Referencia del paciente">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="vacuna" class="col-form-label-sm">VACUNA</label>
                                    <input type="text" id="vacuna" class="form-control form-control-sm" placeholder="Vacuna">
                                </div>
                            </div> -->
                            <div class="form-row">

                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="col-12" style="text-align: center;">
                <button class="btn btn-primary" type="button" onclick="guardar(this)" id="btnFetch">
                    <i class="fa fa-save"></i> &nbsp; {{trans('hospitalizacion.ASIGNAR')}}  
                </button>
            </div>

        </form>

    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        calcular_edad('{{$paciente->fecha_nacimiento}}');
    });

    function calcular_edad(fecha) {
        var hoy = new Date();
        console.log(hoy);
        var nacimiento = fecha;
        var y = hoy.getFullYear();
        //console.log(y);
        var res2 = nacimiento.substr(0, 4);
        console.log(res2);
        var fe1 = parseInt(y);
        var fe2 = parseInt(res2);
        var edad = fe1 - fe2;
        $('#edad').val(edad);
    }

    function guardar(d) {
        $.ajax({
            type: 'POST',
            url: "{{route('hospitalizacion.store')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            cache: true,
            data: $("#formstore").serialize(),
            success: function(data) {
                console.log(data);
                $(d).prop("disabled", true);
                // add spinner to button
                location.href = "{{route('hospital.gcuartos')}}";
                Swal.fire({
                        title: data.state,
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    .then((value) => {
                        window.onbeforeunload = beforeVoid;
                        
                    });
            },
            error: function(data) {
                console.log(data);
            }
        })
    }
</script>

@endsection