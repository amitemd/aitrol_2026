<?php

$arrContextOptions = array(
    "ssl" => array(
        "verify_peer"      => false,
        "verify_peer_name" => false,
    ),
);
$path   = asset('images/mail-header.png');
$type   = pathinfo($path, PATHINFO_EXTENSION);
$data   = file_get_contents($path, false, stream_context_create($arrContextOptions));
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

$path_2   = asset('images/mail-footer.jpg');
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
		<img src="<?php echo $base64; ?>" style="width: 700px;">
		<p>Estimado Paciente:</p>
		<p><strong><?php echo $nombre_paciente; ?></strong></p>
        <p>Usted puede consultar sus resultados en línea en el siguiente enlace <a href="https://tinyurl.com/y46fq6wc">http://www.labs.ec/login.php</a></p>
	    <p>Para ingresar su usuario es: <strong><?php echo $user->email; ?></strong></p>
	    <p>Si usted no ha modificado su clave, la misma será su número de cédula.</p>
		<p>Clave: <strong><?php echo $user->id; ?></strong></p>
	    <p>*Ud.podrá cambiar la clave en el perfil de usuario.</p>
		<p>Este es un email generado automáticamente, no responder al mismo.</p>
		<br>
		<br>
		<img src="<?php echo $base64_2; ?>" style="width: 700px;">
	</body>
</html>


