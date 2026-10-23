<!DOCTYPE html>
<html>	
<head>
<title>Buzon de Sugerencias IECED</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<link rel="stylesheet" href="{{ asset('/css/jquery-ui.css') }}" type="text/css" media="all">
<link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
<link href="{{ asset('/css/style2.css') }}" rel='stylesheet' type='text/css' />
<!--webfonts-->
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<!--//webfonts-->
<style type="text/css">
	h3{
		border-bottom:  none !important;
		text-align: center;
		margin-bottom: 20px;
	}
	body {
		text-align: center;
		background-image: url("{{asset('/images')}}/bg.jpg");
	}

	.centrado{
		margin-top: 40px;
		width: 30%;
	}
	h1{
		margin: 0 auto !important;
	}
</style>
</head>
<body>
	<img class="centrado" src="{{asset('/imagenes/ieced-white.png')}}">
<h1> Buzon de Sugerencias</h1>

<h3> En <b>IECED</b> nos interesa tu opinion</h3>
		<div class="containerw3layouts-agileits">
			<div class="w3layoutscontactagileits">
				
					<div id="wrapper">
							<form  method="POST" action="{{ route('sugerencia.guardar')}}">
								{{ csrf_field() }}
								<div id="login" class="animate w3layouts agileits form">
									<div class="ferry ferry-from">
										<label>Selecione el tipo :</label>
										<div style="width: 100%; text-align: center;">
										
										@php
											$i = 0;
											$j = 0;
										@endphp	
										@foreach($tiposugerencia as $value)
											<label style="width: auto;margin: 10px 15px;">
									            <input type="radio" name="tiposugerencia" value="{{$value->id}}" @if($i == 0) checked="checked" @endif > {{$value->nombre}}
									        </label>
									        @php
												$i++;
											@endphp
										@endforeach
										</div>
									</div>
									<div class="ferry ferry-from" style=" margin-bottom: 20px;">
										<label>Área :</label>
										@foreach($areas as $value)
											<label >
									            <input type="radio" name="area" value="{{$value->id}}" @if($j == 0) checked="checked" @endif> {{$value->nombre}}
									        </label>
									        @php
												$j++;
											@endphp
										@endforeach
										<br><br>
									</div>
									<br><br>
									<br><br>
									<div class="ferry ferry-from">
										<label>Descripcion:</label>
										<textarea id="message" name="mensaje" required ></textarea>
									</div>
									<div class="wthreesubmitaits">
										<input type="submit"  value="Enviar">
									</div>
								</div>
								</form>
						</div>
			</div>
		</div>
		<div class="w3lsfooteragileits">
			<p> &copy; {{date('Y')}} IECED. Todos los derechos reservados</p>
		</div>
		<!-- Necessary-JavaScript-Files-&-Links -->
			<!-- Date-Picker-JavaScript -->

<script type="text/javascript" src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
	<script  src="{{ asset('/js/jquery-ui.js') }}"></script>
			<!-- //Date-Picker-JavaScript -->
		<!-- //Necessary-JavaScript-Files-&-Links -->
	

</body>
</html>