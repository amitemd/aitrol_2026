<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 422 - Datos Incorrectos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            text-align: center;
            padding: 50px;
            color: #333;
        }

        .error-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 48px;
            color: #ff5252;
        }

        .emoji {
            font-size: 80px;
            display: inline-block;
            animation: shake 1s infinite alternate;
        }

        @keyframes shake {
            0% {
                transform: rotate(-5deg);
            }

            100% {
                transform: rotate(5deg);
            }
        }

        p {
            font-size: 18px;
            color: #666;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1>404</h1>
        <div class="emoji">🤯</div>
        <h2>¡Ups! Algo no está bien</h2>
        <p>Los datos enviados no se pueden procesar. Verifica la información e intenta de nuevo.</p>
        <p>Error: {{$message}}</p>
        <a href="/" class="btn">Volver al inicio</a>
    </div>
</body>

</html>