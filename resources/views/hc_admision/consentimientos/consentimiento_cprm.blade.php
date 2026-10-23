<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>CPRE-Consentimiento informado</title>
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
            width: 150px;
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
        <table class="table" >
            <tr>
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA COLANGIOPANCREATOGRAFIA RETRÓGRADA ENDOSCÓPICA (CPRE / ERCP).</td>
            </tr>
            <table>
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
        <p style="text-align:justify;">Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla.
            Puede retirar este consentimiento cuando lo desee. Firmarlo no le obliga a hacerse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Antes de firmar, lea la información siguiente.
        </p>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">8. Tipo de Atención:  @if(($agenda->est_amb_hos)==1) ambulatorio @elseif(($agenda->est_amb_hos)==0)hospitalizado @endif</td>
            </tr>
        </table>
        <table style="width:100%; ">
            <tr>
                <td style="font-weight: bold;">9. Nombre del diagnóstico: {{$consentimiento->cie_10}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">10. Nombre del procedimiento recomendado:  {{substr($nombre->nombre, 0, 22)}} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
            </tr>
            <tr>
                <td style="width:100%; text-align:justify; ">El procedimiento al que va a someterse se llama Colangiografia Retrogada Endoscopica C.P.R.E y es una técnica útil en el estudio y tratamiento de enfermedades de las vías biliares y páncreas. Mediante un tubo flexible con un sistema de iluminaciónón y una cámara (duodenosdoscopio), que se introduce a través de la boca se localiza el orificio de salida del conducto biliar y pancreático al duodeno (papila) y se inyecta por él un líquido (contraste para hacer radiografias). Así se pueden ver las lesiones existentes y la necesidad de tratamiento durante la misma exploraciónón.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Días previos al procedimiento medico ambulatorio, se realizarán todos los exámenes, que incluyen laboratorio, valoración cardiovascular, valoración anestésica y RX de tórax (de ser necesaria), deberá también poner en conocimiento del médico todos sus antecedentes patológicos personales, en caso de padecer enfermedades previas, así como notificar alergias a algún medicamento.

                    El día del procedimiento medico ambulatorio ingresará a un área de preparación donde se le colocará una vía intravenosa a través de un catéter que se lo conectará a una solución intravenosa para hidratación, debiendo permanecer en el lugar hasta que sea trasladado el área de procedimientos medico ambulatorio para realizar la exploración.

                    En el área del procedimiento médico ambulatorio, el anestesiólogo procederá a administrar fármacos anestésicos, debiendo estar el paciente en posición decúbito dorsal (boca arriba) o supino (boca abajo). Una vez sedado el paciente, se le colocará un protector bucal y el médico iniciará el procedimiento introduciendo el endoscopio y haciéndolo progresar a través del esófago y el estómago hasta el duodeno (primera parte del intestino delgado). Se ubicará la ampolla de Vater y se introducirá una delgada sonda a través de la cual se administrará el medio de contraste. Posteriormente se procederá a realizar las distintas tomas radiológicas y tratamientos endoscópicos en función de los hallazgos.

                    A veces, durante la exploración, se producen hallazgos imprevistos, éstos pueden obligar a tener que modificar la forma de hacer el procedimiento medico ambulatorio y utilizar variantes de la misma no contempladas inicialmente.

                    En ocasiones es necesario tomar muestras biológicas para estudiar mejor su caso.

                    La exploración se realiza con anestesia general, que se consigue mediante la administración de fármacos en dosis adecuadas para cada paciente, siendo el anestesiólogo y su equipo los encargados de realizar y controlar, mediante la monitorización correspondiente, el proceso de sedación, con el propósito de proporcionar un estado confortable sin dolor, teniendo en cuenta que no siempre es posible predecir el punto de transición entre la sedación moderada y la profunda o la anestesia general, por lo que se recomienda siempre acudir acompañado para la realización del estudio antes mencionado. De la misma manera se recomienda permanecer en compañía durante las siguientes veinticuatro horas, evitando o no pudiéndose incorporarse a sus actividades habituales o conducir hasta pasado este periodo.


                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">13. GRÁFICO DE LA EXPLORACION</td>
            </tr>
            <tr>
                <td><img class="centrado" src="{{public_path('/imagenes/cprm.png')}}"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">14. DURACIÓN ESTIMADA DE LA EXPLORACIÓN:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">El tiempo aproximado de la exploración es de 45 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">15. BENEFECIOS DEL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La Colangiografia Retrogada Endoscopica C.P.R.E. está indicada siempre que su médico crea necesario conocer y/o tratar alguna enfermedad en el conducto biliar o pancreático, por ejemplo, cálculos (litiasis) o estrechamientos de distintos orígenes.

                    Durante la exploraciónón suelen realizarse tratamientos endoscópicos. El más habitual es la esfinterectomía, que consiste en la apertura del orificio de salida del conducto biliar o pancreático para ampliarlo y extraer así cálculos o eliminar obstáculos. Si existe estrechamiento en algún conducto se puede colocar una prótesis, un pequeño tubo de plástico o metálico, a su través de forma que la bilis salga libremente al intestino. También se puede realizar colangioscopia con litotripcia (destrucción de piedras) dentro del conducto biliar y/o pancreático, entro otros procedimientos.

                    En algunas ocasiones no se logra entrar en la vía biliar para realizar las maniobras programadas. Puede ser debido a anomalías anatómicas (divertículos duodenales), operaciones anteriores sobre el estómago o simplemente por anatomía de cada individuo, en cuyo caso habrá que emplear otras alternativas terapéuticas.



                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan, y la exploración no produce daños o efectos secundarios indeseables. Pero en otros casos no es así, por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso médico ambulatorio.

                    La mayoría son leves y producidas por reacciones no deseadas por la medicación administrada, durante el gesto anestésico. Otras son dolor abdominal y distensión abdominal. Otras complicaciones menores y mucho menos frecuentes son roturas dentales, mordedura de lengua, luxaciones mandibulares o afonía.

                    Pueden producirse reacciones adversas a la medicación administrada (Rash, edema, Prurito, angioedema, espasmos de glotis, etc) que suelen ser leves sin repercusión alguna.


                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
            </tr>
            <tr>
                <td style="text-align:justify;">La inflamación pancreática (pancreatitis) por la irritación del páncreas o la infección de la bilis (colangitis). Si se realiza esfinterectomía puede ocurrir también hemorragia y más raramente, perforación o rotura de la pared duodenal, o atascamiento de un cálculo grande que no puede sacarse.

                    También son complicaciones graves: reacciones alérgicas medicamentosas, alteraciones cardiopulmonares y transmisión de infecciones. De presentarse alguna de ellas, obligan a un tratamiento específico y pueden requerir ingreso en el hospital. Ocasionalmente pueden requerir un tratamiento urgente e incluso una intervención quirúrgica.

                    Mortalidad: algunas complicaciones pueden seguir una evoluciónón fatal. Es una eventualidad excepcional pero puede suceder.

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
                <td style="font-weight: bold;">19. ALTERNATIVAS AL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Actualmente, mediante técnicas de imagen como la resonancia o ecoendoscopia biliopancreática, es posible aclarar muchas enfermedades a éste nivel, de forma que la indicación de esta exploración (C.P.R.E.) es mucho más exacta y frecuentemente va encaminada a hacer alguna forma de tratamiento endoscópico. En algunos casos existe la alternativa de realizar las radiografías y el tratamiento a través de la piel, pinchando directamente por el hígado (colangiografía transparieto hepática o C.T.H.) o por Ultrasonido Endoscopico (Ecoendoscopia), estas técnicas pueden ser necesarias en situaciones muy concretas; por otro lado la alternativa terapéutica sobre la vía biliar y/o pancreática es la cirugía, que tiene considerablemente más riesgos y posibles complicaciones.
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">20. DESCRIPCIÓN DEL MANEJO POSTERIOR AL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td style="text-align:justify;">Una vez finalizado el procedimiento medico ambulatorio, permanecerá durante aproximadamente 2-3 horas en observación para monitorear la evolución del paciente después del procedimiento médico ambulatorio.

                    Después de éste tiempo, el paciente deberá retornar a su lugar de origen (domicilio, hospital), teniendo en consideración que el aire que se usa para insuflar el estómago y el intestino durante el procedimiento puede causar distensión abdominal y eliminación de gases durante aproximadamente 24 horas. Puede presentar también molestias orofaríngeas (dolor de garganta) que pueden perdurar hasta 3 o 4 días.

                    Se recomienda no ingerir líquidos ni alimentos de ninguna clase, hasta que su médico se lo ordene, en caso de ser un paciente ambulatorio, deberá acudir al día siguiente a nuestro centro para realizarse exámenes de laboratorio de control y una valoración clínica, en función de los hallazgos se determinará si está apto para iniciar ingesta de alimentos. Tampoco debe ingerir medicamentos anti inflamatorios, no esteroides (ibuprofeno, naproxeno), antiagregantes o anticoagulantes hasta transcurridas 72 horas del procedimiento, salvo que un médico de nuestro equipo médico determine lo contrario.

                    Luego del procedimiento, el paciente es trasladado a la sala de recuperación en donde después de recuperar su nivel de conciencia de la sedación y comprobada su recuperación, puede ser dado de alta con su acompañante.

                    En caso de presentar los siguientes signos de alarma deberá comunicarse en forma inmediata con nosotros: dolor abdominal intenso con náuseas y vómitos, fiebre mayor o igual a 38ºC o deposiciones negruzcas o sanguinolentas.

                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">21. CONSECUENCIAS POSIBLES SI NO SE REALIZA EL PROCEDIMIENTO:</td>
            </tr>
            <tr>
                <td>Imposibilidad de solucionar la causa de las molestias, agravamiento de los síntomas e incluso la muerte</td>
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
                <td style="text-align:justify;">He facilitado la información completa que conozco, y que me ha sido solicitada, sobre los antecedentes personales, familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos pueden afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento médico ambulatorio que se me ha propuesto; he sido informado de las ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qué consiste, los beneficios y posibles riesgos del procedimiento médico ambulatorio. He escuchado, leído y comprendido la información recibida y se me ha dado la oportunidad de preguntar sobre el procedimiento. He tomado consiente y libremente la decisión de autorizar el procedimiento, en consecuencia, libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y personal paramédico que participen en el proceso. Consiento que durante la exploración, me realicen otro procedimiento adicional, si es considerado necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo retirar mi consentimiento cuando lo estime oportuno.</td>
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
                <td style="text-align:justify;">SI EL PACIENTE NO ENTIENDE LA LENGUA CASTELLANA
                    Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.

                </td>
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
            <tr>
                <th class="th">Nombres y Apellidos del paciente</th>
                <th class="th">Pasaporte No.</th>
                <th class="th">Firma del paciente o Huella, según el caso</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
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
