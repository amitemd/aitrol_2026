<!DOCTYPE html>
<html>
<head>
  <title>HCU_006</title>

  <style>
    @page { margin: 80px 30px; }
    #header { position: fixed; left: 0px; top: -100px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -150px; right: 0px; height: 150px; }
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

    div.WordSection1
      {page:WordSection1;}
    .parrafo2 p{
      margin: 0;
    }
  </style>
</head>
<body>

    <div id="header">

      @php
        $agenda = Sis_medico\Agenda::find($data->id_agenda);
        $empresa = Sis_medico\Empresa::find($id_empresa);

      @endphp
      <div style="float:left;width: 70%;"><p class=MsoNormal align=center style='text-align:center; padding-top: 2pt; margin-top: 40pt;'><b><span style='font-size:25.0pt;line-height:107%'>EPICRISIS</span></b></p>
      </div>

      @if(!is_null($empresa->logo_form))
      <div style="float:left;width: 30%;"><p class=MsoNormal style='text-align:right; padding-top: 4pt; margin-top: 12pt;'><img width=100 height=60 src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}" hspace=12></p>
      </div>
      @else
      <div style="float:left;width: 30%;">
        <p class=MsoNormal style=' padding-top: 2pt; margin-top: 24pt;'><b>{{$empresa->nombre_form}}</b></p>
      </div>
      @endif

      <div style="clear:both;">&nbsp;</div>

    </div>

      @php
        $nom = "";

        if(!is_null($firma)){
          $nom = "/storage/app/avatars/$firma->nombre";
        }
      @endphp


    @if($nom != "")
    <div id="footer">
      <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:center;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img style='position: absolute;top: -12px;margin-left: 320pt;padding-left: 0px;' width=200 height=75 src="{{base_path().$nom}}" align=center hspace=12></o:p></span>
      </p>
    </div>
    @endif



    <div id="content">

      <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 style='margin-left:-8.5pt;border-collapse:collapse;border:none'>
         <tr>
          <td width=118 colspan=2 valign=top style='width:105pt;border:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>ESTABLECIMIENTO</span></b></p>
          </td>
          <td width=96 valign=top style='width:95pt;border:solid windowtext 1.0pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>NOMBRES</span></b></p>
          </td>
          <td width=108 colspan=2 valign=top style='width:95pt;border:solid windowtext 1.0pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>APELLIDOS</span></b></p>
          </td>
          <td width=75 valign=top style='width:60pt;border:solid windowtext 1.0pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>SEXO</span></b></p>
          </td>
          <td width=71 valign=top style='width:50pt;border:solid windowtext 1.0pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>HOJA</span></b></p>
          </td>
          <td width=108 valign=top style='width:100pt;border:solid windowtext 1.0pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal;'><b><span style='font-size:10.0pt;'>HISTORIA
          CLINICA</span></b></p>
          </td>
         </tr>
         <tr>
          <td width=118 colspan=2 valign=top style='width:88.3pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$empresa->nombrecomercial}} </span></p>
          </td>
          <td width=96 valign=top style='width:71.7pt;border-top:none;border-left:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->nombre1}} {{$data->nombre2}}</span></p>
          </td>
          <td width=108 colspan=2 valign=top style='width:80.95pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->apellido1}} {{$data->apellido2}}</span></p>
          </td>
          <td width=75 valign=top style='width:56.25pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>@if($data->sexo=='1')MASCULINO @elseif($data->sexo=='2')FEMENINO @endif</span></p>
          </td>
          <td width=71 valign=top style='width:53.55pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>1</span></p>
          </td>
          <td width=108 valign=top style='width:81.3pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
          text-align:center;line-height:normal'><span style='font-size:9.0pt'>{{$data->id_paciente}}</span></p>
          </td>
         </tr>
         <tr>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>RESUMEN DE CUADRO CLINICO</span></b></p>
          </td>
         </tr>
         <tr>
          <td width=600 height=100 colspan=8 valign=top style='width:500pt;height: 150pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;'><span class="parrafo2" style='font-size:10px !important;line-height: 1.2 !important;'><?php echo $epicrisis->cuadro_clinico ?></span> <span class="parrafo2" style='font-size:11px !important;line-height: 1.2 !important;'> @if($epicrisis->favorable_des!=null)<?php echo $epicrisis->favorable_des; ?>@else &nbsp; @endif </span></p>
          </td>
         </tr>
         <tr>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>RESUMEN DE EVOLUCIÓN Y
          COMPLICACIONES</span></b></p>
          </td>
         </tr>
         <tr>
          <td width=288 colspan=4 valign=top style='width:216.025pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:9pt'>Evolución</span></b></p>
          </td>
          <td width=288 colspan=4 valign=top style='width:216.025pt;border-top:none;
          border-rigth:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:9.0pt'>Complicación</span></b></p>
          </td>
         </tr>
         <tr>
          <td width=288 colspan=4 valign=top style='width:216.025pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px !important'> {{$epicrisis->ep_resumen_evolucion}} </span> <br></p>
          </td>
          <td width=288 colspan=4 valign=top style='width:216.025pt;border-top:none;
          border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px'> {{$epicrisis->complicacion}}<br></span><br></p>
          </td>
         </tr>
         <tr>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>HALLAZGOS RELEVANTES DE EXÁMENES Y
          PROCEDIMIENTOS DIAGNÓSTICOS</span></b></p>
          </td>
         </tr>
         <tr>
          <td width=576 height=100 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <br>
          <p class=MsoNormal style='margin-top: 0;margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          1.2 !important'><span class="parrafo2" style='font-size:10px !important'><?php echo $protocolo->conclusion ?>
            <?php //echo $protocolo->hallazgos ?></span>
          </p>
          </td>
         </tr>
         <tr>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>RESUMEN DE TRATAMIENTO Y
          PROCEDIMIENTOS TERAPEUTICOS</span></b></p>
          </td>
         </tr>
         <tr style='height:108.15pt'>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10px !important'><?php echo $protocolo->hallazgos ?> </span></p>
          </td>
         </tr>
         <tr style='height:6.9pt'>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>RECETA</span></b></p>
          </td>
         </tr>
         <tr style='height:6.9pt'>
          <td width=576 colspan=8 valign=top style='width:432.05pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10px'>@if(!is_null($epicrisis))<?php echo $epicrisis->receta ?>@else <br ><br ><br ><br > @endif</span></p>
          </td>
         </tr>


         <tr height=0>
          <td width=38 style='border:none'></td>
          <td width=80 style='border:none'></td>
          <td width=96 style='border:none'></td>
          <td width=75 style='border:none'></td>
          <td width=33 style='border:none'></td>
          <td width=75 style='border:none'></td>
          <td width=71 style='border:none'></td>
          <td width=108 style='border:none'></td>
         </tr>
      </table>

      <div style="page-break-after:always;"></div>


      <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 width=576
       style='width:432.35pt;margin-left:-12.35pt;border-collapse:collapse;border:
       none'>
        <tr style='height:6.9pt'>
          <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>DIAGNOSTICO INGRESO</span></b></p>
          </td>
          <td width=311 colspan=15 valign=top style='width:5pt;border-top:solid windowtext 1.0pt;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>DIAGNOSTICO EGRESO</span></b></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:6.9pt'>
          <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>PRE PRESUNTIVO</span></b></p>
          </td>
          <td width=311 colspan=15 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>PRE PRESUNTIVO</span></b></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:100pt'>
          <td width=265 height=200 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:30pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px'>@foreach($cie10_in_pre as $value)@if(!is_null($cie10_3->where('id',$value->cie10)->first())){{$cie10_3->where('id',$value->cie10)->first()->descripcion}}@endif @if(!is_null($cie10_4->where('id',$value->cie10)->first())){{$cie10_4->where('id',$value->cie10)->first()->descripcion}}@endif - @endforeach</span></p>
          </td>
          <td width=311 height=200 colspan=15 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:10pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px'>@foreach($cie10_eg_pre as $value)@if(!is_null($cie10_3->where('id',$value->cie10)->first())){{$cie10_3->where('id',$value->cie10)->first()->descripcion}}@endif @if(!is_null($cie10_4->where('id',$value->cie10)->first())){{$cie10_4->where('id',$value->cie10)->first()->descripcion}}@endif - @endforeach</span></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:6.9pt'>
          <td width=20 colspan=2 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>CIE</span></b></p>
          </td>
          <td width=245 colspan=8 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10.0pt'>@foreach($cie10_in_pre as $value){{$value->cie10}} - @endforeach</span></p>
          </td>
          <td width=20 colspan=2 valign=top style='width:5pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>CIE</span></b></p>
          </td>
          <td width=291 colspan=13 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'><p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10.0pt'>@foreach($cie10_eg_pre as $value){{$value->cie10}} - @endforeach</span></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:6.9pt'>
          <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt;'>DEF DEFINITIVO</span></b></p>
          </td>
          <td width=311 colspan=15 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt;background-color: #e6e6e6;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt;'>DEF DEFINITIVO</span></b></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:6.9pt'>
          <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:30pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px'>@foreach($cie10_in_def as $value)@if(!is_null($cie10_3->where('id',$value->cie10)->first())){{$cie10_3->where('id',$value->cie10)->first()->descripcion}}@endif @if(!is_null($cie10_4->where('id',$value->cie10)->first())){{$cie10_4->where('id',$value->cie10)->first()->descripcion}}@endif - @endforeach</span></p>

          </td>
          <td width=311 colspan=15 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:11px'>@foreach($cie10_eg_def as $value)@if(!is_null($cie10_3->where('id',$value->cie10)->first())){{$cie10_3->where('id',$value->cie10)->first()->descripcion}}@endif @if(!is_null($cie10_4->where('id',$value->cie10)->first())){{$cie10_4->where('id',$value->cie10)->first()->descripcion}}@endif - @endforeach</span></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <tr style='height:6.9pt'>
          <td width=20 colspan=2 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>CIE</span></b></p>
          </td>
          <td width=245 colspan=8 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10.0pt'>@foreach($cie10_in_def as $value){{$value->cie10}} - @endforeach</span></p>
          </td>
          <td width=20 colspan=2 valign=top style='width:5pt;border-top:none;border-left:
          none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><b><span style='font-size:10.0pt'>CIE</span></b></p>
          </td>
          <td width=255 colspan=13 valign=top style='width:5pt;border-top:none;
          border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
          padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10.0pt'>@foreach($cie10_eg_def as $value){{$value->cie10}} - @endforeach</span></p>
          </td>
          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
        </tr>
        <!-- fin del cie 10 -->
       <tr>
        <td width=576 colspan=25 valign=top style='width:5pt;border:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>CONDICIONES DE EGRESO Y PRONOSTICO</span></b></p>
        </td>
        <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr>
        <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>CONDICIÓN</span></b></p>
        </td>
        <td width=311 colspan=15 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;background-color: #e6e6e6;'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>PRONÓSTICO</span></b></p>
        </td>
        <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr>
          <td width=265 colspan=10 valign=top style='width:5pt;border:solid windowtext 1.0pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
          normal'><span style='font-size:10.0pt !important'> {{$epicrisis->condicion}} </span> <br></p>
          </td>
          <td width=311 colspan=15 valign=top style='width:5pt;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'> {{$epicrisis->pronostico}}<br></span><br></p>

          <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
          </td>
         </tr>
       <tr>
        <td width=118 colspan=5 valign=top style='width:5pt;border:solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>MEDICO TRATANTE</span></b></p>
        </td>
        <td width=90 colspan=3 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>ESPECIALIDAD</span></b></p>
        </td>
        <td width=119 colspan=5 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>GASTROENTEROLOGO</span></p>
        </td>
        <td width=59 colspan=3 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>CODIGO</span></b></p>
        </td>
        <td width=46 colspan=3 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if($empresa->id=='0992704152001') @else 16203 @endif</span></p>
        </td>
        <td width=126 colspan=5 rowspan=2 valign=top style='width:94.75pt;border-top:
        none;border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
        text-align:center;line-height:normal'><b><span style='font-size:8.0pt'>PERIODO
        DE RESPONSABILIDAD</span></b></p>
        </td>
        <td width=18 rowspan=2 style='width:5pt;border-top:none;border-left:none;
        border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
        text-align:center;line-height:normal'><span style='font-size:10.0pt'>1</span></p>
        </td>
        <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr>
        <td width=73 colspan=2 valign=top style='width:5pt;border:solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>NOMBRES</span></b></p>
        </td>
        <td width=358 colspan=17 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if($doctor != "" ) Dr(a). {{$doctor->nombre1}} {{$doctor->apellido1}} @endif</span></p>
        </td>
        <td style='border:none;border-bottom:solid windowtext 1.0pt' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr>
        <td width=73 colspan=26 height=100 valign=top style='width:5pt;border-right: solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>
        <table>
          <tbody>
            <tr>
              <td colspan="12"></td>
            </tr>
            <tr>
              <td colspan="12"></td>
            </tr>
            <tr>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>ALTA<br>DEFINITIVA</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->alta=='DEFINITIVA') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td><span style='font-size:8.0pt'>ASINTOMÁTICO</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->discapacidad=='ASINTOMÁTICA') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DISCAPACIDAD<br>MODERADA</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->discapacidad=='MODERADA') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>RETIRO<br>AUTORIZADO</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->retiro=='AUTORIZADO') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DEFUNCIÓN<br>MENOS DE 48H</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->defuncion=='MENOS DE 48H') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DÍAS<br>DE ESTADIA</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>{{$epicrisis->dias_estadia}}</span></td>
            </tr>
            <tr>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
            </tr>
            <tr>
              <td colspan="12"></td>
            </tr>
            <tr>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>ALTA<br>TRANSITORIA</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->alta=='TRANSITORIA') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DISCAPACIDAD<br>LEVE</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->discapacidad=='LEVE') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DISCAPACIDAD<br>GRAVE</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->discapacidad=='GRAVE') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>RETIRO<br>NO AUTORIZADO</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->retiro=='NO AUTORIZADO') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DEFUNCIÓN<br>MÁS DE 48H</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>@if($epicrisis->defuncion=='MAS DE 48H') &nbsp;&nbsp;X @else &nbsp; @endif</span></td>
              <td><span>&nbsp;</span></td>
              <td rowspan=2><span style='font-size:8.0pt'>DÍAS<br>INCAPACIDAD</span></td>
              <td style="border: solid 1pt;width: 15px;"><span>{{$epicrisis->dias_incapacidad}}</span></td>
            </tr>
            <tr>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
              <td><span>&nbsp;</span></td>
            </tr>
          </tbody>
        </table>

        </span></b></p>
        </td>
       </tr>
       <tr style='height:6.9pt'>
        <td width=57 valign=top style='width:55pt;border:solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>FECHA</span></b></p>
        </td>
        <td width=170 colspan=8 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>NOMBRE DEL PROFESIONAL</span></b></p>
        </td>
        <td width=37 valign=top style='width:30pt;border-top:none;border-left:
        none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='@if(!is_null($firma)) @if(!is_null($firma->num)) font-size:10.0pt @else font-size:8.0pt @endif @endif' >@if(!is_null($firma)) @if(!is_null($firma->num)) LIBRO @else REG. SENECYT @endif @else REG. SENECYT @endif </span></b></p>
        </td>
        <td width=37 colspan=4 valign=top style='width:30pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>@if(!is_null($firma)) @if(!is_null($firma->num)) FOLIO @else REG. MSP @endif @else REG. MSP @endif</span></b></p>
        </td>
        <td width=68 colspan=4 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>@if(!is_null($firma)) @if(!is_null($firma->num)) NUMERO @else CEDULA @endif @else CEDULA @endif </span></b></p>
        </td>
        <td width=78 colspan=4 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>FIRMA</span></b></p>
        </td>
        <td width=78 colspan=3 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><b><span style='font-size:10.0pt'>HOJA</span></b></p>
        </td>
        <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr style='height:6.9pt'>
        <td width=5 valign=top style='width:25pt;border:solid windowtext 1.0pt;
        border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if(!is_null($epicrisis->fecha_imprime)){{substr($epicrisis->fecha_imprime,0,10)}}@else{{substr($protocolo->created_at,0,10)}}@endif</span></p>
        </td>
        <td width=5 colspan=8 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if($doctor != "" ) Dr(a). {{$doctor->nombre1}} {{$doctor->apellido1}} @endif </span></p>
        </td>
        <td width=47 valign=top style='width:5pt;border-top:none;border-left:
        none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='@if(!is_null($firma)) @if(!is_null($firma->num)) font-size:10.0pt @else font-size:8.0pt @endif @endif'>@if(!is_null($firma))  {{$firma->libro}}  @endif</span></p>
        </td>
        <td width=47 colspan=4 valign=top style='width:4pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 0.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if(!is_null($firma))  {{$firma->folio}} @endif</span></p>
        </td>
        <td width=78 colspan=4 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>@if(!is_null($firma)) @if(!is_null($firma->num)) {{$firma->num}} @endif @else @if(!is_null($doctor)) {{$doctor->id}} @endif  @endif</span></p>
        </td>
        <td width=78 colspan=4 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>&nbsp;</span></p>
        </td>
        <td width=78 colspan=3 valign=top style='width:5pt;border-top:none;
        border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
        padding:0cm 5.4pt 0cm 5.4pt;height:6.9pt'>
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:
        normal'><span style='font-size:10.0pt'>2</span></p>
        </td>
        <td style='border:none;padding:0cm 0cm 0cm 0cm' width=0><p class='MsoNormal'>&nbsp;</td>
       </tr>
       <tr height=0>
        <td width=47 style='border:none'></td>
        <td width=23 style='border:none'></td>
        <td width=2 style='border:none'></td>
        <td width=18 style='border:none'></td>
        <td width=19 style='border:none'></td>
        <td width=62 style='border:none'></td>
        <td width=19 style='border:none'></td>
        <td width=8 style='border:none'></td>
        <td width=8 style='border:none'></td>
        <td width=62 style='border:none'></td>
        <td width=8 style='border:none'></td>
        <td width=21 style='border:none'></td>
        <td width=32 style='border:none'></td>
        <td width=16 style='border:none'></td>
        <td width=26 style='border:none'></td>
        <td width=16 style='border:none'></td>
        <td width=2 style='border:none'></td>
        <td width=33 style='border:none'></td>
        <td width=11 style='border:none'></td>
        <td width=32 style='border:none'></td>
        <td width=19 style='border:none'></td>
        <td width=16 style='border:none'></td>
        <td width=37 style='border:none'></td>
        <td width=19 style='border:none'></td>
        <td width=20 style='border:none'></td>
        <td width=0 style='border:none'></td>
       </tr>
      </table>

    </div>








</body>
</html>