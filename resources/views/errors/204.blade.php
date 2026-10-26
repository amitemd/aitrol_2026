<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración requerida · AITROL</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            align-items: center;
            background: #eef4f8;
            color: #17324d;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
        }

        main {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 18px 50px rgba(23, 50, 77, .14);
            max-width: 620px;
            padding: 40px;
            text-align: center;
            width: 100%;
        }

        .status {
            align-items: center;
            background: #fff3cd;
            border-radius: 50%;
            color: #946200;
            display: inline-flex;
            font-size: 28px;
            height: 72px;
            justify-content: center;
            width: 72px;
        }

        h1 {
            color: #126caf;
            font-size: 28px;
            margin: 24px 0 12px;
        }

        p {
            color: #52697f;
            font-size: 17px;
            line-height: 1.6;
            margin: 0;
        }

        a {
            background: #126caf;
            border-radius: 8px;
            color: #fff;
            display: inline-block;
            font-weight: 700;
            margin-top: 28px;
            padding: 12px 22px;
            text-decoration: none;
        }

        a:hover {
            background: #0d568c;
        }
    </style>
</head>
<body>
    <main>
        <div class="status" aria-hidden="true">!</div>
        <h1>Configuración requerida</h1>
        <p>{{ $mensajes ?? 'Falta completar una configuración necesaria para utilizar este módulo.' }}</p>
        <a href="{{ url('/') }}">Volver al inicio</a>
    </main>
</body>
</html>
