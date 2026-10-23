<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<p>Bienvenido a AITROL.</p>
		<p>Estimado Empleado:</p>
		<p><strong>{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</strong></p>
		<p>Se le ha creado un usuario en el sistema para acceder al mismo ingrese a la siguiente URL:</p>
		<a href="{{asset('/login')}}">{{asset('/login')}}</a>
		<p>
			<b>Su usuario es:</b> {{$usuario->email}} <br>
			<b>Su clave:</b>	   {{$usuario->id}}
		</p>
		<p>Este es un email generado automáticamente, no responder al mismo.</p>
	</body>
</html>
