<!DOCTYPE html>
<html>
@php
$fecha=date('Y-m-d');
@endphp

<?php
$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$diaNacimiento = $fechaNacimiento->format('d');

$fechaNacimiento = new DateTime($paciente->paciente->fecha_nacimiento);
$mesNacimiento = $fechaNacimiento->format('m');

?>


<head>
    <title>Consentimiento informado prueba de VIH</title>
    <style>
        @page {
            margin: 15 0;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 40px;
            margin-left: 20px;
            margin-right: 20px;
            margin-bottom: 1px;
            font-size: 10px;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        table {
            width: 100px;
            border-collapse: collapse;
        }

        .table2d {
            border: none;
            text-align: center;
        }

        .titulo {
            background-color: #C7C3C2;
        }

        p {
            padding-top: 0;
            padding-bottom: 0;
            margin: 0;
        }

        div {
            padding-top: 0;
            padding-bottom: 0;
        }

        .txt_vertical {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
            letter-spacing: 2px;
            word-wrap: break-word;
        }

        .td-border {
            border: 1px solid;
        }

        .td-not-border-top {
            border-top: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-bottom {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-right: 1px solid;
        }

        .td-not-border-right {
            border-bottom: 1px solid;
            border-left: 1px solid;
            border-top: 1px solid;
        }

        .td-not-border-left {
            border-bottom: 1px solid;
            border-top: 1px solid;
            border-right: 1px solid;
        }

        .td-border-top {
            border-top: 1px solid;
        }

        .td-border-bottom {
            border-bottom: 1px solid;
        }

        .td-border-left {
            border-left: 1px solid;
        }

        .td-border-right {
            border-right: 1px solid;
        }

        .bg-blue {
            background-color: #DDEBF7;
        }

        .bg-green {
            background-color: #BADAC8;
        }

        .bg-yellow {
            background-color: #F1FD77;
        }

        .bg-purple {
            background-color: #CCCCFF;
        }

        .bg-turquoise {
            background-color: #40E0D0;
        }

        .bg-celeste {
            background-color: #00BFFF;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        td.contenedor-texto {
            padding: 20px;
            text-align: left;
        }
    </style>
</head>

<body>
    <header>

    </header>
    <main>

        <table align="center" style="width: 85%">
            <tr>
                @php $empresa_nombre = Sis_medico\Empresa::where('prioridad_labs', '1')->where('estado', 1)->first(); @endphp

                <td class="td-border" style="height:45px">
                    @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/'.$empresa_nombre->logo}}" style="width:125px;height: 40px;">
                    @endif
                </td>
                <td style="width: 20%; font-size: 12px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO PARA REALIZACIÓN DE PRUEBA DE VIH</b></td>
                <td class="text-center td-border" style="font-size: 18px;">Vers 0.2</td>
            </tr>
            <tr>
                <td colspan="3" style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Datos de la unidad que solicita</b></td>
            </tr>
            <tr>
                <td class="td-border">1. Nombre de la unidad que solicita</td>
                <td class="td-border">2. Provincia </td>
                <td class="td-border">3.Teléfono</td>
            </tr>
            <tr>
                <td colspan="3" style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Datos del Paciente</b></td>
            </tr>
            <tr>
                <td class="td-border">1. N° Orden del paciente</td>
                <td class="td-border">2. Número de cédula</td>
                <td class="td-border">3. N° Historia Clínica</td>
            </tr>

            <tr>
                <td class="td-border text-center" style="height: 35;" rowspan="3">{{$paciente->id}}</td>
                <td class="td-border text-center">{{$paciente->paciente->id}}</td>
                <td class="td-border text-center">{{$paciente->paciente->id}}</td>
            </tr>
            <tr>
                <td class="td-border">4. Edad: </td>
                <td class="td-border" rowspan="2">
                    5. Sexo:
                    @for($count = 0; $count <= 8; $count++) &nbsp; @endfor H &nbsp; &nbsp; <input type="checkbox" name="H" id="" @if($paciente->paciente->sexo == 1) checked @endif> <br>

                        @for($count = 0; $count <= 50; $count++) &nbsp; @endfor M &nbsp; &nbsp; <input type="checkbox" name="M" id="" @if($paciente->paciente->sexo == 2) checked @endif>
                </td>
            </tr>
            <tr>
                <td class="td-border text-center">{{$edad}}</td>
            </tr>
            <tr>
                <td class="td-border">6. Provincia:</td>
                <td class="td-border">7. Cantón:</td>
                <td class="td-border">8. Ciudad:</td>
            </tr>
            <tr>
                <td class="td-border text-center">&nbsp;</td>
                <td class="td-border text-center"></td>
                <td class="td-border text-center">{{$paciente->paciente->ciudad}}</td>
            </tr>
            <tr>
                <td class="td-border" colspan="2">9. Dirección: @for($count=0; $count<=30;$count++) &nbsp; @endfor {{$paciente->paciente->direccion}} </td>
                <td class="td-border">10. Teléfono: @for($count=0; $count<=10;$count++) &nbsp; @endfor {{$paciente->paciente->telefono1}}</td>
            </tr>
            <tr>
                <td class="td-border" rowspan="2">11. Etnia:</td>
                <td class="td-border" colspan="2">Afrodescendiente: &nbsp; &nbsp;<input type="checkbox" name="H" id="">@for($count=0; $count<=22;$count++) &nbsp; @endfor Indígena:&nbsp; &nbsp;<input type="checkbox" name="H" id=""> @for($count=0; $count<=12;$count++) &nbsp; @endfor Otro:&nbsp; &nbsp;<input type="checkbox" name="H" id="">
                </td>
            </tr>
            <tr>
                <td class="td-border" colspan="2">Blanco: @for($count=0; $count<=9;$count++) &nbsp; @endfor<input type="checkbox" name="H" id="">
                        @for($count=0; $count<=22;$count++) &nbsp; @endfor Mestizo:&nbsp; &nbsp; &nbsp;<input type="checkbox" name="H" id="">
                </td>
            </tr>
            <tr>
                <td class="td-border" style="height: 30px;" colspan="3">Fecha de realización de la prueba: </td>
            </tr>
        </table>
        <br>
        <p style="width: 60%"> @for($count=0; $count<=11;$count++) &nbsp; @endfor Tras haber recibido información sobre:</p>
                <br>
                <table align="center" style="width: 85%">
                    <tr>
                        <td>@for($count=0; $count<=8;$count++) &nbsp; @endfor Pre @for($count=0; $count<=8;$count++) &nbsp; @endfor Post</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>@for($count=0; $count<=6;$count++) &nbsp; @endfor Prueba @for($count=0; $count<=6;$count++) &nbsp; @endfor Prueba</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>@for($count=0; $count<=7;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                                @for($count=0; $count<=8;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                        </td>
                        <td colspan="2">La transmisión del VIH, su prevención, evolución y sus consecuencias</td>
                    </tr>
                    <tr>
                        <td>@for($count=0; $count<=7;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                                @for($count=0; $count<=8;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                        </td>
                        <td colspan="2">El proceso de la Pruebas de VIH </td>
                    </tr>
                    <tr>
                        <td>@for($count=0; $count<=7;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                                @for($count=0; $count<=8;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                        </td>
                        <td colspan="2">El derecho a la confidencialidad </td>
                    </tr>
                    <tr>
                        <td>@for($count=0; $count<=7;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                                @for($count=0; $count<=8;$count++) &nbsp; @endfor <input type="checkbox" name="H" id="">
                        </td>
                        <td colspan="2">Implicaciones de los resultados de una prueba reactiva y no reactiva</td>
                    </tr>
                </table>
                <table align="center" style="width: 85%">
                    <td>
                        <p>Y en compromiso de recibir la orientación, autorizo a que se me realice la prueba de tamizaje y/o la prueba confirmatoria
                            del VIH garantizándome que los resultados obtenidos, así como la información vertida durante estas conversaciones será
                            manejada con total confidencialidad. Asimismo tengo conocimiento de que los casos reactivos serán notificados al Ministerio de
                            Salud Pública por expresa disposición legal.</p>
                        <br>
                        <p>Reconozco que la legislación sobre protección de datos personales establece obligaciones en el tratamiento de datos de carácter
                            personal, entre las que se encuentra la prohibición de usar o ceder datos personales sin la correspondiente autorización del
                            titular de los mismos, en tal efecto, autorizo a {{$empresa->razonsocial}} para utilizar los datos de carácter
                            personal a los que tengan acceso por este documento única y exclusivamente para cumplir con las obligaciones derivadas de
                            éste conforme a la legislación aplicable.</p>
                    </td>
                </table>
                <br>



                <table align="center" style="width: 85%">
                    <tr>
                        <td>
                            <div>
                                <p><b style="font-size: 26px;">________________ @for($count=0; $count<=12;$count++) &nbsp; @endfor ___________________</b>
                            </div>
                            <div>@for($count=0; $count<=10;$count++) &nbsp; @endfor Firma o huella digital: Paciente @for($count=0; $count<=50;$count++) &nbsp; @endfor Firma Representante Legal del paciente</b>
                            </div>
                            <br>
                            <div>@for($count=0; $count<=1;$count++) &nbsp; @endfor C.I: <b> {{$paciente->paciente->id}} </b> @for($count=0; $count<=59;$count++) &nbsp; @endfor <b style="font-size: 10px;"> Nombre:
                                        <b> _________________________________________ </b> @for($count=0; $count<=203;$count++) &nbsp; @endfor</div>
                                            <div>@for($count=0; $count<=76;$count++) &nbsp; @endfor <b style="font-size: 10px;"> <b>C.I:______________________________________________</b></div>
                                            <br>
                                            <b style="font-size: 9px;"> Nombre persona que da la consejería pre-prueba <br>
                                                Fecha:
                                                <br>
                                                <br>
                        </td>
                    </tr>
                </table>
                <table border="4" align="center" style="width: 100%"><b>
                        <td></td>
                </table>
                <p class="text-center">Consejería Post-prueba</p>

                <table align="center" style="width: 85%">
                    <tr>
                        <td>
                            <div>
                                <p><b style="font-size: 26px;">________________ @for($count=0; $count<=12;$count++) &nbsp; @endfor ___________________</b>
                            </div>
                            <div>@for($count=0; $count<=10;$count++) &nbsp; @endfor Firma o huella digital: Paciente @for($count=0; $count<=50;$count++) &nbsp; @endfor Firma Representante Legal del paciente</b>
                            </div>
                            <br>
                            <div>@for($count=0; $count<=1;$count++) &nbsp; @endfor C.I: <b> {{$paciente->paciente->id}} </b> @for($count=0; $count<=59;$count++) &nbsp; @endfor <b style="font-size: 10px;"> Nombre:
                                        <b> _________________________________________ </b> @for($count=0; $count<=203;$count++) &nbsp; @endfor</div>
                                            <div>@for($count=0; $count<=76;$count++) &nbsp; @endfor <b style="font-size: 10px;"> <b>C.I:______________________________________________</b></div>
                                            <br>
                                            <b style="font-size: 9px;"> Nombre persona que da la consejería post-prueba <br>
                                                Fecha:
                                                <br>
                                                <br>
                        </td>
                    </tr>
                </table>


                <table style="width: 100%">
                    <p style="font-size: 10px;">Ley orgánica integral sobre VIH-SIDA<br>
                        Título I: Normas y Principios generales <br>
                        Título I: Normas y Principios generales <br>
                        Título II: De los derechos, obligaciones y Prevención <br>
                        Capítulo I: De los derechos de las personas Art. 5 letra k (Consentimiento previo informado)<br>
                        Capítulo III: Art. 10 Programas de prevención (Asesoría pre-post prueba); Art. 11 Prueba voluntaria <br>
                        Ley de Prevención y Asistencia Integral del VIH/SIDA <br>
                        "Art. 6.- Los casos diagnosticados de VIH/SIDA deberán ser obligatoriamente notificados al Ministerio de Salud Publica…" <br>
                    </p>
                </table>
                <br>
                <p>
                    ADAPTADO DEL PROGRAMA NACIONAL PARA LA PREVENCIÓN Y CONTROL DEL VIH/SIDA MINISTERIO DE SALUD
                </p>
                </td>
                </tr>
                </table>