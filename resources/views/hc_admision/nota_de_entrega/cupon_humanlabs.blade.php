<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cupón HumanLabs</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            background: #eef5f5;
            font-family: Arial, Helvetica, sans-serif;
            color: #174f52;
        }
        .cupon {
            width: 760px;
            min-height: 380px;
            display: flex;
            overflow: hidden;
            background: #fff;
            border: 3px solid #07999a;
            border-radius: 22px;
            box-shadow: 0 14px 35px rgba(0, 76, 78, .18);
        }
        .cupon__contenido {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 38px 42px;
            text-align: center;
        }
        .cupon__logo {
            display: block;
            width: 100%;
            max-width: 430px;
            height: auto;
            margin: 0 auto 14px;
        }
        .cupon__titulo {
            margin: 0;
            color: #f15a24;
            font-size: 30px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .cupon__texto {
            margin: 12px 0 0;
            color: #356f71;
            font-size: 17px;
            line-height: 1.45;
        }
        .cupon__paciente {
            margin-top: 18px;
            color: #174f52;
            font-size: 16px;
            font-weight: bold;
        }
        .cupon__qr {
            width: 245px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 24px;
            background: #07999a;
            color: #fff;
            text-align: center;
        }
        .cupon__qr img {
            width: 170px;
            height: 170px;
            padding: 10px;
            background: #fff;
            border-radius: 12px;
        }
        .cupon__qr-label {
            margin: 16px 0 5px;
            font-size: 15px;
            font-weight: bold;
        }
        .cupon__codigo {
            margin: 0;
            font-size: 14px;
            letter-spacing: 1px;
        }
        @page { size: landscape; margin: 12mm; }
        @media print {
            body { min-height: auto; padding: 0; background: #fff; }
            .cupon { box-shadow: none; break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="cupon">
        <div class="cupon__contenido">
            <img class="cupon__logo" src="{{ asset('imagenes/humanlabs/humanlabs-logo.png') }}" alt="HumanLabs Medicina Diagnóstica">
            <h1 class="cupon__titulo">Cupón de descuento</h1>
            <p class="cupon__texto">Presenta este cupón en HumanLabs y accede al beneficio disponible.</p>
            <p class="cupon__paciente">{{ $paciente->nombre1 }} {{ $paciente->nombre2 }} {{ $paciente->apellido1 }} {{ $paciente->apellido2 }}</p>
        </div>
        <div class="cupon__qr">
            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($url_cupon, 'QRCODE', 8, 8) }}" alt="QR del cupón HumanLabs">
            <p class="cupon__qr-label">Escanea tu cupón</p>
            <p class="cupon__codigo">{{ $codigo_cupon }}</p>
        </div>
    </div>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
