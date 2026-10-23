<html>

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=Generator content="Microsoft Word 15 (filtered)">
<style>
  .bordes{
    border-radius: 200px;
    -moz-border-radius: 200px;
    -webkit-border-radius: 200px;
    border: 2px solid black;
  }
   /* Font Definitions */
   @font-face
    {font-family:"Cambria Math";
    panose-1:2 4 5 3 5 4 6 3 2 4;}
  @font-face
    {font-family:Calibri;
    panose-1:2 15 5 2 2 2 4 3 2 4;}
   /* Style Definitions */
   p.MsoNormal, li.MsoNormal, div.MsoNormal
    {margin-top:0cm;
    margin-right:0cm;
    margin-bottom:8.0pt;
    margin-left:0cm;
    line-height:107%;
    font-size:11.0pt;
    font-family:"Calibri",sans-serif;}
  .MsoChpDefault
    {font-family:"Calibri",sans-serif;}
  .MsoPapDefault
    {margin-bottom:8.0pt;
    line-height:107%;}
  @page WordSection1
    {size:595.3pt 1000pt;
    margin:0pt 0pt 0pt 0pt;}
  div.WordSection1
    {size:595.3pt 1000pt;
    margin:0pt 0pt 0pt 0pt;}



    @page {
    size:590pt 845pt;
    margin-bottom: 0pt;
    margin-left: 20px;

    }
</style>
</head>

<body lang=ES-EC>

<div class=WordSection1 style="margin-left: -8pt; margin-top: -10pt">
<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 align=left
 style='border-collapse:collapse;border:none;margin-left:4.8pt;
 margin-right:4.8pt'>
 <tr style='height:27.6pt'>
  <td colspan=12 style='width:100pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:27.6pt'>
  @if(!is_null($empresa->logo_form))

    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='position:relative;
  z-index:-1895823360'><span style='left:0px;position:absolute;left:23px;
  top:-7px;width:127px;height:33px'><img width=127 height=33
  src="{{base_path().'/storage/app/logo/logo_endoscopynet.png'}}"></span></span>
    @else

    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='position:relative;
  z-index:-1895823360'><span style='left:0px;position:absolute;left:23px;
  top:-7px;width:127px;height:33px;font-size: 9px;'><b>{{$empresa->nombre_form}}</b></span></span>
    @endif
    <b>RECORD ANESTÉSICO</b></p>
  </td>
 </tr>
 <tr>
  <td colspan=3 style='width:50pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>APELLIDO
  PATERNO</span></b></p>
  </td>
  <td colspan=4 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>MATERNO</span></b></p>
  </td>
  <td colspan=3 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>NOMBRES</span></b></p>
  </td>
  <td colspan=2 style='width:85pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>N.
  HISTORIA CLÍNICA</span></b></p>
  </td>
 </tr>
 <tr style='height:16.95pt'>
  <td colspan=3 style='width:50pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:16.95pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$paciente->apellido1}}</span></p>
  </td>
  <td colspan=4 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.95pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$paciente->apellido2}}</span></p>
  </td>
  <td colspan=3 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.95pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$paciente->nombre1}} {{$paciente->nombre2}}</span></p>
  </td>
  <td colspan=2 style='width:85pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:16.95pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$paciente->id}}</span></p>
  </td>
 </tr>
 <tr>
  <td style='width:45pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>FECHA</span></b></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>EDAD</span></b></p>
  </td>
  <td colspan=2 style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>SEXO</span></b></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>ESTATURA</span></b></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>PESO</span></b></p>
  </td>
  <td colspan=2 style='width:55pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:6.0pt'>OCUPACIÓN</span></b><span
  style='font-size:6.0pt'> <b>ACTUAL</b></span></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>SERVICIO</span></b></p>
  </td>
  <td colspan=2 style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>SALA</span></b></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>CAMA</span></b></p>
  </td>
 </tr>
 <tr style='height:17.3pt'>
  <td style='width:45pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{substr($record->fecha,0,10)}}</span></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$age}}</span></p>
  </td>
  <td colspan=2 style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if($paciente->sexo==1) {{"HOMBRE"}} @elseif($paciente->sexo==2) {{"MUJER"}} @endif</span></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$historia->altura}}</span></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$historia->peso}}</span></p>
  </td>
  <td colspan=2 style='width:55pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$paciente->ocupacion}}</span></p>
  </td>
  <td style='width:45pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->servicio}}</span></p>
  </td>
  <td colspan=2 style='width:45pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->sala}}</span></p>
  </td>
  <td  style='width:45pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:17.3pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->cama}}</span></p>
  </td>
 </tr>
 <tr>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>DIAGNÓSTICO</span></b><span
  style='font-size:7.0pt'> <b>PREOPERATORIO</b></span></p>
  </td>
  <td colspan=4 style='width:10pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>DIAGNÓSTICO</span></b><span
  style='font-size:7.0pt'> <b>POSTOPERATORIO</b></span></p>
  </td>
  <td colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>OPERACIÓN</span></b><span
  style='font-size:7.0pt'> <b>PROPUESTA</b></span></p>
  </td>
 </tr>
 <tr style='height:17.7pt'>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:17.7pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->diagnostico_preoperatorio}}</span></p>
  </td>
  <td colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.7pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->diagnostico_postoperatorio}}</span></p>
  </td>
  <td colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.7pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7pt'>{{$record->operacion_propuesta}}</span></p>
  </td>
 </tr>
 <tr>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>CIRUJANO</span></b></p>
  </td>
  <td colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>AYUDANTES</span></b></p>
  </td>
  <td colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>OPERACIÓN</span></b><span
  style='font-size:7.0pt'> <b>REALIZADA</b></span></p>
  </td>
  @php
  $doctor_record = null; $doctor_ayudante_record = null;
  if($record->id_doctor != null ){
    $doctor_record = Sis_medico\User::find($record->id_doctor);
  }
  if($record->id_doctor_ayudante != null ){
    $doctor_ayudante_record = Sis_medico\User::find($record->id_doctor_ayudante);
  }
 @endphp
 </tr>
 <tr style='height:18.1pt'>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:18.1pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if($doctor_record != null) {{ $doctor_record->nombre1 }} {{$doctor_record->nombre2}} {{$doctor_record->apellido1}} {{$doctor_record->apellido2}}  @else @if($agenda->paciente_dr=='1') Dr(a). {{$doctor->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}} @else  @endif @endif</span></p>
  </td> 
  <td colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt; 
  padding:0cm 5.4pt 0cm 5.4pt;height:18.1pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if(!is_null($doctor_ayudante_record))   @if($doctor_ayudante_record != null) {{ $doctor_ayudante_record->nombre1 }} {{$doctor_ayudante_record->nombre2}} {{$doctor_ayudante_record->apellido1}} {{$doctor_ayudante_record->apellido2}} @else Dr(a). {{$doctor->nombre1}} {{$doctor->nombre2}} {{$doctor->apellido1}} {{$doctor->apellido2}} @endif @endif</span></p>
  </td>
  <td colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18.1pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7pt'>{{$record->operacion_realizada}}</span></p>
  </td>
 </tr>
 <tr>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>ANESTESIÓLOGO</span></b></p>
  </td>
  <td colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>AYUDANTES</span></b></p>
  </td>
  <td colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>INSTRUMENTISTAS</span></b></p>
  </td>
 </tr>
 <tr style='height:17.8pt'>
  <td colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:17.8pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if(!is_null($record->id_anestesiologo)) @if($record->id_anestesiologo == "1203240658") Lcda. @else Dr. @endif  {{$record->anestesiologo->nombre1}} {{$record->anestesiologo->nombre2}} {{$record->anestesiologo->apellido1}} {{$record->anestesiologo->apellido2}}@endif<</span></p>
  </td>
  <td colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.8pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if(!is_null($record->id_ayudante)) @if($record->id_ayudante == "1203240658") Lcd(a). @else Dr. @endif {{$record->ayudante->nombre1}} {{$record->ayudante->nombre2}} {{$record->ayudante->apellido1}} {{$record->ayudante->apellido2}}@endif</span></p>
  </td>
  <td  colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:17.8pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>@if($record->id_instrumentista != "") Enf. {{$record->instrumentista->nombre1}} {{$record->instrumentista->nombre2}} {{$record->instrumentista->apellido1}} {{$record->instrumentista->apellido2}}@endif</span></p>
  </td>
 </tr>
 <tr style='height:13.65pt'>
  <td  colspan=4 style='width:135pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:13.65pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>&nbsp;</span></p>
  </td>
  <td  colspan=4 style='width:145pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.65pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:8.0pt'>REGISTRO
  TRANS-ANESTESICO</span></b></p>
  </td>
  <td  colspan=4 style='width:135pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:13.65pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>&nbsp;</span></p>
  </td>
 </tr>
 <tr style='height:240pt'>
  <td  colspan=12 style='border:solid windowtext 1.0pt;
  border-top:none;height:83.95pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>
   @if(is_null($record->url_imagen))
   <img width="757" src="{{base_path().'/public/img_formato/record.png'}}"></span></p>
   @else
  <img src='{{base_path().'/storage/app/hc_ima/'.$record->url_imagen}}' width="757">

  @endif


  </td>
 </tr>
</table>


<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
  <tr>
  <td colspan=20 style='width:465.5pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>DROGAS
  ADMINISTRADAS</span></b></p>
  </td>
  <td colspan=3 style='width:80pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>TIEMPOS</span></b></p>
  </td>
 </tr>
 <tr>
  <td style='width:1pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height: 13pt;'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>N.</span></b></p>
  </td>
  <td colspan=6 style='width:10pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>TIPO</span></b></p>
  </td>
  <td colspan=1 style='width:0.1pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>4</span></b></p>
  </td>
  <td colspan=6 style='width:10pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d4}}</span></p>
  </td>
  <td colspan=1 style='width:0.1pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>8</span></b></p>
  </td>
  <td colspan=5 style='width:10pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d8}}</span></p>
  </td>
  <td colspan=3 style='border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:6.0pt'>DURACION
  ANESTESIA</span></b></p>
  </td>
 </tr>
 <tr>
  <td style='width:3pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height: 13pt;'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>1</span></b></p>
  </td>
  <td colspan=6 style='width:10pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d1}}</span></p>
  </td>
  <td colspan=1 style='width:1.5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>5</span></b></p>
  </td>
  <td colspan=6 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d5}}</span></p>
  </td>
  <td colspan=1 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>9</span></b></p>
  </td>
  <td colspan=5 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d9}}</span></p>
  </td>
  <td colspan=3 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>&nbsp;{{substr($record->duracion_anestesia, 0, -3)}}</span></p>
  </td>
 </tr>
 <tr>
  <td style='width:3pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height: 13pt;'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>2</span></b></p>
  </td>
  <td colspan=6 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d2}}</span></p>
  </td>
  <td colspan=1 style='width:1.5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>6</span></b></p>
  </td>
  <td colspan=6 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d6}}</span></p>
  </td>
  <td colspan=1 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>#</span></b></p>
  </td>
  <td colspan=5 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d10}}</span></p>
  </td>
  <td colspan=3 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:6.0pt'>DURACION
  OPERACIÓN</span></b></p>
  </td>
 </tr>
 <tr>
  <td style='width:3pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height: 13pt;'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>3</span></b></p>
  </td>
  <td colspan=6 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d3}}</span></p>
  </td>
  <td colspan=1 style='width:1.5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>7</span></b></p>
  </td>
  <td colspan=6 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d7}}</span></p>
  </td>
  <td colspan=1 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>#</span></b></p>
  </td>
  <td colspan=5 style='width:71pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{$record->d11}}</span></p>
  </td>
  <td colspan=3 style='width:3pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span style='font-size:7.0pt'>{{substr($record->duracion_operacion, 0, -3)}}</span></p>
  </td>
 </tr>

</table>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:5pt'>
  <td  colspan=3 style='width:100pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:9.0pt'>TÉCNICAS</span></b></p>
  </td>
  <td  colspan=4 style='width:102pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:9.0pt'>INFUSIONES</span></b></p>
  </td>
  <td  colspan=2 style='width:100pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:9.0pt'>COMPLICACIONES
  OPERATORIAS</span></b></p>
  </td>
 </tr>
 <tr style='height:5pt'>
  <td  colspan=2 style='width:50pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>GENERAL</span></b></p>
  </td>
  <td  style='width:50pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7.0pt'>CONDUCTIVA</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom: none;border-right:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0pt;padding-bottom: -2cm;'>
  <p class=MsoNormal style='line-height: normal;'><b><span style='font-size:6.0pt'>DEXTROSA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C.C</span></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:7.0pt; text-align:center;'>{{$record->dextrosa}}</span></p>
  </td>
  <td  style='width:50pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:7.0pt'> </span></b></p>
  </td>
  <td  style='width:4.0cm;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:7.0pt'> </span></b></p>
  </td>
 </tr>
 <tr style='height:5pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'></span><b><span style='font-size:7.0pt'></span></b></p>
  </td>
  <td  style='width:50pt;border: none;
  border-right: solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:9.0pt'></span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:7.0pt'></span></b></p>
  </td>
  <td  style='width:100pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 28) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}

if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>HIPOTENSION</span></b></p>
  </td>
  <td  style='width:60pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 34) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>ARRITMIAS</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13 <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 1) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>SISTEM.
  ABIERTO</span></b></p>
  </td>
  <td  style='width:70pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 18) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>ASEPSIA DE PIEL</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>CLORURO DE SODIO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C.C</span></b><br><span style='font-size:6.0pt; text-align:center;'>{{$record->cloruro_sodio}}</span></p>
  </td>
  <td  style='width:50pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 29) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>DEFICIENCIA RESPIRATORIA </span></b></p>
  </td>
  <td  style='width:4.0cm;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 35) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>PERFORACIÓN DURAMADRE </span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 2) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>SISTEM. CERRADO</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>CON </span></b><b><span
  style='font-size:8.0pt'>_______</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>LACTATO DE RINGER&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C.C</span></b><br><span style='font-size:6.0pt; text-align:center;'>{{$record->lactato_ringer}}</span></p>
  </td>
  <td  style='width:50pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 30) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>DEFICIENCIA TÉCNICA</span></b></p>
  </td>
  <td  style='width:4.0cm;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 36) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>NAUSEAS - VÓMITOS</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 3) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>SISTEM. SEMI-CERR.</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
    <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 19) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>RAQUIDEA</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>SANGRE O DERIVADOS&nbsp;&nbsp;C.C</span></b><br><span style='font-size:6.0pt; text-align:center;'>{{$record->sangre_derivados}}</span></p>
  </td>
  <td  style='width:50pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 31) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>CONDUCTIVA INSUFICIENTE</span></b></p>
  </td>
  <td  style='width:4.0cm;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 37) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>LARINGOESPASMO</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 4) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> >></span><b><span style='font-size:6.0pt'>APARATOS USADOS</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 20) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>EPIDURAL</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>E EXPANSORES&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C.C</span></b><br><span style='font-size:6.0pt; text-align:center;'>{{$record->expansores}}</span></p>
  </td>
  <td  style='width:50pt;border:none;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 32) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>PARO CARDIACO</span></b></p>
  </td>
  <td  style='width:4.0cm;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:5pt;border:none;border-left:solid windowtext 1.0pt;border-right: solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 6) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>CIRC.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 5) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><span style='font-size:6.0pt'>VAIVEN</span></b></p>
  </td>

  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 21) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>SIMPLE</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='font-size:6.0pt;margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>TOTAL</span></b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @php
  $total = $record->total;
@endphp{{$total}}</p>
  </td>
  <td  style='width:50pt;border:none;border-bottom:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 33) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>CAMBIO DE TÉCNICA</span></b></p>
  </td>
  <td  style='width:4.0cm;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 7) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>MÁSCARA FACIAL </span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 22) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>CONTÍNUA</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border:none;
  border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>HEMORRAGIA</span></b></p>
  </td>
  <td  colspan=2 style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>COMENTARIOS</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 8) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>MÁSCARA LARÍNGEA N.</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>ALTURA PUNCIÓN</span></b></p>
  </td>
  <td  colspan=4 valign=bottom style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:right;line-height:normal'><b><span style='font-size:6.0pt'>C.C
  APROX.</span></b></p>
  </td>
  <td  colspan=2 style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>{{$record->comentarios}}</span></b></p>
  </td>
 </tr>
 <tr style='height:10pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>INT.TRAQUEAL</span></b></p>
  </td>
  <td  style='width:10pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 23) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>PUNCIÓN LAT.</span></b></p>
  </td>
  <td  colspan=4 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:6.0pt'>APAGAR</span></b></p>
  </td>
  <td  colspan=2 style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:18pt'>
  <td colspan="2" style='width:5pt;border:none;border-left:solid windowtext 1.0pt;border-right: solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 10) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>ORAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 9) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span></b><b><span style='font-size:6.0pt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NASAL</span></b></p>
  </td>

  <td  style='width:5pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 24) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>LÍNEA MEDIA</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:18pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:5.0pt'>1 MIN</span></b></p>
  </td>
  <td  style='width:5pt;border:solid windowtext 1.0pt;border-left:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:5.0pt'>5 MIN</span></b></p>
  </td>
  <td  style='width:5pt;border:solid windowtext 1.0pt;border-left:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:5.0pt'>10 MIN</span></b></p>
  </td>
  <td  style='width:5pt;border:solid windowtext 1.0pt;border-left:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:5.0pt'>MUERTO</span></b></p>
  </td>
  <td  colspan=2 style='width:5pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:18pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td colspan="2" style='width:5pt;border:none;border-left:solid windowtext 1.0pt;border-right: solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:13px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 12) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>RAPIDO&nbsp;&nbsp;&nbsp;<img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 11) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span></b><b><span style='font-size:6.0pt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LENTO</span></b></p>
  </td>
  <td  style='width:10pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 25) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>AGUJA</span></b></p>
  </td>
  <td  valign=top style='width:10pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td valign=top style='width:10pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td valign=top style='width:10pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td valign=top style='width:10pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td  colspan=2 style='width:10pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt'>TUBO N.</span></b><b><span
  style='font-size:8.0pt;border-bottom: 1px solid black'> &nbsp;&nbsp;&nbsp;{{$record->tubo}} </span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 26) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>NIVEL</span></b></p>
  </td>
  <td  colspan=4 style='width:50pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:6.0pt'>TÉCNICAS
  ESPECIALES</span></b><br></p>
  </td>
  <td  colspan=2 style='width:214.8pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 14) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>MANGUITO INFLAQ.</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 27) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>HIPERBARA</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:6.0pt; text-align:center;'>{{$record->tecnicas_especiales}}</span></b></p>
  </td>
  <td  colspan=2 style='width:214.8pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 15) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>TAPONAMIENTO</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>POSICIÓN PACIENTE</span></b></p>
  </td>
  <td  colspan=4 valign=top style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td  colspan=2 style='width:214.8pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td  colspan=2 style='width:50pt;border-top:none;border-left:
  solid windowtext 1.0pt;border-bottom:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 16) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>ANST. TOPICA</span></b></p>
  </td>
  <td  style='width:50pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>@php $conducido_a = Sis_medico\Sala::find($record->id_sala); @endphp
  <td  colspan=4 valign=top style='width:50pt;border:none;
  border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:5.0pt'>CONDUCIDO A:   </span><span style='font-size:5.0pt'>@if(!is_null($conducido_a)) {{ $conducido_a->nombre_sala}} @else RECUPERACION @endif</span></b></p>
  </td>
  <td  colspan=2 style='width:214.8pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>__________________</p>
  </td>
 </tr>
 <tr style='height:15pt'>
  <td  colspan=2 style='width:50pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><span style='position:absolute;z-index:251663360;margin-left:114px;
  margin-top:3px;width:14px;height:5px'><img width=14 height=13
  <?php $contador = 0;
foreach ($record_tecnicas as $value) {
    if ($value->id_tecnicas_anestesicas == 17) {
        $contador = 1;?> src="{{base_path().'/public/img_formato/cuadx.png'}}" <?php
}
}
if ($contador == 0) {?> src="{{base_path().'/public/img_formato/cuad.png'}}"<?php }?> ></span><b><span style='font-size:6.0pt'>ANST. TRANSORAL</span></b></p>
  </td>
  <td  style='width:50pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:9.0pt'>&nbsp;</span></b></p>
  </td>
  <td  colspan=4 valign=bottom style='width:50pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:-3cm 0cm 0cm 0cm;height:15pt'>
  <p class=MsoNormal align=right style='margin-bottom:0cm;margin-bottom:.001pt;
  text-align:center;line-height:normal'><b><span style='font-size:5.0pt'>POR</span></b><b><span
  style='font-size:5.0pt'>: @if(!is_null($record->id_guiado))Dr. {{$record->guia->nombre1}} {{$record->guia->apellido1}}@endif</span></b> <b><span
  style='font-size:5.0pt'> HORA: </span></b><b><span style='font-size:5.0pt'>{{substr($record->hora,0,5)}}</span></b></p>
  </td>
  @php $firma     = Sis_medico\Firma_Usuario::where('id_usuario', $record->id_anestesiologo)->first(); 
  @endphp
  <td  colspan=2 valign=top style='width:214.8pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:15pt;position: relative;'>
  @if(!is_null($firma))
    <img width=200 height=100 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="position: absolute;top: -130px;left: 90px;width: 110px;" align=center hspace=12><br>
  @endif
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:9.0pt'>FIRMA
  ANESTESIOLOGO</span></b></p>
  </td>
 </tr>
</table>


 <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>

 <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>

 <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>
  <div style="margin-top: 10px;">
    &nbsp;
  </div>

<p class=MsoNormal align=center style='text-align:center'><span style='position:relative;
  z-index:-1895823360'><span style='left:0px;position:absolute;left:23px;
  top:-7px;width:127px;height:33px'><img width=127 height=33
  src="{{base_path().'/storage/app/logo/logo_endoscopynet.png'}}"></span><b><span style='line-height:57%'>VALORACIÓN
POST-ANESTÉSICA</span></b></p>

<p class=MsoNormal>&nbsp;</p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:19.6pt'>
  <td  style='width:200pt;border:solid windowtext 1.0pt;padding:
  0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:8.0pt'>SISTEMA
  CIRCULATORIO</span></b></p>
  </td>
  <td  style='width:120pt;border:solid windowtext 1.0pt;border-left:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>CONCIENCIA</span></b></p>
  </td>
  <td  style='width:214pt;border:solid windowtext 1.0pt;border-left:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:8.0pt'>SATURACIÓN</span></b></p>
  </td>
 </tr>
 <tr style='height:10.6pt'>
  <td  style='width:12pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:10.6pt'>
  <p class="MsoNormal" style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span  style='font-size:8.0pt;'>PA 20% NIVEL PRE-ANESTÉSICO  &nbsp;
  </span><span @if($record->sistema_circulatorio ==2) class="bordes" @endif style='font-size:8.0pt;'>2</span></b></p>
  </td>
  <td  style='width:12pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:5.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->conciencia ==2) class="bordes" @endif>DESPIERTO</span></b></p>
  </td>
  <td  style='width:100pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:20.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>SAT O2 + 92% AIRE AMBIENTE
          </span><span @if($record->saturacion ==2) class="bordes" @endif style='font-size:8.0pt;'>2</span></b></p>
  </td>
 </tr>
 <tr style='height:20.9pt'>
  <td  style='width:12pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>PA 20 - 49% NIVEL PRE-ANESTÉSICO </span><span @if($record->sistema_circulatorio ==1) class="bordes" @endif style='font-size:8.0pt'>1</span></b></p>
  </td>
  <td  style='width:100pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->conciencia ==1) class="bordes" @endif>DESPIERTO AL LLAMADO</span></b></p>
  </td>
  <td  style='width:100pt;border:none;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>NECESITA O2 SAT &gt; 90% </span><span @if($record->saturacion ==1) class="bordes" @endif style='font-size:8.0pt;'>1</span></b></p>
  </td>
 </tr>
 <tr style='height:24.6pt'>
  <td  style='width:12pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>PA 50% NIVEL PRE-ANESTÉSICO
  </span><span @if($record->sistema_circulatorio ==0) class="bordes" @endif style='font-size:8.0pt'>0</span></b></p>
  </td>
  <td  style='width:12pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->conciencia ==0) class="bordes" @endif>NO RESPONDE+</span></b></p>
  </td>
  <td  style='width:100pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>SAT O2 &gt; 90% CON O2 </span><span @if($record->saturacion ==0) class="bordes" @endif style='font-size:8.0pt;'>0</span></b></p>
  </td>
 </tr>
</table>

<p class=MsoNormal>&nbsp;</p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:19.6pt'>
  <td  style='width:557.5pt;border:solid windowtext 1.0pt;padding:
  0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>ACTIVIDADES</span></b></p>
  </td>
 </tr>
 <tr style='height:20.6pt'>
  <td  style='width:550pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:20.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>CAPAZ DE MOVER LAS CUATRO EXTREMIDADES
  VOLUNTARIO O BAJO
  ORDENES                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->actividades ==2) class="bordes" @endif style='font-size:8.0pt;'>2</span></b></p>
  </td>
 </tr>
 <tr style='height:20.9pt'>
  <td  style='width:550pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>CAPAZ DE MOVER LAS DOS EXTREMIDADES
  VOLUNTARIO O BAJO
  ORDENES                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->actividades ==1) class="bordes" @endif style='font-size:8.0pt;'>1</span></b></p>
  </td>
 </tr>
 <tr style='height:24.6pt'>
  <td  style='width:550pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>CAPAZ DE MOVER UNA EXTREMIDAD
  VOLUNTARIO O BAJO ORDENES      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->actividades ==0) class="bordes" @endif style='font-size:8.0pt;'>0</span></b></p>
  </td>
 </tr-->
</table>

<p class=MsoNormal>&nbsp;</p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:19.6pt'>
  <td  style='width:522.8pt;border:solid windowtext 1.0pt;padding:
  0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>RESPIRACIÓN</span></b></p>
  </td>
 </tr>
 <tr style='height:20.6pt'>
  <td  style='width:557.5pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:20.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>CAPAZ DE RESPIRAR PROFUNDAMENTE O
  TOSER                                                                                                                                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->respiracion ==2) class="bordes" @endif style='font-size:8.0pt;'>2</span></b></p>
  </td>
 </tr>
 <tr style='height:20.9pt'>
  <td  style='width:522.8pt;border-top:none;border-left:solid windowtext 1.0pt;
  border-bottom:none;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;
  height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>APNEA RESPIRACIÓN LIMITADA O
  TAQUIPMEA                                                                                                                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->respiracion ==1) class="bordes" @endif style='font-size:8.0pt;'>1</span></b></p>
  </td>
 </tr>
 <tr style='height:24.6pt'>
  <td  style='width:522.8pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt'>APNEICO O CON RESPIRADOR ARTIFICIAL
                                                                                                                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span @if($record->respiracion ==0) class="bordes" @endif style='font-size:8.0pt;'>0</span></b></p>
  </td>
 </tr>
</table>

<p class=MsoNormal>&nbsp;</p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:19.6pt'>
  <td  style='width:558pt;border:solid windowtext 1.0pt;padding:
  0cm 5.4pt 0cm 5.4pt;height:19.6pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>TIPO
  DE ANALGESIA</span></b></p>
  </td>
 </tr>
 <tr style='height:20.6pt'>
  <td  style='width:522.8pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:20.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->tipo_analgesia ==2) class="bordes" @endif >ANALGESIA INTRAVENOSA</span></b></p>
  </td>
 </tr>
 <tr style='height:20.9pt'>
  <td  style='width:522.8pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:20.9pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->tipo_analgesia ==1) class="bordes" @endif >ANALGESIA PERIDURAL</span></b></p>
  </td>
 </tr>
 <tr style='height:24.6pt'>
  <td  style='width:522.8pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 5.4pt 0cm 5.4pt;height:24.6pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:8.0pt' @if($record->tipo_analgesia ==0) class="bordes" @endif >ANALGESIA POR PRN</span></b></p>
  </td>
 </tr>
</table>

<p class=MsoNormal>&nbsp;</p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='margin-left: 4.8pt;border-collapse:collapse;border:none'>
 <tr style='height:18.8pt'>
  <td  colspan=9 style='width:566pt;border:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:18.8pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>CSV</span></b></p>
  </td>
 </tr>
 <tr style='height:20.5pt'>
  <td  style='width:5pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>HORA</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>P. ART</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>PULSO</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>RESP</span></b>.</p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>O2</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>ORINA
  C.C</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt; width: 5pt;'>TEMP.</span></b></p>
  </td>
  <td  style='width:25pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:none;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
  normal'><b><span style='font-size:7pt'>MEDICACIÓN</span></b></p>
  </td>
  <td  style='width:25pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>ANOTACIONES</span></b></p>
  </td>
 </tr>
 @foreach($csv as $value)
 <tr style='height:20.5pt'>
  <td  style='width:5pt;border:solid windowtext 1.0pt;border-top:
  none;padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{substr($value->hora, 0,5)}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->presion_arterial}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->pulso}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->respiracion}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->o2}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->orina}}</span></b></p>
  </td>
  <td  style='width:5pt;border-top:none;border-left:none;border-bottom:
  solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;padding:0cm 1pt 0cm 1pt;
  height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->temperatura}}</span></b></p>
  </td>
  <td  style='width:25pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:none;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>
  <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:7pt'>{{$value->anotaciones}}</span></b></p>
  </td>
  <td  style='width:25pt;border-top:none;border-left:none;
  border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 1pt 0cm 1pt;height:20.5pt'>

  </td>
 </tr>
 @endforeach
</table>
<br>
<div style="text-align: center;">
  @if(!is_null($firma))
    
    <img width=200 height=100 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="width: 110px;" align=center ><br>
  @endif
</div>
<p class=MsoNormal>&nbsp;</p>

</div>

</body>

</html>
