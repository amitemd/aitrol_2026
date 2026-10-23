<!DOCTYPE html>
<html>
<head>
	<title>Buzon de Encuestas y Sugerencias IECED</title>
<!-- metatags-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
	function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Meta tag Keywords -->
<!-- Custom-Style-Sheet -->
	<link href="{{ asset("encuestas")}}/css/popuo-box.css" rel="stylesheet" type="text/css" media="all" />
	<link href="{{ asset("encuestas")}}/css/style.css" rel="stylesheet" type="text/css" media="all"/><!--style_sheet-->
	<link rel="stylesheet" href="{{ asset("encuestas")}}/css/flexslider.css" type="text/css" media="screen" property="" />
	<link rel="stylesheet" href="{{ asset("encuestas")}}/css/font-awesome.css"> <!-- Font-Awesome_Icons-CSS -->
<!--//Custom-Style-Sheet -->
<!--online_fonts-->	
<!--//online_fonts-->
</head>
<body>
<div class="w3l-head" style="text-align: center;">
	<h1>Buzon de Encuestas y Sugerencias</h1>
	<img style="width: 50%;" src="{{asset('/imagenes/logo1.png')}}">
</div>
<div class="w3l-main">

<div class="w3l-rigt-side">
		<div class="w3l-signin">
			<a class="w3_play_icon1" href="{{ route('sugerencia.ingreso') }}"> Sugerencia</a>
		</div>
		<div class="w3l-signup">
			<a class="w3_play_icon1" href="{{ route('sugerencia.encuesta2') }}"> Encuesta</a>
		</div>
		<div class="clear"></div>

</div>
<div class="clear"></div>
</div>

<!-- //for register popup -->
<footer> &copy; {{date('Y')}} Buzon de Sugerencias y Encuestas IECED. Todos los derechos reservados </footer>

</body>
</html>