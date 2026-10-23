@extends('riesgo_caida.base')
@section('action-content')
@php
$data = date("Y-m-d");
@endphp
<style>
    * {
        font-size: 15px;

    }

    .table {
        border-collapse: collapse;
        padding: -1px;

    }

    .table,
    .td {
        border: 1px solid black;
        text-align: center;

    }

    .th {
        text-align: center;
        font-weight: bold;
    }

    table tr td {
        font-weight: bold;
        padding: 3px;

    }

    #container {
        margin: 0px auto;
    }
</style>
<section class="content">
    <div class="container-fluid" style="background-color: white;padding:10px;">
        <div class="box box-primary">
            <div class="col-md-10">
                <h3 style="font-weight:bold;margin-left:12px;">Anexo 12. Escala de riesgo de Morse</h3>
            </div>
            <div class="col-md-2" style="margin-top:5px ;">
                <button type="button" class="btn btn-info" onclick="window.history.back();">Regresar</button>
            </div>
            <table style="width: 100%;border:1px solid black;">
                <tr style="background:white">
                    <th style="text-align: center !important;border-right-style:none !important;width:25%;">
                        <img src="{{asset('/imagenes/logo_riesgo.png')}}" alt="logo" style="margin:3px;">
                    </th>
                    <th style="text-align: center !important;width:50%;font-size:30px !important;">Escala de riesgo de caida Macdems</th>
                    <th style="width:15%;">
                        <table border="1" style="margin: 15px !important;margin-left:20px;">
                            <tr>
                                <th>Version</th>
                                <td style="width: 65PX;">1</td>
                            </tr>
                            <tr>
                                <th>Codigo</th>
                                <td>DNCSS-MSP-008</td>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <td>{{$data}}</td>
                            </tr>
                        </table>


                    </th>
                </tr>
            </table>
            <table style="width: 100%;border:1px solid black;height:8% !Important;">
                <tr>
                    <th class="th" style="background-color: #EAF2F5;">
                        De 13 años a 18 años y adultos
                    </th>
                </tr>
            </table>
            <table style="width: 100%;border:1px solid black;height:8% !important;">
                <tr>
                    <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;">
                        Variables
                    </th>
                    <th class="th" style="background-color: #C1E1E0;">
                        Puntajes
                    </th>
                </tr>
            </table>
            <form name="form" id="form">
                <input type="hidden" id="id_agenda" name="id_agenda" value="{{$id_agenda->id}}">
                <input type="hidden" id="id_paciente" name="id_paciente" value="{{$paciente->id}}">
                <input type="hidden" id="id_camilla" name="id_camilla" value="{{$camilla->id}}">
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="edad">1.Caida Previa</label>
                        <select onchange="mostrar_res(this)" name="caida_previa" id="caida_previa" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">Seleccione</option>
                            <option class="mis-sumar" value="0">No</option>
                            <option class="mis-sumar" value="25">Si</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="ant_caida">2.Comorbilidades</label>
                        <select onchange="mostrar_res(this)" name="comorbilidades" id="comorbilidades" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">Seleccione</option>
                            <option value="15">Si</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="antecedentes">3.Ayuda para Deambular</label>
                        <select onchange="mostrar_res(this)" name="deambular" id="deambular" class="form-control select2 todos_sumados " required>
                            <option value="">Seleccione</option>
                            <option value="0">Ninguna / Reposo en cama / Asistencia</option>
                            <option value="15">Baston / Muleta / Caminador</option>
                            <option value="30">Se apoya en los Muebles</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="compro_conci">4.Venoclisis</label>
                        <select onchange="mostrar_res(this)" name="venoclisis" id="venoclisis" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">Seleccione</option>
                            <option value="0">No</option>
                            <option value="20">Si</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="compro_conci">5.Marcha</label>
                        <select onchange="mostrar_res(this)" name="marcha" id="marcha" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">Seleccione</option>
                            <option value="0">Normal / Reposo en cama / Silla de ruedas</option>
                            <option value="10">Debil</option>
                            <option value="20">Limitada</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="compro_conci">6.Estado Mental</label>
                        <select onchange="mostrar_res(this)" name="estado_mental" id="estado_mental" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">Seleccione</option>
                            <option value="0">Reconoce sus Limitaciones </option>
                            <option value="15">Sobreestima u Olvida sus Limitaciones</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8" style="margin-top: 10px;">
                        <label for="">7. Puntuacion Final y Nivel del Riesgo </label>
                        <img style="padding:10px;margin-left: 4px;margin-right:4px;" src="{{asset('/imagenes/flecha.png')}}" alt="flecha">
                        <input type="text" name="punt_final" id="punt_final" readOnly>
                    </div>
                    <div class="col-md-1" style="margin-top: 17px;text-align:le">
                        <button type="button" id="gard" onclick="guardar()" class="btn btn-danger res" style="display: hidden;">Guardar</button>
                    </div>
                    <div class="col-md-1" style="margin-top: 17px;">
                        <button type="button" onclick="vaciar()" class="btn btn-warning res" style="display: hidden;">Vaciar tabla</button>
                    </div>
                </div>
                <table style="width: 100%;border:1px solid black;height:8% !important;margin-top:12px;">
                    <tr>
                        <th class="th" style="background-color: #C1E1E0;border-right:1px solid black;">
                            Riesgo
                        </th>
                        <th class="th" style="background-color: #C1E1E0;border-right:1px solid black;">
                            Puntaje
                        </th>
                        <th class="th" style="background-color: #C1E1E0;">
                            Accion
                        </th>
                    </tr>
                    <tr id="tablita">
                    </tr>
                </table>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    var contador = 0;

    function vaciar() {
        $("#caida_previa").val("").change();
        $("#comorbilidades").val("").change();
        $("#deambular").val("").change();
        $("#venoclisis").val("").change();
        $("#marcha").val("").change();
        $("#estado_mental").val("").change();
        $("#tablita").children().remove();
        $("#punt_final").val(0);
        contador = 0;
    }
    $(".res").hide();
    var t = $("#id_agenda").val();
    $(document).ready(function() {
        $('.select2').select2({
            tags: false,
        });
    });
    var cont = 0;
    $(".todos_sumados").on('change', function() {
        //console.log("entra");
        var tot = 0;
        var f = 0;
        var suma = 0;
        var valor1 = 0;
        var valor2 = 0;
        var valor3 = 0;
        var valor4 = 0;
        var valor5 = 0;
        var valor6 = 0;
        cont++;
        var final = parseInt($("#punt_final").val(f));
        valor1 = parseInt($("#caida_previa option:selected").val());
        valor2 = parseInt($("#comorbilidades option:selected").val());
        valor3 = parseInt($("#deambular option:selected").val());
        valor4 = parseInt($("#venoclisis option:selected").val());
        valor5 = parseInt($("#marcha option:selected").val());
        valor6 = parseInt($("#estado_mental option:selected").val());
        //tot = parseInt(valor1 + valor2 + valor4 + suma1);
        if (Number.isNaN(valor1)) {
            valor1 = 0;
        }
        if (Number.isNaN(valor2)) {
            valor2 = 0;
        }
        if (Number.isNaN(valor3)) {
            valor3 = 0;
        }
        if (Number.isNaN(valor4)) {
            valor4 = 0;
        }
        if (Number.isNaN(valor5)) {
            valor5 = 0;
        }
        if (Number.isNaN(valor6)) {
            valor6 = 0;
        }
        $("#punt_final").val(parseInt(valor1 + valor2 + valor3 + valor4 + valor5 + valor6));
        var t = $("#punt_final").val();
    });

    function mostrar_res(tot) {
        //console.log(contador);
        contador = parseInt(contador) + parseInt(tot.value);
        if (contador >= 0 && contador <= 25) {
            var ok = ('<td style="border-right:1px solid black;text-align:center;">Bajo</td>"+"<td style="border-right:1px solid black;text-align:center;">0 a 25</td>"+"<td style="text-align:center;">Cuidados bajo Enfermeria</td>');
            $("#tablita").empty().append(ok);

        } else if (contador >= 25 && contador <= 50) {
            var ok1 = ('<td style="border-right:1px solid black;text-align:center;">Medio</td>"+"<td style="border-right:1px solid black;text-align:center;">25 a 50</td>"+"<td style="text-align:center;">Implementacion del plan de prevencion</td>');
            $("#tablita").empty().append(ok1);

        } else if (contador >= 50) {
            var ok3 = ('<td style="border-right:1px solid black;text-align:center;">Alto</td>"+"<td style="border-right:1px solid black;text-align:center;">Mayor a 50</td>"+"<td style="text-align:center;">Implementacion de medidas especiales</td>');
            $("#tablita").empty().append(ok3);
        }

        $(".res").show();

    }

    function guardar() {



        if (document.form.caida_previa.value == '' || document.form.comorbilidades.value == '' || document.form.deambular.value == '' || document.form.venoclisis.value == '' || document.form.marcha.value == '' || document.form.marcha.estado_mental == '') {

            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Campos Vacios',
                showConfirmButton: false,
                timer: 1500
            })
        } else {

            document.getElementById("gard").disabled = true;

            $.ajax({
                url: "{{route('riesgo.guardar_datos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Guardado',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    var url = "{{route('camilla.index')}}";
                    window.location = url;
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });

        }
    }
</script>

@endsection