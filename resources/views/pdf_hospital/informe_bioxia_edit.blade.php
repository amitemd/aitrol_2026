@extends('pdf_hospital.bioxia')
@section('action-content')

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

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 12px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity: 1;
    }

    .ui-autocomplete {
        opacity: 1;
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }
</style>


<input type="hidden" value="{{$infBioxia->hcid}}" name="hcid" id="hcid">

@php 
    $paciente = $infBioxia->historia->paciente; 
    $agenda   = $infBioxia->historia->agenda;
@endphp
<section class="content">
    <div class="row">
        <div class="col-md-9">
            <h4 style="color: red;">
                <b>Paciente: {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}   Fecha: {{$agenda->fechaini}}  Procedimiento:  {{$procedimiento}}</b>
            </h4>
        </div>
        <div class="col-md-3">     
            <a target="_blank" href="{{route('formulario.013B', ['id'=>$infBioxia->id_hc_procedimiento])}} " class="btn btn-success btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Infor.Patología 013B</a>
        </div>

    </div>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="4" style="font-size: 14px;" class="bg-purple"><b>B. DATOS DEL SERVICIO </b></td>
            </tr>
        </thead>
        <tr>
            <td class="text-center bg-green">ESTABLECIMIENTO DE SALUD SOLCICITANTE</td>
            <td class="text-center bg-green">PROFESIONAL SOLICITANTE</td>
            <td class="text-center bg-green">ESPECIALIDAD</td>
            <td class="text-center bg-green">CÓDIGO INTERNO DE MUESTRA DE ANATOMÍA PATOLÓGICA</td>
        </tr>
        <tr>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 30px;" type="text" name="establecimiento" id="establecimiento" required value="{{$infBioxia->establecimiento}}"></td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 30px;" type="text" name="profesional" id="profesional" required value="{{$infBioxia->profesional}}"></td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 30px;" type="text" name="especialidad" id="especialidad" required value="{{$infBioxia->especialidad}}"></td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 30px;" type="text" name="codigo" id="codigo" required value="{{$infBioxia->codigo}}"></td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>DESCRIPCIÓN MACROSCÓPICA</b></td>
            </tr>
            <tr>
                <td>
                    <input onchange="guardar_bioxia(this)" style="width: 100%; height: 45px;" type="text" name="descripcion_macroscopica" id="descripcion_macroscopica" required value="{{$infBioxia->descripcion_macroscopica}}">
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>DESCRIPCIÓN MICROSCÓPICA</b></td>
            </tr>
            <tr>
                <td>
                    <input onchange="guardar_bioxia(this)" style="width: 100%; height: 45px;" type="text" name="descripcion_microscopica" id="descripcion_microscopica" required value="{{$infBioxia->descripcion_microscopica}}">
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="19" style="font-size: 14px;" class="bg-purple"><b>E. INFORME DE CITOLOGÍA VAGINAL</b></td>
            </tr>
        </thead>
        <tr>
            <td colspan="4" class="bg-green text-center">CLASIFICACIÓN BETHESDA</td>
            <td colspan="2" class="bg-green text-center">NORMAL</td>
            <td> <input onchange="guardar_bioxia(this)" style="width: 100%;" type="text" name="normal" id="normal" value="{{$infBioxia->normal}}"></td>
            <td colspan="2" class="bg-green text-center">LIE BAJO</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%;" type="text" name="lie_bajo" id="lie_bajo" value="{{$infBioxia->lie_bajo}}"></td>
            <td colspan="2" class="bg-green text-center">LIE ALTO</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%;" type="text" name="lie_alto" id="lie_alto" value="{{$infBioxia->lie_alto}}"></td>
            <td colspan="2" class="bg-green text-center">CA</td>
            <td colspan="4"><input onchange="guardar_bioxia(this)" style="width: 100%;" type="text" name="ca" id="ca" value="{{$infBioxia->ca}}"></td>
        </tr>
        <tr>
            <td class="bg-green text-center">MUESTRA INADECUADA</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="muestra_ina" id="muestra_ina" value="{{$infBioxia->muestra_ina}}"></td>
            <td class="bg-green text-center">HONGOS</td>
            <td colspan="2"><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="hongos" id="hongos" value="{{$infBioxia->hongos}}"></td>
            <td colspan="2" class="bg-green text-center">ERITROCITOS</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="eritrocitos" id="eritrocitos" value="{{$infBioxia->eritrocitos}}"></td>
            <td colspan="2" class="bg-green text-center">FLORA BACTERIANA</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="flora" id="flora" value="{{$infBioxia->flora}}"></td>
            <td colspan="3" class="bg-green text-center">HISTIOCITOS</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="histiocitos" id="histiocitos" value="{{$infBioxia->histiocitos}}"></td>
            <td class="bg-green text-center">NÚMERO DE MUESTRA</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="text" name="numero_muestra" id="numero_muestra" value="{{$infBioxia->numero_muestra}}"></td>
            <td class="bg-green text-center">FECHA DE PROCESO</td>
            <td><input onchange="guardar_bioxia(this)" style="width: 100%; height: 42px;" type="date" name="fecha_proceso" id="fecha_proceso" value="{{$infBioxia->fecha_proceso}}"></td>
        </tr>
        <tr>
            <td colspan="2" class="text-center bg-green">OBSERVACIONES</td>
            <td colspan="17"><input onchange="guardar_bioxia(this)" style="width: 100%; height: 40px;" type="text" name="observaciones" id="observaciones" value="{{$infBioxia->ca}}"></td>
        </tr>
    </table>

    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>DIAGNÓSTICO</b></td>
            </tr>
            <tr>
                <td>
                    <input onchange="guardar_bioxia(this)" style="width: 100%; height: 45px;" type="text" name="diagnostico" id="diagnostico" required value="{{$infBioxia->diagnostico}}">
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="2" style="font-size: 14px;" class="bg-purple"><b>RECOMENDACIONES</b></td>
            </tr>
            <tr>
                <td>
                    <input onchange="guardar_bioxia(this)" style="width: 100%; height: 45px;" type="text" name="recomendaciones" id="recomendaciones" required value="{{$infBioxia->recomendaciones}}">
                </td>
            </tr>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script>
        function guardar_bioxia(input) {

            let nombre, valor;

            if (input.name === 'descripcion_macroscopica' || input.name === 'descripcion_microscopica' || input.name === 'normal' ||
                input.name === 'lie_bajo' || input.name === 'lie_alto' || input.name === 'ca' || input.name === 'muestra_ina' ||
                input.name === 'hongos' || input.name === 'eritrocitos' || input.name === 'flora' || input.name === 'histiocitos' ||
                input.name === 'numero_muestra' || input.name === 'fecha_proceso' || input.name === 'observaciones' || input.name === 'diagnostico' ||
                input.name === 'recomendaciones' || input.name === 'establecimiento' || input.name === 'profesional' || input.name === 'especialidad'
                || input.name === 'codigo'    
            ) {

                nombre = input.name;
                valor = input.value;


                $.ajax({
                    url: "{{ route('pdf_hospital.informe_bioxias_guardar') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    type: 'POST',
                    data: {
                        [nombre]: valor,

                        hcid: $("#hcid").val(),


                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {

                            sumaTotales(input);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }
        }
    </script>

</section>
@endsection