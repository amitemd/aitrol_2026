<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>CONSENTIMIENTO INFORMADO PARA PHMETRIA ESOFAGICA MEDIANTE CAPSULA</title>
    <style>
        * {
            font-size: 12px;
            padding: -1px;

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
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA PHMETRIA ESOFAGICA MEDIANTE CAPSULA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">2. NOMBRE DEL ESTABLECIMIENTO DE SALUD: </td>
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
        <table style="background :#C2BCBA  ;width:100%;text-align:center;border: 1px solid black">
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
                    7. NOMBRE DEL DIAGNÓSTICO (CODIFICACIÓN CIE10):
                </td>
                <td style="width: 70%;" class="td">&nbsp;</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    8. TIPO DE ATENCIÓN
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
                    <td class="td">La técnica a la que usted va a someterse consiste en medir la acidez en el esófago durante un día completo mediante un sistema de registro portátil. Sirve para cuantificar el ácido que existe en el esófago, el cual produce síntomas como ardor, subida de contenido líquido hacia la garganta (regurgitación) o dolor torácico de causa no cardiológica.</td>
                    <td><img style="margin:6px;margin-top:10px;height: 90px;width:80px;" src="{{public_path('/imagenes/phmetria_cap.png')}}" />

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
                    <td style="text-align:justify;"  class="td">SA través de una endoscopia digestiva alta se introduce una capsula que se adhiere al esófago y queda posicionada a este nivel durante 48 horas, y registra durante este tiempo el PH esofágico. Esta capsula se conectará a un registrador vía Wireless, usted lo llevará a como cartera. Deberá realizar una vida lo más normal posible. Luego de 48 horas entregará el resgitrador. La cápsula expulsará por las heces fecales en máximo 5 días.La endoscopia digestiva alta se realiza en una posición cómoda, en una camilla sobre el lado izquierdo.  Durante ese tiempo se puede respirar sin problema por la nariz o por la boca. Se le colocará un protector dental a través del cual se pasará el endoscopio. Se le aplicará un sedante inyectado. Durante la exploración usted no sentirá ninguna molestia. Una vez finalizada, puede quedar cierta sedación residual durante varias horas. La exploración se realiza con diferentes grados de sedación que se consigue mediante la administración de fármacos en dosis adecuada para cada paciente, siendo el anestesiólogo y su equipo los encargados de realizar y controlar mediante la monitorización correspondiente el proceso de sedación, con el propósito de proporcionar un estado confortable sin dolor, teniendo en cuenta que no siempre es posible predecir el punto de transición entre la sedación moderada y la profunda o la anestesia general, por lo que se recomienda siempre acudir acompañado para la realización del estudio antes mencionado. De la misma manera se recomienda permanecer en compañía durante las siguientes doce horas, no pudiendo reincorporarse a las actividades habituales o conducir hasta pasado este periodo. A veces, durante la exploración, se producen hallazgos imprevistos que pueden obligar a tener que modificar la forma de hacer la exploración y utilizar variantes de la misma, no contempladas inicialmente. Puede ser necesario tomar muestras biológicas para estudiar mejor su caso.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    13. DURACIÓN ESTIMADA DE LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td">Aproximadamente 30 minutos (excepcionalmente el tiempo puede ser mayor).</td>
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
                    <td style="text-align: justify;" class="td">Con la PHmetría esofágica de 48 horas se puede diagnosticar o descartar la presencia de reflujo ácido como causa de enfermedades que afectan al esófago, garganta y vías respiratorias.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    15. RIESGOS FRECUENTES
                    (POCO GRAVES)
                </td>
                <td  class="td">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan, y la exploración endoscópica para introducir la capsula no produce daños o efectos secundarios indeseables. Pero a veces no es así. Por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o exploración. Las complicaciones de la PHmetría esofágica por capsula son excepcionales.  Habitualmente son leves y derivadas de la adhesión de la capsula a la pared del esófago. Puede haber sangrado digestivo lo cual es muy poco frecuente. </td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    16. RIESGOS INFRECUENTES
                    (GRAVES)
                </td>
                <td style="width: 70%;" class="td">Excepcionalmente puede ocasionarse una hemorragia digestiva alta por el desprendimiento de la cápsula de la pared esofágica. Se deben considerar también los problemas de salud que presente cada paciente para las exploraciones que se vayan a realizar.</td>
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
        <table class="table" style="width: 100%;text-align:center;">
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
                <td style="width: 70%;" class="td"> La radiografía con contraste o la endoscopia digestiva alta pueden dar alguna información sobre la presencia de reflujo gastroesofágico, pero no son un sustituto de la PHmetría de 24 horas.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    19. MANEJO POSTERIOR AL PROCEDIMIENTO
                </td>
                <td style="width: 70%;" class="td">Después del procedimiento el paciente retornará inmediatamente a su actividad rutinaria. </td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    20. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td"> Mal diagnóstico, diagnostico errado o diagnóstico tardío de enfermedades del tracto digestivo alto.</td>
            </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;">21. DECLARACIÓN DEL CONSENTIMIENTO INFORMADO</td>
                <td class="td" >Fecha</td>
                <td style="width: 40px;" class="td" >&nbsp;</td>
                <td class="td" >Hora</td>
                <td style="width: 40px;" class="td">&nbsp;</td>
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
                <td class="td" >Fecha</td>
                <td style="width: 40px;" class="td" >&nbsp;</td>
                <td class="td" >Hora</td>
                <td style="width: 40px;" class="td">&nbsp;</td>
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
                <td class="td" >Fecha</td>
                <td style="width: 40px;" class="td" >&nbsp;</td>
                <td class="td" >Hora</td>
                <td style="width: 40px;" class="td">&nbsp;</td>
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
