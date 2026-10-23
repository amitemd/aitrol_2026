<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>ANEXO INFORMATIVO ANASTESICO</title>
    <style>
        * {
            font-size: 12px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;
            text-align: center;

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
    </style>

</head>

<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">1. ANEXO INFORMATIVO DEL CONSENTIMIENTO INFORMADO EN PROCEDIMIENTO ANESTÉSICO</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tbody>
                <tr>
                    <td>
                        <img style="margin:6px;margin-top:10px;height: 100px;width:280px;" src="{{public_path('/imagenes/anexo_anestesico.png')}}" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        FECHA:
                    </td>
                    <td style="width: 70%;" style="font-weight: bold;" class="td">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        NOMBRE COMPLETO DEL PACIENTE:
                    </td>
                    <td style="width: 70%;font-weight:bold" class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                </tr>
                <tr>

                    <td style="width: 30%;font-weight: bold;" class="td">
                        PROCEDIMIENTO ANESTÉSICO PARA:
                    </td>
                    <td style="width: 70%;" class="td"></td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td style="border-right: 1px solid black;">
                    Tipo de Anexo:
                </td>
                <td style="border-right: 1px solid black;">
                    Adulto mayor
                </td>
                <td style="border-right: 1px solid black;width:20%">
                    &nbsp;
                </td>
                <td style="border-right: 1px solid black;">
                    Niños y Adolescentes
                </td>
                <td style="border-right: 1px solid black;width:20%">
                    &nbsp;
                </td>
                <td>
                    Personas Discapacitadas
                </td>
                <td style="border-right: 1px solid black;">

                </td>
                <td style="width:20%">
                    &nbsp;
                </td>
            </tr>
        </table>
        <table style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO PARA ADULTOS MAYORES</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td>
                    Los artículos 35, 36 y 362 de la Constitución de la República del Ecuador, en concordancia con los artículos 4, 35, 36 y 37 de la Ley Orgánica de las Personas Adultas Mayores, como titulares de derechos y garantías, recibirán atención prioritaria y especializada como el aval que debe tener todo paciente a ser informado, para que pueda dar su consentimiento en todos los servicios médicos, tanto públicos como privados. La transmisión de esta información se la hará siempre atendiendo a sus necesidades comunicacionales de manera comprensible, en el idioma según la identidad cultural de la persona adulta mayor y si se requiere de los servicios de un o una traductora para tal fin.
                    He sido informado claramente del procedimiento que me van a realizar y autorizo a efectuar el mismo y en caso de no estar en esa capacidad, la persona que me acompaña o represente, sea o no familiar, concederá dicha autorización para el caso que tuviera dificultades motrices o algún otro impedimento para suscribir o autorizar el documento pertinente.
                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANÍA</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
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
        <table style="background : #9BC2E6 ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO DE NIÑOS Y ADOLESCENTES </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td>
                    Tengo derecho a estar informado y me ampara la Constitución de la República del Ecuador en sus artículos 32 y 362; y, el Código de la Niñez y la Adolescencia que, en su artículo 11 vela por el interés superior del niño para satisfacer el ejercicio efectivo de nuestros derechos, en armonía con el artículo 27 de la Ley ibídem, que garantiza nuestro derecho a disfrutar el más alto nivel de salud física, mental, psicológica y sexual.
                    El doctor me ha explicado e ilustrado el procedimiento que me va a realizar para, a través de la anestesia, lograr que me duerma y así proporcionarme un estado confortable y sin dolor durante el procedimiento que se me va a efectuar.
                    Mi papi o mi mami o mi representante autorizarán la realización de este procedimiento.


                </td>
            </tr>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANÍA</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
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
        <table style="background : #A9D08E ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO DE PERSONAS DISCAPACITADAS</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td>
                    El artículo 35 en concordancia con el artículo 47 de la Constitución de la República del Ecuador, que garantizan los derechos y avalar la atención prioritaria de las personas con discapacidad, así como el artículo 362 de la misma Ley Suprema, que estatuye la garantía del consentimiento informado, el acceso a la información y la confidencialidad que merecen los pacientes. Es en virtud de los artículos aquí invocados que se creó este Anexo Informativo para, por medios idóneos, dar a conocer y se dé seguridad con el cumplimiento de los procedimientos médicos a realizarse.
                    He sido informado claramente, por medios idóneos, del procedimiento que me van a realizar y autorizo a efectuar el mismo y en caso de no estar en esa capacidad, la persona que me acompaña o represente, sea o no familiar, concederá dicha autorización para el caso que tuviera dificultades motrices o algún otro impedimento para suscribir o autorizar el documento pertinente.

                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">CÉDULA DE CIUDADANÍA</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
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
</body>

</html>