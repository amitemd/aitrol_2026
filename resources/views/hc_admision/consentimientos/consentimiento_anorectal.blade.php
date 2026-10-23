<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>Anorrectal - Consentimiento Informado-rev</title>
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
                <td style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA ECOENDOSCOPIA ANO RECTAL TAMBIEN LLAMADO (ULTRASONIDO ENDOSCOPICO ANO RECTAL </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">2. Nombre del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">3. Servicios del Establecimiento de Salud: </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">4. Número de cedula/HCU del Paciente: {{$paciente->id}}</td>
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
            Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla.
            Puede retirar este consentimiento cuando lo desee. Firmarlo no le obliga a hacerse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Lea la información siguiente.
        </p>
    </div>
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
            <td style="font-weight: bold;">10. Nombre del procedimiento recomendado: {{substr($nombre->nombre, 0, 32)}} </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
        </tr>
        <tr>
            <td style="width:100%; text-align:justify; ">El procedimiento al que usted va a someterse es una técnica mixta que utiliza la imagen endoscópica (introducción de un cable con luz y cámara) y ecográfica. La prueba permite el examen ecográfico, toma de biopsias y algunos tratamientos de lesiones en la pared del recto. Para ello se introduce a través del ano un tubo flexible con un sistema de iluminación y una cámara (endoscopio) que además lleva en su punta el sistema de ecografía. De esta manera se evitan interferencias y se pueden estudiar detalles que de otro modo no serían accesibles.

            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
        </tr>
        <tr>
            <td style="text-align:justify;">Días previos al procedimiento médico, se realizarán todos los exámenes, que incluyen laboratorio, valoración cardiovascular y anestésica. Debe avisar de posibles alergias medicamentosas, alteraciones de la coagulación, enfermedades cardiopulmonares, existencia de prótesis, marcapasos, medicaciones actuales o cualquier otra circunstancia. Debe realizarse una limpieza del colon previamente para que la prueba se lleve a cabo de la mejor manera.

                El día del procedimiento medico ingresará a un área de preparación donde se le colocará una vía intravenosa a través de un catéter que se lo conectará a una solución para hidratación, debiendo permanecer en el lugar hasta que sea trasladado al área de procedimientos médicos.

                En el área de procedimientos, el anestesiólogo procederá a la administración de medicamentos anestésicos, debiendo estar el paciente en posición decúbito lateral izquierdo (de lado izquierdo) y se colocará una mascarilla para administrarle oxígeno de manera continua durante el procedimiento.

                El médico iniciará el procedimiento medico introduciendo el endoscopio y haciéndolo progresar hasta el recto-sigma en busca de alguna alteración. Posteriormente se procede a emitir ondas de ultrasonido que determinarán el tipo de lesión a este nivel y la integridad de las estructuras vecinas.

            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;text-transform:uppercase;">13. Gráfico de la exploración </td>
        </tr>
        <tr>
            <td><img style="width: 150px;height:150px;margin:15px" src="{{public_path('/imagenes/anorectal.jpg')}}">
                <img style="width: 150px;height:150px;margin:15px" src="{{public_path('/imagenes/anorectal1.jpg')}}">
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;text-transform:uppercase;">14. Duración estimada de la exploración:</td>
        </tr>
        <tr>
            <td style="text-align:justify;">El tiempo aproximado de la exploración es de 25 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">15. BENEFECIOS DEL PROCEDIMIENTO:</td>
        </tr>
        <tr>
            <td style="text-align:justify;">La Ecoendoscopía o ultrasonido endoscópico permite conocer las características ecográficas y precisar el diagnóstico de lesiones y extensión de las mismas.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
        </tr>
        <tr>
            <td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan, y la intervención no produce daños o efectos secundarios indeseables. Pero a veces no es así. Por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o exploración.

                Pueden producirse reacciones adversas a la medicación administrada (Rash, edema, Prurito, angioedema, espasmos de glotis, etc) que suelen ser leves sin repercusión alguna.

                Adicionalmente entre los riesgos poco graves, puede haber algo de sangrado tras la realización del procedimiento por el contacto del endoscopio con la lesión a investigar que en ocasiones es muy friable y sangra con facilidad.

            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
        </tr>
        <tr>
            <td style="text-align:justify;">Puede ocurrir una perforación del colon. Como consecuencia de alguna de estas complicaciones, excepcionalmente puede ser necesario un tratamiento urgente o una cirugía.
                Mortalidad: algunas complicaciones pueden seguir una evolución fatal. Es una eventualidad excepcional, pero puede suceder.
                Los derivados de sus problemas de salud.
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
            <td style="text-align:justify;">Entre las alternativas al procedimiento se encuentra estudios radiológicos como Resonancia Magnética Nuclear, tomografía computarizada.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">20. DESCRIPCIÓN DEL MANEJO POSTERIOR AL PROCEDIMIENTO:</td>
        </tr>
        <tr>
            <td style="text-align:justify;">Luego del procedimiento, el paciente es trasladado a la sala de recuperación, donde permanecerá aproximadamente 1 hora, hasta recuperar su nivel de conciencia producto de la sedación, una vez comprobada su recuperación, puede ser dado de alta con su acompañante.

                Después de éste tiempo, el paciente deberá retornar a su lugar de origen (domicilio, hospital), teniendo en consideración que el aire que se usa para insuflar el colon, durante el procedimiento, puede causar distensión abdominal y eliminación de gases durante aproximadamente 24 horas. En caso de requerir algún cuidado adicional nuestro personal médico se lo comunicará al alta.

            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">21. CONSECUENCIAS POSIBLES SI NO SE REALIZA EL PROCEDIMIENTO:</td>
        </tr>
        <tr>
            <td>Mal diagnóstico, diagnóstico errado o tardío de enfermedades del recto. </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">22. DECLARACIÓN DE CONSENTIMIENTO INFORMADO</td>
        </tr>
    </table>
    <!-- Aqui comienza -->
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
    </table>
    <table class="table" style="width:100%; ">
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


</body>

<html
