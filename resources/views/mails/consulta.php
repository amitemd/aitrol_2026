<?php

$arrContextOptions = array(
    "ssl" => array(
        "verify_peer"      => false,
        "verify_peer_name" => false,
    ),
);
$path   = asset('header_mail.png');
$type   = pathinfo($path, PATHINFO_EXTENSION);
$data   = file_get_contents($path, false, stream_context_create($arrContextOptions));
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

$path_2   = asset('footer_mail.png');
$type_2   = pathinfo($path_2, PATHINFO_EXTENSION);
$data_2   = file_get_contents($path_2, false, stream_context_create($arrContextOptions));
$base64_2 = 'data:image/' . $type_2 . ';base64,' . base64_encode($data_2);

?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<style type="text/css">
			*{
				font-family: sans-serif !important;
			}
		</style>
		<div style="width: 90%; padding-left: 5%;">
			<img src="<?php echo $base64; ?>" style="width: 100%;">
			<h2 style="text-align: center; "><b>Recordatorio de Cita Médica</b></h2><br>
			<h3 ><strong>Hola <?php echo $nombre_paciente; ?>,</strong></h3>
			<p>Se le ha programado la consulta en la especialidad de <?php echo $especialidad_nombre; ?>:</p>
			<p><strong>Paciente: </strong> <?php echo $nombre_paciente; ?> <br>
				<strong>Día: </strong> <?php echo date("d/m/Y", strtotime($inicio)); ?><br>
				<strong>Hora: </strong> <?php echo date("H:i", strtotime($inicio)); ?><br>
				<strong>Lugar: </strong> <?php echo $hospital_nombre; ?> | <?php echo $consultorio_nombre; ?> <br>
				<strong>Direccion: </strong> <?php echo $hospital_direccion; ?>
			</p>
			<p style="color: #009DCE;">Será un gusto atenderte, recuerda venir 15 minutos antes. <br>
				Este es un email generado automáticamente, no responder al mismo.</p>
			<img src="<?php echo $base64_2; ?>" style="width: 100%;">
		</div>
	</body>
</html>
