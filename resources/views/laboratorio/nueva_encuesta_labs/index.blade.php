<!DOCTYPE html>
<html>

<head>
	<title>{{trans('laboratorio.encuestas')}} Labs</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link rel="stylesheet" href="{{ asset('/css/jquery-ui.css') }}" type="text/css" media="all">
	<link href="{{ asset('/css/wickedpicker2.css') }}" rel="stylesheet" type='text/css' media="all" />
	<link rel="stylesheet" href="{{asset('checknew/dist/css/radiobox.min.css')}}" type="text/css" />
	<link href="{{ asset('/css/style2.css') }}" rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css" />
	<link href="{{ asset ("/plugins/sweet_alert/sweetalert.css") }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
	<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="{{asset('favicon-labs.ico')}}">
	<!--webfonts-->
	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
	<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
	<link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('/css/dropzone.css')}}">
	<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
	<link href="{{ asset('/css/icheck/all.css') }}" rel="stylesheet">
	<script src="{{ asset('/js/icheck.js') }}"></script>
	<!--//webfonts-->
	<style type="text/css">
		h3 {
			border-bottom: none !important;
			text-align: center;

		}

		.centrado {
			width: 200px;
		}

		h1 {
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

		a:link,
		a:visited,
		a:hover,
		a:active {
			color: rgba(0, 0, 16, 0.8);
			text-decoration: none;
		}

		a:hover,
		a:active {
			border-bottom: 0.1em solid rgba(0, 0, 16, 0.8);
			color: rgba(0, 0, 16, 0.8);
			text-decoration: none;
		}

		span {
			color: rgba(0, 0, 16, 0.4);
			font-size: 70%;
		}

		header {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			margin: auto;
			width: 34.6rem;
		}

		header h1 {
			font-size: 2.8em;
		}

		.card {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			margin: auto;
			-webkit-box-shadow: 0 0.5rem 1rem rgba(0, 0, 16, 0.19), 0 0.3rem 0.3rem rgba(0, 0, 16, 0.23);
			box-shadow: 0 0.5rem 1rem rgba(0, 0, 16, 0.19), 0 0.3rem 0.3rem rgba(0, 0, 16, 0.23);
			background-color: rgb(255, 255, 255);
			padding: 0.8rem;
			width: 33rem;
		}

		.rating-container {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-pack: justify;
			-ms-flex-pack: justify;
			justify-content: space-between;
			padding: 0.4rem 0.8rem;
			width: 100%;
		}

		.rating-text p {
			color: rgba(0, 0, 16, 0.8);
			font-size: 1.3rem;
			padding: 0.3rem;
		}

		.rating {
			background-color: rgba(0, 0, 16, 0.8);
			padding: 0.4rem 0.4rem 0.1rem 0.4rem;
			border-radius: 2.2rem;
		}

		svg {
			fill: rgb(178, 178, 178);
			height: 3.6rem;
			width: 3.6rem;
			margin: 0.2rem;
		}

		.rating-form-2 svg {
			height: 3rem;
			width: 3rem;
			margin: 0.5rem;
		}

		#radios label {
			position: relative;
		}

		input[type="radio"] {
			position: absolute;
			opacity: 0;
		}

		input[type="radio"]+svg {
			-webkit-transition: all 0.2s;
			transition: all 0.2s;

		}

		input+svg {
			cursor: pointer;
		}

		input[class="super-happy"]:hover+svg,
		input[class="super-happy"]:checked+svg,
		input[class="super-happy"]:focus+svg {
			fill: rgb(0, 109, 217);
		}

		input[class="happy"]:hover+svg,
		input[class="happy"]:checked+svg,
		input[class="happy"]:focus+svg {
			fill: rgb(0, 204, 79);
		}

		input[class="neutral"]:hover+svg,
		input[class="neutral"]:checked+svg,
		input[class="neutral"]:focus+svg {
			fill: rgb(232, 214, 0);
		}

		input[class="neutral-2"]:hover+svg,
		input[class="neutral-2"]:checked+svg,
		input[class="neutral-2"]:focus+svg {
			fill: rgb(240, 119, 0);
		}

		input[class="sad"]:hover+svg,
		input[class="sad"]:checked+svg,
		input[class="sad"]:focus+svg {
			fill: rgb(230, 31, 31);
		}

		input[class="super-sad"]:hover+svg,
		input[class="super-sad"]:checked+svg,
		input[class="super-sad"]:focus+svg {
			fill: rgb(255, 30, 5);
		}

		footer {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-pack: end;
			-ms-flex-pack: end;
			justify-content: flex-end;
			text-align: right;
			width: 34.6rem;
			margin: auto;
		}

		footer p {
			font-size: 1.3em;
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
				height: 100%;
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
				height: 100%;
				width: 100%;
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

			.boton:hover {
				cursor: pointer;
			}
		}
	</style>
</head>

<body>

	<div class="containerw3layouts-agileits" style="background-color: #F8F9FA;z-index: 3;width: 95%; position: relative;">
		<div class="w3layoutscontactagileits">
			<img class="centrado" style="position: absolute; top:5px; right: 19px;z-index: 2;" src="{{asset('/imagenes/login2.png')}}">
			<h1 style="margin: 30px 0 0 0 !important; color: black !important;z-index: 3;"></h1>
			<br>
			<br>
			<br>

			<h3 style="color: black !important;z-index: 3;height: 100% !important;"></h3>

			<div id="wrapper">
				<div class="table-responsive col-md-12">
					<form class="form-vertical" method="get" id="enviar2">
						{{ csrf_field() }} 
						<input type="hidden" id="id_master" name="id_master" value="{{$id_master_tipo->id}}">
						@php
						$i = 0;
						$j = 0;
						@endphp
						<h3 style="color: black !important;z-index: 3;"> En <b>Labs</b> {{trans('laboratorio.nosinteresatuopinion')}}</h3>

						<div class="form-group row">
							<div class="form-group col-md-6">
								<label style="color: black">{{trans('laboratorio.ceduladelpaciente')}}</label>
							</div>
							<div class="form-group col-md-6">
								<input type="number" style="width: 30%;border: 1px solid black;" class="form-control" name="cedula" maxlength="13" required>
							</div>
							<br>
						</div>
						<!--Reordenar array -->
						@php

						$arrayNuevo = [$arreglo[0],$arreglo[2],$arreglo[3],$arreglo[1]];
						$var = 0;
						@endphp

						@foreach($arrayNuevo as $value)
						@php

						$k = 1;
						$cuenta = count($value);
						@endphp
						@if($cuenta >0)
						@php
						$formato = $value[0]->grupopregunta->tipo_calificacion;

						@endphp
						<!-- Totos los formatos de texto -->

						@if($formato ==2)
						@foreach($value as $dato)
						<table>
							<tbody>
								<tr>
									<td width="25%" style="vertical-align: middle; border-color: black !important; font-size: 13px;">{{$dato->nombre}}</td>
									@for($i=1; $i < 6 ; $i++) <td width="14%" style="text-align: center; border-color: black;vertical-align: middle;font-size: 13px;">
										{{$i}}<input style="opacity: 1;" type="radio" class="radiobox-ping" name="calificacion" value="{{$i}}">
										</td>


										@endfor

								</tr>
							</tbody>
						</table>
						<br>
						@endforeach
						@endif
						<!-- Totos los formatos de reaccion -->
						@if($formato == 3)
						<table id="example2">
							<thead>
								<tr style="font-size: 13px;text-align: center;">
									<th width="30%" style=" font-size: 16px; vertical-align: middle;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.criteriosdeevaluacion')}}</th>
									<th width="5%" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.muybueno')}}</th>
									<th width="5%" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.bueno')}}<br></th>
									<th width="5%" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.nibuenonimalo')}}<br></th>
									<th width="5%" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.malo')}} <br></th>
									<th width="5%" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">{{trans('laboratorio.muymalo')}}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($value as $dato)
								<tr role="row" class="odd">
									<td style="border-color:black !important;font-size: 13px; vertical-align: middle;">{{$dato->nombre}}</td>
									<td>
										<label for="super-happy{{$dato->id}}">
											<input type="radio" id="super-happy{{$dato->id}}" name="{{$dato->id}}" class="super-happy" value="10" />
											<svg viewBox="0 0 24 24">
												<path d="M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.69,16.04 9.67,17.5 12,17.5M8.5,11A1.5,1.5 0 0,0 10,9.5A1.5,1.5 0 0,0 8.5,8A1.5,1.5 0 0,0 7,9.5A1.5,1.5 0 0,0 8.5,11M15.5,11A1.5,1.5 0 0,0 17,9.5A1.5,1.5 0 0,0 15.5,8A1.5,1.5 0 0,0 14,9.5A1.5,1.5 0 0,0 15.5,11M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
											</svg>
										</label>
									</td>
									<td style="text-align: center;vertical-align: middle;border-color:black;">
										<label for="happy{{$dato->id}}">
											<input type="radio" id="happy{{$dato->id}}" name="{{$dato->id}}" class="happy" value="8" />
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24">
												<path d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8C16.3,8 17,8.7 17,9.5M12,17.23C10.25,17.23 8.71,16.5 7.81,15.42L9.23,14C9.68,14.72 10.75,15.23 12,15.23C13.25,15.23 14.32,14.72 14.77,14L16.19,15.42C15.29,16.5 13.75,17.23 12,17.23Z" />
											</svg>
										</label>

									</td>
									<td style="text-align: center;vertical-align: middle;border-color:black;">
										<label for="neutral{{$dato->id}}">
											<input type="radio" class="neutral" name="{{$dato->id}}" id="neutral{{$dato->id}}" value="5" />
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24">
												<path d="M8.5,11A1.5,1.5 0 0,1 7,9.5A1.5,1.5 0 0,1 8.5,8A1.5,1.5 0 0,1 10,9.5A1.5,1.5 0 0,1 8.5,11M15.5,11A1.5,1.5 0 0,1 14,9.5A1.5,1.5 0 0,1 15.5,8A1.5,1.5 0 0,1 17,9.5A1.5,1.5 0 0,1 15.5,11M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M9,14H15A1,1 0 0,1 16,15A1,1 0 0,1 15,16H9A1,1 0 0,1 8,15A1,1 0 0,1 9,14Z" />
											</svg>
										</label>

									</td>
									<td style="text-align: center;vertical-align: middle;border-color:black;">
										<label for="neutral-2{{$dato->id}}">
											<input type="radio" class="neutral-2" name="{{$dato->id}}" id="neutral-2{{$dato->id}}" value="2" />
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24">
												<path d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M15.5,8C16.3,8 17,8.7 17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M12,14C13.75,14 15.29,14.72 16.19,15.81L14.77,17.23C14.32,16.5 13.25,16 12,16C10.75,16 9.68,16.5 9.23,17.23L7.81,15.81C8.71,14.72 10.25,14 12,14Z" />
											</svg>
										</label>
									</td>

									<td style="text-align: center;vertical-align: middle;border-color:black;"><label for="super-sad{{$dato->id}}">
											<input type="radio" class="super-sad" name="{{$dato->id}}" id="super-sad{{$dato->id}}" value="0" />
											<svg viewBox="0 0 24 24">
												<path d="M12,2C6.47,2 2,6.47 2,12C2,17.53 6.47,22 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M16.18,7.76L15.12,8.82L14.06,7.76L13,8.82L14.06,9.88L13,10.94L14.06,12L15.12,10.94L16.18,12L17.24,10.94L16.18,9.88L17.24,8.82L16.18,7.76M7.82,12L8.88,10.94L9.94,12L11,10.94L9.94,9.88L11,8.82L9.94,7.76L8.88,8.82L7.82,7.76L6.76,8.82L7.82,9.88L6.76,10.94L7.82,12M12,14C9.67,14 7.69,15.46 6.89,17.5H17.11C16.31,15.46 14.33,14 12,14Z" />
											</svg>

										</label></td>

								</tr>

								@endforeach
							</tbody>
						</table>
						<br>
						@endif

						@if($formato ==5 )
						@foreach($value as $dato)
						<table style="width:100%">
							<tbody>

								<tr>
									<td width="50%" style="vertical-align: middle; border-color: black !important; font-size: 13px;">{{$dato->nombre}}</td>
									<td width="50%">
										<table style="width: 100%">
											@if($var == 0)
											<thead style="background-color: #45b5b2;font-size: 13px">
												<tr>
													<td width="50%" class="text-center" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">
														SI
													</td>
													<td width="50%" class="text-center" style="padding: 0 14px;border-color:black;background-color: #45b5b2;">
														NO
													</td>
												</tr>
											</thead>
											@endif
											<tbody>
												<tr>
													<td class="text-center">
														<label for="super-happy{{$dato->id}}">
															<input type="radio" id="super-happy{{$dato->id}}" name="{{$dato->id}}" class="super-happy" value="10" />
															<svg viewBox="0 0 24 24">
																<path d="M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.69,16.04 9.67,17.5 12,17.5M8.5,11A1.5,1.5 0 0,0 10,9.5A1.5,1.5 0 0,0 8.5,8A1.5,1.5 0 0,0 7,9.5A1.5,1.5 0 0,0 8.5,11M15.5,11A1.5,1.5 0 0,0 17,9.5A1.5,1.5 0 0,0 15.5,8A1.5,1.5 0 0,0 14,9.5A1.5,1.5 0 0,0 15.5,11M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
															</svg>
														</label>
													</td>
													<td class="text-center">
														<label for="super-sad{{$dato->id}}">
															<input type="radio" class="super-sad" name="{{$dato->id}}" id="super-sad{{$dato->id}}" value="0" />
															<svg viewBox="0 0 24 24">
																<path d="M12,2C6.47,2 2,6.47 2,12C2,17.53 6.47,22 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M16.18,7.76L15.12,8.82L14.06,7.76L13,8.82L14.06,9.88L13,10.94L14.06,12L15.12,10.94L16.18,12L17.24,10.94L16.18,9.88L17.24,8.82L16.18,7.76M7.82,12L8.88,10.94L9.94,12L11,10.94L9.94,9.88L11,8.82L9.94,7.76L8.88,8.82L7.82,7.76L6.76,8.82L7.82,9.88L6.76,10.94L7.82,12M12,14C9.67,14 7.69,15.46 6.89,17.5H17.11C16.31,15.46 14.33,14 12,14Z" />
															</svg>
														</label>
													</td>
												</tr>

											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<br>
						@php $var ++; @endphp
						@endforeach
						@endif

						@if($formato == 1 or $formato == 7)

						@foreach($value as $dato)
						<div style="display:flex; justify-content:space-between">
							<div style="width:25%; margin-top:30px">
								<span style="color: black !important;font-size: 13px;">{{$dato->nombre}}</span><br>
							</div>
							<textarea name="{{$dato->id}}" style="width: 50%; height: 70px; margin-bottom:15px;"></textarea>
						</div>
						@endforeach
						@endif

						@endif
						@endforeach
					</form>
				</div>

				<div class="wthreesubmitaits">
					<button class="boton btn  btn-lg" style="z-index: 9999;position: static; padding: 10px 40px;color: white;border-radius: 20px; background-color:#45b5b2; margin-bottom: 100px" id="boton_enviar" onclick="enviar_encuesta()">{{trans('laboratorio.enviar')}}</button>
					<span style="height: 30px;">&nbsp;</span>
				</div>
			</div>
		</div>
	</div>
	<div class="w3lsfooteragileits">
		<!-- <img class="centrado" style="width: 100%;" src="{{asset('/imagenes/pielabs.png')}}"> -->

	</div>

	<!-- Necessary-JavaScript-Files-&-Links -->
	<!-- Date-Picker-JavaScript -->


	<!-- //Date-Picker-JavaScript -->
	<!-- //Necessary-JavaScript-Files-&-Links -->

	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
	<script>
		$(document).ready(function() {
			$('.check2').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
				increaseArea: '20%' // optional
			});
		});
	</script>
	<script type="text/javascript">
		function enviar_encuesta() {
			let id_encuesta = $('#id_master').val();
			let arrayA = [];
			let array = $("#enviar2").serializeArray();

			for (let index = 0; index < array.length; index++) {
				console.log(array[index]["name"]);
				if (array[index]["name"] == 'cedula') {
					if (array[index]["value"] == '') {
						arrayA.push("La cédula es obligatoria");
					}
				}
				if (array[index]["name"] == '14') {
					if (array[index]["value"] == '') {
						arrayA.push("La recomendación es obligatoria");
					}
				}
			}

			if (!$("input[name=7]").is(':checked')) {
				arrayA.push("La información sobre la resolución de dudas");
			}

			if (!$("input[name=8]").is(':checked')) {
				arrayA.push("La explicación sobre el personal administrativo");
			}

			if (!$("input[name=9]").is(':checked')) {
				arrayA.push("La amabilidad y el respeto que recibio esta vacio");
			}

			if (!$("input[name=15]").is(':checked')) {
				arrayA.push("La limpieza de las instalaciones vacio");
			}
			if (!$("input[name=10]").is(':checked')) {
				arrayA.push("El personal le consulto su nombre esta vacio");
			}
			if (!$("input[name=11]").is(':checked')) {
				arrayA.push("El personal uso los insumos esta vacio");
			}
			if (arrayA.length == 0) {

				$('#boton_enviar').attr("disabled", true);
				$.ajax({
					type: 'post',
					url: "{{ route('nuevo_formato_guardar.guardar')}}",
					datatype: 'json',
					data: $("#enviar2").serialize(),
					success: function(data) {


						if (data.status) {
							alertas('success', 'Muchas Gracias', data.message);
							window.location.href="{{url('nuevo_formato_encuesta')}}";
						} else {
							$('#boton_enviar').attr("disabled", false);
							alertas('error', 'Ocurrio un error', data.message);
							//location.reload();
						}

					},
					error: function(data) {
						$('#boton_enviar').attr("disabled", false);
						alertas('error', 'Error de Conexion', data.message);
					}
				});



			} else {
				console.log(arrayA.join(","));
				alertas('error', 'Error', arrayA.join(","));
			}
		}

		function alertas(icon, title, text) {
			Swal.fire({
				icon: `${icon}`,
				title: `${title}`,
				text: `${text}`,
			})

		}
	</script>
</body>

</html>