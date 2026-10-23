<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<p>Estimado Paciente:</p>
		<p><strong><?php echo $nombre_paciente;?></strong></p>
		<p>Se le ha programado el procedimiento <strong><?php echo $procedimiento_nombre;?></strong> </p> para el día <strong><?php echo $inicio;?></strong><p> en <strong><?php echo $hospital_nombre;?> | <?php echo $consultorio_nombre;?> </strong> ubicada en  <strong><?php echo $hospital_direccion;?></strong> </p>
		
		<p>Este es un email generado automaticamente, no responder al mismo.</p>
	</body>
</html>
 