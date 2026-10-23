<html>
	<head>
	</head>
	<body lang=ES-EC style="margin-top: 70px;text-align: center;">

	
	<span style="font-size: 80px;">{{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</span><br>
	<span style="font-size: 80px;">C.I.: {{$paciente->id}}</span>
	</body>
</html>
