<html>

<head>
  <meta charset="utf-8">
  <meta name=Generator content="Microsoft Word 15 (filtered)">
  <style>
    /* Font Definitions */
    @font-face {
      font-family: Wingdings;
      panose-1: 5 0 0 0 0 0 0 0 0 0;
    }

    @font-face {
      font-family: "Cambria Math";
      panose-1: 2 4 5 3 5 4 6 3 2 4;
    }

    @font-face {
      font-family: Calibri;
      panose-1: 2 15 5 2 2 2 4 3 2 4;
    }

    @font-face {
      font-family: Tahoma;
      panose-1: 2 11 6 4 3 5 4 4 2 4;
    }

    /* Style Definitions */
    p.MsoNormal,
    li.MsoNormal,
    div.MsoNormal {
      margin-top: 0cm;
      margin-right: 0cm;
      margin-bottom: 10.0pt;
      margin-left: 0cm;
      line-height: 115%;
      font-size: 11.0pt;
      font-family: "Calibri", sans-serif;
    }

    p.MsoAcetate,
    li.MsoAcetate,
    div.MsoAcetate {
      mso-style-link: "Texto de globo Car";
      margin: 0cm;
      margin-bottom: .0001pt;
      font-size: 8.0pt;
      font-family: "Tahoma", sans-serif;
    }

    p.MsoListParagraph,
    li.MsoListParagraph,
    div.MsoListParagraph {
      margin-top: 0cm;
      margin-right: 0cm;
      margin-bottom: 10.0pt;
      margin-left: 36.0pt;
      line-height: 115%;
      font-size: 11.0pt;
      font-family: "Calibri", sans-serif;
    }

    p.MsoListParagraphCxSpFirst,
    li.MsoListParagraphCxSpFirst,
    div.MsoListParagraphCxSpFirst {
      margin-top: 0cm;
      margin-right: 0cm;
      margin-bottom: 0cm;
      margin-left: 36.0pt;
      margin-bottom: .0001pt;
      line-height: 115%;
      font-size: 11.0pt;
      font-family: "Calibri", sans-serif;
    }

    p.MsoListParagraphCxSpMiddle,
    li.MsoListParagraphCxSpMiddle,
    div.MsoListParagraphCxSpMiddle {
      margin-top: 0cm;
      margin-right: 0cm;
      margin-bottom: 0cm;
      margin-left: 36.0pt;
      margin-bottom: .0001pt;
      line-height: 115%;
      font-size: 11.0pt;
      font-family: "Calibri", sans-serif;
    }

    p.MsoListParagraphCxSpLast,
    li.MsoListParagraphCxSpLast,
    div.MsoListParagraphCxSpLast {
      margin-top: 0cm;
      margin-right: 0cm;
      margin-bottom: 10.0pt;
      margin-left: 36.0pt;
      line-height: 115%;
      font-size: 11.0pt;
      font-family: "Calibri", sans-serif;
    }

    span.TextodegloboCar {
      mso-style-name: "Texto de globo Car";
      mso-style-link: "Texto de globo";
      font-family: "Tahoma", sans-serif;
    }

    .MsoChpDefault {
      font-family: "Calibri", sans-serif;
    }

    .MsoPapDefault {
      margin-bottom: 10.0pt;
      line-height: 115%;
    }

    @page WordSection1 {
      size: 612.0pt 792.0pt;
      margin: 19.85pt 62.35pt 19.85pt 62.35pt;
    }

    div.WordSection1 {
      page: WordSection1;
    }

    /* List Definitions */
    ol {
      margin-bottom: 0cm;
    }

    ul {
      margin-bottom: 0cm;
    }

    div.WordSection1 {
      size: 595.3pt 1000pt;
      margin: 0pt 0pt 0pt 0pt;
    }

    @page {
      size: 612.0pt 855.0pt;
    }
  </style>

</head>

<body lang=ES-EC>

  <div class=WordSection1 style="margin-left: 15pt; margin-top: -34pt">

    @if($data->id_seguro!='5')
    @if(!is_null($empresa->logo_form))
    <p class=MsoNormal align=center style='text-align:center; padding-top: 2pt;'><img width=280 height=60 src="{{base_path().'/public/formato/header2.jpg'}}" align=center hspace=12></p>
    @else
    <p class=MsoNormal align=center style='text-align:center; padding-top: 2pt;'><span><b>{{$empresa->nombre_form}}</b></span></p>
    @endif
    @else
    <p class=MsoNormal align=center style='text-align:center; padding-top: 2pt;'><span>&nbsp;</span></p>
    @endif
    @php
    $empresa_1 = Sis_medico\Empresa::where('prioridad', '1')->first();
    @endphp
    @php
    $agenda = Sis_medico\Agenda::where('id',$data->id_agenda)->first();
    @endphp

    <table class=MsoTableGrid cellspacing=0 cellpadding=0 style="margin-bottom: 8px">
      <tr style='height:19pt'>
        <td style='width:500pt;padding:0cm 5.4pt 0cm 5.4pt; height:19pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt; text-align:center;line-height:normal'><b><span style='font-size:12.0pt'>{{$empresa->razonsocial}}</span></b></p>
        </td>
      </tr>
    </table>

    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:19pt'>
        <td style='width:500pt;border:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt; height:19pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt; text-align:center;line-height:normal'><b><span style='font-size:12.0pt'>    ACTA
                ENTREGA RECEPCIÓN DE SERVICIOS DE SALUD</span></b></p>
        </td>
      </tr>
    </table>
    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:14.95pt'>
        <td width=654 colspan=2 valign=top style='width:500pt;border:solid black 0.75pt; padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height: normal'><span style='font-size:9.0pt'><b>PRESTADOR:</b> {{$empresa->nombre_form}}</span></p>
        </td>
      </tr>
      <tr style='height:14.95pt'>
        <td width=654 colspan=2 valign=top style='width:490.6pt;border:solid black 0.75pt; border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height: normal'><span style='font-size:9.0pt'><b>PERSONA DE CONTACTO: </b>Dr(a). CARLOS ARTURO ROBLES JARA</span></p>
        </td>
      </tr>
      <tr style='height:14.0pt'>
        <td width=421 valign=top style='width:200pt;border-top:none;border-left: solid black 0.75pt;border-bottom:solid black 0.75pt;border-right:solid windowtext 1.0pt; padding:0cm 5.4pt 0cm 5.4pt;height:14.0pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>TELEFONO:</b> 052440637 </span></p>
        </td>
        <td width=233 valign=top style='width:100pt;border-top:none;border-left:none;border-bottom:solid black 0.75pt;border-right:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:14.0pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>E-MAIL:</b> iecedportoviejo@hotmail.com</span></p>
        </td>
      </tr>
      <tr style='height:14.95pt'>
        <td width=421 valign=top style='width:200pt;border-top:none;border-left:solid black 0.75pt;border-bottom:solid black 0.75pt;border-right:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>MES Y AÑO DE PRESTACION:</b> <?php $mes = substr($date, 5, 2);
                                                                                                                                                              if ($mes == 01) {
                                                                                                                                                                echo "Enero";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 02) {
                                                                                                                                                                echo "Febrero";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 03) {
                                                                                                                                                                echo "Marzo";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 04) {
                                                                                                                                                                echo "Abril";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 05) {
                                                                                                                                                                echo "Mayo";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 06) {
                                                                                                                                                                echo "Junio";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == 07) {
                                                                                                                                                                echo "Julio";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == '08') {
                                                                                                                                                                echo "Agosto";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == '09') {
                                                                                                                                                                echo "Septiembre";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == '10') {
                                                                                                                                                                echo "Octubre";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == '11') {
                                                                                                                                                                echo "Noviembre";
                                                                                                                                                              }
                                                                                                                                                              if ($mes == '12') {
                                                                                                                                                                echo "Diciembre";
                                                                                                                                                              } ?> / {{substr($date, 0, 4)}}</span></p>
        </td>
        <td width=233 valign=top style='width:100pt;border-top:none;border-left:none;border-bottom:solid black 0.75pt;border-right:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height: normal'><span style='font-size:9.0pt'><b>CODIGO DE CIE 10:</b></span><span style='font-size:9.0pt'> {{$agenda->cie10}} </span></p>
        </td>
      </tr>
      <tr style='height:14.0pt'>
        <td width=654 colspan=2 valign=top style='width:490.6pt;border:solid black 0.75pt;border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:14.0pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b><span style='font-size:9.0pt'>NUMERO DE CODIGO DE VALIDACION:</span></b>&nbsp; {{$agenda->cod_val}}</p>
        </td>
      </tr>
      <tr style='height:14.95pt'>
        <td width=654 colspan=2 valign=top style='width:490.6pt;border:solid black 0.75pt;border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>NUMERO DE HISTORIA CLINICA:</b> {{$data->id_paciente}}</span></p>
        </td>
      </tr>
      <tr style='height:14.95pt'>
        <td width=654 colspan=2 valign=top style='width:490.6pt;border:solid black 0.75pt;border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:14.95pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>SERVICIO ENTREGADO:</b>@if($agenda->est_amb_hos == '1') HOSPITALARIO @elseif($agenda->est_amb_hos == '2') EMERGENCIA @else AMBULATORIO @endif </span></p>
        </td>
      </tr>
    </table>

    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt'>&nbsp;</p>

    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:17.05pt'>
        <td width=654 style='width:500pt;border:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:17.05pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>NOMBRE DEL PACIENTE (APELLIDOS Y
                NOMBRES):</b> {{$data->apellido1}} @if($data->apellido2!='(N/A)'){{$data->apellido2}}@endif {{$data->nombre1}} @if($data->nombre2!='(N/A)'){{$data->nombre2}}@endif</span></p>
        </td>
      </tr>
      <tr style='height:18.25pt'>
        <td width=654 style='width:500pt;border:solid black 0.75pt;border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:18.25pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'><b>N° DE CEDULA DE IDENTIDAD:</b> {{$data->id_paciente}}</span></p>
        </td>
      </tr>
    </table>

    <table class=MsoTableGrid cellspacing=0 cellpadding=0 style="margin-bottom: 8px">
      <tr style='height:19pt'>
        <td style='width:500pt;padding:0cm 5.4pt 0cm 5.4pt; height:19pt'>
          <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:center;line-height:normal'><b><span style='font-size:12.0pt'>ACUSE ENTREGA DEL SERVICIO</span></b></p>
        </td>
      </tr>
    </table>

    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:24.9pt'>
        <td style='width:500pt;border:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:24.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:7.0pt'>OBSERVACIONES:</span></b></p>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><span style='font-size:7.0pt'>(Si tuviera alguna observación llene este campo, caso contrario deje en blanco)
            </span></p>
        </td>
      </tr>
    </table>

    <p class=MsoListParagraphCxSpFirst style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;text-indent:-18.0pt'><span style='font-size:9.0pt;line-height:115%;font-family:Symbol'>·<span style='font:7.0pt "Times New Roman"'>&nbsp;&nbsp;&nbsp;
        </span></span><b><span style='font-size:6.0pt;line-height:115%'>Como prestador
          de la RPIS. Conozco obligatoriamente del TPSNS. y sus procedimientos que están
          regulado en la Normativa Legal vigente.</span></b></p>
    <p class=MsoListParagraphCxSpLast style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;text-indent:-18.0pt'><span style='font-size:9.0pt;line-height:115%;font-family:Symbol'>·<span style='font:7.0pt "Times New Roman"'>&nbsp;&nbsp;&nbsp;
        </span></span><b><span style='font-size:6.0pt;line-height:100%'>Además tengo
          conocimiento el acápite que refiere a la Coordinación de pagos y tarifas que
          indican textualmente.</span></b></p>
    <!-- <div align=center>
    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:24.9pt'>
        <td style='width:350pt;border:solid black 0.75pt; padding:0cm 5.4pt 0cm 5.4pt;height:24.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:6.0pt'>"En caso de objeción o débito, el presentador no podrá requerir el pago al usuario/paciente,familiares o acompañante.<br> Cualquier cobro en este sentido será motivo de la sanción que la Ley prevea"</span></b></p>
        </td>
      </tr>
    </table>
  </div> -->
    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
      <tr style='height:24.9pt'>
        <td style='width:500pt;border:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:24.9pt'>
          <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><span style='font-size:7.0pt'>"En caso de objeción o débito, el presentador no podrá requerir el pago al usuario/paciente,familiares o acompañante.<br> Cualquier cobro en este sentido será motivo de la sanción que la Ley prevea"
            </span></p>
        </td>
      </tr>
    </table>
    <div class="col-md-12">
      &nbsp;
    </div>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt'><b style='font-size:10.0pt'>__________________________________
        (firma y sello)</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b style='font-size:10.0pt'>&nbsp;Dr(a).CARLOS ARTURO ROBLES JARA</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'>&nbsp;C.I: 1301656904</p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'>&nbsp;</p>
    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:center;line-height:normal'><b><u>ACUSE RECEPCION DEL SERVICIO</u></b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'>&nbsp;</p>
    @php
    $fecha_ini = date("d/m/Y", strtotime(substr($data->fechaini, 0, 10)));
    @endphp
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style="font-size: 10;">Ciudad: {{$empresa_1->ciudad}}, a los ______ Días del mes de <b><?php $mes = substr($date, 5, 2);
                                                                                                                                                                                        if ($mes == 01) {
                                                                                                                                                                                          echo "Enero";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 02) {
                                                                                                                                                                                          echo "Febrero";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 03) {
                                                                                                                                                                                          echo "Marzo";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 04) {
                                                                                                                                                                                          echo "Abril";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 05) {
                                                                                                                                                                                          echo "Mayo";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 06) {
                                                                                                                                                                                          echo "Junio";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == 07) {
                                                                                                                                                                                          echo "Julio";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == '08') {
                                                                                                                                                                                          echo "Agosto";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == '09') {
                                                                                                                                                                                          echo "Septiembre";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == '10') {
                                                                                                                                                                                          echo "Octubre";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == '11') {
                                                                                                                                                                                          echo "Noviembre";
                                                                                                                                                                                        }
                                                                                                                                                                                        if ($mes == '12') {
                                                                                                                                                                                          echo "Diciembre";
                                                                                                                                                                                        } ?></b> del año <b>{{substr($date, 0, 4)}}</b></span></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b>&nbsp;</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b>&nbsp;</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:8.0pt'>&nbsp;</span></b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt'><b style='font-size:11px'>__________________________________(firma)</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='position:relative;z-index:251661312'><span style='position:absolute;left:-2px;top:-1px;width:286px;height:3px'></span><span style="font-size: 11px"><b>{{$data->apellido1}} @if($data->apellido2!='(N/A)'){{$data->apellido2}}@endif {{$data->nombre1}} @if($data->nombre2!='(N/A)'){{$data->nombre2}}@endif</span></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b>&nbsp;</b></p>

    <div align=center>
      <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0>
        <tr style='height:24.9pt'>
          <td style='width:350pt;border:solid black 0.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:24.9pt'>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:9.0pt'>@if($data->fparentesco!='Principal')Observaciones: Yo <b>{{$data->fnombre1}} @if($data->fnombre2!='(N/A)'){{$data->fnombre2}}@endif {{$data->fapellido1}} @if($data->fapellido2!='(N/A)'){{$data->fapellido2}}@endif</b> en
                mi calidad de <b>{{$data->fparentesco}}</b> .y/o representante o acompañante, del
                paciente <b>{{$data->apellido1}} @if($data->apellido2!='(N/A)'){{$data->apellido2}}@endif {{$data->nombre1}} @if($data->nombre2!='(N/A)'){{$data->nombre2}}@endif</b> certifico que el mencionado paciente recibió
                el servicio registrado en el presente acta. @else Observaciones: Yo…………..…………………................en
                mi calidad de……………………………………….y/o representante o acompañante, del
                paciente………………………………………….………………….certifico que el mencionado paciente recibió
                el servicio registrado en el presente acta. @endif</span></b></p>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt'>&nbsp;</p>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt'>&nbsp;</p>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;margin-top: -15pt;'><b>_________________________________________</b></p>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b>@if($data->fparentesco!='Principal') {{$data->fnombre1}} @if($data->fnombre2!='(N/A)'){{$data->fnombre2}}@endif {{$data->fapellido1}} @if($data->fapellido2!='(N/A)'){{$data->fapellido2}}@endif @endif</b></p>
            <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b style="font-size: 9;">(Utilizar este campo solo cuando el paciente no pueda registrar su firma)</b></p>
          </td>
        </tr>
      </table>
    </div>

    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:8.0pt'>&nbsp;</span></b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b>CERTIFICADO DE FIRMAS:</b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><span style='font-size:8.0pt'>En mi calidad del prestador de servicios, certifico que las firmas constantes en el presente documento, corresponden a la firma del paciente o representante de ser el caso, misma que fue receptada en este centro de atención, por lo tanto, me responsabilizo por contenido de dicho certificado, asumiendo toda la responsabilidad tanto administrativa civil o penal por la veracidad de la información entregada.</span></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:8.0pt'>&nbsp;</span></b></p>
    <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:justify;line-height:normal'><b><span style='font-size:8.0pt'>&nbsp;</span></b></p><br>
    <!-- <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt'><b style='font-size:10.0pt'>__________________________________</b></p>
    <p class=MsoNormal></p>
    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><b style='font-size:10.0pt'>&nbsp;Dr(a).CARLOS ARTURO ROBLES JARA </b></p>
    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'>&nbsp;C.I: 1301656904</p>
    <p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'>&nbsp;</p> -->
  </div>

</body>

</html>
