<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp
<head>
    <meta charset="utf-8" />
    <title>Consentimiento Informado Colonoscopia</title>
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
                <td style="font-weight: bold;">1.CONSENTIMIENTO INFORMADO PARA COLONOSCOPIA (ENDOSCOPIA DIGESTIVA BAJA) </td>
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
        <p style="text-align:justify;">Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla.</p>
        <p style="text-align:justify;">Puede usted retirar este consentimiento cuando lo desee. Firmarlo no le obliga a efectuarse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Lea la información siguiente.</p>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">8. Tipo de Atención: @if(($agenda->est_amb_hos)==1) ambulatorio @elseif(($agenda->est_amb_hos)==0)hospitalizado @endif</td>
            </tr>
        </table>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">9. Nombre del diagnóstico: {{$consentimiento->cie_10}}   </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">10. Nombre del procedimiento recomendado: {{substr($nombre->nombre, 0, 22)}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
            </tr>
            <tr>
                <td style="width:100%; text-align:justify; ">La exploración a la que va a someterse se denomina Colonoscopia (Endoscopia Digestiva Baja) y consiste en la exploración del recto y del colon (intestino grueso) e incluso los últimos centímetros del intestino delgado. Se realiza con un tubo flexible que se introduce por el ano. El instrumental médico denominado endoscopio (colonoscopio) lleva un sistema de iluminación y una cámara. Sirve para el diagnóstico de enfermedades que afectan al intestino grueso así como para el tratamiento de algunas de ellas.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La exploración se realiza en una posición cómoda sobre el lado izquierdo, aunque puede ser conveniente cambiar de postura durante el procedimiento medico ambulatorio. Es indispensable que el paciente realice una correcta preparación previa al procedimiento y así poder realizar la exploración. Para que tolere mejor el procedimiento se le administrará un sedante y si es preciso un analgésico. También se puede realizar con sedación profunda o anestesia general.

                    La exploración se realiza con diferentes grados de sedación, que se consigue mediante la administración de fármacos en dosis adecuada para cada paciente, siendo el anestesiólogo junto al personal asistencial de la Unidad los encargados de realizar y controlar mediante la monitorización correspondiente el proceso de sedación, con el propósito de proporcionar un estado confortable sin dolor, teniendo en cuenta que no siempre es posible predecir el punto de transición entre la sedación moderada y la profunda o la anestesia general, por lo que se recomienda siempre acudir acompañado para la realización del estudio antes mencionado. Otra de las recomendaciones es permanecer en compañía durante las siguientes doce horas, no pudiendo reincorporarse a las actividades habituales o conducir hasta pasado este periodo.

                    A veces, durante la exploración, se producen hallazgos imprevistos, estos pueden obligar a tener que modificar la forma de hacer el procedimiento médico ambulatorio y utilizar variantes de los mismos no contemplados inicialmente.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">13.GRÁFICO DE LA INTERVENCIÓN (Incluya un gráfico previamente seleccionado que facilite la comprensión al paciente)</td>
            </tr>
            <tr>
                <td><img class="centrado" src="{{public_path('/imagenes/colono.jpg')}}"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">14. DURACIÓN ESTIMADA DE LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">El tiempo aproximado de la exploración es de 30 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">15. BENEFECIOS DE LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La colonoscopia está indicada siempre que su médico crea necesario conocer la existencia de alguna enfermedad en su intestino grueso. Por ejemplo, ante síntomas como rectorragia (emisión de sangre a través del ano), anemia por pérdidas de sangre, cambios recientes del ritmo intestinal o diarrea de larga evolución, entre otros.

                    Durante la exploración se pueden realizar tratamientos como: extirpar pólipos (polipectomía o extirpación de pequeños tumores benignos y a veces también malignos), destruir lesiones mediante la aplicación de calor, gas argón o láser, dilatar zonas estrechas o colocar prótesis como también inyectar soluciones esclerosantes en casos de sangrados.

                    En algunos casos puede no lograrse completar la exploración de todo el colon o ser aconsejable realizar más de una sesión o en su defecto utilizar otros métodos de diagnósticos.


                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan y la intervención no produce daños o efectos secundarios indeseables. Pero hay ocasiones en las que no es así, por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o intervención.

                    La colonoscopia es una técnica muy segura. Las complicaciones suelen ser leves y sin repercusión alguna. Estas son: dolor abdominal, sudoración, hinchazón o distensión abdominal y reacciones no deseadas a la medicación administrada. La posibilidad de complicaciones es mayor cuando el endoscopio se emplea para aplicar tratamientos, como polipectomías, destrucción de lesiones con calor, gas argón o láser, dilataciones o colocación de prótesis.

                    Pueden producirse reacciones adversas a la medicación administrada (Rash, edema, Prurito, angioedema, espasmos de glotis, etc.) que suelen ser leves sin repercusión alguna.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Las complicaciones graves son poco frecuentes pero posibles. Entre ellas están: perforación o rotura del intestino, hemorragia, reacciones alérgicas medicamentosas graves, alteraciones cardiopulmonares, rotura de aneurisma abdominal y transmisión de infecciones. Excepcionalmente puede requerirse un tratamiento urgente o una intervención quirúrgica.

                    Se deben considerar también los problemas de salud que presente cada paciente para las exploraciones que se vayan a realizar.
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
                <td style="font-weight: bold;">19. ALTERNATIVAS A LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Una alternativa a la colonoscopia, para el diagnóstico, es la realización de un enema opaco. Este consiste en introducir un contraste por el ano y rellenar todo el colon. Sus resultados diagnósticos son peores y no permite la toma de biopsias ni la realización de tratamientos. Otra alternativa, para el diagnóstico, es la colonografía virtual mediante TC. Esto puede ser necesario de forma complementaria en ciertas circunstancias o si la colonoscopia no ha sido completa. Para los tratamientos, la única alternativa a la endoscopia es una intervención quirúrgica, lo que implica mayores riesgos.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">20. DESCRIPCIÓN DEL MANEJO POSTERIOR A LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Una vez concluido el estudio, al paciente se lo despierta y su periodo de recuperación puede durar 20 minutos aproximadamente. Es posible que persista un leve mareo, por eso la necesidad de que este acompañado siempre por un familiar, dicho mareo desaparecerá con el transcurrir de una a dos horas. Luego del procedimiento medico ambulatorial se recomienda reposo por 12 horas, para lo cual se le extenderá un certificado médico justificativo. Se le entregará su informe impreso del procedimiento con las imágenes y hallazgos, en presencia de su familiar. Si se toman biopsias se le informará el tiempo de procesamiento de las mismas y donde retirar los resultados.

                    El paso del colonoscopio por el intestino puede provocar malestar y sensación de hinchazón. Es posible que tenga momentos de dolor, de corta duración, esto es debido al aire introducido. Una vez finalizada la exploración queda sensación de gases que pasa en poco tiempo. Si se han hecho tratamientos o hay lesiones, como hemorroides, puede verse alguna pequeña hebra de sangre en la primera deposición tras la exploración.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">21. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td>Si no se realiza el procedimiento medico ambulatorio sugerido podría no ser diagnosticado de pólipos, divertículos, o tumores de colon conllevando a un mal diagnóstico de su enfermedad.</td>
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
                <td class="td"> {{$paciente->id}}</td>
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
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Pasaporte No.</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
                <td class="td">&nbsp;</td>
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
        <br>
        <table class="table" style="width:100%; ">
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Cédula No.</th>
                <th class="th">Huella del paciente</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
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
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
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
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
                <td class="td">&nbsp;</td>
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
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
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
                <td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
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
