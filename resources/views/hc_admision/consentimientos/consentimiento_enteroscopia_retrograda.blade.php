<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>ENTEROSCOPIA Retrograda o Baja-Consentimiento informado</title>
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
    <div id="container">
        <table>
            <tr>
                <td style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA ENTEROSCOPIA RETROGRADA O BAJA </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">2. Nombre del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">3. Servicios del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">4. Número de cedula/HCU del Paciente:  {{$paciente->id}}  </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">5. Fecha:  {{substr($consentimiento->created_at, 0, 11)}}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">6. Hora:  {{substr($consentimiento->created_at, 11, 22)}}</td>
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
            Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla.

            Puede retirar este consentimiento cuando lo desee. Firmarlo no le obliga a efectuarse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Lea la información siguiente.


        </p>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">8. Tipo de Atención:  @if(($agenda->est_amb_hos)==1) ambulatorio @elseif(($agenda->est_amb_hos)==0)hospitalizado @endif</td>
            </tr>
        </table>
        <table style="width:100%">
            <tr>
                <td style="font-weight: bold;">9. Nombre del diagnóstico: {{$consentimiento->cie_10}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">10. Nombre del procedimiento recomendado: {{substr($nombre->nombre, 0, 24)}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
            </tr>
            <tr>
                <td style="width:100%; text-align:justify; ">La exploración a la que va a someterse sirve para el diagnóstico y tratamiento de enfermedades del intestino delgado habitualmente en ileon o yeyuno distal. Para la prueba se usa un tubo largo y flexible con un sistema de iluminación y una cámara (enteroscopio). Se pueden utilizar sistemas de ayuda, como balones que se inflan y desinflan alternativamente, que le permiten avanzar por las curvas del intestino delgado. Se introduce por el ano (retrógrada) y se avanza progresivamente a través del colon hasta el intestino delgado y encontrar la zona de estudio. Es posible que, a pesar de todo, no se consiga una exploración completa de todo el intestino delgado, requiriendo utilizar la vía anterógrada (a través de la boca) en determinadas ocasiones.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La exploración se realiza en una posición cómoda sobre el lado izquierdo o boca arriba (decúbito supino). Comúnmente es un procedimiento extenso, por lo que se deberá hacer con anestesia general.

                    En las exploraciones más prolongadas puede quedar una sensación de hinchazón por el aire introducido durante la misma. En algunos casos, una vez finalizado el procedimiento, habrá un periodo de observación para detectar la posible aparición de complicaciones.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">13. Gráfico de la Exploración </td>
            </tr>
            <tr>
                <td><img class="centrado" src="{{public_path('/imagenes/retrogada.jpg')}}"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">14. DURACIÓN ESTIMADA DE LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">El tiempo promedio de la exploración es de 45 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">15. BENEFECIOS DE LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La enteroscopia permite el diagnóstico de lesiones del intestino delgado que puedan ser las causantes de una hemorragia digestiva de origen desconocido, es decir, aquella de la que no se conoce su causa a pesar de haberla estudiado con endoscopias convencionales. Permite también el estudio de ciertos casos de enfermedades inflamatorias (enfermedad de Crohn), de pólipos y poliposis familiares (como el síndrome de Peutz-Jeghers) o de los hallazgos vistos en otras pruebas de imagen.

                    Durante la exploración se pueden realizar también diversos tratamientos endoscópicos, como extirpar pólipos, aplicar calor (gas argón habitualmente) a lesiones que pueden ser causa de hemorragia o anemia.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces éstos no se materializan, y la intervención no produce daños o efectos secundarios indeseables. Pero hay ocasiones que no es así. Por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o intervención.

                    Las complicaciones son poco frecuentes, pueden producirse reacciones no deseadas por la anestesia general que podrían obligar a dar por finalizada la técnica. Otras complicaciones menores son roturas dentales, mordedura de lengua, luxaciones mandibulares o afonía.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Puede producirse hemorragia al tratar grandes lesiones o por alguna que ya estuviese sangrando, podría ocurrir también una perforación del colon o del intestino delgado. Como consecuencia de alguna de estas complicaciones, excepcionalmente puede ser necesario un tratamiento urgente o una cirugía.

                    Mortalidad: algunas complicaciones pueden seguir una evolución fatal. Es una eventualidad excepcional, pero puede suceder, como también los derivados de sus problemas de salud.

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
                <td style="text-align:justify;">Cuando se llega a indicar una enteroscopia ya se han agotado los métodos diagnósticos de imagen o existe una lesión en el intestino delgado que se debe estudiar o tratar. Por ello, la única alternativa suele ser una enteroscopia intraoperatoria en la cual se explora el intestino durante una intervención quirúrgica que supone más riesgos y complicaciones.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">20.  Descripción del manejo posterior al procedimiento:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Luego del procedimiento, el paciente es trasladado a la sala de recuperación en donde después de recuperar su nivel de conciencia producto de la sedación y comprobada su recuperación, puede ser dado de alta con un acompañante.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">21.  Consecuencias posibles si no se realiza el procedimiento:</td>
            </tr>
            <tr>
                <td>Mal diagnóstico, diagnostico errado o diagnóstico tardío de enfermedades del tracto digestivo alto. </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">22. DECLARACIÓN DE CONSENTIMIENTO INFORMADO</td>
            </tr>
            <table style="width:50%; ">
            <tr>
                <td style="font-weight: bold;">Fecha:</td>
                <td></td>
                <td style="font-weight: bold;">Hora:</td>
                <td></td>
            </tr>
           </table>
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
        </table>
        <table>
            <tr>
                <td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
            </tr>
        </table>
        <br>
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
                <td class="td">&nbsp;</td>
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
