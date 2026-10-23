<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
$marvi = strtolower ($empresa->razonsocial);
@endphp

<head>
    <meta charset="utf-8" />
    <title>AUTORIZACION DE USO DE IMAGEN E INFORMACION ENDOSCOPICA</title>
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
            background: #C2BCBA;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
            overflow: hidden;

        }

        .mayusculas {
            text-transform: uppercase;
        }
    </style>

</head>

<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <!-- @if($empresa->id == '0992704152001')
            <tr>
                <td class="td" style="font-weight: bold;"><b>INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A.</b></td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold; height: 18px;">&nbsp;</td>
            </tr>
            @endif -->
            <tr>
                <td class="td" style="font-weight: bold;">{{$empresa->razonsocial}}</td>
            </tr>
            <tr>
                <td style="height: 25px;"></td>
            </tr>
            <tr>
                <td class="td" style="font-weight: bold;">AUTORIZACION DE USO DE IMAGEN E INFORMACION ENDOSCOPICA</td>
            </tr>
            <tr>
                <td style="height: 25px;"></td>
            </tr>
            <tr>
                <td style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">INFORMACION AL PACIENTE</td>
            </tr>
            <tr>
                <td>
                De conformidad con el artículo 362 de la Constitución de la República del Ecuador, que garantiza el consentimiento informado, así como el artículo 66, 
                numeral 19 de la misma Ley Suprema, relativo al acceso, confidencialidad y uso de la información del paciente, en armonía con los artículos 201, 207 y 208
                de la Ley Orgánica de la Salud sobre la responsabilidad de los profesionales de la salud de brindar atención de calidad con calidez y eficacia, buscando el
                mayor beneficio para la salud de sus pacientes, respetando los derechos humanos y los principios bioéticos, promoviéndose la investigación científica y tecnológica, 
                con sujeción a principios bioéticos y de derechos, previo consentimiento informado y por escrito respetando la confidencialidad.  Es en virtud de estos artículos,
                sobre el carácter confidencial y la integridad en el manejo de la salud, y considerando además que el uso de la imagenología sólo puede ser para los fines requeridos 
                a la atención del paciente, pudiendo ser además parte en análisis investigativos o estudios científicos, constituyendo potestad  privativa del usuario o representante 
                legal del paciente dar o no la autorización para la utilización o manejo de los documentos de imágenes médicas, y conforme a las Normas invocadas se mantendrá protegida 
                la identidad acorde a los principios médicos y al derecho de confidencialidad garantizados en la Constitución, en la Ley Orgánica de la Salud y demás leyes rectoras del área de la salud. 
                A su vez se autoriza expresamente el tratamiento de los datos personales relacionados a la atención y/o servicio al que acceda quien suscriba el presente acuerdo, principalmente pero sin limitarse,
                para la indexación en bases de datos, el uso como fuente de información para sistemas y/o programas informáticos de cualquier tipo y en general cualquier tratamiento que tenga relación con la actividad de quien provee los servicios a los que accede quien suscribe el presente documento.

                </td>
            </tr>
            <tr>
                <td style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">INFORMACION DEL PACIENTE</td>
            </tr>
            <tr>
                <td style="height: 25px;"></td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td>
                    Yo, <b> {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}} </b> con cédula de identidad No. <b>{{$paciente->id}}</b> autorizo al @if($marvi == '0992704152001') <b> INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLÍNICA S.A. </b> @else <b> <span class="mayusculas"> {{$marvi}} </span> </b> @endif y a sus profesionales, con fines exclusivos científicos y de educación,
                    la utilización de las imágenes endoscópicas, muestras biológicas y patológicas protegidas que me han realizado.
                    He comprendido que los videos e imágenes realizados por  @if($marvi == '0992704152001') <b> INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLÍNICA S.A. </b> @else <b> <span class="mayusculas"> {{$marvi}} </span> </b> @endif serán de su permanencia y se reserva el derecho a hacer uso de investigaciones o capacitaciones médicas,
                    sin revelar la identidad del paciente, salvaguardando en todo momento mi integridad física y moral.
                    De igual manera es mi deseo establecer que esta autorización es voluntaria y gratuita y que de acuerdo a lo señalado en el Código Orgánico de la Economía Social de los Conocimientos,
                    esta institución cuenta con mi autorización para la utilización, reproducción, transmisión y retransmisión de mi imagen protegida de los estudios médicos realizados.
                    A su vez se autoriza expresamente el tratamiento de los datos personales relacionados a la atención y/o servicio al que acceda quien suscriba el presente acuerdo,
                    principalmente pero sin limitarse, para la indexación en bases de datos, el uso como fuente de información para sistemas y/o programas informáticos de cualquier tipo y
                    en general cualquier tratamiento que tenga relación con la actividad de quien provee los servicios a los que accede quien suscribe el presente documento.
                    <br>
                    @if($marvi == '0992704152001') <b> INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLÍNICA S.A. </b> @else <b> <span class="mayusculas"> {{$marvi}} </span> </b> @endif garantiza la confidencialidad de mi información.
                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;background : #C2BCBA;width:20%">Ciudad :</td>
                <td class="td"></td>
                <td class="td" style="font-weight: bold;background : #C2BCBA;width:20%">Fecha: :</td>
                <td class="td"></td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
            </tr>
            <tr>
                <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                <td class="td">{{$paciente->id}}</td>
                <td class="td">&nbsp;</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td>
                    SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad):
                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL </th>
                    <th class="th">CÉDULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL REPRESENTANTE O HUELLA DACTILAR</th>
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
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
                    <th class="th">CÉDULA DE CIUDADANIA</th>
                    <th class="th">FIRMA , SELLO Y CODIGO DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
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
</body>

</html>