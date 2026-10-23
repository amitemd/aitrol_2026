<html>

<head>


<style>
<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

 /* Font Definitions */
 @font-face
    {font-family:"Cambria Math";
    panose-1:2 4 5 3 5 4 6 3 2 4;
    mso-font-charset:0;
    mso-generic-font-family:roman;
    mso-font-pitch:variable;
    mso-font-signature:3 0 0 0 1 0;}
@font-face
    {font-family:Calibri;
    panose-1:2 15 5 2 2 2 4 3 2 4;
    mso-font-charset:0;
    mso-generic-font-family:swiss;
    mso-font-pitch:variable;
    mso-font-signature:-536859905 1073786111 1 0 511 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
    {mso-style-unhide:no;
    mso-style-qformat:yes;
    mso-style-parent:"";
    margin-top:0cm;
    margin-right:0cm;
    margin-bottom:8.0pt;
    margin-left:0cm;
    line-height:107%;
    mso-pagination:widow-orphan;
    font-size:8pt;
    font-family:"Calibri",sans-serif;
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:Calibri;
    mso-fareast-theme-font:minor-latin;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-fareast-language:EN-US;}
.MsoChpDefault
    {mso-style-type:export-only;
    mso-default-pr50;
    font-family:"Calibri",sans-serif;
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:Calibri;
    mso-fareast-theme-font:minor-latin;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-fareast-language:EN-US;}
.MsoPapDefault
    {mso-style-type:export-only;
    margin-bottom:2pt;
    line-height:107%;}
@page WordSection1
    {size:595.3pt 999pt;
    margin:0pt 0pt 0pt 0pt;
    margin-top: -10cm;
    mso-header-margin:20pt;
    mso-footer-margin:20pt;
    mso-paper-source:0;}
div.WordSection1
    {page:WordSection1;size: 595.3pt 1000pt !important;}
    



</style>

</head>

<body lang=ES-EC style='tab-interval:35.4pt'>
  <div width=50>
    <div class=WordSection1 style="margin-top: -60pt">

        <p class=MsoNormal><span style='mso-ignore:vglayout'>
        <br><br>
        <table cellpadding=0 cellspacing=0 align=left>
         <tr>
          <td width=0 height=18></td>
         </tr>
         @php
          $protocolo = Sis_medico\Protocolo::find($orden->id_protocolo);
          $user = Sis_medico\User::find($orden->id_doctor_ieced);
          $empresa_prioridad_1 = "";
          $empresa_1 = Sis_medico\Empresa::where('prioridad', '1')->first();
          $empresa_prioridad_1 = $empresa_1->id;
        
         @endphp
         <tr>
          <td></td>
          <td width=150 height=20 style='border:.75pt solid black;vertical-align:top;width: 50pt;@if(!is_null($protocolo))@if($protocolo->pre_post=='POST') background-color: #ffd1b3; @else background-color: #b3ffec ; @endif @endif'><span
          style='position:absolute;mso-ignore:vglayout;z-index:251659264'>
          <table cellpadding=0 cellspacing=0 width="100%" >
           <tr>
            <td >
            <div style='padding:4.35pt 10pt 10pt 7.95pt;' class=shape>
            <p class=MsoNormal align=center style='text-align:center; font-size: 8pt'><span lang=ES
            style='mso-ansi-language:ES'><b>FORMULARIO 010</b></span></p>
            </div>
            </td>
           </tr>
          </table>
          </span><![endif]><![if !mso & !vml]>&nbsp;<![endif]><![if !vml]></td>
         </tr>
        </table>

        </span><![endif]><o:p>&nbsp;</o:p></p>

        <p class=MsoNormal style="height: 1px;"><o:p>&nbsp;</o:p></p>

        <br style='mso-ignore:vglayout' clear=ALL>

        <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 width=100
         style='width:100pt;border-collapse:collapse;border:none;mso-border-alt:solid windowtext .5pt;
         mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt'>
         <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
          <td width=10 colspan=3 valign=top style='background-color: white;width:60pt;border:solid windowtext 1.0pt;border-right: none;
          mso-border-alt:solid windowtext .5pt;padding:2.5pt 5.4pt 0cm 5.4pt;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 8pt;'><b>INSTITUCION DEL SISTEMA</b></p>
          </td>
          <td width=10 colspan=3 valign=top style='background-color: white;width:20pt;border:solid windowtext 1.0pt;border-right: none;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:2.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 8pt;'><b>UNIDAD OPERATIVA</b></p>
          </td>
          <td width=10 valign=top style='background-color: white;width:20pt;border:solid windowtext 1.0pt;border-right: none;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:2.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 8pt;'><b>COD.UO</b></p>
          </td>
          <td width=50 colspan=3 valign=top style='background-color: white;width:20pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:2.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='background-color: white;margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 8pt;'><b>COD.LOCALIZACION</b></p>
          </td>
          <td width=50 valign=top rowspan=2 style='background-color: white;width:20pt;border:solid windowtext 1.0pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;border-left: solid windowtext 1.0pt !important;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 7pt;'><b>NUMERO DE</b></p>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 8pt;'><b>HISTORIA CLINICA</b></p>
          </td>
         </tr>
         <tr style='mso-yfti-irow:1'>
          <td width=10 colspan=3 rowspan=2 valign=top style='width:145pt;
          border:solid windowtext 1.0pt;border-top:none;mso-border-top-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:4pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'>{{$empresa->nombrecomercial}}</span></p>
          </td>
          <td width=10 colspan=3 rowspan=2 valign=top style='width:145pt;
          border-top:none;border-left:none;border-bottom:solid windowtext 1.0pt;
          border-right:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext .5pt;
          mso-border-left-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
          padding:4pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'>{{$empresa->nombrecomercial}}</p>
          </td>
          <td width=10 rowspan=2 valign=top style='width:20pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><o:p>&nbsp;</o:p></p>
          </td>
          <td width=50 valign=top style='background-color: white;width:10pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:6pt'><b>PARROQUIA</b><o:p></o:p></span></p>
          </td>
          <td width=57 valign=top style='background-color: white;width:10pt;border-top:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;'><b>CANTÓN</b><o:p></o:p></span></p>
          </td>
         <td width=50 valign=top style='background-color: white;width:10pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.5pt !important;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 0pt 0cm 0pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:6pt'><b>PROVINCIA</b><o:p></o:p></span></p>
          </td>
         </tr>
         <tr style='mso-yfti-irow:2'>
          <td width=30 valign=top style='width:10pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 6pt;'>@if(!is_null($empresa->parroquia)){{$empresa->parroquia}}@endif</p>
          </td>
          <td width=30 valign=top style='width:10pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 6pt;'>@if(!is_null($empresa->canton)){{$empresa->canton}}@endif</p>
          </td>
          <td width=30 valign=top style='width:10pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 6pt;'>@if(!is_null($empresa->provincia)){{$empresa->provincia}}@endif</p>
          </td>
          <td width=50 valign=top style='width:20pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;font-size: 9pt'><o:p>{{$orden->id_paciente}}</o:p></p>
          </td>
         </tr>
         <tr style='mso-yfti-irow:3;'>
          <td width=10 colspan=2 height=11 valign=top style='background-color: white;width:20pt;border:solid windowtext 1.0pt;
          border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
          padding:0.3pt 5.4pt 0cm 5.4pt;border-right:none;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>APELLIDO PATERNO</b><o:p></o:p></span></p>
          </td>
          <td width=10 colspan=2 valign=top style='background-color: white;width:20pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0.3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>APELLIDO MATERNO</b><o:p></o:p></span></p>
          </td>
          <td width=50 colspan=2 valign=top style='background-color: white;width:20pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0.3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>PRIMER NOMBRE</b><o:p></o:p></span></p>
          </td>
          <td width=10 colspan=3 valign=top style='background-color: white;width:20pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0.3pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>SEGUNDO NOMBRE</b><o:p></o:p></span></p>
          </td>
          <td width=5 valign=top style='background-color: white;width:20pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 1.4pt 0cm 1.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>EDAD</b><o:p></o:p></span></p>
          </td>
          <td width=30 valign=top style='background-color: white;width:20pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><b>CEDULA</b><o:p></o:p></span></p>
          </td>
         </tr>
         <tr style='mso-yfti-irow:4;mso-yfti-lastrow:yes;height:10pt'>
          <td width=50 valign=top colspan=2 style='width:50pt;border:solid windowtext 1.0pt;
          border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>{{$orden->papellido1}}</o:p></span></p>
          </td>
          <td width=50 colspan=2 valign=top colspan=2 style='width:50pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>@if($orden->papellido2=='N/A'||$orden->papellido2=='(N/A)') @else{{ $orden->papellido2 }} @endif</o:p></span></p>
          </td>
          <td width=50 colspan=2 valign=top style='width:50pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>{{$orden->pnombre1}}</o:p></spa50>
          </td>
          <td width=10 colspan=3 valign=top style='width:10pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>@if($orden->pnombre2=='N/A'||$orden->pnombre2=='(N/A)') @else{{ $orden->pnombre2 }} @endif</o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:5pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8pt;'><o:p><?php
                                if(!is_null($orden->pfecha_nacimiento)){
                                    $fecha = $orden->pfecha_nacimiento;
                                    list($Y,$m,$d) = explode("-",$fecha);
                                    $edad =(date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
                                    echo $edad;    
                                }
                                ?></o:p></span></p>
          </td>
          <td width=147 valign=top style='width:50pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>{{$orden->id_paciente}}</o:p></span></p>
          </td>
         </tr>
         
        </table>

        <div style="height: 15px !important;">&nbsp;</div>

        <table  class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 width=100
         style='width:100pt;border-collapse:collapse;border:none;mso-border-alt:
         solid windowtext .5pt;mso-yfti-tbllook:1184;mso-padding-alt:0cm 6.4pt 0cm 5.4pt'>
         <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:18.2pt'>
          <td width=10 rowspan=2 valign=top style='width:194pt;border-top: solid windowtext 1.0pt;border-bottom: solid windowtext 1.0pt;
          border-right: none;padding:6.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>&nbsp;</o:p></span></p>
          </td>
          <td width=10 valign=top style='width:15pt;border-top: solid windowtext 1.0pt;border-bottom: none;
          border-right: none;border-left: none;height:18.2pt;padding:5.5pt 5.4pt 0cm 5.4pt;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6.0pt;
          mso-bidi-font-size:11.0pt'><b>SERVICIO</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:15pt;border-bottom: none;
          border-right: none;border-left: none;padding:5.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=cen50style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>SALA</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:15pt;border:solid windowtext 1.0pt;border-bottom: none;
          border-right: none;border-left: none;padding:5.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-heigh50rmal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>CAMA</b><o:p></o:p></span></p>
          </td>
          <td width=10 colspan=6 valign=top style='width:20pt;border-bottom: none;
          border-right: none;border-left: none;padding:5.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>PRIORIDAD</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:50pt;border-bottom: none;
          border-left: none;padding:5.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>FECHA DE TOMA</b><o:p></o:p></span></p>
          </td>
         </tr>
         <tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:14.8pt'>
          <td width=10 colspan=3 valign=top style='width:40pt;border-bottom: solid windowtext 1.0pt;
          border-left:none;border-right: none;border-top: none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:18.2pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:7.0pt;
          mso-bidi-font-size:11.0pt'><b>@if($orden->est_amb_hos=='0'){{"AMBULATORIO"}}@else{{"HOSPITALIZADO"}}@endif</b><o:p></o:p></span></p>
          </td>
          
          <td width=10 valign=top style='width:15pt;border-top:none;border-left:none;border-right: none;
          border-bottom:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>URGENTE</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border-top:none;border-left:none;border-right: none;
          border-bottom:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><o:p>&nbsp;</o:p></span></p>
          </td>
          <td width=10 valign=top style='width:15pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>RUTINA</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>&nbsp;</o:p></span></p>
          </td>
          <td width=10 valign=top style='width:15pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>CONTROL</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:none;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><b style='mso-bidi-font-weight:normal'><span
          style='font-size:8.0pt;mso-bidi-font-size:11.0pt'><b>X</b><o:p></o:p></span></b></p>
          </td>
          <td width=10 valign=top style='width:10pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
          mso-border-alt:solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt;height:14.8pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:8.0pt;
          mso-bidi-font-size:11.0pt'><o:p>
            @if($orden->fecha_convenios != null){{substr($orden->fecha_convenios,0 ,10)}}@else{{substr($orden->fecha_orden, 0, 10)}}  @endif
         </o:p></span></p>
          </td>
         </tr>
         
        </table>

        <div style="height: 15px !important;">&nbsp;</div>

        <table width="99%" border="0"> 
          <tr> 
            <td width="38%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan=4 >1 HEMATOLOGIA</td> 
                   
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=1;@endphp
                @foreach($examenes as $examen)
                  @if($examen->id_agrupador=='1')
                    @if($examen->no_orden_pub=='1' && is_null($detalles->where('id_examen',$examen->id)->first()))
                    @else
                      @if((!is_null($detalles->where('id_examen',$examen->id)->first()) && $examen->id=='628')||$examen->id!='628')  
                        @php $cont=$cont + 1; @endphp 
                        <td height="10">{{$examen->nombre}}</td> 
                        <td height="10" width="1%" style="padding-left: 7pt">
                          @if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>
                          @endif
                        </td> 
                      @endif
                      @if($cont=='2')
                        </tr>
                        <tr style='font-size:6pt;'>
                        @php $cont=0; $filas=$filas + 1;@endphp
                      @endif
                    @endif  
                  @endif
                @endforeach
                @if($cont=='1')
                <td height="10">&nbsp;</td>
                <td height="10" width="1%"></td>
                </tr>
                @endif
                @if($cont=='0')
                <td height="10"></td>
                <td height="10" width="1%"></td>
                <td height="10"></td>
                <td height="10" width="1%"></td>
                </tr>
                @endif
                @while($filas<13)
                <tr style='font-size:6pt;'>
                <td height="10">&nbsp;</td>
                <td height="10" width="1%"></td>
                <td height="10">&nbsp;</td>
                <td height="10"  width="1%"></td>
                </tr>
                @php $filas=$filas + 1;@endphp
                @endwhile

                
              </table> 
            </td> 
            <td width="25%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan="3">2 UROANALISIS</td> 
                 @php $filas=1;@endphp   
                </tr>
                @foreach($examenes as $examen)
                  @if($examen->estado=='1')
                    @if($examen->id_agrupador=='3')
                    @if((!is_null($detalles->where('id_examen',$examen->id)->first()) && $examen->id=='201')||$examen->id!='201') 
                    <tr   style='width:120px; font-size:6pt;'>   
                      <td height="10" colspan="2">{{$examen->nombre}}</td> 
                      <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                    </tr>
                    @endif
                    @php $filas=$filas + 1;@endphp
                    @endif
                  @endif  
                @endforeach
                @while($filas<5)
                <tr  style='width:120px; font-size:6pt;'>
                <td height="10" colspan="2">&nbsp;</td>
                <td height="10" >&nbsp;</td>
                </tr>
                @php $filas=$filas + 1;@endphp
                @endwhile
                @php $filas=$filas + 1;@endphp
                <tr style='font-size:9pt;'> 
                  <td height="10" colspan="3">3 COPROLOGICO</td>    
                </tr>
                @foreach($examenes as $examen)
                @if($examen->id_agrupador=='4') 
                <tr  style='width:120px; font-size:6pt;'>   
                  <td height="10" colspan="2">{{$examen->nombre}}</td> 
                  <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td> 
                </tr>
                @php $filas=$filas + 1;@endphp
                @endif
                @endforeach
              </table> 
            </td>
            <td width="37%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan=4 >4 QUIMICA SANGUINEA</td> 
                   
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=1;@endphp
                @foreach($examenes as $examen)
                @if($examen->id_agrupador=='2') 
                  @php $cont=$cont + 1; @endphp 
                  <td height="10">{{$examen->nombre}}</td> 
                  <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                @if($cont=='2')
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=$filas + 1;@endphp
                @endif
                @endif
                @endforeach
                @if($cont=='1')
                <td height="10">&nbsp;</td>
                <td height="10" width="1%"></td>
                </tr>
                @endif
                @if($cont=='0')
                <!--td height="15"></td>
                <td height="15" width="1%"></td>
                </tr-->
                @endif
                @while($filas<=9)
                <!--tr style='font-size:6pt;'>
                <td height="15">&nbsp;</td>
                <td height="15" width="1%">&nbsp;</td>
                <td height="15">&nbsp;</td>
                <td height="15" width="1%">&nbsp;</td>
                </tr-->
                @php $filas=$filas + 1;@endphp
                @endwhile

                
              </table> 
              
            </td
          </tr> 
        </table>
        <table width="99%" border="0"> 
          <tr> 
            <td width="40%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan=5 >5 SEROLOGIA</td> 
                   
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=1;@endphp
                @foreach($examenes as $examen)
                  @if($examen->id_agrupador=='5') 
                    @php $cont=$cont + 1; @endphp 
                      <td height="10" @if($cont=='2') colspan=2 @endif >{{$examen->nombre}}</td> 
                      <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                      @if($cont=='2')
                        </tr>
                        <tr style='font-size:6pt;'>
                        @php $cont=0; $filas=$filas + 1;@endphp
                      @endif
                  @endif
                @endforeach
                @if($cont=='1')
                  <td height="10" colspan=2 >&nbsp;</td>
                  <td height="10" width="1%"></td>
                  </tr>
                @endif
                @if($cont=='0')
                <td height="10" ></td>
                <td height="10" width="1%"></td>
                <td height="10" colspan=2></td>
                <td height="10" width="1%"></td>
                </tr>
                @endif
                @while($filas<6)
                <tr style='font-size:6pt;'>
                <td height="10" >&nbsp;</td>
                <td height="10" width="1%">&nbsp;</td>
                <td height="10" colspan=2>&nbsp;</td>
                <td height="10" width="1%">&nbsp;</td>
                </tr>
                @php $filas=$filas + 1;@endphp
                @endwhile

                
              </table> 
            </td>
            <td width="35%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan=4 >6 BACTERIOLOGIA</td> 
                   
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=1;@endphp
                @foreach($examenes as $examen)
                @if($examen->id_agrupador=='6') 
                  @php $cont=$cont + 1; @endphp 
                  <td height="10">{{$examen->nombre}}</td> 
                  <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                @if($cont=='2')
                </tr>
                <tr style='font-size:6pt;'>
                @php $cont=0; $filas=$filas + 1;@endphp
                @endif
                @endif
                @endforeach
                @if($cont=='1')
                <td height="10">&nbsp;</td>
                <td height="10"></td>
                </tr>
                @endif
                @if($cont=='0')
                <td height="10"></td>
                <td height="10"></td>
                <td height="10"></td>
                <td height="10"></td>
                </tr>
                @endif
                @while($filas<6)
                <tr style='font-size:6pt;'>
                <td height="10">&nbsp;</td>
                <td height="10">&nbsp;</td>
                <td height="10">&nbsp;</td>
                <td height="10">&nbsp;</td>
                </tr>
                @php $filas=$filas + 1;@endphp
                @endwhile

                
              </table> 
            </td>
            <td width="30%"> 
              <table width="100%" border="1" > 
                <tr style='font-size:9pt;'> 
                  <td colspan="2">7 OTROS</td> 
                 @php $filas=1;@endphp   
                </tr>
                @foreach($examenes as $examen)
                  @if($examen->id_agrupador=='7')
                    @if(!$examen->especial_publico) 
                      @if($examen->no_orden_pub=='1' && is_null($detalles->where('id_examen',$examen->id)->first()))
                      @else
                      <tr style='font-size:6pt;'>   
                        <td height="10">{{$examen->nombre}}</td> 
                        <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                      </tr>
                      @php $filas=$filas + 1;@endphp
                      @endif
                    @else
                      @if(!is_null($detalles->where('id_examen',$examen->id)->first()))
                      <tr style='font-size:6pt;'>   
                        <td height="10">{{$examen->nombre}}</td> 
                        <td height="10" width="1%" style="padding-left: 7pt">@if(!is_null($detalles->where('id_examen',$examen->id)->first()))<b> X </b>@endif</td>  
                      </tr>
                      @php $filas=$filas + 1;@endphp 
                      @endif 
                    @endif
                  @endif
                @endforeach
                @while($filas<7)
                <tr style='font-size:6pt;'>
                <td height="10">&nbsp;</td>
                <td height="10">&nbsp;</td>
                </tr>
                @php $filas=$filas + 1;@endphp
                @endwhile
                @php $filas=$filas + 1;@endphp
                
              </table> 
            </td> 
            
          </tr> 
        </table>

        <p style="font-size:  5pt;"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CODIGO</b></p>

        <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
         style='border-collapse:collapse;border:none;mso-border-alt:solid windowtext .5pt;
         mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;margin-top: -5pt;'>
         <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;mso-yfti-lastrow:yes'>
          <td width=10 valign=top style='width:10pt;border:solid windowtext 1.0pt;
          mso-border-alt:solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>FECHA:</b><o:p></o:p></span></p>
          </td>
          <td valign=top style='width:70px;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6.0pt;
          mso-bidi-font-size:11.0pt'><o:p>&nbsp; @if($orden->fecha_convenios != null){{substr($orden->fecha_convenios,0 ,10)}}@else{{substr($orden->fecha_orden, 0, 10)}}  @endif</o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>HORA:</b><o:p></o:p></span></p>
          </td>
          <td valign=top style='width:10px;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6.0pt;
          mso-bidi-font-size:11.0pt'><o:p>&nbsp;@if($orden->fecha_convenios != null){{substr($orden->fecha_convenios,11 ,15)}}@else{{substr($orden->fecha_orden, 11, 15)}}@endif</o:p></span></p>
          </td>
          <td width=20 valign=top style='width:20pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:3.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>NOMBRE DEL PROFESIONAL</b><o:p></o:p></span></p>
          </td>
          <td width=50 valign=top style='width:50;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:3.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>@if($empresa->id==$empresa_prioridad_1) Dr.(a) {{$user->nombre1}} {{$user->apellido1}} @else Dr. CARLOS
  ROBLES MEDRANDA @endif</b><o:p></o:p></span></p>
          </td>
          
          <td width=15 valign=top style='width:15pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>@if($empresa->id==$empresa_prioridad_1) @else 11003 @endif</b><o:p></o:p></span></p>
          </td>
          <td width=10 valign=top style='width:10pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          @php
            $ruta='';
            $firma = Sis_medico\Firma_Usuario::where('id_usuario',$orden->id_doctor_ieced)->first();
            if(!is_null($firma)){
              $ruta = $firma->nombre;
            }
            if($ruta==''){
              $firma = Sis_medico\Firma_Usuario::where('id_usuario','1307189140')->first();
              if(!is_null($firma)){
              $ruta = $firma->nombre;
              }
            }
          @endphp  
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>FIRMA</b><o:p></o:p></span></p>
          </td>
          <td width=20 valign=top style='width:30;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:0cm 5.4pt 0cm 5.4pt'>
        
          @if($ruta!='')
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6.0pt;
          mso-bidi-font-size:11.0pt'><o:p><img style='position: absolute;top: -12px;padding-left: 0px;' width=300 height=120  @if($empresa->id==$empresa_prioridad_1) src="{{base_path().'/storage/app/avatars/'.$ruta}}" @else src="{{base_path().'/storage/app/logo/firma_rb.png'}}" @endif align=center hspace=12></o:p></span></p>
          @endif
          </td>
          <td width=20 valign=top style='width:20pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:5pt;
          mso-bidi-font-size:11.0pt'><b>NUMERO DE HOJA</b></span><span style='font-size:9.0pt;
          mso-bidi-font-size:11.0pt'><o:p></o:p></span></p>
          </td>
          <td width=5 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-left:none;mso-border-left-alt:solid windowtext .5pt;mso-border-alt:
          solid windowtext .5pt;padding:5.5pt 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:6pt;
          mso-bidi-font-size:11.0pt'><b>1</b><o:p></o:p></span></p>
          </td>
         </tr>
        </table><br>
        <!-- @if( $orden->fecha_orden < '2021-06-01  0:00:00') 
        <p style="font-size:  6pt; margin-top: 0pt;"><img style='position: absolute; left: 150px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs_karla.png'}}" align=center hspace=12><b>SNS-MSP / HCU-form.010A / 2008</b></p>
        @else
        <p style="font-size:  6pt; margin-top: 0pt;"><img style='position: absolute; left: 150px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs_responsable.jpg'}}" align=center hspace=12><b>SNS-MSP / HCU-form.010A / 2008</b></p>
        @endif -->



    </div>
  </div>
</body>

</html>