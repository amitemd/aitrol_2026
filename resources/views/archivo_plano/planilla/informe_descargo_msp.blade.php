<!DOCTYPE html>
<html>
@php
$array = [];
$array1 = [];
@endphp

@foreach ($medicinas as $value)
@if ($value->codigo == "465")
@php
$array[] = $value;
@endphp
@endif
@endforeach

@foreach ($insumos as $value)
@if ($value->codigo == "16")
@php
$array1[] = $value;
@endphp
@endif
@endforeach

<head>
	<title>INFORME DE DESCARGO</title>
	<style>
		@page {
			margin: 0 0;
		}

		body {
			margin-top: 0.2cm;
			margin-left: 0.5cm;
			margin-right: 0.5cm;
			margin-bottom: 0.8cm;
			font-size: 9px;
		}

		header {
			position: fixed;
			top: 0.2cm;
			left: 0.5cm;
			right: 0cm;
			height: 3cm;
			align-content: center;
		}

		.table2 {

			margin-right: 0px;
			margin-top: 0px;
			margin-left: 0.5px;
			margin-bottom: 2px;
			font-size: 9px;
			width: 100%;

		}

		.table {
			width: 100%;
			border-collapse: collapse;
			font-size: 12px;
		}

		.number {

			padding-right: 10px;
			text-align: right;
		}

		/** Define the footer rules **/
		footer {
			position: fixed;
			bottom: 2cm;
			left: 0.5cm;
			right: 0cm;
			height: 2cm;
		}

		.ftr {
			height: 2cm;
		}

		div {
			padding: 1px;
		}

		.css_descripcion {
			font-size: 9px;
			max-height: 50px;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.css_fecha {
			font-size: 9px;
		}

		.titulo_css {
			text-align: center;
		}

		.total_css {

			text-align: right;
			font-weight: bold;
		}

		.subtitulo_css {
			text-align: left;
			font-weight: bold;
			font-size: 9px;
		}

		.cabecera_css {
			font-weight: bold;
			font-size: 9px;
		}

		.contenedor-tablas {
			display: flex;
			justify-content: space-between;
			gap: 20px;
		}

		.tabla-box {
			width: 48%;
		}
	</style>

</head>

<body>
	<header>

	</header>


	<main>

		<table style="width: 100%; border-collapse: collapse;margin-top:10px">
			<tr>
				<td style="width:45%">
					<span style="margin-left:3px">
						<img style="width:150px;height: 50px" src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}" alt="none"></p>
					</span>
				</td>
				<td style="width:55%">
					<span style="font-weight: bold;">LISTA DE MEDICAMENTOS Y MATERIALES USADOS EN SALA DE <br />ENDOSCOPIA</span>
				</td>
			</tr>
		</table>
		<table style="width: 100%; border-collapse: collapse;">
			<tbody>
				<tr>
					<td style="width: 10%; font-weight: bold; text-transform: uppercase;">
						<span style="margin-left:3px">
							PACIENTE:
						</span>
					</td>
					<td style="width: 20%; text-transform: uppercase;">
						{{ $archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2 }}
					</td>

					<td style="width: 15%; font-weight: bold; text-transform: uppercase;">
						<span style="margin-left:-35px">
							PROCEDIMIENTO:
						</span>
					</td>
					<td style="width: 20%; text-transform: uppercase;">
						<span style="margin-left:-30px">
							{{ trim(explode('/', $archivo_plano->nom_procedimiento ?? $texto_procedimiento)[0]) }}
						</span>
					</td>
				</tr>

				<tr>
					<td style="width: 10%; font-weight: bold; text-transform: uppercase;">
						<span style="margin-left:3px">
							FECHA:
						</span>
					</td>
					<td style="width: 45%; text-transform: uppercase;">
						{{ substr($archivo_plano->fecha_ing, 0, 10) }}
					</td>

					<td style="width: 15%; font-weight: bold; text-transform: uppercase;">
						<span style="margin-left:-35px">
							SEGURO:
						</span>
					</td>
					<td style="width: 30%; text-transform: uppercase;">
						<span style="margin-left:-30px">
							{{ $archivo_plano->seguro->nombre }}
						</span>
					</td>
				</tr>
			</tbody>
		</table>
		<table width="100%" style="table-layout: fixed;margin-top:10px">

			<tr>
				<td colspan="6" style="width: 50%; vertical-align: top;">
					<!-- Tabla de Insumos -->
					<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
						<thead>
							<tr>
								<td style="text-align: center;" colspan="5" class="cabecera_css">INSUMO</td>
								<td style="text-align: center;" colspan="1" class="cabecera_css">CANTIDAD</td>
							</tr>
						</thead>
						<tbody>

							@foreach ($insumos as $value)
							@if($value->codigo !== "16")
							<tr>
								<td colspan="5" class="css_descripcion">{{ $value->descripcion }}</td>
								<td colspan="1" class="number">{{ $value->cantidad }}</td>
							</tr>
							@endif
							@endforeach

							@foreach ($array as $value)
							<tr>
								<td colspan="5" class="css_descripcion">{{ $value->descripcion }}</td>
								<td colspan="1" class="number">{{ $value->cantidad }}</td>
							</tr>
							@endforeach

						</tbody>
					</table>
				</td>
				<td colspan="6" style="width: 50%; vertical-align: top;">
					<!-- Tabla de Medicinas -->
					<table border="0.8px" cellspacing="0" cellpadding="0" class="table">
						<thead>
							<tr>
								<td style="text-align: center;" colspan="5" class="cabecera_css">MEDICAMENTOS</td>
								<td style="text-align: center;" colspan="1" class="cabecera_css">CANTIDAD</td>
							</tr>
						</thead>
						<tbody>
							@foreach ($array1 as $value)
							<tr>
								<td colspan="5" class="css_descripcion">{{ $value->descripcion }}</td>
								<td colspan="1" class="number">{{ $value->cantidad }}</td>
							</tr>
							@endforeach
							@foreach ($medicinas as $value)
							@if($value->codigo !== "465")
							<tr>
								<td colspan="5" class="css_descripcion">{{ $value->descripcion }}</td>
								<td colspan="1" class="number">{{ $value->cantidad }}</td>
							</tr>
							@endif
							@endforeach

						</tbody>
					</table>
				</td>

			</tr>
		</table>

		<!--table width="100%" style="margin-top: 30px;">
			<tr>
				<td width="50%" align="center" valign="top">

					@if(!is_null($archivo_plano->id_anestesiologo))
					@php
					$firma_anestesiologo = \Sis_medico\Firma_Usuario::where('id_usuario',$archivo_plano->id_anestesiologo)->first();
					$user_anestesiologo = \Sis_medico\User_espe::where('usuid',$archivo_plano->id_anestesiologo)->first();
					$especialidad = \Sis_medico\Especialidad::find($user_anestesiologo->espid);
					@endphp
					<p style="text-align: center;">
						@if(!is_null($firma_anestesiologo) && !is_null($firma_anestesiologo->firma))
						<img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma_anestesiologo->firma}}" align=center hspace=12><br>
						@else
						<br><br><br>
						<b style="font-size: 10px;">@if(!is_null($firma_anestesiologo)) @if($firma_anestesiologo->sexo == '2') Dra. @else Dr. @endif {{$firma_anestesiologo->doctor->apellido1}} @if($firma_anestesiologo->doctor->apellido2!='N/A'){{$firma_anestesiologo->doctor->apellido2}}@endif {{$firma_anestesiologo->doctor->nombre1}} @endif</b><br>
						@endif
						<br>
						@if(!is_null($firma_anestesiologo)) @if($firma_anestesiologo->sexo == '2') Dra. @else Dr. @endif {{$firma_anestesiologo->doctor->apellido1}}
						@if($firma_anestesiologo->doctor->apellido2!='N/A') {{$firma_anestesiologo->doctor->apellido2}} @endif {{$firma_anestesiologo->doctor->nombre1}}
						@if($firma_anestesiologo->doctor->nombre2!='N/A') {{$firma_anestesiologo->doctor->nombre2}}@endif<br>
						@if(!is_null($especialidad)) {{$especialidad->nombre}} @endif <br> cedula No. {{$firma_anestesiologo->doctor->id}} @endif
					</p>
					@endif
				</td>

				<td width="50%" align="center" valign="top">

					@if($archivo_plano->id_empresa == '1307189140001')
					<img width=200 height=auto src="{{base_path().'/storage/app/logo/firma_robles.png'}}" align=right hspace=12><br>
					@else
					@if(!is_null($archivo_plano->id_doctor))
					@php
					$firma = \Sis_medico\Firma_Usuario::where('id_usuario',$archivo_plano->id_doctor)->first();
					$user_esp = \Sis_medico\User_espe::where('usuid',$archivo_plano->id_doctor)->first();
					$especialidad = \Sis_medico\Especialidad::find($user_esp->espid);
					@endphp

					<p style="text-align: center;">
						@if(!is_null($firma) && !is_null($firma->firma))
						<img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma->firma}}" align=center hspace=12><br>
						@else
						<br><br><br>
						<b style="font-size: 10px;">@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}} @if($firma->doctor->apellido2!='N/A'){{$firma->doctor->apellido2}}@endif {{$firma->doctor->nombre1}} @endif</b><br>
						@endif
						<br>
						@if(!is_null($firma)) @if($firma->sexo == '2') Dra. @else Dr. @endif {{$firma->doctor->apellido1}}
						@if($firma->doctor->apellido2!='N/A') {{$firma->doctor->apellido2}} @endif {{$firma->doctor->nombre1}}
						@if($firma->doctor->nombre2!='N/A') {{$firma->doctor->nombre2}}@endif<br>
						@if(!is_null($especialidad)) {{$especialidad->nombre}} @endif <br> cedula No. {{$firma->doctor->id}} @endif
					</p>
					@endif
					@endif
				</td>
			</tr>
		</table-->
		@php
		$tieneAnestesiologo = !is_null($archivo_plano->id_anestesiologo);
		@endphp
		<table width="100%" style="margin-top: 30px;">
			<tr>

				{{-- FIRMA ANESTESIÓLOGO --}}
				@if($tieneAnestesiologo)
				<td width="50%" align="center" valign="top">
					@php
					$firma_anestesiologo = \Sis_medico\Firma_Usuario::where('id_usuario',$archivo_plano->id_anestesiologo)->first();
					$user_anestesiologo = \Sis_medico\User_espe::where('usuid',$archivo_plano->id_anestesiologo)->first();
					$especialidad_an = !is_null($user_anestesiologo)
					? \Sis_medico\Especialidad::find($user_anestesiologo->espid)
					: null;
					@endphp

					<p style="text-align: center;">
						@if(!is_null($firma_anestesiologo) && !is_null($firma_anestesiologo->firma))
						<img width="150" height="60"
							src="{{ base_path().'/storage/app/avatars/'.$firma_anestesiologo->firma }}"><br>
						@else
						<br><br><br>
						@endif

						@if(!is_null($firma_anestesiologo))
						{{ $firma_anestesiologo->sexo == '2' ? 'Dra.' : 'Dr.' }}
						{{ $firma_anestesiologo->doctor->apellido1 }}
						{{ $firma_anestesiologo->doctor->apellido2 != 'N/A' ? $firma_anestesiologo->doctor->apellido2 : '' }}
						{{ $firma_anestesiologo->doctor->nombre1 }}
						{{ $firma_anestesiologo->doctor->nombre2 != 'N/A' ? $firma_anestesiologo->doctor->nombre2 : '' }}
						<br>
						{{ $especialidad_an->nombre ?? '' }} <br>
						cédula No. {{ $firma_anestesiologo->doctor->id }}
						@endif
					</p>
				</td>
				@endif

				{{-- FIRMA DOCTOR --}}
				<td
					align="center"
					valign="top"
					width="{{ $tieneAnestesiologo ? '50%' : '100%' }}"
					colspan="{{ $tieneAnestesiologo ? 1 : 2 }}">
					@if($archivo_plano->id_empresa == '1307189140001')
					<img width="200" src="{{ base_path().'/storage/app/logo/firma_robles.png' }}">
					@else
					@if(!is_null($archivo_plano->id_doctor))
					@php
					$firma = \Sis_medico\Firma_Usuario::where('id_usuario',$archivo_plano->id_doctor)->first();
					$user_esp = \Sis_medico\User_espe::where('usuid',$archivo_plano->id_doctor)->first();
					$especialidad = !is_null($user_esp)
					? \Sis_medico\Especialidad::find($user_esp->espid)
					: null;
					@endphp

					<p style="text-align: center;">
						@if(!is_null($firma) && !is_null($firma->firma))
						<img width="150" height="60"
							src="{{ base_path().'/storage/app/avatars/'.$firma->firma }}"><br>
						@else
						<br><br><br>
						@endif

						{{ $firma->sexo == '2' ? 'Dra.' : 'Dr.' }}
						{{ $firma->doctor->apellido1 }}
						{{ $firma->doctor->apellido2 != 'N/A' ? $firma->doctor->apellido2 : '' }}
						{{ $firma->doctor->nombre1 }}
						{{ $firma->doctor->nombre2 != 'N/A' ? $firma->doctor->nombre2 : '' }}
						<br>
						{{ $especialidad->nombre ?? '' }} <br>
						cédula No. {{ $firma->doctor->id }}
					</p>
					@endif
					@endif
				</td>

			</tr>
		</table>


	</main>
</body>

</html>