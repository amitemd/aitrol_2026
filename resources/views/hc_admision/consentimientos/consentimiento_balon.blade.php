<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>Colocacion-Consentimiento informado</title>
    <style>
        * {
            font-size: 13px;

        }

        .table {
            border-collapse: collapse;

        }

        .table,
        .td {
            border: 1px solid black;
        }

        .th {
            border: 1px solid black;
            text-align: center;
        }

        .centrado {
            width: 250px;
        }

        table tr td {
            font-size: 13px;

        }

        table tr th {
            font-size: 13px;
            background: grey;
        }

        #caja {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <div id="content">
        <table>
            <tr>
                <td style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA COLOCACION DE BALON INTRAGASTRICO </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">2. Nombre del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">3. Servicios del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">4. Número de cedula/HCU del Paciente: {{$paciente->id}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">5. Fecha: {{substr($consentimiento->created_at, 0, 11)}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">6. Hora: {{substr($consentimiento->created_at, 11, 20)}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">7. Información del Paciente: </td>
            </tr>
        </table>
        <table class="table" style="width:100%;margin-top:10px ">
            <tr>
                <th class="th">Apellido Paterno</th>
                <th class="th">Apellido Materno</th>
                <th class="th">Nombres</th>
                <th class="th">Fecha de Nacimiento</th>
                <th class="th">Edad</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->apellido1}}</td>
                <td class="td">{{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}}</td>
                <td class="td">{{$paciente->fecha_nacimiento}}</td>
                <td class="td">{{$edad}}</td>
            </tr>
        </table>
        <p style="text-align: justify;">
            Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla. Puede retirar este consentimiento cuando lo desee. Firmarlo no le obliga a hacerse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Lea la información siguiente.
        </p>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">8. Tipo de Atención: @if(($agenda->est_amb_hos)==1) ambulatorio @elseif(($agenda->est_amb_hos)==0)hospitalizado @endif</td>

            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="font-weight: bold;">9. Nombre del diagnóstico: {{$consentimiento->cie_10}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">10. Nombre del procedimiento recomendado: {{substr($nombre->nombre, 0, 16)}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
            </tr>
            <tr>
                <td style="width:100%; text-align:justify; ">El balón intragástrico es un dispositivo de silicona de alta calidad que se introduce desinflado en el estómago, a través de la boca, mediante un tubo flexible con un sistema de iluminación y una cámara (endoscopio), bajo anestesia general.
                    Una vez inflado en el estómago, produce sensación de llenura. Se utiliza para aumentar la sensación de saciedad y disminuir la ingesta de alimentos.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La colocación del balón intragástrico se lleva a cabo en una posición cómoda, boca arriba, con anestesia general y mediante un endoscopio que se introduce por la boca. Posteriormente se introduce el balón desinflado y, una vez situado en el estómago, se llena con 500 a 700 c.c. de suero teñido con azul de metileno (colorante), controlando con el endoscopio que el balón queda correctamente inflado y situado.

                    Inmediatamente después habrá un periodo de observación durante el cual se le realizaran controles médicos para detectar la posible aparición de complicaciones
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">13. GRÁFICO DEL PROCEDIMIENTO MEDICO</td>
            </tr>
            <tr>
                <td><img class="centrado" src="{{public_path('/imagenes/balon.jpg')}}" /></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">14. DURACIÓN ESTIMADA DE LA EXPLORACION:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">El tiempo aproximado de duración de este procedimiento es de 20 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">15. BENEFECIOS DEL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La colocación de un balón intragástrico le producirá un aumento de la sensación de saciedad, disminuyendo la ingesta de alimentos y ayudándole a perder peso.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces éstos no se materializan y la intervención no produce daños o efectos secundarios indeseables. Hay ocasiones que no es así, por eso es importante que usted conozca los riesgos que pueden aparecer en este procedimiento o intervención.

                    Hasta las 48 horas siguiente del procedimiento medico es factible la presencia de náuseas y vómitos que se controlaran con ayuda de medicación. Se recomienda permanecer en compañía durante este periodo. El paciente a los pocos días superara dichas molestias.

                    Pueden producirse reacciones adversas a la medicación administrada, que suelen ser leves y sin repercusión alguna. Otras complicaciones menores son roturas dentales, mordedura de lengua, luxaciones mandibulares o afonía relacionadas al procedimiento anestésico.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Con mucha menor frecuencia puede aparecer dolor, infección, aspiración bronquial, hipotensión, hemorragia digestiva, perforación gástrica, obstrucción intestinal y distensión abdominal o pancreatitis.

                    Otros efectos adversos excepcionales son las cardiológicas, depresión o parada respiratoria, relacionada sobre todo a la anestesia, que pueden ser graves y requerir tratamiento médico o quirúrgico, así como un riesgo mínimo de mortalidad.

                    Se debe tomar en consideración los problemas de salud de cada paciente.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">18. DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad y estado de salud)</td>
            </tr>
            <tr>
                <td>Diga SI o NO. De ser afirmativo, en cada caso, detalle lo que corresponda:</td>
            </tr>
            <tr>
                <td>Enfermedades Respiratorias</td>
            </tr>
            <tr>
                <td>Enfermedades Cardiacas</td>
            </tr>
            <tr>
                <td>Marcapasos</td>
            </tr>
            <tr>
                <td>Alergias (a medicinas, alimentos u otros)</td>
            </tr>
            <tr>
                <td>Toma medicamentos antiagregantes o anticoagulantes (Plavix, Aspirina, Rivaroxaban, Dabigatran, etc)</td>
            </tr>
            <tr>
                <td>Ha recibido anestesia anteriormente? (En caso de ser afirmativo, diga si ha tenido algún efecto con ella)</td>
            </tr>
            <tr>
                <td>Asma</td>
            </tr>
            <tr>
                <td>Otros</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">19. ALTERNATIVAS AL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Las dietas hipocalóricas supervisadas por médicos especialistas en endocrinología y nutrición también son útiles para perder peso pero, normalmente, de forma más lenta y con mayores índices de fracaso por abandono o incumplimiento.

                    Otras alternativas al balón intragástrico son las técnicas quirúrgicas para el tratamiento de la obesidad, realizándose por laparoscopia. Todas estas técnicas tienen mayores riesgos que el balón intragástrico y algunas de ellas, además, pueden tener complicaciones a largo plazo como diarrea crónica y desnutrición.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">20. DESCRIPCIÓN DEL MANEJO POSTERIOR AL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Luego del procedimiento médico, el paciente es trasladado a la sala de recuperación en donde después de comprobado que ha recobrado su nivel de conciencia y una vez superados los efectos de la sedación puede ser dado de alta con las indicaciones por parte de nuestro personal médico.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">21. CONSECUENCIAS POSIBLES SI NO SE REALIZA EL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td>No lograr perder peso adecuadamente y con esto incrementar sus riesgos de salud relacionados al sobrepeso y obesidad.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">22. DECLARACIÓN DE CONSENTIMIENTO INFORMADO</td>
            </tr>
        </table>
        <table style="width:50%; ">
            <tr>
                <td style="font-weight: bold;">Fecha:</td>
                <td></td>
                <td style="font-weight: bold;">Hora:</td>
                <td></td>
            </tr>
        </table>
        <table style="width:100%; ">
            <tr>
                <td style="text-align:justify;">He facilitado la información completa que conozco, y que me ha sido solicitada, sobre los antecedentes personales, familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos puede afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento médico ambulatorio que se me ha propuesto; he sido informado de las ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qué consiste, los beneficios y posibles riesgos del procedimiento médico ambulatorio. He escuchado, leído y comprendido la información recibida y se me ha dado la oportunidad de preguntar sobre el procedimiento. He tomado consiente y libremente la decisión de autorizar el procedimiento, en consecuencia, libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y personal paramédico que participen en el proceso. Consiento que durante la exploración, me realicen otro procedimiento adicional, si es considerado necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo retirar mi consentimiento cuando lo estime oportuno.</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del paciente)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="text-align:justify;font-weight:bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad): </td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del representante legal)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Parentesco</th>
                <th class="th">Firma del representante legal o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombre del Profesional de la Salud que realiza el procedimiento</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud que realizará el procedimiento</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">SI EL PACIENTE NO ENTIENDE LA LENGUA CASTELLANA</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <thead>
                <tr>
                    <th class="th">Nombres y Apellidos del paciente</th>
                    <th class="th">Pasaporte No.</th>
                    <th class="th">Firma del paciente o Huella, según el caso</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombres y Apellidos del testigo</th>
                <th class="th">Cédula o Pasaporte No.</th>
                <th class="th">Firma del testigo o Huella, según el caso</th>
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
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
                <th class="th">Cédula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud</th>
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
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE LEER O ESCRIBIR (ANALFABETO)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Cédula No.</th>
                <th class="th">Huella del paciente</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
            </tbody>
        </table>
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombres y Apellidos del testigo</th>
                <th class="th">Cédula No.</th>
                <th class="th">Firma del testigo o Huella, según el caso</th>
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
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
                <th class="th">Cédula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud</th>
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
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">23. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
            </tr>
        </table>
        <table style="width:50%; ">
            <tr>
                <td style="font-weight: bold;">Fecha:</td>
                <td></td>
                <td style="font-weight: bold;">Hora:</td>
                <td></td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="text-align:justify;">Una vez que he entendido claramente los procedimientos médicos ambulatorios propuestos, así como las consecuencias posibles si no se realiza la intervención, no autorizo y me niego a que se me realice el procedimiento propuesto y libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende por no realizar la intervención sugerida.</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del paciente)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="text-align:justify;font-weight:bold">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (debe ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad):</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del representante legal)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Parentesco</th>
                <th class="th">Firma del representante legal o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="text-align:justify;">Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del testigo)</th>
                <th class="th">Cédula de ciudadanía.</th>
                <th class="th">Firma del testigo o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres del Profesional de la Salud que realiza el procedimiento</th>
                <th class="th">Cédula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud que realizará el procedimiento</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">SI EL PACIENTE NO ENTIENDE LA LENGUA CASTELLANA</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Pasaporte No.</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos del testigo</th>
                <th class="th">Cédula o Pasaporte No.</th>
                <th class="th">Firma del testigo o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
        <thead>
            <tr>
                <th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
                <th class="th">Cédula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud</th>
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
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE LEER O ESCRIBIR (ANALFABETO)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
            </tr>
        </table>

        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Cédula No.</th>
                <th class="th">Huella del paciente</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos del testigo</th>
                <th class="th">Cédula No.</th>
                <th class="th">Firma del testigo o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
                <th class="th">Cédula de Ciudadanía</th>
                <th class="th">Firma, sello y código del profesional de la salud</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="font-weight: bold;">24. REVOCATORIA DE CONSENTIMIENTO INFORMADO</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="text-align:justify;">De forma libre y voluntaria, revoco el consentimiento realizado en fecha _______________ y manifiesto expresamente mi deseo de no continuar con los procedimientos médicos ambulatorios y que doy por finalizado en esta fecha.
                    Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende.
                </td>
            </tr>
        </table>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del paciente)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">bsp;</td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td style="text-align:justify;font-weight:bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (debe ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad):</td>
            </tr>
        </table>
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos (del representante legal)</th>
                <th class="th">Cedula de Ciudadanía</th>
                <th class="th">Parentesco</th>
                <th class="th">Firma del representante legal o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
                <td class="th">&nbsp;</td>
            </tr>
        </table>
    </div>
</body>

</html>
