@extends('hospital_admin.iniciod')
@section('action-content')
<style>
*{
	margin:0px;
	padding:0px;
	font-family: Montserrat Medium; 
}
p#header{
	text-align: center;
	font-size: 2.5em;
	color:#9a9a9a;
}

p#subheader{
	text-align: center;
	color:#cecece;
	margin-top:20px;
	font-size: 1.3em;
}

div.contenedor{
	width: 200px;
	height: 230px;
	border-radius: 20px;
	float:none;
   display: inline-block;
   vertical-align: middle;
	-webkit-transition: height .4s;
}

div#uno{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

div#dos{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

div#tres{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

div#cuatro{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

div#cinco{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

div#seis{
	background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
}

img.icon{
	display: block;
	margin:50px auto;
	background-color: rgba(255,255,255,.15);
	width:50%;
	padding:20px;
	-webkit-border-radius: 50%;
	-webkit-box-shadow: 0px 0px 0px 30px rgba(255,255,255,0);
	-webkit-transition:box-shadow .4s;
}

p.texto{
	font-size: 1.2em;
	color:white;
	text-align: center;
	padding-top:10px;
	opacity: .6;
	-webkit-transition: padding-top .4s;
}

div.contenedor:hover{
	height:250px;
}

div.contenedor:hover p.texto{
	padding-top: 30px;
	opacity: 1;
}

div.contenedor:hover img.icon{
	-webkit-box-shadow:0px 0px 0px 0px rgba(255,255,255,.6);
}

</style>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class = "container-fluid text-center">
   <div class="contenedor" id="uno">
      <img class="icon" src="{{asset('/')}}hc4/img/galeria-de-fotos.png">
      <p class="texto">IMAG&Eacute;NES</p>
   </div>

   <div class="contenedor" id="dos">
      <img class="icon" src="{{asset('/')}}hc4/img/educacion.png">
      <p class="texto">LABORATORIO</p>
   </div>

   <div class="contenedor" id="tres" onclick ="location.href='{{route('hospital_admin.farmacia')}}'">
      <img class="icon" src="{{asset('/')}}hc4/img/medicacion1.png">
      <p class="texto">FARMACIA</p>
   </div>

   <div class="contenedor" id="cuatro" onclick ="location.href='{{route('hospital_admin.gestionqui')}}'">
      <img class="icon" src="{{asset('/')}}hc4/img/quirofano1.png">
      <p class="texto">QUIR&Oacute;FANO</p>
   </div>

   <div class="contenedor" id="cinco" onclick ="location.href='{{route('hospital_admin.gestionh')}}'">
      <img class="icon" src="{{asset('/')}}hc4/img/cama-de-hospital1.png">
      <p class="texto">GESTI&Oacute;N DE CUARTO</p>
   </div>

   <div class="contenedor" id="seis">
      <img class="icon" src="{{asset('/')}}hc4/img/linea-de-vida1.png">
      <p class="texto">EMERGENCIA</p>
   </div>
</div>

<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

@endsection