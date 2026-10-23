<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .page {
            padding: 20px;
        }

        /* ── Logo ── */
        .logo-container {
            height: 70px;
            margin-bottom: 5px;
        }

        .logo-container img {
            height: 70px;
        }

        /* ── Títulos ── */
        .titulo-empresa {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 6px 0;
        }

        .titulo-reporte {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 6px 0 12px 0;
        }

        .info-paciente {
            margin-bottom: 14px;
        }

        .info-paciente td {
            padding: 2px 6px;
            font-size: 10px;
        }

        .info-paciente .label {
            font-weight: bold;
        }

        .tabla-principal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabla-principal th {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            font-size: 9px;
            background-color: #f2f2f2;
        }

        .tabla-principal td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            font-size: 10px;
        }

        .titulo-sevo {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 16px 0 8px 0;
        }

        .tabla-sevo {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabla-sevo th {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            font-size: 9px;
            background-color: #f2f2f2;
        }

        .tabla-sevo td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            font-size: 10px;
        }

        .firma-container {
            margin-top: 30px;
            text-align: center;
        }

        .firma-container img {
            height: 40px;
        }

        .firma-linea {
            display: inline-block;
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 4px;
            font-size: 9px;
            text-align: center;
            padding-top: 3px;
        }
    </style>
</head>

<body>
    <div class="page">

        @if($logoBase64)
        <div class="logo-container">
            <img src="{{ $logoBase64 }}" alt="Logo">
        </div>
        @endif

        <div class="titulo-empresa">{{ $empresa->razonsocial }}</div>
        <div class="titulo-reporte">REPORTE DE CONSUMO DE OXIGENO EN PACIENTES</div>

        <table class="info-paciente">
            <tr>
                <td class="label">HC:</td>
                <td>{{ $agenda->paciente->id }}</td>
            </tr>
            <tr>
                <td class="label">PACIENTE:</td>
                <td>
                    {{ $agenda->paciente->apellido1 }}
                    {{ $agenda->paciente->apellido2 }}
                    {{ $agenda->paciente->nombre1 }}
                    {{ $agenda->paciente->nombre2 }}
                </td>
            </tr>
        </table>

        <table class="tabla-principal">
            <thead>
                <tr>
                    <th rowspan="2">FECHA</th>
                    <th colspan="2">HORAS</th>
                    <th rowspan="2">CÓDIGO</th>
                    <th rowspan="2">SERVICIO</th>
                    <th rowspan="2">LITROS</th>
                    <th rowspan="2">MINUTOS</th>
                    <th rowspan="2">HORAS</th>
                    <th rowspan="2">TOTAL/LITROS</th>
                    <th rowspan="2">VALOR UNITARIO</th>
                    <th rowspan="2">VALOR TOTAL</th>
                </tr>
                <tr>
                    <th>INICIO</th>
                    <th>FINAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $fecha_operacion }}</td>
                    <td>{{ $aud_protocolo->hora_inicio }}</td>
                    <td>{{ $aud_protocolo->hora_fin }}</td>
                    <td>H900034</td>
                    <td>OXIGENO (LITROS/MINUTO)</td>
                    <td><strong>{{ $litros }}</strong></td>
                    <td><strong>{{ $minutos}}</strong></td>
                    <td><strong>{{ $duracionHoras }}</strong></td>
                    <td><strong>{{ $totalLitros }}</strong></td>
                    <td>{{ number_format($valorUnitario, 2) }}</td>
                    <td><strong>{{ $valorTotal}}</strong></td>
                </tr>
            </tbody>
        </table>
      
        @if($isProc)
        <div class="titulo-sevo">REPORTE DE CONSUMO DE SEVOFLUORANO EN PACIENTES</div>

        <table class="tabla-sevo">
            <thead>
                <tr>
                    <th rowspan="2">FECHA</th>
                    <th colspan="2">HORAS</th>
                    <th rowspan="2">PORCENTAJE %<br>SEVOFLUORANO</th>
                    <th rowspan="2">LITROS OXIGENO</th>
                    <th rowspan="2">HORAS</th>
                    <th rowspan="2">CONSTANTE</th>
                    <th rowspan="2">CANTIDAD</th>
                </tr>
                <tr>
                    <th>INICIO</th>
                    <th>FINAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $fecha_operacion }}</td>
                    <td>{{ $aud_protocolo->hora_inicio }}</td>
                    <td>{{ $aud_protocolo->hora_fin }}</td>
                    <td><strong>{{ $porcentaje }}</strong></td>
                    <td><strong>{{ $litros }}</strong></td>
                    <td><strong>{{ $duracionHoras }}</strong></td>
                    <td><strong>3.33</strong></td>
                    <td><strong>{{ $cantidadSevo }}</strong></td>
                </tr>
            </tbody>
        </table>
        @endif

        @if($firmaBase64)
        <div class="firma-container">
            <img src="{{ $firmaBase64 }}" alt="Firma" style="width: 250px; height: auto;margin-top:15px"><br>
            <!-- <span class="firma-linea">Firma del Anestesiólogo</span> -->
        </div
            @endif

            </div>
</body>

</html>
