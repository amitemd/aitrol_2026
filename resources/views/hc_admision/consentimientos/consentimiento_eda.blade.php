<!DOCTYPE html>
<html>
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
	<title>GC-EDA-Consentimiento informado Endoscopia Digestiva Alta</title>
	<style>
		.table {
			border-collapse: collapse;
		}

		.table,
		.td {
			border: 1px solid black;
			bottom: 10px;
		}

		.th {
			border: 1px solid black;
			text-align: center;
		}

		.centrado {
			width: 250px;
		}
	</style>
</head>

<body>
	<div id="content">
		<table>
			<tr>
				<td style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA ENDOSCOPIA DIGESTIVA ALTA </td>
			</tr>
			<tr>
				<td style="font-weight: bold;">2. Nombre del Establecimiento de Salud: </td>
			</tr>
			<tr>
				<td style="font-weight: bold;">3. Servicios del Establecimiento de Salud: </td>
			</tr>
			<tr>
				<td style="font-weight: bold;">4. Número de cedula/HCU del Paciente: {{$paciente->id}}</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">5. Fecha: {{substr($consentimiento->created_at, 0, 11)}}</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">6. Hora: {{substr($consentimiento->created_at, 11, 20)}}</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">7. Información del Paciente: </td>
			</tr>
		</table>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Apellido Paterno</th>
				<th class="th">Apellido Materno</th>
				<th class="th">Nombres</th>
				<th class="th">Fecha de Nacimiento</th>
				<th class="th">Edad</th>
			</tr>
			<tr>
				<td class="td">{{$paciente->apellido1}}</td>
				<td class="td">{{$paciente->apellido2}}</td>
				<td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">{{$paciente->fecha_nacimiento}}</td>
				<td class="td">{{$edad}}</td>
			</tr>
		</table>
		<p style="text-align:justify;">Este documento sirve para que usted, o quien lo represente, dé su consentimiento para esta exploración. Eso significa que nos autoriza a realizarla.</p>
		<p style="text-align:justify;">Puede usted retirar este consentimiento cuando lo desee. Firmarlo no le obliga a efectuarse la exploración. De su rechazo no se derivará ninguna consecuencia adversa respecto a la calidad del resto de la atención recibida. Lea la información siguiente.</p>
		<table style="width:100%; ">
			<tr>
				<td style="font-weight: bold;">8. Tipo de Atención: @if(($agenda->est_amb_hos)==1) ambulatorio @elseif(($agenda->est_amb_hos)==0)hospitalizado @endif
				</td>
			</tr>
		</table>
		<table style="width:100%; ">
			<tr>
				<td style="font-weight: bold;">9. Nombre del diagnóstico: {{$consentimiento->cie_10}} </td>
			</tr>
			<tr>
				<td style="font-weight: bold;">10. Nombre del procedimiento recomendado: {{substr($nombre->nombre, 0, 19)}}</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">11. ¿EN QUÉ CONSISTE?</td>
			</tr>
			<tr>
				<td style="width:100%; text-align:justify; ">La exploración a la que va a someterse se llama endoscopía digestiva alta y consiste en el examen del esófago, estómago y primera parte del intestino delgado (duodeno) mediante un tubo flexible con un sistema de iluminación y una cámara (endoscopio), que se introduce a través de la boca. Sirve para el diagnóstico de lesiones situadas en esta zona, así como para el tratamiento de algunas de ellas.</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">12. ¿CÓMO SE REALIZA?</td>
			</tr>
			<tr>
				<td style="text-align:justify;">La exploración se realiza en una posición cómoda, en una camilla sobre el lado izquierdo. Durante ese tiempo se puede respirar sin problema por la nariz o por la boca. Se le colocará un protector dental a través del cual se pasará el endoscopio. Para que se tolere mejor se le aplicará un sedante inyectado.
					Durante la exploración la tolerancia es buena, usted no sentirá ninguna molestia. Una vez finalizada, sólo puede quedar cierta falta de sensibilidad en la garganta que desaparece en 1 hora aproximadamente, así como gases debido al aire que se le insufló. Si se ha administrado sedante intravenoso, puede quedar cierta sedación residual durante varias horas.
					La exploración se realiza con diferentes grados de sedación que se consigue mediante la administración de fármacos en dosis adecuada para cada paciente, siendo el anestesiólogo y su equipo los encargados de realizar y controlar mediante la monitorización correspondiente el proceso de sedación, con el propósito de proporcionar un estado confortable sin dolor, teniendo en cuenta que no siempre es posible predecir el punto de transición entre la sedación moderada y la profunda o la anestesia general, por lo que se recomienda siempre acudir acompañado para la realización del estudio antes mencionado. De la misma manera se recomienda permanecer en compañía durante las siguientes doce horas, no pudiendo reincorporarse a las actividades habituales o conducir hasta pasado este periodo.
					A veces, durante la exploración, se producen hallazgos imprevistos que pueden obligar a tener que modificar la forma de hacer la exploración y utilizar variantes de la misma, no contempladas inicialmente.
					Puede ser necesario tomar muestras biológicas para estudiar mejor su caso.
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">13. GRÁFICO DE LA INTERVENCIÓN</td>
			</tr>
			<tr>
				<td><img class="centrado" src="{{public_path('/imagenes/eda.jpg')}}"></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">14. DURACIÓN ESTIMADA DE LA EXPLORACIÓN:</td>
			</tr>
			<tr>
				<td style="text-align:justify;">El tiempo aproximado de la exploración es de 30 minutos, existiendo casos excepcionales en los que la duración sea mayor.</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">15. BENEFECIOS DE LA EXPLORACIÓN:</td>
			</tr>
			<tr>
				<td style="text-align:justify;">La endoscopía digestiva alta está indicada siempre que su médico crea necesario conocer la existencia de alguna enfermedad en su esófago, estómago o duodeno. Por ejemplo, ante síntomas como dificultad al tragar, ardores o dolor de estómago, entre otros.
					Durante la exploración se pueden realizar tratamientos endoscópicos como dilatar zonas estrechas que impiden el paso de los alimentos, extraer objetos deglutidos y que han quedado atascados, extirpar pólipos, esclerosar o ligar varices esofágicas, aplicar calor a lesiones que pueden ser causa de hemorragia o anemia o destruir con gas argón o láser ciertas lesiones.
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">16. RIESGOS FRECUENTES (POCO GRAVES)</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Cualquier actuación médica tiene riesgos. La mayor parte de las veces los riesgos no se materializan y la intervención no produce daños o efectos secundarios indeseables. Pero a veces no es así, por eso es importante que usted conozca los riesgos que pueden aparecer en este proceso o exploración.
					La endoscopía digestiva alta es una técnica muy segura. La mayoría de las complicaciones son leves y sin repercusión alguna, como las producidas por reacciones no deseadas a la medicación administrada. La posibilidad de complicaciones es mayor cuando el endoscopio se emplea para aplicar tratamientos, como dilataciones, polipectomías, ligadura de varices o extracción de cuerpos extraños. Otras complicaciones menores son roturas dentales, mordedura de lengua, luxaciones mandibulares o afonía.
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">17. RIESGOS POCOS FRECUENTES (GRAVES)</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Entre las complicaciones mayores están la perforación, la hemorragia, reacciones alérgicas medicamentosas, alteraciones cardiopulmonares y transmisión de infecciones. Como consecuencia de alguna de estas complicaciones, excepcionalmente puede ser necesario un tratamiento urgente o una operación.
					Se deben considerar también los problemas de salud que presente cada paciente para las exploraciones que se vayan a realizar.
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">18. DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad y estado de salud)</td>
			</tr>
			<tr>
				<td>Diga SI o NO. De ser afirmativo, en cada caso, detalle lo que corresponda:</td>
			</tr>
			<tr>
				<td>Enfermedades Respiratorias</td>
			</tr>
			<tr>
				<td>Enfermedades Cardiacas</td>
			</tr>
			<tr>
				<td>Marcapasos</td>
			</tr>
			<tr>
				<td>Alergias (a medicinas, alimentos u otros)</td>
			</tr>
			<tr>
				<td>Toma medicamentos antiagregantes o anticoagulantes (Plavix, Aspirina, Rivaroxaban, Dabigatran, etc)</td>
			</tr>
			<tr>
				<td>Ha recibido anestesia anteriormente? (En caso de ser afirmativo, diga si ha tenido algún efecto con ella)</td>
			</tr>
			<tr>
				<td>Asma</td>
			</tr>
			<tr>
				<td>Otros</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">19. ALTERNATIVAS A LA EXPLORACIÓN:</td>
			</tr>
			<tr>
				<td style="text-align:justify;">La alternativa diagnóstica sería una exploración radiológica tras ingerir un contraste baritado para obtener imágenes del tubo digestivo. Sin embargo, permitiría diagnosticar su enfermedad en menor número de casos que la endoscopía, ya que no son posibles la toma de biopsias ni la visualización de lesiones de muy pequeño tamaño. Nunca podría tratar una hemorragia, ni extirpar un pólipo. Por ello, en algunas ocasiones, incluso tras realizar un estudio radiológico, es necesario realizar una endoscopía digestiva alta.

					La cápsula endoscópica (pequeña cámara que se traga) no sustituye tampoco a la endoscopía digestiva alta, por razones parecidas.

					En ocasiones la alternativa al tratamiento endoscópico suele ser una intervención quirúrgica que supone más riesgos y complicaciones.
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">20. DESCRIPCIÓN DEL MANEJO POSTERIOR A LA EXPLORACIÓN:</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Luego del procedimiento, el paciente es trasladado a la sala de recuperación en donde después de recuperar su nivel de conciencia, producto de la sedación y comprobada su recuperación, puede ser dado de alta y, tal como está establecido en el numeral 12 del presente documento, hacerlo en compañía.</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">21. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACIÓN:</td>
			</tr>
			<tr>
				<td>Mal diagnóstico, diagnóstico errado o diagnóstico tardío de enfermedades del tracto digestivo alto</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">22. DECLARACIÓN DE CONSENTIMIENTO INFORMADO</td>
			</tr>
		</table>
		<table style="width:50%; ">
			<tr>
				<td style="font-weight: bold;">Fecha:</td>
				<td></td>
				<td style="font-weight: bold;">Hora:</td>
				<td></td>
			</tr>
		</table>
		<table style="width:100%; ">
			<tr>
				<td style="text-align:justify;">He facilitado la información completa que conozco, y que me ha sido solicitada, sobre los antecedentes personales, familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos pueden afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento médico ambulatorio que se me ha propuesto; he sido informado de las ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qué consiste, los beneficios y posibles riesgos del procedimiento médico ambulatorio. He escuchado, leído y comprendido la información recibida y se me ha dado la oportunidad de preguntar sobre el procedimiento. He tomado consiente y libremente la decisión de autorizar el procedimiento, en consecuencia, libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a médicos y personal paramédico que participen en el proceso. Consiento que, durante la exploración, me realicen otro procedimiento adicional, si es considerado necesario según el juicio del profesional de la salud, para mi beneficio. También conozco que puedo retirar mi consentimiento cuando lo estime oportuno.</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos (del paciente)</th>
				<th class="th">Cedula de Ciudadanía</th>
				<th class="th">Firma del paciente o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">{{$paciente->id}}</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="text-align:justify;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad): </td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos (del representante legal)</th>
				<th class="th">Cedula de Ciudadanía</th>
				<th class="th">Parentesco</th>
				<th class="th">Firma del representante legal o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="th">&nbsp;</td>
				<td class="th">&nbsp;</td>
				<td class="th">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombre del Profesional de la Salud que realiza el procedimiento</th>
				<th class="th">Cedula de Ciudadanía</th>
				<th class="th">Firma, sello y código del profesional de la salud que realizará el procedimiento</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="font-weight: bold;">SI EL PACIENTE NO ENTIENDE LA LENGUA CASTELLANA</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%;margin-top:20px;">
			<thead>
				<tr>
					<th class="th">Nombres y Apellidos del paciente</th>
					<th class="th">Pasaporte No.</th>
					<th class="th">Firma del paciente o Huella, según el caso</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
					<td class="td">&nbsp;</td>
					<td class="td">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos del testigo</th>
				<th class="th">Cédula o Pasaporte No.</th>
				<th class="th">Firma del testigo o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
				<th class="th">Cédula de Ciudadanía</th>
				<th class="th">Firma, sello y código del profesional de la salud</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE LEER O ESCRIBIR (ANALFABETO)</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos del paciente</th>
				<th class="th">Cédula No.</th>
				<th class="th">Huella del paciente</th>
			</tr>
			<tr>
				<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">{{$paciente->id}}</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos del testigo</th>
				<th class="th">Cédula No.</th>
				<th class="th">Firma del testigo o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
				<th class="th">Cédula de Ciudadanía</th>
				<th class="th">Firma, sello y código del profesional de la salud</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="font-weight: bold;">23. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
			</tr>
		</table>
		<table style="width:50%; ">
			<tr>
				<td style="font-weight: bold;">Fecha:</td>
				<td></td>
				<td style="font-weight: bold;">Hora:</td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<td style="text-align:justify;">Una vez que he entendido claramente los procedimientos médicos ambulatorios propuestos, así como las consecuencias posibles si no se realiza la intervención, no autorizo y me niego a que se me realice el procedimiento propuesto y libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende por no realizar el procedimiento medico sugerido.</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos (del paciente)</th>
				<th class="th">Cedula de Ciudadanía</th>
				<th class="th">Firma del paciente o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">{{$paciente->id}}</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="text-align:justify;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (debe ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad):</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos (del representante legal)</th>
				<th class="th">Cedula de Ciudadanía</th>
				<th class="th">Parentesco</th>
				<th class="th">Firma del representante legal o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="th">&nbsp;</td>
				<td class="th">&nbsp;</td>
				<td class="th">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="text-align:justify;">Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este acápite:</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos (del testigo)</th>
				<th class="th">Cédula de ciudadanía.</th>
				<th class="th">Firma del testigo o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres del Profesional de la Salud que realiza el procedimiento</th>
				<th class="th">Cédula de Ciudadanía</th>
				<th class="th">Firma, sello y código del profesional de la salud que realizará el procedimiento</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
			<tr>
				<td style="font-weight: bold;">SI EL PACIENTE NO ENTIENDE LA LENGUA CASTELLANA</td>
			</tr>
			<tr>
				<td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos del paciente</th>
				<th class="th">Pasaporte No.</th>
				<th class="th">Firma del paciente o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombres y Apellidos del testigo</th>
				<th class="th">Cédula o Pasaporte No.</th>
				<th class="th">Firma del testigo o Huella, según el caso</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<tr>
				<th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
				<th class="th">Cédula de Ciudadanía</th>
				<th class="th">Firma, sello y código del profesional de la salud</th>
			</tr>
			<tr>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
				<td class="td">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table>
		<thead>
			<tr>
				<td style="font-weight: bold;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE LEER O ESCRIBIR (ANALFABETO)</td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td style="text-align:justify;">Se me ha explicado toda la información sobre el procedimiento a realizarse, con sus ventajas e inconvenientes, así como sus beneficios o posibles riesgos.</td>
			</tr>
			</tbody>
		</table>

		<table class="table" style="width:100%; ">
		<thead>
			<tr>
				<th class="th">Nombres y Apellidos del paciente</th>
				<th class="th">Cédula No.</th>
				<th class="th">Huella del paciente</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
				<td class="td">{{$paciente->id}}</td>
				<td class="td">&nbsp;</td>
			</tr>
			</tbody>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<thead>
				<tr>
					<th class="th">Nombres y Apellidos del testigo</th>
					<th class="th">Cédula No.</th>
					<th class="th">Firma del testigo o Huella, según el caso</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td">&nbsp;</td>
					<td class="td">&nbsp;</td>
					<td class="td">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<thead>
				<tr>
					<th class="th">Nombre del Profesional de la Salud que asiste en la información del procedimiento</th>
					<th class="th">Cédula de Ciudadanía</th>
					<th class="th">Firma, sello y código del profesional de la salud</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td">&nbsp;</td>
					<td class="td">&nbsp;</td>
					<td class="td">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table>
			<tr>
				<td style="font-weight: bold;">24. REVOCATORIA DE CONSENTIMIENTO INFORMADO</td>
			</tr>
			<tr>
				<td style="text-align:justify;">De forma libre y voluntaria, revoco el consentimiento realizado en fecha _______________ y manifiesto expresamente mi deseo de no continuar con los procedimientos médicos ambulatorios y que doy por finalizado en esta fecha.
					Libero de responsabilidades futuras de cualquier índole al establecimiento de salud y al profesional sanitario que me atiende.
				</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<thead>
				<tr>
					<th class="th">Nombres y Apellidos (del paciente)</th>
					<th class="th">Cedula de Ciudadanía</th>
					<th class="th">Firma del paciente o Huella, según el caso</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td">{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
					<td class="td">{{$paciente->id}}</td>
					<td class="td">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table>
			<tr>
				<td style="text-align:justify;">SI EL PACIENTE NO ESTÁ EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (debe ser llenado también en caso de ser el paciente menor de edad o presente una incapacidad):</td>
			</tr>
		</table>
		<br>
		<table class="table" style="width:100%; ">
			<thead>
				<tr>
					<th class="th">Nombres y Apellidos (del representante legal)</th>
					<th class="th">Cedula de Ciudadanía</th>
					<th class="th">Parentesco</th>
					<th class="th">Firma del representante legal o Huella, según el caso</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="td">&nbsp;</td>
					<td class="th">&nbsp;</td>
					<td class="th">&nbsp;</td>
					<td class="th">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>

</html>
