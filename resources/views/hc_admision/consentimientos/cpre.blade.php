<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>CRM - CONSENTIMIENTO CPRE-rev02</title>
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
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA : COLANGIOPANCREATOGRAFIA RETRÓGRADA ENDOSCÓPICA (CPRE / ERCP)</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">2. NOMBRE DEL ESTABLECIMIENTO DE SALUD: {{$empresa->razonsocial}}</td>
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
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">7.NOMBRE DEL DIAGNOSTICO :  </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    8.TIPO DE ATENCION
                </td>
                <td style="width: 70%;" class="td"></td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    9. NOMBRE DEL PROCEDIMIENTO RECOMENDADO
                </td>
                <td style="width: 70%;" class="td">{{$nombre->nombre}}</td>
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
                    <td class="td">Mediante un tubo flexible con un sistema de iluminación y una cámara (duodenoscopio), que se introduce a través de la boca se localiza el orificio de salida del conducto biliar y pancreático al duodeno (papila) y se inyecta por él un líquido (contraste para hacer radiografías). </td>
                    <td><img style="height:100px;margin:5px" src="{{public_path('/imagenes/cprm.png')}}" /></td>
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
                    <td style="text-align: justify;" class="td">En el área del procedimiento médico ambulatorio, el anestesiólogo procederá a administrar fármacos anestésicos, debiendo estar el paciente en posición decúbito dorsal (boca arriba) o supino (boca abajo). Se realizará con anestesia general, se le colocará un protector bucal y el médico iniciará el procedimiento introduciendo el endoscopio y haciéndolo progresar a través del esófago y el estómago hasta el duodeno (primera parte del intestino delgado). Se ubicará la ampolla de Vater y se introducirá una delgada sonda a través de la cual se administrará el medio de contraste. Posteriormente se procederá a realizar las distintas tomas radiológicas y tratamientos endoscópicos en función de los hallazgos.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    13. DURACIÓN ESTIMADA DE LA EXPLORACIÓN
                </td>
                <td style="width: 70%;" class="td">Aproximadamente de 45 a 60 minutos (excepcionalmente el tiempo puede ser mayor)</td>
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
                    <td style="text-align: justify;" class="td">Tratamiento endoscópicos de enfermedades en el conducto biliar o pancreático, por ejemplo, cálculos (litiasis) o estrechamientos de distintos orígenes, sangrados.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    15. RIESGOS FRECUENTES
                </td>
                <td style="width: 70%;" class="td">Reacciones adversas a la medicación administrada durante la anestecia Otras son dolor abdominal y distensión abdominal, roturas dentales, mordedura de lengua, luxaciones mandibulares o afonía</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    16. RIESGOS INFRECUENTES
                </td>
                <td style="width: 70%;" class="td">Irritación del páncreas o la infección de la bilis (colangitis), hemorragia y más raramente, perforación o rotura de la pared duodenal, reacciones alérgicas medicamentosas, alteraciones cardiopulmonares y transmisión de infecciones.</td>
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
                    <td style="width: 70%;" class="td"> Están las técnicas de imagen como la resonancia o ecoendoscopía biliopancreática, colangiografía transparieto hepática o C.T.H.</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        19. MANEJO POSTERIOR AL PROCEDIMIENTO
                    </td>
                    <td style="width: 70%;" class="td">   Se recomienda no ingerir líquidos ni alimentos de ninguna clase, hasta que su médico se lo ordene, en caso de ser un paciente ambulatorio, deberá acudir al día siguiente a nuestro centro para realizarse exámenes de laboratorio de control y una valoración clínica, en función de los hallazgos se determinará si está apto para iniciar ingesta de alimentos. Tampoco debe ingerir medicamentos anti inflamatorios, no esteroides (ibuprofeno, naproxeno), antiagregantes o anticoagulantes hasta transcurridas 72 horas del procedimiento, salvo que un médico de nuestro equipo médico determine lo contrario
                        En caso de presentar los siguientes signos de alarma deberá comunicarse en forma inmediata con nosotros: dolor abdominal intenso con náuseas y vómitos, fiebre mayor o igual a 38ºC o deposiciones negruzcas o sanguinolentas.</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        20. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN
                    </td>
                    <td style="width: 70%;" class="td"> Imposibilidad de solucionar la causa de las molestias, agravamiento de los síntomas e incluso la muerte.</td>
                </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;">21. DECLARACIÓN DEL CONSENTIMIENTO INFORMADO</td>
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
