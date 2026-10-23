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

//$url = 'https://labs.siaam.ec/sis_medico/public/pagina/resultados_dr_externo/imprimir/'.$orden->id;
$url = asset('pagina/resultados_dr_externo/imprimir/'.$orden->id);  
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<TITLE>Correo</TITLE>
	</head>
	<body>
		<img src="<?php echo $base64; ?>" style="width: 700px;">
		<p>Estimado Doctor:</p>
		<p><strong><?php echo $orden->doc_externo->nombre1 .' '. $orden->doc_externo->apellido1;  ?></strong></p>
        <p>Usted puede consultar los resultados en línea de su paciente <?php echo $nombre_paciente; ?> en el siguiente enlace: </p>
        <p><a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
		<br>
		<br>
		<img src="<?php echo $base64_2; ?>" style="width: 700px;">
	</body>
</html>


