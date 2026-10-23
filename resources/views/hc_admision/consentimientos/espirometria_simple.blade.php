<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>ESPIROMETRIA SIMPLE Y CON BRONCODILATADORES</title>
    <style>
        * {
            font-size: 12px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;
            margin-bottom: 15px;

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
            border: 1px solid black;
            padding: 10px;
        }
    </style>

</head>

<body>
    <div id="container">

        <div>
            <p style="text-align: center;">
                CONSENTIMIENTO INFORMADO PARA PROCEDIMIENTOS MEDICOS
                AMBULATORIALES –ESPIROMETRIA SIMPLE Y CON BRONCODILATADORES
            </p>
        </div>
        <div>
            <p style="margin-bottom: 8px;">
                NOMBRE DEL ESTABLECIMIENTO DE SALUD:
                <span style=" text-decoration: underline;"> INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS
                    GASTROCLINICA S.A.</span>
            </p>
            <p>
                SERVICIO DEL ESTABLECIMIENTO:
                <span style=" text-decoration: underline;">CONSULTAS MEDICAS
                    ESPECIALIZADAS Y PROCEDIMIENTOS MEDICOS AMBULATORIALES POR VIAS ENDOSCOPICAS </span>
            </p>


            <p>
                NUMERO DE HISTORIA CLINICA DEL PACIENTE: ………………………………
            </p>
            <p>
                <span> FECHA: </span> <span style="margin-left:100px;"> HORA:</span>
            </p>
        </div>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL TESTIGO</th>
                    <th class="th">EDAD/FECHA</th>
                    <th class="th">TELEFONOS</th>
                    <th class="th">CEDULA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                    <td class="td">{{$paciente->fecha_nacimiento}}</td>
                    <td class="td">{{$paciente->telefono1}}</td>
                    <td class="td">{{$paciente->id}}</td>
                </tr>
            </tbody>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">DIRECCION DOMICILIARIA DEL PACIENTE</th>
                    <th class="th">CIUDAD</th>
                    <th class="th">PROVINCIA</th>
                    <th class="th">PAIS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->direccion}}</td>
                    <td class="td">{{$paciente->ciudad}}</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">TESTIGO</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">TELEFONOS</th>
                    <th class="th">CEDULA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">DIRECCION DOMICILIARIA DEL TESTIGO</th>
                    <th class="th">CIUDAD</th>
                    <th class="th">PROVINCIA</th>
                    <th class="th">PAIS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">REPRESENTANTE LEGAL</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">TELEFONOS</th>
                    <th class="th">CEDULA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>


        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">DIRECCION DOMICIARIA DEL REPRESENTANTE LEGAL</th>
                    <th class="th">CIUDAD</th>
                    <th class="th">PROVINCIA</th>
                    <th class="th">PAIS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        @php $datosDoctor = Sis_medico\User::where('id',$agenda->doctor1->id)->first(); @endphp
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL PROFESIONAL DE LA SALUD</th>
                    <th class="th">LIBRO</th>
                    <th class="th">FOLIO</th>
                    <th class="th">NUMERO</th>
                    <th class="th">TELEFONOS</th>
                    <th class="th">CEDULA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">@if(!is_null($agenda->doctor1)) {{$agenda->doctor1->nombre1}} {{$agenda->doctor1->nombre2}} {{$agenda->doctor1->apellido1}} {{$agenda->doctor1->apellido2}} @endif</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">@if(!is_null($datosDoctor)) {{$datosDoctor->telefono1}} @endif</td>
                    <td class="td">@if(!is_null($datosDoctor)){{$datosDoctor->id}} @endif</td>
                </tr>
            </tbody>
        </table>

        <div>
            <p style="margin-bottom: 8px;">
                <b>TIPO DE ATENCION:</b><span style="text-decoration: underline;"> AMBULATORIAL</span>
            </p>
            <p>
                <b>NOMBRE DEL DIAGNOSTICO:</b>
            </p>


            <p>
                <b> NOMBRE DEL PROCEDIMIENTO RECOMENDADO: </b> <span style="text-decoration: underline;background-color:yellow">ESPIROMETRIA SIMPLE Y
                    CON BRONCODILATADORES</span>
            </p>
        </div>
        <div>

            <p>
                <b>DEFINICION</b>:. Es un procedimiento que, mediante la introducción en la boca
                de tubo (boquilla), permite determinar como ventilan sus pulmones, determinando
                específicamente la capacidad pulmonar y si hay obstrucción de las vías aéreas,
                con la finalidad de realizar diagnósticos oportunos y definir tratamientos
            </p>

            <p>
                <b>EN QUE CONSISTE?</b>Como primer paso y con el paciente sentado, se coloca una
                pinza de oclusión nasal por lo que se indica al paciente respirar por la boca.
                Posteriormente, se introduce la boquilla al paciente y se indica que debe respirar normal,
                posterior a 3
            </p>

        </div>
    </div>
    <div style="page-break-before: always;">
    </div>
    <div id="container">
        <div>
            <p>
                respiraciones debe tomar todo el aire que pueda y expulsarlo de manera rápida, fuerte
                y sostenida por al menos 4 segundos. Al llegar a este tiempo, debe tomar el aire
                profundamente. Esta maniobra debe ser repetida mínimo en 3 oportunidades y máximo de 8. Al
            </p>

            <p>
                <b>COMO SE REALIZA?</b>
            </p>
            <p>
                El una consulta previa el médico tratante le explicará al paciente en que consiste la prueba y que
                será agendado para la misma El día previo al procedimiento el paciente debe suspender toda
                medicación inhalada (oral y nasal) y anti inflamatoria de las vías respiratorias. No se requiere
                realizar exámenes de laboratorio, valoración cardiovascular, espirometría, RX de Tórax
                y tomografía de tórax , deberá también poner en conocimiento del médico todos sus antecedentes
                patológicos personales, en caso de enfermedades previas.
                El día del procedimiento ingresara a un área de pruebas funcionales. Sele tomaran los datos
                personales del paciente para registrarlos en el equipo donde se realizará la prueba, luego se le
                explicará la maniobra la cual consiste en la colocación de una pinza en la nariz, la toma de aire
                a través de la boca y la expulsión rápida y sostenida del aire por la boquilla. Esta maniobra será
                repetida como mínimo en 3 oportunidades y máximo de 8. Posterior a estas maniobras
                se aplicará 400 mcg de Salbutamol inhalado (4 inhalaciones) y se repetirá la maniobra 15 minutos
                despúes
            </p>
            <p>
                <b>
                    GRAFICO DE LA INTERVENCION (incluya un gráfico previamente seleccionado que facilite
                    la comprensión al paciente)
                </b>
            </p>

            <p>
                <img style="height:200;width:300px" src="{{public_path('/imagenes/broncodilatadores.png')}}" /></td>
            </p>

            <p>

                <span style="font-weight: bold;color:red;">DURACION ESTIMADA DEL PROCEDIMIENTO MEDICO AMBULATORIAL</span>

            </p>

            <p>
                <b>
                    Tiempo aproximado de duración del procedimiento:
                </b>
            </p>
            <p>
                Los estudios <span style="text-decoration: underline;">funcionales de tipo diagnostico</span> pueden durar aproximadamente <span style="text-decoration: underline;">entre 20-30 minutos.</span>
            </p>

            <p>
                <span style="font-weight: bold;">Tiempo de recuperación</span>
            </p>
            <p>
                El procedimiento no contempla tiempo de recuperación
            </p>


            <p>

                <span style="font-weight: bold;color:red;">BENEFICIOS DEL PROCEDIMIENTO MEDICO:</span>

            </p>
            <p>
                El estudio permite la valoración de la capacidad respiratoria, determinando si existe obstrucción
                de los bronquios o restricción de la capacidad pulmonar que permita formular un diagnóstico y
                tratamiento precoz
            </p>

            <p>

                <span style="font-weight: bold;color:red;">RIESGOS FRECUENTES (pocos graves): </span>mareo, tos, dolor de cabeza, expectoración, estornudos,
                congestión nasal

            </p>
        </div>
    </div>
    <div style="page-break-before: always;">
    </div>
    <div id="container">
        <div>

            <p>

                <span style="font-weight: bold;color:red;">RIESGOS FRECUENTES (pocos graves): </span>neumotórax, síncope, aumento presión intraocular,
                dolor torácico
            </p>

            <p>
                <span style="font-weight: bold;color:red;">DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad, estado de salud, creencias, valores, etc):</span>La morbimortalidad asociada al procedimiento es
                de 0,01%. La evaluación previa del paciente en consulta reducen los riesgos notablemente. Se debe
                posponer el estudio hasta que las condiciones del paciente sean las ideales para el estudio
            </p>

            <p>
                <span style="font-weight: bold;color:red;">ALTERNATIVAS AL PROCEDIMIENTO:</span>
            </p>

            <p>
                Entre las alternativas al procedimiento se encuentran: valoración de volúmenes pulmonares,
                difusión alveolo capilar de monóxido de carbono, oscilometría de impulso. Otra alternativa
                es indicar al paciente médico tratamiento empírico
            </p>


            <p>
                <span style="font-weight: bold;color:red;">DESCRIPCION DEL MANEJO POSTERIOR AL PROCEDIMIENTO:</span>
            </p>

            <p>
                Luego del procedimiento, el paciente puede egresar del área de procedimientos y dirigirse a su
                domicilio sin necesidad de tener acompañantes
            </p>

            <p>
                <span style="font-weight: bold;color:red;">CONSECUENCIAS POSIBLES SI NO SE REALIZA EL PROCEDIMIENTO:</span> Mal diagnóstico, diagnostico errado o diagnóstico tardío de enfermedades del aparato respiratorio bajo
            </p>

            <p>
                <span style="font-weight: bold;color:red;">SEDACION:</span> no requiere sedación
            </p>
            <p>
                El que suscribe, _____________{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}_______________________ con cedula de
                ciudadanía No. {{$paciente->usuario->id}}, en pleno uso de mis facultades mentales, declaro que el DR.
                EFRAIN JOSE SANCHEZ ANGARITA CON REGISTRO SANITARIO No.
                121635990 parte del personal médico del INSTITUTO ECUATORIANOD E ENFERMEDADES DIGESTIVAS GASTROCLINICAS S.A) me ha explicado de forma
                satisfactoria la naturaleza y fines del procedimiento de Espirometría simple y con
                broncodilatadores, así como los riesgos existentes, las posibles molestias y complicaciones, de un
                evento que es el más adecuado para mi situación clínica actual. Comprendo que alguna de las
                complicaciones posibles puede requerir observación médica, así como las consecuencias
                previsibles de su realización. También se me ha explicado sobre las posibles alternativas a la
                conducta medica propuesta, siendo absueltas todas mis inquietudes y preguntas de forma
                satisfactoria.
            </p>
            <p>
                Entiendo que, en el curso del procedimiento médico, pueden presentarse situaciones imprevistas
                que requieran procedimientos diferentes a los planificados y que podían ocasionar inclusive la muerte
                como posibilidad remota. También entiendo que no se me han dado garantías de que se puedan
                conseguir los objetivos diagnósticos o terapéuticos previstos. También sé que puedo retirar
                este consentimiento cuando lo desee, antes o durante la intervención, sin que ello se menoscabe la
                atención medica prestada.
            </p>
            <p>
                Declaro que he comprendido perfectamente todo lo anteriormente expuesto, que he podido
                aclarar las dudas planteadas, <b>por lo que doy mi consentimiento </b> para que se realice
                el procedimiento medico antes mencionado y para que así conste, firmo el presente documento
                después de haberlo leído detenidamente, liberando de toda responsabilidad civil, penal, o
                administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible,
                incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y
                personal paramédico que participen en el proceso.
            </p>
            <p>
                Además con plena voluntad y conciencia, doy mi Consentimiento para que se me realice
                <span style="text-decoration: underline;">los procedimientos funcionales respiratorios adicionales, entre los más frecuentes que se
                    podrían presentar en caso necesario y según la condición médica del paciente están:</span>
                determinación de oxido nítrico exhalado bronquial, nasal o y que contribuyan a mejorar mi
                salud, por lo tanto, expresamente LIBERO DE TODA RESPONSABILIDAD al
                <b>Dr. EFRAIN JOSE SANCHEZ ANGARITA con registro sanitario No.121635990 y al INSTITUTO
                    ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLÍNICA S. A. </b>,
                así como a todo su personal médico y paramédico, por cualquier contingencia que se presente en
                mi salud o en mi vida, por la aplicación del tratamiento antes señalado.
            </p>
        </div>
    </div>
    <div style="page-break-before: always;">
    </div>
    <div id="container">
        <div>
            <p>
                <span style="text-decoration: underline;font-weight:bold">Declaración de Consentimiento Informado</span> <span style="margin-left:250px;font-weight:bold">Fecha</span> <span style="margin-left:50px;font-weight:bold">Hora</span>
            </p>

            <p>
                He facilitado la información completa que conozco, y me ha sido solicitada sobre los
                antecedentes personales, familiares y de mi estado de salud. Soy consciente de que
                omitir estos datos puede afectar los resultados del tratamiento. Estoy de acuerdo con los
                procedimientos médicos ambulatoriales que se me ha propuesto; he sido informado de las
                ventajas e inconvenientes del mismo; se me ha explicado de forma clara en que consiste,
                los beneficios y los posibles riesgos de los procedimientos médicos ambulatoriales. He
                tomado consciente y libremente la decisión de autorizar el procedimiento. Consiento que,
                durante la intervención, me realicen otro procedimiento adicional, si es considerado necesario
                según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo
                retirar mi consentimiento cuando lo estime oportuno.
            </p>
            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre Completo del paciente segun el caso</span> <span style="text-decoration: overline;margin-left:80px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del paciente o huella</span>
            </p>
            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre del Medico profesional</span> <span style="text-decoration: overline;margin-left:140px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma,sello y código del Medico</span>
            </p>
            <p>
                <b>Si el paciente no está en capacidad de firmar el consentimiento informado:</b>
            </p>
            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre Completo del representante legal</span> <span style="text-decoration: overline;margin-left:80px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del representante legal</span>
            </p>
            <p>
                <b>(Si el paciente es menor de edad o presenta una incapacidad: Firma del padre/s,
                    tutor o encargado, firma por el paciente y firma del Representante Legal)</b>
            </p>
            <p>
                <b>
                    Parentesco con el Paciente: __________________________________
                </b>
            </p>
            <span style="text-decoration: underline;font-weight:bold">NEGATIVA DEL CONSENTIMIENTO INFORMADO</span> <span style="margin-left:350px;font-weight:bold">Fecha</span> <span style="margin-left:50px;font-weight:bold">Hora</span>
            </p>
            <p>
                Una vez que he entendido claramente los procedimientos médicos ambulatoriales
                (ESPIROMETRIA SIMPLE Y CON BRONCODILATADORES) propuestos, así como las
                consecuencias posibles si no se realiza la intervención, no autorizo y me niego a que se me
                realice el procedimiento propuesto y libero de responsabilidades futuras de cualquier índole
                al establecimiento de salud y al profesional sanitario que me atiende por no realizar la
                intervención sugerida.
            </p>
        </div>
    </div>
    <div style="page-break-before: always;">
    </div>
    <div id="container">

        <div>
            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre Completo del paciente</span> <span style="text-decoration: overline;margin-left:80px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del paciente o huella, según el caso</span>
            </p>

            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre del Médico</span> <span style="text-decoration: overline;margin-left:135px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma, sello y código profesional del medico</span>
            </p>
            <p>
                <b>
                    (Si el paciente es menor de edad o presenta una incapacidad: Firma del padre/s, tutor
                    o encargado, firma por el paciente y firma del Representante Legal)
                </b>
            </p>

            <p style="margin-top: 50px;">
                <span style="text-decoration: overline;">Nombre Completo del Representante Legal</span> <span style="text-decoration: overline;margin-left:135px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del representante legal</span>
            </p>

            <p>
                <b>
                    Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:
                </b>
            </p>
            <p style="margin-top: 50px;">
                <span style="text-decoration: overline;">Nombre Completo del testigo</span> <span style="text-decoration: overline;margin-left:80px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:100px">Firma del testigo</span>
            </p>

            <hr>

            <p>
                <b style="text-decoration: underline;">REVOCATORIA DE CONSENTIMIENTO INFORMADO</b>
            </p>

            <p>
                De forma libre y voluntaria, revoco el consentimiento realizado en fecha _______________ y
                manifiesto expresamente mi deseo de no continuar con los procedimientos médicos
                ambulatoriales y que doy por finalizado en esta fecha ___________________
                Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al
                profesional sanitario que me atiende.
            </p>

            <p style="margin-top:50px;">
                <span style="text-decoration: overline;">Nombre Completo del paciente</span> <span style="text-decoration: overline;margin-left:80px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del paciente o huella, según el caso</span>
            </p>

            <p>
                <b>Si el paciente no está en capacidad de firmar la revocatoria del consentimiento informado: </b>
            </p>

            <p style="margin-top: 50px;">
                <span style="text-decoration: overline;">Nombre Completo del Representante Legal</span> <span style="text-decoration: overline;margin-left:135px">Cedula de ciudadanía</span> <span style="text-decoration: overline;margin-left:80px">Firma del representante legal</span>
            </p>

        </div>
    </div>

</body>

</html>
