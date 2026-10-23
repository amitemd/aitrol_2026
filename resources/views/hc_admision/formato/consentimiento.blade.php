@php
  $id_empresa  = \Session::get('id_empresa');
  $empresa  =  \Sis_medico\Empresa::find($id_empresa);
@endphp
<html>

<head>
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

</style>

<script src="{{ asset ("/bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ asset ("/js/sitio.js") }}"></script>

</head>

<body lang=ES-EC style="margin-top: -10px;">
	<input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="{{$data->fecha_nacimiento}}">

<div class=WordSection1 >

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
<p class=MsoNoSpacing align=center style='text-align:center'><b><span lang=ES
style='font-size:11.0pt'>{{$empresa->nombre_form}}</span></b></p>
  @endif
@endif

<p class=MsoNoSpacing><b><span lang=ES style='font-size:3.0pt'>&nbsp;</span></b></p>
@if($empresa->id == '1793135579001' && $data->id_seguro == 3)  
<p class=MsoNoSpacing><span lang=ES>{{ucfirst(strtolower($empresa->ciudad))}}, ____________________ </span></p>
@else
  @if($data->id_seguro != 6)
  <p class=MsoNoSpacing><span lang=ES>{{ucfirst(strtolower($empresa->ciudad))}}, ______ de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</span></p>
  @else
  @php
      $fecha_ini = date("d/m/Y", strtotime(substr($data->fechaini, 0, 10)));
  @endphp
  <p class=MsoNoSpacing><span lang=ES>{{ucfirst(strtolower($empresa->ciudad))}}, @if($data->tipo=='0') @if(strtoupper($data->nombre) == 'ISSPOL') ______ @else ______ @endif @else {{substr($date,8,2)}} @endif de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</span></p>
  @endif
@endif
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>

<p class=MsoNoSpacing><b><span lang=ES style='font-size:10.0pt;padding-left: 40pt;'>AUTORIZACIÓN
PARA CIRUGIA, TRATAMIENTO CLÍNICO O PROCEDIMIENTO DIAGNÓSTICO</span></b></p>

<p class=MsoNoSpacing style='margin-right:10pt;text-align:justify'><span
lang=ES>Autorizo al personal médico de este establecimiento de salud para
realizar las operaciones quirúrgicas, procedimientos diagnósticos y
tratamientos clínicos propuestos y necesarios para el alivio o la recuperación
de mi enfermedad.</span></p>

<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>

<table border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:16.05pt'>
  <td width=316 valign=top style='width:186.7pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>NOMBRE
  DEL PACIENTE</b></span></p>
  </td>
  <td width=59 valign=top style='width:14.35pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>EDAD</b></span></p>
  </td>
  <td width=204 colspan=2 valign=top style='width:93.1pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>TELÉFONOS</b></span></p>
  </td>
  <td width=139 valign=top style='width:54.55pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>CÉDULA</b></span></p>
  </td>
 </tr>
 <tr style='height:16.05pt' id="t1">
  <td width=316 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>&nbsp;</span>{{$data->nombre1}} @if($data->nombre2!='(N/A)'){{$data->nombre2}}@endif {{$data->apellido1}} @if($data->apellido2!='(N/A)'){{$data->apellido2}}@endif</p>
  </td>
  <td width=59 valign=top style='width:44.35pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span id="xedad" lang=ES>{{$age}}</span></p>
  </td>
  <td width=96 valign=top style='width:71.75pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>{{$data->telefono1}}</span></p>
  </td>
  <td width=108 valign=top style='width:81.35pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>{{$data->telefono2}}</span></p>
  </td>
  <td width=139 valign=top style='width:104.55pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.05pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>{{$data->id_paciente}}</span></p>
  </td>
 </tr>
 <tr height=0>
  <td width=316 style='border:none'></td>
  <td width=59 style='border:none'></td>
  <td width=96 style='border:none'></td>
  <td width=108 style='border:none'></td>
  <td width=139 style='border:none'></td>
 </tr>
</table>

<p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:13.85pt'>
  <td width=284 valign=top style='width:180pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.85pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>NOMBRE
  DEL REPRESENTANTE LEGAL</b></span></p>
  </td>
  <td width=95 valign=top style='width:60.9pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.85pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>PARENTESCO</b></span></p>
  </td>
  <td width=199 colspan=2 valign=top style='width:90pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.85pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>TELÉFONOS</b></span></p>
  </td>
  <td width=141 valign=top style='width:104.75pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.85pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>CÉDULA</b></span></p>
  </td>
 </tr>
 <tr style='height:14.65pt'>
  <td width=284 valign=top style='width:180pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:14.65pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>@if($data->fparentesco!='Principal'){{$data->fnombre1}} @if($data->fnombre2!='(N/A)'){{$data->fnombre2}}@endif {{$data->fapellido1}} @if($data->fapellido2!='(N/A)'){{$data->fapellido2}}@endif @endif</span></p>
  </td>
  <td width=95 valign=top style='width:60.9pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.65pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>@if($data->fparentesco!='Principal'){{$data->fparentesco}}@endif</span></p>
  </td>
  <td width=94 valign=top style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.65pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>@if($data->fparentesco!='Principal'){{$data->telefono3}}@endif</span></p>
  </td>
  <td width=105 valign=top style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.65pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>@if($data->fparentesco!='Principal'){{$data->telefono2}}@endif</span></p>
  </td>
  <td width=141 valign=top style='width:60.9pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:14.65pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>@if($data->fparentesco!='Principal'){{$data->cedulafamiliar}}@endif</span></p>
  </td>
 </tr>
 <tr height=0>
  <td width=284 style='border:none'></td>
  <td width=95 style='border:none'></td>
  <td width=94 style='border:none'></td>
  <td width=105 style='border:none'></td>
  <td width=141 style='border:none'></td>
 </tr>
</table>

<p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:13.15pt'>
  <td width=360 valign=top style='width:230.3pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>TESTIGO</b></span></p>
  </td>
  <td width=110 valign=top style='width:82.55pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>PARENTESCO</b></span></p>
  </td>
  <td width=110 valign=top style='width:82.55pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>TELÉFONOS</b></span></p>
  </td>
  <td width=138 valign=top style='width:104.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>CÉDULA</b></span></p>
  </td>
 </tr>
 <tr style='height:13.15pt'>
  <td width=360 valign=top style='width:230.3pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>&nbsp;</span></p>
  </td>
  <td width=110 valign=top style='width:82.55pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>&nbsp;</span></p>
  </td>
  <td width=110 valign=top style='width:82.55pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>&nbsp;</span></p>
  </td>
  <td width=138 valign=top style='width:104.3pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.15pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES>&nbsp;</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>
@php
  $docProcedimiento = Sis_medico\User::where('id',$data->id_doctor1)->first();
@endphp
<!--nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;-->
 @if(is_null($procedimiento_empresa))
<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:13.15pt'>
  <td width=290 valign=top style='width:200.7pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>NOMBRE DEL PROFESIONAL DE LA SALUD.</b></span></p>
  </td>
  <td width=60 valign=top style='width:50pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>LIBRO</b></span></p>
  </td>
  <td width=60 valign=top style='width:35.0pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>FOLIO</b></span></p>
  </td>
  <td width=80 valign=top style='width:44.05pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt;'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>NÚMERO</b></span></p>
  </td>
  <td width=90 valign=top style='width:67.55pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>TELÉFONOS</b></span></p>
  </td>
  <td width=138 valign=top style='width:80pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span lang=ES><b>CÉDULA</b></span></p>
  </td>
 </tr>
 <tr style='height:13.5pt'>
  <td width=290 valign=top style='width:200.7pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span
  lang=ES > {{$doctor->nombre1}} {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)'){{$doctor->apellido2}} @endif</span></b></p>
  <p class=MsoNoSpacing align=center style='text-align:center;'>
  <span lang=ES > 
  
  
  {{-- @if($data->id_seguro == 6 || $data->id_seguro == 5)
    @if(!is_null($docProcedimiento))
      @if(strtoupper($docProcedimiento->nombre1) == 'PENTAX')
      @else
      {{$docProcedimiento->nombre1}} {{$docProcedimiento->nombre2}} {{$docProcedimiento->apellido1}} {{$docProcedimiento->apellido2}} 
      @endif
    @endif
  @endif --}}
  </span></p>
  </td>
  @php
    $libro='';$folio='';$numero='';
    if($data->id_seguro == 6 || $data->id_seguro == 5){
      if(!is_null($docProcedimiento)){
        $libro=$docProcedimiento->libro;
        $folio=$docProcedimiento->folio;
        $numero=$docProcedimiento->num;
      }
    }
    else{
    $firma = Sis_medico\Firma_Usuario::where('id_usuario',$doctor->id)->first();
    if(!is_null($firma)){
      $libro=$firma->libro;
      $folio=$firma->folio;
      $numero=$firma->num;
      if($numero==null){
        $folio=null;
      }  
    }
    }
  @endphp
  <td width=60 valign=top style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <!--p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($empresa->id=='0992704152001') {{$libro}} @else 1 @endif</span></p-->
  <p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) {{$docProcedimiento->libro}} @endif @endif</span></p>
  </td>
  <td width=60 valign=top style='width:35.0pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <!--p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($empresa->id=='0992704152001') {{$folio}} @else 3771 @endif</span></p-->
   <p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) {{$docProcedimiento->folio}} @endif @endif</span></p>
  </td>
  <td width=80 valign=top style='width:44.05pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <!--p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($empresa->id=='0992704152001') {{$numero}} @else 11003 @endif</span></p-->
  <p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) {{$docProcedimiento->numero}} @endif @endif</span></p>
  </td>
  <td width=90 valign=top style='width:67.55pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($doctor->id != '0912356870')  @endif</span></p>
  </td>
  <td width=138 valign=top style='width:80pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <!--p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >@if($empresa->id=='0992704152001') {{$doctor->id}} @else 1307189140 @endif</span></p-->
   <p class=MsoNoSpacing align=center style='text-align:center;'><span
  lang=ES >{{$docProcedimiento->id}}</span></p>
  </td>
 </tr>
 </table>
 @else
 <table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:12.75pt'>
  <td width=290 valign=top style='width:200.7pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>NOMBRE
  DEL PROFESIONAL DE LA SALUD.</b></span></p>
  </td>
  <td width=60 valign=top style='width:25.05pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>REGISTRO</b></span></p>
  </td>
  <td width=90 valign=top style='width:67.55pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>TELÉFONOS</b></span></p>
  </td>
  <td width=138 valign=top style='width:103.35pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:12.75pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span lang=ES><b>CÉDULA</b></span></p>
  </td>
 </tr>
 <tr style='height:13.5pt'>
  <td width=290 valign=top style='width:200.7pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) @if(!is_null($docProcedimiento)) Dr. {{$docProcedimiento->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}} @endif @endif @endif</span></b></p>
  </td>
  <td width=60 valign=top style='width:100pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) @if(!is_null($docProcedimiento)) {{$docProcedimiento->registro_doctor}} @endif @endif @endif</span></p>
  </td>
  <td width=90 valign=top style='width:80pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) 2109180  @endif @endif</span></p>
  </td>
  <td width=138 valign=top style='width:118pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.5pt'>
  <p class=MsoNoSpacing align=center style='text-align:center'><span
  lang=ES >@if($doctor->id != '0912356870') @if($data->id_seguro == 6 || $data->id_seguro == 5) @if(!is_null($docProcedimiento)) {{$docProcedimiento->id}} @endif @endif @endif</span></p>
  </td>
 </tr>
</table>
 @endif


<p class=MsoNoSpacing><b><span lang=ES style='font-size:7.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>


<p class=MsoNoSpacing><b><span lang=ES style='font-size:13.0pt'>CONSENTIMIENTO
INFORMADO</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:19.0pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:19.0pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>A</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:19.0pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>EL PROFESIONAL
  TRATANTE ME HA INFORMADO SATISFACTORIAMENTE ACERCA DE LOS MOTIVOS Y
  PROPOSITOS DEL TRATAMIENTO PLANIFICADO PARA MI ENFERMEDAD</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:17.65pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>B</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>EL PROFESIONAL
  TRATANTE ME HA EXPLICADO ADECUADAMENTE LAS ACTIVIDADES ESENCIALES QUE SE
  REALIZARAN DURANTE EL TRATAMIENTO DE MI ENFERMEDAD</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:20.25pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:20.25pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>C</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:20.25pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>CONSIENTO A QUE
  SE REALICEN LAS INTERVENCIONES QUIRURGICAS, PROCEDIMIENTOS DIAGNOSTICOS Y
  TRATAMIENTOS NECESARIOS PARA MI ENFERMEDAD</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:18.35pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18.35pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>D</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:18.35pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>CONSIENTO A QUE
  ME ADMINISTREN LA ANESTESIA PROPUESTA</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:19.7pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:19.7pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>E</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:19.7pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>HE ENTENDIDO BIEN
  QUE EXISTE GARANTIA DE LA CALIDAD DE LOS MEDIOS UTILIZADOS PARA EL
  TRATAMIENTO, PERO NO ACERCA DE LOS RESULTADOS</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:18.3pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18.3pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>F</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:18.3pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>HE COMPRENDIDO
  PLENAMENTE LOS BENEFICIOS Y LOS RIESGOS DE COMPLICACIONES DERIVADAS DEL
  TRATAMIENTO</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:19.0pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:19.0pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>G</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:19.0pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>EL PROFESIONAL
  TRATANTE ME HA INFORMADO QUE EXISTE GARANTIA DE RESPETO A MI INTIMIDAD, A MIS
  CREENCIAS RELIGIOSAS Y A LA CONFIDENCIALIDAD DE LA INFORMACION (INCLUSIVE EN
  EL CASO DE VIH/SIDA)</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:17.65pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>H</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>HE COMPRENDIDO
  QUE TENGO EL DERECHO DE ANULAR ESTE CONSENTIMIENTO INFORMADO EN EL MOMENTO
  QUE YO LO CONSIDERE NECESARIO</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing><span lang=ES style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:17.65pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>I</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:17.65pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>DECLARO QUE HE
  ENTREGADO AL PROFESIONAL TRATANTE INFORMACION COMPLETA Y FIDEDIGNA SOBRE LOS
  ANTECEDENTES PERSONALES Y FAMILIARES DE MI ESTADO DE SALUD. ESTOY CONCIENTE
  DE QUE MIS OMISIONES O DISTORSIONES DELIBERADAS DE LOS HECHOS PUEDEN AFECTAR
  LOS RESULTADOS DEL TRATAMIENTO</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing style='text-align:justify'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:21.05pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:21.05pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>J</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:21.05pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>PERMITO EL USO DE
  LAS IMÁGENES ENDOSCOPICAS Y PATOLOGICAS A {{$empresa->nombrecomercial}} Y SUS PROFESIONALES
  CON FINES EXCLUSIVOS, CIENTIFICOS Y DE EDUCACION. {{$empresa->nombrecomercial}} GARANTIZA CONFIDENCIALIDAD
  DE MI INFORMACION PARA ESTOS FINES.</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing style='text-align:justify'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:4.05pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:4.05pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>K</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:4.05pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>HE COMPRENDIDO
  QUE LOS VIDEOS E IMÁGENES REALIZADOS POR EL PROFESIONAL TRATANTE DE LA
  COMPAÑÍA SERÁ DE PERMANENCIA DE {{$empresa->nombrecomercial}} Y SE RESERVA EL DERECHO A
  HACER USO EN INVESTIGACIONES O CAPACITACIONES MÉDICAS, SIN REVELAR LA
  IDENTIDAD DEL PACIENTE, SALVAGUARDANDO SIEMPRE SU INTEGRIDAD FÍSICA Y MORAL
  DEL PACIENTE</span></p>
  </td>
 </tr>
</table>

<p class=MsoNoSpacing style='text-align:justify'><span lang=ES
style='font-size:6.0pt'>&nbsp;</span></p>

<table  border=1 cellspacing=0 cellpadding=0 width=718
 style='width:538.7pt;margin-left:0pt;border-collapse:collapse;border:
 none'>
 <tr style='height:21.05pt'>
  <td width=30 valign=top style='width:10pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:21.05pt'>
  <p class=MsoNoSpacing><b><span lang=ES style='font-size:14.0pt'>L</span></b></p>
  </td>
  <td width=688 valign=top style='width:512.3pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:21.05pt'>
  <p class=MsoNoSpacing><span lang=ES style='font-size:7.0pt'>HE COMPRENDIDO
  QUE LA ENFERMEDAD COMO TAL ME PERTENECE Y LA COMPAÑÍA DEBERÁ ENTREGAR ÚNICA Y
  EXCLUSIVAMENTE EL INFORME MÉDICO DEL ESTUDIO REALIZADO.</span></p>
  </td>
 </tr>
</table>



<p class=MsoNoSpacing><span lang=ES>&nbsp;</span></p>

<p class=MsoNoSpacing><span lang=ES>&nbsp;</span></p>

<p class=MsoNoSpacing><span lang=ES>&nbsp;</span></p>

<p class=MsoNoSpacing><span lang=ES>&nbsp;</span></p>

<p class=MsoNoSpacing><span lang=ES>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________                 _______________________            
          ______________                 ______________</span></p>
  
<p class=MsoNoSpacing><b><span lang=ES style='font-size:8.0pt;padding-left: 15pt;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PACIENTE                             
        REPRESENTANTE LEGAL                               TESTIGO                   
                MEDICO</span></b></p>

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
