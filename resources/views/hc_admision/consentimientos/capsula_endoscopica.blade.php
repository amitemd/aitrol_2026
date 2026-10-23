<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title> CONSENTIMIENTO INFORMADO PARA CAPSULA ENDOSCOPICA</title>
    <style>
        * {
            font-size: 12px;
            padding: 0px;

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

<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA CAPSULA ENDOSCOPICA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">2. NOMBRE DEL ESTABLECIMIENTO DE SALUD:</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">3. SERVICIOS DEL ESTABLECIMIENTO DE SALUD: SERVICIO DE GASTROENTEROLOGIA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">4.Fecha : </td>
                <td class="td" style="font-weight: bold;">5.Hora : </td>
            </tr>
        </table>
        <table style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">6.INFORMACION DEL PACIENTE</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <tr>
                <td style="width: 100%;font-weight: bold;" class="td" rowspan="2">DOCUMENTO DE INDENTIFICACION</td>
                <td style="width: 100%;font-weight: bold;" class="td">CEDULA</td>
                <td style="width: 100%;" class="td">{{$paciente->id}}</td>

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
                    <th class="th">Fecha de Nacimiento</th>
                    <th class="th">Edad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->apellido1}}</td>
                    <td class="td">{{$paciente->apellido2}}</td>
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}}</td>
                    <td class="td">{{$paciente->fecha_nacimiento}}</td>
                    <td class="td">{{$edad}}</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    7. TIPO DE ATENCIÓN
                </td>
                <td style="width: 70%;" class="td">AMBULATORIA</td>
                <td style="width: 30%;" class="td">&nbsp;</td>
                <td style="width: 70%;" class="td">HOSPITALIZACIÓN</td>
                <td style="width: 30%;" class="td">&nbsp;</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    8. NOMBRE DEL DIAGNÓSTICO (CODIFICACIÓN CIE10):
                </td>
                <td style="width: 70%;" class="td">&nbsp;</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    9. NOMBRE DEL PROCEDIMIENTO RECOMENDADO
                </td>
                <td style="width: 70%;" class="td">&nbsp;</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <thead>
                <tr>
                    <th class="th">
                        10. ¿EN QUÉ CONSISTE?
                    </th>
                    <th class="th">
                        11. GRÁFICO DE LA INTERVENCIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">La técnica a la que va a someterse consiste en la filmación del intestino delgado mediante una micro cámara de vídeo que se traga como un comprimido, y a la que denominamos concretamente video cápsula. Sirve para el diagnóstico de enfermedades del intestino delgado. No sirve para la toma de biopsias ni para hacer tratamientos.</td>
                    <td><img style="margin:6px;margin-top:10px;height:110px;width:90px;" src="{{public_path('/imagenes/capsula_endoscopica.png')}}" />
                    </td>

                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th>
                        12. ¿ CÓMO SE REALIZA?
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">La técnica exige que el paciente de pie o sentado, directamente o con algún tipo de ayuda, trague la cápsula. Las imágenes son transmitidas por ondas a unos sensores adheridos a la piel del abdomen o situados en un cinturón especial. Están conectados a un sistema de grabación, similar a un Walkman, que almacena todas las imágenes. Finalizada la exploración, habitualmente transcurridas más de ocho horas, se conecta a una estación de trabajo que permitirá el procesamiento de todos los datos. La cápsula es desechable y será excretada de forma natural por los movimientos intestinales. Se le pedirá que guarde ayuno desde la noche anterior y que el día previo sólo tome dieta líquida y en ocasiones un purgante. Tras un breve período de observación después de la ingestión de la cápsula podrá abandonar el hospital y realizar sus actividades normales. Transcurrido el tiempo establecido, acudirá de nuevo a la consulta para que se le retire el cinturón con el grabador.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    13. DURACIÓN ESTIMADA DE LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td">La colocación del dispositivo portátil (cinturón) y el digerir la capsula endoscópica tiene un tiempo aproximado de 10 minutos.
                    El tiempo habitual que dura la exploración es de 12 horas, caber recalcar que durante este tiempo usted podrá realizar sus actividades de forma normal.</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        14. BENEFICIOS DE LA EXPLORACIÓN
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">Permitirá el estudio del intestino delgado evitando la necesidad de otros tipos de exploración más invasivas y por lo tanto de mayor riesgo para usted.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    15. RIESGOS FRECUENTES
                    (POCO GRAVES)
                </td>
                <td style="width: 70%;" class="td">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan, y la intervención no produce daños o efectos secundarios indeseables. Pero a veces no es así. Por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o prueba funcional endoscópica.Pueden presentarse efectos indeseables, aunque excepcionales, como dificultad para la ingestión de la cápsula, dolor abdominal y vómitos. Durante la exploración la tolerancia es por lo general muy buena presentando sólo las molestias derivadas de portar la grabadora de imágenes.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    16. RIESGOS INFRECUENTES
                    (GRAVES)
                </td>
                <td style="width: 70%;" class="td">La más importante es la obstrucción intestinal provocada por la existencia de una estrechez en la luz del intestino o, en menor medida, la retención de la cápsula por la imposibilidad de la evacuación con la defecación. En esta situación es necesaria una enteroscopia o una intervención quirúrgica para extraer la cápsula, solucionando en muchas ocasiones la enfermedad que motivó la realización del estudio. La ausencia de lesiones en los estudios radiológicos no asegura que la cápsula progrese correctamente.Se deben considerar también los problemas de salud que presente cada paciente para las exploraciones que se vayan a realizar.</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        17. DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad y estado de salud)
                    </th>
                </tr>
            </thead>
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
                <td style="width: 30%;" class="td">Toma de medicamentos antiagregantes o anticuagulantes (plavix, aspirina, rivaraxoban, dabigtran, etc)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">¿Ha recibido anestesia anteriormente?<</td> <td style="width: 20%;" class="td">&nbsp;</td>
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

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    18. ALTERNATIVAS DE LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td">Actualmente existe la posibilidad de utilizar algunos endoscopios más largos que los habituales (enteroscopios) que permiten la visualización endoscópica de porciones más lejanas. Esta exploración permite la toma de biopsias pero es más molesta y laboriosa y es de una eficacia limitada. A veces, se requiere una intervención quirúrgica si se pretende ver todo el intestino delgado. Técnicas radiológicas como el tránsito intestinal o la TAC abdominal pueden ofrecer información sobre el intestino delgado.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    19. MANEJO POSTERIOR AL PROCEDIMIENTO
                </td>
                <td style="width: 70%;" class="td"> Tras la ingesta de la cápsula, permanecerá 30 minutos en nuestras instalaciones para poder confirmar que la cápsula ha pasado al intestino delgado. Puede ser necesario administrar algún medicamento que promueva el vaciamiento del estómago.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    20. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td"> Diagnósticos errados de las distintas patologías del intestino delgado. Lo que conllevará a un tratamiento no efectivo. </td>
            </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;">21. DECLARACIÓN DEL CONSENTIMIENTO INFORMADO</td>
                <td class="td">Fecha:</td>
                <td style="width:40px" class="td">&nbsp;</td>
                <td class="td">HORA:</td>
                <td style="width:40px" class="td" class="td">&nbsp;</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">He facilitado la información completa que conozco, y que me ha sido solicitada, sobre los antecedentes personales, familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos pueden afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento médico ambulatorio que se me ha propuesto; he sido informado de las ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qué consiste, los beneficios y posibles riesgos del procedimiento médico ambulatorio. He escuchado, leído y comprendido la información recibida y se me ha dado la oportunidad de preguntar sobre el procedimiento. He tomado consiente y libremente la decisión de autorizar el procedimiento, en consecuencia, libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y personal paramédico que participen en el proceso. Consiento que, durante la exploración, me realicen otro procedimiento adicional, si es considerado necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo retirar mi consentimiento cuando lo estime oportuno.</td>
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
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                    <td class="td">{{$paciente->id}}</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad): </td>

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
                <td class="td">&nbsp;<!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}--></td>
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
                <td class="td" style="font-weight: bold;">22. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
                <td class="td">Fecha:</td>
                <td style="width:40px" class="td">&nbsp;</td>
                <td class="td">HORA:</td>
                <td style="width:40px" class="td" class="td">&nbsp;</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:5px" class="td">Una vez que he entendido claramente los procedimientos médicos ambulatorios propuestos, así como las consecuencias posibles si no se realiza la intervención, no autorizo y me niego a que se me realice el procedimiento propuesto y libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende por no realizar el procedimiento medico sugerido.</td>
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
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad): </td>

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
                <td class="td">&nbsp;<!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}--></td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td class="td" style="font-weight: bold;">Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite: </td>
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
                <th class="th">23. REVOCATORIA DEL CONSENTIMIENTO INFORMADO</th>
                <td class="td">Fecha:</td>
                <td style="width:40px" class="td">&nbsp;</td>
                <td class="td">HORA:</td>
                <td style="width:40px" class="td" class="td">&nbsp;</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">De forma libre y voluntaria, revoco el consentimiento realizado en fecha _______________ y manifiesto expresamente mi deseo de no continuar con los procedimientos médicos ambulatorios y que doy por finalizado en esta fecha.
                    Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende. </td>
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
                    <td class="td" style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad): </td>
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
                    <td class="td">&nbsp;<!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}--></td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>




    </div>
</body>

</html>