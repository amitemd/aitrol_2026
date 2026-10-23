<html>
	<head>
		<title>Paciente_{{ $agenda->paciente->apellido1}}_{{ $agenda->paciente->nombre1}}</title>
	</head>
	<body lang=ES-EC style="margin-top: -20px;">
		<!--img  src="{{asset('hc_agenda/'.$archivo->archivo)}}"-->
		<div style="width: 90%;">
			<img  src="{{base_path().'/storage/app/hc_agenda/'.$archivo->archivo}}" style="max-width: 90%;">
		</div>
		
	</body>	
</html>