<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<p>Estimado Paciente:</p>
		<p><strong><?php echo $nombre_paciente;?></strong></p>
		<p>Ha realizado correctamente el pago de la orden</p>
        <p>Usted puede consultar sus resultados en línea en el siguiente enlace <a href="https://tinyurl.com/y46fq6wc">http://www.labs.ec/login.php</a></p> 
	    <p>Para ingresar su usuario es: <strong><?php echo $user->email;?></strong></p>
	    <p>Si usted no ha modificado su clave, la misma será su número de cédula.</p> 
		<p>Clave: <strong><?php echo $user->id;?></strong></p>
	    <p>*Ud.podrá cambiar la clave en el perfil de usuario.</p>
		<p>Este es un email generado automáticamente, no responder al mismo.</p>
	</body>
</html>


