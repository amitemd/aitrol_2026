@extends('preanestesico.preanestesico')
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

<?php
//dd($pre_anestesico);

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($pre_anestesico->anestesiologia->historia->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>


<section class="content">
<input type="hidden" value="{{$pre_anestesico->id}}" name="id_pre" id="id_pre" />

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <thead>
            <tr>
                <td colspan="14" style="font-size: 14px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bg-green text-center"><b>CEDULA</b></td>
                <td class="bg-green text-center"><b>PRIMER APELLIDO</b></td>
                <td class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                <td class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                <td class="bg-green text-center" style="width: 15%;"><b>SEGUNDO NOMBRE</b></td>
                <td class="bg-green text-center"><b>SEXO</b></td>
                <td class="bg-green text-center"><b>EDAD</b></td>
            </tr>
            <tr>
                <td class="text-center" style="height: 20px;">{{$pre_anestesico->anestesiologia->historia->paciente->id}}</td>
                <td class="text-center" style="height: 20px;">{{$pre_anestesico->anestesiologia->historia->paciente->apellido1}}</td>
                <td class="text-center">{{$pre_anestesico->anestesiologia->historia->paciente->apellido2}}</td>
                <td class="text-center">{{$pre_anestesico->anestesiologia->historia->paciente->nombre1}}</td>
                <td class="text-center" >{{$pre_anestesico->anestesiologia->historia->paciente->nombre2}}</td>
                <td class="text-center" >@if($pre_anestesico->anestesiologia->historia->paciente->sexo == 1) Masculino @else Femenino @endif</td>
                <td class="text-center" >{{$edad}}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="14" style="font-size: 14px;" class="bg-purple"><b>B. REGISTRO DE VALORACIÓN PREANESTÉSIA</b></td>
            </tr>
            <tr>
                <td style="width: 2%;"></td>
                <td class="text-center" colspan="7"><b> DIAGNOSTICOS </b></td>
                <td class="text-center" colspan="7"> <b> Cie10 </b></td>
            </tr>

            @php
            $cie10 = Sis_medico\Aud_Hc_Cie10::where('hc_id_procedimiento', $hc_procedimiento->id)->where('ingreso_egreso', 'INGRESO')->first();
            @endphp
            @if(!is_null($cie10))
                @php
                $c3 = Sis_medico\Cie_10_3::find($cie10->cie10);
                $c4 = Sis_medico\Cie_10_4::find($cie10->cie10);
                $texto = '';
                if(!is_null($c3)){
                $texto = $c3->descripcion;
                $ciePre = $cie10->cie10;
                }
                if(!is_null($c4)){
                $texto = $c4->descripcion;
                $ciePre = $cie10->cie10;
                }

                @endphp
                <tr>
                    <td class="text-center" style="width: 1%;">1</td>
                    <td colspan="9">{{ $texto }}</td>
                    <td colspan="4" class="text-center">
                        {{ $ciePre }}
                    </td>
                </tr>
            @endif
                <td colspan="3" rowspan="2" style="width: 15%; height: 35px;" class="text-center"><b>PROCEDIMIENTOS/S PROPUESTO/S</b></td>
                @php
                    
                        $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();

                        $mas = true;
                        $texto = "";
                        foreach($adicionales as $value2)
                        {
                            if($mas == true){
                                $texto = $texto.$value2->procedimiento->nombre ;
                                $mas = false;
                            }
                            else{
                                $texto = $texto.' + '. $value2->procedimiento->nombre ;
                            }
                        }
                    @endphp
                <td colspan="11"> {{$texto}}</td>
            </tr>
            <!--tr>
                <td colspan="11"></td>
            </tr-->
        </tbody>
    </table>
    <br>


    <br>
    <br>
    <div id="tabla_diagnostico">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="5" style="font-size: 14px;" class="bg-purple"><b>ANTECEDENTES PATOLÓGICOS PERSONALES</b></td>
            </tr>
            <tr>
                <td></td>
                <td style="width: 33%;"><b> DIAGNÓSTICOS </b></td>
                <td><b> TIEMPO DE EVOLUCION </b></td>
                <td colspan="2"><b> TRATAMIENTO </b></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input style="width: 98%;" type="text" name="diagnosticos" id="diagnosticos" required>
                </td>
                <td>
                    <input style="width: 98%;" type="text" name="tiempoEvolucion" id="tiempoEvolucion" required>
                </td>
                <td>
                    <input style="width: 98%;" type="text" name="tratamiento" id="tratamiento" required>
                </td>
                <td style="text-align: center;">
                    <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr>
                </td>
            </tr>

            @php
            $contador = 1;
            
            @endphp

            @foreach ($rc_antecedentes as $rc)

            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $rc->diagnostico }}" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$rc->tiempo_evolucion}}" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$rc->tratamiento}}" disabled>
                </td>
                <td>
                    <a onclick="eliminar({{ $rc->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <br>


    <br>

    <form id="frm">

        <input type="hidden" value="{{$id}}" name="id_cab" id="id_cab" />
        
        

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td><b> ANESTÉSICO </b></td>
                </tr>
                <tr>
                    <td>
                        <input style="width: 98%; height: 40px;" type="text" name="anestesico" id="anestesico" value="{{$pre_anestesico->anestesico}}" onchange="actualizar();">
                    </td>
                </tr>
                <tr>
                    <td><b> QUIRÚRGICOS </b></td>
                </tr>
                <tr>
                    <td>
                        <input style="width: 98%; height: 40px;" type="text" name="quirurgicos" id="quirurgicos" value="{{$pre_anestesico->quirurgicos}}" onchange="actualizar();">
                    </td>
                </tr>
                <tr>
                    <td><b> ALÉRGICOS </b></td>
                </tr>
                <tr>
                    <td>
                        <input style="width: 98%; height: 40px;" type="text" name="alergicos" id="alergicos" value="{{$pre_anestesico->alergicos}}" onchange="actualizar();">
                    </td>
                </tr>
                <tr>
                    <td><b> TRANSFUSIONES </b></td>
                </tr>
                <tr>
                    <td>
                        <input style="width: 98%; height: 40px;" type="text" name="transfusiones" id="transfusiones" value="{{$pre_anestesico->transfuciones}}" onchange="actualizar();">
                    </td>
                </tr>
                <tr>
                    <td><b> HÁBITOS </b></td>
                </tr>
                <tr>
                    <td>
                        <input style="width: 98%; height: 40px;" type="text" name="habitos" id="habitos" value="{{$pre_anestesico->habitos}}" onchange="actualizar();">
                    </td>
                </tr>

            </tbody>

        </table>
        <br>


        <div style="width: 100%; display: flex; gap: 10px">
            <label>Tipo</label>
            <select style="width: 25%;" class="form-control input-sm" name="tipo" id="tipo" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{$pre_anestesico->tipo == 'ELECTIVA' ? 'selected' : '' }} value="ELECTIVA">ELECTIVA</option>
                <option {{$pre_anestesico->tipo == 'EMERGENCIA' ? 'selected' : '' }} value="EMERGENCIA">EMERGENCIA</option>
                <option {{$pre_anestesico->tipo == 'URGENCIA' ? 'selected' : '' }} value="URGENCIA">URGENCIA</option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>RIESGO QUIRURGICO</label>
            <select style="width: 25%;" class="form-control input-sm" name="riesgo_quirurgico" id="riesgo_quirurgico" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'BAJO' ? 'selected' : '' }} value="BAJO">BAJO</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'MODERADO' ? 'selected' : '' }} value="MODERADO">MODERADO</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'ALTO' ? 'selected' : '' }} value="ALTO">ALTO</option>
            </select>
        </div>

        <br>

        <div style="width: 100%; display: flex; gap: 10px">
            <label>APERTURA BUCAL(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="apertura" id="apertura" onchange="actualizar();">
                <option value="">Seleccione</option>

                <option {{ $pre_anestesico->apertura_bucal == "<2" ? 'selected' : '' }} value="&lt;2">&lt;2</option>
                <option {{ $pre_anestesico->apertura_bucal == '2-2,5' ? 'selected' : '' }} value="2-2,5">2-2,5</option>
                <option {{ $pre_anestesico->apertura_bucal == '2.6-3' ? 'selected' : '' }} value="2.6-3">2.6-3 </option>
                <option {{ $pre_anestesico->apertura_bucal == '>3' ? 'selected' : '' }} value="&gt;3">&gt;3 </option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>DISTANCIA TIROMENTONEANA(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="distancia" id="distancia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == ">3" ? 'selected' : '' }} value="&lt;3"> &lt;3 </option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == "<6" ? 'selected' : '' }} value="&gt;6">&gt;6 </option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == "6 - 6.5" ? 'selected' : '' }} value="6 - 6.5">6 - 6.5</option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == ">6.5" ? 'selected' : '' }} value="&gt;6.5">&gt;6.5 </option>
            </select>
        </div>

        <br>


        <div style="width: 100%; display: flex; gap: 10px">
            <label> MALLAMPATI </label>
            <select style="width: 20%;" class="form-control input-sm" name="mallampati" id="mallampati" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->mallanpati == "I" ? 'selected' : '' }} value="I">I</option>
                <option {{ $pre_anestesico->mallanpati == "II" ? 'selected' : '' }} value="II">II</option>
                <option {{ $pre_anestesico->mallanpati === "III" ? 'selected' : '' }} value="III">III</option>
                <option {{ $pre_anestesico->mallanpati == "IV" ? 'selected' : '' }} value="IV">IV</option>
            </select>

            <div style="margin-right: 50px;"></div>


            <label>PROTRUSIÓN MANDIBULAR </label>
            <select style="width: 20%;" class="form-control input-sm" name="protrusion" id="protrusion" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->protusion_mandibular == "<0" ? 'selected' : '' }} value="&lt;0"> &lt;0 </option>
                <option {{ $pre_anestesico->protusion_mandibular == "0" ? 'selected' : '' }} value="0">0</option>
                <option {{ $pre_anestesico->protusion_mandibular == ">0" ? 'selected' : '' }} value="&gt;0">&gt;0</option>
            </select>
        </div>
        <br>

        <div style="width: 100%; display: flex; gap: 10px">
            <label>PERIMETRO CERVICAL(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="perimetro" id="peimetro" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->perimetro_cervical == "<40" ? 'selected' : '' }} value="&lt;40"> &lt;40 </option>
                <option {{ $pre_anestesico->perimetro_cervical == ">40" ? 'selected' : '' }} value="&gt;40">&gt;40</option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>MOVILIDAD CERVICAL(°) </label>
            <select style="width: 20%;" class="form-control input-sm" name="movilidad" id="movilidad" onchange="actualizar();">
                <option value="">Seleccione </option>
                <option {{ $pre_anestesico->movilidad_cervical == "<35" ? 'selected' : '' }} value="&lt;35"> &lt;35 </option>
                <option {{ $pre_anestesico->movilidad_cervical == ">35" ? 'selected' : '' }} value="&gt;35">&gt;35</option>
            </select>
        </div>
        <br>


        <div style="width: 100%; display: flex; gap: 10px">
            <label>HISTORIA DEINTUBACIÓN DIFICIL </label>
            <select style="width: 20%;" class="form-control input-sm" name="historia" id="historia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->intubacion_dificil == "SI" ? 'selected' : '' }} value="SI">SI</option>
                <option {{ $pre_anestesico->intubacion_dificil == "NO" ? 'selected' : '' }} value="NO">NO</option>
            </select>

            <div style="margin-right: 50px;"></div>


            <label>PATOLOGÍA ASOCIADA A INTUBACIÓN DIFICIL </label>
            <select style="width: 20%;" class="form-control input-sm" name="patologia" id="patologia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->patologia == "SI" ? 'selected' : '' }} value="SI">SI</option>
                <option {{ $pre_anestesico->patologia == "NO" ? 'selected' : '' }} value="NO">NO</option>
            </select>
        </div>
        <br>
        <br>



        <div style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('torax') ? ' has-error' : '' }}">
                <label for="torax" class="col-md-20 control-label">TÓRAX</label>
                <input type="text" name="torax" id="torax" size="20" class="form-control" required value="{{$pre_anestesico->torax}}" onchange="actualizar();">
            </div>


            <div class="form-group col-xs-6{{ $errors->has('corazon') ? ' has-error' : '' }}">
                <label for="corazon" class="col-md-20 control-label">CORAZÓN </label>
                <input type="text" name="corazon" id="corazon" size="20" class="form-control" required value="{{$pre_anestesico->corazon}}" onchange="actualizar();">
            </div>
        </div>
        <br>
        <div style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('pulmones') ? ' has-error' : '' }}">
                <label for="pulmones" class="col-md-20 control-label">PULMONES</label>
                <input type="text" name="pulmones" id="pulmones" size="20" class="form-control" required value="{{$pre_anestesico->pulmones}}" onchange="actualizar();">
            </div>

            <div class="form-group col-xs-6{{ $errors->has('abdomen') ? ' has-error' : '' }}">
                <label for="abdomen" class="col-md-20 control-label"> ABDÓMEN</label>
                <input type="text" name="abdomen" id="abdomen" size="20" class="form-control" required value="{{$pre_anestesico->abdomen}}" onchange="actualizar();">
            </div>
        </div>
        <br>
        <div style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('extremidades') ? ' has-error' : '' }}">
                <label for="extremidades" class="col-md-20 control-label"> EXTREMIDADES</label>
                <input type="text" name="extremidades" id="extremidades" size="20" class="form-control" required value="{{$pre_anestesico->extremidades}}" onchange="actualizar();">
            </div>

            <div class="form-group col-xs-6{{ $errors->has('sistema') ? ' has-error' : '' }}">
                <label for="sistema" class="col-md-20 control-label"> SISTEMA NERVIOSO CENTRAL</label>
                <input type="text" name="sistema" id="sistema" size="20" class="form-control" required value="{{$pre_anestesico->sistema_nervisoso}}" onchange="actualizar();">
            </div>
        </div>
        <br>

        <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('equivalente') ? ' has-error' : '' }}">
                <label for="equivalente" class="col-md-20 control-label">EQUIVALENTE METABÓLICO (METS)</label>
                <input type="text" name="equivalente" id="equivalente" size="20" class="form-control" value="{{$pre_anestesico->equivalente_metabolico}}" required onchange="actualizar();">
            </div>
        </div>



        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="bg-purple"><b>ESCALA E ÍNDICES</b></td>
                </tr>
            </tbody>
        </table>
        <br>

        <div style="width: 100%; display: flex; gap: 10px">
            <label>ESTADO FISICO ASA </label>
            <select style="width: 20%;" class="form-control input-sm" name="estado_fisico" id="estado_fisico" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->estado_fisico == "I" ? 'selected' : '' }} value="I">I</option>
                <option {{ $pre_anestesico->estado_fisico == "II" ? 'selected' : '' }} value="II">II</option>
                <option {{ $pre_anestesico->estado_fisico == "III" ? 'selected' : '' }} value="III">III</option>
                <option {{ $pre_anestesico->estado_fisico == "IV" ? 'selected' : '' }} value="IV">IV</option>
                <option {{ $pre_anestesico->estado_fisico == "V" ? 'selected' : '' }} value="V">V</option>
                <option {{ $pre_anestesico->estado_fisico == "VI" ? 'selected' : '' }} value="VI">VI</option>
            </select>


            <div style="margin-right: 50px;"></div>


            <label for="riesgo_cardiaco" class="col-md-20 control-label">RIESGO CARDÍACO</label>
            <input style="width: 45%;" type="text" name="riesgo_cardiaco" id="riesgo_cardiaco" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_cardiaco}}" required onchange="actualizar();">
        </div>

        <br>




        <div style="width: 100%; display: flex; gap: 5px">

            <label for="riesgo_pulmonar" class="col-md-20 control-label">RIESGO PULMONAR</label>
            <input style="width: 35%;" type="text" name="riesgo_pulmonar" id="riesgo_pulmonar" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_pulmonar}}" required onchange="actualizar();">

            <div style="margin-right: 50px;"></div>

            <label for="riesgo_tromboembolico" class="col-md-20 control-label">RIESGO TROMBOEMBÓLICO</label>
            <input style="width: 35%;" type="text" name="riesgo_tromboembolico" id="riesgo_tromboembolico" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_tromboembolico}}" required onchange="actualizar();">

        </div>

        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <tbody>
                <tr>
                    <td style="font-size: 12px;" class="bg-purple"><b>TIEMPO DE ULTIMA INGESTA</b></td>
                </tr>
            </tbody>
        </table>
        <br>

        <div style="width: 100%; display: flex; gap: 5px">

            <label for="liquidos_claros" class="col-md-20 control-label">LIQUIDOS CLAROS</label>
            <input style="width: 35%;" type="text" name="liquidos_claros" id="liquidos_claros" size="20" class="form-control input-sm" value="{{$pre_anestesico->liquidos_claros}}" required onchange="actualizar();">

            <div style="margin-right: 50px;"></div>

            <label for="leche_materna" class="col-md-20 control-label">LECHE MATERNA</label>
            <input style="width: 35%;" type="text" name="leche_materna" id="leche_materna" size="20" class="form-control input-sm" value="{{$pre_anestesico->leche_materna}}" required onchange="actualizar();">

        </div>



        <div class="box-body">
            <label for="solidos" class="col-md-20 control-label">SOLIDOS</label>
            <input style="width: 35%;" type="text" name="solidos" id="solidos" size="20" class="form-control input-sm" value="{{$pre_anestesico->solidos}}" required onchange="actualizar();">
        </div>
        <br>

        <div class="box-body">
            <label for="plan_anestesico" class="col-md-20 control-label">PLAN ANESTÉSICO</label>
            <input type="text" name="plan_anestesico" id="plan_anestesico" size="20" class="form-control input-sm" value="{{$pre_anestesico->plan_anestesico}}" required onchange="actualizar();">

        </div>
        <br>

        <div class="box-body">
            <label for="observaciones" class="col-md-20 control-label">OBSERVACIONES</label>
            <input type="text" name="observaciones" id="observaciones" size="20" class="form-control input-sm" value="{{$pre_anestesico->observaciones}}" required onchange="actualizar();">
        </div>

    </form>
    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="font-size: 14px;" class="bg-purple"><b>INDICACIONES</b></td>
            </tr>
            <tr>
                <td>
                    <input style="width: 95%; height: 75px" type="text" name="indicaciones" id="indicaciones">
                    <button class="btn btn-primary bg-green" id="indicaciones" onclick="guardar_indicaciones();" type="button">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div id="tabla_indicacion">
    <table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp

            @foreach ($indicaciones as $indicacione)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 100%;" type="text" value="{{ $indicacione->indicaciones }}" disabled>
                </td>
                <td>
                <a onclick="eliminar_indicaciones({{ $indicacione->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <br>

    <div class="box-body">
        <form id="examen" class="row">
            <input type="hidden" id="id_examen_buscador" />

            <div class="form-group col-md-5{{ $errors->has('buscador') ? ' has-error' : '' }}">
                <label for="buscador" class="control-label">Nombre Examen</label>
                <div class="input-group">
                    <input id="buscador" class="form-control input-sm" type="text" name="buscador" value="" required autofocus onchange="cargar_buscador();" style="background-color: #fff0e6;">
                    @if ($errors->has('buscador'))
                    <span class="help-block">
                        <strong>{{ $errors->first('buscador') }}</strong>
                    </span>
                    @endif
                    <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('buscador').value = '';cargar_buscador()"></i>
                    </div>
                    <div class="input-group-addon">
                        <i style="cursor: pointer" class="fa fa-search" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-5{{ $errors->has('resultado') ? ' has-error' : '' }}">
                <label for="resultado" class="control-label">Resultado </label>
                <input type="text" name="resultado" id="resultado" size="20" class="form-control" value="" required maxlength="255">
            </div>

            <!--div class="form-group col-md-2">
                <button type="button" class="btn btn-primary" onclick="agregarExamen()">Agregar</button>
            </div-->

        </form>
    </div>

    <br>
    <div id="tabla_examen">
    <table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp

            @foreach ($examen as $ex)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $ex->nombre }} - @if(!is_null($ex->examen_parametro)) {{$ex->examen_parametro->nombre}} @endif" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$ex->resultado}}" disabled>
                </td>
                <td>
                    <a onclick="eliminar_examen({{ $ex->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td> 
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
        </div>







    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script>
        function guardar() {

            $.ajax({
                url: "{{route('formato.preanestesico_store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    diagnosticos: $("#diagnosticos").val(),
                    tiempoEvolucion: document.getElementById("tiempoEvolucion").value,
                    tratamiento: $("#tratamiento").val(),
                    id_hc_anestesiologia: $('#id_cab').val(),
                    id_rc_pre_anestesico: $("#id_pre").val(),
                },
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        //window.location.reload();
                        tabla_diagnostico();
                    }
                },
                error: function(error) {

                    console.error(error);
                }
            });
        }


        function eliminar(id) {
            Swal.fire({
                title: `Estas seguro de eliminar`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'get',
                        url: "{{url('formato/preanestesico/delete/')}}/" + id,
                        dataType: 'json',
                        success: function(data) {
                            //location.reload();
                            tabla_diagnostico();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
        }


        function actualizar() {

            //alert("ok");
            $.ajax({
                type: 'post',
                url: "{{route('formato.rc_preanestesico_update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },

                datatype: 'json',
                data: $("#frm").serialize(),
                success: function(data) {
                    //console.log(data);
                    //alert(data);

                },
                error: function(data) {

                }
            });
        }

        $("#buscador").autocomplete({
            source: function(request, response) {

                $.ajax({
                    url: "{{route('examenes.buscar_aj')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: {
                        term: request.term
                    },
                    dataType: "json",
                    type: 'post',
                    success: function(data) {
                        response(data);
                        //console.log(data);

                    },

                })
            },
            minLength: 3,
            select: function(data, ui) {
                console.log(ui);
                console.log(ui.item.label);
                $('#buscador').val(ui.item.label);
                $('#id_examen_buscador').val(ui.item.id);

            }
        });





        function agregarExamen() {
            $.ajax({
                url: "{{route('formato.examen_preanestesico_store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    buscador: $('#buscador').val(),
                    resultado: $('#resultado').val(),
                    id_examen: $('#id_examen_buscador').val(),
                    id_hc_anestesiologia: $('#id_cab').val(),
                    id_rc_pre_anestesico: $("#id_pre").val(),
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        //window.location.reload();
                        tabla_examen();
                    }

                },
                error: function(error) {
                    console.error(error);
                }
            });
        }


        function eliminar_examen(id) {
            Swal.fire({
                title: `Estas seguro de eliminar`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'get',
                        url: "{{url('examen/preanestesico/delete/')}}/" + id,
                        dataType: 'json',
                        success: function(data) {
                            //location.reload();
                            tabla_examen();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
        }




        function guardar_indicaciones() {
            $.ajax({
                url: "{{route('formato.indicaciones_agregar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    indicaciones: $('#indicaciones').val(),
                    id_rc_pre_anestesica: $('#id_pre').val(),
                    id_hc_anestesiologia: $('#id_cab').val(),
                    
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        //window.location.reload();
                        tabla_indicacion();
                        $('#indicaciones').val("");
                    }
                    

                },
                error: function(error) {
                    console.error(error);
                }
            });
        }


        function eliminar_indicaciones(id) {
            Swal.fire({
                title: `Estas seguro de eliminar`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'get',
                        url: "{{url('indicaciones/delete/')}}/" + id,
                        dataType: 'html',
                        success: function(data) {
                            //location.reload();
                            tabla_indicacion();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
        }

        function tabla_diagnostico(){

            $.ajax({
                type: 'get',
                url: "{{route('rc_pre-anestesica.carga_tabla_diagnosticos',['id'=> $pre_anestesico->id])}}",
                dataType: 'html',
                success: function(data) {
                   $('#tabla_diagnostico').html(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function tabla_indicacion(){

            $.ajax({
                type: 'get',
                url: "{{route('rc_pre-anestesica.carga_tabla_indicacion',['id'=> $pre_anestesico->id])}}",
                dataType: 'html',
                success: function(data) {
                $('#tabla_indicacion').html(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function tabla_examen(){

            $.ajax({
                type: 'get',
                url: "{{route('rc_pre-anestesica.carga_tabla_examenes',['id'=> $pre_anestesico->id])}}",
                dataType: 'html',
                success: function(data) {
                $('#tabla_examen').html(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    </script>
</section>
@endsection