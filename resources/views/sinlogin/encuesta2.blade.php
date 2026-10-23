<!DOCTYPE html>
<html>	
<head>
<title>Encuestas IECED</title>
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

input[type="radio"] + svg {
	-webkit-transition: all 0.2s;
	transition: all 0.2s;
}

input + svg {
	cursor: pointer;
}

input[class="super-happy"]:hover + svg,
input[class="super-happy"]:checked + svg,
input[class="super-happy"]:focus + svg {
	fill: rgb(0, 109, 217);
}

input[class="happy"]:hover + svg,
input[class="happy"]:checked + svg,
input[class="happy"]:focus + svg {
	fill: rgb(0, 204, 79);
}

input[class="neutral"]:hover + svg,
input[class="neutral"]:checked + svg,
input[class="neutral"]:focus + svg {
	fill: rgb(232, 214, 0);
}

input[class="sad"]:hover + svg,
input[class="sad"]:checked + svg,
input[class="sad"]:focus + svg {
	fill: rgb(229, 132, 0);
}

input[class="super-sad"]:hover + svg,
input[class="super-sad"]:checked + svg,
input[class="super-sad"]:focus + svg {
	fill: rgb(239, 42, 16);
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

</style>
</head>
<body>
	<img class="centrado" style="position: absolute; top:0; width: 100%; z-index: 1;" src="{{asset('/imagenes/cabecera-arriba.png')}}">
	<img class="centrado" style="position: absolute; top:53px; right: 19px;z-index: 2;" src="{{asset('/imagenes/logo1.png')}}">
	<h1 style="margin: 91px 0 0 0 !important; color: black !important;z-index: 3;"></h1>

	<h3 style="color: black !important;z-index: 3;"> En <b>IECED</b> nos interesa tu opinion</h3>
		<div class="containerw3layouts-agileits" style="background-color: #F8F9FA;z-index: 3;">
			<div class="w3layoutscontactagileits">
				
					<div id="wrapper">
						<div class="table-responsive col-md-12">
							<form  method="POST" action="{{ route('encuesta.guardar')}}" >
								{{ csrf_field() }}
										@php
											$i = 0;
											$j = 0;
										@endphp	
									<div class="ferry ferry-from" style=" margin-bottom: 20px; color: black !important;" >
										<label style="color: black !important;">Área  de Atencion:</label>
										@foreach($areas as $value)
											<label style="color: black !important;" >
									            <input required style="color: black !important;" type="radio" name="area"   class="check2"  value="{{$value->id}}" @if($j == 0) checked="checked" @endif> {{$value->nombre}}
									        </label>
									        @php
												$j++;
											@endphp
										@endforeach
										<br><br>
									</div>
									<br><br>
									<br><br>
									@foreach($arreglo as $value)
										@php
											$k = 1;
											$cuenta = count($value);
										@endphp
										@if($cuenta >0)
											@php
												$formato = $value[0]->grupopregunta->tipo_calificacion;

											@endphp
											<!-- Totos los formatos de texto -->
											@if($formato == 1)
												@foreach($value as $dato)
													<span style="color: black !important;font-size: 20px;" >{{$dato->nombre}}</span><br>
													<textarea   name="{{$dato->id}}" style="width: 90%; height: 70px;"> </textarea>
													<br><br>
												@endforeach									
											@endif
											@if($formato ==2)
												@foreach($value as $dato)
													<table class="table table-bordered table-hover dataTable" style="border-color: black;color: black !important;">
														<tbody>
															<tr>
																<td width="30%" style=" vertical-align: middle; border-color: black;">{{$dato->nombre}}</td>
																<td  width="14%" style="text-align: center; border-color: black;vertical-align: middle;">10 min <input  type="radio" class="check2" name="{{$dato->id}}" value="10"></td>
																<td width="14%" style="text-align: center; border-color: black;vertical-align: middle;">15 min <input type="radio" class="check2" name="{{$dato->id}}" value="15" ></td>
																<td width="14%"  style="text-align: center; border-color: black;vertical-align: middle;">20 min <input type="radio" class="check2" name="{{$dato->id}}" value="20"></td>
																<td width="14%" style="text-align: center; border-color: black;vertical-align: middle;">25 min <input type="radio" required class="check2" name="{{$dato->id}}" value="25"></td>
																<td width="14%"  style="text-align: center; border-color: black;vertical-align: middle;">otros <input style="width: 60px;" type="text" name="{{$dato->id}}" ></td>
															</tr>
														</tbody>
													</table>
													<br>
												@endforeach
											@endif
											<!-- Totos los formatos de reaccion -->
											@if($formato == 3)
												<table id="example2"   class="table table-bordered table-hover dataTable"  style="border-color: black;color: black !important;">
										            <thead>
										              <tr style="font-size: 12px;text-align: center;" >
										                <th width="30%" style=" font-size: 18px; vertical-align: middle;border-color:black;">Criterio de Evaluacion</th>
										                <th width="14%" style="padding: 0 14px;border-color:black;">Muy Buena <br></th>
										                <th width="14%" style="padding: 0 14px;border-color:black;">Buena<br></th>
										                <th width="14%" style="padding: 0 14px;border-color:black;">Regular <br></th>
										                <th width="14%" style="padding: 0 14px;border-color:black;">Mala <br></th>
										                <th width="14%" style="padding: 0 14px;border-color:black;">Muy Mala <br></svg>
										                </th>
										              </tr>
										            </thead>
										            <tbody>
										            	@foreach($value as $dato)
											            	<tr role="row" class="odd">
											                  <td style="border-color:black;font-size: 20px;">{{$dato->nombre}}</td>
											                  <td style="border-color:black; text-align: center;vertical-align: middle;">
											                  	<label for="super-happy{{$dato->id}}">
																<input type="radio" required class="super-happy" id="super-happy{{$dato->id}}" name="{{$dato->id}}" value="5" />
																<svg viewBox="0 0 24 24"><path d="M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.69,16.04 9.67,17.5 12,17.5M8.5,11A1.5,1.5 0 0,0 10,9.5A1.5,1.5 0 0,0 8.5,8A1.5,1.5 0 0,0 7,9.5A1.5,1.5 0 0,0 8.5,11M15.5,11A1.5,1.5 0 0,0 17,9.5A1.5,1.5 0 0,0 15.5,8A1.5,1.5 0 0,0 14,9.5A1.5,1.5 0 0,0 15.5,11M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" /></svg>
															</label></td>
											                  <td style="text-align: center;vertical-align: middle;border-color:black;"><label for="happy{{$dato->id}}">
																<input type="radio"  id="happy{{$dato->id}}" name="{{$dato->id}}" class="happy" value="4"  />
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24"><path d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8C16.3,8 17,8.7 17,9.5M12,17.23C10.25,17.23 8.71,16.5 7.81,15.42L9.23,14C9.68,14.72 10.75,15.23 12,15.23C13.25,15.23 14.32,14.72 14.77,14L16.19,15.42C15.29,16.5 13.75,17.23 12,17.23Z" /></svg>
															</label></td>
											                  <td style="text-align: center;vertical-align: middle;border-color:black;"><label for="neutral{{$dato->id}}">
																	<input type="radio" class="neutral" name="{{$dato->id}}"  id="neutral{{$dato->id}}" value="3" />
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24"><path d="M8.5,11A1.5,1.5 0 0,1 7,9.5A1.5,1.5 0 0,1 8.5,8A1.5,1.5 0 0,1 10,9.5A1.5,1.5 0 0,1 8.5,11M15.5,11A1.5,1.5 0 0,1 14,9.5A1.5,1.5 0 0,1 15.5,8A1.5,1.5 0 0,1 17,9.5A1.5,1.5 0 0,1 15.5,11M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M9,14H15A1,1 0 0,1 16,15A1,1 0 0,1 15,16H9A1,1 0 0,1 8,15A1,1 0 0,1 9,14Z" /></svg>
																</label></td>
																<td style="text-align: center;vertical-align: middle;border-color:black;"><label for="sad{{$dato->id}}">
																	<input type="radio" class="sad" name="{{$dato->id}}"  id="sad{{$dato->id}}" value="3" />
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 24 24"><path d="M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M15.5,8C16.3,8 17,8.7 17,9.5C17,10.3 16.3,11 15.5,11C14.7,11 14,10.3 14,9.5C14,8.7 14.7,8 15.5,8M10,9.5C10,10.3 9.3,11 8.5,11C7.7,11 7,10.3 7,9.5C7,8.7 7.7,8 8.5,8C9.3,8 10,8.7 10,9.5M12,14C13.75,14 15.29,14.72 16.19,15.81L14.77,17.23C14.32,16.5 13.25,16 12,16C10.75,16 9.68,16.5 9.23,17.23L7.81,15.81C8.71,14.72 10.25,14 12,14Z" /></svg>

																</label></td>
											                  <td style="text-align: center;vertical-align: middle;border-color:black;"><label for="super-sad{{$dato->id}}">
																	<input type="radio" class="super-sad" name="{{$dato->id}}"  id="super-sad{{$dato->id}}" value="3" />
																	<svg viewBox="0 0 24 24"><path d="M12,2C6.47,2 2,6.47 2,12C2,17.53 6.47,22 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M16.18,7.76L15.12,8.82L14.06,7.76L13,8.82L14.06,9.88L13,10.94L14.06,12L15.12,10.94L16.18,12L17.24,10.94L16.18,9.88L17.24,8.82L16.18,7.76M7.82,12L8.88,10.94L9.94,12L11,10.94L9.94,9.88L11,8.82L9.94,7.76L8.88,8.82L7.82,7.76L6.76,8.82L7.82,9.88L6.76,10.94L7.82,12M12,14C9.67,14 7.69,15.46 6.89,17.5H17.11C16.31,15.46 14.33,14 12,14Z" /></svg>
																</label></td>
											              	</tr>
														@endforeach
										            </tbody>
										          </table>
										          <br>
											@endif
										@endif
									@endforeach
									<div class="wthreesubmitaits">
										<input type="submit"  value="Enviar">
									</div>
								</div>
								</form>
						</div>
					</div>
			</div>
		</div>
		<div class="w3lsfooteragileits" style="position: relative;">
			<p style="color: black;z-index: 2; position: relative;"> &copy; {{date('Y')}} IECED. Todos los derechos reservados</p>
			<img class="centrado" style="position: absolute; bottom: 0; width: 100%;z-index: 1;" src="{{asset('/imagenes/cabecera-abajo.png')}}">
		</div>

		<!-- Necessary-JavaScript-Files-&-Links -->
			<!-- Date-Picker-JavaScript -->

	
			<!-- //Date-Picker-JavaScript -->
		<!-- //Necessary-JavaScript-Files-&-Links -->
	
		<script>
			$(document).ready(function(){
			  $('.check2').iCheck({
			    checkboxClass: 'icheckbox_square-green',
			    radioClass: 'iradio_square-green',
			    increaseArea: '20%' // optional
			  });
			});
</script>
</body>
</html>