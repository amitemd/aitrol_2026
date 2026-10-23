@php

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

@endphp
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
		<h3><strong>{{trans('tecnicof.hello')}} {{$nombre}},</strong></h3>
		<p>{{trans('tecnicof.answer')}}</p>
		<p><strong>@if($estado_solicitud == 0) {{trans('tecnicof.notapproved')}} @elseif($estado_solicitud == 1) {{trans('tecnicof.approved')}} @endif<br>
				<strong>{{trans('tecnicof.justification')}}: </strong> {{$justificacion_final}} <br>
		</p>
		<p style="color: #009DCE;">
			{{trans('tecnicof.automatically')}}
		</p>
		<img src="<?php echo $base64_2; ?>" style="width: 100%;">
	</div>
</body>

</html>