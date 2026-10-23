<!DOCTYPE html>
<html>	
<head>
<title>Encuestas </title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<link rel="stylesheet" href="{{ asset('/css/jquery-ui.css') }}" type="text/css" media="all">
<link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
<link href="{{ asset('/css/style2.css') }}" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />

<!--webfonts-->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->

    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
    <script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script> 
	<script  src="{{ asset('/js/jquery-ui.js') }}"></script>
	<link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
	<script src="{{ asset('/js/icheck.js') }}"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<!--//webfonts-->
<style type="text/css">
	h3{
		border-bottom:  none !important;
		text-align: center;
		margin-bottom: 20px;
	}

	.centrado{
		width: 200px;
	}
	h1{
		margin: 0 auto !important;
	}

	@import url('https://fonts.googleapis.com/css?family=Roboto');

body,
html {
	display: grid;
	height: 100%;
	width: 100%;
	background-color: rgb(242, 242, 242);
	font-family: 'Roboto', sans-serif;
	color: rgba(0, 0, 16, 0.8);
	font-weight: 700;
	padding: 0;
	margin: 0;
}


@media screen and (max-width: 650px) and (max-height: 700px) {
	body,
	html {
		font-size: 0.7rem;
	}
	header h1 {
		font-size: 4em;
	}
	footer p {
		font-size: 2em;
	}
}

@media screen and (max-height: 700px) {
	body,
	html {
		font-size: 0.7rem;
	}
	header h1 {
		font-size: 4em;
	}
	footer p {
		font-size: 2em;
	}
}

@media screen and (max-width: 650px) {
	body,
	html {
		font-size: 0.7rem;
	}
	header h1 {
		font-size: 4em;
	}
	footer p {
		font-size: 2em;
	}
}

@media screen and (max-width: 450px) and (max-height: 550px) {
	body,
	html {
		font-size: 0.6rem;
	}
	header h1 {
		font-size: 4.6em;
	}
	footer p {
		font-size: 3em;
	}
}

@media screen and (max-height: 550px) {
	body,
	html {
		font-size: 0.6rem;
	}
	header h1 {
		font-size: 4.6em;
	}
	footer p {
		font-size: 3em;
	}
}

@media screen and (max-width: 450px) {
	body,
	html {
		font-size: 0.6rem;
	}
	header h1 {
		font-size: 4.6em;
	}
	footer p {
		font-size: 3em;
	}
}

@media screen and (max-width: 400px) and (max-height: 500px) {
	body,
	html {
		height: 500px;
		width: 400px;
	}
}

@media screen and (max-height: 500px) {
	body,
	html {
		height: 500px;
	}
}

@media screen and (max-width: 400px) {
	body,
	html {
		width: 400px;
	}
}


  .boton_2{
  	width: 100%;
  	text-align: center;
    text-decoration: none;
    padding: 0px;
    font-family: arial;
    text-transform: uppercase;
    font-weight: 800;
    color: black;
    background-color: #9DBCF3;
  }
  .boton_2:hover{
    color: #9DBCF3;
    background-color:#CFDCF3;
    text-decoration: none;
  }

  a{
  	color: white;
	width: 100%;
	font-size: 10px;
  }
  .elemento{
  	white-space: normal !important;
  	text-align: center;
  }

</style>

</head>
<body>
	<!-- img class="centrado" style="position: absolute; top:0; width: 100%; z-index: 1;" src="{{asset('/imagenes/cabecera-arriba.png')}}"-- >
	<!-- img class="centrado" style="position: absolute; top:53px; right: 19px;z-index: 2;" src="{{asset('/imagenes/logo1.png')}}"-- >
	<h1 style="margin: 91px 0 0 0 !important; color: black !important;z-index: 3;"></h1>

	<!--h3 style="color: black !important;z-index: 3;"> En <b>IECED</b> nos interesa tu opinión</h3-->

		<div class="container-fluid">
  				<br><br><br><br>

				<div class="row">
					<div class="col-md-1" >&nbsp;</div>	
					<div class="col-md-10 " style="word-wrap: break-word !important;" ><a  href="{{route('sinlogin.nuevo_formato',['id'=>$encuestas->id])}}"class="elemento btn btn-primary btn-lg">{{$encuestas->descripcion}}</a></div>
					<div class="col-md-1" style="color:" >&nbsp;</div>
				</div>	
				<div class="row">
					<div class="col-md-4">&nbsp;</div>	
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">&nbsp;</div>
				</div>	
			
		</div>	
		
</body>
</html>