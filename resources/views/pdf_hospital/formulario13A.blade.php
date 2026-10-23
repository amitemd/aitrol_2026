<!DOCTYPE html>
<html>

<head>
    <title>Formulario 13A</title>
    <style>
        @page {
            margin: 2 0;
        }

        /* Define now the real margins of every page in the PDF */
        body {
            margin-top: 5px;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 1px;
            font-size: 10px;
        }



        /* Define the header rules */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
        }

        /* Define the footer rules */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        .container {
            display: flex;
        }

        table {
            width: 100%;
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

        .bg-purple {
            background-color: #CCCCFF;
        }

        .bg-turquoise {
            background-color: #40E0D0;
        }

        .bg-celeste {
            background-color: #00BFFF;
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

        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        .rotate {
            writing-mode: vertical-lr;
            transform: rotate(-90deg);
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <header>

    </header>
    <main>
        <div class="box-body card" style="text-align: center;">
			@if(!is_null($empresa))
                <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:150px;height: 60px">
			@endif
		</div>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="15" style="font-size: 10px;" class="bg-blue"><b>A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green text-center" style="width: 20%;"><b>INSTITUCION DEL SISTEMA</b></td>
                    <td colspan="2" class="bg-green text-center"><b>UNICODIGO</b></td>
                    <td colspan="2" class="bg-green text-center" style="width: 22%;"><b>ESTABLECIMIENTO DE SALUD</b></td>
                    <td colspan="5" class="bg-green text-center"><b>NÚMERO DE HISTORIA CLÍNICA ÚNICA</b></td>
                    <td colspan="5" class="bg-green text-center"><b>NÚMERO DE ARCHIVO</b></td>
                </tr>
                <tr>
                    <td class="text-center" >&nbsp;{{$formulario013->historiaclinica->seguro->nombre}} &nbsp;</td>
                    <td colspan="2" class="text-center">{{$formulario013->historiaclinica->agenda->empresa->unicodigo}}</td>
                    <td colspan="2" class="text-center">{{$formulario013->historiaclinica->agenda->empresa->nombrecomercial}}</td>
                    <td class="text-center" colspan="5">{{$formulario013->id_paciente}}</td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="2" class="bg-green text-center" style="height: 36px;"><b>PRIMER APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>SEGUNDO APELLIDO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>PRIMER NOMBRE</b></td>
                    <td rowspan="2" class="bg-green text-center" style="width: 15%;"><b>SEGUNDO NOMBRE</b></td>
                    <td rowspan="2" class="bg-green text-center"><b>FECHA DE NACIMIENTO</b></td>
                    <td rowspan="2" class="bg-green text-center"><b>SEXO</b></td>
                    <td rowspan="2" colspan="2" class="bg-green text-center"><b>EDAD</b></td>
                    <td colspan="4" class="bg-green text-center" style="font-size: 6px" ><b>CONDICIÓN EDAD (MARCAR)</b></td>
                </tr>
                <tr>
                    <td class="bg-green text-center">H</td>
                    <td class="bg-green text-center">D</td>
                    <td class="bg-green text-center">M</td>
                    <td class="bg-green text-center">A</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2" style="height: 25px;">{{ $paciente->apellido1 }}</td>
                    <td class="text-center" colspan="2">{{ $paciente->apellido2 }}</td>
                    <td class="text-center" colspan="2">{{ $paciente->nombre1 }}</td>
                    <td class="text-center">{{ $paciente->nombre2 }}</td>
                    <td class="text-center">{{ $paciente->fecha_nacimiento}}</td>
                    <td class="text-center">@if($paciente->sexo==1) Masculino @else Femenino @endif</td>
                    <td class="text-center" colspan="2">{{ $age }}</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">X</td>
                </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="13" style="font-size: 10px;" class="bg-blue"><b>B. DATOS DEL SERVICIO Y PRIORIDAD</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center bg-green" colspan="6">SERVICIO</td>
                    <td class="text-center bg-green">ESPECIALIDAD</td>
                    <td class="text-center bg-green" colspan="4">PRIORIDAD</td>
                    <td class="text-center bg-green">HORA DE TOMA</td>
                    <td class="text-center bg-green">FECHA DE TOMA</td>
                </tr>
                <tr>
                    <td class="text-center bg-green">EMERGENCIA</td>
                    <td class="text-center"></td>
                    <td class="text-center bg-green">CONSULTA EXTERNA</td>
                    <td class="text-center"></td>
                    <td class="text-center bg-green">HOSPITALIZACIÓN</td>
                    <td class="text-center"></td>
                    <td class="text-center">{{$especialidad->nombre}}</td>
                    <td class="text-center bg-green">URGENTE</td>
                    <td class="text-center">@if( $formulario013->prioridad == 0 ) X @endif</td>
                    <td class="text-center bg-green">RUTINA</td>
                    <td class="text-center">@if( $formulario013->prioridad != 0 ) X @endif</td>
                    <td class="text-center">{{ $hora_toma }}</td>
                    <td class="text-center">{{ $fecha_toma }}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="11" style="font-size: 10px;" class="bg-blue"><b>C. ESTUDIO SOLICITADO</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="text-center bg-green" style="width: 5%;">HISTOPATOLOGÍA</td>
                    <td>@if($formulario013->estudio_solicitado == 0) X @endif</td>
                    <td class="text-center bg-green">CITOLOGÍA</td>
                    <td>@if($formulario013->estudio_solicitado == 1) X @endif</td>
                    <td class="text-center bg-green">REVISIÓN DE LAMINILLAS</td>
                    <td>@if($formulario013->estudio_solicitado == 2) X @endif</td>
                    <td class="text-center bg-green">TRANSOPERATORIO</td>
                    <td>@if($formulario013->estudio_solicitado == 3) X @endif</td>
                    <td class="text-center bg-green" style="width: 20%;">OTROS TIPO DE ESTUDIO:</td>
                    <td>@if($formulario013->estudio_solicitado == 4) X @endif</td>
                </tr>
                <tr>
                    <td class="text-center bg-green">DESCRIPCIÓN</td>
                    <td colspan="10">{{ $formulario013->descripcion_estudio }}</td>
                </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="3" style="font-size: 12px;" class="bg-blue"><b>D. RESUMEN CLÍNICO @for($count=0; $count<=45;$count++) &nbsp; @endfor </b> <b style="font-size: 8px;"> REGISTRAR DE MANERA OBLIGATORIA TODOS LOS CAMPOS</b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bg-green" style="width: 50px;">&nbsp;&nbsp;CUADRO CLÍNICO</td>
                    <td colspan="2"><span style="font-size: 10px; important!"><?php echo strip_tags($formulario013->resumen_clinico)?></span></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="bg-green">&nbsp;&nbsp;RESULTADOS DE LABORATORIO</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="bg-green">&nbsp;&nbsp;DATOS DE IMAGEN </td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td class="bg-green">&nbsp;&nbsp;TRATAMIENTO</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" class="bg-green" style="width: 35%;">&nbsp;&nbsp;PROCEDIMIENTO REALIZADO PARA OBTENCIÓN MUESTRA</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; padding:2px;">
			<tbody>
                <tr style="vertical-align: middle;">
                    <td class="td-not-border-right bg-blue" style="width: 10px;"></td>
					<td class="td-not-border-left bg-blue" style="font-size: 10px;"><b>E. DIAGNOSTICOS</b></td>
					<td style="font-size: 10px;border-top: 1px solid;border-bottom: 1px solid;" class="bg-blue text-center"><b>PRE=PRESUNTIVO <br> DEF=DEFINITIVO <br></b></td>
					<td class="td bg-blue text-center" style="width: 5% !important;border-top: 1px solid;border-bottom: 1px solid;">CIE</td>
					<td class="td bg-blue text-center" style="width: 5% !important;border-top: 1px solid;border-bottom: 1px solid;">PRE</td>
					<td class="td-not-border-left bg-blue text-center" style="width: 5%">DEF</td>
					<td style="text-align: left;font-size: 10px;" class="bg-blue td-not-border-right"><b>F. MUESTRA O PIEZA / NUMERO</b></td>
					<td style="text-align: right;" class="bg-blue td-not-border-left"><b style="font-size: 7px;">DESCRIBIR EL ORIGEN Y<br> NUMERO DE MUESTRAS ENVIADAS</b></td>
				</tr>
				@for($ind = 0; $ind < 5; $ind ++)
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
                        <td class="td-border" style="width: 10px;">{{$ind+1}}.</td>
						<td class="td-border" colspan="2">@if(isset($diagnosticos[$ind])){{ $cie10->descripcion }} @else &nbsp; @endif</td>
						<td class="td-border">@if(isset($diagnosticos[$ind])){{ $value->cie10 }} @else &nbsp;  @endif</td>
						<td class="td-border bg-yellow text-center">@if(isset($diagnosticos[$ind])) @if( $value->presuntivo_definitivo =='PRESUNTIVO') X @endif @else &nbsp;  @endif</td>
						<td class="td-border bg-yellow text-center">@if(isset($diagnosticos[$ind])) @if( $value->presuntivo_definitivo =='DEFINITIVO') X @endif @else &nbsp;  @endif</td>
						<td class="td-border" colspan="2">@if(isset($biopsias[$ind])) {{$biopsias[$ind]->descripcion_frasco}} @else &nbsp; @endif</td>
					</tr>
				@endfor
			</tbody>	
		</table>
        <table>
            <thead>
                <tr>
                    <td colspan="21" style="font-size: 10px;" class="bg-blue td-border"><b>G. DATOS BÁSICOS PARA CITOLOGÍA VAGINAL</b></td>
                </tr>
                <tr style="text-align: center;">
                    <td colspan="6" class="bg-green td-border" style="font-size: 8px;">MATERIAL</td>
                    <td colspan="4" class="bg-green td-border" style="font-size: 8px;">ANTICONCEPCIÓN</td>
                    <td rowspan="2" class="bg-green td-border" style="font-size: 8px; height: 50px;">
                        <p class="rotate">TERAPIA HORMONAL</p>
                    </td>
                    <td colspan="3" class="bg-green td-border" style="font-size: 8px;">EDAD DE:</td>
                    <td colspan="4" class="bg-green td-border" style="font-size: 8px;">PARIDAD</td>
                    <td colspan="3" class="bg-green td-border" style="font-size: 8px;">FECHAS</td>
                </tr>
                <tr class="text-center" style="font-size: 6px; width: 100%;">
                    <td class="bg-green td-border" style="height: 70px;">
                        <p class="rotate">ENDOCERVIX</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">EXOCERVIX</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">VAGINAL</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">ENDOMETRIAL</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">OTROS</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">NINGUNA</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">HORMONAL</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">DIU</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">LIGADURA</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">OTRO</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">MENARQUIA</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">MENOPAUSIA</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">INICIO DE RELACIONES SEXUALES</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">GESTACIONES</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">PARTOS</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">ABORTOS</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p class="rotate">CESÁREAS</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p>ÚLTIMA MENSTRUACIÓN</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p >ÚLTIMO PARTO</p>
                    </td>
                    <td class="bg-green td-border" style="height: 50px;">
                        <p >ÚLTIMA CITOLOGÍA</p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                    <td class="td-border" style="height: 20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" class="td-border bg-green" style="font-size: 6;">DESCRIPCIÓN MACROSCÓPICA</td>
                    <td colspan="14"></td>
                    <td rowspan="2" class="td-border text-center bg-green">DIAGNÓSTICO DE ÚLTIMA CITOLOGÍA:</td>
                    <td class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
                <tr>
                    <td colspan="18" class="td-border">&nbsp;</td>
                    <td class="td-border"></td>
                    <td class="td-border"></td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="width: 100%">
            <thead>
                <tr>
                    <td class="bg-blue">H. BIOLOGÍA MOLECULAR Y GENÉTICA</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" style="width: 100%">
            <thead>
                <tr>
                    <td colspan="6" style="font-size: 10px;" class="bg-blue"><b> I. DATOS DEL PROFESIONAL RESPONSABLE </b></td>
                </tr>
            </thead>
            <tr>
                <td class="text-center bg-green" style="width: 15%;">FECHA <br> (aaaa-mm-dd)</td>
                <td class="text-center bg-green" style="width: 15%;"> HORA DE SOLICITUD <br> (hh:mm) </td>
                <td class="text-center bg-green" style="width: 23%;">PRIMER NOMBRE</td>
                <td class="text-center bg-green" colspan="2" style="width: 23%;">PRIMER APELLIDO</td>
                <td class="text-center bg-green" style="width: 23%;">SEGUNDO APELLIDO</td>
            </tr>
            <tr>
                <td style="height: 25px;" class="text-center">{{ $fecha }}</td>
                <td class="text-center">{{ $hora }}</td>
                <td class="text-center">@if(!is_null($doctor)) {{ $doctor->nombre1 }} @endif</td>
                <td class="text-center" colspan="2">@if(!is_null($doctor)) {{ $doctor->apellido1 }} @endif</td>
                <td class="text-center">@if(!is_null($doctor)) {{ $doctor->apellido2 }} @endif</td>
            </tr>
            <tr>
                <td class="text-center bg-green" colspan="2" style="font-size: 9px;">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</td>
                <td colspan="2" class="text-center bg-green">FIRMA</td>
                <td colspan="2" class="text-center bg-green">SELLO</td>
            </tr>
            <tr>
                <td class="text-center" colspan="2" style="height: 38px;">@if(!is_null($doctor)) {{ $doctor->id }} @endif</td>
                <td colspan="2" class="text-center">
                    @if($ruta_firma!="")
					<img src="{{$ruta_firma}}" style="height: 50px;"><br>
					@endif
                </td>
                <td colspan="2" class="text-center">
                    @if(!is_null($doctor)) Dr(a). {{$doctor->apellido1}} @if($doctor->apellido2!='N/A'){{$doctor->apellido2}}@endif {{$doctor->nombre1}} @if($doctor->nombre2!='N/A'){{$doctor->nombre2}}@endif @endif<br>
                    @if(!is_null($especialidad)) {{$especialidad->nombre}} @endif<br>
                    @if(!is_null($doctor)) MSP No. {{$doctor->id}} @endif
                </td>
            </tr>
            <tr>
                <td class="text-center bg-green" style="width: 12%;">FECHA DE RECEPCIÓN <br> (aaaa-mm-dd)</td>
                <td class="text-center bg-green" style="width: 12%;">HORA DE RECEPCIÓN <br> (hh:mm) </td>
                <td class="text-center bg-green" style="width: 23%; font-size: 8px;" sty>NOMBRE Y APELLIDO DE LA PERSONA QUIEN ENTREGA LA MUESTRA</td>
                <td class="text-center bg-green" colspan="2" style="width: 23%; font-size: 8px;">NOMBRE Y APELLIDO DE LA PERSONA QUIEN RECIBE LA MUESTRA</td>
                <td class="text-center bg-green" style="width: 23%; font-size: 9px;">CÓDIGO INTERNO DE MUESTRA</td>
            </tr>

            <tr>
                <td style="height: 25px;" class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center" colspan="2"></td>
                <td class="text-center"></td>
            </tr>
           
        </table>
        <p>SNS-MSP/HCU-form.013A/2021 @for($count=0; $count<=80;$count++) &nbsp; @endfor <b style="font-size: 10px;">ANATOMÍA PATOLÓGICA - SOLICITUD</b></p>

    </main>

</body>

</html>