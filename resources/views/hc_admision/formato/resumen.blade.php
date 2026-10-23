
<html>

<head>
<br>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=Generator content="Microsoft Word 15 (filtered)">
<style>
 
 /* Font Definitions */
 @font-face
	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;}
@font-face
	{font-family:Calibri;
	panose-1:2 15 5 2 2 2 4 3 2 4;}
@font-face
	{font-family:Harrington;
	panose-1:4 4 5 5 5 10 2 2 7 2;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{margin-top:0cm;
	margin-right:0cm;
	margin-bottom:10.0pt;
	margin-left:0cm;
	line-height:115%;
	font-size:11.0pt;
	font-family:"Calibri",sans-serif;}
p.MsoNoSpacing, li.MsoNoSpacing, div.MsoNoSpacing
	{margin:0cm;
	margin-bottom:.0001pt;
	font-size:9.0pt;
	font-family:"Calibri",sans-serif;}
span.apple-converted-space
	{mso-style-name:apple-converted-space;}
.MsoChpDefault
	{font-size:10.0pt;
	font-family:"Calibri",sans-serif;}
@page WordSection1
	{size:595.3pt 841.9pt;
	margin:42.55pt 49.55pt 7.1pt 70.9pt;}
div.WordSection1
	{page:WordSection1;}
 /* List Definitions */
 ol
	{margin-bottom:0cm;}
ul
	{margin-bottom:0cm;}

span {font-size:10.0pt;}

.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   
   color: black;
   text-align: right;
}
table {
  border-collapse: collapse;
}

table, th, td {
  border: 1px solid black;
}
td{
	padding: 0;
}
</style>


</head>


<body lang=ES-EC style="margin-top: -70px;">
	<input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="{{$data->fecha_nacimiento}}">



<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p><p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
@if(!is_null($empresa))
  @if(!is_null($empresa->logo_form))
<p class=MsoNoSpacing align=center style='text-align:center'><span
style='position:relative;z-index:-1895825920'><span style='left:0px;position:
absolute;left:0px;top:-36px;width:225px;height:46px'><img width=225
height=46
src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}"
alt=""></span></span></p>
  @endif
@endif

<p class=MsoNoSpacing><b><span lang=ES style='font-size:3.0pt'>&nbsp;</span></b></p>

@if(!is_null($empresa))
  @if(!is_null($empresa->nombre_form))
<p class=MsoNoSpacing align=center style='text-align:center;border-bottom: 2px outset #001a1a;'><b><span lang=ES
style='font-size:11.0pt'>{{$empresa->nombre_form}}</span></b></p>

  @endif
@endif

<p class=MsoNoSpacing><b><span lang=ES style='font-size:3.0pt'>&nbsp;</span></b></p>


<span ><b>Fecha de Cita: </b> {{substr($agenda->fechaini, 0, 10)}}</span>&nbsp;&nbsp;&nbsp;<span ><b>Hora: </b> {{substr($agenda->fechaini, 10, 10)}}</span><br>
<span ><b>Fecha de Ingreso: </b> {{substr($date, 0, 10)}}</span><span style="position: absolute;left: 300px;"><b>HC Interno: </b> {{$paciente->id}}</span><span style="position: absolute;left: 500px;"><b>Seguro: </b><b style="background-color: #DFF8F9;font-size: 16px;"> {{$seguro->nombre}}</b></span>
<br>
<span><b>Paciente: </b> {{$paciente->id}} - <b style="background-color: #DFF8F9;font-size: 16px;">{{ $paciente->apellido1}} @if($paciente->apellido2 != '(N/A)'){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != '(N/A)' ){{ $paciente->nombre2}}@endif</b></span>
<br>
<span><b>Edad: </b> {{$age}}</span><span style="position: absolute;left: 300px;"><b>Sexo: </b> @if($paciente->sexo == 1){{"Masculino"}}@else{{"Femenino"}}@endif</span><span style="position: absolute;left: 500px;"><b>Estado Civil: </b> @if($paciente->estadocivil == 1){{"Soltero"}}@elseif($paciente->estadocivil == 2){{"Casado"}}@elseif($paciente->estadocivil == 3){{"Viudo"}}@elseif($paciente->estadocivil == 4){{"Divorciado"}}@elseif($paciente->estadocivil == 5){{"Union Libre"}}@elseif($paciente->estadocivil == 6){{"Union de Hecho"}}@endif</span>
<br>
<span><b>Ciudad:  </b>{{$paciente->ciudad}}</span><span style="position: absolute;left: 300px;"><b> Lugar Nacimiento: </b> {{$paciente->lugar_nacimiento}}</span>
<br>
<span><b>Fecha de Nacimiento: </b> {{$paciente->fecha_nacimiento}}</span><span style="position: absolute;left: 300px;"><b>Telefono:  </b>{{$paciente->telefono1}} @if($paciente->telefono2 != ""){{'- '.$paciente->telefono2}}@endif</span>
<br>
<span ><b>Dirección: </b> {{$paciente->direccion}}</span>
<br>
<span ><b>Alergia: </b> {{$paciente->alergias}}</span><span style="position: absolute;left: 300px;"><b>Ingreso: </b> @if($agenda->est_amb_hos == 0){{"Ambulatorio"}}@elseif($agenda->est_amb_hos == 1){{"Hospitalizado"}}@endif</span>
<br>
<span ><b>Responsable: </b> {{ $responsable->nombre1}} {{ $responsable->apellido1}}</span><span style="position: absolute;left: 300px;font-size: 12px;"><b>Ocupacion: </b>{{$paciente->ocupacion}} </span><span style="position: absolute;left: 550px;;font-size: 12px;"><b>Religion: </b>{{$paciente->religion}} </span>
<br>
<span ><b>@if($agenda->proc_consul=='1')Procedimientos:</b> @endif <b style="background-color: #DFF8F9;font-size: 16px;">{{ $procedimientos_txt}}</b></span>
<br>
<span ><b>Observaciones Médicas:</b></span><br>
@php $txt_old = ''; @endphp
@foreach($log_agenda as $log)
	
	
		@if($log->user_crea->id_tipo_usuario=='3')
			@if(($log->descripcion!='' || $log->observaciones!='' || $log->observaciones_ant!='') && $log->descripcion!='DESPLAZAMIENTO RÁPIDO DOCTOR' && $log->descripcion!='DESPLAZAMIENTO RÁPIDO PENTAX' && $log->descripcion!='DESPLAZAMIENTO RÁPIDO SALAS PENTAX')
				
				@if($log->observaciones_ant!='')
					@php $txt = $log->created_at.' -> '.$log->user_crea->apellido1.':'.$log->descripcion.' '.$log->observaciones_ant.' - '.$log->observaciones; @endphp
					@if($txt_old!=$txt)<span style="font-size: 10px;">{{$txt}}</span><br>@endif
				@else
					@php $txt = $log->created_at.' -> '.$log->user_crea->apellido1.':'.$log->descripcion.' '.$log->observaciones; @endphp
					@if($txt_old!=$txt)
					<span style="font-size: 10px;">{{$log->created_at}} -> {{$log->user_crea->apellido1}}: {{$log->descripcion}} @if($log->descripcion!=$log->observaciones){{$log->observaciones}}@endif</span><br>
					@endif
				@endif
				@php $txt_old = $txt; @endphp
			@endif
		@endif
	
@endforeach
<span ><b>______________________________________________________________________________________________________________</b></span><br>
<span ><b>______________________________________________________________________________________________________________</b></span><br>
<span ><b>Observaciones Administrativas:</b></span><br>
@if($log_agenda->count()<=1)
	
	<span style="font-size: 10px;">{{$agenda->fechaini}} -> {{$agenda->user_crea->apellido1}}: AGENDA - {{$agenda->observaciones}}</span><br>
@endif
@php $txt_old = ''; @endphp
@foreach($log_agenda as $log)
	@if($log->user_crea->id_tipo_usuario!='3')
		@if(($log->descripcion!='' || $log->observaciones!='' || $log->observaciones_ant!='') && $log->descripcion!='DESPLAZAMIENTO RÁPIDO DOCTOR' && $log->descripcion!='DESPLAZAMIENTO RÁPIDO PENTAX' && $log->descripcion!='DESPLAZAMIENTO RÁPIDO SALAS PENTAX')
			
			@if($log->observaciones_ant!='')
				@php $txt = $log->created_at.' -> '.$log->user_crea->apellido1.':'.$log->descripcion.' '.$log->observaciones_ant.' - '.$log->observaciones; @endphp
				@if($txt_old!=$txt)<span style="font-size: 10px;">{{$txt}}</span><br>@endif
			@else
				@php $txt = $log->created_at.' -> '.$log->user_crea->apellido1.':'.$log->descripcion.' '.$log->observaciones; @endphp
				@if($txt_old!=$txt)
				<span style="font-size: 10px;">{{$log->created_at}} -> {{$log->user_crea->apellido1}}: {{$log->descripcion}} @if($log->descripcion!=$log->observaciones){{$log->observaciones}}@endif</span><br>
				@endif
			@endif
			@php $txt_old = $txt; @endphp
		@endif
	@endif	
@endforeach
<span ><b>______________________________________________________________________________________________________________</b></span><br>
<span ><b>______________________________________________________________________________________________________________</b></span><br>
<!--span ><b>______________________________________________________________________________________________________________</b></span><br>
<span ><b>______________________________________________________________________________________________________________</b></span><br-->


@if(!is_null($documentos))
<span ><b>Documentos:</b></span>
	<table class="table" style="font-size: 11px;">
		<tr style="padding: 0;">
		  <th style="width: 10%"> #</th>
		  <th style="width: 10%"> Documentación requerida</th>
		  <th style="width: 10%"> Ch.</th>
		  <th style="width: 10%"> P.Recibe</th> 
		  <th style="width: 10%"> Fecha/Hora Entrega</th>
		</tr>
		
		  @php $i=0; @endphp
		  @foreach($documentos as $documento)
		  	@php $archivo = DB::table('archivo_historico as ah')->where('ah.id_documento',$documento->id)->where('ah.id_historia',$data->hcid)->join('paciente as ue','ue.id','ah.id_usuario_entrega')->join('users as ur','ur.id','ah.id_usuario_recibe')->select('ah.*','ue.nombre1 as uenombre1','ue.apellido1 as ueapellido1','ur.nombre1 as urnombre1','ur.apellido1 as urapellido1')->first(); @endphp
		    @if($documento->est_doc_tarea=='0')  
		        @php $i++; @endphp
		        <tr style="padding: 0;">
		          <td>{{$i}}</td> 
		          <td style="font-size: 9px;">{{$documento->nombre}}</td>
		          <td @if(!is_null($archivo)) @if($archivo->estado=='1') style="background-color: #ccf5ff;" @endif @endif><span style="text-align: center;"><input id="ch{{$documento->id}}" type="checkbox" class="flat-green" @if(!is_null($archivo)) @if($archivo->estado=='1')checked @endif @endif ></span></td>
		          <td>@if(!is_null($archivo)){{$archivo->urapellido1}}@endif</td>
		          <td>@if(!is_null($archivo)){{$archivo->fecha_entrega}}@endif</td>
		             
		        </tr> 
		    @endif      
		  @endforeach  
		
		
	</table>
@endif


<div class="footer">
	<p style="font-size: 10px;">{{date('d-m-Y')}}  {{date('G:i A')}}</p>
	
</div>

<script type="text/javascript">

$(document).ready(function () {

    edad2();
   

});


function edad2()
{
    
    var nacimiento = document.getElementById("fecha_nacimiento").value;
    var edad = calcularEdad(nacimiento);
    
    if(isNaN(edad))
    {
      var jsspan = document.getElementById("xedad");
      
      jsspan.innerHTML = "0";
    }
    else
    {
      var jsspan = document.getElementById("xedad");
      jsspan.innerHTML = edad + " años";
    }  
}  	

</script>



</body>


</html>
