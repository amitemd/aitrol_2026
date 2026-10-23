<!DOCTYPE html>
<html>

<head>
	<title>ORDEN 013</title>
	<style>
		@page {
			margin: 0;
		}

		/** Define now the real margins of every page in the PDF **/
		body {
			margin-top: 10px;
			margin-left: 10px;
			margin-right: 10px;
			margin-bottom: 10px;
			font-size: 10px;
		}

		/** Define the header rules **/
		header {
			position: fixed;
			top: 0cm;
			left: 0cm;
			right: 0cm;
			height: 1cm;
		}

		/** Define the footer rules **/
		footer {
			position: fixed;
			bottom: 0cm;
			left: 0cm;
			right: 0cm;
			height: 2cm;
		}

		table {
			width: 100px;
			border-collapse: collapse;
		}

		.table2d {
			border: none;
			text-align: center;
		}

		.titulo {
			background-color: #C7C3C2;
		}

		p {
			padding-top: 0;
			padding-bottom: 0;
			margin: 0;
		}

		div {
			padding-top: 0;
			padding-bottom: 0;
		}

		.txt_vertical {
			writing-mode: vertical-lr;
			transform: rotate(-90deg);
			letter-spacing: 2px;
			word-wrap: break-word;
		}

		.td-border {
			border: 1px solid;

		}

		.td-not-border-top {
			border-top: 1px solid;
			border-left: 1px solid;
			border-right: 1px solid;
		}

		.td-not-border-bottom {
			border-bottom: 1px solid;
			border-left: 1px solid;
			border-right: 1px solid;
		}

		.td-not-border-right {
			border-bottom: 1px solid;
			border-left: 1px solid;
			border-top: 1px solid;
		}

		.td-not-border-left {
			border-bottom: 1px solid;
			border-top: 1px solid;
			border-right: 1px solid;
		}

		.td-border-top {
			border-top: 1px solid;
		}

		.td-border-bottom {
			border-bottom: 1px solid;
		}

		.td-border-left {
			border-left: 1px solid;
		}

		.td-border-right {
			border-right: 1px solid;
		}

		.bg-blue {
			background-color: #DDEBF7;
		}

		.bg-green {
			background-color: #BADAC8;
		}

		.bg-yellow {
			background-color: #F1FD77;
		}

		.text-center {
			text-align: center;
		}

		.text-left {
			text-align: left;
		}

		.text-right {
			text-align: right;
		}
	</style>
</head>

<body>
	<header>
		
	</header>
	<main>
		<div class="box-body card" style="text-align: center">
			@if(!is_null($empresa))
				<img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:200px;height: 50px">
			@endif
		</div>
		<table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
			<tbody>
				<tr>
					<td class="bg-green"><b>INSTITUCION DEL SISTEMA</b></td>
					<td class="bg-green"><b>UNIDAD OPERATIVA</b></td>
					<td class="bg-green"><b>COD UO</b></td>
					<td colspan="3" style="text-align: center;" class="bg-green"><b>COD LOCALIZACION</b></td>
					<td class="bg-green" rowspan="2"><b>NUMERO DE HISTORIA CLINICA</b></td>
				</tr>
				<tr>
					<td rowspan="2"><b></b>{{ $seguro_nombre }}</td>
					<td rowspan="2"><b>{{ $empresa->nombrecomercial }}</b></td>
					<td rowspan="2"><b>{{$paciente->cod_val}}</b></td>
					<td class="bg-green"><b>PARROQUIA</b></td>
					<td class="bg-green"><b>CANTON</b></td>
					<td class="bg-green"><b>PROVINCIA</b></td>
				</tr>
				<tr>
					<td>{{$empresa->parroquia}}</td>
					<td>{{$empresa->canton}}</td>
					<td>{{$empresa->provincia}}</td>
					<td>{{ $paciente->id }}</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table cellpadding="0" cellspacing="0" style="width: 100%;">
			<tbody>
				<tr>
					<td class="bg-green text-center td-not-border-right"><b>APELLIDO PATERNO</b></td>
					<td class="bg-green text-center td-border-top td-border-bottom"><b>APELLIDO MATERNO</b></td>
					<td class="bg-green text-center td-border-top td-border-bottom"><b>PRIMER NOMBRE</b></td>
					<td class="bg-green text-center td-not-border-left"><b>SEGUNDO NOMBRE</b></td>
					<td class="bg-green text-center td-border"><b>EDAD</b></td>
					<td class="bg-green text-center td-border"><b>CEDULA DE CIUDADANIA</b></td>
				</tr>
				<tr>
					<td class="text-center td-border">{{ $paciente->apellido1 }}</td>
					<td class="text-center td-border">{{ $paciente->apellido2 }}</td>
					<td class="text-center td-border">{{ $paciente->nombre1 }}</td>
					<td class="text-center td-border">{{ $paciente->nombre2 }}</td>
					<td class="text-center td-border">{{ $age }}</td>
					<td class="text-center td-border">{{ $paciente->id }}</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table cellpadding="0" cellspacing="0" style="width: 100%;">
			<tbody>
				<tr>
					<td class="text-center bg-green td-not-border-right" style="width: 100px;"><b>&nbsp;</b></td>
					<td class="text-center bg-green td-border-top td-border-bottom">SERVICIO</td>
					<td class="text-center bg-green td-border-top td-border-bottom">SALA</td>
					<td class="text-center bg-green td-border-top td-border-bottom">CAMA</td>
					<td class="text-center bg-green td-border-top td-border-bottom" colspan="6">PRIORIDAD</td>
					<td class="text-center bg-green td-not-border-left">FECHA DE TOMA</td>
				</tr>
				<tr style="text-align: center;">
					<td class="td-border"></td>
					<td class="td-border">AMBULATORIO</td>
					<td class="td-border"></td>
					<td class="td-border"></td>
					<td class="td-border">URGENTE</td>
					<td class="td-border bg-yellow">@if( $formulario013->prioridad == 0 ) X @endif</td>
					<td class="td-border">RUTINA</td>
					<td class="td-border bg-yellow">@if( $formulario013->prioridad == 1 ) X @endif </td>
					<td class="td-border">CONTROL</td>
					<td class="td-border bg-yellow">@if( $formulario013->prioridad == 2 ) X @endif</td>
					<td class="td-border">{{ $fecha_toma }}</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
			<thead>
				<tr>
					<td colspan="6" style="font-size: 14px;" class="bg-blue"><b>1. ESTUDIO SOLICITADO</b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 5%;" class="bg-green">HISTOPATOLOGIA</td>
					<td style="width: 5%;" class="bg-yellow text-center">@if($formulario013->estudio_solicitado == 0) X @endif</td>
					<td style="width: 5%;" class="bg-green">CITOLOGIA</td>
					<td style="width: 5%;" class="bg-yellow text-center">@if($formulario013->estudio_solicitado == 1) X @endif</td>
					<td style="width: 5%;" class="bg-green">DESCRIPCION</td>
					<td>{{ $formulario013->descripcion_estudio }}</td>
				</tr>
			</tbody>
		</table>
		<div style="border-left: 1px solid;border-top: 1px solid;border-right: 1px solid;font-size: 14px;" class="bg-blue"><b>2. RESUMEN CLINICO</b></div>
		<div style="border: 1px solid black;font-size: 10px important!;"><?php echo $formulario013->resumen_clinico ?></div>
		<table style="width: 100%; padding:2px;">
			<thead>
				<tr style="vertical-align: middle;">
					<td class="td-not-border-right bg-blue" style="font-size: 14px;"><b>3. DIAGNOSTICOS</b></td>
					<td style="font-size: 10px;border-top: 1px solid;border-bottom: 1px solid;" class="bg-blue text-center"><b>PRE=PRESUNTIVO <br> DEF=DEFINITIVO <br></b></td>
					<td class="td bg-blue text-center" style="width: 5% !important;border-top: 1px solid;border-bottom: 1px solid;">CIE</td>
					<td class="td bg-blue text-center" style="width: 5% !important;border-top: 1px solid;border-bottom: 1px solid;">PRE</td>
					<td class="td-not-border-left bg-blue text-center" style="width: 5%">DEF</td>
					<td style="text-align: left;font-size: 14px;" class="bg-blue td-not-border-right"><b>4. MUESTRA O PIEZA</b></td>
					<td style="text-align: right;" class="bg-blue td-not-border-left"><b style="font-size: 7px;">DESCRIBIR EL PROCEDIMIENTO, ORIGEN,<br> NUMERO Y ZONA</b></td>
				</tr>
			</thead>
			<tbody>
				@for($ind = 0; $ind < 7; $ind ++)
					@if(isset($diagnosticos[$ind]))
						@php
							$value = $diagnosticos[$ind];
							$cie10 = Sis_medico\Cie_10_3::where('id',$value->cie10)->first();
							if(is_null($cie10)){
								$cie10 = Sis_medico\Cie_10_4::where('id',$value->cie10)->first();
							}
						@endphp
					@endif	
					<tr>
						<td class="td-border" colspan="2">@if(isset($diagnosticos[$ind])){{ $cie10->descripcion }} @else &nbsp; @endif</td>
						<td class="td-border">@if(isset($diagnosticos[$ind])){{ $value->cie10 }} @else &nbsp;  @endif</td>
						<td class="td-border bg-yellow text-center">@if(isset($diagnosticos[$ind])) @if( $value->presuntivo_definitivo =='PRESUNTIVO') X @endif @else &nbsp;  @endif</td>
						<td class="td-border bg-yellow text-center">@if(isset($diagnosticos[$ind])) @if( $value->presuntivo_definitivo =='DEFINITIVO') X @endif @else &nbsp;  @endif</td>
						<td class="td-border" colspan="2">@if(isset($biopsias[$ind])) {{$biopsias[$ind]->descripcion_frasco}} @else &nbsp; @endif</td>
					</tr>
				@endfor
			</tbody>	
		</table>
		
		<br>
		<div style="border-left: 1px solid;border-top: 1px solid;border-right: 1px solid;font-size: 14px;" class="bg-blue"><b>5. TRATAMIENTO QUE RECIBE</b></div>
		<div style="border: 1px solid black;font-size: 10px important!;"><?php echo $formulario013->tratamiento ?></div>
		<br>
		<table style="width: 100%;">
			<thead>
				<tr>
					<td colspan="21" style="font-size: 14px;" class="bg-blue td-border"><b>6. DATOS BÁSICOS PARA CITOLOGÍA VAGINAL</b></td>
				</tr>
				<tr style="text-align: center;">
					<td colspan="6" class="bg-green td-border" style="font-size: 8px;">MATERIAL</td>
					<td colspan="4" class="bg-green td-border" style="font-size: 8px;">ANTICONCEPCION</td>
					<td rowspan="2" class="bg-green td-border" style="font-size: 8px; height: 50px;">
						<p style="transform: rotate(270deg);">TERAPIA HORMONAL</p>
					</td>
					<td colspan="3" class="bg-green td-border" style="font-size: 8px;">EDADES DE:</td>
					<td colspan="4" class="bg-green td-border" style="font-size: 8px;">PARIDAD</td>
					<td colspan="3" class="bg-green td-border" style="font-size: 8px;">FECHAS</td>
				</tr>
			</thead>
			<tbody>
				<tr class="text-center " style="font-size: 6px;width: 100%;">
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ENDOCER VIX</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ENDOCER VIX</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">PARED VAGINAL</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">UNION ESCAMO COLUMNA</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">MUÑON CERVICAL</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">OTRO</p>
					</td>

					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ORAL O INYECTABLE</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">DIU</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">LIGADURA</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">OTRO</p>
					</td>

					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">MENARQUIA</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">MENOPAUSIA</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">INICIO DE RELACIONES SEXUALES</p>
					</td>

					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">GESTACIONES</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">PARTOS</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ABORTOS</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">CESÁREAS</p>
					</td>

					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ÚLTIMA MENSTRUACIÓN</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ÚLTIMO PARTO</p>
					</td>
					<td class="bg-green td-border" style="height: 50px;">
						<p style="transform: rotate(270deg); padding: 0px;">ÚLTIMA CITOLOGÍA</p>
					</td>
				</tr>
				<tr style="width: 100%;">
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>

					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>
					<td rowspan="5" class="bg-yellow td-border">&nbsp;</td>

					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>

					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>

					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>
					<td rowspan="5" class="td-border">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table style="width: 100%">
			<tr>
				<td colspan="6"></td>
				<td class="text-center"><b>CÓDIGO</b></td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<td class="td-border bg-green"><b>FECHA</b></td>
				<td class="td-border" style="width: 50px;">{{ $fecha }}</td>
				<td class="td-border bg-green"><b>HORA</b></td>
				<td class="td-border">{{ $hora }}</td>
				<td class="td-border bg-green"><b>NOMBRE DEL PROFESIONAL</b></td>
				<td class="td-border" style="width: 150px;">@if(!is_null($doctor)) {{ $doctor->nombre1 }} <br> {{ $doctor->apellido1 }} @endif</td>
				<td class="td-border">@if(!is_null($doctor)) {{ $doctor->id }} @endif</td>
				<td class="td-border bg-green"><b>FIRMA</b></td>
				<td class="td-border" style="text-align: center;width: 100px;">
					@if($ruta_firma!="")
					<img src="{{$ruta_firma}}" style="width: 50%;"><br>
					@endif
				</td>
				<td class="td-border bg-green" style="text-align: center;"><b>NÚMERO DE HOJA</b></td>
				<td class="td-border"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
			</tr>
			<tr>
				<td colspan="6"><b>SNS-MSP/HCU-form.013A/2021</b></td>
				<td colspan="5" class="text-right"><b>HISTOPATOLOGÍA-SOLICITUD</b></td>
			</tr>
		</table>
	</main>
</body>

</html>