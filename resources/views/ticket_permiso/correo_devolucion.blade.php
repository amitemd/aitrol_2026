<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
   
    <style>
        .titulo {
            color: #1e80b6;
            padding-top: 20px;
            padding-bottom: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .body {
            background-color: #ECECEC;
        }

        .div_contenido {
            color: #1e80b6;
            margin-top: 15px;
            padding-top: 20px;
            padding-bottom: 10px;
            padding-left: 20px;
            padding-right: 20px;
            background-color: #ffffff !important;
        }
    </style>
</head>

<body class="body">

    <div class="titulo">
        <h1>Devoluci&oacute;n de Solicitud de Permiso</h1>
    </div>
    <hr>
    <div class=".div_contenido">
        <p>
            Su solicitud a sido devuelta, por favor subir más documentación.
        </p>
        @if(!is_null($empresa))
        <p>
            Empresa : {{$empresa->empresa->razonsocial}}
        </p>
        @endif
        <p>
            Link : <a href="{{$email}}">Ver Permiso</a>
        </p>
    </div>
</body>

</html>
