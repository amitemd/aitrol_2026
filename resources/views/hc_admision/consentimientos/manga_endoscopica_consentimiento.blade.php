<!DOCTYPE html>
<html lang="es">
@php
    use Carbon\Carbon;
    $edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>Manga Endoscopica Consentimiento</title>
    <style>
        * {
            font-size: 12px;

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
            border: 1px solid black;
            text-align: center;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
        }
    </style>

</head>
<!-- dkfjdkgk -->
<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">CONSENTIMIENTO INFORMADO PARA MANGA ENDOSCOPICA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">SERVICIO DE GASTROENTEROLOGIA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td">4.Fecha : </td>
                <td class="td">5.Hora : </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <tr>
                <td style="width: 100%;font-weight: bold;" class="td" rowspan="2">DOCUMENTO DE INDENTIFICACION
                </td>
                <td style="width: 100%;font-weight: bold;" class="td">CEDULA</td>
                <td style="width: 100%;" class="td">{{ $paciente->id }}</td>

            </tr>
            <tr>
                <td style="width: 100%;font-weight: bold;" class="td">PASAPORTE</td>
                <td style="width: 100%;" class="td">&nbsp;</td>
            </tr>
        </table>

        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="th">Apellido Paterno</th>
                    <th class="th">Apellido Materno</th>
                    <th class="th">Nombres</th>
                    <th class="th">Edad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{ $paciente->apellido1 }}</td>
                    <td class="td">{{ $paciente->apellido2 }}</td>
                    <td class="td">{{ $paciente->nombre1 }} {{ $paciente->nombre2 }}</td>
                    <td class="td">{{ $edad }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 50%;font-weight: bold;" class="td">
                    8.TIPO DE ATENCION
                </td>
                <td style="width: 70%;" class="td">
                    AMBULATORIO
                </td>
                <td style="width: 70%" class="td">
                    &nbsp;
                </td>
                <td style="width: 70%;" class="td">
                    HOSPITALIZACIÓN
                </td>
                <td style="width: 70%" class="td">
                    &nbsp;
                </td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    NOMBRE DEL PROCEDIMIENTO RECOMENDADO
                </td>
                <td style="width: 70%;" class="td">{{ $nombre->nombre }}</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <thead>
                <tr>
                    <th class="th">
                        ¿EN QUÉ CONSISTE?
                    </th>
                    <th class="th">
                        GRÁFICO DE LA INTERVENCIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">
                        El propósito principal de la técnica es la consecución de la pérdida de peso corporal mediante
                        la colocación de puntos de sutura que reducen la capacidad del estómago en un 70-80%, provocando
                        una
                        sensación de plenitud gástrica precoz y, en consecuencia, la reducción de peso.
                    </td>
                    <td><img style="height:100px;margin:15px" src="{{ public_path('/imagenes/manga_endoscopica.png') }}" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th>
                        ¿CÓMO SE REALIZA?
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">
                        La exploración se realiza en posición sobre el lado izquierdo y ocasionalmente acostado (mirando
                        el techo).
                        Se realiza bajo anestesia general con ventilación mecánica (respiración asistida a travez de
                        tubo endotraqueal)
                        para garantizar oxigenación durante el procedimiento y que se proteja la vía respiratoria. Para
                        proteger los dientes y
                        el endoscopio, se introduce un protector bucal.
                        El endoscopio se introduce a través del esófago hasta el estómago. Por vía endoscópica, se
                        colocan unos puntos de sutura no absorbibles que al ser ajustados provocarán un reducción de la
                        capacidad del estómago de almacenar alimentos, y favorecer así la pérdida de peso corporal. Se
                        trata de un procedimiento que produce unos cambios anatómicos permanentes en el estómago pero
                        que no se extirpa una parte del mismo.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    DURACIÓN DE LA INSTANCIA
                </td>
                <td style="width: 70%;" class="td">Aproximadamente 60 a 90 minutos</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        BENEFICIOS DE LA EXPLORACIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">Pérdida progresiva de peso junto con un plan
                        nutricional y de ejercicio paulatino. Entiendo y asumo que la técnica por sí misma no garantiza
                        la pérdida de peso y que ésta únicamente dependerá de las calorías consumidas y, en
                        consecuencia, de la modificación de la conducta alimentaria. Si esta técnica no fuera efectiva,
                        podrían
                        realizarse intervenciones quirúrgicas más radicales para el tratamiento de la obesidad.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    RIESGOS FRECUENTES
                </td>
                <td style="width: 70%;" class="td">Reacciones no deseadas a la medicación administrada, roturas
                    dentales, mordedura de lengua, luxaciones mandibulares o afonía. Puede ser habitual que presente
                    inicialmente</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    RIESGOS INFRECUENTES
                </td>
                <td style="width: 70%;" class="td">Perforación, la hemorragia, infecciones intra-abdominal
                    normalmente pueden ser tratadas endoscópicamente pero a veces pueden requerir intervención
                    quirúrgica. Reacciones alérgicas medicamentosas, alteraciones cardiopulmonares y transmisión de
                    infecciones.
                    Otras complicaciones graves eventuales derivadas del riesgo quirúrgico particular de cada paciente.
                </td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad y estado de
                        salud)
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td">Diga SI o NO. De ser afirmativo, en cada caso, detalle lo que corresponda:</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">

            <tr>
                <td style="width: 30%;" class="td">Enfermedades respiratorias</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Enfermedades cardiácas</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>
            <tr>
                <td style="width: 30%;" class="td">Marcapasos</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Alergias (a medicinas, alimentos, otros)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Toma de medicamentos antiagregantes o anticuagulantes (plavix,
                    aspirina, rivaraxoban, dabigtran, etc)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td"> ¿Ha recibido anestesia anteriormente?
                    (En caso de ser afirmativo, diga si ha tenido algún efecto con ella)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Asma</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Otro</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center;">
            <tbody>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        ALTERNATIVAS DE LA EXPLORACIÓN
                    </td>
                    <td style="width: 70%;" class="td">Dieta hipocalórica estricta, el balón intragástrico,
                        diferentes dispositivos similares al balón y otras intervenciones quirúrgicas para el
                        tratamiento de la obesidad.</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        MANEJO POSTERIOR AL PROCEDIMIENTO
                    </td>
                    <td style="width: 70%;" class="td">Luego del procedimiento, el paciente es trasladado a la
                        sala de recuperación en donde después de recuperar su nivel de conciencia, producto de la
                        anestesia y comprobada su recuperación, se mantiene en recuperación durante 12-24 horas.</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN
                    </td>
                    <td style="width: 70%;" class="td">Ausencia de pérdida de peso, todas las complicaciones
                        derivadas del mismo.
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;width:50%">DECLARACIÓN DEL CONSENTIMIENTO INFORMADO</td>
                <td class="td" style="font-weight: bold;width:60%;text-align:left">
                    FECHA
                </td>
                <td class="td" style="font-weight: bold;width:50%;text-align:left">
                    HORA
                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">
                    He facilitado la información completa que conozco, y que me ha sido solicitada, sobre los
                    antecedentes personales,
                    familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos pueden
                    afectar los resultados
                    del tratamiento. Estoy de acuerdo con el procedimiento médico ambulatorio que se me ha propuesto; he
                    sido informado de
                    as ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qué consiste, los
                    beneficios y posibles riesgos
                    del procedimiento médico ambulatorio. He escuchado, leído y comprendido la información recibida y se
                    me ha dado la oportunidad de
                    preguntar sobre el procedimiento. He tomado consiente y libremente la decisión de autorizar el
                    procedimiento, en consecuencia,
                    libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso
                    fortuito, fuerza mayor o evento
                    imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y
                    personal paramédico que
                    articipen en el proceso. Consiento que, durante la exploración, me realicen otro procedimiento
                    adicional, si es considerado
                    necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo
                    retirar mi consentimiento cuando lo estime oportuno.</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{ $paciente->nombre1 }} {{ $paciente->nombre2 }} {{ $paciente->apellido1 }}
                        {{ $paciente->apellido2 }}</td>
                    <td class="td">{{ $paciente->id }}</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL
                    CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente
                    una incapacidad): </td>

            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{ $paciente->nombre1familiar }} {{ $paciente->nombre2familiar }} {{ $paciente->apellido1familiar }} {{ $paciente->apellido2familiar }}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
                    <th class="th">CÉDULA DEL CIUDADANIA</th>
                    <th class="th">FIRMA, SELLO Y CÓDIGO DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;width:40%">B. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
                <th class="th" style="text-align: left;width:50%">FECHA</th>
                <th class="th" style="text-align: left;width:50%">HORA</th>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:5px" class="td">Una vez que he entendido claramente los
                    procedimientos médicos ambulatorios propuestos, así como las consecuencias posibles si no se realiza
                    la intervención, no autorizo y me niego a que se me realice el procedimiento propuesto y libero de
                    responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario
                    que me atiende por no realizar el procedimiento medico sugerido.</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td"></td>
                    <td class="td"></td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL
                    CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente
                    una incapacidad): </td>

            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{ $paciente->nombre1familiar }} {{ $paciente->nombre2familiar }} {{ $paciente->apellido1familiar }} {{ $paciente->apellido2familiar }}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td class="td" style="font-weight: bold;">Si el paciente no acepta el procedimiento sugerido
                        por el profesional y se niega a firmar este acápite: </td>
            </thead>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL TESTIGO</th>
                    <th class="th">CÉDULA DEL CIUDADANIA</th>
                    <th class="th">FIRMA DEL TESTIGO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <th class="th" style="font-weight:bold;text-align: left;width:40%">C. REVOCATORIA DEL
                    CONSENTIMIENTO INFORMADO</th>
                <th class="th" style="text-align: left;width:50%">FECHA</th>
                <th class="th" style="text-align: left;width:50%">HORA</th>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">De forma libre y voluntaria, revoco el
                    consentimiento realizado en fecha _______________ y manifiesto expresamente mi deseo de no continuar
                    con los procedimientos médicos ambulatorios y que doy por finalizado en esta fecha.
                    Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional
                    sanitario que me atiende. </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td"></td>
                    <td class="td"></td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL
                        CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o
                        presente una incapacidad): </td>
            </thead>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{ $paciente->nombre1familiar }} {{ $paciente->nombre2familiar }} {{ $paciente->apellido1familiar }} {{ $paciente->apellido2familiar }}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>




    </div>
</body>

</html>
