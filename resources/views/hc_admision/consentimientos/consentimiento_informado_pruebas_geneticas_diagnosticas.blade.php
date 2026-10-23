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
    <title>Consentimiento informado pruebas geneticas diagnosticas</title>
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
            border-left: 1px solid;
            border-right: 1px solid;
            border-bottom: 1px solid;
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


        .texto-izquierda-arriba {
            text-align: left;
            vertical-align: top;
        }

        .justify-text {
            text-align: justify;
        }

        .align-left {
            text-align: left;
        }

        td.contenedor-texto {
            padding: 20px;
            text-align: left;
        }

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        ol {
            list-style-type: decimal;
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <header>

    </header>
    <main>

        <table border="1" align="center" style="width: 85%">
            <tr>
                @php $empresa_nombre = Sis_medico\Empresa::where('prioridad_labs', '1')->where('estado', 1)->first(); @endphp

                <td rowspan="2" class="text-center td-border" style="height:65px; width: 20%;">
                    @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/logo0993075000001.jpg'}}" style="width:150px;height: 75px;">
                    @endif
                </td>
                <td style="width: 35%; font-size: 13px;" class="text-center td-border"><b>{{$empresa->razonsocial}}</b></td>
                <td rowspan="2" class="text-center td-border" style="width: 20%; font-size: 18px;">Vers 0.2</td>
            <tr>
                <td style="font-size: 14px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO- PRUEBAS GENÉTICAS DIAGNÓSTICAS</b></td>
            </tr>
        </table>
        <br>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td colspan="3" style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Datos del Paciente</b></td>
            </tr>
            <tr>
                <td class="td-border">1. N° Orden del paciente</td>
                <td class="td-border">2. Número de cédula</td>
                <td class="td-border">3. N° Historia Clínica</td>
            </tr>
            <tr>
                <td style="height: 20px;" class="td-border text-center" rowspan="3">{{$paciente->id}}</td>
                <td style="height: 20px;" class="td-border text-center">{{$paciente->paciente->id}}</td>
                <td class="td-border text-center">{{$paciente->paciente->id}}</td>
            </tr>
            <tr>
                <td style="height: 15px;" class="td-border">4. Edad:</td>
                <td class="td-border" rowspan="2">
                    5. Sexo:
                    @for($count = 0; $count <= 8; $count++) &nbsp; @endfor H &nbsp; &nbsp; <input type="checkbox" name="H" id="" @if($paciente->paciente->sexo == 1) checked @endif> <br>

                        @for($count = 0; $count <= 57; $count++) &nbsp; @endfor M &nbsp; &nbsp; <input type="checkbox" name="M" id="" @if($paciente->paciente->sexo == 2) checked @endif>
                </td>
            </tr>
            <tr>
                <td style="height: 15px;" class="td-border text-center">{{$edad}}</td>
            </tr>
            <tr>
                <td class="td-border">6. Provincia:</td>
                <td class="td-border">7. Cantón:</td>
                <td class="td-border">8. Ciudad: </td>
            </tr>
            <tr>
                <td class="td-border">&nbsp;</td>
                <td class="td-border"></td>
                <td class="td-border text-center">{{$paciente->paciente->ciudad}}</td>
            </tr>
            <tr>
                <td class="td-border" colspan="2">9. Dirección: @for($count=0; $count<=10;$count++) &nbsp; @endfor {{$paciente->paciente->direccion}}</td>
                <td class="td-border">10. Teléfono: @for($count=0; $count<=10;$count++) &nbsp; @endfor {{$paciente->paciente->telefono1}}</td>
            </tr>
        </table>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td class="td-border">11. Etnia:</td>
                <td class="td-border" colspan="2">Afrodescendiente: &nbsp; &nbsp;<input type="checkbox" name="H" id="">@for($count=0; $count<=22;$count++) &nbsp; @endfor Indígena:&nbsp; &nbsp;<input type="checkbox" name="H" id=""> @for($count=0; $count<=12;$count++) &nbsp; @endfor Otro:&nbsp; &nbsp;<input type="checkbox" name="H" id="">
                </td>
            </tr>
            <tr>
                <td class="td-border" colspan="3"> @for($count=0; $count<=27;$count++) &nbsp; @endfor Blanco: @for($count=0; $count<=10;$count++) &nbsp; @endfor<input type="checkbox" name="H" id="">
                        @for($count=0; $count<=21;$count++) &nbsp; @endfor Mestizo:&nbsp; &nbsp; &nbsp;<input type="checkbox" name="H" id="">
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table border="1" align="center" style="width: 85%">
            <tr>
                <td colspan="3" style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Datos de la unidad/médico que solicita</b></td>
            </tr>
            <tr>
                <td>1.Nombre</td>
                <td>2.N° de colegiado</td>
                <td>3.Centro/Hospital</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4.Telefono</td>
                <td>5. E-mail</td>
                <td>6. Direccion</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Informacion de la muestra</b></td>
            </tr>
            <tr>
                <td class="td-not-border-right" style="font-size: 12px; height: 50px;">Tipo de muestra
                    @for($count=0; $count<=15;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Sangre:
                        @for($count=0; $count<=3;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Saliva:
                            @for($count=0; $count<=8;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; ADN:
                                @for($count=0; $count<=4;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Médula:
                                    @for($count=0; $count<=4;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Semen: <br>
                                        Tejido fresco, origen_________________ Tejido en parafina,origen:_____________ Otros: ______________________________
                </td>
            </tr>
            <tr>
                <td class="td-not-border-right" style="font-size: 12px; height: 50px;">Muestra prenatal: <br>
                    @for($count=0; $count<=5;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Vellosidades coriales:
                        @for($count=0; $count<=5;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Líquido amniótico:
                            @for($count=0; $count<=5;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Restos abortivos:
                                @for($count=0; $count<=5;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Edad gestacional:
                </td>
            </tr>
        </table>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Información del estudio génetico</b></td>
            </tr>
            <tr>
                <td class="td-not-border-right" style="font-size: 12px;">Tipo de análisis:
                    @for($count=0; $count<=15;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Estudio diágnostico:
                        @for($count=0; $count<=12;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp; Estudio familiar:
            </tr>
            <tr>
                <td class="td-not-border-right texto-izquierda-arriba" style="font-size: 12px; height: 55px;">Descripción del estudio solicitado:</td>
            </tr>
        </table>
        <br>
        <table align="center" style="width: 85%">
            <tr>
                <td style="width: 40%; font-size: 12px;" class="text-center td-border"><b>Información clínica del paciente</b></td>
            </tr>
            <tr>
                <td class="td-not-border-right" style="font-size: 12px; height: 18px;">origen ascendencia:</td>
            </tr>
            <tr>
                <td class="td-not-border-right texto-izquierda-arriba" style="font-size: 12px; height: 75px;">Descripción breve del cuadro clínico:</td>
            </tr>
            <tr>
                <td class="td-not-border-right texto-izquierda-arriba" style="font-size: 12px; height: 80px;">Antecedentes familiares relevantes:</td>
            </tr>
        </table>
        <br>
        <p class="page-break-before">
        <table border="1" align="center" style="width: 85%">
            <tr>
                <td rowspan="2" class="text-center td-border" style="height:65px; width: 20%;">
                    @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/logo0993075000001.jpg'}}" style="width:150px;height: 75px;">
                    @endif
                </td>
                <td style="width: 35%; font-size: 13px;" class="text-center td-border"><b>{{$empresa->razonsocial}}</b></td>
                <td rowspan="2" class="text-center td-border" style="width: 20%; font-size: 18px;">Vers 0.2</td>
            <tr>
                <td style="font-size: 14px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO- PRUEBAS GENÉTICAS DIAGNÓSTICAS</b></td>
            </tr>
        </table>
        <br>
        <br>
        <p class="text-center" style="font-size: 15px;"><b> CONSENTIMIENTO INFORMADO </b></p><br>
        <p class="text-center" style="font-size: 18px;"><b> Los siguientes son información explícita relacionada a las pruebas genéticas y</b></p><br>
        <p class="text-center" style="font-size: 18px;"><b> su tratamiento que ponemos a su conocimiento. </b></p><br>
        <table align="center" style="width: 80%">
            <tr>
                <td style="font-size: 16px;" class="justify-text">
                    <ol>
                        <li>Realizar pruebas genéticas de laboratorio en muestras biológicas (sangre/
                            tejido/otros fluidos biológicos), tiene por finalidad diagnosticar si está
                            afectado o es portador de, por ej., una enfermedad metabólica hereditaria.</li> <br>

                        <li>Dichas pruebas serán realizadas en el laboratorio clínico {{$empresa->razonsocial}}</li><br>

                        <li>Únicamente el personal sanitario debidamente autorizado por {{$empresa->razonsocial}} podrá acceder a los
                            datos personales y a losresultados de las pruebas genéticas.</li> <br>
                        <li>El facultativo que le solicita estas pruebas adquiere el compromiso de suministrarle información acerca del objeto de
                            los análisis que se va a realizar. </li> <br>
                        <li>Al realizar el estudio genético solicitado se podrían detectar hallazgos secundarios o fortuitos (hallazgos incidentales y/o variantes de susceptibilidad),
                            que se definen como alteraciones detectadas de forma casual, no relacionadas con el motivo que indicó el estudio, pero que pueden tener implicaciones
                            relevantes para el paciente y/o sus familiares. El paciente debe manifestar su voluntad expresa de ser informado o no de estos hallazgos secundarios o fortuitos en el presente documento. </li> <br>
                        <li>La información obtenida puede ser relevante también para sus familiares y, en este caso, le explicaremos el porqué de la conveniencia de que la conozcan. Es
                            decisión personal suya informarles -algo que nosotros le recomendamos- con el fin de que, si ellos lo desean, puedan acudir a una consulta especializada en
                            genética donde le informarán sobre su riesgo personal y sus opciones de salud en el futuro. </li> <br>
                        <li>Una vez finalizados los análisis, los datos obtenidos y las muestras excedentes (en su caso), se conservarán en el laboratorio de genética de {{$empresa->razonsocial}} durante al menos 5 años y 6 meses respectivamente, por el interés que pueden tener para satisfacer futuras necesidades asistenciales
                            de usted y de sus familiares. </li> <br>
                        <li>Los datos obtenidos se interpretarán de acuerdo con los criterios y fuentes de información disponibles en el momento del estudio. Dicha interpretación puede
                            variar en el futuro según el estado del conocimiento y los avances científicos que ocurran. </li> <br>
                        <li>De conformidad con las mejores prácticas y estándares de calidad de los
                            laboratorios clínicos, {{$empresa->razonsocial}} utiliza el restante
                            de la muestra, así como la información médica y genética del paciente, de
                            manera anónima para fines de investigación o control de calidad (salvo que no
                            sea permitido por la legislación aplicable). Tales usos pueden resultar en el
                            desarrollo de productos y servicios comerciales. El paciente no recibirá
                            notificación de los usos específicos, ni compensación por los mismos. Todos los
                            posibles usos serán de conformidad con la ley aplicable. El paciente debe
                            manifestar su voluntad expresa en relación a la cesión del excedente de su
                            muestra y de la información derivada del estudio en el presente documento.</li> <br>
                    </ol>
                </td>
            </tr>
        </table>


        <p class="page-break-before">

        <table border="1" align="center" style="width: 85%">
            <tr>
                <td rowspan="2" class="text-center td-border" style="height:65px; width: 20%;">
                    @if(!is_null($empresa_nombre->logo))
                    <img src="{{base_path().'/storage/app/logo/logo0993075000001.jpg'}}" style="width:150px;height: 75px;">
                    @endif
                </td>
                <td style="width: 35%; font-size: 13px;" class="text-center td-border"><b>{{$empresa->razonsocial}}</b></td>
                <td rowspan="2" class="text-center td-border" style="width: 20%; font-size: 18px;">Vers 0.2</td>
            <tr>
                <td style="font-size: 14px;" class="text-center td-border"><b>CONSENTIMIENTO INFORMADO- PRUEBAS GENÉTICAS DIAGNÓSTICAS</b></td>
            </tr>
        </table>
        <br>

        <table align="center" style="width: 85%">
            <tr>
                <td style="font-size: 16px;" class="justify-text">
                    <ul>
                        <li>10. Para la mayor parte de las pruebas de genética, el acceso a través de internet a
                            los resultados no estará disponible. Debido a la complejidad de las pruebas
                            genéticas y las importantes implicaciones de los resultados de éstas, los
                            resultados serán reportados sólo a través de un profesional cualificado u otro
                            proveedor de atención de la salud identificado. Los resultados son confidenciales
                            en la medida permitida por la ley
                        </li> <br>
                    </ul>

                </td>
            </tr>
            <tr>

                <td style="font-size: 14px;" class="justify-text">
                    Si ha comprendido la información que se le ha proporcionado, ha resuelto cualquier duda que
                    pudiese tener y otorga su consentimiento para realizar las pruebas genéticas en los términos
                    antes explicados, por favor firme a continuación este consentimiento informado en sentido
                    afirmativo <br><br>
                    Yo, <b>{{$paciente->paciente->nombre1}} &nbsp; {{$paciente->paciente->nombre2}} &nbsp; {{$paciente->paciente->apellido1}} &nbsp; {{$paciente->paciente->apellido2}} &nbsp; </b>declaro que, se me ha proporcionado la
                    información relativa al consentimiento informado de pruebas genéticas diagnósticas y otorgo a
                    {{$empresa->razonsocial}} mi consentimiento expreso para realizar las pruebas
                    genéticas en los términos antes explicados. <br><br>
                    Yo, ……………………………………………….. declaro que he sido informado de que <br>
                    (nombre de la persona a quien se le toma la muestra) podría estar afectado/a o ser portador/a
                    de una alteración genética. <br><br>
                    Doy consentimiento para realizar dichas pruebas genéticas al laboratorio clínico HumanLabs
                    Medicina Diagnóstica S.A., en caso necesario, en otros laboratorios designados por el mismo,
                    con las debidas garantías, para ayudar al proceso diagnóstico. <br><br>
                    En el supuesto particular de detectar hallazgos secundarios que no son sujeto de este estudio
                    pero que podrían ser de interés para mi persona y/o familiares en la actualidad o en el futuro,
                    manifiesto lo siguiente:
                </td>
            </tr>
        </table>
        <br>

        <table align="center" style="width: 85%">
            <tr>
                <td style="font-size: 14px;" class="justify-text">
                    <input type="checkbox" name="H" id=""> &nbsp;No deseo ser informado/a
                    @for($count=0; $count<=12;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp;Deseo ser informado/a <br> <br>
                        Respecto a la cesión del excedente de mi muestra y la información derivada del estudio, <br>
                        manifiesto lo siguiente: <br> <br>
                        <input type="checkbox" name="H" id=""> &nbsp;No permito que sean utilizadas
                        @for($count=0; $count<=15;$count++) &nbsp; @endfor <input type="checkbox" name="H" id=""> &nbsp;permito que sean utilizadas <br> <br>

                            <p style="font-size: 16px;"> Reconozco que la legislación sobre protección de datos personales establece obligaciones en el
                                tratamiento de datos de carácter personal, entre las que se encuentra la prohibición de usar o
                                ceder datos personales sin la correspondiente autorización del titular de los mismos, en tal efecto,
                                autorizo a {{$empresa->razonsocial}} para utilizar los datos de carácter
                                personal a los que tengan acceso por este documento única y exclusivamente para cumplir con
                                las obligaciones derivadas de éste conforme a la legislación aplicable.</p> <br>
                            <br>
                            Fecha: <b>__________________________</b><br> <br><br>
                            <b>________________________________ @for($count=0; $count<=20;$count++) &nbsp; @endfor _____________________________________ </b>
                                    @for($count=0; $count<=3;$count++) &nbsp; @endfor FIRMA DEL PACIENTE O @for($count=0; $count<=28;$count++) &nbsp; @endfor FIRMA DEL REPRESENTANTE DE <br>
                                        @for($count=0; $count<=3;$count++) &nbsp; @endfor REPRESENTANTE LEGAL @for($count=0; $count<=20;$count++) &nbsp; @endfor {{$empresa->razonsocial}} <br> <br>
                                            C.I.: <b> {{$paciente->paciente->id}}</b> @for($count=0; $count<=40;$count++) &nbsp;@endfor C.I.: ______________________________ <br> <br>
                </td>

            </tr>
        </table>
        <table align="center" style="width: 85%">
            <tr>
                <td style="font-size: 13px;"><b>{{$paciente->paciente->nombre1}} {{$paciente->paciente->nombre2}} {{$paciente->paciente->apellido1}} {{$paciente->paciente->apellido2}} </b></td>
                <td style="font-size: 12px; width: 42%;"><b> @for($count=0; $count<=42;$count++) &nbsp; @endfor Nombre:______________________________________</b>
                </td>
            </tr>
        </table>