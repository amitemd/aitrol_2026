<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
$minu = strtolower($empresa->razonsocial);
@endphp

<head>
    <meta charset="utf-8" />
    <title>Consentimiento informado Ecoendoscopia</title>
</head>
<style>
    * {
        font-size: 13px;

    }

    .table {
        border-collapse: collapse;
        text-align: center;


    }

    .table,
    .td {
        border: 1px solid black;
        padding: 2px;
    }

    .th {
        border: 1px solid black;
        text-align: center;
        padding: 3px;
    }

    .centrado {
        width: 250px;
    }

    table tr td {
        font-size: 13px;

    }

    table tr th {
        font-size: 13px;
        background: #C0BBBA;
    }

    #caja {
        border: 1px solid black;
    }
</style>

<body>
    <div id="content">

        <table class="table" style="width:100%;margin-top:10px ">
            <tr>
                <th class="th">
                    ENCUESTA SOBRE VALIDACIÓN DE COMPRENSIÓN DEL CONTENIDO DEL CONSENTIMIENTO INFORMADO.
                </th>
            </tr>
            <tr>
                <td class="td" style="text-align: justify;">
                    {{$minu}} (IECED) para cumplir con lo dispuesto en el numeral 2, del artículo 7.5, del Modelo de Gestión De Aplicación del Consentimiento Informado en Practica Asistencial, reglamentado en el Acuerdo Ministerial No. 5316, publicado en el Registro Oficial Edición Especial No. 510, del 22 de febrero del 2016, le consulta a usted lo siguiente:
                    Si una vez leído el Consentimiento Informado que le ha sido entregado por colaboradores del Dr. Carlos Antonio Robles Medranda que se encuentra interesado en conocer su opinión, puede indicar de manera simplificada, en el siguiente cuadro, si usted ha comprendido su contenido:
                </td>
            </tr>
        </table>
        <table class="table" style="width:100%;margin-top:10px ">
            <tr>
                <th class="th">
                    SI COMPRENDÍ EL CONTENIDO
                </th>
                <th class="th">
                    NO COMPRENDÍ EL CONTENIDO
                </th>
            </tr>
            <tr>
                <td class="td">
                    &nbsp;
                </td>
                <td class="td">
                    &nbsp;
                </td>
            </tr>

        </table>
        <table class="table" style="width:100%;margin-top:10px ">
            <tr>
                <th class="th">
                    NOMBRES Y APELLIDOS
                </th>
                <th class="th">
                    No. de cédula
                </th>
                <th class="th">
                    FIRMA
                </th>

            </tr>
            <tr>
                <td class="td">
                    &nbsp;
                </td>
                <td class="td">
                    &nbsp;
                </td>
                <td class="td">
                    &nbsp;
                </td>
            </tr>

        </table>
        <table class="table" style="width:100%;margin-top:10px ">
            <tr>
                <th class="th">
                    FECHA:
                </th>
                <th class="th">
                    NOMBRE DEL PROCEDIMIENTO:
                </th>
            </tr>
            <tr>
                <td class="td">
                    &nbsp;
                </td>
                <td class="td">
                    &nbsp;
                </td>
            </tr>

        </table>

    </div>

</body>

</html>