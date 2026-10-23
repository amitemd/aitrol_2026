<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<p>Estimado Proveedor:</p>
		<p><strong>{{$usuario->nombrecomercial}}</strong></p>
		<p>Ha recibido un nuevo comprobante de pago. @if($usuario->banco!=null) Depositado a la cuenta: # {{$usuario->cuenta}} del {{$usuario->banco}} @endif</p>
		<p>Este es un email generado automáticamente, no responder al mismo.</p>
	</body>
</html>
